<?php
header('Content-Type: text/txt; charset=UTF-8');
require( "../config.inc.php" );
// Cambio estado a enviado proveedor
$sql_actualizar_pedido=db_query("Update PedidoTercero Set IDEstadoPedidoTercero = '2', FechaGeneracionPedido = NOW() Where IDPedidoTercero = '".$_POST["id_pedido_tercero"]."' Limit 1");
//Generar pdf
$pdf_generado = crear_pdf_pedido($_POST["id_pedido_tercero"]);
if (!$pdf_generado) {
	http_response_code(500);
	echo json_encode(array(
		"status" => "error",
		"message" => "No fue posible generar el PDF del pedido"
	));
	exit;
}
//Envio correo a proveedor y caprino
envia_pedido_tercero($_POST["id_pedido_tercero"],$_POST["rel"]);
echo json_encode(array("status" => "ok"));
?>