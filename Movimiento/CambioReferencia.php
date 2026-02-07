
<link rel="stylesheet" href="admin/jscripts/choosen/chosen.css">

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
	var num_prod=0;
	var num_cambios=0;


	//alert( cont );

	for(i=10;i<= document.frm.ITEM.value;i++){


		if(document.frm.elements["ValorU"+i].value  != '' && document.frm.elements["Cantidad"+i].value != '')
		{

			valorui = getNum(document.frm.elements["ValorU"+i].value ) * ( 1 - ( getNum( document.frm.elements["DescuentoLin"+i].value ) / 100 ) );

			//alert( document.frm.ITEM.value );

			var total = getNum(document.frm.elements["Cantidad"+i].value) * valorui;
			document.frm.elements["Total"+i].value = total;
			formatCurrency(document.frm.elements["Total"+i]);

			TotalSinIva = TotalSinIva + total;

			//Iva = Iva + ( ((getNum(document.frm.elements["Precio"+i].value)*1) - (getNum(document.frm.elements["ValorU"+i].value)*1)) * (getNum(document.frm.elements["Cantidad"+i].value)*1) );
			num_prod = num_prod +1;

		}

	}

	var TotalCambiar = 0;
	for(contador_cambio=1;contador_cambio<=document.frm.TotalItemCambiar.value;contador_cambio++){
		if(document.frm.elements["Total"+contador_cambio].value != ''){
			var total_cambio  = getNum(document.frm.elements["Total"+contador_cambio].value);
			TotalCambiar = (getNum(TotalCambiar)*1) + (getNum(total_cambio)*1);
			num_cambios=num_cambios+1;
		}
		document.frm.elements["TotalCambiar"].value=TotalCambiar;
	}




	//Excendente = TotalSinIva - (getNum(document.frm.elements["Total2"].value)*1);
	Excendente = TotalSinIva - (getNum(document.frm.elements["TotalCambiar"].value)*1);

	//Si el cambio es por la misma ref y color no se cobra excedente
	if(num_prod==num_cambios && num_cambios==1){
		if(document.frm.elements["Numero1"].value==document.frm.elements["Numero10"].value){
			Excendente=0;
		}
	}


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

function calculac2(item_producto)
{
	alert(document.frm.elements["ValorU"+item_producto].value);
	valorui = getNum(document.frm.elements["ValorU"+item_producto].value ) * ( 1 - ( getNum( document.frm.elements["DescuentoLin"+item_producto].value ) / 100 ) );
	document.frm.elements["Total"+item_producto].value = valorui * (getNum( document.frm.elements["Cantidad"+item_producto].value)*1 );
	formatCurrency(document.frm.elements["Total"+item_producto]);

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



var Check = new Array('MismoCliente','NumeroFacturaBono','NumeroDocumento','IDPuntoVenta','IDCliente','IDEmpleado', 'Cantidad1', 'Nombre1', 'ValorTotal');
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

<?


	//envia_correo_proveedor("jorgechirivi@gmail.com","538", "4", "jorge", "Garantia", "plantillas","Excedente","1");
	$TitleMod ="Factura";

	$Table = "Factura";
	$TableJoin = "DetalleFactura";
	$Key = "IDFactura";
	$MOD = "GenerarFactura";
	$m = "Movimientos";
		$permisos = get_permiso($ID_Usuario,$m,$Table);

		

if($permisos[0] >= 2)
{
		//echo $action;
		switch (nvl($action)) {
			case "insert" :
				$sql_cliente = "SELECT *  Cliente WHERE IDCliente = '$IDCliente'";
				$query_cliente = db_query($sql_cliente);
				$r_cliente = db_fetch_object( $query_cliente );
				print_form($r_cliente->IDCliente,"insertar","Confirmar Factura","Confirmar Factura",$HTTP_POST_VARS);

			break;

			case "edit":

               print_form($id,"insert","Generar Factura","Generar Factura");
                break ;
			case "list" :
				$sql = make_qry_string($HTTP_GET_VARS);
				list_r($sql);
			break;

			case "creargarantia":
					if ($_POST[tipo_factura]=="cambio"){
					  $datos_producto = explode("|",$_POST[IDProductoGarantia]);
					  $IDDetalleFactura = $datos_producto[0];
					  $IDFactura=$datos_producto[1];
					  $IDPuntoVenta=$datos_producto[2];
					  $id_producto=$_POST[IDProductoGarantia];

						print_formcrear_garantia($_POST[IDCambio],$id_producto,$IDPuntoVenta,"guardarcambio","Guardar Cambio","Guardar Cambio","cambio");

					}
					else{

					$id_factura=$_POST["IDFactura"];

					// verifo que no exista una garantia ya creada con este producto de esta factura
					  $datos_producto = explode("|",$_POST[IDProductoGarantia]);
					  $IDDetalleFactura = $datos_producto[0];
					  $IDFactura=$datos_producto[1];
					  $IDPuntoVenta=$datos_producto[2];

					  $sql_producto="select * from Garantia Where IDDetalleFactura='".$IDDetalleFactura."' and IDFactura = '".$IDFactura."' and IDPuntoVentaFactura = '".$IDPuntoVenta."999' and IDEstadoGarantia not in ( 9, 10 ) ";
					  $qry_producto=db_query($sql_producto);
					  if (db_num_rows($qry_producto)>0){
							$msg="Ya esta registrada una garantia con este producto/factura, por favor verifique";
							mostrarfactura("mostrar","Buscar",$msg);
					  }else{
						$id_producto=$_POST[IDProductoGarantia];
						print_formcrear_garantia($id_factura,$id_producto,$IDPuntoVenta,"guardarcambio","Guardar Cambio","Guardar Cambio");
					  }
					}
			break;

			case "guardarcambio":

			$Table = "Cambio";
			$TableJoin = "DetalleCambio";
			$Key = "IDCambio";
			$MOD = "cambiar";
			$m = "Movimientos";




				db_query("SET AUTOCOMMIT=0");
				db_query("BEGIN");

				$HTTP_POST_VARS['Excedente'] = ereg_replace("[\$\%\!\@\#\^\&\*\(\)\=\~\`\?\{\}\'\:\'\;\<\>\,]","",$HTTP_POST_VARS['Excedente']);

				$frm= vars_LOG($HTTP_POST_VARS);


				//Verifico si selecciono algun producto que sale
				if(empty($frm["Numero10"])  ||  empty($frm["Talla10"]) || empty($frm["IDCodificacion10"]) || empty($frm["Cantidad10"])):
					echo "Debe seleccionar por lo menos un producto que sale, por favor verifique";
					exit;
				endif;



				//Si es otro cliente el que hace el cambio lo busco en la base de datos y si no existe lo creo
				if($frm["MismoCliente"]!="S"):
					if(!empty($frm["NombreClienteNuevo"]) &&  !empty($frm["ApellidoClienteNuevo"]) && (int)$frm["CedulaNuevo"]>0):
							$sql_otro_cliente = "Select * From Cliente Where Cedula = '".$frm["CedulaNuevo"]."'";
							$result_otro_cliente = db_query($sql_otro_cliente);
							$total_otro_cliente = db_num_rows($result_otro_cliente);
							$datos_cliente_nuevo = db_fetch_array($result_otro_cliente);
							if((int)$total_otro_cliente<=0): //Lo creo
								//valido si l;os datos del cliente nuevo estan
									$frm_cliente["Cedula"] = $frm["CedulaNuevo"];
									$frm_cliente["Nombre"] = $frm["NombreClienteNuevo"];
									$frm_cliente["Apellido"] = $frm["ApellidoClienteNuevo"];
									$frm_cliente["Telefono"] = $frm["TeleCliNuevo"];
									$id_cliente_nuevo=insert_width_table($frm_cliente,"Cliente","IDCliente");
									$frm["IDCliente"] = $id_cliente_nuevo;
							else: //ya existe lo asigno
								$frm["IDCliente"] = $datos_cliente_nuevo["IDCliente"];
							endif;
					else:
						echo "<br><br><span style='color:#EE080C'>Error los datos del cliente nuevo estan incompletos por favor verifique</span>";
						exit;
					endif;
				endif;



				$frm['IDCambio'] = insert($frm);
				if( empty( $frm['Observaciones'] ) )
					$frm['Observaciones'] = "Excedente Generado por Cambio ".$frm['IDCambio'];
				$frm= vars_LOG($frm);

				$frm = ventacambio($frm);

				$verica_producto = db_query("Delete From TMPProductoCambio Where Cookie = '".$_COOKIE["COOKIE_CLIENTE"]."'");

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


			case "mostrar":

				if (!empty($_POST["numero_factura"]) || !empty($_GET["numero_factura"]) || !empty($_GET["numero_cambio"])):


					if (!empty($_POST["numero_factura"]))
						$numero_factura=$_POST["numero_factura"];
					elseif(!empty($_GET["numero_factura"]))
						$numero_factura=$_GET["numero_factura"];

					if (empty($_POST["puntoventa"]) && empty($_GET["puntoventa"])):
						$msg="Debe seleccionar el punto de venta por favor verifique";
						mostrarfactura("mostrar","Buscar",$msg);
					else:
						if (!empty($_POST["puntoventa"]))
							$id_punto_venta = $_POST["puntoventa"];
						elseif (!empty($_GET["puntoventa"]))
							$id_punto_venta = $_GET["puntoventa"];


						if ($numero_factura=="reproceso"){
							// si es un reproceso no se necesita datos de factura ni cliente
							print_formcrear_garantia("0",$id_producto,$id_punto_venta,"guardarcambio","Guardar Cambio","Guardar Cambio");
						}
						elseif ($numero_factura=="mayorista"){
							// si es un reproceso no se necesita datos de factura ni cliente
							print_formcrear_garantia("0",$id_producto,$id_punto_venta,"guardarcambio","Guardar Cambio","Guardar Cambio");
						}
						elseif(!empty($_GET["numero_cambio"])){
							$sql_cambio = "SELECT * FROM Cambio WHERE IDCambio = '$_GET[numero_cambio]' and IDPuntoVenta = '".$id_punto_venta."'";
							$query_cambio = db_query($sql_cambio);
							if( db_num_rows( $query_cambio ) == 0 )
							{
								$msg="No se encontro el cambio por favor verifique";
								mostrarfactura("mostrar","Buscar",$msg);
							}//end if( db_num_rows( $query_cliente ) == 0 )
							else
							{
								$r_cambio = db_fetch_object( $query_cambio );
								//print_form($r_cliente->IDCliente,"insert","Generar Factura","Generar Factura");
								print_formgarantia($r_cambio->IDCambio,"creargarantia","Generar Cambio","Generar Cambio","cambio");
							}//end else



						}
						else{
							//$sql_factura = "SELECT * FROM Factura WHERE NumeroFactura = '$numero_factura' and IDPuntoVenta = '".$IDPuntoVenta."'";

							if(empty($_GET["FechaB"]))
								$fecha_cambio="2023-10-15 00:00:00";
							else
							  $fecha_cambio=$_GET["FechaB"]." 00:00:00";

							if ($_GET["tipofactura"]=="facturabono"){
								$sql_factura = "SELECT * FROM FacturaBono WHERE NumeroFacturaBono = '$numero_factura' and IDPuntoVenta = '".$id_punto_venta."' and FechaFacturaBono >= '2022-12-21'";
							}
							else{
								$sql_factura = "SELECT * FROM Factura WHERE NumeroFactura = '$numero_factura' and IDPuntoVenta = '".$id_punto_venta."' and FechaFactura >= '".$fecha_cambio."'";
							}


							//echo $sql_factura;

							$query_factura = db_query($sql_factura);
							if( db_num_rows( $query_factura ) == 0 )
							{
								$msg="No se encontro la factura por favor verifique.";
								mostrarfactura("mostrar","Buscar",$msg);
							}//end if( db_num_rows( $query_cliente ) == 0 )
							else
							{

								$r_factura = db_fetch_object( $query_factura );
								if ($_GET["tipofactura"]=="facturabono"){
									print_formgarantia($r_factura->IDFacturaBono,"creargarantia","Generar Cambio","Generar Cambio");
								}
								else{
									print_formgarantia($r_factura->IDFactura,"creargarantia","Generar Cambio","Generar Cambio");
								}
								//print_form($r_cliente->IDCliente,"insert","Generar Factura","Generar Factura");
								//print_formgarantia($r_factura->IDFactura,"creargarantia","Generar Garantia","Generar Garantia");
							}//end else
						}
					endif;
			 elseif(!empty($_POST["cedula_cliente"]) || !empty($_GET["cedula_cliente"])):
			 		if(!empty($_GET["cedula_cliente"]))
						$cedula_cliente_buscar = $_GET["cedula_cliente"];
					elseif(!empty($_POST["cedula_cliente"]))
						$cedula_cliente_buscar = $_POST["cedula_cliente"];

					$sql_cliente = "SELECT IDCliente FROM Cliente WHERE Cedula = '".$cedula_cliente_buscar ."'";
					$query_cliente = db_query($sql_cliente);
					if( db_num_rows( $query_cliente ) == 0 )
					{
						$msg="No se encontro el cliente por favor verifique";
						mostrarfactura("mostrar","Buscar",$msg);
					}//end if( db_num_rows( $query_cliente ) == 0 )
					else
					{
						while ($r_cliente = db_fetch_object( $query_cliente )){
							$array_clientes[]=$r_cliente->IDCliente;
						}
						//print_form($r_cliente->IDCliente,"insert","Generar Factura","Generar Factura");
						print_formfactura_cliente($array_clientes,"buscarfactura","Seleccionar factura","Seleccionar factura");
					}//end else

			 elseif(!empty($_POST["nombre_cliente"]) || !empty($_POST["apellido_cliente"])):
			 		if(!empty($_POST["nombre_cliente"]) && empty($_POST["apellido_cliente"])):
						$sql_cliente = "SELECT IDCliente FROM Cliente WHERE Nombre like '%".$_POST["nombre_cliente"]."%'";
					elseif(empty($_POST["nombre_cliente"]) && !empty($_POST["apellido_cliente"])):
						$sql_cliente = "SELECT IDCliente FROM Cliente WHERE Apellido like '%".$_POST["apellido_cliente"]."%'";
					elseif(!empty($_POST["nombre_cliente"]) && !empty($_POST["apellido_cliente"])):
						$sql_cliente = "SELECT IDCliente FROM Cliente WHERE Nombre like '%".$_POST["nombre_cliente"]."%' and Apellido like '%".$_POST["apellido_cliente"]."%'";
					endif;


					$query_cliente = db_query($sql_cliente);
					if( db_num_rows( $query_cliente ) == 0 )
					{
						$msg="No se encontro el cliente por favor verifique";
						mostrarfactura("mostrar","Buscar",$msg);
					}//end if( db_num_rows( $query_cliente ) == 0 )
					else
					{
						while ($r_cliente = db_fetch_object( $query_cliente )){
							$array_clientes[]=$r_cliente->IDCliente;
						}
						//print_r($array_clientes);

						//print_form($r_cliente->IDCliente,"insert","Generar Factura","Generar Factura");
						print_formfactura_cliente($array_clientes,"buscarfactura","Seleccionar factura","Seleccionar factura");
					}//end else
			 endif;


			break;

			case "quitaritem":
				$sql_elimina_item = "Delete From TMPProductoCambio Where IDTMPProductoCambio = '".$_GET["IDItem"]."'";
				db_query($sql_elimina_item);
				//mostrarfactura("mostrar","Buscar");
				print_formcrear_garantia($_GET["IDFactura"],$id_producto,$_GET["IDPuntoVenta"],"guardarcambio","Guardar Cambio","Guardar Cambio");
			break;

			case "quitartodoitem":
				$sql_elimina_item = "Delete From TMPProductoCambio Where Cookie = '".$_COOKIE["COOKIE_CLIENTE"]."'";
				db_query($sql_elimina_item);
				mostrarfactura("mostrar","Buscar");
			break;



			default :
				mostrarfactura("mostrar","Buscar");
			break;

		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col1");


function actualiza_fidelizacion($idcliente, $opciones, $abiertas = "" )
{


	foreach( $opciones as $idpregunta => $opcion )
	{
		$sql_delete = "DELETE FROM FidClienteRespuesta WHERE IDCliente = '" . $idcliente . "'  AND IDFidPregunta = '" . $idpregunta . "' ";
		$qry_delete = db_query( $sql_delete );

		//insertar respuesta
		$sql_insert = " INSERT INTO FidClienteRespuesta (IDCliente, IDFidPregunta, IDFidOpcion, Respuesta, FechaTrCr ) VALUES ( '" . $idcliente . "','" . $idpregunta . "','" . $opcion . "','" . $respuesta . "', NOW() ) ";
		$qry_insert = db_query( $sql_insert );

	}//end for

	//las respuestas abiertas
	foreach( $abiertas as $idpregunta => $value_respuesta )
	{
		$sql_verifica = " SELECT * FROM  FidClienteRespuesta WHERE IDCliente = '" . $idcliente . "'  AND IDFidPregunta = '" . $idpregunta . "' ";
		$qry_verifica = db_query( $sql_verifica );
		if( db_num_rows( $qry_verifica ) > 0  )
			if( !empty( $value_respuesta  ) )
			{
				//actualizar
				$sql_update = "UPDATE FidClienteRespuesta SET Respuesta = '" . $value_respuesta . "' WHERE IDCliente = '" . $idcliente . "'  AND IDFidPregunta = '" . $idpregunta . "'  ";
				db_query( $sql_update );
			}//end if
		elseif( !empty( $value_respuesta  ) )
		{
			//insertar la respuesta abierta
			$sql_insert = " INSERT INTO FidClienteRespuesta (IDCliente, IDFidPregunta, Respuesta, FechaTrCr ) VALUES ( '" . $idcliente . "','" . $idpregunta . "','" . $value_respuesta . "', NOW() ) ";
			$qry_insert = db_query( $sql_insert );
		}//end else
	}//end for

}//end fucntion fidelizacion

/*******************************************************************************************
		funtcion mostrarcedula
*******************************************************************************************/

function mostrarfactura($newmode,$submit_caption,$msg=""){
	global $IDPuntoVenta;
?>

<br>
<form name="frmcliente" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" onsubmit="disable(this);">
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="500">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><span class="gen"><strong>INGRESE CAMBIO</strong></span></td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table align="center" width="500" cellpadding="0" cellspacing="1" border="0" class="forumline">

  <tr>
    <td colspan="4" align="center" valign="rigth">Introduzca alguno de los siguientes datos por favor</td>
    </tr>
  <tr>
    <td width="138" align="center" valign="middle" class="col1">N&uacute;mero de la factura</td>
    <td colspan="3" class="col2" >
      <input type="text" class="tbox" name="numero_factura">

      <span class="col1">Punto Venta</span>
      <!--
      <select class=tbox name=puntoventa>
		  <option value="">Seleccione</option>
						<?
							$sql_puntos = "SELECT P.* FROM PuntoVenta P ";
							$sql_puntos .= "WHERE 1 Order By Nombre";

							$query_puntos = db_query( $sql_puntos );

							while( $r_puntos = db_fetch_object( $query_puntos ) )
							{
								if ($r_puntos->IDPuntoVenta == $IDPuntoVenta )
									$selecciona = " selected";
								else
									$selecciona = " ";

								echo "<option value=$r_puntos->IDPuntoVenta $selecciona>$r_puntos->Nombre</option>";

							}
						?>
						</select>

                        -->

      </td>
  </tr>
  <tr>
    <td class="col1" align="center" valign="middle">C&eacute;dula del cliente</td>
    <td colspan="3" class="col2" ><input type="text" class="tbox" name="cedula_cliente" /></td>
  </tr>
  <tr>
    <td class="col1" align="center" valign="middle">Nombre Cliente</td>
    <td width="144" class="col2" ><input type="text" class="tbox" name="nombre_cliente" /></td>
    <td width="68" class="col2" ><span class="col1">Apellido</span></td>
    <td width="145" class="col2" ><input type="text" class="tbox" name="apellido_cliente" /></td>
    </tr>
  <tr>
    <td colspan="4" align="center" valign="middle" >
      <input type="submit" class="button" name="enviar" value="<?=$submit_caption?>" />
    </td>
    </tr>
  <tr>
    <td colspan="4" align="center" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>

  <tr>
    <td class="col2list" align="center" valign="middle" colspan="4"><input type="hidden" value="<?=$newmode?>" name="action">
      </td>
  </tr>
  <tr>
	<td class="col2list" align="center" valign="middle" colspan="4" style="color:#F00; font-weight:bold">
    	<?php echo $msg; ?>
	</td>
  </tr>


</table>
</form>
<?
}//end	mostrar($newmode,$submit_caption)


/*******************************************************************************************
		funtcion Print_formCliente
*******************************************************************************************/
function print_formgarantia($id="",$newmode,$title,$submit_caption,$tipo_garantia="") {



	GLOBAL $TitleMod,$Table,$MOD,$Key,$cedula,$array_gustos,$array_deportes,$array_hobbies,$array_musica, $datos,$IDPuntoVenta;
	$qid = db_query(" SELECT * FROM Cliente WHERE Cedula = '$id' ");
	$r = db_fetch_object($qid);

?>

<br>
	<form name="frmproducto_garantia" id="frmproducto_garantia" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" class="frmproductogarantia">

<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="500">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />

		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
			Seleccione el producto que se ingresar&aacute; por garant&iacute;a</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table class="forumline" width="500" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline"   >

                <tr bgcolor="#dfe3e7">
                  <td align="center" class=rowform>Seleccionar</td>
                    <td align="center" class=rowform><b>Item</b></td>
                    <td align="center" class=rowform><b>Referencia</b></td>
                    <td align="center" class=rowform><b>Talla</b></td>
                    <td align="center" class=rowform><b>Nombre</b></td>
                    <td align="center" class=rowform><b>Cantidad</b></td>
                    <td align="center" class=rowform><b>Valor U.</b></td>
                    <td align="center" class=rowform><b>Descuento Par.</b></td>
                    <td align="center" class=rowform><b>Total</b></td>
                </tr>

                <?php
					if (!empty($_POST["puntoventa"]))
						$punto_consulta=$_POST["puntoventa"];
					else
						$punto_consulta=$_GET["puntoventa"];


					if ($_GET["tipofactura"]=="facturabono" && empty($_GET[numero_cambio])){
						$sql_detalle = "SELECT * FROM DetalleFacturaBono WHERE IDFacturaBono = '".$id."' and IDPuntoVenta = '".$punto_consulta."'";
					}
					elseif(empty($_GET[numero_cambio])){
					 	$sql_detalle = "SELECT * FROM DetalleFactura WHERE IDFactura = '".$id."' and IDPuntoVenta = '".$punto_consulta."'";
					}


					//echo $sql_detalle;
					if(!empty($sql_detalle)):
					$query_detalle = db_query($sql_detalle);
					$i = 1;
					while( $r_detalle = db_fetch_object( $query_detalle ) ){

							if( $i % 2 == 0 )
								$class = "row2";
							else
								$class = "rowtable";

								$nombre_item=get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));

					?>
                    <tr bgcolor="#dfe3e7">
                      <td align="left" class="<?=$class?>">
                        <?php if($nombre_item!="Excedente"):
							if ($_GET["tipofactura"]=="facturabono"){ ?>
                        	<input type="radio" name="IDProductoGarantia" id="IDProductoGarantia<?=$i?>" value="<?php echo $r_detalle->IDDetalleFacturaBono."|".$r_detalle->IDFacturaBono."|".$r_detalle->IDPuntoVenta."|".$r_detalle->ValorU."|".$r_detalle->IDCodificacionEspecifica;;  ?>">
                            <?php }
							else{ ?>
                            	<input type="radio" name="IDProductoGarantia" id="IDProductoGarantia<?=$i?>" value="<?php echo $r_detalle->IDDetalleFactura."|".$r_detalle->IDFactura."|".$r_detalle->IDPuntoVenta."|".$r_detalle->ValorU."|".$r_detalle->IDCodificacionEspecifica;  ?>">
                            <?php } ?>


                        <?php endif; ?>
                        </td>
                        <td align="left" class="<?=$class?>"><b><?=$i?></b></td>
                        <td align="left" class="<?=$class?>">
                        <?php if($nombre_item!="Excedente"):
                        			echo $nombre_item;
							else:?>
									 Por favor buscar cambio asignado  al numero de la factura
                        <?php endif; ?>
                        </td>
                        <td align="left" class="<?=$class?>"><%=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))%></td>
                        <td align="left" class="<?=$class?>"><%=get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)))%></td>
                        <td align="left" class="<?=$class?>"><?=$r_detalle->Cantidad?></td>
                        <td align="left" class="<?=$class?>"><?echo number_format($r_detalle->ValorU);?></td>
                        <td align="left" class="<?=$class?>"><?echo number_format($r_detalle->DescuentoPar);?></td>
                        <td align="left" class="<?=$class?>">
						<? //echo number_format( ( $r_detalle->ValorU * $r_detalle->Cantidad ) * ( 1 - ( $r_detalle->DescuentoPar / 100 ) ) );?>
						<? echo number_format( ( $r_detalle->ValorU * $r_detalle->Cantidad ) * ( 1 - ( $r_detalle->DescuentoPar / 100 ) ) );?>
						
					</td>
                    </tr>
                <?php
				$i++;
				} ?>

                <?php endif; ?>

                <?php
					if (!empty($_GET[numero_cambio])){
					//Si es de un cambio consulto las referncias del cambio
					$sql_detalle_cambio = "SELECT * FROM DetalleCambio WHERE IDCambio = '".$_GET[numero_cambio]."' and IDPuntoVenta = '".$punto_consulta."'";
					$query_detalle_cambio = db_query($sql_detalle_cambio);
					$i = 1;
					while( $r_detalle_cambio = db_fetch_object( $query_detalle_cambio ) ){

							if( $i % 2 == 0 )
								$class = "row2";
							else
								$class = "rowtable";

					?>
                    <tr bgcolor="#dfe3e7">
                      <td align="left" class="<?=$class?>">
                        <input type="radio" name="IDProductoGarantia" id="IDProductoGarantia<?=$i?>" value="<?php echo $r_detalle_cambio->IDDetalleCambio."|".$r_detalle_cambio->IDCambio."|".$r_detalle_cambio->IDPuntoVenta;  ?>"></td>
                        <td align="left" class="<?=$class?>"><b><?=$i?></b></td>
                        <td align="left" class="<?=$class?>"><%=get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle_cambio->IDCodificacionEspecifica)))%></td>
                        <td align="left" class="<?=$class?>"><%=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))%></td>
                        <td align="left" class="<?=$class?>"><%=get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle_cambio->IDCodificacionEspecifica)))%></td>
                        <td align="left" class="<?=$class?>"><?=$r_detalle->Cantidad?></td>
                        <td align="left" class="<?=$class?>"><?echo number_format($r_detalle_cambio->PrecioU);?></td>
                        <td align="left" class="<?=$class?>"><?echo number_format($r_detalle_cambio->DescuentoRef);?></td>
                        <td align="left" class="<?=$class?>"><?echo number_format( ( $r_detalle_cambio->ValorU * $r_detalle_cambio->Cantidad ) * ( 1 - ( $r_detalle_cambio->DescuentoRef / 100 ) ) );?></td>
                    </tr>
                <?php
				$i++;
				}
					} // end if
				?>


			<tr>
			<td colspan=9 align=center class="col2list">
            	<input type=hidden name=IDFactura id=IDFactura value="<?=$id ?>">
                <input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
				<input type=hidden name=action value=<?=$newmode?>>
                <input type=hidden name=tipo_factura value=<?=$tipo_garantia?>>
                <input type=hidden name=IDCambio value=<?=$_GET[numero_cambio]?>>


				<input type=submit name=submit value="<? echo $submit_caption ?>" id="boton_enviar_producto" class=submit >



			</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
	<?
}// End function print_formgarantia()






/*******************************************************************************************
		funtcion print_formfactura_cliente
*******************************************************************************************/
function print_formfactura_cliente($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$cedula,$array_gustos,$array_deportes,$array_hobbies,$array_musica, $datos,$IDPuntoVenta;

?>

<br>
	<form name="frmproducto_garantia" id="frmproducto_garantia" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" class="frmproductogarantia">

<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="500">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
			Seleccione la factura </span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table class="forumline" width="500" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline"   >

                <tr bgcolor="#dfe3e7">
	                <td align="center" class=rowform>Seleccionar</td>
                    <td align="center" class=rowform><b>Numero Factura</b></td>
                    <td align="center" class=rowform><b>Cliente</b></td>
                    <td align="center" class=rowform><b>PuntoVenta</b></td>
                    <td align="center" class=rowform><b>Fecha Factura</b></td>
                    <td align="center" class=rowform><b>Valor Total</b></td>
                    <td align="center" class=rowform><b>Observacion</b></td>
                </tr>

                <?php
					if (count($id)>0){
						$id_cliente_resultado=implode(",",$id);
					}
					else
						$id_cliente_resultado=0;

					$sql_detalle = "SELECT * FROM Factura WHERE IDCliente in (".$id_cliente_resultado.")  Order By IDFactura DESC";
					$query_detalle = db_query($sql_detalle);
					$i = 1;
					while( $r_detalle = db_fetch_object( $query_detalle ) ){

							if( $i % 2 == 0 )
								$class = "row2";
							else
								$class = "row2";

					?>
                    <tr bgcolor="#dfe3e7">
                        <td align="left" class="<?=$class?>">
                        &nbsp;
                        <?php
						$meses_maximo_cambio = 6;
                        //Valido si supera los 6 meses no se deja hacer cambio
						$fecha_hoy = date('Y-m-d');
						$nuevafecha_maxima = strtotime ( '+'.$meses_maximo_cambio.' month' , strtotime ( substr($r_detalle->FechaFactura,0,10) ) ) ;
						$nuevafecha_maxima = date ( 'Y-m-d' , $nuevafecha_maxima );
						//Valido si tiene autorizacion
						$sql_autorizacion = "Select * From AutorizacionCambio Where IDFactura = '".$r_detalle->IDFactura."' and IDPuntoVenta = '".$r_detalle->IDPuntoVenta."'";
						$result_autorizacion = db_query($sql_autorizacion);
						$total_autorizacion = (int)db_num_rows($result_autorizacion);

						if(strtotime($fecha_hoy) <= strtotime($nuevafecha_maxima) || $total_autorizacion>0):
							$supera_limite = "N";
						?>
                        	<a href='<? echo "?mod=cambioreferencia&action=mostrar&numero_factura="; echo $r_detalle->NumeroFactura; ?>&puntoventa=<?php echo $r_detalle->IDPuntoVenta; ?>'><img src='admin/images/edit.gif' border='0'></a>
                        <?php else:
                        	$supera_limite = "S";
                        endif; ?>

                        </td>
                        <td align="left" class="<?=$class?>"><?php echo $r_detalle->NumeroFactura;?></td>
                        <td align="left" class="<?=$class?>"><?php echo get_field("Cliente","Nombre","IDCliente",$r_detalle->IDCliente) . " " . get_field("Cliente","Apellido","IDCliente",$r_detalle->IDCliente); ?> </td>
                      <td align="left" class="<?=$class?>"><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_detalle->IDPuntoVenta); ?></td>
                        <td align="left" class="<?=$class?>"><?php echo $r_detalle->FechaFactura;?></td>
                        <td align="left" class="<?=$class?>">$<?php echo number_format($r_detalle->ValorTotal);?></td>
                        <td align="left" class="<?=$class?>">
                        <?php
						if($supera_limite=="S"): ?>
                        	<span style="color:#EE080C">Supera los <?php echo $meses_maximo_cambio; ?> por favor solicite autorizacion para cambio</span>
                        <?php endif; ?>

                        </td>
                    </tr>
                <?php
				$i++;
				} ?>



                <?php // FACTURAS BONOS
                $sql_detalle = "SELECT * FROM FacturaBono WHERE IDCliente in (".$id_cliente_resultado.")  Order By IDFacturaBono DESC";
					$query_detalle = db_query($sql_detalle);
					$i = 1;
					while( $r_detalle = db_fetch_object( $query_detalle ) ){

							if( $i % 2 == 0 )
								$class = "row2";
							else
								$class = "row2";

					?>
                    <tr bgcolor="#dfe3e7">
                        <td align="left" class="<?=$class?>">
                        &nbsp;
                        <?php
                        //Valido si supera los 6 meses no se deja hacer cambio
						$meses_maximo_cambio = 6;
						$fecha_hoy = date('Y-m-d');
						$nuevafecha_maxima = strtotime ( '+'.$meses_maximo_cambio.' month' , strtotime ( substr($r_detalle->FechaFacturaBono,0,10) ) ) ;
						$nuevafecha_maxima = date ( 'Y-m-d' , $nuevafecha_maxima );

						//Valido si tiene autorizacion
						$sql_autorizacion_bono = "Select * From AutorizacionCambioBono Where IDFactura = '".$r_detalle->IDFacturaBono."' and IDPuntoVenta = '".$r_detalle->IDPuntoVenta."'";
						$result_autorizacion_bono = db_query($sql_autorizacion_bono);
						$total_autorizacion_bono = (int)db_num_rows($result_autorizacion_bono);


						if(strtotime($fecha_hoy) <= strtotime($nuevafecha_maxima) || $total_autorizacion_bono>0):
							$supera_limite = "N";
						?>

                        <a href='<? echo "?mod=cambioreferencia&action=mostrar&numero_factura="; echo $r_detalle->NumeroFacturaBono; ?>&puntoventa=<?php echo $r_detalle->IDPuntoVenta; ?>&tipofactura=facturabono'><img src='admin/images/edit.gif' border='0'></a>
                        <?php else:
                        	$supera_limite = "S";
                        endif; ?>
                        </td>
                        <td align="left" class="<?=$class?>"><?php echo $r_detalle->NumeroFacturaBono;?></td>
                        <td align="left" class="<?=$class?>"><?php echo get_field("Cliente","Nombre","IDCliente",$r_detalle->IDCliente) . " " . get_field("Cliente","Apellido","IDCliente",$r_detalle->IDCliente); ?> </td>
                      <td align="left" class="<?=$class?>"><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_detalle->IDPuntoVenta); ?></td>
                        <td align="left" class="<?=$class?>"><?php echo $r_detalle->FechaFacturaBono;?></td>
                        <td align="left" class="<?=$class?>">$<?php echo number_format($r_detalle->ValorTotal);?></td>
                        <td align="left" class="<?=$class?>">
                        <?php
						if($supera_limite=="S"): ?>
                        	<span style="color:#EE080C">Supera los <?php echo $meses_maximo_cambio; ?> por favor solicite autorizacion para cambio!.</span>
                        <?php endif; ?>

                        </td>
                    </tr>
                <?php
				$i++;
				} ?>



                <?php
				    //$sql_detalle_cambio = "SELECT * FROM Cambio WHERE IDCliente in (".$id_cliente_resultado.") and IDFacturaCambio	= 0 and IDFactura = 0  Order By IDCambio DESC";
					//$sql_detalle_cambio = "SELECT * FROM Cambio WHERE IDCliente in (".$id_cliente_resultado.") and IDFactura = 0  Order By IDCambio DESC";
					$sql_detalle_cambio = "SELECT * FROM Cambio WHERE IDCliente in (".$id_cliente_resultado.") and (IDFactura = 0 or IDFactura > 0)  Order By IDCambio DESC";
					$query_detalle_cambio = db_query($sql_detalle_cambio);
					$i = 1;
					while( $r_detalle_cambio = db_fetch_object( $query_detalle_cambio ) ){

							if( $i % 2 == 0 )
								$class = "row2";
							else
								$class = "row2";

					?>
                    <tr bgcolor="#dfe3e7">
                        <td align="left" class="<?=$class?>">
                        &nbsp;

                        <?php
                        //Valido si supera los 6 meses no se deja hacer cambio
						$fecha_hoy = date('Y-m-d');
						$nuevafecha_maxima = strtotime ( '+'.$meses_maximo_cambio.' month' , strtotime ( substr($r_detalle_cambio->FechaCambio,0,10) ) ) ;
						$nuevafecha_maxima = date ( 'Y-m-d' , $nuevafecha_maxima );

						//Valido si tiene autorizacion
						$sql_autorizacion = "Select * From AutorizacionCambioReferencia Where IDCambio = '".$r_detalle_cambio->IDCambio."' and IDPuntoVenta = '".$r_detalle_cambio->IDPuntoVenta."'";
						$result_autorizacion = db_query($sql_autorizacion);
						$total_autorizacion_cambio = (int)db_num_rows($result_autorizacion);

						

						if(strtotime($fecha_hoy) <= strtotime($nuevafecha_maxima) || $total_autorizacion_cambio>0):
							//if(1<= 1):
							$supera_limite = "N";
						?>
                        <a href='<? echo "?mod=cambioreferencia&action=mostrar&numero_cambio="; echo $r_detalle_cambio->IDCambio; ?>&puntoventa=<?php echo $r_detalle_cambio->IDPuntoVenta; ?>'><img src='admin/images/edit.gif' border='0'></a>
                        <?php else:
						$meses_maximo_cambio = 6;
                        	$supera_limite = "S";
                        endif; ?>
                        </td>
                        <td align="left" class="<?=$class?>"><font color="#EE080C"> Cambio Numero: </font>  <?php echo $r_detalle_cambio->IDCambio;?></td>
                        <td align="left" class="<?=$class?>"><font color="#EE080C">Cambio </font> <?php echo get_field("Cliente","Nombre","IDCliente",$r_detalle_cambio->IDCliente) . " " . get_field("Cliente","Apellido","IDCliente",$r_detalle_cambio->IDCliente); ?> </td>
                      <td align="left" class="<?=$class?>"><font color="#EE080C">Cambio</font> <?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_detalle_cambio->IDPuntoVenta); ?></td>
                        <td align="left" class="<?=$class?>"><font color="#EE080C">Cambio</font><br>  <?php echo $r_detalle_cambio->FechaCambio;?></td>
                        <td align="left" class="<?=$class?>">$<?php $valor_cambio = get_field("DetalleCambio","ValorU","IDCambio",$r_detalle_cambio->IDCambio); echo number_format($valor_cambio);?></td>
                        <td align="left" class="<?=$class?>">
                        <?php
						if($supera_limite=="S"): ?>
                        	<span style="color:#EE080C">Supera los <?php echo $meses_maximo_cambio; ?> por favor solicite autorizacion para cambio</span>
                        <?php endif; ?>

                        </td>
                    </tr>
                <?php
				$i++;
				} ?>


			<tr>
			<td colspan=10 align=center class="col2list">
            	<input type=hidden name=IDFactura id=IDFactura value="<?=$id ?>">
                <input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
				<input type=hidden name=action value=<?=$newmode?>>
				<input type=submit name=submit value="<? echo $submit_caption ?>" id="boton_enviar_producto" class=submit >



			</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
	<?
}// End function print_formfactura_cliente()







/*******************************************************************************************
funtcion Print_formCrear garantia
*******************************************************************************************/
function print_formcrear_garantia($id="",$id_producto,$IDPuntoVenta,$newmode,$title,$submit_caption,$tipo_factura="") {




	GLOBAL $TitleMod,$Table,$MOD,$Key,$cedula,$array_gustos,$array_deportes,$array_hobbies,$array_musica, $datos, $IVA;

	$habilita_descuento="N";

	$sql_link = "Select * From LinkCambio Where RelacionesPublicas = 'S' Limit 1";
	$result_link = db_query($sql_link);
	$row_link = db_fetch_array($result_link);
	$sql_link_pto = "Select * From PuntoVentaLink Where IDPuntoVenta = '".$datos["IDPuntoVenta"]."' and IDLinkCambio = '".$row_link["IDLinkCambio"]."' Limit 1";
	$result_link_pto = db_query($sql_link_pto);
	$total_link_pto = db_num_rows($result_link_pto);


	if((int)$total_link_pto>0)
		$habilita_descuento="S";

	if($tipo_factura=="cambio"){
						  $sql_datos_factura=db_query("Select * From Cambio Where IDCambio = '".$id."'");
						  $r_factura=db_fetch_array($sql_datos_factura);
					  }
					  else{

						  if ($_GET["tipofactura"]=="facturabono"){
							$sql_datos_factura=db_query("Select * From FacturaBono Where IDFacturaBono = '".$id."' and IDPuntoVenta = '".$IDPuntoVenta."'");
							$r_factura=db_fetch_array($sql_datos_factura);
						  }
						  else{
							  $sql_datos_factura=db_query("Select * From Factura Where IDFactura = '".$id."' and IDPuntoVenta = '".$IDPuntoVenta."'");
							$r_factura=db_fetch_array($sql_datos_factura);
						  }


					}



	if(strtotime($r_factura["FechaFactura"])<=strtotime("2017-01-31")):
	 	$IVA = 0.16;
	 endif;


?>



<br>
	<FORM name="frm" id="frmcambio" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" <?if($newmode!="delete"){?>onsubmit="disable(this);return EvaluarFunciones(this , Check);"<?}?>>

<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="500">

	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				GENERAR </span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table class="forumline" width="500" cellspacing="1" border="0" align="center">
	<tr>
	<td>
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
														<input type="hidden" value="<?=$datos["IDPuntoVenta"]?>" name="IDPuntoVenta"></td>
												</tr>
												<tr>
												  <td class=col1>El cliente que realiza el cambio es el mismo que compr&oacute;?</td>
												  <td class=col2 colspan="3">
                                                  <input type="radio" name="MismoCliente" value="S" class="btnmismocliente" required> SI
                                                  <input type="radio" name="MismoCliente" value="N" class="btnmismocliente"> NO
                                                  </td>
											  </tr>
												<tr>
												  <td colspan="4" class=col1>

                                                  <div id="divcliente" style="display:<?php echo "none"; ?>">
                                                  <table width="100%" border="0">
												    <tbody>
												      <tr class="rowtable">
												        <td class=row1 colspan="4"><b>CLIENTE</b></td>
											          </tr>
												      <tr class="rowtable">
												        <td class=col1>C&eacute;dula</td>
												        <td class=col2><?php
                                                         $id_cliente=$r_factura[IDCliente];
														 $sql_cliente = "Select * From Cliente Where IDCliente = '".$id_cliente."'";
														 $result_cliente=db_query($sql_cliente);
														 $row_cliente = db_fetch_array($result_cliente);
														 $cedula_cliente = $row_cliente["Cedula"];
														 ?>
												          <input type="text" class="tbox" name="Cedula" readonly size="15" value='<?php echo $row_cliente["Cedula"];?>'>
												          <input type="hidden" name="IDCliente" id="Cliente" value="<?=$row_cliente["IDCliente"]?>"></td>
												        <td class=col1>Nombre</td>
												        <td class=col2><input type="text" class="tbox" name="NombreCliente" readonly size="20" value='<?php echo $row_cliente["Nombre"]." ".$row_cliente["Apellido"]?>'></td>
											          </tr>
												      <tr class="rowtable">
												        <td class=col1 nowrap>Telefono Cliente</td>
												        <td class=col2><input type="text" class="tbox" name="TeleCli" readonly size="15" value='<?php echo $row_cliente["Telefono"];?>'></td>
												        <td class=col1></td>
												        <td class=col1></td>
											          </tr>
											        </tbody>
											      </table>
                                                  </div>


                                                  <div id="divclientenuevo" style="display:<?php echo "none"; ?>">
                                                      <table width="100%" border="0">
                                                        <tbody>
                                                          <tr class="rowtable">
                                                            <td class=row1 colspan="4"><b>OTRO CLIENTE</b></td>
                                                          </tr>
                                                          <tr class="rowtable">
                                                            <td class=col1>C&eacute;dula</td>
                                                            <td class=col2>
                                                              <input type="text" class="tbox" name="CedulaNuevo"  size="15" value=''>
                                                            </td>
                                                            <td class=col1>Nombre</td>
                                                            <td class=col2><input type="text" class="tbox" name="NombreClienteNuevo"  size="20" value=''></td>
                                                          </tr>
                                                          <tr class="rowtable">
                                                            <td class=col1 nowrap>Telefono Cliente</td>
                                                            <td class=col2><input type="text" class="tbox" name="TeleCliNuevo"  size="15" value=''></td>
                                                            <td class=col1>Apellido</td>
                                                            <td class=col2>
                                                              <input type="text" class="tbox" name="ApellidoClienteNuevo"  size="20" value=''>
                                                            </span></td>
                                                          </tr>
                                                        </tbody>
                                                      </table>
                                                  </div>



                                                  </td>
											  </tr>

												<tr>
													<td class=col1>Observaciones</td>
													<td class=col2 colspan="3"><textarea class="tareabox" name="Observaciones" rows="4" cols="64"><?=$r->Observaciones?></textarea></td>
												</tr>
													<tr>
														<td class=row1 colspan="4"><b>VENDEDOR</b></td>
													</tr>
												<tr>
													<td class=col1>Seleccione Vendedor</td>
													<td class=col2>

													<? echo formpopup("Empleado WHERE IDPuntoVenta = '".$datos["IDPuntoVenta"]."' and Publicar = 'S' ","Nombre","Apellidos","IDEmpleado",$r->IDIDEmpleado,"input\" id=\"Empleado"); ?></td>
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
													<td class=col2><input type="text" class="tbox" name="Excedente" readonly size="15" value="<?echo $r_factura->Excedente?>"></td>
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


                                                     <?php
					  // datos producto




					  if($tipo_factura=="cambio"){
						  $datos_producto = explode("|",$id_producto);
						  $IDDetalleCambio = $datos_producto[0];
						  $IDCambio=$datos_producto[1];
						  $IDPuntoVenta=$datos_producto[2];

						  $r_detalle->IDDetalleFactura."|".$r_detalle->IDFactura."|".$r_detalle->IDPuntoVenta;
						  $sql_producto="select * from DetalleCambio Where IDDetalleCambio='".$IDDetalleCambio."' and IDCambio = '".$IDCambio."' and IDPuntoVenta = '".$IDPuntoVenta."'";
						  $qry_producto=db_query($sql_producto);
						  $r_detalle=db_fetch_object($qry_producto);

					  }
					  else{

					  $datos_producto = explode("|",$id_producto);
					  $IDDetalleFactura = $datos_producto[0];
					  $IDFactura=$datos_producto[1];
					  $IDPuntoVenta=$datos_producto[2];
					  $ValorItem=$datos_producto[3];
					  if((int)$r_factura["IDAlianza"] >0 && $r_factura["DescuentoAlianza"]>0):
					  	$ValorItem = $ValorItem -($ValorItem*$r_factura["DescuentoAlianza"]/100);
					  endif;



					  $IDCodificacionEspecifica=$datos_producto[4];

					  $r_detalle->IDDetalleFactura."|".$r_detalle->IDFactura."|".$r_detalle->IDPuntoVenta;

					  if ($_GET["tipofactura"]=="facturabono"){
					  	$sql_producto="select * from DetalleFacturaBono Where IDDetalleFacturaBono='".$IDDetalleFactura."' and IDFacturaBono = '".$IDFactura."' and IDPuntoVenta = '".$IDPuntoVenta."'";
					  }
					  else{
						$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$IDDetalleFactura."' and IDFactura = '".$IDFactura."' and IDPuntoVenta = '".$IDPuntoVenta."'";
					  }


					  $qry_producto=db_query($sql_producto);
					  $r_detalle=db_fetch_object($qry_producto);
					  }

					  ?>




                          <?php

						  if($_GET[numero_factura]!="mayorista"):

						  if($tipo_factura=="cambio"){
							$id_referencia_item=160;
						  }
						   else{
						  	$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
						   }

						  if ($id_referencia_item==160){ // Cuando son excedentes consulto la referencia de la compra


							$sql_facturabono=db_query("Select * from FacturaBono Where IDFactura = '".$id."' and IDPuntoVenta = '".$IDPuntoVenta."'");
							$r_facturabono=db_fetch_array($sql_facturabono);
							if (!empty($r_facturabono[IDFacturaBono])){
								$sql_detallefacturabono=db_query("Select * from DetalleFacturaBono Where IDFacturaBono = '".$r_facturabono[IDFacturaBono]."'");
								while($r_detallefacturabono=db_fetch_array($sql_detallefacturabono)){
									$id_referncia_bono=$r_detallefacturabono[IDCodificacionEspecifica];
									$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));
									$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										$array_bonos[]=$id_tipo_ref;
										$array_bono_especifico[]=$id_referncia_bono;

									?>
                                    <input type="radio" name="IDDetalleFacturaBono" value="<?php echo $r_detallefacturabono[IDFacturaBono] ."|" . $r_detallefacturabono[IDDetalleFacturaBono] ?>">
                                    <?php
									$nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
									$precio_referencia=get_field("Referencia","IDPrecio","IDReferencia",$id_referencia_item);

								}

							}
							else{

								//Busco si es un cambio
								if(!empty($r_detalle->IDFactura)):
									$sql_cambio=db_query("Select C.* from Cambio C, DetalleCambio DC Where C.IDCambio = DC.IDCambio and IDFactura = '".$r_detalle->IDFactura."' and IDCliente = '".$id_cliente."' order by IDCambio Desc  limit 1");
								else:
									$sql_cambio=db_query("Select C.* from Cambio C, DetalleCambio DC Where C.IDCambio = DC.IDCambio and C.IDCambio = '".$r_detalle->IDCambio."' and IDCliente = '".$id_cliente."' order by IDCambio Desc  limit 1");

								endif;


								$r_cambio=db_fetch_array($sql_cambio);
								if (!empty($r_cambio[IDCambio])):
									$cambio="S";
									$sql_detalle_cambio=db_query("Select * from DetalleCambio Where IDCambio = '".$r_cambio[IDCambio]."' and IDDEtalleCambio = '".$_POST["IDProductoGarantia"]."'");
									while($r_detalle_cambio=db_fetch_array($sql_detalle_cambio)):
										$id_referncia_cambio=$r_detalle_cambio[IDCodificacionEspecifica];
										$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle_cambio[IDCodificacionEspecifica])));
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										$array_cambios[]=$id_tipo_ref;
										$array_especifica[]=$id_referncia_cambio;
										?>
                                        <!--
                                        <input type="radio" name="IDDetalleCambio" value="<?php echo $r_detalle_cambio[IDCambio] ."|" . $r_detalle_cambio[IDDetalleCambio] ?>">
                                        -->
                                        <?php
									    $nombre_referencia = get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
										$precio_referencia=get_field("Referencia","IDPrecio","IDReferencia",$id_referencia_item);
										$ValorItem=$r_detalle_cambio["ValorU"];
										$IDCodificacionEspecifica=$r_detalle_cambio["IDCodificacionEspecifica"];


									endwhile;

								endif;
								//$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));


							}

						  }
						  else{

							  $id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
							  $nombre_referencia = get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
							  $precio_referencia=get_field("Referencia","IDPrecio","IDReferencia",$id_referencia_item);

						  }

					  ?>
                          <?php  if ($_GET[numero_factura]=="reproceso" || $_GET[numero_factura]=="mayorista" ){



							$sql_referencias = "SELECT R.IDReferencia, R.Numero, PVR.IDPuntoVentaReferencia
													FROM Referencia R, PuntoVentaReferencia PVR
													WHERE PVR.IDPuntoVenta = '".$datos[IDPuntoVenta]."'
													AND PVR.IDReferencia = R.IDReferencia
													AND R.Publicar <> 'N'
													ORDER BY R.Numero";

							/*
							$sql_referencias = "SELECT R.IDReferencia, R.Numero
													FROM Referencia R
													WHERE
													R.Publicar <> 'N'
													ORDER BY R.Numero";

							*/
							$qry_referencias = db_query( $sql_referencias );
							$i = 0;
							while( $r_referencias = db_fetch_array( $qry_referencias ) )
							{
								$array_referencias[$i] = $r_referencias;
								$i++;
							}//end while

						  ?>
							<?php }  ?>

                          <?php else: ?>

                          Referencia mayorista
                              <input type="text" name="ColorMayorista" id="ColorMayorista" value="">

                          <?php endif; ?>



                          <?php
							if (count($array_especifica)>0):
								foreach($array_especifica as $id_especifica):
									$nombre_talla = get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$id_especifica));
								endforeach;
						  elseif (count($array_bonos)>0):
								foreach($array_bonos as $id_especifica):
									$nombre_talla =  get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$id_especifica));
								endforeach;

						  else:
						  	$nombre_talla = get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica));
						  endif;
						  ?>




                          <?php  if ($_GET[numero_factura]=="reproceso"){
						  }
						  else{ ?>

                          <?php
						  if (count($array_cambios)>0):
							foreach($array_cambios as $id_especifica):
								$nombre_tipo_ref =  get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_especifica) ;
							endforeach;
						  elseif (count($array_bonos)>0):
							foreach($array_bonos as $id_especifica):
								$nombre_tipo_ref =  get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_especifica);
							endforeach;

						  else:
						  	$nombre_tipo_ref = get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
						  endif;

							?>
                          <?php } ?>


													  <table class="texto" border="0" cellspacing="1" cellpadding="0" id=table1>
														  <tr bgcolor="#dfe3e7">
															  <td align="center"><b></b></td>
															  <td align="center"><b>Referencia</b></td>
															  <td align="center"><b>Talla</b></td>
															  <td align="center"><b>Nombre</b></td>
															  <td align="center"><b>Cantidad</b></td>
															  <td align="center"><b>Valor U.</b></td>
															  <td align="center"><b>Descuento Par.</b></td>
															  <td align="center"><b>Total</b></td>
																<td align="center"><b>Eliminar</b></td>
														  </tr>

                                                          <?php



														  //Ve aumento el iva aplicado en ese momento cuando no sea sobre cambio
														  if($_GET[numero_cambio]!="" || $_GET[tipofactura]=="facturabono"){
														 	 $ValorItem=$ValorItem;
														  }

														else
														{
															$ValorItem=($ValorItem + ($ValorItem*$IVA));
														}



														//si es de una factura de medellin con credito le quito el 6%
														$IDCiudad= get_field("PuntoVenta","IDCiudad","IDPuntoVenta",$_GET["puntoventa"]);
														if($IDCiudad==2 && empty($_GET["numero_cambio"])):
															$sql_credito="select * from CreditoCuota Where IDFactura = '".$id."'";
															$result_credito=db_query($sql_credito);
															$total_credito=db_num_rows($result_credito);
															if((int)$total_credito>0):
																$ValorItem=$ValorItem  - ($ValorItem*5.7/100);
																//echo $ValorItem;
															endif;
														endif;








														//echo "Valor " . $ValorItem;

															//consulto el descuento aplicado
															$sql_factura_padre = "Select * From Factura Where NumeroFactura = '".$_GET["numero_factura"]."' and IDPuntoVenta = '".$_GET["puntoventa"]."' Order by IDFactura DESC";
															$result_factura_padre = db_query($sql_factura_padre);
															$row_factura_padre = db_fetch_array($result_factura_padre);




														if((int)$r_detalle->DescuentoPar>0):
															$ValorItem = (int)$r_detalle->ValorU - ((int)$r_detalle->ValorU*(int)$r_detalle->DescuentoPar/100);

															$ivaitem= (int)$ValorItem*$IVA;
															$ValorItem+=$ivaitem;

															$row_factura_padre["DescuentoAlianza"];

															switch($row_factura_padre["DescuentoAlianza"]):
																case "15":
																	$sumar_porcentaje=1.176;
																break;
																case "10":
																	$sumar_porcentaje=1.1;
																break;
																default:
																	$sumar_porcentaje=1.1;

															endswitch;





															$ivaitem= (int)$ValorItem*($row_factura_padre["DescuentoAlianza"]+$sumar_porcentaje)/100;
															$ValorItem+=$ivaitem;

															//echo $ValorItem;
														endif;


															if((int)$row_factura_padre["DescuentoAlianza"]>0):
																$ValorItem=$r_detalle->PrecioU;
															endif;


															
													if( ((int)$r_detalle->DescuentoPar>0 && (int)$row_factura_padre["DescuentoAlianza"]<=0)):
															$ValorItem=$r_detalle->PrecioU;															
													endif;

													echo $mystring = $row_factura_padre["ObservacionDescuento"];
													$findme   = 'estar en semana de cumplea';
													$pos = strpos($mystring, $findme);
													if ($pos === false) {
														//echo "aca";
													} else {
														//$ValorItem=$r_detalle->PrecioU-($r_detalle->PrecioU*$r_detalle->DescuentoPar/100);
													}

													


													//if($_GET["numero_factura"]==91312 && $_GET["puntoventa"]==6):
													if($_GET["numero_factura"]==6636):
													 $ValorItem=69882;
												 endif;


													//Jaime Solicita que cuando es menor a esta fecha consultar el precio actual													
													if($row_factura_padre["FechaFactura"]<='2019-07-19' || $row_factura_padre["FechaFactura"]<='2022-12-15'):
														

																//consulto el precio actual


																 $IDRef = get_field("Referencia","IDReferencia","Numero",$nombre_referencia);
																 $IDPrecio = get_field("Referencia","IDPrecio","IDReferencia",$IDRef);
																 $ValorVenta = get_field("Precio","ValorVenta","IDPrecio",$IDPrecio);
																 $Descuento = get_field("Precio","Descuento","IDPrecio",$IDPrecio);

																if((int)$Descuento>0):																	
																	$ValorItem = $ValorVenta - ($ValorVenta * $Descuento / 100);

																else:																	
																	$ValorItem = $ValorVenta;
																endif;
													endif;

													//Jaime Solicita que cuando esel producto tenga descuento del 10% traiga el valor sin descuento
													if($r_detalle->DescuentoRef==10):
														//consulto el precio actual
														 $IDRef = get_field("Referencia","IDReferencia","Numero",$nombre_referencia);
														 $IDPrecio = get_field("Referencia","IDPrecio","IDReferencia",$IDRef);
														 $ValorVenta = get_field("Precio","ValorVenta","IDPrecio",$IDPrecio);
														 $ValorItem = $ValorVenta;
														
													endif;


													

													if((int)$r_factura["ValorIVA"]<=0):
														$ValorItem=$r_detalle->ValorU;
														
												endif;

														  //Guardo los datos en la tabal de temporal de cambios si no existe
															$verica_producto = "Select IDTMPProductoCambio From TMPProductoCambio Where Cookie = '".$_COOKIE["COOKIE_CLIENTE"]."' and Nombre = '".$nombre_referencia."' and Talla = '".$nombre_talla."'";
															$result_producto = db_query($verica_producto);
															if(db_num_rows($result_producto)<=0 && !empty($nombre_referencia) && !empty($nombre_talla) && !empty($IDCodificacionEspecifica)):
																$sql_tmp_cambio = "Insert into TMPProductoCambio (Cookie, Nombre, Talla, IDCodificacionEspecifica, ValorItem)
																				   Values ('".$_COOKIE["COOKIE_CLIENTE"]."','".$nombre_referencia."','".$nombre_talla."','".$IDCodificacionEspecifica."','".$ValorItem."')";
																db_query($sql_tmp_cambio);
															endif;

															//Cosnulto que tiene de cambios seleccionados
															$contador_ref = 1;
															$sql_cambios="Select * From TMPProductoCambio Where Cookie = '".$_COOKIE["COOKIE_CLIENTE"]."'";
															$result_cambios = db_query($sql_cambios);
															while($row_cambios = db_fetch_array($result_cambios)):

                                                              //Averiguo la codificacion especifica de esta referencia en este almacen si no existe lo dejo en blanco
															 $sqlcodifesp="select IDPuntoVentaReferencia, IDTalla From CodificacionEspecifica  Where IDCodificacionEspecifica = '".$row_cambios["IDCodificacionEspecifica"]."'";
															 $resultcodifesp=db_query( $sqlcodifesp);
															 $row_codif_esp=db_fetch_array($resultcodifesp);
															 $sql_ptoref = "Select IDReferencia From PuntoVentaReferencia Where IDPuntoVentaReferencia = '". $row_codif_esp["IDPuntoVentaReferencia"]."'";
															 $result_ptoref=db_query( $sql_ptoref);
															 $row_pto_ref=db_fetch_array($result_ptoref);
															 $sql_ptoref_tienda = "Select IDPuntoVentaReferencia From PuntoVentaReferencia Where IDReferencia = '". $row_pto_ref["IDReferencia"]."' and IDPuntoVenta = '".$datos["IDPuntoVenta"]."'";
															 $result_ptoref_tienda=db_query( $sql_ptoref_tienda);
															 $row_pto_ref_tienda=db_fetch_array($result_ptoref_tienda);
															 $sqlcodifesp_tienda="select IDCodificacionEspecifica From CodificacionEspecifica  Where IDPuntoVentaReferencia = '".$row_pto_ref_tienda["IDPuntoVentaReferencia"]."' and IDTalla = '".$row_codif_esp["IDTalla"]."'";
															 $resultcodifesp_tienda=db_query( $sqlcodifesp_tienda);
															 $row_codif_esp_tienda=db_fetch_array($resultcodifesp_tienda);
															 if(empty($row_codif_esp_tienda["IDCodificacionEspecifica"])):
															 	$row_cambios["Nombre"] = "No existe";
															else:
																$row_cambios["IDCodificacionEspecifica"] = $row_codif_esp_tienda["IDCodificacionEspecifica"];
															 endif;
															?>

                                                                  <tr>
                                                                      <td align="left"><b><?php echo $contador_ref; ?></b></td>
                                                                      <td align="left"><input type=text readonly name=Numero<?php echo $contador_ref; ?> class=tbox size=7 value="<?php echo $row_cambios["Nombre"]; ?>"></td>
                                                                      <td align="left"><input type=text readonly name=Talla<?php echo $contador_ref; ?> class=tbox size=5 value="<?php echo $row_cambios["Talla"]; ?>"></td>
                                                                      <td align="left"><input type=text readonly name=Nombre<?php echo $contador_ref; ?> class=tbox size=15 value="<?php echo $row_cambios["Nombre"]; ?>"></td>
                                                                      <td align="left">

                                                                      <input type="hidden" name="IDCodificacion<?php echo $contador_ref; ?>" value="<?php echo $row_cambios["IDCodificacionEspecifica"]; ?>" />

                                                                      <input type=text name=Cantidad<?php echo $contador_ref; ?> class=tbox size=5 value="1" onblur="calculac2(<?php echo $contador_ref; ?>);" ></td>
                                                                      <td align="left">
                                                                        <?php
                                                                        //$valor_item=$precio_referencia
                                                                        //$valor_item=get_field("Precio","Descripcion","IDTipoReferencia",$id_especifica
                                                                        ?>
                                                                      <input type=text readonly name=ValorU<?php echo $contador_ref; ?> class=tbox size=15 value="$<?php echo number_format($row_cambios["ValorItem"]); ?>"></td>
                                                                      <td align="center"><input type=hidden name="DescuentoLin<?php echo $contador_ref; ?>" value="" onblur="calculac2(<?php echo $contador_ref; ?>);" class=tbox size=2 maxlength="2"></td>
                                                                      <td align="left"><input type=text readonly name=Total<?php echo $contador_ref; ?> class=tbox size=15 value="$<?php echo number_format($row_cambios["ValorItem"]); ?>">
                                                                      <input type="hidden" name="Precio<?php echo $contador_ref; ?>" value="<?php echo $row_cambios["ValorItem"]; ?>" />

                                                                      </td>
                                                                        <td align="center"><a href="?mod=cambioreferencia&action=quitaritem&IDItem=<?php echo $row_cambios["IDTMPProductoCambio"]?>&IDFactura=<?php echo $id; ?>&IDPuntoVenta=<?php echo $IDPuntoVenta; ?>&tipofactura=<?php echo $_GET["tipofactura"]?>"><img src='images/trash.gif' border='0'></a></td>
                                                                  </tr>
                                                          <?php
															  $contador_ref++;
														  endwhile; ?>

                                                          <tr>
														    <td align="left">&nbsp;</td>
														    <td align="left">
                                                            </td>
														    <td align="left">&nbsp;</td>
														    <td align="left">&nbsp;</td>
														    <td align="left">&nbsp;</td>
														    <td align="left">&nbsp;</td>
														    <td colspan="2" align="center">&nbsp;</td>
														    <td align="left">&nbsp;</td>
												        </tr>
														  <tr>
														    <td align="left">&nbsp;</td>
														    <td align="left">
                                                            <input type="hidden" name="TotalCambiar" id="TotalCambiar" value="" />
                                                            <input type="hidden" name="TotalItemCambiar" id="TotalItemCambiar" value="<?php echo ($contador_ref-1); ?>" />
                                                            </td>
														    <td align="left">&nbsp;</td>
														    <td align="left">&nbsp;</td>
														    <td colspan="2" align="left"><img src='images/next.gif' border='0'><a href="?mod=cambioreferencia&action=mostrar&cedula_cliente=<?php echo $cedula_cliente ?>">A&ntilde;adir otro producto</a></td>
														    <td colspan="3" align="center"><a href="?mod=cambioreferencia"></a><a href="?mod=cambioreferencia&action=quitartodoitem"><img src='images/trash.gif' border='0'>Quitar Todos</a></td>
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
												<?
												$contador_ref_cambio=1;
												for( $i = 10; $i < 15; $i++ )
												{
												?>
												<tr>
													<td align="left"><b><?php echo $contador_ref_cambio; ?></b></td>
													<td align="left"><input type=text readonly name=Numero<?=$i?> class=tbox size=7></td>
													<td align="left"><input type=text readonly name=Talla<?=$i?> class=tbox size=5 ></td>
													<td align="left"><input type=text readonly name=Nombre<?=$i?> class=tbox size=15></td>
													<td align="left"><input type=hidden name=IDCodificacion<?=$i?>></td>
													<td align="left"><input type=text name=Cantidad<?=$i?> class=tbox size=5 onblur="if(!compruebamaximo(this.value,<?=$i?>)) this.value = ''; else calculatotal(this.value,<?=$i?>);"></td>
													<td align="left"><input type=text readonly name=ValorU<?=$i?> class=tbox size=15></td>
													<td align="center"><input type=text name="DescuentoLin<?=$i?>" onblur="calculatotal(this.value,<?=$i?>);" class=tbox size=2 maxlength="2" <?php if($habilita_descuento=="N" ) echo "readonly"; ?>></td>
													<td align="left"><input type=text readonly name=Total<?=$i?> class=tbox size=15 ></td>
													<td align="left"><input type=button name=Agregar<?=$i?> class=submit value=Referencia onclick="window.open('Referencia/popReferencias.php?IDPuntoVenta=<?=$datos["IDPuntoVenta"]?>&cont=<?=$i?>','','width=450,height=400');"></td>
													<td align="left"><input type=hidden name=Maximo<?=$i?>></td>
													<td align="left"><input type=hidden name=Precio<?=$i?>></td>
													<td align="left"><input type=hidden name=Descuento<?=$i?>></td>
												</tr>
												<?
													$contador_ref_cambio++;
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
								<input type=hidden name=ITEM><input type="hidden" name="action" value="<?=$newmode?>">
									<input type="submit" id="btn_guardar_cambio" class="button guardar_cambio" name="submit" value="<?=$submit_caption?>"></div>

					</td>
				</tr>
			</table>
		</td>
	</tr>

</table>
		</td>
	</tr>
</table>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
  <script src="admin/jscripts/choosen/chosen.jquery.js" type="text/javascript"></script>
  <script src="admin/jscripts/choosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>

</form>
	<?
}// End function print_formgarantia()


?>
</BODY></HTML>
