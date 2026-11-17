<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false]);
    exit();
}

include("../config/conexao.php");

$dados = json_decode(file_get_contents('php://input'), true);
$aceite = !empty($dados['aceite']) ? 1 : 0;
$uid = (int) $_SESSION['usuario_id'];

$stmt = $conn->prepare("UPDATE usuarios SET consentimento_geo = ?, consentimento_geo_em = NOW() WHERE id = ?");
$stmt->bind_param("ii", $aceite, $uid);
$stmt->execute();

echo json_encode(['ok' => true, 'consentimento' => $aceite]);