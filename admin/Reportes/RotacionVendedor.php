<body><?php

// actualizo las tablas temporales con las maestras para hacer mas eficiente lso reportes

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

								Desde	<input readonly type="text" name="FechaDesde" class="input" value="<?=fecha()?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//-->
								</script>

							</td>
							<td align="left" valign="middle" class="nav">

								Hasta	<input readonly type="text" name="FechaHasta" class="input" value="<?=fecha()?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//-->
								</script>
								<input type="hidden" name="mod" value="RotacionVendedor"><input type="hidden" name="action" value="view"></td>
							<td  align='left' valign='middle' class="nav">Cedula
                            <input type="number" name="NumeroDocumento" id="NumeroDocumento" placeholder="Numero Documento">
                             </td>
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
		if(!empty($_POST["IDPuntoVenta"])):
			$condicion = " and F.IDPuntoVenta = '".$_POST["IDPuntoVenta"]."'";
		endif;

		if(!empty($_POST["NumeroDocumento"])):
			$condicion = " and E.Cedula = '".$_POST["NumeroDocumento"]."' ";
		endif;

		if(!empty( $FechaDesde ) && !empty( $FechaHasta ) && !empty($condicion)){
		?>
		<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
				<table width="100%" border="0" align="center" cellspacing="1" cellpadding="0" bgcolor="#345487">
					<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return Evalua(document.frm)">
						<tr>
							<td class="maintitle" valign="middle">
								Reporte Rotacion Empleados <b>Desde :</b> <?=formatofecha($FechaDesde)?>  <b>Hasta :</b> <?=formatofecha($FechaHasta)?>
							</td>
						</tr>

                	<tr style="display:none">
                      <td><?php echo  $sql_ventasadmin;?></td>
					</tr>
                    	<tr>
							<td class="mainbg">
								<table width="100%" border="0" cellspacing="1" cellpadding="1">
									<tr>
										<td class="titlemedium" align="center" nowrap>Punto Venta</td>
                                        <td class="titlemedium" align="center" nowrap>Cedula</td>
										<td class="titlemedium" align="center" nowrap>Nombre</td>
										<td class="titlemedium" align="center" nowrap>Fecha</td>
										<td class="titlemedium" align="center" nowrap>Ventas</td>
									</tr>
				<?php





						 $sql_empleados = "SELECT E.IDEmpleado, SUM(ValorTotal) TotalVenta, CONCAT(E.Nombre,' ',E.Apellidos) as Nombre, E.Cedula,F.FechaFactura,F.IDPuntoVenta
										  FROM Empleado E, Factura F
										  Where E.IDEmpleado = F.IDEmpleado
										  ".$condicion."
										  AND DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) >= DATE_FORMAT( '$FechaDesde', '%Y-%m-%d' )
										  AND DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) <= DATE_FORMAT( '$FechaHasta', '%Y-%m-%d' )
										  Group by DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ), IDEmpleado
										  Order by IDEmpleado,DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' )
										  ";
						$qry_empleado = db_query( $sql_empleados);


						$i = 0;
						while( $r_empleado = db_fetch_array( $qry_empleado ) )
						{ ?>


								<tr>
										<td class="<?=$class?>" align="center" nowrap><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_empleado['IDPuntoVenta']);?></td>
                                        <td class="<?=$class?>" align="center" nowrap><?=$r_empleado['Cedula']?></td>
										<td class="<?=$class?>" align="center" nowrap><?=$r_empleado['Nombre']?></td>
										<td class="<?=$class?>" align="center" nowrap><?=substr($r_empleado['FechaFactura'],0,10)?></td>
                                        <td class="<?=$class?>" align="center" nowrap><?=number_format($r_empleado['TotalVenta'],0,'','.'); ?></td>


									</tr>
				<?php
						}//end foreach( $array_ventasadmin[$valor['IDEmpleado']] as $llave => $venta  )
				?>

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

		$array_mes=array(31,28,31,30,31,30,31,31,30,31,30,31);
		$mes_inicio=1;
		$mes_final=12;
		$sql_pto_vta = "Select * From PuntoVenta Where Publicar = 'S' Order by IDCiudad,Nombre";
		$result_pto_vta = db_query($sql_pto_vta);
		while( $r_pto_vta = db_fetch_array( $result_pto_vta ) ) {
				for($mes_actual=$mes_inicio;$mes_actual<=$mes_final;$mes_actual++):
					unset($array_empleado);
					$fin_mes=$array_mes[($mes_actual-1)];
					$FechaDesde = "2019-".$mes_actual."-01";
					$FechaHasta = "2019-".$mes_actual."-".$fin_mes;
					$sql_empleados = "SELECT E.IDEmpleado, SUM(ValorTotal) TotalVenta, CONCAT(E.Nombre,' ',E.Apellidos) as Nombre, E.Cedula,F.FechaFactura,F.IDPuntoVenta
								  FROM Empleado E, Factura F
								  Where E.IDEmpleado = F.IDEmpleado
								  AND DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) >= DATE_FORMAT( '$FechaDesde', '%Y-%m-%d' )
								  AND DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ) <= DATE_FORMAT( '$FechaHasta', '%Y-%m-%d' )
								  AND F.IDPuntoVenta = '".$r_pto_vta["IDPuntoVenta"]."'
								  Group by DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' ), IDEmpleado
								  Order by IDEmpleado,DATE_FORMAT( F.FechaFactura,'%Y-%m-%d' )
								  ";

					$qry_empleado = db_query( $sql_empleados);
					$cont=0;
					while( $r_empleado = db_fetch_array( $qry_empleado ) ){
						$dia=substr($r_empleado["FechaFactura"],8,2);
						$array_empleado[$r_empleado["Nombre"]][$dia] = $r_empleado;
						/*
						$array_empleado[$cont]["Nombre"] = $r_empleado["Nombre"];
						$array_empleado[$cont]["Dia"] = substr($r_empleado["FechaFactura"],8,2);
						$array_empleado[$cont]["Mes"] = substr($r_empleado["FechaFactura"],5,2);
						$array_empleado[$cont]["Venta"] = $r_empleado["TotalVenta"];
						*/
						$cont++;
					}
					$array_datos[$mes_actual][$r_pto_vta["IDPuntoVenta"]]=$array_empleado;
				endfor;
		}
		?>


    	<?php
    	$sql_pto_vta = "Select * From PuntoVenta Where Publicar = 'S' Order by IDCiudad,Nombre";
		$result_pto_vta = db_query($sql_pto_vta);
		while( $r_pto_vta = db_fetch_array( $result_pto_vta ) ) {
				for($mes_actual=$mes_inicio;$mes_actual<=$mes_final;$mes_actual++): ?>

                <span style="font-size:14px; font-weight:bold"> Mes: <?php echo $mes_actual; ?> Punto de Venta <?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_pto_vta['IDPuntoVenta']);?></span>
                <table width="100%" border="0" cellspacing="1" cellpadding="1">
                                            <tr>
                                                <td class="titlemedium" align="center" nowrap>Vendedor</td>
                                                <?php for($i=1;$i<=31;$i++): ?>
                                                    <td class="titlemedium" align="center" nowrap><?php echo $i; ?></td>
                                                <?php endfor; ?>
                                                <td class="titlemedium" align="center" nowrap>Totales</td>
                                            </tr>
                        <?php

                                $i = 0;
                                foreach($array_datos[$mes_actual][$r_pto_vta["IDPuntoVenta"]] as $key_datos => $valor):
                                 ?>
                                    <tr>
                                        <td class="<?=$class?>" align="center" nowrap><?php echo $key_datos;?></td>
                                         <?php for($i=1;$i<=31;$i++):
										 	if(strlen($i)<=1)
												$i="0".$i;
										 ?>
                                                    <td class="<?=$class?>" align="center" nowrap>
														<?php
																$valor_item = $valor[$i]["TotalVenta"];
																if((int)$valor_item>0)
																	echo number_format($valor[$i]["TotalVenta"],0,'','.');
																$suma_total += $valor_item;

														?>
                                                    </td>
                                         <?php endfor; ?>
                                        <td class="<?=$class?>" align="center" nowrap><?=number_format($suma_total,0,'','.')?></td>
                                    </tr>
                        <?php
							$suma_total=0;
						endforeach; 	?>

                </table>
          <?php
			  	endfor;
		}; ?>





	<?php PHP
}// Enf function print()

?>
</body>
