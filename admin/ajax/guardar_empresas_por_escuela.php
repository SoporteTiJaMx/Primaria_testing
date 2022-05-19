<?php
if(
	!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
){
	session_start();
	include_once('../../scripts/conexion.php');

	$Licencia_ID = $_POST['Licencia_ID'];
	$Array_empresas_por_escuela = $_POST['Array_empresas_por_escuela'];
	$No_IDs = sizeof($Array_empresas_por_escuela);
	$Array_empresas_por_escuela[0][0];

	$query = "UPDATE licencia_escuela SET Num_Empresas=? WHERE Licencia_ID=? and Escuela_ID=?";
	if ($stmt = $con->prepare($query)) {
		for ($i=0; $i < $No_IDs; $i++) {
			$stmt->bind_param("iii", $Array_empresas_por_escuela[$i][1], $Licencia_ID, $Array_empresas_por_escuela[$i][0]);
			$stmt->execute();
		}
		$stmt->close();
	}
} else {
	echo "<META HTTP-EQUIV='REFRESH' CONTENT='5;URL=../../admin.php'>";
	include_once('../../includes/header.php');
?>
	<div class="container h-100">
		<div class="row align-items-center h-100">
			<div class="col-6 mx-auto">
				<div class="card shadow">
					<div class="card-body">
						<h5 class="card-title mb-5 align-middle"><i class="fas fa-exclamation-circle fa-2x fa-fw ml-2 mr-3 text-pale-green"></i>No puedes acceder a esta sección.</h5>
						<p class="card-text">En unos segundos serás redirigido. Da click en el botón para hacerlo de inmediato.</p>
						<div class="text-right mt-5"><a href="../../admin.php" class="btn btn-warning">Ir</a></div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	include_once('../../includes/footer.php');
}

?>