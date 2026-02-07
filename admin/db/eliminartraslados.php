<body> <?php
$TitleMod ="Eliminar Traslados";

$Table = "Traslado";
$TableJoin = "DetalleTraslado";
$Key = "IDTraslado";
$MOD = "eliminartraslados";
$m="db";

		switch (nvl($action)) {
			case "eliminartraslados" :

				print_form($_POST["IDTraslado"],$_POST["IDPuntoVenta"],"eliminaahorasi","Eliminar Traslado","submit");

			break;
			case "eliminaahorasi" :

				db_query("BEGIN");
				if(!empty( $_POST["idtraslado"] ) && !empty($_POST["idpuntoventa"]) )
				{

					$sql_detalle = "DELETE FROM DetalleTraslado WHERE IDTraslado = '" . $_POST["idtraslado"] . "' AND IDPuntoVentaOrigen = '" . $_POST["idpuntoventa"] . "'  ";
					db_query($sql_detalle);

					$sql_traslado = "DELETE FROM Traslado WHERE IDTraslado = '" . $_POST["idtraslado"] . "' AND IDPuntoVentaOrigen = '" . $_POST["idpuntoventa"] . "' ";
					db_query( $sql_traslado );

					db_query( "COMMIT" );
					print_form("","","eliminartraslados","Validar Traslado","submit");

				}//end if
			break;
			default :
				print_form("","","eliminartraslados","Validar Traslado","submit");
			break;

		} // End switch




/********************************** FIN INSERTAR INVENTARIO ******************************/



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$idpuntoventa = "", $newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key;


?>
<br>

<table cellpadding=1 cellspacing=0 class=bordertable align=left width="100%" >
	<tr>
			<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
		</tr>
	<tr>
			<td>
				<table width="100%" border=0 cellspacing=1 cellpadding=1 class=texto>


					<form name="frmInv" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" >
						<tr class=row2>
							<td colspan="2"><?php echo Mensaje_Info("Tenga en cuenta que no se hayan realizado las operaciones de inventario del traslado");?></td>
						</tr>
						<tr class=row2>
							<td>ID Traslado</td>
							<td>
								<input type="text" name="IDTraslado" value="" />
							</td>
						</tr>
						<tr class=row2>
							<td>Punto de Venta</td>
							<td>
								<select name="IDPuntoVenta" onChange="document.frm.submit();" >
										<option value="">Seleccione Un Punto de Venta</option>
										<?php
										$qry_punto = db_query("SELECT * FROM PuntoVenta WHERE Publicar = 'S' ORDER BY IDCiudad, Nombre ");
										while($punto = db_fetch_object($qry_punto)){
											 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
										}
										?>
								</select>
							</td>
						</tr>
						<tr class=row2>
							<td align="center">
								<input type=hidden name=action value="eliminartraslados"></td>
							<td><input type=submit name=submit value="Validar Traslado" class=submit></td>
						</tr>
					</form>
				</table>
			</td>
	</tr>
</table>
<br><br><br><br><br><br>




<?php
	if( !empty($id) && !empty($idpuntoventa) )
		datos_traslado($id, $idpuntoventa);
}// End function print_form()

function datos_traslado($id, $idpuntoventa){

	$qid = db_query(" SELECT * FROM Traslado WHERE IDTraslado = '$id' AND IDPuntoVentaOrigen = '$idpuntoventa' ");

	$r = db_fetch_object($qid);

	if( !empty( $r ) )
	{
?>


<table class="bordertable" width="100%" cellspacing="1" border="0" align="center">
<FORM name="frm" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" >
	<tr>

		<td class="maintitle"><b></b>
				Traslado a Borrar
		</td>
	</tr>
	<tr>
	<td width="100%">
		<table width="100%" border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff" >

				<tr >
					<td colspan="2" width="100%">

								<div align="center">
									<table width="100%" border=0 align="center">
										<tr>
											<td colspan="4">
												<table class=rowtable width="100%">
													<tr>
														<td class=row1>Origen </td>
														<td class=row2 >
															<?php
																echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaOrigen);
															?>
														</td>
														<td class=row1>Destino </td>
														<td class=row2 >
															<?php
																echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaDestino);
															?>
														</td>
													</tr>
													<tr>
														<td class=row1>Estado</td>
														<td class=row2 ><?php echo get_field("EstadoTraslado","Descripcion","IDEstadoTraslado",$r->IDEstadoTraslado); ?></td>
														<td class=row1>Fecha </td>
														<td class=row2 ><input type="text" class="tbox" name="Fecha" size="19" value='<?=$r->Fecha?>' readonly>

															</td>
													</tr>
													<tr>
														<td class=row1 >Observaciones</td>
														<td class=row2 colspan="3"><textarea class="tareabox" name="Observaciones" rows="4" cols="64"><?=$r->Observaciones?></textarea></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td colspan="4" class=titlemedium>Detalle Traslado</td>

										</tr>
										<tr bgcolor=#e7ebef>
											<td colspan="4">
												<table class="texto" border="0" cellspacing="1" cellpadding="0" width="100%" id=table1>
													<tr bgcolor="#dfe3e7">
														<td class="rowform" align="center"><b>Item</b><b></b></td>
														<td class="rowform" align="center"><b>Referencia</b></td>
														<td class="rowform" align="center"><b>Talla</b></td>
														<td class="rowform" align="center"><b>Nombre</b></td>
														<td class="rowform" align="center"><b>Cantidad</b></td>
													</tr>
													<?php
														$sql_detalle = " SELECT * FROM DetalleTraslado WHERE IDTraslado = '$r->IDTraslado' AND IDPuntoVentaOrigen = '$r->IDPuntoVentaOrigen' ";
														$query_detalle = db_query($sql_detalle);
														$i = 0;
														while( $r_detalle = db_fetch_object( $query_detalle ) )
														{
															$class = repetition()?"row1":"row2";
															$i++;
															$PuntoVentaReferencia = get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica);
															$Talla = get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica);;
													?>
													<tr >
														<td class="<?=$class?>">
															<b><?=$i?></b>
															<input type="hidden" name="IDCodificacionEspecifica[]" value="<?=$r_detalle->IDCodificacionEspecifica?>">
															<input type="hidden" name="Talla[]" value="<?=$Talla?>">
															<input type="hidden" name="IDReferencia[]" value="<?=get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$PuntoVentaReferencia)?>">

														</td>
														<td class="<?=$class?>">
															<?php echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$PuntoVentaReferencia)); ?></td>
														<td class="<?=$class?>">
															<?php echo get_field("Talla","Descripcion","IDTalla",$Talla) ?>
														</td>
														<td class="<?=$class?>">
															<?php echo get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$PuntoVentaReferencia)); ?>
														</td>
														<td class="<?=$class?>">
															<?php echo $r_detalle->Cantidad ?>
														</td>
													</tr>
													<?php
														}//end while
													?>
												</table>
											</td>
										</tr>
										<tr class=row2>
											<td align="center">
												<input type=hidden name=action value="eliminaahorasi">
												<input type=hidden name="idtraslado" value="<?=$r->IDTraslado ?>">
												<input type=hidden name="idpuntoventa" value="<?=$r->IDPuntoVentaOrigen ?>">
												<input type=submit name=submit value="Eliminar Traslado" class=submit>
											</td>
										</tr>
									</table>


					</td>
				</tr>
			</table>
		</td>
	</tr>
	</FORM>

</table>

<?php
	}//end if
	else
	{
		echo "No se ha encontrado el traslado";
	}
} // END function print_form_fotos($id,$numfotos)



?>
