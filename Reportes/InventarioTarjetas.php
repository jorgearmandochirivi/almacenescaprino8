<body> <?


$TitleMod ="Tarjetas Punto de Venta  ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta );

$Table = "TarjetaPunto";
$TableJoin = "";
$Key = "CodigoTarjeta";
$Title = " Consultar Inventario Tarjetas ";
$MOD = "inventariotarjetas";
$m="inventariotarjetas";

$filedir = $dirroot."files/";
$fileexp = $filedir."inventariotarjetas".$fecha."html";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
		
if( $_POST['Exportar'] == 'S')
	$action = "export";


$permisos[0] = "3";

if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			
			case "trasladar":
				db_query( "BEGIN" );

				/*
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
					//ACTUALIZAR CODIGO DE LA TARJETA
					$sql_update = " UPDATE TarjetaPunto SET IDPuntoVenta = '" . $_POST["IDPuntoVenta"] . "' WHERE CodigoTarjeta = '" . $codigotarjeta . "' ";
					db_query( $sql_update );
					
				}//end for
				*/
				
				
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");

				$frm= vars_LOG($HTTP_POST_VARS);
				
				/******Estado Traslado Enviado  1*********/
				$frm['IDEstadoTraslado'] = 1;
				$Table = "Traslado";
				$TableJoin = "DetalleTraslado";
				$Key = "IDTraslado";
				$MOD = "cambiar";
				$frm['Fecha'] = date("Y-m-d H:i:s");
				
				$frm['IDTraslado'] = insert($frm);
				
				$sql_pto_ref = "select * From PuntoVentaReferencia Where IDPuntoVenta = '".$frm[IDPuntoVentaOrigen]."' and IDReferencia = '7615' limit 1";
				$r_pto_ref = db_query($sql_pto_ref);
				$row_pto_ref =  db_fetch_array($r_pto_ref);
				$IDPuntoVentaReferencia = $row_pto_ref["IDPuntoVentaReferencia"];
				$sql_codif = "select * From CodificacionEspecifica Where IDPuntoVentaReferencia = '".$IDPuntoVentaReferencia."' limit 1";
				$r_codif = db_query($sql_codif);
				$row_codif =  db_fetch_array($r_codif);
				$IDCodificacion = $row_codif["IDCodificacionEspecifica"];
				
				foreach($_POST["Tarjeta"] as $numerotarjeta):					
					$iddetalle = get_maxID("DetalleTraslado","IDDetalleTraslado");
					$Codificacion = $IDCodificacion;
					$Cantidad = 1;
					$NumeroTarjeta=$numerotarjeta;
					
					$sql_insert = "INSERT INTO DetalleTraslado (IDDetalleTraslado, IDTraslado,IDPuntoVentaOrigen, IDCodificacionEspecifica, Cantidad, NumeroTarjeta, UsuarioTrCr, FechaTrCr ) ";
					$sql_insert .= "VALUES ('$iddetalle','$frm[IDTraslado]','$frm[IDPuntoVentaOrigen]','$Codificacion','$Cantidad','$NumeroTarjeta','$frm[UsuarioTrCr]','$frm[FechaTrCr]')";
	
					//echo $sql_insert;
	
					db_query($sql_insert);
					
					//insertar el log
					insertlog($ID_Usuario,"DetalleTraslado",$iddetalle,"Insertar",$sql_insert); 
				endforeach;
								
				db_query("COMMIT");
				
				
				
				echo "<script>alert('Traslado Realizado. Esperando repuesta del punto de venta de destino...');</script>";
				
				//Imprimir la factura
				echo "<script>location.href='?mod=GenerarFactura';</script>";
				
				
				
				
				list_r();
				exit;
			break;

			
			
			default : 
				
				list_r();
				
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


/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r(){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$idReferencia,$IDPuntoVenta,$Title,$Saldo, $referencia, $IDTipoReferencia;
	 	
	 	$puntoventa = $IDPuntoVenta;
	 	
	 	

?>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="700">
		<tr>
			<td class="titlemedium"><b></b><span class="gen"><?=$Title." ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta ) ?> - <? echo fecha(); ?></span></td>
		</tr>
	</table>
	
	<table width=700 cellpadding=0 cellspacing=0 align=center class=bordertable>
	
	

        <tr>
            <td>
            	<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">

                <table width="100%">
                    <tr>
                      <td class="titlemedium">Item</td>
                        <td class="titlemedium">Codigos de Tarjetas</td>
                        <td class="titlemedium">Estado</td>
                        <td class="titlemedium">Trasladar/Factura</td>
						<td class="titlemedium">Descripcion</td>
                    </tr>
                    <?
					$cont = 0;
                   	$sql_tarjetas = "SELECT * FROM TarjetaPunto WHERE IDPuntoVenta = '" . $IDPuntoVenta . "' order by Estado,CodigoTarjeta";
					$qry_tarjetas = db_query( $sql_tarjetas );
					
					
					$consecutivo=1;
					while( $r_tarjetas = db_fetch_array( $qry_tarjetas ) )
					{
					?>
                    	<tr>
                    	  <td class="row1"><?php echo $consecutivo++; ?></td>
                        	<td class="row1">
                            	<?
                                	echo $r_tarjetas["CodigoTarjeta"];
								?>
                            </td>
                            <td class="row1">
                            	<?
                                	echo $r_tarjetas["Estado"];
								?>
                            </td>
                            <td class="row1">
                            	<?
                            		if( $r_tarjetas["Estado"] == "D" )
                            		{
                            	?>
                            			<input type="checkbox" name="Tarjeta[<?=$r_tarjetas["CodigoTarjeta"]?>]" value="<?=$r_tarjetas["CodigoTarjeta"]?>" >
                            	<?
                            		}//end if
									elseif($r_tarjetas["Estado"] == "V"){
										$vendidas++;
										$id_factura = get_field( "DetalleFactura","IDFactura","CodigoTarjeta",$r_tarjetas["CodigoTarjeta"] );
										$numero_factura = get_field( "Factura","NumeroFactura","IDFactura",$id_factura );
										$ptoventa_factura = get_field( "Factura","IDPuntoVenta","IDFactura",$id_factura );
										?>
                                        Factura Venta: <?php echo $numero_factura; ?>
                                        <?php
									}
                                	
								?>
                            </td>

							<td class="row1">
								<?php echo $r_tarjetas["Descripcion"]; ?>							
							</td>

                        </tr>
                    <?
						$cont++;
					}//ednw hile
					?>
                    
                    <tr>
                        <td class="maintitle" colspan="4">Total Tarjetas Codificadas: <?=$r_cod->Existencias . " / " . number_format( $cont, 0 ) ?></td>
                    </tr>
                     
               </table>


               <table width="100%">
               		<tr>
               			<td>Seleccione el punto de venta para realizar el traslado</td>
               		</tr>
               		<tr>
               			<td>
               				<select name="IDPuntoVentaDestino" >
									<option value="">Seleccione Un Punto de Venta</option><?								
								$qry_punto = db_query("SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre");
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
							<input type="hidden" name="mod" value="<?=$MOD?>">
							<input type="hidden" name="IDPuntoVentaOrigen" value="<?=$IDPuntoVenta ?>">
							<input type="submit" name="btnTrasladar" class="submit" value="Trasladar">

						</td>

               		</tr>


               </table>

               </form>


          	<td>
     	</td>

    </table>	


<? 			
}// Enf function list()				
?>