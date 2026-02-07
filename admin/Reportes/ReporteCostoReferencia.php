<body> <?php


$TitleMod ="Costo Referencia";

$Table = "CostoReferencia";
$TableJoin = "Referencia";
$Key = "IDCostoReferencia";
$MOD = "ReporteCostoReferencia";
$m="Referencia";

$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		
		
} // End switch
else{
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");
	exit;
}	


	

		
		switch (nvl($action)) {
			
			case "list" :	
			
			if (empty($_GET[Referencia]) &&   empty($_GET[IDProveedor]) && 
				empty($_GET[IDPuntoVenta]) && empty($_GET[Sexo]) ):
				$_GET[IDPuntoVenta]=1;
				
			endif;	
				
			
			if (!empty($_GET[Referencia]))
				$where = " and R.Numero like '%".$_GET[Referencia]."%' ";
	
			if (!empty($_GET[IDProveedor]))
				$where .= " and R.IDProveedor = '".$_GET[IDProveedor]."' ";			
				
			if (!empty($_GET[IDPuntoVenta]))
				$where .= " and P.IDPuntoVenta = '".$_GET[IDPuntoVenta]."' ";
				
			if (!empty($_GET[Sexo]))
				$where .= " and R.Sexo = '".$_GET[Sexo]."' ";		
	
			if (!empty($_GET[IDTipoReferencia]))
				$where .= " and R.IDTipoReferencia = '".$_GET[IDTipoReferencia]."' ";
				
							   
			
			$sql = "SELECT CR.*, PROV.IDProveedor, PROV.Nombre as NombreProveedor, R.IDTipoReferencia, R.Sexo, P.Nombre as NombrePuntoVenta, P.IDPuntoVenta
				 FROM  Referencia R, CostoReferencia CR, Proveedor PROV, PuntoVentaReferencia PR, PuntoVenta P
				 Where R.IDReferencia = CR.IDReferencia and
				 PROV.IDProveedor = R.IDProveedor and
				 R.IDReferencia = PR.IDReferencia and
				 PR.IDPuntoVenta = P.IDPuntoVenta " . $where . " 
				 ORDER BY P.IDPuntoVenta, NombreProveedor, R.IDTipoReferencia, R.IDReferencia";
			
			
			list_r($sql);
			break;
			default : 
					list_r();
			break;
		
		} // End switch





/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('Descripcion','Publicar');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgColor='#FFFFFF'>
		<tr>
			<td class=nav width=76;?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<?php echo $MOD;?>">Administrar <?php echo $TitleMod;?></a> </td>
			<td></td>
		</tr>
</table>
<br>




<?php
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar;
	if(empty($sql))
	 	$sql =  "SELECT CR.*, PROV.IDProveedor, PROV.Nombre as NombreProveedor, R.IDTipoReferencia, R.Sexo, P.Nombre as NombrePuntoVenta, P.IDPuntoVenta
				 FROM  Referencia R, CostoReferencia CR, Proveedor PROV, PuntoVentaReferencia PR, PuntoVenta P
				 Where R.IDReferencia = CR.IDReferencia and
				 PROV.IDProveedor = R.IDProveedor and
				 R.IDReferencia = PR.IDReferencia and
				 PR.IDPuntoVenta = P.IDPuntoVenta
				 AND  P.IDPuntoVenta = 1
				 ORDER BY P.Nombre, NombreProveedor, R.IDTipoReferencia, R.IDReferencia ";
	 	
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=4000;
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
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgColor='#FFFFFF'>
	<tr>
		<td class=nav width=76;?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<?php echo $MOD;?>">Administrar <?php echo $TitleMod;?></a> </td>
		<td></td>
	</tr>
</table>

<?php
		if($rows > 0){
?>		



<br>
<table width=500 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
			<td class=titlemedium bgColor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
		</tr>
<?php filtrar();?>	
<tr>
			<td class=titlemedium  bgColor=#9daac6>Resultado</td>
		</tr>
<tr>
<td class=texto bgColor=#DBEAF5 colspan=12 nowrap>
<?php
	print $pages;
?>
</td>
</tr>
	<tr>
			<td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
<tr>
  <td class=rowform nowrap bgColor=#DBEAF5>Punto de Venta</td>
  <td class=rowform nowrap bgColor=#DBEAF5>Proveedor</td>
  <td class=rowform nowrap bgColor=#DBEAF5>Tipologia</td>
  <td class=rowform nowrap bgColor=#DBEAF5>Genero</td>
<td class=rowform nowrap bgColor=#DBEAF5>Referencia</td>
<td class=rowform nowrap bgColor=#DBEAF5>Fecha</td>
<td class=rowform nowrap bgColor=#DBEAF5>Costo</td>
						<td class=rowform nowrap bgColor=#DBEAF5>Cantidad Vendida</td>
						<td class=rowform nowrap bgColor=#DBEAF5>Costo inventario vendido</td>
					</tr>

<?php 
	$id_pto_venta_anterior=""; 
	$id_proveedor_anterior="";
	$id_tiporef_anterior=""; 
	$contador_costo=0;
	while($r = db_fetch_object($result)){
		unset($array_id_codif_especifica);
		$cantidad_referencia=0;
		$existencias_referencia=0;
		
	//CANTIDAD VENDIDA
						// selecciono pto venta referencia
						$sql_pto_ref="Select * 
									  From PuntoVentaReferencia 
									  Where IDPuntoVenta = '".$r->IDPuntoVenta."' and IDReferencia = '".$r->IDReferencia."'
									  ";
						$result_pto_ref=db_query($sql_pto_ref);
						$row_pto_ref=db_fetch_array($result_pto_ref);
						
						$row_pto_ref[IDPuntoVentaReferencia];
						
						$sql_codif="Select * 
									  From CodificacionEspecifica 
									  Where IDPuntoVentaReferencia = '".$row_pto_ref[IDPuntoVentaReferencia]."'
									  ";
									  
						$result_codif=db_query($sql_codif);
						while($row_codif=db_fetch_array($result_codif)):
							$array_id_codif_especifica[]=$row_codif[IDCodificacionEspecifica];
						endwhile;
						
						if(count($array_id_codif_especifica)>0):
							if (!empty($_GET[FechaInicio]) && !empty($_GET[FechaFin]))
								$condicion_fecha_venta = " and ( DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) >= DATE_FORMAT('".$_GET[FechaInicio]."','%Y-%c-%d' ) and DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) <= DATE_FORMAT('".$_GET[FechaFin]."','%Y-%c-%d' ) ) ";			

						
							$id_codificacion = implode(",",$array_id_codif_especifica);
							$sql_detalle_factura = "Select Cantidad from DetalleFactura DF, Factura F Where DF.IDFactura = F.IDFactura and IDCodificacionEspecifica in (".$id_codificacion.") ". $condicion_fecha_venta . " Group by DF.IDFactura";
							$result_detalle_factura=db_query($sql_detalle_factura);
							while($row_detalle_factura=db_fetch_array($result_detalle_factura)):
								$cantidad_referencia+=$row_detalle_factura[Cantidad];
							endwhile;
							
							
							
							
						endif;
	
	//FIN CANTIDAD VENDIDA
		
		
		
	// Si existe unidad vendida	
	if((int)$cantidad_referencia>0):	
	
	if($contador_costo==0):
		$id_pto_venta_anterior=$r->IDPuntoVenta;
		$id_proveedor_anterior=$r->IDProveedor;
		$id_tiporef_anterior=$r->IDTipoReferencia;
	endif;	
	
	//Si es otro almacen calculo los totales
    if ($r->IDPuntoVenta!=$id_pto_venta_anterior ):
		
	?>
                    
        <tr bgcolor="#D5EAE1" style="font-weight:bold; font-size:10px; height:30px" >
          <td colspan="6" nowrap   >SUBTOTALES <?php echo  get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_pto_venta_anterior); ?></td>
          <td nowrap  align="right">$<?php echo number_format($total_costo_tienda,0,",","."); ?></td>
          <td nowrap  align="center"><?php echo $cantidad_tienda; ?></td>
          <td nowrap  align="right" >$<?php echo number_format($total_costo_vendido_tienda,0,",","."); ?></td>
        </tr>
        <tr>
          <td colspan="9" nowrap class=row1><hr></td>
        </tr>
	<?php 
		$cantidad_tienda=0;
		$total_costo_tienda=0;
		$total_costo_vendido_tienda=0;
		$total_existencias_tienda=0;
		$total_costo_existencias_tienda=0;
		$id_pto_venta_anterior = $r->IDPuntoVenta;
	endif;
	
	//Si es otro tipo de referencia calculo los totales
    if ($r->IDTipoReferencia!=$id_tiporef_anterior ):
	?>
                    
        <tr bgcolor="#EED1D1" style="font-weight:bold; font-size:10px; height:30px">
          <td colspan="6" nowrap >SUBTOTAL TIPOLOGIA <?php echo  get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tiporef_anterior); ?></td>
          <td nowrap  align="right">$<?php echo number_format($total_costo_ref,0,",","."); ?></td>
          <td nowrap  align="center"><?php echo $cantidad_ref; ?></td>
          <td nowrap  align="right" >$<?php echo number_format($total_costo_vendido_ref,0,",","."); ?></td>
        </tr>
        <tr>
          <td colspan="9" nowrap class=row1><hr></td>
        </tr>
	<?php 
		$cantidad_ref=0;
		$total_costo_ref=0;
		$total_costo_vendido_ref=0;
		$total_existencias_ref=0;
		$total_costo_existencias_ref=0;
		$id_tiporef_anterior = $r->IDTipoReferencia;
	endif;
	
	//Si es otro proveedor calculo los totales
    if ($r->IDProveedor!=$id_proveedor_anterior ):
		
	?>
                    
        <tr bgcolor="#909FC7" style="font-weight:bold; font-size:10px; height:30px">
          <td colspan="6" nowrap >SUBTOTAL PROVEEDOR <?php echo  get_field("Proveedor","Nombre","IDProveedor",$id_proveedor_anterior); ?></td>
          <td nowrap  align="right">$<?php echo number_format($total_costo_prov,0,",","."); ?></td>
          <td nowrap  align="center"><?php echo $cantidad_prov; ?></td>
          <td nowrap  align="right" >$<?php echo number_format($total_costo_vendido_prov,0,",","."); ?></td>
        </tr>
        <tr>
          <td colspan="9" nowrap class=row1><hr></td>
        </tr>
	<?php 
		$cantidad_prov=0;
		$total_costo_prov=0;
		$total_costo_vendido_prov=0;
		$total_existencias_prov=0;
		$total_costo_existencias_prov=0;
		$id_proveedor_anterior = $r->IDProveedor;
	endif;
	
	
	
	
		
		
	
?>
  	
<tr>
  <td nowrap class=row1><?php echo $r->NombrePuntoVenta ?></td>
  <td nowrap class=row1><?php echo $r->NombreProveedor ?></td>
  <td nowrap class=row1><?php echo get_field("TipoReferencia","Descripcion","IDTipoReferencia",$r->IDTipoReferencia)?></td>
  <td nowrap class=row1 align="center"><?php echo $r->Sexo ?></td>
<td nowrap class=row1><?php echo get_field("Referencia","Numero","IDReferencia",$r->IDReferencia);  ?></td>
<td nowrap class=row1 align="left"><?php echo $r->Fecha ?></td> 
<td nowrap class=row1 align="right">$<?php  $costo=(int)$r->Costo;
	$total_costo_tienda+=$costo;
	
	$total_costo_ref+=$costo;
	$total_costo_prov+=$costo;
	
	$GRAN_total_costo_tienda+=$costo;
	

	echo number_format($costo,0,",","."); 

?></td>
						<td nowrap class=row1 align="center">
                        <?php echo $cantidad_referencia; 
						
							$cantidad_tienda+=$cantidad_referencia;
							$cantidad_ref+=$cantidad_referencia;
							$cantidad_prov+=$cantidad_referencia;
							
							$GRAN_cantidad_tienda+=$cantidad_referencia;
						?>
                        
                        </td>
						<td nowrap class=row1 align="right" >$
						<?php  $total_costo_vendido = (int)$cantidad_referencia * $costo;  
						
								$total_costo_vendido_tienda += $total_costo_vendido;
								$total_costo_vendido_ref += $total_costo_vendido;
								$total_costo_vendido_prov += $total_costo_vendido;
								$GRAN_total_costo_vendido_tienda += $total_costo_vendido;
								 
							echo number_format($total_costo_vendido,0,",","."); 
						?>
                        </td>
					</tr>
                    
    

   
<?php 
$contador_costo++;
endif; //if((int)$cantidad_referencia>0):	
} // END for
?>

<tr bgcolor="#D5EAE1" style="font-weight:bold; font-size:10px; height:30px">
          <td colspan="6" nowrap >SUBTOTALES <?php echo  get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_pto_venta_anterior); ?></td>
          <td nowrap  align="right">$<?php echo number_format($total_costo_tienda,0,",","."); ?></td>
          <td nowrap  align="center"><?php echo $cantidad_tienda; ?></td>
          <td nowrap  align="right" >$<?php echo number_format($total_costo_vendido_tienda,0,",","."); ?></td>
        </tr>
        <tr>
          <td colspan="9" nowrap class=row1><hr></td>
        </tr>

		<tr bgcolor="#EED1D1" style="font-weight:bold; font-size:10px; height:30px">
		  <td colspan="6" nowrap >SUBTOTAL TIPOLOGIA <?php echo  get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tiporef_anterior); ?></td>
		  <td nowrap  align="right">$<?php echo number_format($total_costo_ref,0,",","."); ?></td>
		  <td nowrap  align="center"><?php echo $cantidad_ref; ?></td>
		  <td nowrap  align="right" >$<?php echo number_format($total_costo_vendido_ref,0,",","."); ?></td>
	    </tr>
        <tr>
          <td colspan="9" nowrap class=row1><hr></td>
        </tr>



	
		<tr bgcolor="#909FC7" style="font-weight:bold; font-size:10px; height:30px">
		  <td colspan="6" nowrap >SUBTOTAL PROVEEDOR <?php echo  get_field("Proveedor","Nombre","IDProveedor",$id_proveedor_anterior); ?></td>
		  <td nowrap  align="right">$<?php echo number_format($total_costo_prov,0,",","."); ?></td>
		  <td nowrap  align="center"><?php echo $cantidad_prov; ?></td>
		  <td nowrap  align="right" >$<?php echo number_format($total_costo_vendido_prov,0,",","."); ?></td>
	    </tr>
        <tr>
          <td colspan="9" nowrap class=row1><hr></td>
        </tr>

                    
        
        
        
        <tr class=titlemedium bgColor="#9daac6">
          <td colspan="6" nowrap >GRAN TOTAL</td>
          <td nowrap  align="right">$<?php echo number_format($GRAN_total_costo_tienda,0,",","."); ?></td>
          <td nowrap  align="center"><?php echo $GRAN_cantidad_tienda; ?></td>
          <td nowrap  align="right" >$<?php echo number_format($GRAN_total_costo_vendido_tienda,0,",","."); ?></td>
        </tr>
        <tr>
          <td colspan="9" nowrap class=row1><hr></td>
        </tr>




<tr>
<td class=texto bgColor=#DBEAF5 colspan=9 nowrap>
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
	<form name="frm" action="./" method="get" onsubmit="return valbuscar(document.frm)">
		<tr>
			<td class="rowform" align="center" colspan=8>
				Referencia: <input type="text" name="Referencia" id="Referencia" >
				Punto de venta: 
				<select name="IDPuntoVenta" id="IDPuntoVenta" class="input">
                	<option value="">[Seleccione]</option>
				<?php 
				$sql_punto_venta = "Select * From PuntoVenta Where 1 order by Nombre asc";
				$result_punto_venta = db_query($sql_punto_venta);
				while($row_punto_venta = db_fetch_array($result_punto_venta)): ?>
                	<option value="<?php echo $row_punto_venta[IDPuntoVenta]; ?>"><?php echo $row_punto_venta[Nombre]; ?></option>
				<?php endwhile; ?>
                </select>
                <br>Proveedor: 
				<select name="IDProveedor" id="IDProveedor" class="input">
                	<option value="">[Seleccione]</option>
				<?php 
				$sql_proveedor = "Select * From Proveedor Where 1 order by Nombre asc";
				$result_proveedor = db_query($sql_proveedor);
				while($row_proveedor = db_fetch_array($result_proveedor)): ?>
                	<option value="<?php echo $row_proveedor[IDProveedor]; ?>"><?php echo $row_proveedor[Nombre]; ?></option>
				<?php endwhile; ?>
                </select>
                
                Genero: 
                <select name="Sexo" id="Sexo" class="input">
                	<option value="">[Seleccione]</option>
                    <option value="F">Femenino</option>
                	<option value="M">Masculino</option>
	            </select>
                
                Tipologia: 
               <select name="IDTipoReferencia" id="IDTipoReferencia" class="input">
                	<option value="">[Seleccione]</option>
				<?php 
				$sql_tiporef = "Select * From TipoReferencia Where 1 order by Descripcion asc";
				$result_tiporef = db_query($sql_tiporef);
				while($row_tiporef = db_fetch_array($result_tiporef)): ?>
                	<option value="<?php echo $row_tiporef[IDTipoReferencia]; ?>"><?php echo $row_tiporef[Descripcion]; ?></option>
				<?php endwhile; ?>
                </select>
                <br>
                Fecha Inicio Venta	<input readonly type="text" name="FechaInicio" class="input" value="">
                <script language="JavaScript1.2">
							<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaInicio,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
							//-->
						</script>
                Fecha Fin Venta	<input readonly type="text" name="FechaFin" class="input" value="">
                <script language="JavaScript1.2">
							<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaFin,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
							//-->
						</script>
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
