<?php
	include("../config.inc.php");
	Encabezado();
	
	//$sql_clientes = " SELECT * FROM Cliente, FidClienteRespuesta WHERE Cliente.IDCliente = FidClienteRespuesta.IDCliente GROUP BY FidClienteRespuesta.IDCliente ";
        $sql_clientes = " 
       
     SELECT cl.*, fcr.* FROM Cliente cl, FidClienteRespuesta fcr WHERE cl.IDCliente = fcr.IDCliente   GROUP BY fcr.IDCliente  
       
";
	GLOBAL $campo;
$now_date = date('m-d-Y H:i');
$result = db_query($sql_clientes);
$title = "Datos Reporte Fidelizacion Fecha $now_date";
$file_type = "vnd.ms-excel";
$file_ending = "xls";
header("Pragma: ");
header("Cache-Control: ");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Content-Type: application/$file_type; charset=ISO-8859-1");
header("Content-Disposition: attachment; filename=$title.$file_ending"); 
echo("$title\n");
//define separator (defines columns in excel & tabs in word)
$sep = "\t"; //tabbed character
$ponerdetalle = "";
print("\n");
//end of printing column names
//Poner los nombres de las columnas
	
	echo "Cedula" . $sep;
	echo "Nombre" . $sep;
	echo "Apellido" . $sep;
	echo "Telefono" . $sep;
        echo "Direccion" . $sep;
	echo "Celular" . $sep;
	echo "Email" . $sep;
	//echo "Fecha" . $sep;
        echo "NumeroHijos" . $sep;
        echo "EstadoCivil" . $sep;
        echo "Ciudad" . $sep;
        
        
        
	
	//traer preguntas
	$sql_preguntas = " SELECT * FROM FidPregunta ";
	$qry_preguntas = db_query( $sql_preguntas );
	while( $r_preguntas = db_fetch_array( $qry_preguntas ) )
	{
		$preguntas[] = $r_preguntas;
	}//end while preguntas
	
	foreach( $preguntas as $key_pregunta => $datos_pregunta )
	{
		echo utf8_encode( $datos_pregunta["Pregunta"] ). $sep;
		//echo "Otro? Cual ?" . $sep;
	}//end for
print("\n");	
//start while loop to get data
    while($row = db_fetch_array($result))
    {
        
		echo $row["Cedula"] . $sep;
		echo $row["Nombre"] . $sep;
		echo $row["Apellido"] . $sep;
		echo $row["Telefono"] . $sep;
                echo $row["Direccion"] . $sep;
		echo $row["Celular"] . $sep;
		echo $row["EMail"] . $sep;
		echo $row["NumeroHijos"] . $sep;
                echo $row["EstadoCivil"] . $sep;  
                
                $sql_Ciudad = "SELECT * FROM `Ciudad` WHERE `IDCiudad` ='".$row["IDCiudad"]. "'";
		
                $qry_Ciudad = db_query( $sql_Ciudad );
                if($qry_Ciudad != ""){
                   while( $r_Ciudad = db_fetch_array( $qry_Ciudad ) )
		{
                    
                    echo $r_Ciudad["Descripcion"] . $sep;
                } 
                }
                else{
                    echo "" . $sep;
                }
                
               
		
		//consultar las respuestas
		$array_resp = array();
		//$sql_resp = "SELECT *, FidClienteRespuesta.FechaTrCr as FechaRespuesta FROM FidClienteRespuesta, FidOpcion WHERE IDCliente = '" . $row["IDCliente"] . "' AND FidOpcion.IDFidOpcion = FidClienteRespuesta.IDFidOpcion  ";
                $sql_resp = "SELECT *, FidClienteRespuesta.FechaTrCr as FechaRespuesta FROM FidClienteRespuesta, FidOpcion, FidPregunta WHERE IDCliente = '" . $row["IDCliente"] . "' AND FidOpcion.IDFidOpcion = FidClienteRespuesta.IDFidOpcion  AND FidOpcion.IDFidPregunta = FidPregunta.IDFidPregunta  ";
		$qry_resp = db_query( $sql_resp );
		while( $r_resp = db_fetch_array( $qry_resp ) )
		{	
			
                    
                        $array_resp[ $r_resp["IDFidPregunta"] ] = $r_resp["Opcion"];
                        $array_abiertas[ $r_resp["IDFidPregunta"] ] = $r_resp["Respuesta"];
			$FechaRespuesta = $r_resp["FechaRespuesta"];
			//$array_resp[ $r_resp["IDFidPregunta"] ]["Abierta"] = trim( $r_resp["Respuesta"] );
		}//end while
		
		echo $FechaRespuesta . $sep;
			
		//PINTAR RESPUESTAS	
                
                
		foreach( $preguntas as $key_pregunta => $datos_pregunta )
		{
			echo utf8_encode( $array_resp[ $datos_pregunta["IDFidPregunta"] ] ). $sep;
                      //  echo "//". $sep;
                        echo utf8_encode( $array_abiertas[ $datos_pregunta["IDFidPregunta"] ] ). $sep;
                       //  echo "//". $sep;
			//echo $array_resp[ $datos_pregunta["IDFidPregunta"] ]["Abierta"] . $sep;
		}//end for
		
        print "\n";
    }
