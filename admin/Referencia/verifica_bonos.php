<?php

	error_reporting(1);
	require("../config.inc.php");

	$sql_bono=db_query("Select * from BonoFidelizacion Where IDFactura >0 Order By IDBonoFidelizacion DESC");
	while($row_bono=db_fetch_array($sql_bono)){
		//echo "<br>Bono  " .$row_bono[IDBonoFidelizacion];	
		$sql_factura=db_query("Select * From Factura Where IDFactura = '".$row_bono[IDFactura]."' and IDPuntoVenta = '".$row_bono[IDPuntoVenta]."' and ValorBono = '0' ");
		while($row_factura=db_fetch_array($sql_factura)){
			
			$sql_forma_pago=db_query("Select * from FormaPagoFactura where IDFactura = '".$row_bono[IDFactura]."' and IDFormaPago = '17'");
			if (db_num_rows($sql_forma_pago)<=0){
				echo "<br>Factura sin valor bono " . $row_factura[IDFactura] . " Punto " .	$row_factura[IDPuntoVenta] . " Bono " . $row_bono[IDBonoFidelizacion];					
				$total_facturas++;
			}
		}
	}
	echo "<br><br>TOTAL:" . $total_facturas;

?>