<?php
session_start();
include("config/conexao.php");

// ---- Acesso restrito a administradores ----
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
$uid = (int) $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT perfil FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
if (!$u || $u['perfil'] !== 'admin') {
    http_response_code(403);
    exit("Acesso restrito a administradores.");
}

// ---- Parâmetros da simulação (valores fixos) ----
$dia_semana      = 1;    // segunda-feira
$is_feriado      = 0;
$distancia_metro = 100;
$is_polo_saude   = 0;
$usuarios_hist   = 300;  // precisa estar na mesma escala da coluna usuarios_historico_dia

// Caminho do Python configurável (variável de ambiente MOBACCESS_PYTHON ou o padrão abaixo)
$python_path = getenv('MOBACCESS_PYTHON') ?: 'C:/Users/cris/AppData/Local/Programs/Python/Python311/python.exe';
$script_path = __DIR__ . '/config/predicao_demanda.py';

$saida = '';
$dados_ia = null;

$desativadas = array_map('trim', explode(',', (string) ini_get('disable_functions')));
if (!function_exists('shell_exec') || in_array('shell_exec', $desativadas, true)) {
    $saida = 'shell_exec está desativado neste servidor (comum em hospedagem compartilhada).';
} else {
    $args = implode(' ', array_map('escapeshellarg', array_map('strval', [
        (int) $dia_semana, (int) $is_feriado, (float) $distancia_metro,
        (int) $is_polo_saude, (float) $usuarios_hist,
    ])));
    $comando = escapeshellarg($python_path) . ' ' . escapeshellarg($script_path) . ' ' . $args . ' 2>&1';
    $saida = (string) shell_exec($comando);

    // Lê só a ÚLTIMA linha (o JSON): avisos do Python antes dela não quebram a leitura
    $linhas = preg_split('/\R/', trim($saida));
    $dados_ia = json_decode(end($linhas), true);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Módulo de Inteligência Analítica - MobAccess</title>
    <style>
        .card-ia-container { background:#fff; max-width:750px; margin:40px auto; padding:25px; border-radius:12px;
            font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; box-shadow:0 4px 15px rgba(0,0,0,.08); border-top:6px solid #0056b3; }
        .header-ia { color:#0056b3; margin-top:0; font-size:20px; display:flex; align-items:center; gap:10px; }
        .box-resultado { background:#f4f7fa; padding:15px; border-radius:8px; margin-top:15px; border-left:4px solid #2ecc71; }
        .box-erro { background:#fff5f5; padding:15px; border-radius:8px; margin-top:15px; border-left:4px solid #e74c3c; color:#c0392b; }
        .numero-scooters { font-size:28px; color:#2c3e50; font-weight:bold; margin:10px 0; }
    </style>
</head>
<body>
<div class="card-ia-container">
    <h2 class="header-ia">🧠 Inteligência Artificial - Análise de Demanda Reativa</h2>
    <p style="color:#555; font-size:14px;">Processamento preditivo baseado no algoritmo <strong>RandomForestRegressor (scikit-learn)</strong> conforme item 5.4 do TCC.</p>

    <?php if (is_array($dados_ia) && ($dados_ia['status'] ?? '') === 'sucesso' && isset($dados_ia['scooters_recomendadas'])): ?>
        <div class="box-resultado">
            <p style="margin:0; color:#666; font-size:13px;">Recomendação de Rebalanceamento Inteligente da Frota:</p>
            <div class="numero-scooters"><?= (int) $dados_ia['scooters_recomendadas'] ?> Scooters Adaptadas</div>
            <span style="font-size:12px; color:#27ae60; font-weight:bold;">✔ Resposta retornada com sucesso do Python para o PHP.</span>
        </div>
    <?php else: ?>
        <div class="box-erro">
            <strong>⚠️ Falha na execução da IA:</strong>
            <p style="font-size:13px; margin:8px 0;">Não foi possível ler os dados estruturados do script Python.</p>
            <span style="font-size:11px; color:#7f8c8d;">Retorno bruto: <?= htmlspecialchars($saida !== '' ? $saida : 'Vazio (verifique o caminho do Python).') ?></span>
        </div>
    <?php endif; ?>
</div>
</body>
</html>