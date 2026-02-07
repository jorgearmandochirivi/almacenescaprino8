<body><?php

$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$Title = " Consultar Inventario ";
$MOD = "InventarioCon";
$m="Referencia";
$permisos = get_permiso($ID_Usuario,$m,$Table);
	
	if($permisos[0] >= 2)
{
		switch ($action) {
			
			case "view" :
				print_from($IDPuntoVenta,$Fecha);
			break;
			
			default :
				print_from("");
			
		
		} // End switch
}
else{
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");
	exit;
}

//exit;
		


 
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($IDPuntoVenta="", $Fecha=""){
	Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA,$Mes_array,$FechaDesde, $FechaHasta,$IDPuntoVenta;
	// require( "Reportes/Calc.php" );
	$Calendario = new Date_Calc;
	
	//echo $Calendario->nextWeekday("30","11","2005");
if(strtotime($FechaDesde)<=strtotime("2017-01-31")):
 	$IVA = 0.16;
 endif; 
	

	
 
?>
	<br>
	<a class="menuppal" href="?mod=TotalesAlmacen">
		<img src="images/house.png" border="0">&nbsp;&nbsp;Consolidado almacenes - meses
	</a>
	|
	<a class="menuppal" href="?mod=TotalesAlmacenReferencia">
		<img src="images/layers.png" border="0">&nbsp;&nbsp;Ventas por almacen - referencia</a><br>
	<table width="100%">
		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" name="Moviles">
						<tr>
							<td valign="middle"><img src="images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="nav">
							
								Desde:	<input type="text" name="FechaDesde" class="input" value="<?=fecha()?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>

							</td>
							<td align="left" valign="middle" class="nav">
								
								Hasta	<input type="text" name="FechaHasta" class="input" value="<?=fecha()?>" size="10">

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
			<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp; Ventas totales mensuales brutas desde ; <?=formatofecha($FechaDesde)?> hasta : <?=formatofecha($FechaHasta)?>
						
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
					$yearnow = $yearbegin * 1;

					$datosfechas = array();
					
					$sql_puntos = "SELECT * FROM PuntoVenta WHERE Publicar = 'S' ORDER BY IDCiudad, Nombre";
					$qry_puntos = db_query( $sql_puntos );
					$i = 0;
					while( $r_puntos = db_fetch_array( $qry_puntos ) )
					{
						$array_puntos[$i] = $r_puntos;
						$i++;
					}//end while
                                        
					do
					{
                                         //   echo $yearnow;
						
                                                    
						
						foreach( $array_puntos as $key => $valor )
						{
							$monthnow = $monthnow * 1;
							$sql_facturas = " SELECT ( SUM(F.ValorTotal) )  as Venta
												FROM Factura F
												WHERE F.IDPuntoVenta = '$valor[IDPuntoVenta]' 
												AND DATE_FORMAT( F.FechaFactura,'%c' ) = '$monthnow'
												AND DATE_FORMAT( F.FechaFactura,'%Y' ) = '$yearnow'
												ORDER BY FechaFactura ASC ";
							//echo $sql_facturas . "<br>";	
                                                       //echo $sql_facturas;
							$qry_facturas = db_query( $sql_facturas );
							
							$array_facturas = db_fetch_array( $qry_facturas );

							$datosfechas[$yearnow][$monthnow][$valor['IDPuntoVenta']] = $array_facturas['Venta'] . "||";
							
						
						}//end for	
						$monthnow = $monthnow + 1;
						
						
						if( $monthnow > 12 )
						{
							$monthnow = 1;
							$yearnow = $yearnow + 1;
							
						}//end if
						
						if( $monthnow > $monthend && $yearnow == $yearend )
							$yearnow = $yearnow + 1;
					
						
					}while($monthnow <= $monthend && $yearnow <= $yearend );
					
					
					
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
								<td class="titlemedium" align="center" nowrap>Punto</td>
							<?php
							$monthbegin = ( $monthbegin * 1 ) - 1;
							$yearbegin = ( $yearbegin * 1 ) ;
							
							foreach( $datosfechas as $year => $datos_meses )
								foreach( $datos_meses as $mes => $valores )
								{
							?>
								<td class="titlemedium" align="center" nowrap><?=$Mes_array[$mes - 1] . " " . $mes . " / " .$year?></td>
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
								<td class="<?=$class?>" align="center" nowrap><?=$valor['Nombre']?></td>
								<?php
								$monthbegin = ( $monthbegin * 1 );
								$yearbegin = ( $yearbegin * 1 );
								
								foreach( $datosfechas as $year => $datos_meses )
									foreach( $datos_meses as $mes => $valores )
									{
								?>
									<td class="<?=$class?>" align="right" nowrap>
										<?php
											echo number_format( $datosfechas[$year][$mes][$valor['IDPuntoVenta']] , 2);											
											$totalespunto[$valor['IDPuntoVenta']]['Totales'] += $datosfechas[$year][$mes][$valor['IDPuntoVenta']];
											$totalesmes[$year][$mes]['Totales'] += $datosfechas[$year][$mes][$valor['IDPuntoVenta']];
											$total += $datosfechas[$year][$mes][$valor['IDPuntoVenta']];
											
											//array grafica meses
											$datos[$mes][$valor['IDPuntoVenta']] = $datosfechas[$year][$mes][$valor['IDPuntoVenta']];
											$opciones[$mes][$valor['IDPuntoVenta']] = $valor['Nombre'];
											
											//array grafica punto
											$datospunto[$valor['IDPuntoVenta']][$mes] = $datosfechas[$year][$mes][$valor['IDPuntoVenta']];
											$opcionespunto[$valor['IDPuntoVenta']][$mes] = $Mes_array[$mes - 1];

										?>
									</td>
								<?php
									}//end for
								?>
								<td class="<?=$class?>" align="center" nowrap>
									<?php
										echo number_format( $totalespunto[$valor['IDPuntoVenta']]['Totales'] , 2);
									?>
								</td>
										<td class="<?=$class?>" align="center" nowrap>
										
											<?php
											if( $totalespunto[$valor['IDPuntoVenta']]['Totales'] > 0 )
											{
												$datos_mes = implode(",",$datospunto[$valor['IDPuntoVenta']]);
												$opciones_mes = implode(",",$opcionespunto[$valor['IDPuntoVenta']]);
												$titulopunto = "Ventas ".$valor['Nombre'];
											?>
											<a href="javascript:;" onClick="window.open('Reportes/graficar.php?datos=<?=$datos_mes?>&opciones=<?=$opciones_mes?>&titulo=<?=$titulopunto?>','','width=550, height=400');" >
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
									<a href="javascript:;" onClick="window.open('Reportes/graficar.php?datos=<?=$datos_mes?>&opciones=<?=$opciones_mes?>&titulo=<?=$titulo?>','','width=550, height=400');" >
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
								foreach( $datosfechas as $year => $datos_meses )
									foreach( $datos_meses as $mes => $valores )
							{
							?>
								<td class="titlemedium" align="right" nowrap>
									<?php
										echo number_format( $totalesmes[$year][$mes]['Totales'] );
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