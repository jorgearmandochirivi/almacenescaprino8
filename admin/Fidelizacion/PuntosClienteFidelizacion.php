<body> <?

$TitleMod ="PuntosCliente";

$Table = "PuntosClienteFidelizacion";
$TableJoin = "Cliente";
$Key = "IDPuntosCliente";
$MOD = "PuntosClienteFidelizacion";
$m = "Fidelizacion";

		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			
			case "cargarpuntos" :
				$frm= $HTTP_POST_VARS;
				if (empty($frm["Observaciones"])){
					echo "<script>alert('Por favor digite en observaciones el motivo por el cual se estan asignando los puntos');</script>";
					echo "<script>location.href='?mod=PuntosClienteFidelizacion&idCliente=" . $frm["IDCliente"] . "';</script>";
				}else{
					//Verifico que el numero de factura exista y pertenezca al punto de venta y sea del cliente
					$sql_verifica_factura="Select * from Factura Where NumeroFactura = '".$frm["IDFactura"]."' and IDPuntoVenta = '".$frm["IDPuntoVenta"]."' and IDCliente = '".$frm["IDCliente"]."'";
					$qry_factura=db_query($sql_verifica_factura);
					if(db_num_rows($qry_factura)>0){
						$row_factura=db_fetch_array($qry_factura);
						$frm["IDFactura"] = $row_factura[IDFactura];
						$frm["FechaTrCr"] = date("Y-m-d");
						$frm["UsuarioTrCr"] = $datos["IDUsuario"];
						$sql_insert = " INSERT INTO PuntosClienteFidelizacion (IDCliente, IDPuntoVenta, IDFactura, IDEmpleado, Puntos, FechaVencimiento, Observaciones, FechaTrCr, UsuarioTrCr) VALUES ('" . $frm["IDCliente"] . "','" . $frm["IDPuntoVenta"] . "','" . $frm["IDFactura"] . "','". $frm["IDEmpleado"] ."','" . $frm["Puntos"] . "','" . $frm["FechaVencimiento"] . "','" . $frm["Observaciones"] . "','" . $frm["FechaTrCr"] . "','" . $frm["UsuarioTrCr"] . "')  ";				
						db_query( $sql_insert );
						$frm["FechaFactura"]=date("Y-m-d H:i:s");
						$frm["id"]=$frm["IDFactura"];
						$frm["idpunto"]=$frm["IDPuntoVenta"];
						genera_bonos($frm["IDCliente"],$frm);
						echo "<script>alert('Puntos agregados con exito');';</script>";
						echo "<script>location.href='?mod=PuntosClienteFidelizacion&idCliente=" . $frm["IDCliente"] . "';</script>";
					}
					else{
						echo "<script>alert('El numero de factura no existe o no pertenece al punto de venta. Verifique por favor');location.href='?mod=PuntosClienteFidelizacion&idCliente=$frm[IDCliente]';</script>";
					}
				}
			break;
			case "modificafecha":
				$sql_cambia_fecha="Update PuntosClienteFidelizacion Set FechaVencimiento = '".$_POST[FechaVencimientoPunto]."' Where IDPuntosClienteFidelizacion = '".$_POST[IDPuntosClienteFidelizacion]."' and IDCliente = '".$_POST[IDCliente]."'";
				db_query($sql_cambia_fecha);
				echo "<script>alert('Fecha Modificada con exito');location.href='?mod=PuntosClienteFidelizacion&idCliente=$_POST[IDCliente]';</script>";				
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


	 	$sql =  "SELECT * FROM $Table WHERE IDCliente = '" . $idCliente . "' ORDER BY IDPuntosClienteFidelizacion ASC ";
	 	
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
	include("Fidelizacion/menutabCliente.php");
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
            <tr class="row2">
              <td>Empleado que asigna</td>
              <td><span class="col2"><? echo formpopup("Empleado WHERE IDPuntoVenta >0 ","Nombre","Nombre","IDEmpleado",$frm[IDEmpleado],"input\" id=\"Empleado"); ?></span></td>
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
                	 <input type=text size=25 class=input   name=FechaVencimiento id=FechaVencimiento value="<?php echo date("Y-m-d"); ?>">
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
                    <input type=hidden name=mod value="PuntosClienteFidelizacion">
                    <input type=submit name=submit value="Cargar Puntos" class="submit">
				</td>
			</tr>        
       	</table>
  	</td>
 </tr>
 </form>
 </table>
 
 
 
 
 <?php if ($_GET[accion]=="edit_fecha_vencimiento"){ 
 // consulto los datos de los puntos
 $sql_puntos="Select * From PuntosClienteFidelizacion Where IDPuntosClienteFidelizacion = '".$_GET[IDPuntosClienteFidelizacion]."'";
 $qry_puntos=db_query($sql_puntos);
 $row_puntos=db_fetch_array($qry_puntos);
 ?>
 
 <table cellpadding=1 cellspacing=0 class=bordertable align=left width="100%" >
<form name="frmFecha" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" >

	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;Modificar Fecha vencimiento puntos</td>
	</tr>
	<tr>
	<td>
		<table width="100%" border=0 cellspacing=1 cellpadding=1 class=texto>
			<tr class=row2>
				<td width="200"> 
            		Puntos 
             	</td>
                <td>
                	 <input type=text size=25 class=input   name=Puntos id=Puntos value="<?php echo $row_puntos["Puntos"]; ?>" readonly>
                </td>
            </tr>  
            <tr class=row2>
				<td width="200"> 
            		Fecha de Vencimiento
             	</td>
                <td>
                	 <input type=text size=25 class=input   name=FechaVencimientoPunto id=FechaVencimientoPunto value="<?php echo $row_puntos["FechaVencimiento"]; ?>">
                     <script language="JavaScript1.2">
						<!--
							if (!document.layers)
								document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frmFecha.FechaVencimientoPunto,\"yyyy-mm-dd\")' width=16 height=16 border=0>")							
						//-->
					</script>
                </td>
            </tr> 
            <tr>
				<td colspan=3 align=center class=row2>
                    <input type=hidden name=IDCliente id=IDCliente value="<?=$idCliente ?>">
                    <input type=hidden name=IDPuntosClienteFidelizacion id=IDPuntosClienteFidelizacion value="<?=$_GET[IDPuntosClienteFidelizacion] ?>">
                    <input type=hidden name=action value="modificafecha">
                    <input type=hidden name=mod value="PuntosClienteFidelizacion">
                    <input type=submit name=submit value="Modificar fecha" class="submit">
				</td>
			</tr>        
       	</table>
  	</td>
 </tr>
 </form>
 </table>
  <?php } ?>
 
 
<br><br><br><br>
<table width="100%" cellpadding=0 cellspacing=0 align=left class=bordertable>

<tr>
	<td>
    
            <table>
            	<tr>
                	<td class=rowform nowrap bgcolor=#DBEAF5>
                    	Total Puntos Acumulados
                    </td>
                	<td><b>
                    	<?php 
						$sql_total_puntos = "Select SUM(Puntos) as Total_Puntos From PuntosClienteFidelizacion Where IDCliente = '".$idCliente."' and IDReglaPunto <> 0";  
						$qry_total_puntos = db_query($sql_total_puntos);
						$row_total_puntos = db_fetch_array($qry_total_puntos);
						echo $row_total_puntos["Total_Puntos"];
						?>
                        </b>
                    </td>
                
            	
                	<td class=rowform nowrap bgcolor=#DBEAF5>
                    Total Puntos Redimidos
                    </td>
                	<td>
                    	<b>
                    	<?php 
						$sql_total_puntos_redimidos = "Select SUM(PuntosRedimidos) as Total_Puntos_Redimidos From PuntosClienteFidelizacion Where IDCliente = '".$idCliente."'";  
						$qry_total_puntos_redimidos = db_query($sql_total_puntos_redimidos);
						$row_total_puntos_redimidos = db_fetch_array($qry_total_puntos_redimidos);
						echo $row_total_puntos_redimidos["Total_Puntos_Redimidos"];
						?>
                        </b>
                    </td>
                
            	
                	<td class=rowform nowrap bgcolor=#DBEAF5>
                    Total Puntos sin Redimir
                    </td>
                	<td>
                    	<b>
                    	<?php 
						$sql_total_puntos_sin_redimir = "Select SUM(Puntos) as Total_Puntos_Redimidos From PuntosClienteFidelizacion Where IDCliente = '".$idCliente."' and Redimido = 'N'";  
						$qry_total_puntos_sin_redimir = db_query($sql_total_puntos_sin_redimir);
						$row_total_puntos_sin_redimir = db_fetch_array($qry_total_puntos_sin_redimir);
						echo $row_total_puntos_sin_redimir["Total_Puntos_Redimidos"];
						?>
						</b>
                    </td>
                </tr>
                
                
            </table>
    
    
    </td>
</tr>



	
    
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
						<td class=rowform nowrap bgcolor=#DBEAF5>Nombre Regla Aplicada</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Descripcion Regla</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Observacion Regla</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Fecha Creacion </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Fecha Vencimiento </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Puntos </td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Redimidos </td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Total Final</td>
					</tr>

<? 
$contador=0;
while($r = db_fetch_object($result)){ 
	$redimido_punto="";
	if (!strpos($r->DescripcionRegla,"excedente")){
?>
	<tr>
        <td nowrap class=row1><a target="_blank" href="?mod=Factura&action=edit&idpunto=<?=$r->IDPuntoVenta?>&id=<?=$r->IDFactura ?>"><? echo get_field( "Factura","NumeroFactura","IDFactura",$r->IDFactura);  ?></a></td>
        <td nowrap class=row1><? echo $array_puntos[ $r->IDPuntoVenta ]["Nombre"] ?></td>
        <td nowrap class=row1><? echo $r->NombreRegla ?></td>
        <td nowrap class=row1><? echo $r->DescripcionRegla ?></td>
        <td nowrap class=row1><? echo $r->ObservacionesRegla ?></td>
        <td nowrap class=row1><? echo $r->FechaTrCr ?></td>
        <td nowrap class=row1>
			<? echo $r->FechaVencimiento ?> 
            <?php if ($r->Redimido=="N"){ ?>
            	<a href="?mod=PuntosClienteFidelizacion&idCliente=<?php echo $r->IDCliente; ?>&IDPuntosClienteFidelizacion=<?php echo $r->IDPuntosClienteFidelizacion; ?>&accion=edit_fecha_vencimiento">cambiar fecha</a>
            <?php } ?>
        </td>
        <td nowrap class=row1 style="background-color:#0F3" align="right" ><b><? echo $r->Puntos?></b></td>
        <td nowrap class=row1 align="center"><? echo $redimido_punto=$r->Redimido ?></td>
        <td nowrap class=row1 style=" background-color: #0C9" align="right"><b> 
				<?php 
				if ($contador==0){
					echo $r->Puntos;	
					$disponibles=(int)$r->Puntos;
				}else{					
					echo $puntos=(int)$r->Puntos+(int)$disponibles;
					$disponibles=(int)$puntos;
				}
				?>
           </b>     
        </td>
	</tr>
    <?php } ?>
    
	<?php if ($redimido_punto=="S" && (int)$disponibles>=350){ ?>
	<tr>
        <td nowrap class=row1 style=" background-color:#FE9EAD"><a target="_blank" href="?mod=Factura&action=edit&idpunto=<?=$r->IDPuntoVenta?>&id=<?=$r->IDFactura ?>"><? echo $r->IDFactura ?></a></td>
        <td nowrap class=row1 style=" background-color:#FE9EAD"><? echo $array_puntos[ $r->IDPuntoVenta ]["Nombre"] ?></td>
        <td nowrap class=row1 style=" background-color:#FE9EAD">redencion bono</td>
        <td nowrap class=row1 style=" background-color:#FE9EAD">redencion bono</td>
        <td nowrap class=row1 style=" background-color:#FE9EAD"><? echo $r->ObservacionesRegla ?></td>
        <td nowrap class=row1 style=" background-color:#FE9EAD"><? echo $r->FechaTrCr ?></td>
        <td nowrap class=row1 style=" background-color:#FE9EAD"><? echo $r->FechaVencimiento ?></td>
        <td nowrap class=row1 style=" background-color:#FE9EAD" align="right" ><b>-
			<? 
			if ($contador==0)
				echo $puntos_bono_generado=(int)$r->PuntosRedimidos;
			else
				echo $puntos_bono_generado=(int)$puntos-((int)$r->Puntos-($r->PuntosRedimidos));
			?>
            </b>
            </td>
        <td nowrap class=row1 align="center"><? echo $r->Redimido ?></td>
        <td nowrap class=row1 style=" background-color: #0C9" align="right"> <b>
				<?php echo $disponibles=(int)$r->Puntos-($r->PuntosRedimidos); ?>
           </b>     
        </td>
	</tr>
    <?php }elseif($redimido_punto=="S" && (int)$disponibles<350){
		$acumula_redimido+=$r->PuntosRedimidos;
			
	} ?>

    
    
    
<?	
	
	
?>
<!--  	
<tr>
						<td nowrap class=row1><a target="_blank" href="?mod=Factura&action=edit&idpunto=<?=$r->IDPuntoVenta?>&id=<?=$r->IDFactura ?>"><? echo $r->IDFactura ?></a></td>
						<td nowrap class=row1><? echo $array_puntos[ $r->IDPuntoVenta ]["Nombre"] ?></td>
						<td nowrap class=row1><? echo $r->NombreRegla ?></td>
						<td nowrap class=row1><? echo $r->DescripcionRegla ?></td>
						<td nowrap class=row1><? echo $r->ObservacionesRegla ?></td>
						<td nowrap class=row1><? echo $r->FechaTrCr ?></td>
						<td nowrap class=row1><? echo $r->FechaVencimiento ?></td>
                        <td nowrap class=row1 style="background-color:#0F3" ><b><? echo $r->Puntos?></b></td>
                        <td nowrap class=row1><? echo $r->Redimido ?></td>
                        <td nowrap class=row1 <?php if ($r->PuntosRedimidos!="0"){ ?> style=" background-color:#FE9EAD"  <?php } ?>> <?php if ($r->PuntosRedimidos!="0"){ ?> <b>-<? echo $r->PuntosRedimidos ?></b> <?php } ?></td>
					</tr>
-->                    
<? 
	$contador++;
} // END for
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