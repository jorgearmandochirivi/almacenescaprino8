<body><?

$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$Title = " Consultar Inventario ";
$MOD = "InventarioCon";
$m="Referencia";
$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		
		
} // End switch
else{
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");
	exit;
}	


		switch ($action) {
			
			case "view" :
				print_from($IDPuntoVenta,$Fecha);
			break;
			
			default :
				print_from("");
			break;
		
		} // End switch


 
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($IDPuntoVenta="", $Fecha=""){
 Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA, $ReteIVA, $ReteICA, $ReteFuente, $FechaDesde, $FechaHasta, $Items;
	
 
?>
	
	<table width="100%">
		
		<tr>
		<td>
				<table width='100%' align='left' border="0" cellspacing="0" cellpadding="2" class="bordertable">
					<form action="./" name="frmPuntoVenta" method="post" >
						<tr>
							<td valign="middle"><img src="images/calendar_edit.png" border="0" alt=""></td>
							<td  align='left' valign='middle' class="nav">
							
								Desde	<input  type="text" name="FechaDesde" class="input" value="<?=$FechaDesde?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaDesde,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
							</td>
							<td align="left" valign="middle" class="nav">
								
								Hasta	<input  type="text" name="FechaHasta" class="input" value="<?=$FechaHasta?>" size="10">

								<script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmPuntoVenta.FechaHasta,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
									//-->
								</script>
							</td>
							<td  align='left' valign='middle' class="nav"><img src="images/house.png" border='0'  alt=''></td>
							<td  align='left' valign='middle' class="nav">Cantidad de Pares</td>
							<td  align='left' valign='middle' class="nav">
                            	<select name="Items" id="Items">
                                	<option value="">Seleccione Cantidad</option>
                                    <option value="2" <?php if($Items==2){?> selected <?php } ?>>2 o mas Pares</option>
                                    <option value="3" <?php if($Items==3){?> selected <?php } ?>>3 o mas Pares</option>
                                    <option value="4" <?php if($Items==4){?> selected <?php } ?>>4 o mas Pares</option>
                                    <option value="5" <?php if($Items==5){?> selected <?php } ?>>Mas de 5 Pares</option>
                                </select>
                            
                            </td>
							<td align="left" valign="middle" class="nav">Puntos de Venta	<select name="IDPuntoVenta" onChange="document.frmPuntoVenta.submit();" >
									<option value="">Seleccione Un Punto de Venta</option><% 								
								$qry_punto = db_query("SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre ");
								while($punto = db_fetch_object($qry_punto)){
									 echo "<option value=$punto->IDPuntoVenta ";if($IDPuntoVenta == $punto->IDPuntoVenta ) echo "selected"; echo ">&nbsp;&nbsp;$punto->Nombre</option>";
								}
							%>
								</select> <input type="hidden" name="mod" value="ConsultaFacturaPares"><input type="hidden" name="action" value="view"></td>
							<td align="left" valign="middle" class="nav"><input type="submit" value="Ver Reporte" name="submit" class="submit"></td>
						</tr>
						<tr>
						  <td valign="middle">&nbsp;</td>
						  <td colspan="2"  align='left' valign='middle' class="nav">Solo facturas con tarjeta de regalo</td>
						  <td colspan="2"  align='left' valign='middle' class="nav">
                          <input type="radio" name="TarjetaRegalo" value="S" <?php if($_POST[TarjetaRegalo]=="S") echo "checked"; ?> >
						    Si
                              <input type="radio" name="TarjetaRegalo" value="N" <?php if($_POST[TarjetaRegalo]=="N" || $_POST[TarjetaRegalo]=="") echo "checked"; ?> >
                          No </td>
						  <td  align='left' valign='middle' class="nav">Proveedor</td>
						  <td align="left" valign="middle" class="nav"><select name="IDProveedor" id="IDProveedor"  >
						    <option value="">Seleccione Proveedor</option>
						    <?php 								
								$qry_proov = db_query("SELECT * FROM Proveedor ORDER BY Nombre ");
								while($proveedor = db_fetch_object($qry_proov)){
									 echo "<option value=$proveedor->IDProveedor ";if($_POST["IDProveedor"] == $proveedor->IDProveedor ) echo "selected"; echo ">&nbsp;&nbsp;$proveedor->Nombre</option>";
								}
							?>
					      </select></td>
						  <td align="left" valign="middle" class="nav">&nbsp;</td>
					  </tr>
					</form>
				</table>
			</td>
		</tr>
		
		<br>
		<br>
		<?
		if(!empty($FechaDesde) && !empty($FechaHasta)  ){
				if (!empty($IDPuntoVenta))
					$condicion_punto=" AND DF.IDPuntoVenta = '$IDPuntoVenta' ";
					
				if (!empty($_POST["IDProveedor"])):
					if (!empty($IDPuntoVenta))
						$condicion_punto = " AND IDPuntoVenta = '$IDPuntoVenta' ";
						
					$sql_ref = "Select IDPuntoVentaReferencia 
								From Referencia R, PuntoVentaReferencia PVR 
								Where R.IDReferencia = PVR.IDReferencia
								AND Publicar = 'S'
								AND IDProveedor = '".$_POST["IDProveedor"]."'" . 
								$condicion_punto;
					$r_ref = db_query($sql_ref);
					while($row_ref = db_fetch_array($r_ref)):
						$array_pto_ref[]=$row_ref["IDPuntoVentaReferencia"];
					endwhile;
					if(count($array_pto_ref)>0):
						$sql_codif_esp="Select * From CodificacionEspecifica Where IDPuntoVentaReferencia in (" . implode(",",$array_pto_ref) . ")";
						$r_codif_esp = db_query($sql_codif_esp);
						while($row_codif_esp = db_fetch_array($r_codif_esp)):
							$array_codif_esp[]=$row_codif_esp["IDCodificacionEspecifica"];	
						endwhile;
						if(count($array_codif_esp)>0):
							$condicion_punto.= " and IDCodificacionEspecifica in (" . implode(",",$array_codif_esp) . ")";
						endif;
						
					endif;
				endif;	
			
				/*
				$sql = " 
				SELECT count(DF.IDFactura) as TotalProductos, F.* 
				FROM  DetalleFactura DF, Factura F 
				WHERE DF.IDFactura = F.IDFactura 
				AND DF.IDPuntoVenta = '$IDPuntoVenta' 
				AND DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) >= DATE_FORMAT('$FechaDesde','%Y-%c-%d' ) 
				AND DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) <= DATE_FORMAT('$FechaHasta','%Y-%c-%d' )
				GROUP BY DF.IDFactura,DF.IDPuntoVenta 
				HAVING count(DF.IDFactura) >= ".(int)$Items." 
				ORDER BY F.FechaFactura DESC " ;				
				*/
				
				
				// selecciono las faturas que cumplen la condicion de fecha
				$sql_factura = " 
				SELECT IDFactura 
				FROM  Factura F 
				WHERE DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) >= DATE_FORMAT('$FechaDesde','%Y-%c-%d' ) 
				AND DATE_FORMAT( F.FechaFactura,'%Y-%c-%d' ) <= DATE_FORMAT('$FechaHasta','%Y-%c-%d' )				
				ORDER BY F.FechaFactura DESC " ;				
				
				
				// selecciono las facturas que cumplen la condicion de items
				
				if ($_POST["TarjetaRegalo"]=="S"){
					$condicion_tarjeta = " and CodigoTarjeta <> '' ";
				}
				
				$sql = " 
				SELECT count(DF.IDFactura) as TotalProductos,DF.*
				FROM  DetalleFactura DF
				WHERE 								
				IDFactura in (".$sql_factura.")
				$condicion_punto
				$condicion_tarjeta
				GROUP BY DF.IDFactura,DF.IDPuntoVenta 
				HAVING count(DF.IDFactura) >= ".(int)$Items." 
				" ;				
				
				
				
				
				$query = db_query( $sql );			
			
		?>
		<tr>
		<td>
				&nbsp;&nbsp;&nbsp;&nbsp;  <img src="images/book_go.png" border="0" alt="">&nbsp; 
				<a href="./?mod=ReportePagos&Fecha=<?=$Fecha?>&IDPuntoVenta=<?=$IDPuntoVenta?>" class="menuppal">
					Ver informe formas de pago
				</a>
				<br>&nbsp; 
			<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp; 
							
						Reporte Almacen : <?=get_field("PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta) ?>&nbsp; &nbsp; Fecha: <?=formatofecha( $Fecha )?>
					</td>
				</tr>
				<?
					
					//print_r( $array_banco );
					//Seleccionar Bancos
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td class="titlemedium" nowrap>Fecha</td>
						  <td class="titlemedium" nowrap>Almac&eacute;n</td>
						  <td class="titlemedium" nowrap>Vendedor</td>
										<td class="titlemedium" nowrap>No. Factura</td>
										<td class="titlemedium" align="center" nowrap>Cantidad</td>
										<td class="titlemedium" align="center" nowrap>Valor Factura</td>
										<td class="titlemedium" align="center" nowrap> Cliente</td>
										<td class="titlemedium" align="center" nowrap> Cedula</td>
							</tr>
						<?
						while ($r = db_fetch_object( $query ))
						{ 
							$sql_factura=db_query("Select * From Factura Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVenta."'");
							$row_factura=db_fetch_object($sql_factura);
							
							//print_r( $valor );
							$class = repetition()?"row2":"row1";
							//print_r($valor);
						?>
						<tr>
						  <td class="<?=$class?>" align="center" nowrap><?=$row_factura->FechaFactura;?></td>
						  <td class="<?=$class?>" align="center" nowrap><? echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta); ?></td>
						  <td class="<?=$class?>" align="center" nowrap><? echo get_field("Empleado","Nombre","IDEmpleado",$row_factura->IDEmpleado) . " " . get_field("Empleado","Apellidos","IDEmpleado",$row_factura->IDEmpleado); ?></td>
										<td class="<?=$class?>" align="center" nowrap><a target="_blank" href="?mod=Factura&action=edit&idpunto=<?=$r->IDPuntoVenta;?>&id=<?=$r->IDFactura ?>"><?=$row_factura->NumeroFactura; ?></a></td>
										<td class="<?=$class?>" align="center" nowrap><? echo $r->TotalProductos ?></td>
										<td class="<?=$class?>" align="center" nowrap>$
										  <?=number_format($row_factura->ValorTotal); ?>
										</td>
										<td class="<?=$class?>" align="center" nowrap><? echo get_field("Cliente","Nombre","IDCliente",$row_factura->IDCliente) . " " . get_field("Cliente","Apellido","IDCliente",$row_factura->IDCliente); ?></td>
										<td class="<?=$class?>" align="right" nowrap><? echo get_field("Cliente","Cedula","IDCliente",$row_factura->IDCliente); ?></td>
						</tr>
						
						<?
						}//end foreach( $r_facturas as $key => $valor )
						?>
							
						<tr>
							<td class="titlemedium" colspan="8" align="right" nowrap>TOTALES</td>
							</tr>
											
					</table>
					<br><br>
					<table width="100%" border="0" cellspacing="1" cellpadding="1">	
						<tr>
							<td class="maintitle" valign="middle">&nbsp;</td>
						</tr>
						
						<tr>
							<td  valign="middle">&nbsp;</td>
						</tr>	
							
					</table>
				</td>
			</tr>
		</form>
	
		</table>
	</td>
	</tr>
	<% 
	 } // END if(!empty($IDEmpresa))
	%>
	</table>
	<%						
}// Enf function print()	

%>
</body>
