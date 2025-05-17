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

// Total para futura paginación si se desea
$total = $db->query("SELECT COUNT(*) FROM promociones")->fetchColumn();
$total_pages = ceil($total / $limit);
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900 border-b-2 border-blue-500 inline-block pb-1">
        Gestión de Promociones
    </h1>
    <a href="crear.php" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition">
        <i class="bi bi-plus-circle text-lg"></i>
        <span class="font-medium">Nueva Promoción</span>
    </a>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="flex items-start gap-3 mb-4 p-4 rounded-lg
        <?= $_SESSION['message_type'] === 'success' ? 'bg-green-100 text-green-800' : '' ?>
        <?= $_SESSION['message_type'] === 'danger' ? 'bg-red-100 text-red-800' : '' ?>
        <?= $_SESSION['message_type'] === 'warning' ? 'bg-yellow-100 text-yellow-800' : '' ?>">
        <svg class="w-6 h-6 mt-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8zM9 9v4h2V9H9zm0-4v2h2V5H9z" clip-rule="evenodd"/>
        </svg>
        <div><?= $_SESSION['message'] ?></div>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

<div class="overflow-x-auto rounded-lg shadow border border-gray-200 bg-white">
    <table class="w-full table-auto border-collapse">
        <thead class="bg-gray-100 text-sm text-gray-700 uppercase">
            <tr>
                <th class="p-3 text-left">Título</th>
                <th class="p-3 text-left">Descuento</th>
                <th class="p-3 text-left">Fechas</th>
                <th class="p-3 text-left">Activa</th>
                <th class="p-3 text-left">Imagen</th>
                <th class="p-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-800 text-sm">
            <?php foreach ($promociones as $promo): ?>
            <tr class="hover:bg-gray-50">
                <td class="p-3"><?= htmlspecialchars($promo['titulo']) ?></td>
                <td class="p-3"><?= number_format($promo['descuento'], 2) ?>%</td>
                <td class="p-3">
                    <?= htmlspecialchars($promo['fecha_inicio']) ?><br>
                    <small class="text-gray-500">hasta <?= htmlspecialchars($promo['fecha_fin']) ?></small>
                </td>
                <td class="p-3">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                        <?= $promo['activa'] ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' ?>">
                        <i class="bi <?= $promo['activa'] ? 'bi-check-circle' : 'bi-x-circle' ?>"></i>
                        <?= $promo['activa'] ? 'Sí' : 'No' ?>
                    </span>
                </td>
                <td class="p-3">
                    <?php if (!empty($promo['imagen_url'])): ?>
                        <img src="/bestune2/public/<?= htmlspecialchars($promo['imagen_url']) ?>"
                             alt="Imagen" class="h-14 w-auto rounded-md object-cover shadow-sm">
                    <?php else: ?>
                        <span class="text-gray-400 italic">Sin imagen</span>
                    <?php endif; ?>
                </td>
                <td class="p-3 flex justify-center gap-2">
                    <a href="editar.php?id=<?= $promo['id_promocion'] ?>" class="text-blue-600 hover:text-blue-800">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </a>
                    <a href="../Includes/actions/promociones/eliminar.php?id=<?= $promo['id_promocion'] ?>"
                       onclick="return confirm('¿Estás seguro de eliminar esta promoción?')"
                       class="text-red-600 hover:text-red-800">
                        <i class="bi bi-trash text-lg"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
