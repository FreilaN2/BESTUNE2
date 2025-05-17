<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
checkAuth();

$page_title = 'Dashboard';
require_once 'includes/header.php';

// Obtener estadísticas
$stats = [
    'usuarios' => $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
    'promociones' => $db->query("SELECT COUNT(*) FROM promociones WHERE activa = TRUE")->fetchColumn(),
    'planes' => $db->query("SELECT COUNT(*) FROM planes WHERE activo = TRUE")->fetchColumn(),
    'eventos' => $db->query("SELECT COUNT(*) FROM eventos WHERE fecha >= NOW()")->fetchColumn()
];
?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
    <!-- Tarjeta usuarios -->
    <div class="bg-blue-600 text-white rounded-lg shadow p-6 hover:scale-[1.02] transition-transform">
        <h5 class="text-lg font-semibold mb-2">Usuarios</h5>
        <p class="text-4xl font-bold"><?= $stats['usuarios'] ?></p>
        <a href="usuarios/listar.php" class="text-white underline text-sm mt-2 inline-block">Ver todos</a>
    </div>

    <!-- Tarjeta promociones -->
    <div class="bg-green-600 text-white rounded-lg shadow p-6 hover:scale-[1.02] transition-transform">
        <h5 class="text-lg font-semibold mb-2">Promociones Activas</h5>
        <p class="text-4xl font-bold"><?= $stats['promociones'] ?></p>
        <a href="promociones/listar.php" class="text-white underline text-sm mt-2 inline-block">Ver todas</a>
    </div>

    <!-- Tarjeta planes -->
    <div class="bg-cyan-600 text-white rounded-lg shadow p-6 hover:scale-[1.02] transition-transform">
        <h5 class="text-lg font-semibold mb-2">Planes Activos</h5>
        <p class="text-4xl font-bold"><?= $stats['planes'] ?></p>
        <a href="planes/listar.php" class="text-white underline text-sm mt-2 inline-block">Ver todos</a>
    </div>

    <!-- Tarjeta eventos -->
    <div class="bg-yellow-500 text-black rounded-lg shadow p-6 hover:scale-[1.02] transition-transform">
        <h5 class="text-lg font-semibold mb-2">Próximos Eventos</h5>
        <p class="text-4xl font-bold"><?= $stats['eventos'] ?></p>
        <a href="eventos/listar.php" class="text-black underline text-sm mt-2 inline-block">Ver todos</a>
    </div>
</div>

<!-- Últimos eventos y promociones -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
    <!-- Últimos eventos -->
    <div class="bg-white rounded-lg shadow p-6">
        <h5 class="text-lg font-semibold mb-4">Últimos Eventos</h5>
        <?php
        $eventos = $db->query("SELECT e.*, u.nombre as creador 
                             FROM eventos e
                             LEFT JOIN usuarios u ON e.id_usuario_creador = u.id_usuario
                             WHERE e.fecha >= NOW() 
                             ORDER BY e.fecha ASC 
                             LIMIT 5")->fetchAll();

        if (count($eventos) > 0): ?>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($eventos as $evento): ?>
                    <li class="py-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium"><?= htmlspecialchars($evento['titulo']) ?></p>
                            <p class="text-sm text-gray-500">Creado por: <?= htmlspecialchars($evento['creador'] ?? 'Sistema') ?></p>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded">
                            <?= date('d/m/Y H:i', strtotime($evento['fecha'])) ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-600">No hay eventos próximos.</p>
        <?php endif; ?>
    </div>

    <!-- Últimas promociones -->
    <div class="bg-white rounded-lg shadow p-6">
        <h5 class="text-lg font-semibold mb-4">Últimas Promociones</h5>
        <?php
        $promociones = $db->query("SELECT p.*, u.nombre as creador 
                                 FROM promociones p
                                 LEFT JOIN usuarios u ON p.id_usuario_creador = u.id_usuario
                                 WHERE p.activa = TRUE 
                                 ORDER BY p.fecha_creacion DESC 
                                 LIMIT 5")->fetchAll();

        if (count($promociones) > 0): ?>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($promociones as $promo): ?>
                    <li class="py-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium"><?= htmlspecialchars($promo['titulo']) ?></p>
                            <p class="text-sm text-gray-500">Código: <?= htmlspecialchars($promo['codigo_promocion']) ?></p>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded">
                            <?= $promo['descuento'] ?>%
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-600">No hay promociones activas.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
