<?
error_reporting(E_ALL);
$dbhost = "localhost";
$dbname = "Caprino";
$dbuser = "almacenescaprino";
$dbpass = "c4prin0";

//URL Sitio
$url="http://www.almacenescaprino.com/";

$error_acceso = "Opps!! no tienes permisos de escritura";

$dirroot = dirname(__FILE__)."/";
$libdir   = $dirroot."lib/";

$DB_DEBUG = true;
$DB_DIE_ON_FAIL = true;

require($libdir."stdlib.inc.php");
require($libdir."dblib.inc.php"); 
require($libdir."passlib.php"); 
require($libdir."search.inc.php");
require($libdir."buildNav.php");
require($libdir."navbar.inc.php");
require($libdir."caprino.php");
require($libdir."pedidos_caprino.php");
require($libdir."salidas_caprino.php");
require($libdir."entradas_caprino.php");
require($libdir."Calc.php");


$app_title= "Puntos de Venta :: CAPRINO";
$ME = $SCRIPT_NAME;
$crypt = "c4prino";

//************* VARIABLES DE SESSION ************
$strcript="INTRANETOLA"; //cadena para crear el password
$tiemposession=2000; //Intervalo de tiempo de duracion de las sessiones en el admin
$tiemposessioninter=40; //Intervalo de tiempo de duracion de las sessiones en la intranet
//************* ... ************
/***********************/
$numerocuotas = 6;
$diascuota = 15;
/***********************/
$formapago = array("Efectivo"=>"Efectivo",
					"Cheque"=>"Cheque",
					"Tarjeta Credito"=>"Tarjeta Credito",
					"A convenir"=>"A convenir"); 


/*********IMPUESTOS******/
$ReteIVA = 0.10;
$ReteICA = 0.00414;
/************************/
					
$array_nivel = array("1"=>"Actualizaci&oacute;n",
					"2"=>"Administrador",
					"3"=>"General - Puntos de Venta");
					
$Mes_array = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$Dia_array = array("1"=>"Lunes","2"=>"Martes","3"=>"Miercoles","4"=>"Jueves","5"=>"Viernes","6"=>"Sabado","0"=>"Domingo");

$edo_orden = array("0"=>"Solicitada","1"=>"Despachada","2"=>"Cancelada");

$array_edocivil = array("1"=>"Casado","2"=>"Soltero","3"=>"Separado","4"=>"Viudo","5"=>"Union Libre");
	
$array_gustos = array("Autom&oacute;viles"=>"Autom&oacute;viles",
"Casa/Jard&iacute;n"=>"Casa/Jard&iacute;n",
"Concursos/Promociones"=>"Concursos/Promociones",
"Comida/Cocina"=>"Comida/Cocina",
"Deportes"=>"Deportes",
"Educaci&oacute;n/Formaci&oacute;n"=>"Educaci&oacute;n/Formaci&oacute;n",
"Empleo"=>"Empleo",
"Familia/Ni&ntilde;os"=>"Familia/Ni&ntilde;os",
"Finanzas"=>"Finanzas",
"Inform&aacute;tica"=>"Inform&aacute;tica",
"Internet"=>"Internet",
"Juegos"=>"Juegos",
"Libros"=>"Libros",
"Moda"=>"Moda",
"Negocios"=>"Negocios",
"Noticias/Novedades"=>"Noticias/Novedades",
"M&uacute;sica"=>"M&uacute;sica",
"Ocio/Entretenimiento"=>"Ocio/Entretenimiento",
"Tecnolog&iacute;a/Entret."=>"Tecnolog&iacute;a/Entret.",
"Regalos"=>"Regalos",
"Salud/Belleza"=>"Salud/Belleza",
"Viajes"=>"Viajes",
"Vivienda/Inmobiliaria"=>"Vivienda/Inmobiliaria",
"Animales"=>"Animales",
"Arte"=>"Arte",
"Cine"=>"Cine",
"Formaci&oacute;n Online"=>"Formaci&oacute;n Online",
"Otros"=>"Otros");			

$array_deportes = array("Futbol"=>"Futbol",
"Baloncesto"=>"Baloncesto",
"Beisbol"=>"Beisbol",
"Futbol Americano"=>"Futbol Americano",
"Tenis"=>"Tenis",
"Golf"=>"Golf",
"Tenis de Mesa"=>"Tenis de Mesa",
"Billar"=>"Billar",
"Atletismo"=>"Atletismo",
"Otros"=>"Otros");	

$array_hobbies = array("Deportes Extremos"=>"Deportes Extremos",
"Viajar"=>"Viajar",
"practicar deportes"=>"practicar deportes",
"Hacer Ejercicio"=>"Hacer Ejercicio",
"Comprar Zapatos"=>"Comprar Zapatos",
"Otros"=>"Otros");				

$array_musica	= array("Heavy Metal"=>"Heavy Metal",
"Salsa"=>"Salsa",
"Merengue"=>"Merengue",
"Rock"=>"Rock",
"Electronica"=>"Electronica",
"Otros"=>"Otros");		

$dblink = db_connect($dbhost, $dbname, $dbuser, $dbpass);

Encabezado();

?>
