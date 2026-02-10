<?php
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Francisco Mu–oz :
	Iniciado: May 19/2005
	Ultima Modificación: May 19/2005
*******************************************************************************************/
/*******************************************************************************************
	getExtension: Recibe un nombre de archivo del tipo "file.ext" y retorna su extension "ext"
	Parametros:
			$archivo: Nombre del archivo con su extension
	Retorna:	
			$a: Extension del archivo
*******************************************************************************************/
function getExtension($archivo){
	$a=explode(".",$archivo);
	return $a[count($a)-1];
}
function getName($archivo){
	$a=explode(".",$archivo);
	return $a[0];
}
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Francisco Mu–oz :
	Iniciado: May 19/2005
	Ultima Modificación: May 19/2005
*******************************************************************************************/
/*******************************************************************************************
	size: Recibe el peso de un archivo y retorna su peso en formato Bytes, Kbs, Mbs... etc. 
	Parametros:
			$file: Valor del archivo
	Retorna:	
			Peso del archivo ### Bytes, ### Kbs, etc..
*******************************************************************************************/
function size($file){
	$size = $file;
	$sizes = Array(' Bytes', ' Kbs', ' Mbs', 'Gbs', 'Tbs', 'Pbs', 'Ebs');
	$ext = $sizes[0];
	
	for ($i=1; (($i < count($sizes)) && ($size >= 1024)); $i++) {
		$size = $size / 1024;
	
		$ext  = $sizes[$i];}
	
		clearstatcache();
	return round($size, 2).$ext;
}
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Francisco Mu–oz :
	Iniciado: May 19/2005
	Ultima Modificación: May 19/2005
*******************************************************************************************/
/*******************************************************************************************
	file_size: Recibe el un array con lkos datos de un archivo envido  por POST_FILES 
	y retorna su peso en formato Bytes, Kbs, Mbs... etc. 
	Parametros:
			$file: Array del archivo
	Retorna:	
			Peso del archivo ### Bytes, ### Kbs, etc..
*******************************************************************************************/
function file_size($file){
	$size = filesize($file);
	$sizes = Array(' Bytes', ' Kbs', ' Mbs', 'Gbs', 'Tbs', 'Pbs', 'Ebs');
	$ext = $sizes[0];
	for ($i=1; (($i < count($sizes)) && ($size >= 1024)); $i++) {
	$size = $size / 1024;
	$ext  = $sizes[$i];}
	clearstatcache();
	return round($size, 2).$ext;
}
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Francisco Mu–oz :
	Iniciado: May 19/2005
	Ultima Modificación: May 19/2005
*******************************************************************************************/
/*******************************************************************************************
	bus: Busca en un array asociativo de sub arrays una coincidencia entre sus elementos. 
		 Compara su parte 0 ($extension) y la coincidencia entre sus n elementos. 
	Parametros:
			$extension: Coincidencia a buscar
			$array: Array asociativo de sub arrays
	Retorna:	
			El elemento 0 del sub array que contenga la coincidencia 
*******************************************************************************************/
function bus($extension,$array){
	$a="unknown.gif";
	for($x=0;$x<count($array);$x++){
		if(in_array($extension,$array[$x])){
			$a=$array[$x][0];
		}
	}
	return $a;
}
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Francisco Mu–oz :
	Iniciado: May 19/2005
	Ultima Modificación: May 19/2005
*******************************************************************************************/
/*******************************************************************************************
	file_type: Busca si el tipo de archivo coincide y retorna su extension indicando que es permitido
	Parametros:
			$type: Coincidencia a buscar
	Retorna:	
			$ext: Extension del archivo ".ext" 
*******************************************************************************************/
function file_type($type) {
	if($type == "image/gif")
		$ext = ".gif";  
	if($type == "image/pjpeg")
		$ext = ".jpg"; 
	if($type == "image/jpeg")
		$ext = ".jpg"; 
	if($type == "image/png")
		$ext = ".png"; 	
		
	return($ext);
}// End function file_type
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Francisco Mu–oz :
	Iniciado: May 19/2005
	Ultima Modificación: May 19/2005
*******************************************************************************************/
/*******************************************************************************************
	copy_files: Copia n archivo en una ubicacion especifica del servidor
	Parametros:
			$frm: Array con los datos del formulario
			$files: Array con los datos del o los archivos
			$SubDirFiles: Ubicacion en el servidor donde se almacenaran los archivos
	Retorna:	
			$frm: Array con los datos del formulario y los nuevos datos del archivo como nombre,tamano y tipo 
*******************************************************************************************/
function copy_files($frm,$files,$SubDirFiles) {
	GLOBAL $filedir,$main_types,$tamano_archivo;
	foreach($files AS $key => $file)
	{		
	
		 $ext = $main_types[ $file['type'] ];
		//if(!empty($ext) && $file['size'] <= $tamano_archivo){// &&  $file['size'] > 0){
		if( $file['size'] <= $tamano_archivo){
			
			if(!file_exists($filedir.$SubDirFiles))
				make_dir($filedir.$SubDirFiles);
				
			$file['name'] = rename_file_if_exists($filedir.$SubDirFiles,$file['name']);
			$file['name'] = preg_replace("[^[a-zA-Z0-9]]","",$file['name']);
			if(copy($file['tmp_name'], $filedir.$SubDirFiles.$file['name'])){
		
				$frm[$key]= $file['name'];
				$frm['FileSize']= $file['size'];
				$frm['FileType']= $file['type'];
			}
			
		}
	}
	
	return $frm;
	
}// End function copy_files
/*
*****************************************************************************************************
* Esta funcion copia n imagenes y si se le da el argumento $tn=1 genera su thumbnail ademas si el	*
* ancho de la imagen sobrepasa el ancho permitido genera el thumbnail con el ancho permitido	 	*
* modificada el viernes 01 de Abril 2005						 	 							 	*
*****************************************************************************************************
*/
/*
function copy_imgs($frm,$files,$tn=0) {
	GLOBAL $imagedir, $ancho_archivo,$ancho_thumbnail;
	$cont=0;
	foreach($files AS $key => $file)
	{
		if($file['name']){
			$ext = file_type($file['type']);
			$tam = @getimagesize($file['tmp_name']);
			$w=$tam[0];
			$file['name'] = no_special_char($file['name']); //Eliminamos posibles caracteres especiales
			if(!empty($ext)){
				if(!file_exists($imagedir))
					make_dir($imagedir);
				$file['name'] = rename_file_if_exists("$imagedir",$file['name']);
					 
				if(copy($file['tmp_name'], $imagedir.$file['name'])){
					if($w > $ancho_archivo)
						thumbnail(2,$file['tmp_name'], $ancho_archivo, "$imagedir/".$file['name']);
					if($tn==1)
						thumbnail(2,$file['tmp_name'], $ancho_thumbnail, "$imagedir/tn_".$file['name']);
					$frm[$key]= $file['name'];
				}
			}
		}
	}
	return $frm;	
	
}// End function copy_imgs
*/
function copy_imgs($frm,$files,$tn=0) {
	GLOBAL $imagedir, $ancho_archivo,$ancho_thumbnail;
	$cont=0;
	foreach($files AS $key => $file)
	{
		if($file['name']){
			$ext = file_type($file['type']);
			$tam = @getimagesize($file['tmp_name']);
			$w=$tam[0];
			$file['name'] = no_special_char($file['name']); //Eliminamos posibles caracteres especiales
			
			
			if(!empty($ext)){
				if(!file_exists($imagedir))
					make_dir($imagedir);
				$file['name'] = rename_file_if_exists("$imagedir",$file['name']);
					 
				if($w < $ancho_archivo){
					$valor_unico = date("s").rand(1,10000);	
					$nombre_archivo_unico = $valor_unico."_".$file['name'];
					if(copy($file['tmp_name'], $imagedir.$nombre_archivo_unico)){
						if($tn==1)
							thumbnail(2,$file['tmp_name'], $ancho_thumbnail, "$imagedir/tn_".$nombre_archivo_unico);
						$frm[$key]= $nombre_archivo_unico;
					}
				}
				else
					window_alert("La imagen tiene un ancho mayor a $ancho_archivo pixeles y no sera cargada");
			}
		}
	}
	return $frm;	
	
}// End function copy_imgs
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Francisco Mu–oz :
	Iniciado: May 19/2005
	Ultima Modificación: May 19/2005
*******************************************************************************************/
/*******************************************************************************************
	make_dir: Crea un directorio en el servidor, si este no existe, con los permisos 757
	Parametros:
			$dir_name: Nombre del directorio a crear
	Retorna:	
			Si tiene exito crea el directorio en el servidor de lo contrario arroja un mensaje de error
*******************************************************************************************/
function make_dir($dir_name){	
	if(!mkdir($dir_name,0755))
		echo "Error al Crear directorio $dir_name";
	else
		chmod($dir_name,0757);
}
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Francisco Mu–oz :
	Iniciado: May 19/2005
	Ultima Modificación: May 19/2005
*******************************************************************************************/
/*******************************************************************************************
	no_special_char: Elimina los caracteres especiales de una cadena
	Parametros:
			$cad: Cadena con formato a validar
	Retorna:	
			$cad: Si cumple con el formato retorna la misma cadena sino le elimina los caracteres no permitidos
*******************************************************************************************/
function no_special_char($cad){
	$cad = preg_replace("[^a-zA-Z0-9\.\_]","",$cad);
	return $cad;
}
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Francisco Mu–oz :
	Iniciado: May 19/2005
	Ultima Modificación: May 19/2005
*******************************************************************************************/
/*******************************************************************************************
	rename_file_if_exists: Compara el nombre de archivo que se quiere subir al servidor
						   con los existentes en el servidor. Si existe otro archivo 
						   con ese mismo nombre le incrementa un numero al posfijo cp ej: "file_cp1.ext"
	Parametros:
			$root: Directorio del servidor donde se encuentran los archivos a comparar
			$file: Nombre del archivo
	Retorna:	
			$file: Si no hay coincidencias devuelve el nombre de archivo de lo contrario 
			le incrementa un numero al posfijo cp.
*******************************************************************************************/
function rename_file_if_exists($root,$file){
	$fin=0; //iniciamos contador de coincidencias en 0
	$flag=0; //iniciamos flag en 0 no hay coincidencias
	$file = no_special_char($file); //Eliminamos posibles caracteres especiales
	$file_tmp = $file; //asignamos en un temporal para validar
	$path = opendir($root); //abrimos el directorio 
	while ($obj = readdir($path)){ //mientras hayan archivos ($obj) en ese archivo
		if($obj == $file_tmp){ //comparamos cada uno de los archivos del path con el archivo temporal
			$arch=explode(".",$file); //existe un archivo con ese nombre
			$name = $arch[0];
			$ext = $arch[1];
			$fin ++; //incrementamos $fin hasta que no hayan coincidencias
			$file_tmp = $name."_cp".$fin.".".$ext; //asignamos a $file_tmp el formato de archivo  
									//ej: arch_cp2 para que luego en el if valide coincidencias
			$flag=1;  //flag en 1 el archivo exste
		}
	}
	
	if($flag==1){ //se encontro una o mas coincidencias en el directorio
		$file = explode(".",$file); 
		$file[0] .= "_cp".$fin; //asignamos al nombre de archivo el formato
		$file = $file[0].".".$file[1]; //le agrego la extension
	}
	return $file; //retornamos el nuevo nombre de archivo
}
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Fabio Sanchez :
	Iniciado: Nov 19/2004
	Ultima Modificación: May 19/2004
*******************************************************************************************/
/*******************************************************************************************
	delete_img: Elimina un archivo en el servidor
	Parametros:
			$foto: Nombre del archivo a eliminar
			$campo : Campo en la base de datos que contiene el nombre del archivo
			$id : Clave del archivo en la base de datos
	Retorna:	
			Elimina el archivo
*******************************************************************************************/
function delete_img($foto,$campo,$id) {
	GLOBAL $TitleMod,$Table,$MOD,$Key,$imagedir;
	
	$campo = "$campo = \"\" ";
	$qid = db_query(" UPDATE $Table SET $campo WHERE $Key = '$id' ");
	unlink("$imagedir/$foto");
} // End function delete_img
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Fabio Sanchez :
	Iniciado: Nov 19/2004
	Ultima Modificación: May 19/2004
*******************************************************************************************/
/*******************************************************************************************
	delete_img: Elimina un archivo en el servidor
	Parametros:
			$foto: Nombre del archivo a eliminar
			$campo : Campo en la base de datos que contiene el nombre del archivo
			$id : Clave del archivo en la base de datos
	Retorna:	
			Elimina el archivo
*******************************************************************************************/
function delete_file($file,$campo,$id) {
	GLOBAL $TitleMod,$Table,$MOD,$Key,$filedir;
	
	$campo = "$campo = \"\", ".$campo."Type = \"\", ".$campo."Size = \"\" ";
	$qid = db_query(" UPDATE $Table SET $campo WHERE $Key = '$id' ");
	unlink("$filedir$file");
} // End function delete_img
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Francisco Mu–oz :
	Iniciado: May 19/2005
	Ultima Modificación: May 19/2005
*******************************************************************************************/
/*******************************************************************************************
	header_download: Encabezado para la descarga de archivos con control de cahce en S.O. Win9X
	Parametros:
			$file: Ruta del archivo a descargar
			$filename : Nombre  del archivo a descargar
	Retorna:	
			Inicia descarga 
*******************************************************************************************/
function header_download($file,$filename){
	// BEGIN extra headers to resolve IE caching bug (JRP 9 Feb 2003)
	// [http://bugs.php.net/bug.php?id=16173]
	header("Pragma: ");
	header("Cache-Control: ");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	//	header("Cache-Control: no-store, no-cache, must-revalidate");  
	//HTTP/1.1
	//	header("Cache-Control: post-check=0, pre-check=0", false);
	// END extra headers to resolve IE caching bug
	
	header("Content-Length: ".filesize($filename)); 
    header("Content-Type: $file->FileType");
 	header("Content-Disposition: attachment; filename={$file->File}"); 
	readfile($filename);
}
/*******************************************************************************************
	Libreria de funciones básicas para php
	Creador por Francisco Mu–oz :
	Iniciado: May 19/2005
	Ultima Modificación: May 19/2005
*******************************************************************************************/
/*******************************************************************************************
	count_download: Incrementa el conteo de un archivo descargado
	Parametros:
			$Table: Tabla de documentos
			$Key  : Clave primaria de la Tabla
			$id   : Numero de la clave al cual se le adiciona la descarga 
	Retorna:	
			El contador de descargas aumentado en 1 (uno) 
*******************************************************************************************/
function count_download($Table, $Key, $id){
	db_query("UPDATE $Table SET  Descargas = Descargas+1 WHERE $Key = '$id'");
	return true;
}
?>
