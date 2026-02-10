<?php
	include("../admin/config.inc.php");
	Encabezado();
	$datos = Verifica_SesionCliente();
	$IDPuntoVenta = $datos['IDPuntoVenta'];
?>
<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
		<meta name="generator" content="Adobe GoLive 6">
		<title>Caprino :: Entradas</title>
		<link rel="stylesheet" href="../styles.css?1" type="text/css">
        <script>
		function imprimir(){
		  var objeto=document.getElementById('areaimprimir');  //obtenemos el objeto a imprimir
		  var ventana=window.open('','_blank');  //abrimos una ventana vacía nueva
		  ventana.document.write(objeto.innerHTML);  //imprimimos el HTML del objeto en la nueva ventana
		  ventana.document.close();  //cerramos el documento
		  ventana.print();  //imprimimos la ventana
		  ventana.close();  //cerramos la ventana
		}
		</script>
        
        
	</head>

	<body bgcolor="#ffffff" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">
<?php

$TitleMod ="Entrada";

$Table = "Entrada";
$TableJoin = "";
$Key = "IDEntrada";

$Remision = $_GET["Remision"];


		switch (nvl($action)) {
			default : 
				print_form($Remision,$IDPuntoVenta);
			break;
		
		} // End switch


/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($Remision,$IDPuntoVenta) {

	GLOBAL $TitleMod,$Table,$Key,$id, $idpunto, $dirroot;
	
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
	}
	
	

?>
<script>
var Check = new Array('Nombre','Publicar');
</script>
		<br>
	
<table class="forumline" width="100%" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<form name="frm" action="<?=$PHP_SELF?>" method="post" >
		<table width=101% border=0 cellspacing=1  class="texto forumline" >
				<tr>
					<td class="col1" nowrap>Numero de Remisi&oacute;n</td>
					<td class="col2"><input type="input" name="Remision" readonly value="<?=$Remision?>" class="tbox" id="Remision"></td>
				</tr>
				<tr>
					<td class="col1" nowrap>Fecha</td>
					<td class="col2" nowrap>
						<input type="input" name="Fecha" readonly value="<?=$fechaRemision?>" id="Fecha" class="tbox">
					</td>
				</tr>

		</table>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline" >
	
				<?php 
				foreach( $array_referencias as $key => $valor ){
				
					$class = repetition()?"col1list":"col2list";
					$tamanoarray = count( $valor );
					$columnasmas = $colspan - $tamanoarray;
				?>
	  			
	  			<tr>
	  				<td>
	  					<table width="100%" bgcolor="#FFFFFF">
							<tr>
								<td nowrap width=150  ><?php echo $key ?></td>
								<?php
								foreach( $valor as $idtalla => $datos )
								{
								?>
									<td nowrap  >
										<table>
											<tr>
												<td>
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
						</table>
					<td>
				</tr>
					
					
					
						<?php } // END for
						if( $TPares > 0 )
						{
				?>
						<tr>
								<td  bgcolor=#DBEAF5 colspan = "<?=$colspan+2?>" nowrap class="navpic" align="center">
							Total Pares = <?=$TPares?>
							</td>
							</tr>
						<?php
						}
						?>
						<tr>
							<td  bgcolor=#DBEAF5 colspan = "<?=$colspan+2?>" nowrap class="navpic" align="center">
							<?php
								print $pages;
							?>
							<input type="hidden" name="action" value="<?=$newmode?>">
							<input type="hidden" name="Referencias" value="<?=$frm['Referencias']?>">
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
							<input type="submit" class="button" name="enviar" value="<?=$caption?>">
						</td>
								
						
							
  </table>
                
</td>                
</tr>
</table>
				
                                
        <div id="areaimprimir">
        	<?php		
			
			$TPares = 0;
			
				
            $filedir = $dirroot . "/filesotros/Entrada/";	
			$name = "Entrada" . $Remision . ".html";
			$namePDF = "Entrada" . $Remision . ".pdf";
			$file = "$filedir$name";
			$filepdf = "$filedir$namePDF";
			ob_start();
			?>
            <table class="forumline" width="100%" cellspacing="1" border="0" align="center">
                    <tr>
                      <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.</td>
                    <td>
                        
                        <table width=101% border=0 cellspacing=1  class="texto forumline" >
                                <tr>
                                    <td class="col1" nowrap>Numero de Remisi&oacute;n</td>
                                    <td class="col2"><?=$Remision?></td>
                                </tr>
                                <tr>
                                    <td class="col1" nowrap>Fecha</td>
                                    <td class="col2" nowrap>
                                        <?=$fechaRemision?>
                                    </td>
                                </tr>
                
                        </table>
                        <table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline" >
                    
                                <?php 
                                foreach( $array_referencias as $key => $valor ){
                                
                                    $class = repetition()?"col1list":"col2list";
                                    $tamanoarray = count( $valor );
                                    $columnasmas = $colspan - $tamanoarray;
                                ?>
                                
                                <tr>
                                    <td>
                                        <table width="100%" bgcolor="#FFFFFF">
                                            <tr>
                                                <td nowrap width=150  ><?php echo $key ?></td>
                                                <?php
                                                foreach( $valor as $idtalla => $datos )
                                                {
                                                ?>
                                                    <td nowrap  >
                                                        <table>
                                                            <tr>
                                                                <td>
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
                                        </table>
                                    <td>
                                </tr>
                                    
                                    
                                    
                                        <?php } // END for
                                        if( $TPares > 0 )
                                        {
                                ?>
                                        <tr>
                                                <td   colspan = "<?=$colspan+2?>" nowrap class="navpic" align="center">
                                            Total Pares = <?=$TPares?>
                                            </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td  bgcolor=#DBEAF5 colspan = "<?=$colspan+2?>" nowrap class="navpic" align="center">
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
                
				<?php                
                $page = ob_get_contents();
                $fw = fopen($file, "w");
                fputs($fw,$page,strlen($page));
                fclose($fw);
                
                ob_end_clean();
                //echo $page;
                //passthru("htmldoc --format pdf --size 'Universal' --textfont Arial --title 'Acta' --charset 8859-15 --left 0cm --right 0cm --top 0cm --bottom 0cm --fontsize 7 --webpage $file -f $filedir/$namePDF");
                passthru("/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf");
				?>      
    	</div>
    
    	<div align="center">	
  		 <a href="/admin/filesotros/Entrada/Entrada<?=$Remision?>.pdf">Imprimir Entrada</a>         
        </div>
                
                
                
                
<?php
}// End function print_form()
?>
</body>
</html>
