<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Gestión de Instagram';
require_once '../includes/header.php';

$sql = "SELECT id_post, descripcion, url_post, url_media, fecha_post, visible FROM instagram_posts ORDER BY fecha_post DESC";
$posts = $db->query($sql)->fetchAll();
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Gestión de Instagram</h4>
        <a href="crear.php" class="btn btn-success btn-round ml-auto">
            <i class="fa fa-plus"></i> Nuevo Post
        </a>
    </div>

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
                <table id="datatable-instagram" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>Media</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= $post['id_post'] ?></td>
                            <td><?= htmlspecialchars($post['descripcion']) ?></td>
                            <td>
                                <?php if (!empty($post['url_media'])): ?>
                                    <?php if (preg_match('/\.(mp4|webm)$/i', $post['url_media'])): ?>
                                        <video src="<?= BASE_URL . htmlspecialchars($post['url_media']) ?>" class="img-fluid rounded" style="max-height: 60px;" muted autoplay loop></video>
                                    <?php else: ?>
                                        <img src="<?= BASE_URL . htmlspecialchars($post['url_media']) ?>" alt="Media del post" class="img-fluid rounded" style="max-height: 60px;">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin media</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($post['fecha_post'])) ?></td>
                            <td>
                                <span class="badge badge-<?= $post['visible'] ? 'success' : 'secondary' ?>">
                                    <i class="fa <?= $post['visible'] ? 'fa-check-circle' : 'fa-eye-slash' ?>"></i>
                                    <?= $post['visible'] ? 'Visible' : 'Oculto' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="editar.php?id=<?= $post['id_post'] ?>" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                                <a href="../includes/actions/instagram/eliminar.php?id=<?= $post['id_post'] ?>" onclick="return confirm('¿Estás seguro de eliminar este post?')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($posts)): ?>
                            <tr><td colspan="6" class="text-center text-muted">No hay publicaciones.</td></tr>
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
        $('#datatable-instagram').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "pageLength": 10
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>
