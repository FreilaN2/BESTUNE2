<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Ficha Técnica - Bestune</title>
    <link rel="shortcut icon" type="image/png" href="assets/img/favicon.ico">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        embed {
            width: 100%;
            height: 100vh;
        }
    </style>
</head>
<body>

<?php
$modelo = $_GET['modelo'] ?? '';

switch ($modelo) {
    case 't55':
        $archivo = 'assets/img/t55/T55_FICHA_TECNICA.pdf';
        break;

    case 'parte-t55':
        $archivo = 'assets/img/t55/CATALOGO_PART_T55_2023_CON_INDICE.pdf';
        break;

    case 'manual-t55':
        $archivo = 'assets/img/t55/MANUAL_CLIENTE_T55_ESPAÑOL.pdf';
        break;

    case 't77':
        $archivo = 'assets/img/t77/T77_FICHA_TECNICA.pdf';
        break;

    case 'parte-t77':
        $archivo = 'assets/img/t77/CATALOGO_PART_T77_2023_CON_INDICE.pdf';
        break;

    case 'manual-t77':
        $archivo = 'assets/img/t77/MANUAL_CLIENTE_T77_ESPAÑOL.pdf';
        break;

    case 't99':
        $archivo = 'assets/img/t99/T99_FICHA_TECNICA.pdf';
        break;

    case 'parte-t99':
        $archivo = 'assets/img/t99/CATALOGO_PART_T99_2023_CON_INDICE.pdf';
        break;

    case 'manual-t99':
        $archivo = 'assets/img/t99/MANUAL_CLIENTE_T99_ESPAÑOL.pdf';
        break;

    case 'b70':
        $archivo = 'assets/img/b70/B70_FICHA_TECNICA.pdf';
        break;

    case 'r7':
        $archivo = 'assets/img/r7/R7_FICHA_TECNICA.pdf';
        break;

    case 'corollacross':
        $archivo = 'assets/img/corollacross/corollacross_FICHA_TECNICA.pdf';
        break;

    default:
        $archivo = '';
        break;
}

if ($archivo && file_exists($archivo)) {
    echo '<embed src="' . $archivo . '" type="application/pdf">';
} else {
    echo '
    <div style="text-align: center; padding: 50px;">
        <h2>Documento no encontrado</h2>
        <p>El documento solicitado no está disponible.</p>
    </div>';
}
?>

</body>
</html>
