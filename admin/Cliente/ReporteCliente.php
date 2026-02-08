
<body>
<?php 


$TitleMod ="Cliente";

$Table = "Cliente";
$TableJoin = "";
$Key = "IDCliente";
$MOD = "ReporteCliente";
$m = "Cliente";
?>

 <?php 


$permisos = get_permiso($ID_Usuario,$m,$Table);


//envia_nuevo_garantia("951");

if($permisos[0] >= 2)
{
		switch (nvl($action)) {


			case "list" :

				if(!empty($_GET[IDPuntoVenta]))
					$condiciones.=" and F.IDPuntoVenta = '".$_GET[IDPuntoVenta]."'";

				if(!empty($_GET[IDCiudad])){
					$TablaJoin2=", PuntoVenta PV, Ciudad CIUD";
					$condiciones.=" and PV.IDCiudad = CIUD.IDCiudad and PV.IDCiudad = '".$_GET[IDCiudad]."' ";
				}


				if (!empty($_GET[limit1]) && !empty($_GET[limit2]) ){
					$condiciones.=" and F.FechaFactura between '".$_GET[limit1]."' and '".$_GET[limit2]."'";
					$condicion_fecha=" and F.FechaFactura between '".$_GET[limit1]."' and '".$_GET[limit2]."'";
				}



				$sql="SELECT SUM(F.ValorTotal) as ValorCompras,C.Nombre,C.Apellido,C.Cedula,C.IDCliente,C.IDCiudad,F.IDPuntoVenta FROM Cliente C, Factura F ".$TablaJoin2." WHERE C.IDCliente=F.IDCliente	".$condiciones." GROUP BY IDCliente ORDER BY ValorCompras DESC";


			//$sql = make_qry_string($HTTP_GET_VARS);
			list_r($sql,$condicion_fecha);
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
	function list_r($sql="",$condicion_fecha=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar;


		if (empty($_GET[limit1]) && empty($_GET[limit2]) ){
			$_GET[limit1]=date("Y-m-01");
			$_GET[limit2]=date("Y-m-30");
			$condiciones.=" and F.FechaFactura between '".$_GET[limit1]."' and '".$_GET[limit2]."'";
		}


	if(empty($sql))
		$sql="SELECT SUM(F.ValorTotal) as ValorCompras,C.Nombre,C.Apellido,C.Cedula,C.IDCliente,C.IDCiudad,F.IDPuntoVenta FROM Cliente C, Factura F WHERE C.IDCliente=F.IDCliente	".$condiciones."	GROUP BY IDCliente ORDER BY ValorCompras DESC";



		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=40;
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

							?>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
	</tr>
</table>
<?php 
		if($rows > 0){
?>
<br>
<table width=500 cellpadding=2 cellspacing=3 align=center class=bordertable>
	<tr>
		<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
	</tr>
	<?php filtrar();?>
	<tr>
		<td class=titlemedium  bgcolor=#9daac6><?php  echo $info;?></td>
	</tr>
	<tr>
		<td class=texto bgcolor=#DBEAF5 colspan= nowrap>
		<?php 
			print $pages;
		?>
		</td>
	</tr>
	<tr>
	  <td>
<table width=100% border=0 cellspacing=4 cellpadding=0>
<tr>
  <td colspan="18" align=left valign=middle class=rowform><a href="Cliente/exportaclientecompra.php?sql=<?php echo $sql; ?>&condicionfecha=<?php echo $condicion_fecha; ?>"><img src="../images/excel_icon.gif" alt="" width="20" height="20" border="0" >Exportar Registros </a></td>
  </tr>
<tr>
						<td class=rowform nowrap bgcolor=#DBEAF5> Cedula  </td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Nombre</a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Ciudad</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Valor</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Items</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Ver detalles</td>
					</tr>

<?php while($r = db_fetch_object($result)){
	$tallap="";
	$id_referencia_item="";
?>

	<tr>
						<td nowrap class="<?php echo $class?>"><?php echo $r->Cedula; ?></td>
						<td nowrap class="<?php echo $class?>"><?php echo $r->Nombre . " " . $r->Apellido; ?></td>
						<td nowrap class="<?php echo $class?>"><?php echo get_field("Ciudad","Descripcion","IDCiudad",$r->IDCiudad) ?></td>
						<td nowrap class="<?php echo $class?>">$<?php echo number_format($r->ValorCompras, 0 ); ?></td>
						<td nowrap class="<?php echo $class?>"><?php 
						 $sql_item="SELECT SUM(DF.Cantidad) as CantidadTotal
									FROM Factura F, DetalleFactura DF
									WHERE F.IDFactura=DF.IDfactura AND
												F.IDCliente='".$r->IDCliente."'
												".$condicion_fecha."
									GROUP BY F.IDCliente";
						$r_item=db_query($sql_item);
						$row_item=db_fetch_array($r_item);
						echo $row_item["CantidadTotal"];
							?></td>
						<td align=center valign=middle nowrap width=50 class=row2>
								&nbsp;<a href='<?php echo "?mod=ClienteFidelizado&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
						</td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=18 nowrap>
	<?php 
		print $pages;
		?>
</td>
</tr>
</table></td>
</tr>
</table>

<?php 
}// End if$rows
else
	echo "<br><br><span class=subtitle><b>No existen registros en  $TitleMod </b></span>";
}// Enf function list()

/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD;
?>
	<form name="frm" action="./" method="get" onSubmit="return valbuscar(document.frm)">
	<tr>
				<td class="rowform" align="center" colspan=8>

                 <table>

									 <tr>
										 <td>Fecha Inicio</td>
										 <td>
											 <?php
											 if (empty($_GET[limit1]) && empty($_GET[limit2]) ){
									 			$_GET[limit1]=date("Y-m-01");
									 			$_GET[limit2]=date("Y-m-30");
									 			$condiciones.=" and F.FechaFactura between '".$_GET[limit1]."' and '".$_GET[limit2]."'";
									 		}
											?>
										 <input type=text readonly size=10 class=input name=limit1 value="<?php echo $_GET[limit1];?>">
					 <script language='JavaScript1.2'>
							 <!--
							 if (!document.layers)
							 document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit1,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
							 //-->
				 </script>
										 </td>
										 <td>Fecha Fin</td>
										 <td>
										 <input type=text size=10 readonly class=input name=limit2 value="<?php echo $_GET[limit2];?>">
					 <script language='JavaScript1.2'>
							 <!--
							 if (!document.layers)
								 document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
							 //-->
				 </script>
										 </td>
										 <td>&nbsp;</td>
										 <td>&nbsp;</td>
									 </tr>

                    <tr>

                         <td>
                         Punto Venta Registra

                        </td>
                        <td>
                        	 <select name="IDPuntoVenta" id="IDPuntoVenta">
                    <option value="">[Seleccione]</option>
                    <?php
                    $sql_vta=db_query("Select * from PuntoVenta Where Publicar = 'S' Order by Nombre");
                    while($row_vta=db_fetch_array($sql_vta)){
                        ?>
                        <option value="<?php echo $row_vta["IDPuntoVenta"]; ?>"><?php echo $row_vta["Nombre"]; ?></option>
                    <?php
                    }
                    ?>
          		</select>

                        </td>
                        <td>
                        Ciudad


                        </td>
                        <td>
                         <select name="IDCiudad" id="IDCiudad">
                            <option value=""></option>
                            <option value="1">Bogota</option>
                            <option value="2">Medellin</option>
                        </select>

                        </td>
                    </tr>

                 </table>
                 <br>
                <input type="hidden" name="mod" value="<?php echo $MOD?>">
                <input type="hidden" name="action" value="list">
                <input type="submit" name="submit" value="Buscar" class="submit">


				</td>
			</tr>
	</form>
<?php 
	}//End function filtrar
?>
