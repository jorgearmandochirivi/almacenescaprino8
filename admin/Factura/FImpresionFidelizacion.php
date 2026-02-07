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

	$TitleMod ="Cliente";

	$Table = "Cliente";
	$TableJoin = "Cliente";
	$Key = "IDCliente";


	$qid = db_query(" SELECT * FROM Cliente WHERE IDCliente = '$id'  ");

	$r = db_fetch_object($qid);


	$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
	$qry_puntoventa = db_query( $sql_puntoVenta );
	$r_puntoventa = db_fetch_object( $qry_puntoventa );

	$filedir = $dirroot . "/files/facturas/";

	$name = "Cliente" . $r->IDCliente . ".html";
	$namePDF = "Cliente" . $r->IDCliente . ".pdf";
	$file = "$filedir$name";
	$filepdf = "$filedir$namePDF";


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




<table  width="215" cellspacing="1" border="0" align="center" id="#content">



    <tr>
        <td class=texto colspan="2">
            Imacal <?php echo $tipo_emp= ($r->FechaTrEd>="2019-07-19 00:00:00") ? "SAS" : "SAS"; ?>
            NIT <?php echo get_field( "NIT","NIT","IDNIT",1 );?>&nbsp;&nbsp;&nbsp;&nbsp;
            R&eacute;gimen com&uacute;n
        </td>
    </tr>

    <tr>
        <td class=texto width="91">Fecha Registro</td>
        <td width="112" colspan="2" nowrap class=texto><?php echo date("Y-m-d") ?></td>
    </tr>
    <?php
    if( !empty( $r->FechaTrEd ) && $r->FechaTrEd !="0000-00-00 00:00:00" )
	{
	?>
    <tr>
        <td class=texto width="91">Fecha ctualizaci&oacute;n</td>
        <td class=texto colspan="2" nowrap><?php echo substr($r->FechaTrEd,0,10) ?></td>
    </tr>
	<?php
	}//end if
	?>
    <tr>
        <td class=texto width="91">Vendedor</td>
        <td class=texto colspan="2" nowrap><?php echo get_field( "Empleado", "Nombre", "IDEmpleado", $r->IDEmpleado ) . " " . get_field( "Empleado", "Apellidos", "IDEmpleado", $r->IDEmpleado ) ?></td>
    </tr>

    <tr>
        <td class=texto nowrap>Nombre Cliente</td>
        <td class=texto colspan="2" nowrap><?php echo $r->Nombre . " " . $r->Apellido; ?></td>
    </tr>    
    <tr>
        <td class=texto nowrap>Cedula</td>
        <td class=texto colspan="2" nowrap><?php echo $r->Cedula?></td>
    </tr>
    <tr>
        <td class=texto nowrap>Fecha de Nac.</td>
        <td class=texto colspan="2" nowrap><?php echo $r->Ano . "-" . $r->Mes . "-" .$r->Dia  ?></td>
    </tr>
    <tr>
        <td class=texto nowrap>Email</td>
        <td class=texto colspan="2" nowrap><?php echo $r->EMail  ?></td>
    </tr>
    <tr>
        <td class=texto nowrap>Tel&eacute;fono</td>
        <td class=texto colspan="2" nowrap><?php echo $r->Telefono  ?></td>
    </tr>
    <tr>
        <td class=texto nowrap>Celular</td>
        <td class=texto colspan="2" nowrap><?php echo $r->Celular  ?></td>
    </tr>
    <tr>
        <td class=texto >Direcci&oacute;n</td>
        <td class=texto colspan="2"><?php echo str_replace(" ","_",$r->Direccion) . "(".get_field( "Ciudad", "Descripcion", "IDCiudad", $r->IDCiudad ) .")"; ?></td>
    </tr>   
    <tr>
        <td class=texto nowrap>Autorizo <br>Envio de SMS</td>
        <td class=texto colspan="2" nowrap><?php echo $r->AceptaSMS  ?></td>
    </tr>
    <tr>
        <td class=texto nowrap>Autorizo <br>envio a correo</td>
        <td class=texto colspan="2" nowrap><?php echo $r->AutorizaMail  ?></td>
    </tr>

    <tr>
        <td class=texto nowrap>Acepto t&eacute;rminos<br> y condiciones</td>
        <td class=texto colspan="2" nowrap><?php echo $r->AceptaTerminos  ?></td>
    </tr>

    <tr>
        <td class=texto nowrap>Acepta Habeas Data</td>
        <td class=texto colspan="2" nowrap><?php echo $r->AceptaHabeas  ?></td>
    </tr>

    <tr>
        <td class=texto nowrap>Numero de Tarjeta<br> entregada	</td>
        <td class=texto colspan="2" nowrap><?php echo $r->NumeroTarjeta  ?></td>
    </tr>

    <tr>
        <td class=texto nowrap>VERIFICADO	</td>
        <td class=texto colspan="2" nowrap>

        <table width="10px" height="10px" border="1px" cellpadding="0" cellspacing="0" >
        	<tr>
            	<td>
                </td>
            </tr>
         </table>

        </td>
    </tr>

    <tr>
            <td colspan=2>
                Acepto, permito y autorizo, a calzado caprino que la informaci&oacute;n <br>
                suministrada sea utilizada con  fines administrativos, mercadeo y de  ventas
                <br>Por favor verifique sus datos
            </td>
       </tr>


    </table>

    <table  width="215" cellspacing="1" border="0" align="center" id="#content">
    <tr>
      <td colspan="4" align="center" class=texto><br><br><br>
               Firma</td>
    </tr>
    <tr>
      <td colspan="4" align="center" class=texto>
       <br>
                C&eacute;dula

      </td>
    </tr>
    <tr>
      <td colspan="4" align="center" class=texto>
       <a href="/admin/files/facturas/Cliente<?php echo $r->IDCliente ?>.pdf">pdf</a>
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
passthru("/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf");
?>
</body>
</html>