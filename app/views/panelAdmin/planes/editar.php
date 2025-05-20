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

    if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['imagen_principal']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.";
        } elseif ($_FILES['imagen_principal']['size'] > 5 * 1024 * 1024) {
            $errors[] = "La imagen es demasiado grande. Máximo 5MB permitidos.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/planes/';

            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            if (!empty($imagen_actual)) {
                $ruta_anterior = dirname(__DIR__, 2) . '/public/' . ltrim($imagen_actual, '/');
                if (file_exists($ruta_anterior)) unlink($ruta_anterior);
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

require_once '../includes/header.php';
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Editar Plan</h4>
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
                    <div class="card-title"><i class="fa fa-cube"></i> Datos del Plan</div>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="nombre_plan">Nombre del Plan</label>
                            <input type="text" name="nombre_plan" id="nombre_plan" class="form-control" required
                                   value="<?= htmlspecialchars($plan['nombre_plan']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="imagen_principal">Imagen Principal</label>
                            <input type="file" name="imagen_principal" id="imagen_principal" accept="image/*"
                                   class="form-control-file">
                            <?php if (!empty($plan['imagen_principal'])): ?>
                                <img src="/BESTUNE2/public/<?= htmlspecialchars($plan['imagen_principal']) ?>" 
                                     alt="Imagen actual" class="mt-2 rounded border" style="max-height: 100px;">
                            <?php endif; ?>
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
</div> <!-- Cierra wrapper -->
<?php require_once '../includes/footer.php'; ?>
