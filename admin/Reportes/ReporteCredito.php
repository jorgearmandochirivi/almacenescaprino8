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
		
		
} // End switch
else{
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");
	exit;
}	


		require( $libdir."dhabiles.inc.php" );

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
 //require( "Reportes/Calc.php" );
 //$Calendario = new Date_Calc;

?>

	<table width="100%">

		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" name="Moviles">
						<tr>
							<td valign="middle"><img src="images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="nav">

								Desde	<input  type="text" name="FechaDesde" class="input" value="<?=$FechaDesde?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//-->
								</script>

							</td>
							<td align="left" valign="middle" class="nav">

								Hasta	<input  type="text" name="FechaHasta" class="input" value="<?=$FechaHasta?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//-->
								</script>
							</td>
							<td  align='left' valign='middle' class="nav"><img src='images/house.png' border='0'  alt=''></td>
							<td align="left" valign="middle" class="nav">Puntos de Venta
								<select name="IDPuntoVenta" onchange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><?php
								$qry_punto = db_query("SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
								</select> <input type="hidden" name="mod" value="ReporteCredito"><input type="hidden" name="action" value="view">
							</td>
							<td align="left" valign="middle" class="nav">Estado
								<select name="Estado" onchange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Estado</option>
									<option value="AlDia" <?php if($_POST["Estado"]=="AlDia") echo "selected"; ?>>Al dia</option>
									<option value="Vencida" <?php if($_POST["Estado"]=="Vencida") echo "selected"; ?>>Vencida</option>
									<option value="Castigada" <?php if($_POST["Estado"]=="Castigada") echo "selected"; ?>>Castigada</option>
									<option value="Pagado" <?php if($_POST["Estado"]=="Pagado") echo "selected"; ?>>Pagado</option>
								}
							?>
								</select> <input type="hidden" name="mod" value="ReporteCredito"><input type="hidden" name="action" value="view">
							</td>


							</tr>
							<tr>
								<td align="left" valign="middle" class="nav">&nbsp;</td>
								<td align="left" valign="middle" class="nav">Cedula
									<input type="text" name="Cedula" id="Cedula" value="<?php echo $_POST["Cedula"]; ?>"></td>


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
		if( !empty( $_POST["Cedula"] )  ){
				 $otra_condicion=" AND CL.Cedula = '".$_POST["Cedula"]."'";
		}

		if( !empty( $FechaDesde ) && !empty( $FechaHasta ) ){
		?>
		<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp; <br>
				<br>
				<!--<a href="exportar/exporttventas.php?IDPuntoVenta=<?=$IDPuntoVenta?>&FechaDesde=<?=$FechaDesde?>&FechaHasta=<?=$FechaHasta?>">Exportar Archivo</a>-->
               <a href="exportar/exportcredito.php?IDPuntoVenta=<?=$IDPuntoVenta?>&FechaDesde=<?=$FechaDesde?>&FechaHasta=<?=$FechaHasta?>&Cedula=<?php echo $_POST["Cedula"] ?>&Estado=<?php echo $_POST["Estado"] ?>">
               	<img src="../images/excel_icon.gif" alt="" width="20" height="20" border="0" >
               	Exportar Archivo
               </a>
				<br>
				<br>
				<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">
			<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp;

						<?php
						echo "CREDITOS ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta )." ".$FechaDesde." - ".$FechaHasta;
						?>
					</td>
				</tr>
				<?php



					 /********************* TRAER DATOS DE VENTAS CON TARJETAS DE CREDITO Y DEBITO 'ID'S MAYOR QUE 2'*********************/
					if( !empty( $IDPuntoVenta ) )
						$condicion = " C.IDPuntoVenta = '$IDPuntoVenta' AND F.IDPuntoVenta = '" . $IDPuntoVenta . "' AND ";
						$sql_facturas = " SELECT C.*,  DATE_FORMAT( C.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF, F.NumeroPagare, F.ComentarioCredito, F.FechaUltimaGestion, F.FechaCartaNotificacion, F.FechaReporteCredito, F.FechaUtimoComentario, F.IDFactura, F.ValorTotalSinBono
											FROM Credito C, Factura F, Cliente CL
											WHERE $condicion C.IDFactura = F.IDFactura AND C.IDPuntoVenta = F.IDPuntoVenta AND CL.IDCliente=F.IDCliente AND C.FechaFactura BETWEEN '$FechaDesde' AND '$FechaHasta' AND F.Estado <> 'ANULADA' $otra_condicion
											ORDER BY FechaFactura DESC, IDPuntoVenta";

					$qry_facturas = db_query( $sql_facturas );

					
					//Puntos de Venta
					$sql_puntos = " SELECT IDPuntoVenta, Nombre FROM PuntoVenta ";
					$qry_puntos = db_query( $sql_puntos );
					while( $r_puntos = db_fetch_array( $qry_puntos ) )
						$array_puntos[ $r_puntos[ IDPuntoVenta ] ] = $r_puntos[ Nombre ];


				?>

				<tr>
					<td class='mainbg'>
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
										<td class="titlemedium" nowrap>Fecha</td>
										<td class="titlemedium" align="center" nowrap># Cdito</td>
										<td class="titlemedium" align="center" nowrap># Pagare</td>
										<td class="titlemedium" nowrap>Cedula</td>
										<td class="titlemedium" width="250px" nowrap>Cliente</td>
							<td class="titlemedium" align="center" nowrap>Pto de vta</td>
							<td class="titlemedium" align="center" nowrap>Nro Fta</td>
								<td class="titlemedium" align="center" nowrap>Vr Fact.</td>
							<td class="titlemedium" align="center" nowrap>Vr Saldo</td>
							<td class="titlemedium" align="center" nowrap>Vr Cuota</td>
							<td class="titlemedium" align="center" nowrap>C. Abona</td>
							<td class="titlemedium" align="center" nowrap>Total Abonado</td>
							<td class="titlemedium" align="center" nowrap>Fechas Abonado</td>
							<td class="titlemedium" align="center" nowrap>C. Pdtes</td>
							<td class="titlemedium" align="center" nowrap>Val. Pdte</td>
							<td class="titlemedium" align="center" nowrap>Fecha Prox Pago</td>

							<td class="titlemedium" align="center" nowrap>Fecha Ult Gestion</td>

							<td class="titlemedium" align="center" nowrap>Fecha Carta Notif</td>
							<td class="titlemedium" align="center" nowrap>Fecha Reportado</td>

            	<td class="titlemedium" align="center" nowrap>Estado</td>
            	<td class="titlemedium" align="center" nowrap># Cuotas</td>
            	<td class="titlemedium" align="center" nowrap>Vr Castigado</td>
							<td class="titlemedium" width="300px" align="left" nowrap>Comentario</td>
									</tr>
						<?php

						//print_r( $array_fechas );

						/************************* MOSTRAR LAS VENTAS CON TARJETA DE CREDITO Y DEBITO ********************************/

						while( $r_facturas = db_fetch_array( $qry_facturas ) )
						{

							$class = repetition()?"row2":"row1";

							$cliente = array( );
							$coutas = array( );
							$candeladas = 0;
							$mostrar = 0;
							$fechaproximo = "";
							$pendientes = 0;
							$cartera_castigada = 0;
							$mostrar_cartera = 0;
							$valor_cartera = 0;
							$TotalCuotaPagada=0;
							$FechasAbono="";

							//SELECT CLIENTE
							$sql_cliente = "SELECT IDCliente, Cedula, Nombre, Apellido FROM Cliente WHERE IDCliente = '$r_facturas[IDCliente]'";
							$qry_cliente = db_query( $sql_cliente );
							$cliente = db_fetch_array( $qry_cliente );
							//SELECT CUOTAS
							$sql_cuotas = " SELECT * FROM CreditoCuota WHERE IDFactura = '$r_facturas[IDFactura]' AND IDPuntoVenta = '$r_facturas[IDPuntoVenta]' and FechaPago <= '".$FechaHasta."' ORDER BY FechaCuota ";
							$qry_cuotas = db_query( $sql_cuotas );
							while( $r_cuotas = db_fetch_array($qry_cuotas) )
							{
								$ValorCuotaPago = $r_cuotas[ "ValorTotal" ];
								$cuotas[ $r_cuotas[IDCuota] ] = $r_cuotas;
								if( $r_cuotas[ FechaPago ] <> "0000-00-00 00:00:00" )
								{
									$candeladas++;
									$TotalCuotaPagada += $r_cuotas[ ValorTotal ];
									$FechasAbono.="<br>".substr($r_cuotas[ FechaPago ],0,10);
								}//end if
								elseif( $mostrar == 0 )
								{
									$fechaproximo = $r_cuotas[ FechaCuota ];
									$mostrar = 1;
								}//end end else

								//Calcular Cartera
								if( !empty($r_cuotas[ Estado ])  )
								{
									$cartera_castigada++;

									$valor_cartera += $r_cuotas[ ValorTotal ];
									$mostrar_cartera = 1;
								}//end if

							}//end while

							$cuotas_pendientes=db_num_rows( $qry_cuotas ) - $candeladas;
							$alerta_cuota_vencida=0;
							if( date( "Y-m-d" ) >= $fechaproximo && $cuotas_pendientes > 0  ):
								$alerta_cuota_vencida=1;
							endif;

							$EsCastiga="";
							$EsPagado="";
							$EsVencida="";
							$EsAlDia="";

							if($mostrar_cartera == 1):
								$EsCastiga="S";
							elseif($cuotas_pendientes==0):
								$EsPagado="S";
							elseif($alerta_cuota_vencida==1):
								$EsVencida="S";
							else:
								$EsAlDia="S";
							endif;


							$mostrar_fila="S";
							if(!empty($_POST["Estado"])){
								switch ($_POST["Estado"]) {
									case 'AlDia':
										if($EsAlDia=="S")
											$mostrar_fila="S";
										else
											$mostrar_fila="N";
									break;
									case 'Vencida':
										if($EsVencida=="S")
											$mostrar_fila="S";
										else
											$mostrar_fila="N";
									break;
									case 'Castigada':
										if($EsCastiga=="S")
											$mostrar_fila="S";
										else
											$mostrar_fila="N";
									break;
									case 'Pagado':
										if($EsPagado=="S")
											$mostrar_fila="S";
										else
											$mostrar_fila="N";
									break;
									default:
										$mostrar_fila="S";
										break;
								}
							}

							if($mostrar_fila=="S"){

						?>
								<tr>
										<td class="<?=$class?>" align="center" nowrap><?=$r_facturas[FechaFacturaF]?></td>
										<td class="<?=$class?>" align="center" nowrap><?=$r_facturas[NumeroDocumento]?></td>
											<td class="<?=$class?>" align="center" nowrap><?=$r_facturas[NumeroPagare]?></td>
										<td class="<?=$class?>" align="center" nowrap><?=$cliente[Cedula] ?></td>
										<td class="<?=$class?>" align="center" ><?=$cliente[Nombre]." ".$cliente[Apellido] ?></td>
									<td class="<?=$class?>" align="center" nowrap><?=$array_puntos[$r_facturas[IDPuntoVenta]]?> </td>
									<td class="<?=$class?>" align="center" nowrap>
											<a target="_blank" href="?mod=Factura&action=edit&id=<?=$r_facturas[IDFactura]?>&idpunto=<?=$IDPuntoVenta?>">
													<?=$r_facturas[NumeroFactura]?>
											</a>
									</td>
									<td class="<?=$class?>" align="right" nowrap><?=number_format( $r_facturas[ValorTotalSinBono],2 ); ?></td>
									<td class="<?=$class?>" align="right" nowrap>
										<?php
										
										$TotalSaldo+=$r_facturas[ValorTotal]; echo number_format( $r_facturas[ValorTotal],2 );
										/*
										$ValorSaldo=(int)$r_facturas["ValorTotal"] - ((int)$ValorCuotaPago*((int)$candeladas));
										$TotalSaldo+=$ValorSaldo; echo number_format( $ValorSaldo,2 ); 
										*/
										?>
									</td>
									<td class="<?=$class?>" align="right" nowrap><?=number_format( $ValorCuotaPago,2 ); ?></td>
									<td class="<?=$class?>" align="center" nowrap>
										<?php
											echo $candeladas;
										?>
									</td>

									<td class="<?=$class?>" align="right" nowrap><?=number_format( $TotalCuotaPagada,2 ); ?></td>
									 <td class="<?=$class?>" align="right" nowrap><?php echo $FechasAbono; ?></td>
										<td class="<?=$class?>" align="center" nowrap>
										<?php
											//echo $pendientes = db_num_rows( $qry_cuotas ) - $candeladas;
											echo $pendientes = 5 - $candeladas;
										?>
									 </td>
									 
										<td class="<?=$class?>" align="center" nowrap>
									    <?php

											//$ValorSaldo=(int)$r_facturas["ValorTotal"] - ((int)$ValorCuotaPago*((int)$candeladas));											


											$faltante_cuotas=$ValorCuotaPago*$pendientes;  
											echo number_format($faltante_cuotas,'0',',','.') ;
											$SumaCartera +=$faltante_cuotas; 
											$tValorTotal += $faltante_cuotas;
											?></td>

										<td class="<?=$class?>" align="center" nowrap><?php
											$alerta_cuota_vencida=0;
											if( date( "Y-m-d" ) >= $fechaproximo && $pendientes > 0  ):
												//echo " <img src='images/iconalert.gif' border=0>    &nbsp;&nbsp;";
												//echo '<span style="color:#EE080C; font-weight:bold">Alerta</span> &nbsp;&nbsp;';
												$alerta_cuota_vencida=1;
											endif;
											echo substr($fechaproximo,0,10);

										?></td>


											<td class="<?=$class?>" align="center" nowrap><?=$r_facturas[FechaUltimaGestion] ?></td>

											<td class="<?=$class?>" align="center" nowrap><?=$r_facturas[FechaCartaNotificacion] ?></td>
											<td class="<?=$class?>" align="center" nowrap><?=$r_facturas[FechaReporteCredito] ?></td>


                                        <td class="<?=$class?>" align="center" nowrap ><?php
											if($mostrar_cartera == 1):
												echo '<span style="color:#06306F; font-weight:bold">C Castigada</span> &nbsp;&nbsp;';
												//$valor_total_cartera += $r_facturas[ ValorTotal ];
												$valor_total_cartera +=$faltante_cuotas;
											elseif($pendientes==0):
												echo "Pagado";
											elseif($alerta_cuota_vencida==1):
												echo '<span style="color:#EE080C; font-weight:bold">Vencida</span>';
												//$valor_total_cartera += $r_facturas[ ValorTotal ];
												$valor_total_cartera +=$faltante_cuotas;
											else:
												echo "Al dia";
											endif;

										?></td>

                                        <td class="<?=$class?>" align="center" nowrap><?php
											if($mostrar_cartera == 1):
												echo $cartera_castigada;
											endif;

										?></td>
                                        <td class="<?=$class?>" align="center" nowrap>$<?php echo number_format($valor_cartera,'0',',','.'); ?></td>
																				<td class="<?=$class?>" align="left"><?php echo $r_facturas[ComentarioCredito]; if($r_facturas[FechaUtimoComentario]!="0000-00-00") echo "<br>".$r_facturas[FechaUtimoComentario]; ?></td>

									</tr>
									<?php
									}
						}//end for

						/****************************** FIN DE MOSTRAR LAS VENTAS CON TARJETA DE CREDITO Y DEBITO ********************************/
						?>
                        <tr>
						  <td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
						  <td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
						  <td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
						  <td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
						  <td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
						  <td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
						  <td class="<?=$class?>" align="right" nowrap>&nbsp;</td>
						  <td class="<?=$class?>" align="center" nowrap><strong>TOTAL SALDO</strong></td>
						  <td class="<?=$class?>" align="center" nowrap><strong>$<?php echo number_format($TotalSaldo,'0',',','.'); ?></strong></td>
							<td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
							<td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
							<td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
							<td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
						  <td class="<?=$class?>" align="center" nowrap><strong>TOTAL CARTERA</strong></td>
							<td class="<?=$class?>" align="center" nowrap><strong>$<?php echo number_format($SumaCartera,'0',',','.'); ?></strong></td>
							<td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
							<td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
							<td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
							<td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
							<td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
							<td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
							<td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
							<td class="<?=$class?>" align="center" nowrap>&nbsp;</td>
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
