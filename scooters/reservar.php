<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
include("../config/conexao.php");

$scooter_id = (int) $_POST['scooter_id'];
$horas = (int) $_POST['horas'];
if ($horas < 1) { $horas = 1; }

// busca preço/hora direto do banco (nunca confia no valor vindo do formulário)
$stmt = $conn->prepare("SELECT preco_hora FROM scooters WHERE id = ?");
$stmt->bind_param("i", $scooter_id);
$stmt->execute();
$res = $stmt->get_result();
$scooter = $res->fetch_assoc();

if (!$scooter) {
    die("Scooter não encontrada.");
}

$valor_total = $horas * $scooter['preco_hora'];

$conn->query("UPDATE scooters SET disponivel = 0 WHERE id = $scooter_id");

// verifica se já está em uso
$check = $conn->query("
    SELECT * FROM reservas 
    WHERE scooter_id = $scooter_id AND data_entrega IS NULL
");
if ($check->num_rows > 0) {
    die("Scooter já está em uso!");
}

$sql = "INSERT INTO reservas 
        (scooter_id, usuario, data_reserva, data_entrega, horas, valor_total) 
        VALUES (?, ?, NOW(), NULL, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isid", $scooter_id, $_POST['usuario'], $horas, $valor_total);
$stmt->execute();

header("Location: ../dashboard.php");
exit();
?>