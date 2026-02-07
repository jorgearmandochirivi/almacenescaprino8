<body>
	<?php

$TitleMod ="Novedades Bancos";

$Table = "NovedadBanco";
$TableJoin = "";
$Key = "IDNovedadBanco";
$MOD = "NovedadBanco";
$m = "Banco";

		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Crear Novedad");
			break;
			
			case "insert" :
				
				$frm= vars_LOG($HTTP_POST_VARS);
				
				//print_r($frm);
				
				foreach( $frm['Banco'] as $idbanco => $valor )
				{
				
					$idpuntoventabanco = get_field("PuntoVentaBanco","IDPuntoVentaBanco","IDBanco",$idbanco."' AND IDPuntoVenta = '$IDPuntoVenta");
					
					$idnovedadbanco = get_maxID("NovedadBanco","IDNovedadBanco");
					
					$valorbanco = $frm['valor'][$idbanco];
					
					$sqlinsert = "INSERT INTO NovedadBanco VALUES ('$idnovedadbanco','$idpuntoventabanco','$Fecha','$valorbanco','$frm[UsuarioTrCr]','$frm[FechaTrCr]','$frm[UsuarioTrEd]','$frm[FechaTrEd]') ";
					db_query( $sqlinsert );
					
				}
				
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				$frm= vars_LOG($HTTP_POST_VARS);
				update($frm);
			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$HTTP_GET_VARS[action]="";
				delete($ID);
			break;
			case "list" :	
			$sql = make_qry_string($HTTP_GET_VARS);
			list_r($sql);
			break;
			default : 
					list_r();
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$IDPuntoVenta,$Fecha;
	
	$qid = db_query(" SELECT * FROM PuntoVentaBanco WHERE IDPuntoVenta = '$IDPuntoVenta' ");

?><br>
	<form name="frm" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg(this,Check)"<?php }?>>

<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="550">
	
	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				<?php echo $TitleMod ?> <?php echo $r->$Key ?>
			</span>
			<span class="gen">
				<?php echo $info ?>
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>
	
<table class="forumline" width="550" cellspacing="1" border="0" align="center">
	<tr>
		<td>
			<table width=100% border=0 cellspacing=1 cellpadding=0 class=texto class="forumline" >
				
				<tr>
					<td class="forumlink" colspan="2">
						<table width=100% border=0 cellspacing=0 cellpadding=0>
						<?php
							
							$Fechainicio = $Fecha." "."00:00:00";
							$Fechafin = $Fecha." "."23:59:59";
							
							$sql_factura = "SELECT sum(ValorTotal) as Total 
											FROM Factura 
											WHERE FechaFactura >= '$Fechainicio' AND FechaFactura <= '$Fechafin' 
											AND IDPuntoVenta = '$IDPuntoVenta'";
							
							$query_factura = db_query($sql_factura);
							
							while( $r_factura = db_fetch_object( $query_factura ) )
							{
								
						?>
							<tr>
								<td width="30%" align="right" class=col1><b>Total Vendido</b></td>
								<td align="left" class=col2>
									<?php echo "$ ".number_format( $r_factura->Total )?>
								</td>
							</tr>
						<?php		
							
							}//end while( $r_factura = db_fetch_object( $query_factura ) )
						?>
						</table>
					</td>
				</tr>
			<?php
			//INFORMACION DE TOTAL VENDIDO EL DIA QUE SE SELECCIONA
			?>
			
				<tr>
					<td class="forumlink" colspan="2">
						<table width=100% border=0 cellspacing=0 cellpadding=0>
							<tr >
								<td width="30%" align="center" class="titulodetablas"><b>Banco</b></td>
								<td align="center" class="titulodetablas">
									<b>Valor</b>
								</td>
							</tr>
							<?php
								$sql_novedad = "SELECT NB.* 
												FROM NovedadBanco NB, PuntoVentaBanco PB 
												WHERE NB.Fecha = '$Fecha' AND NB.IDPuntoVentaBanco = PB.IDPuntoVentaBanco
												AND PB.IDPuntoVenta = '$IDPuntoVenta'";
								
								$query_novedad = db_query($sql_novedad);
								
								while( $r_novedad = db_fetch_object( $query_novedad ) )
								{
									
							?>
							<tr>
								<td width="30%" align="center" class=col1list><?=get_field("Banco","Nombre","IDBanco",get_field("PuntoVentaBanco","IDBanco","IDPuntoVentaBanco",$r_novedad->IDPuntoVentaBanco))?></td>
								<td align="center" class=col1list>
									<?php echo number_format( $r_novedad->Valor )?>
								</td>
							</tr>
							<?php		
								
								}//end while( $r_novedad = db_fetch_object( $query_novedad ) )
							?>
						</table>
					</td>
				</tr>
				
				<?php
				//INFORMACION DEL DIA QUE SE SELECCIONA 
				?>
			
				<tr>
					<td class="navpic" colspan="2">
						Ingrese los Valores Correspondientes a la novedad
					</td>
				</tr>
				<?php
				while( $r = db_fetch_object( $qid ) )
				{
				?>
				<tr>
							<td>
						<table width=100% border=0 cellspacing=0 cellpadding=0 class=texto>
							<tr>
								<td class="col1" width="30%"><?php echo get_field( "Banco","Nombre","IDBanco", $r->IDBanco )."-<b>".get_field( "FormaPago","Descripcion","IDFormaPago", $r->IDFormaPago)."</b>"?></td>
								<td class="col2">
									<input type=text class=input name="valor[<?php echo $r->IDBanco?>]" value="">
									<input type=hidden name="Banco[<?php echo $r->IDBanco?>]" value="<?php echo $r->IDBanco?>">
								</td>
							</tr>
						</table>
					</td>
						</tr>
				<?php
				}//end while( $r = db_fetch_object( $qid ) )
				?>
				
				<tr>
					<td class="navpic" align="center" colspan="2">
						<input type="hidden" name="Fecha" value="<?=$Fecha?>">
						<input type="hidden" name="IDPuntoVenta" value="<?=$IDPuntoVenta?>">
						<input type="hidden" name="action" value="<?=$newmode?>">
						<input type="submit" name="submit" class="input" value="<?=$submit_caption?>">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
<?php
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$IDPuntoVenta;
	if(empty($sql))
	 	$sql =  " SELECT N.* FROM NovedadBanco N, PuntoVentaBanco PV 
	 				WHERE PV.IDPuntoVenta = '$IDPuntoVenta'
	 				AND PV.IDPuntoVentaBanco = N.IDPuntoVentaBanco ORDER BY Fecha DESC ";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=10;
   		$nav->execute($sql,$dblink);
		$total_records =  $nav->total_result;
		$rows = $nav->rows;
		$result = $nav->sql_result;
		$row = $offset;
		$startrow = $offset + 1;
		$finalrow = ($row * $nav->limit) + $rows;
	
	 	$pages = $nav->show_num_pages('&laquo;','&laquo; prev','&raquo;','next &raquo;','|','class=navvar');   // show pages
		
		$info = $nav->show_info(); 
?>
<br>
<form name="frm1" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data">
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="550">
	
	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				Crear Novedad
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table width=550 border=0 cellspacing=1 align="center" cellpadding=0  class="forumline" >
	
	<tr>
		<td colspan="2" >
			<table width=100% border=0 cellspacing=1 cellpadding=0  class="forumline">
						<tr>
						<td class="rowtable">
							Fecha
						</td>
						<td class="row1">
						
							<input type="text" class="input" name="Fecha" size="19" value='<?=fecha()?>' readonly>
							<script language="JavaScript1.2">
								<!--
									if (!document.layers)
										document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm1.Fecha,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
								//-->
							</script>
						
						</td>
				</tr>
						<tr>
					<td class="row1" colspan="2" align="center"><input type="hidden" value="<?=$IDPuntoVenta?>" name="IDPuntoVenta"><input type="hidden" name="action" value="add">
						<input type="submit" class="input" name="submit" value="Crear Novedad">
					</td>
				</tr>
			</table>
		</tr>
	</td>
</table>
</form>

<?php
	if($rows > 0){
?>		
<br>

<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="550">
	
	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				Novedades - <?php echo $info;;?>
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table width=550 border=0 cellspacing=1 align="center" cellpadding=0  class="forumline" >
	<tr>
		<td colspan="2" >
			<?php filtrar();?>	
		</td>
	</tr>
	<tr>
		<td colspan="2" >
			<table width=100% border=0 cellspacing=1 cellpadding=0  class="forumline">
				<tr>
					<td>
						<table width=100% border=0 cellspacing=1 cellpadding=0>
							<tr>
									<td class=rowform nowrap bgcolor=#DBEAF5>Punto de Venta</td>
									<td class=rowform nowrap bgcolor=#DBEAF5>Banco</td>
								<td class=rowform nowrap bgcolor=#DBEAF5>Fecha</td>
									<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Valor</td>
								</tr>
							<?php while($r = db_fetch_object($result)){
							?>
			  				<tr>
									<td nowrap class=row1><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",get_field("PuntoVentaBanco","IDPuntoVenta","IDPuntoVentaBanco",$r->IDPuntoVentaBanco))?></td>
									<td nowrap class=row1><?php echo get_field("Banco","Nombre","IDBanco",get_field("PuntoVentaBanco","IDBanco","IDPuntoVentaBanco",$r->IDPuntoVentaBanco))?></td>
								<td nowrap class=row1><?php echo formatofecha( $r->Fecha )?></td>
									<td align=center valign=middle nowrap width=60 class=row2><?php echo "$ ".number_format($r->Valor) ?>&nbsp;&nbsp;</td>
								</tr>
							<?php } // END for
							?>
							<tr>
								<td class=texto bgcolor=#DBEAF5 colspan=4 nowrap>
									<?php
										print $pages;
										?>
								</td>
							</tr>		
						</table>
					</td>
				</tr>
			</table>
		</td>
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
	<form name="frm" action="./" method="get" onsubmit="return valbuscar(document.frm)">
		<tr>
			<td class="rowform" align="center" colspan=8>
				<select name="field" id="Buscar por" class="popup">
					<option value="">Buscar Por</option>
					<option value="FechaInicio">Fecha Publicacion</option>
					<option value="Nombre">Nombre</option>
					<option value="Contacto">Contacto</option>
					<option value="Lugar">Lugar</option>
					<option value="Hora">Hora</option>
				</select> 
				<input type="text" size="20" name="QryString" id="Buscar Por" class="post"> 
				Entre <input type=text readonly size=10 class=input name=limit1>
				<script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit1,\"yyyy-mm-dd\")' width=16 height=16 border=0>")	
					//-->
				</script>
				 y <input type=text size=10 readonly class=input name=limit2> 
				<script language='JavaScript1.2'>
					<!--
						if (!document.layers)
							document.write("<img src=admin/jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
					//-->
				</script>
				<br>
				ordenar por 
				<select name="order_by" class="popup">
					<option value="FechaInicio">Fecha Publicacion</option>
					<option value="Descripcion">Descripcion</option>
					<option value="Contacto">Contacto</option>
					<option value="Lugar">Lugar</option>
					<option value="Hora">Hora</option>
				</select> 
				de forma 
				<select name="in_order" class="popup">
					<option value="ASC">Ascendente</option>
					<option value="DESC">Descendente</option>
				</select>
				Listar 
				<select name="listar" class="popup">
					<option value="10">10</option>
					<option value="15">15</option>
					<option value="20">20</option>
					<option value="25">25</option>
					<option value="30">30</option>
				</select> 
				<br>
				<input type="hidden" name="mod" value="<?=$MOD?>">
				<input type="hidden" name="rangofield" value="Fecha">
				<input type="hidden" name="action" value="list">
				<input type="hidden" name="tjoin" value="Gerencia">
				<input type="submit" name="submit" value="Buscar" class="submit">
			</td>
		</tr>
	</form>
<?php
	}//End function filtrar
?>
