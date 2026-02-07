<?php
	include("../../admin/config.inc.php");	
	$sql_referente = " Select * From Cliente Where Cedula = '" . $_POST["NumeroDocumento"] . "' LIMIT 1 ";
	$qry_referente = db_query( $sql_referente );
	$r_referente = db_fetch_array( $qry_referente );
	if(!empty($r_referente["IDCliente"])){
		echo json_encode($r_referente["IDCliente"]);
	}
	else{
		echo json_encode("no_existe");
	}?>