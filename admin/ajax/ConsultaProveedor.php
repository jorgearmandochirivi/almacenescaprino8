<?php
header('Content-Type: text/txt; charset=UTF-8');
require( "../config.inc.php" );

$Proveedor = db_query("Select * from Proveedor Where IDProveedor = '".$_POST['IDProveedor']."'");
$RProveedor = db_fetch_array( $Proveedor,$a );
$RProveedor[Ciudad]=get_field("Ciudad","Descripcion","IDCiudad",$RProveedor[IDCIudad]);

echo json_encode($RProveedor);	
?>