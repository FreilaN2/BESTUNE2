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

$page_title = 'Crear Promoción';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $descuento = (float)$_POST['descuento'];
    $codigo = trim($_POST['codigo_promocion']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $activa = isset($_POST['activa']) ? 1 : 0;

    $errors = [];

    if (empty($titulo)) $errors[] = "El título es requerido";
    if ($descuento <= 0 || $descuento > 100) $errors[] = "El descuento debe estar entre 0.01% y 100%";
    if (empty($fecha_inicio)) $errors[] = "La fecha de inicio es requerida";
    if (empty($fecha_fin)) $errors[] = "La fecha de fin es requerida";
    if ($fecha_inicio > $fecha_fin) $errors[] = "La fecha de inicio no puede ser mayor a la fecha de fin";

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
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/promociones/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $imagen_nombre = 'promo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destino = $upload_dir . $imagen_nombre;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                $imagen_url = 'assets/img/promociones/' . $imagen_nombre;
            } else {
                $errors[] = "Error al subir la imagen.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO promociones 
                (titulo, descripcion, descuento, codigo_promocion, fecha_inicio, fecha_fin, imagen_url, activa, id_usuario_creador)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $titulo, $descripcion, $descuento, $codigo, $fecha_inicio, $fecha_fin, $imagen_url, $activa, $_SESSION['user_id']
            ]);

            $_SESSION['message'] = "Promoción creada exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            if ($imagen_url && file_exists($upload_dir . $imagen_nombre)) {
                unlink($upload_dir . $imagen_nombre);
            }
            $errors[] = "Error al crear la promoción: " . $e->getMessage();
        }
    }
}

$activa_checked = (!isset($_POST['activa']) || $_POST['activa']) ? 'checked' : '';
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Crear Promoción</h4>
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
                    <div class="card-title"><i class="fa fa-gift"></i> Nueva Promoción</div>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titulo">Título *</label>
                                    <input type="text" name="titulo" id="titulo" class="form-control" required
                                           value="<?= isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : '' ?>">
                                </div>

                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea name="descripcion" id="descripcion" class="form-control" rows="3"><?= isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="descuento">Descuento (%) *</label>
                                    <input type="number" name="descuento" id="descuento" class="form-control" min="0.01" max="100" step="0.01" required
                                           value="<?= isset($_POST['descuento']) ? htmlspecialchars($_POST['descuento']) : '' ?>">
                                </div>

                                <div class="form-group">
                                    <label for="codigo_promocion">Código de Promoción *</label>
                                    <input type="text" name="codigo_promocion" id="codigo_promocion" class="form-control" required
                                           value="<?= isset($_POST['codigo_promocion']) ? htmlspecialchars($_POST['codigo_promocion']) : '' ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_inicio">Fecha de Inicio *</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required
                                           value="<?= isset($_POST['fecha_inicio']) ? htmlspecialchars($_POST['fecha_inicio']) : '' ?>">
                                </div>

                                <div class="form-group">
                                    <label for="fecha_fin">Fecha de Fin *</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required
                                           value="<?= isset($_POST['fecha_fin']) ? htmlspecialchars($_POST['fecha_fin']) : '' ?>">
                                </div>

                                <div class="form-group">
                                    <label for="imagen">Imagen Promocional</label>
                                    <input type="file" name="imagen" id="imagen" class="form-control-file">
                                    <small class="form-text text-muted">Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB.</small>
                                </div>

                                <div class="form-check mt-3">
                                    <label>
                                        <input type="checkbox" name="activa" <?= $activa_checked ?>>
                                        <span class="form-check-sign">Promoción Activa</span>
                                    </label>
                                </div>
                            </div>
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
