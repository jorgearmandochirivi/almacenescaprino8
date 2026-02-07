<body><?php
		switch ($action) {

			case "view" :
				print_from($IDPuntoVenta,$Fecha);
			break;

			default :
				print_from("");
			break;

		} // End switch



/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($IDPuntoVenta="", $Fecha=""){
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA, $ReteIVA, $ReteICA, $ReteFuente, $FechaDesde, $FechaHasta;

 $sql_retefuente = "SELECT * FROM ReteFuente LIMIT 1";
	$query_retefuente = db_query( $sql_retefuente );
	$r_retefuente = db_fetch_object( $query_retefuente );

	$ReteFuente = $r_retefuente->Valor / 100;

 if(strtotime($FechaDesde)<=strtotime("2017-01-31")):
 	$IVA = 0.16;
 endif;


 

?>

	<table width="100%">

		<tr>
		<td>
				<table width='60%' align='center' border="0" cellspacing="0" cellpadding="2" class="bordertable">
					<form action="./" name="frmPuntoVenta" method="post">
						<tr>
						  <td  align='left' valign='middle' class="nav"> A&ntilde;o
					      </td>
						  <td align="left" valign="middle" class="nav">
							<select name="ano" class="tbox" required>
								<option value="">Seleccione</option>
								<?php
								for($i=2020;$i<=date("Y");$i++){
									$selected = ($i==$_POST["ano"])?"selected":"";
									echo "<option value='".$i."' ".$selected.">".$i."</option>";
								}
								?>
							</select>
						  </td>
						  <td width="3%"  align='left' valign='middle' class="nav">Mes</td>
							<td width="28%" align="left" valign="middle" class="nav">								
                            <select name="mes" class="tbox" required>
								<option value="">Seleccione</option>
								<?php
								for($i=1;$i<=12;$i++){
									$selected = ($i==$_POST["mes"])?"selected":"";
									echo "<option value='".$i."' ".$selected.">".$i."</option>";
								}
								?>
							</select>

                            </td>
						</tr>
						<tr>
						  <td  align='left' valign='middle' class="nav">Referencia</td>
						  <td align="left" valign="middle" class="nav"><span class="col2">
					      <input type=text class=tbox name=referencia value="<?php echo $_POST["referencia"]?>" required>
						  </span></td>
					  	</tr>						
						
						<tr>
						  	<td colspan="4"  align='center' valign='middle' class="nav">
								<input type="submit" value="Ver Reporte" name="submit" class="submit">
								<input type="hidden" value="Kardex" name="mod" id="mod">
							</td>
					  	</tr>
					</form>
				</table>
			</td>
		</tr>

		<br>
		<br>

		<tr>
		<td>

		

 		<?php 
		if($_POST["ano"] && $_POST["mes"]){ ?>
			
			<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">
				<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return Evalua(document.frm)">
					<?php			
					$pto_vet_ref="";
					//Se consulta ventas
					$sql="SELECT IDReferencia, Numero, P.Nombre as RazonSocial, P.Nit
					FROM Referencia R, Proveedor P, Factura F
					WHERE R.IDProveedor = P.IDProveedor and					
					Numero like '".$_POST["referencia"]."' 
					LIMIT 1";
					$query = db_query($sql);
					$row_referencia = db_fetch_object($query);
						
					$sql_pto_ref = "SELECT IDPuntoVentaReferencia  FROM PuntoVentaReferencia WHERE IDReferencia = '".$row_referencia->IDReferencia."' ";
					$qry_pto_ref = db_query( $sql_pto_ref );
					while( $r_pto_ref =  db_fetch_array( $qry_pto_ref ) )
						$array_pto_ref[] = $r_pto_ref["IDPuntoVentaReferencia"];
						
					if(count($array_pto_ref)>0){						
						$pto_vet_ref = implode(",",$array_pto_ref);
					}

					$sql_cod_esp = "SELECT IDCodificacionEspecifica  FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia in (".$pto_vet_ref.")";
					$qry_cod_esp = db_query( $sql_cod_esp );
					while( $r_cod_esp =  db_fetch_array( $qry_cod_esp ) )
						$array_cod_esp[ ] = $r_cod_esp["IDCodificacionEspecifica"];

					if(count($array_cod_esp)>0){						
						$cod_esp = implode(",",$array_cod_esp);
					}								
					
					?>
					<tr>
					<td class='mainbg'>
						<table width="100%" border="1" cellspacing="1" cellpadding="1">
							<tr>
								<td rowspan="2" class="titlemedium" align="center">Proveedor</td>
								<td colspan="4" class="titlemedium" align="center" >Inv Inicial</td>
								<td colspan="10" class="titlemedium" align="center" >Movimiento</td>
								<td colspan="3" class="titlemedium" align="center" >Inv Final</td>

							</tr>
							<tr>
								
								<td class="titlemedium" align="center" nowrap>Referencia</td>
								<td class="titlemedium" align="center" nowrap>Unidades</td>
								<td class="titlemedium" align="center" nowrap>Costo</td>
								<td class="titlemedium" align="center" nowrap>Total</td>
								<td class="titlemedium" align="center" nowrap>Compras unidades</td>
								<td class="titlemedium" align="center" nowrap>Costo Compras</td>
								<td class="titlemedium" align="center" nowrap>Total</td>
								<td class="titlemedium" align="center" nowrap>Costo Promedio</td>
								<td class="titlemedium" align="center" nowrap>Ventas</td>
								<td class="titlemedium" align="center" nowrap>Costo promedio</td>
								<td class="titlemedium" align="center" nowrap>Total</td>
								<td class="titlemedium" align="center" nowrap>Salidas</td>
								<td class="titlemedium" align="center" nowrap>Costo Promedio</td>
								<td class="titlemedium" align="center" nowrap>Total</td>
								<td class="titlemedium" align="center" nowrap>Unidades</td>
								<td class="titlemedium" align="center" nowrap>Costo Promedio</td>
								<td class="titlemedium" align="center" nowrap>Total Costo</td>								
							</tr>
							<?php
							//Inventario Inicial
							$sql_inventario="SELECT IDPuntoVenta, Referencia, Total FROM InventarioHistorial WHERE MONTH(FechaCorte)  = '".$_POST["mes"]."' and YEAR(FechaCorte) = '".$_POST["ano"]."' and Referencia = '".$_POST["referencia"]."' ORDER BY FechaCorte ASC LIMIT 1";
							$r_inventario=db_query($sql_inventario);
							while($row_inventario = db_fetch_object($r_inventario)){
								?>
								<tr>
									<td  align="center" nowrap><?php echo $row_referencia->RazonSocial; ?> </td>
									<td  align="center" nowrap><?php echo $row_referencia->Numero; ?> </td>
									<td  align="center" nowrap><?php echo $row_inventario->Total; ?></td>
									<td  align="center" nowrap>										
										<?php 
										$SumaCosto=0;
										$CostoPromedioProducto=0;
										$sql_costo_prom="SELECT Costo FROM CostoReferencia WHERE IDReferencia = '".$row_referencia->IDReferencia."' ORDER BY FechaTrCr DESC LIMIT 2";
										$r_costo_prom=db_query($sql_costo_prom);
										while($row_costo_prom = db_fetch_object($r_costo_prom)){
											$SumaCosto+=$row_costo_prom->Costo;
										}
										$CostoPromedioProducto=$SumaCosto/db_num_rows($r_costo_prom);


										$sql_costo="SELECT Costo FROM CostoReferencia WHERE IDReferencia = '".$row_referencia->IDReferencia."' ORDER BY FechaTrCr DESC LIMIT 1";
										$r_costo=db_query($sql_costo);
										$row_costo = db_fetch_object($r_costo);
										$CostoProducto=$row_costo->Costo;
										$TotalUnidades=$CostoProducto*(int)$row_inventario->Total;
										echo "$".number_format($CostoProducto,0,",",".");										
										?>
									</td>
									<td>
										<?php echo "$".number_format($TotalUnidades,0,",","."); ?>
									</td>


									<td  align="center" nowrap>
										<?php  										
										$TotalUnidadesCompra=0;
										$sql_mov="SELECT Cantidad FROM Entrada E, PuntoVentaReferencia PR, Referencia R WHERE E.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia AND PR.IDReferencia = R.IDReferencia AND R.Numero LIKE '%".$row_referencia->Numero."%' and MONTH(FechaTrCr)='".$_POST["mes"]."' and YEAR(FechaTrCr) = '".$_POST["ano"]."' GROUP BY E.IDEntrada ORDER BY Fecha DESC";
										$r_movimiento=db_query($sql_mov);
										while($row_movimiento = db_fetch_object($r_movimiento)){
											$TotalUnidadesCompra+=(int)$row_movimiento->Cantidad;											
										}
										echo $TotalUnidadesCompra;										
										?>
									</td>
									<td  align="center" nowrap>$<?php echo number_format($CostoPromedioProducto,0,",",".")  ?></td>
									<td  align="center" nowrap>$<?php 
										$TotalCompra=$TotalUnidadesCompra*$CostoPromedioProducto;
										echo number_format($TotalCompra,0,",","."); ?>
									</td>
									<td  align="center" nowrap>$
										<?php 
											$CostoProm=($TotalCompra+$CostoProducto)/($row_inventario->Total+$TotalUnidadesCompra);
											echo  number_format($CostoProm,0,",","."); ?>
									</td>									
								
									<?php

									//Facturas
									$TotalUnidadesVenta=0;
									$sql_factura = "SELECT F.NumeroFactura, F.FechaFactura, F.ValorTotal, DF.PrecioU, DF.Cantidad
									FROM Factura F, DetalleFactura DF
									WHERE MONTH(F.FechaFactura) = '".$_POST["mes"]."' and YEAR(F.FechaFactura) = '".$_POST["ano"]."' and 
									F.IDFactura = DF.IDFactura and 
									DF.IDCodificacionEspecifica in (".$cod_esp.")";									
									$query = db_query($sql_factura);
									while($row_factura = db_fetch_object($query)){
										$TotalUnidadesVenta+=$row_factura->Cantidad;									
									}
									?>									
									<td  align="center" nowrap><?php echo number_format($TotalUnidadesVenta,0,",","."); ?></td>
									<td  align="center" nowrap>$<?php echo  number_format($CostoPromedioProducto,0,",","."); ?></td>
									<td  align="center" nowrap><?php echo number_format(($TotalUnidadesVenta*$CostoPromedioProducto),0,",","."); ?></td>
									<td>
										<?php
											$TotalUnidadesSalida=0;
											$sql_salida="SELECT DM.Cantidad FROM Movimiento M,DetalleMovimiento DM,PuntoVenta, TipoMovimiento WHERE M.IDMovimiento = DM.IDMovimiento AND (DM.IDPuntoVentaReferencia in (".$pto_vet_ref.")) AND MONTH(M.FechaTrCr)= '".$_POST["mes"]."' and YEAR(M.FechaTrCr)= '".$_POST["ano"]."' AND M.IDPuntoVenta = PuntoVenta.IDPuntoVenta AND TipoMovimiento.IDTipoMovimiento = M.IDTipoMovimiento order by M.IDMovimiento desc";
											$r_salida=db_query($sql_salida);
											while($row_salida = db_fetch_object($r_salida)){
												$TotalUnidadesSalida+=$row_salida->Cantidad;
											}
											echo $TotalUnidadesSalida;
										?>
									</td>
									<td  align="center" nowrap>$<?php echo  number_format($CostoPromedioProducto,0,",","."); ?></td>
									<td  align="center" nowrap>$<?php 
											$TotalSalidas=0;
											$TotalSalidas=$TotalUnidadesSalida*$CostoPromedioProducto;
											echo number_format($TotalSalidas,0,",","."); ?>
										</td>
									
									<td>
										<?php
										//Inventario Final
										$sql_inventario="SELECT IDPuntoVenta, Referencia, Total FROM InventarioHistorial WHERE MONTH(FechaCorte)  = '".($_POST["mes"]+1)."' and YEAR(FechaCorte) = '".$_POST["ano"]."' and Referencia = '".$_POST["referencia"]."' ORDER BY FechaCorte ASC LIMIT 1";
										$r_inventario=db_query($sql_inventario);
										$row_inventario = db_fetch_object($r_inventario);
										echo $row_inventario->Total;
										?>
									</td>	
									<td  align="center" nowrap>$<?php echo  number_format($CostoPromedioProducto,0,",","."); ?></td>
									<td  align="center" nowrap>$<?php echo  number_format(($CostoPromedioProducto*$row_inventario->Total),0,",","."); ?></td>								
								</tr>
								<?php
							}
							?>
						</table>
						
					<br><br></td>
					</tr>
				</form>
			</table>
			<?php 
		} 
		else{
			echo "<br>Seleccione el a&ntilde;o y el mes";
		}
		?>	
	</table>
	<?php
}// Enf function print()

?>
</body>
