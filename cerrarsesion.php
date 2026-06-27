<?php
include("admin/config.inc.php");

$Usuario=$_GET['Usuario'];
switch ($Usuario) {
    case "carolina":
        $IDCerrar="13,109";
        break;
    case "arturo":
        $IDCerrar="338";
        break;
    case "juliana":
            $IDCerrar="190";
        break;        
    default:
        $IDCerrar="13,109";
        break;
}
$sql_cerrar="DELETE FROM Sesion WHERE IDUsuario in ($IDCerrar)";
db_query($sql_cerrar);
echo "Se cerró la sesión del usuario $Usuario";
exit;
?>
