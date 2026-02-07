<?

include("admin/config.inc.php");

//REPORTE VENTA
/*
$sql_factura="SELECT  F.IDFactura,C.Cedula, C.Nombre, C.Apellido,F.NumeroFactura,CIU.Descripcion as CiudadCompra,F.FechaFactura,F.ValorTotal,PV.IDPuntoVenta
FROM Cliente C,Factura F,PuntoVenta PV, Ciudad CIU
WHERE C.IDCliente = F.IDCliente and PV.IDPuntoVenta=F.IDPuntoVenta and CIU.IDCiudad=PV.IDCiudad
And ( DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) = DATE_FORMAT('2020-06-19','%Y-%c-%d' ) or DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) = DATE_FORMAT('2020-07-03','%Y-%c-%d' )) Order by  F.IDFactura";
*/
$sql_factura="SELECT  F.IDFactura,C.Cedula, C.Nombre, C.Apellido,F.NumeroFactura,CIU.Descripcion as CiudadCompra,F.FechaFactura,F.ValorTotal,PV.IDPuntoVenta
FROM Cliente C,Factura F,PuntoVenta PV, Ciudad CIU
WHERE C.IDCliente = F.IDCliente and PV.IDPuntoVenta=F.IDPuntoVenta and CIU.IDCiudad=PV.IDCiudad
And ( DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) = DATE_FORMAT('2020-11-21','%Y-%c-%d' )) Order by  F.IDFactura";

$qry_factura = db_query($sql_factura);
?>

<table border=1>
	<tr>
		<td>Tipo doc</td>
    <td>Identificacion</td>
    <td>Nombre</td>
    <td>Tipo de factura</td>
    <td>Numero de la factura</td>
    <td>Lugar y fecha de la factura</td>
    <td>Categoria del bien cubierto</td>
		<td>Genero del bien cubierto.</td>
		<td>Numero de unidades</td>
		<td>Unidad de medida</td>
		<td>Descripcion del (los) bien (es) comprado (s).</td>
		<td>Valor unitario del bien</td>
		<td>Valor total de la factura.</td>
		<td>Medio de pago.</td>
		<td>Numero de comprobante de pago</td>
		<td>Fecha de entrega de la mercancia vendida.</td>
		<td>Precio de venta al publico o precio de lista a primero (1) junio de 2020. En caso de que el bien no estuviere disponible para la venta a primero (1) de junio, se informara el precio de venta una vez el bien haya estado disponible para venta al publico.</td>
    </tr>

<?php
while ($row_factura = db_fetch_array($qry_factura)):
	$cantidad_producto=0;

	//Detalle Factura
			$sql_detalle_factura = "SELECT * From DetalleFactura Where IDFactura = '".$row_factura[IDFactura]."' and IDPuntoVenta = '".$row_factura[IDPuntoVenta]."'";
			$qry_factura_detalle = db_query($sql_detalle_factura);
			while ($row_factura_detalle = db_fetch_array($qry_factura_detalle)){
				$cantidad_producto++;
				$array_catagoria=array();
				$array_genero=array();
				$array_referencias=array();
				$array_precio=array();
				$array_forma_pago=array();
				$array_precio_vendido=array();
				$pto_vta_ref = get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_factura_detalle["IDCodificacionEspecifica"]);
				$id_ref = get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$pto_vta_ref);
				$sql_referencia="SELECT TR.Descripcion Categoria, TT.Descripcion Genero, R.Numero Referencia
												 FROM TipoReferencia TR, TipoTalla TT, Referencia R
												 WHERE R.IDTipoReferencia=TR.IDTipoReferencia and R.IDTipoTalla=TT.IDTipoTalla and IDReferencia = '".$id_ref."' ";
			  $qry_ref = db_query($sql_referencia);
				while ($row_ref = db_fetch_array($qry_ref)){
					$array_catagoria[]= $row_ref["Categoria"];
					$array_genero[]= $row_ref["Genero"];
					$array_referencias[]= $row_ref["Referencia"];
					$array_precio[]= $row_factura_detalle["PrecioU"];
					$array_precio_vendido[]= $row_factura_detalle["ValorU"];
				}
			}
	//Fin detalle factura

	//Forma pago
	$sql_forma_pago = "SELECT FP.Descripcion
										 FROM FormaPago FP,FormaPagoFactura FPF
										 Where FP.IDFormaPago=FPF.IDFormaPago and IDFactura = '".$row_factura[IDFactura]."' and IDPuntoVenta = '".$row_factura[IDPuntoVenta]."'";
	$qry_forma_pago = db_query($sql_forma_pago);
	while ($row_forma_pago = db_fetch_array($qry_forma_pago)){
		$array_forma_pago[]= $row_forma_pago["Descripcion"];
	}

	?>
		<tr>
			<td>CC</td>
	    <td><?php echo $row_factura["Cedula"]; ?></td>
			<td><?php echo $row_factura["Nombre"] . " " . $row_factura["Apellido"]; ?></td>
			<td>Venta</td>
			<td><?php echo $row_factura["NumeroFactura"]; ?></td>
			<td><?php echo $row_factura["CiudadCompra"] . ", ".substr($row_factura["FechaFactura"],0,10); ?></td>
			<td><?php
			if(count($array_catagoria)>0){
					foreach($array_catagoria as $valor){
							echo $valor . "<br>";
					}
			}
			 ?></td>
			<td><?php if(count($array_genero)>0){
					foreach($array_genero as $valor){
							echo $valor . "<br>";
					}
			}
			 ?></td>
			<td><?php echo $cantidad_producto; ?></td>
			<td>Unidad</td>
			<td><?php if(count($array_referencias)>0){
					foreach($array_referencias as $valor){
							echo $valor . "<br>";
					}
			}
			 ?></td>
			<td><?php if(count($array_precio_vendido)>0){
					foreach($array_precio_vendido as $valor){
							echo $valor . "<br>";
					}
			}
			 ?></td>
			<td><?php echo $row_factura["ValorTotal"]; ?></td>
			<td><?php if(count($array_forma_pago)>0){
					foreach($array_forma_pago as $valor){
							echo $valor . "<br>";
					}
			}
			 ?></td>
			<td>-</td>
			<td>-</td>
			<td><?php if(count($array_precio)>0){
					foreach($array_precio as $valor){
							echo $valor . "<br>";
					}
			}
			 ?></td>
  </tr>
<?php endwhile; ?>

</table>
