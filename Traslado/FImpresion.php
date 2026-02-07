<?
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
		
	$TitleMod ="Traslado";
	
	$Table = "Traslado";
	$TableJoin = "Traslado";
	$Key = "IDTraslado";
	
	$qid = db_query(" SELECT * FROM Traslado WHERE IDTraslado = '$id' AND IDPuntoVentaOrigen = '$idpunto' ");
		
	$r = db_fetch_object($qid);
	
	$sql_puntoVenta = "SELECT * from PuntoVenta WHERE IDPuntoVenta = '$r->IDPuntoVenta' ";
	$qry_puntoventa = db_query( $sql_puntoVenta );
	$r_puntoventa = db_fetch_object( $qry_puntoventa );

	$filedir = $dirroot . "/files/Traslados/";
	
	$name = "Traslado" . $r_puntoventa->Codigo.$r->IDTraslado . ".html";
	$namePDF = "Traslado" . $r_puntoventa->Codigo.$r->IDTraslado . ".pdf";
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
	font-size:7px;
	margin:0;
}
table{
	font-size:7px;
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
	font-size: 7px;
	color: #000000;
}
.bordertable {border: dotted 1px; color:#c3c3c3}
#content { margin-left:0; 
     float:none; 
     width:auto;
     height : 150px; 
     color:black;
	 }
table{
	font-size:7px;
	margin:0;
}


-->
}
</style>


<body>

	
			<table  width="215" cellspacing="1" border="0" align="left" height="100" id="#content" style="table-layout:fixed">
		<tr>
			<td valign="top">
				<table width="100%" border=0 cellspacing=0 cellpadding=0 class=texto bgcolor="#ffffff" style="table-layout:fixed">
					<tr>
						<td colspan="2">
							<div align="center">
								<table width=100% border=0>
									<tr>
									  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.</td>
										<td colspan="4">
											<table class=rowtable width="100%" style="table-layout:fixed">
												


                                                <tr>
													<td class=texto colspan="4" nowrap>Traslado: No. <? echo $r_puntoventa->Codigo.$r->IDTraslado?></td>
												</tr>


												<tr>
													<td class=texto>Almacen Destino</td>
													<td class=texto colspan="3" nowrap><?
																echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaDestino);
															?> </td>
												</tr>
														<tr>
													<td class=texto>Almacen Origen</td>
															<td class=texto colspan="3" nowrap><?
																echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVentaOrigen);
															?></td>
														</tr>
														<tr>
													<td class=texto nowrap>Fecha Traslado</td>
													<td class=texto colspan="3" nowrap><?=$r->Fecha ?></td>
										</tr>
												
                                                
                                                
                                                
                                                
                                                
                                                <tr>
													<td class=texto>Estado</td>
													<td class=texto nowrap><? echo get_field("EstadoTraslado","Descripcion","IDEstadoTraslado",$r->IDEstadoTraslado); ?></td>
													<td class=texto></td>
													<td class=texto></td>
												</tr>
												<tr>
													<td class=texto nowrap>Observaciones</td>
													<td class=texto colspan="3"><?=$r->Observaciones?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
									  <td>&nbsp;</td>
										<td colspan="4">
											<table class="bordertable" border="0" cellspacing="1" cellpadding="0" id=table1 width="100%" style="table-layout:fixed">


                                                <tr>
                                                    <td colspan="4">
                                                        <table class="texto" border="0" cellspacing="1" cellpadding="0" width="100%" id=table1 style="table-layout:fixed">
                                                            <tr>
                                                                <td align="center"><b>Referencia</b></td>
                                                                <td align="center"><b>Talla</b></td>
                                                                <td align="center"><b>Cantidad</b></td>
                                                            </tr>
                                                            <?
                                                                $sql_detalle = " SELECT * FROM DetalleTraslado WHERE $Key = '$r->IDTraslado' AND IDPuntoVentaOrigen = '$r->IDPuntoVentaOrigen' ";
                                                                $query_detalle = db_query($sql_detalle);
                                                                $i = 0;
                                                                while( $r_detalle = db_fetch_object( $query_detalle ) )
                                                                {
                                                                    $class = repetition()?"col1list":"col2list";
                                                                    $i++;
                                                                    $PuntoVentaReferencia = get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica);
                                                                    $Talla = get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica);;
                                                            ?>
                                                            <tr >
                                                                <td  style="background-color:#FFFFFF;">
                                                                    <? echo get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$PuntoVentaReferencia)); ?></td>
                                                                <td style="background-color:#FFFFFF;">
                                                                    <? echo get_field("Talla","Descripcion","IDTalla",$Talla) ?>
                                                                </td>
                                                                <td style="background-color:#FFFFFF;">
                                                                    <?  $cantidad_total += $r_detalle->Cantidad;
																		echo $r_detalle->Cantidad;
																	
																	?>
                                                                </td>
                                                            </tr>
                                                            <?
                                                                }//end while
                                                            ?>
                                                             <tr >
                                                               <td  style="background-color:#FFFFFF;">                                                             
                                                               <td style="background-color:#FFFFFF;">&nbsp;</td>
                                                               <td style="background-color:#FFFFFF;">&nbsp;</td>
                                                             </tr>
                                                             <tr >
                                                                <td  style="background-color:#FFFFFF;">
                                                                    
                                                                <td style="background-color:#FFFFFF;">TOTAL:
                                                                    
                                                                </td>
                                                                <td style="background-color:#FFFFFF;">
                                                                    <? echo $cantidad_total ?>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                
                                        
											</table>
										</td>
									</tr>
                                    
                                    
                                    <tr>
                                      <td class="texto" align="center">&nbsp;</td>
                                    	<td class="texto" colspan="4" align="center">
                                        	<a href="/admin/files/Traslados/Traslado<?=$r_puntoventa->Codigo.$r->IDTraslado?>.pdf">pdf</a>
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
</body>
</html>
<?

$page = ob_get_contents();
$fw = fopen($file, "w");
fputs($fw,$page,strlen($page));
fclose($fw);

ob_end_clean();

echo $page;
//passthru("htmldoc --format pdf --size 'Universal' --textfont Arial --title 'Acta' --charset 8859-15 --left 0cm --right 0cm --top 0cm --bottom 0cm --fontsize 7 --webpage $file -f $filedir/$namePDF");
passthru("/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf");
?>
