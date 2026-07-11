<?php

include 'config/conexao.php';

$msg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = $_POST['nome'];
    $email = $_POST['email'];

    $senha =
    password_hash(
        $_POST['senha'],
        PASSWORD_DEFAULT
    );

    $sql =
    "INSERT INTO usuarios
    (nome,email,senha)
    VALUES (?,?,?)";

    $stmt =
    $conn->prepare($sql);

    $stmt->bind_param(
        "sss",
        $nome,
        $email,
        $senha
    );

    if($stmt->execute()){

        header("Location: login.php");

        exit();
    }

    $msg = "Erro ao cadastrar";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Cadastro</title>
</head>

<body>

<h2>Criar Conta</h2>

<form method="POST">

<input
type="text"
name="nome"
placeholder="Nome"
required>

<input
type="email"
name="email"
placeholder="Email"
required>

<input
type="password"
name="senha"
placeholder="Senha"
required>

<button type="submit">
Cadastrar
</button>

</form>

</body>
</html>
