<?php
date_default_timezone_set("America/Mexico_City");
include_once('../scripts/conexion.php');
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
	$nombre = (isset($_POST["user"])) ? strip_tags(trim($_POST["user"])) : null;
	$pass = (isset($_POST["pass"])) ? strip_tags(trim($_POST["pass"])) : null;
	if (isset($_POST["csrf"]) && $_POST["csrf"] == $_SESSION["token"]) {
		$query = "SELECT * FROM usuarios WHERE Usuario='".mysqli_real_escape_string($con, $nombre)."'";
		$result = mysqli_query($con, $query);
		$row = mysqli_fetch_row($result);
		if (password_verify($pass, $row[2])){
			$User_ID = $row[0];
			$tipo = $row[3];
			$subseccion_general_menos_admins = 80;

			$_SESSION["autentificado"]= "SI";
			$_SESSION["ultimoAcceso"]= date("Y-n-j H:i:s");
			$_SESSION["ultimaAccion"]= $_SESSION["ultimoAcceso"];
			$_SESSION["tipo"]= $tipo;
			$_SESSION["TiempoOnline"]= $row[7];
			$_SESSION["lang"]= $row[8];
			$_SESSION["ancho"]= $row[9];
			$_SESSION["User_ID"] = $User_ID;
			$conexiones = $row[5]+1;
			$_SESSION["Num_accesos"]= $conexiones;
			$update = mysqli_query($con, "UPDATE usuarios SET UltimoAcceso='".$_SESSION["ultimoAcceso"]."', Num_accesos=".$conexiones." WHERE User_ID=".$User_ID);
			$_SESSION["navegador"] = $_SERVER['HTTP_USER_AGENT'];

			if($tipo == 'Sadmin'){
				$datos = mysqli_fetch_row(mysqli_query($con, "SELECT * FROM superadmins WHERE User_ID=" . $User_ID . " AND Sadmin_estatus<2"));
				if (isset($datos[0]) AND $datos[0]>0) {
					$_SESSION["nombre"] = $datos[2];
					$_SESSION["ap_paterno"] = $datos[3];
					$_SESSION["ap_materno"] = $datos[4];
					$_SESSION["email"] = $datos[5];
					$_SESSION["estatus"] = $datos[6];
					$_SESSION["subseccion_general"] = 1000;
					$seccion = "../superadmin.php";
				} else {
					$seccion = "../error_acceso.php";
				}
			}else if($tipo == 'Admin'){
				$datos = mysqli_fetch_row(mysqli_query($con, "SELECT * FROM administradores INNER JOIN centros ON administradores.Centro_ID=centros.Centro_ID WHERE User_ID=" . $User_ID . " AND Admin_estatus<2"));
				if (isset($datos[0]) AND $datos[0]>0) {
					$_SESSION["centro_ID"] = $datos[2];
					$_SESSION["nombre"] = $datos[3];
					$_SESSION["ap_paterno"] = $datos[4];
					$_SESSION["ap_materno"] = $datos[5];
					$_SESSION["email"] = $datos[6];
					$_SESSION["tel"] = $datos[7];
					$_SESSION["cel"] = $datos[8];
					$_SESSION["dir"] = $datos[9];
					$_SESSION["estatus"] = $datos[10];
					$_SESSION["centro"] = $datos[12];
					$_SESSION["subseccion_general"] = 1000;
					$seccion = "../admin.php";
				} else {
					$seccion = "../error_acceso.php";
				}
			}else if($tipo == 'Vincu'){
				$datos = mysqli_fetch_row(mysqli_query($con, "SELECT * FROM vinculadores INNER JOIN instituciones ON vinculadores.Institucion_ID=instituciones.Institucion_ID WHERE vinculadores.User_ID=" . $User_ID . " AND Vincul_estatus<2"));
				if (isset($datos[0]) AND $datos[0]>0) {
					$_SESSION["Centro_ID"] = $datos[2];
					$_SESSION["Institucion_ID"] = $datos[3];
					$_SESSION["puesto"] = $datos[4];
					$_SESSION["nombre"] = $datos[5];
					$_SESSION["ap_paterno"] = $datos[6];
					$_SESSION["ap_materno"] = $datos[7];
					$_SESSION["cumple"] = $datos[8];
					$_SESSION["email"] = $datos[9];
					$_SESSION["tel"] = $datos[10];
					$_SESSION["cel"] = $datos[11];
					$_SESSION["estatus"] = $datos[12];
					$_SESSION["Institucion_nombre"] = $datos[15];
					$_SESSION["Institucion_director"] = $datos[16];
					$_SESSION["Institucion_web"] = $datos[17];
					$_SESSION["Institucion_estatus"] = $datos[18];
					$_SESSION["subseccion_general"] = $subseccion_general_menos_admins;
					$seccion = "../vinculador.php";
				} else {
					$seccion = "../error_acceso.php";
				}
			}else if($tipo == 'Coord'){
				$Institucion_ID = mysqli_fetch_row(mysqli_query($con, "SELECT instituciones.Institucion_ID FROM coordinadores INNER JOIN escuelas ON coordinadores.Escuela_ID=escuelas.Escuela_ID INNER JOIN instituciones ON escuelas.Institucion_ID=instituciones.Institucion_ID WHERE coordinadores.User_ID=" . $User_ID . " AND Coord_estatus<2"));
				if ($Institucion_ID != '') {
					$datos = mysqli_fetch_row(mysqli_query($con, "SELECT * FROM coordinadores INNER JOIN escuelas ON coordinadores.Escuela_ID=escuelas.Escuela_ID INNER JOIN instituciones ON escuelas.Institucion_ID=instituciones.Institucion_ID LEFT JOIN vinculadores ON vinculadores.Institucion_ID=instituciones.Institucion_ID WHERE coordinadores.User_ID=" . $User_ID . " AND Coord_estatus<2"));
					if (isset($datos[0]) AND $datos[0]>0) {
						$_SESSION["Centro_ID"] = $datos[2];
						$_SESSION["Escuela_ID"] = $datos[3];
						$_SESSION["puesto"] = $datos[4];
						$_SESSION["nombre"] = $datos[5];
						$_SESSION["ap_paterno"] = $datos[6];
						$_SESSION["ap_materno"] = $datos[7];
						$_SESSION["cumple"] = $datos[8];
						$_SESSION["email"] = $datos[9];
						$_SESSION["tel"] = $datos[10];
						$_SESSION["cel"] = $datos[11];
						$_SESSION["estatus"] = $datos[12];
						$_SESSION["Institucion_ID"] = $datos[14];
						$_SESSION["Escuela_nombre"] = $datos[16];
						$_SESSION["Escuela_estado"] = $datos[17];
						$_SESSION["Escuela_maps"] = $datos[18];
						$_SESSION["Escuela_web"] = $datos[19];
						$_SESSION["Institucion_nombre"] = $datos[23];
						$_SESSION["Institucion_director"] = $datos[24];
						$_SESSION["Institucion_web"] = $datos[25];
						$_SESSION["Institucion_vinculador_ID"] = $datos[27];
						$_SESSION["Institucion_vinculador"] = $datos[31] . " " . $datos[32] . " " . $datos[33];
						$_SESSION["Institucion_vinculador_mail"] = $datos[36];
						$_SESSION["Institucion_vinculador_tel"] = $datos[37];
						$_SESSION["subseccion_general"] = $subseccion_general_menos_admins;
						$seccion = "../coordinador.php";
					} else {
						$seccion = "../error_acceso.php";
					}
				} else {
					$datos = mysqli_fetch_row(mysqli_query($con, "SELECT * FROM coordinadores INNER JOIN escuelas ON coordinadores.Escuela_ID=escuelas.Escuela_ID WHERE coordinadores.User_ID=" . $User_ID . " AND Coord_estatus<2"));
					if (isset($datos[0]) AND $datos[0]>0) {
						$_SESSION["Centro_ID"] = $datos[2];
						$_SESSION["Escuela_ID"] = $datos[3];
						$_SESSION["puesto"] = $datos[4];
						$_SESSION["nombre"] = $datos[5];
						$_SESSION["ap_paterno"] = $datos[6];
						$_SESSION["ap_materno"] = $datos[7];
						$_SESSION["cumple"] = $datos[8];
						$_SESSION["email"] = $datos[9];
						$_SESSION["tel"] = $datos[10];
						$_SESSION["cel"] = $datos[11];
						$_SESSION["estatus"] = $datos[12];
						$_SESSION["Institucion_ID"] = $datos[15];
						$_SESSION["Escuela_nombre"] = $datos[16];
						$_SESSION["Escuela_estado"] = $datos[17];
						$_SESSION["Escuela_maps"] = $datos[18];
						$_SESSION["Escuela_web"] = $datos[19];
						$_SESSION["subseccion_general"] = $subseccion_general_menos_admins;
						$seccion = "../coordinador.php";
					} else {
						$seccion = "../error_acceso.php";
					}
				}
				$query3 = "SELECT DISTINCT Licencia_ID FROM licencia_empresa WHERE Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Empresa_estatus != 'Cancelada' AND Escuela_ID=".$_SESSION["Escuela_ID"].")";
				$result3 = mysqli_query($con, $query3);
				$row3 = mysqli_fetch_row($result3);
				$_SESSION["Licencia_ID"] = $row3[0];
			}else if($tipo == 'Volun'){
				$datos = mysqli_fetch_row(mysqli_query($con, "SELECT * FROM asesores WHERE User_ID=" . $User_ID . " AND Asesor_estatus<2"));
				if (isset($datos[0]) AND $datos[0]>0) {
					$_SESSION["Asesor_ID"] = $datos[0];
					$_SESSION["Centro_ID"] = $datos[2];
					$_SESSION["nombre"] = $datos[3];
					$_SESSION["ap_paterno"] = $datos[4];
					$_SESSION["ap_materno"] = $datos[5];
					$_SESSION["carrera_ID"] = $datos[6];
					$_SESSION["cumple"] = $datos[7];
					$_SESSION["email"] = $datos[8];
					$_SESSION["tel"] = $datos[9];
					$_SESSION["cel"] = $datos[10];
					$_SESSION["trabajo"] = $datos[11];
					$_SESSION["puesto"] = $datos[12];
					$_SESSION["estatus"] = $datos[13];
					$_SESSION["subseccion_general"] = $subseccion_general_menos_admins;
					$query2 = "SELECT COUNT(*) FROM empresas WHERE Empresa_estatus != 'Cancelada' AND Asesor_ID=" . $_SESSION["Asesor_ID"];
					$result2 = mysqli_query($con, $query2);
					$row2 = mysqli_fetch_row($result2);
					$_SESSION["num_empresas"] = $row2[0];
					$query3 = "SELECT DISTINCT Licencia_ID FROM licencia_empresa WHERE Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Empresa_estatus != 'Cancelada' AND Asesor_ID=".$_SESSION["Asesor_ID"].")";
					$result3 = mysqli_query($con, $query3);
					$row3 = mysqli_fetch_row($result3);
					$_SESSION["Licencia_ID"] = $row3[0];
					$seccion = "../asesor.php";
					$datosS1 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s1 LEFT JOIN empresas ON empresas_info_s1.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s1.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s1.act_alumno=1"));
					if ($datosS1[0]>0) {
						$_SESSION["sesion1"] = 1;
						$_SESSION["sesion1_5"] = 1;
					}
					$datosS2_2 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s2_2 LEFT JOIN empresas ON empresas_info_s2_2.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s2_2.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s2_2.act_alumno=1"));
					if ($datosS2_2[0]>0) {
						$_SESSION["sesion2_2"] = 1;
						$_SESSION["sesion2"] = 1;
					}
					$datosS2_3 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s2_3 LEFT JOIN empresas ON empresas_info_s2_3.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s2_3.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s2_3.act_alumno=1"));
					if ($datosS2_3[0]>0) {
						$_SESSION["sesion2_3"] = 1;
						$_SESSION["sesion2"] = 1;
					}
					$datosS2_4 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s2_4 LEFT JOIN empresas ON empresas_info_s2_4.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s2_4.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s2_4.act_alumno=1"));
					if ($datosS2_4[0]>0) {
						$_SESSION["sesion2_4"] = 1;
						$_SESSION["sesion2"] = 1;
					}
					$datosS3_2 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s3_2 LEFT JOIN empresas ON empresas_info_s3_2.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s3_2.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s3_2.act_alumno=1"));
					if ($datosS3_2[0]>0) {
						$_SESSION["sesion3_2"] = 1;
						$_SESSION["sesion3"] = 1;
					}
					$datosS3_3 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s3_3 LEFT JOIN empresas ON empresas_info_s3_3.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s3_3.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s3_3.act_alumno=1"));
					if ($datosS3_3[0]>0) {
						$_SESSION["sesion3_3"] = 1;
						$_SESSION["sesion3"] = 1;
					}
					$datosS5_1 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s5_1 LEFT JOIN empresas ON empresas_info_s5_1.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s5_1.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s5_1.act_alumno=1"));
					if ($datosS5_1[0]>0) {
						$_SESSION["sesion5_1"] = 1;
						$_SESSION["sesion5"] = 1;
					}
					$datosS5_3 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s5_3 LEFT JOIN empresas ON empresas_info_s5_3.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s5_3.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s5_3.act_alumno=1"));
					if ($datosS5_3[0]>0) {
						$_SESSION["sesion5_3"] = 1;
						$_SESSION["sesion5"] = 1;
					}
					$datosS5_5 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s5_5_coments LEFT JOIN empresas ON empresas_info_s5_5_coments.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s5_5_coments.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s5_5_coments.act_alumno=1"));
					if ($datosS5_5[0]>0) {
						$_SESSION["sesion5_5"] = 1;
						$_SESSION["sesion5"] = 1;
					}
					$datosS6_3 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s6_3_coments LEFT JOIN empresas ON empresas_info_s6_3_coments.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s6_3_coments.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s6_3_coments.act_alumno=1 AND empresas_info_s6_3_coments.tipo=1"));
					if ($datosS6_3[0]>0) {
						$_SESSION["sesion6_3"] = 1;
						$_SESSION["sesion6"] = 1;
					}
					$datosS6_4 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s6_3_coments LEFT JOIN empresas ON empresas_info_s6_3_coments.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s6_3_coments.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s6_3_coments.act_alumno=1 AND empresas_info_s6_3_coments.tipo=2"));
					if ($datosS6_4[0]>0) {
						$_SESSION["sesion6_4"] = 1;
						$_SESSION["sesion6"] = 1;
					}
					$datosS6_5 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s6_5 LEFT JOIN empresas ON empresas_info_s6_5.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s6_5.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s6_5.act_alumno=1"));
					if ($datosS6_5[0]>0) {
						$_SESSION["sesion6_5"] = 1;
						$_SESSION["sesion6"] = 1;
					}
					$datosS6_6 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s6_6_coments LEFT JOIN empresas ON empresas_info_s6_6_coments.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s6_6_coments.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s6_6_coments.act_alumno=1"));
					if ($datosS6_6[0]>0) {
						$_SESSION["sesion6_6"] = 1;
						$_SESSION["sesion6"] = 1;
					}
					$datosS7_3 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s7_3_coments LEFT JOIN empresas ON empresas_info_s7_3_coments.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s7_3_coments.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s7_3_coments.act_alumno=1"));
					if ($datosS7_3[0]>0) {
						$_SESSION["sesion7_3"] = 1;
						$_SESSION["sesion7"] = 1;
					}
					$datosS7_4 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s7_4_coments LEFT JOIN empresas ON empresas_info_s7_4_coments.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s7_4_coments.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s7_4_coments.act_alumno=1"));
					if ($datosS7_4[0]>0) {
						$_SESSION["sesion7_4"] = 1;
						$_SESSION["sesion7"] = 1;
					}
					$datosS7_6 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s7_6_coments LEFT JOIN empresas ON empresas_info_s7_6_coments.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s7_6_coments.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s7_6_coments.act_alumno=1"));
					if ($datosS7_6[0]>0) {
						$_SESSION["sesion7_6"] = 1;
						$_SESSION["sesion7"] = 1;
					}
					$datosS7_7 = mysqli_fetch_row(mysqli_query($con, "SELECT count(*) FROM empresas_info_s7_7 LEFT JOIN empresas ON empresas_info_s7_7.Empresa_ID = empresas.Empresa_ID WHERE empresas_info_s7_7.Empresa_ID IN (SELECT Empresa_ID FROM empresas WHERE Asesor_ID=" . $_SESSION["Asesor_ID"] . " AND empresas.Empresa_estatus='Activa') AND empresas_info_s7_7.act_alumno=1"));
					if ($datosS7_7[0]>0) {
						$_SESSION["sesion7_7"] = 1;
						$_SESSION["sesion7"] = 1;
					}
				} else {
					$seccion = "../error_acceso.php";
				}
			}else if($tipo == 'Alumn'){
				$datos = mysqli_fetch_row(mysqli_query($con, "SELECT * FROM alumnos INNER JOIN centros ON alumnos.Centro_ID=centros.Centro_ID INNER JOIN empresas ON alumnos.Empresa_ID=empresas.Empresa_ID INNER JOIN puestos ON alumnos.Puesto_ID=puestos.Puesto_ID INNER JOIN licencia_empresa ON alumnos.Empresa_ID=licencia_empresa.Empresa_ID WHERE User_ID=" . $User_ID . " AND Alumno_estatus<2"));
				if (isset($datos[0]) AND $datos[0]>0) {
					$_SESSION["Alumno_ID"] = $datos[0];
					$_SESSION["Centro_ID"] = $datos[2];
					$_SESSION["Empresa_ID"] = $datos[3];
					$_SESSION["Puesto_ID"] = $datos[4];
					$_SESSION["nombre"] = $datos[5];
					$_SESSION["ap_paterno"] = $datos[6];
					$_SESSION["ap_materno"] = $datos[7];
					$_SESSION["carrera_ID"] = $datos[8];
					$_SESSION["turno"] = $datos[9];
					$_SESSION["cumple"] = $datos[10];
					$_SESSION["email"] = $datos[11];
					$_SESSION["tel"] = $datos[12];
					$_SESSION["cel"] = $datos[13];
					$_SESSION["alumno_score"] = $datos[14];
					$_SESSION["estatus"] = $datos[15];
					$_SESSION["carta_compromiso"] = $datos[16];
					$_SESSION["subseccion_general"] = $datos[17];
					$_SESSION["centro"] = $datos[19];
					$_SESSION["Escuela_ID"] = $datos[21];
					$_SESSION["Asesor_ID"] = $datos[22];
					$_SESSION["Empresa_nombre"] = $datos[23];
					$_SESSION["Empresa_producto"] = $datos[24];
					$_SESSION["Empresa_score"] = $datos[25];
					$_SESSION["Empresa_estatus"] = $datos[26];
					$_SESSION["Puesto_ID"] = $datos[27];
					$_SESSION["Puesto_nombre"] = $datos[28];
					$_SESSION["Licencia_ID"] = $datos[30];
					$seccion = "../alumno.php";
					$datosVariables = mysqli_fetch_row(mysqli_query($con, "SELECT valor_accion FROM variables WHERE Licencia_ID=" .$_SESSION['Licencia_ID']));
					if ($datosVariables[0]>0) {
						$_SESSION["valor_accion"] = $datosVariables[0];
					} else {
						$_SESSION["valor_accion"] = "";
					}
					$datosS1 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor, COUNT(Alumno_ID), problema1, problema2, conclusion FROM empresas_info_s1 WHERE Alumno_ID=" . $_SESSION["Alumno_ID"]));
					$_SESSION["sesion1"] = $datosS1[0];
					if ($datosS1[2]!="" AND $datosS1[3]!="" AND $datosS1[4]!="") {
						$_SESSION["sesion1_5"] = 1;
					} else {
						$_SESSION["sesion1_5"] = 0;
					}
					$datosS2_2 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s2_2 WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					$_SESSION["sesion2_2"] = $datosS2_2[0];
					$datosS2_3 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s2_3 WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					$_SESSION["sesion2_3"] = $datosS2_3[0];
					$datosS2_4 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s2_4 WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					$_SESSION["sesion2_4"] = $datosS2_4[0];
					$datosS3_2 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s3_2 WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					$_SESSION["sesion3_2"] = $datosS3_2[0];
					$datosS3_3 = mysqli_fetch_row(mysqli_query($con, "SELECT SUM(act_asesor) FROM empresas_info_s3_3 WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					if ($datosS3_3[0]>0) { $S3_3 = 1; } else { $S3_3 = 0; }
					$_SESSION["sesion3_3"] = $S3_3;
					$datosS5_1 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s5_1 WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					$_SESSION["sesion5_1"] = $datosS5_1[0];
					$datosS5_3 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s5_3 WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					$_SESSION["sesion5_3"] = $datosS5_3[0];
					$datosS6_3 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s6_3_coments WHERE Empresa_ID=" . $_SESSION["Empresa_ID"] . " AND tipo=1"));
					$_SESSION["sesion6_3"] = $datosS6_3[0];
					$datosS6_4 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s6_3_coments WHERE Empresa_ID=" . $_SESSION["Empresa_ID"] . " AND tipo=2"));
					$_SESSION["sesion6_4"] = $datosS6_4[0];
					$datosS6_5 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s6_5 WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					$_SESSION["sesion6_5"] = $datosS6_5[0];
					$datosS6_6 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s6_6_coments WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					$_SESSION["sesion6_6"] = $datosS6_6[0];
					$datosS7_3 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s7_3_coments WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					$_SESSION["sesion7_3"] = $datosS7_3[0];
					$datosS7_4 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s7_4_coments WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					$_SESSION["sesion7_4"] = $datosS7_4[0];
					$datosS7_6 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s7_6_coments WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					$_SESSION["sesion7_6"] = $datosS7_6[0];
					$datosS7_7 = mysqli_fetch_row(mysqli_query($con, "SELECT act_asesor FROM empresas_info_s7_7 WHERE Empresa_ID=" . $_SESSION["Empresa_ID"]));
					$_SESSION["sesion7_7"] = $datosS7_7[0];

					if ($datosS2_2[0] + $datosS2_3[0] + $datosS2_4[0]) {
						$_SESSION["sesion2"] = 1;
					}
					if (($datosS3_2[0] + $datosS3_3[0])>0) {
						$_SESSION["sesion3"] = 1;
					}
					if (($datosS5_1[0] + $datosS5_3[0])>0) {
						$_SESSION["sesion5"] = 1;
					}
					if (($datosS6_3[0] + $datosS6_4[0] + $datosS6_5[0] + $datosS6_6[0])>0) {
						$_SESSION["sesion6"] = 1;
					}
					if (($datosS7_3[0] + $datosS7_4[0] + $datosS7_6[0] + $datosS7_7[0])>0) {
						$_SESSION["sesion7"] = 1;
					}
				} else {
					$seccion = "../error_acceso.php";
				}
			}else{
				header("Location: ../error_acceso.php");
			}
			if($tipo != 'Sadmin' AND $tipo != 'Admin'){
				$datos_sesiones = mysqli_query($con, "SELECT * FROM eventos WHERE Licencia_ID=" . $_SESSION["Licencia_ID"]);
				while ($row_sesiones = mysqli_fetch_array($datos_sesiones, MYSQLI_ASSOC)) {
					if ($row_sesiones["Sesiones_ID"] == 1) {$_SESSION["Sesion_1_inicio"] = $row_sesiones["Eventos_inicio"]; $_SESSION["Sesion_1_fin"] = $row_sesiones["Eventos_fin"]; }
					if ($row_sesiones["Sesiones_ID"] == 2) {$_SESSION["Sesion_2_inicio"] = $row_sesiones["Eventos_inicio"]; $_SESSION["Sesion_2_fin"] = $row_sesiones["Eventos_fin"]; }
					if ($row_sesiones["Sesiones_ID"] == 3) {$_SESSION["Sesion_3_inicio"] = $row_sesiones["Eventos_inicio"]; $_SESSION["Sesion_3_fin"] = $row_sesiones["Eventos_fin"]; }
					if ($row_sesiones["Sesiones_ID"] == 4) {$_SESSION["Sesion_4_inicio"] = $row_sesiones["Eventos_inicio"]; $_SESSION["Sesion_4_fin"] = $row_sesiones["Eventos_fin"]; }
					if ($row_sesiones["Sesiones_ID"] == 5) {$_SESSION["Sesion_5_inicio"] = $row_sesiones["Eventos_inicio"]; $_SESSION["Sesion_5_fin"] = $row_sesiones["Eventos_fin"]; }
					if ($row_sesiones["Sesiones_ID"] == 6) {$_SESSION["Sesion_6_inicio"] = $row_sesiones["Eventos_inicio"]; $_SESSION["Sesion_6_fin"] = $row_sesiones["Eventos_fin"]; }
					if ($row_sesiones["Sesiones_ID"] == 7) {$_SESSION["Sesion_7_inicio"] = $row_sesiones["Eventos_inicio"]; $_SESSION["Sesion_7_fin"] = $row_sesiones["Eventos_fin"]; }
					if ($row_sesiones["Sesiones_ID"] == 8) {$_SESSION["Sesion_8_inicio"] = $row_sesiones["Eventos_inicio"]; $_SESSION["Sesion_8_fin"] = $row_sesiones["Eventos_fin"]; }
					/*if ($row_sesiones["Sesiones_ID"] == 9) {$_SESSION["Sesion_9_inicio"] = $row_sesiones["Eventos_inicio"]; $_SESSION["Sesion_9_fin"] = $row_sesiones["Eventos_fin"]; }
					if ($row_sesiones["Sesiones_ID"] == 10) {$_SESSION["Sesion_10_inicio"] = $row_sesiones["Eventos_inicio"]; $_SESSION["Sesion_10_fin"] = $row_sesiones["Eventos_fin"]; }
					if ($row_sesiones["Sesiones_ID"] == 11 {$_SESSION["Sesion_11_inicio"] = $row_sesiones["Eventos_inicio"]; $_SESSION["Sesion_11_fin"] = $row_sesiones["Eventos_fin"]; }
					if ($row_sesiones["Sesiones_ID"] == 12) {$_SESSION["Sesion_12_inicio"] = $row_sesiones["Eventos_inicio"]; $_SESSION["Sesion_12_fin"] = $row_sesiones["Eventos_fin"]; }*/
				}
			}
			header("Location:" . $seccion);
		} else {
			header("Location: ../error_acceso.php");
		}
	}
} else {
	header("Location: ../error_acceso.php");
}

?>