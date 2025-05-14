<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Editar Post de Instagram';
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ID de post no proporcionado";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

$id = (int)$_GET['id'];

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
            $errors[] = "Formato de archivo no permitido. Use JPG, PNG, GIF, MP4 o WEBM.";
        } elseif ($_FILES['media']['size'] > 10 * 1024 * 1024) {
            $errors[] = "El archivo es demasiado grande. Máximo 10MB permitidos.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/instagram/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Eliminar el archivo anterior si existe
            if (!empty($media_actual)) {
                $archivo_anterior = realpath(__DIR__ . '/../../../../') . '/public/' . ltrim($media_actual, '/');
                if (file_exists($archivo_anterior)) {
                    unlink($archivo_anterior);
                }
            }

            // Generar nuevo nombre y mover archivo
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
            $stmt = $db->prepare("UPDATE instagram_posts 
                SET descripcion = ?, url_post = ?, visible = ?, url_media = ?, 
                    id_usuario_actualizador = ?, fecha_actualizacion = NOW() 
                WHERE id_post = ?");
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

$descripcion_value = $_POST['descripcion'] ?? $post['descripcion'];
$url_value = $_POST['url_post'] ?? $post['url_post'];
$visible_checked = isset($_POST['visible']) || $post['visible'] ? 'checked' : '';
?>

<div class="card">
    <div class="card-body">
        <h2 class="card-title">Editar Post de Instagram</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción *</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?= htmlspecialchars($descripcion_value) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="url_post" class="form-label">URL *</label>
                <input type="url" class="form-control" id="url_post" name="url_post" required value="<?= htmlspecialchars($url_value) ?>">
            </div>
            <div class="mb-3">
                <label for="media" class="form-label">Foto o Video</label>
                <input type="file" class="form-control" id="media" name="media" accept="image/*,video/*">
                <small class="text-muted">Formatos permitidos: JPG, PNG, GIF, MP4, WEBM. Máximo 10MB.</small>
                <?php if (!empty($post['url_media'])): ?>
                    <div class="mt-2">
                        <?php if (preg_match('/\.(mp4|webm)$/i', $post['url_media'])): ?>
                            <video src="../<?= htmlspecialchars($post['url_media']) ?>" controls style="max-height: 200px;" class="img-thumbnail"></video>
                        <?php else: ?>
                          <img src="/BESTUNE2/public/<?= htmlspecialchars($post['url_media']) ?>"  alt="Media actual" style="max-height: 200px;" class="img-thumbnail">
                        <?php endif; ?>
                        <p class="text-muted mt-1">Archivo actual</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="visible" name="visible" <?= $visible_checked ?>>
                <label class="form-check-label" for="visible">Visible en el sitio</label>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
