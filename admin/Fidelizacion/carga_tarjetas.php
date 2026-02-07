<?php
	
	include("../config.inc.php");
	Encabezado();
	
	for($i=34001;$i<=50000;$i++):
		$nombre_tarjeta = "CS000".$i;
		$sql_tarjeta=db_query("insert into TarjetaFidelizacion (IDTarjetaFidelizacion, Codigo, Estado) 
					  Values (".$i.",'".$nombre_tarjeta."','A')");
	endfor;
	
	
?>
