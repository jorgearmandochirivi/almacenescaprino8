<?php
include( "../config.inc.php" );
$sep = "|";
$saltolinea = "\n";


header("Pragma: ");
header("Cache-Control: ");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
 header("Content-Type: text/plain");

header("Content-Disposition: attachment; filename=" .  $Fecha . "_" . $IDPuntoVenta);


if(!empty($IDPuntoVenta)){

					$sql_facturas = " SELECT F.IDCliente, F.IDPuntoVenta, F.IDEmpleado, F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura,
										DF.IVA, DF.IDDetalleFactura, DF.ReteIVA, DF.ReteICA, F.IDFactura, F.IDPuntoVenta 
										FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P 
										WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
										AND DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) BETWEEN '" . $Fecha . "' AND '" . $FechaHasta . "'
										AND F.IDFactura = DF.IDFactura 
										AND F.IDPuntoVenta = DF.IDPuntoVenta
										AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica 
										AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia 
										AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio AND R.Reportes <> 'N';";
											
					
					$qry_facturas = db_query( $sql_facturas );
					
					$i = 0;
					$formapago = array();
					
					while( $array_factura = db_fetch_array( $qry_facturas ) )
					{
						$r_facturas[$i] = $array_factura;
						$i++;
						
					}//end while( $r_facturas = db_fetch_array( $qry_facturas ) )
										


						foreach( $r_facturas as $key => $valor )
						{ 
							//print_r( $valor );
							$class = repetition()?"row2":"row1";
							//print_r($valor);
						
						
						echo substr( $valor['FechaFactura'], 0, 10) . $sep; 
						echo substr ( $valor['FechaFactura'], 11 ) . $sep;
						
						
                            $TotalFactura = $valor[ValorTotal] ;
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
                                    $saldo = $r_formasdepago->Valor;
                            
                            if( $valor['DescuentoFactura'] == 0 )
                            {
                                $valorparcial = ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( 1 - (  $valor['DescuentoFactura'] / 100 ) ) ) - ( $valordescuentopar ) ;
                                //echo $valorparcial."-".$TotalFactura."--";
                                $pago = $valorparcial - $saldo ;
                                echo $pago  . $sep;
                            }
                            else
                            {
                                //$valorparcial =  ( ( $valor['PrecioU'] * $valor['Cantidad'] ) + ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) - ( $valordescuentopar );
                                $Precio =  $valor['PrecioU'] - $valordescuentopar;
                                $valorparcial =  ( ( $Precio * $valor['Cantidad'] ) + ( ( $Precio * $valor['Cantidad'] ) *   (  $valor['DescuentoFactura'] / 100  ) ) ) ;
                                /* Se agrega pa las mayores */
                                $mayortotal = $TotalFactura - $valorparcial;
                                if( $mayortotal <> 0 )
                                {
                                    $saldo = ( $valorparcial / $TotalFactura ) * $saldo ; //Que porcentaje del item es para el total														
                                    $pago = $valorparcial - $saldo ;
                                }//end if
                                else //Hasta aqui se agrega pa las mayores
                                    $pago = $valorparcial - $saldo ;
                                echo $pago  . $sep;
                            }//end else
                            
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
                            
                        echo get_field( "Cliente","Cedula","IDCliente",$valor['IDCliente'] ) . $sep;
						echo get_field( "Empleado","Cedula","IDEmpleado",$valor['IDEmpleado'] ) . $sep;
						echo $valor['IDPuntoVenta'] . $sep;
						echo $valor['Numero'] . $sep;


                      	echo $saltolinea;                      
                                           
						}//end foreach( $r_facturas as $key => $valor )
						
                        
	 } // END if(!empty($IDEmpresa))
	 
	 
	
    ?>