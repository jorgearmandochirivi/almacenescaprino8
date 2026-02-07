<?php
	include("config.inc.php");
	Encabezado();
	$datos = Verifica_Sesion();
	$Nombre_Usuario = $datos["Nombre"];
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title><?php echo $app_title;?></title>
<link rel="stylesheet" href="styles.css?1" type="text/css">

<link rel="stylesheet" href="jscripts/choosen/chosen.css">

<link href="../default.css" rel="stylesheet" media="screen">
<script language="JavaScript1.2" src="jscripts/popcalendar.js?1"></script>
<script language="JavaScript1.2" src="jscripts/validaForm.js?1"></script>
<script>
	function showhide(id)
	{
		if (document.getElementById(id).style.display == "none")
	    {
			document.getElementById(id).style.display = "block";
		}
		else
		{
			if (document.getElementById(id).style.display == "block")
	    	{
				document.getElementById(id).style.display = "none";
			}
		}
	}
</script>

</head>
<body onload='init();' bgcolor="#E5E5E5" leftmargin="1" marginheight="1" marginwidth="1" topmargin="1" >
<table width="100%" border="0" cellspacing="0" cellpadding="2" height="99%">

  <tr height="93%">
	<td valign="top" height="93%">
					<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#e5e5e5" height="100%">
						<tr>
							<td align="left" valign="top" bgcolor="white" width="118"><i><img src="images/logosimtools.gif" alt="" height="38" width="118" border="0"><br>
									<br>
								</i>
								<table width=110 >
									<tr height="18">
										<td valign="middle" height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=NovedadBanco" class="menuppal">Bancos</a></div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=FormaPago" class="menuppal">Forma de Pago</a>
											</div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Empleado" class="menuppal">Empleados</a>
											</div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Factura" class="menuppal">Facturas</a></div>
										</td>
									</tr>


									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=TipoTalla" class="menuppal">Tallas</a></div>
										</td>
									</tr>


									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=PuntoVenta" class="menuppal">Puntos de Venta</a></div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Movimiento" class="menuppal">Movimientos</a></div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=RecibirTraslado" class="menuppal">Traslados</a></div>
										</td>
									</tr>
                                    <tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Cambios" class="menuppal">Cambios</a></div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Referencia" class="menuppal">Referencias</a></div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Pedido" class="menuppal">Pedidos</a></div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Cliente" class="menuppal">Clientes</a></div>
										</td>
									</tr>
                                                                        <tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Clientes_Ventas" class="menuppal">Clientes_Ventas</a></div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Proveedor" class="menuppal">Proveedores</a></div>
										</td>
									</tr>
									<tr height="18">
											<td height="18" background="images/botongral.gif">
												<div align="center">
													<a href="javascript:;" onClick="showhide('ContentReportes');" class="menuppal">Reportes</a>
												</div>
											</td>
									</tr>

									<tr>
										<td>
											<table id="ContentReportes" width="90%" align="right" style="display:none;">
												<tr>
													<td  align="left" > - </td>
													<td  align="left" ><a class="menuppal" href="?mod=Reportes">Diarios</a></td>
												</tr>
												<tr>
													<td  align="left" > - </td>
													<td align="left" ><a class="menuppal" href="?mod=MensualNetas">Mensuales</a></td>
												</tr>
												<tr>
													<td  align="left" > - </td>
													<td align="left" ><a class="menuppal" href="?mod=ReporteMensual">Contabilidad</a></td>
												</tr>
												<tr>
													<td  align="left" > - </td>
													<td align="left" ><a class="menuppal" href="?mod=VentasVendedor">Vendedores</a></td>
												</tr>
												<tr>
													<td  align="left" > - </td>
													<td align="left" nowrap ><a class="menuppal" href="?mod=ReporteClientes">Reporte Clientes</a></td>
												</tr>
												<tr>
													<td  align="left" > - </td>
													<td align="left" nowrap ><a class="menuppal" href="?mod=InventarioCon">Reportes Pares</a></td>
												</tr>
												<?php
												if( $Nivel == 0  || $ID_Usuario == 109)
												{
												?>
												<tr>
													<td  align="left" > - </td>
													<td align="left" nowrap ><a class="menuppal" href="?mod=VerSesion">Sesiones admin</a></td>
												</tr>
                                                <?php
												}//end if
												?>
												<tr>
													<td  align="left" > - </td>
													<td align="left" nowrap ><a class="menuppal" href="?mod=LogAcceso">Log de Accesos</a></td>
												</tr>

											</table>
										</td>
									</tr>

									<?php
									if( $Nivel == 0  || $ID_Usuario == 109 || $ID_Usuario == 504)
									{
									?>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=ReporteCreditoM" class="menuppal">Reporte Cr&eacuteditos</a></div>
										</td>
									</tr>
									<?php } ?>

									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Log" class="menuppal">LOG</a></div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=usrperm" class="menuppal">Permisos</a></div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Festivo" class="menuppal">Par&aacute;metros</a></div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Mensaje" class="menuppal">Mensajes</a></div>
										</td>
									</tr>
									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="?mod=Fidelizacion" class="menuppal">Fidelizaci&oacute;n</a></div>
										</td>
									</tr>
									<tr height="18">
									  <td height="18" background="images/botongral.gif"><div align="center"> <a href="?mod=Garantia" class="menuppal">Garant&iacute;as</a></div></td>
									  </tr>

								<tr height="18">
									  <td height="18" background="images/botongral.gif"><div align="center"> <a href="?mod=Alianza" class="menuppal">Alianzas</a></div></td>
								  </tr>

								<tr height="18">
								  <td height="18" background="images/botongral.gif"><div align="center"> <a href="?mod=PedidoTercero" class="menuppal">Pedido Tercero</a></div></td>
								  </tr>

                                  <tr height="18">
								  <td height="18" background="images/botongral.gif"><div align="center"> <a href="?mod=Pqr" class="menuppal">PQR</a></div></td>
								  </tr>


									<tr height="18">
										<td height="18" background="images/botongral.gif">
											<div align="center">
												<a href="login.php?action=LogOut" class="menuppal">LogOut</a></div>
										</td>
									</tr>
								</table>
							</td>
							<td valign="top" bgcolor="white" width="100%">
								<table width="27%" border="0" cellspacing="0" cellpadding="0" align="right" height="16">
									<tr align="right" valign="middle">
										<td width="14%"><span class="rowform"><?php echo date("F j, Y");?>
											Sesi&oacute;n Activa <?php echo usr_datos($ID_Usuario);?></span><br>
										</td>
									</tr>
								</table>
								<?php



	  	switch(nvl($mod)){
			/******************* INICIO BANCO ******************/
	  		case "mBanco" :
				include("Banco/menu.html");
	  		break;
	  		case "Banco" :
				include("Banco/menu.html");
	  			include("Banco/Banco.php");
	  		break;
	  		case "NovedadBanco" :
				include("Banco/menu.html");
	  			include("Banco/novedad.php");				
	  		break;
			  case "NovedadBancoDia" :
				include("Banco/menu.html");
	  			include("Banco/novedaddia.php");				
	  		break;
	  		/******************* FIN BANCO ******************/
	  		/******************* INICIO FORMAPAGO ******************/
	  		case "mFormaPago" :
				include("FormaPago/menu.html");
	  		break;
	  		case "FormaPago" :
				include("FormaPago/menu.html");
	  			include("FormaPago/FormaPago.php");
	  		break;
	  		case "FormaPagoBono" :
				include("FormaPago/menu.html");
	  			include("FormaPago/FormaPagoBono.php");
	  		break;
	  		/******************* FIN FORMAPAGO ******************/
	  		/******************* INICIO FACTURA ******************/
			case "mFactura" :
				include("Factura/menu.html");
	  		break;
	  		case "Factura" :
				include("Factura/menu.html");
	  			include("Factura/Factura.php");
	  		break;
	  		case "Factura_3_X_2" :
				include("Factura/menu.html");
	  			include("Factura/Factura3x2.php");
	  		break;
	  		case "ProductoGratis" :
				include("Factura/menu.html");
	  			include("Factura/ProductoGratis.php");
	  		break;
	  		case "DetalleFactura" :
				include("Factura/menu.html");
	  			include("Factura/DetalleFactura.php");
	  		break;
	  		case "BorrarFactura" :
				include("Factura/menu.html");
	  			include("Factura/BorrarFactura.php");
	  		break;
	  		/******************* FIN FACTURA ******************/
                    	/******************* ClIENTES_VENTAS ******************/
			case "mClientes_Ventas" :
				include("Clientes_Ventas/menu.html");
	  		break;
	  		case "Clientes_Ventas" :
				include("Clientes_Ventas/menu.html");
	  			include("Clientes_Ventas/Clientes_Ventas.php");
	  		break;
	  		case "DetalleClientes_Ventas" :
				include("Clientes_Ventas/menu.html");
	  			include("Clientes_Ventas/DetalleClientes_Ventas.php");
	  		break;

	  		/******************* CLIENTES_VENTAS ******************/
	  		/******************* INICIO TALLA ******************/
	  		case "mTalla" :
				include("Talla/menu.html");
	  		break;
	  		case "Talla" :
				include("Talla/menu.html");
	  			include("Talla/Talla.php");
	  		break;
	  		case "TipoTalla" :
				include("Talla/menu.html");
	  			include("Talla/TipoTalla.php");
	  		break;
	  		/******************* FIN TALLA ******************/

                    /******************* INICIO TALLAFredy ******************/
	  		case "mTallafredy" :
				include("Tallafredy/menu.html");
	  		break;
	  		case "Tallafredy" :
				include("Tallafresy/menu.html");
	  			include("Talla/Talla.php");
	  		break;
	  		case "TipoTallafredy" :
				include("Tallafredy/menu.html");
	  			include("Tallafredy/TipoTalla.php");
	  		break;
	  		/******************* FIN TALLAFredy ******************/
	  		/******************* INICIO EMPLEADO ******************/
	  		case "mEmpleado" :
	  			include("Empleado/menu.html");
	  		break;
	  		case "Cargo" :
	  			include("Empleado/menu.html");
				include("Empleado/Cargo.php");
	  		break;
	  		case "Comision" :
	  			include("Empleado/menu.html");
				include("Empleado/Comision.php");
	  		break;
	  		case "Empleado" :
	  			include("Empleado/menu.html");
				include("Empleado/Empleado.php");
	  		break;
	  		case "CalcularComision" :
	  			include("Empleado/menu.html");
				include("Empleado/calcularcomision.php");
	  		break;
	  		/******************* FIN EMPLEADO ******************/
	  		/******************* INICIO PUNTO VENTA ******************/
	  		case "mPuntoVenta" :
				include("PuntoVenta/menu.html");
	  		break;
	  		case "TipoPuntoVenta" :
				include("PuntoVenta/menu.html");
	  			include("PuntoVenta/TipoPuntoVenta.php");
	  		break;
	  		case "PuntoVenta" :
				include("PuntoVenta/menu.html");
	  			include("PuntoVenta/PuntoVenta.php");
	  		break;
	  		case "Ciudad" :
	  			include("PuntoVenta/menu.html");
				include("PuntoVenta/Ciudad.php");
	  		break;
	  		/******************* FIN PUNTO VENTA ******************/
	  		/******************* INICIO REFERENCIA ******************/
	  		case "mReferencia" :
	  			include("Referencia/menu.html");
	  		break;
	  		case "Tipo" :
	  			include("Referencia/menu.html");
				include("Referencia/Tipo.php");
	  		break;
	  		case "TipoReferencia" :
	  			include("Referencia/menu.html");
				include("Referencia/TipoReferencia.php");
	  		break;
	  		case "Linea" :
	  			include("Referencia/menu.html");
				include("Referencia/Linea.php");
	  		break;
	  		case "Color" :
	  			include("Referencia/menu.html");
				include("Referencia/Color.php");
	  		break;
			case "Tipologia" :
	  			include("Referencia/menu.html");
				include("Referencia/Tipologia.php");
	  		break;
	  		case "Cuero" :
	  			include("Referencia/menu.html");
				include("Referencia/Cuero.php");
	  		break;
	  		case "Precio" :
	  			include("Referencia/menu.html");
				include("Referencia/Precio.php");
	  		break;
	  		case "Referencia" :
	  			include("Referencia/menu.html");
				include("Referencia/Referencia.php");
	  		break;
			case "Capellada" :
				include("Referencia/menu.html");
			  include("Referencia/Capellada.php");
			break;
			case "Forro" :
				include("Referencia/menu.html");
			  include("Referencia/Forro.php");
			break;
			case "Plantilla" :
				include("Referencia/menu.html");
			  include("Referencia/Plantilla.php");
			break;
			case "Suela" :
				include("Referencia/menu.html");
			  include("Referencia/Suela.php");
			break;
			case "Altura" :
				include("Referencia/menu.html");
			  include("Referencia/Altura.php");
			break;
	  		/******************* FIN REFERENCIA ******************/
	  		/******************* INICIO INVENTARIO ******************/
	  		case "Inventario" :
	  			include("Referencia/menu.html");
				include("Referencia/Inventario.php");
	  		break;
	  		case "CodEspecifica" :
	  			include("Referencia/menu.html");
				include("Referencia/CodificacionEspecifica.php");
	  		break;
	  		case "CostoReferencia" :
	  			include("Referencia/menu.html");
				include("Referencia/CostoReferencia.php");
	  		break;
			case "CargaCostoReferencia" :
	  			include("Referencia/menu.html");
				include("Referencia/CargaCostoReferencia.php");
	  		break;
	  		case "Referencia" :
	  			include("Referencia/menu.html");
				include("Referencia/Referencia.php");
	  		break;
	  		/******************* FIN INVENTARIO ******************/
	  		/******************* INICIO PEDIDO ******************/
	  		case "mPedido" :
	  			include("Pedido/menu.php");
	  		break;
	  		case "EstadoPedido" :
	  			include("Pedido/menu.php");
				include("Pedido/EstadoPedido.php");
	  		break;
	  		case "Pedido" :
	  			include("Pedido/menu.php");
				include("Pedido/OrdenCompra.php");
	  		break;
	  		case "Sugerido" :
	  			include("Pedido/menu.php");
				include("Pedido/SugeridoPedido.php");
	  		break;
	  		case "generarporreferencia" :
	  			include("Pedido/menu.php");
				include("Pedido/generarporreferencia.php");
	  		break;
	  		case "mgenerar" :
	  			include("Pedido/menu.php");
				include("Pedido/generarsugeridos.php");
	  		break;
	  		/******************* FIN PEDIDO ******************/
	  		/******************* INICIO MOVIMIENTOS ******************/
	  		case "mMovimiento" :
	  			include("Movimiento/menu.php");
	  		break;
	  		case "TipoMovimiento" :
	  			include("Movimiento/menu.php");
				include("Movimiento/TipoMovimiento.php");
	  		break;
	  		case "Ajuste" :
	  			include("Movimiento/menu.php");
				include("Movimiento/Ajuste.php");
	  		break;
	  		case "mEntradas" :
	  			include("Movimiento/menu.php");
				include("Movimiento/menuentradas.php");
	  		break;
	  		case "Movimiento" :
	  			include("Movimiento/menu.php");
	  			include("Movimiento/menuentradas.php");
				include("Movimiento/VerMovimiento.php");
	  		break;
	  		case "Entrada" :
	  			include("Movimiento/menu.php");
				include("Movimiento/Entrada.php");
	  		break;
	  		case "IngresoOtros" :
	  			include("Movimiento/menu.php");
	  			include("Movimiento/menuentradas.php");
				include("Movimiento/EntradaOtros.php");
			break;
	  		case "VerMovimiento" :
	  			include("Movimiento/menu.php");
				include("Movimiento/VerMovimiento.php");
	  		break;
			case "TarjetaPunto" :
	  			include("Movimiento/menu.php");
				include("Movimiento/TarjetaPunto.php");
	  		break;
	  		/******************* FIN MOVIMIENTOS ******************/
	  		/******************* INICIO TRASLADOS ******************/
	  		case "Traslado" :
	  			include("Traslado/menu.html");
				include("Traslado/Traslado.php");
	  		break;
	  		case "RecibirTraslado" :
	  			include("Traslado/menu.html");
				include("Traslado/Recibir.php");
			break;
	  		/******************* FIN TRASLADOS ******************/
	  		/******************* INICIO SALIDAS ******************/
			 case "Cambios" :
	  			include("Cambio/menu.html");
					include("Cambio/Recibir.php");
			break;
			case "FacturaBono" :
				 include("Cambio/menu.html");
				 include("Cambio/FacturaBono.php");
		 break;

	  		case "Salidas" :
	  			include("Movimiento/menu.php");
	  			include("Movimiento/menusalidas.php");
	  			include("Movimiento/generarfac.php");
	  		break;
	  		case "GenerarFactura" :
	  			include("Movimiento/menu.php");
	  			include("Movimiento/menusalidas.php");
	  			include("Movimiento/Factura.php");
	  		break;
	  		case "SalidaMerca" :
	  			include("Movimiento/menu.php");
	  			include("Movimiento/menusalidas.php");
	  			include("Movimiento/salidaotro.php");
	  		break;
	  		/******************* FIN SALIDAS ******************/
	  		/******************* INICIO CLIENTE******************/
	  		case "mCliente" :
				include("Cliente/menu.html");
	  		break;
	  		case "Cliente" :
	  			include("Cliente/menu.html");
				include("Cliente/Cliente.php");
	  		break;
	  		case "PuntosCliente" :
	  			include("Cliente/menu.html");
				include("Cliente/PuntosCliente.php");
	  		break;
				case "ReporteCliente" :
	  			include("Cliente/menu.html");
					include("Cliente/ReporteCliente.php");
	  		break;
	  		/******************* FIN CLIENTE ******************/
	  		/******************* INICIO PROVEEDOR ******************/
	  		case "mProveedor" :
				include("Proveedor/menu.html");
	  		break;
	  		case "Proveedor" :
				include("Proveedor/menu.html");
				include("Proveedor/Proveedor.php");
	  		break;
	  		/******************* FIN PROVEEDOR ******************/
	  		/******************* INICIO PERMISOS *****************/
	  		case "usrperm":

		  			include("Permisos/menu.html");
		  			include("Permisos/grupos.php");
	  		break;
	  		case "ListModulos":
		  			include("Permisos/menu.html");
		  			include("Permisos/ListModulos.php");
	  		break;
	  		case "ListEmpleados":
		  			include("Permisos/menu.html");
		  			include("Permisos/menugrupo.php");
		  			include("Permisos/ListEmpleados.php");
	  		break;
	  		case "AddEmpleados":
		  			include("Permisos/menu.html");
		  			include("Permisos/menugrupo.php");
		  			include("Permisos/AddEmpleados.php");
	  		break;
	  		case "tools":
		  			include("Permisos/menu.html");
		  			include("Permisos/tools.php");
	  		break;
	  		case "Modulo":
		  			include("Permisos/menu.html");
		  			include("Permisos/Modulo.php");
	  		break;
	  		/******************* FIN PERMISOS *****************/
	  		/************************ LOG **********************************/
	  		case "mLog" :
	  			include("log/menu.html");
	  		break;
	  		case "Log" :
	  			include("log/menu.html");
	  			include("log/log.php");
	  		break;
	  		/******************* FIN LOG ******************/
	  		/************************ REPORTES **********************************/
	  		case "Reportes" :
	  			include("Reportes/menu.php");
	  			include("Reportes/DiarioVentas.php");
	  		break;
	  		case "DiarioVentasAlmacen" :
	  			include("Reportes/menu.php");
	  			include("Reportes/DiarioVentasAlmacen.php");
	  		break;
	  		case "ReportePagos" :
	  			include("Reportes/menu.php");
	  			include("Reportes/DiarioPagos.php");
	  		break;
			case "ReporteCostoReferencia" :
	  			include("Reportes/menucontabilidad.php");
				include("Reportes/ReporteCostoReferencia.php");
	  		break;
			case "ReporteCostoInventario" :
	  			include("Reportes/menucontabilidad.php");
				include("Reportes/ReporteCostoInventario.php");
	  		break;
	  		case "ReporteMensual" :
	  			include("Reportes/menucontabilidad.php");
	  			include("Reportes/menumensualvent.php");
	  			include("Reportes/MensualVentas.php");
	  		break;
	  		case "ConsultaPagos" :
	  			include("Reportes/menucontabilidad.php");
	  			include("Reportes/ConsultaPagos.php");
	  		break;
				case "ConsultaAlianzas" :
	  			include("Reportes/menucontabilidad.php");
	  			include("Reportes/ConsultaAlianzas.php");
	  		break;
	  		case "ConsultaFacturaPares" :
	  			include("Reportes/menucontabilidad.php");
	  			include("Reportes/ConsultaFacturaPares.php");
	  		break;

	  		case "MensualNetas" :
	  			include("Reportes/menumensuales.php");
	  			include("Reportes/MensualVentasNetas.php");
	  		break;
			case "MensualCreditos" :
	  			include("Reportes/menumensuales.php");
	  			include("Reportes/MensualCreditos.php");
	  		break;
	  		case "ExportaMensual" :
	  			include("Reportes/menumensuales.php");
	  			include("Reportes/ExportaMensual.php");
	  		break;
	  		case "NetasAlmacenes" :
	  			include("Reportes/menumensuales.php");
	  			include("Reportes/NetasAlmacenes.php");
	  		break;
	  		case "ReporteVMensual" :
	  			include("Reportes/menucontabilidad.php");
	  			include("Reportes/menumensualvent.php");
	  			include("Reportes/MensualVentasTotales.php");
	  		break;
	  		case "VentasTotales" :
	  			include("Reportes/menumensual.php");
	  			include("Reportes/VentasTotales.php");
	  		break;
	  		case "TotalesAlmacen" :
	  			include("Reportes/menumensuales.php");
	  			include("Reportes/menumensual.php");
	  			include("Reportes/TotalesAlmacen.php");
	  		break;
	  		case "ConsolidadoVentas" :
	  			include("Reportes/menupares.php");
	  			include("Reportes/ConsolidadoVentas.php");
	  		break;
			case "ConsolidadoVentasTercero" :
	  			include("Reportes/menupares.php");
	  			include("Reportes/ConsolidadoVentasTercero.php");
	  		break;
			case "RotacionReferencia" :
				include("Reportes/menupares.php");
	  			include("Reportes/RotacionReferencia.php");
	  		break;
			  case "Kardex" :
				include("Reportes/menupares.php");
	  			include("Reportes/Kardex.php");
	  		break;
	  		case "RotacionInventario" :
	  			include("Reportes/menupares.php");
	  			include("Reportes/RotacionInventario.php");
	  		break;
			case "RotacionInventarioGral" :
	  			include("Reportes/menupares.php");
	  			include("Reportes/RotacionInventarioGral.php");
	  		break;
	  		case "TotalesAlmacenReferencia" :
	  			include("Reportes/menumensuales.php");
	  			include("Reportes/menumensual.php");
	  			include("Reportes/AlmacenReferencia.php");
	  		break;
	  		case "TotalesAlmacenReferenciaPares" :
	  			include("Reportes/menumensuales.php");
	  			include("Reportes/menumpares.php");
	  			include("Reportes/AlmacenReferenciaPares.php");
	  		break;
	  		case "VentasVendedor" :
	  			include("Reportes/menuvendedores.php");
	  			include("Reportes/VentasVendedor.php");
	  		break;
				case "VentasVendedorIndividual" :
	  			include("Reportes/menuvendedores.php");
	  			include("Reportes/VentasVendedorIndividual.php");
	  		break;
			case "RotacionVendedor" :
	  			include("Reportes/menuvendedores.php");
	  			include("Reportes/RotacionVendedor.php");
	  		break;
	  		case "InventarioCon" :
	  			include("Reportes/menupares.php");
	  			include("Reportes/InventarioCon.php");
	  		break;
	  		case "ExportaCodigo" :
	  			include("Reportes/menupares.php");
	  			include("Reportes/ExportaCodigo.php");
	  		break;
	  		case "ExportaAuditoria" :
	  			include("Reportes/menupares.php");
	  			include("Reportes/ExportaAuditoria.php");
	  		break;
	  		case "ReporteClientes" :
	  			include("Reportes/menuclientes.php");
	  			include("Reportes/ReporteClientes.php");
	  		break;
	  		case "InventarioConAlmacen" :
	  			include("Reportes/menupares.php");
	  			include("Reportes/InventarioConalmacen.php");
	  		break;
	  		case "ReferenciaPrecio" :
	  			include("Reportes/menucontabilidad.php");
	  			include("Reportes/ReferenciaPrecio.php");
	  		break;
			case "ReferenciaPrecioActual" :
	  			include("Reportes/menucontabilidad.php");
	  			include("Reportes/ReferenciaPrecioActual.php");
	  		break;
	  		case "ReporteCredito" :
	  			include("Reportes/menucontabilidad.php");
	  			include("Reportes/ReporteCredito.php");
	  		break;
				case "ReporteCreditoM" :
	  			include("Reportes/ReporteCredito.php");
	  		break;
	  		case "ReporteCreditoAbonos" :
	  			include("Reportes/menucontabilidad.php");
	  			include("Reportes/ReporteCreditoAbonos.php");
	  		break;
	  		case "VerSesion" :
	  			include("Reportes/menupares.php");
	  			include("Reportes/VerSesion.php");
	  		break;
	  		case "LogAcceso" :
	  			include("Reportes/menupares.php");
	  			include("Reportes/LogAcceso.php");
	  		break;
			case "InventarioTarjetas" :
	  			include("Reportes/menupares.php");
	  			include("Reportes/InventarioTarjetas.php");
	  		break;
			case "ReporteTalla" :
	  			include("Reportes/menupares.php");
	  			include("Reportes/ReporteTalla.php");
	  		break;
	  		/******************* FIN REPORTES ******************/
	  		/******************* INICIO PARAMETROS ******************/
	  		case "Festivo" :
				include("Parametros/menu.html");
	  			include("Parametros/Festivos.php");
	  		break;
	  		case "DEspeciales" :
				include("Parametros/menu.html");
	  			include("Parametros/DEspeciales.php");
	  		break;

			case "DiasSinIva" :
				include("Parametros/menu.html");
	  			include("Parametros/DiasSinIva.php");
	  		break;

			case "LinkCambios" :
				include("Parametros/menu.html");
	  			include("Parametros/LinkCambios.php");
	  		break;

	  		case "ValorPuntos" :
				if( $Nivel == 0 )
	  			{
					include("Parametros/menu.html");
					include("Parametros/ValorPuntos.php");
				}//end if nivel
	  		break;
			  case "TiendaPromocionSegundoPar" :
				include("Parametros/menu.html");
	  			include("Parametros/TiendaPromocionSegundoPar.php");
	  		break;
	  		/******************* FIN FORMAPAGO ******************/
	  		/******************* INICIO PARAMETROS ******************/
	  		case "Mensaje" :
				include("Mensaje/menu.html");
	  			include("Mensaje/Mensaje.php");
	  		break;
	  		/******************* FIN FORMAPAGO ******************/

			/******************* INICIO FIDELIZACION ******************/
	  		case "Fidelizacion" :
	  			include("Fidelizacion/menu.html");
				include("Fidelizacion/ReglaPunto.php");
	  		break;
	  		case "ParametroFidelizacion" :
				include("Fidelizacion/menu.html");
				include("Fidelizacion/ParametroFidelizacion.php");
	  		break;
	  		case "ClienteFidelizado" :
				include("Fidelizacion/menu.html");
				include("Fidelizacion/ClienteFidelizado.php");
	  		break;
			case "PuntosClienteFidelizacion" :
	  			include("Fidelizacion/menu.html");
				include("Fidelizacion/PuntosClienteFidelizacion.php");
	  		break;
			case "ExcedenteFidelizacion" :
	  			include("Fidelizacion/menu.html");
				include("Fidelizacion/ExcedenteFidelizacion.php");
	  		break;
			case "BonoFidelizacionCliente" :
	  			include("Fidelizacion/menu.html");
				include("Fidelizacion/BonoFidelizacionCliente.php");
	  		break;
			case "BonoFidelizado" :
	  			include("Fidelizacion/menu.html");
				include("Fidelizacion/BonoFidelizacion.php");
	  		break;
			case "PlanContacto" :
	  			include("Fidelizacion/menu.html");
				include("Fidelizacion/PlanContacto.php");
	  		break;
			case "EmailFidelizacion" :
	  			include("Fidelizacion/menu.html");
				include("Fidelizacion/EmailFidelizacion.php");
	  		break;
			case "TarjetaFidelizacion" :
	  			include("Fidelizacion/menu.html");
				include("Fidelizacion/TarjetaFidelizacion.php");
	  		break;
				case "TarjetaRegalo" :
		  			include("Fidelizacion/menu.html");
					include("Fidelizacion/TarjetaRegalo.php");
		  		break;



	  		/******************* FIN FIDELIZACION ******************/


			/******************* INICIO GARANTIAS ******************/
	  		case "Garantia" :
	  			include("Garantia/menu.html");
				include("Garantia/Garantia.php");
	  		break;
	  		case "TipoFinalizacionGarantia" :
	  			include("Garantia/menu.html");
				include("Garantia/TipoFinalizacionGarantia.php");
	  		break;
	  		case "EstadoGarantia" :
	  			include("Garantia/menu.html");
				include("Garantia/EstadoGarantia.php");
	  		break;
	  		case "ParametroGarantia" :
	  			include("Garantia/menu.html");
				include("Garantia/ParametroGarantia.php");
	  		break;

	  		case "GarantiaReporte" :
	  			include("Garantia/menu.html");
				include("Garantia/menu_reporte.php");
				include("Garantia/GarantiaReporte.php");
	  		break;

			case "GarantiaReporteTiempo" :
	  			include("Garantia/menu.html");
				include("Garantia/menu_reporte.php");
				include("Garantia/GarantiaReporteTiempo.php");
	  		break;


			/******************* FIN GARANTIAS ******************/


			/******************* INICIO ALIANZAS ******************/
	  		case "Alianza" :
	  			include("Alianza/menu.html");
				include("Alianza/Alianza.php");
	  		break;

	  		case "AlianzaReporte" :
	  			include("Alianza/menu.html");
				include("Alianza/AlianzaReporte.php");
	  		break;


			/******************* FIN ALIANZAS ******************/

			/******************* INICIO PEDIDO TERCEROS ******************/

			case "ParametroTercero" :
				include("PedidoTercero/menu.html");
				include("PedidoTercero/ParametroTercero.php");
	  		break;

			case "EntregaTercero" :
				include("PedidoTercero/menu.html");
				include("PedidoTercero/EntregaTercero.php");
	  		break;

			case "CurvaTercero" :
				include("PedidoTercero/menu.html");
				include("PedidoTercero/CurvaTercero.php");
	  		break;

			case "PedidoTercero" :
	  			include("PedidoTercero/menu.html");
				include("PedidoTercero/PedidoTercero.php");
	  		break;

			case "ReportePedidoTercero" :
	  			include("PedidoTercero/menu.html");
				include("PedidoTercero/PedidoTerceroReporte.php");
	  		break;



			/******************* FIN PEDIDO TERCEROS ******************/


			/******************* INICIO PQR ******************/

			case "Pqr" :
				include("Pqr/menu.html");
				include("Pqr/Pqr.php");
	  		break;

			case "MotivoPqr" :
				include("Pqr/menu.html");
				include("Pqr/MotivoPqr.php");
	  		break;

			case "TipoPqr" :
				include("Pqr/menu.html");
				include("Pqr/TipoPqr.php");
	  		break;

			case "FuentePqr" :
				include("Pqr/menu.html");
				include("Pqr/FuentePqr.php");
	  		break;

			case "AreaPqr" :
				include("Pqr/menu.html");
				include("Pqr/AreaPqr.php");
	  		break;
			/******************* FIN PQR ******************/



	  		/************************ DATA **********************************/
	  		case "Importar" :
	  			if( $Nivel == 0 )
	  			{
		  			include("db/menu.html");
		  			include("db/Importar.php");
	  			}//end if
	  		break;
	  		case "fixed" :
	  			if( $Nivel == 0 )
	  			{
		  			include("db/menu.html");
		  			include("db/fixed.php");
	  			}//end if
	  		break;

	  		/******************* FIN DATA ******************/


			case "puntoref" :
	  			if( $Nivel == 0 )
	  			{
		  			include("db/menu.html");
		  			include("db/puntoref.php");
	  			}//end if
	  		break;
	  		case "borrarpuntoref" :
	  			if( $Nivel == 0 )
	  			{
		  			include("db/menu.html");
		  			include("db/borrarpuntoref.php");
	  			}//end if
	  		break;
	  		case "dbcodificacion" :
	  			if( $Nivel == 0 )
	  			{
		  			include("db/menu.html");
		  			include("db/dbcodificacion.php");
	  			}//end if
	  		break;
	  		case "depurar_precios" :
	  			if( $Nivel == 0 )
	  			{
		  			include("db/menu.html");
		  			include("db/depurar_precios.php");
	  			}//end if
	  		break;
	  		case "eliminartraslados" :
	  			if( $Nivel == 0 )
	  			{
		  			include("db/menu.html");
		  			include("db/eliminartraslados.php");
	  			}//end if
	  		break;
	  		case "dbmaximos" :
	  			if( $Nivel == 0 )
	  			{
		  			include("db/menu.html");
		  			include("db/Importarmax.php");
	  			}//end if
	  		break;

	  	}// End switch
                mysqli_close($dblink);
               // echo "seccion cerrada;"
	  ?></td>
						</tr>
					</table>
				</td>
		</tr>
			<tr height="37">
				<td class="bodyline" bgcolor="#FFFFFF" height="37">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" height="37">
						<tr height="37">
							<td class="bgBottom" height="37" align="center">
								<span class="siteBotLinks">
								<a href="?mod=Banco" class="siteBotLinks">Banco</a> | <a href="?mod=usr" class="siteBotLinks">Usuarios</a><br>
								</span>
								<span class="gen">&nbsp;</span>
								<span class="copyright">&copy; Copyright 2005
									<a href="(EmptyReference!)" class="copyright">Soluciones de Internet y Mercadeo</a>
								</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="jscripts/sim.js?a=<?php  echo rand(1,100000);?>"></script>


	</body>
</html>
