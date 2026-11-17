<?php
// Garante o gerenciamento da sessão do painel administrativo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Parâmetros para simular a demanda em um novo ponto (Ex: segunda-feira, perto do metrô, 300 pessoas de fluxo)
$dia_semana = 1; 
$is_feriado = 0; 
$distancia_metro = 100; 
$is_polo_saude = 0;
$fluxo_estimado = 300; 

$python_path = 'C:/Users/cris/AppData/Local/Programs/Python/Python311/python.exe';

// Usamos __DIR__ . '/predicao_demanda.py' para forçar o Windows a achar o arquivo na pasta exata onde ele está
$script_path = __DIR__ . '/predicao_demanda.py';
$comando = "\"$python_path\" \"$script_path\" $dia_semana $is_feriado $distancia_metro $is_polo_saude $fluxo_estimado 2>&1";

$resultado_python = shell_exec($comando);

// Converte o JSON gerado pelo scikit-learn para uma variável do PHP
$dados_ia = json_decode($resultado_python, true);

// Garante que a conexão existe antes de tentar usar o $conn
if (!isset($conn)) {
    @include_once __DIR__ . "/conexao.php";
}

// Se o $conn não estiver disponível, interrompe o bloco lógico analítico
if (!isset($conn)) {
    return;
}

// Helpers da camada analítica. Este arquivo NÃO imprime nada e NÃO executa Python.

if (!function_exists('zona_da_coordenada')) {
    function zona_da_coordenada(float $lat, float $lng): string {
        return sprintf('%.2f,%.2f', round($lat, 2), round($lng, 2));
    }
}

if (!function_exists('registrar_fluxo')) {
    function registrar_fluxo(mysqli $conn, int $scooter_id, string $campo): void {
        $permitidos = ['reservas_iniciadas', 'devolucoes'];
        if (!in_array($campo, $permitidos, true)) { return; }
        try {
            $stmt = $conn->prepare("SELECT latitude, longitude FROM scooters WHERE id = ?");
            $stmt->bind_param("i", $scooter_id);
            $stmt->execute();
            $s = $stmt->get_result()->fetch_assoc();
            if (!$s) { return; }

            $zona = zona_da_coordenada((float) $s['latitude'], (float) $s['longitude']);

            $sql = "INSERT INTO medicoes_fluxo (zona, janela_inicio, $campo)
                    VALUES (?, DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), 1)
                    ON DUPLICATE KEY UPDATE $campo = $campo + 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $zona);
            $stmt->execute();
        } catch (Throwable $e) {
            // a análise em segundo plano não deve quebrar o fluxo principal
        }
    }
}
?>

<!-- ======================================================================= -->
<!-- CAMADA VISUAL: Renderização dos Resultados no Painel MobAccess          -->
<!-- ======================================================================= -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Módulo de Inteligência Analítica - MobAccess</title>
    <style>
        .card-ia-container {
            background: #ffffff;
            max-width: 750px;
            margin: 40px auto;
            padding: 25px;
            border-radius: 12px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-top: 6px solid #0056b3;
        }
        .header-ia { color: #0056b3; margin-top: 0; font-size: 20px; display: flex; align-items: center; gap: 10px; }
        .box-resultado { background: #f4f7fa; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #2ecc71; }
        .box-erro { background: #fff5f5; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #e74c3c; color: #c0392b; }
        .numero-scooters { font-size: 28px; color: #2c3e50; font-weight: bold; margin: 10px 0; }
    </style>
</head>
<body>

<div class="card-ia-container">
    <h2 class="header-ia">🧠 Inteligência Artificial - Análise de Demanda Reativa</h2>
    <p style="color: #555; font-size: 14px;">Processamento preditivo baseado no algoritmo <strong>RandomForestRegressor (scikit-learn)</strong> conforme item 5.4 do TCC.</p>
    
    <?php if (isset($dados_ia['status']) && $dados_ia['status'] === 'sucesso'): ?>
        <div class="box-resultado">
            <p style="margin: 0; color: #666; font-size: 13px;">Recomendação de Rebalanceamento Inteligente da Frota:</p>
            <div class="numero-scooters">
                <?php echo $dados_ia['scooters_recomendadas'] ?? 0; ?> Scooters Adaptadas
            </div>
            <span style="font-size: 12px; color: #27ae60; font-weight: bold;">✔ Resposta síncrona retornada com sucesso do Python para o PHP.</span>
        </div>
    <?php else: ?>
        <div class="box-erro">
            <strong>⚠️ Falha na execução da IA:</strong>
            <p style="font-size: 13px; margin: 8px 0;">O interpretador do PHP não conseguiu ler os dados estruturados do script Python.</p>
            <span style="font-size: 11px; color: #7f8c8d;">Retorno bruto obtido: <?php echo htmlspecialchars($resultado_python ?? 'Vazio (Verifique se o Python está configurado no PATH do Windows).'); ?></span>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
