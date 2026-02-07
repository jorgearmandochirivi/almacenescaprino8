<?

include("admin/config.inc.php");

//REPORTE VENTA
/*
$sql_factura="SELECT  F.IDFactura,C.Cedula, C.Nombre, C.Apellido,F.NumeroFactura,CIU.Descripcion as CiudadCompra,F.FechaFactura,F.ValorTotal,PV.IDPuntoVenta
FROM Cliente C,Factura F,PuntoVenta PV, Ciudad CIU
WHERE C.IDCliente = F.IDCliente and PV.IDPuntoVenta=F.IDPuntoVenta and CIU.IDCiudad=PV.IDCiudad
And ( DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) = DATE_FORMAT('2020-06-19','%Y-%c-%d' ) or DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) = DATE_FORMAT('2020-07-03','%Y-%c-%d' )) Order by  F.IDFactura";
*/

/*
$sql_factura="SELECT  F.IDFactura,C.Cedula, C.Nombre, C.Apellido, C.Ano, C.Mes, C.Dia, C.ClubSuavidad,C.Telefono,C.Direccion, C.Genero GeneroCliente, F.NumeroFactura,CIU.Descripcion as CiudadCompra,F.FechaFactura,F.ValorTotal,PV.IDPuntoVenta,PV.Nombre as Tienda, DF.*,DF.ValorU PrecioProd, PV.Resolucion
FROM Cliente C,Factura F,PuntoVenta PV, Ciudad CIU,DetalleFactura DF
WHERE C.IDCliente = F.IDCliente and PV.IDPuntoVenta=F.IDPuntoVenta and CIU.IDCiudad=PV.IDCiudad and  DF.IDFactura=F.IDFactura
And YEAR(FechaFactura)=2019  Order by  F.IDFactura Limit 100000";
*/

$sql_factura="SELECT  F.IDFactura,C.Cedula, C.Nombre, C.Apellido, C.Ano, C.Mes, C.Dia, C.ClubSuavidad,C.Telefono,C.Direccion, C.Genero GeneroCliente, F.NumeroFactura,CIU.Descripcion as CiudadCompra,F.FechaFactura,F.ValorTotal,F.ValorIVA,PV.IDPuntoVenta,PV.Nombre as Tienda,PV.Resolucion
FROM Cliente C,Factura F,PuntoVenta PV, Ciudad CIU
WHERE C.IDCliente = F.IDCliente and PV.IDPuntoVenta=F.IDPuntoVenta and CIU.IDCiudad=PV.IDCiudad
And YEAR(FechaFactura)=2019  Order by  F.IDFactura";

$qry_factura = db_query($sql_factura);
?>

<table border=1>
	<tr>
	<td>Almacen</td>
	<td>Fecha Exp.</td>
	<td>Nro Factura</td>
	<td>Cedula</td>
	<td>Nombre</td>
	<td>Apellido</td>
	<td>Direccion</td>
	<td>Telefono</td>
	<td>Ciudad</td>
	<td>Referencia</td>
	<!--<td>Talla</td>-->
	<td>Valor</td>
	<td>IVA</td>
	<td>Nro Resolucion.</td>
    </tr>

<?php
while ($row_factura = db_fetch_array($qry_factura)):
	$cantidad_producto=0;

	//Detalle Factura
	$array_catagoria=array();
	$array_genero=array();
	$array_referencias=array();
	$array_precio=array();
	$array_forma_pago=array();
	$array_precio_vendido=array();
	$array_tallas= array();
			
	$sql_detalle_factura = "SELECT IDCodificacionEspecifica From DetalleFactura Where IDFactura = '".$row_factura[IDFactura]."' and IDPuntoVenta = '".$row_factura[IDPuntoVenta]."'";
	$qry_factura_detalle = db_query($sql_detalle_factura);
	while ($row_det= db_fetch_array($qry_factura_detalle)){
		$pto_vta_ref = get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_det["IDCodificacionEspecifica"]);
		$id_ref = get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$pto_vta_ref);
		$nom_ref = get_field("Referencia","Numero","IDReferencia",$id_ref);
		$array_referencias[]= $nom_ref;
	}
			
				
			/*	
			$sql_codif = "SELECT CE.*, T.Descripcion as TallaRef From CodificacionEspecifica CE,Talla T Where CE.IDtalla=T.IDTalla and  IDCodificacionEspecifica = '".$row_factura["IDCodificacionEspecifica"]."' LIMIT 1 ";
			$qry_codif = db_query($sql_codif);
			$row_codif = db_fetch_array($qry_codif);
			*/
			

				//$pto_vta_ref = get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_factura_detalle["IDCodificacionEspecifica"]);

				/*
					$pto_vta_ref = $row_codif["IDPuntoVentaReferencia"];
					$id_ref = get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$pto_vta_ref);
					
					$sql_referencia="SELECT TR.Descripcion Categoria, TT.Descripcion Genero, R.Numero Referencia
													FROM TipoReferencia TR, TipoTalla TT, Referencia R
													WHERE R.IDTipoReferencia=TR.IDTipoReferencia and R.IDTipoTalla=TT.IDTipoTalla and IDReferencia = '".$id_ref."' ";				
				//$qry_ref = db_query($sql_referencia);
					while ($row_ref = db_fetch_array($qry_ref)){
						$array_catagoria[]= $row_ref["Categoria"];
						$array_genero[]= $row_ref["Genero"];
						$array_tallas[]= $row_codif["TallaRef"];
						$array_referencias[]= $row_ref["Referencia"];
						$array_precio[]= $row_factura_detalle["PrecioU"];
						$array_precio_vendido[]= $row_factura_detalle["ValorU"];
					}
				*/
			
	//Fin detalle factura
	
	?>
		<tr>
		<td><?php echo $row_factura["Tienda"]; ?></td>
		<td><?php echo $row_factura["FechaFactura"]; ?></td>
		<td><?php echo $row_factura["NumeroFactura"];  ?></td>
		<td><?php echo $row_factura["Cedula"]; ?></td>
		<td><?php echo $row_factura["Nombre"]; ?></td>
		<td><?php echo $row_factura["Apellido"]; ?></td>
		<td><?php echo $row_factura["Direccion"]; ?></td>
		<td><?php echo $row_factura["Telefono"]; ?></td>
		<td><?php echo $row_factura["CiudadCompra"]; ?></td>
		<td><?php if(count($array_referencias)>0){
					echo implode(",",$array_referencias);					
			}
			?>
		</td>
		<!--
		<td><?php if(count($array_tallas)>0){
					foreach($array_tallas as $valor){
						echo implode(",",$array_tallas);							
					}
			}
			?>
		</td>
		-->
		<td><?php echo $row_factura["ValorTotal"]; ?></td>
		<td><?php echo $row_factura["ValorIVA"]; ?></td>
		<td><?php echo $row_factura["Resolucion"]; ?></td>
			
  </tr>
<?php endwhile; ?>

</table>

