

<?php
	$TitleMod ="Cambio";
	
	$Table = "Cambio";
	$TableJoin = "DetalleCambio";
	$Key = "IDCambio";
	$MOD = "cambiar";
	$m = "Movimientos";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "insert" :
			
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");
				
				$HTTP_POST_VARS['Excedente'] = ereg_replace("[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]","",$HTTP_POST_VARS['Excedente']);
				
				$frm= vars_LOG($HTTP_POST_VARS);
				
				//print_r( $frm );
				//exit;

				$frm['IDCambio'] = insert($frm);
				if( empty( $frm['Observaciones'] ) )
					$frm['Observaciones'] = "Excedente Generado por Cambio ".$frm['IDCambio'];
				$frm= vars_LOG($frm);
								
				$frm = ventacambioant($frm);
				
				//print_r($frm);
				
				//db_query( "tales" );
				db_query("COMMIT");
				
				echo "<script>alert('Operacion de cambio Generada Correctamente');</script>";
				
				if( isset( $frm['IDFactura'] ) && !empty( $frm['IDFactura'] ) && $frm['IDFactura'] <> 0 )
				{
				
					//Imprimir la factura
					echo "<script>window.open('FormaPago/popFormapago.php?id=".$frm['IDFactura']."&idpunto=".$IDPuntoVenta."','','width=550, height=350, scrollbars=yes');location.href='?mod=Factura&action=edit&id=".$frm['IDFactura']."';</script>";
				}
				else
				{
					//Imprimir la factura
					echo "<script>location.href='?mod=GenerarFactura&msg=2';</script>";
				}	
				
				//print_form($id,"update","Actualizar $TitleMod","Realizar Movimiento");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Movimiento");
			break ;
			
			case "insertcliente" :
				echo "<script>alert('Documento no existe, por favor verifique')</script>";
				echo "<script>location.href='?mod=cambioreferenciaespecial';</script>";
			break;
			
			case "mostrar":
				$sql_cliente = "SELECT * FROM Cliente WHERE Cedula = '$cedula'";
				$query_cliente = db_query($sql_cliente);
				if( db_num_rows( $query_cliente ) == 0 )
				{
					echo "<script>alert('Documento no existe, por favor verifique')</script>";
					echo "<script>location.href='?mod=cambioreferenciaespecial';</script>";
				}//end if( db_num_rows( $query_cliente ) == 0 )
				else
				{
					$r_cliente = db_fetch_object( $query_cliente );
					print_form($r_cliente->IDCliente,"insert","Realizar $TitleMod","Realizar Movimiento");
				}//end else
				
				
				
			break;
			
			default : 
				mostrarcedula("mostrar","Buscar Cliente");
			break;
			
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col1");
	


/*******************************************************************************************
		funtcion mostrarcedula
*******************************************************************************************/

function mostrarcedula($newmode,$submit_caption){
?>
	
<br>
<form name="frmcliente" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" onsubmit="disable(this);">
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="500">
	
	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b><span class="gen">C&eacute;dula del Cliente </span></td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table align="center" width="500" cellpadding="0" cellspacing="1" border="0" class="forumline">
  
  <tr>
	<td class="col1" align="center" valign="middle">Seleccione el Nit por favor</td>
	<td class="col2" >
    	<select name="cedula" id="cedula">
        	<option value=""></option>
            <option value="899999084">Empresa de Licores de Cundinamarca</option>            
        </select>		
	</td>
  </tr>
  
  <tr>
	<td class="col2list" align="center" valign="middle" colspan="2">
		<input type="submit" class="button" name="enviar" value="<?=$submit_caption?>">
		<input type="hidden" value="<?=$newmode?>" name="action">
	</td>
  </tr>
</table>
</form>
<?php
}//end	mostrar($newmode,$submit_caption)

/*******************************************************************************************
		funtcion Print_formCliente
*******************************************************************************************/
function print_formcliente($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$cedula,$array_gustos,$array_deportes,$array_hobbies,$array_musica;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);
	$newmode = "insertcliente";
?>
	<script>
var Check = new Array('Cedula','Nombre','Apellido','Telefono');
</script>
<br>
	<form name="frmcliente" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="disable(this);return EvaluaReg(this,Check)"<?php }?>>
	
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="500">
	
	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				<?=$title?>
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table class="forumline" width="500" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class="forumline texto" >
						<tr>
							<td class="forumlink" colspan="2">
								<span class="genmed">Campos Minimos Requeridos: Cedula, Nombre, Apellidos, Telefono</span>
							</td>
						</tr>
						<tr >
							<td width="40%" class="col1">C&eacute;dula</td><td class=col2><input type=text size=25 class=tbox value="<?=$cedula?>" name=Cedula id=Cedula value="<?=$r->Cedula ?>"> </td>
						</tr>
						<tr >
							<td class="col1" width="40%"> Nombre </td><td class="col2"><input type=text size=25 class=tbox   name=Nombre id=Nombre value="<?=$r->Nombre ?>"> </td>
						</tr>
						<tr >
							<td width="40%" class="col1"> Apellidos </td><td class="col2"><input type=text size=25 class=tbox   name=Apellido id=Apellidos value="<?=$r->Apellido ?>"> </td>
						</tr>
						<tr >
							<td width="40%" class="col1"> Telefono </td><td class="col2"><input type=text size=25 class=tbox   name=Telefono id=Telefono value="<?=$r->Telefono ?>"> </td>
						</tr>
						<tr >
							<td width="40%" class="col1"> Celular </td><td class="col2"><input type=text size=25 class=tbox   name=Celular id=Celular value="<?=$r->Celular ?>"> </td>
						</tr>
						<tr >
							<td width="40%" class="col1">Direcci&oacute;n</td><td class="col2"><input type=text size=25 class=tbox   name=Direccion id=Direccion value="<?=$r->Direccion ?>"> </td>
						</tr>
						<tr >
							<td width="40%" class="col1">Ciudad</td><td class="col2"><?php echo formpopup("Ciudad","Descripcion","Descripcion","IDCiudad",$r->IDCiudad,"input\" id=\"IDCiudad"); ?></td>
						</tr>
						<tr >
							<td width="40%" class="col1">Empleado</td><td class="col2"><input type=text size=25 class=tbox   name=IDEmpleado id=IDEmpleado value="<?=$r->IDEmpleado ?>"> </td>
						</tr>
						<tr >
							<td height="22" width="40%" class="col1">Fecha de Nacimiento</td>
							<td class="col2">
					<select name="Ano" id="A&ntilde;o de Nacimiento" class="tbox">
                      <option value="">A&ntilde;o</option>
                      <?php
	                      for($i = 1920; $i<1999; $i++)
	                      {
	                      	$op = "<option value=\"$i\" ";
	                      	if( $r->Ano == $i )
	                      		$op .= " selected ";
	                      	$op .= ">".$i."</option>";
	                      	 echo $op;
	                      }
                     
                      ?>
                      
                    </select>
								
								<select name="Mes" id="Mes de Nacimiento" class="tbox">
                                    <option value="">Mes</option>
                                    <option value="1" <?php if( $r->Mes == 1 ) echo " selected " ?>>Enero</option>
                                    <option value="2" <?php if( $r->Mes == 2 ) echo " selected " ?>>Febrero</option>
                                    <option value="3" <?php if( $r->Mes == 3 ) echo " selected " ?>>Marzo</option>
                                    <option value="4" <?php if( $r->Mes == 4 ) echo " selected " ?>>Abril</option>
                                    <option value="5" <?php if( $r->Mes == 5 ) echo " selected " ?>>Mayo</option>
                                    <option value="6" <?php if( $r->Mes == 6 ) echo " selected " ?>>Junio</option>
                                    <option value="7" <?php if( $r->Mes == 7 ) echo " selected " ?>>Julio</option>
                                    <option value="8" <?php if( $r->Mes == 8 ) echo " selected " ?>>Agosto</option>
                                    <option value="9" <?php if( $r->Mes == 9 ) echo " selected " ?>>Septiembre</option>
                                    <option value="10" <?php if( $r->Mes == 10 ) echo " selected " ?>>Octubre</option>
                                    <option value="11" <?php if( $r->Mes == 11 ) echo " selected " ?>>Noviembre</option>
                                    <option value="12" <?php if( $r->Mes == 12 ) echo " selected " ?>>Diciembre</option>
                                  </select>

								<select name="Dia" id="Dia de Nacimiento" class="tbox">
			                      <option value="" selected>Dia</option>
			                      <?php
				                      for($i = 1; $i<=31; $i++)
				                      {
				                      	$op = "<option value=\"$i\" ";
				                      	if( $r->Dia == $i )
				                      		$op .= " selected ";
				                      	$op .= ">".$i."</option>";
				                      	 echo $op;
				                      }
			                     
			                      ?>
		                    </select>
								
								
								</td>
						</tr>
						<tr >
							<td width="40%" class="col1">
								Estado Civil
							</td>
							<td class="col2">
							
								<select name="EstadoCivil" id="Estado Civil" class="tbox">
			                      <option value="" Selected>Seleccione</option>
			                      <option value="Soltero">Soltero(a)</option>
			                      <option value="Casado">Casado(a)</option>
			                      <option value="Separado">Divorciado(a)</option>
			                      <option value="Viudo">Viudo(a)</option>
			                      <option value="UnionLibre">Union Libre</option>
			                      <option value="Otro">Otro</option>
			                    </select>
							
							</td>
						</tr>
						<tr >
							<td width="40%" class="col1">N&uacute;mero de Hijos</td>
							<td class="col2"><input type=text size=25 class=tbox   name=NumeroHijos id=IDEmpleado value="<?=$r->NumeroHijos ?>"></td>
						</tr>
						<tr >
							<td  class="col1" colspan="2">
								<table width="100%" >
									<tr>
										<td class="col1list">
											Gustos
										</td>
										<td class="col1list">
											Deprotes
										</td>
										<td class="col1list">
											Hobbies
										</td>
									</tr>
									<tr>
										<td class="col2" width="33.33%">
											<?php
												echo formcheckgroup($array_gustos,"","Gustos[]");
											?>
										</td>
										<td class="col2" valign="top" width="33.33%">
											<?php
												echo formcheckgroup($array_deportes,"","Deportes[]");
											?>
										</td>
										<td class="col2" valign="top" width="33.33%">
											<?php
												echo formcheckgroup($array_hobbies,"","Hobbies[]");
											?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						
						<tr >
							<td  class="col1" colspan="2">
								<table width="100%" >
									<tr>
										<td class="col1list">M&uacute;sica</td>
										<td class="col1list">
											
										</td>
										<td class="col1list">
											
										</td>
									</tr>
									<tr>
										<td class="col2" width="33.33%">
											<?php
												echo formcheckgroup($array_musica,"","Musica[]");
											?>
										</td>
										<td class="col2" valign="top" width="33.33%">
											
										</td>
										<td class="col2" valign="top" width="33.33%">
											
										</td>
									</tr>
								</table>
							</td>
						</tr>
						
						
						<tr >
							<td class="col1" width="40%">Autorizo a recibir e-mail con promociones o informaci&oacute;n</td>
							<td class="col2"><?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->AutorizaMail, 'AutorizaMail'); ?></td>
						</tr>
						<tr >
			<td width="40%" class="col1"> Publicar </td><td class="col2"><?php echo formradiogroup(array('Si'=>'S','No'=>'N'),$r->Publicar, 'Publicar'); ?></td>
			</tr>
			
			<tr>
			<td colspan=2 align=center class="col2list"><input type=hidden name=IDCliente id=IDCliente value="<?=$r->IDCliente ?>"><input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
				<input type=hidden name=ID value="<?php echo $r->$Key ?>">
				<input type=hidden name=action value=<?=$newmode?>>
				<input type=submit name=submit value="<?php echo $submit_caption ?>" class=submit>
			</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
	<?php
}// End function print_formcliente()


/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/

function print_form($id,$newmode,$title,$submit_caption){
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $IVA,$IDPuntoVenta,$crypt;

	$qid = db_query(" SELECT * FROM Cliente WHERE IDCliente = '$id' ");
		
	$r = db_fetch_object($qid);
	
	$dato = md5_decrypt($id,$crypt);
	$array_dato = explode(",",$dato);
	$array_dato['DetalleFactura'] = $array_dato[0];
	$array_dato['Factura'] = $array_dato[1];
	$array_dato['Referencia'] = $array_dato[2];
	$array_dato['Punto'] = $array_dato[3];
	//print_r( $array_dato );
	
	$sql_factura = " SELECT F.IDFactura, F.IDCliente,C.Cedula,C.Telefono, CONCAT( C.Nombre,' ',C.Apellido ) as Nombre, DF.* 
						FROM Factura F, Cliente C, DetalleFactura DF 
						WHERE  F.IDFactura = '$array_dato[Factura]' 
						AND F.IDPuntoVenta = '$array_dato[Punto]'
						AND F.IDCliente = C.IDCliente 
						AND F.IDFactura = DF.IDFactura 
						AND DF.IDDetalleFactura = '$array_dato[DetalleFactura]' ";
						
	$qry_factura = db_query( $sql_factura );
	
	$r_factura = db_fetch_object( $qry_factura );
	
?>

<script language="JavaScript">
<!--


function addCell(label){
	var cell = document.createElement("TD"); 
	if(label)
		cell.innerHTML = label; 
	return cell;
}
function addInput(size,type,name,value,keypress, blur,cont){
	var input =  document.createElement("INPUT"); 
	if(keypress==1)
		input.setAttribute("onKeyPress","if((event.keyCode < 48 || event.keyCode > 57) ) event.returnValue = false;"); 
	if(blur==1)
		input.setAttribute("onblur","CalculaMontoTotalIngreso(this);"); 
	if(keypress==2)
		input.setAttribute("onKeyPress","return KeyCheck(this,window.event.keyCode);"); 
	if(blur==2)
		input.setAttribute("onblur","formatCurrency(this);CalculaMontoTotalIngreso(this);"); 
	
	if(keypress==4){
		var URL = "'Referencia/popReferencias.php?IDPuntoVenta=<?=$IDPuntoVenta?>&cont="+cont+"&IDFacturaBono=<?=$IDFacturaBono?>'";
		
		var funcion = "window.open("+URL+",'','width=400,height=400');";
		
		input.setAttribute("onclick",funcion); 
	}
	
	if(blur==5)
		input.setAttribute("onblur","if(!compruebamaximo(this.value, cont)) this.value = ''; else calculatotal(this.value,cont);"); 
	
	
	if(type == "text")
	{
		input.setAttribute("class","tbox"); 
		input.setAttribute("size",size); 
		input.setAttribute("type",type); 
		input.setAttribute("name",name); 
		input.setAttribute("value",value);
		if(name != "Cantidad"+cont)
			input.setAttribute("readonly","true");
		
	}
	if(type == "button")
	{
		input.setAttribute("class","submit"); 
		input.setAttribute("type",type); 
		input.setAttribute("name",name); 
		input.setAttribute("value",value);
	}
	if(type == "hidden")
	{
		input.setAttribute("type",type); 
		input.setAttribute("name",name); 
		input.setAttribute("value",value);
	}

	return input;
}

var cont=1;

function addRow(){ 
cont ++;
var tbody = document.getElementById("table1").getElementsByTagName("tbody")[0];
var row = document.createElement("TR"); 

var cell1 = addCell("<b>" + cont + "</b>");
var cell2 = addCell("");
var cell3 = addCell("");
var cell4 = addCell("");
var cell5 = addCell("");
var cell6 = addCell("");
var cell7 = addCell("");
var cell8 = addCell("");
var cell9 = addCell("");
var cell10 = addCell("");
var cell11 = addCell("");
var cell12 = addCell("");

var inp1 = addInput(5,"text","Numero" + cont,"",0,0,cont);
cell2.appendChild(inp1);

var inp2 = addInput(5,"text","Talla" + cont,"",0,0,cont);
cell3.appendChild(inp2);

var inp3 = addInput(15,"text","Nombre" + cont,"",0,0,cont);
cell4.appendChild(inp3);

var inp4 = addInput(5,"hidden","IDCodificacion" + cont,"",0,0,cont);
cell5.appendChild(inp4);

var inp5 = addInput(5,"text","Cantidad" + cont,"",0,5,cont);
cell6.appendChild(inp5);

var inp6 = addInput(15,"text","ValorU" + cont,"",0,0,cont);
cell7.appendChild(inp6);  
  
var inp7 = addInput(15,"text","Total" + cont,"",0,0,cont);
cell8.appendChild(inp7);

var inp8 = addInput(5,"button","Agregar" + cont,"Referencia",4,0,cont);
cell9.appendChild(inp8);

var inp9 = addInput(5,"hidden","Maximo" + cont,"",0,0,cont);
cell10.appendChild(inp9);

var inp10 = addInput(5,"hidden","Precio" + cont,"",0,0,cont);
cell11.appendChild(inp10);
var inp11 = addInput(5,"hidden","Descuento" + cont,"",0,0,cont);
cell12.appendChild(inp11);

row.appendChild(cell1); 
row.appendChild(cell2);
row.appendChild(cell3);
row.appendChild(cell4);
row.appendChild(cell5);
row.appendChild(cell6);
row.appendChild(cell7);
row.appendChild(cell8);
row.appendChild(cell9);
row.appendChild(cell10); 
row.appendChild(cell11);  
row.appendChild(cell12);   

tbody.appendChild(row); 
} 

function delRow(){
	var tbl = document.getElementById('table1');
	var lastRow = tbl.rows.length;
	if (lastRow > 2) {
		tbl.deleteRow(lastRow - 1);
		cont--;
	}
}

function selreferencia(REFERENCIA, NOMBRE, TALLA, CODIFICACION, CONT, MAXIMO, VALORU, DESCUENTOREF){
	document.frm.elements["Numero"+CONT].value = REFERENCIA;
	document.frm.elements["Nombre"+CONT].value = NOMBRE;
	document.frm.elements["Talla"+CONT].value = TALLA;
	document.frm.elements["IDCodificacion"+CONT].value = CODIFICACION;
	document.frm.elements["Cantidad"+CONT].value = 1;
	document.frm.elements["Cantidad"+CONT].focus();

	/*******Si la FacturaBono tiene descuento especial se hace la operacion**************/
	var PRECIO = 0;
	var iva = 1;
	
	document.frm.elements["Precio"+CONT].value = VALORU;
	
	/****Fin Si la FacturaBono tiene descuento especial se hace la operacion************/
	
	
	//VALORU = VALORU - ( VALORU * iva );	
	
	document.frm.elements["ValorU"+CONT].value = VALORU;
	formatCurrency(document.frm.elements["ValorU"+CONT]);
	
	document.frm.elements["Maximo"+CONT].value = MAXIMO;
	document.frm.elements["ITEM"].value = CONT;

	calculatotal(1,CONT);
}

function selreferenciac(REFERENCIA, NOMBRE, TALLA, CODIFICACION, CONT, MAXIMO, VALORU, DESCUENTOREF){
	document.frm.elements["Numero"+CONT].value = REFERENCIA;
	document.frm.elements["Nombre"+CONT].value = NOMBRE;
	document.frm.elements["Talla"+CONT].value = TALLA;
	document.frm.elements["IDCodificacion"+CONT].value = CODIFICACION;
	document.frm.elements["Cantidad"+CONT].value = 1;
	document.frm.elements["Cantidad"+CONT].focus();
	
	/*******Si la FacturaBono tiene descuento especial se hace la operacion**************/
	var PRECIO = 0;
	var iva = 1;
	
	document.frm.elements["Precio"+CONT].value = VALORU;
	
	/****Fin Si la FacturaBono tiene descuento especial se hace la operacion************/
	
	
	//VALORU = VALORU - ( VALORU * iva );	
	
	document.frm.elements["ValorU"+CONT].value = VALORU;
	formatCurrency(document.frm.elements["ValorU"+CONT]);
	
	
	document.frm.elements["Total2"].value = (getNum( document.frm.elements["ValorU2"].value )*1) * (getNum( document.frm.elements["Cantidad2"].value)*1 );
	formatCurrency(document.frm.elements["Total2"]);

	//document.getElementById("YourControlName").focus();
	calculatotal(1,CONT);
	
}


function selcliente(IDCLIENTE, CEDULA, NOMBRE, TELEFONO){
	document.frm.elements["IDCliente"].value = IDCLIENTE;
	document.frm.elements["Cedula"].value = CEDULA;
	document.frm.elements["NombreCliente"].value = NOMBRE;
	document.frm.elements["TeleCli"].value = TELEFONO;
}

function selempleado(IDEMPLEADO, CEDULA, NOMBRE){
	document.frm.elements["IDEmpleado"].value = IDEMPLEADO;
	document.frm.elements["CedulaEmpleado"].value = CEDULA;
	document.frm.elements["NombreEmpleado"].value = NOMBRE;
}

function compruebamaximo(value, cont)
{
	
	var maximo = document.frm.elements["Maximo"+cont].value;
	//alert(value);
	//alert(maximo);
	if( eval(value) > eval(maximo) )
	{	
		alert("El maximo es " + maximo);
		return false;
	}
	else
	{
		
		return true;
	}
}

function getNum(strNum)

{

	num = strNum.toString().replace(/\$|\,/g,'');
	if(isNaN(num))
		num = "0";
	return num;

}

function formatCurrency(InpunObject) 
{

	num = InpunObject.value;
	num = num.toString().replace(/\$|\,/g,'');
	if(isNaN(num))
		num = "0";
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();
	if(cents<10)
	cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0,num.length-(4*i+3))+','+

	num.substring(num.length-(4*i+3));

	InpunObject.value = (((sign)?'':'-') + '$' + num + '.' + cents);

}


function calculatotal(value, cont)
{
	var TotalSinIva = 0;
	var Iva = 0;
	var Excedente = 0;
	TotalCambio = 0;
	var iva = <?=$IVA?>;
	
	//alert( cont );
	
	for(i=3;i<= document.frm.ITEM.value;i++){

		if(document.frm.elements["ValorU"+i].value  != '' && document.frm.elements["Cantidad"+i].value != '')
		{	

			valorui = getNum(document.frm.elements["ValorU"+i].value ) * ( 1 - ( getNum( document.frm.elements["DescuentoLin"+i].value ) / 100 ) );
			
			//alert( document.frm.ITEM.value );
			
			var total = getNum(document.frm.elements["Cantidad"+i].value) * valorui;
			document.frm.elements["Total"+i].value = total;
			formatCurrency(document.frm.elements["Total"+i]);
			
			TotalSinIva = TotalSinIva + total;
			
			//Iva = Iva + ( ((getNum(document.frm.elements["Precio"+i].value)*1) - (getNum(document.frm.elements["ValorU"+i].value)*1)) * (getNum(document.frm.elements["Cantidad"+i].value)*1) );
			
		}

	}
	
	Excendente = TotalSinIva - (getNum(document.frm.elements["Total2"].value)*1);

	if( Excendente > 0 )
	{	
		Iva = Excendente - ( Excendente / ( 1 + <?=$IVA?>) ) ;
		document.frm.elements["Excedente"].value = Excendente;
		formatCurrency(document.frm.elements["Excedente"]);;
		
	}
	else
		document.frm.elements["Excedente"].value = "";
	
	TotalFacturaBono = TotalSinIva + Iva;
	
	
	document.frm.elements["TotalSinIVA"].value = TotalSinIva - Iva;
	formatCurrency(document.frm.elements["TotalSinIVA"]);
	
	document.frm.elements["ValorIVA"].value = Iva;
	formatCurrency(document.frm.elements["ValorIVA"]);
	
	document.frm.elements["ValorTotal"].value = TotalFacturaBono - Iva;
	formatCurrency(document.frm.elements["ValorTotal"]);
	
}
	
	
	/*var totalsiniva = (getNum(document.frm.elements["TotalSinIVA"].value)*1) + (getNum(total)*1);
	document.frm.elements["TotalSinIVA"].value = totalsiniva;
	
	var iva = ((getNum(document.frm.elements["Precio"+cont].value)*1) - (getNum(document.frm.elements["ValorU"+cont].value)*1)) * (getNum(document.frm.elements["Cantidad"+cont].value)*1);
	document.frm.elements["ValorIVA"].value = getNum(document.frm.elements["ValorIVA"].value) + getNum(iva);
	
	totalFacturaBono = (getNum(document.frm.elements["ValorIVA"].value)*1) + (getNum(document.frm.elements["TotalSinIVA"].value)*1) + (getNum(document.frm.elements["ValorTotal"].value)*1);
	document.frm.elements["ValorTotal"].value = totalFacturaBono;
		
}*/

function recalcularvalores()
{
	
	var i = 0;
	
	for(i=1;i<= document.frm.ITEM.value;i++){

		if( document.frm.elements["Precio"+i].value  != '' )
		{	

			document.frm.elements["ValorU"+i].value = document.frm.elements["Precio"+i].value;

			document.frm.elements["ValorU"+i].value = document.frm.elements["ValorU"+i].value - ( document.frm.elements["ValorU"+i].value * <?=$IVA?> );	
			formatCurrency(document.frm.elements["ValorU"+i]);
									
			calculatotal( document.frm.elements["ValorU"+i].value , i );
			
			document.frm.elements["ValorTotal"].value = (getNum( document.frm.elements["TotalSinIVA"].value )*1) + (getNum( document.frm.elements["ValorIVA"].value)*1 );
						
			formatCurrency(document.frm.elements["ValorTotal"]);
			
		}
	}
	
	
	
}

function calculac2()
{
	
	valorui = getNum(document.frm.elements["ValorU2"].value ) * ( 1 - ( getNum( document.frm.elements["DescuentoLin2"].value ) / 100 ) );
	document.frm.elements["Total2"].value = valorui * (getNum( document.frm.elements["Cantidad2"].value)*1 );
	formatCurrency(document.frm.elements["Total2"]);
	
}


function recalcularbono()
{
	
	var i = 0;
	for(i=1;i<= document.frm.ITEM.value;i++){

		if( document.frm.elements["Precio"+i].value  != '' )
		{	

									
			calculatotal( document.frm.elements["ValorU"+i].value , i );
			
			document.frm.elements["ValorTotal"].value = (getNum( document.frm.elements["TotalSinIVA"].value )*1) + (getNum( document.frm.elements["ValorIVA"].value)*1 );
						
			formatCurrency(document.frm.elements["ValorTotal"]);
			
		}
	}
	
	
	
}
		


var Check = new Array('NumeroFacturaBono','NumeroDocumento','IDPuntoVenta','IDCliente','IDEmpleado', 'Cantidad1', 'Nombre1', 'ValorTotal');
function EvaluarFunciones( Form, Check )
{

		if( EvaluaReg(Form, Check) )
		{
			return true;
		}
		else
		{
			enable(Form);
			return false;
		}
	
}//end function
	-->
</script>
<br>
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="580">
	
	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				<?=$title?>
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>
<FORM name="frm" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" <?php if($newmode!="delete"){?>onsubmit="disable(this);return EvaluarFunciones(this , Check);"<?php }?>>
<table class="forumline" width="580" cellspacing="1" border="0" align="center">
	<tr>
	<td width="100%">
		<table width="100%" border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff" >
		
				<tr >
					<td colspan="2" width="100%">
						
								<div align="center">
								<table width="100%" border=0 align="center">
									<tr>
										<td colspan="4">
											<table class=rowtable width="100%">
												<tr>
													<td class=col1>No. Registro Cambio</td>
													<td class=col2 colspan="3"><input type="text" class="tbox" name="RegistroCambio" id="Numero FacturaBono" size="24" value='<?=get_maxID("Cambio WHERE IDPuntoVenta = '$IDPuntoVenta'","IDCambio") ?>'></td>
												</tr>
												<tr>
													<td class=col1>No Factura Cambio</td>
													<td class=col2 colspan="3"><input type="text" class="tbox" name="IDFacturaCambio" id="Numero FacturaBono" size="24" value='<?=$array_dato[Factura] ?>'></td>
												</tr>
												<tr>
													<td class=col1>Fecha Registro Factura</td>
													<td class=col2 colspan="3"><input type="text" class="tbox" name="FechaCambio" size="19" value='<?=fecha()." ".hora()?>' readonly> 
														<script language="JavaScript1.2">
															<!--
																if (!document.layers)
																	document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaFacturaBono,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
															//-->
														</script>
														<input type="hidden" value="<?=$IDPuntoVenta?>" name="IDPuntoVenta"></td>
												</tr>
                                                
													<tr>
														<td class=row1 colspan="4"><b>CLIENTE</b></td>
													</tr>
													<tr>
														<td class=col1>C&eacute;dula</td>
														<td class=col2><input type="text" class="tbox" name="Cedula" readonly size="15" value='<?php echo $r->Cedula;?>'><input type="hidden" name="IDCliente" id="Cliente" value="<?=$r->IDCliente?>"></td>
														<td class=col1>Nombre</td>
														<td class=col2><input type="text" class="tbox" name="NombreCliente" readonly size="20" value='<?php echo $r->Nombre." ".$r->Apellido?>'></td>
													</tr>
													<tr>
														<td class=col1 nowrap>Telefono Cliente</td>
														<td class=col2><input type="text" class="tbox" name="TeleCli" readonly size="15" value='<?php echo $r->Telefono?>'></td>
														<td class=col1></td>
														<td class=col1></td>
													</tr>
                                                
												<tr>
													<td class=col1>Observaciones</td>
													<td class=col2 colspan="3"><textarea class="tareabox" name="Observaciones" rows="4" cols="64"><?=$r->Observaciones?></textarea></td>
												</tr>
													<tr>
														<td class=row1 colspan="4"><b>VENDEDOR</b></td>
													</tr>
												<tr>
													<td class=col1>SEleccione Vendedor</td>
													<td class=col2><?php echo formpopup("Empleado WHERE IDPuntoVenta = '$IDPuntoVenta' ","Nombre","Apellidos","IDEmpleado",$r->IDIDEmpleado,"input\" id=\"Empleado"); ?></td>
													<td class=col1 colspan="2"></td>
												</tr>
												<tr>
													<td class=col1></td>
													<td class=col1></td>
													<td class=col1></td>
													<td class=col1></td>
												</tr>
												<tr>
													<td class=col1>Excedente( $ )</td>
													<td class=col2><input type="text" class="tbox" name="Excedente" readonly size="15" value="<?php echo $r_factura->Excedente?>"></td>
													<td class=col1></td>
													<td class=col1></td>
												</tr>
												<tr>
													<td class=col1></td>
													<td class=col1></td>
													<td class=col1></td>
													<td class=col1></td>
												</tr>
												<tr>
													<td class=navpic colspan="4">Referencia Cambio </td>
												</tr>
												<tr>
													<td colspan="4">
														<table class="texto" border="0" cellspacing="1" cellpadding="0" id=table1>
															<tr bgcolor="#dfe3e7">
																<td align="center"><b></b></td>
																<td align="center"><b>Referencia</b></td>
																<td align="center"><b>Talla</b></td>
																<td align="center"><b>Nombre</b></td>
																<td align="center"><b></b></td>
																<td align="center"><b>Cantidad</b></td>
																<td align="center"><b>Valor U.</b></td>
																<td align="center"><b>Descuento Par.</b></td>
																<td align="center"><b>Total</b></td>
																<td align="center"><b>Agregar</b></td>
																<td align="center"><b></b></td>
																<td align="center"><b></b></td>
																<td align="center"><b></b></td>
															</tr>
															<tr>
																<td align="left"><b>1</b></td>
																<td align="left"><input type=text readonly name=Numero2 class=tbox size=7></td>
																<td align="left"><input type=text readonly name=Talla2 class=tbox size=5></td>
																<td align="left"><input type=text readonly name=Nombre2 class=tbox size=15></td>
																<td align="left"><input type=hidden name=IDCodificacion2></td>
																<td align="left"><input type=text name=Cantidad2 class=tbox size=5 value="1" onblur="calculac2();" ></td>
																<td align="left"><input type=text readonly name=ValorU2 class=tbox size=15></td>
																<td align="center"><input type=text name="DescuentoLin2" value="" onblur="calculac2();" class=tbox size=2 maxlength="2"></td>
																<td align="left"><input type=text readonly name=Total2 class=tbox size=15></td>
																<td align="left"><input type=button name=Agregar2 class=submit value=Referencia onclick="window.open('Referencia/popReferenciasCambio.php?IDPuntoVenta=<?=$IDPuntoVenta?>&cont=2','','width=450,height=400');"></td>
																<td align="left"></td>
																<td align="left"><input type=hidden name=Precio2></td>
																<td align="left"></td>
															</tr>
															<tbody bgcolor=#e7ebef></tbody>
														</table>
													</td>
												</tr>
												<tr>
													<td class=col1><br>
													</td>
													<td class=col1></td>
													<td class=col1></td>
													<td class=col1></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td class=navpic colspan="4"><b>Detalle Cambio</b></td>
									</tr>
									<tr bgcolor=#e7ebef>
										<td colspan="4">
											<table class="texto" border="0" cellspacing="1" cellpadding="0" id=table1>
												<tr bgcolor="#dfe3e7">
													<td align="center"><b></b></td>
													<td align="center"><b>Referencia</b></td>
													<td align="center"><b>Talla</b></td>
													<td align="center"><b>Nombre</b></td>
													<td align="center"><b></b></td>
													<td align="center"><b>Cantidad</b></td>
													<td align="center"><b>Valor U.</b></td>
													<td align="center"><b>Descuento Par.</b></td>
													<td align="center"><b>Total</b></td>
													<td align="center"><b>Agregar</b></td>
													<td align="center"><b></b></td>
													<td align="center"><b></b></td>
													<td align="center"><b></b></td>
												</tr>
												<?php
												for( $i = 3; $i < 8; $i++ )
												{
												?>
												<tr>
													<td align="left"><b><?php echo $item_act=$i-2; ?></b></td>
													<td align="left"><input type=text readonly name=Numero<?=$i?> class=tbox size=7></td>
													<td align="left"><input type=text readonly name=Talla<?=$i?> class=tbox size=5></td>
													<td align="left"><input type=text readonly name=Nombre<?=$i?> class=tbox size=15></td>
													<td align="left"><input type=hidden name=IDCodificacion<?=$i?>></td>
													<td align="left"><input type=text name=Cantidad<?=$i?> class=tbox size=5 onblur="if(!compruebamaximo(this.value,<?=$i?>)) this.value = ''; else calculatotal(this.value,<?=$i?>);"></td>
													<td align="left"><input type=text readonly name=ValorU<?=$i?> class=tbox size=15></td>
													<td align="center"><input type=text name="DescuentoLin<?=$i?>" onblur="calculatotal(this.value,<?=$i?>);" class=tbox size=2 maxlength="2"></td>
													<td align="left"><input type=text readonly name=Total<?=$i?> class=tbox size=15></td>
													<td align="left"><input type=button name=Agregar<?=$i?> class=submit value=Referencia onclick="window.open('Referencia/popReferencias.php?IDPuntoVenta=<?=$IDPuntoVenta?>&cont=<?=$i?>','','width=450,height=400');"></td>
													<td align="left"><input type=hidden name=Maximo<?=$i?>></td>
													<td align="left"><input type=hidden name=Precio<?=$i?>></td>
													<td align="left"><input type=hidden name=Descuento<?=$i?>></td>
												</tr>
												<?php
												}//end for
												?>
												<tbody bgcolor=#e7ebef></tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td class=col1></td>
										<td class=col1 width="250"></td>
										<td class=navpic colspan="2">
											<div align="left">RESUMEN CAMBIO</div>
										</td>
									</tr>
									<tr>
										<td class=col1></td>
										<td class=col1 width="250"></td>
										<td class=col2>
											<div align="right">
												Total Sin IVA</div>
										</td>
										<td class=col2><input type=text readonly name=TotalSinIVA class=tbox size=15></td>
									</tr>
									<tr>
										<td class=col1></td>
										<td class=col1 width="250"></td>
										<td class=col2>
											<div align="right">
												Valor IVA</div>
										</td>
										<td class=col2><input type=text readonly name=ValorIVA class=tbox size=15></td>
									</tr>
									<tr>
										<td class=col1></td>
										<td class=col1 width="250"></td>
										<td class=col2>
											<div align="right">
												Total Factura</div>
										</td>
										<td class=col2><input type=text readonly name=ValorTotal class=tbox size=15></td>
									</tr>
								</table>
								<input type=hidden name=ITEM><input type="hidden" name="action" value="<?=$newmode?>"><input type="hidden" name="IDCliente" value="<?=$id?>">
									<input type="submit" class="button" name="submit" value="<?=$submit_caption?>"></div>
							
					</td>
				</tr>
			</table>
		</td>
	</tr>
	
</table>
</FORM>
<?php
} // END function print_form_fotos($id,$numfotos)
?></BODY></HTML> 
