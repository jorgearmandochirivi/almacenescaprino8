<?php

/*******************************************************************************************
	Libreria de funciones para el Sistema de Puntos de Venta de Caprino - Sistema de Fidelizaciona
	Creador por: John Escobar
	Iniciado: Septiembre /2011
*******************************************************************************************/

/*******************************************************************************************
	actualiza_puntos_fid: realiza la actualización de puntos del cliente según la venta
	Parametros:
			$frm: array con los datos de venta
	Retorna:	
			Void
*******************************************************************************************/
function actualiza_puntos_fid( $frm )
{
	Global $Nombre_Usuario, $ID_Usuario, $IDPuntoVenta, $valorxpunto;
	if( empty( $valorxpunto ) )
		$valorxpunto = 100000;
	
	//Verificar que el usuario está fidelizado FidClienteRespuesta
	$sql_verifica = " SELECT * FROM FidClienteRespuesta WHERE  IDCliente = '" . $frm["IDCliente"] . "'  ";
	$qry_verifica = db_query( $sql_verifica );
	if( db_num_rows( $qry_verifica ) > 0 )
	{
	
		//Ve cuantos puntos son por la compra
		$puntos = $frm[ValorTotal] / $valorxpunto;

		
		//Verificar si el cliente tiene tarjeton vigente
		$sql_tarjeton = "SELECT * FROM ClienteTarjeton WHERE IDCliente = '" . $frm["IDCliente"] . "' AND FechaHasta > CURDATE() AND Vigente = 'S' ";
		$qry_tarjeton = db_query( $sql_tarjeton );
		if( db_num_rows($qry_tarjeton) > 0 )
		{
			$r_tarjeton = db_fetch_array( $qry_tarjeton );
			$IDClienteTarjeton = $r_tarjeton["IDClienteTarjeton"];
		}//end if
		else
		{
			$IDClienteTarjeton = get_maxID("ClienteTarjeton WHERE IDCliente = '$frm[IDCliente]' ","IDClienteTarjeton");
			$sql_insert = "INSERT INTO ClienteTarjeton ( IDClienteTarjeton, IDCliente, IDPuntoVenta, FechaHasta, FechaTrCr ) VALUES ( '" . $IDClienteTarjeton . "','" . $frm["IDCliente"] . "','" . $frm["IDPuntoVenta"] . "', DATE_ADD(CURDATE(), INTERVAL 558 DAY) ,NOW() )";
			$qry_insert = db_query( $sql_insert );
		}//end else
		
		//Insertar los Puntos en la tabla de tarjeton puntos
		$IDPuntos = get_maxID("TarjetonPuntos WHERE IDCliente = '$frm[IDCliente]' AND IDClienteTarjeton = '" . $IDClienteTarjeton . "' ","IDPunto");
		$sql_insert = "INSERT INTO TarjetonPuntos ( IDClienteTarjeton, IDPunto, IDCliente, Puntos, IDPuntoVenta, IDFactura, FechaTrCr ) VALUES ( '" . $IDClienteTarjeton . "','" . $IDPuntos . "','" . $frm["IDCliente"] . "','" . $puntos . "','" . $frm["IDPuntoVenta"] . "','" . $frm["IDFactura"] . "',NOW() )";
		db_query( $sql_insert );
	
		//insertar el log
		insertlog($ID_Usuario,"Fidelizacion",$frm[IDFactura],"Actualizar",$sql_insert);
		
		return $puntos;
	}//end if
	else
	{
		return 0;
		
	}//end else
		
}//end function actualiza_puntos_fid( $frm )


function getpuntos_fid( $frm )
{
	Global $Nombre_Usuario, $ID_Usuario, $IDPuntoVenta;
	
	//Verificar que el usuario está fidelizado FidClienteRespuesta
	$sql_verifica = " SELECT * FROM FidClienteRespuesta WHERE  IDCliente = '" . $frm["IDCliente"] . "'  ";
	$qry_verifica = db_query( $sql_verifica );
	if( db_num_rows( $qry_verifica ) > 0 )
	{
		//Verificar si el cliente tiene tarjeton vigente
		$sql_tarjeton = "SELECT * FROM ClienteTarjeton WHERE IDCliente = '" . $frm["IDCliente"] . "' AND FechaHasta > CURDATE() AND Vigente = 'S' ";
		$qry_tarjeton = db_query( $sql_tarjeton );
		if( db_num_rows($qry_tarjeton) > 0 )
		{
			$r_tarjeton = db_fetch_array( $qry_tarjeton );
			$IDClienteTarjeton = $r_tarjeton["IDClienteTarjeton"];
			
			//traer el tarjeton virtual es un array generado de los parametros
			$sql_param = "SELECT * FROM Fidelizacion";
			$qry_param = db_query( $sql_param );
			while( $r_param = db_fetch_array( $qry_param ) )
				$array_param[ $r_param[IDFidelizacion] ] = $r_param;
			
			//traer los puntos del tarjeton redimidos
			$sql_puntos_redim = "SELECT SUM(Puntos) as Puntos FROM TarjetonPuntos WHERE IDClienteTarjeton = '" . $IDClienteTarjeton . "' AND IDCliente = '" . $frm["IDCliente"] . "' AND Redimido = 'S' " ;
			$qry_puntos_redim = db_query( $sql_puntos_redim );
			$r_puntos_redim = db_fetch_array( $qry_puntos_redim );
			
			//traer los puntos del tarjeton
			$sql_puntos = "SELECT SUM(Puntos) as Puntos FROM TarjetonPuntos WHERE IDClienteTarjeton = '" . $IDClienteTarjeton . "' AND IDCliente = '" . $frm["IDCliente"] . "' AND Redimido = 'N' " ;
			$qry_puntos = db_query( $sql_puntos );
			$r_puntos = db_fetch_array( $qry_puntos );

			//armar el tarjeton con los redimidos y los que no
			//primero los redimidos
			foreach( $array_param as $idparam => $lineatarjeton )
			{
				if( $lineatarjeton["Cantidad"] < $r_puntos_redim["Puntos"] ) //toda la linea redimida
				{
					$array_param[$idparam]["Redimido"] = 'Si';
					$r_puntos_redim["Puntos"] = $r_puntos_redim["Puntos"] - $lineatarjeton["Cantidad"];
				}//end if
				elseif( $r_puntos_redim["Puntos"] <> 0 )
				{
					$array_param[$idparam]["Redimido"] = $r_puntos_redim["Puntos"];
					$r_puntos_redim["Puntos"] = 0; //porque ya estasn asignados a todas las lineas
				}//end else	
				
			}//end for
			
			//luego los que no se han redimido
			foreach( $array_param as $idparam => $lineatarjeton )
			{
				if( $lineatarjeton["Redimido"] <> 'Si' )
				{
					if( ( $lineatarjeton["Cantidad"] - $lineatarjeton["Redimido"] ) < $r_puntos["Puntos"] )
					{
						$array_param[$idparam]["Puntos"] = $lineatarjeton["Cantidad"] - $lineatarjeton["Redimido"]; //restamos los puntos para esa linea
						$r_puntos["Puntos"] = $r_puntos["Puntos"] - ( $lineatarjeton["Cantidad"] - $lineatarjeton["Redimido"] ); //restamos los puntos porque ya los asignamos a la linea
					}//end if
					else
					{
						$array_param[$idparam]["Puntos"] = $r_puntos["Puntos"];
						$r_puntos["Puntos"] = 0; //porque ya están asignados a todas las lineas
					}//end else
				}//end if
			}//end for
			
			return $array_param;
			
		}//end if
		else
		{
			return 0;
		}//end else



		
	}//end if
	else
	{
		return 0;
		
	}//end else
		
}//end function getpuntos_fid( $frm )

function redimir_fid( $idcliente, $puntos )
{
	Global $Nombre_Usuario, $ID_Usuario, $IDPuntoVenta;
	
	//Verificar que el usuario está fidelizado FidClienteRespuesta
	$sql_verifica = " SELECT * FROM FidClienteRespuesta WHERE  IDCliente = '" . $idcliente . "'  ";
	$qry_verifica = db_query( $sql_verifica );
	if( db_num_rows( $qry_verifica ) > 0 )
	{
		
		$sql_tarjeton = "SELECT * FROM ClienteTarjeton WHERE IDCliente = '" . $idcliente . "' AND FechaHasta > CURDATE() AND Vigente = 'S' ";
		$qry_tarjeton = db_query( $sql_tarjeton );

		if( db_num_rows($qry_tarjeton) > 0 )
		{
			$r_tarjeton = db_fetch_array( $qry_tarjeton );
			
			//actualizar los puntos del tarjeton
			$sql_puntos = "SELECT * FROM TarjetonPuntos WHERE IDClienteTarjeton = '" . $r_tarjeton["IDClienteTarjeton"] . "' AND IDCliente = '" . $idcliente . "' AND Redimido = 'N' " ;
			$qry_puntos = db_query( $sql_puntos );
			$redimido = 0;
			while( $r_puntos = db_fetch_array( $qry_puntos ) )
			{
				$array_puntos[ $r_puntos["IDPunto"] ] = $r_puntos;
				if( $r_puntos["Puntos"] == $puntos && $redimido == 0 )//listo, facil porque ya se redime y listo
				{
					//Actualizar registro a redimido
					$sql_update = "UPDATE TarjetonPuntos SET Redimido = 'S' WHERE IDClienteTarjeton = '" . $r_tarjeton["IDClienteTarjeton"] . "' AND IDCliente = '" . $idcliente . "' AND IDPunto = '" . $r_puntos["IDPunto"] . "' ";
					db_query( $sql_update );
					$redimido = 1;
					return true;
				}//end if
			}//end while
			
			if( $redimido == 0 ) //No se ha podido redimir y hay que hacer u reorden
			{
				foreach( $array_puntos as $idpunto => $datos_puntos )
				if( $puntos > 0 )
				{
					if( $puntos < $datos_puntos["Puntos"] )// se le restan los puntos al registro y se hace un nuevo registro con los sobrantes
					{
						$sobrantes = $datos_puntos["Puntos"] - $puntos;
						 $sql_update = "UPDATE TarjetonPuntos SET Puntos = '" . $puntos . "', Redimido = 'S' WHERE IDClienteTarjeton = '" . $r_tarjeton["IDClienteTarjeton"] . "' AND IDCliente = '" . $idcliente . "' AND IDPunto = '" . $idpunto . "' ";
						db_query( $sql_update );
						
						//insert con los sobrantes
						$IDPuntos = get_maxID("TarjetonPuntos WHERE IDCliente = '$idcliente' AND IDClienteTarjeton = '" . $r_tarjeton["IDClienteTarjeton"] . "' ","IDPunto");
						 $sql_insert = "INSERT INTO TarjetonPuntos ( IDClienteTarjeton, IDPunto, IDCliente, Puntos, IDPuntoVenta, IDFactura, FechaTrCr ) VALUES ( '" . $r_tarjeton["IDClienteTarjeton"] . "','" . $IDPuntos . "','" . $idcliente . "','" . $sobrantes . "','" . $datos_puntos["IDPuntoVenta"] . "','" . $datos_puntos["IDFactura"] . "', '" . $datos_puntos["FechaTrCr"] . "' )";
						db_query( $sql_insert );
						
						$puntos = 0;

						return true;
						
					}//end if
					elseif( $puntos > $datos_puntos["Puntos"] ) //es menor los puntos hay que redimir el registro y restar los puntos para seguir mirando
					{
						 $sql_update = "UPDATE TarjetonPuntos SET  Redimido = 'S' WHERE IDClienteTarjeton = '" . $r_tarjeton["IDClienteTarjeton"] . "' AND IDCliente = '" . $idcliente . "' AND IDPunto = '" . $idpunto . "' ";
						db_query( $sql_update );
						$puntos = $puntos - $datos_puntos["Puntos"];
					}//end if
					else // se redimen todos los puntos y seguimos mirando para redimir el resto
					{
						 $sql_update = "UPDATE TarjetonPuntos SET  Redimido = 'S' WHERE IDClienteTarjeton = '" . $r_tarjeton["IDClienteTarjeton"] . "' AND IDCliente = '" . $idcliente . "' AND IDPunto = '" . $idpunto . "' ";
						db_query( $sql_update );
						$puntos = $puntos - $datos_puntos["Puntos"];

					}//end else
				}//end if
			}//end if
			else 
			{
				return true;
			}//end else
		}//end if
		else
			return false;
		
	}//end if
	else
	{
		return 0;
		
	}//end else
		
}//end function getpuntos_fid( $frm )

function set_puntos( $idcliente )
{
	$frm["IDCliente"] = $idcliente;
	$tarjeton = getpuntos_fid( $frm );
	if( $tarjeton)
	{
		$strhtml = "";
		
		$strhtml = "<table width='100%' class='btnPuntos'>
			<tr>
				<td align='right' class='navpic'><a href='#'>Ver Puntos Usuario</a></td>
			</tr></table>";
		
		$strhtml .= "<table width='100%' class='tblPuntos'>
			<tr>
				<td class='navpic'>Puntos</td>
				<td class='navpic'>Puntos Requeridos</td>
				<td class='navpic'>Descuento</td>
			</tr>";
			
			
		foreach( $tarjeton as $idparam => $value )
		{
		
				$strhtml .= "<tr>";
				$strhtml .= "<td class='col1list'>" . $value['Puntos'] . "</td>";
				$strhtml .= "<td class='col1list'>" . $value['Cantidad'] . "</td>";
				$strhtml .= "<td class='col1list' align='right'> $" . number_format( $value['ValorDescuento'] ) . "</td>";
				$strhtml .= "</tr>";

		}//end for


		$strhtml .= "</table>";
	
		return $strhtml;
	}//end if
	else
		return false;
	
}//end function

?>