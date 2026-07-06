<?php
/**** SERVICIOS JSON PARA APPS MOVILES ******/
/**** Creación: Jorge Chirivi ******/
/**** Fecha de Creación: 24 de Junio de 2022 ******/
/**** Scripts Iniciales ******/
require("../admin/config.inc.php");
require  "lib/SIMWebServiceToken.inc.php";

define( "KEY_TOKEN" , "MiClubApp#001.Tok20" );
header("Content-type: application/json; charset=utf-8");

$nowserver = date("Y-m-d H:i:s");
$action = $_POST["action"];
SIMWebServiceToken::liberar_token();

if($action!="gettoken"){
	
	$respuesta = SIMWebServiceToken::comprobar_token($_POST["Token"]);
}

switch( $action ){
	case "gettoken":		
		$Usuario = $_POST["Usuario"];
		$Clave = $_POST["Clave"];		
		//$respuesta = SIMWebServiceToken::get_token($Usuario,$Clave);
		die( json_encode( array(  'success' => $respuesta["success"], 'message'=>$respuesta["message"], 'response' => $respuesta["response"], 'date' => $nowserver ) ) );
		exit;
	break;

	case "gettalla":
		require "lib/SIMWebService.inc.php";
		//$respuesta = SIMWebService::get_talla();
		////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getsubmodulo','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "gettiporeferencia":
		require "lib/SIMWebService.inc.php";
		//$respuesta = SIMWebService::get_tipo_referencia();
		////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getsubmodulo','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getcolor":		
		require "lib/SIMWebService.inc.php";
		//$respuesta = SIMWebService::get_color();
		////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getsubmodulo','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getbono":		
		require "lib/SIMWebService.inc.php";
		$Documento = $_POST["Documento"];
		//$respuesta = SIMWebService::get_bono($Documento);
		////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getsubmodulo','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "getproducto":		
		require "lib/SIMWebService.inc.php";
		$Referencia = $_POST["Referencia"];
		$Pagina = $_POST["Pagina"];
		$CantidadPorPagina = $_POST["CantidadPorPagina"];
		//$respuesta = SIMWebService::get_producto($Referencia,$Pagina,$CantidadPorPagina);
		////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getsubmodulo','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

	case "setpedido":		
		require "lib/SIMWebService.inc.php";
		$NumeroPedido = $_POST["NumeroPedido"];
		$CedulaCliente = $_POST["CedulaCliente"];
		$NombreCliente = $_POST["NombreCliente"];
		$Valor=$_POST["Valor"];
		$FechaPedido=$_POST["FechaPedido"];
		$Referencias = $_POST["Referencias"]; 
		$Bonos = $_POST["Bonos"]; 
		//$respuesta = SIMWebService::set_pedido($NumeroPedido,$CedulaCliente,$NombreCliente,$Valor,$FechaPedido,$Referencias,$Bonos);
		////$sql_log_servicio = $dbo->query("Insert Into LogServicioDiario (IDSocio,Servicio, Parametros, Respuesta) Values ('".$IDSocio."','getsubmodulo','".json_encode($_GET)."','".json_encode($respuesta)."')");
		die( json_encode( array(  'success' => $respuesta[success], 'message'=>$respuesta[message], 'response' => $respuesta[response], 'date' => $nowserver ) ) );
		exit;
	break;

}	



?>
