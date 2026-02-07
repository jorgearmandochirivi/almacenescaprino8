<body><?php
		switch ($action) {
			
			case "view" :
				print_from($Mes, $Ano);
			break;
			
			default :
				print_from( );
			break;
		
		} // End switch


 
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($Mes="", $Ano=""){
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA, $FechaHasta, $Mes_array;
 
  ?>
	
	<table width="100%">
		
		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" name="Moviles">
						<tr>
							<td valign="middle"><img src="images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="nav"><select name="Mes" >
									<option value="">Seleccione un mes...</option><?php 								
										foreach( $Mes_array as $keymes=>$mes ){
											$keymes = $keymes+1;
											echo "<option value=".$keymes." " ;if($Mes == $keymes ) echo "selected"; echo ">&nbsp;&nbsp;$mes</option>";
										}
									?>
								</select></td>
							<td  align='left' valign='middle' class="nav"><img src='images/house.png' border='0'  alt=''></td>
							<td align="left" valign="middle" class="nav"><select name="Ano" >
									<option value="">Seleccione un a&ntilde;o...</option><?php 								
										
											echo "<option value='2006' "; ;if($Ano == "2006" ) echo "selected"; echo ">&nbsp;&nbsp;2006</option>";
											echo "<option value='2007' "; ;if($Ano == "2007" ) echo "selected"; echo ">&nbsp;&nbsp;2007</option>";
											echo "<option value='2008' "; ;if($Ano == "2008" ) echo "selected"; echo ">&nbsp;&nbsp;2008</option>";
											echo "<option value='2009' "; ;if($Ano == "2009" ) echo "selected"; echo ">&nbsp;&nbsp;2009</option>";
											echo "<option value='2010' "; ;if($Ano == "2010" ) echo "selected"; echo ">&nbsp;&nbsp;2010</option>";
											echo "<option value='2011' "; ;if($Ano == "2011" ) echo "selected"; echo ">&nbsp;&nbsp;2011</option>";
											echo "<option value='2012' "; ;if($Ano == "2012" ) echo "selected"; echo ">&nbsp;&nbsp;2012</option>";
											echo "<option value='2013' "; ;if($Ano == "2013" ) echo "selected"; echo ">&nbsp;&nbsp;2013</option>";
											echo "<option value='2014' "; ;if($Ano == "2014" ) echo "selected"; echo ">&nbsp;&nbsp;2014</option>";
											echo "<option value='2015' "; ;if($Ano == "2015" ) echo "selected"; echo ">&nbsp;&nbsp;2015</option>";
											echo "<option value='2016' "; ;if($Ano == "2016" ) echo "selected"; echo ">&nbsp;&nbsp;2016</option>";
											echo "<option value='2017' "; ;if($Ano == "2017" ) echo "selected"; echo ">&nbsp;&nbsp;2017</option>";
									?>
								</select><input type="hidden" name="mod" value="NetasAlmacenes"><input type="hidden" name="action" value="view"></td>
							<td align="left" valign="middle" class="nav">
								<input type="submit" value="Ver Reporte" name="submit" class="submit">
							</td>
						</tr>
				</form>
			</table>
	
		</td>
		</tr>
		
		<br>
		<br>
		<?php
		if(!empty($Mes)){
		?>
		<tr>
		<td><br>
				<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp; 
							
						Reporte Ventas Almacenes : Mes <?=$Mes ?>&nbsp; &nbsp; A&ntilde;o: <?=$Ano?>
					</td>
				</tr>
				<?php
				
				//TRAER ARRAY DE LOS PUNTOS DE VENTA
				$sql_puntos = " SELECT Nombre, IDPuntoVenta FROM PuntoVenta ORDER BY IDCiudad, Nombre  ";
				$qry_puntos = db_query( $sql_puntos );
				while( $r_puntos = db_fetch_array( $qry_puntos ) )
				{
				
					$array_puntos[ $r_puntos[IDPuntoVenta] ] = $r_puntos[Nombre];
					
					$sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura ,
					DATE_FORMAT(F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF
										FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P 
										WHERE F.IDPuntoVenta = '$r_puntos[IDPuntoVenta]' 
										AND DATE_FORMAT( F.FechaFactura,'%c' ) = '$Mes' AND DATE_FORMAT( F.FechaFactura,'%Y' ) = '$Ano' 
										AND F.IDFactura = DF.IDFactura 
										AND F.IDPuntoVenta = DF.IDPuntoVenta
										AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica 
										AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia 
										AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio AND R.Reportes <> 'N';";
											
					$sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, DATE_FORMAT(F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF,F.Descuento as DescuentoFactura,DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoPar
					
										FROM Factura F, DetalleFactura DF
										WHERE F.IDPuntoVenta = '$r_puntos[IDPuntoVenta]' 
										AND DATE_FORMAT( F.FechaFactura,'%c' ) = '$Mes' AND DATE_FORMAT( F.FechaFactura,'%Y' ) = '$Ano' 
										AND F.IDFactura = DF.IDFactura 
										AND F.IDPuntoVenta = DF.IDPuntoVenta
						";
					
					
					$qry_facturas = db_query( $sql_facturas );
					
					$i = 0;
					$formapago = array();
					$r_facturas = array();
					
					while( $array_factura = db_fetch_array( $qry_facturas ) )
					{
						$r_facturas[$i] = $array_factura;
						$i++;
						
					}//end while( $r_facturas = db_fetch_array( $qry_facturas ) )
					
					
					foreach( $r_facturas as $key => $valor )
					{
						//comienzo pago
						if( $valor['DescuentoPar'] > 0 )
							$valordescuentopar = ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( $valor['DescuentoPar'] / 100 );
						else
							$valordescuentopar = 0;
						
						
						//consultar forma de pago pa saber si se le resta
						$sql_formasdepago = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '$valor[IDFactura]' AND IDPuntoVenta = '$IDPuntoVenta' ";
						$qry_formasdepago = db_query( $sql_formasdepago );
						$saldo = 0;
						while( $r_formasdepago = db_fetch_object( $qry_formasdepago ) )
							if( $r_formasdepago->IDFormaPago == 13 ) //13 FormaPago Saldo
								$saldo = $r_formasdepago->Valor;  //saldo
						
						if( $valor['DescuentoFactura'] == 0 )
						{
							$valorparcial = ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( 1 - (  $valor['DescuentoFactura'] / 100 ) ) ) - ( $valordescuentopar ) ;
							$pago = $valorparcial - $saldo ;  //PAGO
						}
						else
						{
							$valorparcial =  ( ( $valor['PrecioU'] * $valor['Cantidad'] ) + ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) - ( $valordescuentopar );
							$Precio =  $valor['PrecioU'] - $valordescuentopar;
							$valorparcial =  ( ( $Precio * $valor['Cantidad'] ) + ( ( $Precio * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) ;
													
							$pago = $valorparcial - $saldo ;  //PAGO
						}//end else
						//fin pago
						
						//$valorparcial = $valor["ValorTotal"];
						 
						//Traer Comision
						
						$pcomision = 0;
						$comision = 0;
						$sql_comisiones = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '$valor[IDFactura]' ";
						$qry_comisiones = db_Query( $sql_comisiones );
						while( $r_comisiones = db_fetch_object( $qry_comisiones ) )
						{
							$pcomision = $r_comisiones->Comision / 100;
							$comision +=  ( $valorparcial / (1 + $IVA) ) * $pcomision;
						}
						//Comision
						
						
						//valor iva
						$valoriva = ( $valorparcial - ( $valorparcial / (1 + $IVA ) ) );
						
						//valor bruto
						$valorbruto = $valorparcial - $valoriva;
						
						//Guardar Array
						$array_facturas[ $valor['FechaFacturaF'] ][ $r_puntos[IDPuntoVenta] ] += $valorparcial - $comision;

						//Totales Array
						$tarray_facturas[ $r_puntos[IDPuntoVenta] ] += $valorparcial - $comision;
						
						//Totales Array
						$tarray_facturasfecha[ $valor['FechaFacturaF'] ] += $valorparcial - $comision;
						
						
						$Fechas[ $valor['FechaFacturaF'] ] = $valor['FechaFacturaF'];

						
						$valorparcial = 0;
						$pago = 0;
						$saldo = 0;
						$valorbruto = 0;
						$comision = 0;
						$valoriva = 0;
						
						
						
					}//end for 
				
				
				}//end while puntos
				
				asort( $Fechas );
										
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td class="titlemedium" align="center" nowrap>Fecha</td>
							
							
							<?php
							foreach( $array_puntos as $idpuntoventa => $nombrepunto )
							{
							?>
							<td class="titlemedium" align="center" nowrap><?=$nombrepunto?></td>
							<?php
							}//end for
							?>
							<td class="titlemedium" align="center" nowrap>Totales</td>
						</tr>
						<?php
						foreach($Fechas as $key => $ValorFecha )
						{ 
							//print_r( $valor );
							$class = repetition()?"row2":"row1";
							//print_r($valor);
						?>
						<tr>
							<td class="<?=$class?>" align="center" nowrap><?=$key?></td>
							
							<?php
							foreach( $array_puntos as $idpuntoventa => $nombrepunto )
							{
							?>
							
							<td class="<?=$class?>" align="right" nowrap>
								<?php
									echo number_format(  $array_facturas[ $key ][ $idpuntoventa ]  , 2 );
								?>
							</td>
							<?php
							}//end for
							?>
							<td class="<?=$class?>" align="right" nowrap>
								<?php
									echo number_format( $tarray_facturasfecha[ $key ]  , 2 );
								?>
							</td>
						</tr>
						
						<?php
						}//end foreach($Fechas as $key => $ValorFecha )
						?>
							
						<tr>
							<td class="titlemedium" align="right" nowrap>Total</td>
							<?php
							foreach( $array_puntos as $idpuntoventa => $nombrepunto )
							{
							?>
							<td class="titlemedium" align="right" nowrap><?=number_format( $tarray_facturas[ $idpuntoventa ] , 2)?></td>
							<?php
							}
							?>
							<td class="titlemedium" align="right" nowrap><?=number_format( array_sum( $tarray_facturas ) , 2)?></td>
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
