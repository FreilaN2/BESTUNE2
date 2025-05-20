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

$stmt = $db->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch();

if (!$usuario) {
    $_SESSION['message'] = "Usuario no encontrado";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $telefono = trim($_POST['telefono']);
    $es_administrador = isset($_POST['es_administrador']) ? 1 : 0;
    $activo = isset($_POST['activo']) ? 1 : 0;

    $errors = [];

    if (empty($nombre)) $errors[] = "El nombre es requerido";
    if (empty($email)) $errors[] = "El email es requerido";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El email no es válido";

    $stmt = $db->prepare("SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario != ?");
    $stmt->execute([$email, $id_usuario]);
    if ($stmt->fetch()) $errors[] = "El email ya está registrado por otro usuario";

    if (!empty($password) && strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres";
    }

    if (empty($errors)) {
        try {
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

<div class="page-inner" style="background-color: #f8f9fa;">
    <div class="page-header">
        <h4 class="page-title">Editar Usuario</h4>
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
                    <div class="card-title"><i class="fa fa-user-edit"></i> Editar Usuario</div>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="nombre">Nombre completo *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required
                                   value="<?= htmlspecialchars($usuario['nombre']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" name="email" id="email" class="form-control" required
                                   value="<?= htmlspecialchars($usuario['email']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" name="telefono" id="telefono" class="form-control"
                                   value="<?= htmlspecialchars($usuario['telefono']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="password">Nueva Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control">
                            <small class="form-text text-muted">Déjala en blanco si no deseas cambiarla.</small>
                        </div>

                        <div class="form-check">
                            <label>
                                <input type="checkbox" name="es_administrador"
                                    <?= $usuario['es_administrador'] ? 'checked' : '' ?>>
                                <span class="form-check-sign">Usuario Administrador</span>
                            </label>
                        </div>

                        <div class="form-check">
                            <label>
                                <input type="checkbox" name="activo"
                                    <?= $usuario['activo'] ? 'checked' : '' ?>>
                                <span class="form-check-sign">Usuario Activo</span>
                            </label>
                        </div>

                        <div class="card-action text-right mt-3">
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
