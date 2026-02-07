

<?php
	$TitleMod ="Bonos Clientes";
	
	$Table = "Cliente";
	$TableJoin = "Factura";
	$Key = "IDFactura";
	$MOD = "GenerarFactura";
	$m = "BonosCliente";
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
<form name="frmcliente" method="post" enctype="multipart/form-data" action="<?php echo $PHP_SELF?>" onsubmit="disable(this);">
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
		<input type="submit" class="button" name="enviar" value="<?php echo $submit_caption?>">
		<input type="hidden" value="<?php echo $newmode?>" name="action">
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
	
	$sql =  "SELECT * FROM Factura WHERE IDCliente = '".$idCliente."' ORDER BY FechaFactura DESC";
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
	<form name="frmcliente" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="disable(this);return EvaluaReg(this,Check)"<?php }?>>
	
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="800">
	
	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>
			<span class="gen">
				<?php echo $title?>
			</span>
		</td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>



<?php
if($rows > 0){
?>

			
            
            					<table width="100%" border="0" cellspacing="2" cellpadding="0" >
									<tr>
										<td colspan="7" align="left" class="rowform">&Uacute;ltimas compras del cliente</td>
									</tr>
									<tr>
										<td align="center" class="rowform">Nro Factura</td>
										<td align="center" class="rowform">Referencias</td>
										<td align="center" class="rowform">Fecha</td>
										<td align="center" class="rowform">PuntoVenta</td>
										<td align="center" class="rowform">Items</td>
										<td align="center" class="rowform">Valor Factura</td>
										<td align="center" class="rowform">Ver Detalle</td>
									</tr>
									<?php
									while( $r_factura = db_fetch_object( $qid ) )
									{
										$class = repetition()?"row1":"row2";
									?>
									<tr>
										<td align="center" class="<?php echo $class?>"><?php echo $r_factura->NumeroFactura;?></td>
										<td align="center" class="<?php echo $class?>">
                                        <?php 
										$sql_ref="Select * from DetalleFactura Where IDFactura = '".$r_factura->IDFactura."' and IDPuntoVenta = '".$r_factura->IDPuntoVenta."'";
										$result_ref=db_query($sql_ref);
										while($row_ref = db_fetch_array($result_ref)):
											$IDPtoRef=get_field( "CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_ref["IDCodificacionEspecifica"] ); 
											$IDRef=get_field( "PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",$IDPtoRef ); 
											echo get_field( "Referencia","Nombre","IDReferencia",$IDRef) . "<br>"; 
										endwhile;
										
										?>
                                        </td>
										<td align="center" class="<?php echo $class?>"><?php echo formatofecha( substr( $r_factura->FechaFactura, 0, 10) );?></td>
										<td align="center" class="<?php echo $class?>"><?php echo get_field( "PuntoVenta","Nombre","IDPuntoVenta",$r_factura->IDPuntoVenta );?></td>
										<td align="center" class="<?php echo $class?>"><?php echo get_field("DetalleFactura","COUNT( IDDetalleFactura )","IDFactura",$r_factura->IDFactura."' AND IDPuntoVenta = '$r_factura->IDPuntoVenta");?></td>
										<td align="right" class="<?php echo $class?>"><?php echo number_format($r_factura->ValorTotal, 2 );?></td>
										<td align="center" class="<?php echo $class?>"><a href="?mod=Factura&action=edit&id=<?php echo $r_factura->IDFactura?>&IDPuntoVenta=<?php echo $r_factura->IDPuntoVenta?>" target="_blank"><img src="admin/images/attach.png" border="0"></a></td>
									</tr>
									<?php
									}//end while
									?>
			</table>
                                
                                
                                
<?php } ?>


</form>
	<?php
}// End function print_formcliente()



?></BODY></HTML> 
