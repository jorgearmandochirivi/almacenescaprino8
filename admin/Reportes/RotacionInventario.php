<body> <?php



$TitleMod ="Codificacion Especifica";



$Table = "CodificacionEspecifica";

$TableJoin = "Referencia";

$Key = "IDCodificacionEspecifica";

$Title = " Consultar Inventario ";

$MOD = "ConsolidadoVentas";

$m="Referencia";

		$permisos = get_permiso($ID_Usuario,$m,$Table);

if($permisos[0] >= 2)

{

		switch (nvl($action)) {

			case "list" :	

				if(!empty($_POST['campo']) && !empty($_POST['referencia']))
					list_r($_POST['campo'],$_POST['referencia']);
				elseif(!empty($_GET['campo']) && !empty($_GET['referencia']))	{
					list_r($_GET['campo'],$_GET['referencia']);				
					
				}

			break;

			default : 

					

				seleccionareferencia("list");

				//list_r();

			break;

		

		} // End switch



}//end if(permisos[0] > 2)

else

	echo Mensaje_Info("No tiene Permisos Suficientes","row2");



/*******************************************************************************************

	seleccionareferencia: formulario de busqueda para la referencia

	Parametros:

			$newmode : nieva accion a tomar con el submit

	Retorna:	

			Void

*******************************************************************************************/

function seleccionareferencia( $newmode)

{

	GLOBAL $Title;

?>	

	<br><br><br><br>

	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="820">

		

		<tr>

			<td class="maintitle"><b></b>

				<span class="gen">

					<?php echo $Title?>

				</span>

			</td>

		</tr>

	</table>

	<table cellspacing='0' cellpadding='2' border='0' align='center' class="bordertable" width="820">

		<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">

			<tr>

			<td class=col1 width=30;?> 

				Buscar Referencia Por

			</td>

				<td class="col2" nowrap>

				&nbsp;&nbsp;&nbsp;	

				<select name="campo" class="input">

					<option value="Numero">Numero</option>

					<option value="Nombre">Nombre</option>

				</select>

				&nbsp;&nbsp;&nbsp;

				<input type=text class=tbox name=referencia>

				<input type="submit" class="button" name="enviar" value="Consultar">



				<input type=hidden name=action value=<?php echo $newmode?>>

				

			</td>

				<td align="left" valign="middle" class="nav" nowrap>Desde <input type="text" name="FechaDesde" class="input" value="<?php echo date( "Y-m-d" 
)?>" size="10"> 

					<script language="JavaScript1.2">

									<!--

										if (!document.layers)

											document.write("<img src=jscripts/imagescalendar/cal.gif 
onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							

									//-->

								</script>

				</td>

				<td align="left" valign="middle" class="nav" nowrap>Hasta <input type="text" name="FechaHasta" class="input" value="<?php echo date( "Y-m-d" 
)?>" size="10"> 

					<script language="JavaScript1.2">

									<!--

										if (!document.layers)

											document.write("<img src=jscripts/imagescalendar/cal.gif 
onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							

									//-->

								</script>

				</td>

			</tr>
			<tr>
				<td class=col1 width=30;?>Tipo de Referencia</td>
				<td class="col2">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td>&nbsp;&nbsp;&nbsp;<select name="IDTipoReferencia">
									<option value="">Seleccione Un Tipo de Referencia</option><?php 								
								$qry_tiporef = db_query("SELECT * FROM TipoReferencia ORDER BY Descripcion");
								while($tiporef = db_fetch_object($qry_tiporef)){
									 echo "<option value=$tiporef->IDTipoReferencia ";if($IDTipoReferencia == $tiporef->IDTipoReferencia ) echo "selected"; echo ">&nbsp;&nbsp;$tiporef->Descripcion</option>";
								}
							?>
								</select></td>
						</tr>
					</table>
				</td>
				<td align="left" valign="middle" class="nav" nowrap>Ciudad</td>
				<td align="left" valign="middle" class="nav" nowrap><?php echo formpopup("Ciudad","Descripcion","Descripcion","IDCiudad",$r->IDCiudad,"input\" id=\"Ciudad"," IDCiudad = 1 or IDCiudad = 2 "); ?></td>
			</tr>
		</form>

	</table>

<?php

}//end function seleccionapuntoventa($idreferencia)





/*******************************************************************************************

		funcion Listar

*******************************************************************************************/

	function list_r($campo, $referencia){

		Global $TitleMod,$MOD,$Table,$Key,$listar,$idReferencia,$IDPuntoVenta,$Title,$IDPuntoVentaR, $FechaDesde, $FechaHasta,  $IDTipoReferencia, $IDCiudad;

	 	



?>

	<br>

	Los datos se muestran de la forma :<br>

	Ventas<br>

	Inventario<br>

	Ventas / ( Ventas + Inventario )<br>

	<br>

	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="820">

		<tr>

			<td class="maintitle"><b></b><span class="gen"><?php echo $Title?> - <?php echo $campo.":".$referencia;  ?> Los Almacenes que no apareces, no tienen venta 
para esta referencia</span></td>

		</tr>

	</table>

	<table width=820 cellpadding=0 cellspacing=0 align=center class=bordertable>

	<tr>

		<td class="titlemedium" >

				<table cellspacing='0' cellpadding='2' border='0' align='center'  width="820">

					<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">

						<tr>

							<td class=col1 width=30;?> 

				Buscar Referencia Por

				</td>

							<td class="col2" nowrap>

				&nbsp;&nbsp;&nbsp;	<select name="campo" class="input">

									<option value="Numero">Numero</option>

									<option value="Nombre">Nombre</option>

								</select>

				&nbsp;&nbsp;&nbsp;

		<input type=text class=tbox name=referencia value="<?php echo $referencia ?>">

		<input type="submit" class="button" name="enviar" value="Consultar">

		<input type=hidden name=action value='<?php echo "list"?>'></td>

							<td align="left" valign="middle" class="nav" nowrap>Desde <input type="text" name="FechaDesde" id="FechaDesde" class="input" 
value="<?php echo $FechaDesde?>" size="10"> 

								<script language="JavaScript1.2">

									<!--

										if (!document.layers)

											document.write("<img src=jscripts/imagescalendar/cal.gif 
onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							

									//-->

								</script>

							</td>

							<td align="left" valign="middle" class="nav" nowrap>Hasta <input type="text" name="FechaHasta" id="FechaHasta" class="input" 
value="<?php echo $FechaHasta?>" size="10"> 

								<script language="JavaScript1.2">

									<!--

										if (!document.layers)

											document.write("<img src=jscripts/imagescalendar/cal.gif 
onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							

									//-->

								</script>

							</td>

						</tr>
						<tr>
							<td class=col1 width=30;?>Tipo de Referencia</td>
							<td class="col2">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td>&nbsp;&nbsp;&nbsp;<select name="IDTipoReferencia">
												<option value="">Seleccione Un Tipo de Referencia</option><?php 								
								$qry_tiporef = db_query("SELECT * FROM TipoReferencia ORDER BY Descripcion");
								while($tiporef = db_fetch_object($qry_tiporef)){
									 echo "<option value=$tiporef->IDTipoReferencia ";if($IDTipoReferencia == $tiporef->IDTipoReferencia ) echo "selected"; echo ">&nbsp;&nbsp;$tiporef->Descripcion</option>";
								}
							?>
											</select></td>
									</tr>
								</table>
							</td>
							<td align="left" valign="middle" class="nav" nowrap>Ciudad</td>
							<td align="left" valign="middle" class="nav" nowrap><?php echo formpopup("Ciudad","Descripcion","Descripcion","IDCiudad",$_POST["IDCiudad"],"input\" id=\"Ciudad"); ?></td>
						</tr>
					</form>

				</table>

			</td>

	</tr>

<?php

if( !empty( $referencia ) )	

{

	if( !empty( $IDCiudad ) )
		$condi_ciu = " and IDCiudad = '" . $IDCiudad . "' ";
	

	$sql_puntos = " SELECT IDPuntoVenta, Nombre FROM PuntoVenta Where Publicar = 'S' " . $condi_ciu . " ORDER BY IDCiudad, Nombre ";

	$qry_puntos = db_query( $sql_puntos );

		while( $r_puntos = db_fetch_array( $qry_puntos ) )

			$array_puntos[$r_puntos["IDPuntoVenta"]] = $r_puntos["Nombre"];

 	

 	//Tallas

	$sql_tallas = " SELECT IDTalla, Descripcion FROM Talla ORDER BY Descripcion ";

	$qry_tallas = db_query( $sql_tallas );
	
	if( !empty( $IDTipoReferencia ) )
			$condicion = " AND R.IDTipoReferencia = '".$IDTipoReferencia."%' ";

		while( $r_tallas = db_fetch_array( $qry_tallas ) )
			$array_tallas[$r_tallas["IDTalla"]] = $r_tallas["Descripcion"];

	 	

	 	 $sql = " SELECT DF.Cantidad, DF.IDPuntoVenta, R.Numero, CE.IDTalla FROM $Table CE, Referencia R, PuntoVentaReferencia PR, Factura F, DetalleFactura DF

	 				WHERE R.Numero LIKE '%$referencia%'  $condicion

	 				AND R.IDReferencia = PR.IDReferencia

	 				AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia

	 				AND CE.IDCodificacionEspecifica = DF.IDCodificacionEspecifica

	 				AND DF.IDFactura = F.IDFactura
					AND DF.IDPuntoVenta = F.IDPuntoVenta

	 				AND F.FechaFactura >= '$FechaDesde' AND F.FechaFactura <= '$FechaHasta' ORDER BY CE.IDTalla";

	 	

	 

		$query_codificacion = db_query($sql);

		$rows = db_num_rows($query_codificacion);



		if($rows > 0){

		?>

			<tr>

				<td class="row1">

					<?php 

						$i = 0;

						$r = array( );

						while($r_codificacionesp = db_fetch_array($query_codificacion))

						{

								$array_ventas[ $r_codificacionesp["IDPuntoVenta"] ][ $r_codificacionesp["IDTalla"] ] += 
$r_codificacionesp["Cantidad"];

						} //end while($r[$i] = db_fetch_array($query_codificacion))

						//print_r($r);

						

						//INVENTARIO

						$sql_inv =  "SELECT CE.*, PR.IDPuntoVenta FROM $Table CE, Referencia R, PuntoVentaReferencia PR WHERE R.$campo LIKE 
'%$referencia%' $condicion AND R.IDReferencia = PR.IDReferencia ";

					 	$sql_inv .= "AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia ";

					 

						$query_codificacion_inv = db_query($sql_inv);

						$rows_inv = db_num_rows($query_codificacion_inv);

						

						$i = 0;

						$r = array( );

						while($r_codificacionesp = db_fetch_array($query_codificacion_inv))

						{

								$array_existencias[ $r_codificacionesp["IDPuntoVenta"] ][ $r_codificacionesp["IDTalla"] ] += 
$r_codificacionesp["Existencias"];

								$array_tallas_mostrar[ $r_codificacionesp["IDTalla"] ] = $array_tallas[ $r_codificacionesp["IDTalla"] ];

						}

						ksort( $array_tallas_mostrar );

					?>

					

					<table width="100%" border="0" cellspacing="1" cellpadding="0">

						<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">

							<tr>

								

								<td class="rowform" width="20%">PUNTOS DE VENTA</td>	

								<?php

								$width=60 / count( $array_tallas_mostrar ); 

								foreach($array_tallas_mostrar as $idtalla => $nombre)

								{

									//if( array_sum($array_existencias[ $idpunto ]) > 0 )

										echo "<td class=rowform width='".$width."%' align=center>".$nombre."</td>";

								}//end for

								?>	

								<td class="rowform" width="20%" align="right" >TOTALES</td>

							</tr>

							

							<?php

							foreach($array_puntos as $idpunto => $nombre)

							{

								$class = repetition()?"row1":"row2";

							?>

							<tr>

								<td class=rowform align=center><?php echo $nombre; ?></td>

								<?php

								foreach($array_tallas_mostrar as $idtalla => $nombre)

								{

									

									echo "<td class=".$class." align=right>";

										$ventas_talla = $array_ventas[$idpunto][$idtalla] ?? 0;
										$inventario_talla = $array_existencias[$idpunto][$idtalla] ?? 0;
										echo $ventas_talla."</br>";

										echo $inventario_talla."</br>";

										$den_rot = $ventas_talla + $inventario_talla;
										$Rotacion = ($den_rot > 0) ? (($ventas_talla / $den_rot) * 100) : 0;
										echo number_format($Rotacion, 2)." % ";

									echo "</td>";

									

										$TotalVentasTalla[ $idtalla ] += $ventas_talla;

										$TotalExistenciasTalla[ $idtalla ] += $inventario_talla;

									

										$TotalVentasPunto[ $idpunto ] += $ventas_talla;

									$TotalExistenciasPunto[ $idpunto ] += $array_existencias[ $idpunto ][ $idtalla ];

									

									$totaltalla[ $idtalla ] += $Rotacion;

									$totalpunto[ $idpunto ] += $Rotacion;

									

								}//end for

								?>	

								<td class="rowform"  align="right" ><?php
								$total_ventas_punto = $TotalVentasPunto[$idpunto] ?? 0;
								$total_exist_punto = $TotalExistenciasPunto[$idpunto] ?? 0;
								echo $total_ventas_punto."<br>".$total_exist_punto."<br>";
								$den_rot_punto = $total_ventas_punto + $total_exist_punto;
								$Rotacion = ($den_rot_punto > 0) ? (($total_ventas_punto / $den_rot_punto) * 100) : 0;
								echo number_format($Rotacion, 2);
								?></td>

							</tr>

							<?php

							}//end for

							?>

							

							<tr>

								<td class="rowform">TOTALES</td>	

								<?php

								foreach($array_tallas_mostrar as $idtalla => $nombre)

								{

									//if( array_sum($array_existencias[ $idpunto ]) > 0 )

										$total_ventas_talla = $TotalVentasTalla[$idtalla] ?? 0;
										$total_exist_talla = $TotalExistenciasTalla[$idtalla] ?? 0;
										$den_rot_talla = $total_ventas_talla + $total_exist_talla;
										$Rotacion = ($den_rot_talla > 0) ? (($total_ventas_talla / $den_rot_talla) * 100) : 0;
										echo "<td class=rowform align=right>".$total_ventas_talla."<br>".$total_exist_talla."<br>".number_format($Rotacion, 2)."</td>";

								}//end for

								?>	

								<td class="rowform" align=right><?php
								$suma_ventas_talla = array_sum(is_array($TotalVentasTalla ?? null) ? $TotalVentasTalla : array());
								$suma_exist_talla = array_sum(is_array($TotalExistenciasTalla ?? null) ? $TotalExistenciasTalla : array());
								echo $suma_ventas_talla."<br>".$suma_exist_talla."<br>";
								$den_rot_total = $suma_ventas_talla + $suma_exist_talla;
								$Rotacion = ($den_rot_total > 0) ? (($suma_ventas_talla / $den_rot_total) * 100) : 0;
								echo number_format($Rotacion, 2);
								?></td>

							</tr>

							

						</form>

					</table>

				</td>

			</tr>

		<?php

		}// End if$rows

		else

			echo "<tr><td><span class=col1list><b>No se encontraron registros con los par&aacute;metros proporcionados </b></span></td></tr>";

}

?>

</table>	



<?php

}// Enf function list()				

?>
