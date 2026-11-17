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

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
<h2>Dashboard</h2>

<?php while($s = $scooters->fetch_assoc()): ?>
<div class="card p-3 mb-3">

    <h5><?= $s['nome'] ?></h5>

    <?php if ($s['ocupada'] > 0): ?>
        <span class="badge bg-danger">Indisponível</span>
    <?php else: ?>
        <form action="scooters/reservar.php" method="POST">
            <input type="hidden" name="scooter_id" value="<?= $s['id'] ?>">
            <input type="hidden" name="usuario" value="<?= $_SESSION['usuario'] ?>">
            <button class="btn btn-success">Reservar</button>
        </form>
    <?php endif; ?>

</div>
<?php endwhile; ?>

<a href="admin.php" class="btn btn-dark">Admin</a>
<a href="logout.php" class="btn btn-secondary">Sair</a>

</div>
