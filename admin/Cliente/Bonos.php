<script>
function ver_datos(valor){
	
	document.getElementById('datos_cliente'+valor).style.display = 'block';
}
</script>

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
	global $IDPuntoVenta;
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


<table width="800" border=0 cellspacing=1 cellpadding=0 align="center">
	<tr>
	  <td colspan="6" nowrap="nowrap" bgcolor="#DBEAF5" class="rowform">BONOS PROXIMOS A VENCERSE</td>
  </tr>
	<tr>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">NUMERO BONO</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Cliente</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Valor</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Fecha Generaci&oacute;n</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5"> Estado </td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Fecha Vencimeinto</td>
    </tr>
<?php
$sql_bonos_prox_vence = "SELECT * FROM BonoFidelizacion WHERE FechaVencimiento >= CURDATE() AND  IDPuntoVenta = '". $IDPuntoVenta ."' AND Estado = 'D' AND FechaVencimiento <= DATE_ADD( CURDATE( ) , INTERVAL 1 MONTH ) ";
$qry_bonos_prox_vence = db_query( $sql_bonos_prox_vence );
while($r = db_fetch_object( $qry_bonos_prox_vence )){
	$conta_bono++;
?>	
	<tr>
	  <td height="39" nowrap="nowrap" class="row1">
		BONO
		<!--<a href="Movimiento/popBono.php?id=<?php echo $r->IDBonoFidelizacion;  ?> " target="_blank"><?php echo $r->IDBonoFidelizacion ?></a>-->
	</td>
	  <td nowrap="nowrap" class="row1">
      <!--
	  <a href="#" onclick="ver_datos(<?php echo $conta_bono; ?>)">
		-->
		<a href="#"">
	  	<?php echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente) . " " .get_field("Cliente","Apellido","IDCliente",$r->IDCliente) ?>
        [+]
      </a>  
      
      <div id="datos_cliente<?php echo $conta_bono; ?>" style="display:none">
      	<table>
        	<tr>
        	  <td>-</td>
        	  <td>Cedula</td>
        	  <td><?php echo get_field("Cliente","Cedula","IDCliente",$r->IDCliente) ?></td>
      	  </tr>
        	<tr>
        	  <td>-</td>
        	  <td>Telefono</td>
        	  <td><?php echo get_field("Cliente","Telefono","IDCliente",$r->IDCliente) ?></td>
   	      </tr>
        	<tr>
        	  <td>-</td>
        	  <td>Celular</td>
        	  <td><?php echo get_field("Cliente","Celular","IDCliente",$r->IDCliente)  ?></td>
   	      </tr>
        	<tr>
        	  <td>-</td>
        	  <td>Email</td>
        	  <td><?php echo get_field("Cliente","Email","IDCliente",$r->IDCliente)  ?></td>
   	      </tr>
        	<tr>
        	  <td>-</td>
        	  <td>Numero de tarjeta</td>
        	  <td><?php echo get_field("Cliente","NumeroTarjeta","IDCliente",$r->IDCliente) ?></td>
   	      </tr>
        </table>
      
      
      </div>
      
      </td>
	  <td nowrap="nowrap" class="row1">$<?php echo number_format($r->Valor,2) ?></td>
	  <td nowrap="nowrap" class="row1"><?php echo $r->Fecha ?></td>
	  <td nowrap="nowrap" class="row1"><?php if($r->Estado=="D"){ ?>
	    Disponible
	    <?php }
		elseif($r->Estado=="V"){ ?>
	    Vencido
        <?php }elseif($r->Estado=="R"){ ?>
	    Redimido
        <?php }elseif($r->Estado=="C"){ ?>
	    Cancelado (Factura Eliminada)
	    <?php } ?>
        </td>
	  <td nowrap="nowrap" class="row1"><?php echo $r->FechaVencimiento ?></td>
    </tr>

  	
	<?php } // END for
?>
	
</table>







<?php
}//end	mostrar($newmode,$submit_caption)


/*******************************************************************************************
		funtcion Print_formCliente
*******************************************************************************************/
function print_form($idCliente,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$cedula,$array_gustos,$array_deportes,$array_hobbies,$array_musica;
	
	$sql =  "SELECT * FROM BonoFidelizacion WHERE IDCliente = '" . $idCliente . "' Order By IDBonoFidelizacion DESC";
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

<table width="800" border=0 cellspacing=1 cellpadding=0 align="center">
	<tr>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">NUMERO BONO</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5"> Punto de Venta </td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Valor</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Fecha Generaci&oacute;n</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5"> Estado </td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Cliente que redimio bono</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Punto donde se redimi&oacute;</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Fecha en que se redimi&oacute;</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Factura Con la que se redimi&oacute;</td>
    </tr>
<?php 
	while($r = db_fetch_object($qid)){
?>
    
	<tr>
	  <td height="39" nowrap="nowrap" class="row1">
		<!--BONO
		<a href="Movimiento/popBono.php?id=<?php echo $r->IDBonoFidelizacion;  ?> " target="_blank">
			-->
			
			<?php echo $r->IDBonoFidelizacion ?>
		
	</td>
	  <td nowrap="nowrap" class="row1"><?php echo $array_puntos[ $r->IDPuntoVenta ]["Nombre"] ?></td>
	  <td nowrap="nowrap" class="row1">$<?php echo number_format($r->Valor,2) ?></td>
	  <td nowrap="nowrap" class="row1"><?php echo $r->Fecha ?></td>
	  <td nowrap="nowrap" class="row1"><?php if($r->Estado=="D"){ ?>
	    Disponible
	    <?php }
		elseif($r->Estado=="V"){ ?>
	    Vencido
        <?php }
		elseif($r->Estado=="R"){ ?>
	    Redimido
        <?php }elseif($r->Estado=="C"){ ?>
	    Cancelado (Factura Eliminada)
	    <?php } ?>
        </td>
	  <td nowrap="nowrap" class="row1"><?php echo get_field("Cliente","Cedula","IDCliente",$r->IDClienteRedimioBono)." " . get_field("Cliente","Nombre","IDCliente",$r->IDClienteRedimioBono) . " " .get_field("Cliente","Apellido","IDCliente",$r->IDClienteRedimioBono);  ?></td>
	  <td nowrap="nowrap" class="row1"><?php echo $array_puntos[ $r->IDPuntoVentaRedimido ]["Nombre"] ?></td>
	  <td nowrap="nowrap" class="row1"><?php echo $r->FechaRedimido ?></td>
	  <td nowrap="nowrap" class="row1"><a target="_blank" href="?mod=Factura&action=edit&idpunto=<?php echo $r->IDPuntoVentaRedimido?>&id=<?php echo $r->IDFactura ?>"><?php echo $r->IDFactura ?></td>
    </tr>

  	
	<?php } // END for
?>

<?php
}// End if$rows
else
	echo "<br><br><span class=subtitle><b>Este cliente no tiene puntos de fidelizaci&oacute;n </b></span>";
?>
	
</table>



</form>
	<?php
}// End function print_formcliente()



?></BODY></HTML> 
