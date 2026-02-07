#!/usr/bin/php -q
<?php

	include("../../config.inc.php");
	
	
	
	 $file = "BaseSuavidadEmailW.txt";
	 
	if($fp = fopen($file,"r")){
			
		$cont = 0;		
		ini_set('auto_detect_line_endings', true); 		
		$IGNORE_FIRTS_ROW = true;
		if($IGNORE_FIRTS_ROW)
			$row = fgets($fp,4096);
			
		while(!feof($fp)){
			
				$FIELD_TEMINATED = "TAB";
				
				$row = fgets($fp,4096);			
				if(!empty($FIELD_TEMINATED))
					if($FIELD_TEMINATED == "TAB")
						$row_data = explode("\t",$row);
					else
						$row_data = explode($FIELD_TEMINATED,$row);	
				
				//Relacion de Campos
				$Cedula = $row_data[0];
				$Email = $row_data[1];				
				
				
				if(!empty($Cedula) && !empty($Email)){
								
						$sql_edit_socio = "Update Cliente 
										  Set EMail  = '".$Email."' 
										  Where Cedula = '".$Cedula."'";
						db_query($sql_edit_socio);	
						
				}
				$cont++;			
		} // END While
		fclose($fp);	
			return array("Numregs"=>$cont,"RegsOK"=>$numregok);
	}
	else
		echo "error open $file";
		
?>