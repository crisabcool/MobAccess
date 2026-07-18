<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
    exit();
}

include("../config/conexao.php");

$reserva_id = (int) ($_POST['reserva_id'] ?? 0);

$stmt = $conn->prepare("SELECT id FROM reservas WHERE id = ? AND usuario = ?");
$stmt->bind_param("is", $reserva_id, $_SESSION['usuario']);
$stmt->execute();
$reserva = $stmt->get_result()->fetch_assoc();

if (!$reserva) {
    echo json_encode(['success' => false, 'message' => 'Reserva não encontrada.']);
    exit();
}

$stmt = $conn->prepare("UPDATE reservas SET liberada = 1 WHERE id = ?");
$stmt->bind_param("i", $reserva_id);
$stmt->execute();

echo json_encode(['success' => true]);