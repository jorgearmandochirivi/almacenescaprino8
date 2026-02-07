<?php
include("../config.inc.php");



//Reviertir Entrada con ref que no existian al momento de ingresar
$id_ref_mal = 709;
$id_pto_vta = 12;
$sql_ref_mal = "SELECT * 
				FROM  `DetallePedidoTerceroReferencia` 
				WHERE  `IDDetallePedidoTercero` = '".$id_ref_mal."'
					and IDPuntoVenta = '".$id_pto_vta."'
				AND  `Estado` LIKE  'Recibido'";
$result_ref_mal = db_query($sql_ref_mal);				
while ($row_ref_mal = db_fetch_array($result_ref_mal )):
	// Consulto el punto de venta ref	
		$sql_ped_ref = db_query("Select * From DetallePedidoTercero Where IDPedidoTercero = '".$row_ref_mal["IDPedidoTercero"]."' and IDDetallePedidoTercero = '".$row_ref_mal["IDDetallePedidoTercero"]."'");	
		$row_ped_ref = db_fetch_array($sql_ped_ref);
		$sql_ref = db_query("Select * From Referencia Where Numero = '".$row_ped_ref["ReferenciaCaprino"].$row_ped_ref["CodigoColor"]."'");
		$row_ref = db_fetch_array($sql_ref);
		$sql_pto_ref = db_query("Select * From PuntoVentaReferencia Where IDReferencia = '".$row_ref["IDReferencia"]."' and IDPuntoVenta = '".$row_ref_mal["IDPuntoVenta"]."'");
		$row_pto_ref = db_fetch_array($sql_pto_ref);
		
		unset($array_tallas_rel);
		$id_tallas_rel = "";
	
		//Consulto las otra tallas posibles ya que una talla esta creada mas de una vez
		$nombre_talla = get_field("Talla","Descripcion","IDTalla",$row_ref_mal["IDTalla"]);
		$sql_tallas_rel = db_query("Select * From Talla Where Descripcion = '".$nombre_talla."'");
		while($row_talla = db_fetch_array($sql_tallas_rel)):
			$array_tallas_rel []=$row_talla[IDTalla]; 
		endwhile;
		
		if (count($array_tallas_rel)>0):
			$id_tallas_rel = implode(",",$array_tallas_rel);
		endif;


	echo "<br>" . $sql_entradas = "SELECT *
	FROM  `Entrada` 
	WHERE `Remision` = '".$row_ref_mal["Remision"]."'
	AND  `IDPuntoVenta` = '".$row_ref_mal["IDPuntoVenta"]."'
	AND IDPuntoVentaReferencia = '".$row_pto_ref["IDPuntoVentaReferencia"]."'";
	
	$result_entrada = db_query($sql_entradas);
	echo "<br>ATENCION BORRAR ENTRADA: Esta Entrada no esta en la remision: IDPedidoTercero: " . $row_ref_mal["IDPedidoTercero"] . " REF: ".$row_ped_ref["ReferenciaCaprino"].$row_ped_ref["CodigoColor"] . " Talla: ". get_field("Talla","Descripcion","IDTalla",$row_ref_mal[IDTalla]);
	
	while ($row_entrada = db_fetch_array($result_entrada)):
		print_r($row_entrada);
	endwhile;
	exit;
	
endwhile;

echo $sql_pedido = "<br>" . "Update set DetallePedidoTerceroReferencia Set Estado = 'Enviado', CantidadRecibido = 0, FechaRecibido = '', Remision= '', NumeroFactura = '' Where IDDetallePedidoTercero ='".$id_ref_mal."'  AND IDPuntoVenta =  '".$id_pto_vta ."' AND  `Estado` LIKE  'Recibido'";
exit;
//Fin revertir




/*
//REVISO ENTRADAS DE TERCERO QUE NO TENGAN REMISION
//echo "<br>" . $sql_pedido_tercero = "Select * From DetallePedidoTerceroReferencia Where Estado = 'Recibido' and Remision > 0 and NumeroFactura <> '' and IDDetallePedidoTercero = 397 and IDPuntoVenta = 4 ";
$sql_pedido_tercero = "Select * From DetallePedidoTerceroReferencia Where Estado = 'Recibido' and Remision > 0 and NumeroFactura <> '' and FEchaRecibido >= '2016-11-01'";
$result_pedido_tercero = db_query($sql_pedido_tercero);
while ($row_pedido_tercero = db_fetch_array($result_pedido_tercero)):
		// Consulto el punto de venta ref	
		$sql_ped_ref = db_query("Select * From DetallePedidoTercero Where IDPedidoTercero = '".$row_pedido_tercero["IDPedidoTercero"]."' and IDDetallePedidoTercero = '".$row_pedido_tercero["IDDetallePedidoTercero"]."'");	
		$row_ped_ref = db_fetch_array($sql_ped_ref);
		$sql_ref = db_query("Select * From Referencia Where Numero = '".$row_ped_ref["ReferenciaCaprino"].$row_ped_ref["CodigoColor"]."'");
		$row_ref = db_fetch_array($sql_ref);
		$sql_pto_ref = db_query("Select * From PuntoVentaReferencia Where IDReferencia = '".$row_ref["IDReferencia"]."' and IDPuntoVenta = '".$row_pedido_tercero["IDPuntoVenta"]."'");
		$row_pto_ref = db_fetch_array($sql_pto_ref);
		
		unset($array_tallas_rel);
		$id_tallas_rel = "";
	
		//Consulto las otra tallas posibles ya que una talla esta creada mas de una vez
		$nombre_talla = get_field("Talla","Descripcion","IDTalla",$row_pedido_tercero["IDTalla"]);
		$sql_tallas_rel = db_query("Select * From Talla Where Descripcion = '".$nombre_talla."'");
		while($row_talla = db_fetch_array($sql_tallas_rel)):
			$array_tallas_rel []=$row_talla[IDTalla]; 
		endwhile;
		
		if (count($array_tallas_rel)>0):
			$id_tallas_rel = implode(",",$array_tallas_rel);
		endif;


	$sql_entradas = "SELECT *
	FROM  `Entrada` 
	WHERE `Remision` = '".$row_pedido_tercero["Remision"]."'
	AND  `IDPuntoVenta` = '".$row_pedido_tercero["IDPuntoVenta"]."'
	AND IDPuntoVentaReferencia = '".$row_pto_ref["IDPuntoVentaReferencia"]."'";
	
	$result_entrada = db_query($sql_entradas);
	if(db_num_rows($result_entrada)<=0):
		echo "<br>ATENCION: Esta Entrada no esta en la remision: IDPedidoTercero: " . $row_pedido_tercero["IDPedidoTercero"] . " REF: ".$row_ped_ref["ReferenciaCaprino"].$row_ped_ref["CodigoColor"] . " Talla: ". get_field("Talla","Descripcion","IDTalla",$row_pedido_tercero[IDTalla]);
	endif;
	
	//while ($row_entrada = db_fetch_array($result_entrada)):
		//print_r($row_entrada);
	//endwhile;
	
endwhile;

exit;
*/



//FIN REVISO ENTRADAS DE TRECERO QUE NO TENGAN REMISION




$sql_entradas_defecto = "SELECT COUNT( IDPuntoVentaReferencia ) , Entrada . * 
FROM  `Entrada` 
WHERE `Remision` =2235
AND  `IDPuntoVenta` = 29
GROUP BY IDPuntoVentaReferencia, IDTalla, Remision
HAVING COUNT( IDPuntoVentaReferencia ) >1
AND Fecha >=  '2015-06-01'";


$sql_entradas_defecto = "SELECT COUNT( IDPuntoVentaReferencia ) , Entrada . * 
FROM  `Entrada` 
WHERE 1
GROUP BY IDPuntoVentaReferencia, IDTalla, Remision
HAVING COUNT( IDPuntoVentaReferencia ) >1
AND Fecha >=  '2015-06-01'";


$result_entradas_defecto = db_query($sql_entradas_defecto);
while ($row_entrada_defecto = db_fetch_array($result_entradas_defecto)):
	$sql_pedido_tercero = "Select * From DetallePedidoTerceroReferencia Where Remision = '".$row_entrada_defecto["Remision"]."' and NumeroFactura  = '".$row_entrada_defecto["NumeroFactura"]."'";
	$result_pedido_tercero = db_query($sql_pedido_tercero);
	$total_detalle_ped = db_num_rows($result_pedido_tercero);
	if($total_detalle_ped > 0 && !in_array($row_entrada_defecto["Remision"],$array_remision)):
		$array_remision[] = $row_entrada_defecto["Remision"];
		echo "<br>Remision: " . $row_entrada_defecto["Remision"] . " Pto: " . $row_entrada_defecto["IDPuntoVenta"];
			
			
			/*
			//Revertir el inventario actual
			$sql_entrada_actual = "Select * From Entrada Where Remision = '".$row_entrada_defecto["Remision"]."' and NumeroFactura = '".$row_entrada_defecto["NumeroFactura"]."'";
			$result_entrada_actual = db_query($sql_entrada_actual);
			while ($row_entrada_actual = db_fetch_array($result_entrada_actual)):
				unset($array_tallas_rel);
				$id_tallas_rel = "";
				
				//Consulto las otra tallas posibles ya que una talla esta creada mas de una vez
				$nombre_talla = get_field("Talla","Descripcion","IDTalla",$row_entrada_actual[IDTalla]);
				$sql_tallas_rel = db_query("Select * From Talla Where Descripcion = '".$nombre_talla."'");
				while($row_talla = db_fetch_array($sql_tallas_rel)):
					$array_tallas_rel []=$row_talla[IDTalla]; 
				endwhile;
				
				if (count($array_tallas_rel)>0):
					$id_tallas_rel = implode(",",$array_tallas_rel);
				endif;
				
				
				//Si tiene Existencias le descuento de lo contraio no
				$sql_existencias = "Select * From CodificacionEspecifica  WHERE IDPuntoVentaReferencia = '".$row_entrada_actual[IDPuntoVentaReferencia]."' AND IDTalla  in (".$id_tallas_rel.") Limit 1";
				$result_existencias = db_query($sql_existencias);
				$row_existencias = db_fetch_array($result_existencias);
				if ($row_existencias["Existencias"]>0):				
					$sql_descontar = "UPDATE CodificacionEspecifica SET Existencias = Existencias - ".(int)$row_entrada_actual[Cantidad] ." WHERE IDPuntoVentaReferencia = '".$row_entrada_actual[IDPuntoVentaReferencia]."' AND IDTalla  in (".$id_tallas_rel.")";
					$result_descontar = db_query($sql_descontar);
				endif;	
			endwhile;
			//FIN Revertir el inventario actual
		
		
			//Borro la entrada creada y hago copia
			$sql_copia_entrada = "INSERT INTO EntradaBck Select * From Entrada Where Remision = '".$row_entrada_defecto["Remision"]."' and NumeroFactura = '".$row_entrada_defecto["NumeroFactura"]."'";
			$result_copia_entrada = db_query($sql_copia_entrada);
			$sql_borra_entrada = "Delete From Entrada Where Remision = '".$row_entrada_defecto["Remision"]."' and NumeroFactura = '".$row_entrada_defecto["NumeroFactura"]."'";
			$result_borra_entrada = db_query($sql_borra_entrada);
			//Crear la entrada Correctamente
			while ($row_pedido_tercero = db_fetch_array($result_pedido_tercero)):
					
					
					// Consulto el punto de venta ref	
					$sql_ped_ref = db_query("Select * From DetallePedidoTercero Where IDPedidoTercero = '".$row_pedido_tercero["IDPedidoTercero"]."' and IDDetallePedidoTercero = '".$row_pedido_tercero["IDDetallePedidoTercero"]."'");	
					$row_ped_ref = db_fetch_array($sql_ped_ref);
					$sql_ref = db_query("Select * From Referencia Where Numero = '".$row_ped_ref["ReferenciaCaprino"].$row_ped_ref["CodigoColor"]."'");
					$row_ref = db_fetch_array($sql_ref);
					$sql_pto_ref = db_query("Select * From PuntoVentaReferencia Where IDReferencia = '".$row_ref["IDReferencia"]."' and IDPuntoVenta = '".$row_pedido_tercero["IDPuntoVenta"]."'");
					$row_pto_ref = db_fetch_array($sql_pto_ref);
					$identrada = get_maxID("Entrada","IDEntrada");
					$inserta_entrada_ok = "INSERT INTO Entrada VALUES($identrada,'$row_entrada_defecto[Remision]','$row_entrada_defecto[NumeroFactura]','".$row_pedido_tercero[FechaRecibido]."','".$row_pto_ref["IDPuntoVentaReferencia"]."','".$row_pedido_tercero["IDTalla"]."','".$row_pedido_tercero["CantidadRecibido"]."','".$row_pedido_tercero[FechaRecibido]."','".$row_pedido_tercero[IDPuntoVenta]."')";									
					$result_inserta_entrada = db_query($inserta_entrada_ok);
					
					
					unset($array_tallas_rel);
					$id_tallas_rel = "";
					
					//Consulto las otra tallas posibles ya que una talla esta creada mas de una vez
					$nombre_talla = get_field("Talla","Descripcion","IDTalla",$row_pedido_tercero["IDTalla"]);
					$sql_tallas_rel = db_query("Select * From Talla Where Descripcion = '".$nombre_talla."'");
					while($row_talla = db_fetch_array($sql_tallas_rel)):
						$array_tallas_rel []=$row_talla[IDTalla]; 
					endwhile;
					
					if (count($array_tallas_rel)>0):
						$id_tallas_rel = implode(",",$array_tallas_rel);
					endif;
					
					
					//Ajsute el inventario
					$sql_aumentar = "UPDATE CodificacionEspecifica SET Existencias = Existencias + ".(int)$row_pedido_tercero["CantidadRecibido"] ." WHERE IDPuntoVentaReferencia = '".$row_pto_ref["IDPuntoVentaReferencia"]."' AND IDTalla  in (".$id_tallas_rel.")";
					$result_aumentar = db_query($sql_aumentar);				
					
					
			endwhile;
			
			*/
	
		
	endif;
endwhile;


/*
//Arreglo los inventarios deacuerdo con las ventas de esos dias
$sql_entrada_bck = "Select * From EntradaBck Where Remision = 1959 Group by Remision, IDPuntoVenta";
//$sql_entrada_bck = "Select * From EntradaBck Where 1 Group by Remision, IDPuntoVenta";
$result_entrada_bck = db_query($sql_entrada_bck);
while ($row_entrada_bck = db_fetch_array($result_entrada_bck)):
	$sql_entrada_actual = "Select * From Entrada Where Remision = '".$row_entrada_bck["Remision"]."' and  IDPuntoVenta = '".$row_entrada_bck["IDPuntoVenta"]."'";	
	$result_entrada_actual = db_query($sql_entrada_actual);
	while ($row_entrada_actual = db_fetch_array($result_entrada_actual)):	
			//Verifico si se vendio	
			$sql_cod_esp = db_query("Select * From CodificacionEspecifica Where IDPuntoVentaReferencia = '".$row_entrada_actual["IDPuntoVentaReferencia"]."' and IDTalla = '".$row_entrada_actual["IDTalla"]."'");
			$row_cod_esp = db_fetch_array($sql_cod_esp);			
			$sql_factura = db_query("Select F.* From Factura F, DetalleFactura DF Where F.IDFactura = DF.IDFactura and DF.IDCodificacionEspecifica = '".$row_cod_esp["IDCodificacionEspecifica"]."' and DF.IDPuntoVenta = '".$row_entrada_actual["IDPuntoVenta"]."' and F.FechaFactura >= '".$row_entrada_actual["Fecha"]."'");
			if (db_num_rows($sql_factura)>0){
				$row_fac = db_fetch_array($sql_factura);
				echo "<br>Este ya fue vendido " . $row_entrada_actual["IDEntrada"] . " Factura: " . $row_fac["NumeroFactura"];	
				//Ajsute el inventario
				$sql_disminuir = "UPDATE CodificacionEspecifica SET Existencias = Existencias - 1 WHERE IDPuntoVentaReferencia = '".$row_entrada_actual["IDPuntoVentaReferencia"]."' AND IDTalla  = '".$row_entrada_actual["IDTalla"]."'";
				//$result_disminuir = db_query($sql_disminuir);	
			}
	endwhile;		
endwhile;
*/



//Arreglo los inventarios deacuerdo con remision
$sql_entrada_bck = "Select * From Entrada Where Remision = 10272 and IDPuntoVenta = 10 Group by Remision, IDPuntoVenta";
//$sql_entrada_bck = "Select * From EntradaBck Where 1 Group by Remision, IDPuntoVenta";
$result_entrada_bck = db_query($sql_entrada_bck);
while ($row_entrada_bck = db_fetch_array($result_entrada_bck)):
	$sql_entrada_actual = "Select * From Entrada Where Remision = '".$row_entrada_bck["Remision"]."' and  IDPuntoVenta = '".$row_entrada_bck["IDPuntoVenta"]."'";	
	$result_entrada_actual = db_query($sql_entrada_actual);
	while ($row_entrada_actual = db_fetch_array($result_entrada_actual)):	
			unset($array_tallas_rel);
			//Consulto las otra tallas posibles ya que una talla esta creada mas de una vez
			$nombre_talla = get_field("Talla","Descripcion","IDTalla",$row_entrada_actual[IDTalla]);
			$sql_tallas_rel = db_query("Select * From Talla Where Descripcion = '".$nombre_talla."'");
			while($row_talla = db_fetch_array($sql_tallas_rel)):
				$array_tallas_rel []=$row_talla[IDTalla]; 
			endwhile;
			
			if (count($array_tallas_rel)>0):
				$id_tallas_rel = implode(",",$array_tallas_rel);
			endif;
			
			
			
			//Verifico si se vendio	
			$sql_cod_esp = db_query("Select * From CodificacionEspecifica Where IDPuntoVentaReferencia = '".$row_entrada_actual["IDPuntoVentaReferencia"]."' and IDTalla = '".$row_entrada_actual["IDTalla"]."'");
			$row_cod_esp = db_fetch_array($sql_cod_esp);			
			
			// verifico que el inventario este igual
			$sql_inv = "Select * From CodificacionEspecifica WHERE IDPuntoVentaReferencia = '".$row_entrada_actual["IDPuntoVentaReferencia"]."' AND IDTalla   in (".$id_tallas_rel.") ";
			$qry_inv = db_query($sql_inv);
			$row_inv = db_fetch_array($qry_inv);	
			
			
			
			if ($row_inv["Existencias"]>$row_entrada_actual["Cantidad"])	:
			
					$sql_ref = "Select * From PuntoVentaReferencia WHERE IDPuntoVentaReferencia = '".$row_entrada_actual["IDPuntoVentaReferencia"]."' AND IDPuntoVenta = '".$row_entrada_bck["IDPuntoVenta"]."'";
					$qry_ref = db_query($sql_ref);
					$row_ref = db_fetch_array($qry_ref);	
					$referencia = get_field("Referencia","Numero","IDReferencia",$row_ref[IDReferencia]);
					$talla =  get_field("Talla","Nombre","IDTalla",$row_inv[IDTalla]);
					$pto_venta  = get_field("PuntoVenta","Nombre","IDPuntoVenta",$row_entrada_bck["IDPuntoVenta"]);
	
			
				echo "<br><br>Atencion esta referencia tiene mas de lo que deberia:  Referencia: " . $referencia . " Talla: " . $talla . " Punto Venta: " . $pto_venta . " Remision: " . $row_entrada_actual["Remision"] . " Se actualizara el inventario  a: " . $row_entrada_actual['Cantidad'] . " existencias";
				echo "<br>" . $sql_disminuir = "UPDATE CodificacionEspecifica SET Existencias = ".$row_entrada_actual['Cantidad']." WHERE IDPuntoVentaReferencia = '".$row_entrada_actual["IDPuntoVentaReferencia"]."' AND IDTalla  in (".$id_tallas_rel.")";
				//$result_disminuir = db_query($sql_disminuir);	
			endif;
			
			if ($row_inv["Existencias"]==0 and $row_entrada_actual["Cantidad"]>0)	:
			
					$sql_ref = "Select * From PuntoVentaReferencia WHERE IDPuntoVentaReferencia = '".$row_entrada_actual["IDPuntoVentaReferencia"]."' AND IDPuntoVenta = '".$row_entrada_bck["IDPuntoVenta"]."'";
					$qry_ref = db_query($sql_ref);
					$row_ref = db_fetch_array($qry_ref);	
					$referencia = get_field("Referencia","Numero","IDReferencia",$row_ref[IDReferencia]);
					$talla =  get_field("Talla","Nombre","IDTalla",$row_inv[IDTalla]);
					$pto_venta  = get_field("PuntoVenta","Nombre","IDPuntoVenta",$row_entrada_bck["IDPuntoVenta"]);
	
			
				echo "<br><br>Atencion esta referencia tiene MENOS de lo que deberia:  Referencia: " . $referencia . " Talla: " . $talla . " Punto Venta: " . $pto_venta . " Remision: " . $row_entrada_actual["Remision"] . " Se actualizara el inventario  a: " . $row_entrada_actual['Cantidad'] . " existencias";
				echo "<br>" . $sql_disminuir = "UPDATE CodificacionEspecifica SET Existencias = ".$row_entrada_actual['Cantidad']." WHERE IDPuntoVentaReferencia = '".$row_entrada_actual["IDPuntoVentaReferencia"]."' AND IDTalla  in (".$id_tallas_rel.")";
				//$result_disminuir = db_query($sql_disminuir);	
			endif;
			
			/*
			$sql_factura = db_query("Select F.* From Factura F, DetalleFactura DF Where F.IDFactura = DF.IDFactura and DF.IDCodificacionEspecifica = '".$row_cod_esp["IDCodificacionEspecifica"]."' and DF.IDPuntoVenta = '".$row_entrada_actual["IDPuntoVenta"]."' and F.FechaFactura >= '".$row_entrada_actual["Fecha"]."'");
			if (db_num_rows($sql_factura)>0){
				$row_fac = db_fetch_array($sql_factura);
				echo "<br>Este ya fue vendido " . $row_entrada_actual["IDEntrada"] . " Factura: " . $row_fac["NumeroFactura"];	
				//Ajsute el inventario
				$sql_disminuir = "UPDATE CodificacionEspecifica SET Existencias = Existencias - 1 WHERE IDPuntoVentaReferencia = '".$row_entrada_actual["IDPuntoVentaReferencia"]."' AND IDTalla  = '".$row_entrada_actual["IDTalla"]."'";
				//$result_disminuir = db_query($sql_disminuir);	
			}
			*/
	endwhile;		
endwhile;



echo "<br><br>Terminado";





exit;
$sql_pedido_tercero = "Select * From DetallePedidoTerceroReferencia Where Estado = 'Recibido' and Remision > 0 and NumeroFactura <> '' Group by NumeroFactura,Remision";
$result_pedido_tercero = db_query($sql_pedido_tercero);
while ($row_pedido_tercero = db_fetch_array($result_pedido_tercero)):
		

		$id_pedido_tercero = $row_pedido_tercero["IDPedidoTercero"];
		$numero_factura = $row_pedido_tercero["NumeroFactura"];;
		$remision = $row_pedido_tercero["Remision"];
	
		$sql_detalle_pedido_tercero = "Select * From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$id_pedido_tercero."' and NumeroFactura = '".$numero_factura."' and Remision = '".$remision."'";		
		$result_detalle_pedido_tercero = db_query($sql_detalle_pedido_tercero);
		$total_detalle_ped = db_num_rows($result_detalle_pedido_tercero);
		
		$sql_entrada_remision = db_query("SELECT * FROM  `EntradaBck` WHERE  `Remision` = '".$remision."' and NumeroFactura = '".$numero_factura."'");
		$total_entradas_remision = db_num_rows($sql_entrada_remision);
		
		if ($total_entradas_remision<$total_detalle_ped && $total_entradas_remision>0):
		
				while ($row_detalle_pedido = db_fetch_array($result_detalle_pedido_tercero)):	
					// Consulto el punto de venta ref	
					$sql_ped_ref = db_query("Select * From DetallePedidoTercero Where IDPedidoTercero = '".$row_detalle_pedido["IDPedidoTercero"]."' and IDDetallePedidoTercero = '".$row_detalle_pedido["IDDetallePedidoTercero"]."'");	
					$row_ped_ref = db_fetch_array($sql_ped_ref);
					$sql_ref = db_query("Select * From Referencia Where Numero = '".$row_ped_ref["ReferenciaCaprino"].$row_ped_ref["CodigoColor"]."'");
					$row_ref = db_fetch_array($sql_ref);
					$sql_pto_ref = db_query("Select * From PuntoVentaReferencia Where IDReferencia = '".$row_ref["IDReferencia"]."' and IDPuntoVenta = '".$row_detalle_pedido["IDPuntoVenta"]."'");
					$row_pto_ref = db_fetch_array($sql_pto_ref);
					
					//Busco la entrada y verifico si quedo bien	
					$sql_entrada_item = db_query("SELECT * FROM  `EntradaBck` WHERE  `Remision` = '".$remision."' AND  `IDPuntoVenta` = '".$row_detalle_pedido["IDPuntoVenta"]."' and IDPuntoVentaReferencia = '".$row_pto_ref["IDPuntoVentaReferencia"]."' and IDTalla = '".$row_detalle_pedido["IDTalla"]."'");
					$total_entradas = db_num_rows($sql_entrada_item);
					if ((int)$total_entradas<=0){								
						$row_entrada_item = db_fetch_array($sql_ref);	
						echo "<br>Esta referencia no quedo en la entrada: " . $row_ref["Numero"] ." de la talla " . get_field("Talla","Nombre","IDTalla",$row_detalle_pedido["IDTalla"]) . " Numero Pares: " . $row_detalle_pedido["CantidadRecibido"] . " Factura " . $numero_factura . " Remision " . $remision . " IDDetallePRef " . $row_detalle_pedido["IDDetallePedidoTerceroReferencia"];
						echo "<br>" . $sql_detalle_pedido_tercero;
						echo "<br>" . "SELECT * FROM  `EntradaBck` WHERE  `Remision` = '".$remision."' AND  `IDPuntoVenta` = '".$row_detalle_pedido["IDPuntoVenta"]."' and IDPuntoVentaReferencia = '".$row_pto_ref["IDPuntoVentaReferencia"]."' and IDTalla = '".$row_detalle_pedido["IDTalla"]."'";
						echo "<br>" . $total_entradas_remision."<".$total_detalle_ped;
						
						
						$sql_caso = db_query("Insert Into CasoTercero (ID) Values ('".$row_detalle_pedido["IDDetallePedidoTerceroReferencia"]."')");
					}
					
					//Actualizo el detalle
					//echo "<br>".$sql_actualiza_estado= "Update DetallePedidoTerceroReferencia Set Estado = 'Enviado', CantidadRecibido = 0, Remision = '".$frm[Remision]."' Where IDDetallePedidoTerceroReferencia = '".$row_detalle_pedido["IDDetallePedidoTerceroReferencia"]."'";
				endwhile;
		endif;		
				
		
		
		//actualizo el inventario de acuerdo a la entrada realizada 
		$sql_entrada = "Select * From Entrada Where Remision = '".$remision."' and NumeroFactura = '".$numero_factura."'";
		$result_entrada = db_query($sql_entrada);
		while ($row_entrada = db_fetch_array($result_entrada)):	
			//Actualizo el inventario	
			//echo "<br>" . "UPDATE CodificacionEspecifica SET Existencias = Existencias - ".$row_entrada["Cantidad"]." WHERE IDPuntoVentaReferencia = '".$row_entrada["IDPuntoVentaReferencia"]."' AND IDTalla = '".$row_entrada["IDTalla"]."'";
		endwhile;
		
		
		//Borro la entrada creada
		//echo "<br>" . $sql_borra_entrada = "Delete From Entrada Where Remision = '".$remision."' and NumeroFactura = '".$numero_factura."'";
	
endwhile;


	//ACTUALIZAR INVENTARIO SEGUN ENTRADA
		/*
		$sql_entrada_actual = "Select * From Entrada Where Remision = '802797' and NumeroFactura = '3792'";
		$result_entrada_actual = db_query($sql_entrada_actual);
		while ($row_entrada_actual = db_fetch_array($result_entrada_actual)):
		unset($array_tallas_rel);
			$id_tallas_rel = "";
			
			//Consulto las otra tallas posibles ya que una talla esta creada mas de una vez
			$nombre_talla = get_field("Talla","Descripcion","IDTalla",$row_entrada_actual[IDTalla]);
			$sql_tallas_rel = db_query("Select * From Talla Where Descripcion = '".$nombre_talla."'");
			while($row_talla = db_fetch_array($sql_tallas_rel)):
				$array_tallas_rel []=$row_talla[IDTalla]; 
			endwhile;
			
			if (count($array_tallas_rel)>0):
				$id_tallas_rel = implode(",",$array_tallas_rel);
			endif;
			
			echo "<br>" . $sql_aumentar = "UPDATE CodificacionEspecifica SET Existencias = Existencias + ".(int)$row_entrada_actual[Cantidad] ." WHERE IDPuntoVentaReferencia = '".$row_entrada_actual[IDPuntoVentaReferencia]."' AND IDTalla  in (".$id_tallas_rel.")";
			$result_aumentar = db_query($sql_aumentar);		
		
		
		endwhile;
		*/

?>