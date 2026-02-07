<?php
	include("../admin/config.inc.php");
	//Encabezado();
	$datos = Verifica_SesionCliente();
	//print_r($datos);
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
	$IDPuntoVenta = $datos["IDPuntoVenta"];
	//include("admin/jscripts/tabs.php");

	$TitleMod ="Factura";

	$Table = "Factura";
	$TableJoin = "Factura";
	$Key = "IDFactura";

	$qid = db_query(" SELECT * FROM Factura WHERE IDFactura = '$id' AND IDPuntoVenta = '$idpunto' ");

	$r = db_fetch_object($qid);

  $ValorIva=(int)$r->ValorIVA;
  
  $resto = $ValorIva % 500;
  if ($resto > 0) {
      $ValorIva += (500 - $resto);
  }

  //verifico de nuevo que la factura tenga las referencias que son
    //Detalle Factura
			$sql_detalle_factura = "SELECT * From DetalleFactura Where IDFactura = '".$id."' and IDPuntoVenta = '".$idpunto."'";
			$qry_factura_detalle = db_query($sql_detalle_factura);
			while ($row_factura_detalle = db_fetch_array($qry_factura_detalle)){
				$cantidad_producto++;
				$array_referencias=array();
        $pto_vta_ref = get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_factura_detalle["IDCodificacionEspecifica"]);				
				$id_ref = get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$pto_vta_ref);
				$sql_referencia="SELECT TR.Descripcion Categoria, TT.Descripcion Genero, R.Numero Referencia
												 FROM TipoReferencia TR, TipoTalla TT, Referencia R
												 WHERE R.IDTipoReferencia=TR.IDTipoReferencia and R.IDTipoTalla=TT.IDTipoTalla and IDReferencia = '".$id_ref."' ";
			  $qry_ref = db_query($sql_referencia);
				while ($row_ref = db_fetch_array($qry_ref)){
          $AplicaBono="S";
          $referencia=$row_ref["Referencia"];	 
          $proveedor1=substr($referencia,0,2);
          $proveedor2=substr($referencia,0,3);
          $proveedor3=substr($referencia,0,1);		      
		      if(  $proveedor1=="ZM" || $proveedor1=="zm"  || $proveedor1=="ZQ" || $proveedor1=="zq" || $proveedor1=="ZU" || $proveedor1=="zu" || $proveedor1=="ZC" || $proveedor1=="zc" || $proveedor2 =="ZWP" || $proveedor2 =="zwp" || $proveedor1 =="un" ||  $proveedor2 =="un" ||  $proveedor1 =="o" ||  $proveedor1 =="O" ||  $proveedor2 =="zwl" ||  $proveedor2 =="ZWL" ||  $proveedor2 =="zse" ||  $proveedor2 =="ZSE")
		      {						
					  $AplicaBono="N";
				  }
        }
			}
	  //Fin detalle factura

    if($AplicaBono=="N"){
      echo "No se genera bono";
      exit;
    }



  //verifico si el bono ya se  guardó
  $sql_bono="SELECT IDBonoIva FROM BonoIva WHERE IDFactura = '".$id."' and IDPuntoVenta = '".$r->IDPuntoVenta."' LIMIT 1";
  $r_bono=db_query($sql_bono);
  $row_bono = db_fetch_object($r_bono);
  if($row_bono->IDBonoIva>0){
    echo "Ya se habia generado un bono de esta factura";
    exit;
  }
  else{



    //Variables
    $DesdeLetra = "a";
    $HastaLetra = "z";
    $DesdeNumero = 1;
    $HastaNumero = 10000;

    $letraAleatoria1 = strtoupper(chr(rand(ord($DesdeLetra), ord($HastaLetra))));
    $letraAleatoria2 = strtoupper(chr(rand(ord($DesdeLetra), ord($HastaLetra))));
    $ConSecutivoUnico=$id.$idpunto;
    $numeroAleatorio = rand($DesdeNumero, $HastaNumero);
    $NumeroBono=$letraAleatoria1.$letraAleatoria2.$ConSecutivoUnico.$numeroAleatorio;
    $sql_inserta_bono="INSERT INTO BonoIva (Codigo,Disponible,IDCliente,IDFactura,IDPuntoVenta,NumeroFactura,Valor,UsuarioTrCr,FechaTrCr) 
                       VALUES('".$NumeroBono."','S','".$r->IDCliente."','".$id."','".$r->IDPuntoVenta."','".$r->NumeroFactura."','".$ValorIva."','Automatico',NOW() ) ";
    db_query($sql_inserta_bono);                       

    require_once $libdir . 'codigobarras.php';
    $parametros_codigo_barras=$NumeroBono; 
    $IDCliente=$r->IDCliente; 
    $alto_barras = '30';
    generar_codigo_barras($parametros_codigo_barras, $IDCliente, $alto_barras,$libdir,$dirroot);


    $UltimoBono=db_insert_id();
    if($UltimoBono<=0){
      echo "Hubo un problema al generar el bono";
      exit;
    }
  }
  
	$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
	$qry_puntoventa = db_query( $sql_puntoVenta );
	$r_puntoventa = db_fetch_object( $qry_puntoventa );

	$filedir = $dirroot . "/files/facturas/";

	$name = "BonoIva" . $UltimoBono . ".html";
	$namePDF = "BonoIva" . $UltimoBono . ".pdf";
	$file = "$filedir$name";
	$filepdf = "$filedir$namePDF";


	$sql_cliente = "SELECT * from Cliente WHERE IDCliente = '$r->IDCliente' ";
	$qry_cliente = db_query( $sql_cliente );
	$r_cliente = db_fetch_object( $qry_cliente );


	$array_fidelizacion = fid_get_puntos( $r->IDCliente, $id );


  

//	ob_end_clean();

	ob_start();

?>

<html>
<head>
</head>
<style>
<!--
body{
	font-size:6.5px;
	margin:0;
}
table{
	font-size:6.5px;
}
@page { size 6cm 12cm;
	margin-left: 0;
	}

@media print{
*{
	margin:0;
	padding:0;
}
body{
	font-size:7px;
	margin:0;
	padding:0;
}

.texto {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 6.5px;
	color: #000000;
}
.mensajefooter{
	font-size:6px;
}


.bordertable {border: dotted 1px; color:#c3c3c3}
#content { margin-left:0;
     float:none;
     width:auto;

     color:black;
	 }
table{
	font-size:6.5px;
	margin:0;
}


-->
}
</style>
<body>..



<?php ob_start(); ?>

<table  width="545" cellspacing="1" border="0" align="center" id="#content">



    <tr>
        <td class=texto colspan="2">
            Imacal <?php echo $tipo_emp= ($r->FechaTrEd>="2019-07-19 00:00:00") ? "SAS" : "SAS"; ?>
            NIT <?=get_field( "NIT","NIT","IDNIT",1 );?>&nbsp;&nbsp;&nbsp;&nbsp;
            R&eacute;gimen com&uacute;n
        </td>
    </tr>

    <tr>
        <td class=texto width="400">Fecha Registro</td>
        <td colspan="2" nowrap class=texto><?=date("Y-m-d") ?></td>
    </tr>
    
    <tr>
        <td class=texto >NRO BONO RECOMPRA</td>
        <td class=texto colspan="2" nowrap><?=$NumeroBono; ?></td>
    </tr>
    <tr>
        <td class=texto >Fecha de Vencimiento</td>
        <td class=texto colspan="2" nowrap>2024-11-30</td>
    </tr>
    <tr>
        <td class=texto >Valor del bono</td>
        <td class=texto colspan="2" nowrap><?php echo "$".number_format($ValorIva,0,',','.'); ?></td>
    </tr>
    <tr>
        <td class=texto>Nro Fact Emisi&oacute;n del bono</td>
        <td class=texto colspan="2" nowrap><?php echo $r->NumeroFactura; ?></td>
    </tr>

    <tr>
        <td class=texto nowrap>Nombre Cliente</td>
        <td class=texto colspan="2" nowrap><?=$r_cliente->Nombre . " " . $r_cliente->Apellido; ?></td>
    </tr>    
    <tr>
        <td class=texto nowrap>Cedula</td>
        <td class=texto colspan="2" nowrap><?=$r_cliente->Cedula?></td>
    </tr>
  </table>

  <table  width="545" cellspacing="1" border="0" align="center" id="#content">
    <tr>
      <td colspan="4" align="left" class=texto><br><br><br>
               Condiciones: <br>
                1. Tu bono ser&aacute; cargado con el valor del I.V.A (19%) generado en la factura, &uacute;nicamente por las compras de productos marca Caprino, superiores a $200.000, entre el 1 y el 31 de Octubre de 2024, en las tiendas Caprino y p&aacute;gina Web.<br>
                2. La compra de tarjetas de regalo en tiendas f&iacute;sicas o bonos de regalo en p&aacute;gina Web no genera bonos recompra<br>
                3. Podr&aacute;s redimirlo por compras superiores a $ 200.000 y m&aacute;ximo  por el 50% del total de la compra. Aplica &uacute;nicamente para productos marca Caprino del 1 al 30 de Noviembre de 2024,  solo se puede redimir un bono por factura. <br>
                4. La redenci&oacute;n del bono no es acumulable con otras promociones, alianzas, bonos de fidelizaci&oacute;n y cumplea&ntilde;os del club de la suavidad.<br>
                5. No aplica para productos de marcas aliadas como: Bottero, Arcopedico, Verofatto, Usaflex, Euroconforto, Felca y Quintero<br>
                6. Debes llenar el bono con tus datos y firma para poderlo redimir y hacerlo v&aacute;lido.<br>
                7. Cada bono tiene un código &uacute;nico que est&aacute; asociado a tu documento de identidad y s&oacute;lo podr&aacute; ser redimido una vez.<br>

               </td>
    </tr>
  </table>
  
  <table  width="545" cellspacing="1" border="0" align="center" id="#content">

    
    <tr>
        <td class=texto nowrap>NRO FACTURA REDIMIDO</td>
        <td class=texto colspan="2" nowrap>________________</td>
    </tr>


    </table>

    <table  width="545" cellspacing="1" border="0" align="center" id="#content">
    <tr>
      <td colspan="4" align="left" class=texto><br><br><br>
               Firma Cliente: ________________</td>
    </tr>

    <tr>
      <td colspan="4" align="left" class=texto><br><br><br>
               Firma Vendedor:________________<br><br></td>
    </tr>

    
    <tr>
      <td colspan="4" align="center" class=texto>
      <?php
      $url_final=str_replace("http","https",$url);
      ?>
      <img src="<?php echo $url_final . "files/codigobarras/".$NumeroBono.".png"; ?>" height="40" width="230">
      </td>
    </tr>
    
    <tr>
      <td colspan="4" align="center" class=texto>
       <a href="/admin/files/facturas/BonoIva<?=$UltimoBono ?>.pdf">pdf</a>
      </td>
    </tr>




</table>


<?php

$page = ob_get_contents();
$fw = fopen($file, "w");
fputs($fw,$page,strlen($page));
fclose($fw);
ob_end_clean();
echo $page;
//passthru("htmldoc --format pdf --size 'Universal' --textfont Arial --title 'Acta' --charset 8859-15 --left 0cm --right 0cm --top 0cm --bottom 0cm --fontsize 7 --webpage $file -f $filedir/$namePDF");
//echo "/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf";
passthru("/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldociva.sh $file $filepdf");
?>
</body>
</html>