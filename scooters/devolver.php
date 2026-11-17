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

$reserva_id = $_POST['reserva_id'];

$conn->query("
UPDATE scooters 
SET disponivel = 1 
WHERE id = (SELECT scooter_id FROM reservas WHERE id = $reserva_id)
");

$sql = "UPDATE reservas 
        SET data_entrega = NOW() 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reserva_id);
$stmt->execute();

include("../config/analytics.php");
$q = $conn->prepare("SELECT scooter_id FROM reservas WHERE id = ?");
$q->bind_param("i", $reserva_id);
$q->execute();
$row = $q->get_result()->fetch_assoc();
if ($row) {
    registrar_fluxo($conn, (int) $row['scooter_id'], 'devolucoes');
}

header("Location: ../admin.php");
exit();
?>