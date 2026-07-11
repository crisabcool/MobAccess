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

$conn->query("
UPDATE scooters 
SET disponivel = 1 
WHERE id = (SELECT scooter_id FROM reservas WHERE id = $reserva_id)
");

$sql = "UPDATE reservas 
        SET data_entrega = NOW() 
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_POST['reserva_id']);
$stmt->execute();

header("Location: ../admin.php");
exit();
?>
