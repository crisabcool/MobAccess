<?php

session_start();

if(
!isset($_SESSION['usuario'])
){
    header(
    "Location: login.php"
    );

    exit();
}

include("../config/conexao.php");

$scooter_id = $_POST['scooter_id'];

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
        (scooter_id, usuario, data_reserva, data_entrega) 
        VALUES (?, ?, NOW(), NULL)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $_POST['scooter_id'], $_POST['usuario']);
$stmt->execute();

header("Location: ../dashboard.php");
exit();
?>
