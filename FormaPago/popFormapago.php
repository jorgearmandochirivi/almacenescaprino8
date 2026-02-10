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
		<script language="JavaScript1.2" src="../jscripts/jquery-1.3.2.min.js?<?=rand(1,100)?>"></script>
		<script language="JavaScript1.2" src="../jscripts/common.js?<?=rand(1,100)?>"></script>



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

			case "buscarbono" :
				$msg_busca_bono="<br>Bono no existe";
				//Busco el id del cliente
				$id_cliente=get_field("Cliente","IDCliente","Cedula",$_GET['BuscarCedula']);
				//Busco el bono del tercero si existe el cliente
				if (!empty($id_cliente)){
					$array_bonos=explode(",",$_GET['BuscarNumero']);
					if(count($array_bonos)>0){
						foreach($array_bonos as $numero_bono_buscar){
							$condicion_numero_bono[]="'".(int)trim($numero_bono_buscar)."'";
						}
					}
					$numeros_buscar=implode(",",$condicion_numero_bono);


					//$sql_bono_tercero =  "SELECT * FROM BonoFidelizacion WHERE IDBonoFidelizacion = '" . $_GET['BuscarNumero'] . "' AND IDCliente = '".$id_cliente."' AND FechaVencimiento >= CURDATE() AND Estado = 'D' ORDER BY Fecha DESC ";
					$sql_bono_tercero =  "SELECT * FROM BonoFidelizacion WHERE IDBonoFidelizacion in (".$numeros_buscar.") AND IDCliente = '".$id_cliente."' AND FechaVencimiento >= CURDATE() AND Estado = 'D' ORDER BY Fecha DESC ";
					$query_bono_tercero=db_query($sql_bono_tercero);
					while($row_bono_tercero=db_fetch_array($query_bono_tercero)){
						$bono_tercero_encontrado=1;
						$id_cliente_bono_pertenece=$id_cliente;
						$id_bono_tercero[]=$row_bono_tercero['IDBonoFidelizacion'];
						$msg_busca_bono="<br>Bono encontrado y disponible para forma de pago";
					}
				}
				else{
					$msg_busca_bono="<br>Bono no existe";
				}
				print_form("insert","Realizar Pago");
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

	$Valor = 0; // Inicializar variable antes de su uso

	if (!empty($frm['IDPuntoVentaBanco']) && is_array($frm['IDPuntoVentaBanco'])) {
		foreach( $frm['IDPuntoVentaBanco'] as $FormaPago )
		{

			$FormaPago;
			$Valor = $Valor + (float)($frm['Valor'][$FormaPago] ?? 0);

		}//end foreach( $frm['IDFormaPago'] as $FormaPago )
	}


	//Verificar si vienen puntos de fidelizaci�n y cumple con el valor


	if (!empty($frm['IDBono']) && is_array($frm['IDBono'])) {
		foreach( $frm['IDBono'] as $key_fid => $valor_fid )
		{
			//consulto valor bono
			$valor_bono=get_field("BonoFidelizacion","Valor","IDBonoFidelizacion",$valor_fid);
			$Valor = $Valor + (float)($valor_bono ?? 0);
		}//end for
	}


	if (!empty($frm['ValorFidelizacion']) && is_array($frm['ValorFidelizacion'])) {
		foreach( $frm['ValorFidelizacion'] as $key_fid => $valor_fid )
		{
			$Valor = $Valor + (float)($valor_fid ?? 0);
		}//end for
	}



	if( round($Valor) == round($frm['ValorTotal']) )
	{

		db_query("SET AUTOCOMMIT=0");
		db_query("BEGIN");

		foreach( $frm['IDPuntoVentaBanco'] as $FormaPago )
		{

			if( $frm['IDFormaPago'][$FormaPago] <> 17 ) //Hacer lo de siempre
			{


			if(!empty( $frm['Valor'][$FormaPago] ))
			{
				$IDFormaPagoFactura = get_maxID( "FormaPagoFactura","IDFormaPagoFactura" );

				$Valor = $frm['Valor'][$FormaPago];
				$DocSoporte = $frm['DocSoporte'][$FormaPago];
				$IDFPago = $frm['IDFormaPago'][$FormaPago];

				//Query Comision Banco
				$sql_banco = "SELECT * FROM NovedadBanco WHERE IDPuntoVentaBanco = '$FormaPago' AND Fecha = '{$frm['FechaFactura']}'";
				$query_banco = db_query($sql_banco);
				$Comision = $frm['Comision'][$FormaPago];
				$Banco = $frm['IDBanco'][$FormaPago];
				 $CodigoTarjeta = $frm['CodigoTargeta'][$FormaPago];



				$sql_insertar_formapago  = "INSERT INTO FormaPagoFactura (IDFormaPagoFactura,IDFactura,IDFormaPago,Valor,IDPuntoVenta,Comision,IDBanco, CodigoTarjeta) ";
				$sql_insertar_formapago .= "VALUES ('$IDFormaPagoFactura', '{$frm['id']}', '$IDFPago', '$Valor','{$frm['idpunto']}','$Comision','$Banco','$CodigoTarjeta')";
				db_query( $sql_insertar_formapago );

				//insertar el log
				//insertlog($ID_Usuario,"FormaPagoFactura",$IDFormaPagoFactura,"Insertar",$sql_insertar_formapago);

				//Actualizar Novedad Banco
				$frm['FechaFactura'] = substr($frm['FechaFactura'],0,10);

				if(db_num_rows($query_banco) == 0)
				{
					$idnovedad = get_maxID( "NovedadBanco","IDNovedadBanco" );
					$sql_insert = "INSERT INTO NovedadBanco (IDNovedadBanco, IDPuntoVentaBanco, Fecha, Valor, Comision) VALUES('$idnovedad','$FormaPago','{$frm['FechaFactura']}','$Valor','$Comision') ";
					db_query($sql_insert);
				}
				else
				{
					$r_novedad = db_fetch_object($query_banco);
					$ValorActualizar = $Valor + $r_novedad->Valor;
					$sql_update = "UPDATE NovedadBanco SET Valor = '$ValorActualizar' WHERE IDPuntoVentaBanco = '$FormaPago' AND Fecha = '{$frm['FechaFactura']}' ";
					db_query($sql_update);
				}


				//VERIFICAR SI ES CREDITO
				if( $IDFPago == 13 )
				{
					//Seleccionar Factura
					$sql_factura = " SELECT NumeroFactura, IDCliente FROM Factura WHERE IDFactura = '{$frm['id']}' AND IDPuntoVenta = '{$frm['idpunto']}' ";
					$qry_factura = db_query( $sql_factura );
					$r_factura = db_fetch_object( $qry_factura );

					$NoDocumento = get_maxID( " Credito WHERE IDPuntoVenta = '{$frm['idpunto']}' ","NumeroDocumento" );
					$sql_credito = " INSERT INTO Credito ( IDFactura, IDCliente, NumeroDocumento, NumeroFactura, IDPuntoVenta,
										FechaFactura,  ValorTotal, UsuarioTrCr, FechaTrCr ) VALUES ( '{$frm['id']}','$r_factura->IDCliente',
										'$NoDocumento','$r_factura->NumeroFactura','{$frm['idpunto']}','{$frm['FechaFactura']}','$Valor','{$datos['IDUsuario']}',
										NOW() ) ";
					$qry_credito = db_query( $sql_credito );

					$fechacuota = $frm['FechaFactura'];
					$valorcuota = $frm['ValorTotal'] / $numerocuotas ;

					$valorcuota = floor($valorcuota);
					$valorcuota = ceil($valorcuota/50)*50;
					//$valorcuota = (int)$valorcuota;

					//Hacer las cuotas
					for( $j = 0; $j < $numerocuotas; $j++ )
						if( $fechacuota <> $frm['FechaFactura'] )
						{

						    $ITEM = get_maxID( " CreditoCuota WHERE IDFactura = '{$frm['id']}' ","IDCuota" );
						   	$sql_cuota = " INSERT INTO CreditoCuota ( IDFactura, IDCuota, NumeroFactura, IDPuntoVenta, FechaCuota,
						    				FechaPago, ValorTotal, UsuarioTrCr, FechaTrCr ) VALUES ( '{$frm['id']}','$ITEM',
						    				'$r_factura->NumeroFactura','{$frm['idpunto']}','$fechacuota','','$valorcuota','{$datos['IDUsuario']}',
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
				foreach( $frm['IDBono'] as $key_fid => $valor_fid )
				{


					$valor_bono=get_field("BonoFidelizacion","Valor","IDBonoFidelizacion",$valor_fid);


					$IDFormaPagoFactura = get_maxID( "FormaPagoFactura","IDFormaPagoFactura" );

					$Valor = $valor_bono;
					$IDFPago = $frm['IDFormaPago'][$FormaPago];
					$Banco = $frm['IDBanco'][$FormaPago];

					$sql_insertar_formapago  = "INSERT INTO FormaPagoFactura (IDFormaPagoFactura,IDFactura,IDFormaPago,Valor,IDPuntoVenta,Comision,IDBanco) ";
					$sql_insertar_formapago .= "VALUES ('$IDFormaPagoFactura', '{$frm['id']}', '$IDFPago', '$Valor','{$frm['idpunto']}','$Comision','$Banco')";

					db_query( $sql_insertar_formapago );

					//envio notificacion de bono redimido
					envia_bono_redimido($frm["IDCliente"],$valor_fid,$frm['idpunto']);

					//redimir puntos
					//redimir_fid( $frm["IDCliente"], 5 );
					fid_redimir_bono( $frm["IDCliente"], $valor_fid,$frm['id'], $frm['idpunto'],$frm['IDClienteRedimioBono']  );
				}//end for

			}//end else



		}//end foreach( $frm['IDFormaPago'] as $FormaPago )
		//db_query( "asadasd" );


		//CALCULO PUNTOS COMPRA DE ACUERDO A LO PAGADO
		$fidelizado_club=get_field("Cliente","ClubSuavidad","IDCliente",$frm['IDCliente']);
		if ($fidelizado_club=="S")
			fid_calculapuntos( $frm );

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
              list($dia,$mes,$a�o)=explode("/", $fecha);
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
              list($dia,$mes,$a�o)=explode("-", $fecha);
        $nueva = mktime(0,0,0, $mes,$dia,$a�o) + $ndias * 24 * 60 * 60;
        $nuevafecha=date("d-m-Y",$nueva);
      return ($nuevafecha);
}

/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($newmode,$submit_caption) {

	GLOBAL $TitleMod,$Table,$Key,$id, $IDPuntoVenta,$idpunto,$msg_busca_bono,$bono_tercero_encontrado,$id_cliente_bono_pertenece,$id_bono_tercero;
	$qid = db_query(" SELECT PuntoVentaBanco.* FROM PuntoVentaBanco, FormaPago
						WHERE PuntoVentaBanco.IDPuntoVenta = '$IDPuntoVenta'
						AND PuntoVentaBanco.IDFormaPago = FormaPago.IDFormaPago
						AND FormaPago.Publicar = 'S'
						ORDER BY PuntoVentaBanco.IDFormaPago ");

	$valor = get_field("Factura","ValorTotal","IDFactura",$id."' AND IDPuntoVenta = '$IDPuntoVenta");
	$cliente = get_field("Factura","IDCliente","IDFactura",$id."' AND IDPuntoVenta = '$IDPuntoVenta");
	$descuento_fac = get_field("Factura","Descuento","IDFactura",$id."' AND IDPuntoVenta = '$IDPuntoVenta");

	// Si el decuento es del 6 es por que es un credito de medellin
	if($descuento_fac==6){
			$valorprimeracuota=(int)($valor/6);
			$valorprimeracuota=ceil($valorprimeracuota/50)*50;
			$valorsaldo=$valor-$valorprimeracuota;
			$valorsaldo=ceil($valorsaldo/50)*50;
			$valor = ceil($valor/50)*50;;
			$factura_credito="S";
	}

	//puntos cliente
	//traer datos del cliente
?>
<script>
var Check = new Array('Nombre','Publicar');
</script>
		<br>


<table cellpadding=1 cellspacing=0 class=bordertable align=center >
	<tr>
		<td class=navpic bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo "No. ".$id."  Valor: $".number_format(round($valor)); ?></td>
	</tr>
	<tr>
	<td>


    <!--
    <form name="frmBuscarBono" id="frmBuscarBono" method="get" action="popFormapago.php" enctype="multipart/form-data">
        <table border="0" width="100%">
              <tr >
                  <td class=col1 valign="top">Buscar Bono</td>
                  <td class=col2>
                    <input type="text" name="BuscarNumero" id="BuscarNumero" placeholder="Numero">
                    <input type="text" name="BuscarCedula" id="BuscarCedula" placeholder="Cedula Pertenece">
                    <input type="hidden" name="id" id="id" value="<?php echo $_GET['id'] ?>">
                    <input type="hidden" name="action" id="action" value="buscarbono">
                    <input type="hidden" name="idpunto" id="idpunto" value="<?php echo $_GET['idpunto'] ?>">
                    <input type="submit" name="BuscarBono" id="BuscarBono">
                    <?php echo $msg_busca_bono; ?>
                  </td>
              </tr>
        </table>
    </form>

    -->




    <form name="frm" id="frmFormaPago" action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?> onSubmit="return EvaluaReg(this,Check)" <?php }?>>

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
						<?=get_field("FormaPago","Descripcion","IDFormaPago",$r->IDFormaPago)?></div>
				</td>
								<td width="37%" class=col2>
					<div align="center">

						<?php
						// Si es un credito y la forma de pago es efectivo
						if($factura_credito=="S" && $r->IDFormaPago==1){
									$valor_pagar=$valorprimeracuota;
									$campo_activo="readonly";
						}
						elseif($factura_credito=="S" && $r->IDFormaPago==13){
									$valor_pagar=$valorsaldo;
						}
						else{
								$valor_pagar="";
								$campo_activo="";
						}

						if($factura_credito=="S")
							$campo_activo="readonly";
						else
							$campo_activo="";



							?>

						<input type="text" size="15" class="input" name="Valor[<?=$r->IDPuntoVentaBanco?>]" value="<?php echo $valor_pagar; ?>" <?php echo $campo_activo; ?>>
                        <?php
                        if( $r->IDFormaPago == "20" )
						{
						?>
                        	<input type="text" size="15" class="input" name="CodigoTargeta[<?=$r->IDPuntoVentaBanco?>]" value="" placeholder="Codigo Tarjeta">
                        <?php
						}//end if
						?>
						<input type="hidden" name="IDFormaPago[<?=$r->IDPuntoVentaBanco?>]" value="<?=$r->IDFormaPago?>">
						<input type="hidden" name="IDPuntoVentaBanco[<?=$r->IDPuntoVentaBanco?>]" value="<?=$r->IDPuntoVentaBanco?>">
						<input type="hidden" name="Comision[<?=$r->IDPuntoVentaBanco?>]" value="<?=$r->Comision?>">
						<input type="hidden" name="IDBanco[<?=$r->IDPuntoVentaBanco?>]" value="<?=$r->IDBanco?>"></div>
				</td>
							</tr>
		<?php
				}//end if
				elseif(1==2) //Se descativo la forma de pago por medio de pago se descuenta en la realizacion de la factura
				{


						$sql_cliente = " SELECT * FROM Cliente WHERE IDCliente = '" . $cliente . "'  ";
						$qry_cliente = db_query( $sql_cliente );
						$datos_cliente = db_fetch_array( $qry_cliente );


						if( $datos_cliente["ClubSuavidad"] == "S" || $bono_tercero_encontrado==1 )
						{

							$puntoscliente = fid_get_puntos( $cliente );



							//traer tabla de puntos
							$sql_tabla_puntos = " SELECT * FROM ValorPuntos WHERE PuntosNecesarios <= '" . $puntoscliente["puntostotal"] . "'  ORDER BY PuntosNecesarios DESC  LIMIT 1 ";
							$qry_tabla_puntos = db_query( $sql_tabla_puntos );
							if( db_num_rows( $qry_tabla_puntos ) >= 0 )
							{
								$r_tabla_puntos = db_fetch_array( $qry_tabla_puntos );


		?>

					<tr >
						<td width="23%" class=col1 valign="top">
							<div align="center">
								<?=get_field("FormaPago","Descripcion","IDFormaPago",$r->IDFormaPago)?>
							</div>
						</td>
						<td width="37%" class=col2>
							<div align="center">
								<table width="100%">
									<tr>
										<th>Redimir</th>
										<th>Numero Bono</th>
										<th>Valor</th>
									</tr>

                                    <?php

										//$sql_bono =  "SELECT * FROM BonoFidelizacion WHERE IDCliente = '" . $cliente . "' AND FechaVencimiento >= CURDATE() AND Fecha <> CURDATE() AND Estado = 'D' ORDER BY Fecha DESC ";
										// selecciono todos los bonos menos el de hoy

										//convierto el resultado a comas
										if (count($id_bono_tercero)>0){
											$id_bonos_buscar=implode(",",$id_bono_tercero);
										}

										if($bono_tercero_encontrado==1){ // si se busco un bono de un tercero solo traigo ese bono
											$condicion_bono = " AND IDCliente = '" . $id_cliente_bono_pertenece . "' AND IDBonoFidelizacion in (".$id_bonos_buscar.") ";
										}
										else{
											$condicion_bono = " AND IDCliente = '" . $cliente . "' ";
										}


										$sql_bono =  "SELECT * FROM BonoFidelizacion WHERE FechaVencimiento >= CURDATE() AND Estado = 'D' $condicion_bono ORDER BY Fecha DESC ";
										$query_bono=db_query($sql_bono);
										while($r_bono=db_fetch_array($query_bono)){ ?>

                                    <tr>
                                    	<td>
                                                <input type="checkbox" value="<?=$r_bono["IDBonoFidelizacion"] ?>" name="IDBono[]" >



                                        </td>
                                        <td>
                                       		<?php
                                            	echo $r_bono["IDBonoFidelizacion"];
											?>
                                       </td>
                                        <td>$
											<?php
                                            	echo number_format($r_bono["Valor"],"0",",",".");
											?>
                                      	</td>

                                    </tr>
                                  <?php } ?>

								</table>


                                <input type="hidden" name="IDFormaPago[<?=$r->IDPuntoVentaBanco?>]" value="<?=$r->IDFormaPago?>">
								<input type="hidden" name="IDPuntoVentaBanco[<?=$r->IDPuntoVentaBanco?>]" value="<?=$r->IDPuntoVentaBanco?>">
								<input type="hidden" name="Comision[<?=$r->IDPuntoVentaBanco?>]" value="<?=$r->Comision?>">
								<input type="hidden" name="IDBanco[<?=$r->IDPuntoVentaBanco?>]" value="<?=$r->IDBanco?>">
							</div>
						</td>
					</tr>
		<?php
							}//end if no hay puntos
						}//end if fidelizado
				}//end else
			}//end while( $r = db_fetch_object($qid) )
		?>


			<tr class=col1list>
				<td class=rowtable align=center colspan="2"><input type="submit" name="Submit" value="<?=$submit_caption?>" id="btn_enviarpago_fac">
					<input type="hidden" name="action" value="<?=$newmode?>">
					<input type="hidden" name="ValorTotal" value="<?=round($valor)?>">
					<input type="hidden" name="id" value="<?=$id?>">
					<input type="hidden" name="IDCliente" value="<?=$cliente ?>">
					<input type="hidden" name="idpunto" value="<?=$idpunto?>">
                    <input type="hidden" name="IDClienteRedimioBono" value="<?=$cliente ?>">
					<input type="hidden" name="FechaFactura" value="<?=substr(get_field("Factura","FechaFactura","IDfactura",$id."' AND IDPuntoVenta = '$IDPuntoVenta"),0,10);?>">
			  </td>
			</tr>
		</table>

		</form>


		</td>
	</tr>
</table>

<?php
}// End function print_form()
?>
</body>
</html>
