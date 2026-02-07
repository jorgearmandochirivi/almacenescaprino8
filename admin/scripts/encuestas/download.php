<?php
	include ("../../admin/config.inc.php3");
	$datos = Verifica_SesionCliente();
	$Nombre_Usuario = $datos["Nombre"];
	$IDUsuario=$datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	if (!$datos["flag"])
        		header("Location: ../../admin/login.php3?redirect=index.php3");

if (isset($id)) {
	$qry_file = db_query("SELECT Boletin FROM vbooth_desc WHERE pollID = '$id' ");
	$file = db_fetch_object($qry_file);
	$filename = "$filedir/$DirFilesBole$file->Boletin";

	header("Content-Type: application/force-download"); 
	header("Content-Length: ".filesize($filename)); 
	header("Content-Disposition: attachment; filename=".$file->Boletin); 
	readfile($filename); 
	exit;
}

?>