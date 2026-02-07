<body> <?php

$TitleMod ="Codificacion Especifica";

$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$Title = " Consultar Inventario ";
$MOD = "BuscReferencia";
$m="Referencia";
		$permisos = get_permiso($ID_Usuario,$m,$Table);
if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "list" :
				list_r($_POST['campo'],$_POST['referencia']);
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
			<td class="maintitle"><b></b>
				<span class="gen">
					<?=$Title?>
				</span>
			</td>
		</tr>
	</table>
	<table cellspacing='0' cellpadding='2' border='0' align='center' class="bordertable" width="650">
		<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
			<tr>
			<td class=col1 width=30;?>
				Buscar Referencia Por:
			</td>
			<td class="col2">
				&nbsp;&nbsp;&nbsp;
				<select name="campo" class="input">
					<option value="Numero">Numero</option>
					<option value="Nombre">Nombre</option>
				</select>
				&nbsp;&nbsp;&nbsp;
				<input type=text class=tbox name=referencia value="<?php echo $referencia ?>">
				<input type="submit" class="button" name="enviar" value="Consultar">

				<input type=hidden name=action value=<?=$newmode?>>

			</td>
		</tr>
			<tr>
				<td class=col1 width=30;?>Tipo de Referencia</td>
				<td class="col2">

				<select name="IDTipoReferencia"  >
									<option value="">Seleccione Un Tipo de Referencia</option><?php
								$qry_tiporef = db_query("SELECT * FROM TipoReferencia ORDER BY Descripcion");
								while($tiporef = db_fetch_object($qry_tiporef)){
									 echo "<option value=$tiporef->IDTipoReferencia ";if($IDTipoReferencia == $tiporef->IDTipoReferencia ) echo "selected"; echo ">&nbsp;&nbsp;$tiporef->Descripcion</option>";
								}
							?>
								</select>

				</td>
			</tr>
			<tr>
				<td class=col1 width=30;?>Punto de venta</td>
				<td class="col2">
					<select name="IDPuntoVenta" id="IDPuntoVenta">
	 <option value="">[Seleccione]</option>
	 <?php
	 $sql_vta=db_query("Select * from PuntoVenta Where Publicar = 'S' Order by Nombre");
	 while($row_vta=db_fetch_array($sql_vta)){
			 ?>
			 <option value="<?php echo $row_vta["IDPuntoVenta"]; ?>"><?php echo $row_vta["Nombre"]; ?></option>
	 <?php
	 }
	 ?>
</select>
				</td>
			</tr>

			<tr>
				<td class=col1 width=30;?>Proveedor</td>
				<td class="col2">
					<select name="IDProveedor" id="IDProveedor" class="input">
	                	<option value="">[Seleccione]</option>
	                    <?php
							$sql_prov = db_query("Select * From Proveedor where Publicar = 'S' order by Nombre asc");
							while ($row_prov = db_fetch_array($sql_prov)): ?>
	                        	<option value="<?php echo $row_prov[IDProveedor];?>" <?php if($row_prov[IDProveedor]==$r->IDProveedor) echo "selected"; ?>><?php echo $row_prov[Nombre];?></option>
							<?php
							endwhile;
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
		Global $TitleMod,$MOD,$Table,$Key,$listar,$idReferencia,$IDPuntoVenta,$Title,$IDPuntoVentaR,  $IDTipoReferencia;


?>
	<br>
	<table border="0" cellpadding="0" cellspacing="0" class="tbt" align="center" width="820">
		<tr>
			<td class="maintitle"><b></b><span class="gen"><?=$Title?> - <?php echo $campo.":".$referencia;  ?> Los Almacenes que no aparecen, no tienen existencias</span></td>
		</tr>
	</table>
	<table width=820 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
		<td class="" >
				<table cellspacing='0' cellpadding='2' border='0' align='center'  width="650">
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
							<input type=text class=tbox name=referencia value="<?php echo $referencia ?>">
							<input type="submit" class="button" name="enviar" value="Consultar">

							<input type=hidden name=action value="list">

						</td>
					</tr>
						<tr>
							<td class=col1 width=30;?>Tipo de Referencia</td>
							<td class="col2">

							<select name="IDTipoReferencia"  >
												<option value="">Seleccione Un Tipo de Referencia</option><?php
											$qry_tiporef = db_query("SELECT * FROM TipoReferencia ORDER BY Descripcion");
											while($tiporef = db_fetch_object($qry_tiporef)){
												 echo "<option value=$tiporef->IDTipoReferencia ";if($IDTipoReferencia == $tiporef->IDTipoReferencia ) echo "selected"; echo ">&nbsp;&nbsp;$tiporef->Descripcion</option>";
											}
										?>
											</select>

							</td>
						</tr>
						<tr>
							<td class=col1 width=30;?>Punto de venta</td>
							<td class="col2">
								<select name="IDPuntoVenta" id="IDPuntoVenta">
				 <option value="">[Seleccione]</option>
				 <?php
				 $sql_vta=db_query("Select * from PuntoVenta Where Publicar = 'S' Order by Nombre");
				 while($row_vta=db_fetch_array($sql_vta)){
						 ?>
						 <option value="<?php echo $row_vta["IDPuntoVenta"]; ?>"><?php echo $row_vta["Nombre"]; ?></option>
				 <?php
				 }
				 ?>
			</select>
							</td>
						</tr>

						<tr>
							<td class=col1 width=30;?>Proveedor</td>
							<td class="col2">
								<select name="IDProveedor" id="IDProveedor" class="input">
				                	<option value="">[Seleccione]</option>
				                    <?php
										$sql_prov = db_query("Select * From Proveedor where Publicar = 'S' order by Nombre asc");
										while ($row_prov = db_fetch_array($sql_prov)): ?>
				                        	<option value="<?php echo $row_prov[IDProveedor];?>" <?php if($row_prov[IDProveedor]==$r->IDProveedor) echo "selected"; ?>><?php echo $row_prov[Nombre];?></option>
										<?php
										endwhile;
									?>
				                </select>
							</td>
						</tr>

					</form>
				</table>
			</td>
	</tr>
<?php
if( !empty( $referencia ) ||  !empty( $_POST["IDPuntoVenta"] ) || !empty( $_POST["IDProveedor"] ) )
{


	if( !empty( $_POST["IDPuntoVenta"] ) ){
		$condicion .= " AND IDPuntoVenta = '".$_POST["IDPuntoVenta"]."%' ";
		echo $condicion_punto = " AND IDPuntoVenta = '".$_POST["IDPuntoVenta"]."' ";
	}

	if( !empty( $_POST["IDProveedor"] ) ){
		$condicion .= " AND R.IDProveedor = '".$_POST["IDProveedor"]."' ";
	}

	if( !empty( $referencia ) ){
		$condicion .= " AND R.$campo LIKE '%$referencia%' ";
	}

	//PUNTOS DE VENTA
	$sql_puntos = " SELECT IDPuntoVenta, Nombre FROM PuntoVenta Where Publicar = 'S' ".$condicion_punto." ORDER BY IDCiudad, Nombre ";
	$qry_puntos = db_query( $sql_puntos );
	while( $r_puntos = db_fetch_array( $qry_puntos ) )
		$array_puntos[$r_puntos[IDPuntoVenta]] = $r_puntos[Nombre];

	//Tallas
	$sql_tallas = " SELECT IDTalla, Descripcion FROM Talla Order by Descripcion ";
	$qry_tallas = db_query( $sql_tallas );
	while( $r_tallas = db_fetch_array( $qry_tallas ) )
		$array_tallas[$r_tallas[IDTalla]] = $r_tallas[Descripcion];

	 	if( !empty( $_POST["IDTipoReferencia"]  ) )
			$condicion = " AND IDTipoReferencia = '".$_POST["IDTipoReferencia"]."%' ";

			//sort($array_tallas);


	 	$sql =  "SELECT CE.*, PR.IDPuntoVenta FROM $Table CE, Referencia R, PuntoVentaReferencia PR,Talla T WHERE 1 $condicion AND R.IDReferencia = PR.IDReferencia AND R.Publicar <> 'N'";
	 	$sql .= " AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia and T.IDTalla=CE.IDTalla order by T.Descripcion ";

		$query_codificacion = db_query($sql);
		$rows = db_num_rows($query_codificacion);

		if($rows > 0){
		?>
			<tr>
				<td class="row1">
					<?php
						$i = 0;
						$r = array( );
						while($r_codificacionesp = db_fetch_array($query_codificacion))
						{
							$array_existencias[ $r_codificacionesp[IDPuntoVenta] ][ $r_codificacionesp[IDTalla] ] += $r_codificacionesp[Existencias];
							$array_tallas_mostrar[ $r_codificacionesp[IDTalla] ] = $array_tallas[ $r_codificacionesp[IDTalla] ];
						} //end while($r[$i] = db_fetch_array($query_codificacion))
						//print_r($r);
					?>

					<table width="100%" border="0" cellspacing="1" cellpadding="0">
						<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return EvaluaReg(this,Check);">
							<tr>
							<td class="rowform">PUNTOS DE VENTA</td>
							<?php
							//sort($array_tallas_mostrar);
							foreach($array_tallas_mostrar as $idtalla => $nombre)
							{
								//if( array_sum($array_existencias[ $idpunto ]) > 0 )
									echo "<td class=rowform align=center>".$nombre."</td>";
							}//end for
							?>
							<td class="rowform">TOTALES</td>
							</tr>
							<?php
							foreach($array_puntos as $idpunto => $nombre)
							{
							?>
							<tr>
								<td class=rowform align=center><?php echo $nombre; ?></td>
								<?php
								foreach($array_tallas_mostrar as $idtalla => $nombre)
								{
									echo "<td class=row1 align=center>".$array_existencias[ $idpunto ][ $idtalla ]."</td>";
									$totaltalla[ $idtalla ] += $array_existencias[ $idpunto ][ $idtalla ];
									$totalpunto[ $idpunto ] += $array_existencias[ $idpunto ][ $idtalla ];

								}//end for
								?>
								<td class="rowform"><?php echo $totalpunto[ $idpunto ]; ?></td>
							</tr>
							<?php
							}//end for
							?>
							<tr>
								<td class="rowform">TOTALES</td>
								<?php
								foreach($array_tallas_mostrar as $idtalla => $nombre)
								{
									//if( array_sum($array_existencias[ $idpunto ]) > 0 )
										echo "<td class=rowform align=center>".$totaltalla[ $idtalla ]."</td>";
								}//end for
								?>
								<td class="rowform"><?php echo array_sum( $totalpunto ); ?></td>
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
?>
</table>

<?php
}// Enf function list()
?>
