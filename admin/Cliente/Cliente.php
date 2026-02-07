

<?php
	$TitleMod ="Puntos Clientes";
	
	$Table = "Cliente";
	$TableJoin = "Factura";
	$Key = "IDFactura";
	$MOD = "GenerarFactura";
	$m = "PuntosCliente";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 0)
{
		//echo $action;
		switch (nvl($action)) {
            
            
			case "list" :	
				$sql = make_qry_string($HTTP_GET_VARS);
				list_r($sql);
			break;
			case "mostrar":
				$sql_cliente = "SELECT * FROM Cliente WHERE Cedula = '$cedula' ORDER BY IDCliente ASC";
				$qry_cliente = db_query( $sql_cliente );
				$r_cliente = db_fetch_object( $qry_cliente );

				if( db_num_rows( $qry_cliente ) > 0 )
				{
					mostrarcedula("mostrar","Buscar Cliente");
					print_form($r_cliente->IDCliente,"Ver Cliente");
				}//end if
				else
				{
					mostrarcedula("mostrar","Buscar Cliente");
				}//end else
				
                
				
			break;
			default : 
				mostrarcedula("mostrar","Buscar Cliente");
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col1");
	

	
/*******************************************************************************************
		funtcion mostrarcedula
*******************************************************************************************/

function mostrarcedula($newmode,$submit_caption){
?>
	
<br>
<form name="frmcliente" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" onsubmit="disable(this);">
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="800">
	
	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b><span class="gen">C&eacute;dula del Cliente </span></td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table align="center" width="800" cellpadding="0" cellspacing="1" border="0" class="forumline">
  
  <tr>
	<td class="col1" align="center" valign="middle">Digite la cedula por favor</td>
	<td class="col2" >
		<input type="text" class="tbox" name="cedula">
	</td>
  </tr>
  
  <tr>
	<td class="col2list" align="center" valign="middle" colspan="2">
		<input type="submit" class="button" name="enviar" value="<?=$submit_caption?>">
		<input type="hidden" value="<?=$newmode?>" name="action">
	</td>
  </tr>
</table>
</form>
<?php
}//end	mostrar($newmode,$submit_caption)


/*******************************************************************************************
		funtcion Print_formCliente
*******************************************************************************************/
function print_form($idCliente,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$cedula,$array_gustos,$array_deportes,$array_hobbies,$array_musica;
	
	$sql =  "SELECT * FROM PuntosClienteFidelizacion WHERE IDCliente = '" . $idCliente . "' ";
	$qid = db_query( $sql );


	$sql_puntos = " SELECT * FROM PuntoVenta ORDER BY IDCiudad, Nombre  ";
	$qry_puntos = db_query( $sql_puntos );
	while( $r_puntos = db_fetch_array( $qry_puntos ) )
		$array_puntos[ $r_puntos["IDPuntoVenta"] ] = $r_puntos;

	$rows = db_num_rows( $qid );
	
?>
	<script>
var Check = new Array('Cedula','Nombre','Apellido');
</script>
<br>
	<form name="frmcliente" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="disable(this);return EvaluaReg(this,Check)"<?php }?>>
	
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="800">
	
	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				<?=$title?>
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

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

<?php
if($rows > 0){
?>

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

<?php 
$contador=0;
while($r = db_fetch_object($qid)){
	$redimido_punto="";
	if (!strpos($r->DescripcionRegla,"excedente")){
?>
	<tr>
        <td nowrap class=row1><a target="_blank" href="?mod=Factura&action=edit&idpunto=<?=$r->IDPuntoVenta?>&id=<?=$r->IDFactura ?>"><?php echo $r->IDFactura ?></a></td>
        <td nowrap class=row1><?php echo $array_puntos[ $r->IDPuntoVenta ]["Nombre"] ?></td>
        <td nowrap class=row1><?php echo $r->NombreRegla ?></td>
        <td nowrap class=row1><?php echo $r->DescripcionRegla ?></td>
        <td nowrap class=row1><?php echo $r->ObservacionesRegla ?></td>
        <td nowrap class=row1><?php echo $r->FechaTrCr ?></td>
        <td nowrap class=row1><?php echo $r->FechaVencimiento ?></td>
        <td nowrap class=row1 style="background-color:#0F3" align="right" ><b><?php echo $r->Puntos?></b></td>
        <td nowrap class=row1 align="center"><?php echo $redimido_punto=$r->Redimido ?></td>
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
        <td nowrap class=row1 style=" background-color:#FE9EAD"><a target="_blank" href="?mod=Factura&action=edit&idpunto=<?=$r->IDPuntoVenta?>&id=<?=$r->IDFactura ?>"><?php echo $r->IDFactura ?></a></td>
        <td nowrap class=row1 style=" background-color:#FE9EAD"><?php echo $array_puntos[ $r->IDPuntoVenta ]["Nombre"] ?></td>
        <td nowrap class=row1 style=" background-color:#FE9EAD">redencion bono</td>
        <td nowrap class=row1 style=" background-color:#FE9EAD">redencion bono</td>
        <td nowrap class=row1 style=" background-color:#FE9EAD"><?php echo $r->ObservacionesRegla ?></td>
        <td nowrap class=row1 style=" background-color:#FE9EAD"><?php echo $r->FechaTrCr ?></td>
        <td nowrap class=row1 style=" background-color:#FE9EAD"><?php echo $r->FechaVencimiento ?></td>
        <td nowrap class=row1 style=" background-color:#FE9EAD" align="right" ><b>-
			<?php 
			if ($contador==0)
				echo $puntos_bono_generado=(int)$r->PuntosRedimidos;
			else
				echo $puntos_bono_generado=(int)$puntos-((int)$r->Puntos-($r->PuntosRedimidos));
			?>
            </b>
            </td>
        <td nowrap class=row1 align="center"><?php echo $r->Redimido ?></td>
        <td nowrap class=row1 style=" background-color: #0C9" align="right"> <b>
				<?php echo $disponibles=(int)$r->Puntos-($r->PuntosRedimidos); ?>
           </b>     
        </td>
	</tr>
    <?php }elseif($redimido_punto=="S" && (int)$disponibles<350){
		$acumula_redimido+=$r->PuntosRedimidos;
			
	} ?>

    
    
    
<?php 
	
	
?>
<!--  	
<tr>
						<td nowrap class=row1><a target="_blank" href="?mod=Factura&action=edit&idpunto=<?=$r->IDPuntoVenta?>&id=<?=$r->IDFactura ?>"><?php echo $r->IDFactura ?></a></td>
						<td nowrap class=row1><?php echo $array_puntos[ $r->IDPuntoVenta ]["Nombre"] ?></td>
						<td nowrap class=row1><?php echo $r->NombreRegla ?></td>
						<td nowrap class=row1><?php echo $r->DescripcionRegla ?></td>
						<td nowrap class=row1><?php echo $r->ObservacionesRegla ?></td>
						<td nowrap class=row1><?php echo $r->FechaTrCr ?></td>
						<td nowrap class=row1><?php echo $r->FechaVencimiento ?></td>
                        <td nowrap class=row1 style="background-color:#0F3" ><b><?php echo $r->Puntos?></b></td>
                        <td nowrap class=row1><?php echo $r->Redimido ?></td>
                        <td nowrap class=row1 <?php if ($r->PuntosRedimidos!="0"){ ?> style=" background-color:#FE9EAD"  <?php } ?>> <?php if ($r->PuntosRedimidos!="0"){ ?> <b>-<?php echo $r->PuntosRedimidos ?></b> <?php } ?></td>
					</tr>
-->                    
<?php 
	$contador++;
} // END for
?>
<tr>
</tr>	

<?php
}// End if$rows
else
	echo "<tr><td><br><br><span class=subtitle><b>Este cliente no tiene puntos de fidelizaci&oacute;n </b></span></td></tr>";
?>
	
</table>



</form>
	<?php
}// End function print_formcliente()



?></BODY></HTML> 
