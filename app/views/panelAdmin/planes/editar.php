<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ID de plan no proporcionado";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

$id_plan = (int)$_GET['id'];

// Obtener información del plan
$stmt = $db->prepare("SELECT * FROM planes WHERE id_plan = ?");
$stmt->execute([$id_plan]);
$plan = $stmt->fetch();

if (!$plan) {
    $_SESSION['message'] = "El plan no existe";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_plan = trim($_POST['nombre_plan']);
    $imagen_actual = $plan['imagen_principal'];
    $imagen_url = $imagen_actual;

    $errors = [];

    if (empty($nombre_plan)) {
        $errors[] = "El nombre del plan es requerido";
    }

    // Procesar nueva imagen si se sube
    if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['imagen_principal']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.";
        } elseif ($_FILES['imagen_principal']['size'] > 5 * 1024 * 1024) {
            $errors[] = "La imagen es demasiado grande. Máximo 5MB permitidos.";
        } else {
            $upload_dir = dirname(__DIR__, 2) . '/public/assets/img/planes/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Eliminar imagen anterior si existe
            if (!empty($imagen_actual)) {
                $ruta_anterior = dirname(__DIR__, 2) . '/public/' . ltrim($imagen_actual, '/');
                if (file_exists($ruta_anterior)) {
                    unlink($ruta_anterior);
                }
            }

            $imagen_nombre = 'plan_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destino = $upload_dir . $imagen_nombre;

            if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $destino)) {
                $imagen_url = 'assets/img/planes/' . $imagen_nombre;
            } else {
                $errors[] = "Error al subir la imagen.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE planes SET nombre_plan = ?, imagen_principal = ? WHERE id_plan = ?");
            $stmt->execute([$nombre_plan, $imagen_url, $id_plan]);

            $_SESSION['message'] = "Plan actualizado correctamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Error al actualizar el plan: " . $e->getMessage();
        }
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="container mt-4">
    <h1>Editar Plan</h1>

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
            <label for="nombre_plan" class="form-label">Nombre del Plan</label>
            <input type="text" class="form-control" id="nombre_plan" name="nombre_plan"
                   value="<?= htmlspecialchars($plan['nombre_plan']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="imagen_principal" class="form-label">Imagen Principal</label>
            <input type="file" class="form-control" id="imagen_principal" name="imagen_principal" accept="image/*">
            <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB.</small>
            <?php if (!empty($plan['imagen_principal'])): ?>
                <div class="mt-2">
                    <img src="../<?= htmlspecialchars($plan['imagen_principal']) ?>" class="img-thumbnail" style="max-height: 100px;">
                    <p class="text-muted mt-1">Imagen actual</p>
                </div>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
