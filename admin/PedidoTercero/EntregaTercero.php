
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



			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;

			case "actualiza_datos_verificacion";

			$frm= vars_LOG($HTTP_POST_VARS);

			//ACTUALIZO LAS CANTIDADES DE PEDIDO POR PUNTO, TALLA y REFERENCIA
				foreach($frm[FechaEntregaContabilidad] as $id_dato => $valor){
					foreach($valor as $id_punto => $fecha_contabilidad):
						if(!empty($fecha_contabilidad)):
							$sql_fecha_contab = "Update  DetallePedidoTerceroReferencia Set FechaEntregaContabilidad  = '".$fecha_contabilidad."' Where NumeroFactura = '".$id_dato."' and IDPuntoVenta = '".$id_punto."' ";
							db_query($sql_fecha_contab);
						endif;
					endforeach;
				}


					foreach($frm[FechaEmision] as $id_dato => $valor){
						foreach($valor as $id_punto => $fecha_emision):
							if(!empty($fecha_emision)):
								$sql_fecha_contab = "Update  DetallePedidoTerceroReferencia Set FechaEmision  = '".$fecha_emision."' Where NumeroFactura = '".$id_dato."' and IDPuntoVenta = '".$id_punto."' ";
								db_query($sql_fecha_contab);
							endif;
						endforeach;
					}

			?>
			<script>
            	alert("Datos Guardados con exito");
            </script>
			<?php
			print_form($frm["IDPedidoTercero"],"update","Actualizar $TitleMod","Realizar Cambios");

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
		$q_detalle = db_query(" SELECT * FROM DetallePedidoTercero WHERE IDPedidoTercero = '$id' ORDER BY IDDetallePedidoTercero ASC ");
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
<?php
//SUBDETALLE PEDIDO
if ($_GET["tab"]=="verificacion" && !empty($id)){

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



<div id="DetallePedido">

<form name="frm" id="frmPedidoTerceroActualiza" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){ ?> onsubmit="return EvaluaReg(this,Check)" <?php } ?>>

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
							<td valign="top" height="16"><a href="./?mod=<?php echo $MOD;?>&id=<?=$id?>&tab=detalle&action=edit" class="TAB">VERIFICACION PEDIDO</a>&nbsp;</td>
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
				      <td colspan="3"><span id="EmailProveedor"><?php echo $datos_proveedor[Email] ?><span class="col2">
					      </span></span></td>
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
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["ReferenciaProveedor"]; ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["ReferenciaCaprino"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["CodigoColor"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["CueroColor"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["Suela"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["Tacon"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["Altura"];  ?></td>
                        <td style="border-bottom:1px solid #000000"><?php echo $array_detalle_orden[$i]["Horma"];  ?></td>
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
								$columna_talla=0;
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
						        <td class="maintitle" bgcolor="#9daac6" colspan="3" ><?php echo $datos_punto_venta[Nombre]; ?> </td>
					          </tr>
						      <tr>

						        <?php
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
										$columna_talla++;
									?>
                                    <?php endforeach;
								endif;
								?>
                                <td class="titlemedium" colspan="<?php echo $columna_talla+=2; ?>" style="color:#EE080C !important">ESTADO: <?php echo $estado_pedido_actual = estado_tercero_pto_vta($id, $datos_punto_venta[IDPuntoVenta]); ?></td>
						        <td colspan="4" bgcolor="#FDEDA4" align=center style="font-weight:bold">ENTREGA</td>
						        <td colspan="3" bgcolor="#F4B594" align=center style="font-weight:bold">DEVOLUCION</td>
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
                                <td class="titlemedium" nowrap align="center">Fac Fisica</td>
                                <td class="titlemedium" nowrap align="center">Pares</td>
                                <td class="titlemedium" nowrap align="center">Fecha Rec</td>
                                <td class="titlemedium" nowrap align="center">Remision</td>
                                <td class="titlemedium" nowrap align="center">Tallas</td>
                                <td class="titlemedium" nowrap align="center">Pares</td>
                                <td class="titlemedium" nowrap align="center">Observ.</td>
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
						        <td class="rowform">
                                	<?php echo $array_detalle_orden[$i]["ReferenciaCaprino"].$array_detalle_orden[$i]["CodigoColor"];  ?>
                                </td>
	                            <?php
								if (count($array_talla)>0):
									$cantidad_recibida_item ="";
									$cantidad_devuelto_item ="";
									unset($array_remision);
									unset($array_factura);
									unset($array_fecha_recibida);
									unset($array_talla_devuelta);
									unset($array_observacion);
									unset($array_fechacontabilidad);

									foreach($array_talla as $id_talla => $datos_talla):
									// Verifico si ya existe algo guardado para no reemplazar
									$sql_detalle_pedido_ref = "Select *
															  From DetallePedidoTerceroReferencia
															  Where IDPedidoTercero= '".$r->IDPedidoTercero."' and
															  IDDetallePedidoTercero = '".$array_detalle_orden[$i]["IDDetallePedidoTercero"]."' and
															  IDPuntoVenta = '".$datos_punto_venta[IDPuntoVenta]."' and
															  IDTalla = '".$datos_talla[IDTalla]."'";



									$result_detalle_pedido_ref = db_query($sql_detalle_pedido_ref);
									$row_detalle_pedido_ref=db_fetch_array($result_detalle_pedido_ref);
									$IDDetallePedidoReferencia = $row_detalle_pedido_ref["IDDetallePedidoTerceroReferencia"];



									if (!empty($row_detalle_pedido_ref[Remision])):
										$array_remision[$row_detalle_pedido_ref[Remision]]=$row_detalle_pedido_ref[Remision];
									endif;
									if (!empty($row_detalle_pedido_ref[NumeroFactura])):
										$array_factura[$row_detalle_pedido_ref[NumeroFactura]]=$row_detalle_pedido_ref[NumeroFactura];
										$array_total_factura[$row_detalle_pedido_ref[NumeroFactura]] = $row_detalle_pedido_ref[NumeroFactura];
									endif;
									if ($row_detalle_pedido_ref[CantidadDevuelto]>0):
										$array_talla_devuelta[$row_detalle_pedido_ref[IDTalla]]=$row_detalle_pedido_ref[IDTalla];
									endif;

									if ($row_detalle_pedido_ref[CantidadRecibida]>0):
										$array_fecha_recibida[substr($row_detalle_pedido_ref[FechaRecibido],0,10)]=$row_detalle_pedido_ref[substr($row_detalle_pedido_ref[FechaRecibido],0,10)];
									endif;

									if (!empty($row_detalle_pedido_ref[Observacion])):
										$array_observacion[$row_detalle_pedido_ref[Observacion]]=$row_detalle_pedido_ref[Observacion];
									endif;

									if (!empty($row_detalle_pedido_ref[FechaEntregaContabilidad])):
										$array_fechacontabilidad[$row_detalle_pedido_ref[FechaEntregaContabilidad]]=$row_detalle_pedido_ref[FechaEntregaContabilidad];
									endif;


									$cantidad_recibida_item += $row_detalle_pedido_ref[CantidadRecibido];
									$cantidad_devuelto_item += $row_detalle_pedido_ref[CantidadDevuelto];

									$refe=$array_detalle_orden[$i]["ReferenciaCaprino"].$array_detalle_orden[$i]["CodigoColor"];
									
									if (is_numeric($row_detalle_pedido_ref["Cantidad"]))
										$valor_pedir_item = (int)$row_detalle_pedido_ref["Cantidad"];
									else
										$valor_pedir_item = (int)$maximo_item[$id_talla] - (int)$existencias_item[$id_talla];

									 	$suma_item_pedir+=$valor_pedir_item;
										$suma_item_pedir_talla[$datos_talla[IDTalla]] +=  $valor_pedir_item;

										$super_total_talla[$datos_talla[IDTalla]][$array_detalle_orden[$i]["IDDetallePedidoTercero"]]+=$valor_pedir_item;

									?>
						           <td class=row1 align=center>

                                   	   <?php if($r->IDEstadoPedidoTercero == 1): ?>
                                    	   <input type="text" name="Pedido[<?php echo $datos_talla[IDTalla]; ?>][<?php echo $datos_punto_venta[IDPuntoVenta] ?>][<?php echo $array_detalle_orden[$i]["IDDetallePedidoTercero"] ?>]"  size="5" value="<?php if (is_numeric($valor_pedir_item)) echo (int)$valor_pedir_item; ?>" style="text-align:center">
                                       <?php
										else:
											if (is_numeric($valor_pedir_item) && $valor_pedir_item!="0") echo (int)$valor_pedir_item;
										endif; ?>



                                   </td>

                                   <?php endforeach;
								endif;
								?>
                                <td bgcolor="#F1CFCF" align=center style="font-weight:bold">
								<?php
									echo number_format($suma_item_pedir,0,",",".");
								?>
                                </td>
                                <td bgcolor="#FDEDA4" align=center style="font-weight:bold"><?php echo implode(",",$array_factura); ?></td>
                                <td bgcolor="#FDEDA4" align=center style="font-weight:bold"><?php echo $cantidad_recibida_item; ?> </td>
                                <td bgcolor="#FDEDA4" align=center style="font-weight:bold"><?php echo implode(",",$array_fecha_recibida); ?></td>
                                <td bgcolor="#FDEDA4" align=center style="font-weight:bold"><?php echo implode(",",$array_remision); ?></td>
                                <td bgcolor="#F4B594" align=center style="font-weight:bold">
								<?php
								if ($array_talla_devuelta):
									foreach($array_talla_devuelta as $valor_talla):
										echo get_field("Talla","Nombre","IDTalla",$valor_talla) . " ";

									endforeach;
								endif;
								?>
                                </td>
                                <td bgcolor="#F4B594" align=center style="font-weight:bold"><?php echo $cantidad_devuelto_item; ?></td>
                                <td bgcolor="#F4B594" align=center style="font-weight:bold"><?php echo implode(",",$array_observacion); ?></td>
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
                                <td class="titlemedium" nowrap align="center"></td>
                                <td class="titlemedium" nowrap align="center"></td>
                                <td class="titlemedium" nowrap align="center"></td>
                                <td class="titlemedium" nowrap align="center"></td>
                                <td class="titlemedium" nowrap align="center"></td>
                                <td class="titlemedium" nowrap align="center"></td>
                                <td class="titlemedium" nowrap align="center"></td>
					          </tr>


					        </tbody>
					      </table>
                          <?php endif; ?>
                          <br />
                          <?php
						 if(count($array_total_factura)>0):	?>
                         	<table width="100%" border="0" cellspacing="1" cellpadding="0">
                            <tr>
                            	<td class="titlemedium">	Factura</td>
                            	<td class="titlemedium">Cantidad</td>
                            	<td class="titlemedium">Devueltos</td>
                            	<td class="titlemedium">Remision</td>
                            	<td class="titlemedium">Fecha Recibido</td>
                              <td class="titlemedium">Fecha Enviada Contabilidad</td>
															<td class="titlemedium">Fecha Emision Factura</td>
                            </tr>
						 	<?php


							foreach($array_total_factura as $factura): ?>
                            <tr>
                            	<td><?php echo $factura; ?></td>
                            	<td><?php
								//Cantidad Factura
								$sql_cantidad_fac= "Select sum(CantidadRecibido) as TotalRecibido, FechaRecibido, Remision From DetallePedidoTerceroReferencia Where NumeroFactura = '".$factura."' and IDPedidoTercero = '".$_GET["id"]."' and IDPuntoVenta = '".$datos_punto_venta[IDPuntoVenta]."'";
								$result_cantidad_fac = db_query($sql_cantidad_fac);
								$row_cantidad_fac = db_fetch_array($result_cantidad_fac);
								$fecha_recibido = substr($row_cantidad_fac["FechaRecibido"],0,10);

								$sql_cantidad_fac= "Select sum(Cantidad) as TotalRecibido, FechaRemision From Entrada Where NumeroFactura = '".$factura."' and IDPuntoVenta = '".$datos_punto_venta[IDPuntoVenta]."' and FechaRemision = '".$fecha_recibido ."' and Remision = '".$row_cantidad_fac["Remision"]."'";
								$result_cantidad_fac = db_query($sql_cantidad_fac);
								$row_cantidad_fac = db_fetch_array($result_cantidad_fac);

								echo $row_cantidad_fac["TotalRecibido"];

								?></td>
                            	<td><?php
								//Cantidad Factura
								$sql_cantidad_fac= "Select sum(CantidadDevuelto) as TotalDevuelto, Remision, FechaRecibido From DetallePedidoTerceroReferencia Where NumeroFactura = '".$factura."' and IDPedidoTercero = '".$_GET["id"]."' and IDPuntoVenta = '".$datos_punto_venta[IDPuntoVenta]."'";
								$result_cantidad_fac = db_query($sql_cantidad_fac);
								$row_cantidad_fac = db_fetch_array($result_cantidad_fac);
								echo $row_cantidad_fac["TotalDevuelto"]; ?></td>
                            	<td><?php echo $row_cantidad_fac["Remision"]; ?></td>
                            	<td>
                                <?php
								//Cantidad Factura
								echo $fecha_recibido; ?>
								</td>
                                <td>
                                <?php
								$sql_fecha_contabili = "Select FechaEntregaContabilidad From DetallePedidoTerceroReferencia Where NumeroFactura = '".$factura."' and IDPedidoTercero = '".$_GET["id"]."' and IDPuntoVenta = '".$datos_punto_venta[IDPuntoVenta]."' Limit 1";
								$result_fecha_contabili = db_query($sql_fecha_contabili);
								$row_fecha_contabili = db_fetch_array($result_fecha_contabili);
								$fecha_contabili = $row_fecha_contabili["FechaEntregaContabilidad"];

                                //$fecha_contabili = get_field("DetallePedidoTerceroReferencia","FechaEntregaContabilidad","NumeroFactura",$factura);
								?>
                                <input type="text" name="FechaEntregaContabilidad[<?php echo $factura ?>][<?php echo $datos_punto_venta[IDPuntoVenta]; ?>]" value="<?php echo $fecha_contabili; ?>"></td>

																<td>
																<?php
								$sql_fecha_emision = "Select FechaEmision From DetallePedidoTerceroReferencia Where NumeroFactura = '".$factura."' and IDPedidoTercero = '".$_GET["id"]."' and IDPuntoVenta = '".$datos_punto_venta[IDPuntoVenta]."' Limit 1";
								$result_fecha_emision = db_query($sql_fecha_emision);
								$row_fecha_emision = db_fetch_array($result_fecha_emision);
								$fecha_emision = $row_fecha_emision["FechaEmision"];

																//$fecha_contabili = get_field("DetallePedidoTerceroReferencia","FechaEntregaContabilidad","NumeroFactura",$factura);
								?>
																<input type="text" name="FechaEmision[<?php echo $factura ?>][<?php echo $datos_punto_venta[IDPuntoVenta]; ?>]" value="<?php echo $fecha_emision; ?>"></td>

                             </tr>
							<?php endforeach; ?>
                            </table>




                            <?php $array_resumen = resumen_tercero_pto_vta($id, $datos_punto_venta[IDPuntoVenta]); ?>
                            <table width="40%" border="0" cellspacing="1" cellpadding="0" align="left">
                            <tr>
                            	<td class="titlemedium">Cantidad Pedidos</td>
                            	<td class="titlemedium">Cantidad Recibidos</td>
                            	<td class="titlemedium">Faltantes</td>
                           	  </tr>
                            <tr>
                            	<td bgcolor="#FFFFFF">
									<?php echo $array_resumen[ToTalPedido];
									$total_pedido += $array_resumen[ToTalPedido]; ?>
                                </td>
                            	<td bgcolor="#FFFFFF">
								<?php echo $array_resumen[ToTalRecibido];
								$total_recibido += $array_resumen[ToTalRecibido]; ?></td>
                                <td bgcolor="#FFFFFF"><?php echo $diferencia = (int)$array_resumen[ToTalPedido] - (int)$array_resumen[ToTalRecibido]; ?></td>
                           	  </tr>

                            </table>





						 <?php endif;
						 unset($array_total_factura);
						 ?>

                         <?php
                          endforeach;
						 ?>




                         <?php

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
			        <td bgcolor="#FFFFFF" align="left">TOTAL RECIBIDOS: <?php echo $total_recibido; ?></td>
		          </tr>
			      <tr>
			        <td bgcolor="#FFFFFF">TOTAL MEDELLIN</td>
			        <td bgcolor="#FFFFFF" align="center" style="font-weight:bold"><?php echo  number_format($total_ciudad[2],0,",",".") ?></td>
			        <td bgcolor="#FFFFFF" align="left">TOTAL FALTANTES: <?php  echo $gran_total_faltante = ((int)$total_ciudad[1] + (int)$total_ciudad[2]) - (int)$total_recibido;; ?></td>
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
_
			        <td class="rowform"><?php echo $array_detalle_orden[$i]["ReferenciaCaprino"].$array_detalle_orden[$i]["CodigoColor"];  ?></td>
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
              <input type=hidden name=action value="actualiza_datos_verificacion">
              <input type=submit name=submit value="<?php  echo "Guardar";  ?>" class=submit>



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

<?php
				if($rows > 0){
		?>

<?php
	}// End if$rows
	else
		echo "<br><br><span class=subtitle><b>No existen registros en  $TitleMod </b></span>";
}// Enf function list()
