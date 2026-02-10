<?php
include("../admin/config.inc.php");


$Table = "CodificacionEspecifica";
$TableJoin = "Referencia";
$Key = "IDCodificacionEspecifica";
$Title = " Consultar Inventario ";
$MOD = "InventarioCon";
$m="Referencia";




//seleccionar tallas
$sql_tallas = " SELECT * FROM Talla WHERE Publicar = 'S' AND IDTalla not in (19,26,25,24,27) ORDER BY Descripcion ";
$qry_tallas = db_query( $sql_tallas );
while( $r_tallas = db_fetch_array( $qry_tallas ) )
{
    //$array_tallas[$r_tallas[IDTalla]] = $r_tallas;
    //con descripcion
    $array_tallas[$r_tallas["Descripcion"]] = $r_tallas;
}//end while
?>
<?php

$sql_pto_venta = "SELECT IDPuntoVenta FROM PuntoVenta WHERE Publicar = 'S' ";
$qry_pto_venta = db_query( $sql_pto_venta );
while($_pto_vta=db_fetch_object( $qry_pto_venta )){
    $puntoventa=$_pto_vta->IDPuntoVenta;
    $sql_referencia = "SELECT * FROM Referencia   WHERE IDReferencia <> '160' $condicion AND Publicar <> 'N' ORDER BY  IDTipoTalla, Numero ";
    $qry_referencia = db_query( $sql_referencia );
    while( $r_referencia = db_fetch_object( $qry_referencia ) )
    {
        $ref = $r_referencia->IDReferencia;					
        $sql =  "SELECT *, T.Descripcion as Talla FROM $Table CE, Referencia R, PuntoVentaReferencia PR, Talla T WHERE PR.IDPuntoVenta = '$puntoventa' AND R.IDReferencia = '$ref' ";
        $sql .= "AND R.IDReferencia = PR.IDReferencia ";
        $sql .= "AND PR.IDPuntoVentaReferencia = CE.IDPuntoVentaReferencia ";
        $sql .= "AND CE.IDTalla = T.IDTalla  ";

        $query_codificacion = db_query($sql);
        $rows = db_num_rows($query_codificacion);
        $array_codificacion = array( );
        while($r_codificacionesp = db_fetch_array($query_codificacion))
        {
            $array_codificacion[ $ref ][ $r_codificacionesp["Talla"] ] = array( "Numero"=>$r_referencia->Numero,"Existencia"=>$r_codificacionesp["Existencias"] );
        }//end while
        $totalreferencia = 0;
        foreach( $array_codificacion as $ref => $arraydatos )
        {
            $linea = substr( $r_referencia->Numero, 0, 2 );
            $mostrar = 0;
            foreach( $array_tallas as $idtalla => $datostallas )
            {
                if( $arraydatos[$idtalla][Existencia] > 0 )
                    $mostrar = 1;
            }//end for
            if( $mostrar == 1 )
            {
                foreach( $array_tallas as $idtalla => $datostallas )
                {
                    $arraydatos[$idtalla][Existencia];
                    $array_linea[ $linea ][ $idtalla ] += 	$arraydatos[$idtalla][Existencia];
                    $totales[ $idtalla ] += $arraydatos[$idtalla][Existencia];
                    $totalreferencia +=  $arraydatos[$idtalla][Existencia];
                }//end for

                $sql_insert="INSERT INTO InventarioHistorial (IDPuntoVenta,Referencia,Total,FechaCorte,UsuarioTrCr,FechaTrCr) 
                        VALUES ('".$puntoventa."', '".$r_referencia->Numero."', '".$totalreferencia."', '".date("Y-m-d")."', 'Cron', '".date("Y-m-d H:i:s")."')";
                db_query( $sql_insert );
            }//end if mostrar
        }//end if
    } //end while referencia
}


echo "<br>Finalizado";


?>
