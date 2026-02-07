<?
include("admin/config.inc.php");

$sql_cerrar="DELETE FROM Sesion WHERE IDUsuario in (13,109)";
db_query($sql_cerrar);
exit;
?>
