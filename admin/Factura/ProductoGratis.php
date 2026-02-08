<body> <?php 

$TitleMod ="Referencia";

$Table = "Referencia";
$TableJoin = "CodificacionEspecifica";
$Key = "IDReferencia";
$MOD = "ProductoGratis";
$m="Referencia";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
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

	GLOBAL $TitleMod,$Table,$MOD,$Key;
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
		if (e.name != 'allbox')
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
			<td>&nbsp;</td>
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
						<tr class=row2>
							<td colspan="2">Si la referencia es producto de una operaci&oacute;n de 'segunda' indique el movimiento aqu&iacute;.</td>
						</tr>
						<tr class=row2>
							<td>Movimiento Segundas</td>
							<td><input type=text size=25 class=input   name=FechaMovimiento id=Numero value="<?php echo $r_movimiento->Fecha ?>"><input type=hidden name=IDMovimiento id=IDReferencia value="<?php echo $r->IDIDMovimiento ?>"><input type="button" name="Segunda" value="Segunda" onClick="window.open( 'Movimiento/popMovimiento.php','','width=600, height=500' );" class=submit></td>
						</tr>
						<tr class=row2>
							<td width="50%">Numero<br>
								<input type=text size=25 class=input   name=Numero id=Numero value="<?php echo $r->Numero ?>"></td>
							<td valign="top">
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
							<td width="50%">Nombre<br>
								<input type=text size=25 class=input   name=Nombre id=Nombre value="<?php echo $r->Nombre ?>"></td>
							<td>Lista de Precios<br>
								<select name="IDPrecio" class="input">
								<option value="">Seleccione...</option>
								<?php 
								
								$sql_precio = " SELECT * FROM Precio WHERE Publicar = 'S' ORDER BY ValorVenta ";
								$qry_precio = db_query( $sql_precio );
								while( $r_precio = db_fetch_object( $qry_precio ) )
								{
									echo "<option value='".$r_precio->IDPrecio."'";
									if( $r_precio->IDPrecio == $r->IDPrecio   )
									{
										echo " selected ";
									}//end if
									echo " >".$r_precio->IDPrecio." - ".$r_precio->ValorVenta." - ".$r_precio->Descuento."%</option>"; 
								}//end while
								?>
								</select>
						</tr>
						<tr class=row2>
			<td width="50%">Proveedor<br>
								<?php echo formpopup("Proveedor","Nombre","Nombre","IDProveedor",$r->IDProveedor,"input\" id=\"Proveedor"); ?></td><td>Talla <br>
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


								</select></td>
			</tr>
						<tr class=row2>
			<td width="50%">Color<br>
								<?php echo formpopup("Color","DescripcionLarga","DescripcionLarga","IDColor",$r->IDColor,"input\" id=\"Color"); ?></td><td>Linea<br>
								<select name=IDLinea>
								<option value="">[ Seleccione ]</option>
								<?php 
									$sql_tipo = "SELECT * FROM Tipo ORDER BY Descripcion";
									$query_tipo = db_query($sql_tipo);
									while($r_tipo = db_fetch_object($query_tipo))
									{
										echo "<option value=''>----".$r_tipo->Descripcion."</option>";
										$sql_linea = "SELECT * FROM Linea WHERE IDTipo = '$r_tipo->IDTipo'";
										$query_linea = db_query($sql_linea);
										while ( $r_linea = db_fetch_object($query_linea) )
										{
											echo "<option value=$r_linea->IDLinea";
											if($r->IDLinea == $r_linea->IDLinea) echo " selected ";
											echo ">".$r_linea->Nombre."</option>";
										}
									}
								?>
								</select></td>
			</tr>
						<tr class=row2>
			<td width="50%">Cuero<br>
								<?php echo formpopup("Cuero","DescripcionLarga","DescripcionLarga","IDCuero",$r->IDCuero,"input\" id=\"Cuero"); ?></td><td> Publicar<br>
								 <?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Publicar, 'Publicar'); ?></td>
			</tr>
						<tr class=row2>
							<td width="50%">Tipo de Referencia<br>
								<?php echo formpopup("TipoReferencia","Descripcion","Descripcion","IDTipoReferencia",$r->IDTipoReferencia,"input\" id=\"Tipo de Referencia"); ?></td>
							<td>Reportes<br>
                              <?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Reportes, 'Reportes'); ?></td>
		  </tr>
						<tr class=row3>
							<td colspan="2">
								<b>PUNTOS DE VENTA</b>
							</td>
						</tr>
						<tr class=row2>
							<td colspan="2">
								<br>
								<?php 
									table_check_list($Table,$Key,$r->$Key,"PuntoVenta","IDPuntoVenta","PuntoVentaReferencia","PuntoVenta[]",$newmode);
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

				// selecciono las facturas que contienen la promocion	
			$sql_referencia_promocion=db_query("SELECT IDFactura FROM Factura where ObservacionDescuento = 'pague 2 lleva 3'");
			while($result_referencia=db_fetch_array($sql_referencia_promocion)){
				$array_referencia[]=$result_referencia[IDFactura];
			}
			
			$id_factura=implode(",",$array_referencia);
			if(empty($id_factura))
				$id_factura=0;		

		
	if(empty($sql))
	 	$sql =  "SELECT * 
				FROM DetalleFactura
				WHERE IDFactura
				IN (
					SELECT IDFactura
					FROM  `Factura` 
					WHERE  `ObservacionDescuento` LIKE  'pague 2 lleva 3'
				)
				AND DescuentoPar >25";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=30;
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
		<td>&nbsp;</td>
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
	<td class=titlemedium  bgcolor=#9daac6><a href="Factura/exportar_productos.php"> <img src="images/excel_icon.gif" border="0" width="20" height="20"></a> Exportar resultado</td>
</tr>


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
				<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Numero&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Numero<?php  if($_GET['order_by']=="Numero"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
				<td class=rowform nowrap bgcolor=#DBEAF5>Factura</td>
				<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDProveedor&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Punto Venta&nbsp;
			    <?php  if($_GET['order_by']=="IDProveedor"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
				<td class=rowform nowrap bgcolor=#DBEAF5>PrecioU</td>
				  </tr>

<?php while($r = db_fetch_object($result)){
?>
  	
<tr>
<td nowrap class=row1><?php echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r->IDCodificacionEspecifica))); ?></td>
<td nowrap class=row1>
	<a href="?mod=Factura_3_X_2&action=edit&id=<?php echo $r->IDFactura; ?>&idpunto=<?php echo $r->IDPuntoVenta ?>">
		<?php 
			echo get_field("Factura","NumeroFactura","IDFactura",$r->IDFactura);
        ?>
    </a>    
</td>
<td nowrap class=row1><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta) ?></td>
						<td nowrap class=row1><?php echo "$".number_format($r->PrecioU); ?></td>
					</tr>
<?php } // END for
?>
<tr>
						<td class=texto bgcolor=#DBEAF5 colspan=4 nowrap>
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
					<option value="Factura.NumeroFactura">Factura</option>
				</select> 
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post"> 
				ordenar por 
				<select name="order_by" class="popup">
					<option value="Numero">Numero</option>
					<option value="Factura.NumeroFactura">Factura</option>
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
				<input type="hidden" name="tjoin" value="Factura">				
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php 		
	}//End function filtrar
?>
