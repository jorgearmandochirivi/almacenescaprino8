<?php
//exit; 
$dbhost = "lldd216.servidoresdns.net";

/*
$dbname = "Baxport";
$dbuser = "root";
$dbpass = "root";
*/

$dbname = "qcz401";
$dbuser = "qcz401";
$dbpass = "Alexiter25";


$dirroot = dirname(__FILE__)."/";
$libdir   = $dirroot."lib/";
$dir_img = "img";
$imagedir = $dirroot.$dir_img;
$dir_file = "files";
$filedir = $dirroot."../".$dir_file;
//$urlroot = "http://".$_SERVER["HTTP_HOST"]."/inetwork"; 
$urlroot = "http://".$_SERVER["HTTP_HOST"]."/baxport/"; 
$urlrootimg = $urlroot."/img";

$tamano_archivo = 20000000;
$ancho_archivo = 5000; // Ancho de las imagenes en pixeles

$folder_img_contenido = $dirroot."../img/contenido/";
$folder_ver_contenido = $urlroot."img/contenido/";
$folder_img_banner = $dirroot."../img/banner/";
$folder_ver_banner = $urlroot."img/banner/";
$folder_img_productos = $dirroot."../img/productos/";
$folder_ver_productos = $urlroot."img/productos/";

/******************** Boletines ********************/
$codemp = '005';
$edo_prod = "00"; // 01 Inactivo 00 activo
$edo_cli = "0";
$zonagral = "999999";
$numdec = 2;
$maxdata = 7;

$orderdir = $filedir."pedido/";
$remisiondir = $filedir."remision/";
$respdir = $filedir."respuesta/";
$procdir = $filedir."procesados/";

$folder_img_boletin = $dirroot."../img/boletin/";
$folder_ver_boletin = $urlroot."img/boletin/";

/**************************************************/
$DB_DEBUG = true;
$DB_DIE_ON_FAIL = true;

require($libdir."stdlib.inc.php");
require($libdir."dblib.inc.php");
require($libdir."search.inc.php");
require($libdir."buildNav.php");
require($libdir."filelib.php");
require($libdir."shoppingcart.php"); 

$url = $urlroot;

$app_title= "Administrador Cotenido / Cat&aacute;logo Virtual :: Baxport";
$ME = $SCRIPT_NAME;

$Mes_array = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

$array_nivel = array("1"=>"Actualizacion",
					"2"=>"Administrador");
$array_modulos = array( "subcategoria"=>array( "idsec"=>"IDSubCategoria" ),
						"producto"=>array( "idsec"=>"IDSubCategoria","idp"=>"IDProducto" ) );
$array_permiso_seccion = array( "inventario"=>"Ver Inventario","precio"=>"Ver Precios","reservas"=>"Ver Reservas","compras"=>"Ver Compras" );						

$estado_reserva = array( "S"=>"Solicitada","A"=>"Aprobada","R"=>"Rechazada" );						
$estado_pedido = array( "S"=>"Solicitada","A"=>"Autorizado","R"=>"Recibido" );						


/**************************************************/	
									
$array_edocivil = array("1"=>"Casado","2"=>"Soltero","3"=>"Separado","4"=>"Viudo","5"=>"Union Libre");

$tipos=array(
	
			array("zipped.gif","zip","rar","cab","arj","lzh","ace","tar","gzip","uue","bz2"),
			array("imagenes.gif","gif","jpg","jpeg","png","bmp","jpe"),
			array("musica.gif","mp3","wav","snd","au","aif","aifc","aiff","wma","rm","rmx","rmj","rms"),
			array("video.gif","mpeg","mpg","avi","wmv","mov"),
			array("txt.gif","txt","rtf"),
			array("doc.gif","doc"),
			array("webs.gif","htm","html","asp","php","shtml","xlm","as","jsp","asa","aspx","cfm","php4","php3","vbs","css","js"),
			array("ppt.gif","ppt"),
			array("xls.gif","xls"),
			array("pdf.gif","pdf"),
			array("swf.gif","swf"),
			array("exe.gif","exe")
			);
			

	
	// definir Descripcion por tipos de archivo	mimetype
$main_types=array(
"text/plain"=>"Archivo de Texto",
"application/msword"=>"MS Word",
"application/pdf"=>"Acrobat Reader",
"application/vnd.ms-excel" =>"MS Excel",
"application/vnd.ms-powerpoint"=>"MS PowerPoint",
"application/ms-powerpoint"=>"MS PowerPoint",
"application/mspowerpoint"=>"MS PowerPoint",
"application/x-shockwave-flash"=>"MacroMedia Flash",
"text/html"=>"Formato Web",
"image/tiff"=>"Archivo de Imagen",
"image/gif"=>"Archivo de Imagen",
"image/jpeg"=>"Archivo de Imagen",
"audio/mpeg"=>"Audio MP3",
"audio/x-midi"=>"Audio Secuencia MIDI",
"audio/x-wav"=>"Audio Audio WAV",
"video/mpeg"=>"Video MPEG",
"video/vndvivo"=>"Formato de Video",
"video/quicktime"=>"Video Quicktime",
"video/x-msvideo"=>"Video AVI",
"video/x-ms-wmv"=>"Video Windows Media",
"application/acad"=>"Formato Autocad",
"application/vndms-project"=>"MS-Project",
"application/vnd.ms-project"=>"MS-Project",
"application/wordperfect51"=>"Word Perfect 5.1",
"application/octet-stream"=>"Archivo de Texto",
"application/x-gzip"=>"Archivo Comprimido",
"application/zip"=>"Archivo Comprimido",
"application/x-tar"=>"Archivo Comprimido",
"image/bmp"=>"Archivo de Imagen",
"image/png"=>"Archivo de Imagen",
"image/x-png"=>"Archivo de Imagen",
"text/rtf"=>"Texto Enriquecido",
"application/vnd.sun.xml.writer"=>"Archivo SXW",
"application/vnd.sun.xml.writer.global"=>"Archivo SXG",
"application/vnd.sun.xml.draw"=>"Archivo SXD",
"application/vnd.sun.xml.calc"=>"Archivo SXC",
"application/vnd.sun.xml.impress"=>"Archivo SXI",
"text/vcf"=>"Archivo VCard",
"application/x-gzip"=>"Archivo TGZ",
"application/x-gzip"=>"Archivo GZ"
);

$dblink = db_connect($dbhost, $dbname, $dbuser, $dbpass);

?>
