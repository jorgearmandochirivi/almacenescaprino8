<?php
	include("../config.inc.php");
	Encabezado();

    $sql_garantias = $_GET[sql];
	$now_date = date('m-d-Y H:i');


	/********************* TRAER DATOS DE VENTAS CON TARJETAS DE CREDITO Y DEBITO 'ID'S MAYOR QUE 2'*********************/

	if( !empty( $_GET["Cedula"] )  ){
			 $otra_condicion=" AND CL.Cedula = '".$_GET["Cedula"]."'";
	}

	if( !empty( $_GET[IDPuntoVenta] ) )
		$condicion = " C.IDPuntoVenta = '$_GET[IDPuntoVenta]' AND F.IDPuntoVenta = '" . $_GET[IDPuntoVenta] . "' AND ";

		$sql_facturas = " SELECT C.*,  DATE_FORMAT( C.FechaFactura,'%Y-%m-%d' ) as FechaFacturaF, F.NumeroPagare, F.ComentarioCredito, F.FechaUltimaGestion, F.FechaCartaNotificacion, F.FechaReporteCredito, F.FechaUtimoComentario, F.IDFactura
							FROM Credito C, Factura F, Cliente CL
							WHERE $condicion C.IDFactura = F.IDFactura AND C.IDPuntoVenta = F.IDPuntoVenta AND CL.IDCliente=F.IDCliente AND C.FechaFactura BETWEEN '$FechaDesde' AND '$FechaHasta' $otra_condicion
							ORDER BY FechaFactura DESC, IDPuntoVenta";

	$qry_facturas = db_query( $sql_facturas );

	//Puntos de Venta
	$sql_puntos = " SELECT IDPuntoVenta, Nombre FROM PuntoVenta ";
	$qry_puntos = db_query( $sql_puntos );
	while( $r_puntos = db_fetch_array( $qry_puntos ) )
		$array_puntos[ $r_puntos[ IDPuntoVenta ] ] = $r_puntos[ Nombre ];



	$title = "Datos Reporte Creditos Fecha $now_date";
	$file_type = "vnd.ms-excel";
	$file_ending = "xls";


	header("Pragma: ");
	header("Cache-Control: ");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Content-Type: application/$file_type; charset=ISO-8859-1");
	header("Content-Disposition: attachment; filename=$title.$file_ending");

	echo "CREDITOS ".get_field( "PuntoVenta","Nombre","IDPuntoVenta",$IDPuntoVenta )." ".$FechaDesde." - ".$FechaHasta . "\n";
	//define separator (defines columns in excel & tabs in word)
	$sep = "\t"; //tabbed character
	$ponerdetalle = "";
	print("\n");
	//end of printing column names
	//Poner los nombres de las columnas

		echo "Fecha" . $sep;
		echo "# Cdito" . $sep;
		echo "# Pagare" . $sep;
		echo "Cedula" . $sep;
		echo "Cliente" . $sep;
		echo "Pto de vta" . $sep;
		echo "Nro Fta" . $sep;
		echo "Vr Total" . $sep;
		echo "C. Abona" . $sep;
		echo "C. Pdtes" . $sep;
		echo "Val. Pdte" . $sep;
		echo "Fecha Prox Pago" . $sep;
		echo "Fecha Ult Gestion" . $sep;
		echo "Fecha Carta Notif" . $sep;
		echo "Fecha Reportado" . $sep;
		echo "Estado" . $sep;
		echo "# Cuotas" . $sep;
		echo "Vr Castigado" . $sep;
		echo "Comentario" . $sep;
		echo "Fecha Comentario" . $sep;


		print("\n");
	//start while loop to get data
		while( $r_facturas = db_fetch_array( $qry_facturas ) )
		{
			$class = repetition()?"row2":"row1";

			$cliente = array( );
			$coutas = array( );
			$candeladas = 0;
			$mostrar = 0;
			$fechaproximo = "";
			$pendientes = 0;
			$cartera_castigada = 0;
			$mostrar_cartera = 0;
			$valor_cartera = 0;

			//SELECT CLIENTE
			$sql_cliente = "SELECT IDCliente, Cedula, Nombre, Apellido FROM Cliente WHERE IDCliente = '$r_facturas[IDCliente]'";
			$qry_cliente = db_query( $sql_cliente );
			$cliente = db_fetch_array( $qry_cliente );
			//SELECT CUOTAS
			$sql_cuotas = " SELECT * FROM CreditoCuota WHERE IDFactura = '$r_facturas[IDFactura]' AND IDPuntoVenta = '$r_facturas[IDPuntoVenta]' and FechaPago <= '".$FechaHasta."'  ORDER BY FechaCuota ";			
			$qry_cuotas = db_query( $sql_cuotas );
			while( $r_cuotas = db_fetch_array($qry_cuotas) )
			{
				$ValorCuotaPago = $r_cuotas[ "ValorTotal" ];
				$cuotas[ $r_cuotas[IDCuota] ] = $r_cuotas;
				if( $r_cuotas[ FechaPago ] <> "0000-00-00 00:00:00" )
				{
					$candeladas++;
				}//end if
				elseif( $mostrar == 0 )
				{
					$fechaproximo = $r_cuotas[ FechaCuota ];
					$mostrar = 1;
				}//end end else

				//Calcular Cartera
				if( !empty($r_cuotas[ Estado ])  )
				{
					$cartera_castigada++;

					$valor_cartera += $r_cuotas[ ValorTotal ];
					$mostrar_cartera = 1;
				}//end if

			}//end while

			$cuotas_pendientes=db_num_rows( $qry_cuotas ) - $candeladas;
			$alerta_cuota_vencida=0;
			if( date( "Y-m-d" ) >= $fechaproximo && $cuotas_pendientes > 0  ):
				$alerta_cuota_vencida=1;
			endif;

			$EsCastiga="";
			$EsPagado="";
			$EsVencida="";
			$EsAlDia="";

			if($mostrar_cartera == 1):
				$EsCastiga="S";
			elseif($cuotas_pendientes==0):
				$EsPagado="S";
			elseif($alerta_cuota_vencida==1):
				$EsVencida="S";
			else:
				$EsAlDia="S";
			endif;


			$mostrar_fila="S";
			if(!empty($_GET["Estado"])){
				switch ($_GET["Estado"]) {
					case 'AlDia':
						if($EsAlDia=="S")
							$mostrar_fila="S";
						else
							$mostrar_fila="N";
					break;
					case 'Vencida':
						if($EsVencida=="S")
							$mostrar_fila="S";
						else
							$mostrar_fila="N";
					break;
					case 'Castigada':
						if($EsCastiga=="S")
							$mostrar_fila="S";
						else
							$mostrar_fila="N";
					break;
					case 'Pagado':
						if($EsPagado=="S")
							$mostrar_fila="S";
						else
							$mostrar_fila="N";
					break;
					default:
						$mostrar_fila="S";
						break;
				}
			}

			if($mostrar_fila=="S"){

				echo $r_facturas[FechaFacturaF]. $sep;
				echo $r_facturas[NumeroDocumento]. $sep;
				echo $r_facturas[NumeroPagare]. $sep;
				echo $cliente[Cedula]. $sep;
				echo $cliente[Nombre]." ".$cliente[Apellido] . $sep;
				echo $array_puntos[$r_facturas[IDPuntoVenta]]. $sep;
				echo $r_facturas[NumeroFactura]. $sep;


				echo number_format($r_facturas[ValorTotal],'0',',','.'). $sep;
				$tValorTotal += $r_facturas[ValorTotal];
				echo $candeladas. $sep;

				echo $pendientes = db_num_rows( $qry_cuotas ) - $candeladas. $sep;
				$faltante_cuotas=$ValorCuotaPago*$pendientes;

				echo number_format($faltante_cuotas,'0',',','.'). $sep;

							$alerta_cuota_vencida=0;
							if( date( "Y-m-d" ) >= $fechaproximo && $pendientes > 0  ):
								$alerta_cuota_vencida=1;
							endif;
							echo substr($fechaproximo,0,10). $sep;

							echo $r_facturas[FechaUltimaGestion]. $sep;

							echo $r_facturas[FechaCartaNotificacion]. $sep;
							echo $r_facturas[FechaReporteCredito]. $sep;



							if($mostrar_cartera == 1):
								echo 'C Castigada'.$sep;
								$valor_total_cartera += $r_facturas[ ValorTotal ];
							elseif($pendientes==0):
								echo "Pagado".$sep;
							elseif($alerta_cuota_vencida==1):
								echo 'Vencida'.$sep;
								$valor_total_cartera += $r_facturas[ ValorTotal ];
							else:
								echo "Al dia".$sep;
							endif;


							if($mostrar_cartera == 1):
								echo $cartera_castigada .$sep;
							else:
									echo " ".$sep;
							endif;

						echo number_format($valor_cartera,'0',',','.'). $sep;


						$comentarios= strip_tags($r_facturas[ComentarioCredito]);
						$comentarios=str_replace("<br>"," ", $comentarios);
						$comentarios = preg_replace("/[\r\n|\n|\r]+/", " ", $comentarios);
						echo $comentarios . $sep;


						if($r_facturas[FechaUtimoComentario]!="0000-00-00")
							echo $r_facturas[FechaUtimoComentario];




			print "\n";
		}

		}

		exit;

?>
