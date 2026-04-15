<?php
	include("../admin/config.inc.php");
	if (!class_exists('PdfModern')) {
		require_once(__DIR__ . "/../admin/lib/PdfModern.php");
	}
	//Encabezado();
	$datos = Verifica_SesionCliente();
	//print_r($datos);
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
	$IDPuntoVenta = $datos["IDPuntoVenta"];
	//include("admin/jscripts/tabs.php");

	$TitleMod ="Factura";

	$Table = "Factura";
	$TableJoin = "Factura";
	$Key = "IDFactura";

	//$qid = db_query(" SELECT G.*,F.FechaFactura,F.IDCliente FROM Garantia G,Factura F WHERE G.IDFactura=F.IDFactura and G.IDGarantia = '$id' AND G.IDPuntoVenta = '$idpunto' ");

	$qid = db_query(" SELECT G.* FROM Garantia G WHERE G.IDGarantia = '$id' AND G.IDPuntoVenta = '$idpunto' ");

	$r = db_fetch_object($qid);

	$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
	$qry_puntoventa = db_query( $sql_puntoVenta );
	$r_puntoventa = db_fetch_object( $qry_puntoventa );

	$filedir = $dirroot . "/files/facturas/";

	$name = "Factura" . $r_puntoventa->Codigo.$r->NumeroFactura . ".html";
	$namePDF = "Factura" . $r_puntoventa->Codigo.$r->NumeroFactura . ".pdf";
	$file = "$filedir$name";
	$filepdf = "$filedir$namePDF";

//	ob_end_clean();

	ob_start();

?>
<html>
<head>
<meta charset="UTF-8">

<style>
@page {
	size: 74mm 260mm;
	margin: 0;
}

html {
	margin: 0;
	padding: 0;
}

body {
	font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
	font-size: 7pt;
	margin: 0 0 0 6mm;
	padding: 0 2mm 1mm 1mm;
	width: 62mm;
	box-sizing: border-box;
	color: #000;
}

table {
	width: 100%;
	border-collapse: collapse;
	font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
	font-size: 6.2pt;
	line-height: 1.1;
	box-sizing: border-box;
	table-layout: fixed;
}

td {
	overflow-wrap: break-word;
	word-wrap: break-word;
	vertical-align: top;
}

.texto,
.rowform,
.row2 {
	font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
	font-size: 6.2pt;
	line-height: 1.1;
	color: #000;
}

font {
	font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif;
}

font[style*="font-size:10px"] {
	font-size: 6.2pt !important;
}

font[style*="font-size:12px"] {
	font-size: 7pt !important;
}

font[style*="font-size:14px"],
font[style*="font-size:16px"] {
	font-size: 8pt !important;
}

.bordertable {
	border: dotted 1px;
	color: #c3c3c3;
}

#content {
	margin-left: 0;
	float: none;
	width: 100%;
	color: black;
}
</style>
</head>



<body>


			<table cellspacing="1" border="0" align="center" id="content">
		<tr>
			<td valign="top">
				<table width="100%" border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff">
					<tr>
						<td colspan="2">
							<div align="center">
								<table width=100% border=0>
									<tr>
											<td colspan="4">


                                        <table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline"  >

										  <tr >
                  <td align="center" class=rowform>
				  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td colspan="2">
                      RPNC83.4 V.6<br>
                      <font style="font-size:12px">


					  <?php if ($r->TipoRegistro=="Garantia"): ?>
                        GARANTIA No.
                        <?php elseif($r->TipoRegistro=="Servicio"): ?>
                        SERVICIO No.
						<?php elseif($r->TipoRegistro=="Restauracion"): ?>
                        RESTAURACION No.
                        <?php else: ?>
                        REPROCESOS No.
                        <?php endif; ?>

                        <font style="font-size:16px">
                          <?php echo $id; ?>
                        </font>

						<?php 
						if($r->Dotacion=="S") echo " / Dotacion"; 
						?>

                        </td>
						<td colspan="2"><font style="font-size:10px">Fac.Venta N&ordm;</font>
						<span class="row2">
					  <font style="font-size:12px">&nbsp;&nbsp;&nbsp;&nbsp;
					  <?php echo get_field("Factura","NumeroFactura","IDFactura",$r->IDFactura); ?></font></span> <br></td>
                    </tr>
                    <tr>
                      
                      
                      </tr>
                    
                      <td width="22%"><font style="font-size:10px">Almac&eacute;n</font></td>
                      <td class="row2">
                        <font style="font-size:10px"><strong>
                          <?php
					  echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta);
					  ?>
                      </strong>
                          </font>
                      </td>
					  <td><font style="font-size:10px">Tel: </font>
                      <font style="font-size:10px">
                        <?php
					  echo get_field("PuntoVenta","Telefono","IDPuntoVenta",$r->IDPuntoVenta);
					  ?>
                      </font></td>
                      </tr>
                    <tr>
                     
                      </tr>
                    <tr>
                      <td><font style="font-size:10px">Cliente</font> </td>
                      <td colspan="3" class="row2">
                        <font style="font-size:10px">
                          <?php

					  $sql_datos_factura=db_query("Select * From Factura Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
					  $r_factura_compra=db_fetch_array($sql_datos_factura);



					  $id_punto_venta=$r->IDPuntoVentaFactura;


									if ($r->TipoRegistro=="Reproceso"){
										$id_proveedor=get_field("Referencia","IDProveedor","IDReferencia",$r->IDReferencia);
										echo get_field("Proveedor","Nombre","IDProveedor",$id_proveedor);

									}
									elseif($r->Mayorista=="S" || $r->Dotacion=="S"){
										echo $r->NombreMayorista;
										$ciudad_c=$r->CiudadMayorista;

									}
									else{
										  $id_cliente=$r_factura_compra["IDCliente"];
										  echo get_field("Cliente","Nombre","IDCliente",$id_cliente) ." ". get_field("Cliente","Apellido","IDCliente",$id_cliente);
									}



						  ?>
                          </font>                        <font style="font-size:12px">&nbsp;</font></td>
                      </tr>

											<?php if(!empty($ciudad_c)){ ?>
											<tr>
                      <td><font style="font-size:10px">Ciudad</font></td>
                      <td colspan="3" class="row2"><font style="font-size:10px"><?php echo $ciudad_c ; ?></font></td>
                      </tr>
											<?php } ?>


					<!--						
					<tr>
                      <td><font style="font-size:10px">Direccion</font></td>
                      <td colspan="3" class="row2"><font style="font-size:10px"><?php echo $r->DireccionMayorista ; ?></font></td>
                      </tr>
					  -->
                    	<tr>
                      <td><font style="font-size:10px">Tel</font></td>
                      <td class="row2"><font style="font-size:10px"><?php echo $r->Telefono; echo " " . get_field("Cliente","Telefono","IDCliente",$id_cliente); ?></font></td>
					  <td colspan="2"><font style="font-size:10px">Cel.</font>
					   <font style="font-size:10px">&nbsp;<?php echo $r->Celular; ?></font></td>
                      </tr>

					 <tr>
					  
                      </tr>


												<tr>
												<td colspan="3" align="left">
												<br>
												<table width="100%" border=0>
													<tr>
														<td align="left">
															<font style="font-size:10px">Fecha Compra: <font style="font-size:14px"><?php echo substr(get_field("Factura","FechaFactura","IDFactura",$r->IDFactura),0,10); ?></font></font>
														</td>
														<td>
														<font style="font-size:10px">Fecha Reclamo: <font style="font-size:14px"><?php echo date("Y-m-d"); ?></font></font>
														</td>
													</tr>
													
												</table>
												</td>

												</tr>




												<tr>
													<td colspan="4"><font style="font-size:10px">
													<?php if($r->TipoRegistro=="Restauracion"){ ?>
														Restauracion_por:
													<?php }
													else{ ?>
														Garantia_por:
													<?php } ?>
													
													
													<?php
													switch($r->CantidadVeces){
														case "1":
															echo "Primera vez";
														break;
														case "2":
															echo "Segunda vez";
														break;
													}
													 ?>
													 </font>
													 </td>													
													</tr>


                    <tr>
                      <td colspan="4"><br><hr></td>
                      </tr>
                    <tr>
                      <td colspan="4">
					  <br>
					  <?php
					  // datos producto
						if ($r->TipoFactura=="facturabono"):
					  	$sql_producto="select * from DetalleFacturaBono Where IDDetalleFacturaBono='".$r->IDDetalleFactura."' and IDFacturaBono = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'";
					  else:
					 	$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."'";
					endif;

					  $qry_producto=db_query($sql_producto);
					  $r_detalle=db_fetch_object($qry_producto);
					  ?>
                      <font style="font-size:10px">
                      Referencia:
                      </font>
                      <font style="font-size:12px">
						<?php  if ($r->TipoRegistro=="Reproceso" || $r->Mayorista=="S" || $r->Dotacion=="S"){
									echo $nombre_referencia=get_field("Referencia","Numero","IDReferencia",$r->IDReferencia);
								 $nombre_talla=get_field("Talla","Descripcion","IDTalla",$r->IDTalla);

						  } elseif(!empty($r->IDDetalleFacturaBono)){

							  $array_bono_detalle=explode("|",$r->IDDetalleFacturaBono);
									if (count($array_bono_detalle)>0 ):
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
										if (!empty($r_facturabono["IDFacturaBono"])){
											$sql_detallefacturabono=db_query("Select * from DetalleFacturaBono Where IDFacturaBono = '".$r_facturabono["IDFacturaBono"]."'");
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

								if($r->Mayorista=="S" || $r->Dotacion=="S"):
										echo $r->ColorMayorista . " Tipo: " . $r->TipoProductoMayorista;
								endif;

							   ?>

                          </font>

                           <font style="font-size:9px">Talla: </font>
                          <font style="font-size:12px">
                          <?php

						  if ($r->TipoRegistro=="Reproceso"){
									echo $nombre_talla=get_field("Talla","Nombre","IDTalla",$r->IDTalla);

						  } elseif($nombre_talla!=""){ echo $nombre_talla; } else {?>
                         			 <?php
									//echo $id_referencia_item;
									 //echo $tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$id_referencia_item));
									 echo $nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica));
									 //echo $nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))?>

                               <?php } ?>


                         &nbsp;&nbsp;
                          <?php  if ($r->TipoRegistro=="Reproceso"){
									$tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$r->IDReferencia);;
									echo get_field("TipoReferencia","Descripcion","IDTipoReferencia",$tipo_ref);

						  } else{ ?>
                         			 <?php echo get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref); ?>

                               <?php } ?>


                                  </font>

<?php if($r->NumeroOrdenProduccion!=""){ ?>
<br><br>
 <font style="font-size:12px">Orden Produccion:  </font><font style="font-size:16px"><?php echo $r->NumeroOrdenProduccion; ?>  </font>
<?php } ?>
																	<?php if($r->Remonta=="S"){ ?>

																	 <font style="font-size:12px">Remonta</font>
																	<?php } ?>




                                            </td>
                      </tr>
                  </table>



                  </td>
                </tr>

								<tr>
									<td align="left" class=rowform><br>
									<hr></td>
									</tr>

								<tr >
									<td align="left" class=rowform>
									<?php if((int)$r->ValorRemonta>0 || (int)$r->ValorBasica>0 || (int)$r->ValorPremium>0){ ?>

									 <font style="font-size:12px">Valor servicio: </font><font style="font-size:16px">
									 <?php //echo "$".number_format($r->ValorRemonta); ?>  </font>
									<?php } ?>

									<?php if($r->Premium=="S" || $r->Basica=="S"){ ?>

									 <font style="font-size:12px">
									 <?php
									 if($r->Premium=="S") echo "Premium";
									 if($r->Basica=="S") echo "Basica";
									 ?>
									 </font>
									 <font style="font-size:16px">
									 <?php if((int)$r->ValorBasica>0) echo "$".number_format($r->ValorBasica); ?>
									 <?php if((int)$r->ValorPremium>0) echo "$".number_format($r->ValorPremium); ?>
									 </font>
									<?php } ?>

									</td>
								</tr>


                <tr>
                  <td align="left" class=rowform><br>
                  <hr></td>
                  </tr>

                <tr >
                  <td align="left" class=rowform><font style="font-size:12px">Identificacion de la
									<?php if($r->TipoRegistro=="Restauracion"){ ?>
										restauracion
									<?php } else { ?>
										garantia
									<?php } ?>
									</font></td>
                </tr>
                <tr >
                  <td align="left" class="row2"><span class="row2"><font style="font-size:12px">

                    <br><?php echo strtoupper($r->Descripcion) ?>
                    </font>

                  </span></td>
                </tr>

				<tr >
                  <td align="left" class=rowform><font style="font-size:12px"><br>Comentario Cliente: </td>
                </tr>
                <tr >
                  <td align="left" class="row2"><span class="row2"><font style="font-size:12px">

                    <?php echo strtoupper($r->ComentarioCliente) ?>
                    </font>

                  </span></td>
                </tr>

                <tr>
                  <td align="left" class=rowform><br><font style="font-size:12px">Estado en el que se recibe el producto</font><br><br></td>
                </tr>
                <tr>
                  <td align="left" class=rowform>
                  <font style="font-size:10px">
                  <?php $array_recibe = array(); ?>
                  <?php if( $r->CueroPelado=="S" ) $array_recibe[]="Cuero Pelado"; ?>
                  <?php if( $r->SuelaDesgastada=="S" ) $array_recibe[]= "Suela Desgastada"; ?>
                  <?php if( $r->Ojete=="S" ) $array_recibe[] = "Ojetes cedidos";  ?>
                  <?php if( $r->CueroManchado=="S" ) $array_recibe[] = "Cuero Manchado"; ?>
                  <?php if( $r->ViraDanada=="S" ) $array_recibe[] = "Vira Da&ntilde;ada"; ?>
                  <?php if( $r->Punteras=="S" ) $array_recibe[] = "Punteras hundidas"; ?>
                  <?php if( $r->CueroRayado=="S" ) $array_recibe[] = "Cuero rayado";  ?>

                  <?php if( $r->ForroManchado=="S" ) $array_recibe[] = "Forro manchado"; ?>
                  <?php if( $r->TaconDesgastado=="S" ) $array_recibe[] = "Tacon Desgastado"; ?>
                  <?php if( $r->ForroRoto=="S" ) $array_recibe[] = "Forro roto"; ?>
                  <?php if( $r->TaconPelado=="S" ) $array_recibe[] = "Tacon Pelado"; ?>
                  <?php if( $r->OtroDescripcion!="" ) $array_recibe[] = $r->OtroDescripcion; ?>


                  <?php
                  if (count($array_recibe)>0){
					 $texto_recibe=implode("<br>",$array_recibe);
					 echo strtoupper($texto_recibe);
				  }

				  ?>
                  </font>
                  <br><br>
                 <font style="font-size:12px">
                  Atendido por:
                  <?php
					  if($r->Mayorista=="S" || $r->Dotacion=="S"){
								echo $r->IngresadoPor;

					  }
					  else{
					  	echo get_field("Empleado","Nombre","IDEmpleado",$r->IDEmpleado) . " " . get_field("Empleado","Apellidos","IDEmpleado",$r->IDEmpleado);
					  }
					  ?>
                      </font>
                  </td>
                </tr>

                <tr >
                  <td align="center" class=rowform><br><br>
                  _________________________________<br>
                  FIRMA DEL CLIENTE

                  <br><br>
                   <font style="font-size:10px">
                  Tiempo de respuesta de su
										<?php if($r->TipoRegistro=="Restauracion"){ ?>
										restauracion
									<?php } else { ?>
										garantia
									<?php } ?> /servicio es de</font>
                  <font style="font-size:12px">
                  15
                  </font>
                  <font style="font-size:10px">
                  dias habiles
                  </font>
                  </td>
                </tr>
                <tr >
                  <td align="left" class=rowform>


                  </td>
                </tr>
                <tr >
                  <td align="left" class=rowform><hr></td>
                </tr>
                <tr>
                  <td align="left" class=rowform><br><font style="font-size:12px">Fecha entrega cliente:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br><br>__________/_____ /_____</font></td>
                  </tr>
                <tr >
                  <td align="center" class=rowform>
                  <br><br><br>_________________________________
                  <br>FIRMA DEL CLIENTE A SATISFACCION

                  </td>
                </tr>
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
</body>
</html>
<?php




$page = ob_get_contents();

$fw = fopen($file, "w");

fputs($fw,$page,strlen($page));

fclose($fw);

ob_end_clean();


echo $page;

//passthru("htmldoc --format pdf --size 'Universal' --textfont Arial --title 'Acta' --charset 8859-15 --left 0cm --right 0cm --top 0cm --bottom 0cm --fontsize 7 --webpage $file -f $filedir/$namePDF");
//echo "/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf";
PdfModern::generate($page, $filepdf, [74, 260]);

?>
