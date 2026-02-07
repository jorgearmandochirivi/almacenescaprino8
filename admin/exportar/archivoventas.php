<?php
	error_reporting( E_ERROR | E_PARSE );
	
	
	include("../config.inc.php");

	$sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura, F.ValorTotal, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef,DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura,
	DF.IVA, DF.IDDetalleFactura, DF.ReteIVA, DF.ReteICA, F.IDFactura, F.IDPuntoVenta 
	FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P 
	WHERE DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) BETWEEN ( DATE_FORMAT('$FechaInicio','%Y-%c-%d' ) AND DATE_FORMAT('$FechaFin','%Y-%c-%d' )  ) 
	AND F.IDFactura = DF.IDFactura 
	AND F.IDPuntoVenta = DF.IDPuntoVenta
	AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica 
	AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia 
	AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio;";
	exit;
											
					
	$qry_facturas = db_query( $sql_facturas );
	
	$i = 0;
	$formapago = array();
	
	while( $array_factura = db_fetch_array( $qry_facturas ) )
	{
		$r_facturas[$i] = $array_factura;
		$i++;
		
	}//end while( $r_facturas = db_fetch_array( $qry_facturas ) )	
	
	/********* CIUDADES *********/
	$sql_ciudad = " SELECT * FROM Ciudad ";
	$qry_ciudad = db_query( $sql_ciudad );
	while( $r_ciudad = db_fetch_array( $qry_ciudad ) )
	{
		$array_ciudad[$r_ciudad[IDCiudad]] = $r_ciudad[Descripcion];
	}//end while

	/********* CIUDADES *********/
	$sql_puntoventa = " SELECT * FROM PuntoVenta ";
	$qry_puntoventa = db_query( $sql_puntoventa );
	while( $r_puntoventa = db_fetch_array( $qry_puntoventa ) )
	{
		$array_puntoventa[$r_puntoventa[IDPuntoVenta]] = $r_puntoventa[Nombre];
	}//end while

	//define date for title: EDIT this to create the time-format you need
	$now_date = date('m-d-Y H:i');
	//define title for .doc or .xls file: EDIT this if you want

	$nombre = "VENTAS Caprino";
	$title = "VENTAS Caprino Fecha $now_date";
	$file_type = "vnd.ms-excel";
	$file_ending = "xls";

	
	
	header("Content-Type: application/$file_type");
	header("Content-Disposition: attachment; filename=$nombre.$file_ending");
	header("Pragma: no-cache");
	header("Expires: 0");
	
	
	echo("$title\n");
	//define separator (defines columns in excel & tabs in word)
	$sep = "\t"; //tabbed character
	//start of printing column names as names of MySQL fields
	$qry = db_query($sql);

	echo "Punto de Venta\t";
	echo "Numero Factura\t";
	echo "Referencia\t";
	echo "Fecha\t";
	echo "Valor\t";
	echo "Pares\t";
	echo "Nombre Cliente\t";
	echo "Numero Identificacion\t";
	echo "Telefono\t";
	echo "Celular\t";
	echo "Email\t";
	echo "\n";
	//print_r( $array_ptoventa )."<br>";
	foreach( $r_facturas as $key => $valor )
	{
		//echo "<br>".$r->IDPtoVenta."<br>";
		
		//Traer Cliente
		$sql_cliente = " SELECT * FROM Cliente WHERE IDCliente = '$r->IDCliente' ";
		$qry_cliente = db_query( $sql_cliente );
		$r_cliente = db_fetch_object( $qry_cliente );

		echo $array_puntoventa[$r->IDPuntoVenta]."\t";
		echo $valor[NumeroFactura]."\t";
		echo $valor[Numero]."\t";
		echo $valor[FechaFactura]."\t";
		echo $valor[ValorU]."\t";
		echo $valor[Cantidad]."\t";
		echo $r_cliente->Nombre." ".$r_cliente->Apellido."\t";
		echo $r_cliente->Cedula."\t";
		echo $r_cliente->Telefono."\t";
		echo $r_cliente->Celular."\t";
		echo $r_cliente->Email."\t";
		echo "\n";
	}//end while
	
?>