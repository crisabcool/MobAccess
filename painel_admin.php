<?php
session_start();
if (isset($_GET['acao']) && $_GET['acao'] === 'sair') {
    session_unset();     // Limpa todas as variáveis de sessão
    session_destroy();   // Destrói a sessão atual
    header("Location: painel_admin.php"); // Redireciona para a tela de login
    exit;
}
include_once __DIR__ . '/config/conexao.php';
include_once __DIR__ . '/config/auth_admin.php';
exigir_admin($conn);

// Configuração do Banco de Dados
$host = 'localhost';
$dbname = 'mobaccess';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}

// Lógica de Login do Admin
if (!isset($_SESSION['admin_logado'])) {
    if (isset($_POST['login'])) {
        $user = $_POST['usuario'] ?? '';
        $pass = $_POST['senha'] ?? '';
        
        if ($user === 'cseletrotecnica@gmail.com' && $pass === '321') {
            $_SESSION['admin_logado'] = true;
            header("Location: painel_admin.php");
            exit;
        } else {
            $erro = "Credenciais inválidas!";
        }
    }
}

// Se não estiver logado, exibe a tela de login
if (!isset($_SESSION['admin_logado'])):
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login Administrativo - MobAccess</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0f172a; color: #fff; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #1e293b; padding: 30px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center mb-4">MobAccess <span class="text-primary">Admin</span></h3>
        <?php if(isset($erro)): ?>
            <div class="alert alert-danger py-2"><?= $erro ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>E-mail do Administrador</label>
                <input type="text" name="usuario" class="form-control bg-dark text-white border-secondary" required value="cseletrotecnica@gmail.com">
            </div>
            <div class="mb-3">
                <label>Senha</label>
                <input type="password" name="senha" class="form-control bg-dark text-white border-secondary" required value="321">
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Entrar no Painel</button>
        </form>
    </div>
</body>
</html>
<?php 
    exit; 
endif;

// --- PROCESSAMENTO DAS AÇÕES DOS BOTÕES ---

// 1. Excluir Usuário
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir_usuario' && isset($_GET['id'])) {
    $idDel = $_GET['id'];
    $stmtDel = $pdo->prepare("DELETE FROM usuarios WHERE id = ? OR usuario_id = ?");
    $stmtDel->execute([$idDel, $idDel]);
    header("Location: admin.php#usuarios");
    exit;
}

// 2. Editar Usuário
if (isset($_POST['editar_usuario'])) {
    $idEdit = $_POST['id'];
    $novoEmail = $_POST['email'];
    $stmtUpd = $pdo->prepare("UPDATE usuarios SET email = ? WHERE id = ? OR usuario_id = ?");
    $stmtUpd->execute([$novoEmail, $idEdit, $idEdit]);
    header("Location: admin.php#usuarios");
    exit;
}

// 3. Ações da Scooter (Destravar / Manutenção)
if (isset($_GET['acao']) && $_GET['acao'] == 'status_scooter' && isset($_GET['id']) && isset($_GET['status'])) {
    $scooterId = $_GET['id'];
    $novoStatus = $_GET['status']; // Ex: Disponível, Em Manutenção, Em Uso
    $stmtScooter = $pdo->prepare("UPDATE scooters SET status = ? WHERE id = ?");
    $stmtScooter->execute([$novoStatus, $scooterId]);
    header("Location: admin.php#scooters");
    exit;
}

// Consultas atuais do banco
$stmtUsuarios = $pdo->query("SELECT * FROM usuarios");
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

$stmtScooters = $pdo->query("SELECT * FROM scooters");
$scooters = $stmtScooters->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - MobAccess</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background: #0f172a; color: #fff; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #94a3b8; margin-bottom: 5px; border-radius: 6px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #3b82f6; color: #fff; }
        .main-content { margin-left: 250px; padding: 30px; }
        .card-stat { border: none; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h4 class="text-center text-white mb-4"><i class="fa-solid fa-bolt text-primary"></i> MobAccess</h4>
        <hr class="text-secondary">
        <ul class="nav flex-column">
            <li class="nav-item"><a href="#" class="nav-link active"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a href="#usuarios" class="nav-link"><i class="fa-solid fa-users me-2"></i> Usuários</a></li>
            <li class="nav-item"><a href="#scooters" class="nav-link"><i class="fa-solid fa-motorcycle me-2"></i> Scooters</a></li>
            <a href="painel_admin.php?acao=sair" class="...">
               <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Painel de Controle Administrativo</h2>
            <span class="badge bg-success p-2">Banco conectado: mobaccess</span>
        </div>

        <!-- Cards de Métricas -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card card-stat bg-white p-3">
                    <h6 class="text-muted">Total de Usuários</h6>
                    <h3><?= count($usuarios) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat bg-white p-3">
                    <h6 class="text-muted">Scooters Cadastradas</h6>
                    <h3><?= count($scooters) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat bg-white p-3">
                    <h6 class="text-muted">Faturamento Mensal</h6>
                    <h3>R$ 12.450</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat bg-white p-3">
                    <h6 class="text-muted">CSAT (Satisfação)</h6>
                    <h3>4.8 / 5.0</h3>
                </div>
            </div>
        </div>

        <!-- Tabela de Usuários -->
        <div class="card card-stat bg-white p-4 mb-4" id="usuarios">
            <h4 class="mb-3">Controle de Usuários</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>E-mail</th>
                            <th>Consentimento Geo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($usuarios) > 0): ?>
                            <?php foreach($usuarios as $u): 
                                $uid = $u['id'] ?? $u['usuario_id'] ?? 1;
                                $uemail = $u['email'] ?? 'N/A';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($uid) ?></td>
                                <td><?= htmlspecialchars($uemail) ?></td>
                                <td><span class="badge bg-info">Ativo</span></td>
                                <td>
                                    <!-- Botão Editar Aciona Modal -->
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $uid ?>"><i class="fa-solid fa-pen"></i></button>
                                    <!-- Botão Excluir -->
                                    <a href="?acao=excluir_usuario&id=<?= $uid ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja realmente excluir este usuário?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>

                            <!-- Modal de Edição para cada usuário -->
                            <div class="modal fade" id="editModal<?= $uid ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar Usuário ID: <?= $uid ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?= $uid ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">E-mail</label>
                                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($uemail) ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" name="editar_usuario" class="btn btn-primary">Salvar Alterações</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted">Nenhum usuário encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabela de Scooters -->
        <div class="card card-stat bg-white p-4" id="scooters">
            <h4 class="mb-3">Monitoramento de Scooters</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID Scooter</th>
                            <th>Status Atual</th>
                            <th>Comandos Operacionais</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($scooters) > 0): ?>
                            <?php foreach($scooters as $s): 
                                $sid = $s['id'] ?? 1;
                                $sstatus = $s['status'] ?? 'Disponível';
                            ?>
                            <tr>
                                <td>#<?= htmlspecialchars($sid) ?></td>
                                <td><span class="badge bg-success"><?= htmlspecialchars($sstatus) ?></span></td>
                                <td>
                                    <a href="?acao=status_scooter&id=<?= $sid ?>&status=Disponível" class="btn btn-sm btn-success">Destravar / Disponível</a>
                                    <a href="?acao=status_scooter&id=<?= $sid ?>&status=Em Manutenção" class="btn btn-sm btn-warning">Enviar para Manutenção</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted">Nenhuma scooter cadastrada.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logado']);
    header("Location: admin.php");
    exit;
}
?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const linksMenu = document.querySelectorAll('.sidebar .nav-link');

    linksMenu.forEach(link => {
        link.addEventListener('click', function() {
            // Remove a classe 'active' de todos os links e ajusta a cor
            linksMenu.forEach(item => {
                item.classList.remove('active', 'bg-primary', 'text-white');
                item.classList.add('text-secondary'); // Ajuste para a cor padrão do seu texto inativo
            });

            // Adiciona a classe ativa apenas no item clicado
            this.classList.remove('text-secondary');
            this.classList.add('active', 'text-white');
        });
    });
});
</script>
</body>
</html>