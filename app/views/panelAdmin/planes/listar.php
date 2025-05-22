<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Gestión de Planes';
require_once '../includes/header.php';

$sql = "SELECT id_plan, nombre_plan, imagen_principal FROM planes ORDER BY nombre_plan ASC";
$planes = $db->query($sql)->fetchAll();
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Gestión de Planes</h4>
        <a href="crear.php" class="btn btn-success btn-round ml-auto">
            <i class="fa fa-plus"></i> Nuevo Plan
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
            }, {
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
                <table id="datatable-planes" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Imagen</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($planes as $plan): ?>
                        <tr>
                            <td><?= $plan['id_plan'] ?></td>
                            <td><?= htmlspecialchars($plan['nombre_plan']) ?></td>
                            <td>
                                <?php if (!empty($plan['imagen_principal'])): ?>
                                    <img src="<?= BASE_URL . htmlspecialchars($plan['imagen_principal']) ?>" 
                                         alt="Imagen del plan" class="img-fluid rounded" style="max-height: 60px;">
                                <?php else: ?>
                                    <span class="text-muted">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="editar.php?id=<?= $plan['id_plan'] ?>" class="btn btn-sm btn-info" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="../includes/actions/planes/eliminar.php?id=<?= $plan['id_plan'] ?>" 
                                   onclick="return confirm('¿Estás seguro de eliminar este plan?')" 
                                   class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($planes)): ?>
                            <tr><td colspan="4" class="text-center text-muted">No hay planes registrados.</td></tr>
                        <?php endif; ?>
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
    $(document).ready(function () {
        $('#datatable-planes').DataTable({

            "pageLength": 10
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>
