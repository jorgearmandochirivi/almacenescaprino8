<?php
	include("../config.inc.php");
	Encabezado();
	$datos = Verifica_Sesion();
	
	header("Content-Type: application/vnd.ms-excel");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("content-disposition: attachment;filename=Codigos.xls");
	
	$SqlExistencias = 
	"SELECT CodEsp.Existencias as Inventario,Co.DescripcionLarga as NombreColor, Ta.Nombre as NombreTalla, Re.Numero as ReferenciaNumero,PunVe.IDCiudad as IDCiudadPunVe, PunVe.IDPuntoVenta as IDPuntoVentaPunVe, PunVe.Nombre as NombrePunVe, Re.Numero as NumeroRe, Ta.Nombre as NombreTa, CodEsp.Existencias as ExistenciasCodEsp, CodEsp.Maximo as MaximoCodEsp, CodEsp.Minimo as MinimoCodEsp, Re.IDPrecio, Re.Saldo
			FROM 
			Referencia as Re, 
			CodificacionEspecifica as CodEsp, 
			Talla as Ta, 
			PuntoVenta as PunVe,
			PuntoVentaReferencia as PunVeRe,
			Color as Co
			WHERE 
			Re.IDReferencia = PunVeRe.IDReferencia
			AND PunVe.IDPuntoVenta = PunVeRe.IDPuntoVenta
			AND CodEsp.IDPuntoVentaReferencia = PunVeRe.IDPuntoVentaReferencia
			AND Ta.IDTalla = CodEsp.IDTalla
			AND PunVe.IDPuntoVenta = '".$_POST["IDPuntoVenta"]."'
			AND Re.IDColor = Co.IDColor
			AND CodEsp.Existencias > 0
			";
	
	$QryExistencias = db_query( $SqlExistencias);
	
	
?>
<table>
	<tr>
		<td>Punto de Venta</td>
		<td>Referencia con talla</td>
		<td>Cantidad</td>
		<td>Referencia sin talla ni color</td>
		<td>Color</td>
		<td>Talla</td>
		<td>Inventario</td>

	</tr>
	
	<?php
	while($Existencias = db_fetch_array( $QryExistencias ))
	{
	?>
	<tr>
		<td><?php echo $Existencias[NombrePunVe]?></td>
		<td><?php echo trim($Existencias[ReferenciaNumero])?><?php echo trim($Existencias[NombreTalla])?></td>
		<td>2</td>
		<td><?php echo substr(trim($Existencias[ReferenciaNumero]), 0,4)?></td>
		<td><?php echo $Existencias[NombreColor]?></td>
		<td><?php echo $Existencias[NombreTalla]?></td>
		<td><?php echo $Existencias[Inventario]?></td>
	</tr>
	<?php
	}
	?>
	
</table>