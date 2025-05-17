<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Gestión de Planes';
require_once '../includes/header.php';

$sql = "SELECT id_plan, nombre_plan, imagen_principal FROM planes ORDER BY nombre_plan ASC";
$planes = $db->query($sql)->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900 border-b-2 border-blue-500 inline-block pb-1">
        Gestión de Planes
    </h1>
    <a href="crear.php" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition">
        <i class="bi bi-plus-circle text-lg"></i>
        <span class="font-medium">Nuevo Plan</span>
    </a>
</div>

<div class="overflow-x-auto rounded-lg shadow border border-gray-200 bg-white">
    <table class="w-full table-auto border-collapse">
        <thead class="bg-gray-100 text-sm text-gray-700 uppercase">
            <tr>
                <th class="p-3 text-left">ID</th>
                <th class="p-3 text-left">Nombre</th>
                <th class="p-3 text-left">Imagen</th>
                <th class="p-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-800 text-sm">
            <?php foreach ($planes as $plan): ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-3"><?= $plan['id_plan'] ?></td>
                    <td class="p-3"><?= htmlspecialchars($plan['nombre_plan']) ?></td>
                    <td class="p-3">
                        <?php if (!empty($plan['imagen_principal'])): ?>
                            <img src="/bestune2/public/<?= htmlspecialchars($plan['imagen_principal']) ?>" alt="Imagen del plan" class="h-14 rounded-md object-contain shadow-sm">
                        <?php else: ?>
                            <span class="text-gray-400 italic">Sin imagen</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3 flex justify-center gap-2">
                        <a href="editar.php?id=<?= $plan['id_plan'] ?>" class="text-blue-600 hover:text-blue-800" title="Editar">
                            <i class="bi bi-pencil-square text-lg"></i>
                        </a>
                        <a href="../Includes/actions/planes/eliminar.php?id=<?= $plan['id_plan'] ?>" 
                           onclick="return confirm('¿Estás seguro de eliminar este plan?')" 
                           class="text-red-600 hover:text-red-800" title="Eliminar">
                            <i class="bi bi-trash text-lg"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
