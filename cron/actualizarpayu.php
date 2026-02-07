<?php
include("../admin/config.inc.php");
$sql_fact="UPDATE Factura SET PagoPayu = '".$_POST["Valor"]."' WHERE IDFactura='".$_POST["IDFactura"]."' and IDPuntoVenta = '".$_POST["IDPuntoVenta"]."'";
db_query($sql_fact);
?>
