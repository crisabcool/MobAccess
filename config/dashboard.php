<?php
session_start();
include("config/conexao.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$scooters = $conn->query("
    SELECT s.*, 
    (SELECT COUNT(*) FROM reservas r 
     WHERE r.scooter_id = s.id AND r.data_entrega IS NULL) AS ocupada
    FROM scooters s
");
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
