<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['reserva_ativa_id'])) {
    header("Location: dashboard.php");
    exit();
}
include("config/conexao.php");

$reserva_id = (int) $_SESSION['reserva_ativa_id'];
$stmt = $conn->prepare("
    SELECT r.*, s.nome, s.tipo, s.codigo_qr
    FROM reservas r
    JOIN scooters s ON s.id = r.scooter_id
    WHERE r.id = ?
");
$stmt->bind_param("i", $reserva_id);
$stmt->execute();
$reserva = $stmt->get_result()->fetch_assoc();

if (!$reserva) {
    header("Location: dashboard.php");
    exit();
}
if ($reserva['liberada']) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>MobAccess | Escanear QR Code</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{font-family: Arial, Helvetica, sans-serif; box-sizing:border-box;}
body{
    background: linear-gradient(180deg, #05081a 0%, #0a1030 100%);
    min-height: 100vh;
    color: #e8e8f0;
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 30px 15px;
    margin:0;
}
.scan-card{
    background: rgba(0,15,60,.65);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    padding: 30px;
    max-width: 420px;
    width:100%;
    text-align:center;
}
h2{ margin-bottom: 6px; }
h2 span{ color:#9b8cff; }
p.subtitulo{ color:#c9c9d8; font-size:14px; margin-bottom:20px; }
#leitor{ border-radius: 14px; overflow:hidden; margin-bottom: 16px; }
.manual-box{ margin-top: 18px; text-align:left; }
.manual-box label{ font-size:13px; color:#c9c9d8; }
.manual-box input{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid rgba(255,255,255,.1);
    background:#1e2b4b;
    color:#fff;
    margin: 6px 0 10px;
}
.btn-liberar{
    background:#6450ff;
    border:none;
    color:#fff;
    padding: 10px 18px;
    border-radius: 10px;
    width:100%;
    font-weight:600;
}
.btn-liberar:hover{ background:#755fff; }
#resultado{ margin-top: 14px; font-size: 14px; min-height: 20px; }
.sucesso{ color:#2ecc71; font-weight:600; }
.erro{ color:#ff6b6b; font-weight:600; }
.btn-voltar-dash{
    display:none;
    margin-top: 16px;
    background:#2ecc71;
    border:none;
    color:#0a1030;
    padding: 10px 18px;
    border-radius: 10px;
    width:100%;
    font-weight:700;
    text-decoration:none;
}
</style>
</head>
<body>
<div class="scan-card">
    <h2>Pagamento <span>aprovado!</span></h2>
    <p class="subtitulo">Escaneie o QR Code da <?= htmlspecialchars($reserva['nome']) ?> para liberar a scooter.</p>

    <div id="leitor" style="width:100%;"></div>

    <div class="manual-box">
        <label>Ou digite o código manualmente:</label>
        <input type="text" id="codigoManual" placeholder="Ex: MOBACCESS-01">
        <button type="button" class="btn-liberar" onclick="validarCodigo(document.getElementById('codigoManual').value)">Liberar com código</button>
    </div>

    <div id="resultado"></div>
    <a href="dashboard.php" class="btn-voltar-dash" id="btnVoltar">Voltar ao Dashboard</a>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const codigoEsperado = <?= json_encode($reserva['codigo_qr']) ?>;
const reservaId = <?= (int) $reserva_id ?>;
let liberado = false;

function validarCodigo(codigoLido) {
    if (liberado) return;
    const resultado = document.getElementById('resultado');

    if (codigoLido.trim().toUpperCase() === (codigoEsperado || '').trim().toUpperCase()) {
        liberado = true;
        resultado.innerHTML = '<span class="sucesso">✅ QR Code válido! Liberando scooter...</span>';

        fetch('scooters/liberar.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'reserva_id=' + reservaId
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                resultado.innerHTML = '<span class="sucesso">🛵 Scooter liberada! Boa viagem.</span>';
                document.getElementById('btnVoltar').style.display = 'block';
                if (typeof html5QrcodeScanner !== 'undefined') {
                    html5QrcodeScanner.clear().catch(() => {});
                }
            } else {
                resultado.innerHTML = '<span class="erro">Erro ao liberar: ' + data.message + '</span>';
                liberado = false;
            }
        });
    } else {
        resultado.innerHTML = '<span class="erro">❌ Código não corresponde a esta reserva.</span>';
    }
}

var html5QrcodeScanner = new Html5QrcodeScanner("leitor", { fps: 10, qrbox: 220 });
html5QrcodeScanner.render(
    (decodedText) => validarCodigo(decodedText),
    (erro) => { /* leitura contínua, ignora frames sem QR */ }
);
</script>
</body>
</html>