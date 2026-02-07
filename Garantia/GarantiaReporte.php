<script src="admin/jscripts/Chart.js-master/Chart.js"></script>

<?php
	$TitleMod ="Factura";
	
	$Table = "Factura";
	$TableJoin = "DetalleFactura";
	$Key = "IDFactura";
	$MOD = "GenerarFactura";
	$m = "Movimientos";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
		
		
	
if($permisos[0] >= 2)
{		
		$action="list";		
		switch ($action) {
			
			case "list" :	
			
				if (!empty($_GET[puntoventa]))
					$condiciones.=" and G.IDPuntoVenta = '".$_GET[puntoventa]."'";

				if (!empty($_GET[Tipo]))
					$condiciones.=" and G.TipoRegistro = '".$_GET[Tipo]."'";

				if (!empty($_GET[TipoReproceso]))
					$condiciones.=" and G.".$_GET[TipoReproceso]." = 'S'";

				if (!empty($_GET[IDEstadoGarantia]))
					$condiciones.=" and G.IDEstadoGarantia = '".$_GET[IDEstadoGarantia]."'";

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


					
				
			$sql = " SELECT G.*, C.*, EG.Nombre, G.FechaTrCr as FechaGarantia
							 FROM Garantia G, EstadoGarantia EG,  Cliente C, Factura F
							 WHERE G.IDFactura = F.IDFactura and C.IDCliente = F.IDCliente and
							 	   EG.IDEstadoGarantia = G.IDEstadoGarantia
								   AND G.IDPuntoVenta = '".$datos[IDPuntoVenta]."'
							 	   $condiciones
							ORDER BY IDGarantia DESC";

			
			//consulto totales para el resumen
			$result_garantia=db_query($sql);
			$totales[contador_garantia]=0;
			$totales[contador_tipo_garantia]=0;
			$totales[contador_tipo_servicio]=0;
			$totales[contador_tipo_reproceso]=0;
			$totales[contador_contrafuerte]=0;
			$totales[contador_cuero]=0;
			$totales[contador_plantilla]=0;
			$totales[contador_cremallera]=0;
			$totales[contador_despegue]=0;
			$totales[contador_cambrion]=0;
			$totales[contador_tacon]=0;
			$totales[contador_cerco]=0;
			$totales[contador_cardado]=0;
			$totales[contador_suela_rota]=0;
			$totales[contador_guarnicion]=0;
			$totales[contador_puntera]=0;
			$totales[contador_herraje]=0;
			while($row_garantia=db_fetch_array($result_garantia)){
				$totales[contador_garantia]++;
				if ($row_garantia[TipoRegistro]=="Garantia")
					$totales[contador_tipo_garantia]++;
				elseif($row_garantia[TipoRegistro]=="Servicio")	
					$totales[contador_tipo_servicio]++;	
				elseif($row_garantia[TipoRegistro]=="Reproceso")	
					$totales[contador_tipo_reproceso]++;
				
				if ($row_garantia[TipoContrafuerte]=="S")	
					$totales[contador_contrafuerte]++;
				if ($row_garantia[TipoCuero]=="S")	
					$totales[contador_cuero]++;
				if ($row_garantia[TipoPlantilla]=="S")	
					$totales[contador_plantilla]++;
				if ($row_garantia[TipoCremallera]=="S")	
					$totales[contador_cremallera]++;
				if ($row_garantia[TipoDespegue]=="S")	
					$totales[contador_despegue]++;
				if ($row_garantia[TipoCambrion]=="S")	
					$totales[contador_cambrion]++;
				if ($row_garantia[TipoTacon]=="S")	
					$totales[contador_tacon]++;
				if ($row_garantia[TipoOtro]!="")	
					$totales[contador_otros]++;
				if ($row_garantia[TipoCerco]=="S")	
					$totales[contador_cerco]++;
				if ($row_garantia[TipoCardado]=="S")	
					$totales[contador_cardado]++;
				if ($row_garantia[TipoSuela]=="S")	
					$totales[contador_suela_rota]++;
				if ($row_garantia[TipoGuarnicion]=="S")	
					$totales[contador_guarnicion]++;
				if ($row_garantia[TipoPuntera]=="S")	
					$totales[contador_puntera]++;
				if ($row_garantia[TipoHerraje]=="S")	
					$totales[contador_herraje]++;
					
					
					
					
			}
			
			
			list_r($sql,$totales);
			break;
			default : 
					list_r();
			break;			
			
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col1");
	
	
/*******************************************************************************************
		funtcion mostrarcedula
*******************************************************************************************/

function list_r($sql="",$totales){
	global $IDPuntoVenta;
	
if(empty($sql))
	 	$sql =  "SELECT * FROM Garantia ORDER BY IDGarantia DESC";
	 	

		
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

 if($_GET['in_order']=="ASC" || $_GET['in_order']==""){
								$img="down.png";
								$order="DESC";
							}else if($_GET['in_order']=="DESC"){
								$img="up.png";
								$order="ASC";
							}
							
							?>	
	

	
<br>


<table width=500 cellpadding=0 cellspacing=0 align=center >
	<tr>
		<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
	</tr>
	<?php filtrar();?>	
	<tr>
		<td class=titlemedium  bgcolor=#9daac6><?php echo $info;;?></td>
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
	        <td> Garantias</td>
	        <td>&nbsp;<?php echo $totales[contador_garantia]; ?></td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
          </tr>
	      <tr>
	        <td> Tipo: Garantias</td>
	        <td>&nbsp;<?php echo $totales[contador_tipo_garantia]; ?></td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
          </tr>
	      <tr>
	        <td> Tipo: Servicios Y reprocesos</td>
	        <td>&nbsp;<?php echo $totales[contador_tipo_reproceso]; ?></td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
          </tr>
	      <tr>
	        <td colspan="4" bgcolor=#DBEAF5>CAUSA GARANTIA</td>
          </tr>
	      <tr>
	        <td>Contrafuerte</td>
	        <td>&nbsp;<?php echo $totales[contador_contrafuerte]; ?></td>
	        <td>Tacon</td>
	        <td>&nbsp;<?php echo $totales[contador_tacon]; ?></td>
          </tr>
	      <tr>
	        <td>Cuero</td>
	        <td>&nbsp;<?php echo $totales[contador_cuero]; ?></td>
	        <td>Cerco</td>
	        <td>&nbsp;<?php echo $totales[contador_cerco]; ?></td>
          </tr>
	      <tr>
	        <td>Plantilla estructural</td>
	        <td>&nbsp;<?php echo $totales[contador_plantilla]; ?></td>
	        <td>Cardado</td>
	        <td>&nbsp;<?php echo $totales[contador_cardado]; ?></td>
          </tr>
	      <tr>
	        <td>Cremallera</td>
	        <td>&nbsp;<?php echo $totales[contador_cremallera]; ?></td>
	        <td>Suela rota</td>
	        <td>&nbsp;<?php echo $totales[contador_suela_rota]; ?></td>
          </tr>
	      <tr>
	        <td>Despegue</td>
	        <td>&nbsp;<?php echo $totales[contador_despegue]; ?></td>
	        <td>Guarnicion</td>
	        <td>&nbsp;<?php echo $totales[contador_guarnicion]; ?></td>
          </tr>
	      <tr>
	        <td>Cambrion</td>
	        <td>&nbsp;<?php echo $totales[contador_cambrion]; ?></td>
	        <td>Puntera</td>
	        <td>&nbsp;<?php echo $totales[contador_puntera]; ?></td>
          </tr>
	      <tr>
	        <td>Herraje</td>
	        <td>&nbsp;<?php echo $totales[contador_herraje]; ?></td>
	        <td>Otros</td>
	        <td>&nbsp;<?php echo $totales[contador_otros]; ?></td>
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
						"<?php echo $totales[contador_contrafuerte]; ?>",
						"<?php echo $totales[contador_cuero]; ?>",
						"<?php echo $totales[contador_plantilla]; ?>",
						"<?php echo $totales[contador_cremallera]; ?>",
						"<?php echo $totales[contador_despegue]; ?>",
						"<?php echo $totales[contador_cambrion]; ?>",
						"<?php echo $totales[contador_tacon]; ?>",
						"<?php echo $totales[contador_cerco]; ?>",
						"<?php echo $totales[contador_cardado]; ?>",
						"<?php echo $totales[contador_suela_rota]; ?>",
						"<?php echo $totales[contador_guarnicion]; ?>",
						"<?php echo $totales[contador_puntera]; ?>",
						"<?php echo $totales[contador_herraje]; ?>",
						"<?php echo $totales[contador_otros]; ?>",
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
					value: <?php echo $totales[contador_tipo_garantia]; ?>,
					color:"#F7464A",
					highlight: "#FF5A5E",
					label: "Garantia"
				},
				{
					value: <?php echo $totales[contador_tipo_servicio]; ?>,
					color: "#46BFBD",
					highlight: "#5AD3D1",
					label: "Servicios"
				},
				{
					value: <?php echo $totales[contador_tipo_reproceso]; ?>,
					color: "#46BFBD",
					highlight: "#5AD3D1",
					label: "Reprocesos"
				}
				

			];

			
			
			
	</script>

      
      </td>
  </tr>
	<tr>
	  <td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
	<tr>
	  <td colspan="8" align=left valign=middle bgcolor=#DBEAF5 class=rowform><span class="texto">
	    <?php
			print $pages;
		?>
	    </span></td>
	  </tr>
    <tr>
  <td colspan="8" align=left valign=middle bgcolor=#DBEAF5 class=rowform><a href="Garantia/exportagarantias.php?sql=<?php echo $sql; ?>"><img src="../images/excel_icon.gif" alt="" width="20" height="20" border="0" >Exportar Registros </a></td>
  </tr>
<tr>
						<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Numero&nbsp;
						    <?php if($_GET['order_by']=="Nombre")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Codigo&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Cliente</a><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Codigo&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>&nbsp;
						    <?php if($_GET['order_by']=="Codigo")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Producto&nbsp;
						    <?php if($_GET['order_by']=="Publicar")<?php <img src="images/<?php echo $img;?>" border=0><?php };?></a> </td>
						<td class=navpic nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=FechaFacturaBono&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Factura&nbsp;
						  <?php if($_GET['order_by']=="FechaFacturaBono")<?php 
						  <img src="images/<?php echo $img;?>" alt="" border=0>
						  <?php };?>
						  </a></td>
						<td class=navpic nowrap bgcolor=#DBEAF5>Fecha</td>
						<td class=navpic nowrap bgcolor=#DBEAF5>Estado</td>
						<td class=navpic nowrap bgcolor=#DBEAF5>Punto de Venta</td>
		</tr>

<?php while($r = db_fetch_object($result)){
?>
  	
<tr>
						<td align=center valign=middle nowrap width=69 class=row2>
						  &nbsp;<a href='<?php echo "?mod=SeguimientoGarantia&action=edit&id="; echo $r->IDGarantia; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap class="<?=$class?>"><?php echo $r->IDGarantia; ?></td>
						<td nowrap class="<?=$class?>"><?php
									$id_cliente= get_field("Factura","IDCliente","IDFactura",$r->IDFactura);
									echo get_field("Cliente","Nombre","IDCliente",$id_cliente)." ".get_field("Cliente","Apellido","IDCliente",$id_cliente)?></td>
						<td nowrap class="<?=$class?>"><?php
									  $sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVenta."'";
									  $qry_producto=db_query($sql_producto);
									  $r_detalle=db_fetch_object($qry_producto);
									echo "<b>Ref:</b> " . get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
									echo " <b>Talla:</b> " .get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica));
									echo " <b>Nombre:</b> " .get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));

									
									?></td>
						<td nowrap class="<?=$class?>"><?php echo get_field("Factura","Numerofactura","IDFactura",$r->IDFactura); ?></td>
						<td nowrap class="<?=$class?>"><?php echo formatofecha(substr($r->FechaGarantia,0,10)) ?></td>
						<td nowrap class="<?=$class?>"><?php echo get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$r->IDEstadoGarantia); ?></td>
						<td nowrap class="<?=$class?>"><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta); ?></td>
		</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=8 nowrap>
	<?php
		print $pages;
		?>
</td>
</tr>		
</table></td>
</tr>
</table>
</form>
<?php
}//end	mostrar($newmode,$submit_caption)


/*******************************************************************************************
		funtcion Print_formCliente
*******************************************************************************************/
function print_formgarantia($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$cedula,$array_gustos,$array_deportes,$array_hobbies,$array_musica, $datos,$IDPuntoVenta;
	$qid = db_query(" SELECT * FROM Cliente WHERE Cedula = '$id' ");
	$r = db_fetch_object($qid);
	
?>
	
<br>
<?php
}// End function print_formgarantia()






/*******************************************************************************************
		funtcion print_formfactura_cliente
*******************************************************************************************/
function print_formfactura_cliente($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$cedula,$array_gustos,$array_deportes,$array_hobbies,$array_musica, $datos,$IDPuntoVenta;
	
?>
	
<br>
<?php
}// End function print_formfactura_cliente()









/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/

function print_form($id,$newmode,$title,$submit_caption,$frm=""){
	GLOBAL $TitleMod,$Table,$MOD,$Key, $ID_Usuario, $IVA,$IDPuntoVenta;
	
	
	
	
	$qid = db_query(" SELECT * FROM Cliente WHERE IDCliente = '$id' ");
		
	$r = db_fetch_object($qid);
?>

<script language="JavaScript">
<!--
		
	-->
</script>
<script>
var Check = new Array('NumeroFactura','NumeroDocumento','IDPuntoVenta','IDCliente','IDEmpleado', 'Cantidad1', 'Nombre1', 'ValorTotal');
</script>
<br>
<?php
} // END function print_form_fotos($id,$numfotos)
?>



<?php
/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD, $datos;
	
?>
	<form name="frm" action="" method="get">
    
    
    
    
    
    
	<tr>
				<td class="rowform" align="center" colspan=8><span class="col2">
                
                
Punto de venta 
				    <select class=tbox name=puntoventa>
				    <option value="">Seleccione</option>
				    <?php
							$sql_puntos = "SELECT P.* FROM PuntoVenta P ";
							$sql_puntos .= "WHERE 1 and IDPuntoVenta = '".$datos[IDPuntoVenta]."' Order By Nombre";
							
							$query_puntos = db_query( $sql_puntos );
						
							while( $r_puntos = db_fetch_object( $query_puntos ) )
							{
								if ($r_puntos->IDPuntoVenta == $IDPuntoVenta )
									$selecciona = " selected";
								else	
									$selecciona = " ";
								
								echo "<option value=$r_puntos->IDPuntoVenta $selecciona>$r_puntos->Nombre</option>";
								
							}
						?>
			    </select>
				</span>Tipo
                
                <select name="Tipo" id="Tipo">
               	  <option value="">[Seleccione]</option>
                    <option value="Garantia">Garantia</option>
                    <option value="Servicio">Servicio</option>
                    <option value="Reproceso">Reproceso</option>
                </select>
                
				  Tipo Reproceso
				  <select name="TipoReproceso" id="TipoReproceso">
				    <option value="">[Seleccione]</option>
                    <option value="Remonta">Remonta</option>
			      </select>
				  <br>Estado
                  <?php echo formpopup("EstadoGarantia","Nombre","IDEstadoGarantia","IDEstadoGarantia",$r->IDEstadoGarantia,"input\" id=\"IDEstadoGarantia"); ?>
                  
				  Entre
                  
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
                
                
                
                
                
<table width="100%"  cellpadding="2" cellspacing="3">
                    <tr>
                      <td colspan="6"><strong>CAUSA DE GARANTIA</strong></td>
                    </tr>
                    <tr>
                      <td class="row2">Contrafuerte</td>
                      <td class="row2"><input type="checkbox" name="TipoContrafuerte" id="TipoContrafuerte" value="S" <?php if ($_GET[TipoContrafuerte]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpContrafuerte" id="tmpContrafuerte" value="<?php echo $r->TipoContrafuerte; ?>"></td>
                      <td class="row2" >Despegue</td>
                      <td class="row2" >
                        
                        <input type="checkbox" name="TipoDespegue" id="TipoDespegue" value="S" <?php if ($_GET[TipoDespegue]=="S"){ echo "checked"; } ?>  />
                          <input type="hidden" name="tmpTipoDespegue" id="tmpTipoDespegue" value="<?php echo $r->TipoDespegue; ?>">
                      </td>
                      <td class="row2">Cardado</td>
                      <td class="row2"><input type="checkbox" name="TipoCardado" id="TipoCardado" value="S" <?php if ($_GET[TipoCardado]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoCardado" id="tmpTipoCardado" value="<?php echo $r->TipoCardado; ?>"></td>
                    </tr>
                    <tr>
                      <td class="row2">Cuero</td>
                      <td class="row2"><input type="checkbox" name="TipoCuero" id="TipoCuero" value="S" <?php if ($_GET[TipoCuero]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoCuero" id="tmpTipoCuero" value="<?php echo $r->TipoCuero; ?>"></td>
                      <td class="row2">Cambrion</td>
                      <td class="row2">
                        
                        <input type="checkbox" name="TipoCambrion" id="TipoCambrion" value="S" <?php if ($_GET[TipoCambrion]=="S"){ echo "checked"; } ?>  />
                          <input type="hidden" name="tmpTipoCambrion" id="tmpTipoCambrion" value="<?php echo $r->TipoCambrion; ?>">
                      </td>
                      <td class="row2">Suela Rota</td>
                      <td class="row2"><input type="checkbox" name="TipoSuela" id="TipoSuela" value="S" <?php if ($_GET[TipoSuela]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoSuela" id="tmpTipoSuela" value="<?php echo $r->TipoRemonta; ?>"></td>
                    </tr>
                    <tr>
                      <td class="row2">Plantilla estructural</td>
                      <td class="row2"><input type="checkbox" name="TipoPlantilla" id="TipoPlantilla" value="S" <?php if ($_GET[TipoPlantilla]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoPlantilla" id="tmpTipoPlantilla" value="<?php echo $r->TipoPlantilla; ?>"></td>
                      <td class="row2">Tacon</td>
                      <td class="row2">
                        <input type="checkbox" name="TipoTacon" id="TipoTacon" value="S" <?php if ($_GET[TipoTacon]=="S"){ echo "checked"; } ?>  />
                        <input type="hidden" name="tmpTipoTacon" id="tmpTipoTacon" value="<?php echo $r->TipoTacon; ?>">
                      </td>
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
                      <td class="row2">Cerco</td>
                      <td class="row2">
                        
                        <input type="checkbox" name="TipoCerco" id="TipoCerco" value="S" <?php if ($_GET[TipoCerco]=="S"){ echo "checked"; } ?>  />
                          <input type="hidden" name="tmpTipoCerco" id="tmpTipoCerco" value="<?php echo $r->TipoCerco; ?>">
                      </td>
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
                      <td class="row2" >&nbsp;</td>
                      <td class="row2">&nbsp;</td>
                      <td class="row2">&nbsp;</td>
                      <td class="row2">&nbsp;</td>
                    </tr>
                  </table>                
                
                
                
                
                
                
                
                <br>
					<input type="hidden" name="mod" value="GarantiaReporte">
					
					<input type="hidden" name="action" value="list">
					
					<input type="submit" name="submit" value="Buscar" class="submit">
	  </td>
	  </tr>
	</form>
<?php
	}//End function filtrar
?>


</BODY></HTML> 
