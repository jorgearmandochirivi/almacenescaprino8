<body><%
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
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA, $FechaHasta;
 
%>
	
	<table width="100%">
		
		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" name="Moviles">
						<tr>
							<td valign="middle"><img src="images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="nav">Fecha	<input readonly type="text" name="Fecha" class="input" value="<?=fecha()?>">

								<script language="JavaScript1.2">
							<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
							//-->
						</script>
								  Hasta Fecha <input readonly type="text" name="FechaHasta" class="input" value="<?=$FechaHasta?>">

								<script language="JavaScript1.2">
							<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
							//-->
						</script>
							</td>
							<td  align='left' valign='middle' class="nav"><img src='images/house.png' border='0'  alt=''></td>
							<td align="left" valign="middle" class="nav">Puntos de Venta	<select name="IDPuntoVenta" onchange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><% 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							%>
								</select> <input type="hidden" name="mod" value="MensualNetas"><input type="hidden" name="action" value="view"></td>
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
		<%
		if(!empty($IDPuntoVenta)){
		%>
		<tr>
		<td><br>
				<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp; 
							
						Reporte Ventas Diarias Almacen : <?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>&nbsp; &nbsp; Fecha: <?=formatofecha( $Fecha )?>
						 - <?=formatofecha( $FechaHasta )?>
					</td>
				</tr>
				<?
					echo $sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, F.ValorBono, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura ,DATE_FORMAT(F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF
										FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P 
										WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
										AND DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) >= DATE_FORMAT('$Fecha','%Y-%c-%d' )  AND DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) <= DATE_FORMAT('$FechaHasta','%Y-%c-%d' ) 
										AND F.IDFactura = DF.IDFactura 
										AND F.IDPuntoVenta = DF.IDPuntoVenta
										AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica 
										AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia 
										AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio;";
											
					
					$qry_facturas = db_query( $sql_facturas );
					
					$i = 0;
					$formapago = array();
					
					while( $array_factura = db_fetch_array( $qry_facturas ) )
					{
						$r_facturas[$i] = $array_factura;
						$i++;
						//echo $array_factura[IDFactura]."<br>";
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
							//$valorparcial =  ( ( $valor['PrecioU'] * $valor['Cantidad'] ) + ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) - ( $valordescuentopar );
							$Precio =  $valor['PrecioU'] - $valordescuentopar;
							$valorparcial =  ( ( $Precio * $valor['Cantidad'] ) + ( ( $Precio * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) ;
													
							$pago = $valorparcial - $saldo ;  //PAGO
						}//end else
						//fin pago
						
						//Traer Comision
						$pcomision = 0;
						$comision = 0;
						$sql_comisiones = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '$valor[IDFactura]' ";
						$qry_comisiones = db_Query( $sql_comisiones );
						while( $r_comisiones = db_fetch_object( $qry_comisiones ) )
						{
							$pcomision = $r_comisiones->Comision / 100;
							$comision +=  ( $valorparcial / (1 + $IVA) ) * $pcomision;
						}						//Comision
						

						
						
						
						//valor iva
						$valoriva = ( $valorparcial - ( $valorparcial / (1 + $IVA ) ) );
						
						//valor bruto
						$valorbruto = $valorparcial - $valoriva;
						
						//Guardar Array
						$array_facturas[ $valor['FechaFacturaF'] ][ 'Pares' ] += $valor['Cantidad'];
						if((int)$valor['ValorBono']>0):
							$array_facturas[ $valor['FechaFacturaF'] ][ 'ValorBono' ] = $valor['ValorBono'];	
							$pago -=  $valor['ValorBono'];
							$valorparcial -=  $valor['ValorBono'];
						endif;
						
						$array_facturas[ $valor['FechaFacturaF'] ][ 'ValorParcial' ] += $valorparcial - $comision;
						$array_facturas[ $valor['FechaFacturaF'] ][ 'Pago' ] += $pago;
						$array_facturas[ $valor['FechaFacturaF'] ][ 'Saldo' ] += $saldo;
						$array_facturas[ $valor['FechaFacturaF'] ][ 'ValorBruto' ] += $valorbruto;
						$array_facturas[ $valor['FechaFacturaF'] ][ 'Comision' ] += $comision;
						$array_facturas[ $valor['FechaFacturaF'] ][ 'IVA' ] += $valoriva;

						//Totales Array
						$tarray_facturas[ 'Pares' ] += $valor['Cantidad'];
						$tarray_facturas[ 'ValorParcial' ] += $valorparcial - $comision;
						$tarray_facturas[ 'Pago' ] += $pago;
						$tarray_facturas[ 'Saldo' ] += $saldo;
						$tarray_facturas[ 'ValorBruto' ] += $valorbruto;
						$tarray_facturas[ 'Comision' ] += $comision;
						$tarray_facturas[ 'IVA' ] += $valoriva;
						
						
						
						

						
						$valorparcial = 0;
						$pago = 0;
						$saldo = 0;
						$valorbruto = 0;
						$comision = 0;
						$valoriva = 0;
						
						
						
					}//end for 
										
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
										<td class="titlemedium" align="center" nowrap>Fecha</td>
										<td class="titlemedium" align="center" nowrap>Pago</td>
										<td class="titlemedium" align="center" nowrap>Saldo</td>
										<td class="titlemedium" align="center" nowrap>Comision Bancos</td>
										<td class="titlemedium" align="center" nowrap>Vr. Parcial</td>
							<td class="titlemedium" align="center" nowrap>IVA</td>
							<td class="titlemedium" align="center" nowrap>Valor Bruto</td>
						</tr>
						<?
						foreach( $array_facturas as $key => $valor )
						{ 
							//print_r( $valor );
							$class = repetition()?"row2":"row1";
							//print_r($valor);
						?>
						<tr>
							<td class="<?=$class?>" align="center" nowrap><?=$key?></td>
							<td class="<?=$class?>" align="right" nowrap>
								<?
									echo number_format( $valor[Pago], 2 );
								?>
							</td>
							<td class="<?=$class?>" align="right" nowrap>
								<?
									echo number_format( $valor[Saldo],2 );
								?>
							</td>
							<td class="<?=$class?>" align="right" nowrap>
								<?
									echo number_format( $valor[Comision],2 );
								?>
							</td>
							<td class="<?=$class?>" align="right" nowrap>
								<?
									echo number_format( $valor[ValorParcial],2 );
								?>
							</td>
							<td class="<?=$class?>" align="right" nowrap>
								<?
									echo number_format( $valor[IVA],2 );
								?>
							</td>
							<td class="<?=$class?>" align="right" nowrap>
								<?
									echo number_format( $valor[ValorBruto],2 );
								?>
							</td>
						</tr>
						
						<?
						}//end foreach( $r_facturas as $key => $valor )
						?>
							
						<tr>
							<td class="titlemedium" align="right" nowrap></td>
							<td class="titlemedium" align="right" nowrap><?=number_format( $tarray_facturas[Pago] , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?=number_format( $tarray_facturas[Saldo] , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?=number_format( $tarray_facturas[Comision] , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?=number_format( $tarray_facturas[ValorParcial] , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?=number_format( $tarray_facturas[IVA] , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?=number_format( $tarray_facturas[ValorBruto] , 2)?></td>
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