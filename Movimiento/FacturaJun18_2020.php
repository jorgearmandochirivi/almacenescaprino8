<script type="text/javascript">
 window.history.forward(1);

function mostrar(){
	document.getElementById('oculto').style.display = 'block';
}

</script>


<?

//envia_bienvenida_club(316657);


	$TitleMod ="Factura";

	$Table = "Factura";
	$TableJoin = "DetalleFactura";
	$Key = "IDFactura";
	$MOD = "GenerarFactura";
	$m = "Movimientos";
	$permisos = get_permiso($ID_Usuario,$m,$Table);




if($permisos[0] >= 2)
{
		//echo $action;
		switch (nvl($action)) {
			case "insert" :

				$sql_cliente = "SELECT * FROM Cliente WHERE IDCliente = '$IDCliente'";
				$query_cliente = db_query($sql_cliente);
				$r_cliente = db_fetch_object( $query_cliente );
				print_form($r_cliente->IDCliente,"insertar","Confirmar Factura","Confirmar Factura",$_POST);

			break;

			case "insertar" :



				$sql_verificafactura = " SELECT * FROM Factura WHERE NumeroFactura = '$_POST[NumeroFactura]' AND IDPuntoVenta = '$IDPuntoVenta' and FechaFactura >='2019-07-20 00:00:00'";
				$qry_verificafactura = db_query( $sql_verificafactura );
				if( db_num_rows( $qry_verificafactura ) == 0 )
				{

					db_query("SET AUTOCOMMIT=0");
					db_query("BEGIN");

					$_POST[ValorTotalSinBono]=$_POST[ValorTotal];

					$_POST[ValorIVASinBono]=$_POST[ValorIVA];
					$_POST[ValorBono]=$_POST[SumaBono];

					$_POST[ValorIVA]=$_POST[ValorIvaMenosBono];
					$_POST[ValorTotal]=$_POST[ValorTotalFactura];


					$frm= vars_LOG($_POST);

					$frm['ValorTotalSinBono'] = ereg_replace("[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]","",$frm['ValorTotalSinBono']);
					$frm['ValorIVASinBono'] = ereg_replace("[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]","",$frm['ValorIVASinBono']);
					$frm['ValorBono'] = ereg_replace("[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]","",$frm['ValorBono']);
					$frm[IDClienteRedimioBono]=$frm[IDCliente];

					$frm['IDFactura'] = insert($frm);
					$frm= vars_LOG($frm);

					venta($frm);
					agregarventaempleado($frm);

					//HAcer iteraciioin por cada ValorFidelizacion
					foreach( $frm[IDBonoFidelizacion] as $key_fid => $valor_fid )
					{
						//envio notificacion de bono redimido
						envia_bono_redimido($frm["IDCliente"],$valor_fid,$frm[IDPuntoVenta]);
						//redimir puntos
						fid_redimir_bono( $frm["IDCliente"], $valor_fid,$frm['IDFactura'], $frm[IDPuntoVenta],$frm[IDClienteRedimioBono]  );
					}//end for



					//print_r($frm);
					// Si la factura tiene una alizanza de referidos marco las facturas como ya redimidas para no volver a tomarlas
					$alianza_referido=get_field("Alianza","AplicaReferido","IDAlianza",$frm["IDAlianza"]);
					$cantidad_referidos=get_field("Alianza","NumeroReferido","IDAlianza",$frm["IDAlianza"]);
					if($alianza_referido=="S"):
						$sql_factura_efectiva_referido = "Select F.IDFactura, F.IDPuntoVenta From Factura F, FormaPagoFactura FPF Where F.IDFactura = FPF.IDFactura and F.IDClienteReferente = '".$frm["IDCliente"]."' and F.Estado <> 'ANULADA' and RedimidaReferido <> 'S' Limit ".$cantidad_referidos;
						$result_factura_efectiva_referido =db_query($sql_factura_efectiva_referido);
						while ($row_factura_efectiva_referido = db_fetch_array($result_factura_efectiva_referido)):
								$actualiza_factura_efectiva_referido = "Update Factura Set RedimidaReferido = 'S', FechaRedimidaReferido = NOW() Where IDFactura = '".$row_factura_efectiva_referido["IDFactura"]."' and IDPuntoVenta = '".$row_factura_efectiva_referido["IDPuntoVenta"]."' and Estado <> 'ANULADA' and RedimidaReferido <> 'S'";
								db_query($actualiza_factura_efectiva_referido);
						endwhile;
					endif;



					//db_query( "tales" );
					db_query("COMMIT");


					//envio mail al refernte informando que un amigo hizo una compra
					if(!empty($frm["IDClienteReferente"])):
						envia_mail_referente($frm["IDClienteReferente"]);
					endif;

					/*echo "<script>alert('Pago Realizado Correctamente');</script>";
					//Imprimir la factura*/

					echo "<script>window.open('FormaPago/popFormapago.php?id=".$frm['IDFactura']."&idpunto=".$IDPuntoVenta."','','width=550, height=350, scrollbars=yes');location.href='?mod=Factura&action=edit&id=".$frm['IDFactura']."';</script>";

					//print_form($id,"update","Actualizar $TitleMod","Realizar Movimiento");

				}//end if
				else
				{
					echo "<script>alert('El numero de factura asignado ya fue creado. Verifique por favor');location.href='?mod=GenerarFactura';</script>";
				}//end else

			break;

			case "insertcliente" :

			//VERIFICO QUE LA CEDULA NO EXISTA
			if($_POST[ClubSuavidad]=="N")
				$cedula_validar=$_POST[cedula_no_club];
			else
				$cedula_validar=$_POST[Cedula];

			$cliente_existe=get_field("Cliente","Cedula","Cedula",$cedula_validar);
			if (!empty($cliente_existe)){
				echo "<script>alert('El numero de documento ya existe por favor verifique');location.href='?mod=GenerarFactura';</script>";
				exit;
			}




				if ($_POST[ClubSuavidad]=="N"){
					$_POST[Cedula]=$_POST[cedula_no_club];
					$_POST[Nombre]=$_POST[nombre_no_club];
					$_POST[Apellido]=$_POST[apellido_no_club];
					$_POST[Telefono]=$_POST[telefono_no_club];

				}

				$_POST['Gustos'] = implode(",",$_POST['Gustos']);
				$_POST['Hobbies'] = implode(",",$_POST['Hobbies']);
				$_POST['Deportes'] = implode(",",$_POST['Deportes']);
				$_POST['Musica'] = implode(",",$_POST['Musica']);
				$frm= vars_LOG($_POST);


				$frm['FechaTrCr'] = date("Y-m-d");
				$frm['IDPuntoVentaFideliza'] = $IDPuntoVenta;
				$frm['IDUsuarioFideliza'] = $frm[IDEmpleado];



				if( !empty( $_POST["Ano"] ) && !empty( $_POST["Mes"] ) )
				{

					$frm["Fidelizado"] = "S";


				}//end if

				//Envia Correo de bienvenida

				$id=insert_width_table($frm,"Cliente","IDCliente");


				//asigno tarjeta
				$sql_tarjeta="Update TarjetaFidelizacion Set IDCliente = '".$id. "', Estado = 'E', IDPuntoVenta = '".$IDPuntoVenta."',FechaTrCr = NOW() Where Codigo = '".$frm[NumeroTarjeta]."' ";
				db_query($sql_tarjeta);

				$id_tarjeta_fidelizacion=get_field("TarjetaFidelizacion","IDTarjetaFidelizacion","IDCliente",$id);
				$sql_actualizo_tarjeta="Update Cliente Set IDTarjetaFidelizacion = '".$id_tarjeta_fidelizacion. "' Where IDCliente = '".$id."' ";
				db_query($sql_actualizo_tarjeta);


				if ($frm[ClubSuavidad]=="S" && !empty($frm["EMail"]) ){
					//envia_bienvenida_club($id);
				}



                if( $frm["ClubSuavidad"] == "S" )
               		echo "<script>window.open( 'Factura/FImpresionFidelizacion.php?id=".$id."','','width=426, height=350' );</script>";






			             $arrayopciones=  $frm["Opcion"];

			  for($i=1;$i <10; $i++){
							 $array_implode = implode(",",$arrayopciones[$i]);
							 $array_implod[$i] = implode(",",$arrayopciones[$i]);
			   }

				$frm["Opcion"]=$array_implod;
				//actualizar fidelizacion
				actualiza_fidelizacion( $id, $frm["Opcion"], $frm["Respuesta"] );

				print_form($id,"insert","Generar Factura","Generar Factura");
			break;

			case "updatecliente" :


				if ($_POST[ClubSuavidad]=="N"){
					$_POST[Cedula]=$_POST[cedula_no_club];
				}


				$_POST['Gustos'] = implode(",",$_POST['Gustos']);
				$_POST['Hobbies'] = implode(",",$_POST['Hobbies']);
				$_POST['Deportes'] = implode(",",$_POST['Deportes']);
				$_POST['Musica'] = implode(",",$_POST['Musica']);

                $frm= vars_LOG($_POST);



				if ($_POST[ClubSuavidad]=="N"){
					$frm[Nombre]=$_POST[nombre_no_club];
					$frm[Apellido]=$_POST[apellido_no_club];
					$frm[Telefono]=$_POST[telefono_no_club];
				}

                $frm['FechaTrEd'] = date("Y-m-d");
				$frm['FechaRegistroClubSuavidad'] = date("Y-m-d H:m:s");
				$frm['FechaClubSuavidad'] = date("Y-m-d");

                $Table = "Cliente";
                $Key = "IDCliente";
			    $arrayopciones=  $frm["Opcion"];

			  for($i=1;$i <10; $i++){
							 $array_implode = implode(",",$arrayopciones[$i]);
							 $array_implod[$i] = implode(",",$arrayopciones[$i]);
			   }
				$frm["Opcion"]=$array_implod;


				if( !empty( $_POST["Ano"] ) && !empty( $_POST["Mes"] ) )
				{
					$frm["Fidelizado"] = "S";



					// Si se modifico algun dato genero boucher de cliente
					if($frm["NombreAnt"]!=$frm["Nombre"] || $frm["ApellidoAnt"]!=$frm["Apellido"] ||
					   $frm["GeneroAnt"]!=$frm["Genero"] || $frm["TelefonoAnt"]!=$frm["Telefono"] ||
					   $frm["EMailAnt"]!=$frm["EMail"] ||   $frm["CelularAnt"]!=$frm["Celular"] ||
					   $frm["DireccionAnt"]!=$frm["Direccion"] ||  $frm["IDCiudadAnt"]!=$frm["IDCiudad"] ||
					   $frm["AutorizaMailAnt"]!=$frm["AutorizaMail"] || $frm["AceptaSMSAnt"]!=$frm["AceptaSMS"] ||
					   $frm["AceptaTerminosAnt"]!=$frm["AceptaTerminos"] || $frm["AceptaHabeasAnt"]!=$frm["AceptaHabeas"] ||
					   $frm["ClubSuavidad"]!=$frm["ClubSuavidadAnt"]
					   ){
	                    echo "<script>window.open( 'Factura/FImpresionFidelizacion.php?id=".$frm["IDCliente"]."','','width=426, height=350' );</script>";
					}

				}//end if


				//Envia Correo de bienvenida
				if ($frm[ClubSuavidad]=="S" && !empty($frm["EMail"]) && ($frm[ClubSuavidadAnt]=="N" || $frm[ClubSuavidadAnt]=="" ) ){
					//envia_bienvenida_club($frm[IDCliente]);
				}

				if ($frm[ClubSuavidad]=="S" && ($frm[ClubSuavidadAnt]=="N" || $frm[ClubSuavidadAnt]=="" ) ){
					$frm['FechaTrCr']=date("Y-m-d H:i:s");
					$frm['FechaClubSuavidad'] = date("Y-m-d");
					$frm['IDPuntoVentaFideliza'] = $IDPuntoVenta;
					$frm['IDUsuarioFideliza'] = $frm[IDEmpleado];
				}



				//actualizo numero de tarjeta si se cambio
				if($frm["NumeroTarjetaAnt"]!=$frm["NumeroTarjeta"]){
					$sql_tarjeta="Update TarjetaFidelizacion Set IDCliente = '".$frm["IDCliente"]. "', Estado = 'E', IDPuntoVenta = '".$IDPuntoVenta."',FechaTrEd = NOW() Where Codigo = '".$frm[NumeroTarjeta]."' ";
					db_query($sql_tarjeta);
					$id_tarjeta_fidelizacion=get_field("TarjetaFidelizacion","IDTarjetaFidelizacion","IDCliente",$frm["IDCliente"]);
					$frm[IDTarjetaFidelizacion] = $id_tarjeta_fidelizacion;
				}

				//Verifico si se ingreso un nuevo numero de tarjeta por perdida
				if ($frm["NumeroTarjetaNuevo"]!=""){
					// Marco la anterior tarjeta con Estado de CP (Cambiada por perdida) para conservar el historial
					$sql_actualiza_tarjeta="Update TarjetaFidelizacion Set IDCliente = '', Estado = 'P', IDPuntoVenta = '', Observacion= '".$frm[Observacion]."' ,FechaTrEd = NOW() Where Codigo = '".$frm[NumeroTarjeta]."' ";
					db_query($sql_actualiza_tarjeta);

					// inserto la tarjeta anterior en historial de tarjetas del cliente
					$inserta_historial="Insert into HistorialTarjetaFidelizacion (Codigo, IDPuntoVenta, Estado, IDCliente, Observacion, FechaTrCr)
										Values ('".$frm[NumeroTarjeta]."','".$IDPuntoVenta."','P','".$frm["IDCliente"]."','".$frm[Observacion]."',NOW()) ";
					db_query($inserta_historial);

					$sql_tarjeta="Update TarjetaFidelizacion Set IDCliente = '".$frm["IDCliente"]. "', Estado = 'E', IDPuntoVenta = '".$IDPuntoVenta."',FechaTrEd = NOW() Where Codigo = '".$frm[NumeroTarjetaNuevo]."' ";
					db_query($sql_tarjeta);
					$id_tarjeta_fidelizacion=get_field("TarjetaFidelizacion","IDTarjetaFidelizacion","IDCliente",$frm["IDCliente"]);
					$frm[IDTarjetaFidelizacion] = $id_tarjeta_fidelizacion;
					$frm[NumeroTarjeta] = $frm["NumeroTarjetaNuevo"];
				}
                update( $frm );

				//actualiza_fidelizacion( $frm[IDCliente], $frm["Opcion"], $frm["Respuesta"] );

				print_form($r_cliente->IDCliente,"insertar","Confirmar Factura","Confirmar Factura",$_POST);

                $Table = "Factura";
                $Key = "IDFactura";

			break;


			case "edit":

               print_form($id,"insertar","Generar Factura","Generar Factura");
                break ;
			case "list" :
				$sql = make_qry_string($HTTP_GET_VARS);
				list_r($sql);
			break;
			case "mostrar":
				$sql_cliente = "SELECT * FROM Cliente WHERE Cedula = '$cedula' or NumeroTarjeta = '$cedula'	";
				$query_cliente = db_query($sql_cliente);
				if( db_num_rows( $query_cliente ) == 0 )
				{
					mostrarcedula("mostrar","Buscar Cliente",$IDPuntoVenta);
					print_formcliente($cedula,"insertcliente","Ingresar Cliente","Ingresar Cliente");
				}//end if( db_num_rows( $query_cliente ) == 0 )
				else
				{
					$r_cliente = db_fetch_object( $query_cliente );
					//print_form($r_cliente->IDCliente,"insert","Generar Factura","Generar Factura");
                    print_formcliente($r_cliente->Cedula,"updatecliente","Guardar Cliente","Guardar Cliente");
				}//end else



			break;
			default :
				mostrarcedula("mostrar","Buscar Cliente",$IDPuntoVenta);
			break;

		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col1");


function actualiza_fidelizacion($idcliente, $opciones, $abiertas = "" )
{


	foreach( $opciones as $idpregunta => $opcion )
	{
		$sql_delete = "DELETE FROM FidClienteRespuesta WHERE IDCliente = '" . $idcliente . "'  AND IDFidPregunta = '" . $idpregunta . "' ";
		$qry_delete = db_query( $sql_delete );

		//insertar respuesta
		$sql_insert = " INSERT INTO FidClienteRespuesta (IDCliente, IDFidPregunta, IDFidOpcion, Respuesta, FechaTrCr ) VALUES ( '" . $idcliente . "','" . $idpregunta . "','" . $opcion . "','" . $respuesta . "', NOW() ) ";
		$qry_insert = db_query( $sql_insert );

	}//end for

	//las respuestas abiertas
	foreach( $abiertas as $idpregunta => $value_respuesta )
	{
		$sql_verifica = " SELECT * FROM  FidClienteRespuesta WHERE IDCliente = '" . $idcliente . "'  AND IDFidPregunta = '" . $idpregunta . "' ";
		$qry_verifica = db_query( $sql_verifica );
		if( db_num_rows( $qry_verifica ) > 0  )
			if( !empty( $value_respuesta  ) )
			{
				//actualizar
				$sql_update = "UPDATE FidClienteRespuesta SET Respuesta = '" . $value_respuesta . "' WHERE IDCliente = '" . $idcliente . "'  AND IDFidPregunta = '" . $idpregunta . "'  ";
				db_query( $sql_update );
			}//end if
		elseif( !empty( $value_respuesta  ) )
		{
			//insertar la respuesta abierta
			$sql_insert = " INSERT INTO FidClienteRespuesta (IDCliente, IDFidPregunta, Respuesta, FechaTrCr ) VALUES ( '" . $idcliente . "','" . $idpregunta . "','" . $value_respuesta . "', NOW() ) ";
			$qry_insert = db_query( $sql_insert );
		}//end else
	}//end for

}//end fucntion fidelizacion

/*******************************************************************************************
		funtcion mostrarcedula
*******************************************************************************************/

function mostrarcedula($newmode,$submit_caption, $IDPuntoVenta){
?>

<br>

<table border="0" cellpadding="1" cellspacing="2" class="tbt" align="center" width="500">
	<tr>
    	<td class="tbtbot">Empleado(a)</td>
        <td class="tbtbot">Cargo</td>
    </tr>
    <?php
			$sql_vendedor = "Select * From Empleado 	Where IDPuntoVenta = '$IDPuntoVenta' and Publicar = 'S' Order by IDCargo Desc";
			$r_empleado = db_query($sql_vendedor);
			while($row_empleado = db_fetch_array($r_empleado)):
				if($row_empleado["IDCargo"]==3 || $row_empleado["IDCargo"]==4)
					$class_admin="font-weight:bold";
				else
					$class_admin="font-weight:bold";
			?>
            <tr>
                <td><?php echo $row_empleado["Nombre"] . " " . $row_empleado["Apellidos"]; ?></td>
                <td><span style="<?php echo $class_admin; ?>" ><?php echo get_field( "Cargo","Cargo","IDCargo",$row_empleado["IDCargo"] ); ?></span></td>
            </tr>
    <?php endwhile; ?>
</table>

<table border="0" cellpadding="1" cellspacing="2" class="tbt" align="center" width="500" bgcolor="#FCE9E9">
	<tr>
    	<td class="">ADMINISTRADOR(A)</td>
        <td class="">
        <?php  $IDemp=get_field( "PuntoVenta","IDEmpleado","IDPuntoVenta",$IDPuntoVenta );
					echo $IDemp=get_field( "Empleado","Nombre","IDEmpleado",$IDemp ) . " " . get_field( "Empleado","Apellidos","IDEmpleado",$IDemp );
				?>
        </td>
    </tr>


</table>


<form name="frmcliente" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" onsubmit="disable(this);">
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="500">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b><span class="gen">N&uacute;mero de Documento</span></td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table align="center" width="500" cellpadding="0" cellspacing="1" border="0" class="forumline">

  <tr>
	<td class="col1" align="center" valign="middle">Digite el n&uacute;mero de Documento &oacute;<br />
	  N&uacute;mero de tarjeta de fidelizaci&oacute;n por favor</td>
	<td class="col2" >
		<input type="text" class="tbox" name="cedula">
	</td>
  </tr>

  <tr>
	<td class="col2list" align="center" valign="middle" colspan="2">
		<input type="submit" class="button" name="enviar" value="<?=$submit_caption?>">
		<input type="hidden" value="<?=$newmode?>" name="action">
	</td>
  </tr>
</table>
</form>
<?
}//end	mostrar($newmode,$submit_caption)


/*******************************************************************************************
		funtcion Print_formCliente
*******************************************************************************************/
function print_formcliente($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$cedula,$array_gustos,$array_deportes,$array_hobbies,$array_musica, $datos,$IDPuntoVenta;

	$IDPuntoVenta = SIMReg::get("IDPuntoVenta");

	$qid = db_query(" SELECT * FROM Cliente WHERE Cedula = '$id' ");
	$r = db_fetch_object($qid);

	echo set_puntos( $r->IDCliente );

	$ciudadpunto = get_field("PuntoVenta","IDCiudad","IDPuntoVenta",$datos["IDPuntoVenta"]);

?>

<br>
	<form name="frmcliente" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" class="formvalida frmCrearCliente" >

<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="500">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				<?=$title?>
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table class="forumline" width="500" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline"  >


                        <?
                        if( !empty( $r->ClubSuavidad ) )
						{
							$msgfidelizado = "Cliente NO pertenece al Club de la Suavidad";
							if( $r->ClubSuavidad == "S" )
								$msgfidelizado = "Cliente pertence al Club de la Suavidad";
						?>
                        <tr >
                            <td class="col2 msg_alert" colspan="2">
                            	<? echo $msgfidelizado; ?>
                          	</td>
						</tr>
                        <?
						}//end if

						if( $r->ClubSuavidad == "S" ) //SI ESTA FIDELIZADO SALE TODO Y LO DEJA ACTUALIZAR
						{
						?>
                            <tr >
                                <td width="40%" class="col1">Numero de Documento ( <span class="rojo">*</span> )</td><td class=col2><input type=text size=25 class="tbox obligatorio cedula_guarda" title="Numero de documento"  name=Cedula id=Cedula value="<?=$r->Cedula ?>"  <?php if (!empty($r->IDCliente)){?> readonly <?php  }?> >
                                	<input type="hidden" name="ClubSuavidad" id="ClubSuavidad" value="S" />
                                 </td>
                            </tr>

                            <?
                                $readonly = "";
                                if( !empty( $r->Nombre ) )
                                {
                                    $readonly = " readonly='readonly' ";
                                }
                                if (!empty($r->IDCliente)){
                                    $bloqueo="disabled='disabled'";
                                 }



                            ?>

                            <tr >
                                <td class="col1" width="40%"> Nombre o Raz&oacute;n Social ( <span class="rojo">*</span> )</td><td class="col2"><input type=text size=25 class="tbox obligatorio" title="Nombre"   name=Nombre id=Nombre  value="<?=$r->Nombre ?>" <?php if (!empty($r->IDCliente ) && !empty($r->Nombre )){?> readonly <?php  }?>> </td>
                            </tr>
                            <tr >
                                <td width="40%" class="col1"> Apellidos ( <span class="rojo">*</span> )</td><td class="col2"><input type=text size=25 class="tbox obligatorio" title="Apellido"   name=Apellido id=Apellidos   value="<?=$r->Apellido ?>" <?php if (!empty($r->IDCliente) && !empty($r->Apellido)){?> readonly <?php  }?>> </td>
                            </tr>
                            <tr >
                                <td class="col1" width="40%">G&eacute;nero( <span class="rojo">*</span> )</td>
                                <?php
                                if (!empty($r->IDCliente) && !empty($r->Genero))
                                    $bloqueo="disabled='disabled'";
                                 else
                                  $bloqueo="";
                                 ?>
                                <td class="col2"><? echo formradiogroup(array('Femenino'=>'F','Masculino'=>'M'),$r->Genero, 'Genero',$bloqueo); ?></td>
                            </tr>
                            <tr >
                                <td width="40%" class="col1"> Telefono ( <span class="rojo">*</span> )</td><td class="col2"><input type=text size=25 class="tbox obligatorio" title="Telefono"   name=Telefono id=Telefono value="<?=$r->Telefono ?>" <?php if (!empty($r->IDCliente) && !empty($r->Telefono)){?> readonly <?php  }?>> </td>
                            </tr>
                            <tr >
                                <td class="col1" width="40%">e-mail</td>
                                <td class="col2"><input type="text" class="tbox" title="Email" value="<?=$r->EMail ?>" id="Email" name="EMail" <?php if (!empty($r->IDCliente) && !empty($r->EMail)){?> readonly <?php  }?>></td>
                            </tr>
                            <tr >
                                <td width="40%" class="col1"> Celular </td><td class="col2"><input type=text size=25 class="tbox"  title="Celular"  name=Celular id=Celular value="<?=$r->Celular ?>" <?php if (!empty($r->IDCliente) && !empty($r->Celular)){?> readonly <?php  }?>> </td>
                            </tr>

                            <tr >
                                <td width="40%" class="col1">Direcci&oacute;n</td><td class="col2"><input type=text size=25 class="tbox"   name=Direccion id=Direccion value="<?=$r->Direccion ?>" <?php if (!empty($r->IDCliente) && !empty($r->Direccion)){?> readonly <?php  }?>> </td>
                            </tr>
                            <tr >
                              <td class="col1">Departamento</td>
                              <?php
                              if (!empty($r->IDCliente) && !empty($r->IDDepartamento))
                                  $bloqueo="disabled";
                               else
                                  $bloqueo="";
                               ?>
                              <td class="col2"><? echo formpopup("Departamento","Nombre","Nombre","IDDepartamento",$r->IDDepartamento,"input\" id=\"IDDepartamento","",$bloqueo); ?></td>
                            </tr>
                            <tr >
                              <?php
                              if (!empty($r->IDCliente) && !empty($r->IDCiudad))
                                  $bloqueo="disabled";
                               else
                                  $bloqueo="";
                               ?>
                                <td width="40%" class="col1">Ciudad</td><td class="col2"><? echo formpopup("Ciudad","Descripcion","Descripcion","IDCiudad",$r->IDCiudad,"input\" id=\"IDCiudad","",$bloqueo); ?></td>
                            </tr>
                            <tr >
                              <td class="col1">Barrio</td>
                              <td class="col2"><input type=text size=25 class="tbox"   name=Barrio id=Barrio value="<?=$r->Barrio ?>" <?php if (!empty($r->IDCliente) && !empty($r->Direccion)){?> readonly <?php  }?>></td>
                            </tr>
                            <tr >
                              <?php
                              if (!empty($r->IDCliente) && !empty($r->IDEmpleado))
                                  $bloqueo="disabled";
                               else
                                  $bloqueo="";
                               ?>
                                <td width="40%" class="col1">Empleado</td><td class="col2"><? echo formpopup("Empleado WHERE IDPuntoVenta = '$IDPuntoVenta' ","Nombre","Apellidos","IDEmpleado",$r->IDEmpleado,"input\" id=\"Empleado","",$bloqueo); ?></td>
                            </tr>
                            <tr >
                              <td class="col1">Autorizo a recibir e-mail con promociones o informaci&oacute;n ( <span class="rojo">*</span> )</td>
                              <?php
                              if (!empty($r->IDCliente) && !empty($r->Genero))
                                  $bloqueo="disabled='disabled'";
                               else
                                $bloqueo="";
                               ?>
                              <td class="col2"><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AutorizaMail, 'AutorizaMail',$bloqueo); ?></td>
                            </tr>
                            <tr >
                              <td class="col1">Autorizo a recibir mensajes de texto (SMS) ( <span class="rojo">*</span> )</td>
                              <?php
                              if (!empty($r->IDCliente) && !empty($r->AceptaSMS))
                                  $bloqueo="disabled='disabled'";
                               else
                                $bloqueo="";
                               ?>
                              <td class="col2"><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AceptaSMS, 'AceptaSMS',$bloqueo); ?></td>
                            </tr>
                            <tr >
                              <td class="col1">Acepta t&eacute;rminos y condiciones( <span class="rojo">*</span> )</td>
                              <?php
                              if (!empty($r->IDCliente) && !empty($r->AceptaTerminos))
                                  $bloqueo="disabled='disabled'";
                               else
                                $bloqueo="";
                               ?>
                              <td class="col2"><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AceptaTerminos, 'AceptaTerminos',$bloqueo); ?></td>
                            </tr>
                            <tr >
                              <td class="col1">Acepta ley Habeas Data ( <span class="rojo">*</span> )</td>
                              <?php
                              if (!empty($r->IDCliente) && !empty($r->AceptaHabeas))
                                  $bloqueo="disabled='disabled'";
                               else
                                $bloqueo="";
                               ?>
                              <td class="col2"><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AceptaHabeas, 'AceptaHabeas',$bloqueo); ?></td>
                            </tr>
                            <tr >
                              <td class="col1">Numero de Tarjeta que se entrega</td>
                              <td class="col2">
                              <?php
							  /*
							  $id_tarjeta=$r->IDTarjetaFidelizacion;
							  $numero_tarjeta=get_field("TarjetaFidelizacion","Codigo","IDTarjetaFidelizacion",$id_tarjeta);
							  if (!empty($id_tarjeta)){
								$solo_lectura="readonly='readonly'";
							  }
							  */
							  $numero_tarjeta=$r->NumeroTarjeta;
							  ?>
                              <input type="text" size="25" class="tbox obligatorio" title="Numero de Tarjeta"   name="NumeroTarjeta" id="NumeroTarjeta" value="<?=$numero_tarjeta ?>" <?php echo $solo_lectura ?> />
                              </td>






                            </tr>


					  <?php
                      // consulto historial
                      $sql_historial_tarjetas="Select * From HistorialTarjetaFidelizacion Where IDCliente = '".$r->IDCliente."' ";
                      $qry_historial=db_query($sql_historial_tarjetas);
                      if (db_num_rows($qry_historial)>0){
                      ?>

                            <tr >
                              <td colspan="2" class="col2" align="left"><table border="0" align="center" cellpadding="1" cellspacing="2">
                                <tr>
                                  <td colspan="4"><strong>HISTORIAL DE TARJETAS ASIGNADAS</strong></td>
                                </tr>
                                <tr>
                                  <td><strong> Numero Tarjeta </strong></td>
                                  <td><strong> Motivo por la cual se cambio </strong></td>
                                  <td><strong> Fecha Cambio </strong></td>
                                  <td><strong> Punto de venta </strong></td>
                                </tr>
                                <?php while($row_historial=db_fetch_array($qry_historial)){ ?>
                                <tr>
                                  <td><?php echo $row_historial[Codigo]; ?></td>
                                  <td><?php echo $row_historial[Observacion]; ?></td>
                                  <td><?php echo $row_historial[FechaTrCr]; ?></td>
                                  <td><?php echo get_field( "PuntoVenta","Nombre","IDPuntoVenta",$row_historial[IDPuntoVenta] );; ?></td>
                                </tr>
                                <?php } ?>
                              </table></td>
                            </tr>

                      <?php } ?>

                            <?php if (!empty($id_tarjeta)){ ?>
                            <tr >
                              <td colspan="2" class="col2" align="left">
                              <a href="#" onclick="mostrar();">
                             	 Cambiar numero de tarjeta por perdida
                              </a>

                              <div id='oculto' style='display:none;'>

                              	<table width="100%" border="0">
                                	<tr>
                                   	  <td>
                                        	Nuevo N&uacute;mero de tarjeta:
                                      </td>
                                    	<td><input type="text" size="25" class="tbox" title="Numero de Tarjeta Nuevo"   name="NumeroTarjetaNuevo" id="NumeroTarjetaNuevo" value="" /></td>

                                    </tr>
                                	<tr>
                                   	  <td>
                                        	Motivo
                                      </td>
                                    	<td>
                                        <textarea name="Observacion" id="Observacion" rows="5" cols="30"></textarea>
                                        </td>
                                    </tr>

                                </table>

                              </div>
                              </td>
                            </tr>
                            <?php } ?>


                            <tr>
                                <td class="col2" colspan="2"><br>
                                    Por pertenecer al club de la suavidad , el d&iacute;a de tu cumplea&ntilde;os recibir&aacute;s el 15 % de descuento, solo necesitamos esta informaci&oacute;n:
                                    <br><br>
                                </td>
                            </tr>
                            <tr >
                                <td height="22" width="40%" class="col1">Fecha de Nacimiento</td>
                                <td class="col2">
                                <?php
                                  //if (empty($r->IDCliente) || (int)$r->Ano<=0 || (int)$r->Mes<=0  || (int)$r->Dia<=0     ){
                                  if (empty($r->IDCliente) && (int)$r->Ano==0 && (int)$r->Mes==0  && (int)$r->Dia==0     ){
                                  ?>
                                        <select name="Ano" id="A&ntilde;o de Nacimiento" class="tbox" >
                                          <option value="">A&ntilde;o</option>
                                          <?
                                              for($i = 1920; $i<2005; $i++)
                                              {
                                                $op = "<option value=\"$i\" ";
                                                if( $r->Ano == $i )
                                                    $op .= " selected ";
                                                $op .= ">".$i."</option>";
                                                 echo $op;
                                              }

                                          ?>

                                        </select>

                                    <select name="Mes" id="Mes de Nacimiento" class="tbox" >
                                        <option value="">Mes</option>
                                        <option value="1" <? if( $r->Mes == 1 ) echo " selected " ?>>Enero</option>
                                        <option value="2" <? if( $r->Mes == 2 ) echo " selected " ?>>Febrero</option>
                                        <option value="3" <? if( $r->Mes == 3 ) echo " selected " ?>>Marzo</option>
                                        <option value="4" <? if( $r->Mes == 4 ) echo " selected " ?>>Abril</option>
                                        <option value="5" <? if( $r->Mes == 5 ) echo " selected " ?>>Mayo</option>
                                        <option value="6" <? if( $r->Mes == 6 ) echo " selected " ?>>Junio</option>
                                        <option value="7" <? if( $r->Mes == 7 ) echo " selected " ?>>Julio</option>
                                        <option value="8" <? if( $r->Mes == 8 ) echo " selected " ?>>Agosto</option>
                                        <option value="9" <? if( $r->Mes == 9 ) echo " selected " ?>>Septiembre</option>
                                        <option value="10" <? if( $r->Mes == 10 ) echo " selected " ?>>Octubre</option>
                                        <option value="11" <? if( $r->Mes == 11 ) echo " selected " ?>>Noviembre</option>
                                        <option value="12" <? if( $r->Mes == 12 ) echo " selected " ?>>Diciembre</option>
                                      </select>

                                    <select name="Dia" id="Dia de Nacimiento" class="tbox" >
                                      <option value="" selected>Dia</option>
                                      <?
                                          for($i = 1; $i<=31; $i++)
                                          {
                                            $op = "<option value=\"$i\" ";
                                            if( $r->Dia == $i )
                                                $op .= " selected ";
                                            $op .= ">".$i."</option>";
                                             echo $op;
                                          }

                                      ?>
                                </select>

                                <?php }
								else{ ?>
									<input type="hidden" name="Ano" id="Ano" value="<?php echo $r->Ano ?>" />
                                    <input type="hidden" name="Mes" id="Ano" value="<?php echo $r->Mes ?>" />
                                    <input type="hidden" name="Dia" id="Ano" value="<?php echo $r->Dia ?>" />
                                    <?php
                                		echo $r->Ano."/".$r->Mes."/".$r->Dia;

								}


								?>





                                    </td>
                            </tr>

						<?
						}//end if si esta fidelizado

						//SI NO ESTA FIDELIZADO
						else //SI NO ESTA FIDELIZADO
						{
						?>
                            <tr >
                                <td width="40%" class="col1">Fidelizar Club de la Suavidad </td>
                                <td class=col2>

                                    <input type="radio" name="ClubSuavidad" class="radioClubSuavidad" value="S" >
                                    Si
                                    <input type="radio" name="ClubSuavidad" class="radioClubSuavidad"  value="N">
                                    No

                                </td>
                            </tr>
                            <tr >
                                <td colspan="2">
                                	<!-- CONTENIDO PARA FIDELIZAR O NO -->
                                    <div class="noFidelizar hide" >
                                    	<table width="100%" border=0 cellspacing=1 cellpadding=1 class="texto forumline" >
                                        	<tr>
                                                <td class="forumlink" colspan="2">
                                                    <span class="genmed">Campos Minimos Requeridos: Cedula, Nombre, Apellidos</span>
                                                </td>
                                            </tr>
                                            <tr >
                                                <td width="40%" class="col1">N&uacute;mero Documento ( <span class="rojo">*</span> )</td><td class=col2><input type=text size=25 class="tbox cedula_no_club" title="Cedula" value="<?=$cedula?>" name="cedula_no_club" id="cedula_no_club" ></td>
                                            </tr>


                                            <tr >
                                                <td class="col1" width="40%"> Nombre( <span class="rojo">*</span> )</td><td class="col2"><input type=text size=25 class="tbox nombre_no_club" title="Nombre"  name="nombre_no_club" id="nombre_no_club" <?=$readonly ?> value="<?=$r->Nombre ?>"> </td>
                                            </tr>
                                            <tr >
                                                <td width="40%" class="col1"> Apellidos ( <span class="rojo">*</span> )</td><td class="col2"><input type=text size=25 class="tbox apellido_no_club" title="Apellidos"  name="apellido_no_club" id="apellido_no_club" <?=$readonly ?>  value="<?=$r->Apellido ?>"> </td>
                                            </tr>
                                            <tr >
                                              <td class="col1">Telefono ( * )</td>
                                              <td class="col2"><input type=text size=25 class="tbox telefono_no_club" title="Telefono"  name="telefono_no_club" id="telefono_no_club" <?=$readonly ?>  value="<?=$r->Telefono ?>"></td>
                                            </tr>
                                            <tr >
                                                <td width="40%" class="col1">Empleado</td><td class="col2"><? echo formpopup("Empleado WHERE IDPuntoVenta = '$IDPuntoVenta' ","Nombre","Apellidos","IDEmpleado",$r->IDEmpleado,"input\" id=\"Empleado"); ?></td>
                                            </tr>
                                        </table>

                                    </div>
                                    <div class="siFidelizar hide" >
                                    	<table width="100%" border=0 cellspacing=1 cellpadding=1 class="texto forumline"  >


                                            <tr>
                                                <td class="forumlink" colspan="2">
                                                    <span class="genmed">Campos Minimos Requeridos: Cedula, Nombre, Apellidos, Telefono, Celular, Mail</span>
                                                </td>
                                            </tr>


                                            <tr >
                                                <td width="40%" class="col1">Numero de Documento ( <span class="rojo">*</span> )</td><td class=col2><input type=text title="Numero de Documento" size=25 class="tbox obligatorio cedula_guarda" value="<?=$cedula?>" name=Cedula id=Cedula > </td>
                                            </tr>

                                            <tr >
                                                <td class="col1" width="40%"> Nombre ( <span class="rojo">*</span> )</td><td class="col2"><input type=text size=25 class="tbox obligatorio" title="Nombre"  name=Nombre id=Nombre <?=$readonly ?> value="<?=$r->Nombre ?>"> </td>
                                            </tr>
                                            <tr >
                                                <td width="40%" class="col1"> Apellidos ( <span class="rojo">*</span> )</td><td class="col2"><input type=text size=25 class="tbox obligatorio" title="Apellido"  name=Apellido id=Apellidos <?=$readonly ?>  value="<?=$r->Apellido ?>"> </td>
                                            </tr>
                                            <tr >
                                                <td class="col1" width="40%">G&eacute;nero  ( <span class="rojo">*</span> )</td>
                                                <td class="col2"><? echo formradiogroup(array('Femenino'=>'F','Masculino'=>'M'),$r->Genero, 'Genero'); ?></td>
                                            </tr>
                                            <tr >
                                                <td width="40%" class="col1"> Telefono ( <span class="rojo">*</span> )</td><td class="col2"><input type=text size=25 class="tbox obligatorio" title="Telefono"  name=Telefono id=Telefono value="<?=$r->Telefono ?>"> </td>
                                            </tr>
                                            <tr >
                                                <td class="col1" width="40%">e-mail</td>
                                                <td class="col2"><input type="text" class="tbox" title="Email" value="<?=$r->EMail ?>" name="EMail" id="Email"></td>
                                            </tr>
                                            <tr >
                                                <td width="40%" class="col1"> Celular </td><td class="col2"><input type=text size=25 class="tbox"   name=Celular id=Celular title="Celular" value="<?=$r->Celular ?>"> </td>
                                            </tr>

                                            <tr >
                                                <td width="40%" class="col1">Direcci&oacute;n</td><td class="col2"><input type=text size=25 class="tbox" title="Direccion"   name=Direccion id=Direccion value="<?=$r->Direccion ?>"> </td>
                                            </tr>
                                            <tr >

                                            	<?
                                                $ciudad_cliente = $r->IDCiudad;
												if( empty( $ciudad_cliente ) )
													$ciudad_cliente = $ciudadpunto ;
												?>
                                                <td width="40%" class="col1">Ciudad</td><td class="col2"><? echo formpopup("Ciudad","Descripcion","Descripcion","IDCiudad",$ciudad_cliente,"input\" id=\"IDCiudad"); ?></td>
                                            </tr>
                                            <tr >
                                                <td width="40%" class="col1">Empleado</td><td class="col2"><? echo formpopup("Empleado WHERE IDPuntoVenta = '$IDPuntoVenta' ","Nombre","Apellidos","IDEmpleado",$r->IDIDEmpleado,"input\" id=\"IDEmpleadoFideliza"); ?></td>
                                            </tr>






                                            <tr >
                                                <td class="col1" width="40%">Autorizo a recibir e-mail con promociones o informaci&oacute;n ( <span class="rojo">*</span> )</td>
                                                <td class="col2"><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AutorizaMail, 'AutorizaMail'); ?></td>
                                            </tr>
                                            <tr >
                                                <td class="col1" width="40%">Autorizo a recibir mensajes de texto (SMS) ( <span class="rojo">*</span> )</td>
                                                <td class="col2"><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AceptaSMS, 'AceptaSMS'); ?></td>
                                            </tr>
                                          <tr >
                                              <td class="col1">Acepta t&eacute;rminos y condiciones( <span class="rojo">*</span> )</td>
                                              <td class="col2"><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AceptaTerminos, 'AceptaTerminos'); ?></td>
                                            </tr>
                                            <tr >
                                              <td class="col1">Acepta ley Habeas Data ( <span class="rojo">*</span> )</td>
                                              <td class="col2"><? echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AceptaHabeas, 'AceptaHabeas'); ?></td>
                                            </tr>
                                            <tr >
                                              <td class="col1">Numero de Tarjeta que se entrega( <span class="rojo">*</span> )</td>
                                              <td class="col2"><input type="text" size="25" class="tbox obligatorio" title="Numero de Tarjeta"   name="NumeroTarjeta" id="NumeroTarjeta" value="<?php echo $r->NumeroTarjeta ?>" /></td>
                                            </tr>
                                            <tr >
                                              <td class="col1">&nbsp;</td>
                                              <td class="col2">&nbsp;</td>
                                            </tr>


                                            <tr>
                                                <td class="col2" colspan="2"><br>
                                                    Por pertenecer al club de la suavidad , el d&iacute;a de tu cumplea&ntilde;os recibir&aacute;s el 15 % de descuento, solo necesitamos esta informaci&oacute;n:
                                                    <br><br>
                                                </td>
                                            </tr>
                                            <tr >
                                                <td height="22" width="40%" class="col1">Fecha de Nacimiento</td>
                                                <td class="col2">
                                        <select name="Ano" id="A&ntilde;o de Nacimiento" class="tbox" >
                                          <option value="">A&ntilde;o</option>
                                          <?
                                              for($i = 1920; $i<2005; $i++)
                                              {
                                                $op = "<option value=\"$i\" ";
                                                if( $r->Ano == $i )
                                                    $op .= " selected ";
                                                $op .= ">".$i."</option>";
                                                 echo $op;
                                              }

                                          ?>

                                        </select>

                                                    <select name="Mes" id="Mes de Nacimiento" class="tbox">
                                                        <option value="">Mes</option>
                                                        <option value="1" <? if( $r->Mes == 1 ) echo " selected " ?>>Enero</option>
                                                        <option value="2" <? if( $r->Mes == 2 ) echo " selected " ?>>Febrero</option>
                                                        <option value="3" <? if( $r->Mes == 3 ) echo " selected " ?>>Marzo</option>
                                                        <option value="4" <? if( $r->Mes == 4 ) echo " selected " ?>>Abril</option>
                                                        <option value="5" <? if( $r->Mes == 5 ) echo " selected " ?>>Mayo</option>
                                                        <option value="6" <? if( $r->Mes == 6 ) echo " selected " ?>>Junio</option>
                                                        <option value="7" <? if( $r->Mes == 7 ) echo " selected " ?>>Julio</option>
                                                        <option value="8" <? if( $r->Mes == 8 ) echo " selected " ?>>Agosto</option>
                                                        <option value="9" <? if( $r->Mes == 9 ) echo " selected " ?>>Septiembre</option>
                                                        <option value="10" <? if( $r->Mes == 10 ) echo " selected " ?>>Octubre</option>
                                                        <option value="11" <? if( $r->Mes == 11 ) echo " selected " ?>>Noviembre</option>
                                                        <option value="12" <? if( $r->Mes == 12 ) echo " selected " ?>>Diciembre</option>
                                                      </select>

                                                    <select name="Dia" id="Dia de Nacimiento" class="tbox">
                                                      <option value="" selected>Dia</option>
                                                      <?
                                                          for($i = 1; $i<=31; $i++)
                                                          {
                                                            $op = "<option value=\"$i\" ";
                                                            if( $r->Dia == $i )
                                                                $op .= " selected ";
                                                            $op .= ">".$i."</option>";
                                                             echo $op;
                                                          }

                                                      ?>
                                                </select>


                                                    </td>
                                            </tr>

                                        </table>
                                    </div>

                                </td>

                            </tr>
                        <?

						}//end elseif no fidelizado


						?>

			<tr>
			<td colspan=2 align=center class="col2list">
            	<input type=hidden name=IDCliente id=IDCliente value="<?=$r->IDCliente ?>">
                <input type=hidden name=IDTarjetaFidelizacion id=IDTarjetaFidelizacion value="<?=$r->IDTarjetaFidelizacion ?>">
                <input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
				<input type=hidden name=ID value="<? echo $r->IDCliente ?>">
                <input type=hidden name=Comentarios value="<? echo $r->Comentarios ?>">
				<input type=hidden name=action value=<?=$newmode?>>
				<input type=submit name=submit value="<? echo $submit_caption ?>" class=submit >

                <input type=hidden name=NombreAnt value="<? echo $r->Nombre ?>">
                <input type=hidden name=ApellidoAnt value="<? echo $r->Apellido ?>">
                <input type=hidden name=GeneroAnt value="<? echo $r->Genero ?>">
                <input type=hidden name=TelefonoAnt value="<? echo $r->Telefono ?>">
                <input type=hidden name=EMailAnt value="<? echo $r->EMail ?>">
                <input type=hidden name=CelularAnt value="<? echo $r->Celular ?>">
                <input type=hidden name=DireccionAnt value="<? echo $r->Direccion ?>">
                <input type=hidden name=IDCiudadAnt value="<? echo $r->IDCiudad ?>">
                <input type=hidden name=AutorizaMailAnt value="<? echo $r->AutorizaMail ?>">
                <input type=hidden name=AceptaSMSAnt value="<? echo $r->AceptaSMS ?>">
                <input type=hidden name=AceptaTerminosAnt value="<? echo $r->AceptaTerminos ?>">
                <input type=hidden name=AceptaHabeasAnt value="<? echo $r->AceptaHabeas ?>">
                <input type=hidden name=ClubSuavidadAnt value="<? echo $r->ClubSuavidad ?>">
                <input type=hidden name=IDUsuarioFideliza value="<? echo $r->IDUsuarioFideliza ?>">
                <input type=hidden name=IDPuntoVentaFideliza value="<? echo $r->IDPuntoVentaFideliza ?>">
                <input type=hidden name=NumeroTarjetaAnt value="<? echo $r->NumeroTarjeta ?>">
                <input type=hidden name=IDTarjetaFidelizacionAnt value="<? echo $r->IDTarjetaFidelizacion ?>">



			</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
	<?
}// End function print_formcliente()



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/

function print_form($id,$newmode,$title,$submit_caption,$frm=""){
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $IVA,$IDPuntoVenta,$AplicaPromoDescuentoSegundoPar;

	$IDPuntoVenta = SIMReg::get("IDPuntoVenta");


	$qid = db_query(" SELECT * FROM Cliente WHERE IDCliente = '$id' ");

	$r = db_fetch_object($qid);

	if($IDPuntoVenta=="31" || $IDPuntoVenta=="7"  || $IDPuntoVenta=="9"  || $IDPuntoVenta=="12" || $IDPuntoVenta=="23" || $IDPuntoVenta=="10")
		$AplicaPromoDescuentoSegundoPar=1;
?>

<script language="JavaScript">
<!--


function recalcular_valores_factura_con_bono(){
	var valor_total_factura = parseInt(document.frm.TotalFacturaNumero.value);
	var suma_bonos = parseInt(document.frm.SumaBonoNumero.value);
	var total_menos_bonos=valor_total_factura - suma_bonos;
	document.frm.ValorMenosBono.value=total_menos_bonos;
	var ValorTotalNoIva = total_menos_bonos / <?=$IVA+1?>;
	var ValorIvaMenosBono = total_menos_bonos - ValorTotalNoIva;
	var ValorTotalFactura = ValorTotalNoIva+ValorIvaMenosBono;
	document.frm.ValorTotalNoIva.value=ValorTotalNoIva;
	document.frm.ValorIvaMenosBono.value=ValorIvaMenosBono;
	// si el valor es negativo es por que el total es menor al valor del bono
	if (parseInt(ValorTotalFactura) < 0) {
		document.frm.ValorTotalFactura.value=0;
		//document.frm.SumaBono.value=valor_total_factura;
		document.frm.ValorMenosBono.value=0;
		//document.frm.elements["SumaBono"]=2;
		document.frm.SobranteBono.value=parseInt(ValorTotalFactura);
		document.frm.ValorIvaMenosBono.value=0;

	}
	else{
		document.frm.ValorTotalFactura.value=ValorTotalFactura;
		document.frm.SobranteBono.value=0;
	}


	formatCurrency(document.frm.elements["ValorMenosBono"]);
	formatCurrency(document.frm.elements["SumaBono"]);
	formatCurrency(document.frm.elements["ValorTotalNoIva"]);
	formatCurrency(document.frm.elements["ValorIvaMenosBono"]);
	formatCurrency(document.frm.elements["ValorTotalFactura"]);


}


function recalcular_valores_factura_con_alianza(descuento,tipo_producto,tipo_alianza){
	// borro todos los calculos de combos
				for(i=1;i<=document.frm.ITEM.value;i++){
						if(tipo_alianza=="Referido"){
							document.frm.elements["DescuentoLin"+i].value ="";
						}
						else{
							document.frm.elements["DescuentoLin"+i].value =document.frm.elements["PrimerDescuentoLin"+i].value;
						}

				}

			for(i=1;i<=document.frm.ITEM.value;i++){
					if( document.frm.elements["Precio"+i].value  != '' )
					{
						//es producto sin descuento (linea) o la alianza aplica para todas la referencias
						if(document.frm.elements["Descuento"+i].value  == 0 || tipo_producto == "T" ){
							if(descuento==""){ // si es 0 aplico el descuento primero si aplica
								document.frm.elements["DescuentoLin"+i].value = document.frm.elements["PrimerDescuentoLin"+i].value;
							}
							else{
								if (document.frm.elements["DescuentoLin"+i].value < descuento){
									document.frm.elements["DescuentoLin"+i].value = descuento;
								}
							}


						}
					}
				}
		recalcularvalores();

}



function addCell(label){
	var cell = document.createElement("TD");
	if(label)
		cell.innerHTML = label;
	return cell;
}
function addInput(size,type,name,value,keypress, blur,cont){
	var input =  document.createElement("INPUT");
	if(keypress==1)
		input.setAttribute("onKeyPress","if((event.keyCode < 48 || event.keyCode > 57) ) event.returnValue = false;");
	if(blur==1)
		input.setAttribute("onblur","CalculaMontoTotalIngreso(this);");
	if(keypress==2)
		input.setAttribute("onKeyPress","return KeyCheck(this,window.event.keyCode);");
	if(blur==2)
		input.setAttribute("onblur","formatCurrency(this);CalculaMontoTotalIngreso(this);");

	if(keypress==4){
		var URL = "'Referencia/popReferencias.php?IDPuntoVenta=<?=$IDPuntoVenta?>&cont="+cont+"&IDFactura=<?=$IDFactura?>'";

		var funcion = "window.open("+URL+",'','width=400,height=400');";

		input.setAttribute("onclick",funcion);
	}

	if(blur==5)
		input.setAttribute("onblur","if(!compruebamaximo(this.value, cont)) this.value = ''; else calculatotal(this.value,cont);");


	if(type == "text")
	{
		input.setAttribute("class","tbox");
		input.setAttribute("size",size);
		input.setAttribute("type",type);
		input.setAttribute("name",name);
		input.setAttribute("value",value);
		if(name != "Cantidad"+cont)
			input.setAttribute("readonly","true");

	}
	if(type == "button")
	{
		input.setAttribute("class","submit");
		input.setAttribute("type",type);
		input.setAttribute("name",name);
		input.setAttribute("value",value);
	}
	if(type == "hidden")
	{
		input.setAttribute("type",type);
		input.setAttribute("name",name);
		input.setAttribute("value",value);
	}

	return input;
}

var cont=1;

function addRow(){
cont ++;
var tbody = document.getElementById("table1").getElementsByTagName("tbody")[0];
var row = document.createElement("TR");

var cell1 = addCell("<b>" + cont + "</b>");
var cell2 = addCell("");
var cell3 = addCell("");
var cell4 = addCell("");
var cell5 = addCell("");
var cell6 = addCell("");
var cell7 = addCell("");
var cell8 = addCell("");
var cell9 = addCell("");
var cell10 = addCell("");
var cell11 = addCell("");
var cell12 = addCell("");

var inp1 = addInput(5,"text","Numero" + cont,"",0,0,cont);
cell2.appendChild(inp1);

var inp2 = addInput(5,"text","Talla" + cont,"",0,0,cont);
cell3.appendChild(inp2);

var inp3 = addInput(15,"text","Nombre" + cont,"",0,0,cont);
cell4.appendChild(inp3);

var inp4 = addInput(5,"hidden","IDCodificacion" + cont,"",0,0,cont);
cell5.appendChild(inp4);

var inp5 = addInput(5,"text","Cantidad" + cont,"",0,5,cont);
cell6.appendChild(inp5);

var inp6 = addInput(15,"text","ValorU" + cont,"",0,0,cont);
cell7.appendChild(inp6);

var inp7 = addInput(15,"text","Total" + cont,"",0,0,cont);
cell8.appendChild(inp7);

var inp8 = addInput(5,"button","Agregar" + cont,"Referencia",4,0,cont);
cell9.appendChild(inp8);

var inp9 = addInput(5,"hidden","Maximo" + cont,"",0,0,cont);
cell10.appendChild(inp9);

var inp10 = addInput(5,"hidden","Precio" + cont,"",0,0,cont);
cell11.appendChild(inp10);
var inp11 = addInput(5,"hidden","Descuento" + cont,"",0,0,cont);
cell12.appendChild(inp11);

row.appendChild(cell1);
row.appendChild(cell2);
row.appendChild(cell3);
row.appendChild(cell4);
row.appendChild(cell5);
row.appendChild(cell6);
row.appendChild(cell7);
row.appendChild(cell8);
row.appendChild(cell9);
row.appendChild(cell10);
row.appendChild(cell11);
row.appendChild(cell12);

tbody.appendChild(row);
}

function delRow(){
	var tbl = document.getElementById('table1');
	var lastRow = tbl.rows.length;
	if (lastRow > 2) {
		tbl.deleteRow(lastRow - 1);
		cont--;
	}
}

function selreferencia(REFERENCIA, NOMBRE, TALLA, CODIFICACION, CONT, MAXIMO, VALORU, DESCUENTOREF,VALORBRUTO){

	var items_total=0;
	Borrar(CONT);

	document.frm.elements["ValorBruto"+CONT].value = VALORBRUTO;
	document.frm.elements["Numero"+CONT].value = REFERENCIA;
	document.frm.elements["Nombre"+CONT].value = NOMBRE;
	document.frm.elements["Talla"+CONT].value = TALLA;
	document.frm.elements["IDCodificacion"+CONT].value = CODIFICACION;

	// SI esta en la semana de cumpleaños aplico el descuento actual por este motivo
	if (document.frm.elements["DescuentoCumple"].value ==1 && DESCUENTOREF<=0 && document.frm.elements["Numero"+CONT].value!="TARJETA"){
		document.frm.elements["DescuentoLin"+CONT].value = "<?php echo (int)get_field("ParametroFidelizacion","Valor","IDParametroFidelizacion","10")?>";
		document.frm.elements["PrimerDescuentoLin"+CONT].value = document.frm.elements["DescuentoLin"+CONT].value
		document.frm.elements["ObservacionDescuento"].value="Se aplica descuento por estar en semana de cumpleanos";
	}

	// SI pertenece a una alianza y el descuento de la alianza es menor que otros descuentos
	if (document.frm.elements["IDAlianza"].value !="" && document.frm.elements["DescuentoLin"+CONT].value < document.frm.elements["DescuentoAlianza"].value && (DESCUENTOREF<=0 || document.frm.elements["TipoProductoAlianza"].value == "T" )){
		var IDAlianzaSelecc = document.frm.elements["IDAlianza"].value;
		var TipoAlianza = document.frm.elements["TipoAlianza"].value;
		var combo = document.getElementById("IDAlianza");
		var selected = combo.options[combo.selectedIndex].text;
		document.frm.elements["DescuentoLin"+CONT].value = document.frm.elements["DescuentoAlianza"].value;

	}

	/*******Si la factura tiene descuento especial se hace la operacion**************/
	var descuento = document.frm.Descuento.value;
	var PRECIO = 0;
	var iva = 1 + (<?=$IVA?>*1);
	//alert( iva );
	document.frm.elements["Precio"+CONT].value = VALORU;
	document.frm.elements["Descuento"+CONT].value = DESCUENTOREF;

	if( descuento > 0)
	{
		//alert( int( VALORU ) + int( ( VALORU * ( descuento / 100 ) ) ) );
		VALORU = parseInt( VALORU ) + parseInt( ( VALORU * ( descuento / 100 ) )  );

	}
	/****Fin Si la factura tiene descuento especial se hace la operacion************/


	VALORU = VALORU / iva ;

	document.frm.elements["ValorU"+CONT].value = VALORU;
	formatCurrency(document.frm.elements["ValorU"+CONT]);

	document.frm.elements["Maximo"+CONT].value = MAXIMO;


	//agregado para las tarjetas
	if( REFERENCIA === "TARJETA" )
	{
		document.frm.elements["ValorU"+CONT].readOnly = false;
		//document.frm.elements["CodigoTarjeta"+CONT].style.display = "block";
		document.frm.elements["CodigoTarjeta"+CONT].style.display = "none";
		document.frm.elements["CodigoTarjetaDigitado"+CONT].style.display = "block";
	}//end if
	else
	{
		document.frm.elements["ValorU"+CONT].readOnly = true;
		document.frm.elements["CodigoTarjeta"+CONT].style.display = "none";
	}//end else

	//recalculo el valor del total de items de la factutra
	for(i=1;i<=10;i++){
		if(document.frm.elements["Numero"+i].value!=""){
			items_total = items_total + 1;
		}
	}

	document.frm.elements["ITEM"].value = items_total;



}


function setcodigotarjeta(CODIGO,CODIGO2,CODIGO3,CODIGO4,CONT){

	for(i=1;i<=document.frm.ITEM.value;i++){
		var codigo = document.frm.elements["CodigoTarjeta"+i].value;
		if (codigo!=""){
			switch(i){
				case 1:
					CODIGO=CODIGO2;
				break;
				case 2:
					CODIGO=CODIGO3;
				break;
				case 3:
					CODIGO=CODIGO4;
				break;
			}
		}
	}

	document.frm.elements["CodigoTarjeta"+CONT].value = CODIGO;


}


function setvalor(valor, i)
{

	var tarjeta=document.frm.elements["CodigoTarjeta"+i].value;
	var descuentolin=document.frm.elements["DescuentoLin"+i].value;
	if(tarjeta!=""){

			var VALORU = valor;

			/*******Si la factura tiene descuento especial se hace la operacion**************/
			var descuento = document.frm.Descuento.value;
			var PRECIO = 0;
			var iva = 1 + (<?=$IVA?>*1);
			//alert( iva );
			document.frm.elements["Precio"+i].value = VALORU;

			if( descuento > 0)
			{
				//alert( int( VALORU ) + int( ( VALORU * ( descuento / 100 ) ) ) );
				VALORU = parseInt( VALORU ) + parseInt( ( VALORU * ( descuento / 100 ) )  );

			}
			/****Fin Si la factura tiene descuento especial se hace la operacion************/



			VALORU = VALORU / iva ;

			document.frm.elements["ValorU"+i].value = VALORU;
			formatCurrency(document.frm.elements["ValorU"+i]);

			recalcularvalores();
	}


}//end funciton

function selcliente(IDCLIENTE, CEDULA, NOMBRE, TELEFONO){

	document.frm.elements["IDCliente"].value = IDCLIENTE;
	document.frm.elements["Cedula"].value = CEDULA;
	document.frm.elements["NombreCliente"].value = NOMBRE;
	document.frm.elements["TeleCli"].value = TELEFONO;

}

function selempleado(IDEMPLEADO, CEDULA, NOMBRE){
	document.frm.elements["IDEmpleado"].value = IDEMPLEADO;
	document.frm.elements["CedulaEmpleado"].value = CEDULA;
	document.frm.elements["NombreEmpleado"].value = NOMBRE;
}

function compruebamaximo(value, cont)
{

	var maximo = document.frm.elements["Maximo"+cont].value;
	//alert(value);
	//alert(maximo);
	if( eval(value) > eval(maximo) )
	{
		alert("El maximo es " + maximo);
		return false;
	}
	else
	{

		return true;
	}
}

function getNum(strNum)

{

	num = strNum.toString().replace(/\$|\,/g,'');
	if(isNaN(num))
		num = "0";
	return num;

}

function formatCurrency(InpunObject)
{

	num = InpunObject.value;
	num = num.toString().replace(/\$|\,/g,'');
	if(isNaN(num))
		num = "0";
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();
	if(cents<10)
	cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0,num.length-(4*i+3))+','+

	num.substring(num.length-(4*i+3));

	InpunObject.value = (((sign)?'':'-') + '$' + num + '.' + cents);

}


function verifica_aplica_alianza_referido(){
	var total_factura = 0;
	var total_item = 0;
	var TipoAlianza = document.frm.elements["TipoAlianza"].value;
	if(TipoAlianza=="Referido"){
		for(i=1;i<= document.frm.ITEM.value;i++){
			if(document.frm.elements["ValorU"+i].value  != '' && document.frm.elements["Cantidad"+i].value != ''){
				total_item = getNum(document.frm.elements["ValorU"+i].value) * getNum(document.frm.elements["Cantidad"+i].value);
				total_factura =  parseInt(total_factura) + parseInt(total_item);
				//total_factura =  document.frm.elements["ValorU"+i].value;
			}
		}
		if(parseInt(total_factura)<=100000){
			for(i=1;i<=document.frm.ITEM.value;i++){
				if(document.frm.elements["ValorU"+i].value  != '' && document.frm.elements["Cantidad"+i].value != ''){
						document.frm.elements["DescuentoLin"+i].value="";
				}
			}
		}
		else{
			for(i=1;i<=document.frm.ITEM.value;i++){
				if(document.frm.elements["ValorU"+i].value  != '' && document.frm.elements["Cantidad"+i].value != ''){
						document.frm.elements["DescuentoLin"+i].value=10;
				}
			}

		}
	}

	if(TipoAlianza=="Referente"){
		//Aplico el descuento solo para el de mayor valor
		var valor_mayor  = 0;
		var total_item = 0;
		var item_mayor = 0;
		var descuento_alianza = document.frm.elements["ValorAlianza"].value;
		for(i=1;i<= document.frm.ITEM.value;i++){
			if(document.frm.elements["ValorU"+i].value  != '' && document.frm.elements["Cantidad"+i].value != ''){
				total_item = getNum(document.frm.elements["ValorU"+i].value) * getNum(document.frm.elements["Cantidad"+i].value);
				if(total_item>valor_mayor){
					valor_mayor = total_item;
					item_mayor = i;
				}
			}
		}

		for(i=1;i<=document.frm.ITEM.value;i++){
				if(parseInt(item_mayor)  == parseInt(i) ){
					document.frm.elements["DescuentoLin"+i].value=descuento_alianza;
				}
				else{
					document.frm.elements["DescuentoLin"+i].value="";
				}
		}
	}


}


function calculatotal(value, cont)
{
		//alert("este");
		var TotalSinIva = 0;
		var Iva = 0;
		TotalFactura = 0;
		var PrecioIva = 0;
		var Precio = 0;
		var PrecioDescuento = 0;
		var DescuentoLin = 0;
		var valorui = 0;
		var precioi = 0;

		//Verifico el total de la factura para descuento de alianza de referido
		verifica_aplica_alianza_referido();

		for(i=1;i<= document.frm.ITEM.value;i++){

			if(document.frm.elements["ValorU"+i].value  != '' && document.frm.elements["Cantidad"+i].value != '')
			{


				if( document.frm.elements["DescuentoLin"+i].value != '' )
				{

					//En la promo ddel 50% segundo par se aplica al valor bruto
					if(document.frm.elements["DescuentoSegundoPar"].value==1 && document.frm.elements["ObservacionDescuento"].value=="segundo par 50%"){
							valor_descuento = getNum(document.frm.elements["ValorBruto"+i].value ) * ( 1 - ( getNum( document.frm.elements["DescuentoLin"+i].value ) / 100 ) );
							valorui = valor_descuento / 1.16;
					}
					else{
							valorui = getNum(document.frm.elements["ValorU"+i].value ) * ( 1 - ( getNum( document.frm.elements["DescuentoLin"+i].value ) / 100 ) );
					}
					precioi = getNum(document.frm.elements["Precio"+i].value ) * ( 1 - ( getNum( document.frm.elements["DescuentoLin"+i].value ) / 100 ) );
				}//end if
				else
				{
					valorui = getNum(document.frm.elements["ValorU"+i].value);
					precioi = getNum(document.frm.elements["Precio"+i].value);
				}//end if

				var total = getNum(document.frm.elements["Cantidad"+i].value) * valorui;
				document.frm.elements["Total"+i].value = total;
				formatCurrency(document.frm.elements["Total"+i]);

				TotalSinIva = TotalSinIva + total;

				if(document.frm.Descuento.value > 0 )
				{
					PrecioDescuento = parseInt(precioi) + parseInt( ( precioi * ( document.frm.Descuento.value / 100 ) ) );
					PrecioIva =  PrecioDescuento - ( PrecioDescuento  / (1+<?=$IVA?>) )  ;
					Iva = Iva + ( ( PrecioIva ) * (getNum(document.frm.elements["Cantidad"+i].value)*1) );
				}
				else
					Iva = Iva + ( ((precioi*1) - ( valorui *1 ) ) * (getNum(document.frm.elements["Cantidad"+i].value)*1) );

			}
			else
			{
				document.frm.elements["Total"+i].value = "";
			}

		}


		TotalFactura = TotalSinIva + Iva;

		document.frm.elements["TotalSinIVA"].value = TotalSinIva;
		formatCurrency(document.frm.elements["TotalSinIVA"]);

		document.frm.elements["ValorIVA"].value = Iva;
		formatCurrency(document.frm.elements["ValorIVA"]);


		document.frm.elements["TotalFacturaNumero"].value = TotalFactura;
		document.frm.elements["ValorTotal"].value = TotalFactura;
		formatCurrency(document.frm.elements["ValorTotal"]);

	recalcular_valores_factura_con_bono();


}


	/*var totalsiniva = (getNum(document.frm.elements["TotalSinIVA"].value)*1) + (getNum(total)*1);
	document.frm.elements["TotalSinIVA"].value = totalsiniva;

	var iva = ((getNum(document.frm.elements["Precio"+cont].value)*1) - (getNum(document.frm.elements["ValorU"+cont].value)*1)) * (getNum(document.frm.elements["Cantidad"+cont].value)*1);
	document.frm.elements["ValorIVA"].value = getNum(document.frm.elements["ValorIVA"].value) + getNum(iva);

	totalfactura = (getNum(document.frm.elements["ValorIVA"].value)*1) + (getNum(document.frm.elements["TotalSinIVA"].value)*1) + (getNum(document.frm.elements["ValorTotal"].value)*1);
	document.frm.elements["ValorTotal"].value = totalfactura;

}*/


// Funcion para promocion pague dos lleve 3
function pague_2_lleve_3()
{

	/*
	var i = 0;
	var contador=0;
	var cantidad_item=0;
	var item_con_varias_cantidades=0;
	var total_items_descuento=0;
	var total_productos_descuento=0;
	var array_item=new Array();
	var array_productos_descuento=new Array();
	var precio_menor=0;
	var item_menor="";
	var precio_actual=0;
	var item_actual=0;

	var precio_actual_2=0;
	var item_actual_2=0;
	var precio_menor_2=0;
	var item_menor_2="";

	var precio_actual_3=0;
	var item_actual_3=0;
	var precio_menor_3=0;
	var item_menor_3="";

	// borro todos los calculos de combos
	for(i=1;i<=document.frm.ITEM.value;i++){
			if(document.frm.elements["ObservacionDescuento"].value=="pague 2 lleva 3"){
				document.frm.elements["DescuentoLin"+i].value="";
				document.frm.elements["PrimerDescuentoLin"+i].value="";
				document.frm.elements["DescuentoLin"+i].style.background="#FFFFFF";
				document.frm.elements["ObservacionDescuento"].value="";
			}
	}


	for(i=1;i<=document.frm.ITEM.value;i++){
		if( document.frm.elements["Precio"+i].value  != '' )
		{
			//es producto con descuento
			if( document.frm.elements["Descuento"+i].value  != 0 ){
				cantidad_item=parseInt(document.frm.elements["Cantidad"+i].value);

				// si algun producto tiene mas de dos cantidades
				if(cantidad_item>=2){
					item_con_varias_cantidades=1;
				}

				total_items_descuento=total_items_descuento+1;
				total_productos_descuento=total_productos_descuento+(cantidad_item*1);
				precio=parseInt(document.frm.elements["Precio"+i].value);
				array_item=[i,precio,cantidad_item];
				array_productos_descuento.push(array_item);

			}
		}
	}

			//valor_descuento=document.frm.elements["DescuentoLin"+i].value;
			//alert(total_productos_descuento);
			if(total_productos_descuento>=3){

				//Con 1 combo
					for (contador=0;contador<=(total_items_descuento-1);contador++){
						precio_actual=array_productos_descuento[contador][1];
						item_actual=array_productos_descuento[contador][0];
						if (precio_menor==0 || precio_actual < precio_menor){
								precio_menor=precio_actual;
								item_menor=item_actual;
						}
					}

				//alert(precio_menor + 'item ' + item_menor);

				//Con 2 combo calculo el segundo precio mas barato
				if(total_productos_descuento>=6){
					for (contador=0;contador<=(total_items_descuento-1);contador++){
						if(contador != (item_menor-1)){ // verifico todos los valores menos el del primer combo
							precio_actual_2=array_productos_descuento[contador][1];
							item_actual_2=array_productos_descuento[contador][0];
							if (precio_menor_2==0 || precio_actual_2 < precio_menor_2){
									precio_menor_2=precio_actual_2;
									item_menor_2=item_actual_2;
							}
						}
					}
				}


				if(total_productos_descuento==9){
					for (contador=0;contador<=(total_items_descuento-1);contador++){
						if(contador != (item_menor-1) && contador != (item_menor_2-1)){ // verifico todos los valores menos el del primer combo
							precio_actual_3=array_productos_descuento[contador][1];
							item_actual_3=array_productos_descuento[contador][0];
							if (precio_menor_3==0 || precio_actual_3 < precio_menor_3){
									precio_menor_3=precio_actual_3;
									item_menor_3=item_actual_3;
							}
						}
					}
				}


				//alert ("El segundo mas barato es " + precio_menor_2 + " del item " + item_menor_2);

			}


			if (item_menor!=""){
				cantidad_item=document.frm.elements["Cantidad"+item_menor].value;
				precio_u=document.frm.elements["Precio"+item_menor].value;
				precio_total_item=precio_u*cantidad_item;
				porcentaje_descuento=precio_u*100/precio_total_item;
				document.frm.elements["DescuentoLin"+item_menor].value=porcentaje_descuento;
				document.frm.elements["DescuentoLin"+item_menor].style.background="#CCFFCC";
				document.frm.elements["ObservacionDescuento"].value="pague 2 lleva 3";
			}

			if (item_menor_2!=""){
				cantidad_item_2=document.frm.elements["Cantidad"+item_menor_2].value;
				precio_u_2=document.frm.elements["Precio"+item_menor_2].value;
				precio_total_item_2=precio_u_2*cantidad_item_2;
				porcentaje_descuento_2=precio_u_2*100/precio_total_item_2;
				document.frm.elements["DescuentoLin"+item_menor_2].value=porcentaje_descuento_2;
				document.frm.elements["DescuentoLin"+item_menor_2].style.background="#CCFFCC";
				document.frm.elements["ObservacionDescuento"].value="pague 2 lleva 3";
			}

			if (item_menor_3!=""){
				cantidad_item_3=document.frm.elements["Cantidad"+item_menor_3].value;
				precio_u_3=document.frm.elements["Precio"+item_menor_3].value;
				precio_total_item_3=precio_u*cantidad_item_3;
				porcentaje_descuento_3=precio_u_3*100/precio_total_item_3;
				document.frm.elements["DescuentoLin"+item_menor_3].value=porcentaje_descuento_3;
				document.frm.elements["DescuentoLin"+item_menor_3].style.background="#CCFFCC";
				document.frm.elements["ObservacionDescuento"].value="pague 2 lleva 3";
			}
			*/

}


function promo_segundo_par(){

	// si para el punto de venta tiene activa la opcion de aplicar promocion "Segundo par 50%" (validacion en login.php)
	if(document.frm.elements["DescuentoSegundoPar"].value==1){
				var i = 0;
				var contador=0;
				var cantidad_item=0;
				var item_con_varias_cantidades=0;
				var total_items_descuento=0;
				var total_productos_descuento=0;
				var array_item=new Array();
				var array_productos_descuento=new Array();
				var precio_menor=0;
				var item_menor="";
				var precio_actual=0;
				var item_actual=0;

				var precio_actual_2=0;
				var item_actual_2=0;
				var precio_menor_2=0;
				var item_menor_2="";

				var precio_actual_3=0;
				var item_actual_3=0;
				var precio_menor_3=0;
				var item_menor_3="";

				// borro todos los calculos de combos
				for(i=1;i<=document.frm.ITEM.value;i++){
						if(document.frm.elements["ObservacionDescuento"].value=="segundo par 100%"){
							document.frm.elements["DescuentoLin"+i].value="";
							document.frm.elements["PrimerDescuentoLin"+i].value="";
							document.frm.elements["DescuentoLin"+i].style.background="#FFFFFF";
							document.frm.elements["ObservacionDescuento"].value="";
						}
				}


				for(i=1;i<=document.frm.ITEM.value;i++){
					if( document.frm.elements["Precio"+i].value  != '' )
					{

						//es producto con descuento

						if( document.frm.elements["Descuento"+i].value  > 0 && document.frm.elements["Talla"+i].value=="35"){

							cantidad_item=parseInt(document.frm.elements["Cantidad"+i].value);

							// si algun producto tiene mas de dos cantidades
							if(cantidad_item>=2){
								item_con_varias_cantidades=1;
							}

							total_items_descuento=total_items_descuento+1;
							total_productos_descuento=total_productos_descuento+(cantidad_item*1);
							precio=parseInt(document.frm.elements["Precio"+i].value);
							array_item=[i,precio,cantidad_item];
							array_productos_descuento.push(array_item);

						}
					}
				}


						//valor_descuento=document.frm.elements["DescuentoLin"+i].value;
						//alert(total_productos_descuento);
						if(total_productos_descuento>=2){

							//Con 1 combo
								for (contador=0;contador<=(total_items_descuento-1);contador++){
									precio_actual=array_productos_descuento[contador][1];
									item_actual=array_productos_descuento[contador][0];
									if (precio_menor==0 || precio_actual < precio_menor){
											precio_menor=precio_actual;
											item_menor=item_actual;
									}
								}

							//alert(precio_menor + 'item ' + item_menor);

							//Con 2 combo calculo el segundo precio mas barato
							if(total_productos_descuento>=4){
								for (contador=0;contador<=(total_items_descuento-1);contador++){
									if(contador != (item_menor-1)){ // verifico todos los valores menos el del primer combo
										precio_actual_2=array_productos_descuento[contador][1];
										item_actual_2=array_productos_descuento[contador][0];
										if (precio_menor_2==0 || precio_actual_2 < precio_menor_2){
												precio_menor_2=precio_actual_2;
												item_menor_2=item_actual_2;
										}
									}
								}
							}


							if(total_productos_descuento==6){
								for (contador=0;contador<=(total_items_descuento-1);contador++){
									if(contador != (item_menor-1) && contador != (item_menor_2-1)){ // verifico todos los valores menos el del primer combo
										precio_actual_3=array_productos_descuento[contador][1];
										item_actual_3=array_productos_descuento[contador][0];
										if (precio_menor_3==0 || precio_actual_3 < precio_menor_3){
												precio_menor_3=precio_actual_3;
												item_menor_3=item_actual_3;
										}
									}
								}
							}


							//alert ("El segundo mas barato es " + precio_menor_2 + " del item " + item_menor_2);

						}


						if (item_menor!=""){
							cantidad_item=document.frm.elements["Cantidad"+item_menor].value;
							precio_u=document.frm.elements["Precio"+item_menor].value;
							precio_total_item=precio_u*cantidad_item;
							porcentaje_descuento=precio_u*100/precio_total_item;
							document.frm.elements["DescuentoLin"+item_menor].value=porcentaje_descuento;
							document.frm.elements["DescuentoLin"+item_menor].style.background="#CCFFCC";
							document.frm.elements["ObservacionDescuento"].value="segundo par 100%";
						}

						if (item_menor_2!=""){
							cantidad_item_2=document.frm.elements["Cantidad"+item_menor_2].value;
							precio_u_2=document.frm.elements["Precio"+item_menor_2].value;
							precio_total_item_2=precio_u_2*cantidad_item_2;
							porcentaje_descuento_2=precio_u_2*100/precio_total_item_2;
							document.frm.elements["DescuentoLin"+item_menor_2].value=porcentaje_descuento_2;
							document.frm.elements["DescuentoLin"+item_menor_2].style.background="#CCFFCC";
							document.frm.elements["ObservacionDescuento"].value="segundo par 100%";
						}

						if (item_menor_3!=""){
							cantidad_item_3=document.frm.elements["Cantidad"+item_menor_3].value;
							precio_u_3=document.frm.elements["Precio"+item_menor_3].value;
							precio_total_item_3=precio_u*cantidad_item_3;
							porcentaje_descuento_3=precio_u_3*100/precio_total_item_3;
							document.frm.elements["DescuentoLin"+item_menor_3].value=porcentaje_descuento_3;
							document.frm.elements["DescuentoLin"+item_menor_3].style.background="#CCFFCC";
							document.frm.elements["ObservacionDescuento"].value="segundo par 100%";
						}

	}

}



function recalcularvalores()
{

	var i = 0;
	var actualizados = 0;
	for(i=1;i<= document.frm.ITEM.value;i++){

		if( document.frm.elements["Precio"+i].value  != '' )
		{

			document.frm.elements["ValorU"+i].value = parseInt( document.frm.elements["Precio"+i].value ) + parseInt( ( document.frm.elements["Precio"+i].value * ( document.frm.Descuento.value / 100 ) ) )  ;

			document.frm.elements["ValorU"+i].value = document.frm.elements["ValorU"+i].value / ( 1 + <?=$IVA?> );
			formatCurrency(document.frm.elements["ValorU"+i]);

			calculatotal( document.frm.elements["ValorU"+i].value , i );

			document.frm.elements["ValorTotal"].value = (getNum( document.frm.elements["TotalSinIVA"].value )*1) + (getNum( document.frm.elements["ValorIVA"].value)*1 );

			formatCurrency(document.frm.elements["ValorTotal"]);

			actualizados = 1;
		}
	}

	if( actualizados == 0 )
	{
		calculatotal( document.frm.elements["ValorU1"].value , 1 );
	}//end fi


}


function Borrar( contador )
{
	document.frm.elements["Numero"+contador].value = "";
	document.frm.elements["Talla"+contador].value = "";
	document.frm.elements["Nombre"+contador].value = "";
	document.frm.elements["IDCodificacion"+contador].value = "";
	document.frm.elements["Cantidad"+contador].value = "";
	document.frm.elements["ValorU"+contador].value = "";
	document.frm.elements["Total"+contador].value = "";
	document.frm.elements["Maximo"+contador].value = "";
	document.frm.elements["Precio"+contador].value = "";
	document.frm.elements["ValorBruto"+contador].value = "";
	document.frm.elements["Descuento"+contador].value = "";
	document.frm.elements["DescuentoLin"+contador].value = "";
	document.frm.elements["PrimerDescuentoLin"+contador].value = "";

	if (document.frm.elements["DescuentoCumple"].value==1){
		document.frm.elements["ObservacionDescuento"].value="";
	}


	pague_2_lleve_3();
	promo_segundo_par();
	recalcularvalores();

}//end function

	-->
</script>
<script>
var Check = new Array('NumeroFactura','NumeroDocumento','IDPuntoVenta','IDCliente','IDEmpleado', 'Cantidad1', 'Nombre1', 'ValorTotal');
</script>
<br>
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="100%">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				<?=$title?>
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>





<?php
$habilita_descuento="N";
//reviso si estab habilitado "relaciones publicas" para habilitar el descuento
$sql_link = "Select * From LinkCambio Where RelacionesPublicas = 'S' Limit 1";
$result_link = db_query($sql_link);
$row_link = db_fetch_array($result_link);
//Reviso si el link esta habilitado para este punto de venta
$sql_link_pto = "Select * From PuntoVentaLink Where IDPuntoVenta = '".$IDPuntoVenta."' and IDLinkCambio = '".$row_link["IDLinkCambio"]."' Limit 1";
$result_link_pto = db_query($sql_link_pto);
$total_link_pto = db_num_rows($result_link_pto);


if((int)$total_link_pto>0)
	$habilita_descuento="S";
?>



<FORM name="frm" id="frmfactura" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg2(this,Check);disable(this);"<?}?>>
<table class="forumline" width="100%" cellspacing="1" border="0" align="center">
	<tr>
	<td width="100%">
		<table width="100%" border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff" >

				<tr >
					<td colspan="2" width="100%">

								<div align="center">
									<table width="100%" border=0 align="center">
									<tr>
											<td colspan="4">
												<table class=rowtable width="100%">
													<tr>
														<td class=col1 >No. Factura</td>
														<td class=col2 colspan="3" >
                                                        <?php

														   $sql_facturas = "Select IDFactura From Factura WHERE IDPuntoVenta = '$IDPuntoVenta' and FechaFactura >='2019-07-20 00:00:00' Limit 1";
														   $qry_facturas = db_query($sql_facturas);
														   $row_facturas = db_fetch_array($qry_facturas);
														   $ultima_fac = (int)$row_facturas["IDFactura"];
														   if($ultima_fac==0):
															   	$consecutivosig=5000;
														   else:
															   	$consecutivosig=get_maxID("Factura WHERE IDPuntoVenta = '$IDPuntoVenta' and FechaFactura >='2019-07-20 00:00:00'","NumeroFactura");
														   endif;


														/*
														//Para salitre se hizo un cambio de repetir numeros con otra inicial
														if($IDPuntoVenta==17):
														   $consecutivosig=1554;
														   $sql_max = "Select max(NumeroFactura) MaximoConsecutivo From Factura WHERE IDPuntoVenta = '$IDPuntoVenta' and NumeroFactura <=7000";
														   $qry_max = db_query($sql_max);
														   $row_max = db_fetch_array($qry_max);
														   $consecutivosig = (int)$row_max["MaximoConsecutivo"]+1;
														 elseif($IDPuntoVenta==34):
														   $sql_max = "Select max(NumeroFactura) MaximoConsecutivo From Factura WHERE IDPuntoVenta = '$IDPuntoVenta' and NumeroFactura <=5000";
														   $qry_max = db_query($sql_max);
														   $row_max = db_fetch_array($qry_max);
														   $consecutivosig = (int)$row_max["MaximoConsecutivo"]+1;
														else:
															//A partir del 2 de nov de 2018 todas las facturas arrancan en 1
														   $consecutivosig=get_maxID("Factura WHERE IDPuntoVenta = '$IDPuntoVenta'","NumeroFactura");
														endif;
														*/


														?>
                                                        <input type="hidden" class="tbox" name="NumeroFactura" id="Numero Factura" size="24" value="<?=$consecutivosig; ?>">
                                                        <?=$consecutivosig; ?>
                                                        </td>
													</tr>
													<tr>
														<td class=col1>Fecha Factura</td>
														<td class=col2 colspan="3"><input type="text" class="tbox" name="FechaFactura" size="19" value='<?=fecha()." ".hora()?>' readonly>
															<script language="JavaScript1.2">
															<!--
																if (!document.layers)
																	document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaFactura,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
															//-->
														</script>
															<input type="hidden" value="<?=$IDPuntoVenta?>" id="IDPuntoVenta" name="IDPuntoVenta"></td>
													</tr>
                          <!--
													<tr>
														<td class=col1>Observaciones</td>
														<td class=col2 colspan="3"><textarea class="tareabox" name="Observaciones" rows="4" cols="64"><?=$frm[Observaciones]?></textarea></td>
													</tr>
                        -->
													<tr>
														<td class=col1>Vendedor</td>
														<td class=col2><? echo formpopup("Empleado WHERE IDPuntoVenta = '$IDPuntoVenta' ","Nombre","Apellidos","IDEmpleado",$frm[IDEmpleado],"input\" id=\"Empleado"); ?></td>
														<td class=col1 colspan="2"></td>
													</tr>
													<tr>
														<td class=col1>														</td>
														<td class=col1></td>
														<td class=col1></td>
														<td class=col1></td>
													</tr>
													<tr>
														<td class=row1 colspan="4"><b>CLIENTE</b></td>
													</tr>
													<tr>
														<td class=col1>Numero Documento</td>
														<td class=col2><input type="text" class="tbox" name="Cedula" readonly size="15" value='<?echo $r->Cedula;?>'><input type="hidden" name="IDCliente" id="Cliente" value="<?=$r->IDCliente?>"></td>
														<td class=col1>Nombre</td>
														<td class=col2><input type="text" class="tbox" name="NombreCliente" readonly size="20" value='<?echo $r->Nombre." ".$r->Apellido?>'></td>
													</tr>
													<tr>
														<td class=col1 nowrap>Telefono Cliente</td>
														<td class=col2><input type="text" class="tbox" name="TeleCli" readonly size="15" value='<?echo $r->Telefono?>'></td>
														<td class="col1" nowrap="nowrap">Numero de Fidelizacion</td>
														<td class="col2"><input name="NumeroTarjeta" type="text" class="tbox" id="NumeroTarjeta" value='<?echo $r->NumeroTarjeta?>' size="20" readonly /></td>
													</tr>
													<tr>
														<td class=col1><br></td>
														<td class=col1></td>
														<td class=col1></td>
														<td class=col1></td>
													</tr>
													<tr>
														<td class=row1 colspan="4"><b>VENTA CREDITO</b></td>
													</tr>
													<tr>
														<td class=col1>Incremento</td>
														<td class=col2 colspan="3"><input type="text" class="tbox" name="Descuento" value="<? echo $frm[Descuento] ?>" size="3"  maxlength="3" onblur="recalcularvalores()">%</td>
													</tr>
													<tr>
														<td class=col1>Comentario </td>
														<td class=col2 colspan="3"><textarea class="tareabox" name="ObservacionDescuento" id="ObservacionDescuento" rows="4" cols="64"><? echo $frm[ObservacionDescuento] ?></textarea></td>
													</tr>


													<tr>
													  <td colspan="4" class=row1>
                                                        <?php if($r->ClubSuavidad=="S"): ?>
                                                            <b>BONOS DISPONIBLES |
                                                            <a href="?mod=PuntosCliente&action=mostrar&cedula=<?php echo $r->Cedula; ?>" target="_blank">HISTORICO PUNTOS </a> |
                                                            <a href="?mod=BonosCliente&action=mostrar&cedula=<?php echo $r->Cedula; ?>" target="_blank">HISTORICO BONOS </a> |
                                                            <a href="?mod=ComprasCliente&action=mostrar&cedula=<?php echo $r->Cedula; ?>" target="_blank">HISTORICO COMPRAS </a> |
                                                            </b>
                                                        <?php
														else: ?>
                                                        <span style="color:#F00; font-size:12px; font-weight:bold">
														CLIENTE NO FIDELIZADO
                                                        </span>
														<?php endif; ?>

                                                        </td>
												  </tr>



													<tr>
													  <td colspan="4" class=col1>
                                                      	<table width="100%" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                          <td colspan="5">

        <table border="0" width="100%">
              <tr >
                  <td class=col1 valign="top">Buscar Bono</td>
                  <td class=col2>
                  	<?php if($submit_caption!="Confirmar Factura"): ?>
                        <input type="text" name="BuscarNumero" id="BuscarNumero" placeholder="Numero">
                        <input type="text" name="BuscarCedula" id="BuscarCedula" placeholder="Cedula Pertenece">
                        <input type="hidden" name="ClienteFactura" id="ClienteFactura" value="<?php echo $_GET[id] ?>">
                        <input type="hidden" name="action" id="action" value="buscarbono">
                        <input type="hidden" name="idpunto" id="idpunto" value="<?php echo $_GET[idpunto] ?>">
                        <input type="button" name="BuscarBono" id="BuscarBono" value="Buscar Bono">
                    <?php endif; ?>

				<?php

				if (!empty($_GET[BuscarCedula]) && !empty($_GET[BuscarNumero])){
						$msg_busca_bono="<br>Bono no existe";
						//Busco el id del cliente
						$id_cliente=get_field("Cliente","IDCliente","Cedula",$_GET[BuscarCedula]);
						//Busco el bono del tercero si existe el cliente
						if (!empty($id_cliente)){
							$array_bonos=explode(",",$_GET[BuscarNumero]);
							if(count($array_bonos)>0){
								foreach($array_bonos as $numero_bono_buscar){
									$condicion_numero_bono[]="'".(int)trim($numero_bono_buscar)."'";
								}
							}
							$numeros_buscar=implode(",",$condicion_numero_bono);




							//$sql_bono_tercero =  "SELECT * FROM BonoFidelizacion WHERE IDBonoFidelizacion = '" . $_GET[BuscarNumero] . "' AND IDCliente = '".$id_cliente."' AND FechaVencimiento >= CURDATE() AND Estado = 'D' ORDER BY Fecha DESC ";
							$sql_bono_tercero =  "SELECT * FROM BonoFidelizacion WHERE IDBonoFidelizacion in (".$numeros_buscar.") AND IDCliente = '".$id_cliente."' AND FechaVencimiento >= CURDATE() AND (Estado = 'D') ORDER BY Fecha DESC ";
							$query_bono_tercero=db_query($sql_bono_tercero);
							while($row_bono_tercero=db_fetch_array($query_bono_tercero)){
								$bono_tercero_encontrado=1;
								$id_cliente_bono_pertenece=$id_cliente;
								$id_bono_tercero[]=$row_bono_tercero[IDBonoFidelizacion];
								$msg_busca_bono="<br>Bono encontrado y disponible para forma de pago";
							}
						}
						else{
							$msg_busca_bono="<br>Bono no existe";
						}
				}

				if($bono_tercero_encontrado==1){
					if (count($id_bono_tercero)>0){
						$id_bonos_buscar=implode(",",$id_bono_tercero);
						$condicion_otros_bonos= " or (IDBonoFidelizacion in ($id_bonos_buscar))";
					}
				}

                 ?>




                    <?php echo $msg_busca_bono; ?>
                  </td>
              </tr>
        </table>


                                                          </td>
                                                          </tr>
                                                        <tr>
                                                          <td><strong>UTILIZAR</strong></td>
                                                        	<td>
                                                           	<strong>Numero</strong></td>
                                                        	<td><strong>Fecha</strong></td>
                                                        	<td>
                                                           	<strong>Valor</strong></td>
                                                        	<td><strong>Fecha Vencimiento</strong></td>

                                                        </tr>
                                                    <?php
													//Si es del punto de venta fabrica y es el usuario zfabrica o soluciones  muestro los bonos web
													if ($IDPuntoVenta=="16" && ($ID_Usuario == "284" || $ID_Usuario == "143" || $ID_Usuario == "13" ) ):
														$otra_condicion_bono = " or Estado = 'W'";
													endif;

													$sql_bono =  "SELECT * FROM BonoFidelizacion WHERE (IDCliente = '" . $r->IDCliente . "' AND FechaVencimiento >= CURDATE() AND (Estado = 'D' ".$otra_condicion_bono."  )  ) $condicion_otros_bonos ORDER BY Fecha DESC ";
													$query_bono=db_query($sql_bono);
														while($r_bono=db_fetch_array($query_bono)){ ?>
                                                        	<tr>
                                                        	  <td>

                                                              <input type="checkbox" class="IDBonoFidelizacion" name="IDBonoFidelizacion[]" id="IDBonoFidelizacion<?php echo $r_bono[IDBonoFidelizacion]?>" value="<?php echo $r_bono[IDBonoFidelizacion]?>"   <?php if (in_array($r_bono[IDBonoFidelizacion],$frm[IDBonoFidelizacion])):  echo "onclick='this.checked=true'"; echo "checked"; else: if($submit_caption=="Confirmar Factura"): echo "onclick='this.checked=false'"; endif;  endif;  ?> />

                                                              </td>
                                                            	<td>
                                                                <a href="Movimiento/popBono.php?id=<?php echo $r_bono[IDBonoFidelizacion];  ?> " target="_blank">
																	<?php echo $r_bono[IDBonoFidelizacion];  ?>
                                                                </a>
                                                                </td>
                                                            	<td><?php echo substr($r_bono[Fecha],0,10); ?></td>
																<td>
																<?php echo "S".number_format($r_bono[Valor],0,",","."); ?>
                                                                </td>
																<td><?php echo $r_bono[FechaVencimiento]; ?></td>
                                                            </tr>
                                                        <? } ?>
                                                        </table>

                                                      </td>
												  </tr>
													<tr>
													  <td colspan="4" class=col1>


                                                      <?php
													  $compras_cliente = get_field("Factura","IDFactura","IDCliente",$r->IDCliente);
													  if($r->ClubSuavidad=="S" && empty($compras_cliente)):
													  ?>

                                                      <table width="100%" border="0">
													      <tbody>
													        <tr>
													          <td colspan="2" class=row1 style="background-color:#FFE9E9"><strong>PRIMERA COMPRA CLUB SUAVIDAD</strong></td>
												            </tr>
													        <tr>
													          <td width="17%" class=row1>C&aacute;dula de quien Refiere:</td>
													          <td width="83%">
                                                              <input type="text" class="tbox" name="CedulaReferente" id="CedulaReferente" value="<? echo $frm[CedulaReferente] ?>">
                                                              <input type="hidden" class="tbox" name="IDClienteReferente" id="IDClienteReferente" value="<? echo $frm[IDClienteReferente] ?>">
                                                               <input type="button" name="BuscarReferente" id="BuscarReferente" value="Buscar">
                                                              </td>
												            </tr>
												          </tbody>
											          </table>

                                                      <?php
													  	if(empty($frm[IDAlianza]))
														  	 $condicion_alianza_primera_compra = " and A.IDAlianza not in (16) ";
														else
															 $condicion_alianza_primera_compra = " ";
													  else:
													  $condicion_alianza_primera_compra = " and A.IDAlianza not in (16) ";
													  endif;


													  //Verifico si es un referente para mostrar el descuento por referidos efectivos
													  $sql_factura_efectiva_referido = "Select * From Factura F, FormaPagoFactura FPF Where F.IDFactura = FPF.IDFactura and F.IDClienteReferente = '".$r->IDCliente."' and F.RedimidaReferido <> 'S'";
													  $result_factura_efectiva_referido =db_query($sql_factura_efectiva_referido);
													  $total_facturas_efectivas = db_num_rows($result_factura_efectiva_referido);
													  $total_porcentaje_acumulado = (int)$total_facturas_efectivas * 10;
													  if($total_facturas_efectivas>5):
													  	$total_facturas_efectivas=5;
													  endif;
													  if ($total_facturas_efectivas>0):
															//consulto las alianzas disponibles
															$sql_alianza_referido = "Select * From Alianza Where AplicaReferido = 'S' and NumeroReferido = '".$total_facturas_efectivas."' and Activo = 'S' Limit 1";
		 												    $result_alianza_referido =db_query($sql_alianza_referido);
															$row_alianza_referido  = db_fetch_array($result_alianza_referido);
															if(!empty($row_alianza_referido["IDAlianza"])):
																$condicion_otra_alianza = "Select IDAlianza From Alianza Where AplicaReferido <> 'S'";
																$result_otra_alianza = db_query($condicion_otra_alianza);
																while($row_otra_alianza = db_fetch_array($result_otra_alianza)):
																	$array_otra_alianza []= $row_otra_alianza["IDAlianza"];
																endwhile;
																$array_otra_alianza [] = $row_alianza_referido["IDAlianza"];
																if(count($array_otra_alianza)>0):
																	$condicion_otra_alianza = implode(",",$array_otra_alianza);
																endif;

																$condicion_alianza_referido = " and A.IDAlianza  in (".$condicion_otra_alianza.") ";
															endif;
															$mensaje_alianza_referido = "<li style='color:#FF0004'>Atencion: Aplica alianza:  ".$row_alianza_referido["Nombre"]. " " .$row_alianza_referido["Descuento"] ."% Total % Para Redimir: " . $total_porcentaje_acumulado."% </li>";
															//$frm[TipoAlianza]="Referente";
													else:

														$condicion_alianza_referido = " and A.IDAlianza  not in (Select IDAlianza From Alianza Where AplicaReferido = 'S') ";
														$mensaje_alianza_referido = "";
													endif;

													  ?>


													    <table width="100%" border="0">
													      <tbody>
													        <tr>
													          <td class=row1><strong>Descuento  por  Alianza</strong></td>
													          <td>
                                                              <input type="hidden" class="tbox" name="TipoAlianza" id="TipoAlianza" value="<? echo $frm[TipoAlianza] ?>">
                                                              <input type="hidden" class="tbox" name="ValorAlianza" id="ValorAlianza" value="">
                                                              <select name="IDAlianza" id="IDAlianza" class="input seleccion_alianza">
                                                              	<option value=""></option>
                                                                <?php
																	$sql_alianza = "Select * From Alianza A,PuntoVentaAlianza PVA Where A.IDAlianza = PVA.IDAlianza and PVA.IDPuntoVenta = '".$IDPuntoVenta."' and Activo = 'S' " . $condicion_alianza_primera_compra . " " . $condicion_alianza_referido;
																	$qry_alianza = db_query($sql_alianza);
																	while($r_alianza = db_fetch_array($qry_alianza)):
																		switch($r_alianza[TipoProducto]):
																			case "L":
																				$texto_aplica = "Solo para l&iacute;nea";
																			break;
																			case "T":
																				$texto_aplica = "Todas las referencias";
																			break;

																		endswitch;
																?>
																		<option class="<?php echo (int)$r_alianza[Descuento]; ?>" title="<?php echo $r_alianza[TipoProducto]; ?>"  value="<?php echo $r_alianza[IDAlianza] ?>" <?php if($frm[ IDAlianza ]== $r_alianza[IDAlianza]) echo "selected"; ?> lang="<?php echo $r_alianza[AplicaReferido] ?>"><?php echo $r_alianza[Nombre] . " - " . $r_alianza[Descuento] . "% - " . $texto_aplica  ;  ?></option>
																	<?php endwhile; ?>
	                                                          </select>
                                                              <input type="hidden" name="DescuentoAlianza" id="DescuentoAlianza" value="<?php echo $frm[ DescuentoAlianza ] ?>">
                                                              <input type="hidden" name="TipoProductoAlianza" id="TipoProductoAlianza" value="<?php echo $frm[ TipoProductoAlianza ] ?>">
                                                              </td>
												            </tr>
												          </tbody>
											          </table></td>
												  </tr>
												</table>
									  </td>
										</tr>





                                <tr>
                                  <td colspan="4" class=row1>
                                    <b>NOTIFICACIONES</b>
                                    <ul>
                                    <?php if(!empty($mensaje_alianza_referido)): ?>
												<?php echo $mensaje_alianza_referido ?>
									<?php endif; ?>

                                    <?php
											$valores_descuento=fid_notificaciones($r);
											$item_notificaciones=implode("",$valores_descuento["Mensaje"]);
											echo $item_notificaciones;
									?>
                                    <span id="notificacion_alianza"></span>
                                    </ul>
                                    </td>
                              </tr>



									<tr>
										<td class=navpic>Detalle Factura</td>
										<td class=navpic colspan="3">
											<div align="right">
												</div>
										</td>
									</tr>
									<tr bgcolor=#e7ebef>
											<td colspan="4" align=center>
												<table class="texto" width="100%" border="0" cellspacing="2" cellpadding="2" id=table1 align="center">
													<tr bgcolor="#dfe3e7">
														<td align="center"><b></b></td>
														<td align="center"><b>Agregar</b></td>
														<td align="center"><b>Referencia</b></td>
														<td align="center"><b>Talla</b></td>
														<td align="center"><b>Nombre</b></td>
														<td align="center"><b></b></td>
														<td align="center"><b>Cantidad</b></td>
													<td align="center"><b>Valor U.</b></td>
													<td align="center"><b>Descuento Par.</b></td>
													<td align="center"><b>Total</b></td>
													<td align="center"><b></b></td>
														<td align="center"><b></b></td>
														<td align="center"><b>Quitar</b>

                                                            <input type=hidden name="DescuentoSegundoPar" id="DescuentoSegundoPar" value="<?php echo $AplicaPromoDescuentoSegundoPar ?>">
                                                            <input type=hidden name="DescuentoCumple" id="DescuentoCumple" value="<?php echo $valores_descuento["DescuentoSemanaCumple"] ?>">
                                                            <input type=hidden name=ITEM value=10>
                                                            <input type="hidden" name="CodigoTarjeta" value="">
                                                       	</td>
													</tr>
													<?
													$campos = 10;
													for( $i=1; $i<=$campos;$i++ )
													{
														$numero = "Numero".$i;
														$talla = "Talla".$i;
														$nombre = "Nombre".$i;
														$idcodificacion = "IDCodificacion".$i;
														$cantidad = "Cantidad".$i;
														$codigotarjeta = "CodigoTarjeta".$i;
														$valoru = "ValorU".$i;
														$total = "Total".$i;
														$maximo = "Maximo".$i;
														$precio = "Precio".$i;
														$ValorBruto = "ValorBruto".$i;
														$descuento = "Descuento".$i;
														$descuentolin = "DescuentoLin".$i;
													?>
													<tr >
														<td align="left"><b><?=$i?></b></td>
														<td align="left">
                                                        	<div id="btn_referencia<?=$i ?>" style="display:none">
                                                         <?php if($submit_caption!="Confirmar Factura"): ?>
                                                          		<?php
																//if($IDPuntoVenta==0 ): ?>
                                                        			<input type=button name=Agregar<?=$i?> rel="<?=$i ?>" class=submit value=Referencia onclick="window.open('Referencia/popReferencias.php?IDPuntoVenta=<?=$IDPuntoVenta?>&cont=<?=$i?>','','scrollbars = yes,width=600,height=500');">
                                                        		<?php
																//endif;
																?>
														<?php endif; ?>
                                                        </div>
                                                        </td>

														<td align="left">
                                                        	<input  type=text  name=Numero<?=$i?> rel="<?=$i ?>" value="<? echo $frm[ $numero ] ?>" class="tbox tboxReferencia" size=8>

                                 <?
                                 $styletarjeta = "style=\"display:none\"";
								 if( !empty( $frm[ $codigotarjeta ] ) )
								 	$styletarjeta = " ";
								 ?>                           <input type="text" name="CodigoTarjeta<?=$i?>" id="CodigoTarjeta<?=$i?>" rel="<?=$i?>" value="<? echo $frm[ $codigotarjeta ] ?>" <?=$styletarjeta ?> class="tbox " title="Codigo Tarjeta" placeholder="Codigo Tarjeta" size=12 readonly />
							                                 <input type="text" name="CodigoTarjetaDigitado<?=$i?>" id="CodigoTarjetaDigitado<?=$i?>" value="<? echo $frm[ $codigotarjeta ] ?>" alt="<?=$i?>" rel="<?=$i?>" <?=$styletarjeta ?> class="tbox tarjeta_digitada" placeholder="Codigo Tarjeta" title="Codigo Tarjeta" size=12 <?php if($submit_caption=="Confirmar Factura"): ?> style="display:none"<?php endif; ?> />


                                                            </td>
														<td align="left"><input type=text readonly name=Talla<?=$i?> value="<? echo $frm[ $talla ] ?>" class="tbox" size=5></td>
														<td align="left"><input type=text readonly name=Nombre<?=$i?> value="<? echo $frm[ $nombre ] ?>" class="tbox" size=10 title="Nombre"></td>
														<td align="left"><input type=hidden name=IDCodificacion<?=$i?> id="IDCodificacion<?=$i?>" value="<? echo $frm[ $idcodificacion ] ?>" ></td>
														<td align="center"><input type=text name=Cantidad<?=$i?> id=Cantidad<?=$i?> value="<? echo $frm[ $cantidad ] ?>" class="tbox" size=5 onblur=" promo_segundo_par(); if(!compruebamaximo(this.value,<?=$i?>)) this.value = ''; else calculatotal(this.value,<?=$i?>);"></td>
													<td align="left"><input type=text readonly id="ValorU<?=$i?>" name=ValorU<?=$i?> value="<? echo $frm[ $valoru ] ?>" class="tbox" size=10 onblur=" setvalor(this.value,<?=$i?>);calculatotal(this.value,<?=$i?>); "></td>
													<td align="center">
                                                    <input type=text name="DescuentoLin<?=$i?>" value="<? echo $frm[ $descuentolin ] ?>" onblur="calculatotal(this.value,<?=$i?>);" class="tbox" size=3 maxlength="3" <?php if($habilita_descuento=="N") echo "readonly"; ?> >
                                                    <input type=hidden name="PrimerDescuentoLin<?=$i?>" value="<? echo $frm[ $descuentolin ] ?>">
                                                    </td>
													<td align="left"><input type=text readonly name=Total<?=$i?> value="<? echo $frm[ $total ] ?>" class="tbox" size=10></td>
													<td align="left"><input type=hidden name=Maximo<?=$i?> value="<? echo $frm[ $maximo ] ?>"></td>
														<td align="left">
                                                        	<input type=hidden name=Precio<?=$i?> value="<? echo $frm[ $precio ] ?>">
                                                            <input type=hidden name=ValorBruto<?=$i?> value="<? echo $frm[ $valorbruto ] ?>">
                                                            </td>
														<td align="left">
                                                        <input type=hidden name=Descuento<?=$i?> value="<? echo $frm[ $descuento ] ?>">
                                                        <?php if($submit_caption!="Confirmar Factura"): ?>
                                                       	 <input type=button name=Borrar<?=$i?> class=submit value=Borrar onclick="Borrar(<?=$i?>);">
                                                        <?php endif; ?>

                                                        </td>

													</tr>
													<?
													}//end for
													?>
												</table>
											</td>
										</tr>
									<tr>
										<td class=col1></td>
										<td class=col1 width="250"></td>
										<td class=navpic colspan="2">
												<div align="left">
													RESUMEN FACTURA</div>
											</td>
									</tr>
										<tr>
										<td class=col1></td>
										<td class=col1 width="250"></td>
										<td class=col2>
												<div align="right">
													Sub Total Factura
												</div>
											</td>
										<td class=col2><input type=text readonly id="ValorTotal" name=ValorTotal value="<? echo $frm[ValorTotal] ?>" class="tbox" size=15>
										  <input type="hidden" name="TotalFacturaNumero" id="TotalFacturaNumero" value="<? echo $frm[TotalFacturaNumero] ?>" />
										  <input type="hidden" readonly value="<? echo $frm[TotalSinIVA] ?>" name="TotalSinIVA" class="tbox" size="15" />
										  <input type="hidden" readonly name="ValorIVA" value="<? echo $frm[ValorIVA] ?>" class="tbox" size="15" /></td>
										</tr>
										<tr>
										  <td class=col1></td>
										  <td class=col1></td>
										  <td class=col2>
                                          <div align="right">
                                         Bonos de fidelizaci&oacute;n redimidos
                                          </div>
                                          </td>
										  <td class=col2><input type="text" readonly name="SumaBono" id="SumaBono" value="<? if (empty($frm[SumaBono])) { echo "0"; } else{  echo $frm[SumaBono]; } ?>" class="tbox" size="15" />
										   <input type="hidden" name="ValorBonoParametro" id="ValorBonoParametro" value="<?php echo (int)get_field("ParametroFidelizacion","Valor","IDParametroFidelizacion","8")?>" />
										   <input type="hidden" name="SumaBonoNumero" id="SumaBonoNumero" value="0" />
                                           <input type="hidden" name="SobranteBono" id="SobranteBono" value="<?php if ($frm[SobranteBono] !=0 ){ echo $frm[SobranteBono];  } else{ echo "0"; }?>" />
										   <input type="hidden" readonly name="ValorTotalNoIva" value="<? echo $frm[ValorTotalNoIva] ?>" class="tbox" size="15" /></td>
									  </tr>
										<tr>
										  <td class=col1></td>
										  <td class=col1></td>
										  <td class="col2"><div align="right"> sub total menos bonos de fidelizaci&oacute;n redimidos </div></td>
										  <td class="col2"><input type="text" readonly name="ValorMenosBono" value="<? echo $frm[ValorMenosBono] ?>" class="tbox" size="15" /></td>
									  </tr>
										<tr>
										  <td class=col1></td>
										  <td class=col1></td>
										  <td class="col2"><div align="right"> Valor IVA </div></td>
										  <td class="col2"><input type="text" readonly name="ValorIvaMenosBono" value="<? echo $frm[ValorIvaMenosBono] ?>" class="tbox" size="15" /></td>
									  </tr>
										<tr>
										  <td class=col1></td>
										  <td class=col1></td>
										  <td class="col2"><div align="right"> Valor a Pagar</div></td>
										  <td class="col2"><input type="text" readonly name="ValorTotalFactura" value="<? echo $frm[ValorTotalFactura] ?>" class="tbox" size="15" /></td>
									  </tr>


									</table>

                                    <?php
                                    			$sql_codigo = " SELECT * FROM TarjetaPunto WHERE Estado = 'D' AND IDPuntoVenta = '" . $IDPuntoVenta . "' ORDER BY CAST(SUBSTRING(CodigoTarjeta,LOCATE(' ', CodigoTarjeta)+1) AS SIGNED) ASC ";
												$qry_codigo = db_query( $sql_codigo );
												$contador_tarjeta=1;
												while($r_codigo = db_fetch_array( $qry_codigo )){
													if($contador_tarjeta<=7):
												?>
													<input type="hidden" name="TarjetaConsecutivo<?php echo $contador_tarjeta; ?>"	 id="TarjetaConsecutivo<?php echo $contador_tarjeta; ?>" value="<?php echo $r_codigo["CodigoTarjeta"]; ?>">
												<?php
													endif;
												$contador_tarjeta++;
												}
												//Averiguo la codif especifica de la tarjeta
												$sql_ptoref="Select * From PuntoVentaReferencia where IDReferencia = 7615 and IDPuntoVenta = '".$IDPuntoVenta."'";
												$result_ptoref=db_query($sql_ptoref);
												$row_ptoref = db_fetch_array($result_ptoref);
												$sql_codif="Select * From CodificacionEspecifica where IDPuntoVentaReferencia = '".$row_ptoref["IDPuntoVentaReferencia"]."' and IDTalla = 16 ";
												$result_codif=db_query($sql_codif);
												$row_codif = db_fetch_array($result_codif);?>
                                                <input type="hidden" name="TarjetaCodificacionEspecifica"	 id="TarjetaCodificacionEspecifica" value="<?php echo $row_codif["IDCodificacionEspecifica"]; ?>">
                                                <?php




                                    ?>

                  <input type="hidden" name="action" value="<?=$newmode?>">

                                    <?php if($submit_caption=="Confirmar Factura"):
										$clase_bloqua_boton = "bloquea_confirmar";
									?>
                                    	<input type="button" name="corregir" id="corregir" onclick="location.href='<?php echo "?mod=GenerarFactura&action=edit&id=".$_GET["id"]."&idnot="; ?>'" value="Regresar y corregir" />


                                    <?php endif; ?>
									<div id="<?php echo $clase_bloqua_boton ?>" ><br>
									<input type="submit" class="button" name="submit" id="envia_factura" value="<?=$submit_caption?>">
                                    </div>

					  </div>

					</td>
				</tr>
			</table>
		</td>
	</tr>

</table>
</FORM>
<?
} // END function print_form_fotos($id,$numfotos)
?></BODY></HTML>
