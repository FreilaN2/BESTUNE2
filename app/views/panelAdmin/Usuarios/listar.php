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

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$stmt = $db->prepare("SELECT * FROM usuarios ORDER BY fecha_registro DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll();

$total = $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$total_pages = ceil($total / $limit);
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900 border-b-2 border-blue-500 inline-block pb-1">
        Gestión de Usuarios
    </h1>
    <a href="crear.php" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition">
        <i class="bi bi-plus-circle text-lg"></i>
        <span class="font-medium">Nuevo Usuario</span>
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
                <th class="p-3 text-left">ID</th>
                <th class="p-3 text-left">Nombre</th>
                <th class="p-3 text-left">Email</th>
                <th class="p-3 text-left">Teléfono</th>
                <th class="p-3 text-left">Registro</th>
                <th class="p-3 text-left">Rol</th>
                <th class="p-3 text-left">Estado</th>
                <th class="p-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-800 text-sm">
            <?php foreach ($usuarios as $usuario): ?>
            <tr class="hover:bg-gray-50">
                <td class="p-3"><?= $usuario['id_usuario'] ?></td>
                <td class="p-3"><?= htmlspecialchars($usuario['nombre']) ?></td>
                <td class="p-3"><?= htmlspecialchars($usuario['email']) ?></td>
                <td class="p-3"><?= htmlspecialchars($usuario['telefono'] ?? 'N/A') ?></td>
                <td class="p-3"><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                <td class="p-3">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium 
                        <?= $usuario['es_administrador'] ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-700' ?>">
                        <i class="bi <?= $usuario['es_administrador'] ? 'bi-shield-lock' : 'bi-person' ?>"></i>
                        <?= $usuario['es_administrador'] ? 'Admin' : 'Usuario' ?>
                    </span>
                </td>
                <td class="p-3">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium 
                        <?= $usuario['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                        <i class="bi <?= $usuario['activo'] ? 'bi-check-circle' : 'bi-x-circle' ?>"></i>
                        <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td class="p-3 flex justify-center gap-2">
                    <a href="editar.php?id=<?= $usuario['id_usuario'] ?>" class="text-blue-600 hover:text-blue-800">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </a>
                    <?php if ($usuario['id_usuario'] != $_SESSION['user_id']): ?>
                    <a href="../includes/actions/usuarios/eliminar.php?id=<?= $usuario['id_usuario'] ?>"
                       onclick="return confirm('¿Estás seguro de eliminar este usuario?')"
                       class="text-red-600 hover:text-red-800">
                        <i class="bi bi-trash text-lg"></i>
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($total_pages > 1): ?>
<div class="mt-6 flex justify-center">
    <ul class="inline-flex -space-x-px text-sm">
        <?php if ($page > 1): ?>
        <li>
            <a href="?page=<?= $page - 1 ?>" class="px-3 py-2 border rounded-l hover:bg-gray-200">←</a>
        </li>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li>
            <a href="?page=<?= $i ?>" class="px-3 py-2 border <?= $i == $page ? 'bg-gray-900 text-white' : 'hover:bg-gray-100' ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
        <li>
            <a href="?page=<?= $page + 1 ?>" class="px-3 py-2 border rounded-r hover:bg-gray-200">→</a>
        </li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
