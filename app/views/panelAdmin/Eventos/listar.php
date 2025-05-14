<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Gestión de Eventos';
require_once '../includes/header.php';

// Consulta base
$sql = "SELECT e.*, u.nombre as creador 
        FROM eventos e
        LEFT JOIN usuarios u ON e.id_usuario_creador = u.id_usuario
        ORDER BY e.fecha DESC";

$eventos = $db->query($sql)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Eventos</h1>
    <a href="crear.php" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nuevo Evento
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Imagen</th>
                        <th>Creador</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eventos as $evento): ?>
                    <tr>
                        <td><?= $evento['id_evento'] ?></td>
                        <td><?= htmlspecialchars($evento['titulo']) ?></td>
                        <td title="<?= date('Y-m-d H:i:s', strtotime($evento['fecha'])) ?>">
                            <?= date('d/m/Y H:i', strtotime($evento['fecha'])) ?>
                        </td>
                        <td>
                            <?php if (!empty($evento['imagen'])): ?>
                                <img src="../<?= htmlspecialchars($evento['imagen']) ?>" alt="Imagen" style="max-height: 50px;" class="img-thumbnail">
                            <?php else: ?>
                                <span class="text-muted">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($evento['creador'] ?? 'Sistema') ?></td>
                        <td>
                            <a href="editar.php?id=<?= $evento['id_evento'] ?>" class="btn btn-sm btn-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="../includes/actions/eventos/eliminar.php?id=<?= $evento['id_evento'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Estás seguro de eliminar este evento?')" 
                               title="Eliminar">
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
