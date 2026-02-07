<?php
header('Content-Type: text/txt; charset=UTF-8');
require( "../config.inc.php" );
$Referencia = db_query("Select * from DetallePedidoTercero Where ReferenciaCaprino = '".$_POST['numero_referencia']."' and CodigoColor = '".$_POST['Color']."' Order by IDDetallePedidoTercero Desc Limit 1");
$RReferencia = db_fetch_array( $Referencia,$a );

if (!empty($RReferencia[IDDetallePedidoTercero])):
	echo json_encode($RReferencia);
else:
	echo json_encode("no_existe");
endif;		


//VERIFICA SI LA EXISTENCIA EXISTE EN CAPRINO
/*
$Referencia = db_query("Select * from Referencia Where Numero = '".$_POST['numero_referencia']."'");
$RReferencia = db_fetch_array( $Referencia,$a );

if (!empty($RReferencia[IDReferencia])):
	echo json_encode("ok");
else:
	echo json_encode("no_existe");
endif;		
*/
?>