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
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

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
?>

<div class="card">
    <div class="card-body">
        <h2 class="card-title">Crear Nueva Promoción</h2>

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
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="descuento" class="form-label">Descuento (%) *</label>
                        <input type="number" class="form-control" id="descuento" name="descuento" min="0.01" max="100" step="0.01" required
                               value="<?= isset($_POST['descuento']) ? htmlspecialchars($_POST['descuento']) : '' ?>">
                        <small class="text-muted">Ejemplo: 15.50 para 15.5% de descuento</small>
                    </div>

                    <div class="mb-3">
                        <label for="codigo_promocion" class="form-label">Código de Promoción *</label>
                        <input type="text" class="form-control" id="codigo_promocion" name="codigo_promocion" required
                               value="<?= isset($_POST['codigo_promocion']) ? htmlspecialchars($_POST['codigo_promocion']) : '' ?>">
                        <small class="text-muted">Código único que los clientes usarán para aplicar el descuento</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required
                               value="<?= isset($_POST['fecha_inicio']) ? htmlspecialchars($_POST['fecha_inicio']) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin *</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required
                               value="<?= isset($_POST['fecha_fin']) ? htmlspecialchars($_POST['fecha_fin']) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen Promocional</label>
                        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB.</small>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="activa" name="activa" <?= !isset($_POST['activa']) || $_POST['activa'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="activa">Promoción Activa</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Promoción</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
