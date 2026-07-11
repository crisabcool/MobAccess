<?php
include("config/conexao.php");

$res = $conn->query("
SELECT r.*, s.nome,
CASE 
    WHEN r.data_entrega IS NULL THEN 'Em uso'
    ELSE 'Finalizado'
END AS status
FROM reservas r
JOIN scooters s ON s.id = r.scooter_id
");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">

<h2>Painel Admin</h2>

<table class="table table-bordered">
<tr>
<th>ID</th>
<th>Scooter</th>
<th>Usuário</th>
<th>Status</th>
<th>Entrega</th>
<th>Ação</th>
</tr>

<?php while($r = $res->fetch_assoc()): ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= $r['nome'] ?></td>
<td><?= $r['usuario'] ?></td>
<td>
    <span class="badge bg-<?= $r['status']=='Em uso'?'warning':'success' ?>">
        <?= $r['status'] ?>
    </span>
</td>

<td><?= $r['data_entrega'] ?? '---' ?></td>

<td>
<?php if ($r['status']=="Em uso"): ?>
<form method="POST" action="scooters/devolver.php">
<input type="hidden" name="reserva_id" value="<?= $r['id'] ?>">
<button class="btn btn-danger btn-sm">Devolver</button>
</form>
<?php endif; ?>
</td>

</tr>
<?php endwhile; ?>

</table>
</div>
