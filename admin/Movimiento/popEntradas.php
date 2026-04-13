<?php 
	include("../config.inc.php");
	Encabezado();
	$datos = Verifica_Sesion();
	$IDPuntoVenta = $datos['IDPuntoVenta'];
?>
<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
		<meta name="generator" content="Adobe GoLive 6">
		<title>Caprino :: Entradas</title>
		<link rel="stylesheet" href="../../styles.css?1" type="text/css">
	</head>

	<body bgcolor="#ffffff" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">
<?php 

$TitleMod ="Entrada";

$Table = "Entrada";
$TableJoin = "";
$Key = "IDEntrada";

$Remision = $_GET['Remision'];
$IDPuntoVenta = $_GET['IDPuntoVenta'];


		switch (nvl($action)) {
			default : 
				print_form($Remision,$IDPuntoVenta);
			break;
		
		} // End switch


/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($Remision,$IDPuntoVenta) {

	GLOBAL $TitleMod,$Table,$Key,$id, $idpunto;
	$array_tallas = array();
	$array_referencias = array();
	$TPares = 0;
	
	//TALLAS
	$sql_tallas = "SELECT IDTalla, Descripcion FROM Talla";
	$qry_tallas = db_query( $sql_tallas );
	while( $r_tallas = db_fetch_array( $qry_tallas ) )
		$array_tallas[ $r_tallas['IDTalla'] ] = $r_tallas['Descripcion'];
	
	   //Consulta original
	   $sql_remision = " SELECT E.*, R.Numero FROM Entrada E, PuntoVentaReferencia P, Referencia R WHERE E.Remision = '$Remision' AND E.IDPuntoVenta = '$IDPuntoVenta'
						AND E.IDPuntoVentaReferencia = P.IDPuntoVentaReferencia AND P.IDReferencia = R.IDReferencia  ";	
						
		$sql_remision = "SELECT SUM(Cantidad) as Total, E.*, R.Numero 
						FROM Entrada E, PuntoVentaReferencia P, Referencia R 
						WHERE E.Remision = '$Remision' AND 
							  E.IDPuntoVenta = '$IDPuntoVenta' AND 
							  E.IDPuntoVentaReferencia = P.IDPuntoVentaReferencia AND 
							  P.IDReferencia = R.IDReferencia 
							  Group by R.Numero,IDTalla	";				
						
	$qry_remision = db_query( $sql_remision );
	while( $r_remision = db_fetch_array( $qry_remision ) )
	{
		$array_referencias[ $r_remision['Numero'] ][ $r_remision['IDTalla'] ] = $r_remision;
		if( empty( $r_laremision ) )
			$r_laremision = $r_remision;
	}//end while

?>
<script>
var Check = new Array('Nombre','Publicar');
</script>
		<br>
	
<table class="forumline" width="100%" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<form name="frm" action="<?php echo $PHP_SELF?>" method="post" >
		<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
				<tr>
					<td class="col1" nowrap>Numero de Remisi&oacute;n</td>
					<td class="col2"><input type="input" name="Remision" readonly value="<?php echo $r_laremision['Remision']?>" class="tbox" id="Remision"></td>
				</tr>
				<tr>
					<td class="col1" nowrap>Fecha</td>
					<td class="col2" nowrap>
						<input type="input" name="Fecha" readonly value="<?php echo $r_laremision['Fecha']?>" id="Fecha" class="tbox">
					</td>
				</tr>
				<tr>
					<td class="col1" nowrap>Punto Venta</td>
					<td class="col2" nowrap>
						<input type="input" name="Fecha" readonly value="<?php echo get_field( "PuntoVenta","Nombre","IDPuntoVenta",$r_laremision['IDPuntoVenta'] );?>" id="Fecha" class="tbox">
					</td>
				</tr>

		</table>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
	
				<?php 
				foreach( $array_referencias as $key => $valor ){
				
					$class = repetition()?"col1list":"col2list";
					$tamanoarray = count( $valor );
					$columnasmas = $colspan - $tamanoarray;
				?>
	  			
	  			<tr>
	  				<td>
	  					<table width="100%">
							<tr>
								<td nowrap width=150 class="<?php echo $class?>"><?php echo $key ?></td>
								<?php 
								foreach( $valor as $idtalla => $datos )
								{
								?>
									<td nowrap  class="<?php echo $class?>">
										<table>
											<tr>
												<td>
													<b><?php echo $array_tallas[ $idtalla ]; ?></b>
												</td>
											</tr>
											<tr>
												<td>
													<?php 
														echo $datos['Total'];
														$TPares += $datos['Total'];
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
								<td  bgcolor=#DBEAF5 colspan = "<?php echo $colspan+2?>" nowrap class="navpic" align="center">
							Total Pares = <?php echo $TPares?>
							</td>
							</tr>
						<?php 
						}
						?>
						<tr>
							<td  bgcolor=#DBEAF5 colspan = "<?php echo $colspan+2?>" nowrap class="navpic" align="center">
							<?php 
								print $pages;
							?>
							<input type="hidden" name="action" value="<?php echo $newmode?>">
							<input type="hidden" name="Referencias" value="<?php echo $frm['Referencias']?>">
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
							<input type="submit" class="button" name="enviar" value="<?php echo $caption?>">
						</td>
								<td></td>
						</td>
					</tr>		
				</table>
<?php 
}// End function print_form()
?>
</body>
</html>
