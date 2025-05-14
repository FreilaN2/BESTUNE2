<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

if (!isAdmin()) {
    $_SESSION['message'] = "No tienes permisos para acceder a esta sección";
    $_SESSION['message_type'] = "danger";
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit();
}

$id_promocion = (int)$_GET['id'];
$page_title = 'Editar Promoción';
require_once '../includes/header.php';

// Obtener datos de la promoción
$stmt = $db->prepare("SELECT * FROM promociones WHERE id_promocion = ?");
$stmt->execute([$id_promocion]);
$promocion = $stmt->fetch();

if (!$promocion) {
    $_SESSION['message'] = "Promoción no encontrada";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

// Obtener usuarios para el selector
$usuarios = $db->query("SELECT id_usuario, nombre, email FROM usuarios ORDER BY nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $descuento = (float)$_POST['descuento'];
    $codigo = trim($_POST['codigo_promocion']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $activa = isset($_POST['activa']) ? 1 : 0;
    $id_usuario = (int)$_POST['id_usuario_creador'];

    $errors = [];

    if (empty($titulo)) $errors[] = "El título es requerido";
    if (empty($descuento)) $errors[] = "El descuento es requerido";
    if ($descuento <= 0 || $descuento > 100) $errors[] = "El descuento debe ser entre 0.01 y 100";
    if (empty($codigo)) $errors[] = "El código de promoción es requerido";
    if (empty($fecha_inicio)) $errors[] = "La fecha de inicio es requerida";
    if (empty($fecha_fin)) $errors[] = "La fecha de fin es requerida";
    if ($fecha_fin < $fecha_inicio) $errors[] = "La fecha de fin no puede ser anterior a la fecha de inicio";

    $imagen_actual = $promocion['imagen_url'] ?? '';
    $imagen_url = $imagen_actual;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de imagen no permitido.";
        } elseif ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            $errors[] = "La imagen es demasiado grande. Máximo 5MB.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/promociones/';
            if ($upload_dir === false) {
                $errors[] = "No se pudo acceder a la carpeta de destino.";
            } else {
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                // Eliminar imagen anterior si existe
                if (!empty($imagen_actual)) {
                    $ruta_antigua = realpath(__DIR__ . '/../../../../') . '/public/' . ltrim($imagen_actual, '/');
                    if (file_exists($ruta_antigua)) unlink($ruta_antigua);
                }

                $imagen_nombre = 'promo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $destino = $upload_dir . $imagen_nombre;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                    $imagen_url = 'assets/img/promociones/' . $imagen_nombre;
                } else {
                    $errors[] = "Error al subir la nueva imagen.";
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE promociones SET 
                titulo = ?, descripcion = ?, descuento = ?, codigo_promocion = ?, 
                fecha_inicio = ?, fecha_fin = ?, imagen_url = ?, activa = ?, id_usuario_creador = ? 
                WHERE id_promocion = ?");
            $stmt->execute([
                $titulo, $descripcion, $descuento, $codigo,
                $fecha_inicio, $fecha_fin, $imagen_url, $activa,
                $id_usuario, $id_promocion
            ]);

            $_SESSION['message'] = "Promoción actualizada exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            if (isset($destino) && file_exists($destino)) unlink($destino);
            $errors[] = "Error al actualizar la promoción: " . $e->getMessage();
        }
    }
}
?>

<div class="card">
    <div class="card-body">
        <h2 class="card-title">Editar Promoción</h2>

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
                               value="<?= htmlspecialchars($promocion['titulo']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($promocion['descripcion']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="descuento" class="form-label">Descuento (%) *</label>
                        <input type="number" step="0.01" min="0.01" max="100" class="form-control" id="descuento" name="descuento" required
                               value="<?= htmlspecialchars($promocion['descuento']) ?>">
                        <small class="text-muted">Ejemplo: 15.50 para 15.5% de descuento</small>
                    </div>

                    <div class="mb-3">
                        <label for="codigo_promocion" class="form-label">Código de Promoción *</label>
                        <input type="text" class="form-control" id="codigo_promocion" name="codigo_promocion" required
                               value="<?= htmlspecialchars($promocion['codigo_promocion']) ?>">
                        <small class="text-muted">Código único para aplicar el descuento</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required
                               value="<?= htmlspecialchars($promocion['fecha_inicio']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin *</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required
                               value="<?= htmlspecialchars($promocion['fecha_fin']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen Promocional</label>
                        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB.</small>
                        <?php if (!empty($promocion['imagen_url'])): ?>
                            <div class="mt-2">
<img src="/bestune2/public/<?= htmlspecialchars($promocion['imagen_url']) ?>" alt="Imagen actual" style="max-height: 100px;" class="img-thumbnail">

                                <p class="text-muted mt-1">Imagen actual</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="id_usuario_creador" class="form-label">Responsable *</label>
                        <select class="form-select" id="id_usuario_creador" name="id_usuario_creador" required>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?= $usuario['id_usuario'] ?>" <?= $promocion['id_usuario_creador'] == $usuario['id_usuario'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($usuario['nombre']) ?> (<?= htmlspecialchars($usuario['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="activa" name="activa" <?= $promocion['activa'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="activa">Promoción Activa</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
