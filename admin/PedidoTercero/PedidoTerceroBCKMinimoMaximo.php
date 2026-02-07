
<body> <?php

require($libdir."filelib.php");

$TitleMod ="Pedido Tercero";

$Table = "PedidoTercero";
$TableJoin = "PedidoTercero";
$Key = "IDPedidoTercero";
$MOD = "PedidoTercero";
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
					$frm["Precio"]=$frm[$Precio];
					$frm["Observacion"]=$frm[$Observacion];
					$frm["IDCurvaTercero"]=$frm[$IDCurvaTercero];
					
					if (!empty($frm[$ReferenciaCaprino])){
							$sql_inserta_detalle = "Insert into DetallePedidoTercero (IDPedidoTercero, ReferenciaCaprino, ReferenciaProveedor, CodigoColor, CueroColor, Suela, Tacon, Altura, Horma, Precio, Observacion, IDCurvaTercero, UsuarioTrEd, FechaTrEd) 
													Values('".$id."','".$frm["ReferenciaCaprino"]."','".$frm["ReferenciaProveedor"]."','".$frm["CodigoColor"]."','".$frm["CueroColor"]."','".$frm["Suela"]."','".$frm["Tacon"]."','".$frm["Altura"]."','".$frm["Horma"]."','".$frm["Precio"]."','".$frm["Observacion"]."','".$frm["IDCurvaTercero"]."','".$ID_Usuario."',NOW())";
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
													 Precio = '".$frm["Precio"]."', 
													 Observacion = '".$frm["Observacion"]."', 
													 IDCurvaTercero = '".$frm["IDCurvaTercero"]."', 
													 UsuarioTrEd = '".$ID_Usuario."', 
													 FechaTrEd = NOW()
													 Where IDDetallePedidoTercero = '".$frm["IDDetallePedidoTercero"]."'";
							db_query($sql_actualiza_detalle);
						
								
						}
						else{
							$sql_inserta_detalle = "Insert into DetallePedidoTercero (IDPedidoTercero, ReferenciaCaprino, ReferenciaProveedor, CodigoColor, CueroColor, Suela, Tacon, Altura, Horma, Precio, Observacion, IDCurvaTercero, UsuarioTrEd, FechaTrEd) 
													Values('".$frm["IDPedidoTercero"]."','".$frm["ReferenciaCaprino"]."','".$frm["ReferenciaProveedor"]."','".$frm["CodigoColor"]."','".$frm["CueroColor"]."','".$frm["Suela"]."','".$frm["Tacon"]."','".$frm["Altura"]."','".$frm["Horma"]."','".$frm["Precio"]."','".$frm["Observacion"]."','".$frm["IDCurvaTercero"]."','".$ID_Usuario."',NOW())";
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
			
			//ACTUALIZO LAS CANTIDADES DE PEDIDO POR PUNTO, TALLA y REFERENCIA
				$sql_borrar_anterior=db_query("Delete From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$frm["IDPedidoTercero"]."'");
				foreach($frm as $id_dato => $datos1){
						foreach($datos1 as $id_talla => $dato_talla)	{
							foreach($dato_talla as $id_punto => $dato_punto)	{
								foreach($dato_punto as $id_referencia => $cantidad)	{
									//echo "<br>ES Talla " . 	$id_talla . " Punto " . $id_punto . "Referencia " . $id_referencia . " Valor = " . $cantidad;
									if ((int)$cantidad>0){
										$sql_detalle_pedido = "Insert Into DetallePedidoTerceroReferencia (IDPedidoTercero, IDDetallePedidoTercero, IDPuntoVenta, IDTalla, Cantidad, Estado, UsuarioTrCr, FechaTrCr)
														Values('".$frm["IDPedidoTercero"]."','".$id_referencia."','".$id_punto."','".$id_talla."','".$cantidad."','Enviado','".$ID_Usuario."',NOW())";
										db_query($sql_detalle_pedido);
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
			
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			
			case "delfoto" :
				$sql_actualiza=db_query("Update ".$Table." Set " . $_GET[campo] . "='' Where ".$Key." = '".$_GET[id]."'");
				print_form($_GET[id],"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			
			case "delete" :
				$HTTP_GET_VARS[action]="";
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

<form name="frm" id="frmPedidoTercero" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){ ?> onsubmit="return EvaluaReg(this,Check)" <?php } ?>>
	

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
				<td width="4"></td>
				<td>&nbsp;</td>
				
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
						  <td><span style="color:#FF7477; font-size:12px; font-weight:bold">ESTADO</span></td>
						  <td><span style="color:#FF7477; font-size:12px; font-weight:bold"><?php echo get_field("EstadoPedidoTercero","Descripcion","IDEstadoPedidoTercero",$r->IDEstadoPedidoTercero); ?></span></td>
		  </tr>
						<tr class=row2>
				<td>Proveedor</td>
				<td><?php echo formpopup("Proveedor","Nombre","Nombre","IDProveedor",$r->IDProveedor,"input proveedor_pedido\" id=\"Proveedor"); ?></td>
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
                          <?php if($newmode=="insert"): ?>
                          	Automatico
                          <?php else: ?>  
                          <input type=text size=25 class=input   name=NumeroOrdenCompra id=NumeroOrdenCompra value="<?=$r->NumeroOrdenCompra ?>" readonly></td>
                          	
						  <?php endif; ?>
                          
          </tr>
          
            <tr class=row2>
              <td>Fecha Pedido</td>
              <td><span class="col2">
                <input type=text readonly size=10 class=input name=FechaPedido id="FechaPedido" title="Fecha Pedido" value="<?=$r->FechaPedido ?>">
                <script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaPedido,\"yyyy-mm-dd\")' width=16 height=16 border=0>")	
					//-->
				</script>
              </span></td>
            </tr>
            <tr class=row2>
              <td>Fecha Entrega</td>
              <td><input type=text size=10 readonly class=input name=FechaEntrega id="FechaEntrega" title="Fecha Entrega"  value="<?=$r->FechaEntrega ?>">
                <script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaEntrega,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//-->
				</script></td>
            </tr>
            <tr class=row2>
              <td>Condiciones Pago</td>
              <td>16 a 30 Dias
                <input type=text size=10 class=input   name="CondicionPago30" id="CondicionPago30" value="<?=$r->CondicionPago30 ?>">
                 % 30 a 45 Dias
                 <input type=text size=10 class=input   name="CondicionPago45" id="CondicionPago45" value="<?=$r->CondicionPago45 ?>">
                 % 
                 45 a 60 Dias
                 <input type=text size=10 class=input   name="CondicionPago60" id="CondicionPago60" value="<?=$r->CondicionPago60 ?>">
                 %</td>
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
			<tr class=row2>
			  <td>Nota 1</td>
			  <td><textarea name="Nota1" class="tbox" title="Nota1" id="Nota1" cols="60" rows="4"><?php if (!empty($r->Nota1)) echo $r->Nota1; else echo get_field("ParametroTercero","Descripcion","IDParametroTercero",1);  ?></textarea></td>
		  </tr>
			<tr class=row2>
			  <td>Nota 2</td>
			  <td><textarea name="Nota2" class="tbox" title="Nota1" id="Nota2" cols="60" rows="4"><?php if (!empty($r->Nota2)) echo $r->Nota2; else echo get_field("ParametroTercero","Descripcion","IDParametroTercero",2);  ?></textarea></td>
		  </tr>
			<tr class=row2>
			<td> Observaciones </td><td><textarea name="Observaciones" class="tbox" title="Observaciones" id="Observaciones" cols="60" rows="4"><?php if (!empty($r->Observaciones)) echo $r->Observaciones; else echo get_field("ParametroTercero","Descripcion","IDParametroTercero",4);  ?></textarea></td>
			</tr>
			<tr class=row2>
			  <td colspan="2"><table width="100%" border="0" class=row2>
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
                          	<img src="<?php echo "imagenes/". $r->Foto1; ?>" width="150" height="150">
                            <a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto3&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>
                          <?php else: ?>
	                          <input type="file" name="Foto3" id="Foto3" class=input>
                          <?php endif; ?></td>
			        <td>Foto 4</td>
			        <td><?php if (!empty($r->Foto4)): ?>
                          	<img src="<?php echo "imagenes/". $r->Foto1; ?>" width="150" height="150">
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
			<td colspan=2 align=center class=row2>&nbsp;</td>
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
                    <td style="color:#FFFFFF; !important;">TACON</td>
                    <td style="color:#FFFFFF; !important;">ALTURA</td>
                    <td style="color:#FFFFFF; !important;">HORMA</td>
                    <td style="color:#FFFFFF; !important;">PRECIO</td>
                    <td style="color:#FFFFFF; !important;">OBSERVACIONES</td>
                    <td style="color:#FFFFFF; !important;">Curva</td>
                  </tr>
                  <?php 
				  
				  if (count($array_detalle_orden)>0)
				  		$detalle_inicial=(int)count($array_detalle_orden)+5;
				else		
					  $detalle_inicial=6;
				  
				  
				  
				  for($i=1;$i<$detalle_inicial;$i++):  ?>
                      <tr>
                        <td >
                        <input type="hidden" name="IDDetallePedidoTercero<?php echo $i ?>" id="IDDetallePedidoTercero<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["IDDetallePedidoTercero"];  ?>">
                        <input type=text size=10 class=input  name="ReferenciaProveedor<?php echo $i ?>" id="ReferenciaProveedor<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["ReferenciaProveedor"];  ?>"></td>
                        <td><input type=text size=10 class="input verifica_referencia"  name="ReferenciaCaprino<?php echo $i ?>" id="ReferenciaCaprino<?php echo $i ?>" alt="<?php echo $i;  ?>" value="<?php echo $array_detalle_orden[$i]["ReferenciaCaprino"];  ?>"></td>
                        <td><input type=text size=10 class=input   name="CodigoColor<?php echo $i ?>" id="CodigoColor<?php echo $i ?>" alt="<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["CodigoColor"];  ?>"></td>
                        <td><input type=text size=10 class=input   name="CueroColor<?php echo $i ?>" id="CueroColor<?php echo $i ?>" alt="<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["CueroColor"];  ?>"></td>
                        <td><input type=text size=10 class=input   name="Suela<?php echo $i ?>" id="Suela<?php echo $i ?>" alt="<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["Suela"];  ?>"></td>
                        <td><input type=text size=10 class=input   name="Tacon<?php echo $i ?>" id="Tacon<?php echo $i ?>" alt="<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["Tacon"];  ?>"></td>
                        <td><input type=text size=10 class=input   name="Altura<?php echo $i ?>" id="Altura<?php echo $i ?>" alt="<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["Altura"];  ?>"></td>
                        <td><input type=text size=10 class=input   name="Horma<?php echo $i ?>" id="Horma<?php echo $i ?>" alt="<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["Horma"];  ?>"></td>
                        <td><input type=text size=10 class="input onlynumber"   name="Precio<?php echo $i ?>" alt="<?php echo $i ?>" id="Precio<?php echo $i ?>" value="<?php echo $array_detalle_orden[$i]["Precio"];  ?>"></td>
                        <td><textarea name="Observacion<?php echo $i ?>" class="input" title="Observacion" role="<?php echo $i ?>"  ="<?php echo $i ?>" id="Observacion<?php echo $i ?>" cols="15" rows="3"><?php echo $array_detalle_orden[$i]["Observacion"];  ?></textarea></td>
                        <td>
                        <select name="IDCurvaTercero<?php echo $i ?>" id="IDCurvaTercero<?php echo $i ?>" class="input" style="width:100px;">
                        	<option value=""></option>
						<?php $sql_curvas = db_query("Select * From CurvaTercero Where 1");
							  while($row_curva = db_fetch_array($sql_curvas)){ ?>
								  	<option value="<?php echo $row_curva[IDCurvaTercero]; ?>" <?php if($array_detalle_orden[$i]["IDCurvaTercero"]==$row_curva[IDCurvaTercero]) echo "selected"; ?>><?php echo $row_curva[Nombre]; ?></option>
							   <?php } ?>
                        </select>
						</td>
                      </tr>
                  <?php endfor; ?>
                  </tbody>
              </table>
              </div>
	              
              <table align="center" width="70%">
			<tr>
			  <td colspan=2 align="right">
		               <input type="hidden" name="ITEMSDETALLE" id="ITEMSDETALLE" value="<?php echo $detalle_inicial; ?>">	
                        <button class="btn btn-default pull-right" id="agrega_detalle"><i class="fa fa-plus"></i> [+]Agregar mas</button>
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
                          $sql_prioridad_alta=db_query("Select Nombre from PuntoVenta Where IDTipoPrioridad = 1 and IDCiudad = 1");
						  while ($row_pto = db_fetch_array($sql_prioridad_alta)){
							echo $row_pto[Nombre] ."<br>"; 	  
						  }
						  ?>
                          
                          </td>
			              <td><?php
                          $sql_prioridad_media=db_query("Select Nombre from PuntoVenta Where IDTipoPrioridad = 2 and IDCiudad = 1");
						  while ($row_pto = db_fetch_array($sql_prioridad_media)){
							echo $row_pto[Nombre] ."<br>";  	  
						  }
						  ?></td>
			              <td><?php
                          $sql_prioridad_baja=db_query("Select Nombre from PuntoVenta Where IDTipoPrioridad = 3 and IDCiudad = 1");
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
                          $sql_prioridad_alta=db_query("Select Nombre from PuntoVenta Where IDTipoPrioridad = 1 and IDCiudad = 2");
						  while ($row_pto = db_fetch_array($sql_prioridad_alta)){
							echo $row_pto[Nombre] ."<br>";  	  
						  }
						  ?></td>
			              <td><?php
                          $sql_prioridad_media=db_query("Select Nombre from PuntoVenta Where IDTipoPrioridad = 2 and IDCiudad = 2");
						  while ($row_pto = db_fetch_array($sql_prioridad_media)){
							echo $row_pto[Nombre] ."<br>";  	  
						  }
						  ?></td>
			              <td><?php
                          $sql_prioridad_baja=db_query("Select Nombre from PuntoVenta Where IDTipoPrioridad = 3 and IDCiudad = 2");
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
              <input type=hidden name="IDEstadoPedidoTercero" id="IDEstadoPedidoTercero" value=<?php if($newmode=="insert") echo "1"; else echo $r->IDEstadoPedidoTercero ?>>
              
              <input type=submit name=submit value="<?php if($newmode=="insert") echo "Guardar y Continuar"; else echo $submit_caption ?>" class=submit></td>
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
	
	$sql_punto_venta = "Select IDPuntoVenta,Nombre,IDCiudad From PuntoVenta Where 1  Order By IDCiudad, Nombre"; 
	$result_punto_venta = db_query($sql_punto_venta);
	while ($row_punto_venta = db_fetch_array($result_punto_venta)){
		$array_punto_venta[ $row_punto_venta["IDPuntoVenta"] ] = $row_punto_venta;
	}


?>
	
    
    
<div id="DetallePedido" style=" <?php if ($_GET[tab]!="detalle" && !empty($id)){ ?> display:none;  <?php } ?>">

<form name="frm" id="frmPedidoTercero" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){ ?> onsubmit="return EvaluaReg(this,Check)" <?php } ?>>
	
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
				<td width="4"></td>
				<td>&nbsp;</td>
				
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
                    <td style="color:#FFFFFF; !important;">TACON</td>
                    <td style="color:#FFFFFF; !important;">ALTURA</td>
                    <td style="color:#FFFFFF; !important;">HORMA</td>
                    <td style="color:#FFFFFF; !important;">PRECIO</td>
                    <td style="color:#FFFFFF; !important;">OBSERVACIONES</td>
                    <td style="color:#FFFFFF; !important;">Curva</td>
                  </tr>
                  <?php 
				  
				 $detalle_inicial=(int)count($array_detalle_orden);
				  
				  for($i=1;$i<=$detalle_inicial;$i++):  ?>
                      <tr>
                        <td><?php echo $array_detalle_orden[$i]["ReferenciaProveedor"]; ?></td>
                        <td><?php echo $array_detalle_orden[$i]["ReferenciaCaprino"];  ?></td>
                        <td><?php echo $array_detalle_orden[$i]["CodigoColor"];  ?></td>
                        <td><?php echo $array_detalle_orden[$i]["CueroColor"];  ?></td>
                        <td><?php echo $array_detalle_orden[$i]["Suela"];  ?></td>
                        <td><?php echo $array_detalle_orden[$i]["Tacon"];  ?></td>
                        <td><?php echo $array_detalle_orden[$i]["Altura"];  ?></td>
                        <td><?php echo $array_detalle_orden[$i]["Horma"];  ?></td>
                        <td><?php echo $array_detalle_orden[$i]["Precio"];  ?></td>
                        <td><?php echo $array_detalle_orden[$i]["Observacion"];  ?></td>
                        <td><?php echo get_field("CurvaTercero","Nombre","IDCurvaTercero",$array_detalle_orden[$i]["IDCurvaTercero"]); ?></td>
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
						  	$id_ciudad_ant = "";
							foreach($array_punto_venta as $id_punto_venta => $datos_punto_venta):
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
						        <td class="rowform" >Minimo</td>
                                <?php 
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>	
							        <td class=row1 align=center><?php echo $minimo_item[$id_talla]=$array_datos_curva[$datos_punto_venta[IDPuntoVenta]][$datos_talla[IDTalla]] ["Minimo"] ?></td>
							        
                                    <?php endforeach; 
								endif;	
								?>
                                <td bgcolor="#F1CFCF" align=center>&nbsp;</td>
					          </tr>
						      <tr>
						        <td class="rowform">Maximo</td>
	                            <?php 
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>	
							        <td class=row1 align=center><?php echo $maximo_item[$id_talla]=$array_datos_curva[$datos_punto_venta[IDPuntoVenta]][$datos_talla[IDTalla]] ["Maximo"] ?></td>
							        
                                    <?php endforeach; 
								endif;	
								?>
                                <td bgcolor="#F1CFCF" align=center>&nbsp;</td>
					          </tr>
						      <tr>
						        <td class="rowform">Existencias</td>
						        <?php 
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>	
							        <td class=row1 align=center>
										<?php 
											if(!empty($minimo_item[$id_talla]) && !empty($maximo_item[$id_talla]) ){
												//CONSUL:TA EXISTENCIAS	
												echo $existencias_item[$id_talla] = "1"; 	
											}
											else{
												$existencias_item[$id_talla] = "0";
											}
											
										?>
                                     </td>
							        
                                    <?php endforeach; 
								endif;	
								?>
                                <td bgcolor="#F1CFCF" align=center>&nbsp;</td>
					          </tr>
                              
                               <tr>
						        <td class="rowform">
                                	<?php echo $array_detalle_orden[$i]["ReferenciaCaprino"];  ?>
                                </td>
	                            <?php 
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									// Verifico si ya eiste algo guardado para no reemplazar
									$sql_detalle_pedido_ref = "Select Cantidad 
															  From DetallePedidoTerceroReferencia
															  Where IDPedidoTercero= '".$r->IDPedidoTercero."' and 
															  IDDetallePedidoTercero = '".$array_detalle_orden[$i]["IDDetallePedidoTercero"]."' and
															  IDPuntoVenta = '".$datos_punto_venta[IDPuntoVenta]."' and 
															  IDTalla = '".$datos_talla[IDTalla]."'";
									$result_detalle_pedido_ref = db_query($sql_detalle_pedido_ref);						  
									$row_detalle_pedido_ref=db_fetch_array($result_detalle_pedido_ref);
									if (!empty($row_detalle_pedido_ref["Cantidad"]))
										$valor_pedir_item = (int)$row_detalle_pedido_ref["Cantidad"];
									else
										$valor_pedir_item = (int)$maximo_item[$id_talla] - (int)$existencias_item[$id_talla];
									 
									 	$suma_item_pedir+=$valor_pedir_item;
										$suma_item_pedir_talla[$datos_talla[IDTalla]] +=  $valor_pedir_item;
										
										$super_total_talla[$datos_talla[IDTalla]][$array_detalle_orden[$i]["IDDetallePedidoTercero"]]+=$valor_pedir_item;
										
									?>	
							        <td class=row1 align=center>
                                    <?php if(!empty($minimo_item[$id_talla]) && !empty($maximo_item[$id_talla]) ){ ?>
	                                    <input type="text" name="Pedido[<?php echo $datos_talla[IDTalla]; ?>][<?php echo $datos_punto_venta[IDPuntoVenta] ?>][<?php echo $array_detalle_orden[$i]["IDDetallePedidoTercero"] ?>]"  size="5" value="<?php if ((int)$valor_pedir_item>0) echo (int)$valor_pedir_item; ?>">
                                    <?php } ?>
                                    
                                    
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
                              <td style="height:2px" bgcolor="#FFFFFF" >
                                	
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
										
										
										echo number_format($suma_item_pedir_talla[$id_talla],0,",","."); 
										
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
			        <td class="rowform"><?php echo $array_detalle_orden[$i]["ReferenciaCaprino"];  ?></td>
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
              
              <input type=submit name=submit value="<?php if($newmode=="insert") echo "Guardar y Continuar"; else echo $submit_caption ?>" class=submit></td>
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
		Global $TitleMod,$MOD,$Table,$Key,$listar;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table WHERE 1 ORDER BY $Key";
	 	
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
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

						<?php while($r = db_fetch_object($result)){
						?>
						  	
						<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
								&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
							</td>
						<td nowrap class=row1><?php echo $r->NumeroOrdenCompra ?></td>
						<td nowrap class=row1><?php echo get_field("Proveedor","Nombre","IDProveedor",$r->IDProveedor) ?></td>
						<td nowrap class=row1><?php echo $r->FechaPedido ?></td>
						<td nowrap class=row1><?php echo $r->FechaEntrega ?></td>
						<td align=center valign=middle nowrap width=60 class=row2>
								&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
							</td>
					</tr>
						<?php } // END for
						?>
						<tr>
							<td class=texto bgcolor=#DBEAF5 colspan=6 nowrap>
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
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
					<option value="Nombre">Nombre</option>
					<option value="Apellidos">Apellidos</option>
					<option value="Cargo.Cargo">Cargo</option>
					<option value="Salario">Salario</option>
					<option value="CodigoVendedor">CodigoVendedor</option>
					<option value="Publicar">Publicar</option>
				</select> 
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post"> 
				Entre <input type=text readonly size=10 class=input name=limit1>
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
				ordenar por 
				<select name="order_by" class="popup">
					<option value="Nombre">Nombre</option>
					<option value="Apellidos">Apellidos</option>
					<option value="Cargo.Cargo">Cargo</option>
					<option value="Salario">Salario</option>
					<option value="CodigoVendedor">CodigoVendedor</option>
					<option value="Publicar">Publicar</option>
				</select> 
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
				<input type="hidden" name="mod" value="<?=$MOD?>">
				<input type="hidden" name="rangofield" value="Fecha">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="Cargo">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php
	}//End function filtrar
?>
