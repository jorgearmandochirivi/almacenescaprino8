<script src="jscripts/Chart.js-master/Chart.js"></script>
<body> 

<?

$TitleMod ="Reportes Garantia";

$Table = "Garantia";
$TableJoin = "";
$Key = "IDGarantia";
$MOD = "GarantiaReporte";
$m = "Garantia";
?>

 <?

$permisos = get_permiso($ID_Usuario,$m,$Table);


		
		
if($permisos[0] >= 2)
{
	
	if (empty($action))
		$action="list";
		
		switch (nvl($action)) {
			case "list" :	
			
				
					
				if(!empty($_GET[IDGarantia]))
					$condiciones.=" and G.IDGarantia = '".$_GET[IDGarantia]."'";
	
				if(!empty($_GET[TipoRegistro]))
					$condiciones.=" and G.TipoRegistro = '".$_GET[TipoRegistro]."'";
					
				if(!empty($_GET[IDEstadoGarantia]))
					$condiciones.=" and G.IDEstadoGarantia = '".$_GET[IDEstadoGarantia]."'";
					
				if(!empty($_GET[IDPuntoVenta]))
					$condiciones.=" and G.IDPuntoVenta = '".$_GET[IDPuntoVenta]."'";
					
				if(!empty($_GET[CantidadVeces]))
					$condiciones.=" and G.CantidadVeces = '".$_GET[CantidadVeces]."'";
					
				if(!empty($_GET[TipoProducto]))
					$condiciones.=" and G.TipoProducto = '".$_GET[TipoProducto]."'";
					
				if(!empty($_GET[Alerta])):
					switch($_GET[Alerta]):
						case "V":
						  $condiciones .= " and G.IDEstadoGarantia not in (9,8,10)";
						  $condiciones .= " and FechaEstimadaEntrega < CURDATE()";						
						break;
						case "PV":
						  $condiciones .= " and G.IDEstadoGarantia not in (9,8,10)";
						  $condiciones .= " and FechaEstimadaEntrega BETWEEN CURDATE() and DATE_ADD( CURDATE() , INTERVAL 5 DAY )";
						break;
						case "NC":
							$condiciones .= " and G.RequiereNotaCredito='S' and G.NumeroNotaCredito=''";
						break;
						case "PF":
							$condiciones.=" and G.IDEstadoGarantia = '11'";
						break;
					endswitch;
					
				endif;	

				if (!empty($_GET[limit1]) && !empty($_GET[limit2]))
					$condiciones.=" and G.FechaTrCr between '".$_GET[limit1]."' and '".$_GET[limit2]."'";

				
				
				
				if (!empty($_GET[TipoContrafuerte]))
					$condiciones.=" and G.TipoContrafuerte = 'S'";

				if (!empty($_GET[TipoCuero]))
					$condiciones.=" and G.TipoCuero = 'S'";
					
				if (!empty($_GET[TipoPlantilla]))
					$condiciones.=" and G.TipoPlantilla = 'S'";
					
				if (!empty($_GET[TipoCremallera]))
					$condiciones.=" and G.TipoCremallera = 'S'";
					
				if (!empty($_GET[TipoDespegue]))
					$condiciones.=" and G.TipoDespegue = 'S'";
					
				if (!empty($_GET[TipoCambrion]))
					$condiciones.=" and G.TipoCambrion = 'S'";
					
				if (!empty($_GET[TipoTacon]))
					$condiciones.=" and G.TipoTacon = 'S'";
					
				if (!empty($_GET[TipoCerco]))
					$condiciones.=" and G.TipoCerco = 'S'";
					
				if (!empty($_GET[TipoCardado]))
					$condiciones.=" and G.TipoCardado = 'S'";
					
				if (!empty($_GET[TipoSuela]))
					$condiciones.=" and G.TipoSuela = 'S'";
					
				if (!empty($_GET[TipoGuarnicion]))
					$condiciones.=" and G.TipoGuarnicion = 'S'";
					
				if (!empty($_GET[TipoPuntera]))
					$condiciones.=" and G.TipoPuntera = 'S'";
					
				if (!empty($_GET[TipoHerraje]))				
					$condiciones.=" and G.TipoHerraje = 'S'";


					
			//CON FACTURA	
			$sql_anterior = " SELECT G.*, C.*, EG.Nombre, G.FechaTrCr as FechaGarantia
							 FROM Garantia G, EstadoGarantia EG,  Cliente C, Factura F
							 WHERE G.IDFactura = F.IDFactura and C.IDCliente = F.IDCliente and
							 	   EG.IDEstadoGarantia = G.IDEstadoGarantia
							 	   $condiciones
							ORDER BY IDGarantia DESC";
							
			 $sql = " SELECT G.*, EG.Nombre, G.FechaTrCr as FechaGarantia
							 FROM Garantia G, EstadoGarantia EG
							 WHERE EG.IDEstadoGarantia = G.IDEstadoGarantia
							 	   $condiciones
							ORDER BY IDGarantia DESC";
							

			
			//consulto totales para el resumen
			$result_garantia=db_query($sql);
			
			$totales[contador_garantia]=0;
			$totales[promedio_fabrica]=0;
			$totales[promedio_entrega_cliente]=0;
			
			while($row_garantia=db_fetch_array($result_garantia)){
				$totales[contador_garantia]++;
				$fecha_ingreso_garantia = substr($r->FechaTrCr,0,10);

				//Fecha Recibido en fabrica
				$sql_recibido_fabrica = "Select * From ComentarioGarantia Where IDGarantia = '".$row_garantia[IDGarantia]."' and IDEstadoGarantia in (5, 12)";
				$result_recibido_fabrica = db_query($sql_recibido_fabrica);
				$row_recibido_fabrica = db_fetch_array($result_recibido_fabrica);
				
				//Fecha Enviado Almacen
				$sql_enviada_tienda = "Select * From ComentarioGarantia Where IDGarantia = '".$row_garantia[IDGarantia]."' and IDEstadoGarantia in (7)";
				$result_enviada_tienda = db_query($sql_enviada_tienda);
				$row_enviada_tienda = db_fetch_array($result_enviada_tienda);
				
				//Fecha Entrega Cliente
				$sql_entrega_cliente = "Select * From ComentarioGarantia Where IDGarantia = '".$row_garantia[IDGarantia]."' and IDEstadoGarantia in (9)";
				$result_entrega_cliente = db_query($sql_entrega_cliente);
				$row_entrega_cliente = db_fetch_array($result_entrega_cliente);
				
				
				// Calculo de dias
				
				$fecha_inicio_fabrica = substr($row_recibido_fabrica[FechaComentario],0,10);
				$fecha_fin_fabrica = substr($row_enviada_tienda[FechaComentario],0,10);
				
				if(!empty($fecha_inicio_fabrica) && !empty($fecha_fin_fabrica)):
					$datetime1 = new DateTime($fecha_inicio_fabrica);
					$datetime2 = new DateTime($fecha_fin_fabrica);
					
					$interval = $datetime1->diff($datetime2);
					$array_tiempo_fabrica[] = $interval->format('%a');
				endif;	

				$fecha_inicio = substr($row_garantia[FechaTrCr],0,10);
				$fecha_fin = substr($row_entrega_cliente[FechaComentario],0,10);
				
				if(!empty($fecha_inicio) && !empty($fecha_fin)):
					$datetime1 = new DateTime($fecha_inicio);
					$datetime2 = new DateTime($fecha_fin);
					
					$interval = $datetime1->diff($datetime2);
					$array_tiempo_entrega[] = $interval->format('%a');
				endif;	
					
			}
			
				//Promedio Fabrica
				foreach($array_tiempo_fabrica as $valor):
					$suma_dias_fabrica += $valor;
				endforeach;
				$promedio_fabrica = (int) ($suma_dias_fabrica / count($array_tiempo_fabrica));
				
				//Promedio Entrega Cliente
				foreach($array_tiempo_entrega as $valor):
					$suma_dias_entrega += $valor;
				endforeach;
				$promedio_entrega = (int) ($suma_dias_entrega / count($array_tiempo_entrega));
	
				
				
				
				$totales[promedio_fabrica]=$promedio_fabrica;
				$totales[promedio_entrega_cliente]=$promedio_entrega;
	
			
			
			list_r($sql,$totales);
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

	GLOBAL $TitleMod,$Table,$MOD,$Key,$ID_Usuario;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' Order by IDGarantia DESC");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('Nombre','Publicar');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
			<td>&nbsp;</td>
		</tr>
</table>
<br>
</form>
<?
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql="",$totales){
		Global $TitleMod,$MOD,$Table,$Key,$listar;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table ORDER BY $Key DESC";
	 	

		
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=50;
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
		<a href="./?mod=<%=$MOD%>">Administrar <% echo $TitleMod%></a> </td>
		<td>&nbsp;</td>
	</tr>
</table>
<?
		if($rows > 0){
?>		
<br>
<table width=500 cellpadding=0 cellspacing=0 align=center >
	<tr>
		<td class=titlemedium bgcolor=#9daac6><b>Listar <? echo $TitleMod ?></b></td>
	</tr>
	<?filtrar();?>	
	<tr>
		<td class=titlemedium  bgcolor=#9daac6><% echo $info;%></td>
	</tr>
	<tr>
	  <td class=texto  colspan= nowrap>
      <table width="100%" border="0" cellpadding="1" cellspacing="2">
        <tr>
          <td colspan="2" bgcolor=#DBEAF5>INFORME DE TIEMPO DE GARANTIAS</td>
          </tr>
	      <tr>
	        <td width="40%">Dias Promedio en Fabrica</td>
	        <td width="60%">&nbsp;<?php  echo $totales[promedio_fabrica]; ?></td>
        </tr>
	      <tr>
	        <td>Dias Promedio entrega al cliente (tiempo total)</td>
	        <td>&nbsp;<?php echo $totales[promedio_entrega_cliente];	 ?></td>
        </tr>
	      <tr>
	        <td colspan="2"><hr></td>
        </tr>
      </table></td>
  </tr>
	<tr>
	  <td><table width=100% border=0 cellspacing=4 cellpadding=0>
	    <tr>
	      <td colspan="8" align=left valign=middle class=rowform><a href="Garantia/exportagarantias_tiempo.php?sql=<?php echo $sql; ?>"><img src="../images/excel_icon.gif" alt="" width="20" height="20" border="0" >Exportar Registros </a></td>
        </tr>
	    <tr>
	      <td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
	      <td class=rowform nowrap bgcolor=#DBEAF5> Numero&nbsp;
	        <% if($_GET['order_by']=="Nombre"){%>
	        <img src="images/<%=$img%>" border=0>
	        <%}%></td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Fecha Ingreso<a style="color: #3A4F6C;text-decoration: none" href="<% echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; %>">&nbsp; </a></td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Fecha Recibido en fabrica</td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Fecha enviado a almacenes</td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Fecha Entrega
al	cliente        
  <% if($_GET['order_by']=="Codigo"){%>
  <%}%></td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Total Dias Fabrica</td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Total Dias Entrega</td>
        </tr>
	    <? while($r = db_fetch_object($result)){
	$tallap="";
	$id_referencia_item="";
?>
	    <tr>
	      <td align=center valign=middle nowrap width=69 class=row2>&nbsp;<a href='<? echo "?mod=Garantia&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a></td>
	      <td nowrap class="<?=$class?>"><? echo $r->IDGarantia; ?></td>
	      <td nowrap class="<?=$class?>"><? echo substr($r->FechaTrCr,0,10); ?></td>
	      <td nowrap class="<?=$class?>">
		  <?php  
		  $sql_recibido_fabrica = "Select * From ComentarioGarantia Where IDGarantia = '".$r->IDGarantia."' and IDEstadoGarantia in (5, 12)";
		  $result_recibido_fabrica = db_query($sql_recibido_fabrica);
		  $row_recibido_fabrica = db_fetch_array($result_recibido_fabrica);
		  echo substr($row_recibido_fabrica[FechaComentario],0,10);
		  ?>
          
          </td>
	      <td nowrap class="<?=$class?>">
	     <?php  
		  $sql_enviada_tienda = "Select * From ComentarioGarantia Where IDGarantia = '".$r->IDGarantia."' and IDEstadoGarantia in (7)";
		  $result_enviada_tienda = db_query($sql_enviada_tienda);
		  $row_enviada_tienda = db_fetch_array($result_enviada_tienda);
		  echo substr($row_enviada_tienda[FechaComentario],0,10);
		  ?>
          
          </td>
	      <td nowrap class="<?=$class?>"><?php  
		  $sql_entrega_cliente = "Select * From ComentarioGarantia Where IDGarantia = '".$r->IDGarantia."' and IDEstadoGarantia in (9)";
		  $result_entrega_cliente = db_query($sql_entrega_cliente);
		  $row_entrega_cliente = db_fetch_array($result_entrega_cliente);
		  echo substr($row_entrega_cliente[FechaComentario],0,10);
		  ?></td>
	      <td nowrap class="<?=$class?>" align="center"><?php
				
				
				$fecha_inicio_fabrica = substr($row_recibido_fabrica[FechaComentario],0,10);
				$fecha_fin_fabrica = substr($row_enviada_tienda[FechaComentario],0,10);
				
				if(!empty($fecha_inicio_fabrica) && !empty($fecha_fin_fabrica)):
					$datetime1 = new DateTime($fecha_inicio_fabrica);
					$datetime2 = new DateTime($fecha_fin_fabrica);
					
					$interval = $datetime1->diff($datetime2);
					echo $interval->format('%a');
				endif;	
				
				
				?></td>
	      <td nowrap class="<?=$class?>" align="center"><?php
				
				
				$fecha_inicio = substr($r->FechaTrCr,0,10);
				$fecha_fin = substr($row_entrega_cliente[FechaComentario],0,10);
				
				if(!empty($fecha_inicio) && !empty($fecha_fin)):
					$datetime1 = new DateTime($fecha_inicio);
					$datetime2 = new DateTime($fecha_fin);
					
					$interval = $datetime1->diff($datetime2);
					echo $interval->format('%a');
				endif;	
				
				
				?></td>
        </tr>
	    <? } // END for
?>
	    <tr>
	      <td class=texto bgcolor=#DBEAF5 colspan=8 nowrap><?
		print $pages;
		?></td>
        </tr>
      </table></td>
</tr>
</table>	

<? 			
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
				<td class="rowform" align="center" colspan=8><p><span class="col2">
				  
				  
			    Numero Garantia	 
			    <input type="text" name="IDGarantia" id="IDGarantia">
			    
			    Tipo de Proceso :
<select name="TipoRegistro" id="TipoRegistro">
    <option value=""></option>
    <option value="Garantia">Garantia</option>
    <option value="Reproceso">Reproceso</option>
    <option value="Servicio">Servicio</option>
  </select>
			    Estado:
<select name="IDEstadoGarantia" id="IDEstadoGarantia">
    <option value="">[Seleccione]</option>
    <?php 
                    $sql_estados=db_query("Select * from EstadoGarantia Where 1 Order by Nombre");
                    while($row_estado=db_fetch_array($sql_estados)){
                        ?>
    <option value="<?php echo $row_estado["IDEstadoGarantia"]; ?>"><?php echo $row_estado["Nombre"]; ?></option>
    <?php
                    }
                    ?>
  </select>
  <br>
			    Punto Venta Registra
<select name="IDPuntoVenta" id="IDPuntoVenta">
    <option value="">[Seleccione]</option>
    <?php 
                    $sql_vta=db_query("Select * from PuntoVenta Where 1 Order by Nombre");
                    while($row_vta=db_fetch_array($sql_vta)){
                        ?>
    <option value="<?php echo $row_vta["IDPuntoVenta"]; ?>"><?php echo $row_vta["Nombre"]; ?></option>
    <?php
                    }
                    ?>
  </select>
			    Garantias por
<select name="CantidadVeces" id="CantidadVeces">
    <option value=""></option>
    <option value="1">Primera Vez </option>
    <option value="2">Segunda Vez</option>
    <option value="3">Tercera Vez</option>
  </select>
			    Clasificacion
<select name="TipoProducto" id="TipoProducto">
    <option value=""></option>
    <option value="C">Producto Caprino</option>
    <option value="T">Producto Tercero</option>
  </select>
			    Alerta
<select name="Alerta" id="Alerta">
  <option value=""></option>
  <option value="V">Vencidos</option>
  <option value="PV">Proximo a vencer</option>
  <option value="NC">Sin Nota Credito</option>
  <option value="PF">Pendiente Finalizar</option>
</select>
			    </span><br>Entre
<input type=text readonly size=10 class=input name=limit1>
			      <script language='JavaScript1.2'>
								<!--
								if (!document.layers)
								document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit1,\"yyyy-mm-dd\")' width=16 height=16 border=0>")	
								//-->
					</script>
			      y
			      <input type=text size=10 readonly class=input name=limit2>
			      <script language='JavaScript1.2'>
								<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
								//-->
					</script>
				    
				    
				    
				    
				    
			      </p>
				  <table width="100%"  cellpadding="2" cellspacing="3">
                    <tr>
                      <td colspan="6"><strong>CAUSA DE GARANTIA</strong></td>
                    </tr>
                    <tr>
                      <td class="row2">Contrafuerte</td>
                      <td class="row2"><input type="checkbox" name="TipoContrafuerte" id="TipoContrafuerte" value="S" <?php if ($_GET[TipoContrafuerte]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpContrafuerte" id="tmpContrafuerte" value="<?php echo $r->TipoContrafuerte; ?>"></td>
                      <td >Despegue</td>
                      <td ><input type="checkbox" name="TipoDespegue" id="TipoDespegue" value="S" <?php if ($_GET[TipoDespegue]=="S"){ echo "checked"; } ?>  />
                        <span class="row2">
                          <input type="hidden" name="tmpTipoDespegue" id="tmpTipoDespegue" value="<?php echo $r->TipoDespegue; ?>">
                        </span></td>
                      <td class="row2">Cardado</td>
                      <td class="row2"><input type="checkbox" name="TipoCardado" id="TipoCardado" value="S" <?php if ($_GET[TipoCardado]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoCardado" id="tmpTipoCardado" value="<?php echo $r->TipoCardado; ?>"></td>
                    </tr>
                    <tr>
                      <td class="row2">Cuero</td>
                      <td class="row2"><input type="checkbox" name="TipoCuero" id="TipoCuero" value="S" <?php if ($_GET[TipoCuero]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoCuero" id="tmpTipoCuero" value="<?php echo $r->TipoCuero; ?>"></td>
                      <td >Cambrion</td>
                      <td><input type="checkbox" name="TipoCambrion" id="TipoCambrion" value="S" <?php if ($_GET[TipoCambrion]=="S"){ echo "checked"; } ?>  />
                        <span class="row2">
                          <input type="hidden" name="tmpTipoCambrion" id="tmpTipoCambrion" value="<?php echo $r->TipoCambrion; ?>">
                        </span></td>
                      <td class="row2">Suela Rota</td>
                      <td class="row2"><input type="checkbox" name="TipoSuela" id="TipoSuela" value="S" <?php if ($_GET[TipoSuela]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoSuela" id="tmpTipoSuela" value="<?php echo $r->TipoRemonta; ?>"></td>
                    </tr>
                    <tr>
                      <td class="row2">Plantilla estructural</td>
                      <td class="row2"><input type="checkbox" name="TipoPlantilla" id="TipoPlantilla" value="S" <?php if ($_GET[TipoPlantilla]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoPlantilla" id="tmpTipoPlantilla" value="<?php echo $r->TipoPlantilla; ?>"></td>
                      <td >Tacon</td>
                      <td ><span class="row2">
                        <input type="checkbox" name="TipoTacon" id="TipoTacon" value="S" <?php if ($_GET[TipoTacon]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoTacon" id="tmpTipoTacon" value="<?php echo $r->TipoTacon; ?>">
                      </span></td>
                      <td class="row2">Guarnicion</td>
                      <td class="row2"><span class="row2">
                        <input type="checkbox" name="TipoGuarnicion" id="TipoGuarnicion" value="S" <?php if ($_GET[TipoGuarnicion]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoGuarnicion" id="tmpTipoGuarnicion" value="<?php echo $r->TipoGuarnicion; ?>">
                      </span></td>
                    </tr>
                    <tr>
                      <td height="27" class="row2">Cremallera</td>
                      <td class="row2"><input type="checkbox" name="TipoCremallera" id="TipoCremallera" value="S" <?php if ($_GET[TipoCremallera]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoCremallera" id="tmpTipoCremallera" value="<?php echo $r->TipoCremallera; ?>"></td>
                      <td >Cerco</td>
                      <td><input type="checkbox" name="TipoCerco" id="TipoCerco" value="S" <?php if ($_GET[TipoCerco]=="S"){ echo "checked"; } ?>  />
                        <span class="row2">
                          <input type="hidden" name="tmpTipoCerco" id="tmpTipoCerco" value="<?php echo $r->TipoCerco; ?>">
                        </span></td>
                      <td class="row2">Puntera</td>
                      <td class="row2"><span class="row2">
                        <input type="checkbox" name="TipoPuntera" id="TipoPuntera" value="S" <?php if ($_GET[TipoPuntera]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoPuntera" id="tmpTipoPuntera" value="<?php echo $r->TipoPuntera; ?>">
                      </span></td>
                    </tr>
                    <tr>
                      <td class="row2">Herraje</td>
                      <td class="row2"><input type="checkbox" name="TipoHerraje" id="TipoHerraje" value="S" <?php if ($_GET[TipoHerraje]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoHerraje" id="tmpTipoHerraje" value="<?php echo $r->TipoHerraje; ?>"></td>
                      <td class="row2">&nbsp;</td>
                      <td class="row2">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                  </table>                
                
                
                
                
                
                
                
                <br>
					<input type="hidden" name="mod" value="<?=$MOD?>">
					
					<input type="hidden" name="action" value="list">
					
					<input type="submit" name="submit" value="Buscar" class="submit">
	  </td>
	  </tr>
	</form>
<?		
	}//End function filtrar
?>
