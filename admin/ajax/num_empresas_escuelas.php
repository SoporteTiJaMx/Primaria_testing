<?php
if(
	!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
){
	session_start();
	include_once('../../scripts/conexion.php');

	$Licencia_ID = $_POST['Licencia_ID'];

	$query = "SELECT licencia_escuela.ID_licencia_escuela, licencia_escuela.Licencia_ID, licencia_escuela.Escuela_ID, licencia_escuela.Num_Empresas, escuelas.Escuela_nombre, COUNT(DISTINCT(licencia_empresa.Empresa_ID)) AS Registradas, (SELECT COUNT(Empresa_estatus) FROM empresas WHERE Empresa_estatus != 'Cancelada' AND Escuela_ID = licencia_escuela.Escuela_ID) AS Utilizadas FROM licencia_escuela LEFT JOIN escuelas ON licencia_escuela.Escuela_ID = escuelas.Escuela_ID LEFT JOIN licencia_empresa ON licencia_empresa.Escuela_ID = escuelas.Escuela_ID WHERE licencia_escuela.Licencia_ID=? GROUP BY licencia_escuela.Escuela_ID";
	if ($stmt = $con->prepare($query)) {
		$stmt->bind_param("i", $Licencia_ID);
		$stmt->execute();
		$stmt->bind_result($ID_licencia_escuela, $Licencia_ID, $Escuela_ID, $Num_Empresas, $Escuela_nombre, $registradas, $utilizadas);

		$tabla = "
			<table id='num_empresas_escuelas_table' class='table table-hover dt-responsive wrap' style='width:100%'>
		        <thead>
		            <tr>
		                <th>Escuela</th>
		                <th class='align-middle text-center'>No. de Empresas</th>
		                <th class='align-middle text-center'>Registradas / Disponibles</th>
		            </tr>
		        </thead>
		        <tbody>";

		while ($stmt->fetch()) {
			$disponibles = $Num_Empresas - $utilizadas;
			$tabla.="<tr>
				<td class='align-middle'>" . $Escuela_nombre . "</td>
				<td class='align-middle text-center'>
					<div class=''>
						<input type='number' class='form-control rounded text-center input_empresas_por_escuela' id=" . $Escuela_ID . " min='" . $registradas . "' max='50' value=" . $Num_Empresas . ">
					</div>
				</td>
				<td class='align-middle text-center'>" . $registradas . " / " . $disponibles . "</td>
			</tr>";
		}
	    $tabla.="</tbody>
	        </table>";
		$stmt->close();
	} else {
		$tabla = "No hay datos por mostrar";
	}

    echo $tabla;
} else {
	echo "<META HTTP-EQUIV='REFRESH' CONTENT='5;URL=../../superadmin.php'>";
	include_once('../../includes/header.php');
?>
	<div class="container h-100">
		<div class="row align-items-center h-100">
			<div class="col-6 mx-auto">
				<div class="card shadow">
					<div class="card-body">
						<h5 class="card-title mb-5 align-middle"><i class="fas fa-exclamation-circle fa-2x fa-fw ml-2 mr-3 text-pale-green"></i>No puedes acceder a esta sección.</h5>
						<p class="card-text">En unos segundos serás redirigido. Da click en el botón para hacerlo de inmediato.</p>
						<div class="text-right mt-5"><a href="../../superadmin.php" class="btn btn-warning">Ir</a></div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	include_once('../../includes/footer.php');
}

?>