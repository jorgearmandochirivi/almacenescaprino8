<body> <?

$TitleMod ="PuntosCliente";

$Table = "PuntosCliente";
$TableJoin = "Cliente";
$Key = "IDPuntosCliente";
$MOD = "PuntosCliente";
$m = "PuntosCliente";

		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			
			case "cargarpuntos" :
				
				$frm= $HTTP_POST_VARS;
				$frm["FechaTrCr"] = date("Y-m-d");
				$frm["UsuarioTrCr"] = $datos["IDUsuario"];
				print_r( $frm );
				
				$sql_insert = " INSERT INTO PuntosCliente (IDCliente, IDPuntoVenta, IDFactura, Puntos, FechaVencimiento, Observaciones, FechaTrCr, UsuarioTrCr) VALUES ('" . $frm["IDCliente"] . "','" . $frm["IDPuntoVenta"] . "','" . $frm["IDFactura"] . "','" . $frm["Puntos"] . "','" . $frm["FechaVencimiento"] . "','" . $frm["Observaciones"] . "','" . $frm["FechaTrCr"] . "','" . $frm["UsuarioTrCr"] . "')  ";
				
				db_query( $sql_insert );
				
				echo "<script>location.href='?mod=PuntosCliente&idCliente=" . $frm["IDCliente"] . "';</script>";
				
			break;
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


	 	$sql =  "SELECT * FROM $Table WHERE IDCliente = '" . $idCliente . "' ORDER BY FechaVencimiento DESC ";
	 	
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
		<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
		<td></td>
	</tr>
</table>
	
<br>
<?
	$TABsel = 2;
	include("Cliente/menutabCliente.php");
?>

<table cellpadding=1 cellspacing=0 class=bordertable align=left width="100%" >
<form name="frm" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" >

	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<? echo $TitleMod ?> <? echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width="100%" border=0 cellspacing=1 cellpadding=1 class=texto>
			<tr class=row2>
				<td width="200"> 
            		Factura 
             	</td>
                <td>
                	 <input type=text size=25 class=input   name=IDFactura id=IDFactura value="">
                </td>
            </tr>
            <tr class="row2">
                <td>
                	Punto de Venta
              	</td>
                <td>
                	<select name="IDPuntoVenta" id="IDPuntoVenta">
                    	<option value="">Seleccione punto de venta</option>
                        <?
                        foreach( $array_puntos as $idpuntoventa => $datos_puntoventa )
						{
						?>
							<option value="<?=$idpuntoventa ?>"><?=$datos_puntoventa["Nombre"] ?></option>
						<?
						}//end for
						?>
                    </select>
                </td>
            </tr>
            <tr class=row2>
				<td width="200"> 
            		Cantidad de Puntos 
             	</td>
                <td>
                	 <input type=text size=25 class=input   name=Puntos id=Puntos value="">
                </td>
            </tr>  
            <tr class=row2>
				<td width="200"> 
            		Fecha de Vencimiento
             	</td>
                <td>
                	 <input type=text size=25 class=input   name=FechaVencimiento id=FechaVencimiento value="">
                     <script language="JavaScript1.2">
						<!--
							if (!document.layers)
								document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaVencimiento,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
						//-->
					</script>
                </td>
            </tr> 
            <tr class=row2>
				<td width="200"> 
            		Observaciones
             	</td>
                <td>
              		<textarea name="Observaciones" id="Observaciones" rows="5" cols="30" class="input" ></textarea>
                </td>
            </tr> 
            <tr>
				<td colspan=3 align=center class=row2>
                    <input type=hidden name=IDCliente id=IDCliente value="<?=$idCliente ?>">
                    <input type=hidden name=action value="cargarpuntos">
                    <input type=hidden name=mod value="PuntosCliente">
                    <input type=submit name=submit value="Cargar Puntos" class="submit">
				</td>
			</tr>        
       	</table>
  	</td>
 </tr>
 </form>
 </table>
<br><br><br><br>
<table width="100%" cellpadding=0 cellspacing=0 align=left class=bordertable>
	
    
<tr>
			<td class=titlemedium  bgcolor=#9daac6><% echo $info;%></td>
		</tr>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=10 nowrap>
<?
	print $pages;
?>
</td>
</tr>

<?
		if($rows > 0){
?>	

	<tr>
			<td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
<tr>
						<td class=rowform nowrap bgcolor=#DBEAF5> Factura </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Punto de Venta </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Fecha Creacion </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Fecha Vencimiento </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Puntos </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Redimidos </td>
					</tr>

<? while($r = db_fetch_object($result)){
?>
  	
<tr>
						<td nowrap class=row1><a target="_blank" href="?mod=Factura&action=edit&idpunto=<?=$r->IDPuntoVenta?>&id=<?=$r->IDFactura ?>"><? echo $r->IDFactura ?></a></td>
						<td nowrap class=row1><? echo $array_puntos[ $r->IDPuntoVenta ]["Nombre"] ?></td>
						<td nowrap class=row1><? echo $r->FechaTrCr ?></td>
						<td nowrap class=row1><? echo $r->FechaVencimiento ?></td>
                        <td nowrap class=row1><? echo $r->Puntos?></td>
                        <td nowrap class=row1><? echo $r->Redimido ?></td>
					</tr>
<? } // END for
?>
<tr>
</tr>	

<?
}// End if$rows
else
	echo "<tr><td><br><br><span class=subtitle><b>Este cliente no tiene puntos de fidelizaci&oacute;n </b></span></td></tr>";
?>
	
</table></td>
		</tr>
</table>	

<? 			

}// Enf function list()				

?>