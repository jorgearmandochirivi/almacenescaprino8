
<body><?php 

$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "list" :
				list_r($_POST['campo'],$_POST['referencia']);
			break;
			default :

				seleccionareferencia("list");
				//list_r();
			break;

		} // End switch

}//end if(permisos[0] > 2)
else{
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");
	exit;
}
	

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
 $array_lastallas = array( 1=>1, 33=>33, 34=>34, 35=>35, 36=>36, 37=>37, 38=>38, 39=>39, 40=>40, 41=>41, 42=>42, 43=>43, 44=>44 , S=>S , M=>M , L=>L , XL=>XL  );
 
?>
	
	<table width="100%">
		
		<tr>
		<td>
				<table width='60%' align='center' border="0" cellspacing="0" cellpadding="2" class="bordertable">
					<form action="./" name="frmPuntoVenta" method="post">
						<tr>
						  <td  align='left' valign='middle' class="nav"> Desde
					      </td>
						  <td align="left" valign="middle" class="nav">
						  <input  type="text" name="FechaDesde" class="input" value="<?php echo $FechaDesde?>" size="10">
                          <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
                          
                          </td>
						  <td width="3%"  align='left' valign='middle' class="nav">Hasta</td>
							<td width="28%" align="left" valign="middle" class="nav"><input  type="text" name="FechaHasta" class="input" value="<?php echo $FechaHasta?>" size="10">
                            <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
                            </td>
						</tr>
						<tr>
						  <td align="left" valign="middle" class="nav"><span class="col2">Proveedor</span></td>
						  <td align="left" valign="middle" class="nav"><span class="col2">
						    <select name="IDProveedor" id="IDProveedor" class="input proveedor_pedido">
						      <option value="">[Seleccione]</option>
						      <?php 
						$sql_prov = db_query("Select * From Proveedor where Publicar = 'S' order by Nombre asc");
						while ($row_prov = db_fetch_array($sql_prov)): ?>
						      <option value="<?php echo $row_prov[IDProveedor];?>" <?php if($_POST["IDProveedor"]==$row_prov[IDProveedor]) echo "selected"; ?>><?php echo $row_prov[Nombre];?></option>
						      <?php	
						endwhile;
					?>
					      </select>
						  </span></td>
						  <td  align='left' valign='middle' class="nav">Referencia</td>
						  <td align="left" valign="middle" class="nav"><span class="col2">
					      <input type=text class=tbox name=referencia value="<?php echo $_POST["referencia"]?>">
						  </span></td>
					  </tr>
						<tr>
						  <td  align='left' valign='middle' class="nav">Puntos de Venta</td>
						  <td align="left" valign="middle" class="nav"><select name="IDPuntoVenta" onChange="document.frmPuntoVenta.submit();" >
						    <option value="">Seleccione Un Punto de Venta</option>
						    <?php  								
								$qry_punto = db_query("SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
						    </select>
						    <input type="hidden" name="mod" value="ReporteTalla">
						    <input type="hidden" name="action" value="view"></td>
						  <td  align='left' valign='middle' class="nav"><input type="submit" value="Ver Reporte" name="submit" class="submit"></td>
						  <td align="left" valign="middle" class="nav">&nbsp;</td>
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
		
					if($_POST["IDProveedor"])	
						$condicion_filtro = " and R.IDProveedor = '".$_POST["IDProveedor"]."' ";
						
					if($_POST["referencia"])	
						$condicion_filtro .= " and R.Numero = '".$_POST["referencia"]."' ";
						
					if($_POST["IDPuntoVenta"])	
						$condicion_filtro .= " and PVR.IDPuntoVenta = '".$IDPuntoVenta."' ";
		
		
		
		if(!empty( $FechaDesde ) ){
		?>
        
        
        <?php
				//QUERY REFERENCIAS DEL PUNTO DEL VENTA
					 $sql_referencias = " SELECT R.Numero, PVR.IDPuntoVentaReferencia 
											FROM Referencia R, PuntoVentaReferencia PVR 
											WHERE PVR.IDReferencia = R.IDReferencia " . $condicion_filtro . " ORDER BY R.Numero ";
						
					$qry_referencias = db_query( $sql_referencias );
					
					$numero_referencias = db_num_rows( $qry_referencias );
			?>
        
				 
				<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">
						&nbsp; Ventas totales <?php echo $FechaDesde." - ".$FechaHasta ?> - <?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>
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
								<td class="navpic" align="center" nowrap>Referencia</td>
							<?php

							foreach( $array_lastallas as $i=>$tales )
							//for( $i = 33; $i <= 43; $i++ )
							{
							?>
								<td class="navpic" align="center" nowrap><?php echo $i?></td>
							<?php
							}//end for
							?>
								<td class="navpic" align="center" nowrap>TOTALES</td>
								</tr>
						<?php
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
											<td class="navpic" align="center" nowrap><?php echo $linea?></td>
											<?php
											foreach( $array_lastallas as $i=>$tales )
											{
											?>
												<td class="navpic" align="right" nowrap>
													<?php
														
														
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
											<?php
											}//end for
											?>
											<td class="navpic" align="center" nowrap>
												
												<?php
												echo array_sum( $array_linea[$linea] );
												?>
											</td>
												</tr>			
									
									
									
									
									
									
									
									
						<?php			
							
							}//end if
							
							$linea = substr( $valor['Numero'], 0, 2 );
							
							//print_r($array_tallas[$valor['IDPuntoVentaReferencia']]);
						?>
							<tr>
								<td class="<?php echo $class?>" align="center" nowrap><?php echo $valor['Numero']?></td>
								<?php
								foreach( $array_lastallas as $i=>$tales )
								{
								?>
									<td class="<?php echo $class?>" align="right" nowrap>
										<?php
											
											
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
								<?php
								}//end for
								?>
								<td class="<?php echo $class?>" align="center" nowrap>
									<?php
										echo number_format( $totalesreferencia[$valor['IDPuntoVentaReferencia']]['Totales'] , 0);
										if( $totalesreferencia[$valor['IDPuntoVentaReferencia']]['Totales'] > 0 )
										{
											//array grafica totales
											$datostotales[$valor['IDPuntoVentaReferencia']] = $totalesreferencia[$valor['IDPuntoVentaReferencia']]['Totales'];
											$opcionestotales[$valor['IDPuntoVentaReferencia']] = $valor['Numero'];
										}
									?>
								</td>
									</tr>
						
						<?php
						}//while( $r_referencias = db_fetch_object( $qry_referencias ) )
						?>
						<tr>
							<td class="navpic" align="center" nowrap>TOTALES</td>
							<?php
							foreach( $array_lastallas as $i=>$tales )
							{
							?>
								<td class="navpic" align="right" nowrap>
									<?php
										echo number_format( $totalestalla[$i]['Totales'] );
									?>
								</td>
							<?php
							}//end for
							?>
							<td class="navpic" align="center" nowrap>
								<?php
									echo number_format( $total, 0);
									
								?>
							</td>
									</tr>
						
						
							
					</table>
				</td>
			</tr>
		</form>
	
		</table>
        
        <?php } ?>
            
            
            
	</td>
	</tr>
	
	</table>
	<?php 						
}// Enf function print()	

?>
</body>
