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

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900 border-b-2 border-blue-500 inline-block pb-1">
        Gestión de Eventos
    </h1>
    <a href="crear.php" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition">
        <i class="bi bi-plus-circle text-lg"></i>
        <span class="font-medium">Nuevo Evento</span>
    </a>
</div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm bg-white">
        <table class="w-full table-auto text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase">
                <tr>
                    <th class="p-3">ID</th>
                    <th class="p-3">Título</th>
                    <th class="p-3">Fecha</th>
                    <th class="p-3">Imagen</th>
                    <th class="p-3">Creador</th>
                    <th class="p-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-800">
                <?php foreach ($eventos as $evento): ?>
                <tr class="hover:bg-gray-50 even:bg-gray-50">
                    <td class="p-3"><?= $evento['id_evento'] ?></td>
                    <td class="p-3"><?= htmlspecialchars($evento['titulo']) ?></td>
                    <td class="p-3" title="<?= date('Y-m-d H:i:s', strtotime($evento['fecha'])) ?>">
                        <?= date('d/m/Y H:i', strtotime($evento['fecha'])) ?>
                    </td>
                    <td class="p-3">
                        <?php if (!empty($evento['imagen'])): ?>
                        <img src="/bestune2/public/<?= htmlspecialchars($evento['imagen']) ?>" alt="Imagen" class="h-14 w-auto rounded-md object-cover shadow-sm">
                        <?php else: ?>
                        <span class="text-gray-400 italic">Sin imagen</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3"><?= htmlspecialchars($evento['creador'] ?? 'Sistema') ?></td>
                    <td class="p-3 text-center flex justify-center gap-2">
                        <a href="editar.php?id=<?= $evento['id_evento'] ?>" class="text-blue-600 hover:text-blue-800" title="Editar">
                            <i class="bi bi-pencil-square text-lg"></i>
                        </a>
                        <a href="../includes/actions/eventos/eliminar.php?id=<?= $evento['id_evento'] ?>"
                           onclick="return confirm('¿Estás seguro de eliminar este evento?')"
                           class="text-red-600 hover:text-red-800" title="Eliminar">
                            <i class="bi bi-trash text-lg"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
