<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Gestión de Promociones';
require_once '../includes/header.php';

// Paginación
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filtros
$filtro_estado = $_GET['estado'] ?? 'todas';
$filtro_activas = $_GET['activas'] ?? 'todas';

$sql = "SELECT p.*, u.nombre as creador FROM promociones p LEFT JOIN usuarios u ON p.id_usuario_creador = u.id_usuario";
$where = [];

if ($filtro_estado === 'activas') $where[] = "p.activa = TRUE";
elseif ($filtro_estado === 'inactivas') $where[] = "p.activa = FALSE";

if ($filtro_activas === 'vigentes') $where[] = "p.fecha_inicio <= CURDATE() AND p.fecha_fin >= CURDATE()";
elseif ($filtro_activas === 'futuras') $where[] = "p.fecha_inicio > CURDATE()";
elseif ($filtro_activas === 'expiradas') $where[] = "p.fecha_fin < CURDATE()";

if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY p.fecha_inicio DESC LIMIT ? OFFSET ?";

$params = [$limit, $offset];
$stmt = $db->prepare($sql);
foreach ($params as $i => $param) {
    $stmt->bindValue($i + 1, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->execute();
$promociones = $stmt->fetchAll();

$total = $db->query("SELECT COUNT(*) FROM promociones")->fetchColumn();
$total_pages = ceil($total / $limit);
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Gestión de Promociones</h4>
        <a href="crear.php" class="btn btn-success btn-round ml-auto">
            <i class="fa fa-plus"></i> Nueva Promoción
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
                <table id="datatable-promos" class="display table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Descuento</th>
                            <th>Fechas</th>
                            <th>Activa</th>
                            <th>Imagen</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($promociones as $promo): ?>
                        <tr>
                            <td><?= htmlspecialchars($promo['titulo']) ?></td>
                            <td><?= number_format($promo['descuento'], 2) ?>%</td>
                            <td>
                                <?= htmlspecialchars($promo['fecha_inicio']) ?><br>
                                <small class="text-muted">hasta <?= htmlspecialchars($promo['fecha_fin']) ?></small>
                            </td>
                            <td>
                                <span class="badge badge-<?= $promo['activa'] ? 'success' : 'secondary' ?>">
                                    <?= $promo['activa'] ? 'Sí' : 'No' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($promo['imagen_url'])): ?>
                                    <img src="<?= BASE_URL ?><?= htmlspecialchars($promo['imagen_url']) ?>"
                                         alt="Imagen" class="img-fluid rounded" style="max-height: 60px;">
                                <?php else: ?>
                                    <span class="text-muted">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="editar.php?id=<?= $promo['id_promocion'] ?>" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                                <a href="../includes/actions/promociones/eliminar.php?id=<?= $promo['id_promocion'] ?>"
                                   onclick="return confirm('¿Estás seguro de eliminar esta promoción?')"
                                   class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($promociones)): ?>
                            <tr><td colspan="6" class="text-center text-muted">No hay promociones disponibles.</td></tr>
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
        $('#datatable-promos').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "pageLength": 10
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>
