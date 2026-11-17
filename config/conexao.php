<?php
include("config/analytics.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "mobaccess";
$conn = new mysqli(
    $host,
    $user,
    $pass,
    $db
);
if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}