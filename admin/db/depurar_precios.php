<body> <?php
$TitleMod ="Depurar Precios";

$Table = "Referencia";
$TableJoin = "CodificacionEspecifica";
$Key = "IDReferencia";
$MOD = "depurar_precios";
$m="depurar_precios";

		switch (nvl($action)) {
			case "depurar_precios" :
				GLOBAL $main_types,$tamano_archivo;
				
				db_query( "BEGIN" );
				
				$sql_precio = "SELECT * FROM Precio WHERE Publicar = 'S' ";
				$qry_precio = db_query( $sql_precio );
				while( $r_precio = db_fetch_array( $qry_precio ) )
				{
					$sql_ref = "SELECT IDReferencia, IDPrecio FROM Referencia WHERE IDPrecio = '" . $r_precio["IDPrecio"] . "' ";
					$qry_ref = db_query( $sql_ref );
					if( db_num_rows( $qry_ref ) == 0 )
					{
						echo $r_precio["IDPrecio"] . " - " . $r_precio["ValorVenta"] . " - " . $r_precio["Descuento"] . "<br>";
						$contador++;
					}//end if
				}//end while
				echo "<br>Total sin referencia";
				echo $contador;
				exit;	
				db_query( "COMMIT" );
								
			break;	
			default : 
					print_form("","Refeencia","Importar Archivos","submit");
			break;
		
		} // End switch
		



/********************************** FIN INSERTAR INVENTARIO ******************************/



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<br>

<table cellpadding=1 cellspacing=0 class=bordertable align=left >
	<tr>
			<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
		</tr>
	<tr>
			<td>
				<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
					
					
					<form name="frmInv" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" >
						
						<tr class=row2>
							<td align="center">
								<input type=hidden name=action value="depurar_precios"></td>
							<td><input type=submit name=submit value="Cargar" class=submit></td>
						</tr>
					</form>
				</table>
			</td>
	</tr>
</table>
<?php
}// End function print_form()


?>
