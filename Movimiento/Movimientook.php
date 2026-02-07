<body> <?php

$TitleMod ="Entrada de Pedidos";

$Table = "Pendientes";
$TableJoin = "";
$Key = "IDPendientes";
$MOD = "Movimiento";
$m = "Movimientos";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form($id,"insert","Actualizar $TitleMod","Realizar Movimiento");
			break;
			
			case "insert" :
				$frm= vars_LOG($HTTP_POST_VARS);
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");
				
				//print_r($HTTP_POST_VARS);
				
				foreach($Ingreso as $key => $valor)
				{
					
					//Actualizar Existencias
				
					$existencias = get_field("CodificacionEspecifica","Existencias", "IDPuntoVentaReferencia",$IDPuntoVentaReferencia[$key]."' AND IDTalla = '$IDTalla[$key]" );
					$existencias = $existencias + $valor;
					
					$maximo = get_field("CodificacionEspecifica","Maximo", "IDPuntoVentaReferencia",$IDPuntoVentaReferencia[$key]."' AND IDTalla = '$IDTalla[$key]" );
					
					if( ( $valor > 0 ) && ( $existencias <= $maximo ) )
					{
						$cantidadactualizar = $Cantidad[$key] - $valor;
						
						if( $cantidadactualizar > 0 )
						{
						
							$sql_actualizar = " UPDATE Pendientes SET CantidadPendiente = '$cantidadactualizar' WHERE IDPendientes = '$IDPendientes[$key]' ";	
							db_query($sql_actualizar);
						
							//insertar el log
							insertlog($ID_Usuario,"Pendientes",$IDPendientes[$key],"Actualizar",$sql_actualizar); 
							
							
						}//end if( $cantidadactualizar > 0 )
						else
						{
							
							//borrar los pendientes para no saturar la pantalla
							$sql_borrar = " DELETE FROM Pendientes WHERE IDPendientes = '$IDPendientes[$key]' ";	
							db_query($sql_borrar);
						
							//insertar el log
							insertlog($ID_Usuario,"Pendientes",$IDPendientes[$key],"Borrar",$sql_borrar); 
						
						}//end else
						
						//insertar entrada
						$identrada = get_maxID("Entrada","IDEntrada");
						$sql_entrada = "INSERT INTO Entrada VALUES('$identrada','$frm[Remision]','$frm[NumeroFactura]','$frm[Fecha]','$IDPuntoVentaReferencia[$key]','$IDTalla[$key]','$valor',NOW(),'$IDPuntoVenta')";
						db_query($sql_entrada);
						
						
						
						$sql_actualizacod = "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDPuntoVentaReferencia = '$IDPuntoVentaReferencia[$key]' AND IDTalla = '$IDTalla[$key]'";
						db_query( $sql_actualizacod );
						
						//insertar el log
						$idcodificacion = get_field("CodificacionEspecifica","IDCodificacionEspecifica", "IDPuntoVentaReferencia",$IDPuntoVentaReferencia[$key]."' AND IDTalla = '$IDTalla[$key]" );
						insertlog($ID_Usuario,"CodificacionEspecifica",$idcodificacion,"Actualizar",$sql_actualizacod);
						
						$existencias = 0;
					
					}//end if( $valor > 0 )
					
				}//end foreach($ingreso as $key => $valor)
				
				/*$frm['ID'] = insert_width_table($frm,"Movimiento","IDMovimiento");
				entradapedido($frm);*/
				db_query("COMMIT");
				
				echo "<script>location.href='?mod=verentrada';</script>";
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Movimiento");
			break ;
			case "update" :
				entradapedido($HTTP_POST_VARS);
				echo "<script>location.href='?mod=Movimiento';</script>";
			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$HTTP_GET_VARS[action]="";
				delete($ID);
			break;
			case "list" :	
				
				if( $field == 'Talla.Descripcion')
				{
					$sql = "SELECT Pendientes.* FROM Pendientes, Talla WHERE Talla.Descripcion LIKE '%$QryString%' AND Talla.IDTalla = Pendientes.IDTalla AND IDPuntoVenta = '$IDPuntoVenta' GROUP BY IDPendientes ORDER BY IDPuntoVentaReferencia ";
				}else
				{
					$sql = make_qry_string($HTTP_GET_VARS);
				}
				
				list_r($sql);
			break;
			case "listar":
				//print_r( $HTTP_POST_VARS );
				list_r($HTTP_POST_VARS);
			break;
			default : 
				previoentrada();
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");



/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($frm){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$IDPuntoVenta;
		
	 	$sql =  "SELECT * FROM Pendientes WHERE  IDPuntoVenta = '$IDPuntoVenta' AND CantidadPendiente > 0 AND IDPuntoVentaReferencia IN ($frm[Referencias]) GROUP BY IDPuntoVentaReferencia ORDER BY IDPuntoVentaReferencia ASC";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=20;
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
							

	if($rows > 0){
	
	$i = 0;
	while( $r = db_fetch_array( $result ) )
	{
		$array_referencias[$i] = $r;
		$sql_tallas = " SELECT * FROM Pendientes WHERE IDPuntoVenta = '$IDPuntoVenta' AND CantidadPendiente > 0 AND IDPuntoVentaReferencia = '$r[IDPuntoVentaReferencia]' ";
		$qry_tallas = db_query( $sql_tallas );
		$j = 0;
		while( $r_tallas = db_fetch_array( $qry_tallas ) )
		{
			$array_tallas[$r[IDPuntoVentaReferencia]][$j] = $r_tallas;
			$j++;
		}//end while
		
		if( $j > $colspan )
			$colspan = $j;
		
		
		
		$i++;
	}//end while
	
	
?>
	<br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="600">
	
		<tr>
			<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
			</td>
			<td class="tbtbot"><b></b>
				<span class="gen">
					<?php echo $TitleMod." - ".$info ?>
				</span>
			</td>
			<td class="tbtr">
				<img src="images/spacer.gif" alt="" width="124" height="22" />
			</td>
		</tr>
	</table>
	
	
	<table class="forumline" width="600" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<?php
			filtrar();
		?>
	</td>
	</tr>
	<tr>
	<td>
		<form name="frm" action="<?=$PHP_SELF?>" method="post" >
		<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
				<tr>
					<td class="col1" nowrap>Numero de Remisi&oacute;n</td>
					<td class="col2"><input type="input" name="Remision" readonly value="<?=$frm[Remision]?>" class="tbox" id="Remision"></td>
					<td class="col1" nowrap>Numero de Factura</td>
					<td class="col2"><input type="input" name="NumeroFactura" readonly value="<?=$frm[NumeroFactura]?>" class="tbox"></td>
				</tr>
				<tr>
					<td class="col1" nowrap>Fecha</td>
					<td class="col2" nowrap>
						<input type="input" name="Fecha" readonly value="<?=$frm[Fecha]?>" id="Fecha" class="tbox">
					</td>
					<td class="col1" colspan="2">
								
					</td>
				</tr>

		</table>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
					<tr>
						<td class=navpic align="center" nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=Referencia.Numero&in_order=".$order."&listar=".$nav->limit."&tjoin=PuntoVentaReferencia&action=list"; ?>' style="text-decoration: none;">REFERENCIA</a><a style="text-decoration: none;" href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Referencia.Numero&tjoin=PuntoVentaReferencia&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>&nbsp;<?php if($_GET['order_by']=="Referencia.Numero")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a></td>
							<td class=navpic nowrap bgcolor=#DBEAF5 align="center" colspan = "<?=$colspan?>">TALLAS</td>
					</tr>
	
				<?php 
				foreach( $array_referencias as $key => $valor ){
				
					$class = repetition()?"col1list":"col2list";
					$tamanoarray = count( $array_tallas[$valor[IDPuntoVentaReferencia]] );
					$columnasmas = $colspan - $tamanoarray;
				?>
	  	
					<tr>
						<td nowrap class="<?=$class?>"></td>
						<?php
						foreach( $array_tallas[$valor[IDPuntoVentaReferencia]] as $preferencia => $datos )
						{
						?>
						<td nowrap class="<?=$class?>"><b><?php echo get_field("Talla","Descripcion","IDTalla",$datos[IDTalla]); ?></b></td>
						<?php
						}
						//anadir las columnas que falten (DISENO)
						for( $i = 0; $i<$columnasmas; $i++ )
						{
						?>
							<td nowrap class="<?=$class?>">
							</td>
						<?php
						}//end for
						?>
						
					</tr>
					
					<tr>
						<td nowrap class="<?=$class?>"><?php echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$valor[IDPuntoVentaReferencia]))?></td>
						<?php
						foreach( $array_tallas[$valor[IDPuntoVentaReferencia]] as $preferencia => $datos )
						{
						?>
							<td nowrap class="<?=$class?>">
								<?php echo number_format($datos[CantidadPendiente]); ?><br>
								<input type="hidden" name="Cantidad[<?=$datos[IDPendientes]?>]" value="<?=$datos[CantidadPendiente]?>">
								<input type="hidden" name="IDPuntoVentaReferencia[<?=$datos[IDPendientes]?>]" value="<?=$datos[IDPuntoVentaReferencia]?>">
								<input type="hidden" name="IDTalla[<?=$datos[IDPendientes]?>]" value="<?=$datos[IDTalla]?>">
								<input type="text" size="5" name="Ingreso[<?=$datos[IDPendientes]?>]">
								<input type="hidden" name="IDPendientes[<?=$datos[IDPendientes]?>]" value="<?=$datos[IDPendientes]?>">
							</td>

						<?php
						}
						//anadir las columnas que falten (DISENO)
						for( $i = 0; $i<$columnasmas; $i++ )
						{
						?>
							<td nowrap class="<?=$class?>">
							</td>
						<?php
						}//end for
						?>
						
					</tr>
				<?php } // END for
				?>
					<tr>
							<td  bgcolor=#DBEAF5 colspan = "<?=$colspan+1?>" nowrap class="navpic" align="center">
							<?php
								print $pages;
							?>
							<input type="hidden" name="action" value="insert">
							<input type="submit" class="button" name="enviar" value="Realizar Entradas">
						</td>						
						
						</td>
					</tr>		
				</table>
		</form>
			</td>
		</tr>
	</table>
	<?php 			
}// End if$rows
else
	echo "<br><br><span class=subtitle><b>No hay pedidos pendientes </b></span>";
}// Enf function list()				


/*******************************************************************************************
		funcion previo entrada
*******************************************************************************************/
	function previoentrada($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$IDPuntoVenta;
	
	$sql_referencias = "SELECT R.IDReferencia, R.Numero, PVR.IDPuntoVentaReferencia
							FROM Referencia R, PuntoVentaReferencia PVR, Pendientes P 
							WHERE PVR.IDPuntoVenta = '$IDPuntoVenta' 
							AND PVR.IDReferencia = R.IDReferencia
							AND PVR.IDPuntoVentaReferencia = P.IDPuntoVentaReferencia";
							
	$qry_referencias = db_query( $sql_referencias );
	$i = 0;
	while( $r_referencias = db_fetch_array( $qry_referencias ) )
	{
		$array_referencias[$i] = $r_referencias; 
		$i++;
	}//end while
	
?>
	<script>
	<!--
	function addSelect(newTxt, newVal, num) {
	  newOption = new Option(newTxt, newVal, false, false);
	  document.frm.Referencias.options[document.frm.Referencias.length] = newOption;
	}	
	
	function removeitem(PopName) {
		var boxLength = PopName.length;
		arrSelected = new Array();
		var count = 0;
		for (i = 0; i < boxLength; i++) {
			if (PopName.options[i].selected) {
			arrSelected[count] = PopName.options[i].value;
			}
			count++;
		}
		var x;
		for (i = 0; i < boxLength; i++) {
			for (x = 0; x < arrSelected.length; x++) {
				if (PopName.options[i].value == arrSelected[x]) {
					PopName.options[i] = null;
		  		 }
			}
			boxLength = PopName.length;
		}
	}
	
	
	function setSelectOptions(PopName)
	{
	   strValues = "";
	   
	     for (var i = 0; i < PopName.length; i++){
	         
	         PopName.options[i].selected = 'TRUE';
	    	if (i == 0)
	    	{ 
				strValues = PopName.options[i].value;
				
	      	}
	        else
	        {
	         strValues = strValues + "," + PopName.options[i].value;
			}
		}
		
		if (i == 0) {
			newoption = new Option('', '', false, false);
			PopName.options[0] = newoption;
		}
		else
			PopName.options[i-1].value = strValues;
		
	    
	    
	    return true;
	}
	var Check = new Array('Remision','Fecha');

	//-->
	</script>
	<br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="600">
	
		<tr>
			<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
			</td>
			<td class="tbtbot"><b></b>
				<span class="gen">
					<?php echo $TitleMod." - ".$info ?>
				</span>
			</td>
			<td class="tbtr">
				<img src="images/spacer.gif" alt="" width="124" height="22" />
			</td>
		</tr>
	</table>
	
	
	<table class="forumline" width="600" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="setSelectOptions(document.frm.Referencias);return EvaluaReg(this,Check)">
			<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
				<tr>
					<td class="col1" nowrap>Numero de Remisi&oacute;n</td>
					<td class="col2"><input type="input" name="Remision" class="tbox" id="Remision"></td>
					<td class="col1" nowrap>Numero de Factura</td>
					<td class="col2"><input type="input" name="NumeroFactura" class="tbox"></td>
				</tr>
				<tr>
					<td class="col1" nowrap>Fecha</td>
					<td class="col2" nowrap><input type="input" name="Fecha" id="Fecha" class="tbox">								<script language="JavaScript1.2">									<!--										if (!document.layers)											document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")																//-->								</script>							</td>
					<td class="col1" colspan="2">
								
					</td>
				</tr>
				<tr>
					<td class="col2" colspan="4">
						Agregue las Referencias a ingresar el pedido y haga clic en 'Continuar'.
					</td>
				</tr>
				<tr>
					<td  colspan="4">
						<table cellpadding="1" align=center cellspacing="1" width="100%">									<td class="col1list">Para agregar una referebcia haga <a href="javascript:;" onclick="window.open('Referencia/poppendientes.php','','width=600,height=500'); this.value=''">click aqu&iacute; </a><br>										<select name="Referencias" style="width:180px; " size="20" multiple class="inputSelect" id="Referencias"></select><br>										</a>Para eliminar una referencia haga <a href="JavaScript:removeitem(document.frm.Referencias);" class="tex-menu-sup">click aqu&iacute; </a><br>									</td>								</table>
					</td>
				</tr>						<tr>							<td colspan="4" align="center" class="col1list">								<input type="hidden" name="action" value="listar">								<input type="submit" value="Continuar" name="enviar">							</td>						</tr>					</table>
		</form>
			</td>
		</tr>
	</table>
<?php
}// Enf function previoentrada()				


/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD,$IDPuntoVenta;
?>
	<form name="frm" action="./" method="get" onsubmit="return valbuscar(document.frm)">
		<tr>
			<td class="rowform" align="center" colspan=8>
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
					<option value="Referencia.Numero">Referencia</option>
					<option value="Talla.Descripcion">Talla</option>
				</select> 
				<input type="text" size="10" name="QryString" id="Buscar Por" class="post"> 
				
				
				ordenar por 
				<select name="order_by" class="popup">
					<option value="PuntoVentaReferencia.IDReferencia">Referencia</option>
					<option value="CantidadPendiente">CantidadPendiente</option>
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
				<input type="hidden" name="rangofield" value="FechaOrden">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="PuntoVentaReferencia">
				<input type="hidden" name="IDPuntoVenta" value="<?=$IDPuntoVenta?>">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php
	}//End function filtrar
?>