<?php
/*******************************************************************************************
	habil: los dias que se consigna en una fecha
	Parametros:
			FechaInicio = Fecha de Inicio para el proceso
			FechaFin = Fecha Fin para el proceso
	Retorna:	
			Devuelve un array con las fechas, que contitiene la fechas 
			de venta que se consignan esse dia
*******************************************************************************************/
function dhabiles($FechaInicio, $FechaFin){
	Global $Dia_array;

	$cal = new Date_Calc;
	
	$anofin = substr( $FechaFin, 0, 4 );
	$mesfin = substr( $FechaFin, 5, 2 );
	$diafin = substr( $FechaFin, 8, 2 );
	
	$anoinicio = substr( $FechaInicio, 0, 4 );
	$mesinicio = substr( $FechaInicio, 5, 2 );
	$diainicio = substr( $FechaInicio, 8, 2 );
	
	$anteriormes = $cal->endOfPrevMonth($diainicio,$mesinicio,$anoinicio,"%Y-%m-%d");
	
	/*********** CONSULTAR LOS FESTIVOS **************/
	$qry_festivos = db_query( $sql_festivos = "SELECT Fecha FROM Festivo" );
	$i = 0;
	while( $r_festivos = db_fetch_array( $qry_festivos ) )
	{
		$array_festivos[$i] = $r_festivos["Fecha"]; 
		$i++;
	}//end while
	
	/*********** CONSULTAR LOS DIAS ESPECIALES **************/
	$qry_especiales = db_query( $sql_especiales = "SELECT Fecha FROM DEspeciales" );
	$i = 0;
	while( $r_especiales = db_fetch_array( $qry_especiales ) )
	{
		$array_especiales[$i] = $r_especiales["Fecha"]; 
		$i++;
	}//end while
		
	$prevFecha = $FechaFin;
	/************ ARMAR ARRAY DE FECHAS *************/
	$i = 0;
	$array_temp = array( );
	do
	{
		$ano = substr( $prevFecha, 0, 4 );
		$mes = substr( $prevFecha, 5, 2 );
		$dia = substr( $prevFecha, 8, 2 );
		
		$esdia = $cal->dayOfWeek($dia,$mes,$ano);
		
		if( $esdia == 5 )
		{
			$array_fechas[$FechaActual][$i] = $prevFecha;
			$i++;
		}//end if
		
		if( in_array( $prevFecha, $array_especiales ) )
		{
			$array_fechas[$FechaActual][$i] = $prevFecha;
			$i++;
		}//end if
		
		if( !in_array( $prevFecha, $array_festivos ) && $esdia <> 0 && $esdia <> 6   )
		{
			$FechaActual = $prevFecha;
			$previa = $cal->prevWeekdaytodos($dia,$mes,$ano,"%Y-%m-%d");
		}//end if
		
		if( $FechaActual == $prevFecha )
			$array_fechas[$FechaActual][$i] = $previa;
		else
			$array_fechas[$FechaActual][$i] = $prevFecha;
		
		$prevFecha = $cal->prevWeekdaytodos($dia,$mes,$ano,"%Y-%m-%d");
		$i++;
		
		
		//echo $FechaActual;
		//echo "<br>";
				
		if( $i == 1000 )
			exit;
		
	}while( $i < 100 );
	
	return( $array_fechas );
}
?>
