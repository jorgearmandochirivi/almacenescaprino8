<?php
include("../../admin/config.inc.php");
header("Content-type: text/json");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

$columns = array();

$datos = Verifica_SesionCliente();
//	print_r($datos);
$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
$ID_Usuario = $datos["IDUsuario"];
$Nivel =  $datos["Nivel"];
$IVA = $datos["IVA"];
$IDPuntoVenta = $datos["IDPuntoVenta"];

$array_tarjetas = explode(",", $_POST["CodTarjeta"]);


foreach ($array_tarjetas as $num_tarjeta) {
	$dblink = db_connect($dbhost, $dbname, $dbuser, $dbpass);
	$sql_bono = " SELECT CodigoTarjeta,Estado From TarjetaPunto Where CodigoTarjeta = '" . $num_tarjeta . "' and (Estado = 'V' or Estado = 'O') LIMIT 1 ";
	$qry_bono = db_query($sql_bono);
	$r_bono = db_fetch_array($qry_bono);

	$sql_bono_valor = " SELECT PrecioU From DetalleFactura Where CodigoTarjeta = '" . $num_tarjeta . "' and (IDFactura > 929564 or IDFactura = 919789)  LIMIT 1 ";
	$qry_bono_valor = db_query($sql_bono_valor);
	$r_bono_valor = db_fetch_array($qry_bono_valor);

	//Consulto cuanto le queda a esa tarjeta
	$sql_bono_consumo = " SELECT SUM(ValorBono) as TotalUtilizado From FacturaBono Where CodigoTarjeta = '" . $num_tarjeta . "' and IDFacturaBono > 15055 ";
	$qry_bono_consumo = db_query($sql_bono_consumo);
	$r_bono_consumo = db_fetch_array($qry_bono_consumo);


	$SaldoTarjeta += $r_bono_valor["PrecioU"] - $r_bono_consumo["TotalUtilizado"];


	// Si es un codigo largo es por que es una tarjeta vendida por la pagina web y dejo pasar
	if (strlen($num_tarjeta) >= 15) {
		$array_tarjeta = explode(",", $num_tarjeta);
		$tarjetanoExiste = 0;
		if (count($array_tarjeta) > 0) {
			$conexion2 = mysql_connect("localhost", "Caprino2013", "2001C4pr1n0SIM") or die("Problemas en la conexion2");
			mysql_select_db("Caprino2013", $conexion2) or die("Problemas en la selección de la base de datos");
			foreach ($array_tarjeta as $cod_tarjeta) {
				$sqltarjeta = "SELECT CodigoTarjeta,Valor FROM DetallePedidoTarjeta WHERE CodigoTarjeta = '" . $cod_tarjeta . "' and Estado = 'Redimido' LIMIT 1";
				$Qrytarjeta = mysql_query($sqltarjeta, $conexion2) or die("Problemas en conexion2:" . mysql_error());
				$DatosTarjeta = mysql_fetch_array($Qrytarjeta);
				if ($DatosTarjeta["CodigoTarjeta"] != "") {
					$tarjetaExiste = "S";
					$r_bono["CodigoTarjeta"] = $num_tarjeta;
					$SaldoTarjeta += $DatosTarjeta["Valor"];
					$r_bono["Estado"] = "V";
				} else {
					$tarjetanoExiste++;
				}
			}
			if ($tarjetanoExiste > 0) {
				$r_bono["CodigoTarjeta"] = "";
				$SaldoTarjeta = "";
				$r_bono["Estado"] = "";
			}
			//mysql_close($conexion2);
		}
	}
}




$columns = array(
	"\"CodigoTarjeta\":\"" . $r_bono["CodigoTarjeta"] . "\"",
	"\"ValorTarjeta\":\"" . $SaldoTarjeta . "\"",
	"\"Estado\":\"" . $r_bono["Estado"] . "\""
);

$str = "{\"column\":{";
$str .= implode(",", $columns);
$str .= "}}";
echo $str;
