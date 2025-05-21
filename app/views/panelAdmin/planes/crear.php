<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Crear Plan';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_plan']);
    $errors = [];

    if (empty($nombre)) {
        $errors[] = "El nombre del plan es requerido";
    }

    $imagen_nombre = null;
    if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['imagen_principal']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.";
        } elseif ($_FILES['imagen_principal']['size'] > 5 * 1024 * 1024) {
            $errors[] = "La imagen es demasiado grande. Máximo 5MB permitidos.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/planes/';
            if ($upload_dir === false) {
                $errors[] = "No se pudo acceder a la carpeta de destino.";
            } else {
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $imagen_nombre = 'plan_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $destino = $upload_dir . $imagen_nombre;

                if (!move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $destino)) {
                    $errors[] = "Error al subir la imagen";
                }
            }
        }
    } else {
        $errors[] = "La imagen principal es requerida";
    }

    if (empty($errors)) {
        try {
            $imagen_url = 'assets/img/planes/' . $imagen_nombre;
            $stmt = $db->prepare("INSERT INTO planes (nombre_plan, imagen_principal) VALUES (?, ?)");
            $stmt->execute([$nombre, $imagen_url]);

            $_SESSION['message'] = "Plan creado exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            if ($imagen_nombre && file_exists($destino)) {
                unlink($destino);
            }
            $errors[] = "Error al crear el plan: " . $e->getMessage();
        }
    }
}
?>

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Crear Nuevo Plan</h4>
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
                    <div class="card-title"><i class="fa fa-plus-circle"></i> Información del Plan</div>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="nombre_plan">Nombre del Plan *</label>
                            <input type="text" id="nombre_plan" name="nombre_plan" required
                                   class="form-control"
                                   value="<?= isset($_POST['nombre_plan']) ? htmlspecialchars($_POST['nombre_plan']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="imagen_principal">Imagen Principal *</label>
                            <input type="file" id="imagen_principal" name="imagen_principal" accept="image/*" required
                                   class="form-control-file">
                            <small class="form-text text-muted">Formatos: JPG, PNG, GIF, WEBP. Máx. 5MB.</small>
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
