<?php
session_start();
include("config/conexao.php");

// sessões antigas (sem usuario_id) precisam logar de novo
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
$uid = (int) $_SESSION['usuario_id'];

// consentimento de localização (null = ainda não perguntado)
$stmt = $conn->prepare("SELECT consentimento_geo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
$consentimento = $u ? $u['consentimento_geo'] : null;

// existe corrida ativa (scooter liberada e ainda não devolvida)?
$stmt = $conn->prepare("SELECT COUNT(*) AS n FROM reservas
                        WHERE usuario_id = ? AND liberada = 1 AND data_entrega IS NULL");
$stmt->bind_param("i", $uid);
$stmt->execute();
$corridaAtiva = ((int) $stmt->get_result()->fetch_assoc()['n']) > 0;

// scooters cadastradas
$result = $conn->query("SELECT * FROM scooters ORDER BY id");
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

.banner-privacidade{
    background: rgba(100,80,255,.15);
    border: 1px solid rgba(155,140,255,.4);
    border-radius: 14px;
    padding: 16px 18px;
    margin-bottom: 20px;
    color: #e8e8f0;
}
.banner-privacidade p{ margin: 0 0 12px; font-size: 15px; line-height: 1.5; }
.banner-botoes{ display: flex; gap: 10px; flex-wrap: wrap; }
.banner-botoes .btn{ min-height: 44px; }
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
.card-scooter .tipo-preco{
    font-size: 12.5px;
    color: #9b8cff;
    margin-bottom: 8px;
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
/* Numeração no mapa */
.scooter-marker{
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    font-size:14px;
    color:#fff;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,.5);
}
/* Modal escuro */
.modal-content{
    background: #10173a;
    color: #fff;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,.08);
}
.modal-header, .modal-footer{
    border-color: rgba(255,255,255,.08);
}
.modal-title span{
    color:#9b8cff;
}
.modal-body label{
    color:#c9c9d8;
    font-size:13.5px;
    margin-bottom:4px;
}
.modal-body input[readonly]{
    background:#1e2b4b;
    color:#fff;
    border: none;
}
.modal-body input{
    background:#1e2b4b;
    color:#fff;
    border: 1px solid rgba(255,255,255,.1);
}
.valor-destaque{
    font-size: 26px;
    font-weight: 700;
    color: #2ecc71;
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

<?php if ($consentimento === null): ?>
<div id="bannerPrivacidade" class="banner-privacidade" role="region" aria-label="Privacidade e localização">
    <p>
        Para melhorar a segurança e o planejamento da frota, o MobAccess pode registrar a sua
        localização <strong>apenas enquanto você estiver usando uma scooter</strong>.
        Você pode mudar essa escolha quando quiser.
    </p>
    <div class="banner-botoes">
        <button type="button" class="btn btn-primary" onclick="confirmarPermissao(event)">Permitir</button>
        <button type="button" class="btn btn-mob-secondary" onclick="responderConsentimento(false)">Agora não</button>

    </div>
</div>
<?php endif; ?>

<!-- MAPA -->
<div id="map"></div>

<!-- LISTA -->
<div class="row">
<?php foreach ($scooters as $s): ?>
<div class="col-md-4 col-sm-6 mb-3">
<div class="card card-scooter p-3">
<h5><?= htmlspecialchars($s['nome']) ?></h5>
<div class="tipo-preco">
    <?= htmlspecialchars($s['tipo']) ?> · R$ <?= number_format($s['preco_hora'], 2, ',', '.') ?>/hora
</div>
<?php if ($s['disponivel']): ?>
    <span class="badge badge-disponivel mb-2">Disponível</span><br>
    <button
        type="button"
        class="btn btn-reservar w-100"
        onclick="abrirModalReserva(
            <?= (int)$s['id'] ?>,
            '<?= htmlspecialchars($s['nome'], ENT_QUOTES) ?>',
            '<?= htmlspecialchars($s['tipo'], ENT_QUOTES) ?>',
            <?= (float)$s['preco_hora'] ?>
        )">
        Reservar
    </button>
<?php else: ?>
    <span class="badge badge-indisponivel">Indisponível</span>
<?php endif; ?>
</div>
</div>
<?php endforeach; ?>
</div>

</div>

<!-- MODAL DE RESERVA -->
<div class="modal fade" id="modalReserva" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reservar <span id="modalNomeScooter"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
        <form action="scooters/iniciar_reserva.php" method="POST">
        <div class="modal-body">

            <input type="hidden" name="scooter_id" id="modalScooterId">
            <input type="hidden" name="usuario" value="<?= htmlspecialchars($_SESSION['usuario']) ?>">

            <label>Tipo da scooter</label>
            <input type="text" id="modalTipo" class="form-control mb-3" readonly>

            <label>Horas de uso</label>
            <input
                type="number"
                name="horas"
                id="modalHoras"
                class="form-control mb-3"
                min="1"
                max="24"
                value="1"
                oninput="atualizarValor()"
                required>

            <label>Valor a pagar</label>
            <div class="valor-destaque" id="modalValor">R$ 0,00</div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-mob-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-reservar">Confirmar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Bootstrap JS (necessário para o modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
var precoScooter = {};

// adiciona no mapa, numeradas 01, 02, 03...
scooters.forEach(function(s, index) {
    var numero = String(index + 1).padStart(2, '0');
    var cor = s.disponivel == 1 ? "#2ecc71" : "#e74c3c";

    var icone = L.divIcon({
        className: '',
        html: '<div class="scooter-marker" style="background:' + cor + '">' + numero + '</div>',
        iconSize: [34, 34],
        iconAnchor: [17, 17]
    });

    var marker = L.marker([s.latitude, s.longitude], { icon: icone }).addTo(map);

    var botaoPopup = s.disponivel == 1
        ? `<button class="btn btn-sm btn-primary mt-2" onclick="abrirModalReserva(${s.id}, '${s.nome}', '${s.tipo}', ${s.preco_hora})">Reservar</button>`
        : '';

    marker.bindPopup(`
        <b>${s.nome}</b> (nº ${numero})<br>
        Tipo: ${s.tipo} — R$ ${parseFloat(s.preco_hora).toFixed(2).replace('.', ',')}/hora<br>
        Status: ${s.disponivel == 1 ? "Disponível" : "Indisponível"}
        ${botaoPopup}
    `);
});

// ---- Modal de reserva ----
var precoAtual = 0;

function abrirModalReserva(id, nome, tipo, preco) {
    document.getElementById('modalScooterId').value = id;
    document.getElementById('modalNomeScooter').innerText = nome;
    document.getElementById('modalTipo').value = tipo;
    document.getElementById('modalHoras').value = 1;
    precoAtual = preco;
    atualizarValor();

    var modal = new bootstrap.Modal(document.getElementById('modalReserva'));
    modal.show();
}

function atualizarValor() {
    var horas = parseInt(document.getElementById('modalHoras').value) || 0;
    var total = horas * precoAtual;
    document.getElementById('modalValor').innerText =
        'R$ ' + total.toFixed(2).replace('.', ',');
}

// ---- Coleta de localização durante a corrida (com consentimento) ----
var CORRIDA_ATIVA = <?= $corridaAtiva ? 'true' : 'false' ?>;
var CONSENTIMENTO = <?= $consentimento === null ? 'null' : (int) $consentimento ?>;
var watchId = null;
var ultimoEnvio = 0;

function responderConsentimento(aceite) {
    fetch('API/consentimento_geo.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({aceite: aceite})
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.ok) {
            var banner = document.getElementById('bannerPrivacidade');
            if (banner) banner.remove();
            CONSENTIMENTO = aceite ? 1 : 0;
            iniciarRastreioSeNecessario();
        }
    });
}

function pararRastreio() {
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }
}

function iniciarRastreioSeNecessario() {
    if (!CORRIDA_ATIVA || CONSENTIMENTO !== 1 || !navigator.geolocation || watchId !== null) return;

    watchId = navigator.geolocation.watchPosition(function(pos) {
        var agora = Date.now();
        if (agora - ultimoEnvio < 15000) return;   // envia no máximo a cada 15 s
        ultimoEnvio = agora;

        fetch('API/log_geolocalizacao.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                lat: pos.coords.latitude,
                lng: pos.coords.longitude,
                precisao: pos.coords.accuracy
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.ok === false && d.motivo === 'sem_corrida') pararRastreio();
        })
        .catch(function() {});
    }, function() {}, { enableHighAccuracy: true, maximumAge: 10000, timeout: 20000 });

            document.addEventListener("DOMContentLoaded", function() {
        // Procura por qualquer botão ou elemento na página que contenha o texto "Permitir"
        const elementos = document.querySelectorAll("button, a, .btn-permitir");
        
        elementos.forEach(function(el) {
            if (el.textContent.trim() === "Permitir") {
                el.addEventListener("click", function(e) {
                    // Exibe o pop-up com a mensagem pedida
                    alert("Permissão concedida!");

                    // Envia a confirmação para a API em segundo plano
                    fetch('API/consentimento_geo.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ consentimento: 1 })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.ok) {
                            // Oculta o banner de consentimento
                            const banner = el.closest('.card-consentimento') || el.parentElement;
                            if (banner) {
                                banner.style.display = 'none';
                            }
                            location.reload();
                        }
                    })
                    .catch(error => console.error("Erro ao gravar consentimento:", error));
                });
            }
        });
    });
}
function confirmarPermissao(event) {
    // 1. Dispara o pop-up com a mensagem pedida
    alert("Permissão concedida!");

    // 2. Envia a confirmação para a API no backend
    fetch('API/consentimento_geo.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ consentimento: 1 })
    })
    .then(response => response.json())
    .then(data => {
        // Recarrega a página após guardar para atualizar o estado do painel
        location.reload();
    })
    .catch(error => {
        console.error("Erro ao gravar consentimento:", error);
    });
}

iniciarRastreioSeNecessario();
</script>
</body>
</html>