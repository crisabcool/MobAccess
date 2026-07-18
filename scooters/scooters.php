<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("config/conexao.php");

header("Content-Type: application/json");

$result = $conn->query("SELECT * FROM scooters WHERE disponivel = 1");
$scooters = [];
while ($row = $result->fetch_assoc()) {
  $scooters[] = $row;
}
echo json_encode($scooters);