<?php
	include("admin/config.inc.php");
	Encabezado();
	$datos = Verifica_SesionCliente();
//	print_r($datos);
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
	$IDPuntoVenta = $datos["IDPuntoVenta"];

	SIMReg::set("IDPuntoVenta", $IDPuntoVenta);

	$sql_punto="SELECT * FROM PuntoVenta WHERE IDPuntoVenta = '$IDPuntoVenta'";
	$qry_punto = db_query($sql_punto);
	$r_punto = db_fetch_object($qry_punto);


	include("admin/jscripts/tabs.php");
?>
<html>

	<head>
		<link href="styles.css" rel="stylesheet" media="screen">
		<link href="admin/styles.css" rel="stylesheet" media="screen">
		<link rel="STYLESHEET" type="text/css" href="admin/styles_imp.css" media="print"> 
		<style type="text/css" media="screen"><!--
#layer1 { position: absolute; top: 64px; left: 930px; width: 100px; height: 100px; visibility: visible; display: block }
--></style>
		<!--<script language="JavaScript1.2" src="admin/jscripts/preloadtabs.js?<?=rand(1,100)?>"></script>-->
		<script language="JavaScript1.2" src="admin/jscripts/formdetect.js?<?=rand(1,100)?>"></script>
		<script language="JavaScript1.2" src="admin/jscripts/popcalendar.js?<?=rand(1,100)?>"></script>
		<script language="JavaScript1.2" src="admin/jscripts/validaForm.js?<?=rand(1,100)?>"></script>
		
		<script language="JavaScript1.2" src="jscripts/jquery-1.3.2.min.js?<?=rand(1,100)?>"></script>
		<script language="JavaScript1.2" src="jscripts/common.js?<?=rand(1,100)?>"></script>

		
        <meta http-equiv="cache-control" content="max-age=0" />
        <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="expires" content="0" />
        <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
        <meta http-equiv="pragma" content="no-cache" />
        
		
		<title><?=$app_title?></title>
	</head>

	<body bgcolor="#ffffff" onLoad="init();">
		<table width="850" border="0" cellspacing="0" cellpadding="0" align="center">
			<div id="nomoimprimir">
			<tr height="42">
				<td align="right" valign="top"  width="850" height="74" style="background-repeat:no-repeat;background-position: top right;" background="images/header_right2.jpg?<?=rand(1,100)?>" >
					
					<div id="datossession">
					
					<font color="#000000">Sesi&oacute;n Activa:</font> <font color="#000000"><b><?=$Nombre_Usuario?></b></font><br>
					<font color="#000000"><?=$r_punto->Nombre;?><br>
					<a href="login.php?action=LogOut" class="menuppal" style="color:#000000;">
						Terminar Sesi&oacute;n
					</a>
					</font>
					
					</div>
				</td>
			</tr>
			<tr>
				<td align="right" valign="bottom" width="850"><br>
				</td>
			</tr>
			<tr>
				<td align="left" valign="bottom" width="850">
					<div id="ddimagetabs">
						<a href="javascript:;" onClick="expandcontent('Factura', this)" >Facturas</a>
						<a href="javascript:;" onClick="expandcontent('Movimiento', this)">Movimientos</a>
						<a href="javascript:;" onClick="expandcontent('Pedido', this)">Pedidos</a>
						<a href="javascript:;" onClick="expandcontent('Traslado', this)">Traslados</a>
                                              
						<a href="javascript:;" onClick="expandcontent('Inventario', this)">Inventario</a>
						<a href="javascript:;" onClick="expandcontent('Reporte', this)">Reportes</a>
                        <a href="javascript:;" onClick="expandcontent('Garantia', this)">Garant&iacute;as</a>
                        <a href="?mod=ConsultaPrecio">Precios</a>
                        <a href="javascript:;" onClick="expandcontent('Pqr', this)">Pqr's</a>
					</div>
					
				</td>
			</tr>
			</div>
			
			<tr>
				<td valign="top" width="850" height="50">
				
					<DIV id="tabcontentcontainer">
						
						<div id="Factura" class="tabcontent">
							<a href="?mod=GenerarFactura" class="copyright">Generar Factura</a> |
							<a href="?mod=GenerarFacturaBono" class="copyright">Redimir Tarjeta Regalo</a> |
							<a href="?mod=Factura" class="copyright">Ver Facturas</a> |
							<a href="?mod=PagoCreditos" class="copyright">Pago de Cr&eacute;ditos</a> |
							<a href="?mod=PuntosCliente" class="copyright">Puntos Fidelizaci&oacute;n</a> | 
                            <a href="?mod=BonosCliente" class="copyright">Bonos Fidelizaci&oacute;n</a> | 
                            <a href="?mod=ComprasCliente" class="copyright">Compras</a>
						</div>
						
						
						<div id="Movimiento" class="tabcontent">
							
							<a href="?mod=Movimiento" class="copyright"> Entradas Caprino </a> |
                            <a href="?mod=MovimientoTercero" class="copyright"> Entradas Tercero </a> |
							<a href="?mod=SalidaMerca" class="copyright">Salida de Mercancia </a>|
							<a href="?mod=FacturaBono" class="copyright">Ver Registros Tarjeta Regalo</a> |
							<a href="?mod=verentrada" class="copyright">Ver Entradas de Pedido </a>|
							<a href="?mod=cambioreferencia" class="copyright">Cambio Referencia </a>|
							<a href="?mod=VerMovimiento" class="copyright">Ver Salidas </a> |
							<a href="?mod=vercambios" class="copyright"> Ver Cambios </a> 
						</div>
						
						<div id="Pedido" class="tabcontent">
							<a href="?mod=Pedido" class="copyright"> Ver Pedidos </a>
						</div>
						
						<div id="Traslado" class="tabcontent" >
							
							<a href="?mod=Traslado" class="copyright"> Traslado </a> |
							<a href="?mod=RecibirTraslado" class="copyright"> Recibir Traslado </a> |
							<a href="?mod=vertraslado" class="copyright"> Ver Traslado </a> 
							
						</div>
                                            	<div id="Traslado2" class="tabcontent" >
							
							<a href="?mod=Traslado2" class="copyright"> Traslado 2 </a> |
							<a href="?mod=RecibirTraslado2" class="copyright"> Recibir Traslado 2</a> |
							<a href="?mod=vertraslado2" class="copyright"> Ver Traslado 2</a> 
							
						</div>
						<div id="Inventario" class="tabcontent" >
							
							<a href="?mod=Inventario" class="copyright"> Consultar Inventario </a> !
							<a href="?mod=InventarioCon" class="copyright"> Consultar Inventario Consolidado </a> !
							<a href="?mod=InventarioConalm" class="copyright"> Consultar Inventario Consolidado Otros Almacenes </a> !
							<a href="?mod=BuscReferencia" class="copyright"> Inventario x Referencia </a> !
						</div>
						
						<div id="Reporte" class="tabcontent" >
							
							<a href="?mod=diario" class="copyright"> Reporte Diario </a> |
                            <a href="?mod=diariocredito" class="copyright"> Reporte Diario Credito </a> |
                            <a href="?mod=reportecredito" class="copyright"> Reporte Creditos </a> |
							<a href="?mod=mensual" class="copyright"> Reporte Mensual </a> |
							<a href="?mod=vendedores" class="copyright"> Ventas Vendedores </a> |
							<a href="?mod=pares" class="copyright"> Ventas Pares </a> |
							<a href="?mod=bonos" class="copyright"> Reporte Bonos </a> |
							<a href="?mod=inventariotarjetas" class="copyright"> Tarjetas de Regalo </a> |
						</div>
						
						<div id="Garantia" class="tabcontent" >
							
							<a href="?mod=Garantia" class="copyright"> Ingreso </a> |
                            <a href="?mod=SeguimientoGarantia" class="copyright"> Seguimiento Garantias </a> |
                            <a href="?mod=GarantiaReporte" class="copyright"> Reportes </a> |
						</div>
                        
                        <div id="Garantia" class="tabcontent" >
							
							<a href="?mod=Garantia" class="copyright"> Ingreso </a> |
                            <a href="?mod=SeguimientoGarantia" class="copyright"> Seguimiento Garantias </a> |
                            <a href="?mod=GarantiaReporte" class="copyright"> Reportes </a> |
						</div>
                        <div id="Pqr" class="tabcontent" >
							
							<a href="?mod=Pqr&action=add" class="copyright"> Ingreso </a> |
                            <a href="?mod=Pqr" class="copyright"> Seguimiento Pqr </a> 
						</div>
                        
                        


					</DIV>
                    
                   <?php
                   $sql_traslado_web = "Select * From Traslado Where IDPuntoVentaOrigen = '".$IDPuntoVenta."' and UsuarioTrCr = 'VentaTiendaVirtual' and IDEstadoTraslado = 1";
				   $qry_traslado_web = db_query($sql_traslado_web);
				   $total_traslado_web = db_num_rows($qry_traslado_web);
				   if($total_traslado_web>0):
				   ?>
                   <br>
                   <table width="100%">
                   <?php while($row_traslado_web = db_fetch_array($qry_traslado_web)){ ?>
                   	<tr>
                    	<td style="color:#EE080C;">
                        	ATENCION: Se gener&oacute; el traslado automatico Numero: <?php echo $row_traslado_web["IDTraslado"] ?> por una venta en la Tienda Virtual. Por favor sacar el producto inmediatamente de exhibici&oacute;n o inventario.
                            <a href="#" onClick="window.open( 'Traslado/FImpresion.php?id=<?php echo $row_traslado_web["IDTraslado"] ?>&idpunto=<?=$row_traslado_web["IDPuntoVentaOrigen"] ?>','','width=426, height=350' )">
                            Clic para aqui imprimir traslado
                            </a>

							                        </td>
                    </tr>
                    <?php }
					?>
					
                   </table>
                   <?php endif; ?>
				   <table>
				   <tr>
					<td>
					<?php	
						
							if (isset($r_punto->FechaFinResolucion) && $r_punto->FechaFinResolucion != "0000-00-00") {
								$fechaFin = new DateTime($r_punto->FechaFinResolucion);
								$hoy = new DateTime();
								$interval = $hoy->diff($fechaFin);
								if ($interval->days <= 10 && $interval->invert == 0) {
									if($interval->days==0){
										$DiasVencimiento=1;
									}
									echo "<br><span align=center style='color: green;  font-size: 14px; '>Facturacion DIAN Vence en ".$DiasVencimiento." dias</span>";
								}
							}
							
							$ArrayNumeroFinResolucion = explode("-", $r_punto->RHasta);
							$NumeroFinResolucion =  $ArrayNumeroFinResolucion[1];

							$sql_fact="SELECT NumeroFactura FROM Factura WHERE IDPuntoVenta = '$r_punto->IDPuntoVenta' ORDER BY IDFactura DESC LIMIT 1";
							$qry_fact = db_query($sql_fact);
							$r_fact = db_fetch_object($qry_fact);
							$NumerofacturaActual = $r_fact->NumeroFactura;
							
							
							$Diferencia = $NumeroFinResolucion - $NumerofacturaActual;
							if($Diferencia <= 10)
								echo "<br>Facturacion DIAN: <span style='color: green; size: 14px;'>Faltan ".$Diferencia." facturas</span>";





							?>

					</td>
					</tr>
					</table>

				   <?php
                	$sql_fac_elec = "Select NumeroFactura From Factura Where FacturaElectronica = '' and IDPuntoVenta = '".$IDPuntoVenta."' and FechaFactura >= '2022-01-01' and Estado <> 'ANULADA' ";
				   $qry_fac_elec = db_query($sql_fac_elec);
				   $total_fac_elec = db_num_rows($qry_fac_elec);
				   if($total_fac_elec>0):
				   ?>
                   <br>
                   <table width="100%">
                   <?php while($row_fac_elec = db_fetch_array($qry_fac_elec)){ ?>
                   	<tr>
                    	<td style="color:#B80AB8;">
                        	ATENCION: Esta factura no se envi&oacute; a las facturacion electronica : <?php echo $row_fac_elec["NumeroFactura"] ?>                             
                        </td>
                    </tr>
                    <?php } ?>
                   </table>
                   <?php endif; ?>
			
					<table width="100%">
						<tr>
							<td>
							<?php
							  	switch(nvl($mod)){

							  		/******************* INICIO FACTURA ******************/
							  		case "Factura" :
							  			include("Factura/Factura.php");
							  		break;
							  		case "FacturaBono" :
							  			include("Factura/FacturaBono.php");
							  		break;
							  		case "vercambios" :
							  			include("Factura/Cambio.php");
							  		break;

							  		case "PagoCreditos" :
							  			include("Factura/PagoCreditos.php");
							  		break;
							  		
							  		/******************* FIN FACTURA ******************/
							  		/******************* INICIO PEDIDO ******************/
							  		case "Pedido" :
										include("Pedido/OrdenCompra.php");
							  		break;
							  		/******************* FIN PEDIDO ******************/
							  		/******************* INICIO MOVIMIENTOS ******************/
							  		case "Movimiento" :
										include("Movimiento/Movimiento.php");
							  		break;
									case "MovimientoTercero" :
										include("Movimiento/MovimientoTercero.php");
							  		break;
							  		case "IngresoOtros" :
										include("Movimiento/EntradaOtros.php");
									break;
									case "cambioreferencia" :
										include("Movimiento/CambioReferencia.php");
									break;
									case "cambioreferenciaanterior" :
										include("Movimiento/CambioReferenciaV1.php");
									break;									
									case "cambioreferenciaespecial" :
										include("Movimiento/CambioReferenciaEspecial.php");
									break;
									case "cambiofactura" :
										include("Movimiento/CambioFactura.php");
									break;
							  		case "VerMovimiento" :
										include("Movimiento/VerMovimiento.php");
							  		break;
							  		case "verentrada" :
										include("Movimiento/verentrada.php");
							  		break;
							  		/******************* FIN MOVIMIENTOS ******************/
							  		/******************* INICIO CLIENTES ******************/
							  		case "PuntosCliente" :
							  			include("Cliente/Cliente.php");
							  		break;
							  		case "BonosCliente" :
							  			include("Cliente/Bonos.php");
							  		break;									
							  		case "ComprasCliente":
							  			include("Cliente/Compras.php");
							  		break;
							  		/******************* INICIO SALIDAS ******************/
							  		case "GenerarFactura" :
							  			include("Movimiento/Factura.php");
							  		break;
							  		case "GenerarFacturaNew" :
							  			include("Movimiento/FacturaNew.php");
							  		break;
							  		case "GenerarFacturaBono" :
							  			include("Movimiento/FacturaBono.php");
							  		break;
							  		case "cambiar" :
							  			include("Movimiento/Cambio.php");
							  		break;
							  		case "SalidaMerca" :
							  			include("Movimiento/salidaotro.php");
							  		break;
							  		/******************* FIN SALIDAS ******************/
							  		/******************* INICIO TRASLADOS ******************/
							  		case "Traslado" :
										include("Traslado/Traslado.php");
							  		break;
							  		case "RecibirTraslado" :
										include("Traslado/Recibir.php");
									break;
									case "vertraslado" :
										include("Traslado/VerTraslado.php");
									break;
                                                                        case "Traslado2" :
										include("Traslado2/Traslado.php");
							  		break;
							  		case "RecibirTraslado2" :
										include("Traslado2/Recibir.php");
									break;
									case "vertraslado2" :
										include("Traslado2/VerTraslado.php");
									break;
							  		/******************* FIN TRASLADOS ******************/
							  		
							  		/******************* INICIO INVENTARIO ******************/
							  		case "Inventario" :
										include("Referencia/CodificacionEspecifica.php");
							  		break;
							  		case "InventarioCon" :
										include("Referencia/InventarioCon.php");
							  		break;
							  		case "InventarioConalm" :
										include("Referencia/InventarioConalm.php");
							  		break;
							  		case "BuscReferencia" :
										include("Referencia/InventarioConalmacen.php");
							  		break;
							  		/******************* FIN INVENTARIO ******************/
							  		
							  		/******************* INICIO REPORTES ******************/
							  		case "bonos" :
										include("Reportes/Bonos.php");
							  		break;
							  		case "diario" :
										include("Reportes/DiarioVentas.php");
							  		break;
									case "diariocredito" :
										include("Reportes/DiarioCreditos.php");
							  		break;
									case "reportecredito" :
										include("Reportes/ReporteCredito.php");
							  		break;
							  		case "pagos" :
										include("Reportes/DiarioPagos.php");
							  		break;
							  		case "mensual" :
										include("Reportes/MensualVentas.php");
							  		break;
							  		case "vendedores" :
										include("Reportes/VentasVendedor.php");
							  		break;
							  		case "pares" :
										include("Reportes/AlmacenReferenciaPares.php");
							  		break;
							  		case "inventariotarjetas" :
										include("Reportes/InventarioTarjetas.php");
							  		break;

							  		/******************* FIN REPORTES ******************/

							  		default :
							  			include("Movimiento/Factura.php");
							  		break;
							  		/******************* INICIO SALIDAS ******************/
							  		case "GenerarFactura" :
							  			include("Movimiento/Factura.php");
							  		break;
							  		case "GenerarFacturaNew" :
							  			include("Movimiento/FacturaNew.php");
							  		break;
							  		case "GenerarFacturaBono" :
							  			include("Movimiento/FacturaBono.php");
							  		break;
							  		case "cambiar" :
							  			include("Movimiento/Cambio.php");
							  		break;
							  		case "SalidaMerca" :
							  			include("Movimiento/salidaotro.php");
							  		break;
							  		/******************* FIN SALIDAS ******************/
							  		/******************* INICIO GARANTIAS ******************/
							  		case "Garantia" :
										include("Garantia/Garantia.php");
							  		break;
							  		case "SeguimientoGarantia" :
										include("Garantia/SeguimientoGarantia.php");
							  		break;
							  		case "GarantiaReporte" :
										include("Garantia/GarantiaReporte.php");
							  		break;
									/******************* FIN GARANTIAS ******************/
									/******************* INICIO CONSULTA PRECIOS ******************/
							  		case "ConsultaPrecio" :
										include("ConsultaPrecio/Precio.php");
							  		break;
									/******************* FIN SALIDAS ******************/
									
									/******************* INICIO PQR ******************/
							  		case "Pqr" :
										include("Pqr/Pqr.php");
							  		break;
							  		case "SeguimientoPqr" :
										include("Pqr/SeguimientoPqr.php");
							  		break;
							  		
									/******************* FIN PQR ******************/
									

									
							  		
							  	}// End switch
							
                                                                mysql_close($dblink); 
                                                              //  echo "seccion cerrada;"
                                                                
                                                                ?>
							
							</td>
						</tr>
						<tr>
							<td>
								<div align="center"><span class="copyright"><br />

								<table width="100%" cellpadding="0" cellspacing="0" border="0">
								  <tr>
									<td align="right"></td>
								  </tr>
								</table>
														<div id="pienomostrar" >

								<table width="100%" cellpadding="0" cellspacing="0" border="0">
								  <tr>
									<td><br>
													<img src="images/bt_left.gif" border="0" /></td>
									<td width="100%" class="indexbom" valign="bottom" align="center">
										<span class="copyright">Desarrollado por 
										<a href="http://www.solucionesdeinternetymercadeo.com/" target="_blank" class="copyright">
											Soluciones de Internet y Mercadeo Ltda.</a> &copy; 2006 <br />
											Calzado Caprino &copy; 2006<br />
														
<br />
											<br>
											<br />
											<br />
											<br />
											<br />
										</span></td>
									<td><br>
													<img src="images/bt_right.gif" border="0" /></td>
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
