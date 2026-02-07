<?php
header('Content-Type: text/txt; charset=UTF-8');
require( "../config.inc.php" );
// Cambio estado a enviado proveedor
$sql_actualizar_pedido=db_query("Update PedidoTercero Set FechaEntrega = '".$_POST["FechaEntrega"]."', FechaTrEd = NOW() Where IDPedidoTercero = '".$_POST["id_pedido_tercero"]."' Limit 1");
echo json_encode("ok");
?>