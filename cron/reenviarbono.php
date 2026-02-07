<?php
include("../admin/config.inc.php");

$array_id_bono=array($_POST["IDBono"]);
envia_bono_cliente($_POST["IDCliente"],$array_id_bono);
echo "enviado";
exit;
?>
