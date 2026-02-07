<body> <?
$TitleMod ="Referencia";

$Table = "Referencia";
$TableJoin = "CodificacionEspecifica";
$Key = "IDReferencia";
$MOD = "Referencia";
$m="db";

		switch (nvl($action)) {
			case "formapago" :
				
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");

				$sql_factura = " select FechaFactura, IDFactura, IDPuntoVenta, NumeroFactura, ValorTotal FROM Factura WHERE IDPuntoVenta = '$_POST[IDPuntoVenta]' 
									AND NumeroFactura = '$_POST[NumeroFactura]' ";
				$qry_factura = db_query( $sql_factura );
				$r_factura = db_fetch_object( $qry_factura );
				
				if( $r_factura->IDFactura > 0 )
				{
					$sql_formapago = " select * FROM PuntoVentaBanco WHERE IDPuntoVenta = '$_POST[IDPuntoVenta]' AND IDFormaPago = '$_POST[IDFormaPago]'  ";
					$qry_formapago = db_query( $sql_formapago );
					if( db_num_rows( $qry_formapago ) > 0 )
					{
						$r_formapago = db_fetch_object( $qry_formapago );
						$IDFormaPagoFactura = get_maxID( "FormaPagoFactura","IDFormaPagoFactura" );
						$sql_insert = "INSERT INTO FormaPagoFactura ( IDFormaPagoFactura, IDFormaPago, IDFactura, IDPuntoVenta, Valor, Comision  ) 
										VALUES ( '$IDFormaPagoFactura','$_POST[IDFormaPago]','$r_factura->IDFactura','$_POST[IDPuntoVenta]','$_POST[Valor]',
										'$r_formapago->Comision' ) ";
						$qry_insert = db_query( $sql_insert );
						echo " FACTURA ACTUALIZADA CORRECTAMENTE ";
					}//end if
					else
						echo " NO ESTA ASIGNADA ESTA FORMA DE PAGO. REVISE POR FAVOR ";
				}//end if
				else
					echo " ESTA FACTURA NO EXISTE. REVISE POR FAVOR. ";
				
				//db_query( "tales" );
				db_query("COMMIT");
				
				print_form("","Refeencia","Importar Archivos","submit");
			break;	
			case "fecha" :
			
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");
				
				echo $sql_update = "UPDATE Factura SET FechaFactura = '$_POST[Fecha]' WHERE IDPuntoVenta = '$_POST[IDPuntoVenta]' AND NumeroFactura = '$_POST[NumeroFactura]'";
				db_query( $sql_update );
				echo " FACTURA ACTUALIZADA CORRECTAMENTE ";
				
				//db_query( "tales" );
				db_query("COMMIT");
				
				print_form("","Refeencia","Importar Archivos","submit");
			break;	
			
			
			
			case "VFormasPago" :
				
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");

				$sql_factura = " select FechaFactura, IDFactura, IDPuntoVenta, NumeroFactura, ValorTotal FROM Factura WHERE IDPuntoVenta = '$_POST[IDPuntoVenta]' 
									AND NumeroFactura = '$_POST[NumeroFactura]' ";
				$qry_factura = db_query( $sql_factura );
				$r_factura = db_fetch_object( $qry_factura );
				
				
				db_query("COMMIT");
				
				VerificaFormas($r_factura->IDFactura,$_POST[IDPuntoVenta]);
			break;	
			
			
			case "factura" :
			
				
				
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");
				
				
				$sql_factura = "select FechaFactura, IDFactura, IDPuntoVenta, NumeroFactura, ValorTotal FROM Factura WHERE IDPuntoVenta = '$_POST[IDPuntoVenta]' AND NumeroFactura = '$_POST[NumeroFactura]'";
				$qry_factura = db_query( $sql_factura );
				$r_factura = db_fetch_object( $qry_factura );
								
				$sql_detalle = "SELECT * FROM DetalleFactura WHERE IDFactura = '$r_factura->IDFactura' AND IDPuntoVenta = '$_POST[IDPuntoVenta]'";
				$qry_detalle = db_query(  $sql_detalle );
				while( $r_detalle = db_fetch_object( $qry_detalle ) )
				{
				
					$sql_cod = "UPDATE CodificacionEspecifica SET Existencias = Existencias + $r_detalle->Cantidad WHERE IDCodificacionEspecifica = '$r_detalle->IDCodificacionEspecifica'";
					$qry_cod = db_query( $sql_cod );
				
				}//end while
				
				
				$sql_forma = "DELETE FROM FormaPagoFactura WHERE IDPuntoVenta = '$_POST[IDPuntoVenta]' AND IDFactura = '$r_factura->IDFactura'";
				$qry_forma = db_query( $sql_forma );
				
				$sql_bdetalle = "DELETE FROM DetalleFactura WHERE IDPuntoVenta = '$_POST[IDPuntoVenta]' AND IDFactura = '$r_factura->IDFactura'";
				$qry_bdetalle = db_query( $sql_bdetalle );
				
				$sql_bvendedor = "DELETE FROM VentasEmpleado WHERE IDPuntoVenta = '$_POST[IDPuntoVenta]' AND IDFactura = '$r_factura->IDFactura'";
				$qry_bvendedor = db_query( $sql_bvendedor );
				
				$sql_bfactura = "DELETE FROM Factura WHERE IDPuntoVenta = '$_POST[IDPuntoVenta]' AND IDFactura = '$r_factura->IDFactura'";
				$qry_bfactura = db_query( $sql_bfactura );
				
				echo " FACTURA BORRADA CORRECTAMENTE ";
				
				//db_query( "tales" );
				db_query("COMMIT");
				
				print_form("","Refeencia","Importar Archivos","submit");
				
			break;	
			case "borraforma" :
			
				
				
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");
				
				
				echo $sql_factura = "delete FROM FormaPagoFactura WHERE IDFormaPagoFactura = '$_GET[IDFormaPagoFactura]' AND IDFactura = '$_GET[IDFactura]' AND IDPuntoVenta = '$_GET[IDPuntoVenta]' ";
				$qry_factura = db_query( $sql_factura );
								
				
				
				//db_query( "tales" );
				db_query("COMMIT");
				
				print_form("","Refeencia","Importar Archivos","submit");
				
			break;
			default : 
					print_form("","Refeencia","Importar Archivos","submit");
			break;
		
		} // End switch
		
	



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<br>

<table cellpadding=1 cellspacing=0 class=bordertable align=left >
	<tr>
			<td class=maintitle bgcolor=#9daac6>Inconvenientes Facturas</td>
		</tr>
	<tr>
			<td>
				<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
					<script>
				var Check2 = new Array("Importar");
				</script>
					<form name="frmInv" action="<? echo $PHP_SELF ?>" method="post" enctype="multipart/form-data" onsubmit="return EvaluaReg(this,Check2)">
						<tr class=row2>
							<td colspan="2"><%=Mensaje_Info("Agregar Forma de Pago")%></td>
						</tr>
						<tr class=row2>
							<td class=row2>
								<div align="left">
									Numero Factura</div>
							</td>
							<td class=row2><input type="text" class="input" name="NumeroFactura" value="" size="24"></td>
						</tr>
						<tr class=row2>
							<td>Punto Venta</td>
							<td><select name="IDPuntoVenta" class="input">
									<? 								$sql_puntoventa = "SELECT * FROM PuntoVenta";
								$query_puntoventa = db_query($sql_puntoventa);
								while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
								{
									echo "<option value='".$r_puntoventa->IDPuntoVenta."'>".$r_puntoventa->Nombre."</option>";	
								}//end while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
							?>
								</select></td>
						</tr>
						<tr class=row2>
							<td>Forma de Pago</td>
							<td>
								<select name="IDFormaPago" class="input">
									<option value=''>Seleccione</option>
									<?
									$sql_formapago = " SELECT * FROM FormaPago ";
									$qry_formapago = db_query( $sql_formapago );
									while( $r_formapago = db_fetch_object( $qry_formapago ) )
									{
										echo " <option value='".$r_formapago->IDFormaPago."' >".$r_formapago->Descripcion."</option> ";
									}//end while
									?>
								</select>
							</td>
						</tr>
						<tr class=row2>
							<td>Valor</td>
							<td><input type="text" class="input" name="Valor" size="24"></td>
						</tr>
						<tr class=row2>
							<td align="center"><input type=hidden name=action value="formapago"></td>
							<td><input type=submit name=submit value="enviar" class=submit></td>
						</tr>
					</form>
					<form name="frmPr" action="<? echo $PHP_SELF ?>" method="post" enctype="multipart/form-data" >
						<tr class=row2>
							<td colspan="2"><%=Mensaje_Info("Cambiar Fecha")%></td>
						</tr>
						<tr class=row2>
							<td class=row2>
								<div align="left">
									Numero Factura</div>
							</td>
							<td class=row2><input type="text" class="input" name="NumeroFactura" size="24"></td>
						</tr>
						<tr class=row2>
							<td>Fecha Factura ( AAA-MM-DD )</td>
							<td>
								<input type="text" name="Fecha" class="input" ">

								<script language="JavaScript1.2">
							<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPr.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
							//-->
						</script>
							</td>
						</tr>
						<tr class=row2>
							<td>Punto Venta</td>
							<td><select name="IDPuntoVenta" class="input">
									<? 								$sql_puntoventa = "SELECT * FROM PuntoVenta";
								$query_puntoventa = db_query($sql_puntoventa);
								while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
								{
									echo "<option value='".$r_puntoventa->IDPuntoVenta."'>".$r_puntoventa->Nombre."</option>";	
								}//end while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
							?>
								</select></td>
						</tr>
						<tr class=row2>
							<td align="center"><input type=hidden name=action value="fecha"></td>
							<td><input type=submit name=submit value="enviar" class=submit></td>
						</tr>
					</form>
					<form name="frmfactura" action="<? echo $PHP_SELF ?>" method="post" enctype="multipart/form-data" >
						<tr class=row2><td colspan=2><%=Mensaje_Info("Eliminar Factura")%></td>
						</tr>
						<tr class=row2>
							<td class=row2>
								<div align="left">
									Numero Factura</div>
							</td>
							<td class=row2><input type="text" class="input" name="NumeroFactura" size="24"></td>
						</tr>
						<tr class=row2>
							<td>Punto Venta</td>
							<td><select name="IDPuntoVenta" class="input">
									<? 								$sql_puntoventa = "SELECT * FROM PuntoVenta";
								$query_puntoventa = db_query($sql_puntoventa);
								while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
								{
									echo "<option value='".$r_puntoventa->IDPuntoVenta."'>".$r_puntoventa->Nombre."</option>";	
								}//end while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
							?>
								</select></td>
						</tr>
						<tr class=row2>
							<td align="center"><input type=hidden name=action value="factura"></td>
							<td><input type=submit name=submit value="enviar" class=submit></td>
						</tr>
					</form>
					
					<form name="frmVerificaFormas" action="<? echo $PHP_SELF ?>" method="post" enctype="multipart/form-data" >
						<tr class=row2>
							<td colspan="2"><%=Mensaje_Info("Verificar Formas de Pago")%></td>
						</tr>
						<tr class=row2>
							<td class=row2>
								<div align="left">
									Numero Factura</div>
							</td>
							<td class=row2><input type="text" class="input" name="NumeroFactura" size="24"></td>
						</tr>
						<tr class=row2>
							<td>Punto Venta</td>
							<td><select name="IDPuntoVenta" class="input">
									<? 								$sql_puntoventa = "SELECT * FROM PuntoVenta";
								$query_puntoventa = db_query($sql_puntoventa);
								while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
								{
									echo "<option value='".$r_puntoventa->IDPuntoVenta."'>".$r_puntoventa->Nombre."</option>";	
								}//end while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
							?>
								</select></td>
						</tr>
						<tr class=row2>
							<td align="center"><input type=hidden name=action value="VFormasPago"></td>
							<td><input type=submit name=submit value="enviar" class=submit></td>
						</tr>
					</form>
					
				</table>
			</td>
	</tr>
</table>
<?
}// End function print_form()


/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function VerificaFormas($IDFactura,$IDPuntoVenta) {

	GLOBAL $TitleMod,$Table,$MOD,$Key;
	$qid = db_query(" SELECT * FROM FormaPagoFactura WHERE IDFactura = '$IDFactura' AND IDPuntoVenta = '$IDPuntoVenta' ");
	
$sql_formapago = " SELECT * FROM FormaPago ";
$qry_formapago = db_query( $sql_formapago );
while( $r_formapago = db_fetch_array( $qry_formapago ) )
	$array_formapago[$r_formapago[IDFormaPago]] = $r_formapago[Descripcion];
?>
<br>

<table cellpadding=1 cellspacing=0 class=bordertable align=left >
	<tr>
			<td class=maintitle bgcolor=#9daac6>Inconvenientes Facturas</td>
		</tr>
	<tr>
			<td>
				<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
					<script>
				var Check2 = new Array("Importar");
				</script>
					<form name="frmInv" action="<? echo $PHP_SELF ?>" method="post" enctype="multipart/form-data" onsubmit="return EvaluaReg(this,Check2)">
						<tr class=row2>
							<td colspan="4"><%=Mensaje_Info("Formas de Pago de la Factura")%></td>
						</tr>
						<tr class=titlemedium>
							<td >Forma de Pago</td>
							<td >Valor</td>
							<td >Comision</td>
							<td >Borrar</td>
						</tr>
						<?
						while( $r = db_fetch_object( $qid ) )
						{
						?>
						<tr class=row2>
							<td ><?=$array_formapago[ $r->IDFormaPago ]?></td>
							<td ><?=$r->Valor?></td>
							<td ><?=$r->Comision?></td>
							<td ><a href="?mod=fixed&action=borraforma&IDFactura=<?=$r->IDFactura?>&IDFormaPagoFactura=<?=$r->IDFormaPagoFactura?>&IDPuntoVenta=<?=$r->IDPuntoVenta?>">Borrar</a></td>
						</tr>
						<?
						}
						?>
					</form>
					
				</table>
			</td>
	</tr>
</table>
<?
}// End function

?>
