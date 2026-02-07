<?php
	include("../config.inc.php");
	Encabezado();
	
// selecciono las facturas que contienen la promocion	
	$sql_facturas_promocion=db_query("Select IDFactura from DetalleFactura where DescuentoPar = 100");
	while($result_facturas=db_fetch_array($sql_facturas_promocion)){
		$array_facturas[]=$result_facturas[IDFactura];	
	}
	
	$id_facturas=implode(",",$array_facturas);
	if(empty($id_facturas))
		$id_facturas=0;	
	
	//$sql_clientes = " SELECT * FROM Cliente, FidClienteRespuesta WHERE Cliente.IDCliente = FidClienteRespuesta.IDCliente GROUP BY FidClienteRespuesta.IDCliente ";
        $sql_clientes = " 
       
     SELECT * FROM Factura where IDFactura in ($id_facturas) or ObservacionDescuento = 'pague 2 lleva 3' ORDER BY FechaFactura DESC 
       
";
	GLOBAL $campo;
$now_date = date('m-d-Y H:i');
$result = db_query($sql_clientes);
$title = "Datos Reporte Facturas Pague 2 Lleve 3 Fecha $now_date";
$file_type = "vnd.ms-excel";
$file_ending = "xls";
header("Pragma: ");
header("Cache-Control: ");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Content-Type: application/$file_type; charset=ISO-8859-1");
header("Content-Disposition: attachment; filename=$title.$file_ending"); 
echo("$title\n");
//define separator (defines columns in excel & tabs in word)
$sep = "\t"; //tabbed character
$ponerdetalle = "";
print("\n");
//end of printing column names
//Poner los nombres de las columnas
	
	echo "Cliente" . $sep;
	echo "Punto Venta" . $sep;
	echo "Numero Factura" . $sep;
	echo "FechaFactura" . $sep;
    echo "Valor Total" . $sep;
	print("\n");	
	//start while loop to get data
    while($r = db_fetch_object($result))
    {
		echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente)." ".get_field("Cliente","Apellido","IDCliente",$r->IDCliente) . $sep;
		echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta) . $sep;
		echo $r->NumeroFactura . $sep;
		echo $r->FechaFactura . $sep;
        echo $r->ValorTotal . $sep;
        print "\n";
    }
