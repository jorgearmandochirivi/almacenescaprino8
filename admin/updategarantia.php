<?php

//error_reporting(E_ALL);
include("config.inc.php");
include("simple_html_dom.php");


$sql_detalle = "SELECT * FROM Garantia WHERE IDReferencia = 0 ";
$qry_detalle = db_query( $sql_detalle );
while( $r = db_fetch_object( $qry_detalle ) )
{

	if ($r->TipoFactura=="facturabono"):
		$sql_producto="select * from DetalleFacturaBono Where IDDetalleFacturaBono='".$r->IDDetalleFactura."' and IDFacturaBono = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'";
	else:
		$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'";
	endif;

	$qry_producto=db_query($sql_producto);
	$r_detalle=db_fetch_object($qry_producto);

	if(!empty($r->IDDetalleFacturaBono)){

		$array_bono_detalle=explode("|",$r->IDDetalleFacturaBono);
			if (count($array_bono_detalle)>0):
				$sql_bono=db_query("Select * From DetalleFacturaBono Where IDDetalleFacturaBono	 = '".$array_bono_detalle[1]."' and IDFacturaBono = '".$array_bono_detalle[0]."'");
				$r_bono=db_fetch_array($sql_bono);

					$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"])));
				$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
				$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
				echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
				$NombreReferencia=$nombre_referencia;
				$ReferenciaG=$id_referencia_item;
			endif;
	}
	elseif(empty($r->IDDetalleCambio)){ ?>
								<?php
			 $id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))) ;
			if ($id_referencia_item==160){ // Cuando son excedentes consulto la referencia de la compra
				$sql_facturabono=db_query("Select * from FacturaBono Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
				$r_facturabono=db_fetch_array($sql_facturabono);
				if (!empty($r_facturabono[IDFacturaBono])){
					$sql_detallefacturabono=db_query("Select * from DetalleFacturaBono Where IDFacturaBono = '".$r_facturabono[IDFacturaBono]."'");
					$r_detallefacturabono=db_fetch_array($sql_detallefacturabono);
					$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));
					$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"]));
				}
				}

			 $id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
			 echo $nombre_ref=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
			 $NombreReferencia=$nombre_ref;
			 $ReferenciaG=$id_referencia_item;

			 ?>
								<?php } else{ // ES UNA REFERNCIA DE ALGUN CAMBIO Y LA CONSULTO
				$array_cambio_detalle=explode("|",$r->IDDetalleCambio);
			if (count($array_cambio_detalle)>0):
				$sql_cambio=db_query("Select * From DetalleCambio Where IDDetalleCambio = '".$array_cambio_detalle[1]."' and IDCambio = '".$array_cambio_detalle[0]."'");
				$r_cambio=db_fetch_array($sql_cambio);

					$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"])));
				$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"]));
				$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
				echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
				$NombreReferencia=$nombre_referencia;
				$ReferenciaG=$id_referencia_item;
			endif;

		}


		echo "<br>".$sql_gar="UPDATE Garantia SET Referencia = '".$NombreReferencia."' WHERE IDGarantia = '".$r->IDGarantia."' ";

		//echo $str_actualiza_inventario .= "<br>";
		db_query( $sql_gar );





}//end fhilw



?>
