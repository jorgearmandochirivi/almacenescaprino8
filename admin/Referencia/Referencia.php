<body> <?php 
require($libdir."filelib.php");

$TitleMod ="Referencia";

$Table = "Referencia";
$TableJoin = "CodificacionEspecifica";
$Key = "IDReferencia";
$MOD = "Referencia";
$m="Referencia";
		$permisos = get_permiso($ID_Usuario,$m,$Table);



function table_check_list_desc($Table,$Key,$key_value,$table_option,$key_option,$table_reference,$check_name,$newmode,$condicion=""){

	$str_qry = "SELECT $key_option FROM $table_reference WHERE $Key = $key_value ";
	
	if($newmode <> "insert")
		$qry_option = db_query($str_qry);
	
	$option_checked = array();
	
	while($option = db_fetch_object($qry_option))
		$option_checked[] = $option->$key_option;
	
		
	$qry = db_query("SELECT * FROM $table_option WHERE 1 ".$condicion);
	
	$array_option = array();
	
	while ($option = db_fetch_object($qry)){
		$array_option[$option->Nombre] = $option->$key_option;
	}
		

	
	echo formcheckgroup($array_option,$option_checked,$check_name);
	
	}
		

if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;

			case "insert" :

				$frm= vars_LOG($_POST);

				$sql_verifica = " SELECT * FROM Referencia WHERE Numero = '$frm[Numero]' ";
				$qry_verifica = db_query( $sql_verifica );
				if( db_num_rows( $qry_verifica ) > 0 )
				{
					echo "esta referencia ya existe en el sistema, verifique por favor";
					exit;
				}//end if

				$id = insert($frm);

				if(isset($PuntoVenta))
					foreach ($PuntoVenta as $IDPuntoVenta)
					{
						$idpuntoventareferencia = get_maxID("PuntoVentaReferencia","IDPuntoVentaReferencia");
						$qry_PuntoVentaReferencia = db_query("INSERT INTO PuntoVentaReferencia values('$idpuntoventareferencia', '$id','$IDPuntoVenta')");
					}


				insert_codEspecifica($id);

				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				$frm= vars_LOG($_POST);

				//Variable temporal para saber si el tipo de talla es actualizado

				$temp = 0;

				if(isset($_POST['IDTipoTalla']))
				{

					$sql_tipotalla = "SELECT IDTipoTalla FROM $Table WHERE $Key = '$ID' ";
					$query_tipotalla = db_query( $sql_tipotalla );

					$r_tipotalla = db_fetch_object( $query_tipotalla );

					if( $r_tipotalla->IDTipoTalla <> $_POST['IDTipoTalla'] )
					{
						$temp = 1;
					}//end if( $r_tipotalla->tipotalla <> $IDTalla )

				}//end if(isset($IDTalla))

				///La descripcion larga se arma automaticamente
				$Capellada=get_field("Capellada","Nombre","IDCapellada",$frm["IDCapellada"]);
				$Forro=get_field("Forro","Nombre","IDForro",$frm["IDForro"]);
				$Plantilla=get_field("Plantilla","Nombre","IDPlantilla",$frm["IDPlantilla"]);
				$Suela=get_field("Suela","Nombre","IDSuela",$frm["IDSuela"]);
				$Altura=get_field("Altura","Nombre","IDAltura",$frm["IDAltura"]);
				$IDPais=get_field("Proveedor","IDPais","IDProveedor",$frm["IDProveedor"]);
				$Pais=get_field("Pais","Descripcion","IDPais",$IDPais);
				$Sic=get_field("Proveedor","Nit","IDProveedor",$frm["IDProveedor"]);
				$AlturaMarroq=$frm["AlturaMarroq"];
				$AnchoMarroq=$frm["AnchoMarroq"];
				$ProfundidadMarroq=$frm["ProfundidadMarroq"];
				$MaterialesMarroq=$frm["MaterialesMarroq"];

				if(!empty($Capellada))
					$DatosDecrip.="Capellada:".$Capellada."\r";				
				if(!empty($Plantilla))
					$DatosDecrip.="Plantilla:".$Plantilla."\r";
				if(!empty($Suela))
					$DatosDecrip.="Suela:".$Suela."\r";
				if(!empty($Altura))
					$DatosDecrip.="Altura:".$Altura."\r\r(para mayor informacion consultar la guia de tallas)\r";


				if(!empty($AlturaMarroq) || !empty($AnchoMarroq) || !empty($ProfundidadMarroq))
					$DatosDecrip.="Dimensiones:"."\r";

				if(!empty($AlturaMarroq))
					$DatosDecrip.="Altura:".$AlturaMarroq."\r";
				if(!empty($AnchoMarroq))
					$DatosDecrip.="Ancho:".$AnchoMarroq."\r";
				if(!empty($ProfundidadMarroq))
					$DatosDecrip.="Profundidad:".$ProfundidadMarroq."\r";
				if(!empty($MaterialesMarroq))
					$DatosDecrip.="Materiales:".$MaterialesMarroq."\r";
				if(!empty($Forro))
					$DatosDecrip.="Forro:".$Forro."\r";	


				if(!empty($Pais))
					$DatosDecrip.="\rPais origen:".$Pais."\r";
				if(!empty($Sic))
					$DatosDecrip.="Codigo Sic:".$Sic."\r";

				

				$frm["DescripcionLarga"]=$DatosDecrip;


				//Subir imagenes
				$frm = copy_imgs($frm,$_FILES);


				update($frm);

				if( $temp == 1 )
				{
					insert_codEspecifica($ID);
				}//end if( $temp == 1 )

				//$qry = db_query("DELETE FROM PuntoVentaReferencia WHERE IDReferencia = '$ID' ");

				//actualizacion de los puntos de venta en donde esta la referencia

				if(isset($PuntoVenta))
				{
					actualizapunto($ID,$PuntoVenta,$_POST['IDTipoTalla']);
				}//end if(isset($PuntoVenta))


				//verificacion del tipo de talla en la base de datos contra el que viene
				//en el POST. Si es diferente se actualiza la codificacion especifica de la referencia

			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$_GET[action]="";
				delete($ID);
			break;

			case "delfoto" :
				$sql_actualiza=db_query("Update Referencia Set " . $_GET[campo] . "='' Where IDReferencia = '".$_GET[id]."'");
				print_form($_GET[id],"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "list" :
			$sql = make_qry_string($_GET);
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

	GLOBAL $TitleMod,$Table,$MOD,$Key,$imagedir;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('IDProveedor','IDTipoTalla','IDColor','IDLinea','Tipo','Cuero','Numero','Nombre','Descripcion','Publicar');

function CheckAll()
{
	for (var i=0;i< document.frm.elements.length;i++)
	{
		var e = document.frm.elements[i];
		if (e.name != 'allbox' && document.frm.elements[i].type === "checkbox")
		e.checked = !e.checked;
	}
}

function selmovimiento( IDMOVIMIENTO, FECHA )
{
	document.frm.IDMovimiento.value= IDMOVIMIENTO;
	document.frm.FechaMovimiento.value= FECHA;
}//end function

</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
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
		$idReferencia = $r->IDReferencia;
	 	include("Referencia/menutabReferencia.php");

	 	$qry_movimiento = db_query( $sql_movimiento = "SELECT * from Movimiento WHERE IDMovimiento = '$r->IDMovimiento' " );
	 	$r_movimiento = db_fetch_object( $qry_movimiento );

	}
?>
<table cellpadding=1 cellspacing=0 class=bordertable align=left >
	<form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?php }?>>
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
						<!--
						<tr class=row2>
							<td colspan="2">Si la referencia es producto de una operaci&oacute;n de 'segunda' indique el movimiento aqu&iacute;.</td>
						</tr>
						<tr class=row2>
							<td>Movimiento Segundas</td>
							<td><input type=text size=25 class=input   name=FechaMovimiento id=Numero value="<?php echo $r_movimiento->Fecha ?>"><input type=hidden name=IDMovimiento id=IDReferencia value="<?php echo $r->IDIDMovimiento ?>"><input type="button" name="Segunda" value="Segunda" onClick="window.open( 'Movimiento/popMovimiento.php','','width=600, height=500' );" class=submit></td>
						</tr>
						-->
						<tr class=row2>
							<td width="50%">Nombre Referencia<br>
								<input type=text size=25 class=input   name=Nombre id=Nombre value="<?php echo $r->Nombre ?>">                 
                            </td>
							<td>
							<table width="100%" border="0" cellspacing="2" cellpadding="0">
									<tr>
										<td class=row2>Sexo</td>
										<td class=row2>Saldo</td>
									</tr>
									<tr>
										<td class=row1><?php echo formradiogroup(array('M'=>'M','F'=>'F','Otro'=>'Otro'),$r->Sexo, 'Sexo'); ?></td>
										<td class=row1><?php echo formradiogroup(array('S'=>'S','N'=>'N'),$r->Saldo, 'Saldo'); ?></td>
									</tr>
									<tr>
										<td></td>
										<td></td>
									</tr>
								</table>
							
							</td>
						</tr>

						<tr class=row2>
							<td width="50%">Numero<br>
							<input type=text size=25 class=input   name=Numero id=Numero value="<?php echo $r->Numero ?>">                   
                                </td>
							<td>
							
							<table width="100%" border="0" cellspacing="2" cellpadding="0">
									<tr>
										<td class=row2><b>CALZADO</b></td>
										<td class=row2><b>MARROQUINERIA</b></td>
									</tr>
									<tr>
										<td class=row1 width="60%" ><br>Capellada <br>
								<?php echo formpopup("Capellada","Nombre","Nombre","IDCapellada",$r->IDCapellada,"input\" id=\"Capellada"," Publicar = 'S' "); ?><br></td>
										<td class=row1 width="40%">
										Altura<br>
										<input type=text size=5 class=input   name=AlturaMarroq id=AlturaMarroq value="<?php echo $r->AlturaMarroq ?>">	                   


										</td>
									</tr>
									
							</table>
							
							</td>
						</tr>

						<tr class=row2>
							<td width="50%">
							
							Codigo Referencia<br>
							<input type=text size=25 class=input   name=CodigoReferencia id=CodigoReferencia value="<?php echo $r->CodigoReferencia ?>">	                   
                                </td>
							<td>
								
							<table width="100%" border="0" cellspacing="2" cellpadding="0">
									<tr>
										<td class=row1 width="60%">
										Forro<br>
							<?php echo formpopup("Forro","Nombre","Nombre","IDForro",$r->IDForro,"input\", id=\"Forro"," Publicar = 'S' "); ?><br>
										</td>
										<td class=row1 width="40%">
										Ancho<br>
										<input type=text size=5 class=input   name=AnchoMarroq id=AnchoMarroq value="<?php echo $r->AnchoMarroq ?>">	                   


										</td>
									</tr>
									
							</table>

							
							
							
							</td>
						</tr>

						<tr class=row2>
							<td width="50%">Codigo Color<br>
								<input type=text size=25 class=input   name=CodigoColor id=CodigoColor value="<?php echo $r->CodigoColor ?>">	            
                            </td>
							<td>
							
							<table width="100%" border="0" cellspacing="2" cellpadding="0">
									<tr>
										<td class=row1 width="60%">
										Plantilla <br>
							<?php echo formpopup("Plantilla","Nombre","Nombre","IDPlantilla",$r->IDPlantilla,"input\" id=\"Plantilla"," Publicar = 'S' "); ?><br>
										</td>
										<td class=row1 width="40%">
										Profundidad<br>
										<input type=text size=5 class=input   name=ProfundidadMarroq id=ProfundidadMarroq value="<?php echo $r->ProfundidadMarroq ?>">	                   


										</td>
									</tr>
									
							</table>

							
							
							
							</td>
						</tr>

						<tr class=row2>
							<td width="50%">Tipo de Referencia<br>
							<?php echo formpopup("TipoReferencia","Descripcion","Descripcion","IDTipoReferencia",$r->IDTipoReferencia,"input\" id=\"Tipo de Referencia"," Publicar = 'S' "); ?>
                            </td>
							<td>
							
							<table width="100%" border="0" cellspacing="2" cellpadding="0">
									<tr>
										<td class=row1 width="60%">
										Suela<br>
										<?php echo formpopup("Suela","Nombre","Nombre","IDSuela",$r->IDSuela,"input\" id=\"IDSuela"," Publicar = 'S' "); ?><br>
										</td>
										<td class=row1 width="40%">
											Materiales<br>
											<textarea name="MaterialesMarroq" id="MaterialesMarroq" rows="3" cols="10"><?php echo $r->MaterialesMarroq ?></textarea>
										
										</td>
									</tr>
									
							</table>

							
							
							</td>
						</tr>





						<tr class=row2>
							<td width="50%">C&oacute;digo de tipolog&iacute;a<br>
							<select name=IDTipologia>
                                  <option value="">[ Seleccione ]</option>
                                  <?php 
								$sql_tipologia = "SELECT * FROM Tipologia ORDER BY Nombre";
								$query_tipologia = db_query($sql_tipologia);
								while($r_tipologia = db_fetch_object($query_tipologia))
								{
									if( db_num_rows( $query_tipologia ) > 0 )
									{
										echo "<option value=$r_tipologia->IDTipologia";
										if($r->IDTipologia == $r_tipologia->IDTipologia) echo " selected ";
										echo ">".$r_tipologia->Nombre."</option>";
									}//end if( db_num_rows( $query_tallas ) > 0 )
								}//end while($r_tipotalla = db_fetch_object($query_tipotalla))
							?>
                                </select>                                
                                </td>
							<td>


							<table width="100%" border="0" cellspacing="2" cellpadding="0">
									<tr>
										<td class=row1 width="60%">
										Altura<br>
								<?php echo formpopup("Altura","Nombre","Nombre","IDAltura",$r->IDAltura,"input\" id=\"Altura"," Publicar = 'S' "); ?>
										</td>
										<td class=row1 width="40%">
										Forro marroq<br>
							<?php echo formpopup("Forro","Nombre","Nombre","IDForro",$r->IDForro,"input\", id=\"Forro"," Publicar = 'S' "); ?><br>
										</td>
									</tr>
									
							</table>
							
							</td>	
						</tr>
						<tr class=row2>
			<td width="50%">Color<br>
			<?php echo formpopup("Color","DescripcionLarga","DescripcionLarga","IDColor",$r->IDColor,"input\" id=\"Color"," Publicar = 'S' "); ?>
								
							</td>
							<td>

							<table width="100%" border="0" cellspacing="2" cellpadding="0">
									<tr>
										<td class=row1 width="60%">
										Proveedor<br>
										<?php echo formpopup("Proveedor","Nombre","Nombre","IDProveedor",$r->IDProveedor,"input\" id=\"Proveedor"," Publicar = 'S' "); ?>
										</td>
										<td class=row1 width="40%"></td>
									</tr>
									
							</table>


							
								</td>
			</tr>
						<tr class=row2>
			<td width="50%">Cuero<br>
			<?php echo formpopup("Cuero","DescripcionLarga","DescripcionLarga","IDCuero",$r->IDCuero,"input\" id=\"Cuero"," Publicar = 'S' "); ?>
			</td>
			<td>
			
								</td>
			</tr>
						<tr class=row2>
			<td width="50%">Linea<br>
			<select name=IDLinea>
								<option value="">[ Seleccione ]</option>
								<?php 
									$sql_tipo = "SELECT * FROM Tipo WHERE Publicar='S' ORDER BY Descripcion";
									$query_tipo = db_query($sql_tipo);
									while($r_tipo = db_fetch_object($query_tipo))
									{
										echo "<option value=''>----".$r_tipo->Descripcion."</option>";
										$sql_linea = "SELECT * FROM Linea WHERE IDTipo = '$r_tipo->IDTipo' and Publicar='S'";
										$query_linea = db_query($sql_linea);
										while ( $r_linea = db_fetch_object($query_linea) )
										{
											echo "<option value=$r_linea->IDLinea";
											if($r->IDLinea == $r_linea->IDLinea) echo " selected ";
											echo ">".$r_linea->Nombre."</option>";
										}
									}
								?>
								</select>
			
								
							
							</td>
								
								<td>
								</td>
			</tr>
						<tr class=row2>
							<td width="50%">Talla<br>
							<select name=IDTipoTalla>
									<option value="">[ Seleccione ]</option><?php 
								$sql_tipotalla = "SELECT * FROM TipoTalla ORDER BY Descripcion";
								$query_tipotalla = db_query($sql_tipotalla);
								while($r_tipotalla = db_fetch_object($query_tipotalla))
								{
									$query_tallas = db_query("SELECT * FROM Talla WHERE IDTipoTalla = '$r_tipotalla->IDTipoTalla'");

									if( db_num_rows( $query_tallas ) > 0 )
									{
										echo "<option value=$r_tipotalla->IDTipoTalla";
										if($r->IDTipoTalla == $r_tipotalla->IDTipoTalla) echo " selected ";
										echo ">".$r_tipotalla->Descripcion."</option>";
									}//end if( db_num_rows( $query_tallas ) > 0 )
								}//end while($r_tipotalla = db_fetch_object($query_tipotalla))
							?>


								</select>
							</td>
							<td>
						</td>
		  			</tr>


					  <tr class=row2>
							<td width="50%">Lista de precios<br>
							<div style="max-width:260px">
								<input type="text"
									   id="precio_search"
									   placeholder="Buscar..."
									   onkeyup="filtraPreciosPrecio();"
									   style="width:100%;margin-bottom:4px" class="input">

								<select name="IDPrecio" id="IDPrecio" size="6" class="input" style="width:100%">
									<option value="">Seleccione...</option>
									<?php 
									$__precios = array();
									$sql_precio = " SELECT * FROM Precio WHERE Publicar = 'S' ORDER BY ValorVenta ";
									$qry_precio = db_query( $sql_precio );
									while( $r_precio = db_fetch_object( $qry_precio ) )
									{
										$texto = $r_precio->IDPrecio." - ".$r_precio->ValorVenta." - ".$r_precio->Descuento."%";
										$__precios[] = array(
											'id'=>$r_precio->IDPrecio,
											'txt'=>$texto
										);
										echo "<option value='".$r_precio->IDPrecio."'";
										if( $r_precio->IDPrecio == $r->IDPrecio ) echo " selected ";
										echo ">".$texto."</option>";
									}
									?>
								</select>
							</div>
							<script type="text/javascript">
							function filtraPreciosPrecio(){
								var inp = document.getElementById('precio_search');
								var sel = document.getElementById('IDPrecio');
								var filtro = inp.value.toLowerCase();
								for(var i=0;i<sel.options.length;i++){
									var opt = sel.options[i];
									if(opt.value===""){ // dejar siempre la opción vacía
										opt.style.display = '';
										continue;
									}
									var txt = opt.text.toLowerCase();
									if(txt.indexOf(filtro) > -1){
										opt.style.display = '';
									}else{
										opt.style.display = 'none';
									}
								}
								// Opcional: si el seleccionado está oculto, limpiar selección
								if(sel.selectedIndex > -1 && sel.options[sel.selectedIndex].style.display==='none'){
									sel.selectedIndex = 0;
								}
							}
							</script>
							</td>
							<td>-<br>
							
						</td>
		  			</tr>

					  <tr class=row2>
							<td width="50%">Publicar<br>
							<?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Publicar, 'Publicar'); ?>
							</td>
							<td>Reportes<br>
							<?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Reportes, 'Reportes'); ?>
						</td>
		  			</tr>



		  


						
		  				<tr class=row2>
						  <td>Imagen1</td>
						  <td>Imagen 2</td>
		  				</tr>
						<tr class=row2>
						  <td>
                          <?php if (!empty($r->Foto1)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto1; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto1&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto1" id="Foto1" class=input>
                          <?php endif; ?>
                          </td>
						  <td><?php if (!empty($r->Foto2)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto2; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto2&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto2" id="Foto2" class=input>
                          <?php endif; ?></td>
		  				</tr>
						<tr class=row2>
						  <td>Imagen 3</td>
						  <td>Imagen 4</td>
		  				</tr>
						<tr class=row2>
						  <td><?php if (!empty($r->Foto3)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto3; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto3&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto3" id="Foto3" class=input>
                          <?php endif; ?></td>
						  <td><?php if (!empty($r->Foto4)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto4; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto4&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto4" id="Foto4" class=input>
                          <?php endif; ?></td>
		  				</tr>

						<tr>
							<td colspan="2" class=maintitle bgcolor=#9daac6>&nbsp;Informacion para pagina web</td>							
						</tr>

						<tr class=row2>
						  <td>Descripcion Corta</td>
						  <td>Descripcion Larga</td>
		  				</tr>
						<tr class=row2>
						  <td>
                          	<textarea name="DescripcionCorta" id="DescripcionCorta" rows="3" cols="20"><?php echo $r->DescripcionCorta ?></textarea>
                          </td>
						  <td>
						  	<textarea name="DescripcionLarga" id="DescripcionLarga" rows="5" cols="40"><?php echo $r->DescripcionLarga ?></textarea>	
						  </td>
		  				</tr>


						<tr class=row2>
						  <td>Imagen1 Para WEB (foto principal)</td>
						  <td>Imagen 2 Para WEB</td>
		  				</tr>
						<tr class=row2>
						  <td>
                          <?php if (!empty($r->FotoWeb1)): ?>
                          	<img src="<?php echo "imagenes/". $r->FotoWeb1; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=FotoWeb1&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="FotoWeb1" id="FotoWeb1" class=input>
                          <?php endif; ?>
                          </td>
						  <td><?php if (!empty($r->FotoWeb2)): ?>
                          	<img src="<?php echo "imagenes/". $r->FotoWeb2; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=FotoWeb2&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="FotoWeb2" id="FotoWeb2" class=input>
                          <?php endif; ?></td>
		  				</tr>
						<tr class=row2>
						  <td>Imagen 3 Para WEB</td>
						  <td>Imagen 4 Para WEB</td>
		  				</tr>
						<tr class=row2>
						  <td><?php if (!empty($r->FotoWeb3)): ?>
                          	<img src="<?php echo "imagenes/". $r->FotoWeb3; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=FotoWeb3&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="FotoWeb3" id="FotoWeb3" class=input>
                          <?php endif; ?></td>
						  <td><?php if (!empty($r->FotoWeb4)): ?>
                          	<img src="<?php echo "imagenes/". $r->FotoWeb4; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=FotoWeb4&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="FotoWeb4" id="FotoWeb4" class=input>
                          <?php endif; ?></td>
		  				</tr>



						<tr class=row3>
							<td colspan="2">
							<b>PUNTOS DE VENTA</b></td>
						</tr>
						<tr class=row2>
							<td colspan="2">
								<br>
								<?php								
									$condicion = " and Publicar = 'S' ";									
									table_check_list_desc($Table,$Key,$r->$Key,"PuntoVenta","IDPuntoVenta","PuntoVentaReferencia","PuntoVenta[]",$newmode, $condicion);
									
								?>
							</td>
						</tr>
						<tr class=row3>
							<td colspan="2">
								<input type="button" name="check" value="Seleccionar Todos" onClick="CheckAll();" class=submit>
							</td>
						</tr>
						<tr class=row2>
							<td colspan="2"> Descripci&oacute;n <br></td>
						</tr>
						<tr class=row2>
			<td width="50%"></td><td></td>
			</tr>
			<tr>
			<td colspan=2 align=center class=row2><input type=hidden name=IDReferencia id=IDReferencia value="<?php echo $r->IDReferencia ?>"><input type=hidden name=UsuarioTrCr value="<?php echo $r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?php echo $r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?php echo $r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?php echo $r->FechaTrEd ?>">
				<input type=hidden name=ID value="<?php echo $r->$Key ?>">
				<input type=hidden name=action value=<?php echo $newmode?>>
				<input type=submit name=submit value="<?php echo $submit_caption ?>" class=submit>
			</td>
			</tr>
			</table>
		</td>
	</tr>
</table>
</form>
<?php 
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar;

	if($_GET["t"]=="todos"){
		$where_publicar=" 1 ";
	}
	else{
		$where_publicar="Publicar='S'";
	}

	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table WHERE ".$where_publicar." ORDER BY $Key DESC";

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
<br>
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
<td class=texto bgcolor=#DBEAF5 colspan=16 nowrap>
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
				<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Numero&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Numero<?php  if($_GET['order_by']=="Numero"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
				<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDProveedor&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Proveedor&nbsp;<?php  if($_GET['order_by']=="IDProveedor"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
				<td class=rowform nowrap bgcolor=#DBEAF5><a href='<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDTipoTalla&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Tipo de Talla</a><a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDTipoTalla&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>"><?php  if($_GET['order_by']=="IDTipoTalla"){?><img src="images/<?php echo $img?>" border=0><?php }?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href='<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDTipoReferencia&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Tipo Ref.&nbsp;<?php  if($_GET['order_by']=="IDTipoReferencia"){?><img src="images/<?php echo $img?>" border=0><?php }?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDLinea&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Linea&nbsp;<?php  if($_GET['order_by']=="IDLinea"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Nombre<?php  if($_GET['order_by']=="Nombre"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Saldo&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Saldo<?php  if($_GET['order_by']=="Saldo"){?><img src="images/<?php echo $img?>" border=0><?php }?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Publicar&nbsp;<?php  if($_GET['order_by']=="Publicar"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
				</tr>

<?php while($r = db_fetch_object($result)){
?>

<tr>
<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
<td nowrap class=row1><?php echo $r->Numero ?></td>
<td nowrap class=row1><?php echo $r->IDProveedor ?></td>
						<td nowrap class=row1><?php echo get_field("TipoTalla","Descripcion","IDTipoTalla",$r->IDTipoTalla) ?></td>
						<td nowrap class=row1><?php echo get_field("TipoReferencia","Descripcion","IDTipoReferencia",$r->IDTipoReferencia)?></td>
						<td nowrap class=row1><?php echo get_field("Linea","Nombre","IDLinea",$r->IDLinea) ?></td>
						<td nowrap class=row1><?php echo $r->Nombre ?></td>
						<td nowrap class=row1><?php echo $r->Saldo ?></td>
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
					<option value="">Buscar Por</option>
					<option value="Numero">Numero</option>
					<option value="Tipo.descripcion">Tipo</option>
					<option value="Linea.Nombre">Linea</option>
					<option value="TipoTalla.Descripcion">tipo de Talla</option>
					<option value="Nombre">Nombre</option>
				</select>
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post">
				ordenar por
				<select name="order_by" class="popup">
					<option value="Numero">Numero</option>
					<option value="Tipo.descripcion">Tipo</option>
					<option value="Linea.Nombre">Linea</option>
					<option value="Nombre">Nombre</option>
				</select>
				<br>
				de forma
				<select name="in_order" class="popup">
					<option value="ASC">Ascendente</option>
					<option value="DESC">Descendente</option>
				</select>
				Listar
				<select name="listar" class="popup">
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
				<input type="hidden" name="tjoin" value="Linea">
				<input type="hidden" name="tlevel" value="TipoTalla">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php 
	}//End function filtrar
?>
