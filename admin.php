<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
include("config/conexao.php");
$res = $conn->query("
SELECT r.*, s.nome,
CASE 
    WHEN r.data_entrega IS NULL THEN 'Em uso'
    ELSE 'Finalizado'
END AS status
FROM reservas r
JOIN scooters s ON s.id = r.scooter_id
ORDER BY r.id DESC
");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>MobAccess | Painel Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
*{
    font-family: Arial, Helvetica, sans-serif;
}
body{
    background: linear-gradient(180deg, #05081a 0%, #0a1030 100%);
    min-height: 100vh;
    color: #e8e8f0;
}
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}
.logo-area{
    display:flex;
    align-items:center;
    gap:12px;
}
.logo-area img{
    height: 42px;
    width: 42px;
    object-fit: contain;
    filter: drop-shadow(0 0 6px rgba(100,80,255,.6));
}
.logo-area h3{
    color:#fff;
    margin:0;
    font-weight:700;
    letter-spacing: .5px;
}
.logo-area h3 span{
    color:#9b8cff;
}
.btn-mob-dark{
    background:#1e2b4b;
    color:#fff;
    border:none;
}
.btn-mob-dark:hover{
    background:#28395f;
    color:#fff;
}
.painel-card{
    background: rgba(0,15,60,.65);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 8px 24px rgba(0,0,0,.4);
}
.painel-card h2{
    color:#fff;
    font-weight:700;
    margin-bottom: 20px;
}
table{
    color:#e8e8f0 !important;
}
.table-bordered{
    --bs-table-border-color: rgba(255,255,255,.1);
}
.table > :not(caption) > * > *{
    background-color: transparent;
    color:#e8e8f0;
    border-color: rgba(255,255,255,.1);
    vertical-align: middle;
}
thead th{
    color:#9b8cff;
    font-weight:600;
    border-bottom: 2px solid rgba(155,140,255,.3) !important;
}
tbody tr:hover td{
    background-color: rgba(155,140,255,.06);
}
.badge-em-uso{
    background:#f39c12;
    color:#1a1a1a;
}
.badge-finalizado{
    background:#2ecc71;
    color:#0a1030;
}
.btn-devolver{
    background:#e74c3c;
    border:none;
    color:#fff;
    transition:.2s;
}
.btn-devolver:hover{
    background:#ff5a48;
    color:#fff;
}
</style>
</head>
<body>
<div class="container mt-4 mb-5">

<div class="topbar">
    <div class="logo-area">
        <img src="images/scooter_img.webp" alt="MobAccess">
        <h3>Mob<span>Access</span></h3>
    </div>
    <div>
        <a href="dashboard.php" class="btn btn-mob-dark btn-sm">Voltar ao Dashboard</a>
    </div>
</div>

<div class="painel-card">
<h2>Painel Admin</h2>
<table class="table table-bordered">
<thead>
<tr>
<th>ID</th>
<th>Scooter</th>
<th>Usuário</th>
<th>Status</th>
<th>Entrega</th>
<th>Ação</th>
</tr>
</thead>
<tbody>
<?php while($r = $res->fetch_assoc()): ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= htmlspecialchars($r['nome']) ?></td>
<td><?= htmlspecialchars($r['usuario']) ?></td>
<td>
    <span class="badge <?= $r['status']=='Em uso' ? 'badge-em-uso' : 'badge-finalizado' ?>">
        <?= $r['status'] ?>
    </span>
</td>
<td><?= $r['data_entrega'] ?? '---' ?></td>
<td>
<?php if ($r['status']=="Em uso"): ?>
<form method="POST" action="scooters/devolver.php">
<input type="hidden" name="reserva_id" value="<?= $r['id'] ?>">
<button class="btn btn-devolver btn-sm">Devolver</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</div>
</body>
</html>