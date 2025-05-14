<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Gestión de Planes';
require_once '../includes/header.php';

// Consulta para obtener los planes
$sql = "SELECT id_plan, nombre_plan, imagen_principal FROM planes ORDER BY nombre_plan ASC";
$planes = $db->query($sql)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Planes</h1>
    <a href="crear.php" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nuevo Plan
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($planes as $plan): ?>
                    <tr>
                        <td><?= $plan['id_plan'] ?></td>
                        <td><?= htmlspecialchars($plan['nombre_plan']) ?></td>
                        <td>
                            <?php if (!empty($plan['imagen_principal'])): ?>
                                <img src="../<?= htmlspecialchars($plan['imagen_principal']) ?>" alt="Imagen del plan" style="max-height: 50px;" class="img-thumbnail">
                            <?php else: ?>
                                <span class="text-muted">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="editar.php?id=<?= $plan['id_plan'] ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="../Includes/actions/planes/eliminar.php?id=<?= $plan['id_plan'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Estás seguro de eliminar este plan?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>