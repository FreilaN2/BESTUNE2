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

$id_usuario = (int)$_GET['id'];
$page_title = 'Editar Usuario';
require_once '../includes/header.php';

// Obtener datos del usuario
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch();

if (!$usuario) {
    $_SESSION['message'] = "Usuario no encontrado";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $telefono = trim($_POST['telefono']);
    $es_administrador = isset($_POST['es_administrador']) ? 1 : 0;
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Validaciones
    $errors = [];
    
    if (empty($nombre)) $errors[] = "El nombre es requerido";
    if (empty($email)) $errors[] = "El email es requerido";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El email no es válido";
    
    // Verificar si el email ya existe (excluyendo al usuario actual)
    $stmt = $db->prepare("SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario != ?");
    $stmt->execute([$email, $id_usuario]);
    if ($stmt->fetch()) $errors[] = "El email ya está registrado por otro usuario";
    
    if (!empty($password) && strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres";
    }
    
    if (empty($errors)) {
        try {
            // Actualizar con o sin contraseña
            if (!empty($password)) {
                $password_hash = hash('sha256', $password);
                $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, email = ?, password_hash = ?, telefono = ?, es_administrador = ?, activo = ? WHERE id_usuario = ?");
                $stmt->execute([$nombre, $email, $password_hash, $telefono, $es_administrador, $activo, $id_usuario]);
            } else {
                $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, email = ?, telefono = ?, es_administrador = ?, activo = ? WHERE id_usuario = ?");
                $stmt->execute([$nombre, $email, $telefono, $es_administrador, $activo, $id_usuario]);
            }
            
            $_SESSION['message'] = "Usuario actualizado exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Error al actualizar el usuario: " . $e->getMessage();
        }
    }
}
?>

<div class="card">
    <div class="card-body">
        <h2 class="card-title">Editar Usuario</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required 
                       value="<?= htmlspecialchars($usuario['nombre']) ?>">
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required 
                       value="<?= htmlspecialchars($usuario['email']) ?>">
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" class="form-control" id="password" name="password">
                <small class="text-muted">Mínimo 8 caracteres</small>
            </div>
            
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" 
                       value="<?= htmlspecialchars($usuario['telefono']) ?>">
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="es_administrador" name="es_administrador" <?= $usuario['es_administrador'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="es_administrador">Usuario Administrador</label>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="activo" name="activo" <?= $usuario['activo'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="activo">Usuario Activo</label>
            </div>
            
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>