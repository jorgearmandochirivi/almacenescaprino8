<?php

if (!isset($DB_DIE_ON_FAIL)) { $DB_DIE_ON_FAIL = true; }
if (!isset($DB_DEBUG)) { $DB_DEBUG = false; }

function db_connect($dbhost, $dbname, $dbuser, $dbpass) {

	global $DB_DIE_ON_FAIL, $DB_DEBUG;
	
	if (! $dbh = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname)) {
		if ($DB_DEBUG) {
			echo "<h2>Can't connect to $dbhost as $dbuser</h2>";
			echo "<p><b>MySQL Error</b>: ", mysqli_connect_error();
		} else {
			echo "<h2>Database error encountered</h2>";
		}

		if ($DB_DIE_ON_FAIL) {
			echo "<p>This script cannot continue, terminating.";
			die();
		}
		return false;
	}

	// Set charset to utf8mb4 for better compatibility
	mysqli_set_charset($dbh, 'utf8mb4');

	// Store connection globally for use by other db_* functions
	$GLOBALS['DB_LINK'] = $dbh;

	return $dbh;
}

function db_disconnect($dblink) {

	mysqli_close($dblink);
}

function db_query($query, $debug=false, $die_on_debug=true, $silent=false) {

	global $DB_DIE_ON_FAIL, $DB_DEBUG;

	// Get the global database connection
	$dblink = $GLOBALS['DB_LINK'] ?? null;

	if (!$dblink) {
		if (!$silent) {
			echo "<h2>No database connection available</h2>";
		}
		return false;
	}

	if ($debug) {
		echo "<pre>" . htmlspecialchars($query) . "</pre>";

		if ($die_on_debug) die;
	}

	$qid = mysqli_query($dblink, $query);

	if (! $qid && ! $silent) {
		if ($DB_DEBUG) {
			echo "<h2>Can't execute query</h2>";
		        echo "<pre>" . htmlspecialchars($query) . "</pre>";
			echo "<p><b>MySQL Error ".mysqli_errno($dblink)." :</b> ".mysqli_error($dblink);
			mysqli_query($dblink, "ROLLBACK");
		} else {
			echo "<h2>Database error encountered</h2>";
		}

		if ($DB_DIE_ON_FAIL) {
			echo "<p>This script cannot continue, terminating.";
			die();
		}
	}

	return $qid;
}

function db_fetch_array($qid) {
/* grab the next row from the query result identifier $qid, and return it
 * as an associative array.  if there are no more results, return FALSE */

	return mysqli_fetch_array($qid);
}

function db_fetch_row($qid) {
/* grab the next row from the query result identifier $qid, and return it
 * as an array.  if there are no more results, return FALSE */

	return mysqli_fetch_row($qid);
}

function db_fetch_object($qid) {
/* grab the next row from the query result identifier $qid, and return it
 * as an object.  if there are no more results, return FALSE */

	return mysqli_fetch_object($qid);
}

function db_num_rows($qid) {
/* return the number of records (rows) returned from the SELECT query with
 * the query result identifier $qid. */

	return mysqli_num_rows($qid);
}

function db_affected_rows() {
/* return the number of rows affected by the last INSERT, UPDATE, or DELETE
 * query */

	$dblink = $GLOBALS['DB_LINK'] ?? null;
	return $dblink ? mysqli_affected_rows($dblink) : 0;
}

function db_insert_id() {
/* if you just INSERTed a new row into a table with an autonumber, call this
 * function to give you the ID of the new autonumber value */

	$dblink = $GLOBALS['DB_LINK'] ?? null;
	return $dblink ? mysqli_insert_id($dblink) : 0;
}

function db_free_result($qid) {
/* free up the resources used by the query result identifier $qid */

	mysqli_free_result($qid);
}

function db_num_fields($qid) {
/* return the number of fields returned from the SELECT query with the
 * identifier $qid */

	return mysqli_num_fields($qid);
}

function db_field_name($qid, $fieldno) {
/* return the name of the field number $fieldno returned from the SELECT query
 * with the identifier $qid */

	$field = mysqli_fetch_field_direct($qid, $fieldno);
	return $field ? $field->name : false;
}

function db_data_seek($qid, $row) {
/* move the database cursor to row $row on the SELECT query with the identifier
 * $qid */

	if (db_num_rows($qid)) { return mysqli_data_seek($qid, $row); }
}


function insert_with_key($frm){

	Global $TitleMod,$Table,$Key,$KeyLength;


	$result = db_query("Select * FROM $Table WHERE $Key = '$frm[$Key]' ");

	if(db_num_rows($result)== 0)
		$qry_insert = db_query(str_qry_insert($Table,$frm));
	else
		window_alert("Registro ya Existe ID $frm[$Key] ");

	print_form($frm[$Key],"update","Actualizar $TitleMod","Realizar Cambios");
}

function insert($frm){

	Global $TitleMod,$Table,$Key,$ID_Usuario;
	Global $MOD, $m;
	Global $permisos,$error_acceso;
	$idnot = $idnot ?? '';
	if($permisos[0]!=2){
		$qid = db_query("Select MAX($Key) AS maximo FROM $Table ");

		$result = db_fetch_object($qid);

					if (isset ($result->maximo))
						$frm[$Key] = $result->maximo + 1;
					else
						$frm[$Key] = 1;
					
		$sql_insert=(str_qry_insert($Table,$frm));
		
		$qry_insert = db_query($sql_insert);
		//log de transaccion
		insertlog($ID_Usuario,$Table,$frm[$Key],"Crear",$sql_insert);

		if($MOD!="GenerarFactura" && $MOD <> "GenerarFacturaBono" )
			window_alert("Registro Agregado correctamente ");

		//Si el modulo es el de referencia se insertan los registros necesarios
		//para llevar el inventario

		if($MOD == "Traslado2"){
			/*
			echo "
				<script>
					location.href='?mod=vertraslado&action=edit&id=".$frm[$Key]."&idpuntoorigen=".$frm["IDPuntoVentaOrigen"]."'
				</script>
			";
			*/
		}

		if( $MOD <> "GenerarFactura" && $MOD <> "GenerarFacturaBono" && $MOD <> "cambiar" && $MOD <> "SalidaMerca" && $MOD <> "CostoReferencia" && $MOD <> "Traslado2" )			
			echo "
				<script>
					location.href='?mod=".$MOD."&action=edit&id=".$frm[$Key].(isset($idnot) ? "&idnot=".$idnot : '')."'
				</script>
			";

	//	header("Location: $url/admin/?mod=$MOD&action=edit&id=$Key");

		return 	$frm[$Key];
	}
	else{
		window_alert($error_acceso);
		return false;
	}
}

function insert_width_table($frm,$Table,$Key){

	Global $TitleMod,$ID_Usuario;
	Global $MOD, $m;
	Global $permisos,$error_acceso;
	$idnot = $idnot ?? '';
	if($permisos[0]!=2){
		$qid = db_query("Select MAX($Key) AS maximo FROM $Table ");

		$result = db_fetch_object($qid);

					if (isset ($result->maximo))
						$frm[$Key] = $result->maximo + 1;
					else
						$frm[$Key] = 1;

		$sql_insert=(str_qry_insert($Table,$frm));
		$qry_insert = db_query($sql_insert);
		//log de transaccion
		insertlog($ID_Usuario,$Table,$frm[$Key],"Crear",$sql_insert);

		//window_alert("Registro Agregado correctamente ");

		//Si el modulo es el de referencia se insertan los registros necesarios
		//para llevar el inventario
		$mod = $MOD ?? '';
		if(!$m && $MOD <> "GenerarFactura"){
			echo "
				<script>
					location.href='?mod=".$MOD."&action=edit&id=".$frm[$Key]."&idnot=".$idnot."'
				</script>
			";
		}

	//	header("Location: $url/admin/?mod=$MOD&action=edit&id=$Key");

		return 	$frm[$Key];
	}



	else{
		window_alert($error_acceso);
		return false;
	}
}

function update($frm){
	Global $Table,$TitleMod,$Key,$ID_Usuario,$m,$MOD;
	Global $permisos,$error_acceso;
	$idnot = $idnot ?? '';

	if($permisos[0]!=2){
//            print_r( $frm );
//            echo $Table;
//            exit;
		$sql_update=(str_qry_update($Table,$frm)).";";
               // echo $sql_update;

		//echo $sql_update;
		//exit;
		$qry_update = db_query($sql_update);

		//insertar el log
		insertlog($ID_Usuario,$Table,$frm['ID'],"Actualizar",$sql_update);
		if($MOD!="GenerarFactura" && $MOD <> "GenerarFacturaBono" )
			window_alert("Registro Actualizado correctamente ");

		$mod = $MOD ?? '';
		if(!$m && $mod != "GenerarFactura")
			print_form($frm['ID'],"update","Actualizar $TitleMod","Realizar Cambios");
	}
	else{
		window_alert($error_acceso);
		return false;
	}


	/*

	Si el modulo es referencia se pregunta si lo que viene en el formulario caambia el tipo de talla que hay
	y si cambia se ealiza la insercion de la codificacion especifica de nuevo


	insert_codEspecifica($ID);
	*/

	if( $MOD <>  "RecibirTraslado" && $MOD <>  "Opciones" )
	{
			echo "
				<script>
					location.href='?mod=".$MOD."&action=edit&id=".$frm[$Key].(isset($idnot) ? "&idnot=".$idnot : '')."'
				</script>
			";
	}
}

function delete($id){

	Global $Table,$Key,$TableJoin,$action,$ID_Usuario,$m,$TitleMod;

	$strpunto = '';
	//Si es Traslado o factura o MOvimiento
	if( ( $Table == "Factura" ) || ( $Table == "Movimiento" ) || ( $Table == "Traslado" ) )
		$strpunto = " AND IDPuntoVenta = (SELECT IDPuntoVenta FROM $Table WHERE $Key = '$id' LIMIT 1) ";

	if (!empty( $TableJoin))
	{
		$qry = db_query("SELECT $Key FROM $TableJoin WHERE $Key = '$id' LIMIT 1");
		if(db_num_rows($qry) == 0)
		{
			$sql_delete=("DELETE FROM $Table WHERE $Key = '$id' $strpunto ");
			$qry_delete = db_query($sql_delete);
			//log de transaccion
			insertlog($ID_Usuario,$Table,$id,"Borrar",$sql_delete);
			window_alert("Registro ID $id eliminado de $Table ");
			if(!$m)
				list_r();
		}
		else {
				window_alert("Este registro tiene relacion con la Tabla $TableJoin \\n No se puede eliminar");
				if(!$m)
					print_form($id,"delete","update","Actualizar $TitleMod","Realizar Cambios");
		}
	}
	else {

		$qry = db_query("SELECT $Key FROM $Table WHERE $Key = '$id' LIMIT 1");
		 if(db_num_rows($qry) != 0)
		{
			$sql_delete=("DELETE FROM $Table WHERE $Key = '$id' $strpunto ");
			$qry_delete = db_query($sql_delete);
			//log de transaccion
			insertlog($ID_Usuario,$Table,$id,"Borrar",$sql_delete);
			window_alert("Registro ID $id eliminado de $Table ");
			if($m!="intereses")
				list_r();
		}
			else {
				window_alert("No existe Registros Con las condiciones dadas $Table \\n No se puede eliminar");
				if(!$m)
					print_form($id,"delete","update","Actualizar $TitleMod","Realizar Cambios");
				}

		 }
}

function str_qry_insert($Table,$frm){

	//Modificacion 22/07/05
	//Global $Table, $IDUsuario;

	Global $IDUsuario;

	 $result = db_query("SHOW FIELDS FROM $Table");

	$fields = '';
	$values = '';
	$field = array();
    while($row = db_fetch_array($result))
		$field[] = $row['Field'];

	$str = "INSERT INTO $Table ( ";

	for($i = 0; $i < (count($field)-1); $i++)
			$fields .= $field[$i].",";

			$fields .= $field[$i];

	$str.= " $fields ) ";

	$str.= " VALUES ( ";

	for($i = 0; $i < (count($field)-1); $i++)
			$values .= "'".$frm[$field[$i]]."',";

			$values .= "'".$frm[$field[$i]]."'";

	$str.= "$values ) ";

	return $str;
}



function str_qry_update($Table,$frm){
	Global $Table,$Key;
	$result = db_query("SHOW FIELDS FROM $Table");
	$value_array = array();
	while($row = db_fetch_array($result)){
		if($row['Field'] <> "Password")
			$array_field[] = $row['Field'];
		else
			if(!empty($row['Field']))
				$array_field[] = $row['Field'];
	}

	if( ( $Table == "Factura" ) || ( $Table == "Movimiento" )  )
		$strpunto = " AND IDPuntoVenta = '$frm[IDPuntoVenta]' ";
	if( $Table == "Traslado" )
		$strpunto = " AND IDPuntoVentaOrigen = '$frm[IDPuntoVentaOrigen]' ";

	$str = "UPDATE $Table SET ";

	foreach($array_field AS $field){
		$campo = substr($field,0,4);
		if(($campo=="Foto" || $campo=="File"  || $field=="Password") && empty($frm[$field]))
			continue;
		if($field <> $Key )
			array_push($value_array," $field = '".$frm[$field]."' ");
	}
	$str.= implode(" , ",$value_array)." WHERE $Key = '$frm[ID]' $strpunto ";

	//window_alert( $str );

	return $str;
}

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Fabio Sanchez :
	Modificado por Francisco Mu?oz :
	Iniciado: Nov 04/2004
	Ultima Modificaci?n: May 20/2005
*******************************************************************************************/
/*******************************************************************************************
	make_qry_string: Crea un string (sentencia sql) para filtrar datos
	Parametros:
			$frm: Array con los par?metros de filtrado cargados por URL desde un formulario
	Retorna:
			$qry_string: sentencia sql para filtrar
*******************************************************************************************/

//*********************************************** INICIO NUEVA FUNCIO MAKEQUERYSTRING *********************************************************

function make_qry_string($frm){
	Global $Table,$joinKey,$k,$idnot,$IDUsuario,$mod;


	if($mod=="Clientes_Ventasedewfef"){

           $select = "Select DetalleFactura.*, $Table.* ";
			$from = " FROM DetalleFactura, ".$Table;
    }
    else{
        $select = "Select $Table.* ";
		$from = " FROM ".$Table;
   	}

	if( !empty($frm['IDPuntoVenta']) )
		$where = " WHERE $Table.IDPuntoVenta = '$frm[IDPuntoVenta]' ";


	elseif( !empty($frm['IDPuntoVentaDestino']) )
		$where = " WHERE $Table.IDPuntoVentaDestino = '$frm[IDPuntoVentaDestino]' AND IDEstadoTraslado = '1' ";

	elseif($mod=="Clientes_Ventaseee" )
		$where = " Where 1 AND $Table.IDFactura = DetalleFactura.IDFactura ";

   	else
		$where = " Where 1 ";




	$field_order =  $Table.".".$frm['order_by'];

	$qry_field = explode(".",$frm['field']);
	if($qry_field[0] && $qry_field[1]){
		$Join_Table = $qry_field[0];
		$Join_Field = $qry_field[1];
	}

	if(!empty($frm['field'])){
		$field = $frm['field'];
		$var = explode(".",$field);
		if($var[1] == "")
			$field = $Table.".".$field;
		$keyword = '';
		$keyword = $keyword.makeboolean($field,$frm['QryString']);
		$where .= "AND $keyword ";
	}

	//inicio clientes_ventas
	if( $mod == "Clientes_Ventas" )
	{

		//verificar valores
		if( !empty( $frm["ValorEntre"] ) && !empty( $frm["ValorHasta"] ) )
		{
			$where.=" AND Cliente.Valor_Total >= '" . $frm["ValorEntre"] . "' AND Cliente.Valor_Total <= '" . $frm["ValorHasta"] . "' ";
		}//end if

		//verificar ano
		if( !empty( $frm["AnoEntre"] ) && !empty( $frm["AnoHasta"] ) )
		{
			$where.=" AND Cliente.Ano >= '" . $frm["AnoEntre"] . "' AND Cliente.Ano <= '" . $frm["AnoHasta"] . "' ";
		}//end if

	}//end if


	//********************************** INICIO RESTRICCION RESERVAS RECURSOS ***********************************************
	if($mod=="todasreservas")
		$where.=" AND $frm[tjoin].Confirmar = 'N' ";
	if($mod=="reservas")
		$where.=" AND $frm[tjoin].IDResponsable = '$IDUsuario' ";
	//********************************** FIN RESTRICCION RESERVAS RECURSOS ***********************************************

	if(!empty($frm['limit1']) && !empty($frm['limit2'])){
		//if(!empty($frm['field']))
		$operator = " AND ";
		$between = $operator.$frm['rangofield']." BETWEEN '".$frm['limit1']."' AND '".$frm['limit2']."'";
	}

	if($Table=="DirectorioCorporativo"){
		$where.=" AND Nivel > 0";
	}

	if($mod=="lnkgral"){
		$where.=" AND IDUsuario = -1";
	}

	//********************************** INICIO DETERMINAR TABLAS JOINS ***********************************************
	if($frm['tjoin'] ){
		$field_order = $frm['order_by'];
		$var = explode(".",$field_order);

		if($var[1] != "" ){
			if($var[0]!=$frm['tjoin'] && $var[0] != $frm['tlevel']){
				// Special case: Referencia joined with PuntoVentaReferencia
				if($var[0] == "Referencia" && $frm['tjoin'] == "PuntoVentaReferencia"){
					$select .= ", Referencia.IDReferencia";
					$from .= ", Referencia";
					$where .= " AND Referencia.IDReferencia = PuntoVentaReferencia.IDReferencia";
				}
				else {
					$IDJoinKey2 = db_fetch_object(db_query("SHOW KEYS FROM $var[0]"));
					if($IDJoinKey2 && !empty($IDJoinKey2->Column_name)){
						$select .= ", ".$var[0].".".$IDJoinKey2->Column_name;
						$from .=", ".$var[0];
						$where .=" AND ".$var[0].".".$IDJoinKey2->Column_name." = ".$frm['tjoin'].".".$IDJoinKey2->Column_name;
					}
				}
			}
		}
		else
			$field_order = "$Table.$field_order";

		$IDJoinKey = db_fetch_object(db_query("SHOW KEYS FROM $frm[tjoin]"));

		if($IDJoinKey && !empty($IDJoinKey->Column_name)){
			$select .= ", ".$frm['tjoin'].".".$IDJoinKey->Column_name;
			$from .=", ".$frm['tjoin'];
			$where .=" AND ".$Table.".".$IDJoinKey->Column_name." = ".$frm['tjoin'].".".$IDJoinKey->Column_name;
		}
		if($Join_Table && $frm['tjoin']!=$Join_Table && (!empty($frm['tlevel']) && $frm['tlevel'] != $Join_Table) && $Join_Table!=$var[0]){
			$IDJoinKey3 = db_fetch_object(db_query("SHOW KEYS FROM $Join_Table"));
			if($IDJoinKey3 && !empty($IDJoinKey3->Column_name)){
				$select .= ", ".$Join_Table.".".$IDJoinKey3->Column_name;
				$from .=", ".$Join_Table;
				$where .= " AND ".$Join_Table.".".$IDJoinKey3->Column_name." = ".$frm['tjoin'].".".$IDJoinKey3->Column_name;
			}
		}

		if(!empty($frm['tlevel'])){ //la tabla join tiene el mismo nivel con $frm['tlevel']
			$IDJoinKey4 = db_fetch_object(db_query("SHOW KEYS FROM {$frm['tlevel']}"));
			if($IDJoinKey4 && !empty($IDJoinKey4->Column_name)){
				$select .= ", ".$frm['tlevel'].".".$IDJoinKey4->Column_name;
				$from .=", ".$frm['tlevel'];
				$where .= " AND ".$frm['tlevel'].".".$IDJoinKey4->Column_name." = ".$Table.".".$IDJoinKey4->Column_name;
			}
		}
		if($idnot)
			$where .=" AND $Table.$joinKey = '$idnot'";
	}
	//********************************** FIN DETERMINAR TABLAS JOINS ***********************************************

	if( !empty($frm['ubicacion']) ){
		if($frm['ubicacion']=="INDICE" || $frm['ubicacion']=="INDICESEC" || $frm['ubicacion']=="MODULO" || $frm['ubicacion']=="SECCION"){
			$where .= " AND FIND_IN_SET('{$frm['ubicacion']}',Noticia.Ubicacion) > 0"; // GROUP BY Noticia.Ubicacion";
		}
	}

	if( !empty($frm['Publicar']) ){
		$where .= " AND Noticia.Publicar = '{$frm['Publicar']}' "; // GROUP BY Noticia.Ubicacion";
	}

	if($field_order=="Entrada.IDPuntoVentaReferencia"){
		$field_order="Entrada.Fecha";
		$frm['in_order'] = " DESC";
	}

	$order = " ORDER BY $field_order {$frm['in_order']} ";

	$qry_string = $select.$from.$where.$between.$order;
	if($k)
		echo $qry_string;

	return $qry_string;

}
//*********************************************** FIN NUEVA FUNCIO MAKEQUERYSTRING *********************************************************
function insertlog($ID_Usuario,$Table,$ID,$transaccion,$operacion)
{
	$IP=get_IP();
	$fechalog=fecha()." ".hora();
	$IDLog=get_maxID("Log","IDLog");
	$operacion = isset($operacion) ? substr($operacion,0,200) : '';
	$sentencia=urlencode($operacion); // sentencia sql realizada en la transaccion
	$sql_log=("INSERT INTO Log (IDLog,IDUsuario,Fecha,Modulo,IDModulo,Transaccion,Operacion,DireccionIP)
				VALUES('$IDLog','$ID_Usuario','$fechalog','$Table','$ID','$transaccion','$sentencia','$IP')");
	db_query($sql_log);
}


function insertlog_acceso($ID_Usuario,$IDPuntoVenta,$Usuario)
{
	$IP = get_IP();
	$IDLog = get_maxID("LogAcceso","IDLog");
	$sql_log = "INSERT INTO LogAcceso (IDLog,IDUsuario,Fecha,IDPuntoVenta,Usuario,DireccionIP)
				VALUES('$IDLog','$ID_Usuario',NOW(),'$IDPuntoVenta','$Usuario','$IP')";

	db_query($sql_log);
}




function get_nodos($cat, $Table, $key, $IDPadre) {
	$idsCat="";
	$qry_cat =db_query("SELECT $key FROM $Table WHERE $IDPadre = '$cat'");

	while($rowcat=db_fetch_object($qry_cat) ) {
		$idCat.="'".$rowcat->$key."'";
		$idCat.=get_nodos($rowcat->$key, $Table, $key, $IDPadre);
		$idCat.="";
	}
	return $idCat;
}// end function


function export2xls($DB_TBLName,$nombre,$sql=""){

//define date for title: EDIT this to create the time-format you need
$now_date = date('m-d-Y H:i');
//define title for .doc or .xls file: EDIT this if you want

$result = db_query($sql);

$title = "Datos Tabla $DB_TBLName Fecha $now_date";

$file_type = "vnd.ms-excel";
$file_ending = "xls";

// Header("Content-Type: application/vnd.ms-excel");

header("Content-Type: application/$file_type");
header("Content-Disposition: attachment; filename=$nombre.$file_ending");
header("Pragma: no-cache");
header("Expires: 0");

echo("$title\n");

//define separator (defines columns in excel & tabs in word)
$sep = "\t"; //tabbed character

//start of printing column names as names of MySQL fields
for ($i = 0; $i < db_num_fields($result); $i++) {
echo db_field_name($result,$i) . "\t";
}
print("\n");
//end of printing column names

//start while loop to get data

while($row = db_fetch_row($result))
{
//set_time_limit(60); // HaRa
$schema_insert = "";
for($j=0; $j < db_num_fields($result);$j++)
{
if(!isset($row[$j]))
$schema_insert .= "NULL".$sep;
elseif ($row[$j] != "")
$schema_insert .= "$row[$j]".$sep;
else
$schema_insert .= "".$sep;
}
$schema_insert = str_replace($sep."$", "", $schema_insert);
//this corrects output in excel when table fields contain \n or \r
//these two characters are now replaced with a space
$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
$schema_insert .= "\t";
print(trim($schema_insert));
print "\n";
}

} // End function

?>
