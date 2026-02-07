<%
	include("../config.inc.php");
	require( "../lib/dhabiles.inc.php" );
	Encabezado();
	$datos = Verifica_Sesion();
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
	$sep = "\t";
	$cal = new Date_Calc();
	
	$sql_facturas = " SELECT F.NumeroFactura,F.IDFactura, F.FechaFactura,DATE_FORMAT(F.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF, F.ValorTotal, R.Numero, DF.ValorU,DF.PrecioU, DF.Cantidad,DF.DescuentoRef, DF.DescuentoPar, P.Descuento, F.Descuento as DescuentoFactura 
										FROM Factura F, DetalleFactura DF, CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Precio P 
										WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
										AND F.FechaFactura BETWEEN '$FechaDesde' AND '$FechaHasta' 
										AND F.IDFactura = DF.IDFactura
										AND F.IDPuntoVenta = DF.IDPuntoVenta 
										AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica 
										AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia 
										AND PVR.IDReferencia = R.IDReferencia AND R.IDPrecio = P.IDPrecio;";
											
					
	$qry_facturas = db_query( $sql_facturas );
	
	$i = 0;
	$formapago = array();
	
	while( $r_facturas = db_fetch_array( $qry_facturas ) )
	{
		$array_factura[$i] = $r_facturas;
		$i++;
	}//end while( $r_facturas = db_fetch_array( $qry_facturas ) )
	//print_r( $array_factura );
	
	foreach( $array_factura as $key => $valor )
	{
		
		$pcomision = 0;
		$comision = 0;
		
		if( $valor['DescuentoPar'] > 0 )
			$valordescuentopar = ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( $valor['DescuentoPar'] / 100 );
		else
			$valordescuentopar = 0;
		
		
		/*SE AGREGA NUEVO*/
		$sql_comisiones = " SELECT * FROM FormaPagoFactura WHERE IDFactura = '$valor[IDFactura]' ";
		$qry_comisiones = db_Query( $sql_comisiones );
		while( $r_comisiones = db_fetch_object( $qry_comisiones ) )
		{
			$pcomision = $r_comisiones->Comision / 100;
			$comision +=  ( $r_comisiones->Valor / (1 + $IVA) ) * $pcomision;
		}											
		//echo number_format( $comision  ,2 ); 
		$ComisionBancos += $comision;
		/*FIN NUEVO*/
		
		$parcial = ( ( $valor['PrecioU'] * $valor['Cantidad'] ) *   ( 1 - (  $valor['DescuentoFactura'] / 100 ) ) ) - $valordescuentopar;
		
		
		
		$valoriva = $parcial - ( $parcial / ( 1 + $IVA ) );
		$ventas[$valor[FechaFacturaF]][Venta] += $parcial - $valoriva;
		$ventas[$valor[FechaFacturaF]][valoriva] += $valoriva;
		$ventas[$valor[FechaFacturaF]][parcial] += $parcial;
		$tventa += $parcial - $valoriva;
		$tiva += $valoriva;
		$ttotal += $parcial;
	}//end foreach
	
	
	foreach( $ventas as $Fecha => $datos )
	{
		//formatear fecha ( AAAAMMDD )
			
		$ano = substr( $Fecha, 0,4 );
		$mes = substr( $Fecha, 5,2 );
		$dia = substr( $Fecha, 8,2 );
		$Fecha = $cal->dateFormat( $dia, $mes, $ano,"%Y%m%d" );		
		$space = "";
		
		$datos[Venta] = round( $datos[Venta] ,0 );
		$datos[valoriva] = round( $datos[valoriva] ,0 );
		$datos[parcial] = round( $datos[parcial] ,0 );
		
		$row = sprintf("%02s %s% 10s% 10s% 10s\r\n",$space,$Fecha,$datos[Venta],$datos[valoriva],$datos[parcial]);
		
		/*
		$row  = "00".$sep;
		$row .= $Fecha.$sep;
		$row .= $datos[Venta].$sep;
		$row .= $datos[valoriva].$sep;
		$row .= $datos[parcial].$sep;
		*/
		$strfile .= $row;
		
	}//end for
	
	/********************** FIN DE MOSTRAR LAS FECHAS CON EFECTIVO *********************************************/

	
	$file_type = "text/plain";
	$file_ending = "txt";
	$archivo = "Mensual ".$FechaDesde." - ".$FechaHasta." - ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta );
	
	header("Content-Type: application/$file_type");
	header("Content-Disposition: attachment; filename=$archivo.$file_ending");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo $strfile;
%>
