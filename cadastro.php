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
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MobAccess | Criar Conta</title>
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}
body{
    height:100vh;
    background:url('images/fundo_site.jpg') center center no-repeat;
    background-size:cover;
    display:flex;
    justify-content:center;
    align-items:center;
    position:relative;
    overflow:hidden;
}
body::before{
    content:'';
    position:absolute;
    width:100%;
    height:100%;
    background:rgba(0,0,0,.65);
}
.erro-login{
    background:rgba(255,0,0,.15);
    color:#ff8080;
    padding:10px 15px;
    border-radius:8px;
    margin-bottom:15px;
    font-size:14px;
    text-align:center;
}
.login-box{
    position:relative;
    z-index:2;
    width:420px;
    max-width:90%;
    background:rgba(0,15,60,.85);
    backdrop-filter:blur(10px);
    border-radius:20px;
    padding:40px;
    text-align:center;
}
h1{
    color:white;
    margin-bottom:10px;
}
.subtitulo{
    color:#d7d7d7;
    margin-bottom:25px;
}
input{
    width:100%;
    padding:14px;
    border:none;
    border-radius:10px;
    margin-bottom:15px;
    background:#1e2b4b;
    color:white;
}
input::placeholder{
    color:#bbb;
}
button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:10px;
    background:#6450ff;
    color:white;
    font-size:16px;
    cursor:pointer;
    transition:.3s;
}
button:hover{
    background:#755fff;
}
.links{
    display:flex;
    justify-content:center;
    gap:10px;
    margin-top:20px;
    flex-wrap:wrap;
}
.btn-link{
    display:inline-block;
    padding:10px 15px;
    background:#243b7f;
    color:white;
    text-decoration:none;
    border-radius:8px;
    transition:0.3s;
    font-size:14px;
}
.btn-link:hover{
    background:#6a5cff;
}
.campo-senha{
    position:relative;
    margin-bottom:15px;
}
.campo-senha input{
    margin-bottom:0;
    padding-right:45px;
}
.toggle-senha{
    position:absolute;
    right:14px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    font-size:18px;
    user-select:none;
    opacity:.8;
    transition:.2s;
}
.toggle-senha:hover{
    opacity:1;
}
.scooter-track{
    position:absolute;
    bottom:0;
    left:0;
    width:100%;
    height:120px;
    overflow:hidden;
    z-index:1;
    pointer-events:none;
}
.scooter-track img{
    position:absolute;
    bottom:10px;
    height:70px;
    opacity:.85;
    filter:drop-shadow(0 6px 6px rgba(0,0,0,.4));
}
.scooter-track .s1{
    animation: passarDireita 14s linear infinite;
}
.scooter-track .s2{
    animation: passarEsquerda 18s linear infinite;
    animation-delay: 3s;
    transform: scaleX(-1);
}
.scooter-track .s3{
    animation: passarDireita 22s linear infinite;
    animation-delay: 7s;
}
@keyframes passarDireita{
    from{ left:-150px; }
    to{ left:110%; }
}
@keyframes passarEsquerda{
    from{ right:-150px; }
    to{ right:110%; }
}
@media(max-width:768px){
    .login-box{
        padding:25px;
    }
    h1{
        font-size:34px;
    }
}
</style>
</head>
<body>

<div class="scooter-track">
    <img src="images/scooter_city.png" class="s1" alt="Scooter City">
    <img src="images/scooter_pro.png" class="s2" alt="Scooter Pro">
    <img src="images/scooter_max.png" class="s3" alt="Scooter Max">
</div>

<div class="login-box">
    <h1>Criar Conta</h1>
    <div class="subtitulo">
        Preencha os dados para começar a usar o MobAccess
    </div>

    <?php if ($msg): ?>
        <div class="erro-login"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

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
        <div class="campo-senha">
            <input
                type="password"
                name="senha"
                id="senha"
                placeholder="Senha"
                required>
            <span
                class="toggle-senha"
                id="toggleSenha"
                title="Mostrar senha">
                👁️
            </span>
        </div>
        <button type="submit">
            Cadastrar
        </button>
    </form>

    <div class="links">
        <a href="login.php" class="btn-link">
        Já tenho conta
        </a>
    </div>
</div>

<script>
const inputSenha = document.getElementById('senha');
const toggleSenha = document.getElementById('toggleSenha');

toggleSenha.addEventListener('click', () => {
    const visivel = inputSenha.type === 'text';
    inputSenha.type = visivel ? 'password' : 'text';
    toggleSenha.title = visivel ? 'Mostrar senha' : 'Ocultar senha';
    toggleSenha.textContent = visivel ? '👁️' : '🙈';
});
</script>

</body>
</html>