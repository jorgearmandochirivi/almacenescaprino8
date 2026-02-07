<link rel="stylesheet" href="admin/jscripts/choosen/chosen.css">
<?


	//envia_correo_proveedor("jorgechirivi@gmail.com","538", "4", "jorge", "Garantia", "plantillas","Excedente","1");

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
				$sql_cliente = "SELECT *  Cliente WHERE IDCliente = '$IDCliente'";
				$query_cliente = db_query($sql_cliente);
				$r_cliente = db_fetch_object( $query_cliente );
				print_form($r_cliente->IDCliente,"insertar","Confirmar Factura","Confirmar Factura",$HTTP_POST_VARS);

			break;

			case "edit":

               print_form($id,"insert","Generar Factura","Generar Factura");
                break ;
			case "list" :
				$sql = make_qry_string($HTTP_GET_VARS);
				list_r($sql);
			break;

			case "creargarantia":
					if ($_POST[tipo_factura]=="cambio"){
					  $datos_producto = explode("|",$_POST[IDProductoGarantia]);
					  $IDDetalleFactura = $datos_producto[0];
					  $IDFactura=$datos_producto[1];
					  $IDPuntoVenta=$datos_producto[2];
					  $id_producto=$_POST[IDProductoGarantia];

						print_formcrear_garantia($_POST[IDCambio],$id_producto,$IDPuntoVenta,"guardargarantia","Guardar Garantia","Guardar Garantia","cambio");

					}
					else{

					$id_factura=$_POST["IDFactura"];

					// verifo que no exista una garantia ya creada con este producto de esta factura
					  $datos_producto = explode("|",$_POST[IDProductoGarantia]);
					  $IDDetalleFactura = $datos_producto[0];
					  $IDFactura=$datos_producto[1];
					  $IDPuntoVenta=$datos_producto[2];

					  $sql_producto="select * from Garantia Where IDDetalleFactura='".$IDDetalleFactura."' and IDFactura = '".$IDFactura."' and IDPuntoVentaFactura = '".$IDPuntoVenta."' and IDEstadoGarantia not in ( 9, 10 ) ";
					  $qry_producto=db_query($sql_producto);
					  if (db_num_rows($qry_producto)>0){
							$msg="Ya esta registrada una garantia con este producto/factura, por favor verifique";
							mostrarfactura("mostrar","Buscar",$msg);
					  }else{
						$id_producto=$_POST[IDProductoGarantia];
						print_formcrear_garantia($id_factura,$id_producto,$IDPuntoVenta,"guardargarantia","Guardar Garantia","Guardar Garantia");
					  }
					}
			break;

			case "guardargarantia":
				$frm= vars_LOG($HTTP_POST_VARS);
				//Verifico las causas de la garantia
				$frm['FechaTrCr'] = date("Y-m-d h:i:s");
				$frm['UsuarioTrCr'] = "Tienda";
				$frm['IDEstadoGarantia'] = 1; //Toda garantia empieza con el estado recibida de cliente
				$dias_garantia=get_field("ParametroGarantia","Valor","IDParametroGarantia",3);
				//$frm['FechaEstimadaEntrega'] = calcula_dias($dias_garantia);


				//Validacion de fecha fija cuando produccion se encuentra en vacaciones y no recibe garantias
				if (date("Y-m-d")>='2015-12-21' && date("Y-m-d")<='2016-01-12'):
					$frm['FechaEstimadaEntrega'] = "2016-02-02";
				else:
					$frm['FechaEstimadaEntrega'] = calcula_dias($dias_garantia);
				endif;



				$id=insert_width_table($frm,"Garantia","IDGarantia");
				//envio correo de notificacion
				envia_nuevo_garantia($id);

				// Abro ventana con recibo
				echo "<script>window.open('Garantia/popBoucherGarantia.php?id=".$id."&idpunto=".$IDPuntoVenta."','','width=550, height=350, scrollbars=yes');
					  location.href='?mod=SeguimientoGarantia&action=edit&id=".$id."';</script>";

				exit;

				window_alert("Garantia agregada con exito con el numero $id ");
				mostrarfactura("mostrar","Buscar Factura");
			break;


			case "mostrar":


				if (!empty($_POST["numero_factura"]) || !empty($_GET["numero_factura"]) || !empty($_GET["numero_cambio"]) || !empty($_GET["IDFactura"])):

					if (!empty($_POST["numero_factura"]))
						$numero_factura=$_POST["numero_factura"];
					elseif(!empty($_GET["numero_factura"]))
						$numero_factura=$_GET["numero_factura"];

					if (empty($_POST["puntoventa"]) && empty($_GET["puntoventa"])):
						$msg="Debe seleccionar el punto de venta por favor verifique";
						mostrarfactura("mostrar","Buscar",$msg);
					else:

						if (!empty($_POST["puntoventa"]))
							$id_punto_venta = $_POST["puntoventa"];
						elseif (!empty($_GET["puntoventa"]))
							$id_punto_venta = $_GET["puntoventa"];



						if ($numero_factura=="reproceso"){
							// si es un reproceso no se necesita datos de factura ni cliente
							print_formcrear_garantia("0",$id_producto,$id_punto_venta,"guardargarantia","Guardar Garantia","Guardar Garantia");
						}
						elseif ($numero_factura=="restauracion"){
							// si es un reproceso no se necesita datos de factura ni cliente
							print_formcrear_garantia("0",$id_producto,$id_punto_venta,"guardargarantia","Guardar Garantia","Guardar Garantia");
						}
						elseif ($numero_factura=="mayorista" || $numero_factura=="dotacion"){
							// si es un reproceso no se necesita datos de factura ni cliente
							print_formcrear_garantia("0",$id_producto,$id_punto_venta,"guardargarantia","Guardar Garantia","Guardar Garantia");
						}
						elseif(!empty($_GET["numero_cambio"])){
							$sql_cambio = "SELECT * FROM Cambio WHERE IDCambio = '$_GET[numero_cambio]' and IDPuntoVenta = '".$id_punto_venta."'";
							$query_cambio = db_query($sql_cambio);
							if( db_num_rows( $query_cambio ) == 0 )
							{
								$msg="No se encontro el cambio por favor verifique";
								mostrarfactura("mostrar","Buscar",$msg);
							}//end if( db_num_rows( $query_cliente ) == 0 )
							else
							{
								$r_cambio = db_fetch_object( $query_cambio );
								//print_form($r_cliente->IDCliente,"insert","Generar Factura","Generar Factura");
								print_formgarantia($r_cambio->IDCambio,"creargarantia","Generar Garantia","Generar Garantia","cambio");
							}//end else



						}
						else{
							//$sql_factura = "SELECT * FROM Factura WHERE NumeroFactura = '$numero_factura' and IDPuntoVenta = '".$IDPuntoVenta."'";

							if ($_GET["tipofactura"]=="facturabono"){
								$sql_factura = "SELECT * FROM FacturaBono WHERE NumeroFacturaBono = '$numero_factura' and IDPuntoVenta = '".$id_punto_venta."'  and FechaFacturaBono >= '2021-10-01 00:00:00'";
							}
							else{
								if($numero_factura<=100 && $id_punto_venta<>16) // secambio numeracion desde 1
									$condicion_factura=" and FechaFactura >= '2021-11-02 00:00:00' ";
								else
									$condicion_factura=" ";

								//$sql_factura = "SELECT * FROM Factura WHERE NumeroFactura = '$numero_factura' and IDPuntoVenta = '".$id_punto_venta."' " . $condicion_factura;
								//$sql_factura = "SELECT * FROM Factura WHERE IDFactura = '".$_GET["IDFactura"]."' and IDPuntoVenta = '".$id_punto_venta."' " . $condicion_factura . " and FechaFactura >= '2018-01-01 00:00:00' ";
								$sql_factura = "SELECT * FROM Factura WHERE IDFactura = '".$_GET["IDFactura"]."' and IDPuntoVenta = '".$id_punto_venta."' " . $condicion_factura . " and FechaFactura >= '2010-01-01 00:00:00' ";
							}

							//echo $sql_factura;

							$query_factura = db_query($sql_factura);
							if( db_num_rows( $query_factura ) == 0 )
							{
								$msg="No se encontro la factura por favor verifique";
								mostrarfactura("mostrar","Buscar",$msg);
							}//end if( db_num_rows( $query_cliente ) == 0 )
							else
							{
								$r_factura = db_fetch_object( $query_factura );
								if ($_GET["tipofactura"]=="facturabono"){
									print_formgarantia($r_factura->IDFacturaBono,"creargarantia","Generar Garantia","Generar Garantia");
								}
								else{
									print_formgarantia($r_factura->IDFactura,"creargarantia","Generar Garantia","Generar Garantia");
								}
								//print_form($r_cliente->IDCliente,"insert","Generar Factura","Generar Factura");
								//print_formgarantia($r_factura->IDFactura,"creargarantia","Generar Garantia","Generar Garantia");
							}//end else
						}
					endif;
			 elseif(!empty($_POST["cedula_cliente"])):
					$sql_cliente = "SELECT IDCliente FROM Cliente WHERE Cedula = '".$_POST["cedula_cliente"]."'";
					$query_cliente = db_query($sql_cliente);
					if( db_num_rows( $query_cliente ) == 0 )
					{
						$msg="No se encontro el cliente por favor verifique";
						mostrarfactura("mostrar","Buscar",$msg);
					}//end if( db_num_rows( $query_cliente ) == 0 )
					else
					{
						while ($r_cliente = db_fetch_object( $query_cliente )){
							$array_clientes[]=$r_cliente->IDCliente;
						}
						//print_form($r_cliente->IDCliente,"insert","Generar Factura","Generar Factura");
						print_formfactura_cliente($array_clientes,"buscarfactura","Seleccionar factura","Seleccionar factura");
					}//end else

			 elseif(!empty($_POST["nombre_cliente"]) || !empty($_POST["apellido_cliente"])):
			 		if(!empty($_POST["nombre_cliente"]) && empty($_POST["apellido_cliente"])):
						$sql_cliente = "SELECT IDCliente FROM Cliente WHERE Nombre like '%".$_POST["nombre_cliente"]."%'";
					elseif(empty($_POST["nombre_cliente"]) && !empty($_POST["apellido_cliente"])):
						$sql_cliente = "SELECT IDCliente FROM Cliente WHERE Apellido like '%".$_POST["apellido_cliente"]."%'";
					elseif(!empty($_POST["nombre_cliente"]) && !empty($_POST["apellido_cliente"])):
						$sql_cliente = "SELECT IDCliente FROM Cliente WHERE Nombre like '%".$_POST["nombre_cliente"]."%' and Apellido like '%".$_POST["apellido_cliente"]."%'";
					endif;


					$query_cliente = db_query($sql_cliente);
					if( db_num_rows( $query_cliente ) == 0 )
					{
						$msg="No se encontro el cliente por favor verifique";
						mostrarfactura("mostrar","Buscar",$msg);
					}//end if( db_num_rows( $query_cliente ) == 0 )
					else
					{
						while ($r_cliente = db_fetch_object( $query_cliente )){
							$array_clientes[]=$r_cliente->IDCliente;
						}
						//print_r($array_clientes);

						//print_form($r_cliente->IDCliente,"insert","Generar Factura","Generar Factura");
						print_formfactura_cliente($array_clientes,"buscarfactura","Seleccionar factura","Seleccionar factura");
					}//end else
			 endif;


			break;
			default :
				mostrarfactura("mostrar","Buscar");
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

function mostrarfactura($newmode,$submit_caption,$msg=""){
	global $IDPuntoVenta;
?>

<br>
<form name="frmcliente" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" onsubmit="disable(this);">
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="500">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><span class="gen"><strong>INGRESE GARANTIA O RESTAURACION</strong></span></td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table align="center" width="500" cellpadding="0" cellspacing="1" border="0" class="forumline">

  <tr>
    <td colspan="4" align="center" valign="rigth">Introduzca alguno de los siguientes datos por favor</td>
    </tr>
  <tr>
    <td width="138" align="center" valign="middle" class="col1">N&uacute;mero de la factura</td>
    <td colspan="3" class="col2" >
      <input type="text" class="tbox" name="numero_factura">

      <span class="col1">Punto Venta</span>
      <select class=tbox name=puntoventa>
		  <option value="">Seleccione</option>
						<?
							$sql_puntos = "SELECT P.* FROM PuntoVenta P ";
							$sql_puntos .= "WHERE 1 Order By Nombre";

							$query_puntos = db_query( $sql_puntos );

							while( $r_puntos = db_fetch_object( $query_puntos ) )
							{
								if ($r_puntos->IDPuntoVenta == $IDPuntoVenta )
									$selecciona = " selected";
								else
									$selecciona = " ";

								echo "<option value=$r_puntos->IDPuntoVenta $selecciona>$r_puntos->Nombre</option>";

							}
						?>
						</select>

      </td>
  </tr>
  <tr>
    <td class="col1" align="center" valign="middle">C&eacute;dula del cliente</td>
    <td colspan="3" class="col2" ><input type="text" class="tbox" name="cedula_cliente" /></td>
  </tr>
  <tr>
    <td class="col1" align="center" valign="middle">Nombre Cliente</td>
    <td width="144" class="col2" ><input type="text" class="tbox" name="nombre_cliente" /></td>
    <td width="68" class="col2" ><span class="col1">Apellido</span></td>
    <td width="145" class="col2" ><input type="text" class="tbox" name="apellido_cliente" /></td>
    </tr>
  <tr>
    <td colspan="4" align="center" valign="middle" >
      <input type="submit" class="button" name="enviar" value="<?=$submit_caption?>" />
    </td>
    </tr>
	<!--
	<tr>
    <td colspan="4" align="center" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
	<tr>
    <td colspan="4" align="left" valign="middle"><strong>INGRESE RESTAURACION</strong></td>
  </tr>
  <tr>
    <td colspan="4" align="center" valign="middle" bgcolor="#CCCCCC"><a href="?mod=Garantia&action=mostrar&numero_factura=restauracion&puntoventa=<?php echo $IDPuntoVenta?>">CREAR RESTAURACION</a></td>
  </tr>
-->
	<tr>
    <td colspan="4" align="center" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" align="left" valign="middle"><strong>INGRESE REPROCESO</strong></td>
  </tr>
  <tr>
    <td colspan="4" align="center" valign="middle" bgcolor="#CCCCCC"><a href="?mod=Garantia&action=mostrar&numero_factura=reproceso&puntoventa=<?php echo $IDPuntoVenta?>">CREAR REPROCESO</a></td>
  </tr>

  <tr>
    <td colspan="4" align="center" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>

  <?php if($IDPuntoVenta==16 || $IDPuntoVenta==21 ){ ?>
	<tr>
		<td colspan="4" align="left" valign="middle"><strong>INGRESO MAYORISTA</strong></td>
	</tr>
  	
	<tr>
		<td colspan="4" align="center" valign="middle" bgcolor="#CCCCCC"><a href="?mod=Garantia&action=mostrar&numero_factura=mayorista&puntoventa=<?php echo $IDPuntoVenta?>">GARANTIA MAYORISTA</a></td>
	</tr>
  <?php } ?>

	<tr>
    <td colspan="4" align="left" valign="middle"><strong>INGRESO DOTACION</strong></td>
    </tr>
  <tr>
    <td colspan="4" align="center" valign="middle" bgcolor="#CCCCCC"><a href="?mod=Garantia&action=mostrar&numero_factura=dotacion&puntoventa=<?php echo $IDPuntoVenta?>">GARANTIA DOTACION</a></td>
  </tr>

  <tr>
    <td class="col2list" align="center" valign="middle" colspan="4"><input type="hidden" value="<?=$newmode?>" name="action">
      </td>
  </tr>
  <tr>
	<td class="col2list" align="center" valign="middle" colspan="4" style="color:#F00; font-weight:bold">
    	<?php echo $msg; ?>
	</td>
  </tr>


</table>
</form>
<?
}//end	mostrar($newmode,$submit_caption)


/*******************************************************************************************
		funtcion Print_formCliente
*******************************************************************************************/
function print_formgarantia($id="",$newmode,$title,$submit_caption,$tipo_garantia="") {



	GLOBAL $TitleMod,$Table,$MOD,$Key,$cedula,$array_gustos,$array_deportes,$array_hobbies,$array_musica, $datos,$IDPuntoVenta;
	$qid = db_query(" SELECT * FROM Cliente WHERE Cedula = '$id' ");
	$r = db_fetch_object($qid);

?>

<br>
	<form name="frmproducto_garantia" id="frmproducto_garantia" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" class="frmproductogarantia">

<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="500">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
			Seleccione el producto que se ingresar&aacute; por garant&iacute;a</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table class="forumline" width="500" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline"   >

                <tr bgcolor="#dfe3e7">
                  <td align="center" class=rowform>Seleccionar</td>
                    <td align="center" class=rowform><b>Item</b></td>
                    <td align="center" class=rowform><b>Referencia</b></td>
                    <td align="center" class=rowform><b>Talla</b></td>
                    <td align="center" class=rowform><b>Nombre</b></td>
                    <td align="center" class=rowform><b>Cantidad</b></td>
                    <td align="center" class=rowform><b>Valor U.</b></td>
                    <td align="center" class=rowform><b>Descuento Par.</b></td>
                    <td align="center" class=rowform><b>Total</b></td>
                </tr>

                <?php
					if (!empty($_POST["puntoventa"]))
						$punto_consulta=$_POST["puntoventa"];
					else
						$punto_consulta=$_GET["puntoventa"];


					if ($_GET["tipofactura"]=="facturabono"){
						$sql_detalle = "SELECT * FROM DetalleFacturaBono WHERE IDFacturaBono = '".$id."' and IDPuntoVenta = '".$punto_consulta."'";
					}
					else{
						$sql_detalle = "SELECT * FROM DetalleFactura WHERE IDFactura = '".$id."' and IDPuntoVenta = '".$punto_consulta."'";
					}


					//echo $sql_detalle;

					$query_detalle = db_query($sql_detalle);
					$i = 1;
					while( $r_detalle = db_fetch_object( $query_detalle ) ){

							if( $i % 2 == 0 )
								$class = "row2";
							else
								$class = "rowtable";
					$nombre_item=get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));

					?>
                    <tr bgcolor="#dfe3e7">
                      <td align="left" class="<?=$class?>">
                        <?php if($nombre_item!="Excedente"):
							if ($_GET["tipofactura"]=="facturabono"){ ?>
                        	<input type="radio" name="IDProductoGarantia" id="IDProductoGarantia<?=$i?>" value="<?php echo $r_detalle->IDDetalleFacturaBono."|".$r_detalle->IDFacturaBono."|".$r_detalle->IDPuntoVenta;  ?>">
                            <?php }
							else{ ?>
                            	<input type="radio" name="IDProductoGarantia" id="IDProductoGarantia<?=$i?>" value="<?php echo $r_detalle->IDDetalleFactura."|".$r_detalle->IDFactura."|".$r_detalle->IDPuntoVenta;  ?>">
                            <?php } ?>


                        <?php endif; ?>
                        </td>
                        <td align="left" class="<?=$class?>"><b><?=$i?></b></td>
                        <td align="left" class="<?=$class?>">
                        <?php if($nombre_item!="Excedente"):
                        			echo $nombre_item;
							else:?>
									 Por favor buscar cambio asignado  al numero de la factura
                        <?php endif; ?>
                        </td>
                        <td align="left" class="<?=$class?>"><%=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))%></td>
                        <td align="left" class="<?=$class?>"><%=get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))%></td>
                        <td align="left" class="<?=$class?>"><?=$r_detalle->Cantidad?></td>
                        <td align="left" class="<?=$class?>"><?echo number_format($r_detalle->ValorU);?></td>
                        <td align="left" class="<?=$class?>"><?echo number_format($r_detalle->DescuentoPar);?></td>
                        <td align="left" class="<?=$class?>"><?echo number_format( ( $r_detalle->ValorU * $r_detalle->Cantidad ) * ( 1 - ( $r_detalle->DescuentoPar / 100 ) ) );?></td>
                    </tr>
                <?php
				$i++;
				} ?>

                <?php
					if (!empty($_GET[numero_cambio])){
					//Si es de un cambio consulto las referncias del cambio
					$sql_detalle_cambio = "SELECT * FROM DetalleCambio WHERE IDCambio = '".$_GET[numero_cambio]."' and IDPuntoVenta = '".$punto_consulta."'";
					$query_detalle_cambio = db_query($sql_detalle_cambio);
					$i = 1;
					while( $r_detalle_cambio = db_fetch_object( $query_detalle_cambio ) ){

							if( $i % 2 == 0 )
								$class = "row2";
							else
								$class = "rowtable";

					?>
                    <tr bgcolor="#dfe3e7">
                      <td align="left" class="<?=$class?>">
                        <input type="radio" name="IDProductoGarantia" id="IDProductoGarantia<?=$i?>" value="<?php echo $r_detalle_cambio->IDDetalleCambio."|".$r_detalle_cambio->IDCambio."|".$r_detalle_cambio->IDPuntoVenta;  ?>"></td>
                        <td align="left" class="<?=$class?>"><b><?=$i?></b></td>
                        <td align="left" class="<?=$class?>"><%=get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle_cambio->IDCodificacionEspecifica)))%></td>
                        <td align="left" class="<?=$class?>"><%=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))%></td>
                        <td align="left" class="<?=$class?>"><%=get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle_cambio->IDCodificacionEspecifica)))%></td>
                        <td align="left" class="<?=$class?>"><?=$r_detalle->Cantidad?></td>
                        <td align="left" class="<?=$class?>"><?echo number_format($r_detalle_cambio->ValorU);?></td>
                        <td align="left" class="<?=$class?>"><?echo number_format($r_detalle_cambio->DescuentoRef);?></td>
                        <td align="left" class="<?=$class?>"><?echo number_format( ( $r_detalle_cambio->ValorU * $r_detalle_cambio->Cantidad ) * ( 1 - ( $r_detalle_cambio->DescuentoRef / 100 ) ) );?></td>
                    </tr>
                <?php
				$i++;
				}
					} // end if
				?>


			<tr>
			<td colspan=9 align=center class="col2list">
            	<input type=hidden name=IDFactura id=IDFactura value="<?=$id ?>">
                <input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
				<input type=hidden name=action value=<?=$newmode?>>
                <input type=hidden name=tipo_factura value=<?=$tipo_garantia?>>
                <input type=hidden name=IDCambio value=<?=$_GET[numero_cambio]?>>


				<input type=submit name=submit value="<? echo $submit_caption ?>" id="boton_enviar_producto" class=submit >



			</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
	<?
}// End function print_formgarantia()






/*******************************************************************************************
		funtcion print_formfactura_cliente
*******************************************************************************************/
function print_formfactura_cliente($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$cedula,$array_gustos,$array_deportes,$array_hobbies,$array_musica, $datos,$IDPuntoVenta;

?>

<br>
	<form name="frmproducto_garantia" id="frmproducto_garantia" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" class="frmproductogarantia">

<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="500">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
			Seleccione la factura </span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table class="forumline" width="500" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline"   >

                <tr bgcolor="#dfe3e7">
	                <td align="center" class=rowform>Seleccionar</td>
                    <td align="center" class=rowform><b>Numero Factura</b></td>
                    <td align="center" class=rowform><b>Cliente</b></td>
                    <td align="center" class=rowform><b>PuntoVenta</b></td>
                    <td align="center" class=rowform><b>Fecha Factura</b></td>
                    <td align="center" class=rowform><b>Valor Total</b></td>
										<td align="center" class=rowform><b>Observacion</b></td>
                </tr>

                <?php
					if (count($id)>0){
						$id_cliente_resultado=implode(",",$id);
					}
					else
						$id_cliente_resultado=0;

					$sql_detalle = "SELECT * FROM Factura WHERE IDCliente in (".$id_cliente_resultado.") Order By IDFactura DESC";
					$query_detalle = db_query($sql_detalle);
					$i = 1;
					while( $r_detalle = db_fetch_object( $query_detalle ) ){
						$supera_limite="";
							if( $i % 2 == 0 )
								$class = "row2";
							else
								$class = "row2";


		$meses_maximo_cambio = 12;
								//Valido si supera los 6 meses no se deja hacer cambio
		$fecha_hoy = date('Y-m-d');
		$nuevafecha_maxima = strtotime ( '+'.$meses_maximo_cambio.' month' , strtotime ( substr($r_detalle->FechaFactura,0,10) ) ) ;
		$nuevafecha_maxima = date ( 'Y-m-d' , $nuevafecha_maxima );
		if(strtotime($fecha_hoy) <= strtotime($nuevafecha_maxima) ):
				$supera_limite="N";
		else:
			$supera_limite="S";
		endif;

		//Valido si tiene autorizacion
		$sql_autorizacion = "Select * From AutorizacionCambio Where IDFactura = '".$r_detalle->IDFactura."' and IDPuntoVenta = '".$r_detalle->IDPuntoVenta."'";
		$result_autorizacion = db_query($sql_autorizacion);
		$total_autorizacion = (int)db_num_rows($result_autorizacion);

					?>
                    <tr bgcolor="#dfe3e7">
                        <td align="left" class="<?=$class?>">
                        &nbsp;
												<?php if($supera_limite=="N" || $total_autorizacion>0): ?>
												<a href='<? echo "?mod=Garantia&action=mostrar&numero_factura="; echo $r_detalle->NumeroFactura; ?>&puntoventa=<?php echo $r_detalle->IDPuntoVenta; ?>&IDFactura=<?php echo $r_detalle->IDFactura; ?>'><img src='admin/images/edit.gif' border='0'></a>
											<?php else: ?>
												<a href='<? echo "?mod=Garantia&action=mostrar&numero_factura="; echo $r_detalle->NumeroFactura; ?>&puntoventa=<?php echo $r_detalle->IDPuntoVenta; ?>&IDFactura=<?php echo $r_detalle->IDFactura; ?>&Tipo=restauracion'>Restauracion</a>
											<?php endif; ?>
                        </td>
                        <td align="left" class="<?=$class?>"><?php echo $r_detalle->NumeroFactura;?></td>
                        <td align="left" class="<?=$class?>"><?php echo get_field("Cliente","Nombre","IDCliente",$r_detalle->IDCliente) . " " . get_field("Cliente","Apellido","IDCliente",$r_detalle->IDCliente); ?> </td>
                      <td align="left" class="<?=$class?>"><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_detalle->IDPuntoVenta); ?></td>
                        <td align="left" class="<?=$class?>"><?php echo $r_detalle->FechaFactura;?></td>
                        <td align="left" class="<?=$class?>">$<?php echo number_format($r_detalle->ValorTotal);?></td>
												<td align="left" class="<?=$class?>">
													<?php
														if($supera_limite=="S"): ?>
															<span style="color:#EE080C">Supera los <?php echo $meses_maximo_cambio; ?> meses. </span>
														<?php endif; ?>
												</td>
                    </tr>
                <?php
				$i++;
				} ?>



                <?php // FACTURAS BONOS
          $sql_detalle = "SELECT * FROM FacturaBono WHERE IDCliente in (".$id_cliente_resultado.") Order By IDFacturaBono DESC";
					$query_detalle = db_query($sql_detalle);
					$i = 1;
					while( $r_detalle = db_fetch_object( $query_detalle ) ){

							if( $i % 2 == 0 )
								$class = "row2";
							else
								$class = "row2";

								$meses_maximo_cambio = 12;

														//Valido si supera los 6 meses no se deja hacer cambio
								$fecha_hoy = date('Y-m-d');
								$nuevafecha_maxima = strtotime ( '+'.$meses_maximo_cambio.' month' , strtotime ( substr($r_detalle->FechaFacturaBono,0,10) ) ) ;
								$nuevafecha_maxima = date ( 'Y-m-d' , $nuevafecha_maxima );
								if(strtotime($fecha_hoy) <= strtotime($nuevafecha_maxima) ):
										$supera_limite="N";
								else:
									$supera_limite="S";
								endif;

								//Valido si tiene autorizacion
								$sql_autorizacion = "Select * From AutorizacionCambio Where IDFactura = '".$r_detalle->IDFactura."' and IDPuntoVenta = '".$r_detalle->IDPuntoVenta."'";
								$result_autorizacion = db_query($sql_autorizacion);
								$total_autorizacion = (int)db_num_rows($result_autorizacion);

					?>
                    <tr bgcolor="#dfe3e7">
                        <td align="left" class="<?=$class?>">
                        &nbsp;
													<?php if($supera_limite=="N" || $total_autorizacion>0): ?>
												<a href='<? echo "?mod=Garantia&action=mostrar&numero_factura="; echo $r_detalle->NumeroFacturaBono; ?>&puntoventa=<?php echo $r_detalle->IDPuntoVenta; ?>&tipofactura=facturabono'><img src='admin/images/edit.gif' border='0'></a>
											<?php endif;  ?>
                        </td>
                        <td align="left" class="<?=$class?>"><?php echo $r_detalle->NumeroFacturaBono;?></td>
                        <td align="left" class="<?=$class?>"><?php echo get_field("Cliente","Nombre","IDCliente",$r_detalle->IDCliente) . " " . get_field("Cliente","Apellido","IDCliente",$r_detalle->IDCliente); ?> </td>
                      <td align="left" class="<?=$class?>"><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_detalle->IDPuntoVenta); ?></td>
                        <td align="left" class="<?=$class?>"><?php echo $r_detalle->FechaFacturaBono;?></td>
                        <td align="left" class="<?=$class?>">$<?php echo number_format($r_detalle->ValorTotal);?></td>
												<td align="left" class="<?=$class?>">
													<?php
														if($supera_limite=="S"): ?>
															<span style="color:#EE080C">Supera los <?php echo $meses_maximo_cambio; ?> meses!</span>
														<?php endif; ?>
												</td>

                    </tr>
                <?php
				$i++;
				} ?>



                <?php
				    //$sql_detalle_cambio = "SELECT * FROM Cambio WHERE IDCliente in (".$id_cliente_resultado.") and IDFacturaCambio	= 0 and IDFactura = 0  Order By IDCambio DESC";
					$sql_detalle_cambio = "SELECT * FROM Cambio WHERE IDCliente in (".$id_cliente_resultado.") and (IDFactura = 0 or IDFactura > 0)  Order By IDCambio DESC";
					$query_detalle_cambio = db_query($sql_detalle_cambio);
					$i = 1;
					while( $r_detalle_cambio = db_fetch_object( $query_detalle_cambio ) ){

							if( $i % 2 == 0 )
								$class = "row2";
							else
								$class = "row2";

								$meses_maximo_cambio = 12;
														//Valido si supera los 6 meses no se deja hacer cambio
								$fecha_hoy = date('Y-m-d');
								$nuevafecha_maxima = strtotime ( '+'.$meses_maximo_cambio.' month' , strtotime ( substr($r_detalle_cambio->FechaCambio,0,10) ) ) ;
								$nuevafecha_maxima = date ( 'Y-m-d' , $nuevafecha_maxima );
								if(strtotime($fecha_hoy) <= strtotime($nuevafecha_maxima) ):
										$supera_limite="N";
								else:
									$supera_limite="S";
								endif;

								//Valido si tiene autorizacion
								$sql_autorizacion = "Select * From AutorizacionCambioReferencia Where IDCambio = '".$r_detalle_cambio->IDCambio."' and IDPuntoVenta = '".$r_detalle_cambio->IDPuntoVenta."'";
								$result_autorizacion = db_query($sql_autorizacion);
								$total_autorizacion_cambio = (int)db_num_rows($result_autorizacion);

					?>
                    <tr bgcolor="#dfe3e7">
                        <td align="left" class="<?=$class?>">
                        &nbsp;
												<?php if($supera_limite=="N" || $total_autorizacion_cambio>0): ?>
												<a href='<? echo "?mod=Garantia&action=mostrar&numero_cambio="; echo $r_detalle_cambio->IDCambio; ?>&puntoventa=<?php echo $r_detalle_cambio->IDPuntoVenta; ?>'><img src='admin/images/edit.gif' border='0'></a>
											<?php endif; ?>
                        </td>
                        <td align="left" class="<?=$class?>"><font color="#EE080C"> Cambio Numero: </font>  <?php echo $r_detalle_cambio->IDCambio;?></td>
                        <td align="left" class="<?=$class?>"><font color="#EE080C">Cambio </font> <?php echo get_field("Cliente","Nombre","IDCliente",$r_detalle_cambio->IDCliente) . " " . get_field("Cliente","Apellido","IDCliente",$r_detalle_cambio->IDCliente); ?> </td>
                      <td align="left" class="<?=$class?>"><font color="#EE080C">Cambio</font> <?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_detalle_cambio->IDPuntoVenta); ?></td>
                        <td align="left" class="<?=$class?>"><font color="#EE080C">Cambio</font><br>  <?php echo $r_detalle_cambio->FechaCambio;?></td>
                        <td align="left" class="<?=$class?>">$<?php $valor_cambio = get_field("DetalleCambio","ValorU","IDCambio",$r_detalle_cambio->IDCambio); echo number_format($valor_cambio);?></td>
												<td align="left" class="<?=$class?>">
													<?php
														if($supera_limite=="S"): ?>
															<span style="color:#EE080C">Atencion . Supera los <?php echo $meses_maximo_cambio; ?> meses.</span>
														<?php endif; ?>
												</td>
                    </tr>
                <?php
				$i++;
				} ?>


			<tr>
			<td colspan=9 align=center class="col2list">
            	<input type=hidden name=IDFactura id=IDFactura value="<?=$id ?>">
                <input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
				<input type=hidden name=action value=<?=$newmode?>>
				<input type=submit name=submit value="<? echo $submit_caption ?>" id="boton_enviar_producto" class=submit >



			</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
	<?
}// End function print_formfactura_cliente()







/*******************************************************************************************
funtcion Print_formCrear garantia
*******************************************************************************************/
function print_formcrear_garantia($id="",$id_producto,$IDPuntoVenta,$newmode,$title,$submit_caption,$tipo_factura="") {


	GLOBAL $TitleMod,$Table,$MOD,$Key,$cedula,$array_gustos,$array_deportes,$array_hobbies,$array_musica, $datos;
?>

<br>
	<form name="frmgarantia" id="frmgarantia" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data">

<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="500">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				GENERAR </span>
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

                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="center" class=rowform><table width="100%" border="0">
                    <tr>
                      <td>Almac&eacute;n Compra</td>
                      <td class="row2"><?php



					  if($tipo_factura=="cambio"){
						  $sql_datos_factura=db_query("Select * From Cambio Where IDCambio = '".$id."'");
						  $r_factura=db_fetch_array($sql_datos_factura);
					  }
					  else{

						  if ($_GET["tipofactura"]=="facturabono"){
							$sql_datos_factura=db_query("Select * From FacturaBono Where IDFacturaBono = '".$id."' and IDPuntoVenta = '".$IDPuntoVenta."'");
							$r_factura=db_fetch_array($sql_datos_factura);
						  }
						  else{
							  $sql_datos_factura=db_query("Select * From Factura Where IDFactura = '".$id."' and IDPuntoVenta = '".$IDPuntoVenta."'");
							$r_factura=db_fetch_array($sql_datos_factura);
						  }


					}

					  echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_factura[IDPuntoVenta]);
					  ?>
                      </td>
                      <td>Tel&eacute;fono Almacen</td>
                      <td class="row2"><?php
					  echo get_field("PuntoVenta","Telefono","IDPuntoVenta",$r_factura[IDPuntoVenta]);
					  ?></td>
                    </tr>
                    <tr>
                      <td>Cliente</td>
                      <td class="row2"><?php
					  $id_cliente=$r_factura[IDCliente];
					  echo get_field("Cliente","Nombre","IDCliente",$id_cliente) ." ". get_field("Cliente","Apellido","IDCliente",$id_cliente); ?></td>
                      <td>Tel&eacute;fono Cliente</td>
                      <td class="row2">&nbsp;<?php echo get_field("Cliente","Telefono","IDCliente",$id_cliente); ?></td>
                    </tr>


                    <?php if($_GET[numero_factura]=="mayorista" || $_GET[numero_factura]=="dotacion"): ?>
                        <tr>
                          <td>Nombre <?php if($_GET[numero_factura]=="mayorista") echo "Mayorista"; else echo "Trabajador";  ?></td>
                          <td class="row2"><input type="text" class="input" name="NombreMayorista" id="NombreMayorista" value="" /></td>
                          <td>Identificacion <?php if($_GET[numero_factura]=="mayorista") echo "Mayorista"; else echo "Trabajador";  ?></td>
                          <td class="row2"><input type="text" class="input" name="IdentificacionMayorista" id="IdentificacionMayorista" value="" /></td>
                        </tr>
                        <tr>
                          <td>Ciudad <?php if($_GET[numero_factura]=="mayorista") echo "Mayorista"; else echo "Trabajador";  ?></td>
                          <td  class="row2"><input type="text" class="input" name="CiudadMayorista" id="CiudadMayorista" value="" /></td>
						  <td>Direccion <?php if($_GET[numero_factura]=="mayorista") echo "Mayorista"; else echo "Trabajador";  ?></td>
                          <td class="row2"><input type="text" class="input" name="DireccionMayorista" id="DireccionMayorista" value="" /></td>
                        </tr>
                    <?php endif; ?>

					


                    <tr>
                      <td>Nuevo Telefono</td>
                      <td class="row2"><input type="text" class="input" name="Telefono" id="Telefono" value="" /></td>
                      <td>Nuevo Celular</td>
                      <td class="row2"><input type="text" class="input" name="Celular" id="Celular" value="" /></td>
                    </tr>
                    <tr>
                      <td>Factura de Venta N&ordm;</td>
                      <td class="row2"><?php echo $r_factura[NumeroFactura]; ?></td>
                      <td>Fecha Compra</td>
                      <td class="row2"><?php echo substr($r_factura[FechaFactura],0,10); ?></td>
                    </tr>

										<tr>
                      <td>Cambio N&ordm;</td>
                      <td class="row2"><?php echo $_GET[numero_cambio]; ?></td>
                      <td>Fecha Cambio</td>
                      <td class="row2"><?php
											$fecha_cambio=substr(get_field("Cambio","FechaCambio","IDCambio",$_GET[numero_cambio]),0,10);
											echo $fecha_cambio; ?></td>
                    </tr>


                    <tr>
                      <td>Fecha Reclamo</td>
                      <td class="row2"><?php echo date("Y-m-d"); ?></td>
                      <td>&nbsp;</td>
                      <td class="row2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td>Producto</td>
                      <td colspan="3">
                      <?php
					  // datos producto						
					  if($tipo_factura=="cambio"){
						  $datos_producto = explode("|",$id_producto);
						  $IDDetalleCambio = $datos_producto[0];
						  $IDCambio=$datos_producto[1];
						  $IDPuntoVenta=$datos_producto[2];

						  $r_detalle->IDDetalleFactura."|".$r_detalle->IDFactura."|".$r_detalle->IDPuntoVenta;
						  $sql_producto="select * from DetalleCambio Where IDDetalleCambio='".$IDDetalleCambio."' and IDCambio = '".$IDCambio."' and IDPuntoVenta = '".$IDPuntoVenta."'";
						  $qry_producto=db_query($sql_producto);
						  $r_detalle=db_fetch_object($qry_producto);

					  }
					  else{

					  $datos_producto = explode("|",$id_producto);
					  $IDDetalleFactura = $datos_producto[0];
					  $IDFactura=$datos_producto[1];
					  $IDPuntoVenta=$datos_producto[2];

					  $r_detalle->IDDetalleFactura."|".$r_detalle->IDFactura."|".$r_detalle->IDPuntoVenta;

					  if ($_GET["tipofactura"]=="facturabono"){
					  	$sql_producto="select * from DetalleFacturaBono Where IDDetalleFacturaBono='".$IDDetalleFactura."' and IDFacturaBono = '".$IDFactura."' and IDPuntoVenta = '".$IDPuntoVenta."'";
					  }
					  else{
						$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$IDDetalleFactura."' and IDFactura = '".$IDFactura."' and IDPuntoVenta = '".$IDPuntoVenta."'";
					  }

					  $qry_producto=db_query($sql_producto);
					  $r_detalle=db_fetch_object($qry_producto);
					  }

					  ?>

                      <table width="100%" border="0">
                        <tr>
                          <td>Referencia</td>
                          <td>Talla</td>
                          <td>Tipo</td>
                        </tr>
                        <tr bgcolor="#dfe3e7" class="texto forumline">
                          <td align="left" class="<?=$class?>">
                          <?php

						if($_GET[numero_factura]!="mayorista" && $_GET[numero_factura]!="dotacion"):

						  if($tipo_factura=="cambio"){
							$id_referencia_item=160;
						  }
						   else{
						  	$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
						   }

						  if ($id_referencia_item==160){ // Cuando son excedentes consulto la referencia de la compra


							$sql_facturabono=db_query("Select * from FacturaBono Where IDFactura = '".$id."' and IDPuntoVenta = '".$IDPuntoVenta."'");
							$r_facturabono=db_fetch_array($sql_facturabono);
							if (!empty($r_facturabono[IDFacturaBono])){
								$sql_detallefacturabono=db_query("Select * from DetalleFacturaBono Where IDFacturaBono = '".$r_facturabono[IDFacturaBono]."'");
								while($r_detallefacturabono=db_fetch_array($sql_detallefacturabono)){
									$id_referncia_bono=$r_detallefacturabono[IDCodificacionEspecifica];
									$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));
									$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										$array_bonos[]=$id_tipo_ref;
										$array_bono_especifico[]=$id_referncia_bono;

									?>
                                    <input type="radio" name="IDDetalleFacturaBono" value="<?php echo $r_detallefacturabono[IDFacturaBono] ."|" . $r_detallefacturabono[IDDetalleFacturaBono] ?>">
                                    <?php
									echo get_field("Referencia","Nombre","IDReferencia",$id_referencia_item) . "<br>";

								}

							}
							else{

								//Busco si es un cambio
								if(!empty($r_detalle->IDFactura)):
									$sql_cambio=db_query("Select C.* from Cambio C, DetalleCambio DC Where C.IDCambio = DC.IDCambio and IDFactura = '".$r_detalle->IDFactura."' and IDCliente = '".$id_cliente."' order by IDCambio Desc  limit 1");
								else:
									$sql_cambio=db_query("Select C.* from Cambio C, DetalleCambio DC Where C.IDCambio = DC.IDCambio and C.IDCambio = '".$r_detalle->IDCambio."' and IDCliente = '".$id_cliente."' order by IDCambio Desc  limit 1");
								endif;
								$r_cambio=db_fetch_array($sql_cambio);
								if (!empty($r_cambio[IDCambio])):
									$cambio="S";
									//$sql_detalle_cambio=db_query("Select * from DetalleCambio Where IDCambio = '".$r_cambio[IDCambio]."'");
									$sql_detalle_cambio=db_query("Select * from DetalleCambio Where IDCambio = '".$r_cambio[IDCambio]."' and IDDetalleCambio='".$IDDetalleCambio."'");
									while($r_detalle_cambio=db_fetch_array($sql_detalle_cambio)):
										$id_referncia_cambio=$r_detalle_cambio[IDCodificacionEspecifica];
										$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle_cambio[IDCodificacionEspecifica])));
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										$array_cambios[]=$id_tipo_ref;
										$array_especifica[]=$id_referncia_cambio;
										?>
                                        <input type="radio" name="IDDetalleCambio" value="<?php echo $r_detalle_cambio[IDCambio] ."|" . $r_detalle_cambio[IDDetalleCambio] ?>">
                                        <?php
									    echo get_field("Referencia","Nombre","IDReferencia",$id_referencia_item) . "<br>";

									endwhile;

								endif;
								//$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));


							}

						  }
						  else{

							  $id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
							  echo get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);

						  }

					  ?>


						<?php  if ($_GET[numero_factura]=="reproceso" || $_GET[numero_factura]=="mayorista" || $_GET[numero_factura]=="dotacion"){

							$sql_referencias = "SELECT R.IDReferencia, R.Numero, PVR.IDPuntoVentaReferencia
													FROM Referencia R, PuntoVentaReferencia PVR
													WHERE PVR.IDPuntoVenta = '".$datos[IDPuntoVenta]."'
													AND PVR.IDReferencia = R.IDReferencia
													AND R.Publicar <> 'N'
													ORDER BY R.Numero";

							/*
							$sql_referencias = "SELECT R.IDReferencia, R.Numero
													FROM Referencia R
													WHERE
													R.Publicar <> 'N'
													ORDER BY R.Numero";

							*/
							$qry_referencias = db_query( $sql_referencias );
							$i = 0;
							while( $r_referencias = db_fetch_array( $qry_referencias ) )
							{
								$array_referencias[$i] = $r_referencias;
								$i++;
							}//end while

						  ?>


		<select name="IDReferencia" id="IDReferencia" data-placeholder="Seleccione Referencias..." class="" style="width:150px;" tabindex="4">
        	<option value=""></option>
			<?php
                foreach( $array_referencias as $key=>$valor ){
						if(in_array($valor[IDReferencia],$array_referencia_guardados))
							$opcion_selecc=" selected ";
						else
							$opcion_selecc="";

                    echo "<option value='$valor[IDReferencia]' $opcion_selecc >$valor[Numero]</option>";
				}
            ?>
          </select>


							<?php }  ?>

                          <?php else: ?>

                          Referencia mayorista/dotacion
                              <input type="text" name="ColorMayorista" id="ColorMayorista" value="">

                          <?php endif; ?>

                          </td>
                          <td align="left" class="<?=$class?>">


                          <?php
							if (count($array_especifica)>0):
								foreach($array_especifica as $id_especifica):
									echo  get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$id_especifica)) . "<br>";
								endforeach;
						  elseif (count($array_bonos)>0):
								foreach($array_bonos as $id_especifica):
									echo  get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$id_especifica)) . "<br>";
								endforeach;

						  else:
						  	echo get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica));
						  endif;
						  ?>

						<?php  if ($_GET[numero_factura]=="reproceso" || $_GET[numero_factura]=="mayorista" || $_GET[numero_factura]=="dotacion"){ ?>
                                <select name="IDTalla" id="IDTalla" data-placeholder="Talla" class="" style="width:60px;" tabindex="5">
                                <option value=""></option>
                                    <?php
                                        $sql_tallas="Select IDTalla, Descripcion, Nombre From Talla Where Publicar = 'S' Group by Nombre Order by Descripcion";
                                        $qry_tallas = db_query( $sql_tallas );
                                        while( $r_tallas = db_fetch_array( $qry_tallas ) ){
                                            echo "<option value='$r_tallas[IDTalla]'>$r_tallas[Descripcion]</option>";
                                        }
                                    ?>
                                  </select>

							<?php }  ?>



                          </td>
                          <td align="left" class="<?=$class?>">
                          <?php if($_GET[numero_factura]=="mayorista" || $_GET[numero_factura]=="dotacion"): ?>
                          	<input type="text" name="TipoProductoMayorista" id="TipoProductoMayorista" value="">
                          <?php endif; ?>



                          <?php  if ($_GET[numero_factura]=="reproceso" || $_GET[Tipo]=="restauracion"){
						  	echo "&nbsp;";
						  }
						  else{ ?>

                          <?php
						  if (count($array_cambios)>0):
							foreach($array_cambios as $id_especifica):
								echo  get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_especifica) . "<br>";
							endforeach;
						  elseif (count($array_bonos)>0):
							foreach($array_bonos as $id_especifica):
								echo  get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_especifica) . "<br>";
							endforeach;

						  else:
						  	echo get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
						  endif;

							?>
                          <?php } ?>

                          </td>
                        </tr>

                      </table></td>
                    </tr>
                  </table></td>
                </tr>

								<?php
								if(!empty($IDDetalleFactura) && !empty($IDFactura) && !empty($IDPuntoVenta)){
								$sql_producto="select * from Garantia Where IDDetalleFactura='".$IDDetalleFactura."' and IDFactura = '".$IDFactura."' and IDPuntoVentaFactura = '".$IDPuntoVenta."'  ";
							  $qry_producto=db_query($sql_producto);
								if (db_num_rows($qry_producto)>0){
									$segunda_vez="S";
								}
							}
								 ?>

                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>
										<?php if($_GET["Tipo"]=="restauracion"){
											echo "Restauracion por: ";
										}
										else{
											echo "Garantia por";
										}
										?>
										</td>
                  <td align="left" class="row2"><input type="radio" name="CantidadVeces" id="CantidadVeces1"  value="1" class="" <?php  if($segunda_vez=="S" && $_GET["Tipo"]!="restauracion") echo "disabled='disabled'" ?>  />
<label for="CantidadVeces1" class="css-label radGroup2">Primera vez</label>
  <input type="radio" name="CantidadVeces" id="CantidadVeces2"  value="2" class="" <?php if($_GET["Tipo"]=="servicio") echo "disabled='disabled'" ?> />
<label for="CantidadVeces2" class="css-label radGroup2">Segunda Vez</label>
<input type="radio" name="CantidadVeces" id="CantidadVeces3"  value="3" class=""   <?php if($_GET["Tipo"]=="servicio") echo "disabled='disabled'" ?> />
<label for="CantidadVeces3" class="css-label radGroup2">Tercera Vez</label>

</td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Clasificacion</td>
                  <td align="left" class="row2">
                  <?php $id_proveedor=get_field("Referencia","IDProveedor","IDReferencia",$id_referencia_item); ?>			     
                  <input type="radio" class="" name="TipoProducto" id="TipoProducto1"  value="C"  <?php if ($_GET[numero_factura]=="mayorista" || $_GET[numero_factura]=="dotacion") echo "checked";?> <?php if($id_proveedor==19) echo "checked"; elseif($_GET[numero_factura]!="mayorista" && $_GET[numero_factura]!="reproceso" && $_GET[Tipo]!="restauracion" && $_GET[numero_factura]!="dotacion" ) echo "disabled"; ?>  />
				  <label for="TipoProducto1" class="css-label radGroup2">Es producto de Caprino</label>
  				  <input type="radio" class="" name="TipoProducto" id="TipoProducto2" value="T" <?php if ($_GET[numero_factura]=="mayorista") echo "disabled='disabled'";?> <?php if($id_proveedor==19) echo "disabled"; elseif($_GET[numero_factura]!="mayorista" && $_GET[numero_factura]!="dotacion" && $_GET[numero_factura]!="reproceso") echo "checked"; ?> />
  				  <label for="TipoProducto2" class="css-label radGroup2">Es producto de tercero</label>
				</td>
                </tr>



                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Numero Orden Produccion</td>
                  <td align="left" class="row2"><input type="text" class="input" name="NumeroOrdenProduccion" id="NumeroOrdenProduccion" value="" /></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td width="18%" align="left" class=rowform>Registro de </td>
                  <td width="82%" align="left" class="row2">
                  <?php
                  		$fecha_i = date("Y-m-d");
												$fecha_f = substr($r_factura[FechaFactura],0,10);
												if(empty($fecha_f) && !empty($fecha_cambio))
													$fecha_f=$fecha_cambio;


                        $dias_transcurridos= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
						$dias_transcurridos  = abs($dias_transcurridos);
						$dias_transcurridos = floor($dias_transcurridos);
						if ($dias_transcurridos>90 && !empty($fecha_f) &&  $_GET["Tipo"]!="restauracion"):
							$solo_servicio="S";
							?>
							<font color="#EE080C">Atencion!: Supera 90 dias para garantia </font><br>

              <?php endif;   ?>


                  		<input type="radio" name="TipoRegistro" id="TipoRegistro1" class="TipoRegistroGarantia " value="Garantia"  <?php if ( ($_GET[numero_factura]=="reproceso" && $_GET[numero_factura]!="mayorista" && $_GET[numero_factura]!="dotacion") || $solo_servicio=="S" || $_GET[Tipo]=="restauracion") echo "disabled='disabled'";?> <?php if ($_GET[numero_factura]=="mayorista" || $_GET[numero_factura]=="dotacion") echo "checked='checked'";?>  />
                        <label for="TipoRegistro1" class="css-label radGroup2">Garant&iacute;a</label>


                    <!--<input type="radio" name="TipoRegistro" id="TipoRegistro2" class="TipoRegistroGarantia " value="Servicio" <?php if ($_GET[numero_factura]=="reproceso") echo "disabled='disabled'";?>  />-->
										<input type="radio" name="TipoRegistro" id="TipoRegistro2" class="TipoRegistroGarantia " value="Restauracion" <?php if ($_GET[numero_factura]=="reproceso" || ( (int)$_GET["numero_factura"]>0 && $_GET["Tipo"]!="restauracion" && $_GET["Tipo"]!="" ) ) echo "disabled='disabled'";?>  <?php if ($_GET[Tipo]=="restauracion") echo "checked='checked'";?> />
                    <label for="TipoRegistro2" class="css-label radGroup2">Restauracion</label>
                    <input type="radio" name="TipoRegistro" id="TipoRegistro3" class="TipoRegistroGarantia " value="Reproceso" <?php if ($_GET[numero_factura]=="reproceso" ) echo "checked='checked'";?> <?php  if($solo_servicio=="S" || $_GET[Tipo]=="restauracion" || ( (int)$_GET["numero_factura"]>0 && $_GET["Tipo"]!="restauracion" )) echo "disabled='disabled'" ?>   />
					<label for="TipoRegistro3" class="css-label radGroup2">Reprocesos</label>


                <div id="divreproceso" style="display:none">
                  <table width="100%" cellpadding="2" cellspacing="1">
                    <tr>
                      <td width="8%"><table width="100%" cellpadding="2" cellspacing="1">
                        <tr>
                          <td>Remonta</td>
                          <td><input type="checkbox" name="Remonta" value="S" /></td>
                          <td>Valor $</td>
                          <td>&nbsp;</td>
                          <td width="14%"><input type="text" name="ValorRemonta" value="" size="10" /></td>
                          <td width="7%">&nbsp;</td>
                          <td width="18%">&nbsp;</td>
                          <td width="20%">&nbsp;</td>
                        </tr>
                        <tr>
                          <td colspan="4">El cliente acepta pagar el valor de la remonta</td>
                          <td colspan="2">Si
                            <input type="radio" name="PagoRemonta" value="S"  /></td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                        </tr>
                      </table></td>
                    </tr>
                  </table>
                </div>

								<?php
								if ($_GET["Tipo"]=="restauracion"){
									$style_resta="";
								}
								else{
									$style_resta="display:none";
								}
								?>

								<div id="divrestauracion" style="<?php echo $style_resta; ?>">
                  <table width="100%" cellpadding="2" cellspacing="1">
                    <tr>
                      <td width="8%">
												<table width="100%" cellpadding="2" cellspacing="1" border="0">
                        <tr>
                          <td colspan="2" bgcolor="#D5F0DA"><input type="checkbox" name="Basica" value="S" />B&aacute;sica</td>
                          <td bgcolor="#D5F0DA">Valor $</td>
                          <td bgcolor="#D5F0DA"><input type="text" name="ValorBasica" id="ValorBasica" value="" size="10" /></td>
                          <td bgcolor="#D5ECF0" width="20%" colspan="2"><input type="checkbox" name="Premium" value="S" />Premium</td>
                          <td bgcolor="#D5ECF0" width="15%">Valor $</td>
                          <td bgcolor="#D5ECF0" width="20%"><input type="text" name="ValorPremium" id="ValorPremium" value="" size="10" /></td>
                        </tr>
                        <tr>
                          <td colspan="5">El cliente acepta pagar el valor de la restauraci&oacute;n</td>
                          <td colspan="1">
                            Si<input type="radio" name="PagoRestauracion" id="PagoRestauracion1" value="S"  />
														No<input type="radio" name="PagoRestauracion" id="PagoRestauracion2" value="N"  />
													</td>
                          <td></td>
                          <td></td>
                        </tr>

												<tr>
                          <td colspan="5">Nro factura con la que pag&oacute;:</td>
                          <td colspan="1">
                            <input type="text" name="NumeroFacturaRestauracion" id="NumeroFacturaRestauracion"   />
													</td>
                          <td></td>
                          <td></td>
                        </tr>
                      </table></td>
                    </tr>
                  </table>
                </div>


                  </td>
                </tr>


                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform>DESCRIPCION DEL ESTADO EN EL QUE SE RECIBE EL PRODUCTO</td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform>
                  <table width="100%"  cellpadding="2" cellspacing="3">
                    <tr>
                      <td><strong>CUERO</strong></td>
                      <td class="row2">Pelados</td>
                      <td class="row2"><input type="checkbox"  name="CueroPelado" value="S" /></td>
                      <td><strong>SUELA</strong></td>
                      <td class="row2">Desgastada</td>
                      <td class="row2"><input type="checkbox" name="SuelaDesgastada" value="S" /></td>
                      <td><strong>OTROS</strong></td>
                      <td class="row2">Ojetes cedidos</td>
                      <td class="row2"><input type="checkbox" name="Ojetes" value="S" /></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td class="row2">Manchados</td>
                      <td class="row2"><input type="checkbox" name="CueroManchado" value="S" /></td>
                      <td>&nbsp;</td>
                      <td class="row2">Vira Da&ntilde;ada</td>
                      <td class="row2"><input type="checkbox" name="ViraDanada" value="S" /></td>
                      <td>&nbsp;</td>
                      <td class="row2">Punteras hundidas</td>
                      <td class="row2"><input type="checkbox" name="Punteras" value="S" /></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td class="row2">Rayados</td>
                      <td class="row2"><input type="checkbox" name="CueroRayado" value="S" /></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td colspan="2">
                      <textarea name="OtroDescripcion" id="OtroDescripcion" placeholder="Otro" rows="2"></textarea>

                      </td>
                    </tr>
                    <tr>
                      <td height="27"><strong>FORRO</strong></td>
                      <td class="row2">Manchado</td>
                      <td class="row2"><input type="checkbox" name="ForroManchado" value="S" /></td>
                      <td><strong>TAC&Oacute;N</strong></td>
                      <td class="row2">Desgastado</td>
                      <td class="row2"><input type="checkbox" name="TaconDesgastado" value="S" /></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td class="row2">Roto</td>
                      <td class="row2"><input type="checkbox" name="ForroRoto" value="S" /></td>
                      <td>&nbsp;</td>
                      <td class="row2">Pelado/Rayado</td>
                      <td class="row2"><input type="checkbox" name="TaconPelado" value="S" /></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform>DESCRIPCION DETALLADA DE LA SITUACION</td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class="rowform"><span class="row2">
                    <input type="text" name="Descripcion" id="Descripcion" size="60" maxlength="40"  class="input">
                  </span></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class="rowform">COMENTARIOS CLIENTE</td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class="row2"><textarea name="ComentarioCliente" id="ComentarioCliente" cols="80" rows="5"></textarea></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform><table width="100%" border="0">
                    <tr>
                      <td><?php
					  // datos producto
					  $datos_producto = explode("|",$id_producto);
					  $IDDetalleFactura = $datos_producto[0];
					  $IDFactura=$datos_producto[1];
					  $IDPuntoVenta=$datos_producto[2];

					  $r_detalle->IDDetalleFactura."|".$r_detalle->IDFactura."|".$r_detalle->IDPuntoVenta;
					  $sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$IDDetalleFactura."' and IDFactura = '".$IDFactura."' and IDPuntoVenta = '".$IDPuntoVenta."'";
					  $qry_producto=db_query($sql_producto);
					  $r_detalle=db_fetch_object($qry_producto);
					  ?>
                        <table width="100%" border="0">
                          <tr>
                            <td>Referencia</td>
                            <td>Talla</td>
                          </tr>
                          <tr bgcolor="#dfe3e7" class="texto forumline">
                            <td align="left" class="<?=$class?>">&nbsp;<%=get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))%></td>
                            <td align="left" class="<?=$class?>">&nbsp;<%=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))%></td>
                          </tr>
                        </table></td>
                    </tr>
                  </table></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Atendido por:</td>
                  <td align="left" class="row2"><span class="col2">
                  <?php if($_GET[numero_factura]=="mayorista" || $_GET[numero_factura]=="dotacion"): ?>
                  	<input type="text" name="IngresadoPor" id="IngresadoPor" value="" required>
                  <?php else: ?>
					  <? echo formpopup("Empleado WHERE Publicar = 'S' and IDPuntoVenta = '".$datos[IDPuntoVenta]."' ","Nombre","Apellidos","IDEmpleado",$r->IDEmpleado,"input\" id=\"Empleado"); ?></span>
                  <?php endif; ?>


                  </td>
                </tr>


			<tr>
			<td colspan=2 align=center class="col2list">
            	<input type=hidden name=IDFactura id=IDFactura value="<?=$id ?>">
                <input type=hidden name=IDDetalleFactura id=IDDetalleFactura value="<?=$IDDetalleFactura ?>">
                <input type=hidden name=IDPuntoVenta id=IDPuntoVenta value="<?=$datos[IDPuntoVenta] ?>">
                <input type=hidden name=IDPuntoVentaFactura id=IDPuntoVentaFactura value="<?=$IDPuntoVenta; ?>">
                <input type=hidden name=Mayorista id=Mayorista value="<?php if($_GET[numero_factura]=="mayorista") echo "S"; ?>">
				<input type=hidden name=Dotacion id=Dotacion value="<?php if($_GET[numero_factura]=="dotacion") echo "S"; ?>">
                <input type=hidden name=TipoFactura id=TipoFactura value="<?php echo $_GET[tipofactura]; ?>">
                <input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
				<input type=hidden name=action value=<?=$newmode?>>
                <input type="hidden" name="Cambios" id="Cambios" value="<?php echo count($array_cambios); ?>">
                <input type="hidden" name="Bonos" id="Bonos" value="<?php echo count($array_bonos); ?>">
				<?php //if ($dias_transcurridos<=90): ?>
                <input type=submit name=submit value="<? echo $submit_caption ?>" class=submit >
                <?php //endif; ?>



			</td>
				</tr>
			</table>
		</td>
	</tr>
</table>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
  <script src="admin/jscripts/choosen/chosen.jquery.js" type="text/javascript"></script>
  <script src="admin/jscripts/choosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>

</form>
	<?
}// End function print_formgarantia()



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/

function print_form($id,$newmode,$title,$submit_caption,$frm=""){
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $IVA,$IDPuntoVenta;

	$qid = db_query(" SELECT * FROM Cliente WHERE IDCliente = '$id' ");

	$r = db_fetch_object($qid);
?>

<script language="JavaScript">
<!--


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

function selreferencia(REFERENCIA, NOMBRE, TALLA, CODIFICACION, CONT, MAXIMO, VALORU, DESCUENTOREF){
	var items_total=0;
	Borrar(CONT);

	document.frm.elements["Numero"+CONT].value = REFERENCIA;
	document.frm.elements["Nombre"+CONT].value = NOMBRE;
	document.frm.elements["Talla"+CONT].value = TALLA;
	document.frm.elements["IDCodificacion"+CONT].value = CODIFICACION;

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
		document.frm.elements["CodigoTarjeta"+CONT].style.display = "block";
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


function setcodigotarjeta(CODIGO, CONT){

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

	for(i=1;i<= document.frm.ITEM.value;i++){

		if(document.frm.elements["ValorU"+i].value  != '' && document.frm.elements["Cantidad"+i].value != '')
		{


			if( document.frm.elements["DescuentoLin"+i].value != '' )
			{
				valorui = getNum(document.frm.elements["ValorU"+i].value ) * ( 1 - ( getNum( document.frm.elements["DescuentoLin"+i].value ) / 100 ) );
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

	document.frm.elements["ValorTotal"].value = TotalFactura;
	formatCurrency(document.frm.elements["ValorTotal"]);

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
	document.frm.elements["Descuento"+contador].value = "";
	document.frm.elements["DescuentoLin"+contador].value = "";
	pague_2_lleve_3();
	recalcularvalores();

}//end function

	-->
</script>
<script>
var Check = new Array('NumeroFactura','NumeroDocumento','IDPuntoVenta','IDCliente','IDEmpleado', 'Cantidad1', 'Nombre1', 'ValorTotal');
</script>
<br>
<?
} // END function print_form_fotos($id,$numfotos)
?>
</BODY></HTML>
