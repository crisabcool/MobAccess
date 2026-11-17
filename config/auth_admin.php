<?php
/**
 * Exige um administrador logado. Confere o perfil NO BANCO a cada acesso:
 * se a conta for rebaixada, o acesso cai na hora.
 */
function exigir_admin(mysqli $conn, string $login_url = 'login.php'): void {
    $uid = (int) ($_SESSION['usuario_id'] ?? 0);

    if ($uid <= 0) {                       // não está logado
        unset($_SESSION['admin_logado']);
        header("Location: " . $login_url);
        exit();
    }

    $stmt = $conn->prepare("SELECT perfil FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $linha = $stmt->get_result()->fetch_assoc();

    if (!$linha || $linha['perfil'] !== 'admin') {   // logado, mas não é admin
        unset($_SESSION['admin_logado']);
        http_response_code(403);
        exit("Acesso restrito a administradores.");
    }

    $_SESSION['admin_logado'] = true;      // mantém compatível com o resto do seu arquivo
}