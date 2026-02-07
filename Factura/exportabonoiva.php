<?php
	include("../admin/config.inc.php");
	Encabezado();
	
    $sql_fac = "SELECT * FROM BonoIva WHERE 1";
	$now_date = date('m-d-Y H:i');
	$result = db_query($sql_fac);
	$title = "Datos Reporte Bono Iva $now_date";
	$file_type = "vnd.ms-excel";
	$file_ending = "xls";
	
	
	header("Pragma: ");
	header("Cache-Control: ");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Content-Type: application/$file_type; charset=ISO-8859-1");
	header("Content-Disposition: attachment; filename=$title.$file_ending"); 
	
	
	echo("$title\n");
	//define separator (defines columns in excel & tabs in word)
	$sep = "\t"; //tabbed character
	$ponerdetalle = "";
	print("\n");
	//end of printing column names
	//Poner los nombres de las columnas
		
		echo "Cliente" . $sep;
		echo "Codigo" . $sep;
		echo "Numero Factura" . $sep;
		echo "Punto de Venta Genera" . $sep;
		echo "Punto de Venta Redime" . $sep;
		echo "Valor" . $sep;
		echo "Disponible" . $sep;		
		echo "Producto" . $sep;

		
		print("\n");	
	//start while loop to get data

		while($row = db_fetch_array($result))
		{	

			//Detalle Factura
			$sql_detalle_factura = "SELECT * From DetalleFactura Where IDFactura = '".$row[IDFactura]."' and IDPuntoVenta = '".$row[IDPuntoVenta]."'";
			$qry_factura_detalle = db_query($sql_detalle_factura);
			while ($row_factura_detalle = db_fetch_array($qry_factura_detalle)){
				$cantidad_producto++;
				$array_referencias=array();
				$pto_vta_ref = get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_factura_detalle["IDCodificacionEspecifica"]);
				$id_ref = get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$pto_vta_ref);
				$sql_referencia="SELECT TR.Descripcion Categoria, TT.Descripcion Genero, R.Numero Referencia
												 FROM TipoReferencia TR, TipoTalla TT, Referencia R
												 WHERE R.IDTipoReferencia=TR.IDTipoReferencia and R.IDTipoTalla=TT.IDTipoTalla and IDReferencia = '".$id_ref."' ";
			  $qry_ref = db_query($sql_referencia);
				while ($row_ref = db_fetch_array($qry_ref)){
					$array_referencias[]= $row_ref["Referencia"];					
				}
			}
			//Fin detalle factura

			$Referencias="";
			if(count($array_referencias)>0){
				$Referencias = implode(",",$array_referencias);
			}
		 


			echo get_field("Cliente","Nombre","IDCliente",$row["IDCliente"]) . " " .get_field("Cliente","Apellido","IDCliente",$row["IDCliente"]) . $sep;
			echo $row["Codigo"] . $sep;
			echo $row["NumeroFactura"] . $sep;
			echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$row["IDPuntoVenta"]) . $sep;
			echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$row["IDPuntoVentaRedime"]) . $sep;
			echo $row["Valor"] . $sep;
			echo $row["Disponible"] . $sep;			
			echo $Referencias . $sep;			
			print "\n";
		}
		
		exit;
		
?>