<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta content="bestune venezuela" name="keywords" />
	<meta content="bestune venezuela,BESTUNE,FAW" name="description" />
	<title>PLANES DE VENTA</title>

<div class="breadcrumb-section breadcrumb-bg" style="background-image:url('assets/img/carretera.webp')">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 offset-lg-2 text-center">
				<div class="breadcrumb-text">
					<p>CONOCE NUESTROS PLANES DE VENTA</p>
					<h1>Planes de Venta</h1>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="abt-section mb-100 mt-100">
    <div class="container">
        <div class="row gy-4">
		<?php
			$query = $pdo->query("SELECT * FROM planes WHERE activo = 1");

			while ($row = $query->fetch(PDO::FETCH_ASSOC)):
				// Validaciones y saneamiento
				$nombrePlan = !empty($row['nombre_plan']) ? htmlspecialchars($row['nombre_plan']) : 'Sin título';
				$imagenPlan = !empty($row['imagen_principal']) ? htmlspecialchars($row['imagen_principal']) : 'assets/img/default.png';
			?>
				<div class="col-lg-6 col-md-12 mb-4">
					<img src="<?= $imagenPlan ?>" alt="<?= $nombrePlan ?>" class="img-fluid rounded shadow img-clickable" onclick="abrirModal(this)">
				</div>
			<?php endwhile; ?>
        </div>
    </div>
</div>

<!-- Modal para imagen ampliada -->
<div class="modal fade" id="imagenModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
	<div class="modal-content border-0 bg-transparent">
	  <div class="modal-body p-0 text-center">
		<img id="modalImagen" src="" class="img-fluid" alt="Imagen ampliada">
	  </div>
	</div>
  </div>
</div>

<!-- Script para abrir modal -->
<script>
function abrirModal(elemento) {
	var modal = new bootstrap.Modal(document.getElementById('imagenModal'));
	document.getElementById("modalImagen").src = elemento.src;
	modal.show();
}
</script>
