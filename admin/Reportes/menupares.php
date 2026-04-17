<script>showhide('ContentReportes');</script>
<?php $mostrarVentasXTalla = false; ?>
<body>
	<table cellspacing="0" cellpadding="2" border="0" align="center" width="100%" bgcolor="#FFFFFF">
		<tr>
			<td id="large" colspan="2" ><img src="images/indicadores.gif" border="0" alt=""> Reportes</td>
		</tr>
		<tr>
			<td id="subtitle" colspan="2"></td>
		</tr>
		<tr>
			<td width="1%" valign="middle" style="border-bottom:1px solid #4C77B6"><img src="images/nav.gif" border="0" alt=""></td>
			<td width="85%" align="left" valign="middle" class="nav" style="border-bottom:1px solid #4C77B6">
				<a href="./?mod=InventarioCon">Inventario</a> |
				<a href="./?mod=InventarioCon&analisis=1">Inventario Analisis</a> |
				<a href="./?mod=InventarioConAlmacen">Inventario x Referencia</a> |
				<?php if($mostrarVentasXTalla){ ?><a href="./?mod=ReporteTalla">Ventas x Talla</a> |<?php } ?>
				<a href="./?mod=ConsolidadoVentas">Ventas x Referencia</a> |
                <a href="./?mod=ConsolidadoVentasTercero">Ventas Terceros x Referencia</a> |
				<!--
				<a href="./?mod=RotacionInventario">Rotaci&oacute;n</a> |
				<a href="./?mod=RotacionInventarioGral">Rotaci&oacute;n General</a> |
				-->
                <a href="./?mod=RotacionReferencia">Rotaci&oacute;n Referencia</a> |
                <a href="./?mod=InventarioTarjetas">Inventario Tarjetas</a> |
				<a href="./?mod=ExportaCodigo">Exportar C&oacute;digos</a> |
				<a href="./?mod=ExportaAuditoria">Exportar Auditoria</a> |
				<a href="./?mod=Kardex">Kardex Auditoria</a> 
			</td>
		</tr>
		</table>
</body>

</html>
