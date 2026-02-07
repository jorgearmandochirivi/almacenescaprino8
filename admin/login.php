<%
	include ("config.inc.php");

	Encabezado();
	
	/*
	$clave_enc = encode_passwd("jaim32009",$strcript);
			echo $clave_enc;*/
            			
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
			$limpiaqry=db_query("delete from Sesion where DATE_ADD(Inicio, INTERVAL $tiemposession MINUTE)<now()");
			
			$clave = make_safe($clave);
			$login = make_safe($login);

			
			$pass=encode_passwd($clave,$strcript);
			$userqry = db_query("select * from Empleado 
Where User = '$login' AND Password ='$pass' AND Autorizado = 'S' AND Nivel<3");
			
					if (db_num_rows($userqry)!= 0){

						$datos_user_obj = db_fetch_object($userqry);
//**********************************************  ************************************************************						
						$sql_sesion = "SELECT IDUsuario FROM Sesion WHERE IDUsuario = '$datos_user_obj->IDEmpleado'";
						$qry_sesion = db_query($sql_sesion);
						if(db_num_rows($qry_sesion)!=0){  //alguien ya ingreso con ese usuario
	 						$ERROR="Alguien ya ingres&oacute; con ese usuario";
						}
						else{
//**********************************************  ************************************************************						

							/**************Consulta del Valor del IVA para guardarlo en los datos de Sesion*****/
								$sql_iva = "SELECT * FROM IVA LIMIT 1";
								$query_iva = db_query( $sql_iva );
								$r_iva = db_fetch_object( $query_iva );
								
								$iva = $r_iva->Valor / 100;
							/******** Fin Consulta del Valor del IVA para guardarlo en los datos de Sesion*****/
							
							$Usuario=array("Nivel"=>$datos_user_obj->Nivel,"IDUsuario"=>$datos_user_obj->IDEmpleado,"Nombre"=>$datos_user_obj->Nombre,"User"=>$user,"flag"=>"TRUE","IVA"=>$iva);

							$usuariosave= addslashes(serialize($Usuario));

							$newsesion=md5(uniqid(date("Y-m-d",time())));

							$fecha=date("Y-m-d H-i-s",time());

							$guardarqry = db_query("insert into Sesion values ('$newsesion','$datos_user_obj->IDEmpleado','$fecha','$usuariosave')");

							setcookie("COOKIE_SESION",$newsesion);
							//insertlog($datos_user_obj->IDEmpleado,"Login",$datos_user_obj->IDEmpleado,"Ingreso","Ingreso ".$datos_user_obj->Nombre." ".$datos_user_obj->Apellidos." al Modulo Administrativo");

							header("Location: $redirect");
						}
					}//if ($pass!=$userdata->password)
					else
						$ERROR="Verifique nombre de usuario y clave";

		break;//Case 'Iniciar'

		case 'LogOut':

				setcookie("COOKIE_SESION"); //Independiente se libera el cookie
                
                $COOKIE_SESION = $_COOKIE["COOKIE_SESION"];

				$borrarqry = db_query("delete from Sesion where IDSesion='$COOKIE_SESION'");

				$err=1;

				$ERROR="Sesion terminada correctamente";


		break; //case 'Logout'

	}//switch($action)

%>
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
<link rel="stylesheet" href="styles.css" type="text/css">
</head>

<body bgcolor="white">
<table border="0" cellspacing="1" cellpadding="0" align=center bgcolor="#345487">
			<tr>
				<td class="maintitle">&nbsp;Inicio de Sesi&oacute;n</td>
			</tr>
			<tr>
	<td class='mainbg'>
		<table class="forumline" width="310" cellpadding="2" cellspacing="1" border="0">
			<form action="<%echo $PHP_SELF%>" method="post" name="loginfrm" onSubmit="return Evalua()">
			<tr>
				<td class="titlemedium" colspan="4" height="25">Ingrese su Usuario y Password para acceder a la aplicaci&oacute;n</td>
			</tr>
			<%
											 if (isset($ERROR)){
											 %>
			<tr>
					<td class="row1" colspan="4"><span class=gen>
												 <%echo "$ERROR";
										%></span></td>
				</tr>
				<%}
				%>
			<tr>
				<td class="row1" colspan="2"><span class="gen">Usuario :</span></td>
				<td class="row2" colspan="2" valign="top"><span class="genmed"><input type="text" class="post" name="login" size="20" /></span></td>
			</tr>
			<tr>
				<td class="row1" colspan="2"><span class="gen">Password :</span></td>
				<td class="row2" colspan="2" valign="middle"><span class="genmed"><input type="password" class="post" name="clave" size="20" /></span></td>
			</tr>
			<tr>
				<td class="rowform" colspan="4" align="center" height="28"><input type="hidden" value="<% echo $redirect%>" name="redirect">
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



	
