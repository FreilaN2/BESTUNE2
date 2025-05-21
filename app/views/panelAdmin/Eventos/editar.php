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
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            // Eliminar imagen anterior si existe
            if (!empty($imagen_actual)) {
                $ruta_antigua = realpath(__DIR__ . '/../../../../') . '/public/' . ltrim($imagen_actual, '/');
                if (file_exists($ruta_antigua)) unlink($ruta_antigua);
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

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE eventos SET titulo = ?, descripcion = ?, fecha = ?, imagen = ? WHERE id_evento = ?");
            $stmt->execute([$titulo, $descripcion, $fecha, $imagen_url, $id_evento]);

            $_SESSION['message'] = "Evento actualizado exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            if (isset($destino) && file_exists($destino)) unlink($destino);
            $errors[] = "Error al actualizar el evento: " . $e->getMessage();
        }
    }
}
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Editar Evento</h4>
        <a href="listar.php" class="btn btn-secondary btn-round ml-auto">
            <i class="fa fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
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
                    <div class="card-title"><i class="fa fa-calendar-edit"></i> Datos del Evento</div>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="titulo">Título *</label>
                            <input type="text" id="titulo" name="titulo" class="form-control" required
                                   value="<?= htmlspecialchars($evento['titulo']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion" rows="4" class="form-control"><?= htmlspecialchars($evento['descripcion']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="fecha">Fecha y Hora *</label>
                            <input type="datetime-local" id="fecha" name="fecha" class="form-control" required
                                   value="<?= date('Y-m-d\TH:i', strtotime($evento['fecha'])) ?>">
                        </div>

                        <div class="form-group">
                            <label for="imagen">Imagen del Evento</label>
                            <input type="file" id="imagen" name="imagen" accept="image/*" class="form-control-file">
                            <?php if (!empty($evento['imagen'])): ?>
                                <div class="mt-2">
                                    <img src="<?= BASE_URL . htmlspecialchars($evento['imagen']) ?>"
                                         alt="Imagen actual" class="img-fluid rounded border" style="max-height: 100px;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-action text-right mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar
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
