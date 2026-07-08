<?php
	include("../config.inc.php");
	Encabezado();

	$sql_garantias = $_GET["sql"];
	$now_date = date('m-d-Y H:i');

	$Table = "Entrada";
	$TableJoin = "";
	$Key = "IDEntrada";
	$MOD = "Entrada";
	$_GET["order_by"]="IDEntrada";

	/********************* TRAER DATOS DE VENTAS CON TARJETAS DE CREDITO Y DEBITO 'ID'S MAYOR QUE 2'*********************/

	if( $_GET["field"] == "NumeroReferencia" )
	{

		$sql = " SELECT * FROM Entrada E, PuntoVentaReferencia PR, Referencia R WHERE E.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia AND PR.IDReferencia = R.IDReferencia
					AND R.Numero LIKE '%$QryString%' GROUP BY E.IDEntrada ORDER BY Fecha DESC " ;

	}//end if
	elseif((int)$_GET["IDProveedor"]>0){
		if(!empty($_GET["limit1"]) && !empty($_GET["limit2"])){
			$condicion_fecha = " AND Fecha BETWEEN '".$_GET["limit1"]."' AND '".$_GET["limit2"]."'  ";
		}

		$sql = " SELECT * FROM Entrada E, PuntoVentaReferencia PR, Referencia R WHERE E.IDPuntoVentaReferencia = PR.IDPuntoVentaReferencia AND PR.IDReferencia = R.IDReferencia
					AND R.IDProveedor = '".$_GET["IDProveedor"]."' ".$condicion_fecha." GROUP BY E.IDEntrada ORDER BY Fecha DESC " ;

	}
	else
	{
		$_GET["rangofield"] = " Fecha ";
		$sql = make_qry_string($_GET);
	}

	$qry_facturas = db_query( $sql );

	//Puntos de Venta
	$sql_puntos = " SELECT IDPuntoVenta, Nombre FROM PuntoVenta ";
	$qry_puntos = db_query( $sql_puntos );
	while( $r_puntos = db_fetch_array( $qry_puntos ) )
		$array_puntos[ $r_puntos["IDPuntoVenta"] ] = $r_puntos["Nombre"];



	$title = "Datos Reporte Entrada Fecha $now_date";
	$file_type = "vnd.ms-excel";
	$file_ending = "xls";


	
	header("Pragma: ");
	header("Cache-Control: ");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Content-Type: application/$file_type; charset=ISO-8859-1");
	header("Content-Disposition: attachment; filename=$title.$file_ending");
	
	echo "ENTRADAS ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta )." ".$FechaDesde." - ".$FechaHasta . "\n";
	//define separator (defines columns in excel & tabs in word)
	$sep = "\t"; //tabbed character
	$ponerdetalle = "";
	print("\n");
	//end of printing column names
	//Poner los nombres de las columnas

		echo "Fecha" . $sep;	
		echo "Punto de Venta" . $sep;
		echo "Proveedor" . $sep;
		echo "Numero Factura" . $sep;
		echo "Remision" . $sep;		
		echo "Referencia" . $sep;
		echo "Talla" . $sep;
		echo "Fecha" . $sep;
		echo "Cantidad" . $sep;
		echo "Costo" . $sep;

		print("\n");
	//start while loop to get data
		while( $r_entrada = db_fetch_array( $qry_facturas ) )
		{
			$IDReferencia = (int)get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$r_entrada["IDPuntoVentaReferencia"]);
			$NombreProveedor = "";

			if($IDReferencia > 0){
				$sql_ref = "SELECT IDProveedor FROM Referencia WHERE IDReferencia = $IDReferencia LIMIT 1";
				$r_sql = db_query($sql_ref, false, true, true);
				if($r_sql){
					$r_ref = db_fetch_array($r_sql);
					$IDProveedor = (int)($r_ref["IDProveedor"] ?? 0);
					if($IDProveedor > 0){
						$NombreProveedor = get_field("Proveedor","Nombre","IDProveedor",$IDProveedor);
					}
				}
			}
			echo $r_entrada["FechaRemision"] .$sep;
			echo $array_puntos[$r_entrada["IDPuntoVenta"]] .$sep;
			echo $NombreProveedor .$sep;
			echo $r_entrada["NumeroFactura"] .$sep;
			echo $r_entrada["Remision"] .$sep;			
			echo get_field("Referencia","Numero","IDReferencia",$IDReferencia) .$sep;
			echo get_field("Talla","Descripcion","IDTalla",$r_entrada["IDTalla"]) .$sep;
			echo formatofecha(substr($r_entrada["Fecha"],0,10))." a las ".substr($r_entrada["Fecha"],10) .$sep;
			echo number_format($r_entrada["Cantidad"])  .$sep;
			echo get_field("CostoReferencia","Costo","IDReferencia",$IDReferencia)  .$sep;
			print "\n";
		}

		exit;

?>
