<?php
session_start();


// Volta uma pasta para achar a configuração correta
include('../config/conexao.php');

// Pega o código vindo da URL (via GET, como você estruturou)
$codigo = $_GET['codigo'] ?? null;

if (!$codigo) {
    echo "<script>alert('Código não fornecido!'); history.back();</script>";
    exit;
}

// 1. Verifica se a scooter existe e está disponível
// Usando 'qr_code' para casar exatamente com o nome da sua coluna no banco
$sql = "SELECT * FROM scooters WHERE qr_code = '$codigo'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $scooter = $result->fetch_assoc();
    
    // Opcional: Se você quiser garantir que ela só seja liberada se o status for 'disponivel'
    if ($scooter['status'] === 'disponivel') {
        
        // 2. ATUALIZA O BANCO: Muda o status da scooter para 'em_uso'
        $sql_update = "UPDATE scooters SET status = 'em_uso' WHERE qr_code = '$codigo'";
        $conn->query($sql_update);
        
        // Salva o ID da scooter na sessão do usuário (útil para a tela de devolver depois)
        $_SESSION['scooter_atual'] = $scooter['id_scooter'] ?? $scooter['id'];

        echo "
        <script>
            alert('Pronto para uso! Scooter liberada.');
            window.location='../dashboard.php'; 
        </script>
        ";
    } else {
        echo "
        <script>
            alert('Esta scooter já está em uso ou em manutenção!');
            history.back();
        </script>
        ";
    }

} else {
    echo "
    <script>
        alert('QR Code inválido!');
        history.back();
    </script>
    ";
}
?>