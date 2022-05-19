<?php
include_once('../includes/coordinador_header.php');
include_once('../scripts/funciones.php');
include_once('../coordinador/side_navbar.php');
if ($_SESSION["tipo"] != "Coord") {
	header('Location: ../error.php');
} else {
	$_SESSION["token"] = md5(uniqid(mt_rand(), true));
	include_once('../scripts/conexion.php');
	$datos = mysqli_fetch_row(mysqli_query($con, "SELECT Licencia_nombre FROM licencias WHERE Licencia_ID=" . $_SESSION['Licencia_ID']));
	$_SESSION['licencia_nombre'] = $datos[0];
?>
<link rel="stylesheet" href="../css/responsive.bootstrap4.css">
<link rel="stylesheet" href="../css/bootstrap-datepicker.css">
<script type="text/javascript">
	$(document).ready(function(){
		$.ajax({
		  url: '../scripts/ajax/top3_ajax.php',
		  success: function(data)
		  {
			var array = JSON.parse(data);
			$("#top1").html(array.top1);
			$("#ptos_top1").html(array.puntos_top1);
			$("#top2").html(array.top2);
			$("#ptos_top2").html(array.puntos_top2);
			$("#top3").html(array.top3);
			$("#ptos_top3").html(array.puntos_top3);
		  }
		})
	})
</script>

	<h5 class="text-center text-dark_gray pt-3 pb-1">
		Observa el TOP 3 de Empresas juveniles del <?php if (isset($_SESSION['licencia_nombre'])) { echo " " . $_SESSION['licencia_nombre']; } ?>.
	</h5>
	<div class="card-body px-5">
		<div class="card-text px-5">
			<p class="text-justify">Entre paréntesis se muestran las <b>estrellas promedio de los alumnos integrantes</b> de cada empresa.</p>
		</div>
	</div>
	<div class="row justify-content-center">
		<div class="d-none col-lg-1"></div>
		<div class="col-12 col-lg-10">
			<div class="row">
				<div class="col-4 text-center">
					<br><br><br><br><br>
					<div class="">
						<h2 id='ptos_top2' class="font-weight-bold"></h2>
						<div id='top2'></div>
					</div>
				</div>
				<div class="col-4 text-center">
					<div class="">
						<h2 id='ptos_top1' class="font-weight-bold"></h2>
						<div id='top1'></div>
					</div>
				</div>
				<div class="col-4 text-center">
					<br><br><br><br><br><br><br><br><br><br>
					<div class="">
						<h2 id='ptos_top3' class="font-weight-bold"></h2>
						<div id='top3'></div>
					</div>
				</div>
			</div>
		</div>
		<div class="d-none col-lg-1"></div>
	</div>
	<div class="row justify-content-center mt-n2 mb-5 pb-5">
		<div class="d-none col-lg-1"></div>
		<div class="col-12 col-lg-10">
			<img src="../images/podium.png" alt="podium" class="img-fluid">
		</div>
		<div class="d-none col-lg-1"></div>
	</div>

<?php
}
include_once('../includes/coordinador_footer.php');
?>