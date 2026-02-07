<?php
	include("../admin/config.inc.php");
	Encabezado();
	$datos = Verifica_SesionCliente();
	//print_r($datos);
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
	$IDPuntoVenta = $datos["IDPuntoVenta"];
	include("admin/jscripts/tabs.php");

	$TitleMod ="Factura";

	$Table = "Credito";
	$TableJoin = "Credito";
	$Key = "IDFacturaBono";


	$qid = db_query(" SELECT * FROM Credito WHERE IDFactura = '$id' AND IDPuntoVenta = '$idpunto'");

	$r = db_fetch_object($qid);

	$sql_cuota = " SELECT * FROM CreditoCuota WHERE IDFactura = '$id' AND IDPuntoVenta = '$idpunto' AND IDCuota = '$idcuota' ";
	$qry_cuota = db_query( $sql_cuota );
	$r_cuota = db_fetch_object( $qry_cuota );

	//Datos factura
	$sql_factura = " SELECT * FROM Factura WHERE IDFactura = '$id' AND IDPuntoVenta = '$idpunto'";
	$qry_factura = db_query( $sql_factura );
	$r_factura = db_fetch_object( $qry_factura );


	$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
	$qry_puntoventa = db_query( $sql_puntoVenta );
	$r_puntoventa = db_fetch_object( $qry_puntoventa );


	$filedir = $dirroot . "/files/facturas/";

	$name = "FCreditos" . $r_puntoventa->Codigo.$r->NumeroFactura . $idcuota . ".html";
	$namePDF = "FCreditos" . $r_puntoventa->Codigo.$r->NumeroFactura . $idcuota . ".pdf";
	$file = "$filedir$name";
	$filepdf = "$filedir$namePDF";

	ob_start();
?>
<html>
<head>
<title>Imprimir Recibo</title>
</head>
<style>
<!--
body{
	font-size:6.5px;
	margin:0;
}
table{
	font-size:8px;
}
@page { size 12cm 12cm;
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
<?php ob_start(); ?>
<body>



    <table class=rowtable width="80%" align="center">
      <tr>
        <td class=texto colspan="4" nowrap>Documento pago cuota credito. <?php echo $r_cuota->Consecutivo; ?></td>
      </tr>
      <tr>
        <td class=texto colspan="4"> NIT
          <?=get_field( "NIT","NIT","IDNIT",1 );?>
          &nbsp;&nbsp;&nbsp;&nbsp;
          R&eacute;gimen com&uacute;n </td>
      </tr>
      <tr>
        <td width="38%" class=texto>Almac&eacute;n</td>
        <td class=texto colspan="3" nowrap>IMACAL
				<?php echo $tipo_emp= ($r_cuota->FechaCuota>="2019-07-19 00:00:00") ? "SAS" : "LTDA"; ?>
          <?=$r_puntoventa->Nombre?></td>
      </tr>
      <tr>
        <td class=texto>Direcci&oacute;n</td>
        <td class=texto colspan="3" nowrap><?=$r_puntoventa->Direccion?></td>
      </tr>
      <tr>
        <td class=texto nowrap>Tel&eacute;fono</td>
        <td class=texto colspan="3" nowrap><?=$r_puntoventa->Telefono?></td>
      </tr>
      <tr>
        <td class=texto nowrap>&nbsp;</td>
        <td class=texto colspan="3" nowrap>&nbsp;</td>
      </tr>
      <tr>
        <td class=texto nowrap>Cuota N&uacute;mero</td>
        <td class=texto colspan="3"><?=$r_cuota->IDCuota?></td>
      </tr>
      <tr>
        <td class=texto nowrap>Fecha Cuota</td>
        <td class=texto colspan="3"><?=$r_cuota->FechaCuota?></td>
      </tr>
      <tr>
        <td class=texto nowrap>Fecha de Pago</td>
        <td class=texto colspan="3"><?=$r_cuota->FechaPago?></td>
      </tr>
      <tr>
        <td class=texto nowrap>Valor Total P&oacute;liza</td>
        <td class=texto colspan="3"><?=number_format( $r->ValorTotal )?></td>
      </tr>
      <tr>
        <td class=texto nowrap>Valor Cuota Quincena</td>
        <td class=texto colspan="3"><?=number_format( $r_cuota->ValorTotal )?></td>
      </tr>
      <tr>
        <td class=texto>Cuotas Pendientes</td>
        <td class=texto colspan="3" nowrap><?php
																$sql_cuotas = " SELECT count(*) as numero FROM CreditoCuota WHERE IDFactura = '$r_cuota->IDFactura' AND IDPuntoVenta = '$r_cuota->IDPuntoVenta' AND FechaPago = '0000-00-00 00:00:00' ";
																$qry_cuotas = db_query( $sql_cuotas );
																$r_cuotas = db_fetch_object( $qry_cuotas );
																echo $r_cuotas->numero;
																?></td>
      </tr>
      <tr>
        <td class=texto nowrap>&nbsp;</td>
        <td class=texto colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class=texto>CLIENTE</td>
        <td class=texto colspan="3" nowrap><?php
														  	 echo get_field("Cliente","CONCAT(Nombre,' ',Apellido)","IDCliente",$r->IDCliente);
															 //echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente) . "<br>".get_field("Cliente","Apellido","IDCliente",$r->IDCliente);
														  ?></td>
      </tr>
      <tr>
        <td class=texto>IDENTIFICACION</td>
        <td class=texto colspan="3" nowrap><?php echo get_field("Cliente","Cedula","IDCliente",$r->IDCliente);?></td>
      </tr>
      <tr>
        <td class=texto>Factura que abona:</td>
        <td class=texto colspan="3" nowrap><?php echo $r_factura->NumeroFactura;?></td>
      </tr>
      <tr>
        <td class=texto>Almacen donde se abona:</td>
        <td class=texto colspan="3" nowrap><?=get_field( "PuntoVenta","Nombre","IDPuntoVenta",$r_cuota->IDPuntoVentaPago);?></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table  width="215" cellspacing="1" border="0" align="center" id="#content">
		<tr>
		  <td valign="top">.</td>
		  <td valign="top" align="left"><table width="98%" border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff">
				<tr>
						<td colspan="2">
							<div align="center">
								<table width=100% border=0>
									<tr>
										<td colspan="4">&nbsp;</td>
									</tr>
									<!--
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto nowrap>
											<div align="right">IVA</div>
										</td>
										<td class=texto align="right"><?=number_format($r->ValorIVASinBono)?></td>
									</tr>
                                    -->



                                    <?php if($r->ValorBono!="0" ): ?>
									<?php endif; ?>


                                    <?php if($r->ValorBono!="0"): ?>
									<?php endif; ?>


                                    <?php if($r->ValorBono!="0"): ?>
									<?php endif; ?>


                                    <!--
									<tr>
										<td class=texto></td>
										<td class=texto width="171"></td>
										<td class=texto nowrap>
											<div align="right">Valor sin IVA</div>
										</td>
										<td class=texto align="right"><?=number_format((int)$r->ValorTotal-(int)$r->ValorIVA)?></td>
									</tr>
                                    -->

									<?php
									$sql_formapago = "SELECT * FROM FormaPagoFactura WHERE IDFactura = '$r->IDFactura' AND IDPuntoVenta = '$r->IDPuntoVenta'";
									$query_formapago = db_query( $sql_formapago );

									while( $r_formapago = db_fetch_object( $query_formapago ) )
									{
										if($r_formapago->Valor <> 0)
										{
								?>
									<?php 									}//end if($r_formapago->Valor <> 0)
								}//end while( $r_formapago = db_fetch_object( $query_formapago ) )
								?>
									<?php
                                    if( !empty( $array_fidelizacion ) && $club_suavidad=="S")
									{
									?>
                                   	<?php
									}//end if
									?>


                                    <tr>
                                    	<td class="texto" colspan="4" align="center">
                                        	<a href="/admin/files/facturas/FCreditos<?php echo $r_puntoventa->Codigo.$r->NumeroFactura . $idcuota ?>.pdf">pdf</a>
                                        </td>

                                    </tr>

								</table>
							</div>
						</td>
			</tr>
				</table>
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
