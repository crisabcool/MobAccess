<?php
session_start();
include("config/conexao.php");
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
// Busca as scooters cadastradas no banco
$result = $conn->query("SELECT * FROM scooters");
$scooters = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>MobAccess | Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Leaflet (MAPA) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
.user-actions{
    display:flex;
    align-items:center;
    gap:12px;
}
.user-actions .bem-vindo{
    color:#c9c9d8;
    font-size:14px;
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
.btn-mob-secondary{
    background:#34495e;
    color:#fff;
    border:none;
}
.btn-mob-secondary:hover{
    background:#4f6880;
    color:#fff;
}
#map {
    height: 400px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,.4);
    margin-bottom: 30px;
}
.card-scooter {
    background: rgba(0,15,60,.65);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 16px;
    transition: 0.25s;
    color: #fff;
}
.card-scooter:hover {
    transform: scale(1.02);
    border-color: rgba(155,140,255,.4);
}
.card-scooter h5{
    font-weight:600;
}
.badge-disponivel{
    background:#2ecc71;
}
.badge-indisponivel{
    background:#e74c3c;
}
.btn-reservar{
    background:#6450ff;
    border:none;
    color:#fff;
    transition:.3s;
}
.btn-reservar:hover{
    background:#755fff;
    color:#fff;
}
</style>
</head>
<body>
<div class="container mt-4">

<div class="topbar">
    <div class="logo-area">
        <img src="images/scooter_img.webp" alt="MobAccess">
        <h3>Mob<span>Access</span></h3>
    </div>
    <div class="user-actions">
        <span class="bem-vindo">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?></span>
        <a href="admin.php" class="btn btn-mob-dark btn-sm">Admin</a>
        <a href="logout.php" class="btn btn-mob-secondary btn-sm">Sair</a>
    </div>
</div>

<!-- MAPA -->
<div id="map"></div>

<!-- LISTA -->
<div class="row">
<?php foreach ($scooters as $s): ?>
<div class="col-md-4 col-sm-6 mb-3">
<div class="card card-scooter p-3">
<h5><?= htmlspecialchars($s['nome']) ?></h5>
<?php if ($s['disponivel']): ?>
    <span class="badge badge-disponivel mb-2">Disponível</span>
    <form action="scooters/reservar.php" method="POST">
        <input type="hidden" name="scooter_id" value="<?= $s['id'] ?>">
        <input type="hidden" name="usuario" value="<?= htmlspecialchars($_SESSION['usuario']) ?>">
        <button class="btn btn-reservar w-100">Reservar</button>
    </form>
<?php else: ?>
    <span class="badge badge-indisponivel">Indisponível</span>
<?php endif; ?>
</div>
</div>
<?php endforeach; ?>
</div>

</div>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// inicia mapa SP como padrão
var map = L.map('map').setView([-23.5505, -46.6333], 13);
// tiles (mapa)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);
// pega localização do usuário
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        var userLat = position.coords.latitude;
        var userLng = position.coords.longitude;
        map.setView([userLat, userLng], 15);
        L.marker([userLat, userLng])
            .addTo(map)
            .bindPopup("📍 Você está aqui")
            .openPopup();
    });
}
// scooters vindas do PHP
var scooters = <?= json_encode($scooters); ?>;
// adiciona no mapa
scooters.forEach(function(s) {
    var cor = s.disponivel == 1 ? "green" : "red";
    var marker = L.circleMarker(
        [s.latitude, s.longitude],
        { color: cor, radius: 8 }
    ).addTo(map);
    marker.bindPopup(`
        <b>${s.nome}</b><br>
        Status: ${s.disponivel == 1 ? "Disponível" : "Indisponível"}
    `);
});
</script>
</body>
</html>