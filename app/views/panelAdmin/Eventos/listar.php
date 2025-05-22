<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Gestión de Eventos';
require_once '../includes/header.php';

$sql = "SELECT e.*, u.nombre as creador 
        FROM eventos e
        LEFT JOIN usuarios u ON e.id_usuario_creador = u.id_usuario
        ORDER BY e.fecha DESC";
$eventos = $db->query($sql)->fetchAll();
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Gestión de Eventos</h4>
        <a href="crear.php" class="btn btn-success btn-round ml-auto">
            <i class="fa fa-plus"></i> Nuevo Evento
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
                <table id="datatable-eventos" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Fecha</th>
                            <th>Imagen</th>
                            <th>Creador</th>
                            <th class="text-center">Acciones</th>
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
                                    <img src="<?= BASE_URL . htmlspecialchars($evento['imagen']) ?>"
                                         alt="Imagen" class="img-fluid rounded" style="max-height: 60px;">
                                <?php else: ?>
                                    <span class="text-muted">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($evento['creador'] ?? 'Sistema') ?></td>
                            <td class="text-center">
                                <a href="editar.php?id=<?= $evento['id_evento'] ?>" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                                <a href="../includes/actions/eventos/eliminar.php?id=<?= $evento['id_evento'] ?>"
                                   onclick="return confirm('¿Estás seguro de eliminar este evento?')"
                                   class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($eventos)): ?>
                            <tr><td colspan="6" class="text-center text-muted">No hay eventos disponibles.</td></tr>
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
    $(document).ready(function() {
        $('#datatable-eventos').DataTable({

            "pageLength": 10
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>
