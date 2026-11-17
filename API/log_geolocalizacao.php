<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'motivo' => 'nao_autenticado']);
    exit();
}

include("../config/conexao.php");

$uid = (int) $_SESSION['usuario_id'];
$dados = json_decode(file_get_contents('php://input'), true);

$lat = isset($dados['lat']) ? (float) $dados['lat'] : null;
$lng = isset($dados['lng']) ? (float) $dados['lng'] : null;
$precisao = isset($dados['precisao']) ? (float) $dados['precisao'] : null;

if ($lat === null || $lng === null || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'motivo' => 'coordenadas_invalidas']);
    exit();
}

try {
    // 1) só com consentimento explícito
    $stmt = $conn->prepare("SELECT consentimento_geo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    if (!$u || (int) $u['consentimento_geo'] !== 1) {
        echo json_encode(['ok' => false, 'motivo' => 'sem_consentimento']);
        exit();
    }

    // 2) só durante uma corrida ativa (minimização de dados)
    $stmt = $conn->prepare("SELECT id, scooter_id FROM reservas
                            WHERE usuario_id = ? AND liberada = 1 AND data_entrega IS NULL
                            ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $corrida = $stmt->get_result()->fetch_assoc();
    if (!$corrida) {
        echo json_encode(['ok' => false, 'motivo' => 'sem_corrida']);
        exit();
    }
    $reserva_id = (int) $corrida['id'];
    $scooter_id = (int) $corrida['scooter_id'];

    // 3) no máximo 1 registro a cada 10 segundos por corrida
    $stmt = $conn->prepare("SELECT TIMESTAMPDIFF(SECOND, criado_em, NOW()) AS seg
                            FROM logs_geolocalizacao WHERE reserva_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("i", $reserva_id);
    $stmt->execute();
    $ultimo = $stmt->get_result()->fetch_assoc();
    if ($ultimo && (int) $ultimo['seg'] < 10) {
        echo json_encode(['ok' => true, 'motivo' => 'ignorado_frequencia']);
        exit();
    }

    // 4) grava
    $stmt = $conn->prepare("INSERT INTO logs_geolocalizacao
        (usuario_id, reserva_id, scooter_id, latitude, longitude, precisao_m, origem)
        VALUES (?, ?, ?, ?, ?, ?, 'usuario')");
    $stmt->bind_param("iiiddd", $uid, $reserva_id, $scooter_id, $lat, $lng, $precisao);
    $stmt->execute();

    echo json_encode(['ok' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'motivo' => 'erro_interno']);