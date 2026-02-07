<?
	include("../config.inc.php");
	Encabezado();
	$datos = Verifica_Sesion();
	$IDPuntoVenta = $datos['IDPuntoVenta'];
?>
<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
		<meta name="generator" content="Adobe GoLive 6">
		<title>Caprino :: Ajustes</title>
		<link rel="stylesheet" href="../../styles.css?1" type="text/css">
	</head>

	<body bgcolor="#ffffff" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">
<?

$TitleMod ="Ajuste";

$Table = "Ajuste";
$TableJoin = "";
$Key = "IDAjuste";

$IDAjuste = $_GET[IDAjuste];
$IDPuntoVenta = $_GET[IDPuntoVenta];


		switch (nvl($action)) {
			default : 
				print_form($IDAjuste,$IDPuntoVenta);
			break;
		
		} // End switch


/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($IDAjuste,$IDPuntoVenta) {

	GLOBAL $TitleMod,$Table,$Key,$id, $idpunto;
	
	//TALLAS
	$sql_tallas = "SELECT IDTalla, Descripcion FROM Talla";
	$qry_tallas = db_query( $sql_tallas );
	while( $r_tallas = db_fetch_array( $qry_tallas ) )
		$array_tallas[ $r_tallas[IDTalla] ] = $r_tallas[Descripcion];
	
	//Cabecera
	$sql = " SELECT * FROM Ajuste WHERE IDAjuste = '$IDAjuste' AND IDPuntoVenta = '$IDPuntoVenta'  ";	
	$qry = db_query( $sql );
	$r = db_fetch_array( $qry );
	
	//Detalle
	$sql_detalle = " SELECT * FROM DetalleAjuste WHERE IDAjuste = '$IDAjuste' AND IDPuntoVenta = '$IDPuntoVenta' ";
	$qry_detalle = db_query( $sql_detalle );
	while( $r_detalle = db_fetch_array( $qry_detalle ) )
	{
		$array_referencias[ $r_detalle[Numero] ][ $r_detalle[Talla] ] = $r_detalle;
	}//end while

?>
<script>
var Check = new Array('Nombre','Publicar');
</script>
		<br>
	
<table class="forumline" width="100%" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<form name="frm" action="<?=$PHP_SELF?>" method="post" >
						<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
							<tr>
								<td class="col1" nowrap>Numero de Ajuste</td>
								<td class="col2"><input type="input" name="IDAjuste" readonly value="<?=$r[IDAjuste]?>" class="tbox" id="Remision"></td>
								<td class="col1" nowrap>Fecha</td>
								<td class="col2" nowrap><input type="input" name="Fecha" readonly value="<?=$r[FechaAjuste]?>" id="Fecha" class="tbox"></td>
							</tr>
							<tr>
								<td class="col1" nowrap>Usuario</td>
								<td class="col2" nowrap><input type="input" name="Usuario" readonly value="<?=$r[UsuarioTrCr]?>" id="Fecha" class="tbox"></td>
								<td class="col1" nowrap>Punto Venta</td>
								<td class="col2" nowrap><input type="input" name="IDPuntoVenta" readonly value='<?=get_field( "PuntoVenta","Nombre","IDPuntoVenta",$r[IDPuntoVenta] );?>' id="Fecha" class="tbox"></td>
							</tr>
							<tr>
								<td class="col1" nowrap>Observaciones</td>
								<td class="col2" colspan="3" nowrap>
									<textarea name="Observaciones" rows="10" cols="50"><?=$r[Observaciones]?></textarea>
								</td>
							</tr>
						</table>
						<table width=100% border=0 cellspacing=1 cellpadding=1 class=texto class="forumline" >
	
				<? 
				foreach( $array_referencias as $key => $valor ){
				
					$class = repetition()?"col1list":"col2list";
					$tamanoarray = count( $valor );
					$columnasmas = $colspan - $tamanoarray;
				?>
	  			
	  			<tr>
	  				<td>
	  					<table width="100%">
							<tr>
								<td nowrap width=150 class="<?=$class?>"><? echo $key ?></td>
								<?
								foreach( $valor as $idtalla => $datos )
								{
								?>
									<td nowrap  class="<?=$class?>">
										<table>
											<tr>
												<td>
													<b><? echo $array_tallas[ $idtalla ]; ?></b>
												</td>
											</tr>
											<tr>
												<td>
													<?
														echo $datos[Cantidad];
														$TPares += $datos[Cantidad];
													?>
												</td>
											</tr>
										</table>
										
									</td>
								<?
								}
								?>
							</tr>
						</table>
					<td>
				</tr>
					
					
					
						<? } // END for
						if( $TPares > 0 )
						{
				?>
						<tr>
								<td  bgcolor=#DBEAF5 colspan = "<?=$colspan+2?>" nowrap class="navpic" align="center">
							Total Pares = <?=$TPares?>
							</td>
							</tr>
						<?
						}
						?>
						<tr>
							<td  bgcolor=#DBEAF5 colspan = "<?=$colspan+2?>" nowrap class="navpic" align="center">
							<?
								print $pages;
							?>
							<input type="hidden" name="action" value="<?=$newmode?>">
							<input type="hidden" name="Referencias" value="<?=$frm['Referencias']?>">
							<?
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
								<td></td>
					
					
						</td>
					</tr>		
				</table>
<?
}// End function print_form()
?>
</body>
</html>