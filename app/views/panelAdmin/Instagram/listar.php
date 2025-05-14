<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Gestión de Instagram';
require_once '../includes/header.php';

// Obtener todos los posts de Instagram
$sql = "SELECT id_post, descripcion, url_post, url_media, fecha_post, visible 
        FROM instagram_posts 
        ORDER BY fecha_post DESC";
$posts = $db->query($sql)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Posts de Instagram</h1>
    <a href="crear.php" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nuevo Post
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Media</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
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
                                        <video src="../<?= htmlspecialchars($post['url_media']) ?>" style="max-height: 50px;" class="img-thumbnail" muted autoplay loop></video>
                                    <?php else: ?>
                                       <img src="/BESTUNE2/public/<?= htmlspecialchars($post['url_media']) ?>"  alt="Media del post" style="max-height: 50px;" class="img-thumbnail">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin media</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($post['fecha_post'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $post['visible'] ? 'success' : 'secondary' ?>">
                                    <?= $post['visible'] ? 'Visible' : 'Oculto' ?>
                                </span>
                            </td>
                            <td>
                                <a href="editar.php?id=<?= $post['id_post'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="../includes/actions/instagram/eliminar.php?id=<?= $post['id_post'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Estás seguro de eliminar este post?')">
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
