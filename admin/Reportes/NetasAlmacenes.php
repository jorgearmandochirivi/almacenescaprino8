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
				print_from($Mes, $Ano);
			break;

			default :
				print_from( );
			break;

		} // End switch


		/*
		switch ($action) {

			case "view" :
				print_from($IDPuntoVenta,$Fecha);
			break;

			default :
				print_from("");
			break;

		} // End switch
		*/
}
else{
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");
	exit;
}






/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($Mes="", $Ano=""){
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA, $FechaHasta, $Mes_array;


 $IVABD=$IVA;

  if(strtotime($Fecha)<=strtotime("2017-01-31")):
 	$IVA = 0.16;
 endif;
 
 

?>

	<table width="100%">

		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<!--<form action="./" name="frmPuntoVenta" method="post" name="Moviles">--->
                <form action="./" name="frmPuntoVenta" method="post" >
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
									<option value="">Seleccione un a&ntilde;o...</option>
										<?php $year_actual=date("Y"); ?>
										<?php
										for($year=$year_actual;$year>=2006;$year--){ ?>
												<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
										<?php }	?>
								</select><input type="hidden" name="mod" value="NetasAlmacenes"><input type="hidden" name="action" value="view"></td>
						  <td align="left" valign="middle" class="nav">
                            <input type="radio" name="AlmacenPublicado" id="AlmacenPublicado" value="T" <?php if($_POST["AlmacenPublicado"]=="T"); echo "checked"; ?> >Con almacenes no publicados
                            <input type="radio" name="AlmacenPublicado" id="AlmacenPublicado" value="P" <?php if($_POST["AlmacenPublicado"]=="T" || $_POST["AlmacenPublicado"]==""); echo "checked"; ?>>Solo los almacenes publicados
                          <input type="submit" value="Ver Reporte" name="submit" class="submit"></td>
							<td align="left" valign="middle" class="nav">&nbsp;</td>
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
				if($_POST["AlmacenPublicado"]=="P"):
					$condicion_almacen = " Publicar = 'S' ";
				else:
					$condicion_almacen = " 1 ";
				endif;

				//TRAER ARRAY DE LOS PUNTOS DE VENTA
				$sql_puntos = " SELECT Nombre, IDPuntoVenta FROM PuntoVenta Where ".$condicion_almacen." ORDER BY IDCiudad, Nombre  ";
				$qry_puntos = db_query( $sql_puntos );
				while( $r_puntos = db_fetch_array( $qry_puntos ) )
				{

					$array_puntos[ $r_puntos[IDPuntoVenta] ] = $r_puntos[Nombre];
					$array_factura_con_comision=array();

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


					$sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, F.IDCliente, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura, F.ValorBono as ValorBono,
										DF.IVA, DF.IDDetalleFactura, DF.ReteIVA, DF.ReteICA, F.IDFactura, F.IDPuntoVenta, DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF
										FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P
										WHERE F.IDPuntoVenta = '$r_puntos[IDPuntoVenta]'
										AND DATE_FORMAT( F.FechaFactura,'%c' ) = '$Mes' AND DATE_FORMAT( F.FechaFactura,'%Y' ) = '$Ano'
										AND F.IDFactura = DF.IDFactura
										AND F.IDPuntoVenta = DF.IDPuntoVenta
										AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica
										AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
										AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio;";


					$sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, F.IDCliente, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura, F.ValorBono as ValorBono,
										DF.IVA, DF.IDDetalleFactura, DF.ReteIVA, DF.ReteICA, F.IDFactura, F.IDPuntoVenta, DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF
										FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P
										WHERE F.IDPuntoVenta = '$r_puntos[IDPuntoVenta]'
										AND DATE_FORMAT( F.FechaFactura,'%c' ) = '$Mes' AND DATE_FORMAT( F.FechaFactura,'%Y' ) = '$Ano'
										AND F.IDFactura = DF.IDFactura
										AND F.IDPuntoVenta = DF.IDPuntoVenta
										AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica
										AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
										AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio;";

					$sql_facturasant = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, F.ValorBono, DATE_FORMAT(F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF,F.Descuento as DescuentoFactura,DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoPar

										FROM Factura F, DetalleFactura DF
										WHERE F.IDPuntoVenta = '$r_puntos[IDPuntoVenta]'
										AND DATE_FORMAT( F.FechaFactura,'%c' ) = '$Mes' AND DATE_FORMAT( F.FechaFactura,'%Y' ) = '$Ano'

										AND F.IDFactura = DF.IDFactura
										AND F.IDPuntoVenta = DF.IDPuntoVenta
										ORDER BY FechaFacturaF,F.IDFactura
						";
						/*
						 *AND DATE_FORMAT( F.FechaFactura,'%d' ) = '15'
										AND F.IDPuntoVenta = 31
						 **/


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

						$IDDiaSinIva = get_field("DiaSinIva","IDDiaSinIva","Fecha",$valor['FechaFacturaF']);
						
						if(strtotime($valor['FechaFacturaF'])<=strtotime("2017-01-31")):
							$IVA = 0.16;
						elseif((int)$IDDiaSinIva > 0 || strtotime($valor['FechaFacturaF'])==strtotime("2020-06-19") || strtotime($valor['FechaFacturaF'])==strtotime("2020-07-03") || strtotime($valor['FechaFacturaF'])==strtotime("2020-07-19") || strtotime($valor['FechaFacturaF'])==strtotime("2020-11-21") ):
							$IVA = 0;			
						else:
							$IVA = $IVABD;			
						endif;
						
						unset($array_referencias);
                                        if($valor['Numero']=="Excedente"):
											// consulto cliente
											$sql_cambio = db_query("SELECT * FROM Cambio WHERE IDCliente in (".$valor['IDCliente'].") and IDFactura = 0 and Excedente = '".($valor['PrecioU'] / ( 1 - ( $valor['DescuentoRef'] / 100 ) ))."'  Order By IDCambio DESC");
											$row_cambio = db_fetch_array($sql_cambio);
											if (!empty($row_cambio["IDCambio"])):
												$sql_detalle_cambio = db_query("SELECT * FROM DetalleCambio WHERE IDCambio = '".$row_cambio["IDCambio"]."'  Order By IDCambio DESC");
												while($row_detalle_cambio = db_fetch_array($sql_detalle_cambio)):
													$pto_vta_ref = get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_detalle_cambio["IDCodificacionEspecifica"]);
													$id_ref = get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$pto_vta_ref);
													$referencia = get_field("Referencia","Numero","IDReferencia",$id_ref);
													$array_referencias[]= $referencia;
												endwhile;



											endif;




											//$valor['Cantidad'] = 0;
										else:

										endif;


						if($valor['Numero']=="Excedente"):

								//$Pares += $valor['Cantidad'];
							else:
								$valor['Cantidad'];
								$Pares += $valor['Cantidad'];
							endif;


								$descuento_bono=0;
                                if ((int)$valor['ValorBono']>0 && $numero_factura_ant !=  $valor['NumeroFactura']){
									$descuento_bono=number_format($valor['ValorBono']);
								}

								$descuento_bono=0;
								if ((int)$valor['ValorBono']>0 && $numero_factura_ant !=  $valor['NumeroFactura']){
									$valor_bono_impr=number_format($valor['ValorBono']);
									$descuento_bono=$valor['ValorBono'];
								}
								else{
									$descuento_bono=0;
									//echo "0";
									$valor_bono_impr=0;
								}

								$conta_bono++;
								if($conta_bono==1){
									$descuento_bono=$valor['ValorBono'];

								}
								
								$numero_factura_ant = $valor['NumeroFactura'];


							$TotalFactura = $valor[ValorTotal] ;

							


												if( $valor['DescuentoPar'] > 0 )
													$valordescuentopar = ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( $valor['DescuentoPar'] / 100 );													
												else
													$valordescuentopar = 0;

													

													

													if($r_puntos["IDPuntoVenta"]=="24" && $valor['FechaFacturaF']=="2022-06-17" && $valor['NumeroFactura']=="2712" ){														
														//echo "<br>PRECIOUNI " . $valor['PrecioU'];
												}	



													if($r_puntos["IDPuntoVenta"]=="10" && $valor['FechaFacturaF']=="2022-06-16" && $valor['NumeroFactura']=="1085" ){
														//echo "valordescuentopar = ( " . $valor['PrecioU']." * " . $valor['Cantidad']." ) *   ( " . $valor['DescuentoPar']." / 100 )";	
														//echo "<br>PRECCC::".$valor['PrecioU'] . " con iva " . $IVA;
													}


												//consultar forma de pago pa saber si se le resta
												$sql_formasdepago = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '$valor[IDFactura]' AND IDPuntoVenta = '".$r_puntos["IDPuntoVenta"]."' ";
												$qry_formasdepago = db_query( $sql_formasdepago );
												$saldo = 0;
												while( $r_formasdepago = db_fetch_object( $qry_formasdepago ) ){
													if( $r_formasdepago->IDFormaPago == 13 ) //13 FormaPago Saldo
														$saldo = $r_formasdepago->Valor;
														
													if( $r_formasdepago->IDFormaPago == 12 && empty($array_addi_reportado[$valor['FechaFacturaF']][$valor["IDFactura"]][$r_puntos["IDPuntoVenta"]]) ){ //Addi														
														$array_addi[$valor['FechaFacturaF']]+=$r_formasdepago->Valor;
														$GranTotalAddi+=$r_formasdepago->Valor;														
														$array_addi_reportado[$valor['FechaFacturaF']][$valor["IDFactura"]][$r_puntos["IDPuntoVenta"]]="S";
													} 

													if( $r_formasdepago->IDFormaPago == 28 && empty($array_bold_reportado[$valor['FechaFacturaF']][$valor["IDFactura"]][$r_puntos["IDPuntoVenta"]]) ){ //Bold
														$array_bold[$valor['FechaFacturaF']]+=$r_formasdepago->Valor;
														$GranTotalBold+=$r_formasdepago->Valor;														
														$array_bold_reportado[$valor['FechaFacturaF']][$valor["IDFactura"]][$r_puntos["IDPuntoVenta"]]="S";
													} 
												}

												if( $valor['DescuentoFactura'] == 0 )
												{	

													if($IVA==0){
														$valor['PrecioU']=$valor['PrecioU']/($IVABD+1);
														
													}	

													$valorparcial = ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( 1 - (  $valor['DescuentoFactura'] / 100 ) ) ) - ( $valordescuentopar ) - $descuento_bono ;
													
													//echo $valorparcial."-".$TotalFactura."--";
													$pago = $valorparcial - $saldo ;

													if($pago<0 || $valor["ValorTotal"]==0):
														$pago = 0;
													endif;

													 number_format( $pago ,2);
													$Pago += $pago;
												}
												else
												{
													//$valorparcial =  ( ( $valor['PrecioU'] * $valor['Cantidad'] ) + ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) - ( $valordescuentopar );
													
													$Precio =  $valor['PrecioU'] - $valordescuentopar;
													$valorparcial =  ( ( $Precio * $valor['Cantidad'] ) + ( ( $Precio * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) ;


													if($r_puntos["IDPuntoVenta"]=="24" && $valor['FechaFacturaF']=="2022-06-17" && $valor['NumeroFactura']=="2712" ){
														//echo "<br>NumFac::".$valor['NumeroFactura'];
														//echo "<br>PRECIOUNITT " . $valor['PrecioU'];
													}

												
													/* Se agrega pa las mayores */
													$mayortotal = $TotalFactura - $valorparcial;
													if( $mayortotal <> 0 )
													{
														//echo "saldo = ( ".$valorparcial ."/". $TotalFactura." ) * ".$saldo ."  FIN ";
														$saldo = ( $valorparcial / $TotalFactura ) * $saldo ; //Que porcentaje del item es para el total														
														$pago = $valorparcial - $saldo ;
													}//end if
													else //Hasta aqui se agrega pa las mayores
														$pago = $valorparcial - $saldo ;

													if($pago<0):
														$pago = 0;
													endif;


													 number_format( $pago ,2); $Pago += $pago;
												}//end else

												//Traer Comision
												$pcomision = 0;
												$comision = 0;
												if($array_factura_con_comision[$valor[IDFactura]]["Calculada"]=="S"){
													$array_forma_pago[ $valor[IDFactura] ][ValorComision]=0;
												}
												else{
													$sql_comisiones = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '$valor[IDFactura]' AND IDPuntoVenta = '$valor[IDPuntoVenta]' ";
													$qry_comisiones = db_Query( $sql_comisiones );
													$array_forma_pago = array();
													$k = 0;
													while( $r_comisiones = db_fetch_object( $qry_comisiones ) )
													{
														$pcomision = $r_comisiones->Comision / 100;
														//echo "<br>(". $r_comisiones->Valor ."/" . "(1 + " . $IVA.") ) * " . $pcomision;
														//echo "<br> RESULTADO: ". $comisioncalculo=( $valorparcial / (1 + $IVA) ) * $pcomision."<br>";
														$comisioncalculo=( $r_comisiones->Valor / (1 + $IVA) ) * $pcomision;
														$comision +=  ( $valorparcial / (1 + $IVABD) ) * $pcomision;
														$k++;
														$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][$k][IDFormaPago] = $r_comisiones->IDFormaPago;
														$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][$k][Valor] = $r_comisiones->Valor;
														$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][$k][Comision] = $r_comisiones->Comision;
														$array_forma_pago[ $valor[IDFactura] ][ $valor[IDDetalleFactura] ][$k][IDBanco] = $r_comisiones->IDBanco;
														$array_forma_pago[ $valor[IDFactura] ][ValorComision] += $comisioncalculo;

														


													}

													

													$array_factura_con_comision[$valor[IDFactura]]["Calculada"]="S";

												}

												$ComisionBancos += $comisioncalculo;

												$valorbruto = $valorparcial - ($array_forma_pago[ $valor[IDFactura] ][ValorComision]) ;
												//$valorbruto = $valorparcial;

											if($valorbruto<0 || $valor["ValorTotal"]==0):
												$valorbruto = 0;
											endif;

											number_format( $valorbruto,2 );
											$ValorParcial += $valorbruto;


						$valoriva = ( $valorparcial - ( $valorparcial / (1 + $IVA ) ) );

								if($valoriva<0 || $valor["ValorTotal"]==0):
									$valoriva=0;
								endif;

								$ValorIVA += $valoriva;

						$valor_bruto_item=$valorparcial - $valoriva;

							if($valor_bruto_item<0 || $valor["ValorTotal"]==0):
								$valor_bruto_item=0;
							endif;


							$ValorBruto += ( $valorparcial - $valoriva );
							$ValorParcialMostrar += ( $valorparcial - $valoriva );
							
							
							if($r_puntos["IDPuntoVenta"]=="20" && $valor['FechaFacturaF']=="2022-07-18"){
								//echo "<br>FAC: ".$valor['NumeroFactura'];
								//echo "<br>FORM: ".$valorparcial ."-". $comision;
								//echo "<br>LA COMI:". $array_forma_pago[ $valor[IDFactura] ][ValorComision];
								//echo "<br>COMI: "."(". $elvalorc ."/ (1 + ". $IVA . ") ) * " . $pcomision . "Tot: ". $comisioncalculo;	
								//echo "<br>BONO: " . $descuento_bono;		
								//echo "<br>VAL: ".$valorbruto;
								//echo "<br>EL IVA " . $IVABD;
							}


						//Guardar Array
						$array_facturas[ $valor['FechaFacturaF'] ][ $r_puntos["IDPuntoVenta"] ] += $valorbruto;

						//Totales Array
						$tarray_facturas[ $r_puntos[IDPuntoVenta] ] += $valorbruto;

						//Totales Array
						$tarray_facturasfecha[ $valor['FechaFacturaF'] ] += $valorbruto;


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
							<td class="titlemedium" align="center" nowrap>Credito<br>Addi</td>
							<td class="titlemedium" align="center" nowrap>BOLD</td>
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
							<td class="<?=$class?>" align="right" nowrap>
								<?php
								echo  number_format( $array_addi[$key]  , 2 );
								?>
							</td>
							<td class="<?=$class?>" align="right" nowrap>
								<?php
								echo  number_format( $array_bold[$key]  , 2 );
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
							<td class="titlemedium" align="right" nowrap><?=number_format( $GranTotalAddi, 2)?></td>
							<td class="titlemedium" align="right" nowrap><?=number_format( $GranTotalBold, 2)?></td>
							
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
