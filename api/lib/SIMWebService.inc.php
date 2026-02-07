<?php

class SIMWebService
{

	public function get_talla()
    {
        $response = array();
        
            $SQL = "SELECT T.*, TT.Descripcion as TipoTalla FROM Talla T, TipoTalla TT WHERE T.IDTipoTalla=TT.IDTipoTalla and  T.Publicar = 'S' ";
            $QRY= db_query($SQL);

            if (db_num_rows($QRY) > 0) :
                $message = db_num_rows($QRY) . "Encontrados";
                while ($Datos = db_fetch_array($QRY)) :

                    $Info["Descripcion"] = $Datos["Descripcion"];
                    $Info["Nombre"] = $Datos["Nombre"];
                    $Info["Tipo"] = $Datos["TipoTalla"];                    
                    array_push($response, $Info);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        

        return $respuesta;
    }

	public function get_tipo_referencia()
    {
        $response = array();
        
            $SQL = "SELECT * FROM TipoReferencia WHERE Publicar = 'S'";
            $QRY= db_query($SQL);

            if (db_num_rows($QRY) > 0) :
                $message = db_num_rows($QRY) . "Encontrados";
                while ($Datos = db_fetch_array($QRY)) :
					$Info["IDTipoReferencia"] = $Datos["IDTipoReferencia"];
                    $Info["Descripcion"] = $Datos["Descripcion"];
                    $Info["Codigo"] = $Datos["Codigo"];
                    array_push($response, $Info);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        

        return $respuesta;
    }

	public function get_color()
    {
        $response = array();
        
            $SQL = "SELECT * FROM Color WHERE Publicar = 'S' ";
            $QRY= db_query($SQL);

            if (db_num_rows($QRY) > 0) :
                $message = db_num_rows($QRY) . "Encontrados";
                while ($Datos = db_fetch_array($QRY)) :                    
                    $Info["Nombre"] = $Datos["Nombre"];
					$Info["DescripcionLarga"] = $Datos["DescripcionLarga"];                    
                    array_push($response, $Info);
                endwhile;

                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        

        return $respuesta;
    }

	public function get_bono($Documento)
    {
        $response = array();
			if(!empty($Documento)){
				$sql_cliente = "SELECT * From Cliente Where Cedula = '".$Documento."'";
				$qry_cliente = db_query( $sql_cliente);
				$registro_update = db_fetch_array( $qry_cliente );
				$id_cliente=$registro_update["IDCliente"];

			
				$SQL = "SELECT * FROM BonoFidelizacion WHERE IDCliente = '" . $id_cliente . "' and Estado = 'D' Order By Estado ASC";
				$QRY = db_query( $SQL );
			
				
				if (db_num_rows($QRY) > 0) :
					$message = db_num_rows($QRY) . "Encontrados";
					while ($Datos = db_fetch_array($QRY)) :

						$Info["IDBonoFidelizacion"] = $Datos["IDBonoFidelizacion"];
						$Info["Valor"] = $Datos["Valor"];
						$Info["FechaVencimiento"] = $Datos["FechaVencimiento"];                    
						array_push($response, $Info);
					endwhile;

					$respuesta["message"] = $message;
					$respuesta["success"] = true;
					$respuesta["response"] = $response;

				else :
					$respuesta["message"] = "No se encontraron registros";
					$respuesta["success"] = false;
					$respuesta["response"] = null;
				endif;

			}
			else{
				$respuesta["message"] = "Faltan parametros!";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
			}
			
        

        return $respuesta;
    }

	public function get_producto($Referencia,$Pagina,$CantidadPorPagina)
    {
        $response = array();
		$array_imagenes_ref=array();

		if((int)$CantidadPorPagina<=0){
			$CantidadPorPagina = 50;
		}
		
			if(!empty($Referencia)){
				$condicion = " and ( Numero like '".$Referencia."%' or  NumeroAnterior like '".$Referencia."%' or Nombre like '".$Referencia."%' or NombreAnterior like '".$Referencia."%' )";
			}
			if((int)$Pagina>0){
				$Pagina=$CantidadPorPagina*($Pagina-1);
				if($Pagina>1)
					$Pagina++;

				$limit = "  LIMIT " . $Pagina . ", ".$CantidadPorPagina;
				
			}

			
			
			$array_puntos_de_venta = array(1=>1,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,12=>12,13=>13,14=>14,15=>15,17=>17,18=>18,19=>19,20=>20,22=>22,23=>23,24=>24,26=>26,27=>27,28=>28,29=>29,30=>30,31=>31,32=>32);

			//Tallas
			$sql_talla="SELECT * From Talla WHERE Publicar = 'S '";
			$r_talla=db_query($sql_talla);
			while($row_talla = db_fetch_array($r_talla)){			
				$array_talla[$row_talla["IDTalla"]]=$row_talla;
			}

			
			if(!empty($limit)){
				$sql_referencia="SELECT IDReferencia From Referencia WHERE Publicar = 'S' and Numero not like 'ZSE%' and Numero not like '%*%' and ExistenciaWeb = 1 " . $condicion . " Group By substr(Numero,1,4) " . $limit;
				$r_referencia=db_query($sql_referencia);
				while($row_referencia = db_fetch_array($r_referencia)){	
					$array_ref_buscar[]=$row_referencia["IDReferencia"];
				}	
				if(count($array_ref_buscar)>0){
					$condicion .= " and IDReferencia in ( " . implode(",",$array_ref_buscar) . ")";
				}
			}
			
			//Consulto las referencias posibles
			//$sql_referencia="SELECT * From Referencia WHERE Publicar = 'S' and Numero like '2E01%' LIMIT 100";
			//$sql_referencia="SELECT * From Referencia WHERE Publicar = 'S' and Numero not like 'ZSE%' and Numero not like '%*%' " . $condicion . $limit;
			$sql_referencia="SELECT * From Referencia WHERE Publicar = 'S' and Numero not like 'ZSE%' and Numero not like '%*%' " . $condicion;
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
								
								$SqlExistencias = "SELECT SUM(Existencias) as TotalExistencias, Re.Numero as NumeroRe, Re.Sexo, Re.Saldo, Re.IDColor, Re.IDTipoReferencia, Ta.Nombre as NombreTa, 
														  CodEsp.Existencias as ExistenciasCodEsp, CodEsp.Maximo as MaximoCodEsp, CodEsp.Minimo as MinimoCodEsp, Re.IDPrecio, Re.Saldo, PunVeRe.IDPuntoVenta,
														  Re.FotoWeb1, Re.FotoWeb2, Re.FotoWeb3, Re.FotoWeb4, Re.DescripcionCorta,Re.DescripcionLarga
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
								if($Existencias["TotalExistencias"]>=2){							
									//if($precio == ''){
										$precio = $Existencias["IDPrecio"];
									//}//END IF PRECIO

									if($saldo_new == ''){
										$saldo_new = $Existencias["Saldo"];
									}//END IF SALDO
									//echo "<br>" . $SqlExistencias;
									//echo "<br>Actualizar / Insertar el producto " . $referencia . " en la talla " . $datos_talla["Descripcion"] . " Unidades " . $unidades . " Precio: " . $precio;								
									
									if($referencia=="LP4CQMUV"){
										//echo $SqlExistencias;
									}
									
									$ReferenciaPublicar=substr($Existencias["NumeroRe"],0,4);	
									$Color=substr($Existencias["NumeroRe"],4);	
									$sql_prec="SELECT ValorVenta,Descuento FROM Precio WHERE IDPrecio = " . $precio;
									$r_precio=db_query($sql_prec);
									$row_prec=db_fetch_array($r_precio);

									$tipo_ref=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$Existencias["IDTipoReferencia"]);
									$nombre_color=utf8_encode(get_field("Color","DescripcionLarga","IDColor",$Existencias["IDColor"]));
									$array_producto[$ReferenciaPublicar]["Color"][$Color]["Talla"][$Existencias["NombreTa"]]="Talla " . $Existencias["NombreTa"];
									$array_producto[$ReferenciaPublicar]["TipoReferencia"]=$tipo_ref;
									
									if(!empty($Existencias["FotoWeb1"]) && empty($FotoPrincipal))
										$FotoPrincipal="https://" . $_SERVER['SERVER_NAME'] . "/admin/imagenes/".$Existencias["FotoWeb1"];

									if(!empty($Existencias["FotoWeb2"])){
										$datos_imagen[$Existencias["FotoWeb2"]]="https://" . $_SERVER['SERVER_NAME'] . "/admin/imagenes/".$Existencias["FotoWeb2"];
										
									}
										
									if(!empty($Existencias["FotoWeb3"])){
										$datos_imagen[$Existencias["FotoWeb3"]]="https://" . $_SERVER['SERVER_NAME'] . "/admin/imagenes/".$Existencias["FotoWeb3"];
										
									}
										
									if(!empty($Existencias["FotoWeb4"])){
										$datos_imagen[$Existencias["FotoWeb4"]]="https://" . $_SERVER['SERVER_NAME'] . "/admin/imagenes/".$Existencias["FotoWeb4"];
										
									}
									
									$array_producto[$ReferenciaPublicar]["ImagenPrincipal"]=$FotoPrincipal;
									$array_producto[$ReferenciaPublicar]["DescripcionCorta"]=$Existencias["DescripcionCorta"];
									$array_producto[$ReferenciaPublicar]["DescripcionLarga"]=$Existencias["DescripcionLarga"];
									$array_producto[$ReferenciaPublicar]["Color"][$Color]["Precio"]=$row_prec["ValorVenta"];
									$array_producto[$ReferenciaPublicar]["Color"][$Color]["Descuento"]=$row_prec["Descuento"];
									$array_producto[$ReferenciaPublicar]["Color"][$Color]["Genero"]=$Existencias["Sexo"];
									$array_producto[$ReferenciaPublicar]["Color"][$Color]["Saldo"]=$Existencias["Saldo"];									
									$array_producto[$ReferenciaPublicar]["Color"][$Color]["NombreColor"]=$nombre_color;
									
								}
						}	
					}	
			}

			array_push($array_imagenes_ref, $datos_imagen);

            if (sizeof($array_producto) > 0) :
                $message = sizeof($array_producto) . " Encontrados";
                foreach ($array_producto as $index_ref => $datos_ref):
					$Info["Referencia"] = $index_ref;
					$Info["TipoReferencia"] = $datos_ref["TipoReferencia"];
					$Info["ImagenPrincipal"] = $datos_ref["ImagenPrincipal"];
					$Info["ImagenReferencia"] = $array_imagenes_ref;
					$Info["DescripcionCortaReferencia"] = $datos_ref["DescripcionCorta"];
					$Info["DescripcionLargaReferencia"] = $datos_ref["DescripcionLarga"];
					$Info["Color"] = $datos_ref["Color"]; 
					array_push($response, $Info);
				endforeach;
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        

        return $respuesta;
    }

	public function actualiza_ref_existencia($Referencia,$Pagina,$CantidadPorPagina)
    {
		$response = array();
		$array_imagenes_ref=array();
			
			$array_puntos_de_venta = array(1=>1,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,12=>12,13=>13,14=>14,15=>15,17=>17,18=>18,19=>19,20=>20,22=>22,23=>23,24=>24,26=>26,27=>27,28=>28,29=>29,30=>30,31=>31,32=>32);

			//Tallas
			$sql_talla="SELECT * From Talla WHERE Publicar = 'S '";
			$r_talla=db_query($sql_talla);
			while($row_talla = db_fetch_array($r_talla)){			
				$array_talla[$row_talla["IDTalla"]]=$row_talla;
			}

			
			//Consulto las referencias posibles
			//$sql_referencia="SELECT * From Referencia WHERE Publicar = 'S' and Numero like '2E01%' LIMIT 100";
			$sql_referencia="SELECT * From Referencia WHERE Publicar = 'S' and Numero not like 'ZSE%' " . $condicion . $limit;
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
								
								$SqlExistencias = "SELECT SUM(Existencias) as TotalExistencias, Re.Numero as NumeroRe, Re.Sexo, Re.Saldo, Re.IDColor, Re.IDTipoReferencia, Ta.Nombre as NombreTa, 
														  CodEsp.Existencias as ExistenciasCodEsp, CodEsp.Maximo as MaximoCodEsp, CodEsp.Minimo as MinimoCodEsp, Re.IDPrecio, Re.Saldo, PunVeRe.IDPuntoVenta,
														  Re.FotoWeb1, Re.FotoWeb2, Re.FotoWeb3, Re.FotoWeb4, Re.DescripcionCorta,Re.DescripcionLarga
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
								if($Existencias["TotalExistencias"]>=2){							
									echo "<br>" . $sql_existencia_web="UPDATE Referencia SET ExistenciaWeb=1 WHERE Numero = '".$referencia."' ";
									db_query($sql_existencia_web);
								}
						}	
					}	
			}

			array_push($array_imagenes_ref, $datos_imagen);

            if (sizeof($array_producto) > 0) :
                $message = sizeof($array_producto) . " Encontrados";
                foreach ($array_producto as $index_ref => $datos_ref):
					$Info["Referencia"] = $index_ref;
					$Info["TipoReferencia"] = $datos_ref["TipoReferencia"];
					$Info["ImagenPrincipal"] = $datos_ref["ImagenPrincipal"];
					$Info["ImagenReferencia"] = $array_imagenes_ref;
					$Info["DescripcionCortaReferencia"] = $datos_ref["DescripcionCorta"];
					$Info["DescripcionLargaReferencia"] = $datos_ref["DescripcionLarga"];
					$Info["Color"] = $datos_ref["Color"]; 
					array_push($response, $Info);
				endforeach;
                $respuesta["message"] = $message;
                $respuesta["success"] = true;
                $respuesta["response"] = $response;

            else :
                $respuesta["message"] = "No se encontraron registros";
                $respuesta["success"] = false;
                $respuesta["response"] = null;
            endif;
        

        return $respuesta;
    }


	public function set_pedido($NumeroPedido,$CedulaCliente,$NombreCliente,$Valor,$FechaPedido,$Referencias,$Bonos){

		include_once("../admin/lib/class.phpmailer.php");
		include_once("../admin/lib/class.smtp.php");
		$PedidoValido="S";

			$mensaje=$NumeroPedido."-".$CedulaCliente."-".$NombreCliente."-".$Valor."-".$FechaPedido."-".$Referencias."-".$Bonos;
			$Subject = "Api Caprino";
			//$para      = 'supervisor@calzadocaprino.com';
			$para      = 'jorgechirivi@gmail.com';
			$titulo    = 'Api Caprino';
			$cabeceras = 'From: tienda@calzadocaprino.com' . "\r\n" .
				'Reply-To: tienda@calzadocaprino.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
			//$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";	
			$cabeceras  .= 'MIME-Version: 1.0' . "\r\n";
			mail($para, $titulo, $mensaje, $cabeceras);
			

		if(!empty($NumeroPedido) && !empty($CedulaCliente) && !empty($NombreCliente) && !empty($Valor) && !empty($FechaPedido) && !empty($Referencias)){

				
				//DATOS TIENDA PRUEBAS
				$conexion2=mysql_connect("localhost","caprinodesarroll","P7d@1q0e23wd$") or die("Problemas en la conexion2");
				mysql_select_db("CaprinoDesarrollo",$conexion2) or die("Problemas en la seleccion de la base de datos");

				$array_prioridad_almacen_cercano = array ("1"=>"Unicentro","6"=>"Galerias","29"=>"Titan","22"=>"Gran Estacion");
				$sql_pto_vta = db_query( "SELECT * FROM PuntoVenta Where Publicar = 'S' and IDPuntoVenta not in (1,6,29,22,10) Order By IDCiudad ASC");
				
				while($r_pto_vta = db_fetch_array( $sql_pto_vta)){
					$array_prioridad_almacen_cercano[$r_pto_vta["IDPuntoVenta"]] = $r_pto_vta["Nombre"];
				}


				
				
				$array_referencias = json_decode($Referencias, true);            	

				if(count($array_referencias)<=0)
					$PedidoValido="N";

				foreach ($array_referencias as $datos_referencia)
				{

					unset($array_tallas_rel);
					$id_tallas_rel="";

					$sql_ref_alamacen = "Select * From Referencia Where Numero  = '".$datos_referencia["Referencia"]."' or NumeroAnterior = '".$datos_referencia["Referencia"]."'";
					$qry_ref_almacen = db_query($sql_ref_alamacen);
					$row_ref_almacen = db_fetch_array($qry_ref_almacen);
					$id_referencia = $row_ref_almacen["IDReferencia"];
					if((int)$id_referencia<=0){
						$PedidoValido="N";
						$respuesta_ws.="referencia no existe: " . $datos_referencia["Referencia"] . " ";
					}
					


					//Consulto las otra tallas posibles ya que una talla esta creada mas de una vez
					$sql_tallas_rel = db_query("Select * From Talla Where Descripcion = '".$datos_referencia[Talla]."'");
					while($row_talla = db_fetch_array($sql_tallas_rel)):
						$array_tallas_rel []=$row_talla[IDTalla];
					endwhile;

					if (count($array_tallas_rel)>0):
						$id_tallas_rel = implode(",",$array_tallas_rel);
					endif;

					// busco el producto segun la cantidad pedida para que se genere los traslados correspondientes
					for($i=1;$i<=(int)$datos_referencia["Cantidad"];$i++):
							$id_almacen_traslado = "";

								//Consulto cual almacen tiene mas inventario de la referncia
								$sql_pto_ref_inv = "Select *
													From PuntoVentaReferencia PVR, CodificacionEspecifica CE
													Where PVR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia and
													PVR.IDPuntoVenta not in (21) and
													Existencias >0 and
													IDReferencia = '".$id_referencia."' and IDTalla in (".$id_tallas_rel.") and CE.Publicar = 'S'";
								$qry_pto_ref_inv = db_query($sql_pto_ref_inv);
								$mayor_existencia = 0;
								while($row_pto_ref_inv = db_fetch_array($qry_pto_ref_inv)){
									if($row_pto_ref_inv["Existencias"]>0):
										if($row_pto_ref_inv["Existencias"]>$mayor_existencia):
											$mayor_existencia=$row_pto_ref_inv["Existencias"];
											$id_pto_vta_esp = $row_pto_ref_inv["IDPuntoVentaReferencia"];
											$id_codif_esp = 	$row_pto_ref_inv["IDCodificacionEspecifica"];
											$sql_pto_existencia = "Select * From PuntoVentaReferencia Where IDPuntoVentaReferencia = '".$row_pto_ref_inv["IDPuntoVentaReferencia"]."'";
											$qry_pto_existencia = db_query($sql_pto_existencia);
											$row_pto_existencia = db_fetch_array($qry_pto_existencia);
											$id_almacen_traslado = 	$row_pto_existencia["IDPuntoVenta"];
										endif;
									endif;
								}


								// Si todos los almacenes tienen solo una existencia, verifico el mas cercano
								if ((int)$mayor_existencia<=1):
									foreach($array_prioridad_almacen as $id_pto => $nom_pto ):
										$sql_pto_ref = "Select * From PuntoVentaReferencia PVR Where IDReferencia = '".$id_referencia."' and IDPuntoVenta = '".$id_pto."'";
										$qry_pto_ref = db_query($sql_pto_ref);
										$row_pto_ref = db_fetch_array($qry_pto_ref);
										$sql_codif_esp = "Select  * From CodificacionEspecifica Where IDPuntoVentaReferencia = '".$row_pto_ref["IDPuntoVentaReferencia"]."' and IDTalla in (".$id_tallas_rel.") and Publicar = 'S'";
										$qry_codif_esp = db_query($sql_codif_esp);
										$row_codif_esp = db_fetch_array($qry_codif_esp);
										if ($row_codif_esp["Existencias"]>0){
											$id_almacen_traslado = $id_pto;
											$id_codif_esp = 	$row_codif_esp["IDCodificacionEspecifica"];
											//Salgo del for ya no necesito buscar mas
											break;
										}
									endforeach;
								endif;

								if(!empty($id_almacen_traslado)):
										// Realizo el movimiento de traslado en el almacen
										$frm['IDEstadoTraslado'] = 1;
										$sql_id_traslado = db_query("Select max(IDTraslado) as Siguiente From Traslado Where 1");
										$row_id_traslado = db_fetch_array($sql_id_traslado);
										$next_id_traslado =  (int)$row_id_traslado["Siguiente"]+1;

										$sql_traslado = "Insert Into Traslado (	IDTraslado, IDPuntoVentaOrigen, IDPuntoVentaDestino, IDEstadoTraslado, Observaciones, Fecha, UsuarioTrCr, FechaTrCr)
														Values ('".$next_id_traslado."','".$id_almacen_traslado."','16','1','Venta Tienda Virtual',NOW(),'VentaTiendaVirtual',NOW())";
										db_query($sql_traslado);

										//guardar en detalletraslado
										$sql_id_traslado_detalle = db_query("Select max(IDDetalleTraslado) as SiguienteDetalle From DetalleTraslado Where 1");
										$row_id_traslado_detalle = db_fetch_array($sql_id_traslado_detalle);
										$iddetalle =  (int)$row_id_traslado_detalle["SiguienteDetalle"]+1;

										$Codificacion = $id_codif_esp;
										$Cantidad = 1;

										$sql_insert = "INSERT INTO DetalleTraslado (IDDetalleTraslado, IDTraslado,IDPuntoVentaOrigen, IDCodificacionEspecifica, Cantidad, NumeroTarjeta, UsuarioTrCr, FechaTrCr ) ";
										$sql_insert .= "VALUES ('$iddetalle','".$next_id_traslado."','".$id_almacen_traslado."','$Codificacion','$Cantidad','$NumeroTarjeta','VentaTiendaVirtual',NOW())";
										db_query($sql_insert);

										//$mail_tienda = "Select * From PuntoVenta";
										$html .= "
										Cordial saludo,\r\n
										Le informamos que se realizó un traslado autom&aacute;tico de la siguiente referencia a Vta  Fca por una compra de tienda virtual
										\r\n\r\n";

										$sql_pto_vta_orig = db_query( "SELECT * FROM PuntoVenta Where IDPuntoVenta = '".$id_almacen_traslado."' ");
										$r_pto_vta_orig = db_fetch_array( $sql_pto_vta_orig);

										$html .= "
											Punto Venta Origen: ".$r_pto_vta_orig["Nombre"]."\r\n
											Referencia: ".$datos_referencia["Referencia"]."\r\n
											Talla: ".$datos_referencia["Talla"]."\r\n
											Cantidad: ".$datos_referencia["Cantidad"]."\r\n
											Pedido Web # ".$NumeroPedido."
											\r\n\r\n
										Por favor revisarlo y sacar inmediatamente el producto de exhibicion, tambi&eacute; en se solicita enviar la bolsa respectiva";

										if( !empty( $html ) ):											
											$Subject = "TRASLADO VTA FCA Tienda Virtual";
											//$para      = 'supervisor@calzadocaprino.com';
											$para      = 'jorgechirivi@gmail.com';
											$titulo    = 'TRASLADO VTA FCA Tienda Virtual';
											$mensaje   = $html;
											$cabeceras = 'From: tienda@calzadocaprino.com' . "\r\n" .
												'Reply-To: tienda@calzadocaprino.com' . "\r\n" .
												'X-Mailer: PHP/' . phpversion();
											//$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";	
											$cabeceras  .= 'MIME-Version: 1.0' . "\r\n";

											mail($para, $titulo, $mensaje, $cabeceras);
										endif;

										$respuesta_ws .= " Traslado Terminado: " . $datos_referencia["Referencia"];
								else:
									$PedidoValido="N";
									$respuesta_ws .= "No se encontro almacen para hacer traslado del producto: " . $datos_referencia["Referencia"];
								endif;

					endfor;
				}// end while

				$array_bonos = json_decode($Bonos, true);            	
				foreach ($array_bonos as $datos_bono){
					if(!empty($datos_bono["IDBonoFidelizacion"])):
							$sql_bono_redimido="Update BonoFidelizacion set  Estado = 'W', FechaTrEd = NOW(), UsuarioTrEd = 'Web' Where IDBonoFidelizacion = '".$datos_bono["IDBonoFidelizacion"]."' Limit 1";
							db_query($sql_bono_redimido);
					endif;					
				}

			if($PedidoValido=="S"){
				$respuesta["message"] = "Pedido insertado con exito. " .$respuesta_ws;
            	$respuesta["success"] = true;
        		$respuesta["response"] = null;
			}
			else{
				$respuesta["message"] = "Error al insertar el pedido: " .$respuesta_ws;
				$respuesta["success"] = false;
				$respuesta["response"] = null;
			}

			
		}
		else{
			$respuesta["message"] = "Atencion, faltan parametros";
            $respuesta["success"] = false;
        	$respuesta["response"] = null;
		}
		
		return $respuesta;
	}

}//end class
?>
