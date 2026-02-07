<body> <?php

$TitleMod ="Ajustes de Inventario";

$Table = "Ajuste";
$TableJoin = "DetalleAjuste";
$Key = "IDAjuste";
$MOD = "Ajuste";
$m = "Ajustes";
$permisos = get_permiso($ID_Usuario,$m,$Table);


if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form($id,"insert","Actualizar $TitleMod","Realizar Movimiento");
			break;
			
			case "insert" :
				$frm= vars_LOG($_POST);
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");
				
				//print_r($_POST);
				//print_r( $_POST );
				
				//Insertar Cabecera de Ajuste
				$IDAjuste = get_maxID( "Ajuste WHERE IDPuntoVenta = '$IDPuntoVenta'","IDAjuste" );
				$sql_cabecera = " INSERT INTO Ajuste (  IDAjuste, NumeroAjuste, IDPuntoVenta, Observaciones, FechaAjuste,
									 UsuarioTrCr, FechaTrCr) VALUES ( '$IDAjuste','$IDAjuste','$IDPuntoVenta',
									'$Observaciones','$FechaAjuste','$Nombre_Usuario',NOW() )  ";
				db_query( $sql_cabecera );
				
				foreach( $Ingreso as $Codificacion => $Cantidad )
					if( $Cantidad <> 0 )
					{
						$Numero = $Referencia[$Codificacion];
						$Talla = $Tallas[$Codificacion];
						//Realizar operación de invetnario
						$sql_inventario = " UPDATE CodificacionEspecifica SET Existencias = Existencias + ( ".$Cantidad." ) 
											WHERE IDCodificacionEspecifica = '$Codificacion' ";
						db_query( $sql_inventario );
						
										
						//Insertar detalle del ajuste
						
						$IDDetalleAjuste = get_maxID( "DetalleAjuste WHERE IDAjuste = '$IDAjuste'","IDDetalleAjuste" );
						$sql_detalle = "INSERT INTO DetalleAjuste ( IDDetalleAjuste, IDAjuste, IDPuntoVenta, IDCodificacionEspecifica,
											Cantidad, UsuarioTrCr, FechaTrCr, Numero, Talla ) VALUES ( '$IDDetalleAjuste','$IDAjuste','$IDPuntoVenta',
											'$Codificacion','$Cantidad','$Nombre_Usuario',NOW(), '$Numero', '$Talla' )";
						db_query( $sql_detalle );
						
					}//end for
				
				//db_query( "tales" );
				
				db_query("COMMIT");
				
				if( $msg <> 1 )
					echo "<script>location.href='?mod=verentrada';</script>";
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Movimiento");
			break ;
			case "update" :
				entradapedido($_POST);
				echo "<script>location.href='?mod=Movimiento';</script>";
			break;
			case "verdetalle":
				verdetalle($id);
			break ;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$_GET[action]="";
				delete($ID);
			break;
			case "list" :	
			
				if( $field == "IDPuntoVenta" )
				{
				
					$sql = " SELECT * FROM  Referencia R, PuntoVentaReferencia PR 
	 				WHERE  PR.IDPuntoVenta = '$QryString' 
	 				AND PR.IDReferencia = R.IDReferencia 
	 				GROUP BY PR.IDPuntoVentaReferencia 
	 				ORDER BY R.Numero ASC " ;
					
				}//end if


				list_r($sql);
			break;
			case "listar":
				//print_r( $_POST );
				list_r($_POST,"entrada");
			break;
			case "entrada":
				//print_r( $_POST );
				list_r($_POST,"insert");
			break;
			default : 
				previoentrada();
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");



/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($frm,$newmode){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$IDPuntoVenta,$_POST;
		
	 	$sql =  "SELECT * FROM  Referencia R, PuntoVentaReferencia PR 
	 				WHERE  PR.IDPuntoVenta = '$IDPuntoVenta' 
	 				AND PR.IDPuntoVentaReferencia IN ($frm[Referencias])
	 				AND PR.IDReferencia = R.IDReferencia 
	 				GROUP BY PR.IDPuntoVentaReferencia 
	 				ORDER BY R.Numero ASC";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=1000;
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
		$sql_tallas = " SELECT * FROM CodificacionEspecifica WHERE   IDPuntoVentaReferencia = '$r[IDPuntoVentaReferencia]' ";
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
	<table class="bordertable" width="600" cellspacing="1" border="0" align="center">
		<tr>
			<td class="maintitle" bgcolor="#9daac6"><b><?php echo $TitleMod ?></b></td>
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
		<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="bordertable" >
				<tr>
					<?php
					if( empty( $frm[IDAjuste] ) )
						$frm[IDAjuste] = get_maxID( "Ajuste WHERE IDPuntoVenta = '$IDPuntoVenta'","IDAjuste" );
					?>
					<td class="row1" nowrap>Numero de Ajuste</td>
					<td class="row2"><input type="input" name="NumeroAjuste" readonly value="<?=$frm[IDAjuste]?>" class="tbox" id="NumeroAjuste"></td>
				</tr>
				<tr>
					<td class="row1" nowrap>Fecha</td>
					<td class="row2" nowrap>
						<input type="input" name="FechaAjuste" readonly value="<?=$frm[FechaAjuste]?>" id="FechaAjuste" class="tbox">
					</td>
				</tr>
						<tr>
							<td class="row1" nowrap>Observaciones</td>
							<td class="row2" nowrap><textarea name="Observaciones" rows="10" cols="50"><?=$frm[Observaciones]?></textarea></td>
						</tr>
					</table>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
					<tr>
						<td class=navpic align="center" nowrap bgcolor=#DBEAF5><a href='<?php echo "?mod=".$MOD."&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=Referencia.Numero&in_order=".$order."&listar=".$nav->limit."&tjoin=PuntoVentaReferencia&action=list"; ?>' style="text-decoration: none;">REFERENCIA</a><a style="text-decoration: none;" href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Referencia.Numero&tjoin=PuntoVentaReferencia&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>&nbsp;<?php if($_GET['order_by']=="Referencia.Numero"){?><img src="images/<?php echo $img;?>" border=0><?php }?></a></td>
							<td class=navpic nowrap bgcolor=#DBEAF5 align="center" colspan = "<?=$colspan+1?>">TALLAS</td>
						</tr>
	
				<?php 
				foreach( $array_referencias as $key => $valor ){
				
					$class = repetition()?"row1list":"row2list";
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
						<td class="<?=$class?>">
						</td>
					</tr>
					
					<tr>
						<td nowrap class="<?=$class?>"><?php echo $LaReferencia = get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$valor[IDPuntoVentaReferencia]))?></td>
						<?php
						$TIngreso = 0;
						foreach( $array_tallas[$valor[IDPuntoVentaReferencia]] as $preferencia => $datos )
						{
						?>
							<td nowrap class="<?=$class?>">
								<?php
								echo $datos[Existencias]."<br>";
								?>
								<input type="hidden" name="Cantidad[<?=$datos[IDPendientes]?>]" value="<?=$datos[CantidadPendiente]?>">
								<input type="hidden" name="IDCodificacionEspecifica[<?=$datos[IDCodificacionEspecifica]?>]" value="<?=$datos[IDCodificacionEspecifica]?>">
								<?php 
									$pend = $datos[IDCodificacionEspecifica]; 
									$Ingr = $_POST[Ingreso];
									$TIngreso += $Ingr[$pend];
									$TPares += $Ingr[$pend];
								?>
								<input type="text" size="5" name="Ingreso[<?=$datos[IDCodificacionEspecifica]?>]"  value="<?php echo $Ingr[$pend] ?>">
								<input type="hidden" name="IDCodificacionEspecifica[<?=$datos[IDCodificacionEspecifica]?>]" value="<?=$datos[IDCodificacionEspecifica]?>">
								<input type="hidden" name="Referencia[<?=$datos[IDCodificacionEspecifica]?>]" value="<?=$LaReferencia?>">
								<input type="hidden" name="Tallas[<?=$datos[IDCodificacionEspecifica]?>]" value="<?=$datos[IDTalla]?>">
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
						<td class="<?=$class?>">
							<input type="text" readonly size="5" name="TIngreso" value="<?=$TIngreso?>" value="">
						</td>
					</tr>
						<?php } // END for
						if( $TPares > 0 )
						{
				?>
						<tr>
							<td  bgcolor=#DBEAF5 colspan = "<?=$colspan+2?>" nowrap class="navpic" align="center">
							Total Pares a ingresar = <?=$TPares?>
							</td>
						</tr>
						<?php
						}
						?>
						<tr>
							<td  bgcolor=#DBEAF5 colspan = "<?=$colspan+2?>" nowrap class="navpic" align="center">
							<?php
								print $pages;
							?>
							<input type="hidden" name="action" value="<?=$newmode?>">
							<input type="hidden" name="IDPuntoVenta" value="<?=$IDPuntoVenta?>">
							<input type="hidden" name="Referencias" value="<?=$frm['Referencias']?>">
							<?php
							if( $newmode == "entrada" )
							{
								$caption = "Realizar Ajuste";
							}
							else
							{
								$caption = "Comfirmar Ajuste";
							}
							?>
							<input type="submit" class="button" name="enviar" value="<?php echo $caption;?>">
						</td>
							<td></td>
						
						</td>
					</tr>		
				</table>
		</form>
			</td>
		</tr>
	</table>
	<?php 			
}// End if$rows

}// Enf function list()				


/*******************************************************************************************
		funcion previo entrada
*******************************************************************************************/
	function previoentrada($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$IDPuntoVenta;
	
	$sql_referencias = "SELECT R.IDReferencia, R.Numero, PVR.IDPuntoVentaReferencia
							FROM Referencia R, PuntoVentaReferencia PVR 
							WHERE PVR.IDPuntoVenta = '$IDPuntoVenta' 
							AND PVR.IDReferencia = R.IDReferencia";
							
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
	
	<?php
	if( empty( $IDPuntoVenta ) )
	{
	?>
	<table width=590 cellpadding=0 cellspacing=0 align=center class=bordertable>
		<form name="frm" action="<?=$PHP_SELF?>" method="post" >
		<tr>
			<td colspan=2 class="maintitle" bgcolor="#9daac6"><b><?php echo $TitleMod ?></b></td>
		</tr>
		<tr>
		<td width="117">Puntos de Venta	</td>
		<td><select name="IDPuntoVenta" onChange="document.frm.submit();" >
				<option value="">Seleccione Un Punto de Venta</option><?php 								
			$qry_punto = db_query("SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre");
			while($punto = db_fetch_object($qry_punto)){
				 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
			}
		?>
			</select>
			<input type=hidden name=mod value=<?=$MOD?>
			</td>
	</tr>
		</form>
	</table>
	<?php
	
	
		verajustes();
	
	}//end if
	else
	{
	?>
	
	<table width=590 cellpadding=0 cellspacing=0 align=center class=bordertable>
		<tr>
		<td width="117">Puntos de Venta	</td>
		<td><select name="IDPuntoVenta" onChange="document.frmPuntoVenta.submit();" >
				<option value="">Seleccione Un Punto de Venta</option><?php 								
			$qry_punto = db_query("SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre");
			while($punto = db_fetch_object($qry_punto)){
				 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
			}
		?>
			</select>
			</td>
	</tr>
	</table>
	<br><br>
	<table class="bordertable" width="600" cellspacing="1" border="0" align="center">
		<tr>
			<td class="maintitle" bgcolor="#9daac6"><b><?php echo $TitleMod ?></b></td>
		</tr>
		<td>
			<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="setSelectOptions(document.frm.Referencias);return EvaluaReg(this,Check)">
				<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto  >
					<tr><?php
					if( empty( $frm[IDAjuste] ) )
						$frm[IDAjuste] = get_maxID( "Ajuste WHERE IDPuntoVenta = '$IDPuntoVenta'","IDAjuste" );
						?>
						<td class="row1" nowrap>Numero de Ajuste</td>
						<td class="row2"><input type="input" name="IDAjuste" class="tbox" id="Ajuste" value="<?=$frm[IDAjuste]?>"></td>
					</tr>
					<tr>
						<td class="row1" nowrap>Fecha de Ajuste</td>
						<td class="row2" nowrap><input type="input" name="FechaAjuste" id="FechaAjuste" class="tbox">

							<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaAjuste,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
						</td>
					</tr>
					<tr>
						<td class="row1" nowrap>Observaciones</td>
						<td class="row2" nowrap><textarea name="Observaciones" rows="10" cols="50"></textarea></td>
					</tr>
					<tr>
						<td class="row2" colspan="2">
						Agregue las Referencias a ingresar el ajuste y haga clic en 'Continuar'.
					</td>
					</tr>
					<tr>
						<td  colspan="2" align="center">
							<table cellpadding="1" align=center cellspacing="1" width="100%">
								<td class="row1list">Para agregar una referebcia haga <a href="javascript:;" onClick="window.open('Movimiento/popajustes.php?IDPuntoVenta=<?=$IDPuntoVenta?>','','width=600,height=500'); this.value=''">click aqu&iacute; </a><br>
									<select name="Referencias" style="width:180px; " size="20" multiple class="inputSelect" id="Referencias"></select><br>
									</a>Para eliminar una referencia haga <a href="JavaScript:removeitem(document.frm.Referencias);" class="tex-menu-sup">click aqu&iacute; </a><br>
								</td>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center" class="row1list"><input type="hidden" name="action" value="listar">
								<input type="hidden" name="IDPuntoVenta" value="<?=$IDPuntoVenta?>">
								<input type="submit" value="Continuar" name="enviar"></td>
					</tr>
				</table>
			</form>
		</td>
		</tr>
	</table>
<?php
	}//end else
}// Enf function previoentrada()				



/*******************************************************************************************
		funcion verajustes
*******************************************************************************************/
	function verajustes(){
		Global $TitleMod,$MOD,$Table,$Key,$listar;
	if(empty($sql))
	 	$sql =  "SELECT * FROM Ajuste ORDER BY FechaAjuste DESC";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=15;
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
	<table width=500 cellpadding=0 cellspacing=0 align=center class=bordertable>
		<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
		</tr>
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
				<table width=100% border=0 cellspacing=1 cellpadding=0>
					<tr>
						<td class=rowform nowrap bgcolor=#DBEAF5>Ver Detalle</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Punto de Venta</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Fecha de Ajuste</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Usuario</td>
					</tr>
	
				<?php 
				while($r = db_fetch_object($result)){
				?>
	  	
					<tr>
						<td nowrap class=row1>
							<a href="javascript:;" onClick="window.open( 'Movimiento/popDetalleAjuste.php?IDAjuste=<?=$r->IDAjuste?>&IDPuntoVenta=<?=$r->IDPuntoVenta?>','','width=700, height=700, scrollbars=yes' )">
							<img src='images/edit.gif' border='0'>
							</a>
						</a>
						<td nowrap class=row1><?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta)?></td>
						<td nowrap class=row1><?php echo $r->FechaAjuste?></td>
						<td nowrap class=row1><?php echo $r->UsuarioTrCr?></td>
					</tr>
				<?php } // END for
				?>
					<tr>
						<td class=texto bgcolor=#DBEAF5 colspan=5 nowrap>
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
}// Enf function verajustes()			






/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD,$IDPuntoVenta;
?>
	<form name="frm" action="./" method="get" onSubmit="return valbuscar(document.frm)">
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