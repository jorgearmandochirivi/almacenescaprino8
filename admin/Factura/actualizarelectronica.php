<?php
	include("../config.inc.php");
	Encabezado();
	$update_fac="UPDATE Factura
								SET FacturaElectronica = '".$_POST["Valor"]."'
								WHERE IDFactura='".$_POST["IDFactura"]."' and IDPuntoVenta = '".$_POST["IDPuntoVenta"]."' LIMIT 1";
	db_query($update_fac);
	?>
["ok"]
