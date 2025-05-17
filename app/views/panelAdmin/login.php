<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && hash('sha256', $password) === $user['password_hash']) {
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['is_admin'] = $user['es_administrador'];
        $_SESSION['message'] = "Bienvenido, " . $user['nombre'];
        $_SESSION['message_type'] = "success";
        header("Location: index.php");
        exit();
    } else {
        $error = "Credenciales incorrectas";
        error_log("Intento de login fallido para: $email");
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-gray-800 rounded-xl shadow-lg p-8 animate-fadeInUp">
        <h2 class="text-2xl font-bold text-center mb-6">Iniciar Sesión</h2>

        <?php if (isset($error)): ?>
            <div class="bg-red-500 text-white text-sm px-4 py-2 mb-4 rounded">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label for="email" class="block mb-1 text-sm font-medium">Email</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label for="password" class="block mb-1 text-sm font-medium">Contraseña</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 transition-colors px-4 py-2 rounded font-semibold">Ingresar</button>
        </form>
    </div>

</body>
</html>
