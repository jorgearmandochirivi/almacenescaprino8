


<script src="jscripts/Chart.js-master/Chart.js"></script>
<body> 

<?php 

$TitleMod ="Reportes Garantia";

$Table = "Garantia";
$TableJoin = "";
$Key = "IDGarantia";
$MOD = "GarantiaReporte";
$m = "Garantia";
?>

 <?php 

$permisos = get_permiso($ID_Usuario,$m,$Table);


		
		
if($permisos[0] >= 2)
{
	
	if (empty($action))
		$action="list";
		
		switch (nvl($action)) {
			case "list" :	
			
				
					
			if(!empty($_GET['IDGarantia']))
					$condiciones.=" and G.IDGarantia = '".$_GET['IDGarantia']."'";
	
			if(!empty($_GET['TipoRegistro']))
					$condiciones.=" and G.TipoRegistro = '".$_GET['TipoRegistro']."'";
					
			if(!empty($_GET['IDEstadoGarantia']))
					$condiciones.=" and G.IDEstadoGarantia = '".$_GET['IDEstadoGarantia']."'";
					
			if(!empty($_GET['IDPuntoVenta']))
					$condiciones.=" and G.IDPuntoVenta = '".$_GET['IDPuntoVenta']."'";
					
			if(!empty($_GET['CantidadVeces']))
					$condiciones.=" and G.CantidadVeces = '".$_GET['CantidadVeces']."'";
					
			if(!empty($_GET['TipoProducto']))
					$condiciones.=" and G.TipoProducto = '".$_GET['TipoProducto']."'";
					
			if(!empty($_GET['Alerta'])):
					switch($_GET['Alerta']):
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

				if (!empty($_GET['limit1']) && !empty($_GET['limit2']))
					$condiciones.=" and G.FechaTrCr between '".$_GET['limit1']."' and '".$_GET['limit2']."'";

				
				
				
				if (!empty($_GET['TipoContrafuerte']))
					$condiciones.=" and G.TipoContrafuerte = 'S'";

				if (!empty($_GET['TipoCuero']))
					$condiciones.=" and G.TipoCuero = 'S'";
					
				if (!empty($_GET['TipoPlantilla']))
					$condiciones.=" and G.TipoPlantilla = 'S'";
					
				if (!empty($_GET['TipoCremallera']))
					$condiciones.=" and G.TipoCremallera = 'S'";
					
				if (!empty($_GET['TipoDespegue']))
					$condiciones.=" and G.TipoDespegue = 'S'";
					
				if (!empty($_GET['TipoCambrion']))
					$condiciones.=" and G.TipoCambrion = 'S'";
					
				if (!empty($_GET['TipoTacon']))
					$condiciones.=" and G.TipoTacon = 'S'";
					
				if (!empty($_GET['TipoCerco']))
					$condiciones.=" and G.TipoCerco = 'S'";
					
				if (!empty($_GET['TipoCardado']))
					$condiciones.=" and G.TipoCardado = 'S'";
					
				if (!empty($_GET['TipoSuela']))
					$condiciones.=" and G.TipoSuela = 'S'";
					
				if (!empty($_GET['TipoGuarnicion']))
					$condiciones.=" and G.TipoGuarnicion = 'S'";
					
if (!empty($_GET['TipoPuntera']))
				$condiciones.=" and G.TipoPuntera = 'S'";
				
			if (!empty($_GET['TipoHerraje']))				
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
		$totales['contador_garantia']=0;
		$totales['contador_tipo_garantia']=0;
		$totales['contador_tipo_servicio']=0;
		$totales['contador_tipo_reproceso']=0;
		$totales['contador_remonta']=0;
		$totales['contador_autoriza_especial']=0;
		$totales['contador_par_nuevo']=0;
		$totales['contador_no_aceptado']=0;
		
		$totales['contador_una_vez']=0;
		$totales['contador_segunda_vez']=0;
		$totales['contador_tercera_vez']=0;
		
		$totales['contador_contrafuerte']=0;
		$totales['contador_cuero']=0;
		$totales['contador_plantilla']=0;
		$totales['contador_cremallera']=0;
		$totales['contador_despegue']=0;
		$totales['contador_cambrion']=0;
		$totales['contador_tacon']=0;
		$totales['contador_cerco']=0;
		$totales['contador_cardado']=0;
		$totales['contador_suela_rota']=0;
		$totales['contador_guarnicion']=0;
		$totales['contador_puntera']=0;
		$totales['contador_herraje']=0;
		$totales['contador_otros']=0;
		while($row_garantia=db_fetch_array($result_garantia)){
			$totales['contador_garantia']++;
			if ($row_garantia['TipoRegistro']=="Garantia")
				$totales['contador_tipo_garantia']++;
			elseif($row_garantia['TipoRegistro']=="Servicio")	
				$totales['contador_tipo_servicio']++;	
			elseif($row_garantia['TipoRegistro']=="Reproceso")	
				$totales['contador_tipo_reproceso']++;
			
			
			if ($row_garantia['Remonta']=="S")	
				$totales['contador_remonta']++;
			if ($row_garantia['IDTipoFinalizacionGarantia']!="0")
				$totales['contador_autoriza_especial']++;
			if ($row_garantia['IDTipoFinalizacionGarantia']=="1")	
				$totales['contador_par_nuevo']++;
			if ($row_garantia['IDTipoFinalizacionGarantia']=="3")
				$totales['contador_no_aceptado']++;
			if ($row_garantia['CantidadVeces']=="1")	
				$totales['contador_una_vez']++;
			if ($row_garantia['CantidadVeces']=="2")	
				$totales['contador_segunda_vez']++;				
			if ($row_garantia['CantidadVeces']=="3")
				$totales['contador_tercera_vez']++;
				
			
			
			if ($row_garantia['TipoContrafuerte']=="S")	
				$totales['contador_contrafuerte']++;
			if ($row_garantia['TipoCuero']=="S")	
				$totales['contador_cuero']++;
			if ($row_garantia['TipoPlantilla']=="S")	
				$totales['contador_plantilla']++;
			if ($row_garantia['TipoCremallera']=="S")	
				$totales['contador_cremallera']++;
			if ($row_garantia['TipoDespegue']=="S")	
				$totales['contador_despegue']++;
			if ($row_garantia['TipoCambrion']=="S")	
				$totales['contador_cambrion']++;
			if ($row_garantia['TipoTacon']=="S")	
				$totales['contador_tacon']++;
			if ($row_garantia['TipoOtro']!="")	
				$totales['contador_otros']++;
			if ($row_garantia['TipoCerco']=="S")	
				$totales['contador_cerco']++;
			if ($row_garantia['TipoCardado']=="S")	
				$totales['contador_cardado']++;
			if ($row_garantia['TipoSuela']=="S")	
				$totales['contador_suela_rota']++;
			if ($row_garantia['TipoGuarnicion']=="S")	
				$totales['contador_guarnicion']++;
			if ($row_garantia['TipoPuntera']=="S")	
				$totales['contador_puntera']++;
			if ($row_garantia['TipoHerraje']=="S")	
				$totales['contador_herraje']++;
					
					
			}
			
			
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
			<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
			<td>&nbsp;</td>
		</tr>
</table>
<br>
</form>
<?php 
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
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td>&nbsp;</td>
	</tr>
</table>
<?php 
		if($rows > 0){
?>		
<br>
<table width=500 cellpadding=0 cellspacing=0 align=center >
	<tr>
		<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
	</tr>
	<?php filtrar();?>	
	<tr>
		<td class=titlemedium  bgcolor=#9daac6><?php  echo $info;?></td>
	</tr>
	<tr>
	  <td class=texto  colspan= nowrap>
      <table width="100%" border="0" cellpadding="1" cellspacing="2">
        <tr>
	        <td colspan="4">RESUMEN</td>
          </tr>
	      <tr>
	        <td colspan="4" bgcolor=#DBEAF5>ITEM</td>
          </tr>
	      <tr>
	        <td>Cantidad Garantias</td>
	        <td>&nbsp;<?php echo $totales['contador_tipo_garantia']; ?></td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
        </tr>
	      <tr>
	        <td>Cantidad Servicios</td>
	        <td>&nbsp;<?php echo $totales['contador_tipo_servicio']; ?></td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
        </tr>
	      <tr>
	        <td>Cantidad Reprocesos</td>
	        <td>&nbsp;<?php echo $totales['contador_tipo_reproceso']; ?></td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
        </tr>
	      <tr>
	        <td><strong>TOTAL PROCESOS</strong></td>
	        <td>&nbsp;<strong><?php echo $totales['contador_garantia']; ?></strong> </td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
        </tr>
	      <tr>
	        <td colspan="4"><hr></td>
        </tr>
	      <tr>
	        <td>Cantidad Remontas</td>
	        <td>&nbsp;<?php echo $totales['contador_remonta']; ?></td>
	        <td>Pares una vez</td>
	        <td>&nbsp;<?php echo $totales['contador_una_vez']; ?></td>
        </tr>
	      <tr>
	        <td>Autorizaciones especiales</td>
	        <td>&nbsp;<?php echo $totales['contador_autoriza_especial']; ?></td>
	        <td>Pares segunda vez</td>
	        <td>&nbsp;<?php echo $totales['contador_segunda_vez']; ?></td>
        </tr>
	      <tr>
	        <td>Pares nuevos</td>
	        <td>&nbsp;<?php echo $totales['contador_par_nuevo']; ?></td>
	        <td>Pares tercera vez</td>
	        <td>&nbsp;<?php echo $totales['contador_tercera_vez']; ?></td>
        </tr>
	      <tr>
	        <td>No aceptadas</td>
	        <td>&nbsp;<?php echo $totales['contador_no_aceptado']; ?></td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
        </tr>
	      <tr>
	        <td colspan="4" bgcolor=#DBEAF5>CAUSA GARANTIA</td>
          </tr>
	      <tr>
	        <td>Contrafuerte</td>
	        <td>&nbsp;<?php echo $totales['contador_contrafuerte']; ?></td>
	        <td>Tacon</td>
	        <td>&nbsp;<?php echo $totales['contador_tacon']; ?></td>
          </tr>
	      <tr>
	        <td>Cuero</td>
	        <td>&nbsp;<?php echo $totales['contador_cuero']; ?></td>
	        <td>Cerco</td>
	        <td>&nbsp;<?php echo $totales['contador_cerco']; ?></td>
          </tr>
	      <tr>
	        <td>Plantilla estructural</td>
	        <td>&nbsp;<?php echo $totales['contador_plantilla']; ?></td>
	        <td>Cardado</td>
	        <td>&nbsp;<?php echo $totales['contador_cardado']; ?></td>
          </tr>
	      <tr>
	        <td>Cremallera</td>
	        <td>&nbsp;<?php echo $totales['contador_cremallera']; ?></td>
	        <td>Suela rota</td>
	        <td>&nbsp;<?php echo $totales['contador_suela_rota']; ?></td>
          </tr>
	      <tr>
	        <td>Despegue</td>
	        <td>&nbsp;<?php echo $totales['contador_despegue']; ?></td>
	        <td>Guarnicion</td>
	        <td>&nbsp;<?php echo $totales['contador_guarnicion']; ?></td>
          </tr>
	      <tr>
	        <td>Cambrion</td>
	        <td>&nbsp;<?php echo $totales['contador_cambrion']; ?></td>
	        <td>Puntera</td>
	        <td>&nbsp;<?php echo $totales['contador_puntera']; ?></td>
          </tr>
	      <tr>
	        <td>Herraje</td>
	        <td>&nbsp;<?php echo $totales['contador_herraje']; ?></td>
	        <td>Otros</td>
	        <td>&nbsp;<?php echo $totales['contador_otros']; ?></td>
        </tr>
      </table></td>
  </tr>
	<tr>
	  <td class=texto  colspan= nowrap bgcolor=#DBEAF5>Graficas</td>
  </tr>
	<tr>
	  <td class=texto  colspan= nowrap>
		<div style="width: 50%; float:left">
        	<strong>CAUSA DE LA GARANTIA</strong>
			<canvas id="canvas" height="600" width="700"></canvas>
		</div>      
        
<script>
	var barChartData = {
		labels : ["Contrafuerte","Cuero","Plantilla Estruc.","Cremallera","Despegue","Cambrion","Tacon","Cerco","Cardado","Suela","Guarnicion","Puntera","Herraje","Otros"],
		datasets : [
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,0.8)",
				highlightFill: "rgba(220,220,220,0.75)",
				highlightStroke: "rgba(220,220,220,1)",
				data : [
					"<?php echo $totales['contador_contrafuerte']; ?>",
					"<?php echo $totales['contador_cuero']; ?>",
					"<?php echo $totales['contador_plantilla']; ?>",
					"<?php echo $totales['contador_cremallera']; ?>",
					"<?php echo $totales['contador_despegue']; ?>",
					"<?php echo $totales['contador_cambrion']; ?>",
					"<?php echo $totales['contador_tacon']; ?>",
					"<?php echo $totales['contador_cerco']; ?>",
					"<?php echo $totales['contador_cardado']; ?>",
					"<?php echo $totales['contador_suela_rota']; ?>",
					"<?php echo $totales['contador_guarnicion']; ?>",
					"<?php echo $totales['contador_puntera']; ?>",
					"<?php echo $totales['contador_herraje']; ?>",
					"<?php echo $totales['contador_otros']; ?>",
						]
			},
		]

	}

	</script>        

		<div id="canvas-holder" style="float:left">
        	<strong>TIPO</strong><br>
			<canvas id="chart-area" width="300" height="300"/>
		</div>

<script>

		var pieData = [
				{
				value: <?php echo $totales['contador_tipo_garantia']; ?>,
				color:"#F7464A",
				highlight: "#FF5A5E",
				label: "Garantia"
			},
			{
				value: <?php echo $totales['contador_tipo_servicio']; ?>,
				color: "#46BFBD",
				highlight: "#5AD3D1",
				label: "Servicios"
			},
			{
				value: <?php echo $totales['contador_tipo_reproceso']; ?>,
				

			];

				
				var ctx = document.getElementById("canvas").getContext("2d");
				window.myBar = new Chart(ctx).Bar(barChartData, {
					responsive : true
				});
				
				var ctx_pie = document.getElementById("chart-area").getContext("2d");
				window.myPie = new Chart(ctx_pie).Pie(pieData);
			
			
			
			
	</script>

      
      </td>
  </tr>
	<tr>
	  <td><table width=100% border=0 cellspacing=4 cellpadding=0>
	    <tr>
	      <td colspan="12" align=left valign=middle class=rowform><a href="Garantia/exportagarantias.php?sql=<?php echo $sql; ?>"><img src="../images/excel_icon.gif" alt="" width="20" height="20" border="0" >Exportar Registros </a></td>
        </tr>
	    <tr>
	      <td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
	      <td class=rowform nowrap bgcolor=#DBEAF5> Numero&nbsp;
	        <?php  if($_GET['order_by']=="Nombre"){?>
	        <img src="images/<?php echo $img?>" border=0>
	        <?php }?></td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Tipo<a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">&nbsp; </a></td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Clasif</td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Por:</td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Cliente
	        <?php  if($_GET['order_by']=="Codigo"){?>
	        <img src="images/<?php echo $img?>" border=0>
	        <?php }?></td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Ref</td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Talla</td>
	      <td class=rowform nowrap bgcolor=#DBEAF5>Tipo</td>
	      <td class=navpic nowrap bgcolor=#DBEAF5>Fecha</td>
	      <td class=navpic nowrap bgcolor=#DBEAF5>Estado</td>
	      <td class=navpic nowrap bgcolor=#DBEAF5>Almacen Reg. Garantia</td>
        </tr>
	    <?php while($r = db_fetch_object($result)){
	$tallap="";
	$id_referencia_item="";
?>
	    <tr>
	      <td align=center valign=middle nowrap width=69 class=row2>&nbsp;<a href='<?php echo "?mod=Garantia&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a></td>
	      <td nowrap class="<?php echo $class?>"><?php echo $r->IDGarantia; ?></td>
	      <td nowrap class="<?php echo $class?>"><?php echo $r->TipoRegistro; ?></td>
	      <td nowrap class="<?php echo $class?>"><?php  if($r->TipoProducto=="C") echo "Caprino"; elseif($r->TipoProducto=="T") echo "Tercero" ; ?></td>
	      <td nowrap class="<?php echo $class?>"><?php  if($r->CantidadVeces=="1") echo "Primera"; elseif($r->CantidadVeces=="2") echo "Segunda" ; else echo "Tercera"; ?>
	        Vez</td>
	      <td nowrap class="<?php echo $class?>"><?php 
						if(!empty($r->IDDetalleCambio)){
									  $array_cambio_detalle=explode("|",$r->IDDetalleCambio);	
									  $sql_datos_factura=db_query("Select * From Cambio Where IDCambio = '".$array_cambio_detalle[0]."'");
									  $r_factura=db_fetch_array($sql_datos_factura);				  										
									}
									else{
										
									  if ($r->TipoFactura=="facturabono"):	
										  $sql_datos_factura=db_query("Select * From FacturaBono Where IDFacturaBono = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
									  else:
									  	  $sql_datos_factura=db_query("Select * From Factura Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
									  endif;
									  $r_factura=db_fetch_array($sql_datos_factura);				  										
									}
									if ($r->TipoRegistro=="Reproceso"){
										$id_proveedor=get_field("Referencia","IDProveedor","IDReferencia",$r->IDReferencia);
										echo get_field("Proveedor","Nombre","IDProveedor",$id_proveedor);
									}
									elseif($r->Mayorista=="S"){
										echo $r->NombreMayorista;	
									}
									else{
										$id_cliente= $r_factura[IDCliente];
										echo get_field("Cliente","Nombre","IDCliente",$id_cliente)." ".get_field("Cliente","Apellido","IDCliente",$id_cliente);
									}
									?></td>
	      <td nowrap class="<?php echo $class?>"><?php
									
									if ($r->TipoRegistro=="Reproceso" || $r->Mayorista=="S" ){										
										echo get_field("Referencia","Numero","IDReferencia",$r->IDReferencia);
										$tallap=get_field("Talla","Descripcion","IDTalla",$r->IDTalla);
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$r->IDReferencia);;
										$tipop= get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
									}
									elseif(!empty($r->IDDetalleFacturaBono)){
										$array_bono_detalle=explode("|",$r->IDDetalleFacturaBono);
										if (count($array_bono_detalle)>0):
											$sql_bono=db_query("Select * From DetalleFacturaBono Where IDDetalleFacturaBono	 = '".$array_bono_detalle[1]."' and IDFacturaBono = '".$array_bono_detalle[0]."'");
											$r_bono=db_fetch_array($sql_bono);
											
											$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"])));
											$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
											$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
											echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
											$tipop=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
											$tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
										endif;

										
									}
	
									
									elseif(empty($r->IDDetalleCambio)){
										  $id_referencia_item="";
										  $id_punto_venta=$r->IDPuntoVentaFactura;
										  
										  if ($r->TipoFactura=="facturabono"):	
											  	$sql_producto="select * from DetalleFacturaBono Where IDDetalleFacturaBono='".$r->IDDetalleFactura."' and IDFacturaBono = '".$r->IDFactura."' and IDPuntoVenta = '".$id_punto_venta."'";
										  else:
											  $sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$id_punto_venta."'";
										  endif;
										  $qry_producto=db_query($sql_producto);
										  $r_detalle=db_fetch_object($qry_producto);
										  $id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
										  $tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica));
										  
										  
										if ($id_referencia_item==160){ // Cuando son excedentes consulto la referencia de la compra										
											$sql_facturabono=db_query("Select * from FacturaBono Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
											$r_facturabono=db_fetch_array($sql_facturabono);							
											if (!empty($r_facturabono[IDFacturaBono])){											
												$sql_detallefacturabono=db_query("Select * from DetalleFacturaBono Where IDFacturaBono = '".$r_facturabono[IDFacturaBono]."'");
												$r_detallefacturabono=db_fetch_array($sql_detallefacturabono);								
												$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));
												$tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"]));
											}
										  }									 
										  
										  
										  
										  $id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										  echo get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);										 
										  $tipop=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
									}
									else{
										$array_cambio_detalle=explode("|",$r->IDDetalleCambio);
										if (count($array_cambio_detalle)>0):
											$sql_cambio=db_query("Select * From DetalleCambio Where IDDetalleCambio = '".$array_cambio_detalle[1]."' and IDCambio = '".$array_cambio_detalle[0]."'");
											$r_cambio=db_fetch_array($sql_cambio);
											
											$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"])));
											$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"]));
											$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
											echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
											$tipop=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
										endif;
										
									}
									
									if($r->Mayorista=="S"):
										echo $r->ColorMayorista;
									endif;	
									
									?></td>
	      <td nowrap class="<?php echo $class?>"><?php 
							if ($tallap!="")
								echo $tallap;
							else	
								echo $tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$id_referencia_item));
							
							
							?></td>
	      <td nowrap class="<?php echo $class?>"><?php 
		  if($r->Mayorista=="S"):
			echo $r->TipoProductoMayorista;
		  else:
		  	echo $tipop; 
		endif;	
			?></td>
	      <td nowrap class="<?php echo $class?>"><?php echo formatofecha(substr($r->FechaTrCr,0,10)) ?></td>
	      <td nowrap class="<?php echo $class?>" style="color: #900; font-weight:bold"><?php 
							echo get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$r->IDEstadoGarantia); 
							
							if ($r->IDEstadoGarantia==10): // si es autorizacion especial consulto quien la dio								
								$sql_usuario_especial=db_query("Select * from ComentarioGarantia Where IDGarantia = '".$r->IDGarantia."' and IDEstadoGarantia = 10");
								$row_usuario_especial = db_fetch_array($sql_usuario_especial);
								echo "<br><font style='color: #000;'>Por:" . get_field("Empleado","Nombre","IDEmpleado",$row_usuario_especial[IDEmpleado]) . " " . get_field("Empleado","Apellidos","IDEmpleado",$row_usuario_especial[IDEmpleado])."</font>";
							endif;
							
							
							
							?></td>
	      <td nowrap class="<?php echo $class?>"><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta); ?></td>
        </tr>
	    <?php } // END for
?>
	    <tr>
	      <td class=texto bgcolor=#DBEAF5 colspan=12 nowrap><?php 
		print $pages;
		?></td>
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
                    $sql_vta=db_query("Select * from PuntoVenta Where Publicar = 'S' Order by Nombre");
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
			    </span><br>Entre:
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
                      <td class="row2"><input type="checkbox" name="TipoContrafuerte" id="TipoContrafuerte" value="S" <?php if ($_GET['TipoContrafuerte']=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpContrafuerte" id="tmpContrafuerte" value="<?php echo $r->TipoContrafuerte; ?>"></td>
                      <td >Despegue</td>
                      <td ><input type="checkbox" name="TipoDespegue" id="TipoDespegue" value="S" <?php if ($_GET['TipoDespegue']=="S"){ echo "checked"; } ?>  />
                        <span class="row2">
                          <input type="hidden" name="tmpTipoDespegue" id="tmpTipoDespegue" value="<?php echo $r->TipoDespegue; ?>">
                        </span></td>
                      <td class="row2">Cardado</td>
                      <td class="row2"><input type="checkbox" name="TipoCardado" id="TipoCardado" value="S" <?php if ($_GET['TipoCardado']=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoCardado" id="tmpTipoCardado" value="<?php echo $r->TipoCardado; ?>"></td>
                    </tr>
                    <tr>
                      <td class="row2">Cuero</td>
                      <td class="row2"><input type="checkbox" name="TipoCuero" id="TipoCuero" value="S" <?php if ($_GET['TipoCuero']=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoCuero" id="tmpTipoCuero" value="<?php echo $r->TipoCuero; ?>"></td>
                      <td >Cambrion</td>
                      <td><input type="checkbox" name="TipoCambrion" id="TipoCambrion" value="S" <?php if ($_GET['TipoCambrion']=="S"){ echo "checked"; } ?>  />
                        <span class="row2">
                          <input type="hidden" name="tmpTipoCambrion" id="tmpTipoCambrion" value="<?php echo $r->TipoCambrion; ?>">
                        </span></td>
                      <td class="row2">Suela Rota</td>
                      <td class="row2"><input type="checkbox" name="TipoSuela" id="TipoSuela" value="S" <?php if ($_GET['TipoSuela']=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoSuela" id="tmpTipoSuela" value="<?php echo $r->TipoRemonta; ?>"></td>
                    </tr>
                    <tr>
                      <td class="row2">Plantilla estructural</td>
                      <td class="row2"><input type="checkbox" name="TipoPlantilla" id="TipoPlantilla" value="S" <?php if ($_GET['TipoPlantilla']=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoPlantilla" id="tmpTipoPlantilla" value="<?php echo $r->TipoPlantilla; ?>"></td>
                      <td >Tacon</td>
                      <td ><span class="row2">
                        <input type="checkbox" name="TipoTacon" id="TipoTacon" value="S" <?php if ($_GET['TipoTacon']=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoTacon" id="tmpTipoTacon" value="<?php echo $r->TipoTacon; ?>">
                      </span></td>
                      <td class="row2">Guarnicion</td>
                      <td class="row2"><span class="row2">
                        <input type="checkbox" name="TipoGuarnicion" id="TipoGuarnicion" value="S" <?php if ($_GET['TipoGuarnicion']=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoGuarnicion" id="tmpTipoGuarnicion" value="<?php echo $r->TipoGuarnicion; ?>">
                      </span></td>
                    </tr>
                    <tr>
                      <td height="27" class="row2">Cremallera</td>
                      <td class="row2"><input type="checkbox" name="TipoCremallera" id="TipoCremallera" value="S" <?php if ($_GET['TipoCremallera']=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoCremallera" id="tmpTipoCremallera" value="<?php echo $r->TipoCremallera; ?>"></td>
                      <td >Cerco</td>
                      <td><input type="checkbox" name="TipoCerco" id="TipoCerco" value="S" <?php if ($_GET['TipoCerco']=="S"){ echo "checked"; } ?>  />
                        <span class="row2">
                          <input type="hidden" name="tmpTipoCerco" id="tmpTipoCerco" value="<?php echo $r->TipoCerco; ?>">
                        </span></td>
                      <td class="row2">Puntera</td>
                      <td class="row2"><span class="row2">
                        <input type="checkbox" name="TipoPuntera" id="TipoPuntera" value="S" <?php if ($_GET['TipoPuntera']=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoPuntera" id="tmpTipoPuntera" value="<?php echo $r->TipoPuntera; ?>">
                      </span></td>
                    </tr>
                    <tr>
                      <td class="row2">Herraje</td>
                      <td class="row2"><input type="checkbox" name="TipoHerraje" id="TipoHerraje" value="S" <?php if ($_GET['TipoHerraje']=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoHerraje" id="tmpTipoHerraje" value="<?php echo $r->TipoHerraje; ?>"></td>
                      <td class="row2">&nbsp;</td>
                      <td class="row2">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
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
