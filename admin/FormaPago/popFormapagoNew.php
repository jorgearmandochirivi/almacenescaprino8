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
	
	//Verificar si vienen puntos de fidelización y cumple con el valor
	foreach( $frm[ValorFidelizacion] as $key_fid => $valor_fid )
	{
		$Valor = $Valor + $valor_fid;
	}//end for
	
	
		
	if( round($Valor) == round($frm['ValorTotal']) )
	{
		
		db_query("SET AUTOCOMMIT=0");
		db_query("BEGIN");
				
		foreach( $frm['IDPuntoVentaBanco'] as $FormaPago )
		{
			
			if( $frm[IDFormaPago][$FormaPago] <> 17 ) //Hacer lo de siempre
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
					$valorcuota = $frm['ValorTotal'] / $numerocuotas ;
					
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
			
			
			
			}//end if forma de pago es fidelizacion
			else //PROCESO DE FIDELIZACION
			{
				//HAcer iteraciioin por cada ValorFidelizacion
				
				foreach( $frm[ValorFidelizacion] as $key_fid => $valor_fid )
				{
				
					$IDFormaPagoFactura = get_maxID( "FormaPagoFactura","IDFormaPagoFactura" );
				
					$Valor = $valor_fid;
					$IDFPago = $frm[IDFormaPago][$FormaPago];
					$Banco = $frm['IDBanco'][$FormaPago];

					$sql_insertar_formapago  = "INSERT INTO FormaPagoFactura (IDFormaPagoFactura,IDFactura,IDFormaPago,Valor,IDPuntoVenta,Comision,IDBanco) ";
					$sql_insertar_formapago .= "VALUES ('$IDFormaPagoFactura', '$frm[id]', '$IDFPago', '$Valor','$frm[idpunto]','$Comision','$Banco')";
					db_query( $sql_insertar_formapago );
					
					//redimir puntos
					redimir_fid( $frm["IDCliente"], 5 );
					
			
				}//end for
				
			}//end else	
			
			
		
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
              list($dia,$mes,$año)=explode("/", $fecha);
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
              list($dia,$mes,$año)=explode("-", $fecha);
        $nueva = mktime(0,0,0, $mes,$dia,$año) + $ndias * 24 * 60 * 60;
        $nuevafecha=date("d-m-Y",$nueva);
      return ($nuevafecha);  
}

/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($newmode,$submit_caption) {

	GLOBAL $TitleMod,$Table,$Key,$id, $IDPuntoVenta,$idpunto;
	$qid = db_query(" SELECT * FROM $Table WHERE IDPuntoVenta = '$IDPuntoVenta' ORDER BY IDFormaPago ");
	
	$valor = get_field("Factura","ValorTotal","IDFactura",$id."' AND IDPuntoVenta = '$IDPuntoVenta");
	$cliente = get_field("Factura","IDCliente","IDFactura",$id."' AND IDPuntoVenta = '$IDPuntoVenta");

?>
<script>
var Check = new Array('Nombre','Publicar');
</script>
		<br>
		<form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?> onSubmit="return EvaluaReg(this,Check)" <?php }?>>
	
<table cellpadding=1 cellspacing=0 class=bordertable align=center >
	<tr>
		<td class=navpic bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo "No. ".$id."  Valor: $".number_format(round($valor)); ?></td>
	</tr>
	<tr>
	<td>
		<table width=467 border=0 cellspacing=1 cellpadding=1 class=texto>
			<tr class=row2>
				<td width="23%" class=titulodetablas align=center>Forma de Pago</td>
								<td width="37%" class=titulodetablas align=center>Valor</td>
							</tr>	
		<?php
			while( $r = db_fetch_object($qid) )
			{
				if( $r->IDFormaPago <> 17 ) //Puntos Fidelizacion
				{
		?>
							
			<tr >
				<td width="23%" class=col1>
					<div align="center">
						<?php echo get_field("FormaPago","Descripcion","IDFormaPago",$r->IDFormaPago)?></div>
				</td>
								<td width="37%" class=col2>
					<div align="center">
						<input type="text" size="15" class="input" name="Valor[<?php echo $r->IDPuntoVentaBanco?>]" value="">
						<input type="hidden" name="IDFormaPago[<?php echo $r->IDPuntoVentaBanco?>]" value="<?php echo $r->IDFormaPago?>">
						<input type="hidden" name="IDPuntoVentaBanco[<?php echo $r->IDPuntoVentaBanco?>]" value="<?php echo $r->IDPuntoVentaBanco?>">
						<input type="hidden" name="Comision[<?php echo $r->IDPuntoVentaBanco?>]" value="<?php echo $r->Comision?>">
						<input type="hidden" name="IDBanco[<?php echo $r->IDPuntoVentaBanco?>]" value="<?php echo $r->IDBanco?>"></div>
				</td>
							</tr>
		<?php
				}//end if
				else //TODA LA COSA PARA EL PLAN DE FIDELIZACION
				{
		?>
			
					<tr >
						<td width="23%" class=col1 valign="top">
							<div align="center">
								<?php echo get_field("FormaPago","Descripcion","IDFormaPago",$r->IDFormaPago)?>
							</div>
						</td>
						<td width="37%" class=col2>
							<div align="center">
								<table width="100%">
									<tr>
										<th>Puntos</th>
										<th>Puntos Requeridos</th>
										<th>Descuento</th>
										<th>Redimir</th>
									</tr>
									<?php
									$frm_cliente["IDCliente"] = $cliente;
									$array_puntos = getpuntos_fid($frm_cliente);
									foreach( $array_puntos as $idparam => $value )
									{
										if( $value["Redimido"] <> "Si" && $value["Puntos"] == $value["Cantidad"] )
										{
									?>
										<tr>
											<td><?php echo $value["Puntos"] ?></td>
											<td><?php echo $value["Cantidad"] ?></td>
											<td><?php echo number_format( $value["ValorDescuento"] ) ?></td>
											<td>
													<input type="checkbox" value="<?php echo $value["ValorDescuento"] ?>" name="ValorFidelizacion[]">
													
													
													
											</td>
										</tr>
									<?php
										}//end if
									}//end for
									
									?>
								
								</table>
								<input type="hidden" name="IDFormaPago[<?php echo $r->IDPuntoVentaBanco?>]" value="<?php echo $r->IDFormaPago?>">
								<input type="hidden" name="IDPuntoVentaBanco[<?php echo $r->IDPuntoVentaBanco?>]" value="<?php echo $r->IDPuntoVentaBanco?>">
								<input type="hidden" name="Comision[<?php echo $r->IDPuntoVentaBanco?>]" value="<?php echo $r->Comision?>">
								<input type="hidden" name="IDBanco[<?php echo $r->IDPuntoVentaBanco?>]" value="<?php echo $r->IDBanco?>">
							</div>
						</td>
					</tr>
		<?php			
				}//end else
			}//end while( $r = db_fetch_object($qid) )
		?>
			<tr class=col1list>
				<td class=rowtable align=center colspan="2">
					<input type="submit" name="Submit" value="<?php echo $submit_caption?>">
					<input type="hidden" name="action" value="<?php echo $newmode?>">
					<input type="hidden" name="ValorTotal" value="<?php echo round($valor)?>">
					<input type="hidden" name="id" value="<?php echo $id?>">
					<input type="hidden" name="IDCliente" value="<?php echo $cliente ?>">
					<input type="hidden" name="idpunto" value="<?php echo $idpunto?>">
					<input type="hidden" name="FechaFactura" value="<?php echo substr(get_field("Factura","FechaFactura","IDfactura",$id."' AND IDPuntoVenta = '$IDPuntoVenta"),0,10);?>">
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
<?php
}// End function print_form()
?>
</body>
</html>