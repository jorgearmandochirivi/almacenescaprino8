<?php
header('Content-Type: text/txt; charset=UTF-8');
require( "../config.inc.php" );

$array_datos["Encontrado"]="N";
$array_datos["DatosAdmin"]="";
if($_POST['IDCargo']==3 || $_POST['IDCargo']==4){
  $Proveedor = db_query("Select * from Empleado Where IDPuntoVenta = '".$_POST['IDPuntoVenta']."' and IDCargo in (3,4)");
  while($RDatos = db_fetch_array( $Proveedor,$a )){
      $nombre_admin=$RDatos["Nombre"] .  " " . $RDatos["Apellidos"] .  " " . get_field("Cargo","Cargo","IDCargo",$RDatos["IDCargo"]);
  }
  $array_datos["Encontrado"]="S";
  $array_datos["DatosAdmin"]=$nombre_admin;
}

echo json_encode($array_datos);
?>
