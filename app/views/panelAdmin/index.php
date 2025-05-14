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

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Usuarios</h5>
                <p class="card-text display-4"><?= $stats['usuarios'] ?></p>
                <a href="usuarios/listar.php" class="text-white">Ver todos</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h5 class="card-title">Promociones Activas</h5>
                <p class="card-text display-4"><?= $stats['promociones'] ?></p>
                <a href="promociones/listar.php" class="text-white">Ver todas</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <h5 class="card-title">Planes Activos</h5>
                <p class="card-text display-4"><?= $stats['planes'] ?></p>
                <a href="planes/listar.php" class="text-white">Ver todos</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title">Próximos Eventos</h5>
                <p class="card-text display-4"><?= $stats['eventos'] ?></p>
                <a href="eventos/listar.php" class="text-white">Ver todos</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Últimos Eventos</h5>
            </div>
            <div class="card-body">
                <?php
                $eventos = $db->query("SELECT e.*, u.nombre as creador 
                                     FROM eventos e
                                     LEFT JOIN usuarios u ON e.id_usuario_creador = u.id_usuario
                                     WHERE e.fecha >= NOW() 
                                     ORDER BY e.fecha ASC 
                                     LIMIT 5")->fetchAll();
                
                if (count($eventos) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($eventos as $evento): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <?= htmlspecialchars($evento['titulo']) ?>
                                    <small class="d-block text-muted">Creado por: <?= htmlspecialchars($evento['creador'] ?? 'Sistema') ?></small>
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    <?= date('d/m/Y H:i', strtotime($evento['fecha'])) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="alert alert-info">No hay eventos próximos</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Últimas Promociones</h5>
            </div>
            <div class="card-body">
                <?php
                $promociones = $db->query("SELECT p.*, u.nombre as creador 
                                         FROM promociones p
                                         LEFT JOIN usuarios u ON p.id_usuario_creador = u.id_usuario
                                         WHERE p.activa = TRUE 
                                         ORDER BY p.fecha_creacion DESC 
                                         LIMIT 5")->fetchAll();
                
                if (count($promociones) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($promociones as $promo): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <?= htmlspecialchars($promo['titulo']) ?>
                                    <small class="d-block text-muted">Código: <?= htmlspecialchars($promo['codigo_promocion']) ?></small>
                                </div>
                                <span class="badge bg-success rounded-pill">
                                    <?= $promo['descuento'] ?>%
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="alert alert-info">No hay promociones activas</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>