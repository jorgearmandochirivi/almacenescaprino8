<body> <?php

$TitleMod ="Codificacion Especifica";

$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$MOD = "CodEspecifica";
$m="Referencia";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;
			case "insert" :
				$frm= vars_LOG($HTTP_POST_VARS);
				$id = insert($frm);
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			
			case "update" :
				$frm= vars_LOG($HTTP_POST_VARS);
				
				//array con los campos de la tabla a actualizar
				$array_campos = array("Existencias","Minimo","Maximo");
				
				actualizamatriz($frm, $array_campos);
				
				echo "<SCRIPT>location.href='?mod=".$MOD."&idReferencia=".$frm[id]."';</SCRIPT>";
			break;
			
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$HTTP_GET_VARS[action]="";
				delete($ID);
			break;
			case "list" :	
				list_r($HTTP_POST_VARS['puntoventa']);
			break;
			default : 
					
				seleccionapuntoventa($idReferencia, "list");
				//list_r();
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","row2");

/*******************************************************************************************
	seleccionapuntoventa: Selecciona un punto de venta para ver la codificacion especifica de la referencia
	Parametros:
			$idreferencia : id de la referencia a mostrar
			$newmode : nieva accion a tomar con el submit
	Retorna:	
			Void
*******************************************************************************************/
function seleccionapuntoventa($idreferencia, $newmode)
{
	Global $idReferencia;
?>	
	<br><br><br><br>
	<?php
	$TABsel = 1;
 	include("Referencia/menutabReferencia.php");
	?>	
	<table cellspacing='0' cellpadding='2' border='0' align='left' class=bordertable width=300>
		<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">
		<tr>
			<td class=row1 width=76;?> 
				Seleccione Punto de Venta>
			</td>
			<td>
				<select class=input name=puntoventa onChange="document.frm.submit();">
					<option value="">Seleccione</option>
				<?php
					$sql_puntos = "SELECT P.* FROM PuntoVenta P, PuntoVentaReferencia PR ";
					$sql_puntos .= "WHERE PR.IDReferencia = '$idreferencia' AND PR.IDPuntoVenta = P.IDPuntoVenta GROUP BY P.IDPuntoVenta";
					
					$query_puntos = db_query( $sql_puntos );
				
					while( $r_puntos = db_fetch_object( $query_puntos ) )
					{
						
						echo "<option value=$r_puntos->IDPuntoVenta>$r_puntos->Nombre</option>";
						
					}
				?>
				</select>
				<input type=hidden name=idReferencia value=<?=$idreferencia?>>
				<input type=hidden name=action value=<?=$newmode?>>
			</td>
		</tr>
		</form>
	</table>
<?php
}//end function seleccionapuntoventa($idreferencia)


/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($puntoventa){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$idReferencia;
	 	$sql =  "SELECT * FROM $Table CE, PuntoVentaReferencia PR WHERE PR.IDPuntoVenta = '$puntoventa' ";
	 	$sql .= "AND PR.IDReferencia = '$idReferencia' AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia ";
	 	
	$query_codificacion = db_query($sql);
	$rows = db_num_rows($query_codificacion);
?>

<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76;?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<?php echo Referencia;?>">Administrar <?php echo Referencia;?></a> </td>
		<td></td>
	</tr>
</table>
<?php
if($rows > 0){
?>		
<br>
<?php
	$TABsel = 1;
 	include("Referencia/menutabReferencia.php");
?>	
<table width=500 cellpadding=0 cellspacing=0 align=left class=bordertable>
	<tr>
		<td class="maintitle" bgcolor="#9daac6">
			<table cellspacing='0' cellpadding='2' border='0' align='left' class=bordertable width=100% >
				<form name="frm1" action="<?=$PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">
				<tr>
					<td width=70;?> 
						<b>
							<?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$puntoventa) ?> - Referencia: <?php echo get_field("Referencia","Numero","IDReferencia",$idReferencia)?>
						</b>
					</td>
					<td>
						<select class=input name=puntoventa onChange="document.frm1.submit();">
							<option value="">Seleccione</option>
						<?php
							$sql_puntos = "SELECT P.* FROM PuntoVenta P, PuntoVentaReferencia PR ";
							$sql_puntos .= "WHERE PR.IDReferencia = '$idReferencia' AND PR.IDPuntoVenta = P.IDPuntoVenta GROUP BY P.IDPuntoVenta";
							
							$query_puntos = db_query( $sql_puntos );
						
							while( $r_puntos = db_fetch_object( $query_puntos ) )
							{
								
								echo "<option value=$r_puntos->IDPuntoVenta>$r_puntos->Nombre</option>";
								
							}
						?>
						</select>
						<input type=hidden name=idReferencia value=<?=$idReferencia?>>
						<input type=hidden name=action value=list>
					</td>
				</tr>
				</form>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<?php 
				$i = 0;
				while($r[$i] = db_fetch_array($query_codificacion))
				{
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
				<form name="frm" action="<?=$PHP_SELF?>" method="post" onSubmit="return EvaluaReg(this,Check);">
					<tr>
						<td class="titlemedium" nowrap>
							<?php echo get_field("Referencia","Nombre","IDReferencia",$idReferencia)?>
						</td>
					<?php
					foreach($r as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=titlemedium align=center>".get_field("Talla","Descripcion","IDTalla",$talla[IDTalla])."</td>";
					}
					?>	
					</tr>
					
					<tr>
						<td class="rowform">
							Existencias
						</td>
					<?php
					foreach($r as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=row1 align=center><input type=text size=5  value=".$talla[Existencias]." name=Existencias[$talla[IDCodificacionEspecifica]]></td>";
					}
					?>	
					</tr>
					
					<tr>
						<td class="rowform">
							Minimo
						</td>
					<?php
					foreach($r as $talla)
					{
						if(!empty($talla[IDTalla]))
							echo "<td class=row1 align=center><input type=text size=5 value=".$talla[Minimo]." name=Minimo[$talla[IDCodificacionEspecifica]]></td>";
					}
					?>	
					</tr>
					
					<tr>
						<td class="rowform">
							Maximo
						</td>
					<?php
					$i = 1;
					foreach($r as $talla)
					{
						if(!empty($talla[IDTalla]))
						{
							echo "<td class=row1 align=center><input type=text size=5 value=".$talla[Maximo]." name=Maximo[$talla[IDCodificacionEspecifica]]></td>";
							$i++;
						}
					}
					
					?>	
					</tr>
					
					<tr>
						<td class="titlemedium" colspan=<?=$i?> align="right">
							<?php
							foreach($r as $talla)
							{
								if(!empty($talla[IDTalla]))
								{
									echo "<input type=hidden value=".$talla[IDCodificacionEspecifica]." name=Codigos[$talla[IDCodificacionEspecifica]]>";
									$i++;
								}
							}
							?>
							<input type="hidden" name="action" value="update">
							<input type="hidden" name="id" value="<?=$idReferencia?>">
							<input type="submit" class="submit" value="Actualizar">
						</td>
					</tr>
					
				</form>
			</table>
		</td>
	</tr>
</table>	

<?php
}// End if$rows
else
	echo "<br><br><span class=subtitle><b>No existen registros en  $TitleMod </b></span>";
}// Enf function list()				
?>
