<?php
session_start();
include("config/conexao.php");

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// 1. Verifica formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: login.php?erro=formato");
    exit();
}

// 2. Busca usuário pelo email
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: login.php?erro=naoexiste");
    exit();
}

$usuario = $result->fetch_assoc();

// 3. Verifica senha
if (!password_verify($senha, $usuario['senha'])) {
    header("Location: login.php?erro=senha");
    exit();
}

// 4. Login OK
session_regenerate_id(true);
$_SESSION['usuario'] = $usuario['nome'];
$_SESSION['usuario_id'] = (int) $usuario['id'];

// Administrador vai para o painel; os demais, para o app
if (($usuario['perfil'] ?? '') === 'admin') {
    header("Location: painel_admin.php");
} else {
    header("Location: dashboard.php");
}
exit();