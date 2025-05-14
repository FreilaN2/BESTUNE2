<?php
$aliados = $_GET['aliados'] ?? null;

$titulo = match((int) $aliados) {
    1 => 'CHACAO',
    2 => 'GUATIRE',
    3 => 'ALTAMIRA',
    4 => 'VALENCIA',
    5 => 'ANACO',
    6 => 'LECHERIA',
    7 => 'EL TIGRE',
    8 => 'BARINAS',
    9 => 'LAS MERCEDES',
    10 => 'VALLE DE LA PASCUA',
    default => 'ALIADO'
};
?>

<div class="breadcrumb-section breadcrumb-bg" style="background-image:url('assets/img/carretera.webp')">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="breadcrumb-text">
                    <p>Agencias autorizadas</p>
                    <h1><?= $titulo ?></h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="contact-from-section mt-100 mb-150">
    <div class="container">
        <div class="row">
            <?php if ($aliados == 1): ?>
                <?php include '../app/views/contacto/aliado_1.php'; ?>
            <?php elseif ($aliados == 3): ?>
                <?php include '../app/views/contacto/aliado_3.php'; ?>
            <?php elseif ($aliados == 4): ?>
                <?php include '../app/views/contacto/aliado_4.php'; ?>
            <?php elseif ($aliados == 6): ?>
                <?php include '../app/views/contacto/aliado_6.php'; ?>
            <?php elseif ($aliados == 9): ?>
                <?php include '../app/views/contacto/aliado_9.php'; ?>
            <?php elseif ($aliados == 10): ?>
                <?php include '../app/views/contacto/aliado_10.php'; ?>
            <?php elseif ($aliados == 8): ?>
                <div class="col-lg-8 text-center">
                    <div class="section-title">
                        <h3 class="orange-text">COMING SOON</h3>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulario lateral derecho -->
            <div class="col-lg-4 mt-4">
                <div class="form-title">
                    <h2>Solicite información</h2>
                    <p>Nuestros agentes con gusto lo atenderán para cualquier información.</p>
                </div>
                <div id="form_status"></div>
                <div class="contact-form">
                    <form method="POST" action="#" id="fruitkha-contact">
                        <input type="hidden" name="token" value="FsWga4&@f6aw">
                        <p>
                            <input type="text" placeholder="Nombres y Apellidos" name="name" id="name" required>
                            <input type="email" placeholder="Correo electrónico" name="email" id="email" required>
                        </p>
                        <p>
                            <input type="tel" placeholder="Teléfono" name="phone" id="phone" style="width: 100%;" required>
                        </p>
                        <p>
                            <input type="text" placeholder="Asunto" name="subject" id="subject" required>
                        </p>
                        <p>
                            <textarea name="message" id="message" cols="30" rows="10" placeholder="Mensaje" required></textarea>
                        </p>
                        <p><input type="submit" value="Enviar"></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>