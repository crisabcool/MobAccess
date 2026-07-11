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
<title>Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Leaflet (MAPA) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
#map {
    height: 400px;
    border-radius: 10px;
}
.card-scooter {
    transition: 0.2s;
}
.card-scooter:hover {
    transform: scale(1.02);
}
</style>
</head>
<body class="bg-light">
<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>🚀 Dashboard</h3>
    <div>
        <span class="me-2">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?></span>
        <a href="admin.php" class="btn btn-dark btn-sm">Admin</a>
        <a href="logout.php" class="btn btn-secondary btn-sm">Sair</a>
    </div>
</div>

<!-- MAPA -->
<div id="map" class="mb-4"></div>

<!-- LISTA -->
<div class="row">
<?php foreach ($scooters as $s): ?>
<div class="col-md-4 col-sm-6 mb-3">
<div class="card card-scooter p-3">
<h5><?= htmlspecialchars($s['nome']) ?></h5>
<?php if ($s['disponivel']): ?>
    <span class="badge bg-success mb-2">Disponível</span>
    <form action="scooters/reservar.php" method="POST">
        <input type="hidden" name="scooter_id" value="<?= $s['id'] ?>">
        <input type="hidden" name="usuario" value="<?= htmlspecialchars($_SESSION['usuario']) ?>">
        <button class="btn btn-primary w-100">Reservar</button>
    </form>
<?php else: ?>
    <span class="badge bg-danger">Indisponível</span>
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