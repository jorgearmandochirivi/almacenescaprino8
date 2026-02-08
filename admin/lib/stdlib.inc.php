<?php

function nvl(&$var, $default="") {
/* if $var is undefined, return $default, otherwise return $var */

	return isset($var) ? $var : $default;
}


function pv(&$var) {
/* prints $var with the HTML characters (like "<", ">", etc.) properly quoted,
 * or if $var is undefined, will print an empty string.  note this function
 * must be called with a variable, for normal strings or functions use p() */

	echo isset($var) ? htmlSpecialChars(stripslashes($var)) : "";
}

function o($var) {
/* returns $var with HTML characters (like "<", ">", etc.) properly quoted,
 * or if $var is empty, will return an empty string. */

	return empty($var) ? "" : htmlSpecialChars(stripslashes($var));
}

function p($var) {
/* prints $var with HTML characters (like "<", ">", etc.) properly quoted,
 * or if $var is empty, will print an empty string. */

	echo empty($var) ? "" : htmlSpecialChars(stripslashes($var));
}

function Verifica_Sesion(){
	global $COOKIE_SESION,$tiemposession;
	global $PHP_SELF;

	$COOKIE_SESION = $_COOKIE["COOKIE_SESION"];
	$tiemposession = 2000;

	$DATOS=array("flag"=>FALSE);

	//Primero verificar que el cookie este activo

	if (!isset($COOKIE_SESION)){
		header ("Location: login.php?msg=1");

}//if (!isset($COOKIE_SESION))
	else{
		//Limpio las sesiones que tengan mas de 20 minutos

		$limpiaqry=db_query("delete from Sesion where DATE_ADD(Inicio, INTERVAL $tiemposession MINUTE)<now()");

						$fecha=date("Y-m-d H-i-s",time());

		//Ahora sigue Buscar mi sesion
		$findsesionqry=db_query("select Datos from Sesion where IDSesion='$COOKIE_SESION'");

		if (!db_num_rows($findsesionqry)){
			//No hay ningun registro en la BD, osea que expiro
			header ("Location: login.php?msg=2");

}//if (!NumREcords($findsesionqry)){
		else{
			//Encontro la sesion recupero el arreglo y lo envio

			$objeto=db_fetch_object($findsesionqry);
			$arreglo=$objeto->Datos;

			$DATOS=unserialize(StripSlashes($arreglo));
			//ACtualizo la sesio a la hora de la transaccion

			$fechaqry = db_query("update Sesion set Inicio=now() where IDSesion='$COOKIE_SESION'");
		}//if (!NumREcords($findsesionqry)){
	}//if (!isset($COOKIE_SESION)

	return $DATOS;
}//function Verifica_sesion

function Verifica_SesionCliente(){
	global $COOKIE_CLIENTE;
	global $PHP_SELF,$tiemposessioninter;
	global $QUERY_STRING;

	$COOKIE_CLIENTE = $_COOKIE["COOKIE_CLIENTE"];
	$tiemposessioninter = 40;

	$DATOS=array("flag"=>FALSE);

	if($QUERY_STRING){
		$redirect = "./?".rawurlencode($QUERY_STRING);
		$url_vars = "&redirect=$redirect";
	}

	//Primero verificar que el cookie este activo
	if (!isset($COOKIE_CLIENTE)){
		header ("Location: login.php?msg=1$url_vars");
	}//if (!isset($COOKIE_CLIENTE))
	else{	
		//Limpio las sesiones que tengan mas de $tiemposession minutos

		$limpiaqry=db_query("delete from Sesion_Cliente where DATE_ADD(Inicio, INTERVAL $tiemposessioninter MINUTE)<now()");
		//Ahora sigue Buscar mi sesion		
		$findsesionqry=db_query("select Datos from Sesion_Cliente where IDSesion='$COOKIE_CLIENTE'");
		if (!db_num_rows($findsesionqry)){
			//No hay ningun registro en la BD, osea que expiro
			header ("Location: login.php?msg=2$url_vars");
		}//if (!NumREcords($findsesionqry)){
		else{
			//Encontro la sesion recupero el arreglo y lo envio
			$objeto=db_fetch_object($findsesionqry);
			$arreglo=$objeto->Datos;
			$DATOS=unserialize(StripSlashes($arreglo));
			//ACtualizo la sesio a la hora de la transaccion

			$fechaqry = db_query("update Sesion_Cliente set Inicio=now() where IDSesion='$COOKIE_CLIENTE'");
		}//if (!NumREcords($findsesionqry)){
	}//if (!isset($COOKIE_CLIENTE)

	return $DATOS;
}//function Verifica_sesionCliente

function Encabezado(){
	error_reporting(1+4);
	header("Content-type: text/html");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-cache, must-revalidate");
 	header("connection: keep-alive");
	header("Pragma: no-cache");
}

function makeurl_string()
  {
    global $REQUEST_METHOD, $HTTP_GET_VARS, $HTTP_POST_VARS;
    $cgi = $REQUEST_METHOD == 'GET' ? $HTTP_GET_VARS : $HTTP_POST_VARS;

    foreach ($cgi as $key => $value) {
      if ($key != "row"  && !empty($value) && $key != "Submit")
        $query_string .= "&" . $key . "=" . $value;
    }
    echo $query_string;
//    return $query_string;
  }

function auto_back($Nivel,$NivelOk){

	if($Nivel <> $NivelOk)
		echo "<script language=\"JavaScript\" type=\"text/javascript\">
				window.history.go(-1);</script>";

}

function display_msg($msg){

   echo "<center><p GP>";
   //echo "<font face=Arial, Helvetica, sans-serif size=3 color=#000066>" ;
   echo $msg;
   echo "</font></p></center>";

}

function window_alert($msg){

		echo "<script>

      		  alert(\"  $msg \\n\");

			</script> ";

} // End function


function formradiogroup($options,$value,$name) {

	$radiogroup = "";

	foreach($options as $key => $val) {
		$radiogroup .= "<input type=\"radio\" name=\"".$name."\" value=\"".$val."\"";
		if (!empty($value)) {
			$radiogroup .= (($val==$value) ? " checked" : "");
		}
		$radiogroup .= "> ".$key;
	}  // end foreach
	return $radiogroup;
}

function view_field($caption,$field){

	   echo empty($field) ? "" : $caption.$field;

}

function view_field_numeric($caption,$field=""){

	  if(!empty($field) &&  $field<> 0)
	  		echo $caption.number_format($field,2,',','.')."<br>";

}


function formcheckgroup($options,$selection,$name) {

	$checkgroup = "";
	//print_r($selection);
	foreach($options as $key => $val) {
		$checkgroup .= "<input type=\"checkbox\" name=\"".$name."\" value=\"".$val."\"";
		if (!empty($selection)) {
			$checkgroup .= (inarray($val,$selection) ? " checked" : "");
		}
		$checkgroup .= "> ".$key."<br>";
	}  // end foreach
	return $checkgroup;
}

function inArray($value, $val_array) {
	$result = FALSE;
	if (empty($value) && empty($val_array)) {
		return $result; // las dos variables deben ser validas
	} else {
		if (is_array($val_array)) {
			for ($i=0;$i < count($val_array);$i++) {
				if ($val_array[$i] == $value) {
					$result = TRUE;
					break;
				}
			}
		} else {
       if ($val_array==$value)	$result=TRUE;
		}
	}
	return $result;
}

function send_mail($To,$Subject,$Msg,$From) {

	if (mail($To,$Subject,$Msg,"From: ".$From) )
		return TRUE;

}

function formpopup($table,$field,$order,$name,$value,$style,$where = "") {

$popup .= "<select name=\"$name\" class=\"$style\">";
$popup .= "<option value=\"\">[ Seleccione ]</option>";

$sql_where  = "";
if($where != "")
$sql_where = " WHERE ".$where;

$sql = " SELECT * FROM $table ".$sql_where."ORDER BY $order ";
$qry = db_query($sql);

while ($r = db_fetch_object($qry) ) {
$popup .= "<option value=".$r->$name;

$popup .= (($r->$name==$value) ? " selected='selected'" : "");

$popup .=  " >".$r->$field." ".$r->Apellidos."</option>";

} // End while

$popup .= "</select>";

return $popup;

} // End function

function formpopuparray($options,$selection,$name) {

	$checkgroup = "<select name='$name' class=inputSelect><option value=''>[Seleccione]</option>";

	foreach($options as $key => $val) {
	$checkgroup .= "<option value=\"".$key."\"";
	if (!empty($selection) && $selection == $key)
	$checkgroup .= " selected";

	$checkgroup .= "> ".$val."</option>";
	} // end foreach

	$checkgroup .= "</select>";

	return $checkgroup;
}

function get_field($table,$field,$key,$value) {

$qry = db_query(" SELECT $field FROM $table WHERE $key = '$value'  ");

$r = db_fetch_object($qry);

return $r->$field;

} // End function

function formlistbox($max, $name, $field, $class){
	echo"<select class='$class' name='$name'>
	<option></option>";
		for($i=1;$i<=$max;$i++){
			echo "<option value=$i ";
			if($field == $i)
				echo "selected";
			echo ">$i</option>";
		}
	echo "</select>";
}

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Francisco Mu?oz :
	Iniciado: May 31/2005
	Ultima Modificaci?n: May 31/2005
*******************************************************************************************/
/*******************************************************************************************
	get_maxID: Obtiene el maximo valor de claves primarias registradas en una tabla
	Parametros:
			$table: Tabla de la cual queremos hallar el maximo
			$Key: Campo o clave prinmaria de la tabla
	Retorna:
			$newid: El maximo id+1 en caso de que existan registos, sino retorna el numero 1
*******************************************************************************************/

function get_maxID($table,$key){
$qid = db_query("SELECT MAX($key) AS maximo FROM $table");
$result = db_fetch_object($qid);
if (isset ($result->maximo))
	return $newid = $result->maximo + 1;
else
	return $newid = 1;

}//End Function get_maxID



function table_check_list($Table,$Key,$key_value,$table_option,$key_option,$table_reference,$check_name,$newmode){

$str_qry = "SELECT $key_option FROM $table_reference WHERE $Key = $key_value ";

if($newmode <> "insert")
	$qry_option = db_query($str_qry);

$option_checked = array();

while($option = db_fetch_object($qry_option))
	$option_checked[] = $option->$key_option;

$qry = db_query("SELECT * FROM $table_option ");

$array_option = array();

while ($option = db_fetch_object($qry))
	$array_option[$option->Nombre] = $option->$key_option;

echo formcheckgroup($array_option,$option_checked,$check_name);

}

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Francisco Mu?oz :
	Iniciado: May 31/2005
	Ultima Modificaci?n: May 31/2005
*******************************************************************************************/
/*******************************************************************************************
	fecha: Devuelve la fecha en el formato y-m-d
	Parametros:
			Void
	Retorna:
			Devuelve la fecha en el formato y-m-d
*******************************************************************************************/
function fecha(){
	return date("Y-m-d");
}

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Francisco Mu?oz :
	Iniciado: May 31/2005
	Ultima Modificaci?n: May 31/2005
*******************************************************************************************/
/*******************************************************************************************
	hora: Devuelve la hora en el formato h:m:s
	Parametros:
			Void
	Retorna:
			Devuelve la hora en el formato h:m:s
*******************************************************************************************/
function hora(){
	return date("H:i:s");
}

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Francisco Mu?oz :
	Iniciado: May 31/2005
	Ultima Modificaci?n: May 31/2005
*******************************************************************************************/
/*******************************************************************************************
	vars_LOG: Inserta los datos de usuario hora y fecha de modificacion o creacion de algun formulario
	Parametros:
			$frm: Array con los datos del formulario
	Retorna:
			$frm: Array con los datos del formulario y los n uevos datos de usuario hora y fecha de modificacion o creacion
*******************************************************************************************/

function vars_LOG($frm){
	GLOBAL $Nombre_Usuario,$Table,$Key;
	$sql= "SELECT UsuarioTrCr,FechaTrCr,UsuarioTrEd,FechaTrEd FROM $Table WHERE $Key = '{$_POST['ID']}'";
	$qry=db_query($sql);
	$r = db_fetch_object($qry);

	$now=fecha()." ".hora();
	if(isset($_POST['action']) && $_POST['action']=="insert"){
		$frm['UsuarioTrCr']=$Nombre_Usuario;
		$frm['FechaTrCr']=$now;
		$frm['UsuarioTrEd']=$r->UsuarioTrEd;
		$frm['FechaTrEd']=$r->FechaTrEd;
	}
	if(isset($_POST['action']) && $_POST['action']=="update"){
		$frm['UsuarioTrEd']=$Nombre_Usuario;
		$frm['FechaTrEd']=$now;
		$frm['UsuarioTrCr']=$r->UsuarioTrCr;
		$frm['FechaTrCr']=$r->FechaTrCr;
	}
	return $frm;
}

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Francisco Mu?oz :
	Iniciado: May 31/2005
	Ultima Modificaci?n: May 31/2005
*******************************************************************************************/
/*******************************************************************************************
	formatofecha: Cambia el formato de fecha y-m-d a "mes ## de ####"
	Parametros:
			$frm: Array con los datos del formulario
	Retorna:
			$frm: Array con los datos del formulario y los n uevos datos de usuario hora y fecha de modificacion o creacion
*******************************************************************************************/
function formatofecha($fecha) {
	Global $Mes_array;
	$anio = substr($fecha,0,4);
	$mes = substr($fecha,5,-3);
	$dia = substr($fecha,8);
	return $formato = $Mes_array[$mes-1]." ".$dia." de ".$anio;

} // End function

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Francisco Mu?oz :
	Iniciado: May 31/2005
	Ultima Modificaci?n: May 31/2005
*******************************************************************************************/
/*******************************************************************************************
	formatofecha: Cambia el formato de fecha y-m-d a "mes ## de ####"
	Parametros:
			$frm: Array con los datos del formulario
	Retorna:
			$frm: Array con los datos del formulario y los n uevos datos de usuario hora y fecha de modificacion o creacion
*******************************************************************************************/
function view_max_words($texto,$max) {
	$words = split(" ",$texto);
	for($i=0;$i < $max; $i++){
		$short_text .= $words[$i]." ";
	}//end for
	echo rtrim($short_text);
}

/*******************************************************************************************
		funtcion formpopuphora
*******************************************************************************************/


function formpopuphora($fecha,$name,$style) {

	$horasvalue=array("00:00:00","00:30:00","01:00:00","01:30:00","02:00:00","02:30:00","03:00:00","03:30:00","04:00:00",
	"04:30:00","05:00:00","05:30:00","06:00:00","06:30:00","07:00:00","07:30:00","08:00:00","08:30:00","09:00:00",
	"09:30:00","10:00:00","10:30:00","11:00:00","11:30:00","12:00:00","12:30:00","13:00:00","13:30:00","14:00:00",
	"14:30:00","15:00:00","15:30:00","16:00:00","16:30:00","17:00:00","17:30:00","18:00:00","18:30:00","19:00:00",
	"19:30:00","20:00:00","20:30:00","21:00:00","21:30:00","22:00:00","22:30:00","23:00:00","23:30:00");

	$horamostrar=array("12:00 am","12:30 am","01:00 am","01:30 am","02:00 am","02:30 am","03:00 am","03:30 am","04:00 am",
	"04:30 am","05:00 am","05:30 am","06:00 am","06:30 am","07:00 am","07:30 am","08:00 am","08:30 am","09:00 am",
	"09:30 am","10:00 am","10:30 am","11:00 am","11:30 am","12:00 pm","12:30 pm","01:00 pm","01:30 pm","02:00 pm",
	"02:30 pm","03:00 pm","03:30 pm","04:00 pm","04:30 pm","05:00 pm","05:30 pm","06:00 pm","06:30 pm","07:00 pm",
	"07:30 pm","08:00 pm","08:30 pm","09:00 pm","09:30 pm","10:00 pm","10:30 pm","11:00 pm","11:30 pm");

	$popup .= "<select name=\"$name\" class=\"$style\">";
	$popup .= "<option value=\"\">[ Seleccione ]</option>";

	foreach($horasvalue as $key=>$horavalue){
		$popup .= "<option value=".$horavalue;

		$popup .= (($horavalue==$fecha) ? " selected" : "");

		$popup .=  " >".$horamostrar[$key]."</option>";

	} // End foreach

	$popup .= "</select>";

	return $popup;

} // End function formpopuphora

function repetition($reps=2) {

        static $skip = 1;
        if ($skip++ == $reps) {
                $skip=1;
                return TRUE;
        } else {
                return FALSE;
        }
}

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Francisco Mu?oz :
	Iniciado: May 31/2005
	Ultima Modificaci?n: May 31/2005
*******************************************************************************************/
/*******************************************************************************************
	Fotos: Inserta imagenes en un texto que contenga las palabras reservadas [Foto1], [Foto2]... [Foto(n)]
	Parametros:
			$texto: Cadena donde se reemplazara la(s) palabra(s) reservada(s) [Foto(n)]
			$r: Array con el contenido de los datos de la foto tales como alineacion, numero de foto, derechos y pie de foto
	Retorna:
			$texto: Cadena con la(s) imagene(s) reemplazando la(s) palabra(s) reservada(s) [Foto(n)]
*******************************************************************************************/
function Fotos($texto,$r) {
	global $imagedir,$url;
	for($i=1;$i<=4;$i++){
		$num_foto = "Foto".$i;
		$derechos = "DerechosFoto".$i;
		$pie = "PieFoto".$i;
		$alineacion = "AlineacionFoto".$i;

		$root=$url."/img/noticia/";
	if($r->$num_foto)
		$foto[$i] = "
			<TABLE align=".$r->$alineacion." cellspacing=2 cellpadding=2 border=0 width=100>
				<TR>
					<TD class=piefoto>
						<img src='".$root.$r->$num_foto."' hspace=2 border=0>
						".$r->$derechos."<br>".$r->$pie."
					</TD>
				</TR>
			</TABLE>
		";
	}
	$lista_fotos = array(
		"[Foto1]" => "$foto[1]",
		"[Foto2]" => "$foto[2]",
		"[Foto3]" => "$foto[3]",
		"[Foto4]" => "$foto[4]"
	);

	return $texto  = strtr($texto, $lista_fotos);
}//End function Fotos


/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Francisco Mu?oz :
	Iniciado: May 31/2005
	Ultima Modificaci?n: May 31/2005
*******************************************************************************************/
/*******************************************************************************************
	usr_datos: Retorna el nombre compoleto del usuario
	Parametros:
			$ID_Usuario: Clave primaria del usuario en la tabla de usuarios
	Retorna:
			Nombre y apellido del Usuario
*******************************************************************************************/
function usr_datos($ID_Usuario){
	$nombre=get_field("Empleado","Nombre","IDEmpleado",$ID_Usuario);
	$apellido=get_field("Empleado","Apellidos","IDEmpleado",$ID_Usuario);
	return $nombre." ".$apellido;
}


/************* traer ip *******************/
/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Francisco Mu?oz :
	Iniciado: May 31/2005
	Ultima Modificaci?n: May 31/2005
*******************************************************************************************/
/*******************************************************************************************
	get_IP: Captura la IP del usuario que accede a la aplicaci?n
	Parametros:
			Void
	Retorna:
			$ip: La IP del Usuario
*******************************************************************************************/
function get_IP()
{
	if(getenv("HTTP_CLIENT_IP"))
	{
		$ip = getenv("HTTP_CLIENT_IP");
	} else
	if(getenv("HTTP_X_FORWARDED_FOR")) {
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	} else {
		$ip = getenv("REMOTE_ADDR");
		}
return $ip;
}

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Jimmy Velandia :
	Modificada por: Francisco Mu?oz
	Iniciado: May 31/2005
	Ultima Modificaci?n: Jun 30/2005
*******************************************************************************************/
/*******************************************************************************************
	formatohora: Retorna la hora en el formato ##:##:## am/pm
	Parametros:
			$hora: formato de la hora ##:##:##
	Retorna:
			$hora: Nuevo formato ##:##:## am/pm
*******************************************************************************************/
/*******************************************************************************************
		funtcion formatohora
*******************************************************************************************/

function formatohora($hora) {
	$time = explode(":",$hora);
	$hora = $time[0];
	$min = $time[1];
	$seg = $time[2];

	if($hora > 12){
		$hora = $hora-12;
		$merid = "pm";
		if($hora<10)
			$hora = "0$hora";
	}
	else
		$merid = "am";

	return "$hora:$min:$seg $merid";

} // End function formpopuphora

/******** Inicio Funciones para Capacitacion **************************/


function get_join_field($table,$jointo,$field,$forein,$value) {

$qry = db_query(" SELECT T2.$field
					FROM $table T1,$jointo T2
					WHERE T1.$forein = T2.$forein AND  T2.$forein = '$value'  ");

$r = db_fetch_object($qry);

return $r->$field;

} // End function

function formpopup2($table,$key,$field,$order,$name,$value,$style) {

$popup .= "<select name=\"$name\" class=\"$style\">";
$popup .= "<option value=\"\">[ Seleccione ]</option>";

$qry = db_query(" SELECT * FROM $table ORDER BY $order ");

while ($r = db_fetch_object($qry) ) {
$popup .= "<option value=".$r->$key;

$popup .= (($r->$key==$value) ? " selected" : "");

$popup .=  " >".$r->$field."</option>";

} // End while

$popup .= "</select>";

return $popup;

} // End function

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creado por: Francisco Mu?oz
	Iniciado: May 31/2005
	Ultima Modificaci?n: may 31/2005
*******************************************************************************************/
/*******************************************************************************************
	ponderado: Calcula el ponderado de un puntaje
	Parametros:
			$correctas: Numero de respuestas correctas
			$total: Total de preguntas
	Retorna:
			$r->Nombre: El nombre en la Tabla Cap_Ponderado para ese puntaje (Excelente, Bueno, etc).
*******************************************************************************************/

function ponderado($correctas, $total){
	Global $calificacion;
	$resultado = $correctas/$total;
//	$resultado = $correctas/10;
	$query = db_query(" SELECT * FROM Cap_Ponderado WHERE '$resultado' BETWEEN Pond_INI AND Pond_FIN");
	$r = db_fetch_object($query);
	return $r->Nombre;
}//end function ponderado

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creado por: Francisco Mu?oz
	Iniciado: May 31/2005
	Ultima Modificaci?n: may 31/2005
*******************************************************************************************/
/*******************************************************************************************
	pesoPregunta: Calcula el peso asignado a una pregunta con respecto al numero de preguntas total y a su puntaje
	Parametros:
			$IdExamenEstudiante: Clave primaria del examen presentado por el alumno para hallar a que examen pertenece
	Retorna:
			Calculo del peso de esa pregunta
*******************************************************************************************/
function pesoPregunta($IdExamenEstudiante){
	$IDExamen = get_field("Cap_ExamenEstudiante","IdExamen","IdExamenEstudiante",$IdExamenEstudiante);
	$sql_Peso = "
		SELECT R.IdPregunta
		FROM Cap_Pregunta P, Cap_RespuestaExamenEstudiante R
		WHERE R.IdExamenEstudiante = '$IdExamenEstudiante'
		AND P.IdPregunta = R.IdPregunta
		GROUP BY R.IdPregunta
	";
	$total = 0;
	$qry_Peso = db_query($sql_Peso);
	while($r_Peso = db_fetch_object($qry_Peso)){
		$puntaje = get_puntaje_rta($IdExamenEstudiante,$r_Peso->IdPregunta);
		$Peso = get_field("Cap_PesoPregunta","Peso","IdExamen",$IDExamen."' AND IdPregunta='$r_Peso->IdPregunta");
		if($Peso>0){
			$total += $Peso;
			$Nota = $Peso * $puntaje;
		}else{
			$total ++;
			$Nota = $puntaje;
		}

		$sum += $Nota;
		//echo "Puntaje: $puntaje   ->  Peso: $Peso   ->    Nota: $Nota<br>";
	}
	//echo "<br>($sum*100)/$total";
	return ($sum*100)/$total;
}// end function pesoPregunta

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creado por: Francisco Mu?oz
	Iniciado: May 31/2005
	Ultima Modificaci?n: may 31/2005
*******************************************************************************************/
/*******************************************************************************************
	get_puntaje_rta: Retorna el puntaje dado por el docente de la respuesta abierta
	Parametros:
			$IdExamenEstudiante: Clave primaria del examen presentado por el alumno para hallar a que examen pertenece
			$IdPregunta: Clave primaria de la pregunta
	Retorna:
			Retorna el puntaje dado por el docente de la respuesta abierta
*******************************************************************************************/
function get_puntaje_rta($IdExamenEstudiante,$IdPregunta){
	 $sql = "
		SELECT R.IDTipoRespuesta
		FROM Cap_Respuesta R,Cap_RespuestaExamenEstudiante RE
		WHERE RE.IdPregunta = '$IdPregunta'
		AND RE.IdExamenEstudiante = '$IdExamenEstudiante'
		AND RE.IdRespuesta = R.IdRespuesta
		AND RE.IdPregunta = R.IdPregunta
	";
	$qry = db_query($sql);
	if(db_num_rows($qry)){ //Es cualquier tipo de pregunta excepto abierta
		$r = db_fetch_object($qry);
		if($r->IDTipoRespuesta==1) // es correcto??
			return 1;
		else
			return 0;
	}
	else{ //Es pregunta abierta
		$IDRA = get_field("Cap_RespuestaExamenEstudiante","IdPuntajeRtaAbierta","IdExamenEstudiante",$IdExamenEstudiante."' AND IdPregunta = '$IdPregunta");
		return get_field("Cap_RtaAbierta","Puntaje","IdPuntajeRtaAbierta",$IDRA);
	}
}// end function get_puntaje_rta

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creado por: Francisco Mu?oz
	Iniciado: May 31/2005
	Ultima Modificaci?n: may 31/2005
*******************************************************************************************/
/*******************************************************************************************
	get_correctas_RA: Retorna el puntaje dado por el docente de la respuesta abierta si es mayor a 0.5
	Parametros:
			$IdExamenEstudiante: Clave primaria del examen presentado por el alumno para hallar a que examen pertenece
	Retorna:
			Retorna el puntaje dado por el docente de la respuesta abierta si es mayor a 0.5
*******************************************************************************************/
function get_correctas_RA($IdExamenEstudiante){
	$sql_RA = "SELECT count(RA.Puntaje) as CorrectasRA
	FROM Cap_RtaAbierta RA, Cap_RespuestaExamenEstudiante RE
	WHERE RE.IdExamenEstudiante ='$IdExamenEstudiante'
	AND RA.IdPuntajeRtaAbierta = RE.IdPuntajeRtaAbierta
	AND RA.Puntaje > 0.5
	AND RE.IdPuntajeRtaAbierta > '0'";
	$r = db_fetch_object(db_query($sql_RA));
	return $r->CorrectasRA;
}// end function get_correctas_RA

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creado por: Francisco Mu?oz
	Iniciado: May 31/2005
	Ultima Modificaci?n: may 31/2005
*******************************************************************************************/
/*******************************************************************************************
	get_correct: Obtiene el numero de respuestas correctas de un examen
	Parametros:
			$IdExamen: Examen en el cual esta la pregunta
			$IdExamenEstudiante: Clave primaria del examen presentado por el alumno para hallar a que examen pertenece
	Retorna:
			Retorna el numero de respuestas correctas de un examen
*******************************************************************************************/
function get_correct($IdExamen,$IdExamenEstudiante){

	$sql = "SELECT count(EE.IdExamenEstudiante) AS Total
			FROM Cap_ExamenEstudiante EE,Cap_RespuestaExamenEstudiante RE,Cap_Pregunta P,Cap_Respuesta R
			WHERE RE.IdPregunta = P.IdPregunta
			AND P.IdPregunta = R. IdPregunta
			AND RE.IdRespuesta = R.IdRespuesta
			AND R.IdTipoRespuesta = 1
			AND RE.IdExamenEstudiante = EE.IdExamenEstudiante
			AND EE.IdExamen = '$IdExamen'
			AND EE.IdExamenEstudiante = '$IdExamenEstudiante'
			GROUP BY EE.IdExamenEstudiante";

	$r = db_fetch_object(db_query($sql));

	if($r->Total)
		return $r->Total;
	else
		return 0;

}

/******** Fin Funciones para Capacitacion **************************/



/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por: Francisco Mu?oz
	Iniciado: May 18/2005
	Ultima Modificaci?n: May 18/2005
*******************************************************************************************/
/*******************************************************************************************
	set_visitas: Coloca el numero de visitas de un modulo
	Parametros:
			$m = Modulo al que se ingresa
			$IDUsr = Usuaio quien accede al modulo
	Retorna:
			El contador de visitas de un modulo
*******************************************************************************************/
function set_visitas($m,$IDUsr){
	//capturamos las variables a almacenar en la tabla VisitasSite
	if($m)
		$IDM=get_field("ModuloSite","IDModuloSite","DirectorioModulo",$m); //el usuario ingreso a algun modulo
	else
		$IDM=-1; //el usuario Ingreso a la pantalla principal

	$IDV=get_maxID(VisitasSite,IDVisitasSite);
	$fecha = fecha();
	$hora = hora();
	$IP=get_IP();
	$OS = get_os();
	$Browser = get_navigator();

	//Si es el mismo usuario el mismo dia y en el mismo modulo y la misma IP entonces incrementamos el contador
	if($IDVisita = get_field("VisitasSite","IDVisitasSite","Fecha",$fecha."' AND  IDUsuario = $IDUsr AND IDModuloSite = '$IDM' AND IP = '$IP"))
		$sql = "UPDATE VisitasSite SET Contador = Contador+1, Hora = '$hora' WHERE IDVisitasSite=$IDVisita";
	else //sino lo agregamos como un nuevo registro
		$sql = "INSERT INTO VisitasSite (IDVisitasSite,IDModuloSite,IDUsuario,Contador,Fecha,Hora,OS,Browser,IP)
			VALUES ($IDV,'$IDM',$IDUsr,1,'$fecha','$hora','$OS','$Browser','$IP')";
	db_query($sql);
}


/*******************************************************************************************
	Mensaje_Info: Coloca un mensaje de informacion para el usuario.
	Parametros:
			$mensaje = Mensaje que se quiere mostrar al usuario (string)
	Retorna:
			Imprime la tabla con el mensaje que se desea mostrar.
*******************************************************************************************/
function Mensaje_info($mensaje,$alineacion="center",$estilo="category")
{
	echo("<table width=100% border=0 cellpadding=0 cellspacing=0 align=center ><tr><td class=$estilo align=$alineacion><img src=\"images/iconalert.gif\" border=0 > $mensaje</td></tr></table>");
}

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por: Francisco Mu?oz
	Iniciado: Jun 20/2005
	Ultima Modificaci?n: Jun 20/2005
*******************************************************************************************/
/*******************************************************************************************
	get_os: Captura el Sistema Operatico del usuario que visita la web
	Parametros:
			Void
	Retorna:
			El nombre del navegador y el icono del Sistema Operativo .
*******************************************************************************************/

function get_os(){
	$user = $GLOBALS[HTTP_USER_AGENT];

	$os = "Onbekend"; $img="unknow.gif";

	if(strpos($user, "Linux"))  { $os = "Linux"; $img="linux.gif";}
	if(strpos($user, "Unix"))  { $os = "Unix"; $img="unix.gif";}
	if(strpos($user, "Mac"))  { $os = "MacOS"; $img="mac.gif";}
	if(strpos($user, "FreeBSD"))  { $os = "FreeBSD"; $img="freebsd.gif";}
	if(strpos($user, "BEOS"))  { $os = "BeOS"; $img="beos.gif";}

	if(strpos($user, "Windows"))
	{
	 $os = "Windows"; $img="windows.gif";
	if(strpos($user, "95")) { $os = "Windows 95"; $img="windows.gif";}
	if(strpos($user, "98")) { $os = "Windows 98"; $img="windows.gif";}
	if(strpos($user, "SE")) { $os = "Windows 98SE"; $img="windows.gif";}
	}

	if(strpos($user, "Windows NT 5.0"))  { $os = "Windows 2000"; $img="windows.gif";}
	if(strpos($user, "Windows NT 5.1"))  { $os = "Windows XP"; $img="windows.gif";}
	if(strpos($user, "Windows XP"))  { $os = "Windows XP"; $img="winxp.gif";}
	if(strpos($user, "Windows NT 5.2"))  { $os = "Windows Server 2003"; $img="windows.gif";}

	// Waarschijnlijke useragents:
	if(strpos($user, "Windows NT 5.3"))  { $os = "Windows Longhorn"; $img="longhorn.gif";}
	if(strpos($user, "Windows NT 5.4"))  { $os = "Windows Blackcomb"; $img="blackcomb.gif";}

	return "$os&$img";

}// end function get_os

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Francisco Mu?oz :
	Iniciado: May 04/2005
	Ultima Modificaci?n: May 20/2005
*******************************************************************************************/
/*******************************************************************************************
	get_permiso: Obtiene los permisos del usuario de la Intranet para el modo de acceso a un modulo o seccion
	Parametros:
			$ID_Usuario: Identificador del usuario logueado
			$m: Modulo en el que se encuentra el usuario
			$tabla: Identificador de la tabla
	Retorna:
			$permiso: Array que contiene el permiso, la gerencia y el modulo del usuario logueado
	Nota:
			Ppara agregar un nuevo modulo se agrega en el case ese nuevo modulo $m y en
			$condicion se agrega la sentencia sql que evalua los accesos de ese nivel
*******************************************************************************************/



function get_permiso($ID_Usuario,$m,$tabla){
GLOBAL $Nivel;
	if( $Nivel <> 0  )
	{
		 	$sql = "
			SELECT P.*,  G.IDGrupoUsuarios, R.*, T.*
			FROM Permisos P, ModuloSite M, GrupoUsuarios G, RestriccionxUsuario R, ModuloSite_Tabla MT, Tabla T
			WHERE R.IDEmpleado = '$ID_Usuario'
			AND R.IDGrupoUsuarios = G.IDGrupoUsuarios
			AND G.IDGrupoUsuarios = P.IDGrupoUsuarios
			AND M.DirectorioModulo = '$m'
			AND P.IDModuloSite = M.IDModuloSite
			AND T.Descripcion = '$tabla'
			AND T.IDTabla = MT.IDTabla
			AND MT.IDModuloSite = M.IDModuloSite
			AND P.IDSeccionSite = MT.IDTabla
			GROUP BY G.IDGrupoUsuarios
		";



		$qry = db_query($sql);
		$r = db_fetch_object($qry);
		$permiso = array($r->Permiso,$m,$tabla);
		return $permiso;
	}
	else
	{
		$permiso = array(3, $m, $tabla);
		return $permiso;
	}
}// end function

/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por Francisco Mu?oz :
	Iniciado: May 31/2005
	Ultima Modificaci?n: May 31/2005
*******************************************************************************************/
/*******************************************************************************************
	get_permisos_admin: Obtiene los permisos del usuario del admin para el modo de acceso a un modulo
	Parametros:
			$IDM: Identificador del modulo al que quiere acceder
	Retorna:
			false: En caso de que no tenga permisos ni de lectura ni escritura
			true: En caso de tener permisos de Lectura escritura o solo lectura
*******************************************************************************************/
function get_permisos_admin($IDM){
	GLOBAL $ID_Usuario,$Nivel;
	if($Nivel <> 0)
	{
		$IDG = get_field("RestriccionXAdmin","IDGrupoAdmin","IDUsuario",$ID_Usuario);
		if(!$IDG) return 3; //Si el usuario no esta en algun grupo se permite el acceso completo
		$perm = get_field("PermisoAdm","Permiso","IDGrupoAdmin",$IDG."' AND IDModuloAdmin='$IDM");
		if($perm==1){
			include "denegado.php";
			return 0;
		}
		else
			return $perm;
	}

}


/*******************************************************************************************
	Libreria de funciones b?sicas para php3
	Creador por: Francisco Mu?oz
	Iniciado: Jun 20/2005
	Ultima Modificaci?n: Jun 20/2005
*******************************************************************************************/
/*******************************************************************************************
	get_navigator: Captura el explorador del usuario que visita la web
	Parametros:
			Void
	Retorna:
			El nombre del navegador y el icono del explorador .
*******************************************************************************************/

function get_navigator(){
	$user = $GLOBALS[HTTP_USER_AGENT];
	$browser = "Onbekend"; $img = "unknow.gif";

	// De volgende strings zijn getest:
	if(strpos($user, "Mozilla")) { $browser = "Mozilla"; $img = "mozzila.gif"; }
	if(strpos($user, "Firebird")) { $browser = "Mozilla Firebird"; $img = "mozzila.gif"; }
	if(strpos($user, "Firefox")) { $browser = "Mozilla Firefox"; $img = "mozzila.gif"; }
	if(strpos($user, "Netscape")) { $browser = "Netscape"; $img = "ns.gif"; }
	if(strpos($user, "Netscape6/")) { $browser = "Netscape 6"; $img = "ns.gif"; }
	if(strpos($user, "Netscape/7.1")) { $browser = "Netscape 7.1"; $img = "ns.gif"; }
	if(strpos($user, "Mozilla/4")) { $browser = "Netscape 4.0"; $img = "ns.gif";}
	if(strpos($user, "MSIE"))  { $browser = "Microsoft Internet Explorer"; $img = "ie.gif"; }
	if(strpos($user, "MSIE 6.0")) { $browser = "Microsoft Internet Explorer 6"; $img = "ie.gif";}
	if(strpos($user, "Opera")) { $browser = "Opera"; $img = "opera.gif"; }

	// Volgende strings zijn niet getest:
	if(strpos($user, "MSIE 5.0")) { $browser = "Microsoft Internet Explorer 5"; $img = "ie.gif";}
	if(strpos($user, "MSIE 5.5")) { $browser = "Microsoft Internet Explorer 5.5"; $img = "ie.gif";}
	if(strpos($user, "MSIE 4.0")) { $browser = "Microsoft Internet Explorer 4.0"; $img = "ie4.gif";}
	if(strpos($user, "Konqueror")) { $browser = "Konqueror"; $img = "konqueror.gif";}
	if(strpos($user, "SAGEM")) { $browser = "Sagem WAP"; $img = "gsm.gif";}

	if(strpos($user, "Nautilus")) { $browser = "Nautilus"; $img = "nautilus.gif";}
	if(strpos($user, "Lynx")) { $browser = "Lynx"; $img = "lynx.gif";}
	if(strpos($user, "Galeon")) { $browser = "Galeon"; $img = "galeon.gif";}
	if(strpos($user, "Safari")) { $browser = "Safari"; $img = "safari.gif";}
	if(strpos($user, "Kameleon")) { $browser = "Kameleon"; $img = "kameleon.gif";}

	return "$browser&$img";
} // end function get_$navigator

/****** funciones encrypt ********/

function get_rnd_iv($iv_len) {
	$iv = '';
	while ($iv_len-- > 0) {
		$iv .= chr(mt_rand() & 0xff);
	}
	return $iv;
}
function md5_encrypt($plain_text,$password,$iv_len=16){
	$plain_text.="\x13";
	$n=strlen($plain_text);
	if($n%16)$plain_text.=str_repeat("\0",16-($n%16));
	$i=0;
	$enc_text=get_rnd_iv($iv_len);
	$iv=substr($password^$enc_text,0,512);
	while($i<$n){
	$block=substr($plain_text,$i,16)^pack('H*',md5($iv));
	$enc_text.=$block;
	$iv=substr($block.$iv,0,512)^$password;
	$i+=16;
	}
	return base64_encode($enc_text);
}
function md5_decrypt($enc_text,$password,$iv_len=16){
	$enc_text=base64_decode($enc_text);
	$n=strlen($enc_text);
	$i=$iv_len;
	$plain_text='';
	$iv=substr($password^substr($enc_text,0,$iv_len),0,512);
	while($i<$n){
	$block=substr($enc_text,$i,16);
	$plain_text.=$block^pack('H*',md5($iv));
	$iv=substr($block.$iv,0,512)^$password;
	$i+=16;
	}
	return preg_replace('/\\x13\\x00*$/','',$plain_text);
}

function make_safe($variable){
	$variable = addslashes(trim($variable));
	return $variable;
}//end function

/****** End funciones encrypt ******/


function inserta_log_envio($IDEmailFidelizacion,$id_cliente){
	$sql_inserta_log="Insert into DetalleEnvioMsj (IDEmailFidelizacion, IDCliente, FechaEnvio) Values ('".$IDEmailFidelizacion."', '".$id_cliente."', NOW())";
	$qry_inserta_log=db_query($sql_inserta_log);
}



/****** FUNCIONES DE ENVIO DE CORREOS AUTOMATICOS ******/
function envia_bienvenida_club($id_cliente){

	include_once("class.phpmailer.php");
	include_once("class.smtp.php");

	$nombre=get_field("Cliente","Nombre","IDCliente",$id_cliente);
	$apellido=get_field("Cliente","Apellido","IDCliente",$id_cliente);
	$correo=get_field("Cliente","EMail","IDCliente",$id_cliente);
	$nombre_cliente=$nombre. " " . $apellido;
	$mensaje=get_field("EmailFidelizacion","Mensaje","IDEmailFidelizacion",1);
	$Msg=str_replace("[Nombre]",$nombre_cliente,$mensaje);

	$url_baja="http://www.calzadocaprino.com";
	$mail = new phpmailer();
	$mail->Subject=get_field("EmailFidelizacion","Asunto","IDEmailFidelizacion",1);
	$mail->Body =$Msg;
	$mail->IsHTML(true);
	$mail->Sender = get_field("EmailFidelizacion","Replyto","IDEmailFidelizacion",1);
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = true;
	$mail->From = get_field("EmailFidelizacion","EmailRemitente","IDEmailFidelizacion",1);
	$mail->FromName = get_field("EmailFidelizacion","NombreRemitente","IDEmailFidelizacion",1);
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
	$mail->AddAddress($correo);
	$confirm=$mail->Send();

}

function envia_bono_cliente($id_cliente,$id_bonos){

global $dirroot;

	include_once("class.phpmailer.php");
	include_once("class.smtp.php");
	$mail = new phpmailer();

	
	$nombre=get_field("Cliente","Nombre","IDCliente",$id_cliente);
	$apellido=get_field("Cliente","Apellido","IDCliente",$id_cliente);
	$cedula=get_field("Cliente","Cedula","IDCliente",$id_cliente);
	$correo=get_field("Cliente","EMail","IDCliente",$id_cliente);
	$nombre_cliente=$nombre. " " . $apellido;
	$mensaje=get_field("EmailFidelizacion","Mensaje","IDEmailFidelizacion",10);
	$Msg=str_replace("[Nombre]",$nombre_cliente,$mensaje);

	if (is_array($id_bonos) && count($id_bonos)>0){
		
		foreach($id_bonos as $id_bono_value){
			$id_bono=$id_bono_value;
			$fecha_vencimiento=get_field("BonoFidelizacion","FechaVencimiento","IDBonoFidelizacion",$id_bono);
			$valor_bono=get_field("BonoFidelizacion","Valor","IDBonoFidelizacion",$id_bono);

			
			$cuerpo_bono=get_field("EmailFidelizacion","Mensaje","IDEmailFidelizacion",10);
			/*
			$cuerpo_bono=str_replace("[NumeroBono]",$id_bono_value,$cuerpo_bono);
			$cuerpo_bono=str_replace("[FechaVencimiento]",$fecha_vencimiento,$cuerpo_bono);
			$cuerpo_bono=str_replace("[ValorBono]",$valor_bono,$cuerpo_bono);
			$cuerpo_bono=str_replace("[NombreCliente]",$nombre_cliente,$cuerpo_bono);
			$cuerpo_bono=str_replace("[DocumentoCliente]",$cedula,$cuerpo_bono);
			*/

			$valorNumericoBono=(int)$id_bono_value*21+133;	  
			
        	$ImagenCodigo="BonoCaprino-".$valorNumericoBono.".png";

			$url = "http://www.almacenescaprino.com/files/codigobarras/".$ImagenCodigo;        
			
			if(! @ file_get_contents($url)){
				echo 'path doesnt exist';
			}
			else{         
				$cuerpo_bono=str_replace("[ImagenCodigoBarras]",$ImagenCodigo,$cuerpo_bono);			   

				/*
				$filedir = $dirroot . "../files/bonos/";
				$name = "Bono" . $id_bono_value . ".html";
				$namePDF = "Bono" . $id_bono_value . ".pdf";
				$file = "$filedir$name";
				$filepdf = "$filedir$namePDF";

				ob_start();
				echo $cuerpo_bono;
				$page = ob_get_contents();
				$fw = fopen($file, "w");
				fputs($fw,$page,strlen($page));
				fclose($fw);
				ob_end_clean();
				echo $page;
				passthru("/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf");
				$msj_bonos.=$cuerpo_bono;
				$mail->AddAttachment($filepdf,"Bono".$id_bono_value.".pdf");
				*/
			}
				


			
		}
	}



	$Msg=str_replace("[Bono]",$msj_bonos,$Msg);

	$url_baja="http://www.calzadocaprino.com";

	//$mail->Subject=get_field("EmailFidelizacion","Asunto","IDEmailFidelizacion",8);
	$Asunto= "Hola! ". $nombre_cliente . " por tu fidelidad has ganado un bono de $35.000";
	$mail->Body =$cuerpo_bono;
	$mail->IsHTML(true);
	$mail->Sender = get_field("EmailFidelizacion","EmailRemitente","IDEmailFidelizacion",8);
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = true;
	$mail->From = get_field("EmailFidelizacion","EmailRemitente","IDEmailFidelizacion",8);
	$mail->FromName = get_field("EmailFidelizacion","NombreRemitente","IDEmailFidelizacion",8);
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");

	$url_baja="http://www.calzadocaprino.com";
	
    $cabeceras = 'From: tienda@calzadocaprino.com' . "\r\n" .
		'Reply-To: tienda@calzadocaprino.com' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	$cabeceras .= 'MIME-Version: 1.0' . "\r\n";
    $cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$To=$email_punto_venta.",jaimer@calzadocaprino.com";
	//$correo="arturorfarias@gmail.com";
	//$correo="jorgechirivi@gmail.com";
    mail($correo,$Asunto,$cuerpo_bono,$cabeceras);
	//$mail->AddAddress($correo);
	//$confirm=$mail->Send();
}

function envia_bono_redimido($id_cliente,$id_bono,$id_punto_venta){

	include_once("class.phpmailer.php");
	include_once("class.smtp.php");

	$nombre=get_field("Cliente","Nombre","IDCliente",$id_cliente);
	$apellido=get_field("Cliente","Apellido","IDCliente",$id_cliente);
	$cedula=get_field("Cliente","Cedula","IDCliente",$id_cliente);
	$correo=get_field("Cliente","EMail","IDCliente",$id_cliente);
	$punto_venta=get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_punto_venta);
	$nombre_cliente=$nombre. " " . $apellido;
	$mensaje=get_field("EmailFidelizacion","Mensaje","IDEmailFidelizacion",2);
	$Msg=str_replace("[Nombre]",$nombre_cliente,$mensaje);
	$Msg=str_replace("[PuntoVenta]",$punto_venta,$Msg);

	if (!empty($id_bono)){
			$fecha_vencimiento=get_field("BonoFidelizacion","FechaVencimiento","IDBonoFidelizacion",$id_bono);
			$valor_bono=get_field("BonoFidelizacion","Valor","IDBonoFidelizacion",$id_bono);
			$cuerpo_bono=get_field("EmailFidelizacion","Mensaje","IDEmailFidelizacion",9);
			$cuerpo_bono=str_replace("[NumeroBono]",$id_bono,$cuerpo_bono);
			$cuerpo_bono=str_replace("[FechaVencimiento]",$fecha_vencimiento,$cuerpo_bono);
			$cuerpo_bono=str_replace("[ValorBono]",$valor_bono,$cuerpo_bono);
			$cuerpo_bono=str_replace("[NombreCliente]",$nombre_cliente,$cuerpo_bono);
			$cuerpo_bono=str_replace("[DocumentoCliente]",$cedula,$cuerpo_bono);
			$msj_bonos.=$cuerpo_bono;
	}


	$Msg=str_replace("[Bono]",$msj_bonos,$Msg);

	$url_baja="http://www.calzadocaprino.com";
	$mail = new phpmailer();
	$mail->Subject=get_field("EmailFidelizacion","Asunto","IDEmailFidelizacion",2);
	$mail->Body =$Msg;
	$mail->IsHTML(true);
	$mail->Sender = get_field("EmailFidelizacion","Replyto","IDEmailFidelizacion",2);
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = true;
	$mail->From = get_field("EmailFidelizacion","EmailRemitente","IDEmailFidelizacion",2);
	$mail->FromName = get_field("EmailFidelizacion","NombreRemitente","IDEmailFidelizacion",2);
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
	$mail->AddAddress($correo);
	$confirm=$mail->Send();

}



function envia_mail_referente($id_cliente){

	include_once("class.phpmailer.php");
	include_once("class.smtp.php");

	$correo=get_field("Cliente","EMail","IDCliente",$id_cliente);
	$mensaje=get_field("EmailFidelizacion","Mensaje","IDEmailFidelizacion",13);

	//Verifico si es un referente para mostrar el descuento por referidos efectivos
	 $sql_factura_efectiva_referido = "Select * From Factura F, FormaPagoFactura FPF Where F.IDFactura = FPF.IDFactura and F.IDClienteReferente = '".$id_cliente."' and F.RedimidaReferido <> 'S'";
	 $result_factura_efectiva_referido =db_query($sql_factura_efectiva_referido);
	 $total_facturas_efectivas = db_num_rows($result_factura_efectiva_referido);
	 $total_porcentaje_acumulado = 10+ ((int)$total_facturas_efectivas * 10);

	$Msg=str_replace("[Porcentaje]",$total_porcentaje_acumulado,$mensaje);

	$url_baja="http://www.calzadocaprino.com";
	$mail = new phpmailer();
	$mail->Subject=get_field("EmailFidelizacion","Asunto","IDEmailFidelizacion",13);
	$mail->Body =$Msg;
	$mail->IsHTML(true);
	$mail->Sender = get_field("EmailFidelizacion","Replyto","IDEmailFidelizacion",13);
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = true;
	$mail->From = get_field("EmailFidelizacion","EmailRemitente","IDEmailFidelizacion",13);
	$mail->FromName = get_field("EmailFidelizacion","NombreRemitente","IDEmailFidelizacion",13);
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
	$mail->AddAddress($correo);
	$confirm=$mail->Send();
}



function envia_nuevo_garantia($id_garantia){
	include_once("class.phpmailer.php");
	include_once("class.smtp.php");


	$id_factura=get_field("Garantia","IDFactura","IDGarantia",$id_garantia);
	$id_punto_venta=get_field("Garantia","IDPuntoVenta","IDGarantia",$id_garantia);
	$id_punto_venta_factura=get_field("Garantia","IDPuntoVentaFactura","IDGarantia",$id_garantia);
	$id_detalle_factura=get_field("Garantia","IDDetalleFactura","IDGarantia",$id_garantia);
	$id_referencia_reproceso=get_field("Garantia","IDReferencia","IDGarantia",$id_garantia);
	$id_talla_reproceso=get_field("Garantia","IDTalla","IDGarantia",$id_garantia);
	$id_detalle_cambio = get_field("Garantia","IDDetalleCambio","IDGarantia",$id_garantia);



	$id_cliente= get_field("Factura","IDCliente","IDFactura",$id_factura);
	$descripcion_garantia= get_field("Garantia","Descripcion","IDGarantia",$id_garantia);
	$tipo_garantia=get_field("Garantia","TipoRegistro","IDGarantia",$id_garantia);
	$tipo_producto=get_field("Garantia","TipoProducto","IDGarantia",$id_garantia);

	$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));



	$nombre=get_field("Cliente","Nombre","IDCliente",$id_cliente);
	$apellido=get_field("Cliente","Apellido","IDCliente",$id_cliente);
	$cedula=get_field("Cliente","Cedula","IDCliente",$id_cliente);


	$punto_venta=get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_punto_venta);
	$nombre_cliente=$nombre. " " . $apellido;


	$id_referencia="";




	if ($tipo_producto=="T"){

		$correo=get_field("ParametroGarantia","Valor","IDParametroGarantia",2);
		// ES UNA REFERNCIA DE ALGUN CAMBIO Y LA CONSULTO
		if (!empty($id_detalle_cambio)){
			$array_cambio_detalle=explode("|",$id_detalle_cambio);
			if (count($array_cambio_detalle)>0):
				$sql_cambio=db_query("Select * From DetalleCambio Where IDDetalleCambio = '".$array_cambio_detalle[1]."' and IDCambio = '".$array_cambio_detalle[0]."'");
				$r_cambio=db_fetch_array($sql_cambio);
				$id_referencia=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"])));
				$talla_ref=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"]));
			endif;

		}
		else{
			// consulto el correo del proveedor de acuerdo con la referencia
			if ($id_factura!="0" && $id_punto_venta_factura!="0" && $id_detalle_factura!="0"){
				$sql_codificacion_especifica=db_query("SELECT * FROM DetalleFactura WHERE IDFactura = '".$id_factura."' and IDPuntoVenta = '".$id_punto_venta_factura."' and IDDetalleFactura = '".$id_detalle_factura."'");
				$row_especifica=db_fetch_object($sql_codificacion_especifica);
				$id_referencia=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_especifica->IDCodificacionEspecifica)));
				$numero_referencia=get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_especifica->IDCodificacionEspecifica)));
				$talla_ref=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$row_especifica->IDCodificacionEspecifica));
			}
			elseif($id_referencia_reproceso!="0"){
				$id_referencia=$id_referencia_reproceso;
				$numero_referencia=get_field("Referencia","Numero","IDReferencia",$id_referencia);
				$talla_ref=get_field("Talla","Descripcion","IDTalla",$id_talla_reproceso);
				$nombre_cliente=get_field("Talla","Descripcion","IDTalla",$id_talla_reproceso);;

			}
		}




		if (!empty($id_referencia)){
			$id_proveddor_envio=get_field("Referencia","IDProveedor","IDReferencia",$id_referencia);
			$correo_proveedor=get_field("Proveedor","Email","IDProveedor",$id_proveddor_envio);
			$nombre_proveedor=get_field("Proveedor","Nombre","IDProveedor",$id_proveddor_envio);
			if (!empty($correo_proveedor)){
				envia_correo_proveedor($correo_proveedor,$id_garantia, $punto_venta, $nombre_proveedor, $tipo_garantia, $descripcion_garantia,$numero_referencia,$talla_ref);
				$nombre_cliente=$nombre_proveedor;
			}
		}

	}
	else{
		$correo=get_field("ParametroGarantia","Valor","IDParametroGarantia",1);
	}


	//$correo = "jorgechirivi@gmail.com";
	envia_correo_garantia_caprino($correo,$id_garantia, $punto_venta, $nombre_proveedor, $tipo_garantia, $descripcion_garantia,$numero_referencia,$talla_ref);



}



function envia_correo_garantia_caprino($correo,$id_garantia, $punto_venta, $nombre_cliente, $tipo_garantia, $descripcion_garantia,$numero_referencia,$talla_ref){

	$qid = db_query(" SELECT * FROM Garantia WHERE IDGarantia = '".$id_garantia."'");
	$r = db_fetch_object($qid);

  $sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'";
  $qry_producto=db_query($sql_producto);
  $r_detalle=db_fetch_object($qry_producto);

						 if ($r->TipoRegistro=="Reproceso"){
							echo $nombre_referencia=get_field("Referencia","Numero","IDReferencia",$r->IDReferencia);

						  } elseif(!empty($r->IDDetalleFacturaBono)){

							  $array_bono_detalle=explode("|",$r->IDDetalleFacturaBono);
									if (count($array_bono_detalle)>0):
										$sql_bono=db_query("Select * From DetalleFacturaBono Where IDDetalleFacturaBono	 = '".$array_bono_detalle[1]."' and IDFacturaBono = '".$array_bono_detalle[0]."'");
										$r_bono=db_fetch_array($sql_bono);

								   		$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"])));
										$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										$nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
									endif;
						  }
						  elseif(empty($r->IDDetalleCambio)){

							   ?>
                         			 <?php

									$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))) ;
									if ($id_referencia_item==160){ // Cuando son excedentes consulto la referencia de la compra
										$sql_facturabono=db_query("Select * from FacturaBono Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
										$r_facturabono=db_fetch_array($sql_facturabono);
										if (!empty($r_facturabono[IDFacturaBono])){
											$sql_detallefacturabono=db_query("Select * from DetalleFacturaBono Where IDFacturaBono = '".$r_facturabono[IDFacturaBono]."'");
											$r_detallefacturabono=db_fetch_array($sql_detallefacturabono);
											$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));
											$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"]));
										}
									  }
									 $id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
									 $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);

									 ?>


                               <?php }
							   else{ // ES UNA REFERNCIA DE ALGUN CAMBIO Y LA CONSULTO
							   		$array_cambio_detalle=explode("|",$r->IDDetalleCambio);
									if (count($array_cambio_detalle)>0):
										$sql_cambio=db_query("Select * From DetalleCambio Where IDDetalleCambio = '".$array_cambio_detalle[1]."' and IDCambio = '".$array_cambio_detalle[0]."'");
										$r_cambio=db_fetch_array($sql_cambio);

								   		$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"])));
										$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"]));
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										$nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
									endif;

								}



	include_once("class.phpmailer.php");
	include_once("class.smtp.php");

	$mensaje="
			<body>
				<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
					<tr>
						<td>
							<img src='http://www.calzadocaprino.com/assets/img/logo-caprino.png'>
						</td>
					</tr>
					<tr>
						<td>
							Cordial Saludo:
							<br>
							Le informo que recibimos una garantia , por favor confirmar si pasa por ella o programar  su  transportadora o notificar si se envia por la nuestra.
							<br><br>
							<br><br>
							<table  border='0' cellpadding='0' cellspacing='0' width='452' align='center'>
								<tr>
									<td width='176'>
										NUMERO GARANTIA
									</td>
									<td width='624'>
										".$id_garantia."
									</td>
								</tr>
								<tr>
									<td>
										Punto de venta
									</td>
									<td>
										".$punto_venta."
									</td>
								</tr>
								<tr>
									<td>
										Cliente
									</td>
									<td>
										".$nombre_cliente."
									</td>
								</tr>
								<tr>
									<td>
										Tipo garantia
									</td>
									<td>
										".$tipo_garantia."
									</td>
								</tr>
								<tr>
									<td>
										Descripcion
									</td>
									<td>
										".$descripcion_garantia."
									</td>
								</tr>
								<tr>
									<td>
										Referencia
									</td>
									<td>
										".$nombre_referencia."
									</td>
								</tr>
								<tr>
									<td>
										Talla
									</td>
									<td>
										".$talla_ref."
									</td>
								</tr>


							</table>
						</td>
					</tr>

				</table>

				<br>
				Le recuerdo que nuestra pol&iacute;tica para la  entrega de las garant&iacute;as a los clientes, es de 15 d&iacute;as h&aacute;biles desde el d&iacute;a que se recibe en el punto de venta, por lo que solicitamos  dar tr&aacute;mite lo m&aacute;s pronto posible y confirmar el envio del producto al correo el&eacute;ctronico ;  favor adjuntar el  registro de garantía especificando el proceso realizado.

			</body>
	";

	$url_baja="http://www.calzadocaprino.com";
	$mail = new phpmailer();




	$array_correo=explode(",",$correo);

	if (count($array_correo)>0){
		foreach($array_correo as $correo_value){
			$mail->AddAddress($correo_value);
		}
	}


	$mail->Subject="Nueva garantia Caprino Numero " . $id_garantia;
	$mail->Body =$mensaje;
	$mail->IsHTML(true);
	$mail->Sender='ventas@calzadocaprino.com';
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = true;
	$mail->From = "jaimer@calzadocaprino.com";
	$mail->FromName = "Caprino";
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
	$confirm=$mail->Send();





}



	function envia_correo_proveedor($correo,$id_garantia, $punto_venta, $nombre_cliente, $tipo_garantia, $descripcion_garantia,$numero_referencia,$talla_ref){

	$qid = db_query(" SELECT * FROM Garantia WHERE IDGarantia = '".$id_garantia."'");
	$r = db_fetch_object($qid);

	$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'";
  $qry_producto=db_query($sql_producto);
  $r_detalle=db_fetch_object($qry_producto);

	$correo_pto_venta=get_field("PuntoVenta","Email","IDPuntoVenta",$r->IDPuntoVenta);
	if(!empty($correo_pto_venta)){
		$correo.=",".$correo_pto_venta;
	}

						 if ($r->TipoRegistro=="Reproceso"){
							echo $nombre_referencia=get_field("Referencia","Numero","IDReferencia",$r->IDReferencia);

						  } elseif(!empty($r->IDDetalleFacturaBono)){

							  $array_bono_detalle=explode("|",$r->IDDetalleFacturaBono);
									if (count($array_bono_detalle)>0):
										$sql_bono=db_query("Select * From DetalleFacturaBono Where IDDetalleFacturaBono	 = '".$array_bono_detalle[1]."' and IDFacturaBono = '".$array_bono_detalle[0]."'");
										$r_bono=db_fetch_array($sql_bono);

								   		$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"])));
										$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										$nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
									endif;
						  }
						  elseif(empty($r->IDDetalleCambio)){

							   ?>
                         			 <?php

									$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))) ;
									if ($id_referencia_item==160){ // Cuando son excedentes consulto la referencia de la compra
										$sql_facturabono=db_query("Select * from FacturaBono Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
										$r_facturabono=db_fetch_array($sql_facturabono);
										if (!empty($r_facturabono[IDFacturaBono])){
											$sql_detallefacturabono=db_query("Select * from DetalleFacturaBono Where IDFacturaBono = '".$r_facturabono[IDFacturaBono]."'");
											$r_detallefacturabono=db_fetch_array($sql_detallefacturabono);
											$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));
											$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"]));
										}
									  }
									 $id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
									 $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);

									 ?>


                               <?php }
							   else{ // ES UNA REFERNCIA DE ALGUN CAMBIO Y LA CONSULTO
							   		$array_cambio_detalle=explode("|",$r->IDDetalleCambio);
									if (count($array_cambio_detalle)>0):
										$sql_cambio=db_query("Select * From DetalleCambio Where IDDetalleCambio = '".$array_cambio_detalle[1]."' and IDCambio = '".$array_cambio_detalle[0]."'");
										$r_cambio=db_fetch_array($sql_cambio);

								   		$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"])));
										$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"]));
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										$nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
									endif;

								}



	include_once("class.phpmailer.php");
	include_once("class.smtp.php");

	$mensaje="
			<body>
				<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
					<tr>
						<td>
							<img src='http://www.calzadocaprino.com/assets/img/logo-caprino.png'>
						</td>
					</tr>
					<tr>
						<td>
							Cordial Saludo:
							<br>
							Le informo que recibimos una garantia , por favor confirmar si pasa por ella o programar  su  transportadora o notificar si se envia por la nuestra.
							<br><br>
							<br><br>
							<table  border='0' cellpadding='0' cellspacing='0' width='452' align='center'>
								<tr>
									<td width='176'>
										NUMERO GARANTIA
									</td>
									<td width='624'>
										".$id_garantia."
									</td>
								</tr>
								<tr>
									<td>
										Punto de venta
									</td>
									<td>
										".$punto_venta."
									</td>
								</tr>
								<tr>
									<td>
										Cliente
									</td>
									<td>
										".$nombre_cliente."
									</td>
								</tr>
								<tr>
									<td>
										Tipo garantia
									</td>
									<td>
										".$tipo_garantia."
									</td>
								</tr>
								<tr>
									<td>
										Descripcion
									</td>
									<td>
										".$descripcion_garantia."
									</td>
								</tr>
								<tr>
									<td>
										Referencia
									</td>
									<td>
										".$nombre_referencia."
									</td>
								</tr>
								<tr>
									<td>
										Talla
									</td>
									<td>
										".$talla_ref."
									</td>
								</tr>


							</table>
						</td>
					</tr>

				</table>

				<br>
				Le recuerdo que nuestra pol&iacute;tica para la  entrega de las garant&iacute;as a los clientes, es de 15 d&iacute;as h&aacute;biles desde el d&iacute;a que se recibe en el punto de venta, por lo que solicitamos  dar tr&aacute;mite lo m&aacute;s pronto posible y confirmar el envio del producto al correo el&eacute;ctronico ;  favor adjuntar el  registro de garantía especificando el proceso realizado.

			</body>
	";

	$url_baja="http://www.calzadocaprino.com";
	$mail = new phpmailer();


	$array_correo=explode(",",$correo);

	if (count($array_correo)>0){
		foreach($array_correo as $correo_value){
			$mail->AddAddress($correo_value);
		}
	}


	$mail->Subject="Garantia Caprino Numero: " . $id_garantia;
	$mail->Body =$mensaje;
	$mail->IsHTML(true);
	$mail->Sender='jaimer@calzadocaprino.com';
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = true;
	$mail->From = "jaimer@calzadocaprino.com";
	$mail->FromName = "Caprino";
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
	$confirm=$mail->Send();





}


function envia_comentario_garantia($id_garantia,$frm,$IDEmpleado){
	include_once("class.phpmailer.php");
	include_once("class.smtp.php");

	$id_factura=get_field("Garantia","IDFactura","IDGarantia",$id_garantia);
	$id_punto_venta=get_field("Garantia","IDPuntoVenta","IDGarantia",$id_garantia);
	$id_estado=get_field("Garantia","IDEstadoGarantia","IDGarantia",$id_garantia);
	$tipo_registro=get_field("Garantia","TipoRegistro","IDGarantia",$id_garantia);
	$id_cliente= get_field("Factura","IDCliente","IDFactura",$id_factura);
	$descripcion_garantia= get_field("Garantia","Descripcion","IDGarantia",$id_garantia);

	$nombre=get_field("Cliente","Nombre","IDCliente",$id_cliente);
	$apellido=get_field("Cliente","Apellido","IDCliente",$id_cliente);
	$cedula=get_field("Cliente","Cedula","IDCliente",$id_cliente);
	$correo=get_field("ParametroGarantia","Valor","IDParametroGarantia",1);
	$punto_venta=get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_punto_venta);
	$nombre_estado=get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$id_estado);
	$nombre_cliente=$nombre. " " . $apellido;



	$id_punto_venta_factura=get_field("Garantia","IDPuntoVentaFactura","IDGarantia",$id_garantia);
	$id_detalle_factura=get_field("Garantia","IDDetalleFactura","IDGarantia",$id_garantia);
	$id_referencia_reproceso=get_field("Garantia","IDReferencia","IDGarantia",$id_garantia);
	$id_talla_reproceso=get_field("Garantia","IDTalla","IDGarantia",$id_garantia);
	$tipo_garantia=get_field("Garantia","TipoRegistro","IDGarantia",$id_garantia);
	$tipo_producto=get_field("Garantia","TipoProducto","IDGarantia",$id_garantia);
	$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
	$id_referencia="";


		if ($id_factura!="0" && $id_punto_venta_factura!="0" && $id_detalle_factura!="0"){
			$sql_codificacion_especifica=db_query("SELECT * FROM DetalleFactura WHERE IDFactura = '".$id_factura."' and IDPuntoVenta = '".$id_punto_venta_factura."' and IDDetalleFactura = '".$id_detalle_factura."'");
			$row_especifica=db_fetch_object($sql_codificacion_especifica);
			$id_referencia=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_especifica->IDCodificacionEspecifica)));
			$numero_referencia=get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_especifica->IDCodificacionEspecifica)));
			$talla_ref=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$row_especifica->IDCodificacionEspecifica));
		}
		elseif($id_referencia_reproceso!="0"){
			$id_referencia=$id_referencia_reproceso;
			$numero_referencia=get_field("Referencia","Numero","IDReferencia",$id_referencia);
			$talla_ref=get_field("Talla","Descripcion","IDTalla",$id_talla_reproceso);
			$nombre_cliente=get_field("Talla","Descripcion","IDTalla",$id_talla_reproceso);
		}





	$mensaje="
			<body>
				<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
					<tr>
						<td>
							<img src='http://www.calzadocaprino.com/assets/img/logo-caprino.png'>
						</td>
					</tr>
					<tr>
						<td>
							Cordial Saludo:
							<br>Se gener&oacute; un cambio de estado en la garantia con la siguiente informaci&oacute;n:
							<br><br>
							<table  border='0' cellpadding='0' cellspacing='0' width='452' align='center'>

								<tr>
									<td width='176'>
										Tipo Proceso
									</td>
									<td width='624'>
										".$tipo_registro."
									</td>
								</tr>
								<tr>
									<td width='176'>
										Numero de garantia:
									</td>
									<td width='624'>
										".$id_garantia."
									</td>
								</tr>

								<tr>
									<td>
										Referencia
									</td>
									<td>
										".$numero_referencia."
									</td>
								</tr>
								<tr>
									<td>
										Talla
									</td>
									<td>
										".$talla_ref."
									</td>
								</tr>
								<tr>
									<td>
										Punto de venta
									</td>
									<td>
										".$punto_venta."
									</td>
								</tr>
								<tr>
									<td>
										Cliente
									</td>
									<td>
										".$nombre_cliente."
									</td>
								</tr>
								<tr>
									<td>
										Descripcion
									</td>
									<td>
										".$descripcion_garantia."
									</td>
								</tr>
								<tr>
									<td>
										Estado:
									</td>
									<td>
										".$nombre_estado."
									</td>
								</tr>

								<tr>
									<td>
										<b>Respuesta de:</b> ". get_field("Empleado","Nombre","IDEmpleado",$IDEmpleado)."
									</td>
									<td>
										".$frm[Descripcion]."
									</td>
								</tr>
							</table>
						</td>
					</tr>

				</table>

				<br>Por favor ingrese al administrador para dar seguimiento a la garant&iacute;a

			</body>
	";


	$url_baja="http://www.calzadocaprino.com";
	$mail = new phpmailer();
	$array_correo=explode(",",$correo);
	if (count($array_correo)>0){
		foreach($array_correo as $correo_value){
			$mail->AddAddress($correo_value);
		}
	}


	$mail->Subject="Cambio de estado de proceso Numero " . $id_garantia;
	$mail->Body =$mensaje;
	$mail->IsHTML(true);
	$mail->Sender='ventas@calzadocaprino.com';
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = true;
	$mail->From = "jaimer@calzadocaprino.com";
	$mail->FromName = "Caprino";
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
	$confirm=$mail->Send();

}


function envia_comentario_garantia_almacen($id_garantia,$frm,$IDEmpleado){
	include_once("class.phpmailer.php");
	include_once("class.smtp.php");

	$id_factura=get_field("Garantia","IDFactura","IDGarantia",$id_garantia);
	$id_punto_venta=get_field("Garantia","IDPuntoVenta","IDGarantia",$id_garantia);
	$id_estado=get_field("Garantia","IDEstadoGarantia","IDGarantia",$id_garantia);
	$id_tipo_finalizacion=get_field("Garantia","IDTipoFinalizacionGarantia","IDGarantia",$id_garantia);
	$tipo_registro=get_field("Garantia","TipoRegistro","IDGarantia",$id_garantia);
	$tipo_fidelizacion=get_field("TipoFinalizacionGarantia","Nombre","IDTipoFinalizacionGarantia",$id_tipo_finalizacion);
	$id_cliente= get_field("Factura","IDCliente","IDFactura",$id_factura);
	$descripcion_garantia= get_field("Garantia","Descripcion","IDGarantia",$id_garantia);

	
	

	$nombre=get_field("Cliente","Nombre","IDCliente",$id_cliente);
	$apellido=get_field("Cliente","Apellido","IDCliente",$id_cliente);
	$cedula=get_field("Cliente","Cedula","IDCliente",$id_cliente);
	$punto_venta=get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_punto_venta);
	$email_punto_venta=get_field("PuntoVenta","Email","IDPuntoVenta",$id_punto_venta);
	$nombre_estado=get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$id_estado);
	$nombre_cliente=$nombre. " " . $apellido;



	$id_punto_venta_factura=get_field("Garantia","IDPuntoVentaFactura","IDGarantia",$id_garantia);
	$id_detalle_factura=get_field("Garantia","IDDetalleFactura","IDGarantia",$id_garantia);
	$id_referencia_reproceso=get_field("Garantia","IDReferencia","IDGarantia",$id_garantia);
	$id_talla_reproceso=get_field("Garantia","IDTalla","IDGarantia",$id_garantia);
	$tipo_garantia=get_field("Garantia","TipoRegistro","IDGarantia",$id_garantia);
	$tipo_producto=get_field("Garantia","TipoProducto","IDGarantia",$id_garantia);
	$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
	$id_referencia="";


		if ($id_factura!="0" && $id_punto_venta_factura!="0" && $id_detalle_factura!="0"){
			$sql_codificacion_especifica=db_query("SELECT * FROM DetalleFactura WHERE IDFactura = '".$id_factura."' and IDPuntoVenta = '".$id_punto_venta_factura."' and IDDetalleFactura = '".$id_detalle_factura."'");
			$row_especifica=db_fetch_object($sql_codificacion_especifica);
			$id_referencia=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_especifica->IDCodificacionEspecifica)));
			$numero_referencia=get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_especifica->IDCodificacionEspecifica)));
			$talla_ref=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$row_especifica->IDCodificacionEspecifica));
		}
		elseif($id_referencia_reproceso!="0"){
			$id_referencia=$id_referencia_reproceso;
			$numero_referencia=get_field("Referencia","Numero","IDReferencia",$id_referencia);
			$talla_ref=get_field("Talla","Descripcion","IDTalla",$id_talla_reproceso);
			$nombre_cliente=get_field("Talla","Descripcion","IDTalla",$id_talla_reproceso);
		}





	$mensaje="
			<body>
				<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
					<tr>
						<td>
							<img src='http://www.calzadocaprino.com/assets/img/logo-caprino.png'>
						</td>
					</tr>
					<tr>
						<td>
							Cordial Saludo:
							<br>Se realiz&oacute; un autorizaci&oacute;n especial a la siguiente garant&iacute;a
							<br><br>
							<table  border='0' cellpadding='0' cellspacing='0' width='452' align='center'>

								<tr>
									<td width='176'>
										Tipo Proceso
									</td>
									<td width='624'>
										".$tipo_registro."
									</td>
								</tr>
								<tr>
									<td width='176'>
										Numero de garantia:
									</td>
									<td width='624'>
										".$id_garantia."
									</td>
								</tr>

								<tr>
									<td>
										Referencia
									</td>
									<td>
										".$numero_referencia."
									</td>
								</tr>
								<tr>
									<td>
										Talla
									</td>
									<td>
										".$talla_ref."
									</td>
								</tr>
								<tr>
									<td>
										Punto de venta
									</td>
									<td>
										".$punto_venta."
									</td>
								</tr>
								<tr>
									<td>
										Cliente
									</td>
									<td>
										".$nombre_cliente."
									</td>
								</tr>
								<tr>
									<td>
										Descripcion
									</td>
									<td>
										".$descripcion_garantia."
									</td>
								</tr>
								<tr>
									<td>
										Estado:
									</td>
									<td>
										".$nombre_estado."
									</td>
								</tr>

								<tr>
									<td>
										<b>Respuesta de:</b> ". get_field("Empleado","Nombre","IDEmpleado",$IDEmpleado)."
									</td>
									<td>
										".$frm[Descripcion]." " . $tipo_fidelizacion . "
									</td>
								</tr>
							</table>
						</td>
					</tr>

				</table>
			</body>
	";




	$url_baja="http://www.calzadocaprino.com";
	
    $cabeceras = 'From: tienda@calzadocaprino.com' . "\r\n" .
		'Reply-To: tienda@calzadocaprino.com' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	$cabeceras .= 'MIME-Version: 1.0' . "\r\n";
    $cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$To=$email_punto_venta.",jaimer@calzadocaprino.com";
	//$To="jorgechirivi@gmail.com";
    mail($To,"Autorizacion especial Numero " . $id_garantia,$mensaje,$cabeceras);

}


function envia_nota_credito($id_garantia){

	include_once("class.phpmailer.php");
	include_once("class.smtp.php");

	$id_factura=get_field("Garantia","IDFactura","IDGarantia",$id_garantia);
	$id_punto_venta=get_field("Garantia","IDPuntoVenta","IDGarantia",$id_garantia);
	$id_estado=get_field("Garantia","IDEstadoGarantia","IDGarantia",$id_garantia);
	$tipo_registro=get_field("Garantia","TipoRegistro","IDGarantia",$id_garantia);
	$id_cliente= get_field("Factura","IDCliente","IDFactura",$id_factura);
	$descripcion_garantia= get_field("Garantia","Descripcion","IDGarantia",$id_garantia);

	$nombre=get_field("Cliente","Nombre","IDCliente",$id_cliente);
	$apellido=get_field("Cliente","Apellido","IDCliente",$id_cliente);
	$cedula=get_field("Cliente","Cedula","IDCliente",$id_cliente);
	$correo=get_field("ParametroGarantia","Valor","IDParametroGarantia",1);
	$punto_venta=get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_punto_venta);
	$nombre_estado=get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$id_estado);
	$nombre_cliente=$nombre. " " . $apellido;



	$id_punto_venta_factura=get_field("Garantia","IDPuntoVentaFactura","IDGarantia",$id_garantia);
	$id_detalle_factura=get_field("Garantia","IDDetalleFactura","IDGarantia",$id_garantia);
	$id_referencia_reproceso=get_field("Garantia","IDReferencia","IDGarantia",$id_garantia);
	$id_talla_reproceso=get_field("Garantia","IDTalla","IDGarantia",$id_garantia);
	$tipo_garantia=get_field("Garantia","TipoRegistro","IDGarantia",$id_garantia);
	$tipo_producto=get_field("Garantia","TipoProducto","IDGarantia",$id_garantia);
	$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
	$id_referencia="";


		if ($id_factura!="0" && $id_punto_venta_factura!="0" && $id_detalle_factura!="0"){
			$sql_codificacion_especifica=db_query("SELECT * FROM DetalleFactura WHERE IDFactura = '".$id_factura."' and IDPuntoVenta = '".$id_punto_venta_factura."' and IDDetalleFactura = '".$id_detalle_factura."'");
			$row_especifica=db_fetch_object($sql_codificacion_especifica);
			$id_referencia=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_especifica->IDCodificacionEspecifica)));
			$numero_referencia=get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$row_especifica->IDCodificacionEspecifica)));
			$talla_ref=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$row_especifica->IDCodificacionEspecifica));
		}
		elseif($id_referencia_reproceso!="0"){
			$id_referencia=$id_referencia_reproceso;
			$numero_referencia=get_field("Referencia","Numero","IDReferencia",$id_referencia);
			$talla_ref=get_field("Talla","Descripcion","IDTalla",$id_talla_reproceso);
			$nombre_cliente=get_field("Talla","Descripcion","IDTalla",$id_talla_reproceso);
		}

		if (!empty($id_referencia)){
			$id_proveddor_envio=get_field("Referencia","IDProveedor","IDReferencia",$id_referencia);
			$correo_proveedor=get_field("Proveedor","Email","IDProveedor",$id_proveddor_envio);
			$nombre_proveedor=get_field("Proveedor","Nombre","IDProveedor",$id_proveddor_envio);
		}





	$mensaje="
			<body>
				<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
					<tr>
						<td>
							<img src='http://www.calzadocaprino.com/assets/img/logo-caprino.png'>
						</td>
					</tr>
					<tr>
						<td>
							Cordial Saludo:
							<br>Le informamos que se realiz&oacute; una devoluci&oacute;n de un producto, con la siguiente informaci&oacute;n:
							<br><br>
							<table  border='0' cellpadding='0' cellspacing='0' width='452' align='center'>

								<tr>
									<td width='176'>
										Tipo Proceso
									</td>
									<td width='624'>
										".$tipo_registro."
									</td>
								</tr>
								<tr>
									<td width='176'>
										Numero de garantia:
									</td>
									<td width='624'>
										".$id_garantia."
									</td>
								</tr>
								<tr>
									<td width='176'>
										Proveedor:
									</td>
									<td width='624'>
										".$nombre_proveedor."
									</td>
								</tr>

								<tr>
									<td>
										Referencia
									</td>
									<td>
										".$numero_referencia."
									</td>
								</tr>
								<tr>
									<td>
										Talla
									</td>
									<td>
										".$talla_ref."
									</td>
								</tr>
								<tr>
									<td>
										Punto de venta
									</td>
									<td>
										".$punto_venta."
									</td>
								</tr>
								<tr>
									<td>
										Cliente
									</td>
									<td>
										".$nombre_cliente."
									</td>
								</tr>
								<tr>
									<td>
										Descripcion
									</td>
									<td>
										".$descripcion_garantia."
									</td>
								</tr>

							</table>
						</td>
					</tr>

				</table>

				<br>Por favor ingrese al administrador para dar seguimiento a la garant&iacute;a

			</body>
	";


	$url_baja="http://www.calzadocaprino.com";
	$mail = new phpmailer();
	$array_correo=explode(",",$correo);
	if (count($array_correo)>0){
		foreach($array_correo as $correo_value){
			$mail->AddAddress($correo_value);
		}
	}

	//$mail->AddAddress("jorgechirivi@gmail.com");

	$mail->Subject="Nota credito Garantia Nro: " . $id_garantia;
	$mail->Body =$mensaje;
	$mail->IsHTML(true);
	$mail->Sender='ventas@calzadocaprino.com';
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = true;
	$mail->From = "jaimer@calzadocaprino.com";
	$mail->FromName = "Caprino";
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
	//$confirm=$mail->Send();

	$cabeceras = 'From: tienda@calzadocaprino.com' . "\r\n" .
		'Reply-To: tienda@calzadocaprino.com' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";	
	$cabeceras  .= 'MIME-Version: 1.0' . "\r\n";
	$To=$correo;
	mail($To,"Nota credito Garantia Nro: " . $id_garantia,"ventas@calzadocaprino.com",$cabeceras);

}


function envia_comentario_tercero($id_garantia,$frm,$IDEmpleado){
	include_once("class.phpmailer.php");
	include_once("class.smtp.php");

	$id_factura=get_field("Garantia","IDFactura","IDGarantia",$id_garantia);
	$id_punto_venta=get_field("Garantia","IDPuntoVenta","IDGarantia",$id_garantia);
	$id_cliente= get_field("Factura","IDCliente","IDFactura",$id_factura);
	$descripcion_garantia= get_field("Garantia","Descripcion","IDGarantia",$id_garantia);

	$nombre=get_field("Cliente","Nombre","IDCliente",$id_cliente);
	$apellido=get_field("Cliente","Apellido","IDCliente",$id_cliente);
	$cedula=get_field("Cliente","Cedula","IDCliente",$id_cliente);
	$correo=get_field("ParametroGarantia","Valor","IDParametroGarantia",2);
	$punto_venta=get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_punto_venta);
	$nombre_cliente=$nombre. " " . $apellido;
	$mensaje="
			<body>
				<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
					<tr>
						<td>
							<img src='http://www.calzadocaprino.com/assets/img/logo-caprino.png'>
						</td>
					</tr>
					<tr>
						<td>
							Cordial Saludo:
							<br>Se gener&oacute; una garant&iacute;a de un producto de tercero con la siguiente informaci&oacute;n:
							<br><br>
							<table  border='0' cellpadding='0' cellspacing='0' width='452' align='center'>
								<tr>
									<td width='176'>
										NUMERO GARANTIA
									</td>
									<td width='624'>
										".$id_garantia."
									</td>
								</tr>
								<tr>
									<td>
										Punto de venta
									</td>
									<td>
										".$punto_venta."
									</td>
								</tr>
								<tr>
									<td>
										Cliente
									</td>
									<td>
										".$nombre_cliente."
									</td>
								</tr>
								<tr>
									<td>
										Descripcion
									</td>
									<td>
										".$descripcion_garantia."
									</td>
								</tr>

								<tr>
									<td>
										<b>Respuesta de:</b> ". get_field("Empleado","Nombre","IDEmpleado",$IDEmpleado)."
									</td>
									<td>
										".$frm[Descripcion]."
									</td>
								</tr>
							</table>
						</td>
					</tr>

				</table>

				<br>Por favor ingrese al administrador para dar seguimiento a la garant&iacute;a

			</body>
	";


	$url_baja="http://www.calzadocaprino.com";
	$mail = new phpmailer();
	$array_correo=explode(",",$correo);
	if (count($array_correo)>0){
		foreach($array_correo as $correo_value){
			$mail->AddAddress($correo_value);
		}
	}


	$mail->Subject="Cambio de estado de garantia Numero " . $id_garantia;
	$mail->Body =$mensaje;
	$mail->IsHTML(true);
	$mail->Sender='ventas@calzadocaprino.com';
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = true;
	$mail->From = "jaimer@calzadocaprino.com";
	$mail->FromName = "Caprino";
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
	$confirm=$mail->Send();

}

function get_client_ip() {
	$ipaddress = '';
	if (getenv('HTTP_CLIENT_IP'))
		$ipaddress = getenv('HTTP_CLIENT_IP');
	else if(getenv('HTTP_X_FORWARDED_FOR'))
		$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	else if(getenv('HTTP_X_FORWARDED'))
		$ipaddress = getenv('HTTP_X_FORWARDED');
	else if(getenv('HTTP_FORWARDED_FOR'))
		$ipaddress = getenv('HTTP_FORWARDED_FOR');
	else if(getenv('HTTP_FORWARDED'))
	   $ipaddress = getenv('HTTP_FORWARDED');
	else if(getenv('REMOTE_ADDR'))
		$ipaddress = getenv('REMOTE_ADDR');
	else
		$ipaddress = 'UNKNOWN';
	return $ipaddress;
  }

function envia_pedido_tercero_original($id_pedido_tercero,$rel){

	global $dirroot;
	include_once("class.phpmailer.php");
	include_once("class.smtp.php");


	$sql_pedido = db_query("Select * From PedidoTercero Where IDPedidoTercero = '".$id_pedido_tercero."'");
	$row_pedido = db_fetch_array($sql_pedido);

	//Si es un reenvio no se vuelve a enviar a caprino solo al proveedor
	//if($rel!="reenvio"):
	$correo=get_field("ParametroTercero","Valor","IDParametroTercero",3);
	//endif;

	$correo_proveedor=get_field("Proveedor","Email","IDProveedor",$row_pedido[IDProveedor]);
	if (!empty($correo_proveedor)){
		//if($rel!="reenvio"):
			$correo .= ",".$correo_proveedor;
		//else:
			//$correo .= $correo_proveedor;
		//endif;
	}


	$mensaje="
			<body>
				<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
					<tr>
						<td>
							<img src='http://www.calzadocaprino.com/assets/img/logo-caprino.png'>
						</td>
					</tr>
					<tr>
						<td>
							Cordial Saludo: <br>
							<br>Se gener&oacute; un pedido con la siguiente informacion, consulte el archivo adjunto para conocer mas detalles:
							<br><br>
							<table  border='0' cellpadding='0' cellspacing='0' width='452' align='center'>
								<tr>
									<td width='176'>
										NUMERO
									</td>
									<td width='624'>
										".$row_pedido["NumeroOrdenCompra"]."
									</td>
								</tr>
								<tr>
									<td>
										Fecha Pedido
									</td>
									<td>
										".$row_pedido["FechaPedido"]."
									</td>
								</tr>
								<tr>
									<td>
										Fecha Entrega
									</td>
									<td>
										".$row_pedido["FechaEntrega"]."
									</td>
								</tr>

							</table>
						</td>
					</tr>

				</table>

			</body>
	";


	$url_baja="http://www.calzadocaprino.com";
	$mail = new phpmailer();



	$array_correo=explode(",",$correo);
	if (count($array_correo)>0){
		foreach($array_correo as $correo_value){
			//echo "<br>" . $correo_value;
			$mail->AddAddress($correo_value);
		}
	}

	//$mail->AddAddress("jorgechirivi@gmail.com");

	$filename = $dirroot . "PedidoTercero/pedidos/Pedido".$row_pedido[NumeroOrdenCompra].'.pdf';
	$nombre = "Pedidos_".$row_pedido[NumeroOrdenCompra].'.pdf';
	$mail->AddAttachment($filename,$nombre);


	$mail->Subject="Orden de Compra Numero " . $row_pedido[NumeroOrdenCompra];
	$mail->Body =$mensaje;
	$mail->IsHTML(true);
	$mail->Sender='ventas@calzadocaprino.com';
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = true;
	$mail->From = "jaimer@calzadocaprino.com";
	$mail->FromName = "Caprino";
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
	$confirm=$mail->Send();

}

function envia_pedido_tercero($id_pedido_tercero,$rel){

	global $dirroot,$url;
	include_once("class.phpmailer.php");
	include_once("class.smtp.php");


	$sql_pedido = db_query("Select * From PedidoTercero Where IDPedidoTercero = '".$id_pedido_tercero."'");
	$row_pedido = db_fetch_array($sql_pedido);

	//Si es un reenvio no se vuelve a enviar a caprino solo al proveedor
	//if($rel!="reenvio"):
	$correo=get_field("ParametroTercero","Valor","IDParametroTercero",3);
	//endif;

	$correo_proveedor=get_field("Proveedor","Email","IDProveedor",$row_pedido[IDProveedor]);
	if (!empty($correo_proveedor)){
		//if($rel!="reenvio"):
			$correo .= ",".$correo_proveedor;
		//else:
			//$correo .= $correo_proveedor;
		//endif;
	}

	//$correo="jorgechirivi@gmail.com,jorgearmandochirivi@yahoo.com,jaimer@calzadocaprino.com";

	$array_correo_enviar=explode(",",$correo);
	if (count($array_correo_enviar)>0){
		foreach($array_correo_enviar as $correo_enviar){


			$Link=$url."detallepedidotercero.php?IDPedidoTercero=".$id_pedido_tercero."&Correo=".$correo_enviar;
			$mensaje="
					<body>
						<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
							<tr>
								<td>
									<img src='http://www.calzadocaprino.com/assets/img/logo-caprino.png'>
								</td>
							</tr>
							<tr>
								<td>
									Cordial Saludo: <br>
									<br>Se gener&oacute; un pedido con la siguiente informacion, <a href='".$Link."'>pulse aca para conocer mas detalles</a>:
									<br><br>
									<table  border='0' cellpadding='0' cellspacing='0' width='452' align='center'>
										<tr>
											<td width='176'>
												NUMERO
											</td>
											<td width='624'>
												".$row_pedido["NumeroOrdenCompra"]."
											</td>
										</tr>
										<tr>
											<td>
												Fecha Pedido
											</td>
											<td>
												".$row_pedido["FechaPedido"]."
											</td>
										</tr>
										<tr>
											<td>
												Fecha Entrega
											</td>
											<td>
												".$row_pedido["FechaEntrega"]."
											</td>
										</tr>
		
									</table>
								</td>
							</tr>
		
						</table>
		
					</body>
			";
		
		
			$url_baja="http://www.calzadocaprino.com";
			$mail = new phpmailer();
		
		
		
			$array_correo=explode(",",$correo_enviar);
			if (count($array_correo)>0){
				foreach($array_correo as $correo_value){
					//echo "<br>" . $correo_value;
					$mail->AddAddress($correo_value);
				}
			}
		
			//$mail->AddAddress("jorgechirivi@gmail.com");
		
			//$filename = $dirroot . "PedidoTercero/pedidos/Pedido".$row_pedido[NumeroOrdenCompra].'.pdf';
			//$nombre = "Pedidos_".$row_pedido[NumeroOrdenCompra].'.pdf';
			//$mail->AddAttachment($filename,$nombre);
		
			$mail->Subject="Orden de Compra Numero " . $row_pedido[NumeroOrdenCompra];
			$mail->Body =$mensaje;
			$mail->IsHTML(true);
			$mail->Sender='ventas@calzadocaprino.com';
			$mail->Timeout=120;
			$mail->Host = "localhost";
			$mail->Mailer = 'smtp';
			$mail->Password = 's0luci0nes#A';
			$mail->Username = 'postmater@correosim.com';
			$mail->SMTPAuth = true;
			$mail->From = "jaimer@calzadocaprino.com";
			$mail->FromName = "Caprino";
			$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
			//$confirm=$mail->Send();
			$cabeceras = 'From: ventas@calzadocaprino.com' . "\r\n" .
    					 'Reply-To: ventas@calzadocaprino.com' . "\r\n" .
    					 'X-Mailer: PHP/' . phpversion();
			$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
			$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";						 
			mail ( $correo_enviar , "Orden de Compra Numero " . $row_pedido["NumeroOrdenCompra"] , $mensaje, $cabeceras );    
	
		}
	}


  	

}


function envia_pedido_mostrar($id_pedido_tercero,$correo){

	global $dirroot, $url;
  	$IP=get_client_ip();
	$sql_vista="INSERT INTO PedidoTerceroVista (IDPedidoTercero,IP,Correo,Fecha) VALUES ('".$id_pedido_tercero."','".$IP."','".$correo."',NOW() ) ";
  	db_query($sql_vista);
	$sql_pedido = db_query("Select * From PedidoTercero Where IDPedidoTercero = '".$id_pedido_tercero."'");
	$row_pedido = db_fetch_array($sql_pedido);	
  	$filename = "<a href='".$url . "admin/PedidoTercero/pedidos/Pedido".$row_pedido[NumeroOrdenCompra].'.pdf'."'>Ver adjunto</a>";	
	$mensaje="
			<body>
				<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
					<tr>
						<td>
							<img src='http://www.calzadocaprino.com/assets/img/logo-caprino.png'>
						</td>
					</tr>
					<tr>
						<td>
							Cordial Saludo: <br>
							<br>Se gener&oacute; un pedido con la siguiente informacion, consulte el archivo adjunto para conocer mas detalles:
							<br><br>
							<table  border='0' cellpadding='0' cellspacing='0' width='452' align='center'>
								<tr>
									<td width='176'>
										NUMERO
									</td>
									<td width='624'>
										".$row_pedido["NumeroOrdenCompra"]."
									</td>
								</tr>
								<tr>
									<td>
										Fecha Pedido
									</td>
									<td>
										".$row_pedido["FechaPedido"]."
									</td>
								</tr>
								<tr>
									<td>
										Fecha Entrega
									</td>
									<td>
										".$row_pedido["FechaEntrega"]."
									</td>
								</tr>

                <tr>
									<td>
										Archivo Adjunto
									</td>
									<td>
										".$filename."
									</td>
								</tr>

							</table>
						</td>
					</tr>

				</table>

			</body>
	";
	
	return 	$mensaje;

	
}


function estado_tercero_pto_vta($IDPedidoTercero, $IDPuntoVenta){
		// Verifico cuantos se pidieron
		$qry_item_pedido =  "Select SUM(Cantidad) as TotalPedido From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$IDPedidoTercero."' and IDPuntoVenta = '".$IDPuntoVenta."'";
		$sql_item_pedido = db_query($qry_item_pedido);
		$row_item_pedido = db_fetch_array($sql_item_pedido);
		$total_pedido = $row_item_pedido["TotalPedido"];

		// Verifico cuantos se entregaron
		$qry_item_recibido =  "Select SUM(CantidadRecibido) as TotalRecibido From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$IDPedidoTercero."' and IDPuntoVenta = '".$IDPuntoVenta."'";
		$sql_item_recibido = db_query($qry_item_recibido);
		$row_item_recibido = db_fetch_array($sql_item_recibido);
		$total_recibido = $row_item_recibido["TotalRecibido"];


		if((int)$total_pedido==0 && (int)$total_recibido==0){
			$estado_punto_venta="No pedido";
		}
		elseif($total_pedido==$total_recibido){
			$estado_punto_venta="Entregado Total";
		}
		elseif((int)$total_recibido=="0"){
			$estado_punto_venta="Pendiente Recibir";
		}
		elseif((int)$total_pedido>=(int)$total_recibido){
			$estado_punto_venta="Entregado Parcial";
		}

		return "<b>".$estado_punto_venta."</b>";

}

function resumen_tercero_pto_vta($IDPedidoTercero, $IDPuntoVenta){
		// Verifico cuantos se pidieron
		$qry_item_pedido =  "Select SUM(Cantidad) as TotalPedido From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$IDPedidoTercero."' and IDPuntoVenta = '".$IDPuntoVenta."'";
		$sql_item_pedido = db_query($qry_item_pedido);
		$row_item_pedido = db_fetch_array($sql_item_pedido);
		$total_pedido = $row_item_pedido["TotalPedido"];

		// Verifico cuantos se entregaron
		$qry_item_recibido =  "Select SUM(CantidadRecibido) as TotalRecibido From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$IDPedidoTercero."' and IDPuntoVenta = '".$IDPuntoVenta."'";
		$sql_item_recibido = db_query($qry_item_recibido);
		$row_item_recibido = db_fetch_array($sql_item_recibido);
		$total_recibido = $row_item_recibido["TotalRecibido"];

		$array_resumen["ToTalPedido"] = $total_pedido;
		$array_resumen["ToTalRecibido"] = $total_recibido;

		return $array_resumen;

}

function estado_tercero($IDPedidoTercero,$IDEstadoPedido){
		// Verifico cuantos se pidieron
		$qry_item_pedido =  "Select SUM(Cantidad) as TotalPedido From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$IDPedidoTercero."'";
		$sql_item_pedido = db_query($qry_item_pedido);
		$row_item_pedido = db_fetch_array($sql_item_pedido);
		$total_pedido = $row_item_pedido["TotalPedido"];

		// Verifico cuantos se entregaron
		$qry_item_recibido =  "Select SUM(CantidadRecibido) as TotalRecibido From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$IDPedidoTercero."'";
		$sql_item_recibido = db_query($qry_item_recibido);
		$row_item_recibido = db_fetch_array($sql_item_recibido);
		$total_recibido = $row_item_recibido["TotalRecibido"];


		if((int)$total_pedido==0 && (int)$total_recibido==0){
			$estado_punto_venta="Guardado";
		}
		elseif($total_pedido==$total_recibido){
			$estado_punto_venta="Entregado Total";
			//actualizo el estado del pedido a entregado si no está actualizado
			if($IDEstadoPedido!="3"):
				$sql_estado_pedido="Update PedidoTercero Set IDEstadoPedidoTercero = '3' Where  IDPedidoTercero = '".$IDPedidoTercero."' Limit 1";
				db_query($sql_estado_pedido);
			endif;
		}
		elseif((int)$total_recibido=="0"){
			$estado_punto_venta="Pendiente Recibir";
		}
		elseif((int)$total_pedido>=(int)$total_recibido){
			$estado_punto_venta="Entregado Parcial";
		}

		return "<b>".$estado_punto_venta."</b>";

}



function notificar_devuelto($IDDetalleTercero,$Observacion){


	$correo = get_field("ParametroTercero","Valor","IDParametroTercero",3);


	$q_detalle_tecero = db_query(" SELECT * FROM DetallePedidoTerceroReferencia WHERE IDDetallePedidoTerceroReferencia = '".$IDDetalleTercero."'");
	$r_detalle_tecero = db_fetch_array($q_detalle_tecero);


	$q_pedido = db_query(" SELECT * FROM PedidoTercero WHERE IDPedidoTercero = '".$r_detalle_tecero[IDPedidoTercero]."'");
	$r_pedido = db_fetch_array($q_pedido);


	//Correo proveedor
	$correo_proveedor = get_field("Proveedor","Email","IDProveedor",$r_pedido[IDProveedor]);
	$nombre_proveedor = get_field("Proveedor","Nombre","IDProveedor",$r_pedido[IDProveedor]);
	if(!empty($correo_proveedor)):
		//$correo=",".$correo_proveedor;
	endif;


	$q_pedido_detalle = db_query(" SELECT * FROM DetallePedidoTercero WHERE IDDetallePedidoTercero = '".$r_detalle_tecero[IDDetallePedidoTercero]."'");
	$r_pedido_detalle = db_fetch_array($q_pedido_detalle);





	include_once("class.phpmailer.php");
	include_once("class.smtp.php");

	$mensaje="
			<body>
				<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
					<tr>
						<td>
							<img src='http://www.calzadocaprino.com/assets/img/logo-caprino.png'>
						</td>
					</tr>
					<tr>
						<td>
							Cordial Saludo:
							<br>
							Le informo que se realiz&oacute; una devoluci&oacute;n de un item de un pedido al recibirlo en el punto de venta, con la siguiente informaci&oacute;n
							<br><br>
							<br><br>
							<table  border='0' cellpadding='0' cellspacing='0' width='452' align='center'>
								<tr>
									<td width='176'>
										NUMERO ORDEN PEDIDO
									</td>
									<td width='624'>
										".$r_pedido[NumeroOrdenCompra]."
									</td>
								</tr>
								<tr>
									<td>
										Proveedor
									</td>
									<td>
										".$nombre_proveedor."
									</td>
								</tr>
								<tr>
									<td>
										Punto de venta
									</td>
									<td>
										".get_field("PuntoVenta","Nombre","IDPuntoVenta",$r_detalle_tecero[IDPuntoVenta]) ."
									</td>
								</tr>
								<tr>
									<td>
										Referencia Caprino
									</td>
									<td>
										".$r_pedido_detalle[ReferenciaCaprino].$r_pedido_detalle[CodigoColor]."
									</td>
								</tr>
								<tr>
									<td>
										Referencia Proveedor
									</td>
									<td>
										".$r_pedido_detalle[ReferenciaProveedor]."
									</td>
								</tr>
								<tr>
									<td>
										Talla
									</td>
									<td>
										".get_field("Talla","Descripcion","IDTalla",$r_detalle_tecero[IDTalla])."
									</td>
								</tr>
								<tr>
									<td>
										Numero Factura
									</td>
									<td>
										".$r_detalle_tecero[NumeroFactura]."
									</td>
								</tr>
								<tr>
									<td>
										Observaciones
									</td>
									<td>
										".$r_detalle_tecero[Observacion]."
									</td>
								</tr>

							</table>
						</td>
					</tr>

				</table>

				<br>


			</body>
	";

	$url_baja="http://www.calzadocaprino.com";
	$mail = new phpmailer();

	$array_correo=explode(",",$correo);

	if (count($array_correo)>0){
		foreach($array_correo as $correo_value){
			$mail->AddAddress($correo_value);
		}
	}


	//$mail->AddAddress("jorgechirivi@gmail.com");

	$mail->Subject="Nota Credito producto devuelto " . $r_pedido[NumeroOrdenCompra];
	$mail->Body =$mensaje;
	$mail->IsHTML(true);
	$mail->Sender='ventas@calzadocaprino.com';
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = true;
	$mail->From = "jaimer@calzadocaprino.com";
	$mail->FromName = "Caprino";
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
	$confirm=$mail->Send();


}



function crear_pdf_pedido($id_pedido_tercero){

	global $dirroot, $url,$libdir;
	$url=str_replace("http","https",$url);

	$sql_pedido = db_query("Select * From PedidoTercero Where IDPedidoTercero = '".$id_pedido_tercero."'");
	$row_pedido = db_fetch_array($sql_pedido);

	if (!empty($id_pedido_tercero)){
		$item_detalle=1;
		$q_detalle = db_query(" SELECT * FROM DetallePedidoTercero WHERE IDPedidoTercero = '$id_pedido_tercero' ORDER BY IDDetallePedidoTercero ASC ");
		while( $r_detalle = db_fetch_array( $q_detalle,$a ) ){
			$array_detalle_orden[ $item_detalle ] = $r_detalle;
			$item_detalle++;
		}
	}


	
	ob_start();

?>
<table align="center" width="90%" >
  <tr>
    <td ><img src="https://calzadocaprino.com//2017/img/LogoCaprino2022.jpeg" width="120" height="80"></td>
    <td valign="top" align="center" >RA72.13<br>V3</td>
  </tr>
  </table>

  



<table align="center" width="90%" bgcolor="#EEEEEE">
  <tr>
    <td ><strong>PROVEEDOR</strong></td>
    <td ><font color="#FF383B"><?php echo strtoupper(get_field("Proveedor","Nombre","IDProveedor",$row_pedido[IDProveedor])); ?></font></td>
    <td ><strong>ORDEN DE COMPRA: No.</strong></td>
    <td colspan="3" ><font color="#FF383B"><?php echo $row_pedido[NumeroOrdenCompra]; ?></font></td>
  </tr>
  <tr>
    <td width="15%" ><strong>RAZON SOCIAL</strong></td>
    <td width="14%" >IMACAL SAS.</td>
    <td width="20%" ><strong>FECHA PEDIDO</strong></td>
    <td width="16%" ><?php echo $row_pedido[FechaPedido]; ?></td>
    <td width="15%" ><strong>FECHA ENTREGA</strong></td>
    <td width="20%" ><?php echo $row_pedido[FechaEntrega]; ?></td>
  </tr>
  <tr>
    <td  nowrap><strong>NIT</strong></td>
    <td  nowrap>860.033.182-4</td>
    <td  nowrap><strong>DIRECCION</strong></td>
    <td  nowrap>CARRERA 41B  Nro. 9-65</td>
    <td  nowrap><strong>TEL</strong></td>
    <td  nowrap>370 12 66</td>
  </tr>
  <tr>
    <td  nowrap><strong>CIUDAD</strong></td>
    <td  nowrap>BOGOTA</td>
    <td  nowrap><strong>Email</strong></td>
    <td colspan="3"  nowrap>jaimer@calzadocaprino.com , currego@calzadocaprino.com</td>
  </tr>
</table>
<table  border="0" cellspacing="1" cellpadding="0" id=table1 width="90%" align="center">
  <tr >
    <td align="center" bgcolor="#CAE2F5" class="texto"><b>Ref Prov</b></td>
    <td align="center" bgcolor="#CAE2F5" class="texto"><b>Ref Cap</b></td>
    <td align="center" bgcolor="#CAE2F5" class="texto"><b>Col</b></td>
    <td align="center" bgcolor="#CAE2F5" class="texto"><b>Cuero Col</b></td>
    <td align="center" bgcolor="#CAE2F5" class="texto"><b>Suela</b></td>
    <td align="center" nowrap bgcolor="#CAE2F5" class="texto"><b>Tacon/Alt</b></td>
    <td align="center" nowrap bgcolor="#CAE2F5" class="texto"><b>Hor</b></td>
    <td align="center" nowrap bgcolor="#CAE2F5" class="texto"><b>Produc</b></td>
    <td align="center" nowrap bgcolor="#CAE2F5" class="texto"><b>Precio</b></td>
    <td align="center" nowrap bgcolor="#CAE2F5" class="texto"><b>Observaciones</b></td>
  </tr>
  <?php
												$sql_detalle = "SELECT * FROM DetallePedidoTercero WHERE IDPedidoTercero = '".$row_pedido[IDPedidoTercero]."' ORDER BY IDDetallePedidoTercero ASC";
												$query_detalle = db_query($sql_detalle);
												$i = 0;
												$segunda = 0;
												while( $r_detalle = db_fetch_object( $query_detalle ) )
												{
													$class = repetition()?"texto":"texto";
													$i++;
											?>
  <tr >
    <td align="center" class="<?php echo $class?>" style="border-bottom:1px solid #000000"><?php
															echo $r_detalle->ReferenciaProveedor;?></td>
    <td align="center" class="<?php echo $class?>" style="border-bottom:1px solid #000000"><?php
															echo $r_detalle->ReferenciaCaprino;?></td>
    <td align="center" class="<?php echo $class?>" style="border-bottom:1px solid #000000"><?php
															echo $r_detalle->CodigoColor;?></td>
    <td align="center" class="<?php echo $class?>" style="border-bottom:1px solid #000000"><?php
															echo $r_detalle->CueroColor;?></td>
    <td align="center" class="<?php echo $class?>" style="border-bottom:1px solid #000000"><?php
															echo $r_detalle->Suela;?></td>
    <td align="center" class="<?php echo $class?>" style="border-bottom:1px solid #000000"><?php
															echo $r_detalle->Tacon;?></td>
    <td align="center" class="<?php echo $class?>" style="border-bottom:1px solid #000000"><?php
															echo $r_detalle->Horma;?></td>
    <td align="center" class="<?php echo $class?>" style="border-bottom:1px solid #000000"><?php
															echo $r_detalle->Producto;?></td>
    <td align="right" class="<?php echo $class?>" style="border-bottom:1px solid #000000">$<?php
															echo $r_detalle->Precio;?></td>
    <td align="left" class="<?php echo $class?>" style="font-size:8px; border-bottom:1px solid #000000"><?php
															echo $r_detalle->Observacion;?></td>
  </tr>
  <tr >
    <td colspan="10" align="center" class="<?php echo $class?>" style="border-bottom:1px solid #000000"><hr></td>
  </tr>
  <?php
												}

											?>
</table>


<table width="90%" align="center" border="0">
<tr>


    			<?php if (!empty($row_pedido[Foto1])): ?>
                <td>				
                <img src="<?php echo $url."admin/imagenes/". $row_pedido[Foto1]; ?>" width="150" height="150" style="float:left;clear:none;">
                </td>
                <?php endif;?>
                <?php if (!empty($row_pedido[Foto2])): ?>
                <td>
                <img src="<?php echo $url."admin/imagenes/". $row_pedido[Foto2]; ?>" width="150" height="150" style="float:left;clear:none;">
                </td>
				<?php endif;?>
                <?php if (!empty($row_pedido[Foto3])): ?>
                <td>
                <img src="<?php echo $url."admin/imagenes/". $row_pedido[Foto3]; ?>" width="150" height="150" style="float:left;clear:none;">
                </td>
				<?php endif;?>
                <?php if (!empty($row_pedido[Foto4])): ?>
                <td>
                <img src="<?php echo $url."admin/imagenes/". $row_pedido[Foto4]; ?>" width="150" height="150">
                </td>
				<?php endif;?>
                <?php if (!empty($row_pedido[Foto5])): ?>
                </tr>
                <tr>
                <td>
                <img src="<?php echo $url."admin/imagenes/". $row_pedido[Foto5]; ?>" width="150" height="150">
                </td>
				<?php endif;?>
	            <?php if (!empty($row_pedido[Foto6])): ?>
                <td>
                <img src="<?php echo $url."admin/imagenes/". $row_pedido[Foto6]; ?>" width="150" height="150">
                </td>
				<?php endif;?>
                <?php if (!empty($row_pedido[Foto7])): ?>
                <td>
                <img src="<?php echo $url."admin/imagenes/". $row_pedido[Foto7]; ?>" width="150" height="150">
                </td>
				<?php endif;?>
                <?php if (!empty($row_pedido[Foto8])): ?>
                <td>
                <img src="<?php echo $url."admin/imagenes/". $row_pedido[Foto8]; ?>" width="150" height="150">
                </td>
				<?php endif;?>

</tr>

</table>

<table width="90%" align="center" border="1" cellpadding="0" cellspacing="0">
	<tr>
    	<td ><span class="texto"><?php echo $row_pedido[Nota1]; ?></span></td>
    </tr>
	<tr>
    	<td style="color:#EF0206"><font color="#FF383B"><?php echo $row_pedido[Nota2]; ?></font></td>
    </tr>
	<tr>
    	<td><span class="texto"><?php echo $row_pedido[Observaciones]; ?></span></td>
    </tr>

</table>


<table width="90%" align="center" border="1" cellpadding="0" cellspacing="0">
	<tr>
    	<td>
       	 <img src="https://www.almacenescaprino.com/images/etiquetacalzadof2022.png" width="550" height="150">
        </td>
    </tr>
</table>





<table width="90%" border="0" align="center" >
                <tbody>
                  <tr>
                    <td colspan="2" valign="top" align="center"><strong>PRIORIDAD DE ENTREGA</strong></td>
                  </tr>
                  <tr>
                    <td valign="top"><table width="100%" border="0" >
                      <tbody>
                        <tr>
                          <td colspan="3" align="center" bgcolor="#B1CFE6"><strong>BOGOTA</strong></td>
                        </tr>
                        <tr>
                          <td align="center" bgcolor="#FFFB4F">1</td>
                          <td align="center" bgcolor="#E7C8E8">2</td>
                          <td align="center" bgcolor="#EDD6CA">3</td>
                        </tr>
                        <tr>
                          <td><?php
                          $sql_prioridad_alta=db_query("Select Nombre from PuntoVenta Where Publicar='S' and IDTipoPrioridad = 1 and IDCiudad = 1 Order by OrdenPrioridadEntrega");
						  while ($row_pto = db_fetch_array($sql_prioridad_alta)){
							echo $row_pto[Nombre] ."<br>";
						  }
						  ?></td>
                          <td><?php
                          $sql_prioridad_media=db_query("Select Nombre from PuntoVenta Where Publicar='S' and IDTipoPrioridad = 2 and IDCiudad = 1 Order by OrdenPrioridadEntrega");
						  while ($row_pto = db_fetch_array($sql_prioridad_media)){
							echo $row_pto[Nombre] ."<br>";
						  }
						  ?></td>
                          <td><?php
                          $sql_prioridad_baja=db_query("Select Nombre from PuntoVenta Where Publicar='S' and IDTipoPrioridad = 3 and IDCiudad = 1 Order by OrdenPrioridadEntrega");
						  while ($row_pto = db_fetch_array($sql_prioridad_baja)){
							echo $row_pto[Nombre] ."<br>";
						  }
						  ?></td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td valign="top" align="center">
                    <table width="91%" border="0" >
                      <tbody>
                        <tr>
                          <td colspan="3" align="center" bgcolor="#BDD9BF"><strong>MEDELLIN</strong></td>
                        </tr>
                        <tr>
                          <td align="center" bgcolor="#FFFB4F">1</td>
                          <td align="center" bgcolor="#E7C8E8">2</td>
                          <td align="center" bgcolor="#EDD6CA">3</td>
                        </tr>
                        <tr>
                          <td><?php
                          $sql_prioridad_alta=db_query("Select Nombre from PuntoVenta Where Publicar = 'S' and IDTipoPrioridad = 1 and IDCiudad = 2 Order by OrdenPrioridadEntrega");
						  while ($row_pto = db_fetch_array($sql_prioridad_alta)){
							echo $row_pto[Nombre] ."<br>";
						  }
						  ?></td>
                          <td><?php
                          $sql_prioridad_media=db_query("Select Nombre from PuntoVenta Where Publicar = 'S' and IDTipoPrioridad = 2 and IDCiudad = 2 Order by OrdenPrioridadEntrega");
						  while ($row_pto = db_fetch_array($sql_prioridad_media)){
							echo $row_pto[Nombre] ."<br>";
						  }
						  ?></td>
                          <td><?php
                          $sql_prioridad_baja=db_query("Select Nombre from PuntoVenta Where Publicar='S' and IDTipoPrioridad = 3 and IDCiudad = 2 Order by OrdenPrioridadEntrega");
						  while ($row_pto = db_fetch_array($sql_prioridad_baja)){
							echo $row_pto[Nombre] ."<br>";
						}
						  ?></td>
                        </tr>
                      </tbody>
                    </table></td>
                  </tr>
                </tbody>
              </table>



 <?php


	 $sql_tallas = "Select * From Talla Where 1 Group By Descripcion Order By Nombre";
	$result_talla = db_query($sql_tallas);
	while ($row_talla = db_fetch_array($result_talla)){
		$array_talla[ $row_talla["IDTalla"] ] = $row_talla;
	}



		$sql_punto_venta_pedido = "Select IDPuntoVenta From DetallePedidoTerceroReferencia Where IDPedidoTercero = '".$id_pedido_tercero."' Group by IDPuntoVenta";
		$result_punto_venta_pedido = db_query($sql_punto_venta_pedido);
		while ($row_punto_venta_pedido = db_fetch_array($result_punto_venta_pedido)){
			$array_puntos_pedido [] = $row_punto_venta_pedido["IDPuntoVenta"];
		}

		if (count($array_puntos_pedido)>0):
			$id_puntos = implode(",",$array_puntos_pedido);
		else:
			$id_puntos	= 0;
		endif;

		 $sql_punto_venta = "Select IDPuntoVenta,Nombre,IDCiudad, Direccion From PuntoVenta Where Publicar = 'S' and IDPuntoVenta  in  (".$id_puntos.")   Order By IDCiudad, Nombre";


	//$sql_punto_venta = "Select IDPuntoVenta,Nombre,IDCiudad, Direccion From PuntoVenta Where Publicar = 'S' and  IDPuntoVenta not in  (16,21)  Order By IDCiudad, Nombre";
	$result_punto_venta = db_query($sql_punto_venta);
	while ($row_punto_venta = db_fetch_array($result_punto_venta)){
		$array_punto_venta[ $row_punto_venta["IDPuntoVenta"] ] = $row_punto_venta;
	}

	 $detalle_inicial=(int)count($array_detalle_orden);


                          if (count($array_punto_venta)>0):
						  	$id_ciudad_ant = "";
							$contador_tabla=0;
							foreach($array_punto_venta as $id_punto_venta => $datos_punto_venta):
							$contador_tabla++;
						   ?>
                          <table width="90%" border="0" cellspacing="1" cellpadding="0" align="center">
						    <tbody>

                            <?php if ($datos_punto_venta[IDCiudad]!=$id_ciudad_ant){
									$id_ciudad_ant= $datos_punto_venta[IDCiudad];
									if ($datos_punto_venta[IDCiudad]=="1")
										$color="#B1CFE6";
									else
										$color="#BDD9BF";


							?>
                            <tr>
								  <td bgcolor="<?php echo $color; ?>"  colspan=10 align=center style="font-size:18px; color:#EB373A"><strong><?php echo get_field("Ciudad","Descripcion","IDCiudad",$datos_punto_venta[IDCiudad]); ?></strong><br>&nbsp;</td>
						    </tr>
                            <?php } ?>

						      <tr>
						        <td class="maintitle" bgcolor="#9daac6" colspan="10" ><?php echo $datos_punto_venta[Nombre] . "<br>" .$datos_punto_venta[Direccion] ?></td>
					          </tr>
						      <tr>
						        <td class="titlemedium">Talla:</td>
                                <?php
								if (count($array_talla)>0):
									unset($suma_item_pedir_talla);
									$total_tienda="0";
									foreach($array_talla as $id_talla => $datos_talla):
									?>
							        <td class="titlemedium" nowrap align="center"><?php echo $datos_talla[Nombre]; ?></td>

                                    <?php endforeach;
								endif;
								?>
                                <td class="titlemedium" nowrap align="center">TOTAL</td>
					          </tr>


                               <?php for($i=1;$i<=$detalle_inicial;$i++):
								unset($array_datos_curva);
								unset($minimo_item);
								unset($maximo_item);
								unset($existencias_item);
								$suma_item_pedir=0;




							   ?>

						      <tr>
						        <td class="rowform">
                                	<?php echo $array_detalle_orden[$i]["ReferenciaCaprino"].$array_detalle_orden[$i]["CodigoColor"];  ?>
                                </td>
	                            <?php

								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									$valor_pedir_item="";
									// Verifico si ya existe algo guardado para no reemplazar
									 $sql_detalle_pedido_ref = "Select Cantidad
															  From DetallePedidoTerceroReferencia
															  Where IDPedidoTercero= '".$row_pedido[IDPedidoTercero]."' and
															  IDDetallePedidoTercero = '".$array_detalle_orden[$i]["IDDetallePedidoTercero"]."' and
															  IDPuntoVenta = '".$datos_punto_venta[IDPuntoVenta]."' and
															  IDTalla = '".$datos_talla[IDTalla]."'";
									$result_detalle_pedido_ref = db_query($sql_detalle_pedido_ref);
									$row_detalle_pedido_ref=db_fetch_array($result_detalle_pedido_ref);

									if (is_numeric($row_detalle_pedido_ref["Cantidad"]))
										$valor_pedir_item = (int)$row_detalle_pedido_ref["Cantidad"];


									 	$suma_item_pedir+=$valor_pedir_item;
										$suma_item_pedir_talla[$datos_talla[IDTalla]] +=  $valor_pedir_item;

										$super_total_talla[$datos_talla[IDTalla]][$array_detalle_orden[$i]["IDDetallePedidoTercero"]]+=$valor_pedir_item;

									?>
						           <td class=row1 align=center>
                                      <?php  if (is_numeric($valor_pedir_item)) echo (int)$valor_pedir_item; ?>
                                   </td>

                                   <?php endforeach;
								endif;
								?>
                                <td bgcolor="#F1CFCF" align=center style="font-weight:bold">
								<?php
									echo number_format($suma_item_pedir,0,",",".");
								?>
                                </td>
					          </tr>
                              <tr>
                              <td style="height:5px" bgcolor="#FFFFFF" >

                                </td>
                               <?php
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>
							        <td bgcolor="#FFFFFF"></td>

                                    <?php endforeach;
								endif;
								?>
                                <td bgcolor="#FFFFFF"></td>
					          </tr>
                              <?php endfor; ?>

                              <tr>
                              <td bgcolor="#F1CFCF" style="font-weight:bold" >TOTALES</td>
                               <?php
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>
							        <td bgcolor="#F1CFCF" align="center" style=" font-weight:bold">
									<?php
										$total_tienda+=$suma_item_pedir_talla[$id_talla];
										if ($suma_item_pedir_talla[$id_talla]!="0"){
											echo number_format($suma_item_pedir_talla[$id_talla],0,",",".");
										}

										?>


                                        </td>

                                    <?php endforeach;
								endif;
								?>
                                <td bgcolor="#F1CFCF" align="center" style="font-weight:bold">
								<?php
								$total_ciudad[$datos_punto_venta[IDCiudad]] += $total_tienda;
								echo number_format($total_tienda,0,",","."); ?>
                                </td>
					          </tr>


					        </tbody>
					      </table>


                         <?php

                          endforeach;
						 endif;
						  ?>

                          <table width="90%" border="0" cellspacing="1" cellpadding="0" align="center">
			    <tbody>



			      <tr>
			        <td align="center" colspan="3" bgcolor="#8F8FF5" style="font-weight:bold; font-style:14px; color:#FFFFFF">RESUMEN</td>
		          </tr>
			      <tr>
			        <td bgcolor="#FFFFFF">TOTAL BOGOTA</td>
			        <td bgcolor="#FFFFFF" align="center" style="font-weight:bold"><?php echo  number_format($total_ciudad[1],0,",",".") ?></td>
			        <td bgcolor="#FFFFFF" align="center">&nbsp;</td>
		          </tr>
			      <tr>
			        <td bgcolor="#FFFFFF">TOTAL MEDELLIN</td>
			        <td bgcolor="#FFFFFF" align="center" style="font-weight:bold"><?php echo  number_format($total_ciudad[2],0,",",".") ?></td>
			        <td bgcolor="#FFFFFF" align="center">&nbsp;</td>
		          </tr>
			      <tr>
			        <td bgcolor="#FEFFBB" style="font-weight:bold">TALLA:</td>
			        <?php
								if (count($array_talla)>0):
									unset($suma_item_pedir_talla);
									$total_tienda="0";
									foreach($array_talla as $id_talla => $datos_talla):
									$suma_talla_resumen="0";
									?>
			        <td bgcolor="#FEFFBB" align="center" style="font-weight:bold"><?php echo $datos_talla[Nombre]; ?></td>
			        <?php endforeach;
								endif;
								?>
			        <td bgcolor="#FEFFBB" align="center" style="font-weight:bold">TOTAL</td>
		          </tr>
			      <?php for($i=1;$i<=$detalle_inicial;$i++):
								$suma_item_pedir=0;
								$suma_talla_resumen="0";

								if (!empty($array_detalle_orden[$i]["IDCurvaTercero"])){
									//Consulto el detalle de minimos y maximos
									$sql_datos_curva= "Select* From DetalleCurvaTercero Where IDCurvaTercero = '".$array_detalle_orden[$i]["IDCurvaTercero"]."'";
									$result_datos_curva = db_query($sql_datos_curva);
									while ($row_datos_curva = db_fetch_array($result_datos_curva)){
										$array_datos_curva[ $row_datos_curva["IDPuntoVenta"] ] [ $row_datos_curva["IDTalla"] ] [ $row_datos_curva["Tipo"] ]  = $row_datos_curva["Valor"];
									}
								}


							   ?>
			      <tr>
			        <td class="rowform"><?php echo $array_detalle_orden[$i]["ReferenciaCaprino"] . $array_detalle_orden[$i]["CodigoColor"];  ?></td>
			        <?php
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):

									 	$suma_item_pedir+=$valor_pedir_item;
										$suma_item_pedir_talla[$datos_talla[IDTalla]] +=  $valor_pedir_item;

									?>
			        <td class=row1 align=center>
                    	<?php echo $super_total_talla[$id_talla][$array_detalle_orden[$i]["IDDetallePedidoTercero"]];
							$suma_talla_resumen+=$super_total_talla[$id_talla][$array_detalle_orden[$i]["IDDetallePedidoTercero"]];
							$suma_talla[$datos_talla[IDTalla]]+=$super_total_talla[$id_talla][$array_detalle_orden[$i]["IDDetallePedidoTercero"]];

						 ?>
                    </td>
			        <?php endforeach;
								endif;
								?>
			        <td bgcolor="#E6EEDA" align=center style="font-weight:bold"><?php

									echo number_format($suma_talla_resumen,0,",",".");
								?></td>
		          </tr>
			      <tr>
			        <td style="height:2px" bgcolor="#FFFFFF" ></td>
			        <?php
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>
			        <td bgcolor="#FFFFFF"></td>
			        <?php endforeach;
								endif;
								?>
			        <td bgcolor="#FFFFFF"></td>
		          </tr>
			      <?php endfor; ?>
			      <tr>
			        <td bgcolor="#E6EEDA" style="font-weight:bold" >TOTALES</td>
			        <?php
								if (count($array_talla)>0):
									foreach($array_talla as $id_talla => $datos_talla):
									?>
			        <td bgcolor="#E6EEDA" align="center" style=" font-weight:bold"><?php
										$total_completo+=$suma_talla[$id_talla];
										echo number_format($suma_talla[$id_talla],0,",",".");


										 ?></td>
			        <?php endforeach;
								endif;
								?>
			        <td bgcolor="#E6EEDA" align="center" style="font-weight:bold"><?php echo number_format($total_completo,0,",","."); ?></td>
		          </tr>
		        </tbody>
		      </table>


<?php
$filedir = $dirroot . "/PedidoTercero/pedidos/";
$name = "Pedido" . $row_pedido[NumeroOrdenCompra] . ".html";
$namePDF = "Pedido" . $row_pedido[NumeroOrdenCompra] . ".pdf";
$file = "$filedir$name";
$filepdf = "$filedir$namePDF";

$page = ob_get_contents();
$fw = fopen($file, "w");
fputs($fw,$page,strlen($page));
fclose($fw);
ob_end_clean();
//echo $page;
//passthru("/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh  --size 'Universal' --textfont Arial  --charset 8859-15 --left 0cm --right 0cm --top 0cm --bottom 0cm --fontsize 7  $file $filepdf ");
//echo "/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldoc.sh $file $filepdf";
passthru("/var/www/vhosts/almacenescaprino.com/cgi-bin/htmldocpedido.sh $file $filepdf");





}

/****** FIN FUNCIONES DE ENVIO DE CORREOS AUTOMATICOS ******/

function calcula_dias($dias){
//Esta pequeña funcion me crea una fecha de entrega sin sabados ni domingos
    //$fechaInicial = date("Y-m-d"); //obtenemos la fecha de hoy, solo para usar como referencia al usuario
	$fechaInicial = date("Y-m-d"); //obtenemos la fecha de hoy, solo para usar como referencia al usuario
    $MaxDias = $dias; //Cantidad de dias maximo para el prestamo, este sera util para crear el for
         //Creamos un for desde 0 hasta 3
         for ($i=0; $i<$MaxDias; $i++){
                  //Acumulamos la cantidad de segundos que tiene un dia en cada vuelta del for
        $Segundos = $Segundos + 86400;
                  //Obtenemos el dia de la fecha, aumentando el tiempo en N cantidad de dias, segun la vuelta en la que estemos
        $caduca = date("D",time()+$Segundos);
		$fecha_caduca=date("Y-m-d",time()+$Segundos);
		$fecha_festivo=get_field("Festivo","Dia","Fecha",$fecha_caduca);

                           //Comparamos si estamos en sabado o domingo, si es asi restamos una vuelta al for, para brincarnos el o los dias...
            if ($caduca == "Sat")
            {
                $i--;
            }
            else if ($caduca == "Sun")
            {
                $i--;
            }
            else if ((int)$fecha_festivo>=1 and $fecha_festivo<=5)
            {
                $i--;
            }
            else
            {
                //Si no es sabado o domingo, y el for termina y nos muestra la nueva fecha
                $FechaFinal = date("Y-m-d",time()+$Segundos);
            }
    }

	return $FechaFinal;

}


function envia_respuesta_cliente($id_pqr,$respuesta){

	global $dirroot;
	include_once("class.phpmailer.php");
	include_once("class.smtp.php");

	$sql_pqr = db_query("Select * From Pqr Where IDPqr = '".$id_pqr."'");
	$row_pqr = db_fetch_array($sql_pqr);
	$tipo_pqr=get_field("TipoPqr","Nombre","IDTipoPqr",$row_pqr["IDTipoPqr"]);
	$motivo_pqr=get_field("MotivoPqr","Nombre","IDMotivoPqr",$row_pqr["IDMotivoPqr"]);
	$cliente_pqr=get_field("Cliente","Nombre","IDCliente",$row_pqr["IDCliente"]) . " " . get_field("Cliente","Apellido","IDCliente",$row_pqr["IDCliente"]);

	$correo=get_field("Cliente","EMail","IDCliente",$row_pqr["IDCliente"]);
	//$correo="jorgechirivi@gmail.com";

	$mensaje="
			<body>
				<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
					<tr>
						<td>
							<img src='http://www.calzadocaprino.com/assets/img/CalzadoCaprinoLogoNew.png'>
						</td>
					</tr>
					<tr>
						<td>
							Cordial Saludo: <br>
							<br>Se ha dado la siguiente respuesta a su solicitud.
							<br><br>
							<table  border='0' cellpadding='0' cellspacing='0' width='452' align='center'>
								<tr>
									<td width='176'>
										NUMERO
									</td>
									<td width='624'>
										".$row_pqr["Numero"]."
									</td>
								</tr>
								<tr>
									<td>
										Fecha
									</td>
									<td>
										".$row_pqr["Fecha"]."
									</td>
								</tr>
								<tr>
									<td>
										Tipo
									</td>
									<td>
										".$tipo_pqr."
									</td>
								</tr>
								<tr>
									<td>
										Motivo
									</td>
									<td>
										".$motivo_pqr."
									</td>
								</tr>
								<tr>
									<td>
										Cliente
									</td>
									<td>
										".$cliente_pqr."
									</td>
								</tr>
								<tr>
									<td colspan='2'>
										<br><strong>Respuesta Caprino (".date("Y-m-d").")</strong><br>
										".$respuesta."
									</td>

								</tr>

								<tr>
									<td colspan='2'>
										<br><br><strong>".$tipo_pqr." (comentario)</strong><br>
										".$row_pqr["Descripcion"]."
									</td>

								</tr>


							</table>
						</td>
					</tr>

				</table>

			</body>
	";


	$url_baja="http://www.calzadocaprino.com";
	$mail = new phpmailer();



	$array_correo=explode(",",$correo);
	if (count($array_correo)>0){
		foreach($array_correo as $correo_value){
			//echo "<br>" . $correo_value;
			$mail->AddAddress($correo_value);
		}
	}

	$mail->AddBCC("drestrepo@calzadocaprino.com");
	//$mail->AddBCC("jaimer@calzadocaprino.com");
	$mail->Subject="Respuesta a su Pqrs ";
	$mail->Body =$mensaje;
	$mail->IsHTML(true);
	$mail->Sender='contacto@calzadocaprino.com';
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = false;
	$mail->From = "contacto@calzadocaprino.com";
	$mail->FromName = "Caprino";
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
	$confirm=$mail->Send();


}

function notificar_nuevo_pqr($id_pqr){

	global $dirroot;
	include_once("class.phpmailer.php");
	include_once("class.smtp.php");

	$sql_pqr = db_query("Select * From Pqr Where IDPqr = '".$id_pqr."'");
	$row_pqr = db_fetch_array($sql_pqr);
	$tipo_pqr=get_field("TipoPqr","Nombre","IDTipoPqr",$row_pqr["IDTipoPqr"]);
	$motivo_pqr=get_field("MotivoPqr","Nombre","IDMotivoPqr",$row_pqr["IDMotivoPqr"]);
	$cliente_pqr=get_field("Cliente","Nombre","IDCliente",$row_pqr["IDCliente"]) . " " . get_field("Cliente","Apellido","IDCliente",$row_pqr["IDCliente"]);

	$correo=get_field("AreaPqr","CorreoResponsable","IDAreaPqr",$row_pqr["IDAreaPqr"]);

	$array_correo = "currego@calzadocaprino.com, mapereira@calzadocaprino.com, drestrepo@calzadocaprino.com, mercadeo@calzadocaprino.com";
	//$array_correo = "jorgechirivi@gmail.com, lucyruro@yahoo.com";

	$mensaje="
			<body>
				<table border='0' cellpadding='0' cellspacing='0' width='800px' align='center'>
					<tr>
						<td>
							<img src='http://www.calzadocaprino.com/assets/img/CalzadoCaprinoLogoNew.png'>
						</td>
					</tr>
					<tr>
						<td>
							Cordial Saludo: <br>
							<br>Se ha creado un nuevo Pqr
							<br><br>
							<table  border='0' cellpadding='0' cellspacing='0' width='452' align='center'>
								<tr>
									<td width='176'>
										NUMERO
									</td>
									<td width='624'>
										".$row_pqr["Numero"]."
									</td>
								</tr>
								<tr>
									<td>
										Fecha
									</td>
									<td>
										".$row_pqr["Fecha"]."
									</td>
								</tr>
								<tr>
									<td>
										Tipo
									</td>
									<td>
										".$tipo_pqr."
									</td>
								</tr>
								<tr>
									<td>
										Motivo
									</td>
									<td>
										".$motivo_pqr."
									</td>
								</tr>
								<tr>
									<td>
										Cliente
									</td>
									<td>
										".$cliente_pqr."
									</td>
								</tr>


								<tr>
									<td>
										Respuesta Caprino
									</td>
									<td>
										".$respuesta."
									</td>
								</tr>

							</table>
						</td>
					</tr>

				</table>

			</body>
	";


	$url_baja="http://www.calzadocaprino.com";
	$mail = new phpmailer();



	$array_correo=explode(",",$correo);
	if (count($array_correo)>0){
		foreach($array_correo as $correo_value){
			//echo "<br>" . $correo_value;
			$mail->AddAddress($correo_value);
		}
	}

	$mail->Subject="Nuevo Pqr";
	$mail->Body =$mensaje;
	$mail->IsHTML(true);
	$mail->Sender='contacto@calzadocaprino.com';
	$mail->Timeout=120;
	$mail->Host = "localhost";
	$mail->Mailer = 'smtp';
	$mail->Password = 's0luci0nes#A';
	$mail->Username = 'postmater@correosim.com';
	$mail->SMTPAuth = true;
	$mail->From = "contacto@calzadocaprino.com";
	$mail->FromName = "Caprino";
	$mail->AddCustomHeader("List-Unsubscribe: <mailto:contacto@calzadocaprino.com>,  <$url_baja>");
	$confirm=$mail->Send();

}


?>
