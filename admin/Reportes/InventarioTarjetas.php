<body> <?php
function header_export($file){


	$filename = $file.date('m_d_Y_H_i').".xls";
	
	header("Pragma: ");
	header("Cache-control: ");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i ")." GMT");
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment;filename=$filename");

} // End funtion header_export

$TitleMod ="Tarjetas Punto de Venta  ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta );

$Table = "TarjetaPunto";
$TableJoin = "";
$Key = "CodigoTarjeta";
$Title = " Consultar Inventario Tarjetas ";
$MOD = "InventarioTarjetas";
$m="InventarioTarjetas";

$filedir = $dirroot."files/";
$fileexp = $filedir."InventarioTarjetas".$fecha."html";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
		
if( $_POST['Exportar'] == 'S')
	$action = "export";




if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			
			case "trasladar":
				db_query( "BEGIN" );

				foreach( $_POST["Tarjeta"] as $codigotarjeta => $tarjeta )
				{
				
					//TRAER DATOS DE LA TARHJETA
					$sql_tarjeta = " SELECT * FROM TarjetaPunto WHERE CodigoTarjeta = '" . $codigotarjeta . "'  ";
					$qry_tarjeta = db_query( $sql_tarjeta );
					$datos_tarjeta = db_fetch_array( $qry_tarjeta );

					//INSERTAR TRASLADO
					$idtraslado = get_maxID("TrasladoTarjeta","IDTrasladoTarjeta");
					$sql_insert = " INSERT INTO TrasladoTarjeta (IDTrasladoTarjeta, IDPuntoVentaOrigen, IDPuntoVentaDestino, CodigoTarjeta, Observaciones, Fecha, UsuarioTrCr, FechaTrCr) 
					VALUES ('" . $idtraslado . "','" . $_POST["IDPuntoVentaOrigen"] . "','" . $_POST["IDPuntoVenta"] . "','" . $codigotarjeta . "','" . $_POST["Observaciones"] . "',NOW(),'" . $ID_Usuario . "',NOW())  ";
					db_query( $sql_insert );


					//Actualizar Las existencias de tarjetas en el punto de destino
					$sql_puntoventareferencia = "select * FROM PuntoVentaReferencia WHERE IDPuntoVenta = '" . $_POST["IDPuntoVenta"]  . "' AND IDReferencia = '7615'";
					$qry_puntoventareferencia = db_query(  $sql_puntoventareferencia );
					$r_puntoventareferencia = db_fetch_array( $qry_puntoventareferencia );

					$sql_update_inventario = "UPDATE CodificacionEspecifica SET Existencias = Existencias + 1 WHERE IDPuntoVentaReferencia = '" . $r_puntoventareferencia["IDPuntoVentaReferencia"] . "' LIMIT 1 ";
					db_query( $sql_update_inventario );


					//ACtualizar existencias de tarjetas en el punto de origen
					$sql_puntoventareferencia = "select * FROM PuntoVentaReferencia WHERE IDPuntoVenta = '" . $_POST["IDPuntoVentaOrigen"]  . "' AND IDReferencia = '7615'";
					$qry_puntoventareferencia = db_query(  $sql_puntoventareferencia );
					$r_puntoventareferencia = db_fetch_array( $qry_puntoventareferencia );

					$sql_update_inventario = "UPDATE CodificacionEspecifica SET Existencias = Existencias - 1 WHERE IDPuntoVentaReferencia = '" . $r_puntoventareferencia["IDPuntoVentaReferencia"] . "' LIMIT 1 ";
					db_query( $sql_update_inventario );


					//ACTUALIZAR CODIGO DE LA TARJETA
					$sql_update = " UPDATE TarjetaPunto SET IDPuntoVenta = '" . $_POST["IDPuntoVenta"] . "' WHERE CodigoTarjeta = '" . $codigotarjeta . "' ";
					db_query( $sql_update );

					//exit;

				}//end for
				list_r();
				db_query( "COMMIT" );
				exit;
			break;

			case "export" :	
					unlink($fileexp);
					ob_start();	
					//seleccionareferencia("list");
					seleccionareferencia( "list");
					list_r($_POST['campo'],$_POST['referencia']);
					$page = ob_get_contents();
					$fecha = date( "Y-m-d" );
					$name = "Inventario".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$_POST["IDPuntoVenta"] ).$fecha.".html";
					$file = $filedir.$name;
					file_put_contents($file, $page);
					ob_end_clean();
					header_export($file);
					echo $page;
			break;
			case "list" :	
					seleccionareferencia( "list");
					list_r();
			break;
			default : 
				unlink($fileexp);
				ob_start();	
				//seleccionareferencia("list");
				seleccionareferencia( "list");
				list_r();
				$page = ob_get_contents();
				$fecha = date( "Y-m-d" );
				$name = "Inventario$fecha.xls";
				$file = $filedir.$name;
				
				file_put_contents($file, $page);
				ob_end_clean();
				header_export($file);
				echo $page;
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");

/*******************************************************************************************
	seleccionareferencia: formulario de busqueda para la referencia
	Parametros:
			$newmode : nieva accion a tomar con el submit
	Retorna:	
			Void
*******************************************************************************************/
function seleccionareferencia( $newmode)
{
	GLOBAL $Title, $MOD, $IDTipoReferencia;
?>	
	<br><br><br><br>
	<table cellspacing='0' cellpadding='2' border='0' align='center' class="forumline bordertable" width="700" >
		<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">
			<tr>
				<td class=maintitle colspan="2">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="117">Puntos de Venta	</td>
							<td><select name="IDPuntoVenta" onChange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><?php								
								$qry_punto = db_query("SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
								</select>
							</td>
							
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" class="button" name="enviar" value="Consultar">
								<input type=hidden name=action value=<?php echo $newmode?>><input type=hidden name=mod value="<?php echo $MOD?>">
                            </td>
						</tr>
					</table>
				</td>
			</tr>
		</form>
	</table>
	<?php
}//end function seleccionapuntoventa($idreferencia)


/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($campo="", $referencia=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$idReferencia,$IDPuntoVenta,$Title,$Saldo, $referencia, $IDTipoReferencia;
	 	
	 	$puntoventa = $IDPuntoVenta;
		$referencia = 7615;
		$talla = 16;
		
		$sql_punto_venta_referencia = " SELECT IDPuntoVentaReferencia FROM PuntoVentaReferencia WHERE IDReferencia = '" . $referencia . "' AND IDPuntoVenta = '" . $IDPuntoVenta . "'  ";
		$qry_punto_venta_referencia = db_query( $sql_punto_venta_referencia );
		$r_punto_venta_referencia = db_fetch_array( $qry_punto_venta_referencia );
		$puntoventaref = $r_punto_venta_referencia["IDPuntoVentaReferencia"];
		
		
		//existe registro?
		$sql_cod = "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$puntoventaref' AND IDTalla = '$talla' ";
		$qry_cod = db_query( $sql_cod );
		$r_cod = db_fetch_object( $qry_cod );
	 	
	 	

?>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="700">
		<tr>
			<td class="titlemedium"><b></b><span class="gen"><?php echo $Title." ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta ) ?> - <?php echo fecha(); ?></span></td>
		</tr>
	</table>
	
	<table width=700 cellpadding=0 cellspacing=0 align=center class=bordertable>
	
	

        <tr>
            <td>
            	<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">

                <table width="100%">
                    <tr>
                        <td class="titlemedium">Codigos de Tarjetas</td>
                        <td class="titlemedium">Estado</td>
                        <td class="titlemedium">Factura</td>
						<td class="titlemedium">Descripcion</td>
                    </tr>
                    <?php
					$cont = 0;
                   	$sql_tarjetas = "SELECT * FROM TarjetaPunto WHERE IDPuntoVenta = '" . $IDPuntoVenta . "' ";
					$qry_tarjetas = db_query( $sql_tarjetas );
					
					$disponibles = 0;
					
					while( $r_tarjetas = db_fetch_array( $qry_tarjetas ) )
					{
					?>
                    	<tr>
                        	<td class="row1">
                            	<?php
                                	echo $r_tarjetas["CodigoTarjeta"];
								?>
                            </td>
                            <td class="row1">
                            	<?php
                                	echo $r_tarjetas["Estado"];
								?>
                            </td>
                            <td class="row1">
                            	<?php
                            		if( $r_tarjetas["Estado"] == "D" )
                            		{
                            			$disponibles++;
                            	?>
                            			<!--<input type="checkbox" name="Tarjeta[<?php echo $r_tarjetas["CodigoTarjeta"]?>]" value="<?php echo $r_tarjetas["CodigoTarjeta"]?>" >-->
                            	<?php
                            		}//end if
									elseif($r_tarjetas["Estado"] == "V"){
										$vendidas++;
										$id_factura = get_field( "DetalleFactura","IDFactura","CodigoTarjeta",$r_tarjetas["CodigoTarjeta"] );
										$numero_factura = get_field( "Factura","NumeroFactura","IDFactura",$id_factura );
										$ptoventa_factura = get_field( "Factura","IDPuntoVenta","IDFactura",$id_factura );
										?>
                                        Factura Venta: <a href="<?php echo "?mod=Factura&action=edit&id=".$id_factura."&idpunto=".$ptoventa_factura.""; ?>"><?php echo $numero_factura; ?></a>
                                        <?php
									}
                                	
								?>
                            </td>
							<td class="row1">
								<?php echo $r_tarjetas["Descripcion"]; ?>
							</td>

                        </tr>
                    <?php
						$cont++;
					}//ednw hile
					?>
                    <tr>
                    	<td colspan="3">
                    		<!-- Tarjetas en Existencias: <?php echo $r_cod->Existencias ?><br> -->
                    		Tarjetas Disponibles (D): <?php echo $disponibles ?><br>
                            Tarjetas Vendidas (V): <?php echo $vendidas ?>
                    	</td>
                    </tr>
                    
                     
               </table>

			<!--
               <table width="100%">
               		<tr>
               			<td>Seleccione el punto de venta para realizar el traslado</td>
               		</tr>
               		<tr>
               			<td>
               				<select name="IDPuntoVenta" >
									<option value="">Seleccione Un Punto de Venta</option><?php								
								$qry_punto = db_query("SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							?>
							</select>

						</td>

               		</tr>

               		<tr>
               			<td>
               				

               				<textarea name="Observaciones" class="input" rows="7" cols="30"></textarea>


						</td>

               		</tr>

               		<tr>
               			<td>
               				

               				
							<input type="hidden" name="action" value="trasladar">
							<input type="hidden" name="mod" value="<?php echo $MOD?>">
							<input type="hidden" name="IDPuntoVentaOrigen" value="<?php echo $IDPuntoVenta ?>">
							<input type="submit" name="btnTrasladar" class="submit" value="Trasladar">

						</td>

               		</tr>


               </table>
               -->

               </form>


          	<td>
     	</td>

    </table>	


<?php
}// Enf function list()				
?>
