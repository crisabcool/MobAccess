<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "mobaccess");

$result = $conn->query("SELECT * FROM scooters WHERE status='disponivel'");

$scooters = [];

while ($row = $result->fetch_assoc()) {
  $scooters[] = $row;
}

echo json_encode($scooters);
