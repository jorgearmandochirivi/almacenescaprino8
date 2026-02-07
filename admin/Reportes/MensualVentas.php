<body><?php
		
		require( $libdir."dhabiles.inc.php" );
	
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
			break;
		
		} // End switch
}
else
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");

 
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($IDPuntoVenta="", $Fecha=""){
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA,$Mes_array,$FechaDesde, $FechaHasta,$IDPuntoVenta, $ReteIVA, $ReteICA;;
 //require( "Reportes/Calc.php" );
 //$Calendario = new Date_Calc;
 
 $ReteICA =  get_field( "ReteICA","Valor","IDReteICA",1 )  ;
 //$ReteIVA = get_field( "ReteIVA","Valor","IDReteIVA",1 ) / 100;
 $ReteFuente = get_field( "ReteFuente","Valor","IDReteFuente",1 ) / 100;
 
  if(strtotime($FechaDesde)<=strtotime("2017-01-31")):
 	$IVA = 0.16;
 endif;
 
?>
	
	<table width="100%">
		
		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" name="Moviles">
						<tr>
							<td valign="middle"><img src="images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="nav">
							
								Desde	<input  type="text" name="FechaDesde" class="input" value="<?php echo $FechaDesde?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>

							</td>
							<td align="left" valign="middle" class="nav">
								
								Hasta	<input  type="text" name="FechaHasta" class="input" value="<?php echo $FechaHasta?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
							</td>
							<td  align='left' valign='middle' class="nav"><img src='images/house.png' border='0'  alt=''></td>
							<td align="left" valign="middle" class="nav">Puntos de Venta	<select name="IDPuntoVenta" onChange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><?php 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
								</select> <input type="hidden" name="mod" value="ReporteMensual"><input type="hidden" name="action" value="view"></td>
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
		if(!empty($IDPuntoVenta) && !empty( $FechaDesde ) && !empty( $FechaHasta ) ){
		?>
		<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp; <br>
				<br>
				<a href="exportar/exporttventas.php?IDPuntoVenta=<?php echo $IDPuntoVenta?>&FechaDesde=<?php echo $FechaDesde?>&FechaHasta=<?php echo $FechaHasta?>">Exportar Archivo</a>
				<br>
				<br>
				<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onSubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp; 
							
						<?php
						echo "MENSUAL CONSIGNACIONES ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta )." ".$FechaDesde." - ".$FechaHasta;
						?>
					</td>
				</tr>
				<?php

					
					$array_fechas = dhabiles($FechaDesde, $FechaHasta);


					//print_r( $array_fechas );
					
					 
					 /********************* TRAER DATOS DE VENTAS CON TARJETAS DE CREDITO Y DEBITO 'ID'S MAYOR QUE 2'*********************/
					$sql_facturas = " SELECT F.*, FormaPago.Descripcion NombrePago,
											DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF,
											SUM( F.ValorTotal ) as TotalFactura, SUM( FP.Valor ) as ValorFP,FP.*,
											SUM( ( FP.Valor / ( 1 + $IVA ) ) * ( FP.Comision / 100 ) ) as ComisionFP
											FROM Factura F, FormaPagoFactura FP, FormaPago
											WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
											AND F.FechaFactura BETWEEN '$FechaDesde' AND '$FechaHasta' 
											AND F.IDFactura = FP.IDFactura
											AND F.IDPuntoVenta = FP.IDPuntoVenta
											AND FormaPago.IDFormaPago = FP.IDFormaPago
											AND FP.IDFormaPago > 2	
											GROUP BY FP.IDFormaPago, DATE_FORMAT( F.FechaFactura, '%Y-%c-%d' )
											ORDER BY FechaFactura ASC";
											
					$qry_facturas = db_query( $sql_facturas );
					
					$i = 0;
					$formapago = array();
					
					while( $r_facturas = db_fetch_array( $qry_facturas ) )
					{
						$array_factura[$r_facturas['FechaFacturaF']][$i] = $r_facturas;
						$i++;
					}//end while( $r_facturas = db_fetch_array( $qry_facturas ) )
					//print_r( $array_factura );
					
					 /********************* TRAER DATOS DE VENTAS CON EFECTIVO 'ID'S MENOR O IGUAL QUE 2'*********************/
					$sql_facturasef = " SELECT F.*, FormaPago.Descripcion NombrePago,
											DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF,
											SUM( F.ValorTotal ) as TotalFactura, SUM( FP.Valor ) as ValorFP,FP.*
											FROM Factura F, FormaPagoFactura FP, FormaPago
											WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
											AND F.FechaFactura BETWEEN '$FechaDesde' AND '$FechaHasta' 
											AND F.IDFactura = FP.IDFactura
											AND F.IDPuntoVenta = FP.IDPuntoVenta
											AND FormaPago.IDFormaPago = FP.IDFormaPago
											AND FP.IDFormaPago <= 2 											
											GROUP BY  DATE_FORMAT( F.FechaFactura, '%Y-%c-%d' )											
											ORDER BY FechaFactura ASC
											";
											
					$qry_facturasef = db_query( $sql_facturasef );
					
					$i = 0;
					$formapagoef = array();
					
					while( $r_facturasef = db_fetch_array( $qry_facturasef ) )
					{
						$array_facturaef[$r_facturasef['FechaFacturaF']][$i] = $r_facturasef;
						$i++;
					}//end while( $r_facturasef = db_fetch_array( $qry_facturasef ) )
					
					
					//traer los codigo de bancos
					$qry_bancos = db_query( $sql_bancos = "SELECT IDBanco, Nombre FROM Banco");
					while( $r_bancos = db_fetch_array( $qry_bancos ) )
					{
						$array_bancos[$r_bancos[IDBanco]] = $r_bancos[Nombre];
					}//en while bancos
									
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td class="titlemedium" nowrap>Fecha</td>
							<td class="titlemedium" nowrap>Banco</td>
							<td class="titlemedium" nowrap>Forma Pago</td>
							<td class="titlemedium" align="center" nowrap>Venta</td>
							<td class="titlemedium" align="center" nowrap>Comisi&oacute;n</td>
							<td class="titlemedium" align="center" nowrap>Rete. Fuente</td>
							<td class="titlemedium" align="center" nowrap>Valor Neto</td>
							<td class="titlemedium" align="center" nowrap>Rte. IVA</td>
							<td class="titlemedium" align="center" nowrap>Rte. ICA</td>
							<td class="titlemedium" align="center" nowrap>Ingreso</td>
						</tr>
						<?php
						
						//voltear el array porque viene al reves
						
						$array_fechas = array_reverse( $array_fechas );
						
						/************************* MOSTRAR LAS VENTAS CON TARJETA DE CREDITO Y DEBITO ********************************/
						foreach( $array_fechas as $Fecha => $valor )
						{ 

							
							$class = repetition()?"row2":"row1";
							//print_r($valor);
							//print_r( $array_factura );
							
							foreach( $array_factura as $key => $datos )
							{

								
								
								//print_r( $datos );
								$FechaConsulta = $Fecha;
								foreach( $datos as $llave => $dato )
								{	
									
									if( in_array( $key, $valor ) )
									{

										if(strtotime($key)<=strtotime("2017-01-31")):
											$IVA = 0.16;
										endif;

										
										$Base = $dato['ValorFP'] / ( 1 + $IVA );
										$ValorIVA = $dato['ValorFP'] - ( $dato['ValorFP'] /  (1 + $IVA ) ) ;

										
										$ValorTotal = $dato['ValorFP'];
										$Comision = $dato['ComisionFP'];
										$ValorRetefuente = ( $Base * $ReteFuente ); 
										$Neto = $ValorTotal - $Comision;
										//$ValorRIVA = $ValorIVA * $ReteIVA; 
										$ValorRIVA = ( $dato['ValorFP'] - ( $dato['ValorFP'] / (1 + $IVA ) ) ) * $ReteIVA;
										//echo "FROMIVA" . "(". $dato['ValorFP'] ."- ( " . $dato['ValorFP'] ."/ (1 + " . $IVA .") ) ) * " . $ReteIVA;										

										$ValorRICA = ( ($Base / 1000) * $ReteICA );
										//$Ingreso = ( $Neto - $ValorRetefuente -  $ValorRIVA - $ValorRICA );
										$Ingreso = $dato['ValorFP']  -  ($ValorRICA + $ValorRIVA + $ValorRetefuente + $ValorIVA + $Comision); 
										//echo  "<br>FORMULA: " . $dato['ValorFP']  ."- (".$ValorRICA ."+". $ValorRIVA ."+". $ValorRetefuente ."+". $ValorIVA ."+". $Comision .")"."<br>"; 
										//echo $Ingreso;
										//echo "<br>";


										$CBanco = $array_bancos[$dato[IDBanco]];
										
										$array_valores[$key][$dato['IDFormaPago']]['IDBanco'] =  $CBanco;
										$array_valores[$key][$dato['IDFormaPago']]['NombrePago'] =  $dato["NombrePago"];
										$array_valores[$key][$dato['IDFormaPago']]['Venta'] +=  $ValorTotal;
										$array_valores[$key][$dato['IDFormaPago']]['Comision'] +=  $Comision;
										$array_valores[$key][$dato['IDFormaPago']]['ValorRetefuente'] +=  $ValorRetefuente;
										$array_valores[$key][$dato['IDFormaPago']]['Neto'] +=  $Neto;
										$array_valores[$key][$dato['IDFormaPago']]['ValorRIVA'] +=  $ValorRIVA;
										$array_valores[$key][$dato['IDFormaPago']]['ValorRICA'] +=  $ValorRICA;
										$array_valores[$key][$dato['IDFormaPago']]['Ingreso'] +=  $Ingreso;
									}//end if
									
								}//end forech
							}//end foreach
						}//end foreach( $r_facturas as $key => $valor )

						
						foreach( $array_valores as $Fecha => $valor )
						{
							
							foreach( $valor as $FormaPago => $datos )
							{
								
						?>
								<tr>
									<td class="<?php echo $class?>" align="center" nowrap><?php echo $Fecha?></td>
									<td class="<?php echo $class?>" align="center" nowrap><?php echo $datos[IDBanco]?></td>
									<td class="<?php echo $class?>" align="center" nowrap><?php echo $datos[NombrePago]?></td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $ValorTotal = $datos[Venta],2 ); $tValorTotal += $ValorTotal;?> </td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $Comision = $datos[Comision] ,2); $tComision += $Comision?></td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $ValorRetefuente = $datos[ValorRetefuente],2 ); $tValorRetefuente += $ValorRetefuente;?></td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $Neto = $datos[Neto] ,2 ); $tNeto += $Neto; ?></td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $ValorRIVA = $datos[ValorRIVA] ,2 ); $tValorRIVA += $ValorRIVA; ?> </td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $ValorRICA = $datos[ValorRICA],2); $tValorRICA += $ValorRICA; ?></td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $Ingreso = $datos[Ingreso] ,2 ); $tIngreso += $Ingreso; ?></td>
								</tr>
						<?php
							}//end for
						}//end for
						
						/****************************** FIN DE MOSTRAR LAS VENTAS CON TARJETA DE CREDITO Y DEBITO ********************************/
						
						
						/************************* MOSTRAR LAS VENTAS CON EFECTIVO ********************************/
						foreach( $array_fechas as $Fecha => $valor )
						{ 
							$class = repetition()?"row2":"row1";
							//print_r($valor);
							foreach( $array_facturaef as $key => $datos )
							{
								foreach( $datos as $llave => $dato )
								{
									if( in_array( $key, $valor ) )
									{
										$Base = $dato['ValorFP'] / ( 1 + $IVA );
										$ValorIVA = $dato['ValorFP'] - ( $dato['ValorFP'] / ( 1 + $IVA ) ) ;
							
										$ValorTotal = $dato['ValorFP'];
										$Comision = 0;
										$ValorRetefuente = 0;//( $Base * $ReteFuente ); 
										$Neto = $ValorTotal - $Comision;
										$ValorRIVA = 0;//$ValorIVA * $ReteIVA; 
										$ValorRICA = 0;//( ($Base / 1000) * $ReteICA );
										//$Ingreso = ( $Neto - $ValorRetefuente -  $ValorRIVA - $ValorRICA );
										$Ingreso = $dato['ValorFP']  -  ($ValorRICA + $ValorRIVA + $ValorRetefuente + $ValorIVA + $Comision); 
										

										$CBanco = $array_bancos[$dato[IDBanco]];
										
										$array_valoresef[$key]['IDBanco'] =  $CBanco;
										$array_valoresef[$key]['NombrePago'] =  $dato["NombrePago"];
										$array_valoresef[$key]['Venta'] +=  $ValorTotal;
										$array_valoresef[$key]['Comision'] +=  $Comision;
										$array_valoresef[$key]['ValorRetefuente'] +=  $ValorRetefuente;
										$array_valoresef[$key]['Neto'] +=  $Neto;
										$array_valoresef[$key]['ValorRIVA'] +=  $ValorRIVA;
										$array_valoresef[$key]['ValorRICA'] +=  $ValorRICA;
										$array_valoresef[$key]['Ingreso'] +=  $Ingreso;
									}//end if
								}//end forech
							}//end foreach
						}//end foreach( $r_facturas as $key => $valor )

						foreach( $array_valoresef as $Fecha => $datos )
						{
								
						?>
								<tr>
									<td class="<?php echo $class?>" align="center" nowrap><?php echo $Fecha?></td>
									<td class="<?php echo $class?>" align="center" nowrap><?php echo $datos[IDBanco]?></td>
									<td class="<?php echo $class?>" align="center" nowrap><?php echo $datos[NombrePago]?></td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $ValorTotal = $datos[Venta],2 ); $tValorTotal += $ValorTotal;?> </td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $Comision = $datos[Comision] ,2); $tComision += $Comision?></td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $ValorRetefuente = $datos[ValorRetefuente],2 ); $tValorRetefuente += $ValorRetefuente;?></td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $Neto = $datos[Neto] ,2 ); $tNeto += $Neto; ?></td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $ValorRIVA = $datos[ValorRIVA] ,2 ); $tValorRIVA += $ValorRIVA; ?> </td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $ValorRICA = $datos[ValorRICA],2); $tValorRICA += $ValorRICA; ?></td>
									<td class="<?php echo $class?>" align="right" nowrap><?php echo number_format( $Ingreso = $datos[Ingreso] ,2 ); $tIngreso += $Ingreso; ?></td>
								</tr>
						<?php
						}//end for
						
						/********************** FIN DE MOSTRAR LAS FECHAS CON EFECTIVO *********************************************/
						
						
						
						?>					
						<tr>
							<td class="titlemedium" colspan="3" align="right" nowrap>TOTALES</td>							
							<td class="titlemedium" align="right" nowrap><?php echo number_format( $tValorTotal, 2) ?></td>
							<td class="titlemedium" align="right" nowrap><?php echo number_format( $tComision, 2) ?></td>
							<td class="titlemedium" align="right" nowrap><?php echo number_format( $tValorRetefuente, 2) ?></td>
							<td class="titlemedium" align="right" nowrap><?php echo number_format( $tNeto , 2) ?></td>
							<td class="titlemedium" align="right" nowrap><?php echo number_format( $tValorRIVA , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?php echo number_format( $tValorRICA , 2)?></td>
							<td class="titlemedium" align="right" nowrap><?php echo number_format( $tIngreso , 2)?></td>
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
