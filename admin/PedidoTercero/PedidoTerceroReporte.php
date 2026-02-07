
<body> <?php

//envia_pedido_tercero("54","reenvio");

//crear_pdf_pedido(132);

require($libdir."filelib.php");


$TitleMod ="Pedido Tercero";

$Table = "PedidoTercero";
$TableJoin = "PedidoTercero";
$Key = "IDPedidoTercero";
$MOD = "ReportePedidoTercero";
$m="PedidoTercero";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{


		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;

			case "insert" :
				$frm= vars_LOG($HTTP_POST_VARS);



				//Armar el codigo del pedido
				$codigo_proveedor=get_field("Proveedor","Codigo","IDProveedor",$frm[IDProveedor]);
				//Siguiente Consecutivo
				$sql_num_pedido = db_query("Select count(IDProveedor) as Total From PedidoTercero Where IDProveedor = '".$frm[IDProveedor]."'");
				$row_total = db_fetch_array($sql_num_pedido);
				$siguiente=(int)$row_total["Total"]+1;

				//Agrego los ceros para dar formato por ejemplo ZA0053
				if (strlen($siguiente)==1)
					$ceros="000";
				elseif(strlen($siguiente)==2)
					$ceros="00";
				elseif(strlen($siguiente)==3)
					$ceros="0";


				$numero_orden=$codigo_proveedor.$ceros.$siguiente;
				$frm[IDEstadoPedidoTercero] = "1";
				$frm["NumeroOrdenCompra"]=$numero_orden;
				//Subir imagenes
				$frm = copy_imgs($frm,$_FILES);
				$id = insert($frm);

				$Items = $frm['ITEMSDETALLE'];
				for($i = 1; $i < $Items; $i++){
					$IDDetallePedidoTercero = "IDDetallePedidoTercero".$i;
					$ReferenciaProveedor = "ReferenciaProveedor".$i;
					$ReferenciaCaprino = "ReferenciaCaprino".$i;
					$CodigoColor = "CodigoColor".$i;
					$CueroColor = "CueroColor".$i;
					$Suela = "Suela".$i;
					$Tacon = "Tacon".$i;
					$Altura = "Altura".$i;
					$Horma = "Horma".$i;
					$Producto = "Producto".$i;
					$Precio = "Precio".$i;
					$Observacion = "Observacion".$i;
					$IDCurvaTercero = "IDCurvaTercero".$i;

					$frm["IDDetallePedidoTercero"]=$frm[$IDDetallePedidoTercero];
					$frm["ReferenciaProveedor"]=$frm[$ReferenciaProveedor];
					$frm["ReferenciaCaprino"]=$frm[$ReferenciaCaprino];
					$frm["CodigoColor"]=$frm[$CodigoColor];
					$frm["CueroColor"]=$frm[$CueroColor];
					$frm["Suela"]=$frm[$Suela];
					$frm["Tacon"]=$frm[$Tacon];
					$frm["Altura"]=$frm[$Altura];
					$frm["Horma"]=$frm[$Horma];
					$frm["Producto"]=$frm[$Producto];
					$frm["Precio"]=$frm[$Precio];
					$frm["Observacion"]=$frm[$Observacion];
					$frm["IDCurvaTercero"]=$frm[$IDCurvaTercero];

					if (!empty($frm[$ReferenciaCaprino])){
							$sql_inserta_detalle = "Insert into DetallePedidoTercero (IDPedidoTercero, ReferenciaCaprino, ReferenciaProveedor, CodigoColor, CueroColor, Suela, Tacon, Altura, Horma, Producto, Precio, Observacion, IDCurvaTercero, UsuarioTrEd, FechaTrEd)
													Values('".$id."','".$frm["ReferenciaCaprino"]."','".$frm["ReferenciaProveedor"]."','".$frm["CodigoColor"]."','".$frm["CueroColor"]."','".$frm["Suela"]."','".$frm["Tacon"]."','".$frm["Altura"]."','".$frm["Horma"]."', '".$frm["Producto"]."', '".$frm["Precio"]."','".$frm["Observacion"]."','".$frm["IDCurvaTercero"]."','".$ID_Usuario."',NOW())";
							db_query($sql_inserta_detalle);
					}
				}//end for($i = 1; $i < $Items; $i++)


				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				$frm= vars_LOG($HTTP_POST_VARS);

				$actualiza_estado = "Update PedidoTercero Set IDEstadoPedidoTercero = '".$_POST["IDEstadoPedidoTercero"]."' Where IDPedidoTercero = '".$frm["IDPedidoTercero"]."'";
				db_query($actualiza_estado);

				$Items = $frm['ITEMSDETALLE'];
				for($i = 1; $i < $Items; $i++){
					$IDDetallePedidoTercero = "IDDetallePedidoTercero".$i;
					$ReferenciaProveedor = "ReferenciaProveedor".$i;
					$ReferenciaCaprino = "ReferenciaCaprino".$i;
					$CodigoColor = "CodigoColor".$i;
					$CueroColor = "CueroColor".$i;
					$Suela = "Suela".$i;
					$Tacon = "Tacon".$i;
					$Altura = "Altura".$i;
					$Horma = "Horma".$i;
					$Producto = "Producto".$i;
					$Precio = "Precio".$i;
					$Observacion = "Observacion".$i;
					$IDCurvaTercero = "IDCurvaTercero".$i;

					$frm["IDDetallePedidoTercero"]=$frm[$IDDetallePedidoTercero];
					$frm["ReferenciaProveedor"]=$frm[$ReferenciaProveedor];
					$frm["ReferenciaCaprino"]=$frm[$ReferenciaCaprino];
					$frm["CodigoColor"]=$frm[$CodigoColor];
					$frm["CueroColor"]=$frm[$CueroColor];
					$frm["Suela"]=$frm[$Suela];
					$frm["Tacon"]=$frm[$Tacon];
					$frm["Altura"]=$frm[$Altura];
					$frm["Horma"]=$frm[$Horma];
					$frm["Producto"]=$frm[$Producto];
					$frm["Precio"]=$frm[$Precio];
					$frm["Observacion"]=$frm[$Observacion];
					$frm["IDCurvaTercero"]=$frm[$IDCurvaTercero];

					if (!empty($frm[$ReferenciaCaprino])){
						// verifico si existe el item
						if (!empty($frm["IDDetallePedidoTercero"])){
							$sql_actualiza_detalle = "Update DetallePedidoTercero SET
													 ReferenciaCaprino = '".$frm["ReferenciaCaprino"]."',
													 ReferenciaProveedor = '".$frm["ReferenciaProveedor"]."',
													 CodigoColor = '".$frm["CodigoColor"]."' ,
													 CueroColor = '".$frm["CueroColor"]."',
													 Suela = '".$frm["Suela"]."',
													 Tacon = '".$frm["Tacon"]."',
													 Altura = '".$frm["Altura"]."',
													 Horma = '".$frm["Horma"]."',
													 Producto = '".$frm["Producto"]."',
													 Precio = '".$frm["Precio"]."',
													 Observacion = '".$frm["Observacion"]."',
													 IDCurvaTercero = '".$frm["IDCurvaTercero"]."',
													 UsuarioTrEd = '".$ID_Usuario."',
													 FechaTrEd = NOW()
													 Where IDDetallePedidoTercero = '".$frm["IDDetallePedidoTercero"]."'";
							db_query($sql_actualiza_detalle);


						}
						else{
							$sql_inserta_detalle = "Insert into DetallePedidoTercero (IDPedidoTercero, ReferenciaCaprino, ReferenciaProveedor, CodigoColor, CueroColor, Suela, Tacon, Altura, Horma, Producto, Precio, Observacion, IDCurvaTercero, UsuarioTrEd, FechaTrEd)
													Values('".$frm["IDPedidoTercero"]."','".$frm["ReferenciaCaprino"]."','".$frm["ReferenciaProveedor"]."','".$frm["CodigoColor"]."','".$frm["CueroColor"]."','".$frm["Suela"]."','".$frm["Tacon"]."','".$frm["Altura"]."','".$frm["Horma"]."', '".$frm["Producto"]."', '".$frm["Precio"]."','".$frm["Observacion"]."','".$frm["IDCurvaTercero"]."','".$ID_Usuario."',NOW())";
							db_query($sql_inserta_detalle);
						}

					}
				}//end for($i = 1; $i < $Items; $i++)

				//Subir imagenes
				$frm = copy_imgs($frm,$_FILES);



				update($frm);
			break;

			case "inserta_detalle_referencia";
			$frm= vars_LOG($HTTP_POST_VARS);
			//echo "Select * From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$frm["IDPedidoTercero"]."'";
			//ACTUALIZO LAS CANTIDADES DE PEDIDO POR PUNTO, TALLA y REFERENCIA
				//$sql_borrar_anterior=db_query("Delete From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$frm["IDPedidoTercero"]."'");
				foreach($frm as $id_dato => $datos1){
						foreach($datos1 as $id_talla => $dato_talla)	{
							foreach($dato_talla as $id_punto => $dato_punto)	{
								foreach($dato_punto as $id_referencia => $cantidad)	{
									//echo "<br>ES Talla " . 	$id_talla . " Punto " . $id_punto . "Referencia " . $id_referencia . " Valor = " . $cantidad;
									if (is_numeric($cantidad)){
										// Si ya existe lo actualizo de lo contrario inserto
										$qry_detalle_ref = "Select * From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$frm["IDPedidoTercero"]."' and IDDetallePedidoTercero = '".$id_referencia."'and IDPuntoVenta = '".$id_punto."' and IDTalla = '".$id_talla."'";
										$sql_detalle_ref = db_query($qry_detalle_ref);
										if(db_num_rows($sql_detalle_ref)>0):
											//echo "<br>Actualizo";
											$row_detalle_ref = db_fetch_array($sql_detalle_ref);
											$id_detalle_ref = $row_detalle_ref["IDDetallePedidoTerceroReferencia"];
											$sql_detalle_ref_update = "Update DetallePedidoTerceroReferencia SET Cantidad = '".$cantidad."'  Where IDDetallePedidoTerceroReferencia = '".$id_detalle_ref."'";
											db_query($sql_detalle_ref_update);
										else:
											//echo "<br>Inserto";
											$sql_detalle_pedido = "Insert Into DetallePedidoTerceroReferencia (IDPedidoTercero, IDDetallePedidoTercero, IDPuntoVenta, IDTalla, Cantidad, Estado, UsuarioTrCr, FechaTrCr)
														Values('".$frm["IDPedidoTercero"]."','".$id_referencia."','".$id_punto."','".$id_talla."','".$cantidad."','Enviado','".$ID_Usuario."',NOW())";
										db_query($sql_detalle_pedido);
										endif;
									}
								}
							}
						}
				}

			?>
			<script>
            	alert("Pedido Guardado con exito");
            </script>
			<?php
			print_form($frm["IDPedidoTercero"],"update","Actualizar $TitleMod","Realizar Cambios");

			break;

			case "genera_pedido":
				?>
                <script>
    	        	alert("Pedido Generado con exito");
	            </script>
                <?php
				print_form($_GET[id],"update","Actualizar $TitleMod","Realizar Cambios");
			break ;

			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;

			case "delfoto" :
				$sql_actualiza=db_query("Update ".$Table." Set " . $_GET[campo] . "='' Where ".$Key." = '".$_GET[id]."'");
				print_form($_GET[id],"update","Actualizar $TitleMod","Realizar Cambios");
			break;

			case "delitem" :
				$sql_borra_item = "Delete From DetallePedidoTercero Where 	IDDetallePedidoTercero = '".$_GET[iditem]."'";
				$sql_borra_detalle_item = "Delete From DetallePedidoTerceroReferencia Where IDDetallePedidoTercero = '".$_GET[iditem]."'";
				$sql_actualiza_item=db_query($sql_borra_item);
				$sql_actualiza_itemdetalle=db_query($sql_borra_detalle_item);
				?>
                <span style="color:#FF7477; font-size:12px; font-weight:bold">
                <?php //echo get_field("EstadoPedidoTercero","Descripcion","IDEstadoPedidoTercero",$r->IDEstadoPedidoTercero); ?>
                </span>
<script>
    	        	alert("Item borrado con exito");
	            </script>
                <?php
				print_form($_GET[id],"update","Actualizar $TitleMod","Realizar Cambios");
			break;

			case "activarpedido" :
				$update_estado = "Update PedidoTercero Set IDEstadoPedidoTercero = 1 Where IDPedidoTercero  = '".$_GET[id]."'";
				$sql_actualiza_estado=db_query($update_estado);
				?>
<script>
    	        	alert("Estado actualizado con exito");
	            </script>
                <?php
				print_form($_GET[id],"update","Actualizar $TitleMod","Realizar Cambios");
			break;

			case "delete" :
				$HTTP_GET_VARS[action]="";
				delete($ID);
			break;
			case "list" :
			if(!empty($_GET[NumeroFactura])):
					$condiciones .=" and DPTR.Numerofactura LIKE '%".$_GET[NumeroFactura]."%'";
				endif;

				if(!empty($_GET[NumeroOrdenCompra]))
					$condiciones.=" and PT.NumeroOrdenCompra LIKE '%".$_GET[NumeroOrdenCompra]."%'";

				if(!empty($_GET[IDProveedor]))
					$condiciones.=" and PT.IDProveedor = '".$_GET[IDProveedor]."'";

				if(!empty($_GET[IDEstadoPedidoTercero]))
					$condiciones.=" and PT.IDEstadoPedidoTercero = '".$_GET[IDEstadoPedidoTercero]."'";

				if(!empty($_GET[ReferenciaCaprino]))
					$condiciones.=" and DPT.ReferenciaCaprino  like '%".$_GET[ReferenciaCaprino]."%'";

				if(!empty($_GET[FechaDesde])  && !empty($_GET[FechaHasta])){
					$condiciones.=" and DPTR.FechaRecibido >= '".$_GET[FechaDesde]." 00:00:00' and DPTR.FechaRecibido <= '".$_GET[FechaHasta]." 23:59:59' ";
					$condicion_fecha.=" and FechaRecibido >= '".$_GET[FechaDesde]." 00:00:00' and FechaRecibido <= '".$_GET[FechaHasta]." 23:59:59' ";
				}

				if(!empty($_GET[Tipologia]))
					$condiciones.=" and DPT.Producto  like '%".$_GET[Tipologia]."%'";

				if(!empty($_GET[IDPuntoVenta])):
					$condiciones.=" and DPTR.IDPuntoVenta = '".$_GET[IDPuntoVenta]."'";
					$condicion_ptovta.=" and IDPuntoVenta = '".$_GET[IDPuntoVenta]."' ";
				endif;



				if (!empty($_GET[limit1]) && !empty($_GET[limit2]) )
					$condiciones.=" and G.FechaTrCr between '".$_GET[limit1]."' and '".$_GET[limit2]."'";

				$sql = "Select PT.*
					  From PedidoTercero PT, DetallePedidoTercero DPT, DetallePedidoTerceroReferencia DPTR
					  Where PT.IDPedidoTercero = DPT.IDPedidoTercero AND
					  DPT.IDDetallePedidoTercero = DPTR.IDDetallePedidoTercero
					  $condiciones
					  Group by PT.IDPedidoTercero
					  Order by IDPedidoTercero desc";


			//$sql = make_qry_string($HTTP_GET_VARS);
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

	GLOBAL $TitleMod,$Table,$MOD,$Key,$strcript,$array_nivel, $Nivel;


	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' $where ");
	$r = db_fetch_object($qid);

	/*
	$sql_referencias = "SELECT * FROM Referencia WHERE Publicar <> 'N' ORDER BY Numero";
	$qry_referencias = db_query( $sql_referencias );
	while( $r_referencias = db_fetch_array( $qry_referencias ) )
		$array_referencias[] = $r_referencias;
	*/

	if (!empty($id)){
		$item_detalle=1;
		$q_detalle = db_query(" SELECT * FROM DetallePedidoTercero WHERE IDPedidoTercero = '$id'");
		while( $r_detalle = db_fetch_array( $q_detalle,$a ) ){
			$array_detalle_orden[ $item_detalle ] = $r_detalle;
			$item_detalle++;
		}
	}


?>
<script>
var Check = new Array('IDProveedor','FechaPedido','FechaEntrega');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76;?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
			<a href="./?mod=<?php echo $MOD;?>">Administrar <?php echo $TitleMod;?></a> </td>
			<td><a href="./?mod=<?php echo $MOD;?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>

<div id="Pedido" style=" <?php if ($_GET[tab]=="detalle" && !empty($id)){ ?> display:none;  <?php } ?>">

<form name="frm" id="frmPedidoTercero" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){ ?> onSubmit="return EvaluaReg(this,Check)" <?php } ?>>


<table cellpadding=1 cellspacing=0 class=bordertable align=center width="70%" >
	<tr>
	  <td>
      	    <table  border="0" cellspacing="0" cellpadding="0">
		<tr>
				<td >


                <?php
						if($_GET[tab]=="" || $_GET[tab]=="pedido" ):
							$color_tab = "#18C3D5";
						else:
							$color_tab = "#02387A";
						endif;
					?>

					<table  border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $color_tab; ?>" id=TB1>
						<tr height="16">
							<td class="LeftCurve" valign="top" align="left" width="12" height="16" nowrap>&nbsp;&nbsp;&nbsp;</td>
							<td valign="top" nowrap  height="16"><a href="./?mod=<?php echo $MOD;?>&action=edit&id=<?=$id?>&tab=pedido" class="TAB">PEDIDO TERCERO</a>&nbsp;</td>
							<td align="right" class="RightCurve" width="10" nowrap height="16">&nbsp;&nbsp;</td>
						</tr>
					</table>
				</td>
				<td width="4"></td>
				<td >
                	<?php
						if($_GET[tab]=="detalle"):
							$color_tab = "#18C3D5";
						else:
							$color_tab = "#02387A";
						endif;
					?>
					<table border="0"  bgcolor="<?php echo $color_tab; ?>" cellspacing="0" cellpadding="0" id=TB2>
						<tr height="16">
							<td class="LeftCurve" valign="top" align="left" width="12" height="16" nowrap>&nbsp;&nbsp;&nbsp;</td>
							<td valign="top" height="16"><a href="./?mod=<?php echo $MOD;?>&id=<?=$id?>&tab=detalle&action=edit" class="TAB">DETALLE PEDIDO</a>&nbsp;</td>
							<td align="right" class="RightCurve" width="10" nowrap height="16">&nbsp;&nbsp;</td>
						</tr>
					</table>
				</td>

				<td>&nbsp;</td>
                <td >
                	<?php
						if($_GET[tab]=="verificacion"):
							$color_tab = "#18C3D5";
						else:
							$color_tab = "#02387A";
						endif;
					?>
					<table border="0"  bgcolor="<?php echo $color_tab; ?>" cellspacing="0" cellpadding="0" id=TB2>
						<tr height="16">
							<td class="LeftCurve" valign="top" align="left" width="12" height="16" nowrap>&nbsp;&nbsp;&nbsp;</td>
							<td valign="top" height="16"><a href="./?mod=EntregaTercero&id=<?=$id?>&tab=verificacion&action=edit" class="TAB">VERIFICACION PEDIDO</a>&nbsp;</td>
							<td align="right" class="RightCurve" width="10" nowrap height="16">&nbsp;&nbsp;</td>
						</tr>
					</table>
				</td>



			</tr>
	</table>

      </td>
	  </tr>
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width="100%" border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
						  <td width="11%"><span style="color:#FF7477; font-size:12px; font-weight:bold">ESTADO</span></td>
						  <td colspan="3"><span style="color:#FF7477; font-size:12px; font-weight:bold">
                          <?php echo formpopup("EstadoPedidoTercero","Descripcion","Descripcion","IDEstadoPedidoTercero",$r->IDEstadoPedidoTercero,"input\" id=\"IDCiudad"); ?></span>

                          <?php
						  if($r->IDEstadoPedidoTercero=="2" && $Nivel==0): ?>
                          	<a href='<?php echo "?mod=$MOD&action=activarpedido&id=".$r->$Key ?>'>Cambiar estado a Guardado</a>
                          <?php endif;?>


                          </td>
		  </tr>

				<tr class=row2>
				<td>Proveedor</td>
				<td colspan="3">
				<select name="IDProveedor" id="IDProveedor" class="input proveedor_pedido">
                	<option value="">[Seleccione]</option>
                    <?php
						$sql_prov = db_query("Select * From Proveedor where Publicar = 'S' order by Nombre asc");
						while ($row_prov = db_fetch_array($sql_prov)): ?>
                        	<option value="<?php echo $row_prov[IDProveedor];?>" <?php if($row_prov[IDProveedor]==$r->IDProveedor) echo "selected"; ?>><?php echo $row_prov[Nombre];?></option>
						<?php
						endwhile;
					?>
                </select>
				</td>
			</tr>
						<tr class=row2>
				<td colspan="4"><table width="90%" border="0" style="border:1px solid #E8E2E2" align="center">
				  <tbody>
				    <tr>
				      <th colspan="4">Datos Proveedor
				        <?php
									if( !empty($r->IDProveedor)){
									 	 $sql_datos_proveedor = db_query("Select * From Proveedor Where IDProveedor = '" . $r->IDProveedor . "'");
										 $datos_proveedor = db_fetch_array($sql_datos_proveedor);
										 $datos_proveedor[Ciudad]=get_field("Ciudad","Descripcion","IDCiudad",$datos_proveedor[IDCiudad]);

									}
									?>
			          </th>
			        </tr>
				    <tr>
				      <td width="16%"><strong>Nombre</strong></td>
				      <td width="31%"><span id="NombreProveedor"><?php echo $datos_proveedor[Nombre] ?></span></td>
				      <td width="19%"><strong>Direccion</strong></td>
				      <td width="34%"><span id="DireccionProveedor"><?php echo $datos_proveedor[Direccion] ?></span></td>
			        </tr>
				    <tr>
				      <td><strong>Telefono</strong></td>
				      <td><span id="TelefonoProveedor"><?php echo $datos_proveedor[Telefono] ?></span></td>
				      <td><strong>Ciudad</strong></td>
				      <td><span id="CiudadProveedor"><?php echo $datos_proveedor[Ciudad] ?></span></td>
			        </tr>
				    <tr>
				      <td><strong>Email</strong></td>
				      <td colspan="3"><span id="EmailProveedor"><?php echo $datos_proveedor[Email] ?></span></td>
			        </tr>
			      </tbody>
				  </table></td>
			</tr>
            <tr class=row2>
			              <td> Orden de Compra</td>
			              <td colspan="3" abbr="">
                          <?php if($newmode=="insert"): ?>
                          	Automatico
                          <?php else: ?>
                          <input type=text size=25 class=input   name=NumeroOrdenCompra id=NumeroOrdenCompra value="<?=$r->NumeroOrdenCompra ?>" readonly>

                          <?php if((int)$r->IDEstadoPedidoTercero>1 ): ?>
                          PDF Pedido
                          <a target="_blank" href="PedidoTercero/pedidos/Pedido<?php echo $r->NumeroOrdenCompra; ?>.pdf"><img src="images/descargapdf.jpeg" width="30" height="30">
                          <?php endif; ?>
                          </td>

						  <?php endif; ?>

          </tr>

            <tr class=row2>
              <td>Fecha Pedido</td>
              <td width="28%"><span class="col2">
                <input type=text readonly size=10 class=input name=FechaPedido id="FechaPedido" title="Fecha Pedido" value="<?=$r->FechaPedido ?>">
                <script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaPedido,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//-->
				</script>
              </span></td>
              <td width="13%">Fecha Entrega</td>
              <td width="48%"><input type=text size=10 readonly class=input name=FechaEntrega id="FechaEntrega" title="Fecha Entrega"  value="<?=$r->FechaEntrega ?>">
              <script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaEntrega,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//-->
				</script>

              </td>
            </tr>



            <tr class=row2>
              <td>Condiciones Pago</td>
              <td colspan="3" valign="middle">
              <textarea name="NotaPago" id="NotaPago" cols="80" rows="2"><?php if (!empty($r->NotaPago)) echo $r->NotaPago; else echo get_field("ParametroTercero","Descripcion","IDParametroTercero",7);  ?></textarea>
              15 Dias
                <input type=text size=2 class=input   name="CondicionPago30" id="CondicionPago30" value="<?=$r->CondicionPago30 ?>">
                 % 30  Dias
                 <input type=text size=2 class=input   name="CondicionPago45" id="CondicionPago45" value="<?=$r->CondicionPago45 ?>">
                 %
                 45 Dias
                 <input type=text size=2 class=input   name="CondicionPago60" id="CondicionPago60" value="<?=$r->CondicionPago60 ?>">
                 %</td>
            </tr>


            <tr class=row2>
              <td colspan="4"></td>
            </tr>
			<?php
			if($r->Publicar == 'S')
			{
			?>
			<?php
			}
			?>
			<tr class=row2>
			  <td>Nota 1</td>
			  <td colspan="3"><textarea name="Nota1" class="" title="Nota1" id="Nota1" cols="140" rows="5"><?php if (!empty($r->Nota1)) echo $r->Nota1; else echo get_field("ParametroTercero","Descripcion","IDParametroTercero",1);  ?></textarea></td>
		  </tr>
			<tr class=row2>
			  <td>Nota 2</td>
			  <td colspan="3"><textarea name="Nota2" class="tbox" title="Nota1" id="Nota2" cols="140" rows="5"><?php if (!empty($r->Nota2)) echo $r->Nota2; else echo get_field("ParametroTercero","Descripcion","IDParametroTercero",2);  ?></textarea></td>
		  </tr>
			<tr class=row2>
			<td> Observaciones </td><td colspan="3"><textarea name="Observaciones" class="tbox" title="Observaciones" id="Observaciones" cols="140" rows="5"><?php if (!empty($r->Observaciones)) echo $r->Observaciones; else echo get_field("ParametroTercero","Descripcion","IDParametroTercero",4);  ?></textarea></td>
			</tr>
			<tr class=row2>
			  <td colspan="4"><table width="100%" border="0" class=row2>
			    <tbody>
			      <tr>
			        <td class="row2">Foto 1</td>
			        <td class="row2">
	                    <?php if (!empty($r->Foto1)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto1; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto1&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto1" id="Foto1" class=input>
                          <?php endif; ?>
                    </td>
			        <td>Foto 2</td>
			        <td><?php if (!empty($r->Foto2)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto2; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto2&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto2" id="Foto2" class=input>
                          <?php endif; ?></td>
		          </tr>
			      <tr>
			        <td class="row2">Foto 3</td>
			        <td class="row2"><?php if (!empty($r->Foto3)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto3; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto3&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto3" id="Foto3" class=input>
                          <?php endif; ?></td>
			        <td>Foto 4</td>
			        <td><?php if (!empty($r->Foto4)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto4; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto4&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto4" id="Foto4" class=input>
                          <?php endif; ?></td>
		          </tr>
			      <tr>
			        <td class="row2">Foto 5</td>
			        <td class="row2"><?php if (!empty($r->Foto5)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto5; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto5&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto5" id="Foto5" class=input>
                          <?php endif; ?></td>
			        <td>Foto 6</td>
			        <td><?php if (!empty($r->Foto6)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto6; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto6&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto6" id="Foto6" class=input>
                          <?php endif; ?></td>
		          </tr>
			      <tr>
			        <td class="row2">Foto 7</td>
			        <td class="row2"><?php if (!empty($r->Foto7)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto7; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto7&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto7" id="Foto7" class=input>
                          <?php endif; ?></td>
			        <td>Foto 8</td>
			        <td> <?php if (!empty($r->Foto8)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto8; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto8&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto8" id="Foto8" class=input>
                          <?php endif; ?></td>
		          </tr>
		        </tbody>
		      </table></td>
		  </tr>
			<tr>
			<td colspan=4 align=center class=row2>&nbsp;</td>
			</tr>
			</table>




		</td>
	</tr>
</table>




  <div id="detalle-cont">
        <table id="tabla_detalle_pedido" width="70%" border="0" align="center" style="border:1px solid #E5E5E5" cellpadding="2" cellspacing="1">
                <tbody>
                  <tr bgcolor="#9399E4" class="maintitle"  align="center" >
                    <td style="color:#FFFFFF; !important;">Ref <br>
                    Provee</td>
                    <td style="color:#FFFFFF; !important;">Ref <br>
                    Caprino</td>
                    <td style="color:#FFFFFF; !important;">COL</td>
                    <td style="color:#FFFFFF; !important;">CUERO Y COLOR</td>
                    <td style="color:#FFFFFF; !important;">SUELA</td>
                    <td style="color:#FFFFFF; !important;">TACON/<br>ALTURA</td>
                    <td style="color:#FFFFFF; !important;">HORMA</td>
                    <td style="color:#FFFFFF; !important;">PRODUCTO</td>
                    <td style="color:#FFFFFF; !important;">PRECIO</td>
                    <td style="color:#FFFFFF; !important;">OBSERVACION</td>
                    <td style="color:#FFFFFF; !important;">Curva</td>
                    <td style="color:#FFFFFF; !important;">Eliminar</td>
                  </tr>
                  <?php

				  if (count($array_detalle_orden)>0)
				  		$detalle_inicial=(int)count($array_detalle_orden)+10;
				else
					  $detalle_inicial=9;



				  for($i=1;$i<$detalle_inicial;$i++):  ?>
                      <tr>
                        <td >
                        <input type="hidden" name="IDDetallePedidoTercero<?php echo $i ?>" id="IDDetallePedidoTercero<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["IDDetallePedidoTercero"];  ?>">
                        <input type=text size=7 class=input  name="ReferenciaProveedor<?php echo $i ?>" id="ReferenciaProveedor<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["ReferenciaProveedor"];  ?>"></td>
                        <td><input type=text size=4 class="input"  name="ReferenciaCaprino<?php echo $i ?>" id="ReferenciaCaprino<?php echo $i ?>" alt="<?php echo $i;  ?>" value="<?php echo $array_detalle_orden[$i]["ReferenciaCaprino"];  ?>"></td>
                        <td><input type=text size=2 class="input verifica_referencia"   name="CodigoColor<?php echo $i ?>" id="CodigoColor<?php echo $i ?>" alt="<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["CodigoColor"];  ?>"></td>
                        <td><textarea name="CueroColor<?php echo $i ?>" class="input" title="CueroColor" role="<?php echo $i ?>"  ="<?php echo $i ?>" id="CueroColor<?php echo $i ?>" cols="30" rows="3"><?php echo $array_detalle_orden[$i]["CueroColor"];  ?></textarea></td>
                        <td><input type=text size=10 class=input   name="Suela<?php echo $i ?>" id="Suela<?php echo $i ?>" alt="<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["Suela"];  ?>"></td>
                        <td><input type=text size=10 class=input   name="Tacon<?php echo $i ?>" id="Tacon<?php echo $i ?>" alt="<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["Tacon"];  ?>"></td>
                        <td><input type=text size=3 class=input   name="Horma<?php echo $i ?>" id="Horma<?php echo $i ?>" alt="<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["Horma"];  ?>"></td>
                        <td><input type=text size=16 class=input   name="Producto<?php echo $i ?>" id="Producto<?php echo $i ?>" alt="<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["Producto"];  ?>">                        </td>
                        <td><input type=text size=6 class="input onlynumber"   name="Precio<?php echo $i ?>" alt="<?php echo $i ?>" id="Precio<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["Precio"];  ?>"></td>
                        <td><textarea name="Observacion<?php echo $i ?>" class="input" title="Observacion" role="<?php echo $i ?>"  ="<?php echo $i ?>" id="Observacion<?php echo $i ?>" cols="25" rows="3"><?php echo $array_detalle_orden[$i]["Observacion"];  ?></textarea></td>
                        <td>
                        <select name="IDCurvaTercero<?php echo $i ?>" id="IDCurvaTercero<?php echo $i ?>" class="input" style="width:100px;">
                        	<option value=""></option>
						<?php $sql_curvas = db_query("Select * From CurvaTercero Where 1 Order By Nombre");
							  while($row_curva = db_fetch_array($sql_curvas)){ ?>
								  	<option value="<?php echo $row_curva[IDCurvaTercero]; ?>" <?php if($array_detalle_orden[$i]["IDCurvaTercero"]==$row_curva[IDCurvaTercero]) echo "selected"; ?>><?php echo $row_curva[Nombre]; ?></option>
							   <?php } ?>
                        </select>
						</td>
                        <td align="center">
                        <?php if (!empty($array_detalle_orden[$i]["IDDetallePedidoTercero"]) && $r->IDEstadoPedidoTercero != 2): ?>
                        <a href='<?php echo "?mod=$MOD&action=delitem&id=".$r->$Key."&iditem="; echo $array_detalle_orden[$i]["IDDetallePedidoTercero"]; ?>'><img src='images/trash.gif' border='0'></a>
                        <?php endif; ?>
                        </td>
                      </tr>
                  <?php
				  endfor; ?>
                  </tbody>
              </table>
              </div>



    <table align="center" width="70%">
			<tr>
			  <td colspan=2 align="right">
		               <input type="hidden" name="ITEMSDETALLE" id="ITEMSDETALLE" value="<?php echo $detalle_inicial; ?>">
                        <!-- <button class="btn btn-default pull-right" id="agrega_detalle"><i class="fa fa-plus"></i> [+]Agregar mas</button> -->
              </td>
			  </tr>
			<tr>
			  <td colspan=2 align=center class=maintitle bgcolor=#9daac6>PRIORIDAD DE ENTREGA</td>
			  </tr>
			<tr>
			  <td colspan=2 align=center class=row2><table width="100%" border="0" >
			    <tbody>
			      <tr>
			        <td valign="top"><table width="100%" border="0" class=texto>
			          <tbody>
			            <tr>
			              <td colspan="3" align="center" bgcolor="#B1CFE6"><strong>BOGOTA</strong></td>
		                </tr>
			            <tr>
			              <td align="center" bgcolor="#FFFB4F">1</td>
			              <td align="center" bgcolor="#E7C8E8">2</td>
			              <td align="center" bgcolor="#EDD6CA">3</td>
		                </tr>
			            <tr>
			              <td>
                          <?php
                          $sql_prioridad_alta=db_query("Select Nombre from PuntoVenta Where IDTipoPrioridad = 1 and IDCiudad = 1 and Publicar = 'S'");
						  while ($row_pto = db_fetch_array($sql_prioridad_alta)){
							echo $row_pto[Nombre] ."<br>";
						  }
						  ?>

                          </td>
			              <td><?php
                          $sql_prioridad_media=db_query("Select Nombre from PuntoVenta Where IDTipoPrioridad = 2 and IDCiudad = 1 and Publicar = 'S'");
						  while ($row_pto = db_fetch_array($sql_prioridad_media)){
							echo $row_pto[Nombre] ."<br>";
						  }
						  ?></td>
			              <td><?php
                          $sql_prioridad_baja=db_query("Select Nombre from PuntoVenta Where IDTipoPrioridad = 3 and IDCiudad = 1 and Publicar = 'S'");
						  while ($row_pto = db_fetch_array($sql_prioridad_baja)){
							echo $row_pto[Nombre] ."<br>";
						  }
						  ?></td>
		                </tr>
		              </tbody>
		            </table></td>
			        <td valign="top"><table width="100%" border="0" class=texto>
			          <tbody>
			            <tr>
			              <td colspan="3" align="center" bgcolor="#BDD9BF"><strong>MEDELLIN</strong></td>
		                </tr>
			            <tr>
			              <td align="center" bgcolor="#FFFB4F">1</td>
			              <td align="center" bgcolor="#E7C8E8">2</td>
			              <td align="center" bgcolor="#EDD6CA">3</td>
		                </tr>
			            <tr>
			              <td><?php
                          $sql_prioridad_alta=db_query("Select Nombre from PuntoVenta Where IDTipoPrioridad = 1 and IDCiudad = 2 and Publicar = 'S'");
						  while ($row_pto = db_fetch_array($sql_prioridad_alta)){
							echo $row_pto[Nombre] ."<br>";
						  }
						  ?></td>
			              <td><?php
                          $sql_prioridad_media=db_query("Select Nombre from PuntoVenta Where IDTipoPrioridad = 2 and IDCiudad = 2 and Publicar = 'S'");
						  while ($row_pto = db_fetch_array($sql_prioridad_media)){
							echo $row_pto[Nombre] ."<br>";
						  }
						  ?></td>
			              <td><?php
                          $sql_prioridad_baja=db_query("Select Nombre from PuntoVenta Where IDTipoPrioridad = 3 and IDCiudad = 2 and Publicar = 'S'");
						  while ($row_pto = db_fetch_array($sql_prioridad_baja)){
							echo $row_pto[Nombre] ."<br>";
						  }
						  ?></td>
		                </tr>
		              </tbody>
		            </table></td>
		          </tr>
		        </tbody>
		      </table></td>
			  </tr>
			<tr>
			<td colspan=2 align=center class=row2>


            <input type=hidden name=IDPedidoTercero id=IDPedidoTercero value="<?=$r->IDPedidoTercero ?>">
			  <input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
              <input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
              <input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
              <input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
              <input type=hidden name=ID value="<?php echo $r->$Key ?>">
              <input type=hidden name=action value=<?=$newmode?>>
              <!-- <input type=hidden name="IDEstadoPedidoTercero" id="IDEstadoPedidoTercero" value=<?php if($newmode=="insert") echo "1"; else echo $r->IDEstadoPedidoTercero ?>> -->

              <?php if($r->IDEstadoPedidoTercero == 1 || $r->IDEstadoPedidoTercero == ""):  ?>
              <input type=submit name=submit value="<?php if($newmode=="insert") echo "Guardar y Continuar"; else echo $submit_caption ?>" class=submit>
              <?php endif; ?>
              </td>
			</tr>
			</table>


</form>
</div>

<?php
//SUBDETALLE PEDIDO
if ($_GET["tab"]=="detalle" && !empty($id)){

    $sql_tallas = "Select * From Talla Where 1 Group By Descripcion Order By Nombre";
	$result_talla = db_query($sql_tallas);
	while ($row_talla = db_fetch_array($result_talla)){
		$array_talla[ $row_talla["IDTalla"] ] = $row_talla;
	}

	//Si ya fue guardado solo mustro los puntos de venta seleccionados


	if($r->IDEstadoPedidoTercero == 1):
		$sql_punto_venta = "Select IDPuntoVenta,Nombre,IDCiudad From PuntoVenta Where Publicar = 'S' and  IDPuntoVenta not in  (16,21)   Order By IDCiudad, Nombre";

	else:
		$sql_punto_venta_pedido = "Select IDPuntoVenta From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$r->IDPedidoTercero."' Group by IDPuntoVenta";
		$result_punto_venta_pedido = db_query($sql_punto_venta_pedido);
		while ($row_punto_venta_pedido = db_fetch_array($result_punto_venta_pedido)){
			$array_puntos_pedido [] = $row_punto_venta_pedido["IDPuntoVenta"];
		}

		if (count($array_puntos_pedido)>0):
			$id_puntos = implode(",",$array_puntos_pedido);
		endif;

		$sql_punto_venta = "Select IDPuntoVenta,Nombre,IDCiudad From PuntoVenta Where Publicar = 'S' and IDPuntoVenta  in  (".$id_puntos.")   Order By IDCiudad, Nombre";


	endif;


		$result_punto_venta = db_query($sql_punto_venta);
		while ($row_punto_venta = db_fetch_array($result_punto_venta)){
			$array_punto_venta[ $row_punto_venta["IDPuntoVenta"] ] = $row_punto_venta;
		}



?>



<div id="DetallePedido" style=" <?php if ($_GET[tab]!="detalle" && !empty($id)){ ?> display:none;  <?php } ?>">

<form name="frm" id="frmPedidoTercero" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){ ?> onSubmit="return EvaluaReg(this,Check)" <?php } ?>>

<table cellpadding=1 cellspacing=0 class=bordertable align=center width="70%" >
	<tr>
	  <td>
      <table  border="0" cellspacing="0" cellpadding="0">
		<tr>
				<td >


                <?php
						if($_GET[tab]=="" || $_GET[tab]=="pedido" ):
							$color_tab = "#18C3D5";
						else:
							$color_tab = "#02387A";
						endif;
					?>

					<table  border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $color_tab; ?>" id=TB1>
						<tr height="16">
							<td class="LeftCurve" valign="top" align="left" width="12" height="16" nowrap>&nbsp;&nbsp;&nbsp;</td>
							<td valign="top" nowrap  height="16"><a href="./?mod=<?php echo $MOD;?>&action=edit&id=<?=$id?>&tab=pedido" class="TAB">PEDIDO TERCERO</a>&nbsp;</td>
							<td align="right" class="RightCurve" width="10" nowrap height="16">&nbsp;&nbsp;</td>
						</tr>
					</table>
				</td>
				<td width="4"></td>
				<td >
                	<?php
						if($_GET[tab]=="detalle"):
							$color_tab = "#18C3D5";
						else:
							$color_tab = "#02387A";
						endif;
					?>
					<table border="0"  bgcolor="<?php echo $color_tab; ?>" cellspacing="0" cellpadding="0" id=TB2>
						<tr height="16">
							<td class="LeftCurve" valign="top" align="left" width="12" height="16" nowrap>&nbsp;&nbsp;&nbsp;</td>
							<td valign="top" height="16"><a href="./?mod=<?php echo $MOD;?>&id=<?=$id?>&tab=detalle&action=edit" class="TAB">DETALLE PEDIDO</a>&nbsp;</td>
							<td align="right" class="RightCurve" width="10" nowrap height="16">&nbsp;&nbsp;</td>
						</tr>
					</table>
				</td>

				<td>&nbsp;</td>
                <td >
                	<?php
						if($_GET[tab]=="verificacion"):
							$color_tab = "#18C3D5";
						else:
							$color_tab = "#02387A";
						endif;
					?>
					<table border="0"  bgcolor="<?php echo $color_tab; ?>" cellspacing="0" cellpadding="0" id=TB2>
						<tr height="16">
							<td class="LeftCurve" valign="top" align="left" width="12" height="16" nowrap>&nbsp;&nbsp;&nbsp;</td>
							<td valign="top" height="16"><a href="./?mod=EntregaTercero&id=<?=$id?>&tab=verificacion&action=edit" class="TAB">VERIFICACION PEDIDO</a>&nbsp;</td>
							<td align="right" class="RightCurve" width="10" nowrap height="16">&nbsp;&nbsp;</td>
						</tr>
					</table>
				</td>

				<td width="4"></td>

			</tr>
	</table>

      </td>
	  </tr>
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width="100%" border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
						  <td width="23%"><span style="color:#FF7477; font-size:12px; font-weight:bold">ESTADO</span></td>
						  <td width="77%"><span style="color:#FF7477; font-size:12px; font-weight:bold"><?php echo get_field("EstadoPedidoTercero","Descripcion","IDEstadoPedidoTercero",$r->IDEstadoPedidoTercero); ?></span></td>
		  </tr>
						<tr class=row2>
				<td colspan="2"><table width="90%" border="0" style="border:1px solid #E8E2E2" align="center">
				  <tbody>
				    <tr>
				      <th colspan="4">Datos Proveedor
				        <?php
									if( !empty($r->IDProveedor)){
									 	 $sql_datos_proveedor = db_query("Select * From Proveedor Where IDProveedor = '" . $r->IDProveedor . "'");
										 $datos_proveedor = db_fetch_array($sql_datos_proveedor);

									}
									?>
			          </th>
			        </tr>
				    <tr>
				      <td width="16%"><strong>Nombre</strong></td>
				      <td width="31%"><span id="NombreProveedor"><?php echo $datos_proveedor[Nombre] ?></span></td>
				      <td width="19%"><strong>Direccion</strong></td>
				      <td width="34%"><span id="DireccionProveedor"><?php echo $datos_proveedor[Direccion] ?></span></td>
			        </tr>
				    <tr>
				      <td><strong>Telefono</strong></td>
				      <td><span id="TelefonoProveedor"><?php echo $datos_proveedor[Telefono] ?></span></td>
				      <td><strong>Ciudad</strong></td>
				      <td><span id="CiudadProveedor"><?php echo $datos_proveedor[Ciudad] ?></span></td>
			        </tr>
				    <tr>
				      <td><strong>Email</strong></td>
				      <td colspan="3"><span id="EmailProveedor"><?php echo $datos_proveedor[Email] ?></span></td>
			        </tr>
			      </tbody>
				  </table></td>
			</tr>
            <tr class=row2>
			              <td> Orden de Compra</td>
			              <td abbr="">
                          <?php echo $r->NumeroOrdenCompra ?>
                          </td>


          </tr>
            <tr class=row2>
              <td colspan="2"></td>
            </tr>
			<?php
			if($r->Publicar == 'S')
			{
			?>
			<?php
			}
			?>
		  </table>




		</td>
	</tr>
</table>


  <div id="detalle-cont">
        <table id="tabla_detalle_pedido" width="70%" border="0" align="center" style="border:1px solid #E5E5E5" cellpadding="2" cellspacing="1">
                <tbody>
                  <tr bgcolor="#9399E4" class="maintitle"  align="center" >
                    <td style="color:#FFFFFF; !important;">Ref <br>
                    Provee</td>
                    <td style="color:#FFFFFF; !important;">Ref <br>
                    Caprino</td>
                    <td style="color:#FFFFFF; !important;">COL</td>
                    <td style="color:#FFFFFF; !important;">CUERO Y COLOR</td>
                    <td style="color:#FFFFFF; !important;">SUELA</td>
                    <td style="color:#FFFFFF; !important;">TACON<br>ALTURA</td>
                    <td style="color:#FFFFFF; !important;">HORMA</td>
                    <td style="color:#FFFFFF; !important;">PRODUCTO</td>
                    <td style="color:#FFFFFF; !important;">PRECIO</td>
                    <td style="color:#FFFFFF; !important;">OBSERVACIONES</td>
                    <td style="color:#FFFFFF; !important;">Curva</td>
                  </tr>
                  <?php

				 $detalle_inicial=(int)count($array_detalle_orden);

				  for($i=1;$i<=$detalle_inicial;$i++):  ?>
                      <tr>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["ReferenciaProveedor"]; ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["ReferenciaCaprino"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["CodigoColor"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["CueroColor"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["Suela"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["Tacon"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["Horma"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["Producto"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["Precio"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["Observacion"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo get_field("CurvaTercero","Nombre","IDCurvaTercero",$array_detalle_orden[$i]["IDCurvaTercero"]); ?></td>
                      </tr>
                  <?php endfor; ?>
          </tbody>
      </table>
              </div>

              <table align="center" width="70%">
			<tr>
			  <td colspan=2 align=center class=maintitle bgcolor=#9daac6>DETALLE CANTIDADES SEGUN CURVA</td>
			  </tr>

			<tr>
			  <td colspan=2 align=center class=row2>



              <?php
                          if (count($array_punto_venta)>0):
						    $flag_total_producto = 0;
						  	$id_ciudad_ant = "";
							foreach($array_punto_venta as $id_punto_venta => $datos_punto_venta):
							// verifico si tiene por lo menos un producto pedido para mostrarlo y el estado es enviado a proveedor
							if ($r->IDEstadoPedidoTercero<>1):
								$sql_total_pedido=db_query("Select SUM(Cantidad) as Total_Producto From DetallePedidoTerceroReferencia
												   Where IDPedidoTercero= '".$r->IDPedidoTercero."' and
													     IDPuntoVenta = '".$datos_punto_venta[IDPuntoVenta]."'");
								$row_total_producto = db_fetch_array($sql_total_pedido);
								if((int)$row_total_producto["Total_Producto"]<=0):
									$flag_total_producto = 1;
								else:
									$flag_total_producto = 0;
								endif;
							else:
								$flag_total_producto = 0;
							endif;
						   ?>


                          <?php
                          // Si tiene por lo menos un producto
						  if ($flag_total_producto ==0):
						  ?>
                          <table width="100%" border="0" cellspacing="1" cellpadding="0">
						    <tbody>

                            <?php if ($datos_punto_venta[IDCiudad]!=$id_ciudad_ant){
									$id_ciudad_ant= $datos_punto_venta[IDCiudad];
									if ($datos_punto_venta[IDCiudad]=="1")
										$color="#B1CFE6";
									else
										$color="#BDD9BF";


							?>
                            <tr>
								  <td bgcolor="<?php echo $color; ?>"  colspan=3 align=center style="font-size:14px; color:#EB373A"><?php echo get_field("Ciudad","Descripcion","IDCiudad",$datos_punto_venta[IDCiudad]); ?></td>
						    </tr>
                            <?php } ?>

						      <tr>
						        <td class="maintitle" bgcolor="#9daac6" colspan="3" ><?php echo $datos_punto_venta[Nombre]; ?></td>
					          </tr>
						      <tr>
						        <td class="titlemedium">Talla:</td>
                                <?php
								if (count($array_talla)>0):
									unset($suma_item_pedir_talla);
									$total_tienda="0";
									foreach($array_talla as $id_talla => $datos_talla):
									?>
							        <td class="titlemedium" nowrap align="center"><?php echo $datos_talla[Nombre]; ?></td>

                                    <?php endforeach;
								endif;
								?>
                                <td class="titlemedium" nowrap align="center">TOTAL</td>
					          </tr>


                               <?php for($i=1;$i<=$detalle_inicial;$i++):
								unset($array_datos_curva);
								unset($minimo_item);
								unset($maximo_item);
								unset($existencias_item);
								$suma_item_pedir=0;
								$suma_existencia_ref=0;

								if (!empty($array_detalle_orden[$i]["IDCurvaTercero"])){
									//Consulto el detalle de minimos y maximos
									$sql_datos_curva= "Select* From DetalleCurvaTercero Where IDCurvaTercero = '".$array_detalle_orden[$i]["IDCurvaTercero"]."'";
									$result_datos_curva = db_query($sql_datos_curva);
									while ($row_datos_curva = db_fetch_array($result_datos_curva)){
										$array_datos_curva[ $row_datos_curva["IDPuntoVenta"] ] [ $row_datos_curva["IDTalla"] ] [ $row_datos_curva["Tipo"] ]  = $row_datos_curva["Valor"];
									}
								}


							   ?>

						      <tr>
						        <td class="rowform">Maximo</td>
	                            <?php
								$total_curva=0;
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>
							        <td class=row1 align=center>
									<?php
										$total_curva +=$array_datos_curva[$datos_punto_venta[IDPuntoVenta]][$datos_talla[IDTalla]] ["Maximo"];
										echo $maximo_item[$id_talla]=$array_datos_curva[$datos_punto_venta[IDPuntoVenta]][$datos_talla[IDTalla]] ["Maximo"];

										?></td>

                                    <?php endforeach;
								endif;
								?>
                                <td bgcolor="#F1CFCF" align=center><?php echo $total_curva; ?></td>
					          </tr>
						      <tr>
						        <td class="rowform">Existencias</td>
						        <?php
								unset($array_pendientes);

								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
										unset($array_tallas_rel);
										$id_tallas_rel="";
										$total_existencias="";
									?>
							        <td class=row1 align=center>
										<?php
											if(!empty($maximo_item[$id_talla]) ){
												//CONSULTO REFERENCIA
												$id_referencia="";
												$referencia_completa=$array_detalle_orden[$i]["ReferenciaCaprino"].$array_detalle_orden[$i]["CodigoColor"];
												$id_referencia = get_field("Referencia","IDReferencia","Nombre",$referencia_completa);


												//Consulto las otra tallas posibles ya que una talla esta creada mas de una vez
												$nombre_talla = get_field("Talla","Descripcion","IDTalla",$id_talla);
												$sql_tallas_rel = db_query("Select * From Talla Where Descripcion = '".$nombre_talla."'");
												while($row_talla = db_fetch_array($sql_tallas_rel)):
													$array_tallas_rel []=$row_talla[IDTalla];
												endwhile;

												if (count($array_tallas_rel)>0):
													$id_tallas_rel = implode(",",$array_tallas_rel);
												endif;


												if(!empty($id_referencia)){
													//CONSULTA EXISTENCIAS
													$sql =  "SELECT * FROM CodificacionEspecifica CE, PuntoVentaReferencia PR
															 WHERE PR.IDPuntoVenta = '".$datos_punto_venta["IDPuntoVenta"]."'
															  AND PR.IDReferencia = '".$id_referencia."'
															  AND IDTalla in (".$id_tallas_rel.")
															  AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia";
													$query_codificacion = db_query($sql);
													$rows = db_fetch_array($query_codificacion);
													$existencias_item[$id_talla] = $rows["Existencias"];
													$total_existencias = $rows["Existencias"];
													$suma_existencia_ref+=$rows["Existencias"];
												}
											}
											else{
												$existencias_item[$id_talla] = "0";
											}


											//Verifico tambien lo pendiente por entregar de otros pedidos para sumarlos como si fuera existencia

											if(!empty($id_tallas_rel)):
													$sql_pedido_por_entregar= "Select DPTR.*
																			   From DetallePedidoTercero DPT, DetallePedidoTerceroReferencia DPTR
																			   Where DPT.IDDetallePedidoTercero =  DPTR.IDDetallePedidoTercero and
																					 ReferenciaCaprino = '".$array_detalle_orden[$i]["ReferenciaCaprino"]."' and
																					 CodigoColor = '".$array_detalle_orden[$i]["CodigoColor"]."'	and
																					 DPT.IDPedidoTercero <> '".$r->IDPedidoTercero."' and
																					 DPTR.IDPuntoVenta = '".$datos_punto_venta["IDPuntoVenta"]."' and
																					 DPTR.IDTalla in (".$id_tallas_rel.")  and
																					 DPTR.Estado = 'Enviado'";

													$result_pedido_por_entregar = db_query($sql_pedido_por_entregar);
													$row_pedido_por_entregar = db_fetch_array($result_pedido_por_entregar);
													if((int)$row_pedido_por_entregar["Cantidad"]>0):
														$existencias_item[$id_talla] += $row_pedido_por_entregar["Cantidad"];
														//$suma_existencia_ref+=$row_pedido_por_entregar["Cantidad"];
														$array_pendientes[$id_talla]=$row_pedido_por_entregar["Cantidad"];
														//echo "(Pendiente Entregar) ";
													endif;
												endif;



											if((int)$maximo_item[$id_talla]>0 && (int)$suma_existencia_ref==0):
												echo "0";
											elseif((int)$total_existencias>0):
												echo $total_existencias;
											endif;

										?>
                                     </td>

                                    <?php endforeach;
								endif;

								?>
                                <td bgcolor="#F1CFCF" align=center>
                                <?php echo $suma_existencia_ref; ?>
                                </td>
					          </tr>


                               <tr>
						        <td class="rowform">
                                	Pendientes
                                </td>
	                            <?php
								$total_pendiente=0;
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>
							        <td class=row1 align=center>
                                    <?php
									$total_pendiente +=  (int)$array_pendientes[$id_talla];
									echo $array_pendientes[$id_talla];
									?>
                                    </td>

                                    <?php endforeach;
								endif;
								?>
                                <td bgcolor="#F1CFCF" align=center style="font-weight:bold">
								<?php
									echo number_format($total_pendiente,0,",",".");
								?>
                                </td>
					          </tr>

                               <tr>
						        <td class="rowform">
                                	<?php echo $array_detalle_orden[$i]["ReferenciaCaprino"].$array_detalle_orden[$i]["CodigoColor"];  ?>
                                </td>
	                            <?php
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									// Verifico si ya existe algo guardado para no reemplazar
									$sql_detalle_pedido_ref = "Select Cantidad
															  From DetallePedidoTerceroReferencia
															  Where IDPedidoTercero= '".$r->IDPedidoTercero."' and
															  IDDetallePedidoTercero = '".$array_detalle_orden[$i]["IDDetallePedidoTercero"]."' and
															  IDPuntoVenta = '".$datos_punto_venta[IDPuntoVenta]."' and
															  IDTalla = '".$datos_talla[IDTalla]."'";


									$result_detalle_pedido_ref = db_query($sql_detalle_pedido_ref);
									$row_detalle_pedido_ref=db_fetch_array($result_detalle_pedido_ref);




									if (is_numeric($row_detalle_pedido_ref["Cantidad"])){
										$valor_pedir_item = (int)$row_detalle_pedido_ref["Cantidad"];
									}
									else{
										$valor_pedir_item = (int)$maximo_item[$id_talla] - (int)$existencias_item[$id_talla];
										if ($valor_pedir_item<0 && is_numeric($valor_pedir_item))
											$valor_pedir_item="";

									}


									if((int)db_num_rows($result_detalle_pedido_ref)<=0 && (int)$valor_pedir_item==0)
										$valor_pedir_item =0;

									 	$suma_item_pedir+=$valor_pedir_item;
										$suma_item_pedir_talla[$datos_talla[IDTalla]] +=  $valor_pedir_item;

										$super_total_talla[$datos_talla[IDTalla]][$array_detalle_orden[$i]["IDDetallePedidoTercero"]]+=$valor_pedir_item;

									?>
							        <td class=row1 align=center>
                                    <?php
									echo $entra;
									//echo "<br>" . $valor_pedir_item . "<br>";

									if(is_numeric($maximo_item[$id_talla]) ){ ?>
                                    	<?php if($r->IDEstadoPedidoTercero == 1): ?>
	                                    	<input type="text" name="Pedido[<?php echo $datos_talla[IDTalla]; ?>][<?php echo $datos_punto_venta[IDPuntoVenta] ?>][<?php echo $array_detalle_orden[$i]["IDDetallePedidoTercero"] ?>]"  size="5" value="<?php if (is_numeric($valor_pedir_item)) echo (int)$valor_pedir_item; ?>" style="text-align:center">
                                        <?php
										else:
											if (is_numeric($valor_pedir_item)) echo (int)$valor_pedir_item;
										endif; ?>
                                    <?php }


									//if($datos_punto_venta[IDPuntoVenta]==22)
										//echo $sql_detalle_pedido_ref;

									?>


                                    </td>

                                    <?php endforeach;
								endif;
								?>
                                <td bgcolor="#F1CFCF" align=center style="font-weight:bold">
								<?php
									echo number_format($suma_item_pedir,0,",",".");
								?>
                                </td>
					          </tr>


                              <tr>
                              <td style="height:5px" bgcolor="#FFFFFF" >

                                </td>
                               <?php
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>
							        <td bgcolor="#FFFFFF"></td>

                                    <?php endforeach;
								endif;
								?>
                                <td bgcolor="#FFFFFF"></td>
					          </tr>
                              <?php endfor; ?>

                              <tr>
                              <td bgcolor="#F1CFCF" style="font-weight:bold" >TOTALES</td>
                               <?php
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>
							        <td bgcolor="#F1CFCF" align="center" style=" font-weight:bold">
									<?php
										$total_tienda+=$suma_item_pedir_talla[$id_talla];
										if ($suma_item_pedir_talla[$id_talla]!="0"){
											echo number_format($suma_item_pedir_talla[$id_talla],0,",",".");
										}

										?>


                                        </td>

                                    <?php endforeach;
								endif;
								?>
                                <td bgcolor="#F1CFCF" align="center" style="font-weight:bold">
								<?php
								$total_ciudad[$datos_punto_venta[IDCiudad]] += $total_tienda;
								echo number_format($total_tienda,0,",","."); ?>
                                </td>
					          </tr>


					        </tbody>
					      </table>
                          <?php endif; // fin validacion si tiene por lo menos un producto pedido ?>
                          <br />
                         <?php

                          endforeach;
						 endif;
						  ?>




              </td>
			  </tr>
			<tr>
			  <td colspan=2 align=center class=row2>

              <br><br>


              <table width="100%" border="0" cellspacing="1" cellpadding="0">
			    <tbody>



			      <tr>
			        <td align="center" colspan="3" bgcolor="#8F8FF5" style="font-weight:bold; font-style:14px; color:#FFFFFF">RESUMEN</td>
		          </tr>
			      <tr>
			        <td bgcolor="#FFFFFF">TOTAL BOGOTA</td>
			        <td bgcolor="#FFFFFF" align="center" style="font-weight:bold"><?php echo  number_format($total_ciudad[1],0,",",".") ?></td>
			        <td bgcolor="#FFFFFF" align="center">&nbsp;</td>
		          </tr>
			      <tr>
			        <td bgcolor="#FFFFFF">TOTAL MEDELLIN</td>
			        <td bgcolor="#FFFFFF" align="center" style="font-weight:bold"><?php echo  number_format($total_ciudad[2],0,",",".") ?></td>
			        <td bgcolor="#FFFFFF" align="center">&nbsp;</td>
		          </tr>
			      <tr>
			        <td bgcolor="#FEFFBB" style="font-weight:bold">TALLA:</td>
			        <?php
								if (count($array_talla)>0):
									unset($suma_item_pedir_talla);
									$total_tienda="0";
									foreach($array_talla as $id_talla => $datos_talla):
									$suma_talla_resumen="0";
									?>
			        <td bgcolor="#FEFFBB" align="center" style="font-weight:bold"><?php echo $datos_talla[Nombre]; ?></td>
			        <?php endforeach;
								endif;
								?>
			        <td bgcolor="#FEFFBB" align="center" style="font-weight:bold">TOTAL</td>
		          </tr>
			      <?php for($i=1;$i<=$detalle_inicial;$i++):
								$suma_item_pedir=0;
								$suma_talla_resumen="0";

								if (!empty($array_detalle_orden[$i]["IDCurvaTercero"])){
									//Consulto el detalle de minimos y maximos
									$sql_datos_curva= "Select* From DetalleCurvaTercero Where IDCurvaTercero = '".$array_detalle_orden[$i]["IDCurvaTercero"]."'";
									$result_datos_curva = db_query($sql_datos_curva);
									while ($row_datos_curva = db_fetch_array($result_datos_curva)){
										$array_datos_curva[ $row_datos_curva["IDPuntoVenta"] ] [ $row_datos_curva["IDTalla"] ] [ $row_datos_curva["Tipo"] ]  = $row_datos_curva["Valor"];
									}
								}


							   ?>
			      <tr>
			        <td class="rowform"><?php echo $array_detalle_orden[$i]["ReferenciaCaprino"] . $array_detalle_orden[$i]["CodigoColor"];  ?></td>
			        <?php
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):

									 	$suma_item_pedir+=$valor_pedir_item;
										$suma_item_pedir_talla[$datos_talla[IDTalla]] +=  $valor_pedir_item;

									?>
			        <td class=row1 align=center>
                    	<?php echo $super_total_talla[$id_talla][$array_detalle_orden[$i]["IDDetallePedidoTercero"]];
							$suma_talla_resumen+=$super_total_talla[$id_talla][$array_detalle_orden[$i]["IDDetallePedidoTercero"]];
							$suma_talla[$datos_talla[IDTalla]]+=$super_total_talla[$id_talla][$array_detalle_orden[$i]["IDDetallePedidoTercero"]];

						 ?>
                    </td>
			        <?php endforeach;
								endif;
								?>
			        <td bgcolor="#E6EEDA" align=center style="font-weight:bold"><?php

									echo number_format($suma_talla_resumen,0,",",".");
								?></td>
		          </tr>
			      <tr>
			        <td style="height:2px" bgcolor="#FFFFFF" ></td>
			        <?php
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>
			        <td bgcolor="#FFFFFF"></td>
			        <?php endforeach;
								endif;
								?>
			        <td bgcolor="#FFFFFF"></td>
		          </tr>
			      <?php endfor; ?>
			      <tr>
			        <td bgcolor="#E6EEDA" style="font-weight:bold" >TOTALES</td>
			        <?php
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>
			        <td bgcolor="#E6EEDA" align="center" style=" font-weight:bold"><?php
										$total_completo+=$suma_talla[$id_talla];
										echo number_format($suma_talla[$id_talla],0,",",".");


										 ?></td>
			        <?php endforeach;
								endif;
								?>
			        <td bgcolor="#E6EEDA" align="center" style="font-weight:bold"><?php echo number_format($total_completo,0,",","."); ?></td>
		          </tr>
		        </tbody>
		      </table></td>
			  </tr>
			<tr>
			<td colspan=2 align=center class=row2>


            <input type=hidden name=IDPedidoTercero id=IDPedidoTercero value="<?=$r->IDPedidoTercero ?>">
			  <input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
              <input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
              <input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
              <input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
              <input type=hidden name=ID value="<?php echo $r->$Key ?>">
              <input type=hidden name=action value="inserta_detalle_referencia">
              <input type=hidden name="IDEstadoPedidoTercero" id="IDEstadoPedidoTercero" value=<?php if($newmode=="insert") echo "1"; else echo $r->IDEstadoPedidoTercero ?>>

              <?php


			  if($r->IDEstadoPedidoTercero == 1):  ?>
              <input type=submit name=submit value="<?php if($newmode=="insert") echo "Guardar y Continuar"; else echo $submit_caption ?>" class=submit>
              <?php endif ?>

               <?php
			   // Solo muestro el boton de generar si ya se guardo el detalle
									$sql_detalle_pedido_ref = "Select count(*) Total
															  From DetallePedidoTerceroReferencia
															  Where IDPedidoTercero= '".$r->IDPedidoTercero."'";
									$result_detalle_pedido_ref = db_query($sql_detalle_pedido_ref);
									$row_detalle_pedido_ref=db_fetch_array($result_detalle_pedido_ref);


			   if($row_detalle_pedido_ref["Total"]>0){
					   if($r->IDEstadoPedidoTercero == 1):
								$mensaje_boton ="GENERAR PEDIDO";
							else:
								$mensaje_boton ="REENVIAR PEDIDO A PROVEEDOR";
								$tipo_envio="reenvio";
							endif;
					   ?>
					  <input type="button" name="genera_pedido" id="genera_pedido" alt="<?=$r->IDPedidoTercero ?>" rel="<?php echo $tipo_envio; ?>"  value="<?php echo $mensaje_boton; ?>" class=submit>
             <?php }
			 else{ ?>
             	<br>Para poder generar el pedido primero debe guardar la sugerencia de cantidades

				<?php }

			 ?>

              </td>
              </td>

			</tr>
			</table>

</form>
</div>

<?php
}


?>






<?php
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar, $condicion_fecha,$condicion_ptovta;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table WHERE 1 ORDER BY $Key Desc";

		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=50;
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


	$sql_punto_venta = "Select IDPuntoVenta,Nombre,IDCiudad From PuntoVenta Where 1  Order By IDCiudad, Nombre";
	$result_punto_venta = db_query($sql_punto_venta);
	while ($row_punto_venta = db_fetch_array($result_punto_venta)){
		$array_punto_venta[ $row_punto_venta["IDPuntoVenta"] ] = $row_punto_venta;
	}

							?>
		<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
			<tr>
				<td class=nav width=76;?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
				<a href="./?mod=<?php echo $MOD;?>">Administrar <?php echo $TitleMod;?></a> </td>
				<td><a href="./?mod=<?php echo $MOD;?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
			</tr>
		</table>
		<?php
				if($rows > 0){
		?>
		<br>
		<table  cellpadding=0 cellspacing=0 align=center class=bordertable>
			<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
		</tr>
			<?php filtrar();?>
			<tr>
			<td class=titlemedium  bgcolor=#9daac6><?php echo $info;;?></td>
		</tr>
			<tr>
				<td class=texto bgcolor=#DBEAF5 colspan=12 nowrap>
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
						<td class=rowform nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Numero Orden</a><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>&nbsp;
					        <?php if($_GET['order_by']=="NumeroOrdenCompra")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Proveedor&nbsp;
                            <?php if($_GET['order_by']=="IDProveedor")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Apellidos&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Fecha Pedido&nbsp;
						    <?php if($_GET['order_by']=="FechaPedido")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDCargo&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Fecha Entrega&nbsp;
						    <?php if($_GET['order_by']=="FechaEntrega")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Fecha <br>
						  Ultima Fac</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Estado</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Referencias</td>
                        <td class=rowform nowrap bgcolor=#DBEAF5>Facturas</td>
						<td class=rowform nowrap bgcolor=#DBEAF5><span class="titlemedium">Cantidad <br>Pedidos</span></td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Cantidad<br> Recibidos</td>
						<td class=rowform nowrap bgcolor=#DBEAF5><span class="titlemedium">Faltantes</span></td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

						<?php while($r = db_fetch_object($result)){
						?>

						<tr>
						<td align=center valign=middle nowrap width=69 class=row2>
								&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
							</td>
						<td nowrap class=row1><?php echo $r->NumeroOrdenCompra ?></td>
						<td nowrap class=row1><?php echo get_field("Proveedor","Nombre","IDProveedor",$r->IDProveedor) ?></td>
						<td nowrap class=row1><?php echo $r->FechaPedido ?></td>
						<td nowrap class=row1><?php echo $r->FechaEntrega ?></td>
						<td nowrap class=row1><?php
                        $sql_fecha="Select * From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$r->$Key."' Order by FechaRecibido desc Limit 1";
						$result_fecha = db_query($sql_fecha);
						$row_fecha=db_fetch_array($result_fecha);
						echo substr($row_fecha["FechaRecibido"],0,10);
						?></td>
						<td nowrap class=row1>
						<?php
						echo estado_tercero($r->IDPedidoTercero);
						//echo get_field("EstadoPedidoTercero","Descripcion","IDEstadoPedidoTercero",$r->IDEstadoPedidoTercero)
						?>
                        </td>
						<td nowrap class=row1>

                        <table border="1" cellpadding=0 cellspacing=0 align=center class=bordertable>
                        <tr>
                        	<td>Ref</td>
                            <td>Cant</td>
                        </tr>
                        <?php
						//consulta ref
						$sql_ref="Select * From DetallePedidoTercero Where IDPedidoTercero = '".$r->$Key."' ";
						$result_ref = db_query($sql_ref);
						while($row_ref=db_fetch_array($result_ref)): ?>
                            <tr>
                                <td><?php echo $row_ref["ReferenciaCaprino"].$row_ref["CodigoColor"]; ?></td>
                                <td>
                                <?php
                                $sql_suma="Select sum(CantidadRecibido) as TotalPedido From DetallePedidoTerceroReferencia Where IDDetallePedidoTercero = '".$row_ref["IDDetallePedidoTercero"]."' " . $condicion_fecha . " " . $condicion_ptovta;
	                            $result_suma = db_query($sql_suma);
                                $row_suma=db_fetch_array($result_suma);
                                echo $row_suma["TotalPedido"];
								?>
                                </td>
                            </tr>
						<?php endwhile;	?>
                        </table>
                        </td>
                        <td nowrap class=row1>

                        <table border="1" cellpadding=0 cellspacing=0 align=center class=bordertable>
                        <tr>
                        	<td><b>Almacen</b></td>
                        	<td><b>Factura</b></td>
                            <td><b>Cant</b></td>
                        </tr>
                        <?php
						//consulta ref
						echo $suma_total_entregado;
						$sql_ref="Select sum(CantidadRecibido) as TotalItemFactura, NumeroFactura,IDPuntoVenta From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$r->$Key."' ".$condicion_fecha . " " . $condicion_ptovta ." group by IDPuntoVenta,NumeroFactura ";
						$result_ref = db_query($sql_ref);
						$suma_total_entregado=0;
						while($row_ref=db_fetch_array($result_ref)): ?>
                            <tr>
                                <td><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$row_ref[IDPuntoVenta]); ?></td>
                                <td><?php echo $row_ref["NumeroFactura"]; ?></td>
                                <td>
									<?php
										echo $row_ref["TotalItemFactura"];
										$suma_total_entregado += $row_ref["TotalItemFactura"];
										?>
                                 </td>


                            </tr>
						<?php endwhile;	?>
                        </table>
                        </td>
						<td nowrap class=row1>
                         <?php

						 	unset($array_resumen);
							$total_pedido=0;
							 $total_recibido=0;
							 $diferencia=0;
						   if (count($array_punto_venta)>0):
						     $flag_total_producto = 0;
						  	 $id_ciudad_ant = "";
							 foreach($array_punto_venta as $id_punto_venta => $datos_punto_venta):
								 $array_resumen = resumen_tercero_pto_vta($r->$Key, $datos_punto_venta[IDPuntoVenta]);
								 $array_resumen[ToTalPedido];
								 $total_pedido += $array_resumen[ToTalPedido];
								 $array_resumen[ToTalRecibido];
								 $total_recibido += $array_resumen[ToTalRecibido];
								// $diferencia_pedido = ((int)$array_resumen[ToTalPedido] - (int)$array_resumen[ToTalRecibido]);
							 endforeach;
						   endif;


						 ?>
                         <?php
						 $gran_total_pedido +=$total_pedido;
						 echo $total_pedido; ?>

                        </td>
						<td nowrap class=row1>
                        <?php
						//$gran_total_recibido += $total_recibido;
						$gran_total_recibido += $suma_total_entregado;
						echo $suma_total_entregado
						//echo 	$total_recibido;
						?>
                        </td>
						<td nowrap class=row1>
                        <?php


						echo $diferencia_total = $total_pedido - $total_recibido;
						$gran_total_faltante += $diferencia_total;

						?>
                        </td>
						<td align=center valign=middle nowrap width=69 class=row2>
								&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>
							</td>
					</tr>

						<?php } // END for
						?>
                        <tr>
						  <td align="right" valign=middle nowrap class=row2 colspan="7">Totales</td>
						  <td nowrap class=row1>&nbsp;</td>
                         <td nowrap class=row1></td>
						  <td nowrap class=row1><?php echo $gran_total_pedido; ?></td>
						  <td nowrap class=row1><?php echo $gran_total_recibido; ?></td>
                          <td nowrap class=row1><?php echo $gran_total_faltante; ?></td>
						  <td align=center valign=middle nowrap class=row2>&nbsp;</td>
					  </tr>


						<tr>
							<td class=texto bgcolor=#DBEAF5 colspan=12 nowrap>
								<?php
									print $pages;
									?>
							</td>
						</tr>
					</table>
				</td>
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

							<table>
									<tr>
											<td>Numero Orden</td>
											<td><input type="text" name="NumeroOrdenCompra" id="NumeroOrdenCompra"></td>
											<td>Numero de Factura</td>
											<td><input type="text" name="NumeroFactura" id="NumeroFactura"></td>
											<td>Proveedor :</td>
											<td><select name="IDProveedor" id="IDProveedor">
			                	 <option value="">[Seleccione]</option>
			                    <?php
			                    $sql_provee=db_query("Select * from Proveedor Where 1 Order by Nombre");
			                    while($row_provee=db_fetch_array($sql_provee)){
			                        ?>
			                        <option value="<?php echo $row_provee["IDProveedor"]; ?>"><?php echo $row_provee["Nombre"]; ?></option>
			                    <?php
			                    }
			                    ?>
			          		</select></td>
									</tr>
									<tr>
											<td>Estado:</td>
											<td><select name="IDEstadoPedidoTercero" id="IDEstadoPedidoTercero">
			                    <option value="">[Seleccione]</option>
			                    <?php
			                    $sql_estados=db_query("Select * from EstadoPedidoTercero Where 1 Order by Descripcion");
			                    while($row_estado=db_fetch_array($sql_estados)){
			                        ?>
			                        <option value="<?php echo $row_estado["IDEstadoPedidoTercero"]; ?>"><?php echo $row_estado["Descripcion"]; ?></option>
			                    <?php
			                    }
			                    ?>
			          		</select></td>
											<td>Referencia</td>
											<td><input type="text" name="ReferenciaCaprino" id="ReferenciaCaprino"></td>
											<td>Punto Venta:</td>
											<td><select name="IDPuntoVenta" id="IDPuntoVenta"  >
			                  <option value="">Seleccione Un Punto de Venta</option>
			                  <?php
											$qry_punto = db_query("SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre ");
											while($punto = db_fetch_object($qry_punto)){
												 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
											}
										?>
			                </select></td>
									</tr>
									<tr>
											<td>Tipolog&iacute;a</td>
											<td><input type="text" name="Tipologia" id="Tipologia"></td>
											<td>Desde</td>
											<td><input  type="text" name="FechaDesde" class="input" value="<?=$FechaDesde?>" size="10">

											<script language="JavaScript1.2">
												<!--
													if (!document.layers)
														document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
												//-->
											</script></td>
											<td>Hasta</td>
											<td><input  type="text" name="FechaHasta" class="input" value="<?=$FechaHasta?>" size="10">

											<script language="JavaScript1.2">
												<!--
													if (!document.layers)
														document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
												//-->
											</script></td>
									</tr>
							</table>
              

                  <br>
                <input type="hidden" name="mod" value="<?=$MOD?>">
                <input type="hidden" name="action" value="list">
                <input type="submit" name="submit" value="Buscar" class="submit">
				</td>
			</tr>
	</form>
<?php
	}//End function filtrar
?>
