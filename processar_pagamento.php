<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['reserva_pendente'])) {
    header("Location: dashboard.php");
    exit();
}
include("config/conexao.php");

$r = $_SESSION['reserva_pendente'];
$metodo = $_POST['metodo'] ?? 'debito';

// SIMULAÇÃO: aqui entraria a integração real com um gateway de pagamento (ex: Mercado Pago).
// Por enquanto, o pagamento é sempre considerado aprovado.

$stmt = $conn->prepare("SELECT id FROM reservas WHERE scooter_id = ? AND data_entrega IS NULL");
$stmt->bind_param("i", $r['scooter_id']);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    die("Esta scooter já foi reservada por outra pessoa enquanto você pagava.");
}

$sql = "INSERT INTO reservas 
        (scooter_id, usuario, data_reserva, data_entrega, horas, valor_total, status_pagamento, metodo_pagamento, liberada) 
        VALUES (?, ?, NOW(), NULL, ?, ?, 'aprovado', ?, 0)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "isids",
    $r['scooter_id'],
    $_SESSION['usuario'],
    $r['horas'],
    $r['valor_total'],
    $metodo
);
$stmt->execute();
$reserva_id = $stmt->insert_id;

$conn->query("UPDATE scooters SET disponivel = 0 WHERE id = " . (int)$r['scooter_id']);

$_SESSION['reserva_ativa_id'] = $reserva_id;
unset($_SESSION['reserva_pendente']);

header("Location: escanear.php");
exit();