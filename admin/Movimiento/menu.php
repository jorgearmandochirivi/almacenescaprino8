<body>
	<?
	$m = "Movimientos";
	?>
	<table cellspacing="0" cellpadding="2" border="0" align="center" width="100%" bgcolor="#FFFFFF">
		<tr>
			<td id="large" colspan="2" ><img src="images/folders.gif" border="0" alt=""> Movimientos</td>
		</tr>
		<tr>
			<td id="subtitle" colspan="2"></td>
		</tr>
		<tr>
			<td style="border-bottom:1px solid #4C77B6">
				<table width="100%" border="0" cellspacing="2" cellpadding="0">
					<tr>
						<td width="32"><img src="images/nav.gif" border="0" alt=""></td>
						<td width="70%" class=nav  >
							<a href="?mod=TipoMovimiento">Tipos de Movimiento</a> |
							<a href="?mod=VerMovimiento">Movimientos</a> |
							<a href="?mod=Ajuste">Ajustes de Inventario</a> |
							<a href="?mod=Entrada">Entradas</a> |
							<a href="?mod=TarjetaPunto">Tarjetas Puntos de Venta</a> |
						</td>
						<td class=nav>
							<form action="?mod=Movimiento" method="Post" name="formularioOrden">
								<div align="right">
									Orden No.
									<input type="text" size="10" name="Orden" id="Orden" class="post"> 
									<input type="submit" name="submit" value="Generar" class="submit">
								</div>
							</form>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>

</html>