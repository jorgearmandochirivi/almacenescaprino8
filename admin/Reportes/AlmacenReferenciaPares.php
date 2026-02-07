<body><%
		switch ($action) {
			
			case "view" :
				//print_r( $_POST );
				//exit;
				print_from($_POST);
			break;
			
			default :
				print_from("");
			break;
		
		} // End switch


 
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($frm = ""){
	Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA,$Mes_array,$IDPuntoVenta,$Mes, $FechaDesde, $FechaHasta;
	//require( "Reportes/Calc.php" );
	$Calendario = new Date_Calc;
	//print_r($frm);
	//echo $Calendario->nextWeekday("30","11","2005");
	 
	$array_lastallas = array( 1=>1, 33=>33, 34=>34, 35=>35, 36=>36, 37=>37, 38=>38, 39=>39, 40=>40, 41=>41, 42=>42, 43=>43 );
	 
 
%>
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
							<td colspan="3" valign="middle"><input type="hidden" name="mod" value="TotalesAlmacenReferenciaPares"><input type="hidden" name="action" value="view">
								<table>
									<tr>
										<td valign="middle"></td>
										<td colspan="2" valign="middle" class="nav" align="left">
							
								Desde	<input type="text" name="FechaDesde" class="input" value="<?=$FechaDesde?>" size="10"
											<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
										</td>
										<td align="left" valign="middle" class="nav"></td>
										<td colspan="2" align="left" valign="middle" class="nav" nowrap>
								
								Hasta	<input  type="text" name="FechaHasta" class="input" value="<?=$FechaHasta?>" size="10"
											<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
										</td>
									</tr>
								</table>
							</td>
							<td align="left" valign="middle" class="nav" align="right">Puntos de Venta  </td>
							<td align="left" valign="middle" class="nav"><select name="IDPuntoVenta"  >
									<option value="">Seleccione Un Punto de Venta</option><% 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							%>
								</select></td>
										<td align="left" valign="middle" class="nav">
								
							</td>
									</tr>
						<tr>
							<td valign="middle"><img src="images/zoom.png" border="0" alt="">Filtrar Por:   <input type="Checkbox" name="filtro" value= "S" onclick="showDIV('filtros')"></td>
							<td align="left" valign="middle" colspan="4" class="nav"></td>
							<td align="right" valign="middle" class="nav"><input type="submit" value="Ver Reporte" name="verreporte" class="button"> </td>
						</tr>
						
							<tr>
							<td colspan="6">
							<div id="filtros" style="display:none;">
							<table>
							<tr>
								<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td  valign="middle" class="nav" align="right">Referencia</td>
								<td  valign="middle" class="nav" align="left"><input type="text" name="Referencia" class="input" value="<?=$Referencia?>" size="20"></td>
								<td align="left" valign="middle"  class="nav">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td align="right" valign="middle" class="nav">Sexo</td>
								<td align="left" valign="middle" class="texto"><input type="radio" name="Sexo" value="M" size="20">&nbsp;&nbsp;M &nbsp;&nbsp;<input type="radio" name="Sexo" value="F" size="20">&nbsp;&nbsp;F &nbsp;&nbsp;</td>
							</tr>
							<tr>
								<td valign="middle"></td>
								<td  valign="middle" class="nav" align="right">Linea</td>
								<td  valign="middle" class="nav" align="left"><? echo formpopup("Linea","Nombre","IDLinea","IDLinea",$IDLinea,"InputSelect\" id=\"IDLinea"); ?></td>
								<td align="left" valign="middle"  class="nav">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td  valign="middle" class="nav" align="right">Cuero</td>
								<td  valign="middle" class="nav" align="left"><? echo formpopup("Cuero","Descripcion","IDCuero","IDCuero",$IDCuero,"InputSelect\" id=\"IDCuero"); ?></td>
							</tr>
										<tr>
								<td valign="middle"></td>
								<td  valign="middle" class="nav" align="right">Proveedor</td>
								<td  valign="middle" class="nav" align="left"><? echo formpopup("Proveedor","Nombre","IDProveedor","IDProveedor",$IDProveedor,"InputSelect\" id=\"IDProveedor"); ?></td>
														<td align="left" valign="middle"  class="nav"></td>
														<td align="right" valign="middle" class="nav">Saldo</td>
														<td align="left" valign="middle" class="texto"><input type="radio" name="Saldo" value="S" size="20">S<input type="radio" name="Saldo" value="N" size="20">N</td>
													</tr>
									</table>
							</div>
							</td>
							</tr>
						
					</form>
			</table>
	
		</td>
		</tr>

		<%
		
		
		
		if(!empty( $FechaDesde ) && !empty( $IDPuntoVenta ) ){
		%>
		<tr>
		<td>
			<?
				//QUERY REFERENCIAS DEL PUNTO DEL VENTA
					$sql_referencias = " SELECT R.Numero, PVR.IDPuntoVentaReferencia 
											FROM Referencia R, PuntoVentaReferencia PVR 
											WHERE PVR.IDPuntoVenta = '$IDPuntoVenta' 
											AND PVR.IDReferencia = R.IDReferencia ";
					if( !empty( $frm['Referencia'] ))
						$sql_referencias .= " AND R.Numero = '$frm[Referencia]' ";
						
					if( !empty( $frm['Sexo'] ))
						$sql_referencias .= " AND FIND_IN_SET('$frm[Sexo]',Sexo) > 0 ";
						
					if( !empty( $frm['IDLinea'] ))
						$sql_referencias .= " AND R.IDLinea = '$frm[IDLinea]' ";
						
					if( !empty( $frm['IDCuero'] ))
						$sql_referencias .= " AND R.IDCuero = '$frm[IDCuero]' ";
						
					if( !empty( $frm['IDProveedor'] ))
						$sql_referencias .= " AND R.IDProveedor = '$frm[IDProveedor]' ";
					if( !empty( $frm['Saldo'] ))
						$sql_referencias .= " AND R.Saldo = '$frm[Saldo]' ";
					
					$sql_referencias .= " ORDER BY R.Numero ";
					$qry_referencias = db_query( $sql_referencias );
					
					$numero_referencias = db_num_rows( $qry_referencias );
			?>
			<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">
						&nbsp; Ventas totales <? echo $FechaDesde." - ".$FechaHasta ?> - <?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>
						- Mostrando <?=$numero_referencias?> Referencias
					</td>
				</tr>
				<?
					
					$i = 0;
					while( $r_referencias = db_fetch_array( $qry_referencias ) )
					{
						$array_referencias[$i] = $r_referencias;
						$i++;
						$j = 0;
						
						//QUERY TALLAS DE LA REFERENCIA
						$sql_tallas = "SELECT T.Descripcion, T.IDTalla, C.IDCodificacionEspecifica 
										FROM Talla T, CodificacionEspecifica C
										WHERE C.IDPuntoVentaReferencia = '$r_referencias[IDPuntoVentaReferencia]' 
										AND C.IDTalla = T.IDTalla ";
						$qry_tallas = db_query( $sql_tallas );
						$ventas = 0;
						while( $r_tallas = db_fetch_array( $qry_tallas ) )
						{
						
							$array_tallas[$r_referencias[IDPuntoVentaReferencia]][$j] = $r_tallas;
							$j++;
							
							//QUERY TOTAL VENDIDO DE LA REFERENCIA
							$Mes = $Mes * 1;
							
							/****************/
							/*  Se elimino AND DATE_FORMAT( F.FechaFactura,'%c' ) = '$Mes' cambiado por fechas   */
							/*****************/
							
							$sql_facturas = " SELECT SUM( DF.Cantidad ) as ValorVentas FROM Factura F, DetalleFactura DF WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
										AND F.IDFactura = DF.IDFactura AND F.IDPuntoVenta = DF.IDPuntoVenta AND DF.IDCodificacionEspecifica = '$r_tallas[IDCodificacionEspecifica]' ";
							
							if( !empty( $FechaDesde ) && !empty( $FechaHasta ) )
								$sql_facturas .= " AND DATE_FORMAT(F.FechaFactura,'%Y-%m-%d') >= '$FechaDesde' AND DATE_FORMAT(F.FechaFactura,'%Y-%m-%d') <= '$FechaHasta' ";
							elseif( !empty( $FechaDesde ) )
								$sql_facturas .= " AND DATE_FORMAT(F.FechaFactura,'%Y-%m-%d') = '$FechaDesde' ";
								
							$sql_facturas .= " GROUP BY DF.IDDetalleFactura ";
							
							$qry_facturas = db_query( $sql_facturas );
							
							while( $r_facturas = db_fetch_object( $qry_facturas ) )
							{
								$array_facturas[$r_tallas['IDCodificacionEspecifica']] += $r_facturas->ValorVentas;
							
								$ventas += $r_facturas->ValorVentas;
							}//end while
							
							
						}//end while( $r_referencias = db_fetch_array( $qry_referencias ) )
						
						if( $ventas == 0 )
						{
							array_pop( $array_referencias );
						}//end if
						
						
					}//end while
					//print_r( $array_tallas );					
					//print_r( $array_facturas );	
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
								<td class="titlemedium" align="center" nowrap>Referencia</td>
							<?

							foreach( $array_lastallas as $i=>$tales )
							//for( $i = 33; $i <= 43; $i++ )
							{
							?>
								<td class="titlemedium" align="center" nowrap><?=$i?></td>
							<?
							}//end for
							?>
								<td class="titlemedium" align="center" nowrap>TOTALES</td>
									<td class="titlemedium" align="center" nowrap>GRAFICAR</td>
								</tr>
						<?
						//print_r($array_referencias);
						foreach( $array_referencias as $key => $valor )
						{
							$class = repetition()?"row2":"row1";
							
							//print_r( $array_referencias );
							//exit( );
							
							if( $linea <> substr( $valor['Numero'], 0, 2 )  )
							{
									
						?>			
									
									
									<tr>
											<td class="titlemedium" align="center" nowrap><?=$linea?></td>
											<?
											foreach( $array_lastallas as $i=>$tales )
											{
											?>
												<td class="titlemedium" align="right" nowrap>
													<?
														
														
														foreach( $array_tallas[$valor['IDPuntoVentaReferencia']] as $llave => $talla )
														{
															//print_r($talla);
															if( $talla['Descripcion'] == $i  )
															{
																echo $array_linea[$linea][$i];
																
															}//end if( $talla['Descripcion'] == $i  )
															
														}//end foreach( $array_tallas[$valor['IDPuntoVentaReferencia']] as $llave => $talla )
			
														
			
													?>
												</td>
											<?
											}//end for
											?>
											<td class="titlemedium" align="center" nowrap>
												
												<?
												echo array_sum( $array_linea[$linea] );
												?>
											</td>
													<td class="titlemedium" align="center" nowrap>
													
													
													</td>
												</tr>			
									
									
									
									
									
									
									
									
						<?			
							
							}//end if
							
							$linea = substr( $valor['Numero'], 0, 2 );
							
							//print_r($array_tallas[$valor['IDPuntoVentaReferencia']]);
						?>
							<tr>
								<td class="<?=$class?>" align="center" nowrap><?=$valor['Numero']?></td>
								<?
								foreach( $array_lastallas as $i=>$tales )
								{
								?>
									<td class="<?=$class?>" align="right" nowrap>
										<?
											
											
											foreach( $array_tallas[$valor['IDPuntoVentaReferencia']] as $llave => $talla )
											{
												//print_r($talla);
												if( $talla['Descripcion'] == $i && $array_facturas[$talla['IDCodificacionEspecifica']] > 0 )
												{
													echo $array_facturas[$talla['IDCodificacionEspecifica']];
													$array_linea[$linea][$i] += $array_facturas[$talla['IDCodificacionEspecifica']];
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
								<?
								}//end for
								?>
								<td class="<?=$class?>" align="center" nowrap>
									<?
										echo number_format( $totalesreferencia[$valor['IDPuntoVentaReferencia']]['Totales'] , 2);
										if( $totalesreferencia[$valor['IDPuntoVentaReferencia']]['Totales'] > 0 )
										{
											//array grafica totales
											$datostotales[$valor['IDPuntoVentaReferencia']] = $totalesreferencia[$valor['IDPuntoVentaReferencia']]['Totales'];
											$opcionestotales[$valor['IDPuntoVentaReferencia']] = $valor['Numero'];
										}
									?>
								</td>
										<td class="<?=$class?>" align="center" nowrap>
										
											<?
											if( $totalesreferencia[$valor['IDPuntoVentaReferencia']]['Totales'] > 0 )
											{
												$datos_referencia = implode(",",$datosreferencia[$valor['IDPuntoVentaReferencia']]);
												$opciones_referencia = implode(",",$opcionesreferencia[$valor['IDPuntoVentaReferencia']]);
												$titulo_referencia = "Ventas ".$valor['Numero'];
											?>
											<a href="javascript:;" onclick="window.open('Reportes/graficar.php?datos=<?=$datos_referencia?>&opciones=<?=$opciones_referencia?>&titulo=<?=$titulo_referencia?>','','width=550, height=400');" >
												<img src="images/chart_pie.png" border="0">
											</a>
											<?
											}
											?>
										</td>
									</tr>
						
						<?
						}//while( $r_referencias = db_fetch_object( $qry_referencias ) )
						?>
						<tr >
							<td class="row1" align="center" nowrap></td>
							<?
							foreach( $array_lastallas as $i=>$tales )
							{
							?>
								<td class="row1" align="center" nowrap>
									<?
									if( $totalestalla[$i]['Totales'] > 0 )
									{	
										$datos_tallas = implode(",",$datostallas[$i]);
										$opciones_tallas = implode(",",$opcionestallas[$i]);
										$titulo_tallas = "Ventas talla ".$i;
									?>
									<a href="javascript:;" onclick="window.open('Reportes/graficar.php?datos=<?=$datos_tallas?>&opciones=<?=$opciones_tallas?>&titulo=<?=$titulo_tallas?>','','width=550, height=400');" >
										<img src="images/chart_pie.png" border="0">
									</a>
									<?
									}
									?>
								</td>
							<?
							}//end for
							?>
							<td class="row1" align="center" nowrap>
								<?
								if( $total > 0 )
									{
										$datos_totales = implode(",",$datostotales);
										$opciones_totales = implode(",",$opcionestotales);
										$titulo_totales = "Ventas Totales ";
								?>
									<a href="javascript:;" onclick="window.open('Reportes/graficar.php?datos=<?=$datos_totales?>&opciones=<?=$opciones_totales?>&titulo=<?=$titulo_totales?>','','width=550, height=400');" >
										<img src="images/chart_pie.png" border="0">
									</a>
								<?
									}
								?>
							</td>
							<td class="row1" align="center" nowrap></td>
									</tr>	
						
						<tr>
							<td class="titlemedium" align="center" nowrap>TOTALES</td>
							<?
							foreach( $array_lastallas as $i=>$tales )
							{
							?>
								<td class="titlemedium" align="right" nowrap>
									<?
										echo number_format( $totalestalla[$i]['Totales'] );
									?>
								</td>
							<?
							}//end for
							?>
							<td class="titlemedium" align="center" nowrap>
								<?
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
	<% 
	 } // END if(!empty($IDEmpresa))
	%>
	</table>
	<%						
}// Enf function print()	

%>
</body>