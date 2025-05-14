<?php
// Función para mostrar mensajes
function displayMessages() {
    if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show">
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php 
        unset($_SESSION['message'], $_SESSION['message_type']);
    endif;
}

// Función para subir imágenes
function uploadImage($file, $uploadDir = 'uploads/') {
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return ['error' => 'Formato de imagen no permitido'];
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
        return ['error' => 'La imagen es demasiado grande'];
    }
    
    $filename = uniqid() . '.' . $ext;
    $destino = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destino)) {
        return ['success' => $uploadDir . $filename];
    } else {
        return ['error' => 'Error al subir la imagen'];
    }
}
?>