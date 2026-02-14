<body> <?php 

$TitleMod ="Cliente Fidelizado";

$Table = "Cliente";
$TableJoin = "Factura";
$Key = "IDCliente";
$MOD = "ClienteFidelizado";
$m = "Cliente";

		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;

			case "insert" :
				$frm= vars_LOG($HTTP_POST_VARS);

				$id_cliente=$frm["IDCliente"];

				// verico el numero de la tarjeta
				$sql_tarjeta = " Select * From TarjetaFidelizacion Where Codigo = '" . $frm["NumeroTarjeta"] . "' AND (Estado = 'A' or Estado = 'E') LIMIT 1 ";
				$qry_tarjeta = db_query( $sql_tarjeta );
				//$r_tarjeta = db_fetch_array( $qry_tarjeta );
				if (db_num_rows($qry_tarjeta)<=0){
					window_alert("El numero de tarjeta no existe por favor verifique ");
				}
				else{
					if (!empty($id_cliente)){
					//verifico que este id tenga alguna tarjeta para validar
					$id_tarjeta=get_field("TarjetaFidelizacion","IDTarjetaFidelizacion","IDCliente",$id_cliente);
					if(empty($id_tarjeta))
						$condicion=" and Estado = 'A'";
					else
						$condicion=" and IDCliente = '".$id_cliente."'";
					}
					else{
						$condicion=" and Estado = 'A'";
					}

					$sql_tarjeta_cliente = " Select * From TarjetaFidelizacion Where Codigo = '" . $frm["NumeroTarjeta"] . "'  $condicion";
					$qry_tarjeta_cliente = db_query( $sql_tarjeta_cliente );





					//$r_tarjeta = db_fetch_array( $qry_tarjeta );
					if (db_num_rows($qry_tarjeta_cliente)<=0){
							window_alert("El numero de tarjeta ya fue asignado a otro cliente, por favor verifique ");
					}
					else{
						$id = insert($frm);
						$sql_tarjeta="Update TarjetaFidelizacion Set IDCliente = '".$id. "', Estado = 'E',FechaTrEd = NOW(), UsuarioTrEd = 'Administrador' Where Codigo = '".$frm["NumeroTarjeta"]."' ";
						db_query($sql_tarjeta);
						$id_tarjeta_fidelizacion=get_field("TarjetaFidelizacion","IDTarjetaFidelizacion","IDCliente",$id);
						$sql_actualizo_tarjeta="Update Cliente Set IDTarjetaFidelizacion = '".$id_tarjeta_fidelizacion. "' Where IDCliente = '".$id."' ";
						db_query($sql_actualizo_tarjeta);
						print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
					}
				}

			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				$frm= vars_LOG($HTTP_POST_VARS);

				if ($frm["ClubSuavidad"]=="N"){
					$sql_baja_club="Update Cliente set ClubSuavidad = 'N'  Where IDCliente = '".$frm["IDCliente"]."'";
					db_query($sql_baja_club);
					window_alert("El cliente ya no pertenece al club de la suavidad, recuerde desvincular la tarjeta ");
					print_form($frm["IDCliente"],"update","Actualizar $TitleMod","Realizar Cambios");


				}
				else{

						$id_cliente=$frm["IDCliente"];

						// verico el numero de la tarjeta
						//$sql_tarjeta = " Select * From TarjetaFidelizacion Where Codigo = '" . $frm[NumeroTarjeta] . "' AND (Estado = 'A' or Estado = 'E') LIMIT 1 ";
						//$qry_tarjeta = db_query( $sql_tarjeta );
						//$r_tarjeta = db_fetch_array( $qry_tarjeta );
						//if (db_num_rows($qry_tarjeta)<=0){
							//window_alert("El numero de tarjeta no existe por favor verifique ");
							//print_form($frm[IDCliente],"update","Actualizar $TitleMod","Realizar Cambios");
						//}
						//else{
							//$sql_tarjeta_cliente = " Select * From TarjetaFidelizacion Where (Estado = 'A' and Codigo = '" . $frm[NumeroTarjeta]. "') or ( Codigo = '" . $frm[NumeroTarjeta]. "' and Estado = 'E' and  IDCliente = '".$frm[IDCliente]."' )";
							//$qry_tarjeta_cliente = db_query( $sql_tarjeta_cliente );
							//$r_tarjeta = db_fetch_array( $qry_tarjeta );
							//if (db_num_rows($qry_tarjeta_cliente)<=0){
								//window_alert("El numero de tarjeta ya fue asignado a otro cliente, por favor verifique ");
								//print_form($frm[IDCliente],"update","Actualizar $TitleMod","Realizar Cambios");
							//}
							//else{
								//Libero la tarjeta para volverla a asignar
								//$sql_update_tarjeta="Update TarjetaFidelizacion Set IDCliente = '', Estado = 'A', IDPuntoVenta = '', FechaTrEd = NOW(), UsuarioTrEd = 'Administrador' Where IDCliente = '".$frm[IDCliente]."' ";
								//db_query($sql_update_tarjeta);
								//$sql_update_tarjeta="Update Cliente Set NumeroTarjeta = '', IDTarjetaFidelizacion = '' Where IDCliente = '".$frm[IDCliente]."' ";
								//db_query($sql_update_tarjeta);

								//$sql_tarjeta="Update TarjetaFidelizacion Set IDCliente = '".$frm[IDCliente]. "', Estado = 'E',FechaTrEd = NOW(), UsuarioTrEd = 'Administrador' Where Codigo = '".$frm[NumeroTarjeta]."' ";
								//db_query($sql_tarjeta);

								//$id_tarjeta_fidelizacion=get_field("TarjetaFidelizacion","IDTarjetaFidelizacion","IDCliente",$frm[IDCliente]);
								//$frm[IDTarjetaFidelizacion] = $id_tarjeta_fidelizacion;

								update($frm);
							//}
						//}
				}



			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$HTTP_GET_VARS["action"]="";
				delete($ID);
			break;
			case "list" :
			$sql = make_qry_string($HTTP_GET_VARS);
			list_r($sql);
			break;
			default :
					list_r();
			break;

		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('Cedula','Nombre','Apellido','Telefono','IDCiudad','Publicar','Dia','Mes','AutorizaMail');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='left' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
			<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
			<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>
<?php 
if($newmode <> "insert")
{
	$TABsel = 1;
	$idCliente = $r->IDCliente;
	include("Fidelizacion/menutabCliente.php");


}//end if newmode
?>

<table cellpadding=1 cellspacing=0 class=bordertable align=left >
<form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?php }?>>

	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=507 border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
			<td width="40%"> Numero Documento<br>
								<input type=text size=25 class=input   name=Cedula id=Cedula value="<?php echo $r->Cedula ?>"></td><td width="10"> </td>
							<td>Nombre<br>
								<input type=text size=25 class=input   name=Nombre id=Nombre value="<?php echo $r->Nombre ?>"> </td>
						</tr>
						<tr class=row2>
			<td width="40%"></td><td width="10"> </td>
							<td></td>
						</tr>
			<tr class=row2>
			<td width="40%"> Apellidos <br>
								<input type=text size=25 class=input name=Apellido id=Apellidos value="<?php echo $r->Apellido ?>"></td><td width="10"> </td>
							<td>Genero <br>
						    <span class="col2"><?php echo formradiogroup(array('Femenino'=>'F','Masculino'=>'M'),$r->Genero, 'Genero'); ?></span></td>
						</tr>
			<tr class=row2>
			  
			<td width="40%">Celular <br>
								<input type=text size=25 class=input   name=Celular id=Celular value="<?php echo $r->Celular ?>"></td>
								
			

			  <td>&nbsp;</td>
			  <td>
			  	Pasaporte<br>
				<input type=text size=25 class=input   name=Pasaporte id=Pasaporte value="<?php echo $r->Pasaporte ?>"></td><br>
              </td>
		  </tr>

		  <tr class=row2>
			  <td>e-mail <br>
              <input type=text size=25 class=input name=EMail id=IDEmpleado2 value="<?php echo $r->EMail ?>"></td>
			  <td></td>
			  <td><b>e-mail original</b><br>
              <?php echo $r->EMailOriginal ?>
			  <input type=hidden  name=EMailOriginal id=EMailOriginal value="<?php echo $r->EMailOriginal ?>"></td><td width="10"> </td>
			</td>
		  </tr>


			<tr class=row2>

			<td>Telefono <br>
              <input type=text size=25 class=input   name=Telefono id=Telefono value="<?php echo $r->Telefono ?>"></td>
			  <td></td>
				<td>Direccion <br>
					<input type=text size=25 class=input   name=Direccion id=Direccion value="<?php echo $r->Direccion ?>"></td>
			</tr>
			<tr class=row2>
			  <td>Barrio
              <br>
				<input type=text size=25 class=input   name=Barrio id=Barrio value="<?php echo $r->Barrio ?>">
              </td>
			  <td></td>
			  <td>Departamento<br>
              <?php echo formpopup("Departamento","Nombre","Nombre","IDDepartamento",$r->IDDepartamento,"input\" id=\"IDDepartamento"); ?></td>
		  </tr>
			<tr class=row2>
			<td width="40%">Ciudad<br>
								<?php echo formpopup("Ciudad","Descripcion","Descripcion","IDCiudad",$r->IDCiudad,"input\" id=\"IDCiudad"); ?></td><td width="10"> </td>
							<td>Empleado<br>
								<input type=text size=25 class=input   name=IDEmpleado id=IDEmpleado value="<?php echo $r->IDEmpleado ?>"></td>
						</tr>
			<tr class=row2>
			<td width="40%">Fecha de Nacimiento(aaaa/mm/dd)<br>
								<input type=text size=4 class=input   name=Ano id=Ano value="<?php echo $r->Ano ?>">/
                                <input type=text size=4 class=input   name=Mes id=Mes value="<?php echo $r->Mes ?>">/
                                <input type=text size=4 class=input   name=Dia id=Dia value="<?php echo $r->Dia ?>">

                                </td><td width="10"> </td>
							<td>Autorizo a recibir mensajes de texto (SMS) <span class="col2"><br>
						    <?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AceptaSMS, 'AceptaSMS'); ?></span></td>
						</tr>
			<tr class=row2>
				<td width="40%">Autorizo a recibir e-mail con promociones o informaci&oacute;n<br>
					<?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AutorizaMail, 'AutorizaMail'); ?></td>
				<td width="10"></td>
				<td> Publicar <br>
					<?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Publicar, 'Publicar'); ?></td>
			  </tr>
						<tr class=row2>
							<td width="40%">Acepta t&eacute;rminos y condiciones<br>							  <span class="col2"><?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AceptaTerminos, 'AceptaTerminos'); ?></span></td>
							<td width="10"></td>
							<td>Acepta ley Habeas Data <span class="col2"><br>
						    <?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AceptaHabeas, 'AceptaHabeas'); ?></span></td>
						</tr>
						<tr class=row2>
						  <td>Numero de Tarjeta que se entrega<br>
						    <span class="col2">
                              <?php
							  $id_tarjeta=$r->IDTarjetaFidelizacion;
							  $numero_tarjeta=get_field("TarjetaFidelizacion","Codigo","IDTarjetaFidelizacion",$id_tarjeta);
							  if (!empty($id_tarjeta)){
								//$solo_lectura="readonly='readonly'";
							  }
							  ?>

						    <input type="text" size="25" class="tbox mandatory" title="Numero de Tarjeta"   name="NumeroTarjeta" id="NumeroTarjeta" value="<?php echo $numero_tarjeta ?>" <?php echo $solo_lectura ?> />
					      </span></td>
						  <td></td>
						  <td>Fidelizado Club de la suavidad<span class="col2"><br>
					      <?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->ClubSuavidad, 'ClubSuavidad'); ?></span></td>
			  </tr>
						<tr class=row2>
						  <td>Punto de venta que fidelizo<br>
                          <select name="IDPuntoVentaFideliza" id="IDPuntoVentaFideliza">
                          	<option value="">[Punto Venta]</option>
                            <?php
								$sql_puntos_venta="Select * From PuntoVenta Where 1 Order by Nombre";
								$qry_puntos_venta=db_query($sql_puntos_venta);
								while($row_puntos_venta=db_fetch_array($qry_puntos_venta)){
							?>
								<option value="<?php echo $row_puntos_venta["IDPuntoVenta"]?>" <?php if($row_puntos_venta["IDPuntoVenta"]==$r->IDPuntoVentaFideliza) echo "selected"; ?> ><?php echo $row_puntos_venta["Nombre"]?></option>

                             <?php
								}
							?>
                          </select>
						  <td></td>
						  <td>Empleado fidelizo<br>

                        <select name="IDUsuarioFideliza" id="IDUsuarioFideliza">
                          	<option value="">[Empleado Fideliza]</option>
                            <?php
								$sql_empleados_venta="Select * From Empleado Where 1 Order by Nombre";
								$qry_empleados_venta=db_query($sql_empleados_venta);
								while($row_empleado=db_fetch_array($qry_empleados_venta)){
							?>
								<option value="<?php echo $row_empleado["IDEmpleado"]?>" <?php if($row_empleado["IDEmpleado"]==$r->IDUsuarioFideliza) echo "selected"; ?> ><?php echo $row_empleado["Nombre"] . " " .$row_empleado["Apellidos"] ?></option>

                             <?php
								}
							?>
                          </select>


                          </td>
		  </tr>
              <?php
              // consulto historial
			  $sql_historial_tarjetas="Select * From HistorialTarjetaFidelizacion Where IDCliente = '".$r->IDCliente."' ";
			  $qry_historial=db_query($sql_historial_tarjetas);
			  if (db_num_rows($qry_historial)>0){
			  ?>
						<tr class=row2>
						  <td colspan="3">

                          <table border="0" align="center" cellpadding="1" cellspacing="2">
                          	<tr>
                          	  <td colspan="4"><strong>HISTORIAL DE TARJETAS ASIGNADAS</strong></td>
                       	    </tr>
                          	<tr>
                            	<td><strong>
                               	  Numero Tarjeta
                              </strong></td>
                            	<td><strong>
                               	  Motivo por la cual se cambio
                              </strong></td>
                            	<td><strong>
                               	  Fecha Cambio
                              </strong></td>
                            	<td><strong>
                               	  Punto de venta
                              </strong></td>
                           	</tr>

                            <?php while($row_historial=db_fetch_array($qry_historial)){ ?>
                          	<tr>
                          	  <td><?php echo $row_historial["Codigo"]; ?></td>
                          	  <td><?php echo $row_historial["Observacion"]; ?></td>
                          	  <td><?php echo $row_historial["FechaTrCr"]; ?></td>
                          	  <td><?php echo get_field( "PuntoVenta","Nombre","IDPuntoVenta",$row_historial["IDPuntoVenta"] );; ?></td>
                       	    </tr>
                           <?php } ?>
                          </table>


                          </td>
		  </tr>
          <?php } ?>


						<tr class=row2>
						  <td colspan="3">Comentarios<br>
                          <textarea name="Comentarios" id="Comentarios" cols="60" rows="5"><?php echo $r->Comentarios ?></textarea>
                          </td>
		  </tr>
						<tr>
							<td colspan=3 align=center class=row2>
                <input type=hidden name=IDCliente id=IDCliente value="<?php echo $r->IDCliente ?>">
                <input type=hidden name=UsuarioTrCr value="<?php echo $r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?php echo $r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?php echo $r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?php echo $r->FechaTrEd ?>">
				<input type=hidden name=ID value="<?php echo $r->$Key ?>">

                <input type=hidden name=NumeroFidelizacion value="<?php echo $r->NumeroFidelizacion ?>">
                <input type=hidden name=EstadoCivil value="<?php echo $r->EstadoCivil ?>">
                <input type=hidden name=NumeroHijos value="<?php echo $r->NumeroHijos ?>">
                <input type=hidden name=Gustos value="<?php echo $r->Gustos ?>">
                <input type=hidden name=Deportes value="<?php echo $r->Deportes ?>">
                <input type=hidden name=Restaurantes value="<?php echo $r->Restaurantes ?>">
                <input type=hidden name=Musica value="<?php echo $r->Musica ?>">
                <input type=hidden name=Hobies value="<?php echo $r->Hobies ?>">
                <input type=hidden name=FechaRegistroClubSuavidad value="<?php echo $r->FechaRegistroClubSuavidad ?>">



				<input type=hidden name=action value=<?php echo $newmode?>>
				<input type=submit name=submit value="<?php echo $submit_caption ?>" class=submit>
			</td>
						</tr>
						<tr>
							<td colspan="3" align=center class=row2><br>
							</td>
						</tr>
						<?php 
						if( $newmode <> "insert" )
						{

							$sql_facturas = " SELECT * FROM Factura WHERE IDCliente = '$r->IDCliente' ORDER BY FechaFactura DESC ";
							$qry_facturas = db_query( $sql_facturas );

						?>
						<tr>
							<td colspan="3" align=center class=row2>
								<table width="100%" border="0" cellspacing="2" cellpadding="0" class="bordertable">
									<tr>
										<td colspan="6" align="left" class="maintitle">&Uacute;ltimas compras del cliente</td>
									</tr>
									<tr>
										<td align="center" class="titlemedium">Nro Factura</td>
										<td align="center" class="titlemedium">Fecha</td>
										<td align="center" class="titlemedium">PuntoVenta</td>
										<td align="center" class="titlemedium">Items</td>
										<td align="center" class="titlemedium">Valor Factura</td>
										<td align="center" class="titlemedium">Ver Detalle</td>
									</tr>
									<?php 
									while( $r_factura = db_fetch_object( $qry_facturas ) )
									{
										$class = repetition()?"row1":"row2";
									?>
									<tr>
										<td align="center" class="<?php echo $class?>"><?php echo $r_factura->NumeroFactura;?></td>
										<td align="center" class="<?php echo $class?>"><?php echo formatofecha( substr( $r_factura->FechaFactura, 0, 10) );?></td>
										<td align="center" class="<?php echo $class?>"><?php echo get_field( "PuntoVenta","Nombre","IDPuntoVenta",$r_factura->IDPuntoVenta );?></td>
										<td align="center" class="<?php echo $class?>">
											<?php
												echo $ContadorItem=get_field("DetalleFactura","COUNT( IDDetalleFactura )","IDFactura",$r_factura->IDFactura."' AND IDPuntoVenta = '$r_factura->IDPuntoVenta");
												$ItemTotal+=$ContadorItem;
											?>
										</td>
										<td align="right" class="<?php echo $class?>"><?php
										$TotalCompras+=$r_factura->ValorTotal;
										echo number_format($r_factura->ValorTotal, 2 );
										?></td>
										<td align="center" class="<?php echo $class?>"><a href="?mod=Factura&action=edit&id=<?php echo $r_factura->IDFactura?>&idpunto=<?php echo $r_factura->IDPuntoVenta?>" target="_blank"><img src="images/attach.png" border="0"></a></td>
									</tr>
									<?php 
									}//end while
									?>
									<tr>
										<td align="center" class="<?php echo $class?>">&nbsp;</td>
										<td align="center" class="<?php echo $class?>">&nbsp;</td>
										<td align="center" class="<?php echo $class?>"><strong>Totales:</strong></td>
										<td align="center" class="<?php echo $class?>">
											<strong>
											<?php
												echo $ItemTotal;
											?>
										</strong>
										</td>
										<td align="right" class="<?php echo $class?>">
											<strong>
											<?php
											echo number_format($TotalCompras, 2 );
										?>
									</strong>
									</td>
										<td align="center" class="<?php echo $class?>">&nbsp;</td>
									</tr>
								</table>
								<br>

								<?php 
								/***********************PRODUCTOS MAS REQUERIDOS**********************************/
								$sql_productos = " SELECT F.NumeroFactura, R.IDReferencia, R.Numero, DF.Cantidad, L.Nombre
													FROM Factura F, DetalleFactura DF,CodificacionEspecifica C, PuntoVentaReferencia PVR, Referencia R, Linea L
													WHERE F.IDCliente = '$r->IDCliente'
													AND F.IDFactura = DF.IDFactura
													AND F.IDPuntoVenta = DF.IDPuntoVenta
													AND DF.IDCodificacionEspecifica = C.IDCodificacionEspecifica
													AND C.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
													AND PVR.IDReferencia = R.IDReferencia
													AND R.IDLinea = L.IDLinea
													ORDER BY DF.Cantidad DESC ";
								$qry_productos = db_query( $sql_productos );
								?>
								<table width="100%" border="0" cellspacing="2" cellpadding="0" class="bordertable">
									<tr>
										<td colspan="6" align="left" class="maintitle">Productos mas requeridos</td>
									</tr>
									<tr>
										<td align="center" class="titlemedium">NumeroFactura</td>
										<td align="center" class="titlemedium">Referencia</td>
										<td align="center" class="titlemedium">Cantidad</td>
										<td align="center" class="titlemedium">Linea</td>
										<td align="center" class="titlemedium">Ver Referencia</td>
									</tr>
									<?php 
									while( $r_producto = db_fetch_object( $qry_productos ) )
									{
										$class = repetition()?"row1":"row2";
									?>
									<tr>
										<td align="center" class="<?php echo $class?>"><?php echo $r_producto->NumeroFactura;?></td>
										<td align="center" class="<?php echo $class?>"><?php echo $r_producto->Numero;?></td>
										<td align="center" class="<?php echo $class?>"><?php echo $r_producto->Cantidad;?></td>
										<td align="center" class="<?php echo $class?>"><?php echo $r_producto->Nombre;?></td>
										<td align="center" class="<?php echo $class?>"><a href="?mod=Referencia&action=edit&id=<?php echo $r_producto->IDReferencia?>" target="_blank"><img src="images/attach.png" border="0"></a></td>
									</tr>
									<?php 
									}//end while
									?>
								</table>
								<?php 
								/***********************PRODUCTOS MAS REQUERIDOS**********************************/
								?>
							</td>
						</tr>
						<?php 
						}//end if newmode
						?>
					</table>
		</td>
	</tr>
</form>
</table>

<?php 
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table Where ClubSuavidad = 'S' ORDER BY $Key";
	else{
		//$sql = str_replace("Where 1","Where 1 and ClubSuavidad = 'S'",$sql);
		$sql = str_replace("Where 1","Where 1 and ClubSuavidad = 'S'",$sql);
	}


		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=60;
   		$nav->execute($sql,$dblink);
		$total_records =  $nav->total_result;
		$rows = $nav->rows;
		$result = $nav->sql_result;
		$row = $offset;
		$startrow = $offset + 1;
		$finalrow = ($row * $nav->limit) + $rows;

	 	$pages = $nav->show_num_pages('&laquo;','&laquo; prev','&raquo;','next &raquo;','|','class=navvar');   // show pages

		$info = $nav->show_info();

 		if($_GET['in_order']=="ASC" || $_GET['in_order']==""){
								$img="down.png";
								$order="DESC";
							}else if($_GET['in_order']=="DESC"){
								$img="up.png";
								$order="ASC";
							}

							?>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
	</tr>
</table>
<?php 
		if($rows > 0){
?>
<br>
<table width=500 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
		</tr>
<?php filtrar();?>
		<tr>
			<td class=titlemedium  bgcolor=#9daac6><?php  echo $info;?></td>
		</tr>

		<tr>
			<td align="left" bgcolor="#E9E9E9" >
            <a href="Fidelizacion/exportafidelizados.php">
            <img src="../images/excel_icon.gif" width="20" height="20" border="0" >Exportar Registros
            </a>
            </td>
		</tr>

<tr>
<td class=texto bgcolor=#DBEAF5 colspan=10 nowrap>
<?php 
	print $pages;
?>
</td>
</tr>
	<tr>
			<td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
<tr>
						<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Cedula&in_order=".$order."&listar=".$nav->limit; ?>&action=list">Cedula&nbsp;<?php  if($_GET['order_by']=="Cedula"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit; ?>&action=list">Nombre&nbsp;<?php  if($_GET['order_by']=="Nombre"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td><td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Apellido&in_order=".$order."&listar=".$nav->limit; ?>&action=list">Apellidos&nbsp;<?php  if($_GET['order_by']=="Apellidos"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td><td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Telefono&in_order=".$order."&listar=".$nav->limit; ?>&action=list">Telefono&nbsp;<?php  if($_GET['order_by']=="Telefono"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Celular&in_order=".$order."&listar=".$nav->limit; ?>&action=list">Celular&nbsp;<?php  if($_GET['order_by']=="Celular"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDCiudad&in_order=".$order."&listar=".$nav->limit; ?>&action=list">IDCiudad&nbsp;<?php  if($_GET['order_by']=="IDCiudad"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit; ?>&action=list">Publicar&nbsp;<?php  if($_GET['order_by']=="Publicar"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

<?php while($r = db_fetch_object($result)){
?>

<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap class=row1><?php echo $r->Cedula ?></td>
						<td nowrap class=row1><?php echo $r->Nombre ?></td> <td nowrap class=row1><?php echo $r->Apellido?></td> <td nowrap class=row1><?php echo $r->Telefono ?></td>
						<td nowrap class=row1><?php echo $r->Celular ?></td>
						<td nowrap class=row1><?php echo get_field("Ciudad","Descripcion","IDCiudad",$r->IDCiudad) ?></td>
						<td nowrap class=row1><?php echo $r->Publicar ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>
</td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=9 nowrap>
	<?php 
		print $pages;
		?>
</td>
</tr>
</table></td>
		</tr>
</table>

<?php 
}// End if$rows
else
	echo "<br><br><span class=subtitle><b>No existen registros en  $TitleMod </b></span>";
}// Enf function list()

/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD;
?>
	<form name="frm" action="./" method="get" onSubmit="return valbuscar(document.frm)">
	<tr>
				<td class="rowform" align="center" colspan=8>
					<select name="field" id="Buscar por" class="popup">
						<option value="Cedula">Cedula</option>
						<option value="Nombre">Nombre</option>
						<option value="Apellido">Apellido</option>
						<option value="Telefono">Telefono</option>
						<option value="AutoriazaMail">AutorizaMail</option>
						<!--<option value="Ciudad.Descripcion">Ciudad</option>-->
					</select> <input type="text" size="20" name="QryString" id="Buscar Por" class="post"> Entre <input type=text readonly size=10 class=input name=limit1>
					<script language='JavaScript1.2'>
								<!--
								if (!document.layers)
								document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit1,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
								//-->
					</script>
					 y <input type=text size=10 readonly class=input name=limit2>
					<script language='JavaScript1.2'>
								<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
								//-->
					</script>
					 <br>
					ordenar por <select name="order_by" class="popup">
						<option value="Cedula">Cedula</option>
						<option value="Nombre">Nombre</option>
						<option value="Apellido">Apellido</option>
						<option value="Telefono">Telefono</option>
					</select> de forma <select name="in_order" class="popup">
						<option value="ASC">Ascendente</option>
						<option value="DESC">Descendente</option>
					</select>
					Listar <select name="listar" class="popup">
									<option value="10">10</option>
									<option value="15">15</option>
									<option value="20">20</option>
									<option value="25">25</option>
									<option value="30">30</option>
								</select>
					<br>
					<input type="hidden" name="mod" value="<?php echo $MOD?>">
					<input type="hidden" name="rangofield" value="Fecha">
					<input type="hidden" name="action" value="list">
					<input type="hidden" name="tjoin" value="">
					<input type="submit" name="submit" value="Buscar" class="submit">
				</td>
			</tr>
	</form>
<?php 
	}//End function filtrar
?>
