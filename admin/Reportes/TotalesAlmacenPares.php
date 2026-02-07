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
	Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA,$Mes_array,$FechaDesde, $FechaHasta,$IDPuntoVenta;
	// require( "Reportes/Calc.php" );
	$Calendario = new Date_Calc;
	
	//echo $Calendario->nextWeekday("30","11","2005");
	 
	
	 
 
  ?>
	<br>
	<a class="menuppal" href="./?mod=TotalesAlmacen">
		<img src="images/house.png" border="0">&nbsp;&nbsp;Consolidado almacenes - meses
	</a>
	|
	<a class="menuppal" href="./?mod=TotalesAlmacenReferencia">
		<img src="images/layers.png" border="0">&nbsp;&nbsp;Ventas por almacen - referencia
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
							<td  align='left' valign='middle' class="nav">
							
								Desde	<input type="text" name="FechaDesde" class="input" value="<?php echo fecha()?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>

							</td>
							<td align="left" valign="middle" class="nav">
								
								Hasta	<input type="text" name="FechaHasta" class="input" value="<?php echo fecha()?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
								<input type="hidden" name="mod" value="TotalesAlmacen"><input type="hidden" name="action" value="view"></td>
							<td align="left" valign="middle" class="nav">
								<input type="submit" value="Ver Reporte" name="submit" class="submit">
							</td>
						</tr>
				</form>
			</table>
	
		</td>
		</tr>

		<?php
		if(!empty( $FechaDesde ) && !empty( $FechaHasta ) ){
		?>
		<tr>
		<td>
			<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp; Ventas totales mensuales brutas desde ; <?php echo formatofecha($FechaDesde)?> hasta : <?php echo formatofecha($FechaHasta)?>
						
					</td>
				</tr>
				<?php
					
					$daybegin = substr( $FechaDesde, 8 , 10 );
					$monthbegin = substr( $FechaDesde, 5 , 2 );
					$yearbegin = substr( $FechaDesde, 0, 4 );
					
					$dayend = substr( $FechaHasta, 8 , 10 );
					$monthend = substr( $FechaHasta, 5 , 2 );
					$yearend = substr( $FechaHasta, 0 , 4 );
					
					$monthnow = $monthbegin * 1;

					$datosfechas = array();
					
					$sql_puntos = "SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre";
					$qry_puntos = db_query( $sql_puntos );
					$i = 0;
					while( $r_puntos = db_fetch_array( $qry_puntos ) )
					{
						$array_puntos[$i] = $r_puntos;
						$i++;
					}//end while
					do
					{
						
						
						foreach( $array_puntos as $key => $valor )
						{
							$monthnow = $monthnow * 1;
							$sql_facturas = " SELECT SUM(DF.Cantidad)  as Venta
												FROM Factura F, DetalleFactura DF
												WHERE F.IDPuntoVenta = '$valor[IDPuntoVenta]' 
												AND DATE_FORMAT( F.FechaFactura,'%c' ) = '$monthnow'
												AND DF.IDFactura = F.IDFactura
												ORDER BY FechaFactura ASC ";
											
							$qry_facturas = db_query( $sql_facturas );
							
							$array_facturas = db_fetch_array( $qry_facturas );

							$datosfechas[$monthnow][$valor['IDPuntoVenta']] = $array_facturas['Venta'];
							
						
						}//end for	
						$monthnow = $monthnow + 1;
						
						
					}while($monthnow <= $monthend);
					
					
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
								<td class="titlemedium" align="center" nowrap>Punto</td>
							<?php
							$monthbegin = ( $monthbegin * 1 ) - 1;
							for( $i = $monthbegin; $i < $monthend; $i++ )
							{
							?>
								<td class="titlemedium" align="center" nowrap><?php echo $Mes_array[$i]?></td>
							<?php
							}//end for
							?>
								<td class="titlemedium" align="center" nowrap>TOTALES</td>
										<td class="titlemedium" align="center" nowrap>GRAFICAR</td>
									</tr>
						<?php
						//print_r($datosfechas);
						//print_r($array_puntos);
						foreach( $array_puntos as $key => $valor )
						{
							$class = repetition()?"row2":"row1";
						?>
							<tr>
								<td class="<?php echo $class?>" align="center" nowrap><?php echo $valor['Nombre']?></td>
								<?php
								$monthbegin = ( $monthbegin * 1 );
								for( $i = $monthbegin; $i < $monthend; $i++ )
								{
								?>
									<td class="<?php echo $class?>" align="right" nowrap>
										<?php
											echo number_format( $datosfechas[$i+1][$valor['IDPuntoVenta']] , 2);
											$totalespunto[$valor['IDPuntoVenta']]['Totales'] += $datosfechas[$i+1][$valor['IDPuntoVenta']];
											$totalesmes[$i+1]['Totales'] += $datosfechas[$i+1][$valor['IDPuntoVenta']];
											$total += $datosfechas[$i+1][$valor['IDPuntoVenta']];
											
											//array grafica meses
											$datos[$i+1][$valor['IDPuntoVenta']] = $datosfechas[$i+1][$valor['IDPuntoVenta']];
											$opciones[$i+1][$valor['IDPuntoVenta']] = $valor['Nombre'];
											
											//array grafica punto
											$datospunto[$valor['IDPuntoVenta']][$i+1] = $datosfechas[$i+1][$valor['IDPuntoVenta']];
											$opcionespunto[$valor['IDPuntoVenta']][$i+1] = $Mes_array[$i];

										?>
									</td>
								<?php
								}//end for
								?>
								<td class="<?php echo $class?>" align="center" nowrap>
									<?php
										echo number_format( $totalespunto[$valor['IDPuntoVenta']]['Totales'] , 2);
									?>
								</td>
										<td class="<?php echo $class?>" align="center" nowrap>
										
											<?php
											if( $totalespunto[$valor['IDPuntoVenta']]['Totales'] > 0 )
											{
												$datos_mes = implode(",",$datospunto[$valor['IDPuntoVenta']]);
												$opciones_mes = implode(",",$opcionespunto[$valor['IDPuntoVenta']]);
												$titulopunto = "Ventas ".$valor['Nombre'];
											?>
											<a href="javascript:;" onclick="window.open('Reportes/graficar.php?datos=<?php echo $datos_mes?>&opciones=<?php echo $opciones_mes?>&titulo=<?php echo $titulopunto?>','','width=550, height=400');" >
												<img src="images/chart_pie.png" border="0">
											</a>
											<?php
											}
											?>
										</td>
									</tr>
						
						<?php
						}//while( $r_puntos = db_fetch_object( $qry_puntos ) )
						?>
						<tr >
							<td class="row1" align="center" nowrap></td>
							<?php
							$monthbegin = ( $monthbegin * 1 );
							for( $i = $monthbegin; $i < $monthend; $i++ )
							{
							?>
								<td class="row1" align="center" nowrap>
									<?php
									if( $totalesmes[$i+1]['Totales'] > 0 )
									{	
										$datos_mes = implode(",",$datos[$i+1]);
										$opciones_mes = implode(",",$opciones[$i+1]);
										$titulo = "Ventas ".$Mes_array[$i];
									?>
									<a href="javascript:;" onclick="window.open('Reportes/graficar.php?datos=<?php echo $datos_mes?>&opciones=<?php echo $opciones_mes?>&titulo=<?php echo $titulo?>','','width=550, height=400');" >
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
								
							</td>
							<td class="row1" align="center" nowrap></td>
									</tr>	
						
						<tr>
							<td class="titlemedium" align="center" nowrap>TOTALES</td>
							<?php
							$monthbegin = ( $monthbegin * 1 );
							for( $i = $monthbegin; $i < $monthend; $i++ )
							{
							?>
								<td class="titlemedium" align="right" nowrap>
									<?php
										echo number_format( $totalesmes[$i+1]['Totales'] );
									?>
								</td>
							<?php
							}//end for
							?>
							<td class="titlemedium" align="center" nowrap><?php echo number_format( $total, 2);?></td>
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