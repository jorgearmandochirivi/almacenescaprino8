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
					mostrarcedula("mostrar","Buscar Precio");
			break;
			default : 
				mostrarcedula("mostrar","Buscar Precio");
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
<form name="frmcliente" method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>" onsubmit="disable(this);">
<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="800">
	
	<tr>
		<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
		</td>
		<td class="tbtbot"><b></b>REFERENCIA<span class="gen"></span></td>
		<td class="tbtr">
			<img src="images/spacer.gif" alt="" width="124" height="22" />
		</td>
	</tr>
</table>

<table align="center" width="800" cellpadding="0" cellspacing="1" border="0" class="forumline">
  
  <tr>
	<td class="col1" align="center" valign="middle">Digite la referencia:</td>
	<td class="col2" >
		<input type="text" class="tbox" name="Referencia"> 
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


<?php if (!empty($_POST["Referencia"])): ?>
<table width="800" border=0 cellspacing=1 cellpadding=0 align="center">
	<tr>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Referencia</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Ref Antigua</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Tipologia</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Precio Base</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Descuento</td>
	  <td class="rowform" nowrap="nowrap" bgcolor="#DBEAF5">Precio Venta</td>
    </tr>
<?php
/*
//Consulto el id de la ref
$sql_ref = "Select IDReferencia Where Numero like '%".$_POST["Referencia"]."%'";
$result_ref = db_query($sql_ref);
while ($row_ref = db_fetch_array($result_ref)):
	$array_id_ref[]=$row_ref["IDReferencia"];
endwhile;

if(count($array_id_ref)>0):
	$id_ref = implode(",",$array_id_ref);
	$sql_ptovta_ref ="Select * From PuntoVentaReferencia IDReferencia in (".$id_ref.")";
	$result_ptoref = db_query($sql_ptovta_ref);
	while ($row_ptoref = db_fetch_array($result_ptoref)):
		$array_pto_ref[]=$row_ref["IDPuntoVentaReferencia"];
	endwhile;
endif;
*/

 $sql_referencia_precio = "SELECT R.Numero,R.NumeroAnterior, P.ValorVenta, P.Descuento,R.IDTipologia 
							  FROM Referencia R, Precio P 
							  WHERE R.IDPrecio = P.IDPrecio and R.Numero like '%".$_POST["Referencia"]."%'
							  and R.Publicar = 'S'";
							  
 $sql_referencia_precio = "SELECT CE.IDCodificacionEspecifica, R.Numero,R.NumeroAnterior, P.ValorVenta, P.Descuento,R.IDTipologia, CE.Existencias 
							FROM Referencia R, Precio P, PuntoVentaReferencia PVR, CodificacionEspecifica CE
							WHERE R.IDPrecio = P.IDPrecio 
							and PVR.IDReferencia = R.IDReferencia
							and CE.IDPuntoVentaReferencia = PVR.IDPuntoVentaReferencia
							and CE.Existencias >0							
							and R.Numero like '%".$_POST["Referencia"]."%'
							and R.Publicar = 'S'
							Group by R.IDReferencia";							  
							  
							  
							  
$qry_referencia_precio = db_query( $sql_referencia_precio );
while($r = db_fetch_object( $qry_referencia_precio )){ ?>	
	<tr>
	  <td nowrap="nowrap" class="row1"><?php echo $r->Numero ?></td>
	  <td nowrap="nowrap" class="row1"><?php echo $r->NumeroAnterior ?></td>
	  <td nowrap="nowrap" class="row1"><?php echo get_field("Tipologia","Nombre","IDTipologia",$r->IDTipologia) ?></td>
	  <td height="39" nowrap="nowrap" class="row1"><?php $precio_bruto=(int)$r->ValorVenta; echo number_format($precio_bruto,2) ?></td>
	  <td nowrap="nowrap" class="row1"><?php echo $descuento=(int)$r->Descuento ?>%</td>
	  <td nowrap="nowrap" class="row1">$
	  <?php 
	  if ($descuento>0)
		  	$precio_venta = $precio_bruto - ( $precio_bruto * $descuento / 100);
		else
			$precio_venta = $precio_bruto;
		  
	  
	  echo number_format($precio_venta,2) ?>
     </td>
    </tr>

  	
	<?php } // END for
?>
	
</table>

<?php endif; ?>







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
	  <td height="39" nowrap="nowrap" class="row1"><a href="Movimiento/popBono.php?id=<?php echo $r->IDBonoFidelizacion;  ?> " target="_blank"><?php echo $r->IDBonoFidelizacion ?></a></td>
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
	  <td nowrap="nowrap" class="row1"><a target="_blank" href="?mod=Factura&action=edit&idpunto=<?=$r->IDPuntoVentaRedimido?>&id=<?=$r->IDFactura ?>"><?php echo $r->IDFactura ?></td>
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
