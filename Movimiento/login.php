<?php

function logear($usr, $pswrd)
{

	global $dbo;
	
				$sqllog =$dbo->query("SELECT * FROM Registro WHERE Email='".$usr."' AND Password=OLD_PASSWORD('".$pswrd."')");
				
				
				if($dbo->rows($sqllog) != 0)
				{
					$datos_user_obj = $dbo->object( $sqllog );				
								
					$Usuario = $datos_user_obj;
			
					$usuariosave = addslashes( serialize( $Usuario ) );
			
					$newsesion = md5( uniqid( date( "Y-m-d" , time() ) ) );
			
					$fecha = date( "Y-m-d H-i-s" , time() );
			
					$guardarqry = $dbo->query( "INSERT INTO Sesion_Cliente values ( '" . $newsesion . "' , '".$Usuario->IDRegistro."', '" . $fecha . "' , '" . $usuariosave . "' )" );
					
			
			
					setcookie( "SESION_CLIENTE" , $newsesion, time()+3600);
					
					
						$logeado='s';
					
					header("Location:index.php?m=est");	
					
				}else{
					array_push($error,'Usuario y/o contrase&ntilde;a incorrectas');
				}
			
}
?>           