<body><?php
		switch ($action) {
			
			case "view" :
				print_from($HTTP_POST_VARS);
			break;
			
			default :
				print_from("");
			break;
		
		} // End switch


 
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($frm = ""){
	Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA,$Mes_array,$IDPuntoVenta,$Mes;
	//require( "Reportes/Calc.php" );
	$Calendario = new Date_Calc;
	//print_r($frm);
	//echo $Calendario->nextWeekday("30","11","2005");
	 
	
	 
 
  ?>
	<script>
   		function showDIV(iddiv){
			if (!document.getElementById)
				return
			
			//document.getElementById('saludo').style.display= "none"
			if( document.getElementById(iddiv).style.display == "none" )
			{
				document.getElementById(iddiv).style.display="";
			}
			else
			{
				document.getElementById(iddiv).style.display="none";
			}
			
			
		}
		
   	</script>
	<br>
	<a class="menuppal" href="./?mod=TotalesAlmacen">
		<img src="images/house.png" border="0">&nbsp;&nbsp;Consolidado almacenes - meses
	</a>
	|
	<a class="menuppal" href="./?mod=TotalesAlmacenReferencia">
		<img src="images/layers.png" border="0">&nbsp;&nbsp;Ventas por almacen - referencia
	</a>
	|
	<a class="menuppal" href="./?mod=TotalesAlmacenReferenciaPares">
		<img src="images/layers.png" border="0">&nbsp;&nbsp;Ventas por almacen - referencia pares
	</a>
	<br>
	<br>
	<table width="100%">
		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" name="Moviles">
						<tr>
							<td valign="middle"><img src="images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="nav">Mes 
								<select name="Mes" >
									<option value="">Seleccione un mes...</option>
									<?php 								
										foreach( $Mes_array as $keymes=>$mes ){
											$keymes = $keymes+1;
											echo "<option value=".$keymes." " ;if($Mes == $keymes ) echo "selected"; echo ">&nbsp;&nbsp;$mes</option>";
										}
									?>
								</select></td>
							<td align="left" valign="middle" class="nav"><input type="hidden" name="mod" value="TotalesAlmacenReferencia"><input type="hidden" name="action" value="view"></td>
							<td align="left" valign="middle" class="nav" align="right">Puntos de Venta  </td>
							<td align="left" valign="middle" class="nav"><select name="IDPuntoVenta" onchange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><?php 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
								</select></td>
							<td align="left" valign="middle" class="nav">
								
							</td>
						</tr>
						<tr>
							<td valign="middle"><img src="images/zoom.png" border="0" alt=""></td>
							<td align="left" valign="middle" colspan="4" class="nav">Filtrar Por:   <input type="Checkbox" name="filtro" value= "S" onclick="showDIV('filtros')"> </td>
							<td align="right" valign="middle" class="nav"><input type="submit" value="Ver Reporte" name="verreporte" class="button"> </td>
						</tr>
						
							<tr>
							<td colspan="6">
							<div id="filtros" style="display:none;">
							<table>
							<tr>
								<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td  valign="middle" class="nav" align="right">Referencia</td>
								<td  valign="middle" class="nav" align="left"><input type="text" name="Referencia" class="input" value="<?php echo $Referencia?>" size="20"></td>
								<td align="left" valign="middle"  class="nav">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td align="right" valign="middle" class="nav">Sexo</td>
								<td align="left" valign="middle" class="texto"><input type="radio" name="Sexo" value="M" size="20">&nbsp;&nbsp;M &nbsp;&nbsp;<input type="radio" name="Sexo" value="F" size="20">&nbsp;&nbsp;F &nbsp;&nbsp;</td>
							</tr>
							<tr>
								<td valign="middle"></td>
								<td  valign="middle" class="nav" align="right">Linea</td>
								<td  valign="middle" class="nav" align="left"><?php echo formpopup("Linea","Descripcion","IDLinea","IDLinea",$IDLinea,"InputSelect\" id=\"IDLinea"); ?></td>
								<td align="left" valign="middle"  class="nav">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td  valign="middle" class="nav" align="right">Cuero</td>
								<td  valign="middle" class="nav" align="left"><?php echo formpopup("Cuero","Descripcion","IDCuero","IDCuero",$IDCuero,"InputSelect\" id=\"IDCuero"); ?></td>
							</tr>
							<tr>
								<td valign="middle"></td>
								<td  valign="middle" class="nav" align="right">Proveedor</td>
								<td  valign="middle" class="nav" align="left"><?php echo formpopup("Proveedor","Nombre","IDProveedor","IDProveedor",$IDProveedor,"InputSelect\" id=\"IDProveedor"); ?></td>
								<td align="left" valign="middle"  class="nav"></td>
								<td align="left" valign="middle" class="nav"></td>
								<td align="left" valign="middle" class="nav"></td>
							</tr>
							</table>
							</div>
							</td>
							</tr>
						
					</form>
			</table>
	
		</td>
		</tr>

		<?php
		if(!empty( $Mes ) && !empty( $IDPuntoVenta ) ){
		?>
		<tr>
		<td>
			<?php
				//QUERY REFERENCIAS DEL PUNTO DEL VENTA
					$sql_referencias = " SELECT R.Numero, PVR.IDPuntoVentaReferencia 
											FROM Referencia R, PuntoVentaReferencia PVR 
											WHERE PVR.IDPuntoVenta = '$IDPuntoVenta' 
											AND PVR.IDReferencia = R.IDReferencia ";
											
						if( !empty( $frm['Referencia'] ))
							$sql_referencias .= " AND R.Numero = '".$frm["Referencia"]."' ";
						
						if( !empty( $frm['Sexo'] ))
							$sql_referencias .= " AND FIND_IN_SET('".$frm["Sexo"]."',Sexo) > 0 ";
						
						if( !empty( $frm['IDLinea'] ))
							$sql_referencias .= " AND R.IDLinea = '".$frm["IDLinea"]."' ";
						
						if( !empty( $frm['IDCuero'] ))
							$sql_referencias .= " AND R.IDCuero = '".$frm["IDCuero"]."' ";
						
						if( !empty( $frm['IDProveedor'] ))
							$sql_referencias .= " AND R.IDProveedor = '".$frm["IDProveedor"]."' ";
					
					$sql_referencias .= " ORDER BY R.Numero ";
						
					$qry_referencias = db_query( $sql_referencias );
					
					$numero_referencias = db_num_rows( $qry_referencias );
			?>
			<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">
						&nbsp; Ventas totales <?php echo $Mes_array[$Mes-1]?> - <?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>
						- Mostrando <?php echo $numero_referencias?> Referencias
					</td>
				</tr>
				<?php
					
					$i = 0;
					while( $r_referencias = db_fetch_array( $qry_referencias ) )
					{
						$array_referencias[$i] = $r_referencias;
						$i++;
						$j = 0;
						
						//QUERY TALLAS DE LA REFERENCIA
						$sql_tallas = "SELECT T.Descripcion, T.IDTalla, C.IDCodificacionEspecifica 
										FROM Talla T, CodificacionEspecifica C
										WHERE C.IDPuntoVentaReferencia = '".$r_referencias["IDPuntoVentaReferencia"]."' 
										AND C.IDTalla = T.IDTalla ";
						$qry_tallas = db_query( $sql_tallas );
						while( $r_tallas = db_fetch_array( $qry_tallas ) )
						{
						
							$array_tallas[$r_referencias["IDPuntoVentaReferencia"]][$j] = $r_tallas;
							$j++;
							
							//QUERY TOTAL VENDIDO DE LA REFERENCIA
							$Mes = $Mes * 1;
							$sql_facturas = " SELECT SUM( ( DF.ValorU ) * DF.Cantidad ) as ValorVentas FROM Factura F, DetalleFactura DF WHERE F.IDPuntoVenta = '$IDPuntoVenta' AND DATE_FORMAT( F.FechaFactura,'%c' ) = '$Mes'
										AND F.IDFactura = DF.IDFactura AND F.IDPuntoVenta = DF.IDPuntoVenta AND DF.IDCodificacionEspecifica = '".$r_tallas["IDCodificacionEspecifica"]."' GROUP BY DF.IDDetalleFactura";
							$qry_facturas = db_query( $sql_facturas );
							
while( $r_facturas = db_fetch_object( $qry_facturas ) )
							{
								$array_facturas[$r_tallas['IDCodificacionEspecifica']] += $r_facturas->ValorVentas;
							
								$ventas += $r_facturas->ValorVentas;
							}//end while							
						}//end while( $r_referencias = db_fetch_array( $qry_referencias ) )
						
					}//end while
					//print_r( $array_tallas );					
					//print_r( $array_facturas );	
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
								<td class="titlemedium" align="center" nowrap>Referencia</td>
							<?php

							for( $i = 33; $i <= 43; $i++ )
							{
							?>
								<td class="titlemedium" align="center" nowrap><?php echo $i?></td>
							<?php
							}//end for
							?>
								<td class="titlemedium" align="center" nowrap>TOTALES</td>
									<td class="titlemedium" align="center" nowrap>GRAFICAR</td>
								</tr>
						<?php
						//print_r($array_referencias);
						foreach( $array_referencias as $key => $valor )
						{
							$class = repetition()?"row2":"row1";
							//print_r($array_tallas[$valor['IDPuntoVentaReferencia']]);
						?>
							<tr>
								<td class="<?php echo $class?>" align="center" nowrap><?php echo $valor['Numero']?></td>
								<?php
								for( $i = 33; $i <= 43; $i++ )
								{
								?>
									<td class="<?php echo $class?>" align="right" nowrap>
										<?php
											
											
											foreach( $array_tallas[$valor['IDPuntoVentaReferencia']] as $llave => $talla )
											{
												//print_r($talla);
												if( $talla['Descripcion'] == $i && $array_facturas[$talla['IDCodificacionEspecifica']] > 0 )
												{
													echo number_format( $array_facturas[$talla['IDCodificacionEspecifica']], 2);
													
													$totalesreferencia[$valor['IDPuntoVentaReferencia']]['Totales'] += $array_facturas[$talla['IDCodificacionEspecifica']];
													$totalestalla[$i]['Totales'] += $array_facturas[$talla['IDCodificacionEspecifica']];
													$total += $array_facturas[$talla['IDCodificacionEspecifica']];
													
													//array grafica referencias
													$datostallas[$i][$valor['IDPuntoVentaReferencia']] = $array_facturas[$talla['IDCodificacionEspecifica']];
													$opcionestallas[$i][$valor['IDPuntoVentaReferencia']] = $valor['Numero'];
													
													//array grafica tallas
													$datosreferencia[$valor['IDPuntoVentaReferencia']][$i] = $array_facturas[$talla['IDCodificacionEspecifica']];
													$opcionesreferencia[$valor['IDPuntoVentaReferencia']][$i] = $i;
													
												}//end if( $talla['Descripcion'] == $i  )
												
											}//end foreach( $array_tallas[$valor['IDPuntoVentaReferencia']] as $llave => $talla )

											

										?>
									</td>
								<?php
								}//end for
								?>
								<td class="<?php echo $class?>" align="center" nowrap>
									<?php
										echo number_format( $totalesreferencia[$valor['IDPuntoVentaReferencia']]['Totales'] , 2);
										if( $totalesreferencia[$valor['IDPuntoVentaReferencia']]['Totales'] > 0 )
										{
											//array grafica totales
											$datostotales[$valor['IDPuntoVentaReferencia']] = $totalesreferencia[$valor['IDPuntoVentaReferencia']]['Totales'];
											$opcionestotales[$valor['IDPuntoVentaReferencia']] = $valor['Numero'];
										}
									?>
								</td>
										<td class="<?php echo $class?>" align="center" nowrap>
										
											<?php
											if( $totalesreferencia[$valor['IDPuntoVentaReferencia']]['Totales'] > 0 )
											{
												$datos_referencia = implode(",",$datosreferencia[$valor['IDPuntoVentaReferencia']]);
												$opciones_referencia = implode(",",$opcionesreferencia[$valor['IDPuntoVentaReferencia']]);
												$titulo_referencia = "Ventas ".$valor['Numero'];
											?>
											<a href="javascript:;" onclick="window.open('Reportes/graficar.php?datos=<?php echo $datos_referencia?>&opciones=<?php echo $opciones_referencia?>&titulo=<?php echo $titulo_referencia?>','','width=550, height=400');" >
												<img src="images/chart_pie.png" border="0">
											</a>
											<?php
											}
											?>
										</td>
									</tr>
						
						<?php
						}//while( $r_referencias = db_fetch_object( $qry_referencias ) )
						?>
						<tr >
							<td class="row1" align="center" nowrap></td>
							<?php
							for( $i = 33; $i <= 43; $i++ )
							{
							?>
								<td class="row1" align="center" nowrap>
									<?php
									if( $totalestalla[$i]['Totales'] > 0 )
									{	
										$datos_tallas = implode(",",$datostallas[$i]);
										$opciones_tallas = implode(",",$opcionestallas[$i]);
										$titulo_tallas = "Ventas talla ".$i;
									?>
									<a href="javascript:;" onclick="window.open('Reportes/graficar.php?datos=<?php echo $datos_tallas?>&opciones=<?php echo $opciones_tallas?>&titulo=<?php echo $titulo_tallas?>','','width=550, height=400');" >
										<img src="images/chart_pie.png" border="0">
									</a>
									<?php
									}
									?>
								</td>
							<?php
							}//end for
							?>
							<td class="row1" align="center" nowrap>
								<?php
								if( $total > 0 )
									{
										$datos_totales = implode(",",$datostotales);
										$opciones_totales = implode(",",$opcionestotales);
										$titulo_totales = "Ventas Totales ";
								?>
									<a href="javascript:;" onclick="window.open('Reportes/graficar.php?datos=<?php echo $datos_totales?>&opciones=<?php echo $opciones_totales?>&titulo=<?php echo $titulo_totales?>','','width=550, height=400');" >
										<img src="images/chart_pie.png" border="0">
									</a>
								<?php
									}
								?>
							</td>
							<td class="row1" align="center" nowrap></td>
									</tr>	
						
						<tr>
							<td class="titlemedium" align="center" nowrap>TOTALES</td>
							<?php
							for( $i = 33; $i <= 43; $i++ )
							{
							?>
								<td class="titlemedium" align="right" nowrap>
									<?php
										echo number_format( $totalestalla[$i]['Totales'] );
									?>
								</td>
							<?php
							}//end for
							?>
							<td class="titlemedium" align="center" nowrap>
								<?php
									echo number_format( $total, 2);
									
								?>
							</td>
										<td class="titlemedium" align="center" nowrap></td>
									</tr>
						
						
							
					</table>
				</td>
			</tr>
		</form>
	
		</table>
	</td>
	</tr>
	<?php 
	 } // END if(!empty($IDEmpresa))
	?>
	</table>
	<?php						
}// Enf function print()	

  ?>
</body>
