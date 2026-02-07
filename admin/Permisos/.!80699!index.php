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
<title><?php  echo $app_title;?></title>
<link rel="stylesheet" href="styles.css?1" type="text/css">
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
												if( $Nivel == 0 )
												{
												?>
												<tr>
													<td  align="left" > - </td>
													<td align="left" nowrap ><a class="menuppal" href="?mod=VerSesion">Sesiones admin</a></td>
												</tr>
												<?php 
												}//end if
												?>
											</table>
										</td>
									</tr>
									
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
