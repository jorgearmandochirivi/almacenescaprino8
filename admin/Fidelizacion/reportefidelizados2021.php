<?php
	ini_set('max_execution_time', 0);
	include("../config.inc.php");
	Encabezado();

	$year = 2021;

	//Total Facturas  por mes
	$sql_cliente_mes = "Select month(FechaFactura) as Mes, count(*) as Total  From Factura where Year(FechaFactura)='".$year."' and Estado NOT LIKE 'ANULADA' Group by month(FechaFactura)";
	$result_cliente_mes = db_query($sql_cliente_mes);
	while($row_cliente_mes = db_fetch_array($result_cliente_mes)):
		$mes = (int)$row_cliente_mes["Mes"];
		$array_cliente_mes[$mes]  =  $row_cliente_mes["Total"];
	endwhile;

	//Total Clientes  por mes
	/*
	$sql_cliente_mes_grupo = "Select month(FechaFactura) as Mes, count(*) as Total  From Factura where Year(FechaFactura)='".$year."' and IDCliente in ()  and Est NOT LIKE 'ANULADA' Group by month(FechaFactura), IDCliente";
	$result_cliente_mes_grupo = db_query($sql_cliente_mes_grupo);
	while($row_cliente_mes_grupo = db_fetch_array($result_cliente_mes_grupo)):
		$mes_grupo = (int)$row_cliente_mes_grupo["Mes"];
		$array_cliente_mes_grupo[$mes_grupo]  =  $row_cliente_mes_grupo["Total"];
	endwhile;
	*/


	//Total Compras Clientes  por mes
	$sql_compra_mes_cli = "SELECT month(FechaFactura) as Mes, SUM( ValorTotal ) ValorCompra
					FROM Factura
					WHERE Year(FechaFactura)='".$year."'
					Group by month(FechaFactura)";
	$result_compra_mes_cli = db_query($sql_compra_mes_cli);
	while($row_compra_mes_cli = db_fetch_array($result_compra_mes_cli)):
		$mes = (int)$row_compra_mes_cli["Mes"];
		$array_compra_mes_cli[$mes]  =  $row_compra_mes_cli["ValorCompra"];
	endwhile;



	//Toal Clientes nuevos por mes
	$sql_nuevo_mes = "Select month(FechaTrCr) as Mes, count(*) as Total  From Cliente where Year(FechaTrCr)='".$year."' Group by month(FechaTrCr)";
	$result_nuevo_mes = db_query($sql_nuevo_mes);
	while($row_nuevo_mes = db_fetch_array($result_nuevo_mes)):
		$mes = (int)$row_nuevo_mes["Mes"];
		$array_total_mes[$mes]  =  $row_nuevo_mes["Total"];
	endwhile;


	$sql_id_nuevo_mes = "Select IDCliente  From Cliente where Year(FechaTrCr)='".$year."' Group by IDCliente";
	$result_id_nuevo_mes = db_query($sql_id_nuevo_mes);
	while($row_id_nuevo_mes = db_fetch_array($result_id_nuevo_mes)):
		$array_id_nuevo[]  =  $row_id_nuevo_mes["IDCliente"];
	endwhile;

	if(count($array_id_nuevo)>0):
		$id_nuevos = implode(",",$array_id_nuevo);
	endif;

	//Total Compras Clientes  por mes
	$sql_compra_mes = "SELECT month(FechaFactura) as Mes, SUM( ValorTotal ) ValorCompra
					FROM Factura
					WHERE Year(FechaFactura)='".$year."' and IDCliente in (".$id_nuevos.")
					Group by month(FechaFactura)";
	$result_compra_mes = db_query($sql_compra_mes);
	while($row_compra_mes = db_fetch_array($result_compra_mes)):
		$mes = (int)$row_compra_mes["Mes"];
		$array_compra_mes[$mes]  =  $row_compra_mes["ValorCompra"];
	endwhile;

	//TotalFidelizados
	//$sql_fidelizado = "Select month(FechaTrCr) as Mes, count(*) Total From Cliente Where year(FechaTrCr) = '".$year."' and ClubSuavidad = 'S' Group by month(FechaTrCr)";
	$sql_fidelizado =" SELECT month(FechaTrCr) as Mes, count(IDCliente) as Total
					  FROM  Cliente
					  WHERE YEAR( FechaTrCr ) ='".$year."'
					  AND ClubSuavidad = 'S'
					  Group by month(FechaTrCr)

					  ";
	$result_fidelizado = db_query($sql_fidelizado);
	while($row_fidelizado = db_fetch_array($result_fidelizado)):
		$mes = (int)$row_fidelizado["Mes"];
		$array_fidelizado[$mes]  =  $row_fidelizado["Total"] + $total_dic;
	endwhile;

	//Total Compras Clientes  fidelizados
	$sql_fidelizado ="SELECT IDCliente
					  FROM  Cliente
					  WHERE YEAR( FechaTrCr ) ='".$year."'
					  and ClubSuavidad = 'S'
					  ";
	$result_fidelizado = db_query($sql_fidelizado);
	while($row_fidelizado = db_fetch_array($result_fidelizado)):
		$array_id_fidelizado[]  =  $row_fidelizado["IDCliente"];
	endwhile;
	if(count($array_id_fidelizado)>0):
		$id_fidelizados = implode(",",$array_id_fidelizado);
	endif;

$sql_compra_mes_fid = "SELECT month(FechaFactura) as Mes, SUM( ValorTotal ) ValorCompra
					FROM Factura
					WHERE Year(FechaFactura)='".$year."' and IDCliente in (".$id_fidelizados.")
					Group by month(FechaFactura)";
	$result_compra_mes_fid = db_query($sql_compra_mes_fid);

	while($row_compra_mes_fid = db_fetch_array($result_compra_mes_fid)):
		$mes = (int)$row_compra_mes_fid["Mes"];
		$array_compra_mes_fid[$mes]  =  $row_compra_mes_fid["ValorCompra"];
	endwhile;

	$sql_factura_mes_fid = "SELECT month(FechaFactura) as Mes, COUNT( IDFactura ) TotalFactura
					FROM Factura
					WHERE Year(FechaFactura)='".$year."' and IDCliente in (".$id_fidelizados.")
					Group by month(FechaFactura)";

	$result_factura_mes_fid = db_query($sql_factura_mes_fid);

	while($row_factura_mes_fid = db_fetch_array($result_factura_mes_fid)):
		$mes = (int)$row_factura_mes_fid["Mes"];
		$array_factura_mes_fid[$mes]  =  $row_factura_mes_fid["TotalFactura"];
	endwhile;


	//FIDELIZADOS NUEVOS:
	$sql_fidelizado_new ="
					  SELECT month(FechaTrCr) as Mes, IDCliente
					  FROM  Cliente
					  WHERE YEAR( FechaTrCr ) ='".$year."'
					  AND ClubSuavidad = 'S'
					 ";
	$result_fidelizado_new = db_query($sql_fidelizado_new);
	while($row_fidelizado_new = db_fetch_array($result_fidelizado_new)):
		//averiguo si compro algo antes de este mes
		$sql_compra_anterior = "Select IDFactura from Factura Where IDCliente = '".$row_fidelizado_new["IDCliente"]."' and FechaFactura < '".$year."-".$row_fidelizado_new["Mes"]."-01"."'";
		$result_compra_anterior = db_query($sql_compra_anterior);
		if (db_num_rows($result_compra_anterior)<=0):
			//es nueva compra
			$array_new_fid[$row_fidelizado_new["Mes"]]++;
			$array_id_new_fid[]=$row_fidelizado_new["IDCliente"];
		endif;
	endwhile;



	if(count($array_id_new_fid)>0):
		$id_fidelizados_new = implode(",",$array_id_new_fid);
	endif;

	$sql_compra_mes_fid = "SELECT month(FechaFactura) as Mes, SUM( ValorTotal ) ValorCompra
					FROM Factura
					WHERE Year(FechaFactura)='".$year."' and IDCliente in (".$id_fidelizados_new.")
					Group by month(FechaFactura)";
	$result_compra_mes_fid = db_query($sql_compra_mes_fid);
	while($row_compra_mes_fid = db_fetch_array($result_compra_mes_fid)):
		$mes = (int)$row_compra_mes_fid["Mes"];
		$array_compra_mes_fid_new[$mes]  =  $row_compra_mes_fid["ValorCompra"];
	endwhile;



?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Reporte Fidelizados</title>
</head>

<body>

<table border="1" cellpadding="2" cellspacing="3">
	<tr>
    	<td>
        	<b>Descripcion</b>
        </td>
        <?php for($i=1;$i<=12;$i++): ?>
    	<td><b>Mes <?php echo $i ?></b></td>
        <?php endfor; ?>
   	</tr>
    <tr>
	  <td>Total Facturas</td>
	   <?php for($i=1;$i<=12;$i++): ?>
    	<td><?php echo $array_cliente_mes[$i]; ?></td>
        <?php endfor; ?>
  </tr>
      <tr>
	  <td>Total Clientes</td>
	   <?php for($i=1;$i<=12;$i++): ?>
    	<td>
				<?php
                $sql_cliente_mes = "Select DISTINCT(IDCliente) From Factura Where month(FechaFactura) = '".$i."' and Year(FechaFactura)='".$year."' and Estado NOT LIKE 'ANULADA' Group by IDCliente";
                $result_cliente_mes = db_query($sql_cliente_mes);
                $row_cliente_mes = db_fetch_array($result_cliente_mes);
                $gran_total_clientes += db_num_rows($result_cliente_mes);
                echo db_num_rows($result_cliente_mes);

				?>
        </td>
        <?php endfor; ?>
  </tr>

	<tr>
	  <td>Compras clientes</td>
	   <?php for($i=1;$i<=12;$i++): ?>
    	<td align="right">$<?php echo number_format((int)$array_compra_mes_cli[$i],'0',',','.'); ?></td>
        <?php endfor; ?>
  </tr>
	<tr>
	  <td>Total Clientes (nuevos)</td>
	   <?php for($i=1;$i<=12;$i++): ?>
    	<td><?php echo $array_total_mes[$i]; ?></td>
        <?php endfor; ?>
  </tr>
	<tr>
	  <td>Compras clientes (nuevos)</td>
	   <?php for($i=1;$i<=12;$i++): ?>
    	<td align="right">$<?php echo number_format((int)$array_compra_mes[$i],'0',',','.'); ?></td>
        <?php endfor; ?>
  </tr>
	<tr>
	  <td>Total fidelizados</td>
	   <?php for($i=1;$i<=12;$i++): ?>
    	<td><?php echo $array_fidelizado[$i] ?></td>
        <?php endfor; ?>
  </tr>
	<tr>
	  <td>Compras fidelizados</td>
	   <?php for($i=1;$i<=12;$i++): ?>
    	<td>$<?php echo number_format((int)$array_compra_mes_fid[$i],'0',',','.'); ?></td>
        <?php endfor; ?>
  </tr>
	<tr>
	  <td>Total Facturas</td>
	  <?php for($i=1;$i<=12;$i++): ?>
    	<td><?php echo $array_factura_mes_fid[$i] ?></td>
        <?php endfor; ?>
  </tr>
	<tr>
	  <td>Nuevos fidelizados</td>
	  <?php for($i=1;$i<=12;$i++): ?>
    	<td><?php echo $array_new_fid[$i] ?></td>
        <?php endfor; ?>
  </tr>
	<tr>
	  <td>Compras nuevos fidelizados</td>
	   <?php for($i=1;$i<=12;$i++): ?>
   	  <td align="right">$<?php echo number_format((int)$array_compra_mes_fid_new[$i],'0',',','.'); ?></td>
        <?php endfor; ?>
  </tr>
</table>

<br>

<table border="1" cellpadding="2" cellspacing="3">
  <tr>
    <td><b>Descripcion</b></td>
    <td><b>A&ntilde;o <?php echo $year ?></b></td>
  </tr>
  <tr>
    <td>Total Facturas </td>
    <td>
	<?php for($i=1;$i<=12;$i++):
    	$sum_total_mes_todo += $array_cliente_mes[$i];
    endfor; ?>
	<?php echo $sum_total_mes_todo; ?></td>
  </tr>
  <tr>
    <td>Total Clientes</td>
    <td><?php echo $gran_total_clientes; ?></td>
  </tr>
  <tr>
    <td>Compras clientes</td>
    <?php for($i=1;$i<=12;$i++):
    	$total_mes += $array_compra_mes_cli[$i];
    endfor; ?>
    <td align="right">$<?php echo number_format((int)$total_mes,'0',',','.'); ?></td>
  </tr>
  <tr>
    <td>Total Clientes (nuevos)</td>
    <?php for($i=1;$i<=12;$i++):
			$sum_total_mes += $array_total_mes[$i];
		  endfor;
	?>
    <td><?php echo$sum_total_mes; ?></td>
  </tr>
  <tr>
    <td>Compras clientes (nuevos)</td>
    <?php for($i=1;$i<=12;$i++):
    	$total_mes_nuevo += $array_compra_mes[$i];
    endfor; ?>
    <td align="right">$<?php echo number_format((int)$total_mes_nuevo,'0',',','.'); ?></td>
  </tr>
  <tr>
    <td>Total fidelizados</td>
    <?php for($i=1;$i<=12;$i++):
			$total_fid+=$array_fidelizado[$i];
         endfor; ?>
    <td><?php echo $total_fid; ?></td>

  </tr>
  <tr>
    <td>Compras fidelizados</td>
    <?php for($i=1;$i<=12;$i++):
		$total_compra_fid += $array_compra_mes_fid[$i];
    endfor; ?>
    <td>$<?php echo number_format((int)$total_compra_fid,'0',',','.'); ?></td>

  </tr>
  <tr>
    <td>Facturas Fidelizados</td>
    <?php for($i=1;$i<=12;$i++):
    		$total_fac_fid += $array_factura_mes_fid[$i];
    	endfor; ?>
    <td><?php echo $total_fac_fid; ?></td>
  </tr>
  <tr>
    <td>Nuevos fidelizados</td>
    <?php for($i=1;$i<=12;$i++):
    		$total_new_fid += $array_new_fid[$i];
    	endfor; ?>
    <td><?php echo $total_new_fid; ?></td>

  </tr>
  <tr>
    <td>Compras nuevos fidelizados</td>
    <?php for($i=1;$i<=12;$i++):
				$total_compras_fid_new += $array_compra_mes_fid_new[$i];
   	endfor; ?>
    <td align="right">$<?php echo number_format((int)$total_compras_fid_new,'0',',','.'); ?></td>

  </tr>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>


</body>
</html>
