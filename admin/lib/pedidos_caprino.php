<?php

/*******************************************************************************************
	Libreria de funciones para el Sistema de Puntos de Venta de Caprino - Pedidos
	Creador por: John Escobar
	Iniciado: Jul 29/2005
	Ultima Modificaci?n: Jul 29/2005
*******************************************************************************************/

/*******************************************************************************************
	generarpedidopuntos: Genera todos los pedidos de los puntos de venta seleccionados de acuerdo
			a las existencias en cada almacen y lo pedido para el mismo.
	Parametros:
			$array_puntos: array con los puntos seleccionados para generar los pedidos
	Retorna:	
			Void
*******************************************************************************************/
function generarpedidopuntos( $array_puntos )
{
	Global $Nombre_Usuario, $ID_Usuario;
	
	$Table = "OrdenCompra";
	$Key = "IDOrdenCompra";
	$TableJoin = "DetalleOrdenCompra";
	
	$generados = 0;
	
	foreach( $array_puntos as $punto )
	{

		$sql_puntos = "SELECT * FROM PuntoVentaReferencia WHERE IDPuntoVenta = '$punto'";	
		$query_puntos = db_query( $sql_puntos );
		
		$idpedido = 0;
		
		
		while( $r_puntos = db_fetch_object( $query_puntos ) )
		{

			$sql_codificacion =  "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$r_puntos->IDPuntoVentaReferencia'";
			$query_codificacion = db_query( $sql_codificacion );
			
			if( db_num_rows( $query_codificacion ) > 0 )
			{
				
				$r_codificacion = array();
				$i = 0;
				
				while($r_codificacion[$i] = db_fetch_array($query_codificacion))
				{
					$i++;
				} //end while($r[$i] = db_fetch_array($query_codificacion))
				//print_r($r);
				
				foreach( $r_codificacion as $valor )
				{
				
					if( !empty( $valor['IDTalla'] ) )
					{
						
						//Verificar Cantidad Pedida
						
						$pedido = vercantidadpedida($r_puntos->IDPuntoVentaReferencia,$valor['IDTalla']);
						
						
						//SE GENERA EL SUGERIDO			
							
						$pedir = $valor['Maximo'] - ($pedido + $valor['Existencias']);
						
						
						/*window_alert( $valor['Maximo']);
						window_alert( $valor['Existencias']);
						window_alert( $pedir );
						*/
						
						if($pedir > 0)
						{
						
							if( $idpedido == 0 )
							{
							
								$idpedido = get_maxID($Table,$Key);
								
								$strinsert_sugerido = "INSERT INTO $Table (IDOrdenCompra, FechaOrden, IDEmpleado, IDPuntoVenta,NumeroOrden, IDEstadoPedido, ";
								$strinsert_sugerido .= "Publicar, UsuarioTrCr, FechaTrCr) ";
								$strinsert_sugerido .= "VALUES ('$idpedido', now(), '$ID_Usuario', '$punto','$idpedido', '1','S', '$Nombre_Usuario', now())";
								
								$query_sugerido = db_query( $strinsert_sugerido );
								
								$generados++;
								
								//insertar el log
								//insertlog($ID_Usuario,$Table,$idpedido,"Insertar",$strinsert_sugerido);
							
							}//if( $idpedido == 0 )
							
							$iddetalle = get_maxID("DetalleOrdenCompra","IDDetalleOrdenCompra");
							
							$strinsertdetalle  = "INSERT INTO DetalleOrdenCompra (IDDetalleOrdenCompra, IDOrdenCompra, IDPuntoVentaReferencia,IDTalla, IDEstadoPedido,Cantidad, Pedido,Publicar, UsuarioTrCr, FechaTrCr) ";
							$strinsertdetalle .= "VALUES ('$iddetalle','$idpedido', '$r_puntos->IDPuntoVentaReferencia','$valor[IDTalla]','1','$pedir', '$pedido','S','$Nombre_Usuario',now())";
						
							$query_detalle = db_query( $strinsertdetalle );
							
							//insertar el log
							//insertlog($ID_Usuario,"DetalleOrdenCompra",$iddetalle,"Insertar",$strinsertdetalle); 
							
							
							//ACTUALIZAR PENDIENTES
							$sql_pendiente = "SELECT * FROM Pendientes WHERE IDPuntoVentaReferencia = '$r_puntos->IDPuntoVentaReferencia' AND IDTalla = '$valor[IDTalla]'";
							$query_pendiente = db_query($sql_pendiente);
							if( db_num_rows( $query_pendiente ) > 0 )
							{
							
								$r_pendiente = db_fetch_object( $query_pendiente );
								$cantidadactualizar = $r_pendiente->CantidadPendiente + $pedir;
								$sql_actualizapendiente = "UPDATE Pendientes SET CantidadPendiente = '$cantidadactualizar' WHERE IDPendientes = $r_pendiente->IDPendientes";
								db_query($sql_actualizapendiente);
								
								//insertar el log
								//insertlog($ID_Usuario,"Pendientes",$r_pendiente->IDPendientes,"Actualizar",$sql_actualizapendiente);					
							}
							else
							{
								$idpendiente = get_maxID("Pendientes","IDPendientes");
								$sql_insertapendiente = "INSERT INTO Pendientes ( IDPendientes, IDPuntoVentaReferencia, IDTalla, CantidadPendiente, IDPuntoVenta ) VALUES('$idpendiente','$r_puntos->IDPuntoVentaReferencia','$valor[IDTalla]','$pedir','$punto')";
								db_query($sql_insertapendiente);
								
								//insertar el log
								//insertlog($ID_Usuario,"Pendientes",$idpendiente,"Insertar",$sql_insertapendiente);	
							}
							
							
						}//if($pedir > 0)
					
						$pedido = 0;
						$pedir = 0;
						
					}//if( !empty( $valor[IDTalla] ) )
					
				}//end foreach( $r as $valor )
				
			
			}//end if( db_num_rows( $query_codificacion ) > 0 )
			else
			{
			
				window_alert( "Referencia no existe o no se ha codificado correctamente" );
				echo "<SCRIPT>location.href='?mod=mPedido';</SCRIPT>";
			
			}//end else
			
			
		}//end while( $r_puntos = db_fetch_object( $query_puntos ) )

	}//end foreach( $array_puntos as $punto )
	
	window_alert( $generados." Generados" );
	
}//end function generartodo( )


/*******************************************************************************************
	Libreria de funciones para el Sistema de Puntos de Venta de Caprino - Pedidos
	Creador por: John Escobar
	Iniciado: Jul 29/2005
	Ultima Modificaci?n: Jul 29/2005
*******************************************************************************************/

/*******************************************************************************************
	generarpedidopuntos: Genera todos los pedidos de los puntos de venta seleccionados de acuerdo
			a las existencias en cada almacen y lo pedido para el mismo.
	Parametros:
			$array_puntos: array con los puntos seleccionados para generar los pedidos
	Retorna:	
			Void
*******************************************************************************************/
/*Se comenta para realizar la funcion genera automaticamente en la tabla de pedido
function generarpedidopuntos( $array_puntos )
{
	Global $Nombre_Usuario, $ID_Usuario,$Table,$Key,$TableJoin;
	
	foreach( $array_puntos as $punto )
	{

		$sql_puntos = "SELECT * FROM PuntoVentaReferencia WHERE IDPuntoVenta = '$punto'";	
		$query_puntos = db_query( $sql_puntos );
		
		$idsugerido = 0;
		
		
		while( $r_puntos = db_fetch_object( $query_puntos ) )
		{
			
			$pedido = 0;
			
			$sql_codificacion =  "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$r_puntos->IDPuntoVentaReferencia'";
			$query_codificacion = db_query( $sql_codificacion );
			
			if( db_num_rows( $query_codificacion ) > 0 )
			{
				
				if( $idsugerido == 0 )
				{
				
					$idsugerido = get_maxID($Table,$Key);
					$idpedido = get_maxID("OrdenCompra","IDOrdenCompra");
					
					$strinsert_sugerido = "INSERT INTO $Table (IDSugeridoPedido, Fecha, IDEmpleado, IDPuntoVenta,NumeroSugerido, ";
					$strinsert_sugerido .= "Publicar, UsuarioTrCr, FechaTrCr) ";
					$strinsert_sugerido .= "VALUES ('$idsugerido', now(), '$ID_Usuario', '$punto','$idpedido', 'S', '$Nombre_Usuario', now())";
					
					$query_sugerido = db_query( $strinsert_sugerido );
					
					//insertar el log
					insertlog($ID_Usuario,$Table,$idsugerido,"Insertar",$strinsert_sugerido);
				
				}//if( !empty( $idsugerido ) )
				
				$r_codificacion = array();
				$i = 0;
				
				while($r_codificacion[$i] = db_fetch_array($query_codificacion))
				{
					$i++;
				} //end while($r[$i] = db_fetch_array($query_codificacion))
				//print_r($r);
				
				foreach( $r_codificacion as $valor )
				{
				
					if( !empty( $valor['IDTalla'] ) )
					{
						
						//VERIFICAR LA TABLA DE PEDIDO PARA VER LOS PEDIDOS SOLICITADOS
						//Estado de pedidos Solicitado : IDEstadoPedido = 1;
						
						$sql_pedidos_solicitados = "SELECT * FROM DetalleOrdenCompra WHERE IDEstadoPedido = 1 AND IDPuntoVentaReferencia = '$r_puntos->IDPuntoVentaReferencia' AND IDTalla = '$valor[IDTalla]'";
						
						$query_pedidos_solicitados = db_query( $sql_pedidos_solicitados );
						
						if( db_num_rows( $query_pedidos_solicitados ) > 0 )
						{
							while( $r_pedidos_solicitados = db_fetch_object( $query_pedidos_solicitados ) )
							{
								
								$pedido = $pedido + $r_pedidos_solicitados->Cantidad;
							
							}//end while( $r_pedidos_solicitados = db_fetch_object( $query_pedidos_solicitados ) )
					
						}//end if( db_num_rows( $query_pedidos_solicitados ) > 0 )
									
						//VERIFICAR CONTRA LA TABLA DE MOVIMIENTOS PARA LOCALIZAR LOS PEDIDOS PARCIALES
						//Estado de pedidos recibidos parcialmente : IDEstadoPedido = 4;	
						
						$sql_pedidos_parciales  = "SELECT DOC.IDOrdenCompra, DOC.IDPuntoVentaReferencia, DOC.IDTalla as talladetalle, ";
						$sql_pedidos_parciales .= "DOC.Cantidad as cantidaddetalle, OC.IDOrdenCompra, M.*, DM.* ";
						$sql_pedidos_parciales .= "FROM DetalleOrdenCompra DOC, OrdenCompra OC, Movimiento M , DetalleMovimiento DM ";
						$sql_pedidos_parciales .= "WHERE DOC.IDEstadoPedido = 4 AND DOC.IDPuntoVentaReferencia = '$r_puntos->IDPuntoVentaReferencia' AND DOC.IDTalla = '$valor[IDTalla]'";
						$sql_pedidos_parciales .= "AND DOC.IDOrdenCompra = OC.IDOrdenCompra ";
						
						//Revisar si se valida contra el Id o contra el numero de la orden de compra
						$sql_pedidos_parciales .= "AND OC.NumeroOrden = M.NumeroOrden AND M.IDMovimiento = DM.IDMovimiento ";
						$sql_pedidos_parciales .= "AND DM.IDTalla = DOC.IDTalla";
						
						$query_pedidos_parciales = db_query( $sql_pedidos_parciales );
						
						if( db_num_rows( $query_pedidos_parciales ) > 0 )
						{
							while( $r_pedidos_PARCIALES = db_fetch_object( $query_pedidos_solicitados ) )
							{
								
								$falta_recibir = $r_pedidos_parciales->cantidaddestalle - $r_pedidos_parciales->Cantidad;
								
								if( $falta_recibir > 0  )
								{
								
									$pedido = $pedido + $falta_recibir;
									
								}//end if( $falta_recibir > 0  )
								
							}//end while( $r_pedidos_parciales = db_fetch_object( $query_pedidos_parciales ) )
							
						}//end if( db_num_rows( $query_pedidos_parciales ) > 0 )
						
						//SE GENERA EL SUGERIDO			
							
						$pedir = $valor['Maximo'] - ($pedido + $valor['Existencias']);
						
						//if($pedir > 0)
						//{
							
							$iddetalle = get_maxID("DetalleSugeridoPedido","IDDetalleSugeridoPedido");
							
							$strinsertdetalle  = "INSERT INTO DetalleSugeridoPedido (IDDetalleSugeridoPedido, IDSugeridoPedido, IDPuntoVentaReferencia,IDTalla, Cantidad,Pedido, Publicar, UsuarioTrCr, FechaTrCr) ";
							$strinsertdetalle .= "VALUES ('$iddetalle','$idsugerido', '$r_puntos->IDPuntoVentaReferencia','$valor[IDTalla]','$pedir', '$pedido','S','$Nombre_Usuario',now())";
						
							$query_detalle = db_query( $strinsertdetalle );
							
							//insertar el log
							insertlog($ID_Usuario,"DetalleSugeridoPedido",$iddetalle,"Insertar",$strinsertdetalle); 
					
						//}//if($pedir > 0)
					
						$pedido = 0;
						$pedir = 0;
						
					}//if( !empty( $valor[IDTalla] ) )
					
				}//end foreach( $r as $valor )
				
			
			}//end if( db_num_rows( $query_codificacion ) > 0 )
			else
			{
			
				window_alert( "Referencia no existe o no se ha codificado correctamente" );
				echo "<SCRIPT>location.href='?mod=mPedido';</SCRIPT>";
			
			}//end else
			
			
		}//end while( $r_puntos = db_fetch_object( $query_puntos ) )

	}//end foreach( $array_puntos as $punto )
	
	window_alert( "Sugeridos Generados" );
	
}//end function generartodo( )*/



/*******************************************************************************************
	generarpedidoreferencia: Genera todos los pedidos de los puntos de venta seleccionados de acuerdo
			a las existencias en cada almacen y lo pedido para el mismo.
	Parametros:
			$array_puntos: array de los datos
	Retorna:	
			Void
*******************************************************************************************/
function generarpedidoreferencia( $array_puntos )
{
	Global $Nombre_Usuario, $ID_Usuario;
	
	$Table = "OrdenCompra";
	$Key = "IDOrdenCompra";
	$TableJoin = "DetalleOrdenCompra";
	
	$generados = 0;
	
	//print_r($array_puntos);
	
	$punto = $array_puntos['IDPuntoVenta'];
	

	//Modificado el 16 de Abril por John Escobar para generar pedidos automaticos
	if( empty( $array_puntos["IDReferencia"] ) )
		$referencia = get_field("Referencia","IDReferencia","Numero",$array_puntos['Referencia']);
	else
		$referencia = $array_puntos["IDReferencia"];

	$sql_puntos = "SELECT * FROM PuntoVentaReferencia WHERE IDPuntoVenta = '$punto' AND IDReferencia = '$referencia'";	
	$query_puntos = db_query( $sql_puntos );
	
	$idpedido = 0;
	
	
	$r_puntos = db_fetch_object( $query_puntos );

	$sql_codificacion =  "SELECT * FROM CodificacionEspecifica WHERE IDPuntoVentaReferencia = '$r_puntos->IDPuntoVentaReferencia'";
	$query_codificacion = db_query( $sql_codificacion );
	
	if( db_num_rows( $query_codificacion ) > 0 )
	{
		
		$r_codificacion = array();
		$i = 0;
		
		while($r_codificacion[$i] = db_fetch_array($query_codificacion))
		{
			$i++;
		} //end while($r[$i] = db_fetch_array($query_codificacion))
		//print_r($r);
		
		foreach( $r_codificacion as $valor )
		{
		
			if( !empty( $valor['IDTalla'] ) )
			{
				
				//Verificar Cantidad Pedida
				
				$pedido = vercantidadpedida($r_puntos->IDPuntoVentaReferencia,$valor['IDTalla']);
				
				
				//SE GENERA EL SUGERIDO			
					
				$pedir = $valor['Maximo'] - ($pedido + $valor['Existencias']);
				
				
				/*window_alert( $valor['Maximo']);
				window_alert( $valor['Existencias']);
				window_alert( $pedir );
				*/
				
				if($pedir > 0)
				{
				
					if( $idpedido == 0 )
					{
					
						$idpedido = get_maxID($Table,$Key);
						
						$strinsert_sugerido = "INSERT INTO $Table (IDOrdenCompra, FechaOrden, IDEmpleado, IDPuntoVenta,NumeroOrden, IDEstadoPedido, ";
						$strinsert_sugerido .= "Publicar, UsuarioTrCr, FechaTrCr) ";
						$strinsert_sugerido .= "VALUES ('$idpedido', now(), '$ID_Usuario', '$punto','$idpedido', '1','S', '$Nombre_Usuario', now())";
						
						$query_sugerido = db_query( $strinsert_sugerido );
						
						$generados++;
						
						//insertar el log
						insertlog($ID_Usuario,$Table,$idpedido,"Insertar",$strinsert_sugerido);
					
					}//if( $idpedido == 0 )
					
					$iddetalle = get_maxID("DetalleOrdenCompra","IDDetalleOrdenCompra");
					
					$strinsertdetalle  = "INSERT INTO DetalleOrdenCompra (IDDetalleOrdenCompra, IDOrdenCompra, IDPuntoVentaReferencia,IDTalla, IDEstadoPedido,Cantidad, Pedido,Publicar, UsuarioTrCr, FechaTrCr) ";
					$strinsertdetalle .= "VALUES ('$iddetalle','$idpedido', '$r_puntos->IDPuntoVentaReferencia','$valor[IDTalla]','1','$pedir', '$pedido','S','$Nombre_Usuario',now())";
				
					$query_detalle = db_query( $strinsertdetalle );
					
					//insertar el log
					insertlog($ID_Usuario,"DetalleOrdenCompra",$iddetalle,"Insertar",$strinsertdetalle); 
					
					//ACTUALIZAR PENDIENTES
					$sql_pendiente = "SELECT * FROM Pendientes WHERE IDPuntoVentaReferencia = '$r_puntos->IDPuntoVentaReferencia' AND IDTalla = '$valor[IDTalla]'";
					$query_pendiente = db_query($sql_pendiente);
					if( db_num_rows( $query_pendiente ) > 0 )
					{
					
						$r_pendiente = db_fetch_object( $query_pendiente );
						$cantidadactualizar = $r_pendiente->CantidadPendiente + $pedir;
						$sql_actualizapendiente = "UPDATE Pendientes SET CantidadPendiente = '$cantidadactualizar' WHERE IDPendientes = $r_pendiente->IDPendientes";
						db_query($sql_actualizapendiente);
						
						//insertar el log
						insertlog($ID_Usuario,"Pendientes",$r_pendiente->IDPendientes,"Actualizar",$sql_actualizapendiente);					
					}
					else
					{
						$idpendiente = get_maxID("Pendientes","IDPendientes");
						$sql_insertapendiente = "INSERT INTO Pendientes VALUES('$idpendiente','$r_puntos->IDPuntoVentaReferencia','$valor[IDTalla]','$pedir','$punto','$ID_Usuario',NOW(),'$ID_Usuario',NOW())";
						db_query($sql_insertapendiente);
						
						//insertar el log
						insertlog($ID_Usuario,"Pendientes",$idpendiente,"Insertar",$sql_insertapendiente);	
					}
					
				}//if($pedir > 0)
			
				$pedido = 0;
				$pedir = 0;
				
			}//if( !empty( $valor[IDTalla] ) )
			
		}//end foreach( $r as $valor )
		
	
	}//end if( db_num_rows( $query_codificacion ) > 0 )
	else
	{
	
		window_alert( "Referencia no existe o no se ha codificado correctamente" );
		//echo "<SCRIPT>location.href='?mod=Pedido';</SCRIPT>";
	
	}//end else
		
	window_alert( $generados." Generados" );
	
}//end function generarpedidoreferencia( )




/*******************************************************************************************
	Libreria de funciones para el Sistema de Puntos de Venta de Caprino - Pedidos
	Creador por: John Escobar
	Iniciado: Jul 29/2005
	Ultima Modificaci?n: Jul 29/2005
*******************************************************************************************/

/*******************************************************************************************
	vercantidadpedida: Devuelve la cantidad que se ha pedido o se ha recibido parcialmente
					de una referencia especifica, de una talla especifica, de un almacen.
	Parametros:
			$IDPuntoVentaReferencia: ID que relaciona el punto de venta con una referencia especifica
			$IDTalla: ID de la talla de la que se quiere saber la cantidad que se ha pedido
	Retorna:	
			Void
*******************************************************************************************/
/*Se comenta por el cambio de enfoque. la  idea es ver lo pendiente... se generan dos tablas
para este manejo, Pendientes y Entradas
function vercantidadpedida( $IDPuntoVentaReferencia, $IDTalla )
{
		
	//VERIFICAR LA TABLA DE PEDIDO PARA VER LOS PEDIDOS SOLICITADOS
	//Estado de pedidos Solicitado : IDEstadoPedido = 1;
	
	$sql_pedidos_solicitados = "SELECT * FROM DetalleOrdenCompra WHERE IDEstadoPedido = 1 AND IDPuntoVentaReferencia = '$IDPuntoVentaReferencia' AND IDTalla = '$IDTalla'";
	
	$query_pedidos_solicitados = db_query( $sql_pedidos_solicitados );
	
	if( db_num_rows( $query_pedidos_solicitados ) > 0 )
	{
		while( $r_pedidos_solicitados = db_fetch_object( $query_pedidos_solicitados ) )
		{
			
			$pedido = $pedido + $r_pedidos_solicitados->Cantidad;
		
		}//end while( $r_pedidos_solicitados = db_fetch_object( $query_pedidos_solicitados ) )

	}//end if( db_num_rows( $query_pedidos_solicitados ) > 0 )
				
	//VERIFICAR CONTRA LA TABLA DE MOVIMIENTOS PARA LOCALIZAR LOS PEDIDOS PARCIALES
	//Estado de pedidos recibidos parcialmente : IDEstadoPedido = 4;	
	
	$sql_pedidos_parciales  = "SELECT DOC.IDOrdenCompra, DOC.IDPuntoVentaReferencia, DOC.IDTalla as talladetalle, ";
	$sql_pedidos_parciales .= "DOC.Cantidad as cantidaddetalle, OC.IDOrdenCompra, M.*, DM.* ";
	$sql_pedidos_parciales .= "FROM DetalleOrdenCompra DOC, OrdenCompra OC, Movimiento M , DetalleMovimiento DM ";
	$sql_pedidos_parciales .= "WHERE DOC.IDEstadoPedido = '4' AND DOC.IDPuntoVentaReferencia = '$IDPuntoVentaReferencia' AND DOC.IDTalla = '$IDTalla'";
	$sql_pedidos_parciales .= "AND DOC.IDOrdenCompra = OC.IDOrdenCompra ";
	
	//Revisar si se valida contra el Id o contra el numero de la orden de compra
	$sql_pedidos_parciales .= "AND OC.IDOrdenCompra = M.IDOrdenCompra AND M.IDMovimiento = DM.IDMovimiento ";
	$sql_pedidos_parciales .= "AND DM.IDTalla = DOC.IDTalla";
	
	$query_pedidos_parciales = db_query( $sql_pedidos_parciales );
	
	if( db_num_rows( $query_pedidos_parciales ) > 0 )
	{
		while( $r_pedidos_parciales = db_fetch_object( $query_pedidos_parciales ) )
		{
			
			$falta_recibir = $r_pedidos_parciales->cantidaddetalle - $r_pedidos_parciales->Cantidad;
						
			if( $falta_recibir > 0  )
			{
			
				$existencias = get_field( "CodificacionEspecifica","Existencias","IDPuntoVentaReferencia", $IDPuntoVentaReferencia."' AND IDTalla = '$IDTalla" );
				$maximo = get_field( "CodificacionEspecifica","Maximo","IDPuntoVentaReferencia", $IDPuntoVentaReferencia."' AND IDTalla = '$IDTalla" );
				
				$pedido = $falta_recibir;
				
			}//end if( $falta_recibir > 0  )
			
		}//end while( $r_pedidos_parciales = db_fetch_object( $query_pedidos_parciales ) )
		
	}//end if( db_num_rows( $query_pedidos_parciales ) > 0 )
	
	return $pedido;

}//end function vercantidadpedida( $IDPuntoVentaReferencia, $IDTalla )
*/

/*******************************************************************************************
	Libreria de funciones para el Sistema de Puntos de Venta de Caprino - Pedidos
	Creador por: John Escobar
	Iniciado: Jul 29/2005
	Ultima Modificaci?n: Jul 29/2005
*******************************************************************************************/

/*******************************************************************************************
	vercantidadpedida: Devuelve la cantidad que se ha pedido o se ha recibido parcialmente
					de una referencia especifica, de una talla especifica, de un almacen.
	Parametros:
			$IDPuntoVentaReferencia: ID que relaciona el punto de venta con una referencia especifica
			$IDTalla: ID de la talla de la que se quiere saber la cantidad que se ha pedido
	Retorna:	
			Void
*******************************************************************************************/
function vercantidadpedida( $IDPuntoVentaReferencia, $IDTalla )
{
		
	//VERIFICAR LA TABLA DE PEDIDO PARA VER LOS PEDIDOS SOLICITADOS
	//Estado de pedidos Solicitado : IDEstadoPedido = 1;
	
	$sql_pedidos_solicitados = "SELECT * FROM Pendientes WHERE IDPuntoVentaReferencia = '$IDPuntoVentaReferencia' AND IDTalla = '$IDTalla'";
	
	$query_pedidos_solicitados = db_query( $sql_pedidos_solicitados );
	
	if( db_num_rows( $query_pedidos_solicitados ) > 0 )
	{
		$r_pedidos_solicitados = db_fetch_object( $query_pedidos_solicitados ) ;

		$pedido = $r_pedidos_solicitados->CantidadPendiente;
		
	}//end if( db_num_rows( $query_pedidos_solicitados ) > 0 )
					
	return $pedido;

}//end function vercantidadpedida( $IDPuntoVentaReferencia, $IDTalla )

/*******************************************************************************************
	entradapedido: Actualiza las tablas de pedido, codificacion especifica, movimientos
						etc, y demas pertinentes cuando se hace una entrada de pedido
	Parametros:
			$frm : array con los estados a actualizar
	Retorna:	
			$frm: array con los datos
*******************************************************************************************/
function entradapedido($frm)
{
	Global $Key, $ID_Usuario, $Nombre_Usuario;
	
	/***************Estado de los Pedidos****************/
	/*													*/	
	/*			Solicitado = 1							*/
	/*			Recibido = 2							*/
	/*			Anulado = 3								*/
	/*			RecibidoParcial = 4						*/
	/*													*/	
	/****************************************************/
	
	//Pendiente para funcion de entrada de pedidos a los almacenes.
	
	//print_r($frm);
	
	$sql_detalle = "SELECT * FROM DetalleOrdenCompra WHERE IDOrdenCompra = '$frm[IDOrdenCompra]'";
	$query_detalle = db_query( $sql_detalle );
	
	//Variabl para controlar el estado de la orden de compra
	//1 = RecibidoParcialMente
	//2 = Recibido
	
	$temporal = 0;
	
	while( $r_detalle = db_fetch_object( $query_detalle ) )
	{
		
		//Insertar lo que viene en el array. la cantidad de producto que viene en la remision
		
		if( !empty( $frm[$r_detalle->IDPuntoVentaReferencia][$r_detalle->IDTalla] ) && $frm[$r_detalle->IDPuntoVentaReferencia][$r_detalle->IDTalla] <> 0 )
		{ 
		
			$iddetallemov = get_maxID("DetalleMovimiento","IDDetalleMovimiento");
		
			$cantidad = $frm[$r_detalle->IDPuntoVentaReferencia][$r_detalle->IDTalla];
			
			$sql_dmovimiento  = "INSERT INTO DetalleMovimiento ( IDDetalleMovimiento, IDMovimiento, IDPuntoVentaReferencia, ";
			$sql_dmovimiento .= "IDTalla, Cantidad, UsuarioTrCr, FechaTrCr) VALUES ( '$iddetallemov','$frm[ID]', ";
			$sql_dmovimiento .= "'$r_detalle->IDPuntoVentaReferencia','$r_detalle->IDTalla','$cantidad', ";
			$sql_dmovimiento .= "'$Nombre_Usuario',NOW())";
		
			db_query( $sql_dmovimiento );
			
			//insertar el log
			insertlog($ID_Usuario,"DetalleMovimiento",$iddetallemov,"Insertar",$sql_dmovimiento); 
		
			//Actualizar el estado del detalle del pedido, comparando lo que se pidio con lo que se trae
		
			$sql_cantidadrecibida  = "SELECT SUM(DM.Cantidad) as CantidadRecibida FROM DetalleMovimiento DM,Movimiento M, Ordencompra O ";
			$sql_cantidadrecibida .= "WHERE O.IDOrdenCompra = M.IDOrdenCompra AND O.IDOrdenCompra = '$frm[IDOrdenCompra]' ";
			$sql_cantidadrecibida .= "AND M.IDMovimiento = DM.IDMovimiento AND DM.IDPuntoVentaReferencia = '$r_detalle->IDPuntoVentaReferencia' AND DM.IDTalla = '$r_detalle->IDTalla'";
									
			$query_cantidadrecibida = db_query( $sql_cantidadrecibida );
			
			$r_cantidadrecibida = db_fetch_object( $query_cantidadrecibida );
			
			if( $r_cantidadrecibida->CantidadRecibida < $r_detalle->Cantidad )
			{
			
				$sql_actualizaestado = "UPDATE DetalleOrdencompra SET IDEstadoPedido = '4' WHERE IDDetalleOrdenCompra = '$r_detalle->IDDetalleOrdenCompra'";
				db_query( $sql_actualizaestado );
				
				//insertar el log
				insertlog($ID_Usuario,"DetalleOrdencompra",$r_detalle->IDDetalleOrdenCompra,"Actualizar",$sql_actualizaestado); 
				$temporal = 1;
							
			}//end if( $cantidad < $r_detalle->Cantidad )
			else
			{
			
				$sql_actualizaestado = "UPDATE DetalleOrdencompra SET IDEstadoPedido = '2' WHERe IDDetalleOrdenCompra = '$r_detalle->IDDetalleOrdenCompra'";
				db_query( $sql_actualizaestado );
				
				//insertar el log
				insertlog($ID_Usuario,"DetalleOrdencompra",$r_detalle->IDDetalleOrdenCompra,"Actualizar",$sql_actualizaestado);
				
			}//end else
		
			
		
		}//end if( !empty( $frm[$r_detalle->IDPuntoVentaReferencia][$r_detalle->IDTalla] ) )
		
		$existencias = 0;
			
	}//end while( $r_detalle = db_fetch_object( $query_detalle ) )
	
	if($temporal == 1)
	{
	
		$sql_actualizaestado = "UPDATE OrdenCompra SET IDEstadoPedido = '4' WHERE IDOrdenCompra = '$frm[IDOrdenCompra]'";
		db_query( $sql_actualizaestado );
		
		//insertar el log
		insertlog($ID_Usuario,"Ordencompra",$frm['IDOrdenCompra'],"Actualizar",$sql_actualizaestado); 
	
	}
	else
	{
		$sql_actualizaestado = "UPDATE OrdenCompra SET IDEstadoPedido = '2' WHERE IDOrdenCompra = '$frm[IDOrdenCompra]'";
		db_query( $sql_actualizaestado );
		
		//insertar el log
		insertlog($ID_Usuario,"Ordencompra",$frm['IDOrdenCompra'],"Actualizar",$sql_actualizaestado); 
		$temporal = 1;
		
	}
	
	return $frm;

}//end function entradapedido($frm)
?>