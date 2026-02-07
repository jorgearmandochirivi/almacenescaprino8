<?php
require_once('php-jwt-master/src/BeforeValidException.php');
require_once('php-jwt-master/src/ExpiredException.php');
require_once('php-jwt-master/src/JWT.php');
require_once('php-jwt-master/src/SignatureInvalidException.php');

use Firebase\JWT\JWT;

class SIMWebServiceToken
{

		function get_token($Usuario,$Clave){
				if ( !empty( $Usuario ) && !empty( $Clave ) ) {
					 $issuedAt   = time();
					 $notBefore  = $issuedAt + 0;             //Adding 10 seconds
					 $segundos_expira=60000;
					 $expire     = $notBefore + $segundos_expira;            // Adding 60 seconds


					$sql_verifica = "SELECT * FROM UsuarioWS WHERE Usuario = '" . $Usuario . "' and  Clave = '" . sha1( $Clave ) . "' and Activo = 'S' Limit 1";
					$qry_verifica = db_query($sql_verifica);
					if ( db_num_rows( $qry_verifica ) == 0 ) {
						$respuesta[ "message" ] = "Datos incorrectos";
						$respuesta[ "success" ] = false;
						$respuesta[ "response" ] = NULL;
					}
					else{
							$response=array();
							$datos_usuario = db_fetch_array( $qry_verifica );
							//Genero el token
					    $token = array(
					       "iss" => "https://www.almacenescaprino.com",
					       "aud" => "https://www.almacenescaprino.com",
					       "iat" => $issuedAt,
					       "nbf" => $notBefore,
								'exp'  => $expire,
								"data" => array(
								"IDUsuarioWS" => $datos_usuario["IDUsuarioWS"],
								"Nombre" => $datos_usuario["Nombre"],
								"Empresa" => $datos_usuario["Empresa"]
							)
					    );

							$jwt = JWT::encode($token, KEY_TOKEN);
							$datos_token["Token"] = $jwt;
							$datos_token["Expira"] = $segundos_expira;
							$sql_actualiza = "UPDATE UsuarioWS SET Token = '" . $jwt . "' , FechaTrEd = NOW() WHERE IDUsuarioWS = '".$datos_usuario["IDUsuarioWS"]."'  ";
							db_query($sql_actualiza);

							array_push($response, $datos_token);

							$respuesta[ "message" ] = "Token Generado con exito ";
							$respuesta[ "success" ] = true;
							$respuesta[ "response" ] = $response;
					}

				} else {
					$respuesta[ "message" ] = "T1. Atencion faltan parametros";
					$respuesta[ "success" ] = false;
					$respuesta[ "response" ] = NULL;
			}
			return $respuesta;
		}

		function valida_token($Token){
				if ( !empty( $Token )  ) {
					//print_r($decoded);
					try {
							// decode jwt
							
							$decoded = JWT::decode($Token, KEY_TOKEN, array('HS256'));							
							$respuesta[ "message" ] = "Token valido";
							$respuesta[ "success" ] = true;
							$respuesta[ "response" ] = null;
					}
					catch (Exception $e){
						print_r($e);
						$respuesta[ "message" ] = "Token invalido";
						$respuesta[ "success" ] = false;
						$respuesta[ "response" ] = "";
					}

				} else {
					$respuesta[ "message" ] = "T2. Token vacio";
					$respuesta[ "success" ] = false;
					$respuesta[ "response" ] = NULL;
			}
			return $respuesta;
		}


		function comprobar_token($Token){			
				//Valido el Tokenç
				$sql_tok="SELECT Token FROM UsuarioWS WHERE Token = '".$Token."' ";
				$qry_tok = db_query($sql_tok);
				if ( db_num_rows( $qry_tok ) > 0 ) {
					$respuesta[ "message" ] = "Token valido";
					$respuesta[ "success" ] = true;
					$respuesta[ "response" ] = null;
				}	
				else{
					$respuesta[ "message" ] = "Token invalido";
					$respuesta[ "success" ] = false;
					$respuesta[ "response" ] = null;
				}
				if(!$respuesta["success"]){
					die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
					exit;
				}
		}


		function liberar_token(){
			$mifecha = new DateTime(); 
			$mifecha->modify('-30 minute'); 
			$FechaHoraBorrar=$mifecha->format('Y-m-d H:i:s');
			$sql_tok="UPDATE UsuarioWS SET Token = ''  WHERE FechaTrEd <= DATE_SUB(NOW(),INTERVAL 60 MINUTE) ";
			db_query($sql_tok);
		}

}//end class
?>
