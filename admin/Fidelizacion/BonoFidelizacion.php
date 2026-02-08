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
$MOD = "BonoFidelizado";
$m = "Fidelizacion";

		$permisos = get_permiso($ID_Usuario,$m,$Table);


if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "list" :
				$sql = make_qry_string($HTTP_GET_VARS);
				list_r($sql);
			break;
			case "modificafecha":
				$sql_cambia_fecha="Update BonoFidelizacion Set FechaVencimiento = '".$_POST[FechaVencimientoBono]."', Estado = '".$_POST["EstadoBono"]."'  Where IDBonoFidelizacion = '".$_POST[IDBonoFidelizacion]."'";
				db_query($sql_cambia_fecha);
				echo "<script>alert('Fecha Modificada con exito');location.href='?mod=BonoFidelizado';</script>";
			break;


			default :
					list_r();
			break;

		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");




/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r( $sql ){
		Global $TitleMod,$MOD,$Table,$Key,$listar;


		if(empty($sql))
		 	$sql =  "SELECT $Table.* FROM $Table, Cliente  WHERE $Table.IDCliente = Cliente.IDCliente ORDER BY Fecha DESC ";

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
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td></td>
	</tr>
</table>

<br>

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



<?php if ($_GET[accion]=="edit_fecha_vencimiento"){
 // consulto los datos de los puntos
 $sql_bono="Select * From BonoFidelizacion Where IDBonoFidelizacion = '".$_GET[IDBonoFidelizacion]."'";
 $qry_bono=db_query($sql_bono);
 $row_bono=db_fetch_array($qry_bono);
 ?>

 <table cellpadding=1 cellspacing=0 class=bordertable align=left width="100%" >
<form name="frmFecha" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" >

	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;Modificar Fecha/ Estado vencimiento bono</td>
	</tr>
	<tr>
	<td>
		<table width="100%" border=0 cellspacing=1 cellpadding=1 class=texto>
			<tr class=row2>
				<td width="200">
            		Numero bono
             	</td>
                <td>				
                	 <input type=text size=25 class=input   name=Puntos id=Puntos value="<?php echo $row_bono["IDBonoFidelizacion"]; ?>" readonly>					
                </td>
            </tr>
            <tr class=row2>
				<td width="200">
            		Fecha de Vencimiento
             	</td>
                <td>
                	 <input type=text size=25 class=input   name=FechaVencimientoBono id=FechaVencimientoBono value="<?php echo $row_bono["FechaVencimiento"]; ?>">
                     <script language="JavaScript1.2">
						<!--
							if (!document.layers)
								document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmFecha.FechaVencimientoBono,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
						//-->
					</script>
                </td>
            </tr>
            <tr class=row2>
              <td>Estado</td>
              <td><span class="col2">
                  <select name="EstadoBono" id="EstadoBono" >
                    <option value=""></option>
                    <option value="D" <?php  if($row_bono["Estado"]=="D") echo "selected"; ?> >Disponible</option>
                    <option value="V" <?php  if($row_bono["Estado"]=="V") echo "selected"; ?>>Vencido</option>
                    <option value="W" <?php  if($row_bono["Estado"]=="W") echo "selected"; ?>>Web</option>
                    <option value="R" <?php  if($row_bono["Estado"]=="R") echo "selected"; ?>>Redimido</option>
                  </select>
			</td>
            </tr>
            <tr>
				<td colspan=3 align=center class=row2>
                    <input type=hidden name=IDBonoFidelizacion id=IDPuntosClienteFidelizacion value="<?php echo $_GET[IDBonoFidelizacion] ?>">
                    <input type=hidden name=action value="modificafecha">
                    <input type=hidden name=mod value="BonoFidelizado">
                    <input type=submit name=submit value="Modificar fecha" class="submit">
				</td>
			</tr>
       	</table>
  	</td>
 </tr>
 </form>
 </table>
  <?php } ?>


<table width="100%" cellpadding=0 cellspacing=0 align=left class=bordertable>

<tr>
			<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
		</tr>
<?php filtrar();?>
<tr>
			<td class=titlemedium  bgcolor=#9daac6><?php  echo $info;?></td>
		</tr>
<tr>
  <td class=texto bgcolor=#DBEAF5 colspan=10 nowrap><a href="Fidelizacion/exportabonos.php"><img src="../images/excel_icon.gif" alt="" width="20" height="20" border="0" >Exportar Registros </a></td>
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
						<td class=rowform nowrap bgcolor=#DBEAF5>Numero Documento</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Nombre Cliente</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Valor</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Fecha Generaci&oacute;n</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Fecha Vencimiento</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Estado </td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Cliente que redimio bono</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Punto donde se redimi&oacute;</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Fecha en que se redimi&oacute;</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Factura Con la que se redimi&oacute;</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Valor Factura</td>
					</tr

>
  <?php while($r = db_fetch_object($result)){
?>

<tr>
  <td nowrap class=row1>
  	<a href="../Movimiento/popBono.php?id=<?php echo $r->IDBonoFidelizacion; ?>" target="_blank">		
  		<?php echo $r->IDBonoFidelizacion ?>
	</a>
	<?php if($_GET["QryString"]){ ?>
	<br><b>Reenviar Bono a:</b> <?php echo get_field("Cliente","Email","IDCliente",$r->IDCliente); ?>
	<br>
		<?php 		
		if($r->Estado=="D"){ ?>
			<input type="button" value="Reenviar" class="submit reenviar_bono" IDCliente="<?php echo $r->IDCliente ?>" IDBono="<?php echo $r->IDBonoFidelizacion ?>"> 
		<?php }   ?>	
	<?php } ?>	
	</td>
						<td nowrap class=row1><?php echo $array_puntos[ $r->IDPuntoVenta ]["Nombre"] ?></td>
						<td nowrap class=row1><?php echo get_field("Cliente","Cedula","IDCliente",$r->IDCliente); ?></td>
						<td nowrap class=row1><?php echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente) . " " . get_field("Cliente","Apellido","IDCliente",$r->IDCliente); ?></td>
						<td nowrap class=row1><?php echo number_format($r->Valor,0) ?></td>
						<td nowrap class=row1 align=center><?php echo $r->Fecha ?></td>
						<td nowrap class=row1>
							<?php echo $r->FechaVencimiento ?>
							<?php //if ($r->Estado=="D" || $r->Estado=="V" ){ ?>
                                <a href="?mod=BonoFidelizado&IDBonoFidelizacion=<?php echo $r->IDBonoFidelizacion; ?>&accion=edit_fecha_vencimiento">cambiar fecha / estado</a>
                            <?php //} ?>
                        </td>
						<td nowrap class=row1><b><?php if($r->Estado=="D"){ ?> Disponible<?php }elseif($r->Estado=="V"){ ?>Vencido <?php }elseif($r->Estado=="W"){ ?>Web<?php }elseif($r->Estado=="R"){ ?>Redimido <?php } elseif($r->Estado=="C"){ echo "Anulado"; } ?></b></td>
						<td nowrap class=row1><?php echo get_field("Cliente","Cedula","IDCliente",$r->IDClienteRedimioBono)." " . get_field("Cliente","Nombre","IDCliente",$r->IDClienteRedimioBono) . " " .get_field("Cliente","Apellido","IDCliente",$r->IDClienteRedimioBono);  ?></td>
						<td nowrap class=row1><?php echo $array_puntos[ $r->IDPuntoVentaRedimido ]["Nombre"] ?></td>
		  <td nowrap class=row1><?php echo $r->FechaRedimido ?></td>
		  <td nowrap class=row1><a target="_blank" href="?mod=Factura&action=edit&idpunto=<?php echo $r->IDPuntoVentaRedimido?>&id=<?php echo $r->IDFactura ?>"><?php echo $r->IDFactura ?></td>
		  <td nowrap class=row1 align="right">
		  	<?php
			if($r->Estado=="R"){
				$sql_factura_redimido = db_query("Select * From Factura Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaRedimido."'");
				$row_factura_redimido = db_fetch_array($sql_factura_redimido);
				echo "$".number_format($row_factura_redimido["ValorTotal"],0,',','.');
			}
			 ?>

           </td>
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

<?php
/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD;
?>
	<form name="frm" action="./" method="get" onSubmit="return valbuscar(document.frm)">
	<tr>
				<td class="rowform" align="center" colspan=8>
					<select name="field" id="Buscar por" class="popup">
                    	<option value="IDBonoFidelizacion">Numero bono</option>
						<option value="Cliente.Cedula">Cedula</option>
						<!--<option value="Ciudad.Descripcion">Ciudad</option>-->
					</select>


                    <input type="text" size="20" name="QryString" id="Buscar Por" class="post"> Entre <input type=text readonly size=10 class=input name=limit1>
					<script language='JavaScript1.2'>
								<!--
								if (!document.layers)
								document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit1,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
								//-->
					</script>
					 y <input type=text size=10 readonly class=input name=limit2>
					<script language='JavaScript1.2'>
								<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
								//-->
					</script>
					 <br>
					ordenar por <select name="order_by" class="popup">
						<option value="IDBonoFidelizacion">Numero bono</option>
                        <option value="Cliente.Cedula">Cedula</option>
					</select> de forma <select name="in_order" class="popup">
						<option value="ASC">Ascendente</option>
						<option value="DESC" selected>Descendente</option>
					</select>
					Listar <select name="listar" class="popup">
									<option value="10">10</option>
									<option value="15">15</option>
									<option value="20">20</option>
									<option value="25">25</option>
									<option value="30">30</option>
								</select>
					<br>
					<input type="hidden" name="mod" value="<?php echo $MOD?>">
					<input type="hidden" name="rangofield" value="Fecha">
					<input type="hidden" name="action" value="list">
					<input type="hidden" name="tjoin" value="Cliente">
					<input type="submit" name="submit" value="Buscar" class="submit">
				</td>
			</tr>
	</form>
<?php 
	}//End function filtrar
?>
