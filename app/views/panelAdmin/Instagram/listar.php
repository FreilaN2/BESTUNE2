<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Gestión de Instagram';
require_once '../includes/header.php';

$sql = "SELECT id_post, descripcion, url_post, url_media, fecha_post, visible FROM instagram_posts ORDER BY fecha_post DESC";
$posts = $db->query($sql)->fetchAll();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900 border-b-2 border-blue-500 inline-block pb-1">
        Gestión de Instagram
    </h1>
    <a href="crear.php" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition">
        <i class="bi bi-plus-circle text-lg"></i>
        <span class="font-medium">Nuevo Post</span>
    </a>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="flex items-start gap-3 mb-4 p-4 rounded-lg
        <?= $_SESSION['message_type'] === 'success' ? 'bg-green-100 text-green-800' : '' ?>
        <?= $_SESSION['message_type'] === 'danger' ? 'bg-red-100 text-red-800' : '' ?>
        <?= $_SESSION['message_type'] === 'warning' ? 'bg-yellow-100 text-yellow-800' : '' ?>">
        <svg class="w-6 h-6 mt-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8zM9 9v4h2V9H9zm0-4v2h2V5H9z" clip-rule="evenodd"/>
        </svg>
        <div><?= $_SESSION['message'] ?></div>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>

<div class="overflow-x-auto rounded-lg shadow border border-gray-200 bg-white">
    <table class="w-full table-auto border-collapse">
        <thead class="bg-gray-100 text-sm text-gray-700 uppercase">
            <tr>
                <th class="p-3 text-left">ID</th>
                <th class="p-3 text-left">Descripción</th>
                <th class="p-3 text-left">Media</th>
                <th class="p-3 text-left">Fecha</th>
                <th class="p-3 text-left">Estado</th>
                <th class="p-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-800 text-sm">
            <?php foreach ($posts as $post): ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-3"><?= $post['id_post'] ?></td>
                    <td class="p-3"><?= htmlspecialchars($post['descripcion']) ?></td>
                    <td class="p-3">
                        <?php if (!empty($post['url_media'])): ?>
                            <?php if (preg_match('/\.(mp4|webm)$/i', $post['url_media'])): ?>
                                <video src="/BESTUNE2/public/<?= htmlspecialchars($post['url_media']) ?>" class="h-14 rounded" muted autoplay loop></video>
                            <?php else: ?>
                                <img src="/BESTUNE2/public/<?= htmlspecialchars($post['url_media']) ?>" alt="Media del post" class="h-14 rounded object-cover">
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-gray-400 italic">Sin media</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3"><?= date('d/m/Y', strtotime($post['fecha_post'])) ?></td>
                    <td class="p-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                            <?= $post['visible'] ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' ?>">
                            <i class="bi <?= $post['visible'] ? 'bi-check-circle' : 'bi-eye-slash' ?>"></i>
                            <?= $post['visible'] ? 'Visible' : 'Oculto' ?>
                        </span>
                    </td>
                    <td class="p-3 flex justify-center gap-2">
                        <a href="editar.php?id=<?= $post['id_post'] ?>" class="text-blue-600 hover:text-blue-800">
                            <i class="bi bi-pencil-square text-lg"></i>
                        </a>
                        <a href="../includes/actions/instagram/eliminar.php?id=<?= $post['id_post'] ?>"
                           onclick="return confirm('¿Estás seguro de eliminar este post?')"
                           class="text-red-600 hover:text-red-800">
                            <i class="bi bi-trash text-lg"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
