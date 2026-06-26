<?php
include("../admin/config.inc.php");
include("../includes/FacturaElectronica.inc.php");

$Hoy=date("Y-m-d");
$sql_iva = "SELECT * FROM IVA LIMIT 1";
$query_iva = db_query( $sql_iva );
$r_iva = db_fetch_object( $query_iva );
$IVA = $r_iva->Valor / 100;


$sql_dia_sin_iva = "SELECT IDDiaSinIva FROM DiaSinIva Where Fecha='".$Hoy."' LIMIT 1";
$query_dia_sin_iva = db_query( $sql_dia_sin_iva );
$row_dia_sin_iva = db_fetch_object( $query_dia_sin_iva );
if((int)$row_dia_sin_iva->IDDiaSinIva>0){
	$DiaSinIva="S";
}
else{
	$DiaSinIva="N";
}


// Consulto las resoluciones
$array_resolucion_pto_vta=array();
$sql_pto_venta="SELECT IDPuntoVenta,NumeroResolucion,Codigo, IDCiudad FROM PuntoVenta WHERE 1 ";
$r_pto_venta=db_query($sql_pto_venta);
while($row_pto_venta=db_fetch_array($r_pto_venta)){
	$array_resolucion_pto_vta[$row_pto_venta["IDPuntoVenta"]]["Resolucion"]=$row_pto_venta["NumeroResolucion"];	
	$array_resolucion_pto_vta[$row_pto_venta["IDPuntoVenta"]]["Codigo"]=$row_pto_venta["Codigo"];	
	$array_resolucion_pto_vta[$row_pto_venta["IDPuntoVenta"]]["IDCiudad"]=$row_pto_venta["IDCiudad"];	
}

if(empty($_POST["IDFactura"])){
		$sql_fact="SELECT F.*, E.Nombre, E.Apellidos FROM Factura F, Empleado E WHERE F.IDEmpleado=E.IDEmpleado and FacturaElectronica = '' and FechaFactura >= '".$Hoy."' and F.Estado <> 'ANULADA' ORDER BY F.IDFactura DESC  LIMIT 30";
}
else{
		$sql_fact="SELECT F.*, E.Nombre, E.Apellidos FROM Factura F, Empleado E WHERE F.IDEmpleado=E.IDEmpleado and FacturaElectronica <> 'S' and  F.IDFactura='".$_POST["IDFactura"]."' and F.IDPuntoVenta = '".$_POST["IDPuntoVenta"]."' and F.Estado <> 'ANULADA' ORDER BY F.IDFactura DESC  LIMIT 1";
}


//$sql_fact="SELECT F.*, E.Nombre, E.Apellidos FROM Factura F, Empleado E WHERE F.IDEmpleado=E.IDEmpleado and FacturaElectronica = '' and F.IDFactura='814636' and F.IDPuntoVenta = 24 and F.Estado <> 'ANULADA' ORDER BY F.IDFactura DESC  LIMIT 30";

$r_fac=db_query($sql_fact);
$facturaElectronica = new FacturaElectronica();
while($row_fact=db_fetch_array($r_fac)){
	$NumeroResolucion=$array_resolucion_pto_vta[$row_fact["IDPuntoVenta"]]["Resolucion"];
	$CodigoAlmacen=$array_resolucion_pto_vta[$row_fact["IDPuntoVenta"]]["Codigo"];
	$IDCiudad=$array_resolucion_pto_vta[$row_fact["IDPuntoVenta"]]["IDCiudad"];
	$facturaElectronica->factura($row_fact,$NumeroResolucion,$CodigoAlmacen,$IVA,$IDCiudad,$DiaSinIva);
}

?>
