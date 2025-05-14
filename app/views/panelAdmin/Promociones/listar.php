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
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : 'todas';
$filtro_activas = isset($_GET['activas']) ? $_GET['activas'] : 'todas';

// Consulta base
$sql = "SELECT p.*, u.nombre as creador 
        FROM promociones p
        LEFT JOIN usuarios u ON p.id_usuario_creador = u.id_usuario";

$where = [];
$params = [];

if ($filtro_estado === 'activas') {
    $where[] = "p.activa = TRUE";
} elseif ($filtro_estado === 'inactivas') {
    $where[] = "p.activa = FALSE";
}

if ($filtro_activas === 'vigentes') {
    $where[] = "p.fecha_inicio <= CURDATE() AND p.fecha_fin >= CURDATE()";
} elseif ($filtro_activas === 'futuras') {
    $where[] = "p.fecha_inicio > CURDATE()";
} elseif ($filtro_activas === 'expiradas') {
    $where[] = "p.fecha_fin < CURDATE()";
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY p.fecha_inicio DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $db->prepare($sql);
foreach ($params as $i => $param) {
    $stmt->bindValue($i + 1, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->execute();
$promociones = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Promociones</h1>
    <a href="crear.php" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nueva Promoción
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descuento</th>
                        <th>Fechas</th>
                        <th>Activa</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($promociones as $promo): ?>
                        <tr>
                            <td><?= htmlspecialchars($promo['titulo']) ?></td>
                            <td><?= number_format($promo['descuento'], 2) ?>%</td>
                            <td>
                                <?= htmlspecialchars($promo['fecha_inicio']) ?> <br>
                                <small class="text-muted">hasta <?= htmlspecialchars($promo['fecha_fin']) ?></small>
                            </td>
                            <td>
                                <?= $promo['activa'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?>
                            </td>
                            <td>
                                <?php if (!empty($promo['imagen_url'])): ?>
                                    <img src="../<?= htmlspecialchars($promo['imagen_url']) ?>" alt="Imagen" class="img-thumbnail" style="max-height: 60px;">
                                <?php else: ?>
                                    <span class="text-muted">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="editar.php?id=<?= $promo['id_promocion'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="../Includes/actions/promociones/eliminar.php?id=<?= $promo['id_promocion'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('¿Estás seguro de eliminar esta promoción?')">
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
