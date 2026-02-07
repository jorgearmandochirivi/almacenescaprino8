<?php
	ini_set('max_execution_time', 0);
	include("../config.inc.php");
	Encabezado();
	
	if(empty($_POST["Year"]))
		$year = date("Y");
	else
		$year = $_POST["Year"];
	
	
	
	if(empty($_POST["Mes"]))
		$month=date("m");
	else
		$month=$_POST["Mes"];	
	
		
?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Reporte Fidelizados</title>
</head>

<body>

Seleccione mes a consultar: 
<form name="frmfiltro" id="frmfiltro" enctype="multipart/form-data" method="post">
    <select name="Mes" id="Mes">
        <option value="">[Seleccion mes]</option>
        <option value="1">Ene</option>
        <option value="2">Feb</option>
        <option value="3">Mar</option>
        <option value="4">Abr</option>
        <option value="5">May</option>
        <option value="6">Jun</option>
        <option value="7">Jul</option>
        <option value="8">Ago</option>
        <option value="9">Sep</option>
        <option value="10">Oct</option>
        <option value="11">Nov</option>
        <option value="12">Dic</option>
    </select>
    
    
     <select name="Year" id="Year">
        <option value="">[Seleccion a&ntilde;o]</option>
        <?php for($year_actual=date("Y");$year_actual>=2017;$year_actual--):?>
	        <option value="<?php echo $year_actual; ?>"><?php echo $year_actual; ?></option>	
        <?php endfor; ?>
    </select>
    
    
    <input type="submit" name="consultar" value="consultar">
    
    
    
  </form>  
  
   
  
  
  
<br><br>
Reporte Mes : <?php echo $month; ?> - <?php echo $year; ?>
<br><br>
	 
    
<table border="1" cellpadding="2" cellspacing="3">
	<tr>
    	<td><b>Tienda</b></td>
    	<td><b>Vendedor</b></td>
        
    	<td><b>Clientes atendidos <?php echo $i ?></b></td>
    	<td><b>Fidelizados</b></td>
        
   	</tr>
    
    <?php
	 
    $sql_tiendas = "Select * From PuntoVenta Where Publicar = 'S'";
	$result_tiendas = db_query($sql_tiendas);
	while($row_tiendas = db_fetch_array($result_tiendas)):
		$total_atendido_tienda = 0;
		$total_fideliza_tienda = 0;
	?>
    
   <?php    
   	$sql_factura = "Select count(IDEmpleado) as TotalVentas, IDEmpleado From Factura Where month(FechaFactura) = ".$month." and year(FechaFactura) = '".$year."' and IDPuntoVenta = '".$row_tiendas["IDPuntoVenta"]."' and (Estado = 'ACTIVA' or Estado = '') and IDEmpleado >0 Group by IDEmpleado";
	$result_fatura = db_query($sql_factura);
	while ($row_empleado = db_fetch_array($result_fatura)):
   ?>
    <tr>
	  <td><?php echo $row_tiendas["Nombre"]; ?></td>
	  <td><?php echo utf8_encode(get_field("Empleado","Nombre","IDEmpleado",$row_empleado["IDEmpleado"]) . " " . get_field("Empleado","Apellidos","IDEmpleado",$row_empleado["IDEmpleado"])); ?></td>
	   
    	<td><?php 
		
		$total_atendido_tienda += (int)$row_empleado["TotalVentas"];
		echo $row_empleado["TotalVentas"]; 		
		?></td>
    	<td>
        <?php
		
		/*
		if($row_empleado["IDEmpleado"]==270):		
				$totalfidelizados=0;
				//Validando si compro el cliente este mes
				echo $sql_fid = "Select * From Cliente Where month(FechaTrCr) = ".$month." and year(FechaTrCr) = '".$year."' and IDPuntoVentaFideliza = '".$row_tiendas["IDPuntoVenta"]."' and IDUsuarioFideliza = '".$row_empleado["IDEmpleado"]."'";
				$result_fid = db_query($sql_fid);
				while($row_fid = db_fetch_array($result_fid)):
					//verifico si teien compra este mes para calcularlo como fidelizado efectivo
					$sql_factura = "Select IDFactura From Factura Where month(FechaFactura)  = ".$month." and year(FechaFactura) = '".$year."' and IDPuntoVenta = '".$row_tiendas["IDPuntoVenta"]."' and IDEmpleado = '".$row_empleado["IDEmpleado"]."' Limit 1";
					$result_factura = db_query($sql_factura);
					if(db_num_rows($result_factura)>0):
						$totalfidelizados++; 
					else:
						echo "<br>Fidelizado sin compra " . 	$sql_factura;
						exit;
					endif;
				endwhile;
				echo $totalfidelizados;
				exit;
		*/		
			//Consulto cuantos fidelizo en el mes en este almacen
			$sql_fid = "Select count(IDUsuarioFideliza) as TotalFideliza, IDUsuarioFideliza From Cliente Where month(FechaTrCr) = ".$month." and year(FechaTrCr) = '".$year."' and IDPuntoVentaFideliza = '".$row_tiendas["IDPuntoVenta"]."' and IDUsuarioFideliza = '".$row_empleado["IDEmpleado"]."' Group by IDUsuarioFideliza";
			$result_fid = db_query($sql_fid);
			$row_fid = db_fetch_array($result_fid);
			$total_fideliza_tienda += (int)$row_fid["TotalFideliza"];
			echo $row_fid["TotalFideliza"];
			
		
		?>
        </td>
  	</tr>  
    <?php endwhile; ?>
  <?php endwhile; ?>
    
  
     
</table>
<p>&nbsp;</p>



</body>
</html>
