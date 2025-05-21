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

$stmt = $db->prepare("SELECT * FROM promociones WHERE id_promocion = ?");
$stmt->execute([$id_promocion]);
$promocion = $stmt->fetch();

if (!$promocion) {
    $_SESSION['message'] = "Promoción no encontrada";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

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
    if ($fecha_fin < $fecha_inicio) $errors[] = "La fecha de fin no puede ser anterior a la de inicio";

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
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

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

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE promociones SET titulo = ?, descripcion = ?, descuento = ?, codigo_promocion = ?, fecha_inicio = ?, fecha_fin = ?, imagen_url = ?, activa = ?, id_usuario_creador = ? WHERE id_promocion = ?");
            $stmt->execute([$titulo, $descripcion, $descuento, $codigo, $fecha_inicio, $fecha_fin, $imagen_url, $activa, $id_usuario, $id_promocion]);

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

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Editar Promoción</h4>
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
                    <div class="card-title"><i class="fa fa-tag"></i> Datos de la Promoción</div>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titulo">Título *</label>
                                    <input type="text" name="titulo" id="titulo" class="form-control" required value="<?= htmlspecialchars($promocion['titulo']) ?>">
                                </div>

                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea name="descripcion" id="descripcion" class="form-control" rows="3"><?= htmlspecialchars($promocion['descripcion']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="descuento">Descuento (%) *</label>
                                    <input type="number" name="descuento" id="descuento" class="form-control" min="0.01" max="100" step="0.01" required value="<?= htmlspecialchars($promocion['descuento']) ?>">
                                </div>

                                <div class="form-group">
                                    <label for="codigo_promocion">Código de Promoción *</label>
                                    <input type="text" name="codigo_promocion" id="codigo_promocion" class="form-control" required value="<?= htmlspecialchars($promocion['codigo_promocion']) ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_inicio">Fecha de Inicio *</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required value="<?= htmlspecialchars($promocion['fecha_inicio']) ?>">
                                </div>

                                <div class="form-group">
                                    <label for="fecha_fin">Fecha de Fin *</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required value="<?= htmlspecialchars($promocion['fecha_fin']) ?>">
                                </div>

                                <div class="form-group">
                                    <label for="imagen">Imagen Promocional</label>
                                    <input type="file" name="imagen" id="imagen" class="form-control-file">
                                    <?php if (!empty($promocion['imagen_url'])): ?>
                                        <img src="<?= BASE_URL ?><?= htmlspecialchars($promocion['imagen_url']) ?>" class="mt-2 rounded border" style="max-height: 100px;" alt="Imagen actual">
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="id_usuario_creador">Responsable *</label>
                                    <select name="id_usuario_creador" id="id_usuario_creador" class="form-control">
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <option value="<?= $usuario['id_usuario'] ?>" <?= $promocion['id_usuario_creador'] == $usuario['id_usuario'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($usuario['nombre']) ?> (<?= htmlspecialchars($usuario['email']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-check mt-3">
                                    <label>
                                        <input type="checkbox" name="activa" <?= $promocion['activa'] ? 'checked' : '' ?>>
                                        <span class="form-check-sign">Promoción Activa</span>
                                    </label>
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
