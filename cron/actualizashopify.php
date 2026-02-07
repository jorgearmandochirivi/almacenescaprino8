#!/usr/bin/php -q
<?php
include("../admin/config.inc.php");

$Token="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";
$ClaveApi="40de37747e93b22d03f54dd19a693b40";
$ClaveSecreta="0a2b5a035db40252988de09340edab89";
$NombreTienda='calzadocaprino';
$VersionApi='2022-04';
$UrlApi='https://'.$ClaveApi.':'.$Token.'@'.$NombreTienda.'.myshopify.com/admin/api/'.$VersionApi.'/';



function listar_productos(){
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $UrlApi . 'products.json',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	));
	
	$response = curl_exec($curl);
	
	curl_close($curl);
	echo $response;
	exit;
}


	$array_puntos_de_venta = array(1=>1,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,12=>12,13=>13,14=>14,15=>15,17=>17,18=>18,19=>19,20=>20,22=>22,23=>23,24=>24,26=>26,27=>27,28=>28,29=>29,30=>30,31=>31,32=>32);

	//Tallas
	$sql_talla="SELECT * From Talla WHERE Publicar = 'S '";
	$r_talla=db_query($sql_talla);
	while($row_talla = db_fetch_array($r_talla)){			
		$array_talla[$row_talla["IDTalla"]]=$row_talla;
	}

	//Consulto las referencias posibles
	$sql_referencia="SELECT * From Referencia WHERE Publicar = 'S' and Numero like '2E01%' LIMIT 100";
	$r_referencia=db_query($sql_referencia);
	while($row_referencia = db_fetch_array($r_referencia)){	
		$ReferenciaPublicar=$row_referencia["Numero"];	
		$array_referencia[$ReferenciaPublicar]=$ReferenciaPublicar;
	}

	foreach ($array_referencia as $referencia){
		if( sizeof( $array_puntos_de_venta ) > 0 ){
			$SqlExistencias = "";
			$listo = 0;
			$unidades = 0;			
			$puntos["IDPuntoVenta"] = $key_puntos_de_venta;
			$IDConsultaPunto=implode(",",$array_puntos_de_venta);
				
				foreach($array_talla as $key_talla => $datos_talla){
				
						echo "<br><br>".$referencia;
						$SqlExistencias = "SELECT SUM(Existencias) as TotalExistencias, Re.Numero as NumeroRe, Ta.Nombre as NombreTa, CodEsp.Existencias as ExistenciasCodEsp, CodEsp.Maximo as MaximoCodEsp, CodEsp.Minimo as MinimoCodEsp, Re.IDPrecio, Re.Saldo, PunVeRe.IDPuntoVenta
						FROM
						Referencia as Re,
						CodificacionEspecifica as CodEsp,
						Talla as Ta,
						PuntoVentaReferencia as PunVeRe
						WHERE
						Re.IDReferencia = PunVeRe.IDReferencia
						AND PunVeRe.IDPuntoVenta in (".$IDConsultaPunto.")
						AND CodEsp.IDPuntoVentaReferencia = PunVeRe.IDPuntoVentaReferencia
						AND Ta.IDTalla = CodEsp.IDTalla
						AND Re.Numero LIKE '".$referencia."'
						AND Ta.Nombre = '".$datos_talla["Descripcion"]."'
						GROUP BY NumeroRe
						";
						
						$QryExistencias = db_query( $SqlExistencias);
						$Existencias = db_fetch_array( $QryExistencias );
						//if(db_num_rows( $QryExistencias ) > 0){							
						  if($Existencias["TotalExistencias"]>0){							
							if($precio == ''){
								$precio = $Existencias["IDPrecio"];
							}//END IF PRECIO

							if($saldo_new == ''){
								$saldo_new = $Existencias["Saldo"];
							}//END IF SALDO

							$array_producto_actualizar[$Existencias["NumeroRe"]][$Existencias["NombreTa"]]["Cantidad"]++;
							$array_producto_actualizar[$Existencias["NumeroRe"]][$Existencias["NombreTa"]]["Precio"]=$precio;
							//echo "<br>" . $SqlExistencias;
							//echo "<br>Actualizar / Insertar el producto " . $referencia . " en la talla " . $datos_talla["Descripcion"] . " Unidades " . $unidades . " Precio: " . $precio;								
							$ReferenciaPublicar=substr($Existencias["NumeroRe"],0,4);	
							$Color=substr($Existencias["NumeroRe"],4);	
							$sql_prec="SELECT ValorVenta,Descuento FROM Precio WHERE IDPrecio = " . $precio;
							$r_precio=db_query($sql_prec);
							$row_prec=db_fetch_array($r_precio);
							$array_producto[$ReferenciaPublicar]["Color"][$Color][$Existencias["NombreTa"]]=$row_prec["ValorVenta"]."|".$row_prec["Descuento"];
							
						}


						
				}	
				
		}	
	}
	echo "<br><br>";
	print_r($array_producto);		
	exit;				
				

	
	foreach($array_producto_actualizar as $ref => $datos_ref){
		foreach($datos_ref as $talla => $otos_datos){
			echo "TALLA " . $talla;			
			if($otos_datos["Cantidad"]>2){				
				if(!empty($ref)){
					// Producto se debe crear o actualizar				
					echo "REF " . $ref . " Talla:  " . $talla . " TALLA OTRA: " . $datos_ref["Talla"] . " Cantidad " . $datos_ref["Cantidad"];
					//print_r($datos_ref);
					//exit;
					echo "<br>";
				}
			}
		}	
	}


	
	

            				  






?>

