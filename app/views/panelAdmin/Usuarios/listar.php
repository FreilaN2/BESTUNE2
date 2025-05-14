<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

if (!isAdmin()) {
    $_SESSION['message'] = "No tienes permisos para acceder a esta sección";
    $_SESSION['message_type'] = "danger";
    header("Location: ../index.php");
    exit();
}

$page_title = 'Gestión de Usuarios';
require_once '../includes/header.php';

// Paginación
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Obtener usuarios
$stmt = $db->prepare("SELECT * FROM usuarios ORDER BY fecha_registro DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll();

// Contar total de usuarios
$total = $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$total_pages = ceil($total / $limit);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Usuarios</h1>
    <a href="crear.php" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nuevo Usuario
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Registro</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= $usuario['id_usuario'] ?></td>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= htmlspecialchars($usuario['telefono'] ?? 'N/A') ?></td>
                        <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                        <td>
                            <span class="badge bg-<?= $usuario['es_administrador'] ? 'primary' : 'secondary' ?>">
                                <?= $usuario['es_administrador'] ? 'Administrador' : 'Usuario' ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $usuario['activo'] ? 'success' : 'danger' ?>">
                                <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td>
                            <a href="editar.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if ($usuario['id_usuario'] != $_SESSION['user_id']): ?>
                            <a href="eliminar.php?id=<?= $usuario['id_usuario'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Anterior</a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Siguiente</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>