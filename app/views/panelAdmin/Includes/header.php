<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' | ' : '' ?><?= SITE_NAME ?></title>
    <link rel="icon" type="image/x-icon" href="/BESTUNE2/public/assets/img/favicon.ico">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap Icons (opcional) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Tu CSS personalizado -->
    <link rel="stylesheet" href="/BESTUNE2/public/assets/css/styles.css">
</head>
<body class="bg-gray-100 text-gray-800 flex flex-col min-h-screen">

<!-- NAVBAR -->
<nav class="bg-gray-900 text-white shadow">
  <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">
      <!-- Logo -->
      <a href="/BESTUNE2/app/views/panelAdmin/index.php" class="flex items-center">
        <img src="<?= SITE_LOGO ?>" alt="<?= SITE_NAME ?>" class="h-10">
      </a>

      <!-- Menu -->
      <div class="flex-1 hidden md:flex justify-center items-center space-x-6">
        <a href="/BESTUNE2/app/views/panelAdmin/usuarios/listar.php" class="hover:text-yellow-400 transition">Usuarios</a>
        <a href="/BESTUNE2/app/views/panelAdmin/promociones/listar.php" class="hover:text-yellow-400 transition">Promociones</a>
        <a href="/BESTUNE2/app/views/panelAdmin/planes/listar.php" class="hover:text-yellow-400 transition">Planes</a>
        <a href="/BESTUNE2/app/views/panelAdmin/eventos/listar.php" class="hover:text-yellow-400 transition">Eventos</a>
        <a href="/BESTUNE2/app/views/panelAdmin/instagram/listar.php" class="hover:text-yellow-400 transition">Instagram</a>
      </div>

      <!-- Icono logout -->
      <div class="ml-auto flex items-center space-x-2">
        <a href="/BESTUNE2/app/views/panelAdmin/logout.php" title="Cerrar sesión" class="text-white hover:text-red-400 transition">
          <i class="bi bi-box-arrow-right text-2xl"></i>
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- CONTENIDO PRINCIPAL -->
<main class="flex-grow max-w-screen-xl mx-auto px-4 py-6 w-full">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="mb-4 p-4 rounded text-white 
            <?= $_SESSION['message_type'] === 'success' ? 'bg-green-500' : '' ?>
            <?= $_SESSION['message_type'] === 'danger' ? 'bg-red-500' : '' ?>
            <?= $_SESSION['message_type'] === 'warning' ? 'bg-yellow-500 text-black' : '' ?>
        ">
            <div class="flex justify-between items-center">
                <span><?= $_SESSION['message'] ?></span>
                <button onclick="this.parentElement.parentElement.remove();" class="text-white hover:text-gray-200 font-bold text-xl leading-none">&times;</button>
            </div>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>
