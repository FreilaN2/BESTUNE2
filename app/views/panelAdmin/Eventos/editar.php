<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit();
}

$id_evento = (int)$_GET['id'];
$page_title = 'Editar Evento';
require_once '../includes/header.php';

// Obtener datos del evento
$stmt = $db->prepare("SELECT * FROM eventos WHERE id_evento = ?");
$stmt->execute([$id_evento]);
$evento = $stmt->fetch();

if (!$evento) {
    $_SESSION['message'] = "Evento no encontrado";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha = $_POST['fecha'];

    $errors = [];

    if (empty($titulo)) $errors[] = "El título es requerido";
    if (empty($fecha)) $errors[] = "La fecha es requerida";

    $imagen_actual = $evento['imagen'] ?? '';
    $imagen_url = $imagen_actual;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.";
        } elseif ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            $errors[] = "La imagen es demasiado grande. Máximo 5MB.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/eventos/';
            if ($upload_dir === false) {
                $errors[] = "No se pudo acceder a la carpeta de destino.";
            } else {
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                // Eliminar imagen anterior si existe
                $ruta_antigua = realpath(__DIR__ . '/../../../../') . '/public/' . ltrim($imagen_actual, '/');
                if (!empty($imagen_actual) && file_exists($ruta_antigua)) {
                    unlink($ruta_antigua);
                }

                $imagen_nombre = 'evento_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $destino = $upload_dir . $imagen_nombre;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                    $imagen_url = 'assets/img/eventos/' . $imagen_nombre;
                } else {
                    $errors[] = "Error al subir la nueva imagen.";
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE eventos SET 
                titulo = ?, 
                descripcion = ?, 
                fecha = ?, 
                imagen = ? 
                WHERE id_evento = ?");
            $stmt->execute([
                $titulo,
                $descripcion,
                $fecha,
                $imagen_url,
                $id_evento
            ]);

            $_SESSION['message'] = "Evento actualizado exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            if (isset($destino) && file_exists($destino)) {
                unlink($destino);
            }
            $errors[] = "Error al actualizar el evento: " . $e->getMessage();
        }
    }
}
?>

<div class="card">
    <div class="card-body">
        <h2 class="card-title">Editar Evento</h2>

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
                               value="<?= htmlspecialchars($evento['titulo']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="5"><?= htmlspecialchars($evento['descripcion']) ?></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha y Hora *</label>
                        <input type="datetime-local" class="form-control" id="fecha" name="fecha" required
                               value="<?= date('Y-m-d\TH:i', strtotime($evento['fecha'])) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen del Evento</label>
                        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB.</small>
                        <?php if (!empty($evento['imagen'])): ?>
                            <div class="mt-2">
                               <img src="/bestune2/public/<?= htmlspecialchars($evento['imagen']) ?>" alt="Imagen actual" style="max-height: 100px;" class="img-thumbnail">
                                <p class="text-muted mt-1">Imagen actual</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
