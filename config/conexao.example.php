<?php
// Copie este arquivo para "conexao.php" e preencha com os dados reais do seu banco local.
// Este arquivo (conexao.example.php) NÃO contém credenciais e pode ser versionado no Git.

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