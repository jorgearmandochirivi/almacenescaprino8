<?php
	include("../config.inc.php");
	Encabezado();

	function sanitizeTabReturn( $str )
    {
        return preg_replace( '/[\n\r\t]+/', '', $str);
    }

  $sql_garantias = $_GET[sql];
	$sql_garantias=str_replace("ORDER BY"," ORDER BY ",$sql_garantias);
	$sql_garantias=str_replace("Group By"," Group By ",$sql_garantias);

	$now_date = date('m-d-Y H:i');
	$result = db_query($sql_garantias);
	$title = "Datos Reporte Garantias Fecha $now_date";
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

		echo "Numero" . $sep;
		echo "Tipo" . $sep;
		echo "Estado" . $sep;
		echo "Cantidad Veces" . $sep;
		echo "Fecha Ingreso garantia" . $sep;
		echo "Fecha estimada de entrega" . $sep;
		echo "Cliente" . $sep;
		//echo "Cedula Cliente" . $sep;
		//echo "Telefono Cliente" . $sep;
		//echo "Celular Cliente" . $sep;
		//echo "Direccion Cliente" . $sep;
		echo "Club Suavidad" . $sep;
		echo "Almacen Recibe Garantia" . $sep;
		echo "Atendido por" . $sep;
		echo "Referencia Producto" . $sep;
		echo "Talla" . $sep;
		echo "Tipo" . $sep;
		echo "Proveedor" . $sep;
		echo "Remonta" . $sep;
		echo "NumeroFacturaRemonta" . $sep;
		echo "ValorRemonta" . $sep;
		echo "PagoRemonta" . $sep;
		echo "Requiere Nota Credito" . $sep;
		echo "Numero Nota Credito" . $sep;
		echo "Fecha enviada Contabilidad Nota Credito" . $sep;
		echo "Nota a proveedor Aplicada" . $sep;
		echo "Numero Orden Produccion" . $sep;

		echo "Basica" . $sep;
		echo "ValorBasica" . $sep;
		echo "Premium" . $sep;
		echo "ValorPremium" . $sep;
		echo "Nro Factura Restauracion" . $sep;



		echo "Contrafuerte" . $sep;
		echo "Cuero" . $sep;
		echo "Plantilla Est" . $sep;
		echo "Cremallera" . $sep;
		echo "Herraje" . $sep;
		echo "Despegue" . $sep;
		echo "Cambrion" . $sep;
		echo "Tacon" . $sep;
		echo "Cerco" . $sep;
		echo "Cardado" . $sep;
		echo "Suela" . $sep;
		echo "Guarnicion" . $sep;
		echo "Puntera" . $sep;
		echo "Otro" . $sep;
		echo "Descripcion Otro" . $sep;

		echo "Fecha Salida Almacen" . $sep;
		echo "FechaEnviada Reparacion" . $sep;
		echo "Fecha Enviada Almacen" . $sep;
		echo "Fecha Autorizacion Especial " . $sep;
		echo "Descripcion Autorizacion Especial " . $sep;
		echo "Fecha Entrada Almacen" . $sep;
		echo "Fecha Entrega Cliente" . $sep;
		echo "Descripcion" . $sep;

		print("\n");
	//start while loop to get data
		while($row = db_fetch_array($result))
		{

		  if ($row["TipoFactura"]=="facturabono"):
		  	$sql_datos_factura=db_query("Select * From FacturaBono Where IDFacturaBono = '".$row["IDFactura"]."' and IDPuntoVenta = '".$row["IDPuntoVentaFactura"]."'");
		  else:
			$sql_datos_factura=db_query("Select * From Factura Where IDFactura = '".$row["IDFactura"]."' and IDPuntoVenta = '".$row["IDPuntoVentaFactura"]."'");
		  endif;

		  $r_factura=db_fetch_array($sql_datos_factura);


			echo sanitizeTabReturn($row["IDGarantia"]) . $sep;
			echo sanitizeTabReturn($row["TipoRegistro"]) . $sep;
			echo sanitizeTabReturn(get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$row["IDEstadoGarantia"])) . $sep;
			echo $row["CantidadVeces"] . $sep;
			echo substr($row["FechaTrCr"],0,10) . $sep;
			echo $row["FechaEstimadaEntrega"] . $sep;
			$id_cliente=$r_factura[IDCliente];
			if($row[Mayorista]=="S"){
				echo sanitizeTabReturn($row[NombreMayorista]) . $sep;
				//echo $row[IdentificacionMayorista	] . $sep;
				//echo $row[Telefono] . $sep;
				//echo $row[Celular] . $sep;
				//echo $row[CiudadMayorista] . $sep;
			}
			else{
				echo sanitizeTabReturn(get_field("Cliente","Nombre","IDCliente",$id_cliente) . " " .get_field("Cliente","Apellido","IDCliente",$id_cliente)) . $sep;
				//echo get_field("Cliente","Cedula","IDCliente",$id_cliente) . $sep;
				//echo get_field("Cliente","Telefono","IDCliente",$id_cliente) . $sep;
				//echo get_field("Cliente","Celular","IDCliente",$id_cliente) . $sep;
				//echo get_field("Cliente","Direccion","IDCliente",$id_cliente) . $sep;
			}


			echo sanitizeTabReturn(get_field("Cliente","ClubSuavidad","IDCliente",$id_cliente)) . $sep;
			echo sanitizeTabReturn(get_field("PuntoVenta","Nombre","IDPuntoVenta",$row["IDPuntoVenta"])) . $sep;
			if($row[Mayorista]=="S")
				echo $row[IngresadoPor] . $sep;
			else
				echo sanitizeTabReturn(get_field("Empleado","Nombre","IDEmpleado",$row["IDEmpleado"]) . " " . get_field("Empleado","Apellidos","IDEmpleado",$row["IDEmpleado"])) . $sep;
			//Producto
			if ($row["TipoFactura"]=="facturabono"):
				$sql_producto="select * from DetalleFacturaBono Where IDDetalleFacturaBono='".$row["IDDetalleFactura"]."' and IDFacturaBono = '".$row["IDFactura"]."' and IDPuntoVenta = '".$row["IDPuntoVentaFactura"]."'";
			else:
				$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$row["IDDetalleFactura"]."' and IDFactura = '".$row["IDFactura"]."' and IDPuntoVenta = '".$row["IDPuntoVentaFactura"]."'";
			endif;

			$qry_producto=db_query($sql_producto);
			$r_detalle=db_fetch_object($qry_producto);

			//REFERENCIA DEL PRODUCTO TALLA TIPO

					if($row[Mayorista]=="S"){
						echo sanitizeTabReturn($row[ColorMayorista]) . $sep;
					}
					elseif ($row[TipoRegistro]=="Reproceso"){
						echo sanitizeTabReturn(get_field("Referencia","Numero","IDReferencia",$row[IDReferencia])) . $sep;
						$id_proveedor_ref = get_field("Referencia","IDProveedor","IDReferencia",$row[IDReferencia]);
						$tallap=get_field("Talla","Descripcion","IDTalla",$row[IDTalla]);
						$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$row[IDReferencia]);
						$tipop= get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
					}
					elseif(!empty($row[IDDetalleFacturaBono])){
						$array_bono_detalle=explode("|",$row[IDDetalleFacturaBono]);
						if (count($array_bono_detalle)>0):
							$sql_bono=db_query("Select * From DetalleFacturaBono Where IDDetalleFacturaBono	 = '".$array_bono_detalle[1]."' and IDFacturaBono = '".$array_bono_detalle[0]."'");
							$r_bono=db_fetch_array($sql_bono);

							$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"])));
							$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
							$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
							$id_proveedor_ref = get_field("Referencia","IDProveedor","IDReferencia",$id_referencia_item);
							echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item) . $sep;
							$tipop=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
							$tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
						endif;


					}


					elseif(empty($row[IDDetalleCambio])){
						  $id_referencia_item="";
						  $id_punto_venta=$row[IDPuntoVentaFactura];

						  if ($row["TipoFactura"]=="facturabono"):
							$sql_producto="select * from DetalleFacturaBono Where IDDetalleFacturaBono='".$row[IDDetalleFactura]."' and IDFacturaBono = '".$row["IDFactura"]."' and IDPuntoVenta = '".$id_punto_venta."'";
						  else:
							$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$row[IDDetalleFactura]."' and IDFactura = '".$row[IDFactura]."' and IDPuntoVenta = '".$id_punto_venta."'";
						  endif;

						  //$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$row[IDDetalleFactura]."' and IDFactura = '".$row[IDFactura]."' and IDPuntoVenta = '".$id_punto_venta."'";
						  $qry_producto=db_query($sql_producto);
						  $r_detalle=db_fetch_object($qry_producto);
						  $id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
						  $tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica));


						if ($id_referencia_item==160){ // Cuando son excedentes consulto la referencia de la compra
							$sql_facturabono=db_query("Select * from FacturaBono Where IDFactura = '".$row[IDFactura]."' and IDPuntoVenta = '".$row[IDPuntoVentaFactura]."'");
							$r_facturabono=db_fetch_array($sql_facturabono);
							if (!empty($r_facturabono[IDFacturaBono])){
								$sql_detallefacturabono=db_query("Select * from DetalleFacturaBono Where IDFacturaBono = '".$r_facturabono[IDFacturaBono]."'");
								$r_detallefacturabono=db_fetch_array($sql_detallefacturabono);
								$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));
								$tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"]));
							}
						  }



						  $id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
						  echo get_field("Referencia","Nombre","IDReferencia",$id_referencia_item) . $sep;
						  $id_proveedor_ref = get_field("Referencia","IDProveedor","IDReferencia",$id_referencia_item);
						  $tipop=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
					}
					else{
						$array_cambio_detalle=explode("|",$row[IDDetalleCambio]);
						if (count($array_cambio_detalle)>0):
							$sql_cambio=db_query("Select * From DetalleCambio Where IDDetalleCambio = '".$array_cambio_detalle[1]."' and IDCambio = '".$array_cambio_detalle[0]."'");
							$r_cambio=db_fetch_array($sql_cambio);

							$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"])));
							$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"]));
							$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
							$id_proveedor_ref = get_field("Referencia","IDProveedor","IDReferencia",$id_referencia_item);
							echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item) . $sep;
							$tipop=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
						endif;

					}


					if($row[Mayorista]=="S"){
						echo $tallap=get_field("Talla","Descripcion","IDTalla",$row[IDTalla]) . $sep;
					}
					else{
						if ($tallap!="")
							echo sanitizeTabReturn($tallap) . $sep;
						else
							echo $tallap=sanitizeTabReturn(get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$id_referencia_item))) . $sep;

					}

					if($row[Mayorista]=="S"){
						echo sanitizeTabReturn($row[TipoProductoMayorista]) . $sep;
					}
					else{
						echo $tipop . $sep;
					}




			//FIN REFERENCIA DEL PRODUCTO TALLA TIPO

			echo sanitizeTabReturn(trim(get_field("Proveedor","Nombre","IDProveedor",$id_proveedor_ref))) . $sep;
			echo sanitizeTabReturn($row["Remonta"]) . $sep;
			echo sanitizeTabReturn($row["NumeroFacturaRemonta"]) . $sep;
			echo sanitizeTabReturn($row["ValorRemonta"]) . $sep;
			echo sanitizeTabReturn($row["PagoRemonta"]) . $sep;
			echo sanitizeTabReturn($row["RequiereNotaCredito"]) . $sep;
			echo sanitizeTabReturn($row["NumeroNotaCredito"]) . $sep;
			echo sanitizeTabReturn($row["FechaNotaCreditoContabilidad"]) . $sep;
			echo sanitizeTabReturn($row["NotaCreditoAplicada"]) . $sep;
			echo sanitizeTabReturn(trim($row["NumeroOrdenProduccion"])) . $sep;

			echo sanitizeTabReturn($row["Basica"]) . $sep;
			echo sanitizeTabReturn($row["ValorBasica"]) . $sep;
			echo sanitizeTabReturn($row["Premium"]) . $sep;
			echo sanitizeTabReturn($row["ValorPremium"]) . $sep;
			echo sanitizeTabReturn($row["NumeroFacturaRestauracion"]) . $sep;

			echo sanitizeTabReturn($row["TipoContrafuerte"]) . $sep;
			echo sanitizeTabReturn($row["TipoCuero"]) . $sep;
			echo sanitizeTabReturn($row["TipoPlantilla"]) . $sep;
			echo sanitizeTabReturn($row["TipoCremallera"]) . $sep;
			echo sanitizeTabReturn($row["TipoHerraje"]) . $sep;
			echo sanitizeTabReturn($row["TipoDespegue"]) . $sep;
			echo sanitizeTabReturn($row["TipoCambrion"]) . $sep;
			echo sanitizeTabReturn($row["TipoTacon"]) . $sep;
			echo sanitizeTabReturn($row["TipoCerco"]) . $sep;
			echo sanitizeTabReturn($row["TipoCardado"]) . $sep;
			echo sanitizeTabReturn($row["TipoSuela"]) . $sep;
			echo sanitizeTabReturn($row["TipoGuarnicion"]) . $sep;
			echo sanitizeTabReturn($row["TipoPuntera"]) . $sep;
			if (!empty($row["TipoOtro"])){
				echo "S" . $sep;
			}
			else{
				echo " " . $sep;
			}
			echo sanitizeTabReturn(eregi_replace("[\n|\r|\n\r]", " ", $row["TipoOtro"])) . $sep;

			echo $row["FechaSalidaAlmacen"] . $sep;
			//Fecha Enviada Reparacion
			$sql_fecharep="select * from ComentarioGarantia Where IDGarantia='".$row["IDGarantia"]."' and IDEstadoGarantia = '5'";
			$qry_fecharep=db_query($sql_fecharep);
			$r_fecharep=db_fetch_object($qry_fecharep);
			echo substr($r_fecharep->FechaComentario,0,10) . $sep;

			//Fecha Enviada Almacen
			$sql_fechaalm="select * from ComentarioGarantia Where IDGarantia='".$row["IDGarantia"]."' and IDEstadoGarantia = '7'";
			$qry_fechaalm=db_query($sql_fechaalm);
			$r_fechaalm=db_fetch_object($qry_fechaalm);
			echo substr($r_fechaalm->FechaComentario,0,10) . $sep;

			//Fecha Autorizacion Especial
			$sql_fechaaut="select * from ComentarioGarantia Where IDGarantia='".$row["IDGarantia"]."' and IDEstadoGarantia = '10'";
			$qry_fechaaut=db_query($sql_fechaaut);
			$r_fechaaut=db_fetch_object($qry_fechaaut);
			echo substr($r_fechaaut->FechaComentario,0,10) . $sep;
			//Descripcion autorizacion Especial
			//echo eregi_replace("[\n|\r|\n\r]", " ", $r_fechaaut->Descripcion) . $sep;
			echo get_field("TipoFinalizacionGarantia","Nombre","IDTipoFinalizacionGarantia",$row["IDTipoFinalizacionGarantia"]) . $sep;

			echo $row["FechaEntradaAlmacen"] . $sep;
			echo $row["FechaEntregaCliente"] . $sep;
			echo sanitizeTabReturn(eregi_replace("[\n|\r|\n\r]", " ", $row["Descripcion"])) . $sep;

			print "\n";

		}

		exit;

?>
