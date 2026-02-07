<?php
include("admin/config.inc.php");
$IDPedidoTercero=$_GET["IDPedidoTercero"];
$Correo=$_GET["Correo"];
$resp=envia_pedido_mostrar($IDPedidoTercero,$Correo);
echo $resp;
?>