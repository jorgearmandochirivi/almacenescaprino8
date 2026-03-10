<?php

/*******************************************************************************************
	Libreria de funciones para el Sistema de Puntos de Venta de Caprino - Salidas de MErcancia
	Creador por: John Escobar
	Iniciado: Agosto 11/2005
	Ultima Modificaci?n: Agosto 11/2005
*******************************************************************************************/

/*******************************************************************************************
	venta: Realiza todos los movimiento necesarios cuando se hace una venta en el punto de venta.
	Parametros:
			$frm: array con los datos de venta
	Retorna:
			Void
*******************************************************************************************/
function venta( $frm )
{
	Global $Nombre_Usuario, $ID_Usuario, $Table, $Key, $MOD, $IDPuntoVenta;

	//Insertar los Items en la tabla de detalles

	
	$Items = $frm['ITEM'];

	//print_r( $frm );

	for($i = 1; $i <= $Items; $i++)
	{

		$iddetalle = get_maxID("DetalleFactura WHERE IDFactura = '".$frm["IDFactura"]."' ","IDDetalleFactura");
		$IDCodificacion = "IDCodificacion".$i;
		$Cantidad = "Cantidad".$i;
		$CodigoTarjeta = "CodigoTarjeta".$i;
		$ValorU = "ValorU".$i;
		$PrecioU = "Precio".$i;
		$DescuentoRef = "Descuento".$i;
		$DescuentoPar = "DescuentoLin".$i;

		$ValorU = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm[$ValorU]);
		$PrecioU = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm[$PrecioU]).".00";
		$DescuentoRef = $frm[$DescuentoRef];
		$DescuentoPar = $frm[$DescuentoPar];

		if( $frm[$Cantidad] > 0 )
		{

			$str_insert_detalle  = "INSERT INTO DetalleFactura ( IDDetalleFactura,IDFactura,IDPuntoVenta,IDCodificacionEspecifica,Cantidad,ValorU,PrecioU,DescuentoRef,DescuentoPar, UsuarioTrCr,FechaTrCr, CodigoTarjeta ) ";
			$str_insert_detalle .= "VALUES ( '$iddetalle','".$frm['IDFactura']."','".$frm["IDPuntoVenta"]."','$frm[$IDCodificacion]','$frm[$Cantidad]','$ValorU','$PrecioU','$DescuentoRef','$DescuentoPar','".$frm["UsuarioTrCr"]."','".$frm["FechaTrCr"]."','$frm[$CodigoTarjeta]' )";
			//echo $str_insert_detalle .= "<br>";

			db_query( $str_insert_detalle );


			//Verificar si es una tarjeta
			if( !empty( $frm[$CodigoTarjeta] ) )
			{
				$idcodigotarjeta = get_maxID("TarjetaCodigo ","IDTarjetaCodigo");
				$sql_tarjeta = " INSERT INTO TarjetaCodigo (IDTarjetaCodigo, CodigoTarjeta, IDFactura, IDCliente, IDPuntoVenta, IDCodificacionEspecifica, IDReferencia, Valor, FechaTrCr, UsuarioTrCr) VALUES ('" . $idcodigotarjeta . "','". $frm[$CodigoTarjeta] ."','" . $frm["IDFactura"] . "','" . $frm["IDCliente"] . "','" . $frm["IDPuntoVenta"] . "','" . $frm[$IDCodificacion] . "','','" . $PrecioU . "',NOW(),'" . $frm["UsuarioTrCr"] . "')  ";
				db_query( $sql_tarjeta );

				//actualizar codigo de tarjeta a vendida
				$sql_actualiza_tarjeta = "UPDATE TarjetaPunto SET Estado = 'V', FechaTrEd = NOW() WHERE IDPuntoVenta = '" . $frm["IDPuntoVenta"] . "' AND CodigoTarjeta = '". $frm[$CodigoTarjeta] ."' ";

				db_query( $sql_actualiza_tarjeta );


			}//end if




			//insertar el log
			insertlog($ID_Usuario,"DetalleFactura",$iddetalle,"Insertar",$str_insert_detalle);

		}//end iif

	}//end for($i = 1; $i < $Items; $i++)


	//Realizar el Movimiento correspondiente en la tabla de Codificacion especifica

	for($i = 1; $i <= $Items; $i++)
	{

		$IDCodificacion = "IDCodificacion".$i;
		$Cantidad = "Cantidad".$i;
		$existencias = get_field( "CodificacionEspecifica","Existencias","IDCodificacionEspecifica", $frm[$IDCodificacion]);
		$existencias = is_numeric($existencias) ? (float)$existencias : 0;
		$cantidad_valor = isset($frm[$Cantidad]) && is_numeric($frm[$Cantidad]) ? (float)$frm[$Cantidad] : 0;
		$existencias = $existencias - $cantidad_valor;

		$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDCodificacionEspecifica = '$frm[$IDCodificacion]'";
		//echo $str_actualiza_inventario .= "<br>";

		db_query( $str_actualiza_inventario );

		//insertar el log
		insertlog($ID_Usuario,"CodificacionEspecifica",$frm[$IDCodificacion],"Insertar",$str_actualiza_inventario);

	}//end for($i = 1; $i < $Items; $i++)

	$frm['ValorIVA'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['ValorIVA']);

	$frm['ValorTotal'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['ValorTotal']);

	$sql_actualizafactura = "UPDATE Factura SET ValorIVA = '".$frm["ValorIVA"]."', ValorTotal = '".$frm['ValorTotal']."', FechaCreacion = NOW() WHERE IDFactura = '".$frm['IDFactura']."' AND IDPuntoVenta = '".$frm['IDPuntoVenta']."'";
	db_query($sql_actualizafactura);

	//Actualiza Pagare
	if( !empty( $frm["NumeroPagare"] ) )
	{
		$sql_actualiza_pagare = "UPDATE Pagare SET Estado = 'V', FechaTrEd = NOW() WHERE IDPuntoVenta = '" . $frm["IDPuntoVenta"] . "' AND CodigoPagare = '". $frm["NumeroPagare"] ."' ";
		db_query( $sql_actualiza_pagare );
	}//end if

	//insertar el log
	insertlog($ID_Usuario,"Factura",$frm['IDFactura'],"Actualizar",$sql_actualizafactura);


	//insertar puntos fidelizacion
	//actualiza_puntos_fid($frm);
	fid_actualizapuntos( $frm ); //Agregado por John Escobar Junio 2013

	//Insertar Resolucion DIAN del Punto de Venta

	$Resolucion = get_field("PuntoVenta","Resolucion","IDPuntoVenta",$frm['IDPuntoVenta']);
	$RDesde = get_field("PuntoVenta","RDesde","IDPuntoVenta",$frm['IDPuntoVenta']);
	$RHasta = get_field("PuntoVenta","RHasta","IDPuntoVenta",$frm['IDPuntoVenta']);

	$sql_actualizafactura = "UPDATE Factura SET Resolucion = '".$Resolucion."', RDesde = '".$RDesde."', RHasta = '".$RHasta."', FechaCreacion = NOW() WHERE IDFactura = '".$frm['IDFactura']."'  AND IDPuntoVenta = '".$frm['IDPuntoVenta']."'";	
	db_query($sql_actualizafactura);

	//insertar el log
	insertlog($ID_Usuario,"Factura",$frm['IDFactura'],"Actualizar",$sql_actualizafactura);

}//end function venta( $frm )

/*******************************************************************************************
	agregarventaempleado:Agrega un regisro a la tabla de ventas empleados necesaria para el calculo de la comision.
			$frm: array con los datos de salida de mercancia
	Retorna:
			Void
*******************************************************************************************/
function agregarventaempleado( $frm )
{
	//print_r($frm);
	$administrador = get_field("PuntoVenta","IDEmpleado","IDEmpleado",$frm['IDEmpleado']);
	if($administrador <> "")
		$cargo = "Administrador";
	else
		$cargo = "Empleado";

	$frm['ValorTotal'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['ValorTotal']);

	$idadministrador = get_field("PuntoVenta","IDEmpleado","IDPuntoVenta",$frm['IDPuntoVenta']);
	$idventaempleado = get_maxID("VentasEmpleado","IDVentasEmpleado");
	$sql_ventaadministrador = "INSERT INTO VentasEmpleado VALUES ('$idventaempleado','$idadministrador','Administrador','".$frm["IDPuntoVenta"]."','".$frm["IDFactura"]."','".$frm["ValorTotal"]."')";
	$queryventaadministrador = db_query($sql_ventaadministrador);

	if($cargo == "Empleado")
	{
		$idventaempleado = get_maxID("VentasEmpleado","IDVentasEmpleado");
		$sql_ventaempleado = "INSERT INTO VentasEmpleado VALUES ('$idventaempleado','".$frm["IDEmpleado"]."','$cargo','".$frm["IDPuntoVenta"]."','".$frm["IDFactura"]."','".$frm["ValorTotal"]."')";
		$queryventaempleado = db_query($sql_ventaempleado);
	}
}

/*******************************************************************************************
	Libreria de funciones para el Sistema de Puntos de Venta de Caprino - Salidas de MErcancia
	Creador por: John Escobar
	Iniciado: Agosto 11/2005
	Ultima Modificaci?n: Agosto 11/2005
*******************************************************************************************/

/*******************************************************************************************
	venta: Realiza todos los movimiento necesarios cuando se hace una venta con bonos en el punto de venta.
	Parametros:
			$frm: array con los datos de venta
	Retorna:
			Void
*******************************************************************************************/
function ventabono( $frm )
{
	Global $Nombre_Usuario, $ID_Usuario, $Table, $Key, $MOD, $IVA;

	//Insertar los Items en la tabla de detalles

	$Items = $frm['ITEM'];

	//print_r( $frm );

	for($i = 1; $i <= $Items; $i++)
	{

		$iddetalle = get_maxID("DetalleFacturaBono WHERE IDFacturaBono = '".$frm['IDFacturaBono']."' ","IDDetalleFacturaBono");
		$IDCodificacion = "IDCodificacion".$i;
		$Cantidad = "Cantidad".$i;
		$ValorU = "ValorU".$i;
		$PrecioU = "Precio".$i;
		//$DescuentoRef = "Descuento".$i;
		$DescuentoRef = "DescuentoLin".$i;

		$ValorU = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm[$ValorU]);
		$PrecioU = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm[$PrecioU]).".00";
		$DescuentoRef = $frm[$DescuentoRef];

		$str_insert_detalle  = "INSERT INTO DetalleFacturaBono ( IDDetalleFacturaBono,IDFacturaBono,IDPuntoVenta,IDCodificacionEspecifica,Cantidad,ValorU,PrecioU,DescuentoRef,UsuarioTrCr,FechaTrCr ) ";
		$str_insert_detalle .= "VALUES ( '$iddetalle','".$frm['IDFacturaBono']."','".$frm['IDPuntoVenta']."','".$frm[$IDCodificacion]."','".$frm[$Cantidad]."','$ValorU','$PrecioU','$DescuentoRef','".$frm['UsuarioTrCr']."','".$frm['FechaTrCr']."' )";

		db_query( $str_insert_detalle );

		//insertar el log
		insertlog($ID_Usuario,"DetalleFacturaBono",$iddetalle,"Insertar",$str_insert_detalle);

	}//end for($i = 1; $i < $Items; $i++)

	//Realizar el Movimiento correspondiente en la tabla de Codificacion especifica

	for($i = 1; $i <= $Items; $i++)
	{

	$IDCodificacion = "IDCodificacion".$i;
	$Cantidad = "Cantidad".$i;
	$DescuentoRef = "DescuentoLin".$i;

	$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = Existencias - '$frm[$Cantidad]' WHERE IDCodificacionEspecifica = '$frm[$IDCodificacion]'";

	db_query( $str_actualiza_inventario );

	//insertar el log
	insertlog($ID_Usuario,"CodificacionEspecifica",$frm[$IDCodificacion],"Insertar",$str_actualiza_inventario);

	}//end for($i = 1; $i < $Items; $i++)

	$frm['ValorIVA'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['ValorIVA']);

	$frm['ValorTotal'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['ValorTotal']);

	$sql_actualizaFacturaBono = "UPDATE FacturaBono SET  ValorTotal = '".$frm['ValorTotal']."' WHERE IDFacturaBono = '".$frm["IDFacturaBono"]."' AND IDPuntoVenta = '".$frm['IDPuntoVenta']."'";
	db_query($sql_actualizaFacturaBono);

	//insertar el log
	insertlog($ID_Usuario,"FacturaBono",$frm['IDFacturaBono'],"Actualizar",$sql_actualizaFacturaBono);

	$frm['Excedente'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['Excedente']);
	if( $frm['Excedente'] > 0 )
	{
		//INSERTAR FACTURA CON EL EXCEDENTE

		$Resolucion = get_field("PuntoVenta","Resolucion","IDPuntoVenta",$frm['IDPuntoVenta']);
		$RDesde = get_field("PuntoVenta","RDesde","IDPuntoVenta",$frm['IDPuntoVenta']);
		$RHasta = get_field("PuntoVenta","RHasta","IDPuntoVenta",$frm['IDPuntoVenta']);

		$idfactura = get_maxID( "Factura WHERE IDPuntoVenta = '".$frm['IDPuntoVenta']."'","IDFactura" );
		//$numerofactura = get_maxID( "Factura WHERE IDPuntoventa = '".$frm['IDPuntoVenta']."' ","NumeroFactura" );

	   //$sql_facturas = "Select IDFactura From Factura WHERE IDPuntoVenta = '".$frm['IDPuntoVenta']."' and FechaFactura >='2021-12-01 09:00:00' Limit 1";
	   $sql_facturas = "Select IDFactura From Factura WHERE IDPuntoVenta = '".$frm['IDPuntoVenta']."' and FechaFactura >='2022-11-21 09:00:00' Limit 1";
	   $qry_facturas = db_query($sql_facturas);
	   $row_facturas = db_fetch_array($qry_facturas);
	   $ultima_fac = (int)$row_facturas["IDFactura"];
	   if($ultima_fac==0):
			$numerofactura=5001;
	   else:
			//$numerofactura=get_maxID("Factura WHERE IDPuntoVenta = '$frm['IDPuntoVenta']' and FechaFactura >='2021-12-01 09:00:00'","NumeroFactura");
			$numerofactura=get_maxID("Factura WHERE IDPuntoVenta = '".$frm['IDPuntoVenta']."' and FechaFactura >='2022-11-21 09:00:00'","NumeroFactura");
	   endif;



		$sql_insert_factura = " INSERT INTO Factura ( IDFactura, IDCliente, NumeroFactura, IDPuntoVenta, IDEmpleado, FechaFactura, ValorIVA,
								ValorTotal, Resolucion, RDesde, RHasta, UsuarioTrCr, FechaTrCr )
								VALUES ( '$idfactura','".$frm['IDCliente']."','".$numerofactura."','".$frm['IDPuntoVenta']."','".$frm['IDEmpleado']."',NOW(),'".$frm['ValorIVA']."','".$frm['Excedente']."',
								'$Resolucion','$RDesde','$RHasta','".$frm["UsuarioTrCr"]."','".$frm['FechaTrCr']."' ) ";

		db_query( $sql_insert_factura );

		//insertar el log
		insertlog($ID_Usuario,"Factura",$idfactura,"Insertar",$sql_insert_factura);

		//INSERTAR DETALLE FACTURA
		//se agrega el excedente como una referencia - el id de la referencia excedente esta guardada en la tabla parametros IDParametro = 1

		$IDExcedente = (int)get_field("Parametros", "Parametro", "IDParametro", 1);
		$IDCod = 0;
		if ($IDExcedente > 0) {
			$sql_cod_excedente = "SELECT C.IDCodificacionEspecifica
				FROM CodificacionEspecifica C
				INNER JOIN PuntoVentaReferencia PVR ON C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
				WHERE PVR.IDReferencia = '" . $IDExcedente . "'
				AND PVR.IDPuntoVenta = '" . (int)$frm['IDPuntoVenta'] . "'
				LIMIT 1";
			$qry_cod_excedente = db_query($sql_cod_excedente, false, true, true);
			if ($qry_cod_excedente && db_num_rows($qry_cod_excedente) > 0) {
				$row_cod_excedente = db_fetch_array($qry_cod_excedente);
				$IDCod = (int)$row_cod_excedente["IDCodificacionEspecifica"];
			}
		}
		$ValorU = $frm['Excedente'] / ( 1 + $IVA );
		$sql_detalle = " INSERT INTO DetalleFactura (IDDetalleFactura, IDFactura,IDPuntoVenta, IDCodificacionEspecifica, Cantidad, ValorU, PrecioU, DescuentoPar)
							VALUES ( '1','$idfactura','".$frm['IDPuntoVenta']."','$IDCod','1','$ValorU','".$frm['Excedente']."','".$frm[$DescuentoRef]."' ) ";

		db_query( $sql_detalle );

		//insertar el log
		insertlog($ID_Usuario,"DetalleFactura",$idfactura,"Insertar",$sql_detalle);

		//ACTUALIZAR FACTURA BONO CON EL ID DE LA FACTURA
		//SE CAMBIA LA ACTUALIZACION DE LA FACTURA CON EL PUNTO DE VENTA COMO PARTE DE LA LLAVE PRINCIPAL
		$sql_actualizafacturaBono = "UPDATE FacturaBono SET IDFactura = '$idfactura' WHERE IDFacturaBono = '".$frm["IDFacturaBono"]."' AND IDPuntoVenta = '".$frm['IDPuntoVenta']."'";
		db_query( $sql_actualizafacturaBono );

		//insertar el log
		insertlog($ID_Usuario,"FacturaBono",$frm['IDFacturaBono'],"Insertar",$sql_actualizafacturaBono);


		$frm['IDFactura'] = $idfactura;
		//ACTUALIZAR VENTAS VENDEDOR
		agregarventaempleadobono($frm);



	}//end if $frm['Excedente']

	return $frm;

}//end function venta( $frm )

/*******************************************************************************************
	Libreria de funciones para el Sistema de Puntos de Venta de Caprino - Salidas de MErcancia
	Creador por: John Escobar
	Iniciado: Agosto 11/2005
	Ultima Modificaci?n: Agosto 11/2005
*******************************************************************************************/

/*******************************************************************************************
	ventacambio: Realiza todos los movimiento necesarios cuando se hace una venta con bonos en el punto de venta.
	Parametros:
			$frm: array con los datos de venta
	Retorna:
			Void
*******************************************************************************************/
function ventacambio( $frm )
{
	Global $Nombre_Usuario, $ID_Usuario, $Table, $Key, $mod, $IVA;

	//Insertar los Items en la tabla de detalles

	$Items = $frm['ITEM'];

	//print_r( $frm );
	//exit;

	if( $mod == "cambioreferencia" )
		$iniciar = 10;
	else
		$iniciar = 1;

	 $iniciar;
	for($i = $iniciar; $i <= $Items; $i++)
	{
		//echo  $iddetalle = get_maxID("DetalleCambio WHERE IDCambio = '$frm['IDCambio']' AND IDPuntoVenta = '$frm['IDPuntoVenta']' ","IDDetalleCambio");
		$iddetalle = $i;
		//echo "<br>";
		$IDCodificacion = "IDCodificacion".$i;

		$IDCodificacionCambio = "IDCodificacion1";
		$Cantidad = "Cantidad".$i;
		$ValorU = "ValorU".$i;
		$PrecioU = "Precio".$i;
		$DescuentoRef = "Descuento".$i;

		$ValorU = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm[$ValorU]);
		$PrecioU = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm[$PrecioU]).".00";
		$DescuentoRef = $frm[$DescuentoRef];

		if( !empty( $frm[$Cantidad] ) )
		{
			$str_insert_detalle  = "INSERT INTO DetalleCambio ( IDDetalleCambio,IDCambio,IDPuntoVenta,IDCodificacionEspecificaCambio,IDCodificacionEspecifica,Cantidad,ValorU,PrecioU,DescuentoRef,UsuarioTrCr,FechaTrCr ) ";
			$str_insert_detalle .= "VALUES ( '$iddetalle','".$frm["IDCambio"]."','".$frm["IDPuntoVenta"]."','".$frm[$IDCodificacionCambio]."','".$frm[$IDCodificacion]."','".$frm[$Cantidad]."','$ValorU','$PrecioU','$DescuentoRef','".$frm["UsuarioTrCr"]."','".$frm["FechaTrCr"]."' )";
			db_query( $str_insert_detalle );

			//descargar de inventario
			if( $i >= 10 )
			{
				$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = Existencias - '$frm[$Cantidad]' WHERE IDCodificacionEspecifica = '$frm[$IDCodificacion]'";
				db_query( $str_actualiza_inventario );
			}
			//insertar el log
			insertlog($ID_Usuario,"DetalleCambio",$iddetalle,"Insertar",$str_insert_detalle);
			$str_insert_detalle = "";
		}
		
	}//end for($i = 1; $i < $Items; $i++)
	//exit;
	//Realizar el Movimiento correspondiente en la tabla de Codificacion especifica
	//Aumentar inventario

	//$IDCodificacion = "IDCodificacion2";
	//$Cantidad = "Cantidad2";

	for($item_cambiar=1;$item_cambiar<=$frm["TotalItemCambiar"];$item_cambiar++){
		$IDCodificacion = "IDCodificacion".$item_cambiar;
		$Cantidad = "Cantidad".$item_cambiar;
		$ValorU = "ValorU".$item_cambiar;
		$PrecioU = "PrecioU".$item_cambiar;
		$DescuentoRef = "DescuentoLin".$item_cambiar;

		$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = Existencias + '$frm[$Cantidad]' WHERE IDCodificacionEspecifica = '$frm[$IDCodificacion]'";
		db_query( $str_actualiza_inventario );
		//insertar el log
		insertlog($ID_Usuario,"CodificacionEspecifica",$frm[$IDCodificacion],"Insertar",$str_actualiza_inventario);
		$str_insert_detalle_producto  = "INSERT INTO DetalleProductoCambio ( IDCambio,IDPuntoVenta,IDCodificacionEspecificaCambio,IDCodificacionEspecifica,Cantidad,ValorU,PrecioU,DescuentoRef,UsuarioTrCr,FechaTrCr ) ";
		$str_insert_detalle_producto .= "VALUES ( '".$frm["IDCambio"]."','".$frm["IDPuntoVenta"]."','".$frm[$IDCodificacion]."','".$frm[$IDCodificacion]."','".$frm[$Cantidad]."','".$frm[$ValorU]."','".$frm[$PrecioU]."','".$frm[$DescuentoRef]."','".$frm["UsuarioTrCr"]."','".$frm["FechaTrCr"]."' )";
		db_query( $str_insert_detalle_producto );
	}

	//$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = Existencias + '$frm[$Cantidad]' WHERE IDCodificacionEspecifica = '$frm[$IDCodificacion]'";
	//db_query( $str_actualiza_inventario );


	//Disminuir inventario
	//$IDCodificacion = "IDCodificacion1";
	//$Cantidad = "Cantidad1";

	//$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = Existencias - '$frm[$Cantidad]' WHERE IDCodificacionEspecifica = '$frm[$IDCodificacion]'";

	//db_query( $str_actualiza_inventario );

	//insertar el log
	insertlog($ID_Usuario,"CodificacionEspecifica",$frm[$IDCodificacion],"Insertar",$str_actualiza_inventario);


	$frm['ValorIVA'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['ValorIVA']);

	$frm['ValorTotal'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['ValorTotal']);

	$sql_actualizaFacturaBono = "UPDATE Cambio SET  ValorTotal = '".$frm["ValorTotal"]."' WHERE IDCambio = '".$frm["RegistroCambio"]."'";
	db_query($sql_actualizaFacturaBono);

	//insertar el log
	insertlog($ID_Usuario,"Cambio",$frm['RegistroCambio'],"Actualizar",$sql_actualizaFacturaBono);

	$frm['Excedente'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['Excedente']);
	if( $frm['Excedente'] > 0 )
	{
		//INSERTAR FACTURA CON EL EXCEDENTE

		$Resolucion = get_field("PuntoVenta","Resolucion","IDPuntoVenta",$frm['IDPuntoVenta']);
		$RDesde = get_field("PuntoVenta","RDesde","IDPuntoVenta",$frm['IDPuntoVenta']);
		$RHasta = get_field("PuntoVenta","RHasta","IDPuntoVenta",$frm['IDPuntoVenta']);

		$idfactura = get_maxID( "Factura WHERE IDPuntoVenta = '".$frm["IDPuntoVenta"]."'","IDFactura" );


	   $sql_facturas = "Select IDFactura From Factura WHERE IDPuntoVenta = '".$frm["IDPuntoVenta"]."' and FechaFactura >='2021-12-01 09:00:00' Limit 1";
	   $qry_facturas = db_query($sql_facturas);
	   $row_facturas = db_fetch_array($qry_facturas);
	   $ultima_fac = (int)$row_facturas["IDFactura"];
	   if($ultima_fac==0):
			$numerofactura=1;
	   else:
			$numerofactura=get_maxID("Factura WHERE IDPuntoVenta = '".$frm["IDPuntoVenta"]."' and FechaFactura >='2021-12-01 09:00:00'","NumeroFactura");
	   endif;

		//$numerofactura = get_maxID( "Factura WHERE IDPuntoventa = '$frm['IDPuntoVenta']' ","NumeroFactura" );
		$sql_insert_factura = " INSERT INTO Factura ( IDFactura, IDCliente, NumeroFactura, IDPuntoVenta, IDEmpleado, FechaFactura, FechaCreacion, ValorIVA,
								ValorTotal, Resolucion, RDesde, RHasta, UsuarioTrCr, FechaTrCr )
								VALUES ( '$idfactura','".$frm["IDCliente"]."','$numerofactura','".$frm["IDPuntoVenta"]."','".$frm["IDEmpleado"]."',NOW(),NOW(),'".$frm["ValorIVA"]."','".$frm["Excedente"]."',
								'$Resolucion','$RDesde','$RHasta','".$frm["UsuarioTrCr"]."','".$frm["FechaTrCr"]."' ) ";

		db_query( $sql_insert_factura );

		//insertar el log
		insertlog($ID_Usuario,"Factura",$idfactura,"Insertar",$sql_insert_factura);

		//INSERTAR DETALLE FACTURA
		//se agrega el excedente como una referencia - el id de la referencia excedente esta guardada en la tabla parametros IDParametro = 1

		$IDExcedente = (int)get_field("Parametros", "Parametro", "IDParametro", 1);
		$IDCod = 0;
		if ($IDExcedente > 0) {
			$sql_cod_excedente = "SELECT C.IDCodificacionEspecifica
				FROM CodificacionEspecifica C
				INNER JOIN PuntoVentaReferencia PVR ON C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
				WHERE PVR.IDReferencia = '" . $IDExcedente . "'
				AND PVR.IDPuntoVenta = '" . (int)$frm["IDPuntoVenta"] . "'
				LIMIT 1";
			$qry_cod_excedente = db_query($sql_cod_excedente, false, true, true);
			if ($qry_cod_excedente && db_num_rows($qry_cod_excedente) > 0) {
				$row_cod_excedente = db_fetch_array($qry_cod_excedente);
				$IDCod = (int)$row_cod_excedente["IDCodificacionEspecifica"];
			}
		}
		$ValorU = $frm['Excedente'] / ( 1 + $IVA );
		$sql_detalle = " INSERT INTO DetalleFactura (IDDetalleFactura, IDFactura,IDPuntoVenta, IDCodificacionEspecifica, Cantidad, ValorU, PrecioU)
							VALUES ( '1','$idfactura','".$frm["IDPuntoVenta"]."','$IDCod','1','$ValorU','".$frm["Excedente"]."' ) ";

		db_query( $sql_detalle );

		//insertar el log
		insertlog($ID_Usuario,"DetalleFactura",$idfactura,"Insertar",$sql_detalle);

		//ACTUALIZAR FACTURA BONO CON EL ID DE LA FACTURA
		$sql_actualizafacturaBono = "UPDATE Cambio SET IDFactura = '$idfactura' WHERE IDCambio = '".$frm["RegistroCambio"]."' AND IDPuntoVenta = '".$frm["IDPuntoVenta"]."' ";
		db_query( $sql_actualizafacturaBono );

		//insertar el log
		insertlog($ID_Usuario,"FacturaBono",$frm['IDFacturaBono'],"Insertar",$sql_actualizafacturaBono);


		$frm['IDFactura'] = $idfactura;
		//ACTUALIZAR VENTAS VENDEDOR
		agregarventaempleadobono($frm);



	}//end if $frm['Excedente']

	return $frm;

}//end function venta( $frm )


function ventacambioant( $frm )
{
	Global $Nombre_Usuario, $ID_Usuario, $Table, $Key, $mod, $IVA;

	//Insertar los Items en la tabla de detalles

	$Items = $frm['ITEM'];

	//print_r( $frm );
	//exit;


	if( $mod == "cambioreferenciaanterior" )
		$iniciar = 3;
	else
		$iniciar = 1;
	 $iniciar;


	for($i = $iniciar; $i <= $Items; $i++)
	{
		//echo  $iddetalle = get_maxID("DetalleCambio WHERE IDCambio = '$frm['IDCambio']' AND IDPuntoVenta = '$frm['IDPuntoVenta']' ","IDDetalleCambio");
		$iddetalle = $i;
		echo "<br>";
		$IDCodificacion = "IDCodificacion".$i;

		$IDCodificacionCambio = "IDCodificacion2";
		$Cantidad = "Cantidad".$i;
		$ValorU = "ValorU".$i;
		$PrecioU = "Precio".$i;
		$DescuentoRef = "Descuento".$i;

		$ValorU = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm[$ValorU]);
		$PrecioU = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm[$PrecioU]).".00";
		$DescuentoRef = $frm[$DescuentoRef];

		if( !empty( $frm[$Cantidad] ) )
		{
			 $str_insert_detalle  = "INSERT INTO DetalleCambio ( IDDetalleCambio,IDCambio,IDPuntoVenta,IDCodificacionEspecificaCambio,IDCodificacionEspecifica,Cantidad,ValorU,PrecioU,DescuentoRef,UsuarioTrCr,FechaTrCr ) ";
			 echo $str_insert_detalle .= "VALUES ( '$iddetalle','".$frm["IDCambio"]."','".$frm["IDPuntoVenta"]."','".$frm[$IDCodificacionCambio]."','".$frm[$IDCodificacion]."','".$frm[$Cantidad]."','$ValorU','$PrecioU','$DescuentoRef','".$frm["UsuarioTrCr"]."','".$frm["FechaTrCr"]."' )";


			db_query( $str_insert_detalle );

			$str_insert_detalle = "";

			//descargar de inventario
			if( $i >= 3 )
			{
				 $str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = Existencias - '$frm[$Cantidad]' WHERE IDCodificacionEspecifica = '$frm[$IDCodificacion]'";

				db_query( $str_actualiza_inventario );
			}
			//insertar el log
			insertlog($ID_Usuario,"DetalleCambio",$iddetalle,"Insertar",$str_insert_detalle);
		}//end if
	}//end for($i = 1; $i < $Items; $i++)
	//exit;
	//Realizar el Movimiento correspondiente en la tabla de Codificacion especifica
	//Aumentar inventario
	$IDCodificacion = "IDCodificacion2";
	$Cantidad = "Cantidad2";

	$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = Existencias + '$frm[$Cantidad]' WHERE IDCodificacionEspecifica = '$frm[$IDCodificacion]'";

	db_query( $str_actualiza_inventario );




	//insertar el log
	insertlog($ID_Usuario,"CodificacionEspecifica",$frm[$IDCodificacion],"Insertar",$str_actualiza_inventario);

	//Disminuir inventario
	//$IDCodificacion = "IDCodificacion1";
	//$Cantidad = "Cantidad1";

	//$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = Existencias - '$frm[$Cantidad]' WHERE IDCodificacionEspecifica = '$frm[$IDCodificacion]'";

	//db_query( $str_actualiza_inventario );

	//insertar el log
	insertlog($ID_Usuario,"CodificacionEspecifica",$frm[$IDCodificacion],"Insertar",$str_actualiza_inventario);


	$frm['ValorIVA'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['ValorIVA']);

	$frm['ValorTotal'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['ValorTotal']);

	$sql_actualizaFacturaBono = "UPDATE Cambio SET  ValorTotal = '".$frm["ValorTotal"]."' WHERE IDCambio = '".$frm["RegistroCambio"]."'";
	db_query($sql_actualizaFacturaBono);

	//insertar el log
	insertlog($ID_Usuario,"Cambio",$frm['RegistroCambio'],"Actualizar",$sql_actualizaFacturaBono);

	$frm['Excedente'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['Excedente']);
	if( $frm['Excedente'] > 0 )
	{
		//INSERTAR FACTURA CON EL EXCEDENTE

		$Resolucion = get_field("PuntoVenta","Resolucion","IDPuntoVenta",$frm['IDPuntoVenta']);
		$RDesde = get_field("PuntoVenta","RDesde","IDPuntoVenta",$frm['IDPuntoVenta']);
		$RHasta = get_field("PuntoVenta","RHasta","IDPuntoVenta",$frm['IDPuntoVenta']);

		$idfactura = get_maxID( "Factura WHERE IDPuntoVenta = '".$frm["IDPuntoVenta"]."'","IDFactura" );


		$sql_facturas = "Select IDFactura From Factura WHERE IDPuntoVenta = '".$frm["IDPuntoVenta"]."' and FechaFactura >='2021-12-01 09:00:00' Limit 1";
		$qry_facturas = db_query($sql_facturas);
		$row_facturas = db_fetch_array($qry_facturas);
		$ultima_fac = (int)$row_facturas["IDFactura"];
		if($ultima_fac==0):
		 $numerofactura=5000;
		else:
		 $numerofactura=get_maxID("Factura WHERE IDPuntoVenta = '".$frm["IDPuntoVenta"]."' and FechaFactura >='2021-12-01 09:00:00'","NumeroFactura");
		endif;

		$sql_insert_factura = " INSERT INTO Factura ( IDFactura, IDCliente, NumeroFactura, IDPuntoVenta, IDEmpleado, FechaFactura, ValorIVA,
								ValorTotal, Resolucion, RDesde, RHasta, UsuarioTrCr, FechaTrCr )
								VALUES ( '$idfactura','".$frm["IDCliente"]."','$numerofactura','".$frm["IDPuntoVenta"]."','".$frm["IDEmpleado"]."',NOW(),'".$frm["ValorIVA"]."','".$frm["Excedente"]."',
								'$Resolucion','$RDesde','$RHasta','".$frm["UsuarioTrCr"]."','".$frm["FechaTrCr"]."' ) ";

		db_query( $sql_insert_factura );

		//insertar el log
		insertlog($ID_Usuario,"Factura",$idfactura,"Insertar",$sql_insert_factura);

		//INSERTAR DETALLE FACTURA
		//se agrega el excedente como una referencia - el id de la referencia excedente esta guardada en la tabla parametros IDParametro = 1

		$IDExcedente = (int)get_field("Parametros", "Parametro", "IDParametro", 1);
		$IDCod = 0;
		if ($IDExcedente > 0) {
			$sql_cod_excedente = "SELECT C.IDCodificacionEspecifica
				FROM CodificacionEspecifica C
				INNER JOIN PuntoVentaReferencia PVR ON C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
				WHERE PVR.IDReferencia = '" . $IDExcedente . "'
				AND PVR.IDPuntoVenta = '" . (int)$frm["IDPuntoVenta"] . "'
				LIMIT 1";
			$qry_cod_excedente = db_query($sql_cod_excedente, false, true, true);
			if ($qry_cod_excedente && db_num_rows($qry_cod_excedente) > 0) {
				$row_cod_excedente = db_fetch_array($qry_cod_excedente);
				$IDCod = (int)$row_cod_excedente["IDCodificacionEspecifica"];
			}
		}
		$ValorU = $frm['Excedente'] / ( 1 + $IVA );
		$sql_detalle = " INSERT INTO DetalleFactura (IDDetalleFactura, IDFactura,IDPuntoVenta, IDCodificacionEspecifica, Cantidad, ValorU, PrecioU)
							VALUES ( '1','$idfactura','".$frm["IDPuntoVenta"]."','$IDCod','1','$ValorU','".$frm["Excedente"]."' ) ";

		db_query( $sql_detalle );

		//insertar el log
		insertlog($ID_Usuario,"DetalleFactura",$idfactura,"Insertar",$sql_detalle);

		//ACTUALIZAR FACTURA BONO CON EL ID DE LA FACTURA
		$sql_actualizafacturaBono = "UPDATE Cambio SET IDFactura = '$idfactura' WHERE IDCambio = '".$frm["RegistroCambio"]."' AND IDPuntoVenta = '".$frm["IDPuntoVenta"]."' ";
		db_query( $sql_actualizafacturaBono );

		//insertar el log
		insertlog($ID_Usuario,"FacturaBono",$frm['IDFacturaBono'],"Insertar",$sql_actualizafacturaBono);


		$frm['IDFactura'] = $idfactura;
		//ACTUALIZAR VENTAS VENDEDOR
		agregarventaempleadobono($frm);



	}//end if $frm['Excedente']

	return $frm;

}//end function venta( $frm )


/*******************************************************************************************
	Libreria de funciones para el Sistema de Puntos de Venta de Caprino - Salidas de MErcancia
	Creador por: John Escobar
	Iniciado: Mayo 28/2013
	Ultima Modificaci?n: Mayo 28/2013
*******************************************************************************************/

/*******************************************************************************************
	ventacambiofactura: Realiza todos los movimiento necesarios cuando se hace una venta con bonos en el punto de venta.
	Parametros:
			$frm: array con los datos de venta
	Retorna:
			Void
*******************************************************************************************/
function ventacambiofactura( $frm )
{
	Global $Nombre_Usuario, $ID_Usuario, $Table, $Key, $mod, $IVA;

	//Insertar los Items en la tabla de detalles

	$Items = $frm['ITEM'];


	//hacer los dos bloques entrada 2 - 7
	for( $i = 2; $i <= 7; $i++ )
	{
		$iddetalle = $i;
		$IDCodificacion = "IDCodificacion".$i;

		$IDCodificacionCambio = "IDCodificacion2";
		$Cantidad = "Cantidad".$i;
		$ValorU = "ValorU".$i;
		$PrecioU = "Precio".$i;
		$DescuentoRef = "Descuento".$i;

		$ValorU = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm[$ValorU]);
		$PrecioU = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm[$PrecioU]).".00";
		$DescuentoRef = $frm[$DescuentoRef];

		if( !empty( $frm[$Cantidad] ) )
		{
			 $str_insert_detalle  = "INSERT INTO DetalleCambioFacturaEntrada ( IDDetalleCambioFacturaEntrada,IDCambioFactura,IDPuntoVenta,IDCodificacionEspecifica,Cantidad,ValorU,PrecioU,DescuentoRef,UsuarioTrCr,FechaTrCr ) ";
			 $str_insert_detalle .= "VALUES ( '$iddetalle','".$frm["IDCambioFactura"]."','".$frm["IDPuntoVenta"]."','".$frm[$IDCodificacion]."','".$frm[$Cantidad]."','$ValorU','$PrecioU','$DescuentoRef','".$frm["UsuarioTrCr"]."','".$frm["FechaTrCr"]."' )";


			db_query( $str_insert_detalle );

			$str_insert_detalle = "";

			//descargar de inventario
			if( $i >= 3 )
			{
				$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = Existencias + '$frm[$Cantidad]' WHERE IDCodificacionEspecifica = '$frm[$IDCodificacion]'";

				db_query( $str_actualiza_inventario );
			}
		}//end if
	}//end for

	//hacer los dos bloques salida 2 - 7
	for( $i = 8; $i <= 13; $i++ )
	{
		$iddetalle = $i;
		$IDCodificacion = "IDCodificacion".$i;

		$IDCodificacionCambio = "IDCodificacion2";
		$Cantidad = "Cantidad".$i;
		$ValorU = "ValorU".$i;
		$PrecioU = "Precio".$i;
		$DescuentoRef = "Descuento".$i;

		$ValorU = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm[$ValorU]);
		$PrecioU = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm[$PrecioU]).".00";
		$DescuentoRef = $frm[$DescuentoRef];

		if( !empty( $frm[$Cantidad] ) )
		{
			 $str_insert_detalle  = "INSERT INTO DetalleCambioFacturaSalida ( IDDetalleCambioFacturaSalida,IDCambioFactura,IDPuntoVenta,IDCodificacionEspecifica,Cantidad,ValorU,PrecioU,DescuentoRef,UsuarioTrCr,FechaTrCr ) ";
			 $str_insert_detalle .= "VALUES ( '$iddetalle','".$frm["IDCambioFactura"]."','".$frm["IDPuntoVenta"]."','".$frm[$IDCodificacion]."','".$frm[$Cantidad]."','$ValorU','$PrecioU','$DescuentoRef','".$frm["UsuarioTrCr"]."','".$frm["FechaTrCr"]."' )";


			db_query( $str_insert_detalle );

			$str_insert_detalle = "";

			//descargar de inventario
			if( $i >= 3 )
			{
				$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = Existencias - '$frm[$Cantidad]' WHERE IDCodificacionEspecifica = '$frm[$IDCodificacion]'";

				db_query( $str_actualiza_inventario );
			}
		}//end if
	}//end for

	//exit;
	//Realizar el Movimiento correspondiente en la tabla de Codificacion especifica
	//Aumentar inventario

	$frm['Excedente'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['Excedente']);
	if( $frm['Excedente'] > 0 )
	{
		//INSERTAR FACTURA CON EL EXCEDENTE

		$Resolucion = get_field("PuntoVenta","Resolucion","IDPuntoVenta",$frm['IDPuntoVenta']);
		$RDesde = get_field("PuntoVenta","RDesde","IDPuntoVenta",$frm['IDPuntoVenta']);
		$RHasta = get_field("PuntoVenta","RHasta","IDPuntoVenta",$frm['IDPuntoVenta']);

		$idfactura = get_maxID( "Factura WHERE IDPuntoVenta = '".$frm['IDPuntoVenta']."'","IDFactura" );


		//$numerofactura = get_maxID( "Factura WHERE IDPuntoventa = '$frm['IDPuntoVenta']' ","NumeroFactura" );
	   $sql_facturas = "Select IDFactura From Factura WHERE IDPuntoVenta = '".$frm['IDPuntoVenta']."' and FechaFactura >='2021-12-01 09:00:00' Limit 1";
	   $qry_facturas = db_query($sql_facturas);
	   $row_facturas = db_fetch_array($qry_facturas);
	   $ultima_fac = (int)$row_facturas["IDFactura"];
	   if($ultima_fac==0):
			$numerofactura=5000;
	   else:
			$numerofactura=get_maxID("Factura WHERE IDPuntoVenta = '".$frm['IDPuntoVenta']."' and FechaFactura >='2021-12-01 09:00:00'","NumeroFactura");
	   endif;



		$sql_insert_factura = " INSERT INTO Factura ( IDFactura, IDCliente, NumeroFactura, IDPuntoVenta, IDEmpleado, FechaFactura, ValorIVA,
								ValorTotal, Resolucion, RDesde, RHasta, UsuarioTrCr, FechaTrCr )
								VALUES ( '$idfactura','".$frm["IDCliente"]."','$numerofactura','".$frm["IDPuntoVenta"]."','".$frm["IDEmpleado"]."',NOW(),'".$frm["ValorIVA"]."','".$frm["Excedente"]."',
								'$Resolucion','$RDesde','$RHasta','".$frm["UsuarioTrCr"]."','".$frm["FechaTrCr"]."' ) ";

		db_query( $sql_insert_factura );


		//INSERTAR DETALLE FACTURA
		//se agrega el excedente como una referencia - el id de la referencia excedente esta guardada en la tabla parametros IDParametro = 1

		$IDExcedente = (int)get_field("Parametros", "Parametro", "IDParametro", 1);
		$IDCod = 0;
		if ($IDExcedente > 0) {
			$sql_cod_excedente = "SELECT C.IDCodificacionEspecifica
				FROM CodificacionEspecifica C
				INNER JOIN PuntoVentaReferencia PVR ON C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
				WHERE PVR.IDReferencia = '" . $IDExcedente . "'
				AND PVR.IDPuntoVenta = '" . (int)$frm['IDPuntoVenta'] . "'
				LIMIT 1";
			$qry_cod_excedente = db_query($sql_cod_excedente, false, true, true);
			if ($qry_cod_excedente && db_num_rows($qry_cod_excedente) > 0) {
				$row_cod_excedente = db_fetch_array($qry_cod_excedente);
				$IDCod = (int)$row_cod_excedente["IDCodificacionEspecifica"];
			}
		}
		$ValorU = $frm['Excedente'] / ( 1 + $IVA );
		$sql_detalle = " INSERT INTO DetalleFactura (IDDetalleFactura, IDFactura,IDPuntoVenta, IDCodificacionEspecifica, Cantidad, ValorU, PrecioU)
							VALUES ( '1','$idfactura','".$frm['IDPuntoVenta']."','$IDCod','1','$ValorU','".$frm['Excedente']."' ) ";

		db_query( $sql_detalle );


		//ACTUALIZAR FACTURA BONO CON EL ID DE LA FACTURA
		$sql_actualizafacturaBono = "UPDATE CambioFactura SET IDFactura = '$idfactura' WHERE IDCambioFactura = '".$frm['RegistroCambio']."' AND IDPuntoVenta = '".$frm['IDPuntoVenta']."' ";
		db_query( $sql_actualizafacturaBono );

		$frm['IDFactura'] = $idfactura;


		//ACTUALIZAR VENTAS VENDEDOR
		//agregarventaempleadobono($frm);



	}//end if $frm['Excedente']

	return $frm;

}//end function venta( $frm )



/*******************************************************************************************
	agregarventaempleadobono:Agrega un regisro a la tabla de ventas empleados necesaria para el calculo de la comision.
			$frm: array con los datos de salida de mercancia
	Retorna:
			Void
*******************************************************************************************/
function agregarventaempleadobono( $frm )
{
	//print_r($frm);
	$administrador = get_field("PuntoVenta","IDEmpleado","IDEmpleado",$frm['IDEmpleado']);
	if($administrador <> "")
		$cargo = "Administrador";
	else
		$cargo = "Empleado";

	$frm['Excedente'] = preg_replace("/[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]/","",$frm['Excedente']);

	$idadministrador = get_field("PuntoVenta","IDEmpleado","IDPuntoVenta",$frm['IDPuntoVenta']);
	$idventaempleado = get_maxID("VentasEmpleado","IDVentasEmpleado");
	$sql_ventaadministrador = "INSERT INTO VentasEmpleado VALUES ('$idventaempleado','$idadministrador','Administrador','".$frm['IDPuntoVenta']."','".$frm['IDFactura']."','".$frm['Excedente']."')";
	$queryventaadministrador = db_query($sql_ventaadministrador);

	if($cargo == "Empleado")
	{
		$idventaempleado = get_maxID("VentasEmpleado","IDVentasEmpleado");
		$sql_ventaempleado = "INSERT INTO VentasEmpleado VALUES ('$idventaempleado','".$frm['IDEmpleado']."','$cargo','".$frm['IDPuntoVenta']."','".$frm['IDFactura']."','".$frm['Excedente']."')";
		$queryventaempleado = db_query($sql_ventaempleado);
	}
}
/*******************************************************************************************
	salidamercancia:Realiza la salida de mercancia de un punto de venta por otros conceptos
	que no son la venta.
			$frm: array con los datos de salida de mercancia
	Retorna:
			Void
*******************************************************************************************/
function salidamercancia( $frm )
{
	Global $Nombre_Usuario, $ID_Usuario, $IDPuntoVenta;

	//Insertar los Items en la tabla de detalles

	$Items = $frm['ITEM'];

	for($i = 1; $i <= $Items; $i++)
	{

		$iddetalle = get_maxID("DetalleMovimiento WHERE IDMovimiento = '".$frm['IDMovimiento']."' ","IDDetalleMovimiento");
		$IDCodificacion = "IDCodificacion".$i;
		$Cantidad = "Cantidad".$i;
		$IDPuntoVentaReferencia = get_field( "CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica", $frm[$IDCodificacion]);
		$IDTalla = get_field( "CodificacionEspecifica","IDTalla","IDCodificacionEspecifica", $frm[$IDCodificacion]);

		$str_insert_detalle  = "INSERT INTO DetalleMovimiento ( IDDetalleMovimiento,IDMovimiento,IDPuntoVentaReferencia,IDPuntoVenta, IDTalla,Cantidad,UsuarioTrCr,FechaTrCr ) ";
		$str_insert_detalle .= "VALUES ( '$iddetalle','".$frm['IDMovimiento']."','$IDPuntoVentaReferencia','".$frm['IDPuntoVenta']."','$IDTalla','".$frm[$Cantidad]."','".$frm['UsuarioTrCr']."','".$frm['FechaTrCr']."' )";
		//echo $str_insert_detalle .= "<br>";

		db_query( $str_insert_detalle );

		//insertar el log
		insertlog($ID_Usuario,"DetalleMovimiento",$iddetalle,"Insertar",$str_insert_detalle);

	}//end for($i = 1; $i < $Items; $i++)

	//Realizar el Movimiento correspondiente en la tabla de Codificacion especifica

	for($i = 1; $i <= $Items; $i++)
	{

		$IDCodificacion = "IDCodificacion".$i;
		$Cantidad = "Cantidad".$i;
		$existencias = get_field( "CodificacionEspecifica","Existencias","IDCodificacionEspecifica", $frm[$IDCodificacion]);
		$existencias = is_numeric($existencias) ? (float)$existencias : 0;
		$cantidad_valor = isset($frm[$Cantidad]) && is_numeric($frm[$Cantidad]) ? (float)$frm[$Cantidad] : 0;
		$existencias = $existencias - $cantidad_valor;

		$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDCodificacionEspecifica = '".$frm[$IDCodificacion]."'";
		//echo $str_actualiza_inventario .= "<br>";

		db_query( $str_actualiza_inventario );

		//insertar el log
		insertlog($ID_Usuario,"CodificacionEspecifica",$frm[$IDCodificacion],"Insertar",$str_actualiza_inventario);

	}//end for($i = 1; $i < $Items; $i++)

}//end function salidamercancia( $frm )

/*******************************************************************************************
	salidamercancia_tercero:Realiza la salida de mercancia de un punto de venta por devolucion
	en los pedidos de terceros
*******************************************************************************************/

function salidamercancia_tercero( $array_sql_devueltos_id, $IDPedidoTercero )
{
	Global $Nombre_Usuario, $ID_Usuario, $IDPuntoVenta;

		if(count($array_sql_devueltos_id)>0):
			//Inserto el encabezado del movimiento
			$id_movimiento_next = get_maxID("Movimiento","IDMovimiento");
			$sql_movimiento = "Insert into Movimiento (IDMovimiento, IDTipoMovimiento, IDPuntoVenta, Fecha, IDEmpleado, Observaciones, UsuarioTrCr, FechaTrCr)
											Values('".$id_movimiento_next."','10','".$IDPuntoVenta."',NOW(),'".$ID_Usuario."','Pedido tercero $IDPedidoTercero','".$Nombre_Usuario."',NOW())";
			db_query($sql_movimiento);



			//Inserto el detalle del movimiento
			foreach($array_sql_devueltos_id as $IDDetalleTercero):
				// Datos del item del pedido
				$sql_detalle_tercero = "Select * From DetallePedidoTerceroReferencia Where IDDetallePedidoTerceroReferencia = '".$IDDetalleTercero."'";
				$qry_detalle_tercero = db_query($sql_detalle_tercero);
				$r_detalle_tercero = db_fetch_array($qry_detalle_tercero);

				$sql_pedido_tercero = "Select * From DetallePedidoTercero Where IDDetallePedidoTercero = '".$r_detalle_tercero["IDDetallePedidoTercero"]."'";
				$qry_pedido_tercero = db_query($sql_pedido_tercero);
				$r_pedido_tercero = db_fetch_array($qry_pedido_tercero);

				$referencia = $r_pedido_tercero["ReferenciaCaprino"].$r_pedido_tercero["CodigoColor"];
				//verifico que exista la referencia
				$id_referencia =  get_field("Referencia","IDReferencia","Nombre",$referencia);
				if(!empty($id_referencia)):
					 $sql =  db_query("SELECT * FROM  Referencia R, PuntoVentaReferencia PR
							WHERE  PR.IDPuntoVenta = '$IDPuntoVenta'
							AND PR.IDReferencia = R.IDReferencia
							AND PR.IDReferencia = '".$id_referencia."'
							ORDER BY R.Numero ASC");

					$row_punto_ref = db_fetch_array($sql);
					$IDPuntoVentaReferencia = $row_punto_ref["IDPuntoVentaReferencia"];
				endif;


				//Insertar los Items en la tabla de detalles
				$iddetalle = get_maxID("DetalleMovimiento WHERE IDMovimiento = '".$id_movimiento_next."' ","IDDetalleMovimiento");
				$str_insert_detalle  = "INSERT INTO DetalleMovimiento ( IDDetalleMovimiento,IDMovimiento,IDPuntoVentaReferencia,IDPuntoVenta, IDTalla,Cantidad,UsuarioTrCr,FechaTrCr ) ";
				$str_insert_detalle .= "VALUES ( '".$iddetalle."','".$id_movimiento_next."','".$IDPuntoVentaReferencia."','".$r_detalle_tercero["IDPuntoVenta"]."','".$r_detalle_tercero["IDTalla"]."','".$r_detalle_tercero["CantidadDevuelto"]."','".$ID_Usuario."',NOW() )";
				//echo $str_insert_detalle .= "<br>";
				db_query( $str_insert_detalle );

				//insertar el log
				insertlog($ID_Usuario,"DetalleMovimiento",$iddetalle,"Insertar",$str_insert_detalle);


				unset($array_tallas_rel);

				//Consulto las otra tallas posibles ya que una talla esta creada mas de una vez
				$nombre_talla = get_field("Talla","Descripcion","IDTalla",$r_detalle_tercero["IDTalla"]);
				$sql_tallas_rel = db_query("Select * From Talla Where Descripcion = '".$nombre_talla."'");
				while($row_talla = db_fetch_array($sql_tallas_rel)):
					$array_tallas_rel []=$row_talla[IDTalla];
				endwhile;

				if (count($array_tallas_rel)>0):
					$id_tallas_rel = implode(",",$array_tallas_rel);
				endif;

				//Realizar el Movimiento correspondiente en la tabla de Codificacion especifica
				$sql_existencia = "Select * From CodificacionEspecifica Where IDPuntoVentaReferencia='".$IDPuntoVentaReferencia."' AND IDTalla in ($id_tallas_rel) ";
				$qry_existencia = db_query($sql_existencia);
				$row_existencia=db_fetch_array($qry_existencia);
				if (db_num_rows($qry_existencia)>0):
					$existencias = get_field( "CodificacionEspecifica","Existencias","IDCodificacionEspecifica", $row_existencia["IDCodificacionEspecifica"]);
					if((int)$existencias>0):
							$existencias = $existencias - $r_detalle_tercero["CantidadDevuelto"];
							$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDCodificacionEspecifica = '".$row_existencia["IDCodificacionEspecifica"]."'";
							//echo $str_actualiza_inventario .= "<br>";
							db_query( $str_actualiza_inventario );
							//insertar el log
							insertlog($ID_Usuario,"CodificacionEspecifica",'".$row_existencia["IDCodificacionEspecifica"]."',"Insertar",$str_actualiza_inventario);
					endif;
				endif;

			endforeach;



	endif;

}//end function salidamercancia( $frm )

?>
