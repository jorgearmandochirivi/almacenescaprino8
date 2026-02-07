<body>
	<?php 

$TitleMod ="Generar Pedido Sugerido";

$Table = "SugeridoPedido";
$TableJoin = "DetalleSugeridoPedido";
$Key = "IDSugeridoPedido";
$MOD = "generarpedidos";
$m = "Pedido";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			
			case "edit":
				print_form($id,"update","$TitleMod","Generar el Pedido");
			break ;
			
			case "update" :
				$frm= vars_LOG($HTTP_POST_VARS);
				
				$id = generarpedido($frm);
								
				echo "<SCRIPT>location.href='?mod=Pedido&action=edit&id=".$id."';</SCRIPT>";
			break;
			
			case "delref" :
				$id = delitem($idref, $idsugerido);
				echo "<SCRIPT>location.href='?mod=".$MOD."&action=edit&id=".$id."';</SCRIPT>";
			break;

			case "agregaritem" :
				$id = sugerir($HTTP_POST_VARS['Referencia'], $HTTP_POST_VARS['IDSugerido']);
				 echo "<SCRIPT>location.href='?mod=".$MOD."&action=edit&id=".$id."';</SCRIPT>";
				break;
			
			default : 
				 $id = sugerir($HTTP_POST_VARS['Referencia']);
				 echo "<SCRIPT>location.href='?mod=".$MOD."&action=edit&id=".$id."';</SCRIPT>";
			break;
		
		} // End switch




/*******************************************************************************************
	sugerir: Realiza el Pedido Sugerido de acuerdo a las existencias, minimos, maximos  y
			pedidos sin entrar.
	Parametros:
			$idReferencia : id de la referencia a generar el sugerido
	Retorna:	
			$id: ID del pedido sugerido generado por la funcion.
*******************************************************************************************/
function sugerir($idReferencia, $idsugerido)
{
	Global $MOD,$Table,$Key, $ID_Usuario, $Nombre_Usuario;
	
	$idReferencia = get_field( "Referencia","IDReferencia","Numero", $idReferencia." AND Publicar = 'S' ");
	
	$sql_codificacion =  "SELECT * FROM CodificacionEspecifica WHERE IDReferencia = '$idReferencia'";
	$query_codificacion = db_query( $sql_codificacion );
	
	if( db_num_rows( $query_codificacion ) > 0 )
	{
		
		if( empty( $idsugerido ) )
		{
		
			$idsugerido = get_maxID($Table,$Key);
			$idpedido = get_maxID("OrdenCompra","IDOrdenCompra");
			
			$strinsert_sugerido = "INSERT INTO $Table (IDSugeridoPedido, Fecha, IDEmpleado, NumeroSugerido, ";
			$strinsert_sugerido .= "Publicar, UsuarioTrCr, FechaTrCr) ";
			$strinsert_sugerido .= "VALUES ('$idsugerido', now(), '$ID_Usuario', '$idpedido', 'S', '$Nombre_Usuario', now())";
			
			$query_sugerido = db_query( $strinsert_sugerido );
			
			//insertar el log
			insertlog($ID_Usuario,$Table,$idsugerido,"Insertar",$strinsert_sugerido);
		
		}//if( !empty( $idsugerido ) )
		
		while($r_codificacion[$i] = db_fetch_array($query_codificacion))
		{
			$i++;
		} //end while($r[$i] = db_fetch_array($query_codificacion))
		//print_r($r);
		
		foreach( $r_codificacion as $valor )
		{
			
			//VERIFICAR LA TABLA DE PEDIDO PARA VER LOS PEDIDOS SOLICITADOS
			//Estado de pedidos aprobados : IDEstadoPedido = 5;
			
			$sql_pedidos_solicitados = "SELECT * FROM DetalleOrdenCompra WHERE IDEstadoPedido = 5 AND IDReferencia = '$idReferencia' AND IDTalla = '$valor[IDTalla]'";
			
			$query_pedidos_solicitados = db_query( $sql_pedidos_solicitados );
			
			if( db_num_rows( $query_pedidos_solicitados ) > 0 )
			{
				while( $r_pedidos_solicitados = db_fetch_object( $query_pedidos_solicitados ) )
				{
					
					$pedido = $pedido + $r_pedidos_solicitados->Cantidad;
				
				}//end while( $r_pedidos_solicitados = db_fetch_object( $query_pedidos_solicitados ) )
		
			}//end if( db_num_rows( $query_pedidos_solicitados ) > 0 )
						
			//VERIFICAR CONTRA LA TABLA DE MOVIMIENTOS PARA LOCALIZAR LOS PEDIDOS PARCIALES
			//Estado de pedidos recibidos parcialmente : IDEstadoPedido = 4;	
			
			$sql_pedidos_parciales  = "SELECT DOC.IDOrdenCompra, DOC.IDReferencia, DOC.IDTalla as talladetalle, ";
			$sql_pedidos_parciales .= "DOC.Cantidad as cantidaddetalle, OC.IDOrdenCompra, M.*, DM.* ";
			$sql_pedidos_parciales .= "FROM DetalleOrdenCompra DOC, OrdenCompra OC, Movimiento M , DetalleMovimiento DM ";
			$sql_pedidos_parciales .= "WHERE DOC.IDEstadoPedido = 4 AND DOC.IDReferencia = '$idReferencia' AND DOC.IDTalla = '$valor[IDTalla]'";
			$sql_pedidos_parciales .= "AND DOC.IDOrdenCompra = OC.IDOrdenCompra ";
			
			//Revisar si se valida contra el Id o contra el numero de la orden de compra
			$sql_pedidos_parciales .= "AND OC.NumeroOrden = M.NumeroOrden AND M.IDMovimiento = DM.IDMovimiento ";
			$sql_pedidos_parciales .= "AND DM.IDTalla = DOC.IDTalla";
			
			$query_pedidos_parciales = db_query( $sql_pedidos_parciales );
			
			if( db_num_rows( $query_pedidos_parciales ) > 0 )
			{
				while( $r_pedidos_PARCIALES = db_fetch_object( $query_pedidos_solicitados ) )
				{
					
					$falta_recibir = $r_pedidos_parciales->cantidaddestalle - $r_pedidos_parciales->Cantidad;
					
					if( $falta_recibir > 0  )
					{
					
						$pedido = $pedido + $falta_recibir;
						
					}//end if( $falta_recibir > 0  )
					
				}//end while( $r_pedidos_parciales = db_fetch_object( $query_pedidos_parciales ) )
				
			}//end if( db_num_rows( $query_pedidos_parciales ) > 0 )
			
			//SE GENERA EL SUGERIDO			
				
			$pedir = $valor['Maximo'] - ($pedido + $valor['Existencias']);
			
			//if($pedir > 0)
			//{
				
				$iddetalle = get_maxID("DetalleSugeridoPedido","IDDetalleSugeridoPedido");
				
				$strinsertdetalle  = "INSERT INTO DetalleSugeridoPedido (IDDetalleSugeridoPedido, IDSugeridoPedido, IDReferencia,IDTalla, Cantidad, Publicar, UsuarioTrCr, FechaTrCr) ";
				$strinsertdetalle .= "VALUES ('$iddetalle','$idsugerido', $idReferencia,'$valor[IDTalla]','$pedir','S','$Nombre_Usuario',now())";
			
				$query_detalle = db_query( $strinsertdetalle );
				
				//insertar el log
				insertlog($ID_Usuario,"DetalleSugeridoPedido",$iddetalle,"Insertar",$strinsertdetalle); 
		
			//}//if($pedir > 0)
		
			$pedido = 0;
			
		}//end foreach( $r as $valor )
		
		window_alert( "Sugerido Generado" );
		return $idsugerido;
	
	}//end if( db_num_rows( $query_codificacion ) > 0 )
	else
	{
	
		window_alert( "Referencia no existe o no se ha codificado correctamente" );
		echo "<SCRIPT>location.href='?mod=mPedido';</SCRIPT>";
	
	}//end else
	
}//end function sugerir($idReferencia)


/*******************************************************************************************
		funcion print_form
*******************************************************************************************/
function print_form($id,$newmode,$TitleMod,$submit_option)
{
	
	Global $TitleMod,$MOD,$Table,$Key;
	
	$sql =  "SELECT * FROM $Table WHERE $Key = '$id'";
	 	
	$query_sugerido = db_query($sql);
	$r_sugerido = db_fetch_object( $query_sugerido );
	
?>
<br><br>

<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;
				<img src=images/folderopen.gif border=0> 
					<a href="./?mod=Sugerido">
						Administrar Pedidos Sugeridos
					</a>
			</td>
			<td></td>
		</tr>
</table>

<br><br>
	<table width=500 cellpadding=0 cellspacing=0 align=center class=bordertable>
		<tr>
			<td class="maintitle" bgcolor="#9daac6"><b><?php echo $TitleMod ?></b></td>
		</tr>
		<tr>
			<td>
				<table width="100%" border="0" cellspacing="1" cellpadding="0">
					<form name="frm" action="<?php echo $PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
						<tr>
							<td class="row1" nowrap>
								<table width=100% cellspacing="1" cellpadding="1" bgcolor=#ffffff>
									<tr>
										<td class=row1 colspan="2"></td>
										<td class=row1>
											<div align="left">
												
												 Numero.</div>
										</td>
										<td class=row1>
											<input type="text" class="input" name="NumeroPedido" size="24" value="<?php echo $r_sugerido->NumeroSugerido?>">
										</td>
									</tr>
									<tr>
										<td class=row1>Fecha</td>
										<td class=row1>
											<input type="text" class="input" name="Fecha" size="15" value="<?php echo fecha()?>" readonly>
											<script language="JavaScript1.2">
												<!--
													if (!document.layers)
														document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
													//-->
											</script>
										</td>
										<td class=row1>Estado </td>
										<td class=row1>
											<input type="text" class="input" name="EstadoPedido" size="24" value="<?php echo get_field("EstadoPedido","Descripcion","IDEstadoPedido",$r_sugerido->IDEstadoPedido)?>">
											<input type="hidden" name="IDEstadoPedido" size="24" value="<?php echo $r_sugerido->IDEstadoPedido?>"></td>
									</tr>
									<tr>
										<td class=row1>Observaciones</td>
										<td colspan="3" class=row1><textarea name="Observaciones" rows="4" cols="64"></textarea></td>
									</tr>
									<tr>
										<td class=row1 colspan="4"></td>
									</tr>
									<tr>
										<td class=titlemedium colspan="3">
											Detalle del Pedido Sugerido
										</td>
										<td class=titlemedium align=right>	
											<input type=button name=submit value="Sugerir Item" class=submit onclick="javascript:popUp('Pedido/agregaritem.php?&idsugerido=<?php echo $id?>','350','100')">
										</td>	
									</tr>
									<tr>
										<td class=row2 colspan="4">
											<?php verdetallesugerido($id);?>
										</td>
									</tr>
									<tr>
										<td class=row2 colspan="4" align="center">
											<input type="hidden" name="action" value="<?php echo $newmode?>">
											<input type="hidden" name="idsugerido" value="<?php echo $id?>">
											<input type="submit" class="submit" name="submit" value="<?php echo $submit_option?>">
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</form>
				</table>
			</td>
		</tr>
	</table>	
<?php 
}// Enf function print_form()				

/*******************************************************************************************
	verdetallesugerido: Muestra el detalle de el pedido sugerido
	Parametros:
			$id : id del detalle sugerido a mostar
	Retorna:	
			Void
*******************************************************************************************/

function verdetallesugerido($id)
{
	
	Global $TitleMod,$MOD,$Table,$Key,$TableJoin;
	
	$sql_referencias =  "SELECT * FROM $TableJoin WHERE $Key = '$id' GROUP BY IDPuntoVentaReferencia";
	
	$query_referencias = db_query( $sql_referencias );
	
	$i=0;
?>
	<table width=100% cellpadding=1 cellspacing=0 class=text bgcolor=#ffffff>
<?php 
	while( $r_referencias = db_fetch_object( $query_referencias ) )
	{
		
		$sql_detalle =  "SELECT * FROM $TableJoin WHERE $Key = '$id' AND IDPuntoVentaReferencia = '$r_referencias->IDPuntoVentaReferencia'";
		$query_detalle = db_query($sql_detalle);
		$rows_detalle = db_num_rows($query_detalle);
		
		while($r_detalle[$i] = db_fetch_array($query_detalle))
		{
			$i++;
		} //end while($r[$i] = db_fetch_array($query_detalle))
		
		$i = 0;
		//print_r($r);
		
		//REALIZAR EL QUERY PARA VER LA CODIFICACION ESPECIFICA DE LA REFERENCIA
		
		$sql_codificacion = "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$r_referencias->IDPuntoVentaReferencia' GROUP BY IDCodificacionEspecifica";
		$query_codificacion = db_query( $sql_codificacion );
		
		while($r_codificacion[$i] = db_fetch_array($query_codificacion))
		{
			$i++;
		} //end while($r_codificacion[$i] = db_fetch_array($query_codificacion))
		
		$i = 0;
		
	?>
		
			<tr>
				<td class=rowform align=center>
				<?php 
					echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$r_referencias->IDPuntoVentaReferencia));	
				?>
				</td>
				<?php 
					foreach($r_detalle as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=rowform align=center>".get_field("Talla","Descripcion","IDTalla",$talla[IDTalla])."</td>";
					}//end foreach($r_detalle as $talla)
				?>
				<td class=rowform align=center>
					<a href="./?mod=<?php echo $MOD?>&action=delref&idref=<?php echo $r_referencias->IDPuntoVentaReferencia?>&idsugerido=<?php echo $id?>" title="Quitar Item">
						<img src="images/trash.gif" border="0">
					</a>
				</td>	
			</tr>
			
			<tr>
				<td class=rowform align=center>
					Existencias
				</td>
				<?php 
					foreach($r_codificacion as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=row2 align=center>".$talla[Existencias]."</td>";
					}//end foreach($r_detalle as $talla)
				?>
				<td class=row2 align=center>
				</td>	
			</tr>
			
			<tr>
				<td class=rowform align=center>
					M&aacute;ximo
				</td>
				<?php 
					foreach($r_codificacion as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=row2 align=center>".$talla[Maximo]."</td>";
					}//end foreach($r_detalle as $talla)
				?>
				<td class=row2 align=center>
				</td>	
			</tr>
			
			<tr>
				<td class=rowform align=center>
					Minimo
				</td>
				<?php 
					foreach($r_codificacion as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=row2 align=center>".$talla[Minimo]."</td>";
					}//end foreach($r_detalle as $talla)
				?>
				<td class=row2 align=center>
				</td>	
			</tr>
			
			<tr>
				<td class="rowform" align=center>
					SUGERIDO
				</td>
				<?php 
				foreach($r_detalle as $talla)
				{
					if(!empty($talla[IDTalla]))
					{
						echo "<td class=row1 align=center><input type=text size=5 value=".$talla[Cantidad]." name=".get_field("Referencia","Numero","IDReferencia",$r_referencias->IDReferencia)."[$talla[IDTalla]]>";
						//SE IMPRIME UN HIDDEN CON EL ID DE LAS TALLA
						//echo "<input type=hidden value=".$talla[IDTalla]." name=Talla".get_field("Referencia","Numero","IDReferencia",$r_referencias->IDReferencia)."[$talla[IDTalla]]></td>";
					}
				}
				?>
				<td class=row1 align=center>
					
				</td>	
			</tr>
	<?php 
	
	$r_detalle = array();
	$r_codificacion = array();
	
	}//end while( $r_referencias = db_fetch_object( $query_referencias ) )
	?>
	</table>
<?php 
}// end function verdetallesugerido($id)
?>