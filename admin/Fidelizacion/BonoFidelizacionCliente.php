<body> <?php 

//actualizo los bonos vencidos
$sql_vencidos = db_query("UPDATE BonoFidelizacion Set Estado = 'V'
				WHERE  FechaVencimiento < CURDATE() 
				AND Estado =  'D'");
/*				
//Actualizo bonos Web sin registro de factura como disponibles				
$sql_web_no_pagos = db_query("UPDATE BonoFidelizacion Set Estado = 'D'
				WHERE  Estado = 'W' and FechaTrEd <= DATE_SUB(NOW(),INTERVAL 3 DAY)");
*/				

$TitleMod ="Bonos";

$Table = "BonoFidelizacion";
$TableJoin = "Cliente";
$Key = "IDBonoFidelizacion";
$MOD = "BonoFidelizacion";
$m = "Fidelizacion";

		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			
			default : 
					list_r( $_GET["idCliente"] );
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");




/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r( $idCliente ){
		Global $TitleMod,$MOD,$Table,$Key,$listar;


	 	$sql =  "SELECT * FROM $Table WHERE IDCliente = '" . $idCliente . "' ORDER BY Fecha DESC ";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=1000;
   		$nav->execute($sql,$dblink);
		$total_records =  $nav->total_result;
		$rows = $nav->rows;
		$result = $nav->sql_result;
		$row = $offset;
		$startrow = $offset + 1;
		$finalrow = ($row * $nav->limit) + $rows;
	
	 	$pages = $nav->show_num_pages('&laquo;','&laquo; prev','&raquo;','next &raquo;','|','class=navvar');   // show pages
		
		$info = $nav->show_info(); 

 		if($_GET['in_order']=="ASC" || $_GET['in_order']==""){
								$img="down.png";
								$order="DESC";
							}else if($_GET['in_order']=="DESC"){
								$img="up.png";
								$order="ASC";
							}
		
		$sql_puntos = " SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre  ";
		$qry_puntos = db_query( $sql_puntos );
		while( $r_puntos = db_fetch_array( $qry_puntos ) )
			$array_puntos[ $r_puntos["IDPuntoVenta"] ] = $r_puntos;
							
?>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td></td>
	</tr>
</table>
	
<br>
<?php 
	$TABsel = 2;
	include("Fidelizacion/menutabCliente.php");
?>

<table cellpadding=1 cellspacing=0 class=bordertable align=left width="100%" >
<form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" >

	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>&nbsp;</td>
 </tr>
 </form>
 </table>
<br><br><br><br>
<table width="100%" cellpadding=0 cellspacing=0 align=left class=bordertable>
	
    
<tr>
			<td class=titlemedium  bgcolor=#9daac6><?php  echo $info;?></td>
		</tr>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=10 nowrap>
<?php 
	print $pages;
?>
</td>
</tr>

<?php 
		if($rows > 0){
?>	

	<tr>
			<td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
<tr>
  <td class=rowform nowrap bgcolor=#DBEAF5>NUMERO BONO</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Punto de Venta </td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Valor</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Fecha Generaci&oacute;n</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Estado </td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Cliente que redimio bono</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Punto donde se redimi&oacute;</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Fecha en que se redimi&oacute;</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Factura Con la que se redimi&oacute;</td>
					</tr

><?php while($r = db_fetch_object($result)){
?>
  	
<tr>
  <td nowrap class=row1><?php echo $r->IDBonoFidelizacion ?></td>
						<td nowrap class=row1><?php echo $array_puntos[ $r->IDPuntoVenta ]["Nombre"] ?></td>
						<td nowrap class=row1><?php echo number_format($r->Valor,2) ?></td>
						<td nowrap class=row1><?php echo $r->Fecha ?></td>
						<td nowrap class=row1><?php if($r->Estado=="D"){ ?> Disponible<?php }elseif($r->Estado=="V"){ ?>Vencido <?php }elseif($r->Estado=="W"){ ?>Web<?php } elseif($r->Estado=="R"){ ?>Redimido <?php }elseif($r->Estado=="C"){ ?> Cancelado (Factura Eliminada) <?php } ?></td>
						<td nowrap class=row1><?php echo get_field("Cliente","Cedula","IDCliente",$r->IDClienteRedimioBono)." " . get_field("Cliente","Nombre","IDCliente",$r->IDClienteRedimioBono) . " " .get_field("Cliente","Apellido","IDCliente",$r->IDClienteRedimioBono);  ?></td>
						<td nowrap class=row1><?php echo $array_puntos[ $r->IDPuntoVentaRedimido ]["Nombre"] ?></td>
		  <td nowrap class=row1><?php echo $r->FechaRedimido ?></td>
		  <td nowrap class=row1><a target="_blank" href="?mod=Factura&action=edit&idpunto=<?php echo $r->IDPuntoVentaRedimido?>&id=<?php echo $r->IDFactura ?>"><?php echo get_field( "Factura","NumeroFactura","IDFactura",$r->IDFactura) ?></td>
					</tr>
<?php } // END for
?>
<?php 
}// End if$rows
else
	echo "<tr><td><br><br><span class=subtitle><b>Este cliente no tiene puntos de fidelizaci&oacute;n </b></span></td></tr>";
?>
	
</table></td>
		</tr>
</table>	

<?php 			

}// Enf function list()				

?>