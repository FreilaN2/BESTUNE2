<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Crear Post de Instagram';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = trim($_POST['descripcion']);
    $url = trim($_POST['url_post']);
    $visible = isset($_POST['visible']) ? 1 : 0;

    $errors = [];

    if (empty($descripcion)) $errors[] = "La descripción es requerida";
    if (empty($url)) $errors[] = "La URL es requerida";
    if (!filter_var($url, FILTER_VALIDATE_URL)) $errors[] = "La URL no es válida";

    $media_url = null;

    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'];
        $ext = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de archivo no permitido. Use JPG, PNG, GIF, MP4 o WEBM.";
        } elseif ($_FILES['media']['size'] > 10 * 1024 * 1024) {
            $errors[] = "El archivo es demasiado grande. Máximo 10MB permitidos.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/instagram/';
            if ($upload_dir === false) {
                $errors[] = "No se pudo acceder a la carpeta de destino.";
            } else {
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $media_nombre = 'instagram_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $destino = $upload_dir . $media_nombre;

                if (move_uploaded_file($_FILES['media']['tmp_name'], $destino)) {
                    $media_url = 'assets/img/instagram/' . $media_nombre;
                } else {
                    $errors[] = "Error al subir el archivo.";
                }
            }
        }
    } else {
        $errors[] = "Debe subir una foto o video.";
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO instagram_posts 
                (descripcion, url_post, visible, url_media, id_usuario_actualizador) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $descripcion,
                $url,
                $visible,
                $media_url,
                $_SESSION['user_id']
            ]);

            $_SESSION['message'] = "Post creado exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            if ($media_url && file_exists($upload_dir . $media_nombre)) {
                unlink($upload_dir . $media_nombre);
            }
            $errors[] = "Error al guardar el post: " . $e->getMessage();
        }
    }
}

$descripcion_value = $_POST['descripcion'] ?? '';
$url_value = $_POST['url_post'] ?? '';
$visible_checked = isset($_POST['visible']) ? 'checked' : '';
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Crear Post de Instagram</h4>
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
                    <div class="card-title"><i class="fa fa-instagram"></i> Datos del Post</div>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="descripcion">Descripción *</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required><?= htmlspecialchars($descripcion_value) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="url_post">URL *</label>
                            <input type="url" name="url_post" id="url_post" class="form-control" required value="<?= htmlspecialchars($url_value) ?>">
                            <small class="form-text text-muted">Ejemplo: https://www.instagram.com/p/ABC123xyz/</small>
                        </div>

                        <div class="form-group">
                            <label for="media">Foto o Video *</label>
                            <input type="file" name="media" id="media" accept="image/*,video/*" class="form-control-file" required>
                            <small class="form-text text-muted">Formatos: JPG, PNG, GIF, MP4, WEBM. Máx. 10MB.</small>
                        </div>

                        <div class="form-check mt-3">
                            <label>
                                <input type="checkbox" name="visible" <?= $visible_checked ?>>
                                <span class="form-check-sign">Visible en el sitio</span>
                            </label>
                        </div>

                        <div class="card-action text-right mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Crear
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
</div> <!-- Cierra wrapper -->
<?php require_once '../includes/footer.php'; ?>
