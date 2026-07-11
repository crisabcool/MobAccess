<?php
include("../config/db.php");

header("Content-Type: application/json");

$res = $conn->query("
SELECT r.id, s.nome, r.usuario, r.data_reserva, r.data_entrega
FROM reservas r
JOIN scooters s ON s.id = r.scooter_id
");

$data = [];

while($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
