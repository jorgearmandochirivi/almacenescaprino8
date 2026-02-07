<body> <?php

$TitleMod ="Entradas";

$Table = "Entrada";
$TableJoin = "";
$Key = "IDEntrada";
$MOD = "Entrada";
$m = "VerMovimientos";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
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
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table ORDER BY Fecha DESC";
	 	
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
							
							?><?php
	if($rows > 0){
?><br>
	<br>
	<br>
	<table width=700 cellpadding=0 cellspacing=0 align=center class=bordertable>
		<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
		</tr>
	<?php filtrar();?>	
	<tr>
			<td class=titlemedium  bgcolor=#9daac6><?php echo $info;;?></td>
		</tr>
	<tr>
	<td class=texto bgcolor=#DBEAF5 colspan=11 nowrap>
	<?php
		print $pages;
	?>
	</td>
	</tr>
		<tr>
			<td>
<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
					<tr>
						<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=IDPuntoVenta&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Punto de Venta</a></td>
						<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=Remision&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Remisi&oacute;n</a><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Remision&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>&nbsp;<?php if($_GET['order_by']=="Remision")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a></td>
						<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=NumeroFactura&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Numero Factura</a><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=NumeroFactura&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>&nbsp;<?php if($_GET['order_by']=="NumeroFactura")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a></td>
							<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=Referencia.Numero&tjoin=PuntoVentaReferencia&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Referencia</a><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Referencia.Numero&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>&nbsp;<?php if($_GET['order_by']=="Referencia.Numero")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a></td>
							<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=IDTalla&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Talla</a><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDTalla&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'><?php if($_GET['order_by']=="IDTalla")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a></td>
						<td class=navpic nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=Cantidad&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Cantidad</a><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Cantidad&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'><?php if($_GET['order_by']=="Cantidad")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a></td>
						<td class=navpic nowrap bgcolor=#DBEAF5>&nbsp;&nbsp;&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Fecha</a><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Fecha&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'><?php if($_GET['order_by']=="Fecha")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a></td>
					</tr>
	
				<?php 
				while($r = db_fetch_object($result)){
				
					$class = repetition()?"col1list":"col2list";
				?>
	  	
					<tr>
						<td align="right" style="text-align:right;" nowrap class="<?=$class?>"><a href="javascript:;" onClick="window.open( 'Movimiento/popEntradas.php?Remision=<?=$r->Remision?>&IDPuntoVenta=<?=$r->IDPuntoVenta?>','','width=500, height=500, scrollbars=1, resize=yes' )"><?php echo  get_field( "PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta ) ; ?></a></td>
						<td align="right" style="text-align:right;" nowrap class="<?=$class?>">
								<a href='javascript:;' onClick="window.open( 'Movimiento/popEntradas.php?Remision=<?=$r->Remision?>&IDPuntoVenta=<?=$r->IDPuntoVenta?>','','width=500, height=500, scrollbars=1, resize=yes' )"><?php echo  $r->Remision ; ?></a>
							</td>
						<td align="right" style="text-align:right;" nowrap class="<?=$class?>"><?php echo  $r->NumeroFactura ; ?></td>
							<td align="right" style="text-align:right;" nowrap class="<?=$class?>"><?php echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$r->IDPuntoVentaReferencia))?></td>
							<td nowrap class="<?=$class?>"><?php echo get_field("Talla","Descripcion","IDTalla",$r->IDTalla); ?></td>
						<td align="right" style="text-align:right;" nowrap class="<?=$class?>">
							<?php echo number_format($r->Cantidad); ?>   </td>
						<td align="left" style="text-align:left;" nowrap class="<?=$class?>">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo formatofecha(substr($r->Fecha,0,10))." a las ".substr($r->Fecha,10)?>
						</td>
					</tr>
				<?php } // END for
				?>
					<tr>
						<td class=col1 bgcolor=#DBEAF5 nowrap></td>
						<td class=col1 bgcolor=#DBEAF5 colspan=6 nowrap><?php
								print $pages;
							?><input type="hidden" name="action" value="insert"></td>
						
						
						
						
						
						</td>
		</tr>		
				</table>			</td>
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
					<option value="Remision">Remision</option>
                    <option value="NumeroFactura">Numero de Factura</option>
					<option value="PuntoVenta.Nombre">Nombre Punto</option>
				</select> 
				<input type="text" size="20" name="QryString" id="Buscar por" class="post"> 
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
					<option value="Remision" selected>Remision</option>
					<option value="Fecha">Fecha</option>
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
					<option value="50" selected>50</option>
				</select> 
				<br>
				<input type="hidden" name="mod" value="<?=$MOD?>">
				<input type="hidden" name="rangofield" value="Fecha">
				<input type="hidden" name="tjoin" value="PuntoVenta">
				<input type="hidden" name="action" value="list">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php
	}//End function filtrar
	
/*******************************************************************************************
	verdetallemovimiento: Muestra el detalle de el Movimiento de Entrada Contra Pedido
	Parametros:
			$id : id del Pedido a Mostrar
	Retorna:	
			Void
*******************************************************************************************/

function verdetallemovimiento($id)
{
	
	Global $TitleMod,$MOD,$Table,$Key,$TableJoin;
	
	echo $sql_referencias =  "SELECT * FROM $TableJoin WHERE $Key = '$id' GROUP BY IDPuntoVentaReferencia";
	
	$query_referencias = db_query( $sql_referencias );
	
	$i=0;
?>
	<table width=80% cellpadding=1 cellspacing=0 align=center bgcolor=#DEE3E7 class=bordertable>
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
		
		//Query Para ver la codificacion especifica
		
		
		$sql_codificacion =  "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$r_referencias->IDPuntoVentaReferencia'";
		$query_codificacion = db_query($sql_codificacion);
		$rows_codificacion = db_num_rows($query_codificacion);
		
		while($r_codificacion[$i] = db_fetch_array($query_codificacion))
		{
			$i++;
		} //end while($r[$i] = db_fetch_array($query_codificacion))
		
		$i = 0;
		//print_r($r);
		
?>
		
			<tr>
				<td class=rowform align=center>
					EXISTENCIAS
				</td>
				<?php
					foreach($r_codificacion as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=rowform align=center>".$talla[Existencias]."</td>";
					}//end foreach($r_detalle as $talla)
				?>
			</tr>
			
			
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
			</tr>
			
			<tr>
				<td class="row1" align=center>
					<b>CANTIDAD PEDIDA</b>
				</td>
				<?php
				foreach($r_detalle as $talla)
				{
					if(!empty($talla[IDTalla]))
					{
						echo "<td class=row1 align=center><input type=text readonly size=5 value=".$talla[Cantidad]." name=ref".$r_referencias->IDPuntoVentaReferencia."[$talla[IDTalla]]>";
					}
				}
				?>
			</tr>
			
			<tr>
				<td class="row2" align=center>
					<b>CANTIDAD RECIBIDA</b>
				</td>
				<?php
				foreach($r_detalle as $talla)
				{
					if(!empty($talla[IDTalla]))
					{
						
						$sql_cantidadrecibida  = "SELECT SUM(DM.Cantidad) as CantidadRecibida FROM DetalleMovimiento DM,Movimiento M, Ordencompra O ";
						$sql_cantidadrecibida .= "WHERE O.IDOrdenCompra = M.IDOrdenCompra AND O.IDOrdenCompra = '$talla[IDOrdenCompra]' ";
						$sql_cantidadrecibida .= "AND M.IDMovimiento = DM.IDMovimiento AND DM.IDPuntoVentaReferencia = '$talla[IDPuntoVentaReferencia]' AND DM.IDTalla = '$talla[IDTalla]'";
												
						$query_cantidadrecibida = db_query( $sql_cantidadrecibida );
						
						$r_cantidadrecibida = db_fetch_object( $query_cantidadrecibida );
						
						echo "<td class=row2 align=center><input type=text size=5 readonly value='".$r_cantidadrecibida->CantidadRecibida."' name='CantidadRecibida[$talla[IDTalla]]'>";
					}
				}
				?>
			</tr>
			
			<tr>
				<td class="row1" align=center>
					<b>INGRESO</b>
				</td>
				<?php
				foreach($r_detalle as $talla)
				{
					if(!empty($talla[IDTalla]))
					{
						echo "<td class=row1 align=center><input type=text size=5 value=".$talla[Cantidad]." name=".$r_referencias->IDPuntoVentaReferencia."[$talla[IDTalla]]>";
						//SE IMPRIME UN HIDDEN CON EL ID DE LAS TALLA
						//echo "<input type=hidden value=".$talla[IDTalla]." name=Talla".get_field("Referencia","Numero","IDReferencia",$r_referencias->IDReferencia)."[$talla[IDTalla]]></td>";
					}
				}
				?>
			</tr>
			<tr>
				<td class="row2" align=center><br>
				</td>
				<?php
				foreach($r_detalle as $talla)
				{
					if(!empty($talla[IDTalla]))
					{
						echo "<td class=row2 align=center></td>";
					}
				}
				?>
			</tr>
	<?php
	
	$r_detalle = array();
	$r_codificacion = array();
	
	}//end while( $r_referencias = db_fetch_object( $query_referencias ) )
	?>
	</table>
<?php
}// end function verdetallepedido($id)
?>