<body>
<?php 

$TitleMod ="Generar Pedido Sugerido";

$Table = "SugeridoPedido";
$TableJoin = "DetalleSugeridoPedido";
$Key = "IDSugeridoPedido";
$MOD = "mgenerar";
$m = "Pedido";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			
			case "generarreferencia":
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");
				
				generarpedidoreferencia($HTTP_POST_VARS);
				
				db_query("COMMIT");
				
				echo "<SCRIPT>location.href='?mod=Pedido';</SCRIPT>";
			break ;
			
			case "generarpunto" :
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");
				
				generarpedidopuntos($HTTP_POST_VARS['PuntoVenta']);
				
				db_query("COMMIT");
				
				echo "<SCRIPT>location.href='?mod=Pedido';</SCRIPT>";
			break;
			
			default : 
				print_form("$TitleMod");
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");
/*******************************************************************************************
		funcion print_form
*******************************************************************************************/
function print_form($TitleMod)
{
	
	Global $TitleMod,$MOD,$Table,$Key;
	
?>
<script>
function CheckAll()
{	 
	for (var i=0;i< document.gpuntos.elements.length;i++)
	{
		var e = document.gpuntos.elements[i];
		if (e.name != 'allbox')
		e.checked = !e.checked;
	}
}
</script>
<br>
	<br>
	<table width=500 cellpadding=0 cellspacing=0 align=center class=bordertable>
		<tr>
			<td id=large class=row1><b><?php echo $TitleMod ?></b></td>
		</tr>
		<tr>
			<td>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td class="row1" nowrap><br>
							</td>
						</tr>
						<tr>
							<td class="row1" nowrap>
								<table width=80% cellspacing="1" cellpadding="1" bgcolor=#ffffff align=center class=bordertable>
									<tr>
										<td class="titlemedium" colspan="2">Generar Por Referencia</td>
									</tr>
									<form action="<?php echo $PHP_SELF?>" method="Post" name="greferencia">
									<tr>
										<td width="30%">Referencia</td>
										<td><input type="text" size="18" name="Referencia" id="Referencia" class="post"></td>
									</tr>
									<tr>
										<td width="30%">Punto de Venta</td>
										<td>
											<?php echo formpopup("PuntoVenta","Nombre","Nombre","IDPuntoVenta",$r->IDPuntoVenta,"input\" id=\"PuntoVenta\" onchange=\"document.greferencia.submit();"); ?>
											<input type="hidden" name="action" value="generarreferencia">
										</td>
									</tr>
									</form>
								</table>
							</td>
						</tr>
						<tr>
							<td class="row1" nowrap><br>
							</td>
						</tr>
						<tr>
							<td class="row1" nowrap>
								<table width=80% cellspacing="1" cellpadding="1" bgcolor=#ffffff align=center class=bordertable>
									<tr>
										<td class="titlemedium" colspan="2">Generar Por Punto de Venta</td>
									</tr>
									<form action="<?php echo $PHP_SELF?>" method="Post" name="gpuntos">
									<tr>
										<td><b>PUNTOS DE VENTA</b></td>
										<td>
</td>
									</tr>
									<tr>
										<td colspan="2">
											<div align="left">
												<br>
												<input type="hidden" name="action" value="generarpunto">
												<?php 
													$query_puntos = db_query( "SELECT * FROM PuntoVenta " );
													
													$puntos = array();
													
													while( $r_puntos = db_fetch_object( $query_puntos ) )
													{
													
														echo "<input type='checkbox' name='PuntoVenta[]' value='$r_puntos->IDPuntoVenta'> $r_puntos->Nombre";
														echo "<br>";
													}//end while( $r_puntos = db_fetch_object( $query_puntos ) )
												?>
												<br>
												<br>
												<a href="#" onclick="CheckAll();">Seleccionar Todos</a></div>
										</td>
									</tr>
									<tr>
										<td>
											<div align="center">
												<input type="submit" value="Generar" name="Generar" class="input"></div>
										</td>
										<td></td>
									</tr>
								</form>
								</table>
							</td>
						</tr>
						<tr>
							<td class="row1" nowrap><br>
							</td>
						</tr>
						<tr>
							<td class="row1" nowrap><br>
							</td>
						</tr>
				</table>
			</td>
		</tr>
	</table>	
<?php 
}// Enf function print_form()				
?>