<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Crear Evento';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha = $_POST['fecha'];

    $errors = [];

    if (empty($titulo)) $errors[] = "El título es requerido";
    if (empty($fecha)) $errors[] = "La fecha es requerida";

    // Procesar imagen
    $imagen_nombre = null;
    $imagen_url = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.";
        } elseif ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            $errors[] = "La imagen es demasiado grande. Máximo 5MB permitidos.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/eventos/';
            if ($upload_dir === false) {
                $errors[] = "No se pudo acceder a la carpeta de destino.";
            } else {
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $imagen_nombre = 'evento_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $destino = $upload_dir . $imagen_nombre;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                    $imagen_url = 'assets/img/eventos/' . $imagen_nombre;
                } else {
                    $errors[] = "Error al subir la imagen";
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO eventos 
                (titulo, descripcion, fecha, imagen, id_usuario_creador) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $titulo,
                $descripcion,
                $fecha,
                $imagen_url,
                $_SESSION['user_id']
            ]);

            $_SESSION['message'] = "Evento creado exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            if ($imagen_nombre && file_exists($destino)) {
                unlink($destino);
            }
            $errors[] = "Error al crear el evento: " . $e->getMessage();
        }
    }
}
?>

<div class="card">
    <div class="card-body">
        <h2 class="card-title">Crear Nuevo Evento</h2>

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
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required 
                            value="<?= isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="5"><?= isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha y Hora *</label>
                        <input type="datetime-local" class="form-control" id="fecha" name="fecha" required 
                            value="<?= isset($_POST['fecha']) ? htmlspecialchars($_POST['fecha']) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen del Evento</label>
                        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB.</small>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Evento</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
