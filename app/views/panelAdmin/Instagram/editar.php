<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ID de post no proporcionado";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

$id = (int)$_GET['id'];
$page_title = 'Editar Post de Instagram';
require_once '../includes/header.php';

$stmt = $db->prepare("SELECT * FROM instagram_posts WHERE id_post = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    $_SESSION['message'] = "Post no encontrado";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = trim($_POST['descripcion']);
    $url = trim($_POST['url_post']);
    $visible = isset($_POST['visible']) ? 1 : 0;

    $errors = [];

    if (empty($descripcion)) $errors[] = "La descripción es requerida";
    if (empty($url)) $errors[] = "La URL es requerida";
    if (!filter_var($url, FILTER_VALIDATE_URL)) $errors[] = "La URL no es válida";

    $media_actual = $post['url_media'];
    $media_url = $media_actual;

    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'];
        $ext = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de archivo no permitido.";
        } elseif ($_FILES['media']['size'] > 10 * 1024 * 1024) {
            $errors[] = "El archivo es demasiado grande. Máx. 10MB.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/instagram/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $archivo_anterior = realpath(__DIR__ . '/../../../../') . '/public/' . ltrim($media_actual, '/');
            if (!empty($media_actual) && file_exists($archivo_anterior)) unlink($archivo_anterior);

            $media_nombre = 'instagram_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destino = $upload_dir . $media_nombre;

            if (move_uploaded_file($_FILES['media']['tmp_name'], $destino)) {
                $media_url = 'assets/img/instagram/' . $media_nombre;
            } else {
                $errors[] = "Error al subir el archivo.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE instagram_posts SET descripcion = ?, url_post = ?, visible = ?, url_media = ?, id_usuario_actualizador = ?, fecha_actualizacion = NOW() WHERE id_post = ?");
            $stmt->execute([$descripcion, $url, $visible, $media_url, $_SESSION['user_id'], $id]);

            $_SESSION['message'] = "Post actualizado exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Error al guardar el post: " . $e->getMessage();
        }
    }
}

?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Editar Post de Instagram</h4>
        <a href="listar.php" class="btn btn-secondary btn-round ml-auto">
            <i class="fa fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="row">
        <div class="col-md-10 offset-md-1">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg border rounded">
                <div class="card-header">
                    <div class="card-title"><i class="fab fa-instagram"></i> Datos del Post</div>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="descripcion">Descripción *</label>
                                    <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required><?= htmlspecialchars($_POST['descripcion'] ?? $post['descripcion']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="url_post">URL *</label>
                                    <input type="url" name="url_post" id="url_post" class="form-control" required value="<?= htmlspecialchars($_POST['url_post'] ?? $post['url_post']) ?>">
                                </div>

                                <div class="form-check mt-3">
                                    <label>
                                        <input type="checkbox" name="visible" <?= ($post['visible'] ?? 0) ? 'checked' : '' ?>>
                                        <span class="form-check-sign">Visible en el sitio</span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="media">Media (imagen o video)</label>
                                    <input type="file" name="media" id="media" class="form-control-file" accept="image/*,video/*">
                                    <small class="form-text text-muted">Formatos: JPG, PNG, GIF, MP4, WEBM. Máx. 10MB</small>

                                    <?php if (!empty($post['url_media'])): ?>
                                        <div class="mt-3">
                                            <?php if (preg_match('/\.(mp4|webm)$/i', $post['url_media'])): ?>
                                                <video src="/BESTUNE2/public/<?= htmlspecialchars($post['url_media']) ?>" controls class="rounded shadow" style="max-height: 180px;"></video>
                                            <?php else: ?>
                                                <img src="/BESTUNE2/public/<?= htmlspecialchars($post['url_media']) ?>" class="rounded border" style="max-height: 180px;" alt="Media actual">
                                            <?php endif; ?>
                                            <p class="text-muted mt-1">Media actual</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="card-action text-right mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar Cambios
                            </button>
                            <a href="listar.php" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</main>
</div>
<?php require_once '../includes/footer.php'; ?>
