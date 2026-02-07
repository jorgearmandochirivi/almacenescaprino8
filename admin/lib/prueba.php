<?php

//error_reporting(E_ALL);
include("config.inc.php");



envia_comentario_garantia_almacen("7125",$frm,$IDEmpleado);
echo "aca";
exit;




$cabeceras = 'From: ventas@calzadocaprino.com' . "\r\n" .
    					 'Reply-To: ventas@calzadocaprino.com' . "\r\n" .
    					 'X-Mailer: PHP/' . phpversion();
			$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
			$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

/*
$id_garantia=18154;
$frm["Descripcion"]="test";
$IDEmpleado=1;
$resp=envia_comentario_garantia_almacen_test($id_garantia,$frm,$IDEmpleado);
echo "FIN";
exit;

*/
mail ( "jorgechirivi@gmail.com,jaimer@calzadocaprino.com" , "PRUEBA CORREO " , "test", $cabeceras );    
echo "enviado";
exit;
envia_pedido_tercero(611,$rel);
//$correo="jorgechirivi@gmail.com";
//$resp=envia_pedido_mostrar(615,$correo);
//echo $resp;
echo "enviado new";
exit;
$resp=envia_comentario_garantia_almacen($id,$frm,$ID_Usuario);
echo "FIN";
exit;



?>
