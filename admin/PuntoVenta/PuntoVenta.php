<body> <?php 

$TitleMod ="PuntoVenta";

$Table = "PuntoVenta";
$TableJoin = "PuntoVentaReferencia";
$Key = "IDPuntoVenta";
$MOD = "PuntoVenta";
$m="PuntoVenta";
$permisos = get_permiso($ID_Usuario,$m,$Table);

if($permisos[0] >= 2)
{

		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;

			case "insert" :
				$frm= vars_LOG($_POST);
				$id = insert($frm);

				if(isset($Banco))
					foreach ($Banco as $IDBanco)
					{
						$idpuntoventabanco = get_maxID("PuntoVentaBanco","IDPuntoVentaBanco");
						$qry_PuntoVentaBanco = db_query("INSERT INTO PuntoVentaBanco values('$idpuntoventabanco', '$id','$IDBanco')");
					}

				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :

				$frm= vars_LOG($_POST);
				update($frm);


				if( $Nivel == 0){

					if(isset($PVentaFPago))
					{

						foreach ($PVentaFPago as $key => $valor)
						{

							$sql_actualiza = "UPDATE PuntoVentaBanco SET IDBanco = '$PVentaBanco[$key]', IDFormaPago = '$valor', Comision = '$PVentaComision[$key]' WHERE IDPuntoVentaBanco = '$key'  ";
							$qry_PuntoVentaActual = db_query($sql_actualiza);

						}//end foreach ($Banco as $IDBanco)

					}//end if(isset($Banco))

					if(isset($IdBanco))
					{

						foreach ($IdBanco as $key => $valor)
						{

							$sql_select = "SELECT * FROM PuntoVentaBanco WHERE IDPuntoVenta = '$frm[ID]' AND IDBanco = '$valor' AND IDFormaPago = '$IdFormaPago[$key]'";
							$query_banco = db_query($sql_select);
							if( db_num_rows( $query_banco ) ==  0 )
							{

								$idpuntoventabanco = get_maxID("PuntoVentaBanco","IDPuntoVentaBanco");
								$sql_insert = "INSERT INTO PuntoVentaBanco values('$idpuntoventabanco', '$id','$valor','$IdFormaPago[$key]','$Comision[$key]' )";
								$qry_PuntoVentaBanco = db_query($sql_insert);

							}//end if( db_num_rows( $query_banco ) ==  0 )

						}//end foreach ($Banco as $IDBanco)

					}//end if(isset($Banco))
				}

				//print_r($_POST);

				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");

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
			case "delfpago" :
			$sql_deletefpago = "DELETE FROM PuntoVentaBanco WHERE IDPuntoVentaBanco = '$idfpago'";
			db_query($sql_deletefpago);
			echo "<script>location.href='?mod=$MOD&action=update&id=$id';</script>";
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
var Check = new Array('IDEmpleado','IDCiudad','IDTipoPuntoVenta','Nombre','Codigo','Direccion','Telefono','Publicar');

function addCell(label){
	var cell = document.createElement("TD");
	if(label)
		cell.innerHTML = label;

	cell.setAttribute("align","center");

	return cell;
}
function addInput(size,type,name,value,keypress, blur){
	var input =  document.createElement("INPUT");
	if(keypress==1)
		input.setAttribute("onKeyPress","if((event.keyCode < 48 || event.keyCode > 57) ) event.returnValue = false;");
	if(blur==1)
		input.setAttribute("onblur","CalculaMontoTotalIngreso(this);");
	if(keypress==2)
		input.setAttribute("onKeyPress","return KeyCheck(this,window.event.keyCode);");
	if(blur==2)
		input.setAttribute("onblur","formatCurrency(this);CalculaMontoTotalIngreso(this);");
	input.setAttribute("class","input");
	input.setAttribute("size",size);
	input.setAttribute("type",type);
	input.setAttribute("name",name);
	input.setAttribute("value",value);

	return input;
}

function addSelectF(size,type,name,value,keypress, blur){
	var input =  document.createElement("SELECT");
	input.setAttribute("name",name);

	<?php 
	$sql_formapago = "SELECT * FROM FormaPago";
	$query_formapago = db_query($sql_formapago);
	$i = 0;
	while( $r_formapago = db_fetch_object( $query_formapago ) )
	{

		echo "var option".$i." = document.createElement('OPTION');";
		echo "option".$i.".setAttribute('value','".$r_formapago->IDFormaPago."'); ";
		echo "option".$i.".innerHTML = '".$r_formapago->Descripcion."';";
		echo "input.appendChild(option".$i.");";

		$i++;

	}//end while( $r_formapago = db_fetch_object( $query_formapago ) )
	?>

	return input;
}

function addSelectB(size,type,name,value,keypress, blur){
	var input =  document.createElement("SELECT");
	input.setAttribute("name",name);
	<?php 
	$sql_banco = "SELECT * FROM Banco";
	$query_banco = db_query($sql_banco);
	$i = 0;
	while( $r_banco = db_fetch_object( $query_banco ) )
	{

		echo "var option".$i." = document.createElement('OPTION');";
		echo "option".$i.".setAttribute('value','".$r_banco->IDBanco."'); ";
		echo "option".$i.".innerHTML = '".$r_banco->Nombre."';";
		echo "input.appendChild(option".$i.");";

		$i++;

	}//end while( $r_formapago = db_fetch_object( $query_formapago ) )
	?>

	return input;
}

var cont=<?php echo get_maxID("PuntoVentaBanco","IDPuntoVentaBanco")?>;

function addRow(){
cont ++;
var tbody = document.getElementById("table1").getElementsByTagName("tbody")[0];
var row = document.createElement("TR");

row.setAttribute("class","row2");

var cell1 = addCell("<b>" + cont + "</b>");
var cell2 = addCell("");
var cell3 = addCell("");
var cell4 = addCell("");


var inp1 = addSelectF(5,"text","IdFormaPago["+cont+"]","",0,0);
cell2.appendChild(inp1);

var inp2 = addSelectB(5,"text","IdBanco["+cont+"]","",0,0);
cell3.appendChild(inp2);

var inp3 = addInput(10,"text","Comision["+cont+"]","",0,0);
cell4.appendChild(inp3);

row.appendChild(cell1);
row.appendChild(cell2);
row.appendChild(cell3);
row.appendChild(cell4);

tbody.appendChild(row);
}

function delRow(){
	var tbl = document.getElementById('table1');
	var lastRow = tbl.rows.length;
	if (lastRow > 1) {
		tbl.deleteRow(lastRow - 1);
		cont--;
	}
}

-->

</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
			<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
			<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>
<form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?php }?>>

<table cellpadding=1 cellspacing=0 class=bordertable align=center >
	<tr>
				<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
			</tr>
	<tr>
				<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
			<tr class=row2>
			<td class="row1"> Tipo de Punto</td><td><?php echo formpopup("TipoPuntoVenta","Nombre","Nombre","IDTipoPuntoVenta",$r->IDTipoPuntoVenta,"input\" id=\"Tipo de Venta"); ?></td>
			</tr>

            <tr class=row2>
				<td class="row1"> Prioridad para entrega pedidos de terceros</td><td><?php echo formpopup("TipoPrioridad","Nombre","Nombre","IDTipoPrioridad",$r->IDTipoPrioridad,"input\" id=\"Tipo de prioridad"); ?></td>
			</tr>


						<tr class=row2>
			<td class="row1"> Ciudad </td><td><?php echo formpopup("Ciudad","Descripcion","Descripcion","IDCiudad",$r->IDCiudad,"input\" id=\"Ciudad"); ?></td>
			</tr>
			<tr class=row2>
			<td class="row1"> Administrador </td>
			<td>
			<?php echo formpopup("Empleado","Nombre","Apellidos","IDEmpleado",$r->IDEmpleado,"input\" id=\"Empleado"," Publicar = 'S'"); ?> </td>
			</tr>
						<tr class=row2>
							<td class="row1">Comisi&oacute;n</td>
							<td><?php echo formpopup("Comision","Porcentaje","Porcentaje","IDComision",$r->IDComision,"input\" id=\"Comision"); ?></td>
						</tr>
						<tr class=row2>
			<td class="row1"> Nombre </td><td><input type=text size=25 class=input   name=Nombre id=Nombre value="<?php echo $r->Nombre ?>"> </td>
			</tr>
			<tr class=row2>
			<td class="row1"> Codigo </td><td><input type=text size=25 class=input   name=Codigo id=Codigo value="<?php echo $r->Codigo ?>"> </td>
			</tr>
			<tr class=row2>
			<td class="row1"> Numero de Equipo de Computo </td><td><input type=text size=25 class=input   name=EquipoComputo id=EquipoComputo value="<?php echo $r->EquipoComputo ?>"> </td>
			</tr>
			<tr class=row2>
			<td class="row1"> Direccion </td><td><input type=text size=25 class=input   name=Direccion id=Direccion value="<?php echo $r->Direccion ?>"> </td>
			</tr>
			<tr class=row2>
			<td class="row1"> Telefono </td><td><input type=text size=25 class=input   name=Telefono id=Telefono value="<?php echo $r->Telefono ?>"> </td>
			</tr>
            <tr class=row2>
			<td class="row1"> Email  </td><td><input type=text size=25 class=input   name=Email id=Email value="<?php echo $r->Email ?>"> </td>
			</tr>
			<tr class=row2>
			<td class="row1"> Publicar </td><td><?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Publicar, 'Publicar'); ?></td>
			</tr>
			<tr class=row2>
			<td class="row1"> Centro de Costo </td><td><input type=text size=25 class=input   name=CentroCosto id=CentroCosto value="<?php echo $r->CentroCosto ?>"> </td>
			</tr>
			<tr class=row2>
			<td class="row1"> IP </td><td><input type=text size=25 class=input   name=IP id=IP value="<?php echo $r->IP ?>"> </td>
			</tr>
			<tr >
				<td class=titlemedium colspan="2">
					Resolucion DIAN
				</td>
			</tr>
			<tr class=row2 >
				<td class="row1"></td><td></td>

			</tr>
			<tr class=row2>
				<td colspan="2">
					<table  border="0" bgcolor="#ffffff" cellspacing="1" cellpadding="1"  width="100%">
									<tr>
										<td class="row1" width="30%"> Numero Resolucion </td>
										<td class="row2" colspan="3"><input type="text" name="NumeroResolucion" value="<?php echo $r->NumeroResolucion?>" size="44"></td>
									</tr>
									<tr>
										<td class="row1" width="30%"> Resolucion </td>
										<td class="row2" colspan="3"><input type="text" name="Resolucion" value="<?php echo $r->Resolucion?>" size="44"></td>
									</tr>
									<tr>
										<td class="row1" width="30%">Fecha Fin Resolucion </td>
										<td class="row2" colspan="3"><input type="text" class="input" name="FechaFinResolucion" size="19" value="<?php if($r->FechaFinResolucion!="0000-00-00") echo $r->FechaFinResolucion; ?>" readonly>
											<script language="JavaScript1.2">
															<!--
																if (!document.layers)
																	document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaFinResolucion,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
															//-->
														</script></td>
									</tr>
									<tr>
							<td class="row1" width="30%"> Desde </td><td class="row2"><input type="text" name="RDesde" value="<?php echo $r->RDesde?>" size="8"></td>

							<td class="row1"> Hasta </td><td class="row2"><input type="text" name="RHasta" value="<?php echo $r->RHasta?>" size="8"></td>

						</tr>
					</table>
				</td>
			</tr>
			</tr>

						<tr >
				<td class=titlemedium width="100%" colspan="2">Forma de Pago
								<input type="button" onClick="addRow()" value="agregar"> <input type="button" onClick="delRow()" value="remover">
							</td>
			</tr>
			<?php 
							$sql_fpago = "SELECT * FROM PuntoVentaBanco WHERe IDPuntoVenta = '$id';";
							$query_fpago = db_query($sql_fpago);
							if(db_num_rows($query_fpago))
							{
						?>
			<tr class=row2>
							<td colspan="2">
								<table class="bordertable"  border="0" bgcolor="#ffffff" cellspacing="1" cellpadding="1"  width="100%">
									<tr class="rowform">
										<td  align="center">
											<b>Item</b>
										</td>
										<td  align="center">
											<b>Forma de Pago</b>
										</td>
										<td  align="center">
											<b>Banco</b>
										</td>
										<td  align="center">
											<b>Comision Banco</b>
										</td>
										<td align="center">
											<b>Borrar</b>
										</td>
									</tr>

									<?php 
									$i = 1;
									while( $r_fpago = db_fetch_object($query_fpago) )
									{

									?>
										<tr class="row2">
											<td  align="center">
												<?php echo $i;?>
											</td>
											<td  align="center">
												<select name="PVentaFPago[<?php echo $r_fpago->IDPuntoVentaBanco?>]" class="InputSelect">
													<?php 
														$sql_PPago = "SELECT * FROM FormaPago";
														$query_PPago = db_query($sql_PPago);
														while( $r_PPago = db_fetch_object( $query_PPago ) )
														{

															$optionPPago =  "<option value='".$r_PPago->IDFormaPago."'";
															if($r_fpago->IDFormaPago == $r_PPago->IDFormaPago )
																$optionPPago .= " selected ";
															$optionPPago .= ">".$r_PPago->Descripcion."</option>";
															echo $optionPPago;
														}//end while( $r_formapago = db_fetch_object( $query_formapago ) )
													?>
												</select>
											</td>
											<td  align="center">
												<select name="PVentaBanco[<?php echo $r_fpago->IDPuntoVentaBanco?>]" class="InputSelect">
													<?php 
														$sql_Pbanco = "SELECT * FROM Banco";
														$query_Pbanco = db_query($sql_Pbanco);
														while( $r_Pbanco = db_fetch_object( $query_Pbanco ) )
														{

															$optionPBanco =  "<option value='".$r_Pbanco->IDBanco."'";
															if($r_fpago->IDBanco == $r_Pbanco->IDBanco )
																$optionPBanco .= " selected ";
															$optionPBanco .= ">".$r_Pbanco->Nombre."</option>";
															echo $optionPBanco;
														}//end while( $r_formapago = db_fetch_object( $query_formapago ) )
													?>
												</select>
											</td>
											<td  align="center">
												<input type="text" value="<?php echo $r_fpago->Comision?>" size="5" name="PVentaComision[<?php echo $r_fpago->IDPuntoVentaBanco?>]" class="input">
											</td>
											<td align="center">
												<a href='<?php echo "?mod=$MOD&action=delfpago&id=".$id."&idfpago=";echo $r_fpago->IDPuntoVentaBanco; ?>'><img src='images/trash.gif' border='0'></a></td>
									</tr>
									<?php 
										$i++;
									}
									?>

								</table>
							</td>
						</tr>
			<?php 
							}//end if(db_num_rows($query_fpago))
							?>

						<tr class=row2>
							<td colspan="2">
								<table class="bordertable"  border="0" bgcolor="#ffffff" cellspacing="1" cellpadding="1" id=table1 width="100%">
									<tr class="rowform">
										<td  align="center">
											<b>Item</b>
										</td>
										<td  align="center">
											<b>Forma de Pago</b>
										</td>
										<td  align="center">
											<b>Banco</b>
										</td>
										<td  align="center">
											<b>Comision Banco</b>
										</td>
									</tr>
									<tbody bgcolor=#e7ebef>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
			<td colspan="2" align=center class=row2><input type=hidden name=IDPuntoVenta id=IDPuntoVenta value="<?php echo $r->IDPuntoVenta ?>"><input type=hidden name=UsuarioTrCr value="<?php echo $r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?php echo $r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?php echo $r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?php echo $r->FechaTrEd ?>">
				<input type=hidden name=ID value="<?php echo $r->$Key ?>">
				<input type=hidden name=action value=<?php echo $newmode?>>
				<input type=submit name=submit value="<?php echo $submit_caption ?>" class=submit>
			</td>
			</tr>
			<tr>
				<td align=center class=row2><br>
				</td>
				<td align=center class=row2></td>
			</tr>
			<?php 
				$sql_rformaspago = " SELECT SUM( FPF.Valor ) as Valor, FP.Descripcion
										FROM Factura F, FormaPagoFactura FPF, FormaPago FP
										WHERE F.IDPuntoVenta = '$r->IDPuntoVenta'
										AND F.IDFactura = FPF.IDFactura
										AND F.IDPuntoVenta = FPF.IDPuntoVenta
										AND FPF.IDFormaPago = FP.IDFormaPago
										GROUP BY FP.IDFormaPago
										ORDER BY Valor DESC";
				$qry_rformaspago = db_query( $sql_rformaspago );

			if( db_num_rows( $qry_rformaspago ) > 0 )
			{
			?>
			<tr><td align=left class="titlemedium" colspan="2">Reporte Formas de Pago Almac&eacute;n</td></tr>
			<tr>
				<td align=left class=row1 colspan=2>

					<table width="400" border="0" cellspacing="2" cellpadding="0" align="center" class="bordertable">
						<tr>
							<td class="rowform" align="center" ><b>Forma de Pago</b></td>
							<td class="rowform" align="center"><b>Valor</b></td>
						</tr>
						<?php 
						while( $r_rformaspago = db_fetch_object( $qry_rformaspago ) )
						{
							$class = repetition()?"row1":"row2";
						?>
							<tr>
								<td class="<?php echo $class?>" align="right"><?php echo $r_rformaspago->Descripcion?></td>
								<td class="<?php echo $class?>" align="right"><?php echo number_format( $r_rformaspago->Valor,2 )?></td>
							</tr>
						<?php 
						}//end while
						?>
					</table>
				</td>
			</tr>
			<?php 
			}//end if
			?>
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
	 	$sql =  "SELECT * FROM $Table WHERE ".$where_publicar." ORDER BY $Key";


		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=100;
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
<table width=700 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
		</tr>
<?php filtrar();?>
<tr>
			<td class=titlemedium  bgcolor=#9daac6><?php  echo $info;?></td>
		</tr>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=13 nowrap>
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
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Nombre&nbsp;<?php  if($_GET['order_by']=="Nombre"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDEmpleado&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Administrador&nbsp;<?php  if($_GET['order_by']=="IDEmpleado"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=IDTipoPuntoVenta&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">TipoPuntoVenta&nbsp;<?php  if($_GET['order_by']=="IDTipoPuntoVenta"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>

						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Codigo&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Codigo&nbsp;<?php  if($_GET['order_by']=="Codigo"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Telefono&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Telefono&nbsp;<?php  if($_GET['order_by']=="Telefono"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none">Comentario</td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

<?php while($r = db_fetch_object($result)){
?>

<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td  class=row1><?php echo $r->Nombre ?></td>

						<td nowrap class=row1><?php echo get_field("Empleado","concat(Nombre,' ',Apellidos)","IDEmpleado",$r->IDEmpleado) ?></td>
						<td nowrap class=row1><?php echo get_field("TipoPuntoVenta","Nombre","IDTipoPuntoVenta",$r->IDTipoPuntoVenta) ?></td>
						<td nowrap class=row1><?php echo $r->Codigo ?></td>
						<td nowrap class=row1><?php echo $r->Telefono ?></td>
						<td nowrap class=row1>
							<?php 															
							if (isset($r->FechaFinResolucion) && $r->FechaFinResolucion != "0000-00-00") {								
								$fechaFin = new DateTime($r->FechaFinResolucion);
								$hoy = new DateTime();
								$interval = $hoy->diff($fechaFin);								
								if ($interval->days <= 10 && $interval->invert == 0) {									
									if($interval->days==0){
										$DiasVencimiento=1;
									}
									else{
										$DiasVencimiento=$interval->days+1;
									}
									echo "<span style='color: red;'>Vence en ".$DiasVencimiento." dias</span>";
								}
							}
							
							$ArrayNumeroFinResolucion = explode("-", $r->RHasta);
							$NumeroFinResolucion =  $ArrayNumeroFinResolucion[1];

							$sql_fact="SELECT NumeroFactura FROM Factura WHERE IDPuntoVenta = '$r->IDPuntoVenta' ORDER BY IDFactura DESC LIMIT 1";
							$qry_fact = db_query($sql_fact);
							$r_fact = db_fetch_object($qry_fact);
							$NumerofacturaActual = $r_fact->NumeroFactura;
							
							
							$Diferencia = $NumeroFinResolucion - $NumerofacturaActual;
							if($Diferencia <= 10)
								echo "<span style='color: red;'>Faltan ".$Diferencia." facturas</span>";





							?>

						</td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>
</td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=7 nowrap>
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
					<option value="Nombre">Nombre</option>
					<option value="Empleado.Nombre">Administador</option>
					<option value="Ciudad.Descripcion">Ciudad</option>
					<option value="TipoPuntoVenta.Nombre">Tipo</option>
					<option value="Codigo">Codigo</option>
					<option value="Direccion">Direccion</option>
					<option value="Telefono">Telefono</option>
					<option value="Publicar">Publicar</option>
				</select>
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post">
				ordenar por
				<select name="order_by" class="popup">
					<option value="Nombre">Nombre</option>
					<option value="Empleado.Nombre">Administador</option>
					<option value="Ciudad.Descripcion">Ciudad</option>
					<option value="TipoPuntoVenta.Nombre">Tipo</option>
					<option value="Codigo">Codigo</option>
					<option value="Direccion">Direccion</option>
					<option value="Telefono">Telefono</option>
					<option value="Publicar">Publicar</option>
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
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="Ciudad">
				<input type="hidden" name="tlevel" value="Empleado">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php 
	}//End function filtrar
?>
