<?php

error_reporting(E_ALL);
include("/var/www/vhosts/almacenescaprino.com/httpdocs/admin/config.inc.php");

/**** ELIMINAR FACTURAS ****

    
   $qry_fact = db_query("SELECT IDFactura from Factura where year(FechaFactura) = 2014 and month(FechaFactura) = 11");
   
   while($f = db_fetch_object($qry_fact)){
    
	db_query("DELETE FROM DetalleFactura WHERE IDFactura = '$f->IDFactura' ");
   
   }
    
  $qry_fact = db_query("DELETE from Factura where year(FechaFactura) = 2014 and month(FechaFactura) = 11");
  
  exit;
/**********/

include("/var/www/vhosts/almacenescaprino.com/httpdocs/admin/simple_html_dom.php");

$filename ="/var/www/vhosts/almacenescaprino.com/httpdocs/admin/files/facturas/FacturaSD23994.html";//README.txt";

$filename = "/var/www/vhosts/almacenescaprino.com/httpdocs/admin/files/facturas/FacturaGA79662.html";

$filenameArray = array("FacturaSD23994.html",
		  "FacturaGA79662.html",
		  "FacturaSF8770.html",
		    "FacturaF44579.html","FacturaSF8770.html","FacturaF44579.html");

$arraycodPunto = array("UN"=>"1",
"S"=>"3",
"B"=>"4",
"H"=>"31",
"GA"=>"6",
"F"=>"7",
"C"=>"8",
"L"=>"9",
"K"=>"10",
"M"=>"12",
"AV"=>"13",
"R"=>"14",
"PA"=>"15",
"FV"=>"16",
"SP"=>"17",
"OV"=>"18",
"UM"=>"19",
"U"=>"20",
"Fabrica"=>"21",
"GE"=>"22",
"MY"=>"23",
"SD"=>"24",
"CH"=>"25",
"PL"=>"26",
"SF"=>"27",
"CM"=>"28",
"TP"=>"29",
"SM"=>"30",
""=>"31",
"UB"=>"32");

$FormaPago = array (
"Efectivo"=>"1",
"Cheque"=>"2",
"Visa Credito"=>"3",
"MC Credito"=>"4",
"Dinners"=>"5",
"A. Express"=>"6",
"MC Debito"=>"7",
"Visa Debito"=>"8",
"Bono sodexho"=>"9",
"Bono big pass"=>"10",
"Club"=>"12",
"Saldo"=>"13",
"Electron"=>"14",
"Maestro"=>"15",
"Falabella"=>"16",
"Puntos Fidelizacion"=>"17",
"Credito Facil"=>"18",
"Tarjeta Exito"=>"19",
"Tarjeta de Regalo"=>"20"
);

$empleados = array("4"=>"Patricia Riveros",
"19"=>"Rocio Pe?a",
"19"=>"Rocio Peña",
"25"=>"Zoraida Guio",
"36"=>"Luz M Castano",
"40"=>"Yeimy Torregrosa",
"44"=>"Luz Dary Fajardo",
"46"=>"Richard Qimbay",
"48"=>"Salitre Plaza",
"61"=>"Eucaris Monsalve",
"66"=>"Mariela Marin",
"77"=>"Carolina Marroquin",
"79"=>"Mayury Castillo",
"81"=>"Maria Ordo?ez",
"88"=>"Centro Comercial Unicentro",
"90"=>"Centro Comercial Plaza",
"91"=>"Centro Comercial Galerias",
"92"=>"Lago",
"94"=>"Calle 122",
"95"=>"Granahorrar",
"96"=>"Country",
"97"=>"Gran Estacion",
"111"=>"Temporal",
"113"=>"Felipe Guzman",
"121"=>"Centro Comercial Oviedo",
"122"=>"Maracaibo",
"123"=>"Centro Comercial Bulevar",
"124"=>"Junin",
"125"=>"Avenida 19",
"126"=>"UnicentroMedellin",
"127"=>"Calle 60",
"128"=>"Fripo",
"138"=>"Adriana Osuna",
"142"=>"Fabrica",
"143"=>"Venta Directa",
"150"=>"Carolina Diaz",
"155"=>"Maryie Bustos",
"158"=>"Mayorca",
"160"=>"Carolina Acosta Grajales",
"173"=>"Carolina Ramos",
"183"=>"San Diego",
"184"=>"Centro Chia",
"190"=>"Angela Donoso",
"192"=>"Maria Teresa Oma?a",
"193"=>"Mauricio Pastrana",
"201"=>"Alexandra George Oliver",
"202"=>"Centro Comercial Palatino",
"207"=>"Alejandra Maria Cata?o Velez",
"220"=>"Sandra Aguilar",
"224"=>"Olga Lucia Velez",
"226"=>"Maria Liliana Pereira D",
"236"=>"Jorge Sanchez",
"237"=>"Centro Comerial Santafe?",
"240"=>"Vanesa Cifuentes Garcia",
"243"=>"YeimiYojana CalambasQuilinbo",
"246"=>"Gina Carvajal",
"258"=>"Lizeth Johanna Ortiz",
"267"=>"Natalia Fonseca",
"269"=>"Tatiana Panessi",
"270"=>"Yaneth Astrid Ospina Gomez",
"271"=>"Encargado",
"272"=>"Laura Gonzalez",
"275"=>"Luz Dary Benavidez",
"278"=>"Katherin Yesenia Gomez Benitez",
"279"=>"Marcela Enciso",
"281"=>"Leidy Johanna Ortiz",
"282"=>"Johan Salinas",
"284"=>"VentasFabrica",
"286"=>"Centro Mayor",
"289"=>"C.c. Titan",
"290"=>"Maria Vargas",
"292"=>"Laura Herrera",
"295"=>"Blanca Maria Leal P.",
"300"=>"Lina Marcela Betancur Londoño",
"310"=>"Karen Fraile",
"317"=>"Olga LuciaPulido",
"318"=>"Nury Beleño",
"324"=>"Catalina Díaz",
"326"=>"Miguel AngelAndrade",
"327"=>"C.c. Santafe Medellin",
"336"=>"Martha Quintana",
"337"=>"Carol Rodriguez Zubieta",
"339"=>"Paola Saenz",
"340"=>"Angie Colo",
"341"=>"Maria Fda Herrera",
"342"=>"Milena P. Sarmiento A.",
"343"=>"Heidy Bustos",
"344"=>"Mateo Torres",
"345"=>"Monica Garcia",
"346"=>"Noralba Monroy",
"347"=>"Carlos Martinez",
"348"=>"Leidy Yuliana Jimenez Mesa",
"349"=>"Ginna PaolaFern?ndez",
"350"=>"Centro Cial Unicentro 2",
"351"=>"Cristian Escobar",
"352"=>"Tatiana Ortiz",
"353"=>"Andrea Garcia Gonzalez",
"354"=>"Jose Segura",
"355"=>"Leonardo Gutierrez",
"356"=>"Yenny Fontecha",
"357"=>"Ana Maria Salgado H.",
"358"=>"Diana Lopez",
"359"=>"Americas Outlet",
"360"=>"Teresa Saboya",
"361"=>"Juan Sebastian Gil Camelo",
"362"=>"SergioCastro",
"365"=>"Natalia Andrea Oliver",
"366"=>"M?nicaMart?nez",
"368"=>"Yeimy Ni?o",
"370"=>"Adriana Trujillo",
"371"=>"Pedro Chitiva",
"372"=>"Javier Castillo",
"373"=>"Laura Valentina Oyaga Ramirez",
"374"=>"Juan Carlos Ruiz M",
"375"=>"Diana Zerda",
"376"=>"Ilma Edith Ramirez",
"377"=>"Jackeline Colmenares",
"379"=>"Ingrid Sampedro",
"380"=>"Tihany Cardenas",
"381"=>"Sandra Jimenez",
"382"=>"Marisela Bautista",
"383"=>"MayerlyCorredor",
"384"=>"Laura Milena Muñoz Muñoz",
"386"=>"Diana Suarez",
"387"=>"Paola Huertas",
"388"=>"Johana Rueda",
"389"=>"Johana Rueda",
"390"=>"Paola Galvis",
"391"=>"Bertha Gonzalez",
"392"=>"Lorena Guioth",
"401"=>"Claudia Fonseca",
"403"=>"Ana Hernandez",
"404"=>"Yeicy Lorena Campos Murillo",
"405"=>"Monica  Salazar",
"406"=>"Ximena Martrinez",
"407"=>"Ana Gabriela Blanco",
"408"=>"Laura Villalobos",
"409"=>"Jessica Ladino",
"410"=>"Nini Johana Soto Almanza",
"411"=>"Elizabeth Pinchao",
"412"=>"MonicaSoler",
"413"=>"Sandra Zambrano",
"414"=>"Paloma ?lvarez",
"317"=>"Olga Lucia  Pulido",
"192"=>"Maria Teresa Omaña",
"379"=>"Ingrid Sanpedro",
"81"=>"Maria Ordoñez",
"409"=>"Jesica Liliana Ladino",
"400"=>"Soporte",
"414"=>"María Angelica De La Hoz",
"417"=>"Johana Satoba",
"419"=>"Alexandra Gonsalez",
 


"415"=>"Ana IsabelNi?o");

$directorio = '/var/www/vhosts/almacenescaprino.com/httpdocs/admin/files/facturas2/';


function listDir( $dirname )
{ 
	if( $dirname[ strlen( $dirname ) - 1 ] != "/" )
		$dirname.="/";
	
	$result_array = array();
	
	$mode = fileperms($dirname);
	
	if( ( $mode & 0x4000 ) == 0x4000 && ( $mode & 0x00004 ) == 0x00004)
	{ 
		chdir( $dirname ); 
		$handle = @opendir( $dirname) ;
	}
	
	if( isset( $handle ) )
	{
		while ( $file = readdir( $handle ) )
		{
			if( $file == '.' || $file == '..' ) 
				continue; 
			
			if( is_file( $dirname . $file ) ) 
				$result_array[] = $file;
		} 
		
		closedir( $handle );
	} 
	return $result_array;
}
	
 $files = listDir($directorio);

//$files = $filenameArray;

//print_r($files);

//exit;

//$filenameArray
$update = 0;

foreach($files As $filename){
   
	$arrayPie = array();
	$arrayDetalle = array();
	 
	$strNumfac = str_replace(".html","",$filename);
	$strNumfac = str_replace("Factura","",$strNumfac);
	
	$codPunto = ereg_replace("[^A-Z]", "", $strNumfac)."\t"; 
	$Numfac = ereg_replace("[^0-9]", "", $strNumfac)."\t"; 

	$codPunto = trim($codPunto);
	$Numfac = trim($Numfac);
	
	//echo " IDPto ";
	$IDPuntoVenta = $arraycodPunto["$codPunto"];

	$sql_verifica = "SELECT * FROM Factura WHERE NumeroFactura = '" . $Numfac . "' AND IDPuntoVenta = '" . $IDPuntoVenta . "' ";
	$qry_verifica = db_query( $sql_verifica );
	$row_datos_factura=db_fetch_array($qry_verifica);
	
	
	if( db_num_rows($qry_verifica) != 0 )
	{
	
		
	       $html =  file_get_html($directorio.$filename);//->plaintext;


		
		//header('Content-type: application/ms-excel');
		//header('Content-Disposition: attachment; filename=sample.csv');
		
		//$fpcsv = fopen("/var/www/vhosts/almacenescaprino.com/httpdocs/admin/files/facturas.csv", "ab+");
		
		//$fpcsv = fopen("php://output", "w");
		
		//$tableHeader// =  $html->find('table[class=texto]',0);
		

		$tableHeader =  $html->find('div[align=center]',0);


		
		$tableHeader =  $tableHeader->find('table',0);
		
		$htmlHeader = $tableHeader->find('table[class=rowtable]',0);


		
		//exit;
		//echo $tableHeader =  $html->find('table[class=rowtable]',0);
		$cont=0;
		$flagDet = 0;

		
		foreach($tableHeader->find('tr') as $element)
		{
			$cont++;
		 
		 	if($htmlHeader = $element->find('table[class=rowtable]',0)){
		    
		    $contHeader = 0;
		   
		    $dataFactura = array();
		   
		    foreach($htmlHeader->find('tr') as $elementH) // OBTENER DATOs CABECERA
		    {
			
				$td = array();
				
				if($contHeader >= 4 && $contHeader <= 7){
				    
				    foreach( $elementH->find('td') as $row)  
				    {
						$colval = trim(str_replace("\n","",$row->plaintext));
						$colval = trim(str_replace("\r","",$row->plaintext));
						
						$colval = trim(str_replace("\t","",$colval));
						
						if(!empty($colval)){
						    
						    $td = $colval;
						}
				    }
				    $dataFactura[] = $td;

				 	//   echo "\n";
				    
				}
				
				$contHeader++;
		    }
		    if($contHeader == 8){
		
				$dataFactura["IDPuntoVenta"] = $IDPuntoVenta;
			
				$dataFactura["CodPunto"] = $codPunto;
				$dataFactura["NumFact"]  = $Numfac;
				
				//print_r($dataFactura);
				//fputcsv($fpcsv, $dataFactura,"\t");
		    }
		    
		    
		}// end if find
		elseif( ($htmlDetalle = $tableHeader->find('table[class=bordertable]',0)) && !$flagDet){ // OBTERNER DETALLE ARTICULOS
		    
		    $flagDet = 0;
		    
		    foreach($htmlDetalle->find('tr') as $elementD)
		    {
				//$td = array();
				    
				if($flagDet){
				    
					$data = explode(" ",trim($elementD->plaintext));
					$data = str_replace("\r","",$data);
					$data = str_replace("\t","",$data);
					$data = str_replace("\n","",$data);
					$data = str_replace(",","",$data);
					$data = str_replace("%","",$data);
					$dataDetalle = array_filter($data, "strlen");// array_unique($data);
					
					//print_r($dataDetalle);
					
					$arrayDetalle[] = $dataDetalle;
					
				//	echo "\n";
				        
				//	fputcsv($fpcsv, $arrayDetalle,"\t");
				}
			 	$flagDet = 1;
		    }//end for
		    
		} // End if find2
		
		elseif($cont >= 10){ // OBTENER PIE DE FACTURA
		    
		 //  $td = array();
		 
		     $col = 0;
		     
			if($element->find('td[width=171]')){
			    
			    foreach( $element->find('td') as $row)  
			    {
					$colval = trim(str_replace("\n","",$row->plaintext));
					$colval = str_replace("\t","",$colval);
				 	$colval = str_replace(",","",$colval);
			  	    
				      if(!empty($colval)){
					
					if(!$col){
					   $colname = $colval;
					    $col = 1;
					}
					else    
					    $arrayPie[$colname] = $colval;
					 
					  // echo $colval."\t";
				    }
			    }
			  
			  //  fputcsv($fpcsv, $arrayPie,"\t");
			    
			  //  echo "\n";
			} 
		    }
		}
	

		
	//	echo " IDEMPLEADO ".$id_empleado_factura=array_search($dataFactura[0],$empleados);
		$id_empleado_factura=array_search(utf8_encode($dataFactura[0]),$empleados);
		
		if (empty($id_empleado_factura)){
			echo "<br>Vacio " . htmlentities($dataFactura[0]) . " Punto venta: " . $IDPuntoVenta;
			
		}
		
		
	//	print_r($dataFactura);
	//	print_r($arrayDetalle);	
	//	print_r($arrayPie);
		
	//	echo "UPDATE FACTURA CON EL ID EMPLEADO";
	//	echo "INSERT VENTAEMPLEADO";
	
	
	//borro datos comision
	//$sql_borra_comision="Delete from VentasEmpleado Where IDFactura = '".$row_datos_factura[IDFactura]."'";
	//db_query($sql_borra_comision);
	
	// verifico que no exista datos de comisiones para esta factura para no duplica registros
	
	
	
			$administrador = get_field("PuntoVenta","IDEmpleado","IDEmpleado",$id_empleado_factura);
			if($administrador <> "")
				$cargo = "Administrador";
			else
				$cargo = "Empleado";
			
			$idadministrador = get_field("PuntoVenta","IDEmpleado","IDPuntoVenta",$IDPuntoVenta);
			$idventaempleado = get_maxID("VentasEmpleado","IDVentasEmpleado");
			$sql_ventaadministrador = "INSERT INTO VentasEmpleado VALUES ('$idventaempleado','$idadministrador','Administrador','$IDPuntoVenta','$row_datos_factura[IDFactura]','$row_datos_factura[ValorTotal]')";
			$queryventaadministrador = db_query($sql_ventaadministrador);
				
			if($cargo == "Empleado")
			{
				$idventaempleado = get_maxID("VentasEmpleado","IDVentasEmpleado");
				$sql_ventaempleado = "INSERT INTO VentasEmpleado VALUES ('$idventaempleado','$id_empleado_factura','$cargo','$IDPuntoVenta','$row_datos_factura[IDFactura]','$row_datos_factura[ValorTotal]')";
				$queryventaempleado = db_query($sql_ventaempleado);
			}		
	
	//Actualizo Factura
	$sql_actualiza_factura="Update Factura Set IDEmpleado = '".$id_empleado_factura."' Where IDFactura = '".$row_datos_factura[IDFactura]."' and IDEmpleado = 0";
	db_query($sql_actualiza_factura);
	
		
	
		//echo " ****************************************** \n";
		
		/**********/
		/* INSERT DE LA FACTURA 

		//Verificar si ya está en la base de datos
		
			db_query("SET AUTOCOMMIT=0");
			db_query("BEGIN");

			$frm_factura = array();
			$frm_detalle = array();
			$frm_formapago = array();
			$MOD = "GenerarFactura";
			$m = "readfactura";

			//insertar Cliente
			$sql_cliente = "SELECT * FROM Cliente WHERE Cedula = '" . $dataFactura[3] . "' ";
			$qry_cliente = db_query( $sql_cliente );
			if( db_num_rows( $qry_cliente ) > 0 )
			{
				$r_cliente = db_fetch_array( $qry_cliente );
				$frm_factura["IDCliente"] = $r_cliente["IDCliente"];
			}//end if
			else
			{
				$data_cliente["Cedula"] = $dataFactura[3];
				$data_cliente["Nombre"] = $dataFactura[2];
				
				$frm_factura["IDCliente"] = insert_width_table($data_cliente,"Cliente","IDCliente");
			
			}//end else

			//traer datos de factura
			$frm_factura["NumeroFactura"] = $dataFactura["NumFact"];
			$frm_factura["IDPuntoVenta"] = $dataFactura["IDPuntoVenta"];
			$frm_factura["FechaFactura"] = $dataFactura[1];
			$frm_factura["ValorIVA"] = $arrayPie["IVA"];
			$frm_factura["ValorTotal"] = $arrayPie["Total"];
			$frm_factura["FechaTrCr"] = "2014-11-09";

			$Table = "Factura";
			$Key = "IDFactura";

			$frm_factura['IDFactura'] = insert($frm_factura);

			//insert detalle
			foreach( $arrayDetalle as $key_detalle => $detalle_factura )
			{	

				$cantidad = $detalle_factura[116];
				$preciou = $detalle_factura[118];
				$valoru = $detalle_factura[121];
				$descuentoref = $detalle_factura[117];
				$descuentopar = $detalle_factura[119];

				//traer codificacionespecifica
				$referencia = $detalle_factura[0];
				$talla = $detalle_factura[62];
				$sql_referencia = "SELECT * FROM Referencia WHERE Numero = '" . $referencia . "' ";
				$qry_referencia = db_query( $sql_referencia );
				$r_referencia = db_fetch_array( $qry_referencia );

				//traer id talla
				$sql_talla = "SELECT IDTalla FROM Talla WHERE IDTipoTalla = '" . $r_referencia["IDTipoTalla"] . "' AND Nombre = '" . $talla . "' ";
				$qry_talla = db_query( $sql_talla );
				$r_talla = db_fetch_array( $qry_talla );

				//traer puntoventareferencia
				$sql_puntoref = "SELECT * FROM PuntoVentaReferencia WHERE IDPuntoVenta = '" . $frm_factura["IDPuntoVenta"] . "' AND IDReferencia = '" . $r_referencia["IDReferencia"] . "' ";
				$qry_puntoref = db_query( $sql_puntoref );
				$r_puntoref = db_fetch_array( $qry_puntoref );

				//traer codificacion especifica
				$sql_codificacion = "SELECT * FROM CodificacionEspecifica WHERE IDTalla = '" . $r_talla["IDTalla"] . "' AND IDPuntoVentaReferencia = '" . $r_puntoref["IDPuntoVentaReferencia"] . "' ";
				$qry_codificacion = db_query( $sql_codificacion );
				$r_codificacion = db_fetch_array( $qry_codificacion );
				$codificacion = $r_codificacion["IDCodificacionEspecifica"];

				$iddetalle = get_maxID("DetalleFactura WHERE IDFactura = '$frm_factura[IDFactura]' ","IDDetalleFactura");
				
				$str_insert_detalle  = "INSERT INTO DetalleFactura ( IDDetalleFactura,IDFactura,IDPuntoVenta,IDCodificacionEspecifica,Cantidad,ValorU,PrecioU,DescuentoRef,DescuentoPar, UsuarioTrCr,FechaTrCr ) ";
				$str_insert_detalle .= "VALUES ( '$iddetalle','$frm_factura[IDFactura]','$frm_factura[IDPuntoVenta]','$codificacion','$cantidad','$valoru','$preciou','$descuentoref','$descuentopar','admin','$frm_factura[FechaTrCr]' )";
				//echo $str_insert_detalle .= "<br>";
			
				db_query( $str_insert_detalle );

				//$existencias = get_field( "CodificacionEspecifica","Existencias","IDCodificacionEspecifica", $codificacion );
			
				//$existencias = $existencias - $cantidad;
				//$str_actualiza_inventario  = "UPDATE CodificacionEspecifica SET Existencias = '$existencias' WHERE IDCodificacionEspecifica = '$codificacion'";
				//echo $str_actualiza_inventario .= "<br>";
				
				//db_query( $str_actualiza_inventario );

			}//end for

			//insert forma de pago
			foreach( $arrayPie as $namecol => $valorpie )
			{
				if( $namecol <> "IVA" && $namecol <> "Total" ) //para traer solo las formas de pago
				{
					$IDFPago = $FormaPago["$namecol"];
					$Valor = $valorpie;

					//Traer comision del banco
					$sql_comision_banco = "SELECT * FROM PuntoVentaBanco WHERE IDPuntoVenta = '" . $frm_factura["IDPuntoVenta"] . "' AND IDFormaPago = '" . $IDFPago . "' ";
					$qry_comision_banco = db_query( $sql_comision_banco );
					$r_comision_banco = db_fetch_array( $qry_comision_banco );

					$Comision = $r_comision_banco["Comision"];
					$Banco = $r_comision_banco["IDBanco"];

					$IDFormaPagoFactura = get_maxID( "FormaPagoFactura","IDFormaPagoFactura" );

					$sql_insertar_formapago  = "INSERT INTO FormaPagoFactura (IDFormaPagoFactura,IDFactura,IDFormaPago,Valor,IDPuntoVenta,Comision,IDBanco) ";
					$sql_insertar_formapago .= "VALUES ('$IDFormaPagoFactura', '$frm_factura[IDFactura]', '$IDFPago', '$Valor','$frm_factura[IDPuntoVenta]','$Comision','$Banco')";

					//echo $sql_insertar_formapago .= "<br>";

					db_query( $sql_insertar_formapago );

				}//end if
			}//end for

			
	
	//END insert factura
	*////
	
	$update++;
	
	}//end if verifica factura


	
	
//	exit;
unlink($directorio.$filename);
	
} // END foreach file

echo $update;

//db_query( "tales" );
db_query("COMMIT");


exit;
	

$filename = "/var/www/vhosts/almacenescaprino.com/httpdocs/admin/files/facturas/FacturaGA79662.html";							
	//	$line = file($filename);
				
							//	for($i=1;$i <= count($line);$i++)
							//	echo "$i $line[$i]<br>";
							
	$i = 1;
	if($fd = fopen ($filename, "rb"))
	while (!feof($fd)) {
	    $buffer = fgets($fd, 4096);
	    echo "$i $buffer <br>";
		$i++;
	}
	fclose ($fd);
		
	exit;	
								
	// funciona OK leyendo el archiv
	
	$filename = $filedir.$frm['Archivo'];
		
	if(!empty($frm['Archivo'])){
		$i = 1;
		$fd = fopen ($filename, "rb");
		while (!feof($fd)) {
		    $buffer = fgets($fd, 4096);
		    echo "<a href=\"javascript:OpenWin('add_comentario.php3?id=$id&ln=$i')\">$i </a>";
		    echo " $buffer <br>";
			
			$qry_coment = db_query("SELECT NoLinea,User,Comentario,Fecha
									FROM Comentario 
									WHERE IDDocumento = '$id' 
									AND NoLinea = '$i'");
			if(db_num_rows($qry_coment))
				while ($Comentario = db_fetch_object($qry_coment)){
					echo "<span class=fontBlue><b>$Comentario->Fecha $Comentario->User : </b>$Comentario->Comentario </span> <br>";
				}
				
			$i++;
		}
		fclose ($fd);
	}
		
		
								
?>
