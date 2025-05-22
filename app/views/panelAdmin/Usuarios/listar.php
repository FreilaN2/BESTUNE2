<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

if (!isAdmin()) {
    $_SESSION['message'] = "No tienes permisos para acceder a esta sección";
    $_SESSION['message_type'] = "danger";
    header("Location: " . PANEL_PATH . "app/views/panelAdmin/index.php");
    exit();
}

$page_title = 'Gestión de Usuarios';
require_once '../includes/header.php';

$stmt = $db->prepare("SELECT * FROM usuarios ORDER BY fecha_registro DESC");
$stmt->execute();
$usuarios = $stmt->fetchAll();
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Gestión de Usuarios</h4>
        <a href="crear.php" class="btn btn-success btn-round ml-auto">
            <i class="fa fa-plus"></i> Nuevo Usuario
        </a>
    </div>

    <br>
    <?php if (isset($_SESSION['message'])): ?>
    <script>
        $(document).ready(function () {
            $.notify({
                icon: 'flaticon-info',
                title: 'Mensaje',
                message: '<?= $_SESSION['message'] ?>',
            },{
                type: '<?= $_SESSION['message_type'] ?>',
                placement: {
                    from: "top",
                    align: "right"
                },
                delay: 3000,
            });
        });
    </script>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable-users" class="display table table-striped table-hover">
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
                            <td><?= htmlspecialchars($usuario['telefono'] ?? 'No registrado') ?></td>
                            <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                            <td>
                                <span class="badge badge-<?= $usuario['es_administrador'] ? 'primary' : 'secondary' ?>">
                                    <i class="fa <?= $usuario['es_administrador'] ? 'fa-user-shield' : 'fa-user' ?>"></i>
                                    <?= $usuario['es_administrador'] ? 'Admin' : 'Usuario' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $usuario['activo'] ? 'success' : 'danger' ?>">
                                    <i class="fa <?= $usuario['activo'] ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                                    <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <a href="editar.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                                <?php if ($usuario['id_usuario'] != $_SESSION['user_id'] && $usuario['id_usuario'] != 1): ?>
                                <a href="../includes/actions/usuarios/eliminar.php?id=<?= $usuario['id_usuario'] ?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div> 
    </div>
</div>
</main>
</div> <!-- Cierra wrapper -->

<!-- Librerías necesarias -->
<link rel="stylesheet" href="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/css/datatables.min.css">
<script src="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/js/core/jquery.3.2.1.min.js"></script>
<script src="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/js/plugin/datatables/datatables.min.js"></script>
<script src="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

<!-- Inicialización de DataTables -->
<script>
    $(document).ready(function() {
        $('#datatable-users').DataTable({
        
            "pageLength": 10
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>
