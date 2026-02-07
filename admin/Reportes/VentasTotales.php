<body><%
		switch ($action) {
			
			case "view" :
				print_from($IDPuntoVenta,$Fecha);
			break;
			
			default :
				print_from("");
			break;
		
		} // End switch


 
/*******************************************************************************************
		funcion Listar
*******************************************************************************************/

function print_from($IDPuntoVenta="", $Fecha=""){
	Global $dblink,$total_records,$row,$numtoshow,$Nivel,$IVA,$Mes_array,$FechaDesde, $FechaHasta,$IDPuntoVenta;
	require( "Reportes/Calc.php" );
	$Calendario = new Date_Calc;
	
	//echo $Calendario->nextWeekday("30","11","2005");
	 
	$daybegin = substr( $FechaDesde, 8 , 10 );
	$monthbegin = substr( $FechaDesde, 5 , 2 );
	$yearbegin = substr( $FechaDesde, 0, 4 );
	
	$dayend = substr( $FechaHasta, 8 , 10 );
	$monthend = substr( $FechaHasta, 5 , 2 );
	$yearend = substr( $FechaHasta, 0 , 4 );
	 
	$sql_retefuente = "SELECT * FROM ReteFuente LIMIT 1";
	$query_retefuente = db_query( $sql_retefuente );
	$r_retefuente = db_fetch_object( $query_retefuente );
	
	$ReteFuente = $r_retefuente->Valor / 100;
 
%>
	
	<table width="100%">
		<%
		if(!empty($IDPuntoVenta) && !empty( $FechaDesde ) && !empty( $FechaHasta ) ){
		%>
		<tr>
		<td>
			<table width="100%" border="0" align='center' cellspacing="1" cellpadding="0" bgcolor="#345487">	
			<form name="frm" action="<?=$PHP_SELF?>" method="post" onsubmit="return Evalua(document.frm)">
				<tr>
					<td class="maintitle" valign="middle">&nbsp; 
							Ventas totales mensuales desde ; <?=formatofecha($FechaDesde)?> hasta : <?=formatofecha($FechaHasta)?>
						
					</td>
				</tr>
				<?
					
					$nowdate = $yearbegin.$monthbegin.$daybegin;
					$enddate = $Calendario->nextWeekday($dayend,$monthend,$yearend);
					$datosfechas = array();
					do
					{
						
						
						$sql_facturas = " SELECT ( SUM(F.ValorTotal) - SUM(F.ValorIVA) )  as Venta, 
											SUM(F.ValorIVA) as IVA, 
											SUM( (FPF.Valor / ( 1 + $IVA ) ) * ( FPF.Comision / 100 ) ) as Comision 
											FROM Factura F,FormaPagoFactura FPF
											WHERE F.IDPuntoVenta = '$IDPuntoVenta' 
											AND DATE_FORMAT( F.FechaFactura,'%Y%c%d' ) = DATE_FORMAT( '$nowdate', '%Y%c%d' )
											AND F.IDFactura = FPF.IDFactura
											ORDER BY FechaFactura ASC ";
										
						$qry_facturas = db_query( $sql_facturas );
						
						while( $array_facturas = db_fetch_array( $qry_facturas ) )
						{
							$datosfechas[$nowdate] = $array_facturas;
						}//end while( $array_facturas = db_fetch_array( $qry_facturas ) )
						
						$daybegin = substr( $nowdate, 6 , 2 );
 						$monthbegin = substr( $nowdate, 4 , 2 );
						$yearbegin = substr( $nowdate, 0, 4 );
						
						$nexdate = $Calendario->nextWeekday($daybegin,$monthbegin,$yearbegin);
						$nowdate = $nexdate;

					}while($nowdate <= $enddate);
					
				?>
				
				<tr>
					<td class='mainbg'> 
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td class="titlemedium" align="center" nowrap>Fecha</td>
							<td class="titlemedium" align="center" nowrap>Venta </td>
							<td class="titlemedium" align="center" nowrap>IVA</td>
							<td class="titlemedium" align="center" nowrap>TOTAL</td>
						</tr>
						<?
						foreach( $datosfechas as $key => $valor )
						{ 
							$class = repetition()?"row2":"row1";
							//print_r($valor);
							if( !empty( $valor['Venta'] ) )
							{
						?>
						<tr>
							<td class="<?=$class?>" align="center" nowrap><?=$key?></td>
							<td class="<?=$class?>" align="right" nowrap><?=$valor['Venta']?> </td>
							<td class="<?=$class?>" align="right" nowrap><?=$valor['IVA']?></td>
							<td class="<?=$class?>" align="center" nowrap><?echo $valor['Venta'] + $valor['IVA'];?></td>
						</tr>
						
						<?
							}//end if( !empty( $valor['Venta'] ) )
						}//foreach( $datosfechas as $key => $valor )
						?>
							
							
					</table>
				</td>
			</tr>
		</form>
	
		</table>
	</td>
	</tr>
	<% 
	 } // END if(!empty($IDEmpresa))
	%>
	</table>
	<%						
}// Enf function print()	

%>
</body>