<body>
<script>
function imprimir_garantia(IDGarantia, IDPuntoVenta){
	window.open("Garantia/popBoucherGarantia.php?id="+IDGarantia+"&idpunto="+IDPuntoVenta,"","width=550, height=350, scrollbars=yes");
}
</script>
<?php
$TitleMod ="Seguimiento Garantia";

$Table = "Garantia";
$TableJoin = "";
$Key = "IDGarantia";
$MOD = "SeguimientoGarantia";
$m = "Factura";




		$permisos = get_permiso($ID_Usuario,$m,"Factura");


if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;

			case "insertarcomentario":
				$frm= vars_LOG($HTTP_POST_VARS);


				if ($frm[FechaSalidaAlmacen]!="0000-00-00" && $frm[FechaSalidaAlmacen]!="" && $frm[FechaSalidaAlmacen] != $frm[FechaSalidaAlmacenAnt])
					$txt_cambio_fecha.=" Cambi&oacute; fecha Salida Almacen por: " . $frm[FechaSalidaAlmacen];
				if ($frm[FechaEntradaAlmacen]!="0000-00-00" && $frm[FechaEntradaAlmacen]!="" && $frm[FechaEntradaAlmacen] != $frm[FechaEntradaAlmacenAnt])
					$txt_cambio_fecha.=" Cambi&oacute; fecha Entrada Almacen por: " . $frm[FechaSalidaAlmacen];
				if ($frm[FechaEntregaCliente]!="0000-00-00" && $frm[FechaEntregaCliente]!="" && $frm[FechaEntregaCliente] != $frm[FechaEntregaClienteAnt])
					$txt_cambio_fecha.=" Cambi&oacute; fecha Entrega Cliente por: " . $frm[FechaSalidaAlmacen];

				if (!empty($frm[Descripcion])  || !empty($txt_cambio_fecha) ){
					$sql_inserta_comentario="INSERT INTO ComentarioGarantia (IDGarantia, IDEmpleado, IDEstadoGarantia, Descripcion, FechaComentario, UsuarioTrCr, FechaTrCr) Values ('".$frm[IDGarantia]."','".$ID_Usuario."','".$frm[IDEstadoGarantia]."','".$frm[Descripcion] . "\r" .$txt_cambio_fecha ."',NOW(),'".$ID_Usuario."',NOW())";
					$qry_inserta_comentario=db_query($sql_inserta_comentario);
					//actualizo el estado de la garantia

					if ($frm[IDEstadoGarantia] == "2" || $frm[IDEstadoGarantia] == "3" || $frm[IDEstadoGarantia] == "4")
						$frm[FechaSalidaAlmacen]=date("Y-m-d");

					if ($frm[IDEstadoGarantia] == "6" || $frm[IDEstadoGarantia] == "8")
						$frm[FechaEntradaAlmacen]=date("Y-m-d");

					if ($frm[IDEstadoGarantia] == "9"){
						$frm[FechaEntregaCliente]=date("Y-m-d");
					}



					if ($frm[TipoProductoGarantia]=="T"):
								  $sql_actualiza_tipo="Update Garantia
											SET TipoContrafuerte = '".$frm[TipoContrafuerte]."',
											TipoCuero = '".$frm[TipoCuero]."',
											TipoPlantilla = '".$frm[TipoPlantilla]."',
											TipoCremallera = '".$frm[TipoCremallera]."',
											TipoDespegue = '".$frm[TipoDespegue]."',
											TipoCambrion = '".$frm[TipoCambrion]."',
											TipoTacon = '".$frm[TipoTacon]."',
											TipoCerco = '".$frm[TipoCerco]."',
											TipoCardado = '".$frm[TipoCardado]."',
											TipoSuela = '".$frm[TipoSuela]."',
											TipoGuarnicion = '".$frm[TipoGuarnicion]."',
											TipoPuntera = '".$frm[TipoPuntera]."',
											TipoHerraje = '".$frm[TipoHerraje]."',
											TipoOtro = '".$frm[TipoOtro]."'
											Where IDGarantia = '".$frm[IDGarantia]."' ";
							db_query($sql_actualiza_tipo);



					endif;

					$sql_actualiza_estado="UPDATE Garantia SET IDEstadoGarantia = '".$frm[IDEstadoGarantia]."', FechaSalidaAlmacen = '".$frm[FechaSalidaAlmacen]."' , FechaEntradaAlmacen = '".$frm[FechaEntradaAlmacen]."', FechaEntregaCliente = '".$frm[FechaEntregaCliente]."', NumeroGuia = '".$frm[NumeroGuia]."', NumeroFacturaRestauracion= '".$frm[NumeroFacturaRestauracion]."' Where IDGarantia = '".$frm[IDGarantia]."'";
					$qry_actualiza_estado=db_query($sql_actualiza_estado);
					// Envio notificacion
					envia_comentario_garantia($id,$frm,$ID_Usuario);
					/*
					if ($frm[IDEstadoGarantia]=="3" || $frm[IDEstadoGarantia]=="4"){
						envia_comentario_tercero($id,$frm,$ID_Usuario);
					}
					*/
					window_alert("Comentario agregado con exito ");
					if ($frm[IDEstadoGarantia] == "9"){
					// Abro ventana con recibo
					echo "<script>window.open('Garantia/popBoucherGarantia.php?id=".$frm[IDGarantia]."&idpunto=".$frm[IDPuntoVenta]."','','width=550, height=350, scrollbars=yes');
						  location.href='?mod=Garantia&action=edit&id=".$id."';</script>";

					}

				}

				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;



			case "list":

				if($field == "NumeroFactura")
					$condiciones=" and F.Numerofactura LIKE '%$QryString%'";
				elseif($field == "IDGarantia")
					$condiciones=" and G.IDGarantia = '$QryString'";
				elseif($field == "Cedula")
					$condiciones=" and C.Cedula like '$QryString'";
				elseif($field == "NombreGarantia")
					$condiciones=" and TG.Nombre LIKE '%$QryString%'";
				elseif($field == "EstadoNombre")
					$condiciones=" and EG.Nombre LIKE '%$QryString%'";

				if (!empty($_GET[limit1]) && !empty($_GET[limit2]) )
					$condiciones=" and G.FechaTrCr between '".$_GET[limit1]."' and '".$_GET[limit2]."'";



				/*
				 $sql = " SELECT G.*, C.*,EG.Nombre
							 FROM Garantia G, EstadoGarantia EG,  Cliente C, Factura F
							 WHERE G.IDFactura = F.IDFactura and C.IDCliente = F.IDCliente and
							 	   EG.IDEstadoGarantia = G.IDEstadoGarantia

								   $condiciones
							ORDER BY ".$order_by. " " . $in_order;
				*/

				$sql = " SELECT G.*,EG.Nombre
							 FROM Garantia G, EstadoGarantia EG
							 WHERE EG.IDEstadoGarantia = G.IDEstadoGarantia

								   $condiciones
							ORDER BY ".$order_by. " " . $in_order;

				list_r($sql);

			break;
			default :

				list_r($sql);
			break;

		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id,$newmode,$title,$submit_caption){
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $IVA,$IDPuntoVenta;

	$qid = db_query(" SELECT * FROM Garantia WHERE IDGarantia = '$id' AND IDPuntoVenta = '$IDPuntoVenta' ");

	$r = db_fetch_object($qid);
?>


<script>
function Comprobar(formulario)
{
	var guia=document.getElementById('NumeroGuia').value;
	if (guia.length==0)
	{
		alert("Numero de Guia o  Persona a quien entrega es obligatorio.");
		return false;
	}
}


</script>

<br>
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="550">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				<?php echo $title?>
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<form name="frmdetalle" id="frmdetalle" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" onsubmit="return Comprobar();">

<table class="forumline" width="550" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<table width=100% border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff" >

				<tr >
					<td colspan="2">

								<div align="center">
									<table width=100% border=0>
										<tr>
											<td width="250" colspan="4">
												<table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline"  >

                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="center" class=rowform><table width="100%" border="0">
                    <tr>
                      <td width="42%">REGISTRO NUMERO</td>
                      <td colspan="3" class="row2"><?php echo $r->IDGarantia; ?></td>
                      </tr>
                    <tr>
                      <td>ESTADO</td>
                      <td colspan="3" class="row2">
                      <span style="color:#F00">
                      <?php echo strtoupper(get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$r->IDEstadoGarantia)); ?></span>
                      </td>
                    </tr>
                    <tr>
                      <td>TIPO AUTORIZACION</td>
                      <td colspan="3" class="row2"><span style="color:#F00"><?php echo strtoupper(get_field("TipoFinalizacionGarantia","Nombre","IDTipoFinalizacionGarantia",$r->IDTipoFinalizacionGarantia)); ?>
                        <?php if ($r->RequiereDevolucion=="S") echo "(Se requiere devolver producto no aceptado  a fabrica)"?>
                      </span></td>
                    </tr>
                    <tr>
                      <td>Fecha Estimada para resolver garantia</td>
                      <td colspan="3" class="row2"><span class="<?php echo $class?>"><?php echo formatofecha(substr($r->FechaEstimadaEntrega,0,10)) ?></span></td>
                    </tr>
                    <tr>
                      <td>Almac&eacute;n Compra</td>
                      <td width="19%" class="row2"><?php

					  if ($r->TipoFactura=="facturabono"):
					  	$sql_datos_factura=db_query("Select * From FacturaBono Where IDFacturaBono = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
					  else:
						$sql_datos_factura=db_query("Select * From Factura Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
					  endif;

					  $r_factura_compra=db_fetch_array($sql_datos_factura);



					  $id_punto_venta=$r->IDPuntoVentaFactura;
					  echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_punto_venta);
					  ?>
                      </td>
                      <td width="18%">Tel. Almacen</td>
                      <td width="21%" class="row2"><?php
					  echo get_field("PuntoVenta","Telefono","IDPuntoVenta",$id_punto_venta);
					  ?></td>
                    </tr>
                    <tr>
                      <td>Almac&eacute;n Registra Garantia</td>
                      <td class="row2"><?php
					  echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta);
					  ?></td>
                      <td>Tel.</td>
                      <td class="row2"><?php
					  echo get_field("PuntoVenta","Telefono","IDPuntoVenta",$r->IDPuntoVenta);
					  ?></td>
                    </tr>
                    <tr>
                      <td>Cliente</td>
                      <td class="row2"><?php

									if ($r->TipoRegistro=="Reproceso"){
										$id_proveedor=get_field("Referencia","IDProveedor","IDReferencia",$r->IDReferencia);
										echo get_field("Proveedor","Nombre","IDProveedor",$id_proveedor);

									}
									elseif($r->Mayorista=="S"){
										echo $r->NombreMayorista;
									}
									else{
										  $id_cliente=$r_factura_compra[IDCliente];
										  echo get_field("Cliente","Nombre","IDCliente",$id_cliente) ." ". get_field("Cliente","Apellido","IDCliente",$id_cliente) .  " - " . get_field("Cliente","Cedula","IDCliente",$id_cliente);
									}

						  ?>

                          </td>
                      <td>Tel. Cliente</td>
                      <td class="row2">&nbsp;<?php echo get_field("Cliente","Telefono","IDCliente",$id_cliente); ?></td>
                    </tr>
                    <tr>
                      <td>Ciudad</td>
                      <td class="row2"><?php echo $r->CiudadMayorista;; ?></td>
                      <td>Direccion Mayorista</td>
                      <td class="row2"><?php echo $r->DireccionMayorista;; ?></td>
                    </tr>
                    <tr>
                      <td>Telefono1</td>
                      <td class="row2"><?php echo $r->Telefono; ?></td>
                      <td>Celular</td>
                      <td class="row2"><?php echo $r->Celular; ?></td>
                    </tr>
                    <tr>
                      <td>Factura de Venta N&ordm;</td>
                      <td class="row2"><?php
					  if ($r->TipoFactura=="facturabono"):
						  echo $r_factura_compra[NumeroFacturaBono] . " (bono) ";
					  else:
					  	  echo $r_factura_compra[NumeroFactura];
					  endif;
					?>

                      </td>
                      <td>Fecha Compra</td>
                      <td class="row2"><?php
					  if ($r->TipoFactura=="facturabono"):
					  	echo substr(get_field("FacturaBono","FechaFacturaBono","IDFacturaBono",$r->IDFactura),0,10);
					  else:
					  	echo substr(get_field("Factura","FechaFactura","IDFactura",$r->IDFactura),0,10);
                      endif;
					  ?>
                      </td>

                    </tr>
                    <tr>
                      <td>Fecha Reclamo</td>
                      <td colspan="3" class="row2"><?php echo $r->FechaTrCr; ?></td>
                      </tr>
                    <tr>
                      <td>Producto</td>
                      <td colspan="3">
                      <?php
					  // datos producto
					  if ($r->TipoFactura=="facturabono"):
					  	$sql_producto="select * from DetalleFacturaBono Where IDDetalleFacturaBono='".$r->IDDetalleFactura."' and IDFacturaBono = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'";
					  else:
						$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'";
					  endif;


					  $qry_producto=db_query($sql_producto);
					  $r_detalle=db_fetch_object($qry_producto);
					  ?>

                      <table width="100%" border="0">
                        <tr>
                          <td>Referencia</td>
                          <td>Talla</td>
                          <td>Tipo</td>
                        </tr>
                        <tr bgcolor="#dfe3e7" class="texto forumline">
                          <td align="left" class="<?php echo $class?>">&nbsp;
                          <?php  if ($r->TipoRegistro=="Reproceso" ||$r->Mayorista=="S"){
									echo $nombre_referencia=get_field("Referencia","Numero","IDReferencia",$r->IDReferencia);

						  } elseif(!empty($r->IDDetalleFacturaBono)){

							  $array_bono_detalle=explode("|",$r->IDDetalleFacturaBono);
									if (count($array_bono_detalle)>0):
										$sql_bono=db_query("Select * From DetalleFacturaBono Where IDDetalleFacturaBono	 = '".$array_bono_detalle[1]."' and IDFacturaBono = '".$array_bono_detalle[0]."'");
										$r_bono=db_fetch_array($sql_bono);

								   		$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"])));
										$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
									endif;
						  }
						  elseif(empty($r->IDDetalleCambio)){

							   ?>
                         			 <?php

									$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))) ;
									if ($id_referencia_item==160){ // Cuando son excedentes consulto la referencia de la compra

										$sql_facturabono=db_query("Select * from FacturaBono Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
										$r_facturabono=db_fetch_array($sql_facturabono);
										if (!empty($r_facturabono[IDFacturaBono])){
											$sql_detallefacturabono=db_query("Select * from DetalleFacturaBono Where IDFacturaBono = '".$r_facturabono[IDFacturaBono]."'");
											$r_detallefacturabono=db_fetch_array($sql_detallefacturabono);
											$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));
											$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"]));
										}
									  }
									 $id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
									 echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);

									 ?>


                               <?php }
							   else{ // ES UNA REFERNCIA DE ALGUN CAMBIO Y LA CONSULTO
							   		$array_cambio_detalle=explode("|",$r->IDDetalleCambio);
									if (count($array_cambio_detalle)>0):
										$sql_cambio=db_query("Select * From DetalleCambio Where IDDetalleCambio = '".$array_cambio_detalle[1]."' and IDCambio = '".$array_cambio_detalle[0]."'");
										$r_cambio=db_fetch_array($sql_cambio);

								   		$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"])));
										$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"]));
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
									endif;

								}
							   ?>

                               <?php if($r->Mayorista=="S"):
						  				echo $r->ColorMayorista;
									  endif;
								?>


                          </td>
                          <td align="left" class="<?php echo $class?>">&nbsp;

						  <?php  if ($r->TipoRegistro=="Reproceso" || $r->Mayorista=="S"){
									echo $nombre_talla=get_field("Talla","Nombre","IDTalla",$r->IDTalla);

						  } elseif($nombre_talla!=""){ echo $nombre_talla; } else {?>
                         			 <?php echo $nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))?></td>
                               <?php } ?>


                          <td align="left" class="<?php echo $class?>">


						<?php  if ($r->TipoRegistro=="Reproceso"){
									$tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$r->IDReferencia);;
									echo get_field("TipoReferencia","Descripcion","IDTipoReferencia",$tipo_ref);

						  }
						  elseif($r->Mayorista=="S"){
								echo  $r->TipoProductoMayorista;
						  }

						   else{ ?>
                         			 <?php echo get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref); ?>

                               <?php } ?>


                          </td>
                        </tr>
                      </table></td>
                    </tr>
                  </table></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Garantia por</td>
                  <td align="left" class="row2"><input type="radio" class="css-checkbox" name="CantidadVeces" id="CantidadVeces1"  value="1" <?php if($r->CantidadVeces=="1") { echo "checked"; } ?>   disabled  />
                    <label for="CantidadVeces1" class="css-label radGroup2">Primera vez</label>
                    <input type="radio" class="css-checkbox" name="CantidadVeces" id="CantidadVeces2"  value="2" <?php if($r->CantidadVeces=="2") { echo "checked"; } ?> disabled  />
                    <label for="CantidadVeces2" class="css-label radGroup2">Segunda Vez</label>
                    <input type="radio" class="css-checkbox" name="CantidadVeces" id="CantidadVeces3"  value="3" <?php if($r->CantidadVeces=="3") { echo "checked"; } ?> disabled  />
                    <label for="CantidadVeces3" class="css-label radGroup2">Tercera Vez</label>
                    </td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Clasificacion</td>
                  <td align="left" class="row2"><input type="radio" class="css-checkbox" name="TipoProducto" id="TipoProducto1"  value="C" <?php if($r->TipoProducto=="C") { echo "checked"; } ?> disabled />
                    <label for="TipoProducto1" class="css-label radGroup2">Es producto de Caprino</label>
                    <input type="radio" name="TipoProducto" id="TipoProducto2" class="css-checkbox"  value="T" <?php if($r->TipoProducto=="T") { echo "checked"; } ?> disabled />
                    <label for="TipoProducto2" class="css-label radGroup2">Es producto de tercero</label> </td>
                </tr>



                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Numero Orden Produccion</td>
                  <td align="left" class="row2"><?php echo $r->NumeroOrdenProduccion; ?></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td width="18%" align="left" class=rowform>Registro de </td>
                  <td width="82%" align="left" class="row2">
                    <input type="radio" name="TipoRegistro" id="TipoRegistro1" class="TipoRegistroGarantia css-checkbox" value="Garantia" <?php if($r->TipoRegistro=="Garantia") { echo "checked"; } ?>   disabled  />
                    <label for="TipoRegistro1" class="css-label radGroup2">Garant&iacute;a</label>
                    <input type="radio" name="TipoRegistro" id="TipoRegistro2" class="TipoRegistroGarantia css-checkbox" value="Restauracion" <?php if($r->TipoRegistro=="Restauracion") { echo "checked"; } ?> disabled  />
                    <label for="TipoRegistro2" class="css-label radGroup2">Restauracion</label>
                    <input type="radio" name="TipoRegistro" id="TipoRegistro3" class="TipoRegistroGarantia css-checkbox" value="Reproceso" <?php if($r->TipoRegistro=="Reproceso") { echo "checked"; } ?> disabled  />
                    <label for="TipoRegistro3" class="css-label radGroup2">Reprocesos</label>



                    <?php if($r->TipoRegistro=="Servicio" || $r->TipoRegistro=="Restauracion"){ ?>
                    <div id="divreproceso">
                      <table width="100%" cellpadding="2" cellspacing="1">
                        <tr>
                          <td>Remonta</td>
                          <td><input type="checkbox" name="Remonta" value="Rem" <?php if($r->Remonta=="S"){ echo "checked"; } ?> disabled /></td>
                          <td>Valor</td>
                          <td>$<?php echo number_format($r->ValorRemonta); ?></td>
                          <td width="14%">Basica: <?php echo $r->Basica; ?></td>
                          <td width="5%">Valor: <?php echo $r->ValorBasica; ?></td>
                          <td width="12%">Premium: <?php echo $r->Premium; ?></td>
                          <td width="27%">Valor: <?php echo $r->ValorPremium; ?></td>
                          </tr>
                        <tr>
                          <td colspan="4">El cliente acepta pagar el valor de la restauracion</td>
                          <td colspan="2">Si
                            <input type="radio" name="PagoRemonta" value="S" <?php if($r->PagoRemonta=="S"){ echo "checked"; } ?> disabled  /></td>
													<!--
													<td>Numero Factura Restauracion</td>
                          <td><?php echo $r->NumeroFacturaRestauracion; ?></td>
												-->
                          </tr>
                        </table>

                      </div>
                    <?php } ?>


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
                      <td class="row2">					  
					  <input type="checkbox" class="" name="CueroPelado" value="S" <?php if ($r->CueroPelado=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Pelados</label></td>
                      <td class="row2">&nbsp;</td>
                      <td><strong>SUELA</strong></td>
                      <td class="row2"><input type="checkbox" class="" name="SuelaDesgastada" value="S" <?php if ($r->SuelaDesgastada=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Desgastada</label></td>
                      <td class="row2">&nbsp;</td>
                      <td><strong>OTROS</strong></td>
                      <td class="row2"><input type="checkbox" class="" name="Ojetes" value="S" <?php if ($r->Ojetes=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Ojetes cedidos</label></td>
                      <td class="row2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td class="row2"><input type="checkbox" class="" name="CueroManchado" value="S" <?php if ($r->CueroManchado=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Manchados</label></td>
                      <td class="row2">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td class="row2"><input type="checkbox" class="" name="ViraDanada" value="S" <?php if ($r->ViraDanada=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Vira Da&ntilde;ada</label></td>
                      <td class="row2">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td class="row2"><input type="checkbox" class="" name="Punteras" value="S" <?php if ($r->Punteras=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Punteras hundidas</label></td>
                      <td class="row2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td class="row2"><input type="checkbox" class="" name="CueroRayado" value="S" <?php if ($r->CueroRayado=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Rayados</label></td>
                      <td class="row2">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td height="27"><strong>FORRO</strong></td>
                      <td class="row2"><input type="checkbox" class="" name="ForroManchado" value="S" <?php if ($r->ForroManchado=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Manchado</label></td>
                      <td class="row2">&nbsp;</td>
                      <td><strong>TAC&Oacute;N</strong></td>
                      <td class="row2"><input type="checkbox" class="" name="TaconDesgastado" value="S" <?php if ($r->TaconDesgastado=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Desgastado</label></td>
                      <td class="row2">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td><textarea name="OtroDescripcion" id="OtroDescripcion" placeholder="Otro" rows="2" disabled><?php echo $r->OtroDescripcion ?></textarea></td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td class="row2"><input type="checkbox" class="" name="ForroRoto" value="S" <?php if ($r->ForroRoto=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Roto</label></td>
                      <td class="row2">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td class="row2"><input type="checkbox" class="" name="TaconPelado" value="S" <?php if ($r->TaconPelado=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Pelado/Rayado</label></td>
                      <td class="row2">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
                </tr>

                <?php if($r->TipoProducto=="T"): ?>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform>IDENTIFICACION DE LA CAUSA DE LA GARANTIA </td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform><table width="100%"  cellpadding="2" cellspacing="3">
                    <tr>
                      <td class="row2"><input type="checkbox" class="" name="TipoContrafuerte" id="TipoContrafuerte" value="S" <?php if ($r->TipoContrafuerte=="S"){ echo "checked"; } ?>  />
                        <label for="TipoContrafuerte" class="css-label2">Contrafuerte</label></td>
                      <td class="row2"><input type="hidden" name="tmpContrafuerte" id="tmpContrafuerte" value="<?php echo $r->TipoContrafuerte; ?>"></td>
                      <td ><input type="checkbox" class="" name="TipoDespegue" id="TipoDespegue" value="S" <?php if ($r->TipoDespegue=="S"){ echo "checked"; } ?>  />
                        <label for="TipoDespegue" class="css-label2">Despegue</label></td>
                      <td ><input type="hidden" name="tmpTipoDespegue" id="tmpTipoDespegue" value="<?php echo $r->TipoDespegue; ?>"></td>
                      <td class="row2"><input type="checkbox" class="" name="TipoCardado" id="TipoCardado" value="S" <?php if ($r->TipoCardado=="S"){ echo "checked"; } ?>  />
                        <label for="TipoCardado" class="css-label2">Cardado</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoCardado" id="tmpTipoCardado" value="<?php echo $r->TipoCardado; ?>"></td>
                    </tr>
                    <tr>
                      <td class="row2"><input type="checkbox" class="" name="TipoCuero" id="TipoCuero" value="S" <?php if ($r->TipoCuero=="S"){ echo "checked"; } ?>  />
                        <label for="TipoCuero" class="css-label2">Cuero</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoCuero" id="tmpTipoCuero" value="<?php echo $r->TipoCuero; ?>"></td>
                      <td ><input type="checkbox" class="" name="TipoCambrion" id="TipoCambrion" value="S" <?php if ($r->TipoCambrion=="S"){ echo "checked"; } ?>  />
                        <label for="TipoCambrion" class="css-label2">Cambrion</label></td>
                      <td><input type="hidden" name="tmpTipoCambrion" id="tmpTipoCambrion" value="<?php echo $r->TipoCambrion; ?>"></td>
                      <td class="row2"><input type="checkbox" class="" name="TipoSuela" id="TipoSuela" value="S" <?php if ($r->TipoSuela=="S"){ echo "checked"; } ?>  />
                        <label for="TipoSuela" class="css-label2">Suela Rota</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoSuela" id="tmpTipoSuela" value="<?php echo $r->TipoRemonta; ?>"></td>
                    </tr>
                    <tr>
                      <td class="row2"><input type="checkbox" class="" name="TipoPlantilla" id="TipoPlantilla" value="S" <?php if ($r->TipoPlantilla=="S"){ echo "checked"; } ?>  />
                        <label for="TipoPlantilla" class="css-label2">Plantilla estructural</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoPlantilla" id="tmpTipoPlantilla" value="<?php echo $r->TipoPlantilla; ?>"></td>
                      <td ><input type="checkbox" class="" name="TipoTacon" id="TipoTacon" value="S" <?php if ($r->TipoTacon=="S"){ echo "checked"; } ?>  />
                        <label for="TipoTacon" class="css-label2">Tacon</label></td>
                      <td ><input type="hidden" name="tmpTipoTacon" id="tmpTipoTacon" value="<?php echo $r->TipoTacon; ?>"></td>
                      <td class="row2"><input type="checkbox" class="" name="TipoGuarnicion" id="TipoGuarnicion" value="S" <?php if ($r->TipoGuarnicion=="S"){ echo "checked"; } ?>  />
                        <label for="TipoGuarnicion" class="css-label2">Guarnicion</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoGuarnicion" id="tmpTipoGuarnicion" value="<?php echo $r->TipoGuarnicion; ?>"></td>
                    </tr>
                    <tr>
                      <td height="27" class="row2"><input type="checkbox" class="" name="TipoCremallera" id="TipoCremallera" value="S" <?php if ($r->TipoCremallera=="S"){ echo "checked"; } ?>  />
                        <label for="TipoCremallera" class="css-label2">Cremallera</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoCremallera" id="tmpTipoCremallera" value="<?php echo $r->TipoCremallera; ?>"></td>
                      <td ><input type="checkbox" class="" name="TipoCerco" id="TipoCerco" value="S" <?php if ($r->TipoCerco=="S"){ echo "checked"; } ?>  />
                        <label for="TipoCerco" class="css-label2">Cerco</label></td>
                      <td><input type="hidden" name="tmpTipoCerco" id="tmpTipoCerco" value="<?php echo $r->TipoCerco; ?>"></td>
                      <td class="row2"><input type="checkbox" class="" name="TipoPuntera" id="TipoPuntera" value="S" <?php if ($r->TipoPuntera=="S"){ echo "checked"; } ?>  />
                        <label for="TipoPuntera" class="css-label2">Puntera</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoPuntera" id="tmpTipoPuntera" value="<?php echo $r->TipoPuntera; ?>"></td>
                    </tr>
                    <tr>
                      <td class="row2"><input type="checkbox" class="" name="TipoHerraje" id="TipoHerraje" value="S" <?php if ($r->TipoHerraje=="S"){ echo "checked"; } ?>  />
                        <label for="TipoHerraje" class="css-label2">Herraje</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoHerraje" id="tmpTipoHerraje" value="<?php echo $r->TipoHerraje; ?>"></td>
                      <td class="row2">&nbsp;</td>
                      <td class="row2"><strong>OTROS</strong></td>
                      <td class="row2"><textarea name="TipoOtro" id="TipoOtro" placeholder="Otro" rows="2" cols="30"><?php echo $r->TipoOtro ?></textarea>
                        <input type="hidden" name="tmpTipoOtro" id="tmpTipoOtro" value="<?php echo $r->TipoOtro; ?>"></td>
                      <td class="row2">&nbsp;</td>
                    </tr>
                  </table></td>
                </tr>

                <?php endif; ?>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform>DESCRIPCION DETALLADA DE LA SITUACION</td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class="row2">
                    <?php echo $r->Descripcion ?>
                  </td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform>COMENTARIOS CLIENTE</td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class="row2">&nbsp;<?php echo $r->ComentarioCliente ?></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform><table width="100%" border="0">
                    <tr>
                      <td><?php
					  // datos producto
					  $sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."'";					  $qry_producto=db_query($sql_producto);
					  $r_detalle=db_fetch_object($qry_producto);
					  ?>
                        <table width="100%" border="0">
                          <tr>
                            <td>Referencia</td>
                            <td>Talla</td>
                          </tr>
                          <tr bgcolor="#dfe3e7" class="texto forumline">
                            <td align="left" class="<?php echo $class?>">
                            <?php echo $nombre_referencia; ?>
                            </td>
                            <td align="left" class="<?php echo $class?>">&nbsp;
                            <?php echo $nombre_talla ?>
                            </td>
                          </tr>
                        </table></td>
                    </tr>
                  </table></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Atendido por:</td>
                  <td align="left" class="row2"><?php
					  if($r->Mayorista=="S"):
					  	echo $r->IngresadoPor;
					  else:
					  	echo get_field("Empleado","Nombre","IDEmpleado",$r->IDEmpleado) . " " . get_field("Empleado","Apellidos","IDEmpleado",$r->IDEmpleado);
					  endif;
					  ?></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Numero de Guia o  Persona a quien entrega</td>
                  <td align="left" class="row2"><input type="text" class="input obligatorio" name="NumeroGuia" id="NumeroGuia" value="<?php echo $r->NumeroGuia; ?>" ></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Fecha Salida Almac&eacute;n</td>
                  <td align="left" class="row2"><span class="col2">
                    <input type="input" name="FechaSalidaAlmacen" id="FechaSalidaAlmacen" class="tbox" value="<?php if ($r->FechaSalidaAlmacen!="0000-00-00") { echo $r->FechaSalidaAlmacen; }?>" readonly>
                    <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											//document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmdetalle.FechaSalidaAlmacen,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//-->
								</script>
                  </span></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Fecha Entrada Almac&eacute;n</td>
                  <td align="left" class="row2"><span class="col2">
                    <input type="input" name="FechaEntradaAlmacen" id="FechaEntradaAlmacen" class="tbox" value="<?php if ($r->FechaEntradaAlmacen!="0000-00-00") { echo $r->FechaEntradaAlmacen; }?>" readonly>
                    <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											//document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmdetalle.FechaEntradaAlmacen,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//-->
								</script>
                  </span></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Fecha en la que se entrega el producto al cliente</td>
                  <td align="left" class="row2"><span class="col2">
                    <input type="input" name="FechaEntregaCliente" id="FechaEntregaCliente" class="tbox" value="<?php if ($r->FechaEntregaCliente!="0000-00-00") { echo $r->FechaEntregaCliente; }?>" readonly >
                    <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											//document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmdetalle.FechaEntregaCliente,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//-->
								</script>
                  </span></td>
                </tr>
								<tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Cancelada con factura Nro</td>
                  <td align="left" class="row2"><input type="text" class="input obligatorio" name="NumeroFacturaRestauracion" id="NumeroFacturaRestauracion" value="<?php echo $r->NumeroFacturaRestauracion; ?>" <?php  if(!empty($r->NumeroFacturaRestauracion)) echo "readonly"; ?>  ></td>
                </tr>


			<tr>
			  <td colspan=2 align=center class="col2list">&nbsp;</td>
			  </tr>
			</table>
											</td>
										</tr>
										<tr bgcolor=#e7ebef>
											<td colspan="4">Agregar Estado</td>
										</tr>
                                        <tr>
                                        	<td>


                                            <script>
									var CheckDetalle = new Array('DetalleDescripcion','IDEstadoGarantia');
									</script>



									  <table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
										<tr class=row2>
										  <td>Nuevo Estado garantia</td>
										  <td align="left"> &nbsp;
										  <?php
										  if($r->TipoProducto=="C"):
										  	$estados_id="1, 2, 8, 9,11";
										  elseif($r->TipoProducto=="T"):
										  	$estados_id="1, 3, 4, 8, 9,11";
										  endif;


										  echo formpopup("EstadoGarantia","Nombre","IDEstadoGarantia","IDEstadoGarantia",$r->IDEstadoGarantia,"input\" id=\"IDEstadoGarantia"," IDEstadoGarantia in ( ".$estados_id.") "); ?>

                                          </td>
										  </tr>
										<tr class=row2>
										  <td><span class="col1">Descripcion proceso realizado</span></td>
										  <td align="left"><textarea name="Descripcion" id="Descripcion" cols="50" rows="5" ></textarea></td>
										  </tr>
										<tr class=row2>
										  <td>&nbsp;</td>
										  <td>&nbsp;</td>
										  </tr>
										<tr>
										  <td colspan=2 align=center class=row2>
											<input type=hidden name=IDGarantia value="<?php echo $r->IDGarantia ?>">
                                            <input type=hidden name=TipoProductoGarantia id="TipoProductoGarantia" value="<?php echo $r->TipoProducto ?>">
                                            <input type=hidden name=IDPuntoVenta value="<?php echo $r->IDPuntoVenta ?>">
											<input type=hidden name=ID value="<?php echo $r->$Key ?>">
                                            <input type=hidden name="FechaSalidaAlmacenAnt" value="<?php echo $r->FechaSalidaAlmacen ?>">
                                            <input type=hidden name="FechaEntradaAlmacenAnt" value="<?php echo $r->FechaEntradaAlmacen ?>">
                                            <input type=hidden name="FechaEntregaClienteAnt" value="<?php echo $r->FechaEntregaCliente ?>">
                                            <input type=hidden name="IDEstadoGarantiaAnt" id="IDEstadoGarantiaAnt" value="<?php echo $r->IDEstadoGarantia ?>">
											<input type=hidden name=action value="insertarcomentario">
											<input type=submit name=submit value="Guardar Proceso" class=submit>
                                            <br><br>
                                            <input type="button"  name="Imprimir" value="Imprimir Garantia" class=submit onClick="imprimir_garantia(<?php echo $r->IDGarantia?>,<?php echo $r->IDPuntoVenta?>)" >


                                            </td>



										  </tr>
									  </table>



                                            </td>
                                        </tr>



										<tr bgcolor=#e7ebef>
											<td colspan="4">Historial</td>
										</tr>
									  <tr>
										  <td colspan="4">


<table cellpadding="1" cellspacing="2" width="100%" border="0">
        <?php
		 $sql_comentario="SELECT * FROM ComentarioGarantia Where IDGarantia = '".$r->IDGarantia."' Order by IDComentarioGarantia DESC";
		 $qry_comentario=db_query($sql_comentario);
		 while($r_comentario=db_fetch_object($qry_comentario)){
		 ?>
        	<tr style="background-color: #E4E4E4">
            	<td align="left" >
                	<b>Fecha:</b>
                </td>
            	<td align="left">
                	<?php echo $r_comentario->FechaTrCr;  ?>
               </td>
        	 	<td align="left">
                	<b>Usuario:</b>
                </td>
            	<td align="left">
            	  <?php echo get_field("Empleado","Nombre","IDEmpleado",$r_comentario->IDEmpleado);  ?>
          	  </td>
            	<td align="left"><strong>Nuevo Estado</strong></td>
            	<td align="left"><?php echo get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$r_comentario->IDEstadoGarantia);  ?></td>
            	</tr>
        	<tr>
        	  <td colspan="6" align="left"><?php echo $r_comentario->Descripcion;  ?></td>
       	  </tr>
          <?php } ?>


        </table>


                                        </td>
									  </tr>
									</table>

</div>

					</td>
				</tr>
			</table>
		</td>
	</tr>

</table>
</FORM>
<?php
} // END function print_form_fotos($id,$numfotos)
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$IDPuntoVenta;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table WHERE IDPuntoVenta = '$IDPuntoVenta' ORDER BY IDGarantia DESC";

		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=50;
   		$nav->execute($sql,$dblink);
		$total_records =  $nav->total_result;
		$rows = $nav->rows;
		$result = $nav->sql_result;
		$row = $offset;
		$startrow = $offset + 1;
		$finalrow = ($row * $nav->limit) + $rows;

	 	$pages = $nav->show_num_pages('&laquo;','&laquo; prev','&raquo;','next &raquo;','|','class=navvar');   // show pages

		$info = $nav->show_info();

 if($_GET['in_order']=="ASC" || $_GET['in_order']==""){
								$img="down.png";
								$order="DESC";
							}else if($_GET['in_order']=="DESC"){
								$img="up.png";
								$order="ASC";
							}

							?><?php
		if($rows > 0){
?><br>
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="650">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				Listar <?php echo $TitleMod ?>
			</span>
			<span class="gen">
				<?php echo $info ?>
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>
<table class="forumline" width="650" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline" >
			<tr>
				<td class="forumlink" colspan="2">
					<?php filtrar();?>
				</td>
			</tr>
			<tr>
				<td class="forumlink" colspan="2">
					<table width=100% border=0 cellspacing=1 cellpadding=0>
							<tr>
								<td align=center class=navpic valign=middle bgcolor=#DBEAF5 width=69>Ver</td>
								<td class=navpic nowrap bgcolor=#DBEAF5>Numero</td>
								<td class=navpic nowrap bgcolor=#DBEAF5>Tipo</td>
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=IDCliente&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Cliente
									    <?php if($_GET['order_by']=="IDCliente"){ ?><img src="images/<?php echo $img;?>" border=0><?php } ?></a> </td>
									<td class=navpic nowrap bgcolor=#DBEAF5>Ref</td>
									<td class=navpic nowrap bgcolor=#DBEAF5>Talla</td>
									<td class=navpic nowrap bgcolor=#DBEAF5>Tipo</td>
									<td class=navpic nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=FechaFacturaBono&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Factura&nbsp;
									    <?php if($_GET['order_by']=="FechaFacturaBono"){ ?><img src="images/<?php echo $img;?>" border=0><?php } ?></a> </td>
									<td class=navpic nowrap bgcolor=#DBEAF5>Fecha Ingreso</td>
									<td class=navpic nowrap bgcolor=#DBEAF5>Estado</td>
									<td class=navpic nowrap bgcolor=#DBEAF5>Almacen Compra</td>
									<td class=navpic nowrap bgcolor=#DBEAF5 align="center">Fecha Estimada de entrega</td>
									<td class=navpic nowrap bgcolor=#DBEAF5 align="center">Nota Credito</td>
									<td class=navpic nowrap bgcolor=#DBEAF5 align="center">Alertas</td>
								</tr>

							<?php while($r = db_fetch_object($result)){
								$class = repetition()?"col1list":"col2list";
								$i++;
								$tallap="";
								$id_referencia_item="";
							?>

							<tr>
								<td align=center valign=middle nowrap width=50 class="<?php echo $class?>">
								&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
								</td>
								<td nowrap class="<?php echo $class?>"><?php echo $r->IDGarantia; ?></td>
								<td nowrap class="<?php echo $class?>"><?php echo $r->TipoRegistro; ?></td>
									<td nowrap class="<?php echo $class?>">
									<?php
									if(!empty($r->IDDetalleCambio)){
									  $array_cambio_detalle=explode("|",$r->IDDetalleCambio);
									  $sql_datos_factura=db_query("Select * From Cambio Where IDCambio = '".$array_cambio_detalle[0]."'");
									  $r_factura=db_fetch_array($sql_datos_factura);
									}
									else{

									  if ($r->TipoFactura=="facturabono"):
									  	$sql_datos_factura=db_query("Select * From FacturaBono Where IDFacturaBono = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
									  else:
									  	$sql_datos_factura=db_query("Select * From Factura Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
									  endif;
									  $r_factura=db_fetch_array($sql_datos_factura);

									}

									if ($r->TipoRegistro=="Reproceso"){
										$id_proveedor=get_field("Referencia","IDProveedor","IDReferencia",$r->IDReferencia);
										echo get_field("Proveedor","Nombre","IDProveedor",$id_proveedor);
									}
									elseif($r->Mayorista=="S"){
										echo $r->NombreMayorista;
									}
									else{
										$id_cliente= $r_factura[IDCliente];
										echo get_field("Cliente","Nombre","IDCliente",$id_cliente)." ".get_field("Cliente","Apellido","IDCliente",$id_cliente);
									}

									?>


                                    </td>
									<td nowrap class="<?php echo $class?>"><?php

									if ($r->TipoRegistro=="Reproceso" || $r->Mayorista=="S"){
										echo get_field("Referencia","Numero","IDReferencia",$r->IDReferencia);
										$tallap=get_field("Talla","Descripcion","IDTalla",$r->IDTalla);
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$r->IDReferencia);;
										$tipop= get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
									}
									elseif(!empty($r->IDDetalleFacturaBono)){
										$array_bono_detalle=explode("|",$r->IDDetalleFacturaBono);
										if (count($array_bono_detalle)>0):
											$sql_bono=db_query("Select * From DetalleFacturaBono Where IDDetalleFacturaBono	 = '".$array_bono_detalle[1]."' and IDFacturaBono = '".$array_bono_detalle[0]."'");
											$r_bono=db_fetch_array($sql_bono);

											$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"])));
											$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
											$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
											echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
											$tipop=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
											$tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
										endif;


									}

									elseif(empty($r->IDDetalleCambio)){

										  $id_referencia_item="";
										  $id_punto_venta=$r->IDPuntoVentaFactura;
										  if ($r->TipoFactura=="facturabono"):
										  	$sql_producto="select * from DetalleFacturaBono Where IDDetalleFacturaBono='".$r->IDDetalleFactura."' and IDFacturaBono = '".$r->IDFactura."' and IDPuntoVenta = '".$id_punto_venta."'";
										  else:
										  	$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$id_punto_venta."'";
										  endif;

										  //echo $sql_producto;
										  //$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$id_punto_venta."'";
										  $qry_producto=db_query($sql_producto);
										  $r_detalle=db_fetch_object($qry_producto);
										  $id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
										  $tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica));

										if ($id_referencia_item==160){ // Cuando son excedentes consulto la referencia de la compra
											$sql_facturabono=db_query("Select * from FacturaBono Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
											$r_facturabono=db_fetch_array($sql_facturabono);
											if (!empty($r_facturabono[IDFacturaBono])){
												$sql_detallefacturabono=db_query("Select * from DetalleFacturaBono Where IDFacturaBono = '".$r_facturabono[IDFacturaBono]."'");
												$r_detallefacturabono=db_fetch_array($sql_detallefacturabono);
												$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));
												$tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"]));
											}
										  }



										  $id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										  echo get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
										 $tipop=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
									}
									else{
										$array_cambio_detalle=explode("|",$r->IDDetalleCambio);
										if (count($array_cambio_detalle)>0):
											$sql_cambio=db_query("Select * From DetalleCambio Where IDDetalleCambio = '".$array_cambio_detalle[1]."' and IDCambio = '".$array_cambio_detalle[0]."'");
											$r_cambio=db_fetch_array($sql_cambio);

											$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"])));
											$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"]));
											$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
											echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
											$tipop=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
											$tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"]));
										endif;


									}

									if($r->Mayorista=="S"):
										echo $r->ColorMayorista;
									endif;


									?></td>
									<td nowrap class="<?php echo $class?>"><?php
							if ($tallap!="")
								echo $tallap;
							else
								echo $tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$id_referencia_item));


							?></td>
									<td nowrap class="<?php echo $class?>"><?php
							if($r->Mayorista=="S"):
								echo $r->TipoProductoMayorista;
							else:
								echo $tipop;
							endif;	 ?></td>
									<td nowrap class="<?php echo $class?>"><?php
									if ($r->TipoFactura=="facturabono"):
										echo $r_factura[NumeroFacturaBono]  . "(bono)";
									else:
										echo $r_factura[NumeroFactura];
									endif;

									?></td>
									<td nowrap class="<?php echo $class?>"><?php echo formatofecha(substr($r->FechaTrCr,0,10)) ?></td>
									<td nowrap class="<?php echo $class?>"><?php echo get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$r->IDEstadoGarantia); ?></td>
									<td nowrap class="<?php echo $class?>"><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaFactura); ?></td>
									<td nowrap class="<?php echo $class?>"><?php echo formatofecha(substr($r->FechaEstimadaEntrega,0,10)) ?></td>
									<td nowrap class="<?php echo $class?>">
										<?php
                                            echo $r->RequiereNotaCredito;
                                            if ($r->RequiereNotaCredito=="S"){ echo " Numero: " . $r->NumeroNotaCredito ; }
                                        ?>
                                    </td>
									<td nowrap class="<?php echo $class?>">
							<?php

							if ($r->TipoRegistro=="Reproceso"){
								if ($r->IDEstadoGarantia!=8  && $r->IDEstadoGarantia!=10 && $r->IDEstadoGarantia!=9){
									$hoy=date("Y-m-d");
									$fecha_vencimiento = $r->FechaEstimadaEntrega;
									$diferencia_dias=strtotime ( $fecha_vencimiento ) - strtotime ( $hoy );
									$dias=intval($diferencia_dias/60/60/24) ;
									if ($dias >= 0 && $dias <= 3  ){ ?>
										<img src="admin/images/campananaranja.jpg" width="15" height="15">
									<?php
										echo "Vence en " . $dias . " dias";
									}elseif ($dias <0){ ?>
										<img src="admin/images/campanaalerta.jpg" width="15" height="15" >
									<?php
										echo "Vencida hace " . abs($dias) . " dias";
									}

								}
							}
							elseif ($r->IDEstadoGarantia!=8 && $r->IDEstadoGarantia!=9  && $r->IDEstadoGarantia!=10 && $r->IDEstadoGarantia!=12){
								$hoy=date("Y-m-d");
								$fecha_vencimiento = $r->FechaEstimadaEntrega;
								$diferencia_dias=strtotime ( $fecha_vencimiento ) - strtotime ( $hoy );
								$dias=intval($diferencia_dias/60/60/24) ;
								if ($dias >= 0 && $dias <= 3  ){ ?>
                                	<img src="admin/images/campananaranja.jpg" width="15" height="15">
                                <?php
									echo "Vence en " . $dias . " dias";
								}elseif ($dias <0){ ?>
                                	<img src="admin/images/campanaalerta.jpg" width="15" height="15" >
                                <?php
									echo "Vencida hace " . abs($dias) . " dias";
								}


							}


							// Si se marco que necesita numero de nota credita y no se ha digitado
							if ($r->RequiereNotaCredito=="S" && $r->NumeroNotaCredito==""){ ?>
							<br><img src="admin/images/campananaranja.jpg" width="15" height="15" >
                            No se ha ingresado el Numero de la nota credito
                            <?php
							}



							// Si esta pendiente de enviar producto rechazado a fabrica
							if (($r->IDEstadoGarantia=="9" || $r->IDEstadoGarantia=="10" ) && $r->RequiereDevolucion=="S"){ ?>
							<br><img src="admin/images/campanaalerta.jpg" width="15" height="15" >
                            Pendiente de enviar producto a fabrica
                            <?php
							}


							?>

                                    </td>
								</tr>
							<?php } // END for
							?>
							<tr>
							<td  class="navpic" colspan=14 nowrap>
									<?php
										print $pages;
									?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
	</tr>
</table>
<?php
}// End if$rows
else
	echo "<br><br><span class=subtitle><b>No existen registros en  $TitleMod </b></span>";
}// Enf function list()

/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD,$IDPuntoVenta;
?>
	<form name="frm" action="./" method="get" onSubmit="return valbuscar(document.frm)">
		<tr>
			<td class="rowform" align="center" colspan=8>
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
					<option value="IDGarantia">Numero Seguimiento</option>
                    <option value="NumeroFactura">Numero Factura</option>
					<option value="Cedula">Cedula</option>
					<!--<option value="NombreGarantia">Tipo garantia</option> -->
					<option value="EstadoNombre">Estado garantia</option>

				</select>
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post">
				Entre <input type=text readonly size=10 class=input name=limit1>
				<script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit1,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//-->
				</script>
				 y <input type=text size=10 readonly class=input name=limit2>
				<script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//-->
				</script>
				<br>
				ordenar por
				<select name="order_by" class="popup">
                	<option value="IDGarantia">Numero Seguimiento</option>
					<option value="NumeroFactura">Numero Factura</option>
					<option value="Cedula">Cedula</option>
					<!--<option value="NombreGarantia">Tipo garantia</option>-->
					<option value="EstadoNombre">Estado garantia</option>
				</select>
				de forma
				<select name="in_order" class="popup">
					<option value="ASC">Ascendente</option>
					<option value="DESC">Descendente</option>
				</select>
				Listar
				<select name="listar" class="popup">
					<option value="10">10</option>
					<option value="15">15</option>
					<option value="20">20</option>
					<option value="25">25</option>
					<option value="30">30</option>
				</select>
				<br>
				<input type="hidden" name="mod" value="<?php echo $MOD?>">
				<input type="hidden" name="rangofield" value="FechaFacturaBono">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="IDPuntoVenta" value="<?php echo $IDPuntoVenta?>">
				<input type="hidden" name="tjoin" value="Cliente">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>

<?php
	}//End function filtrar
?>
