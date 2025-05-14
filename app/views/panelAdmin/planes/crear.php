<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Crear Plan';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_plan']);
    
    // Validaciones
    $errors = [];
    
    if (empty($nombre)) {
        $errors[] = "El nombre del plan es requerido";
    }
    
    // Procesar imagen
    $imagen_nombre = null;
    if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['imagen_principal']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.";
        } elseif ($_FILES['imagen_principal']['size'] > 5 * 1024 * 1024) {
            $errors[] = "La imagen es demasiado grande. Máximo 5MB permitidos.";
        } else {
            // Ruta absoluta al directorio donde se guardará la imagen
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
            $stmt = $db->prepare("INSERT INTO planes 
                                (nombre_plan, imagen_principal) 
                                VALUES (?, ?)");
            // Ruta pública que se usará en el sitio web (sin /public)
            $imagen_url = 'assets/img/planes/' . $imagen_nombre;
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

<div class="card">
    <div class="card-body">
        <h2 class="card-title">Crear Nuevo Plan</h2>
        
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
                        <label for="nombre_plan" class="form-label">Nombre del Plan *</label>
                        <input type="text" class="form-control" id="nombre_plan" name="nombre_plan" required 
                               value="<?= isset($_POST['nombre_plan']) ? htmlspecialchars($_POST['nombre_plan']) : '' ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="imagen_principal" class="form-label">Imagen Principal *</label>
                        <input type="file" class="form-control" id="imagen_principal" name="imagen_principal" accept="image/*" required>
                        <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB.</small>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Guardar Plan</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
