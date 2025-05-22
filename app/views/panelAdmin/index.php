<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
checkAuth();

$page_title = 'Dashboard';
require_once 'includes/header.php';

$stats = [
    'usuarios' => $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
    'promociones' => $db->query("SELECT COUNT(*) FROM promociones WHERE activa = TRUE")->fetchColumn(),
    'planes' => $db->query("SELECT COUNT(*) FROM planes WHERE activo = TRUE")->fetchColumn(),
    'eventos' => $db->query("SELECT COUNT(*) FROM eventos WHERE fecha >= NOW()")->fetchColumn()
];

$eventos = $db->query("SELECT e.*, u.nombre as creador FROM eventos e LEFT JOIN usuarios u ON e.id_usuario_creador = u.id_usuario WHERE e.fecha >= NOW() ORDER BY e.fecha ASC LIMIT 5")->fetchAll();
$promociones = $db->query("SELECT p.*, u.nombre as creador FROM promociones p LEFT JOIN usuarios u ON p.id_usuario_creador = u.id_usuario WHERE p.activa = TRUE ORDER BY p.fecha_creacion DESC LIMIT 5")->fetchAll();
$instagram = $db->query("SELECT * FROM instagram_posts WHERE visible = 1 ORDER BY fecha_actualizacion DESC LIMIT 4")->fetchAll();
?>

<style>
    /* Márgenes laterales adaptativos y centrado del contenido */
    .page-inner {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        margin-left: auto !important;
        margin-right: auto !important;
        max-width: 1440px !important;
    }

    .content, .main-panel {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }

    /* Eliminar márgenes/paddings innecesarios en general */
    .content, .page-inner {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    html, body {
        height: 100%;
        padding-left: 0 !important;
        margin-left: 0 !important;
        overflow-x: hidden;
        
    }
    

    .wrapper {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .main-panel {
        width: 100% !important;
        flex: 1;
    }

    .content, .page-inner {
        width: 100% !important;
        
    }

    .container, .container-fluid {
        padding-left: 0 !important;
        margin-left: 0 !important;
        width: 100% !important;
    }

    .main-header {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }

    /* Imágenes uniformes en Instagram */
    .card-profile .card-img-top,
    .card-profile video.card-img-top {
        height: 250px;
        object-fit: cover;
        width: 100%;
    }
</style>



<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="d-flex align-items-center mb-4">
                <i class="fas fa-tachometer-alt text-primary mr-2" style="font-size: 24px;"></i>
                <h4 class="page-title mb-0">Dashboard / Resumen General</h4>
            </div>

            <!-- Estadísticas -->
            <div class="row">
                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div class="col ml-3">
                                    <p class="card-category">Usuarios</p>
                                    <h4 class="card-title"><?= $stats['usuarios'] ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="<?= PANEL_PATH ?>app/views/panelAdmin/usuarios/listar.php" class="btn btn-sm btn-primary">Ver todos</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-success bubble-shadow-small">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                </div>
                                <div class="col ml-3">
                                    <p class="card-category">Promociones Activas</p>
                                    <h4 class="card-title"><?= $stats['promociones'] ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="<?= PANEL_PATH ?>app/views/panelAdmin/promociones/listar.php" class="btn btn-sm btn-success">Ver todas</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                </div>
                                <div class="col ml-3">
                                    <p class="card-category">Planes Activos</p>
                                    <h4 class="card-title"><?= $stats['planes'] ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="<?= PANEL_PATH ?>app/views/panelAdmin/planes/listar.php" class="btn btn-sm btn-info">Ver todos</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </div>
                                <div class="col ml-3">
                                    <p class="card-category">Próximos Eventos</p>
                                    <h4 class="card-title"><?= $stats['eventos'] ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="<?= PANEL_PATH ?>app/views/panelAdmin/eventos/listar.php" class="btn btn-sm btn-warning">Ver todos</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimos Eventos y Promociones -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card full-height">
                        <div class="card-header">
                            <div class="card-title">Últimos Eventos</div>
                        </div>
                        <div class="card-body">
                            <?php if (count($eventos) > 0): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($eventos as $evento): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?= htmlspecialchars($evento['titulo']) ?></strong><br>
                                                <small class="text-muted">Creado por: <?= htmlspecialchars($evento['creador'] ?? 'Sistema') ?></small>
                                            </div>
                                            <span class="badge badge-primary"><?= date('d/m/Y H:i', strtotime($evento['fecha'])) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">No hay eventos próximos.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card full-height">
                        <div class="card-header">
                            <div class="card-title">Últimas Promociones</div>
                        </div>
                        <div class="card-body">
                            <?php if (count($promociones) > 0): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($promociones as $promo): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?= htmlspecialchars($promo['titulo']) ?></strong><br>
                                                <small class="text-muted">Código: <?= htmlspecialchars($promo['codigo_promocion']) ?></small>
                                            </div>
                                            <span class="badge badge-success"><?= $promo['descuento'] ?>%</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">No hay promociones activas.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimos posts de Instagram -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Últimos Posts de Instagram</div>
                        </div>
                        <div class="card-body">
                            <?php if (count($instagram) > 0): ?>
                                <div class="row">
                                    <?php foreach ($instagram as $post): ?>
                                        <div class="col-md-3">
                                            <div class="card card-profile">
                                                <?php if (preg_match('/\.(mp4|webm)$/i', $post['url_media'])): ?>
                                                    <video src="<?= BASE_URL . $post['url_media'] ?>" controls class="card-img-top rounded"></video>
                                                <?php else: ?>
                                                    <img src="<?= BASE_URL . $post['url_media'] ?>" class="card-img-top rounded" alt="Post de Instagram">
                                                <?php endif; ?>
                                                <div class="card-body text-center">
                                                    <p class="card-text"><?= htmlspecialchars($post['descripcion']) ?></p>
                                                    <a href="<?= htmlspecialchars($post['url_post']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">Ver en Instagram</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No hay publicaciones visibles.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

       </div>
    </div>
</div>

	</main>
</div> <!-- Cierre de .wrapper -->
<?php require_once 'includes/footer.php'; ?>