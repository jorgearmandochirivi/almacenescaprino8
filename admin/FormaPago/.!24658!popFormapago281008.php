<?php
	include("../admin/config.inc.php");
	Encabezado();
	$datos = Verifica_SesionCliente();
	$IDPuntoVenta = $datos['IDPuntoVenta'];
  ?>
<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
		<meta name="generator" content="Adobe GoLive 6">
		<title>Caprino :: Forma de Pago</title>
		<link rel="stylesheet" href="../styles.css?1" type="text/css">
	</head>

	<body bgcolor="#ffffff" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">
<?php

$TitleMod ="Forma de Pago Factura";

$Table = "PuntoVentaBanco";
$TableJoin = "";
$Key = "IDPuntoVentaBanco";


		switch (nvl($action)) {
			case "insert" :
				if(insertarformapago($_POST))
					echo "<script>window.open( '../Factura/FImpresion.php?id=".$id."&idpunto=".$idpunto."','','width=426, height=350' );opener.location.reload(true);window.close();</script>";
			break;
			default : 
				print_form("insert","Realizar Pago");
			break;
		
		} // End switch

/*******************************************************************************************
	insertarformapago: Borra un Item ( Referencia del Pedido ).
	Parametros:
			$frm : array con las formas de pago de la factura
	Retorna:	
			void
*******************************************************************************************/
function insertarformapago($frm)
{
	GLOBAL $Table, $Key, $ID_Usuario, $idpunto,$datos, $numerocuotas, $diascuota; 
	

	
	
	foreach( $frm['IDPuntoVentaBanco'] as $FormaPago )
	{
		
		$FormaPago; 
		$Valor = $Valor + $frm[Valor][$FormaPago];
	
	}//end foreach( $frm['IDFormaPago'] as $FormaPago )
	
	//exit;
		
	if( round($Valor) == round($frm['ValorTotal']) )
	{
		
		db_query("SET AUTOCOMMIT=0");
		db_query("BEGIN");
				
		foreach( $frm['IDPuntoVentaBanco'] as $FormaPago )
		{
			if(!empty( $frm[Valor][$FormaPago] ))
			{
				$IDFormaPagoFactura = get_maxID( "FormaPagoFactura","IDFormaPagoFactura" );
				
				$Valor = $frm[Valor][$FormaPago];
				$DocSoporte = $frm[DocSoporte][$FormaPago];
				$IDFPago = $frm[IDFormaPago][$FormaPago];
								
				//Query Comision Banco
				$sql_banco = "SELECT * FROM NovedadBanco WHERE IDPuntoVentaBanco = '$FormaPago' AND Fecha = '$frm[FechaFactura]'";
				$query_banco = db_query($sql_banco);
				$Comision = $frm['Comision'][$FormaPago];
				$Banco = $frm['IDBanco'][$FormaPago];
				
				$sql_insertar_formapago  = "INSERT INTO FormaPagoFactura (IDFormaPagoFactura,IDFactura,IDFormaPago,Valor,IDPuntoVenta,Comision,IDBanco) ";
				$sql_insertar_formapago .= "VALUES ('$IDFormaPagoFactura', '$frm[id]', '$IDFPago', '$Valor','$frm[idpunto]','$Comision','$Banco')";
				db_query( $sql_insertar_formapago );
			
				//insertar el log
				//insertlog($ID_Usuario,"FormaPagoFactura",$IDFormaPagoFactura,"Insertar",$sql_insertar_formapago);
			
				//Actualizar Novedad Banco
				$frm[FechaFactura] = substr($frm[FechaFactura],0,10);
				
				if(db_num_rows($query_banco) == 0)
				{
					$idnovedad = get_maxID( "NovedadBanco","IDNovedadBanco" );
					$sql_insert = "INSERT INTO NovedadBanco (IDNovedadBanco, IDPuntoVentaBanco, Fecha, Valor, Comision) VALUES('$idnovedad','$FormaPago','$frm[FechaFactura]','$Valor','$Comision') ";
					db_query($sql_insert);
				}
				else
				{
					$r_novedad = db_fetch_object($query_banco);
					$ValorActualizar = $Valor + $r_novedad->Valor;
					$sql_update = "UPDATE NovedadBanco SET Valor = '$ValorActualizar' WHERE IDPuntoVentaBanco = '$FormaPago' AND Fecha = '$frm[FechaFactura]' ";
					db_query($sql_update);
				}
				
				
				//VERIFICAR SI ES CREDITO
				if( $IDFPago == 13 )
				{
					//Seleccionar Factura
					$sql_factura = " SELECT NumeroFactura, IDCliente FROM Factura WHERE IDFactura = '$frm[id]' AND IDPuntoVenta = '$frm[idpunto]' ";
					$qry_factura = db_query( $sql_factura );
					$r_factura = db_fetch_object( $qry_factura );
					
					$NoDocumento = get_maxID( " Credito WHERE IDPuntoVenta = '$frm[idpunto]' ","NumeroDocumento" );
					$sql_credito = " INSERT INTO Credito ( IDFactura, IDCliente, NumeroDocumento, NumeroFactura, IDPuntoVenta,
										FechaFactura,  ValorTotal, UsuarioTrCr, FechaTrCr ) VALUES ( '$frm[id]','$r_factura->IDCliente',
										'$NoDocumento','$r_factura->NumeroFactura','$frm[idpunto]','$frm[FechaFactura]','$Valor','$datos[IDUsuario]',
										NOW() ) ";
					$qry_credito = db_query( $sql_credito );
					
					$fechacuota = $frm[FechaFactura];
					$valorcuota = $frm[ValorTotal] / $numerocuotas ;
					
					$valorcuota = floor($valorcuota);
					//$valorcuota = (int)$valorcuota;
					
					//Hacer las cuotas
					for( $j = 0; $j < $numerocuotas; $j++ )
						if( $fechacuota <> $frm[FechaFactura] )
						{
						
						    $ITEM = get_maxID( " CreditoCuota WHERE IDFactura = '$frm[id]' ","IDCuota" );
						   	$sql_cuota = " INSERT INTO CreditoCuota ( IDFactura, IDCuota, NumeroFactura, IDPuntoVenta, FechaCuota,
						    				FechaPago, ValorTotal, UsuarioTrCr, FechaTrCr ) VALUES ( '$frm[id]','$ITEM',
						    				'$r_factura->NumeroFactura','$frm[idpunto]','$fechacuota','','$valorcuota','$datos[IDUsuario]',
											NOW()  ) ";
							$qry_cuota = db_query( $sql_cuota );
							
							$fechacuota = explode("-", $fechacuota );
							$anonow = $fechacuota[0];
							$mesnow = $fechacuota[1];
							$dianow = $fechacuota[2];
						
							$Fecha_tope = new Fecha();
							$Fecha_tope->Fecha($anonow,$mesnow, $dianow) ;
						    $Fecha_tope -> SumaTiempo(date( "Y-m-d" ), 0, 0, $diascuota);
						    $fechacuota = $Fecha_tope -> getFecha();
						
						}//end if
						else
						{
							$fechacuota = explode("-", $fechacuota );
							$anonow = $fechacuota[0];
							$mesnow = $fechacuota[1];
							$dianow = $fechacuota[2];
						
							$Fecha_tope = new Fecha();
							$Fecha_tope->Fecha($anonow,$mesnow, $dianow) ;
						    $Fecha_tope -> SumaTiempo(date( "Y-m-d" ), 0, 0, $diascuota);
						    $fechacuota = $Fecha_tope -> getFecha();
						}//end if
					
				}//end if	
			
				
				
			}//end if
		
		}//end foreach( $frm['IDFormaPago'] as $FormaPago )
		//db_query( "asadasd" );
		db_query("COMMIT");
		return true;
		
	}//end if($Valor == $frm['Valor']);
	else
	{
		window_alert("El valor debe ser igual al monto de la factura");
		print_form("insert","Realizar Pago");
		return false;
	}

}// end function insertarformapago($frm)

class Fecha {
          var $fecha;

          function Fecha($a = 0, $m = 0, $d = 0) {
               If ($a==0) $a = Date("Y");
               If ($m==0) $m = Date("m");
               if ($d==0) $d = Date("d");
               $this -> fecha = Date("Y-m-d", mktime(0,0,0,$m,$d,$a));
          }

          function SumaTiempo($fechainicial, $a = 0, $m = 0, $d = 0) {
               $array_date = explode("-", $this->fecha);
               $this->fecha = Date("Y-m-d", mktime(0, 0, 0, $array_date[1] + $m, $array_date[2] + $d, $array_date[0] + $a));
          }

          function getFecha() { return $this->fecha; }
     }

     

function suma_fechas($fecha,$ndias)
{
      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
