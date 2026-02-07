<?

include("admin/config.inc.php");

//REPORTE VENTA
/*
$sql_factura="SELECT  F.IDFactura,F.NumeroFactura, F.FechaFactura,C.Cedula,F.ValorTotal,PV.Nombre NombrePtoVenta,PV.IDPuntoVenta
FROM Cliente C,Factura F,PuntoVenta PV
WHERE C.IDCliente = F.IDCliente and PV.IDPuntoVenta=F.IDPuntoVenta
And F.FechaFactura>= '2021-04-01' and F.FechaFactura<='2021-07-31' and F.IDPuntoVenta=27
Order by  F.IDFactura";
*/

$sql_factura="SELECT  F.IDFactura,F.NumeroFactura, F.FechaFactura,C.Cedula,C.Nombre,C.Apellido, F.ValorTotal,PV.Nombre NombrePtoVenta,PV.IDPuntoVenta
FROM Cliente C,Factura F,PuntoVenta PV
WHERE C.IDCliente = F.IDCliente and PV.IDPuntoVenta=F.IDPuntoVenta
And F.FechaFactura>= '2020-01-01' and F.FechaFactura<='2020-12-31'
Order by  F.IDFactura";

$qry_factura = db_query($sql_factura);
?>

<table border=1>
	<tr>
		<td>Numero de la factura</td>
		<td>Fecha de la factura</td>
    <td>Cedula</td>
	<td>Nombre</td>
	<td>Apellido</td>
    <td>Valor total</td>
		<td>Total Items</td>
		<td>Referencias</td>
		<td>Punto de venta</td>
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

	?>
		<tr>
			<td><?php echo $row_factura["NumeroFactura"]; ?></td>
			<td><?php echo substr($row_factura["FechaFactura"],0,10); ?></td>
			<td><?php echo $row_factura["Cedula"]; ?></td>
			<td><?php echo $row_factura["Nombre"]; ?></td>
			<td><?php echo $row_factura["Apellido"]; ?></td>
			<td><?php echo $row_factura["ValorTotal"]; ?></td>
			<td><?php echo count($array_referencias); ?></td>
			<td><?php
			foreach($array_referencias as $referencia){
				echo "<br>".$referencia;
			}


			?></td>
			<td><?php echo $row_factura["NombrePtoVenta"]; ?></td>

  </tr>
<?php endwhile; ?>

</table>
