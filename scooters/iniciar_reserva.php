<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}
include("../config/conexao.php");

$scooter_id = (int) $_POST['scooter_id'];
$horas = (int) $_POST['horas'];
if ($horas < 1) { $horas = 1; }

$stmt = $conn->prepare("SELECT * FROM scooters WHERE id = ? AND disponivel = 1");
$stmt->bind_param("i", $scooter_id);
$stmt->execute();
$scooter = $stmt->get_result()->fetch_assoc();

if (!$scooter) {
    die("Esta scooter não está mais disponível.");
}

$valor_total = $horas * $scooter['preco_hora'];

$_SESSION['reserva_pendente'] = [
    'scooter_id'  => $scooter_id,
    'nome'        => $scooter['nome'],
    'tipo'        => $scooter['tipo'],
    'preco_hora'  => $scooter['preco_hora'],
    'horas'       => $horas,
    'valor_total' => $valor_total,
];

header("Location: ../pagamento.php");
exit();