<?php
	include("../admin/config.inc.php");
	Encabezado();
	$datos = Verifica_SesionCliente();
	$IDPuntoVenta = $datos['IDPuntoVenta'];
	
	
	
	$TitleMod ="Entrada";
	$Table = "Entrada";
	$TableJoin = "";
	$Key = "IDEntrada";	
	$Remision = $_GET["Remision"];

	ob_start();

?>
<html>
<head>
</head>
<style>
<!--
body{
	font-size:11px;
	margin:0;
	font-family: "Arial Black";
}
table{
	font-size:12px;
	font-family: "Arial Black";
}
@page { 

	size 6cm 12cm; 
	margin-left: 0;
	font-family: "Arial Black";
	}

</style>


<body>
<?php

//TALLAS
	$sql_tallas = "SELECT IDTalla, Descripcion FROM Talla";
	$qry_tallas = db_query( $sql_tallas );
		while( $r_tallas = db_fetch_array( $qry_tallas ) )
			$array_tallas[ $r_tallas["IDTalla"] ] = $r_tallas["Descripcion"];
	
	$sql_remision = " SELECT E.*, R.Numero FROM Entrada E, PuntoVentaReferencia P, Referencia R WHERE E.Remision = '$Remision' AND E.IDPuntoVenta = '$IDPuntoVenta'
						AND E.IDPuntoVentaReferencia = P.IDPuntoVentaReferencia AND P.IDReferencia = R.IDReferencia  ";	
	$qry_remision = db_query( $sql_remision );
		while( $r_remision = db_fetch_array( $qry_remision ) ){
			$array_total[ $r_remision["Numero"] ][ $r_remision["IDTalla"] ] += $r_remision["Cantidad"];	
			$r_remision["Cantidad"] = $array_total[ $r_remision["Numero"] ][ $r_remision["IDTalla"] ];
			$array_referencias[ $r_remision["Numero"] ][ $r_remision["IDTalla"] ] = $r_remision;
			$fechaRemision = $r_remision["FechaRemision"];
			$id_punto_venta = $r_remision["IDPuntoVenta"];
	}

?>
                                
        	<?php		
			
			$TPares = 0;
			
				
            $filedir = $dirroot . "/filesotros/Entrada/";	
			$name = "Entrada" . $Remision . ".html";
			$namePDF = "Entrada" . $Remision . ".pdf";
			$file = "$filedir$name";
			$filepdf = "$filedir$namePDF";			
			?>
            <table class="forumline" width="100%" cellspacing="1" border="0" align="center">
                    <tr>
                      <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.</td>
                    <td>
                        
                        <table width=101% border=0 cellspacing=1  class="texto forumline" >
                                <tr>
                                  <td class="col1" nowrap>Almacen: <span class="col2">
                                      <?php
                                      $sql_almacen = db_query("Select * From PuntoVenta Where IDPuntoVenta = '".$id_punto_venta."'");
									  $row_punto = db_fetch_array($sql_almacen);
									  echo $row_punto["Nombre"];
									  ?>
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="col1" nowrap>Numero de Remisi&oacute;n: <span class="col2">
                                      <?=$Remision?>
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="col1" nowrap>Fecha: <span class="col2">
                                      <?=$fechaRemision?>
                                    </span></td>
                                </tr>
                
                        </table>
                        
                        <table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline" >
                    
                                <?php 
                                foreach( $array_referencias as $key => $valor ){
                                
                                    $class = repetition()?"col1list":"col2list";
                                    $tamanoarray = count( $valor );
                                    $columnasmas = $colspan - $tamanoarray;
									$contador++;
                                ?>
                                
                                <tr>
                                    <td>
                                        <table width="10%" bgcolor="#FFFFFF">
                                            <tr>
                                                <td nowrap><?php echo $key ?></td>
                                                <?php												
                                                foreach( $valor as $idtalla => $datos )
                                                {
													
                                                ?>
                                                    <td nowrap  >
                                                        <table>
                                                            <tr>
                                                                <td >
                                                                    <b><?php echo $array_tallas[ $idtalla ]; ?></b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <?php
                                                                        echo $datos["Cantidad"];
                                                                        $TPares += $datos["Cantidad"];
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        
                                                    </td>
                                                <?php
                                                }
                                                ?>
                                            </tr>
                                </table>                                    </tr>
                                
								        <?php 
                                        if($contador==20): ?>
                                            <div style="page-break-after:always;"></div>
                                            <?php 
											echo '</table>';
											echo '<table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline" >';
                                            $contador=0;
                                        endif; ?>
                                    
                                    
                                    
                                        <?php } // END for
                                        if( $TPares > 0 )
                                        {
                                ?>
                                        <tr>
                                                <td   colspan = "<?=$colspan+2?>" nowrap class="navpic" align="left">
                                            Total Pares = <?=$TPares?>
                                            </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td  bgcolor=#DBEAF5 colspan = "<?=$colspan+2?>" nowrap class="navpic" align="left">
                                            <?php
                                                print $pages;
                                            ?>
                                            <?php
                                            if( $newmode == "entrada" )
                                            {
                                                $caption = "Realizar Entrada";
                                            }
                                            else
                                            {
                                                $caption = "Comfirmar Entrada";
                                            }
                                            ?>                                            
                                        </td>
                                                
                                        
                                            
                                </table>
                                
                </td>                
                </tr>
                </table>
</body>
</html>

<?php

require_once("../jscripts/dompdf/dompdf_config.inc.php");
$dompdf = new DOMPDF();
//$dompdf->set_paper( array(0,0, 5 * 72, 5 * 72), "portrait" ); // 12" x 12"
$dompdf->set_paper( array(0,0, 5 * 72, 12 * 72), "portrait" ); // 12" x 12"
$dompdf->load_html(ob_get_clean());
$dompdf->render();
//$pdf = $dompdf->output();
$filename = "files/Entrada" . $Remision .".pdf";
file_put_contents($filename, $pdf);
//$dompdf->stream($filename);
$dompdf->stream($filename, array("Attachment" => false));

exit;
?>
