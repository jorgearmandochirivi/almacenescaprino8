<?php
	include ("admin/config.inc.php");

	

switch($msg){
	case 1:
		$ERROR="No tiene una Sesi&oacute;n Activa";
		break;
	case 2:
		$ERROR="La sesi&oacute;n ya expir&oacute;, debe iniciar una nueva";
		break;
}

if(empty($redirect))
	$redirect = "./";

	switch($action){

		case 'Iniciar':
			$limpiaqry=db_query("delete from Sesion_Cliente where DATE_ADD(Inicio, INTERVAL $tiemposession MINUTE) < NOW() ");

			$clave = make_safe($_POST["clave"]);
			$login = make_safe($_POST["login"]);


			$pass=encode_passwd($clave,$strcript);
			 $sql_usuario = "select * from Empleado
Where User = '$login' AND Password ='$pass' AND Autorizado = 'S' ";

			$userqry = db_query( $sql_usuario);
/*echo $sql_usuario;*/

					if (db_num_rows($userqry)!= 0){

						$datos_user_obj = db_fetch_object($userqry);

							/**************Consulta del Valor del IVA para guardarlo en los datos de Sesion*****/
								$sql_iva = "SELECT * FROM IVA LIMIT 1";
								$query_iva = db_query( $sql_iva );
								$r_iva = db_fetch_object( $query_iva );

								$iva = $r_iva->Valor / 100;
							/******** Fin Consulta del Valor del IVA para guardarlo en los datos de Sesion*****/
							$IP = get_IP();


							$array_ip=explode(",",$IP);
							if(count($array_ip)>1)
								$IP = trim($array_ip[1]);

						//echo $IP;
							$sql_punto = "SELECT * FROM PuntoVenta WHERE IP = '" . $IP . "' AND IDPuntoVenta = '" . $datos_user_obj->IDPuntoVenta . "' ";
							$qry_punto = db_query( $sql_punto );

					

							if( db_num_rows( $qry_punto ) == 0 && $datos_user_obj->Nivel <> 0 && ($datos_user_obj->IDPuntoVenta!=29 && $datos_user_obj->IDPuntoVenta!=1 && $datos_user_obj->IDPuntoVenta!=22 && $datos_user_obj->IDPuntoVenta!=14 && $datos_user_obj->IDPuntoVenta!=10 && $datos_user_obj->IDPuntoVenta!=24 && $datos_user_obj->IDPuntoVenta!=6 && $datos_user_obj->IDPuntoVenta!=25 && $datos_user_obj->IDPuntoVenta!=16) && $IP <> "190.85.198.43" && $IP <> "190.85.198.41" && $IP <> "186.82.253.17" && $IP <> "190.146.231.50" && $IP <> "192.168.0.180" && $IP <> "127.0.0.1" &&  $IP <> "190.85.198.43" &&  $IP <> "186.116.156.45")
							{
								echo "Usted no esta autorizado para ingresar al almacen desde esa ubicaci&oacute;n. Se ah enviado un email para realizar seguimiento de su ubicaci&oacute;n:";

								//estan tratando de entrar a:
								$sql_punto_usuario = " SELECT * FROM PuntoVenta WHERE IDPuntoVenta = '" . $datos_user_obj->IDPuntoVenta . "' ";
								$qry_punto_usuario = db_query( $sql_punto_usuario );
								$r_punto_usuario = db_fetch_array( $qry_punto_usuario );

								//tratando desde
								$sql_punto_ip = " SELECT * FROM PuntoVenta WHERE IP = '" . $IP . "' ";
								$qry_punto_ip = db_query( $sql_punto_ip );
								$r_punto_ip = db_fetch_array( $qry_punto_ip );

								if( empty( $r_punto_ip["Nombre"] ) )
									$r_punto_ip["Nombre"] = " Almacen no encontrado ";

								$cabeceras .= 'From: SIM Colombia <info@simcolombia.com>' . "\r\n";
								$subject = "Alguien desde la IP " . $IP . " que es del almacen: " . $r_punto_ip["Nombre"] . ", esta entrando a " . $r_punto_usuario["Nombre"] . " sin estar alli ";


								include_once("admin/lib/class.phpmailer.php");
								include_once("admin/lib/class.smtp.php");
								$mensaje="Ingreso no autorizado al almacen " . $datos_user_obj->IDPuntoVenta . " ( " . $r_punto_usuario["Nombre"] . " ) ";
								$mail = new phpmailer();
								$mail->AddAddress("jaimer@calzadocaprino.com");
								//$mail->AddAddress("di.carourrego@gmail.com");
								$mail->AddAddress("mapereira@calzadocaprino.com");
								$mail->AddAddress("currego@calzadocaprino.com");
								$mail->AddAddress("supervisor@calzadocaprino.com");
								$mail->AddAddress("medellin@calzadocaprino.com");
								$mail->AddAddress("jorgechirivi@gmail.com");
								$mail->Subject=$subject;
								$mail->Body =$mensaje;
								$mail->IsHTML(true);
								$mail->Sender='ventas@calzadocaprino.com';
								$mail->Timeout=120;
								$mail->Host = "localhost";
								$mail->Mailer = 'smtp';
								$mail->Password = 's0luci0nes#A';
								$mail->Username = 'postmater@correosim.com';
								$mail->SMTPAuth = false;
								$mail->From = "tienda@calzadocaprino.com";
								$mail->FromName = "Caprino";
								$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
								//$confirm=$mail->Send();
								mail ( "jaimer@calzadocaprino.com, mapereira@calzadocaprino.com,currego@calzadocaprino.com,medellin@calzadocaprino.com" , "Ingreso no autorizado al almacen " . $datos_user_obj->IDPuntoVenta . " ( " . $r_punto_usuario["Nombre"] . " ) " , $subject, $cabeceras );
								exit;
							}//end if
							
							$Usuario=array("Nivel"=>$datos_user_obj->Nivel,"IDUsuario"=>$datos_user_obj->IDEmpleado,"Nombre"=>$datos_user_obj->Nombre,"User"=>$user,"flag"=>"TRUE","IVA"=>$iva,"IDPuntoVenta"=>$datos_user_obj->IDPuntoVenta);

							$usuariosave= addslashes(serialize($Usuario));

							$newsesion=md5(uniqid(date("Y-m-d",time())));

							$fecha=date("Y-m-d H-i-s",time());
							$guardarqry = db_query("insert into Sesion_Cliente values ('$newsesion','$datos_user_obj->IDEmpleado','$fecha','$usuariosave')");
							//INSERTAR LOG DE ACCESO
							insertlog_acceso( $datos_user_obj->IDEmpleado,$datos_user_obj->IDPuntoVenta,$datos_user_obj->Nombre . " " . $login );							

							
							//SET COOKIE
							setcookie("COOKIE_CLIENTE",$newsesion);

							//insertlog($datos_user_obj->IDEmpleado,"Login",$datos_user_obj->IDEmpleado,"Ingreso","Ingreso ".$datos_user_obj->Nombre." ".$datos_user_obj->Apellidos." al Modulo Administrativo");

							
							header("Location: $redirect");

					}//if ($pass!=$userdata->password)
					else
						$ERROR="Verifique nombre de usuario y clave";

		break;//Case 'Iniciar'

		case 'LogOut':

				setcookie("COOKIE_CLIENTE"); //Independiente se libera el cookie

				$COOKIE_CLIENTE = $_COOKIE["COOKIE_CLIENTE"];

				$borrarqry = db_query("delete from Sesion_Cliente where IDSesion='$COOKIE_CLIENTE'");

				$err=1;

				$ERROR="Sesion terminada correctamente";


		break; //case 'Logout'

	}//switch($action)

?>
<html>


	<head>

		<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">

		<title>Inicio de Sesion</title>

<script language="JavaScript1.2"><!--

	function Evalua(){

		if (document.loginfrm.login.value==""){
			window.alert("Por favor llene el campo 'Usuario'");
			document.loginfrm.login.focus();
			return false;
		}

		if ( document.loginfrm.clave.value==""){
			window.alert("Por favor llene el campo 'Clave'");
			document.loginfrm.clave.focus();
			return false;
		}
	}

	// -->

</script>
<link rel="stylesheet" href="admin/styles.css" type="text/css">
</head>

<body bgcolor="white">
<table border="0" cellspacing="1" cellpadding="0" align=center bgcolor="#345487">
			<tr>
				<td class="maintitle">&nbsp;Inicio de Sesi&oacute;n</td>
			</tr>
			<tr>
	<td class='mainbg'>
		<table class="forumline" width="310" cellpadding="2" cellspacing="1" border="0">
			<form action="<?php echo $PHP_SELF;?>" method="post" name="loginfrm" onSubmit="return Evalua()">
			<tr>
				<td class="titlemedium" colspan="4" height="25">Ingrese su Usuario y Password para acceder a la aplicaci&oacute;n</td>
			</tr>
			<?php
											 if (isset($ERROR)){
											 ?>
			<tr>
					<td class="row1" colspan="4"><span class=gen>
												 <?php echo "$ERROR";
										?></span></td>
				</tr>
				<?php }
				?>
			<tr>
				<td class="row1" colspan="2"><span class="gen">Usuario :</span></td>
				<td class="row2" colspan="2" valign="top"><span class="genmed"><input type="text" class="post" name="login" size="20" /></span></td>
			</tr>
			<tr>
				<td class="row1" colspan="2"><span class="gen">Password :</span></td>
				<td class="row2" colspan="2" valign="middle"><span class="genmed"><input type="password" class="post" name="clave" size="20" /></span></td>
			</tr>
			<tr>
				<td class="rowform" colspan="4" align="center" height="28"><input type="hidden" value="<?php echo $redirect;?>" name="redirect">
				<input class="submit" type="submit" name="action" value="Iniciar">
				<input type="reset" class="submit" value="Limpiar" class="buttons"></td>
			</tr>
			</form>
		</table>
	</td>
	</tr>
</table>
	</body>
</html>
