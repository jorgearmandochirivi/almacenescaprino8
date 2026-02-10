<body> <?php

 $TitleMod ="Codificacion Especifica";

$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$Title = " Consultar Inventario ";
$MOD = "Inventario";
$m="Referencia";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "list" :	
				list_r($HTTP_POST_VARS['campo'],$HTTP_POST_VARS['referencia']);
			break;
			default : 
					
				seleccionareferencia("list");
				//list_r();
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");

/*******************************************************************************************
	seleccionareferencia: formulario de busqueda para la referencia
	Parametros:
			$newmode : nieva accion a tomar con el submit
	Retorna:	
			Void
*******************************************************************************************/
function seleccionareferencia( $newmode)
{
	GLOBAL $Title;
?>	
	<br><br><br><br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="650">
		
		<tr>
			<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" />
			</td>
			<td class="tbtbot"><b></b>
				<span class="gen">
					<?=$Title?>
				</span>
			</td>
			<td class="tbtr">
				<img src="images/spacer.gif" alt="" width="124" height="22" />
			</td>
		</tr>
	</table>
	<table cellspacing='0' cellpadding='2' border='0' align='center' class="forumline" width="650">
		<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
		<tr>
			<td class=col1 width=30;?> 
				Buscar Referencia Por
			</td>
			<td class="col2">
				&nbsp;&nbsp;&nbsp;	
				<select name="campo" class="input">
					<option value="Numero">Numero</option>
					<option value="Nombre">Nombre</option>
				</select>
				&nbsp;&nbsp;&nbsp;
				<input type=text class=tbox name=referencia>
				<input type="submit" class="button" name="enviar" value="Consultar">

				<input type=hidden name=action value=<?=$newmode?>>
				
			</td>
		</tr>
			<tr>
				<td class=col1 width=30;?>Punto de Venta</td>
				<td class="col2">&nbsp;&nbsp;&nbsp; <select name="IDPuntoVentaR" class="InputSelect">
					<?php
						$sql_puntoventa = "SELECT * FROM PuntoVenta Where Publicar = 'S' ORDER BY IDCiudad, Nombre";
						$query_puntoventa = db_query($sql_puntoventa);
						while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
						{
							echo "<option value='".$r_puntoventa->IDPuntoVenta."'>".$r_puntoventa->Nombre."</option>";	
						}//end while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
					?>
					</select>
				</td>
			</tr>
		</form>
	</table>
<?php
}//end function seleccionapuntoventa($idreferencia)


/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($campo, $referencia){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$idReferencia,$IDPuntoVenta,$Title,$IDPuntoVentaR;
	 	
	 	$puntoventa = $IDPuntoVenta;

?>
	<br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="650">
		<tr>
			<td class="tbtl"><img src="images/spacer.gif" alt="" width="22" height="22" /></td>
			<td class="tbtbot"><b></b><span class="gen"><?=$Title?> - <?php echo $campo.":".$referencia;  ?></span></td>
			<td class="tbtr"><img src="images/spacer.gif" alt="" width="124" height="22" /></td>
		</tr>
	</table>
	<table width=650 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
		<td >
				<table cellspacing='0' cellpadding='2' border='0' align='center' class="forumline" width="650">
					<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
						<tr>
							<td class=col1 width=30;?> 
				Buscar Referencia Por
				</td>
							<td class="col2">
				&nbsp;&nbsp;&nbsp;	<select name="campo" class="input">
									<option value="Numero">Numero</option>
									<option value="Nombre">Nombre</option>
								</select>
				&nbsp;&nbsp;&nbsp;
		<input type=text class=tbox name=referencia>
		<input type="submit" class="button" name="enviar" value="Consultar">
		<input type=hidden name=action value='<?="list"?>'></td>
						</tr>
						<tr>
							<td class=col1 width=30;?>Punto de Venta</td>
							<td class="col2">&nbsp;&nbsp;&nbsp; <select name="IDPuntoVentaR" class="InputSelect">
									<?php 						$sql_puntoventa = "SELECT * FROM PuntoVenta";
						$query_puntoventa = db_query($sql_puntoventa);
						while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
						{
							echo "<option value='".$r_puntoventa->IDPuntoVenta."'>".$r_puntoventa->Nombre."</option>";	
						}//end while( $r_puntoventa = db_fetch_object( $query_puntoventa ) )
					?>
								</select></td>
						</tr>
					</form>
				</table>
			</td>
	</tr>
<?php
//if( !empty( $referencia ) )	
//{
exit;
$campo = "Numero";
 	
	 $sql_referencia = "SELECT $campo FROM Referencia ORDER BY Numero ";
	 $qry_referencia = db_query( $sql_referencia );
	 while( $r_referencia = db_fetch_object( $qry_referencia ) )
	 {
	 	
	 	$ref = $r_referencia->$campo;
	 	
	 	$sql =  "SELECT * FROM $Table CE, Referencia R, PuntoVentaReferencia PR WHERE PR.IDPuntoVenta = '$IDPuntoVentaR' ";
	 	$sql .= " AND R.IDReferencia = PR.IDReferencia ";
	 	$sql .= "AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia ";
	 
		$query_codificacion = db_query($sql);
		$rows = db_num_rows($query_codificacion);

		if($rows > 0){
		?>
			<tr>
				<td>
					<?php 
						$i = 0;
						$r = array( );
						while($r_codificacionesp = db_fetch_array($query_codificacion))
						{
							$r[$i] = $r_codificacionesp;
							$i++;
						} //end while($r[$i] = db_fetch_array($query_codificacion))
						//print_r($r);
						
						//VALIDACION DE LOS CAMPOS DE LA MATRIZ
						
						$contcampos = 1;
						$poscheck = 0;
						while ( $contcampos <= $i )
						{
							$NamesCheck[$poscheck] = "Existencias[$contcampos]";
							$Check[$poscheck] = "Existencias[$contcampos]";
							$poscheck++;
							
							$NamesCheck[$poscheck] = "Minimo[$contcampos]";
							$Check[$poscheck] = "Minimo[$contcampos]";
							$poscheck++;
							
							$NamesCheck[$poscheck] = "Maximo[$contcampos]";
							$Check[$poscheck] = "Maximo[$contcampos]";
							$poscheck++;
							
							$contcampos++;
						}// end while ( $contcampos <= $i )
						
						$chek=implode("','",$Check);
						$namesCheck=implode("','",$NamesCheck);
						echo "<script>var NamesCheck = new Array('$namesCheck');</script>";
						echo "<script>var Check = new Array('$chek');</script>";
					?>
					
					<table width="100%" border="0" cellspacing="1" cellpadding="0">
						<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
							<tr>
								<td class="navpic" nowrap>
									
								</td>
							<?php
							foreach($r as $talla)
							{
								if(!empty($talla["IDTalla"]))
									echo "<td class=navpic align=center>".get_field("Talla","Descripcion","IDTalla",$talla["IDTalla"])."</td>";
							}
							?>	
							</tr>
							
							<tr>
								<td class="col2" width=100>
									<?php
										echo $r_referencia->$campo;
									?>
								</td>
							<?php
							foreach($r as $talla)
							{
								if(!empty($talla["IDTalla"]))
									echo "<td class=row1 align=center>" . $talla["Existencias"] . "</td>";
							}
							?>	
							</tr>
							
							
							
							
							
						</form>
					</table>
				</td>
			</tr>
		<?php
		}// End if$rows
		else
			echo "<tr><td><span class=col1list><b>No se encontraron registros con los par&aacute;metros proporcionados </b></span></td></tr>";
	}
//}
?>
</table>	

<?php
}// Enf function list()				
?>
