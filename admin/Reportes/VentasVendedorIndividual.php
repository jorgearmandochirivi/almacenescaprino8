<body><?php

// actualizo las tablas temporales con las maestras para hacer mas eficiente lso reportes
//$sql_actualiza_factura=db_query("INSERT IGNORE INTO FacturaBck SELECT * FROM `Factura` WHERE `IDFactura` >= 461644");
//$sql_actualiza_venta_empleado=db_query("INSERT IGNORE INTO VentasEmpleadoBck SELECT * FROM `VentasEmpleado` WHERE IDFactura >= 723722");

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
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA,$Mes_array,$FechaDesde, $FechaHasta;

  if(strtotime($FechaDesde)<=strtotime("2017-01-31")):
 	$IVA = 0.16;
 endif;


?>

	<table width="100%">

		<tr>
		<td>
			<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
				<form action="./" name="frmPuntoVenta" method="post" >
						<tr>
							<td valign="middle"><img src="images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="nav">

								Mes
								<select name="mes" id="mes" class="input">
										<option value="1">Enero</option>
										<option value="2">Febrero</option>
										<option value="3">Marzo</option>
										<option value="4">Abril</option>
										<option value="5">Mayo</option>
										<option value="6">Junio</option>
										<option value="7">Julio</option>
										<option value="8">Agosto</option>
										<option value="9">Septiembre</option>
										<option value="10">Octubre</option>
										<option value="11">Noviembre</option>
										<option value="12">Diciembre</option>
								</select>

							</td>
							<td align="left" valign="middle" class="nav">

								A&ntilde;o
								<select name="year" id="year" class="input">
										<?php
										$year_actual=date("Y");
										$year_hasta=$year_actual-3;
										for ($desde=$year_actual;$desde>=$year_hasta;$desde--){ ?>
										<option value="<?php echo $desde; ?>"><?php echo $desde; ?></option>
									<?php } ?>
								</select>

								<input type="hidden" name="mod" value="VentasVendedorIndividual"><input type="hidden" name="action" value="view"></td>
							<td  align='left' valign='middle' class="nav">Puntos de Venta	<select name="IDPuntoVenta" onChange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><?php
								$qry_punto = db_query("SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
								</select> </td>
							<td align="left" valign="middle" class="nav"></td>
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
		if(!empty( $_POST["mes"] ) && !empty( $_POST["year"] ) ){

			$FechaDesde = $_POST["year"] . "-".$_POST["mes"]."-01";
			$FechaHasta = $_POST["year"] . "-".$_POST["mes"]."-31";


		?>
		<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
				<table width="100%" border="0" align="center" cellspacing="1" cellpadding="0" bgcolor="#345487">
					<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return Evalua(document.frm)">
						<tr>
							<td class="maintitle" valign="middle">
								Reporte Ventas Empleados <b>Desde :</b> <?=formatofecha($FechaDesde)?>  <b>Hasta :</b> <?=formatofecha($FechaHasta)?>
							</td>
						</tr>
						<?php


						$qry_puntosventa = db_query( "SELECT IDPuntoVenta, Nombre FROM PuntoVenta" );
						while( $r_puntodeventa = db_fetch_array( $qry_puntosventa ) )
						{
							$array_punto[$r_puntodeventa['IDPuntoVenta']] = $r_puntodeventa['Nombre'];
						}//end while punto venta
				?>
                	<tr style="display:none">
                      <td><?php echo  $sql_ventasadmin;?></td>
					</tr>
                    	<tr>
							<td class="mainbg">
								<table width="100%" border="0" cellspacing="1" cellpadding="1">
									<tr>
										<td class="titlemedium" align="center" nowrap>Cedula</td>
										<td class="titlemedium" align="center" nowrap>Nombre</td>
										<td class="titlemedium" align="center" nowrap>Tipo</td>
										<td class="titlemedium" align="center" nowrap>Punto Venta</td>
										<td class="titlemedium" align="center" nowrap>Valor Facturas</td>

									</tr>
				<?php

				 $sql_venta="SELECT E.Cedula,E.Nombre,E.Apellidos,E.IDCargo,SUM(ValorTotal) as ValorTotal
				FROM `Factura` F, Empleado E
				WHERE F.IDEmpleado=E.IDEmpleado and FechaFactura >= '".$FechaDesde."' and FechaFactura <=  '".$FechaHasta."'  and F.IDPuntoVenta = '".$IDPuntoVenta."'
				Group by F.IDEmpleado";
				$r_venta=db_query($sql_venta);
				while($row_venta=db_fetch_array($r_venta)){
					$class = repetition()?"row2":"row1";
					?>
					<tr>
						<td class="<?=$class?>" align="center" nowrap><?=$row_venta['Cedula']?></td>
						<td class="<?=$class?>" align="center" nowrap><?=$row_venta['Nombre']. " " . $row_venta['Apellidos']?></td>
						<td class="<?=$class?>" align="center" nowrap><?=get_field( "Cargo","Cargo","IDCargo",$row_venta['IDCargo'] )?></td>
						<td class="<?=$class?>" align="center" nowrap><?=$array_punto[ $_POST["IDPuntoVenta"] ];?></td>
						<td class="<?=$class?>" align="right" nowrap>
								<?php
								$suma_total+=$row_venta["ValorTotal"];
								echo number_format( $row_venta["ValorTotal"]);
								?>
						</td>

					</tr>

				<?php } ?>
				<tr>
					<td class="<?=$class?>" align="center" nowrap><?=$row_venta['Cedula']?></td>
					<td class="<?=$class?>" align="center" nowrap><?=$row_venta['Nombre']. " " . $row_venta['Apellidos']?></td>
					<td class="<?=$class?>" align="center" nowrap><?=$row_venta['Cargo']?></td>
					<td class="<?=$class?>" align="center" nowrap><b>TOTAL</b></td>
					<td class="<?=$class?>" align="right" nowrap><b><?php echo "$".number_format($suma_total,2);		?></b>
					</td>
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
}

?>
</body>
