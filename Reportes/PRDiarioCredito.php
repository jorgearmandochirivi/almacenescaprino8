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
		
	$TitleMod ="Impresion Reporte Diario Credito";
	
	//$sql_credito = "SELECT * FROM CreditoCuota WHERE DATE_FORMAT(FechaPago,'%Y-%m-%d' ) = '$Fecha' AND IDPuntoVentaPago = '$IDPuntoVenta'";
	$sql_credito = "SELECT * FROM CreditoCuota WHERE DATE_FORMAT(FechaPago,'%Y-%m-%d' ) = '$Fecha' AND (IDPuntoVentaPago = '$IDPuntoVenta' or ( IDPuntoVenta = '$IDPuntoVenta' and IDPuntoVentaPago = 0 )) ";						
	
	$qry_credito = db_query( $sql_credito );

	//punto de venta
	$sql_punto = " SELECT * FROM PuntoVenta WHERE IDPuntoVenta = '" . $IDPuntoVenta . "'  ";
	$qry_punto = db_query( $sql_punto );
	$r_puntoventa = db_fetch_object( $qry_punto );
	
	$i = 0;
	$formapago = array();
	
	while( $array_credito = db_fetch_array( $qry_credito ) )
	{	
		$r_credito[$i] = $array_credito;
		$i++;
		
	}//end while( $r_facturas = db_fetch_array( $qry_facturas ) )
	
	$filedir = $dirroot . "/files/facturas/";
	
	$name = "RDiario" . $r_puntoventa->Codigo.$Fecha . ".html";
	$namePDF = "RDiario" . $r_puntoventa->Codigo.$Fecha . ".pdf";
	$file = "$filedir$name";
	$filepdf = "$filedir$namePDF";

	if (!is_dir($filedir)) {
		@mkdir($filedir, 0775, true);
	}

	
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
     height : 300px; 
     color:black;
	 }
table{
	font-size:6.5px;
	margin:0;
}


-->
}
</style>


<body>



	<table  width="215" cellspacing="1" border="0" align="left" height="100" id="#content">
		<tr>
			<td align="center">
				IMACAL LTDA.<br>
				Nit 860033182-4<br>
				Almacen <?=$r_puntoventa->Nombre ?><br>
				No equipo computo <?=$r_puntoventa->EquipoComputo ?><br>
				Fecha Generacion: <?=date( "Y-m-d" );?><br>
                Fecha Reporte: <?php echo $_GET["Fecha"] ?>
			</td>
		</tr>
		<tr>
			<td class='mainbg'> 
				<table width="100%" border="0" cellspacing="1" cellpadding="1">
					<tr>
						<td class="navpic" nowrap>No.</td>
						<td class="navpic" align="center" nowrap>Almac Cred </td>
						<td class="navpic" align="center" nowrap># Cuota Abono</td>
						<td class="navpic" align="center" nowrap># Cuota Pend.</td>
						<td class="navpic" align="center" nowrap>Valor Abono</td>
						<td class="navpic" align="center" nowrap>Valor Saldo</td>
					</tr>
					<?php
					
					foreach( $r_credito as $key => $valor )
					{ 
						//print_r( $valor );
						$class = repetition()?"row2":"row1";
						//print_r($valor);
					?>
					<tr>
						<td class="<?=$class?>" align="center" nowrap><?=$valor['NumeroFactura']?></td>
						<td class="<?=$class?>" align="center" nowrap><?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$valor["IDPuntoVenta"]) ?> </td>
						<td class="<?=$class?>" align="center" nowrap><?=$numero_cuota=$valor["IDCuota"]?></td>
						<td class="<?=$class?>" align="center" nowrap>
                        
                        <?php
									//$sql_cuotas = " SELECT count(*) as numero FROM CreditoCuota WHERE IDFactura = '{$valor['IDFactura']}' AND IDPuntoVenta = '{$valor['IDPuntoVenta']}' AND FechaPago = '0000-00-00 00:00:00' ";
									//$qry_cuotas = db_query( $sql_cuotas );
									//$r_cuotas = db_fetch_object( $qry_cuotas );
									echo $pendiente_cuota=5 - $numero_cuota;
									?>
                      </td>
									<td class="<?=$class?>" align="right" nowrap>
                                    <?=number_format( $valor["ValorTotal"] , 0); $Pago+=$valor["ValorTotal"];  ?>
                                    </td>
						<td class="<?=$class?>" align="right" nowrap>
                        <?php
						$saldo = (int)$pendiente_cuota * (int)$valor["ValorTotal"];
						echo number_format($saldo,0);
						?>			
                        </td>
					</tr>
					
					<?php
					}//end foreach( $r_facturas as $key => $valor )
					?>
						
					<tr>
						<td class="navpic" colspan="2" align="right" nowrap><b>TOT</b></td>
						<td class="navpic" align="center" nowrap><b><?=$Pares ?></b></td>
						<td class="navpic" align="right" nowrap></td>
						<td class="navpic" align="right" nowrap><b><?=number_format( $Pago , 2)?></b></td>
						<td class="navpic" align="right" nowrap></td>
					</tr>
				</table>
	  </tr>
		</td>
				

	</table>


	

	

	

</body>
</html>
<?php

	$page = ob_get_contents();
	$fw = fopen($file, "w");
	if ($fw === false) {
		ob_end_clean();
		die("No se pudo abrir el archivo de salida: " . $file);
	}
	fwrite($fw, $page);
	fclose($fw);

ob_end_clean();

echo $page;
//passthru("htmldoc --format pdf --size 'Universal' --textfont Arial --title 'Acta' --charset 8859-15 --left 0cm --right 0cm --top 0cm --bottom 0cm --fontsize 7 --webpage $file -f $filedir/$namePDF");
//echo "/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf";
passthru("/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf");
?>
