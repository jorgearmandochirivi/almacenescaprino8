
<head>
<title>..::Caprino</title>
<link rel="stylesheet" href="../styles.css" type="text/css">
</head>
<body>

<?
	$TitleMod ="Traslados";

	$Table = "Traslado";
	$TableJoin = "DetalleTraslado";
	$Key = "IDTraslado";
	$MOD = "Traslado";
	$m = "Traslado";
		//$permisos = get_permiso($ID_Usuario,$m,$Table);
//if($permisos[0] >= 2)
//{
		switch (nvl($action)) {
			case "insert" :
				//print_r($HTTP_POST_VARS);
				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");

				$frm= vars_LOG($HTTP_POST_VARS);

				/******Estado Traslado Enviado  1*********/
				$frm['IDEstadoTraslado'] = 1;

				$frm['IDTraslado'] = insert($frm);

				print_r($frm);

				for( $i = 1; $i <= $frm['ITEM']; $i++ )
				{
					$cant = "Cantidad".$i;
					$cod = "IDCodificacion".$i;
					if( !empty($frm[$cant]) )
					{

						$iddetalle = get_maxID("DetalleTraslado","IDDetalleTraslado");
						$Codificacion = $frm[$cod];
						$Cantidad = $frm[$cant];

						$sql_insert = "INSERT INTO DetalleTraslado (IDDetalleTraslado, IDTraslado, IDCodificacionEspecifica, Cantidad, UsuarioTrCr, FechaTrCr ) ";
						$sql_insert .= "VALUES ('$iddetalle','$frm[IDTraslado]','$Codificacion','$Cantidad','$frm[UsuarioTrCr]','$frm[FechaTrCr]')";

						db_query($sql_insert);

						//insertar el log
						insertlog($ID_Usuario,"DetalleTraslado",$iddetalle,"Insertar",$sql_insert);

					}

				}

				db_query("COMMIT");

				echo "<script>alert('Traslado Realizado. Esperando repuesta del punto de venta de destino...');</script>";

				//Imprimir la factura
				echo "<script>location.href='?mod=GenerarFactura';</script>";

				//print_form($id,"update","Actualizar $TitleMod","Realizar Movimiento");
			break;

			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Movimiento");
			break ;
			case "list" :
				$sql = make_qry_string($HTTP_GET_VARS);
				list_r($sql);
			break;
			default :
				print_form($id,"insert","Realizar $TitleMod","Realizar Traslado");
			break;

		} // End switch

//}//end if(permisos[0] > 2)
//else
//	echo Mensaje_Info("No tiene Permisos Suficientes","col1");





/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/

function print_form($id,$newmode,$title,$submit_caption){
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $IVA,$IDPuntoVenta;

	$qid = db_query(" SELECT * FROM Cliente WHERE IDCliente = '$id' ");

	$r = db_fetch_object($qid);
?>

<script language="JavaScript">
<!--

function validar(form) {
	 var ret=true;
	 var i=0;
	 while (ret==true)
	 {
	    if (form.elements[i].id=="req")
	    {
	        if (form.elements[i].value==""){
	          alert ("Faltan fotos por escoger.");
	          form.elements[i].focus();
	          ret=false;
	          i=form.elements.length;
	        }
	    }
	    i++;
	 }
	 return ret;
}

function addCell(label){
	var cell = document.createElement("TD");
	if(label)
		cell.innerHTML = label;

	cell.setAttribute("align","center"),
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
		var URL = "'Referencia/popReferencias.php?IDPuntoVenta=<?=$IDPuntoVenta?>&cont="+cont+"&IDFactura=<?=$IDFactura?>'";

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
var cell9 = addCell("");
var cell10 = addCell("");
var cell11 = addCell("");

var inp1 = addInput(8,"text","Numero" + cont,"",0,0,cont);
cell2.appendChild(inp1);

var inp2 = addInput(5,"text","Talla" + cont,"",0,0,cont);
cell3.appendChild(inp2);

var inp3 = addInput(15,"text","Nombre" + cont,"",0,0,cont);
cell4.appendChild(inp3);

var inp4 = addInput(5,"hidden","IDCodificacion" + cont,"",0,0,cont);
cell5.appendChild(inp4);

var inp5 = addInput(5,"text","Cantidad" + cont,"",0,5,cont);
cell6.appendChild(inp5);


var inp8 = addInput(5,"button","Agregar" + cont,"Referencia",4,0,cont);
cell9.appendChild(inp8);

var inp9 = addInput(5,"hidden","Maximo" + cont,"",0,0,cont);
cell10.appendChild(inp9);

var inp10 = addInput(5,"hidden","Precio" + cont,"",0,0,cont);
cell11.appendChild(inp10);

row.appendChild(cell1);
row.appendChild(cell2);
row.appendChild(cell3);
row.appendChild(cell4);
row.appendChild(cell5);
row.appendChild(cell6);
row.appendChild(cell9);
row.appendChild(cell10);
row.appendChild(cell11);

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

function selreferencia(REFERENCIA, NOMBRE, TALLA, CODIFICACION, CONT, MAXIMO, VALORU){
	document.frm.elements["Numero"+CONT].value = REFERENCIA;
	document.frm.elements["Nombre"+CONT].value = NOMBRE;
	document.frm.elements["Talla"+CONT].value = TALLA;
	document.frm.elements["IDCodificacion"+CONT].value = CODIFICACION;

	/*******Si la factura tiene descuento especial se hace la operacion**************/
	var descuento = 0;
	var PRECIO = 0;
	var iva = <?=$IVA?>;


	document.frm.elements["Maximo"+CONT].value = MAXIMO;
	document.frm.elements["ITEM"].value = CONT;
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



	-->
</script>
<script>
var Check = new Array('IDPuntoVentaDestino''Fecha');
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
<FORM name="frm" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" <?if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?}?>>
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
														<td class=col1>Fecha </td>
														<td class=col2 colspan="3"><input type="text" class="tbox" name="Fecha" size="19" value='<?=fecha()." ".hora()?>' readonly>
															<script language="JavaScript1.2">
															<!--
																if (!document.layers)
																	document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaFactura,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
															//-->
														</script>
															<input type="hidden" value="<?=$IDPuntoVenta?>" name="IDPuntoVentaOrigen"></td>
													</tr>
													<tr>
														<td class=col1>Destino</td>
														<td class=col2 colspan="3">
															<select name="IDPuntoVentaDestino" class="InputSelect">
															<?
																$sql_puntoventa = "SELECT * FROM PuntoVenta";
																$query_puntoventa = db_query($sql_puntoventa);
																while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
																{
																	echo "<option value='".$r_puntoventa->IDPuntoVenta."'>".$r_puntoventa->Nombre."</option>";
																}//end while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
															?>
															</select>
														</td>
													</tr>
													<tr>
														<td class=col1>Quien lo pide?</td>
														<td class=col2 colspan="3"><input type="text" name="QuienPide" id="QuienPide" value="<?php echo$r->QuienPide; ?>" ></td>
													</tr>
													<tr>
														<td class=col1>Observaciones</td>
														<td class=col2 colspan="3"><textarea class="tareabox" name="Observaciones" rows="4" cols="64"><?=$r->Observaciones?></textarea></td>
													</tr>
													<tr>
														<td class=col1><br></td>
														<td class=col1></td>
														<td class=col1></td>
														<td class=col1></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td class=navpic>Detalle Traslado</td>
											<td class=navpic colspan="3">
												<div align="right">
													<input type="button" onClick="addRow()" value="agregar"><input type="button" onClick="delRow()" value="remover"></div>
											</td>
										</tr>
										<tr bgcolor=#e7ebef>
											<td colspan="4">
												<table class="texto" border="0" cellspacing="1" cellpadding="0" width="100%" id=table1>
													<tr bgcolor="#dfe3e7">
														<td align="center"><b></b></td>
														<td align="center"><b>Referencia</b></td>
														<td align="center"><b>Talla</b></td>
														<td align="center"><b>Nombre</b></td>
														<td align="center"><b></b></td>
														<td align="center"><b>Cantidad</b></td>
														<td align="center"><b>Agregar</b></td>
														<td align="center"><b></b></td>
														<td align="center"><b></b></td>
													</tr>
													<tr >
														<td align="center"><b>1</b></td>
														<td align="center"><input type=text readonly name=Numero1 class=tbox size=8></td>
														<td align="center"><input type=text readonly name=Talla1 class=tbox size=5></td>
														<td align="center"><input type=text readonly name=Nombre1 class=tbox size=15></td>
														<td align="center"><input type=hidden name=IDCodificacion1><input type=hidden name=ITEM></td>
														<td align="center"><input type=text name=Cantidad1 class=tbox size=5 onBlur="if(!compruebamaximo(this.value,1)) this.value = ''; "></td>
														<td align="center"><input type=button name=Agregar1 class=submit value=Referencia onClick="window.open('Referencia/popReferencias.php?IDPuntoVenta=<?=$IDPuntoVenta?>&cont=1','','width=450,height=400');"></td>
														<td align="center"><input type=hidden name=Maximo1></td>
														<td align="center"><input type=hidden name=Precio1></td>
													</tr>
													<tbody bgcolor=#e7ebef></tbody>
												</table>
											</td>
										</tr>
									</table>
									<input type="hidden" name="action" value="<?=$newmode?>">
									<input type="submit" class="button" name="submit" value="<?=$submit_caption?>"></div>

					</td>
				</tr>
			</table>
		</td>
	</tr>

</table>
</FORM>
<?
} // END function print_form_fotos($id,$numfotos)
?></BODY></HTML>
