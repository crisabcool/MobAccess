<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['reserva_pendente'])) {
    header("Location: dashboard.php");
    exit();
}
$r = $_SESSION['reserva_pendente'];
$pixCode = 'MOBACCESS-PIX-' . strtoupper(substr(md5($r['scooter_id'] . time()), 0, 12));
$qrImgUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($pixCode);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>MobAccess | Pagamento</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
*{font-family: Arial, Helvetica, sans-serif;}
body{
    background: linear-gradient(180deg, #05081a 0%, #0a1030 100%);
    min-height: 100vh;
    color: #e8e8f0;
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 30px 15px;
}
.pagamento-card{
    background: rgba(0,15,60,.65);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    padding: 35px;
    max-width: 480px;
    width:100%;
}
.logo-area{
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom: 20px;
}
.logo-area img{
    height: 36px;
    filter: drop-shadow(0 0 6px rgba(100,80,255,.6));
}
.logo-area span{
    color:#9b8cff;
    font-weight:700;
}
.resumo{
    background: rgba(255,255,255,.04);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 22px;
    font-size: 14px;
}
.resumo .valor{
    font-size: 26px;
    font-weight: 700;
    color:#2ecc71;
}
.nav-pagamento .nav-link{
    color:#c9c9d8;
    border-radius: 10px;
    margin-right: 6px;
}
.nav-pagamento .nav-link.active{
    background:#6450ff;
    color:#fff;
}
.form-control{
    background:#1e2b4b;
    border: 1px solid rgba(255,255,255,.1);
    color:#fff;
}
.form-control:focus{
    background:#1e2b4b;
    color:#fff;
    border-color:#9b8cff;
    box-shadow: 0 0 0 .2rem rgba(155,140,255,.25);
}
label{ font-size: 13px; color:#c9c9d8; margin-bottom:4px; display:block; }
.btn-confirmar{
    background:#6450ff;
    border:none;
    color:#fff;
    padding: 12px;
    font-weight:600;
}
.btn-confirmar:hover{ background:#755fff; color:#fff; }
.pix-box{ text-align:center; }
.pix-code{
    background:#1e2b4b;
    padding: 10px;
    border-radius: 8px;
    font-size: 12.5px;
    word-break: break-all;
    margin-top: 10px;
}
</style>
</head>
<body>
<div class="pagamento-card">

    <div class="logo-area">
        <img src="images/scooter_img.webp" alt="MobAccess">
        <span>MobAccess · Pagamento</span>
    </div>

    <div class="resumo">
        <div><?= htmlspecialchars($r['nome']) ?> — <?= htmlspecialchars($r['tipo']) ?></div>
        <div><?= $r['horas'] ?> hora(s) × R$ <?= number_format($r['preco_hora'],2,',','.') ?></div>
        <div class="valor mt-2">R$ <?= number_format($r['valor_total'],2,',','.') ?></div>
    </div>

    <ul class="nav nav-pills nav-pagamento mb-3" id="tabsPagamento">
        <li class="nav-item">
            <button type="button" class="nav-link active" onclick="selecionarMetodo(this,'debito')">Débito</button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" onclick="selecionarMetodo(this,'credito')">Crédito</button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" onclick="selecionarMetodo(this,'pix')">Pix</button>
        </li>
    </ul>

    <form action="processar_pagamento.php" method="POST" id="formPagamento">
        <input type="hidden" name="metodo" id="inputMetodo" value="debito">

        <div id="camposCartao">
            <label>Número do cartão</label>
            <input type="text" class="form-control mb-3" placeholder="0000 0000 0000 0000" maxlength="19" required>

            <label>Nome impresso no cartão</label>
            <input type="text" class="form-control mb-3" placeholder="Como está no cartão" required>

            <div class="row">
                <div class="col-7">
                    <label>Validade</label>
                    <input type="text" class="form-control mb-3" placeholder="MM/AA" maxlength="5" required>
                </div>
                <div class="col-5">
                    <label>CVV</label>
                    <input type="text" class="form-control mb-3" placeholder="123" maxlength="4" required>
                </div>
            </div>
        </div>

        <div id="campoPix" class="pix-box" style="display:none;">
            <img src="<?= $qrImgUrl ?>" alt="QR Code Pix" class="rounded">
            <div class="pix-code"><?= $pixCode ?></div>
            <small class="text-secondary d-block mt-2">Simulação — nenhuma cobrança real será feita.</small>
        </div>

        <button type="submit" class="btn btn-confirmar w-100 mt-3">Confirmar Pagamento</button>
        <a href="dashboard.php" class="btn btn-link text-secondary w-100 mt-1">Cancelar</a>
    </form>

</div>

<script>
function selecionarMetodo(btn, metodo) {
    document.querySelectorAll('#tabsPagamento .nav-link').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('inputMetodo').value = metodo;

    var campoCartao = document.getElementById('camposCartao');
    var campoPix = document.getElementById('campoPix');
    var camposInputs = campoCartao.querySelectorAll('input');

    if (metodo === 'pix') {
        campoCartao.style.display = 'none';
        campoPix.style.display = 'block';
        camposInputs.forEach(i => i.required = false);
    } else {
        campoCartao.style.display = 'block';
        campoPix.style.display = 'none';
        camposInputs.forEach(i => i.required = true);
    }
}
</script>

</body>
</html>