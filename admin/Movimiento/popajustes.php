<%
	include("../config.inc.php");
	Encabezado();
	$datos = Verifica_Sesion();
	//print_r($datos);
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];

 	 	
%>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
		<title><%pv($app_title);%> Ajustes</title>
		<link href="../styles.css" rel="stylesheet" media="screen">
	
	<script>
	function additem(PopName,selectedValue,selectedText)  
			{
				
				var swc= "2";
				var boxLength = PopName.length;
				var i;
				var isNew = true;
				if (boxLength != 0) {
					for (i = 0; i < boxLength; i++) {
						thisitem = PopName.options[i].text;
						if (thisitem == selectedText) {
							isNew = false;
							break;
						}
					}
				} 
				if (isNew) {
					//opener.document.frm.Referencias.value = selectedValue;
					//opener.document.frm.Referencias.text = selectedText;
					
					opener.addSelect(selectedText,selectedValue,swc);
				}
				
			}
		</script>


</head>
	<body bgcolor="#ffffff">
	<table width="100%" cellpadding="3" cellspacing="0" border="0" class="content">
	<tr>
		<td class="navpic"  align="left"><strong><font ><%pv($app_title);%></font></strong></td>
	</tr>
	<tr>
		<td class="col2"  align="left">
			Seleccione cada una de las referencias seleccionandolas y haciedo clic en 'Agregar'. Cuando haya terminado
			cierre esta ventana haciendo clic en cerrar
		</td>
	</tr>
	</table>
		<form name=form method="Post" action="">
			<div align="center">
				<?
				
				$sql_referencias = "SELECT R.IDReferencia, R.Numero, PVR.IDPuntoVentaReferencia,P.ValorVenta as ValorVenta, P.Descuento
										FROM Referencia R, PuntoVentaReferencia PVR, Precio P
										WHERE PVR.IDPuntoVenta = '$IDPuntoVenta' 
										AND PVR.IDReferencia = R.IDReferencia
										AND R.IDPrecio = P.IDPrecio
										GROUP BY PVR.IDPuntoVentaReferencia
										ORDER BY R.Numero";
										
				$qry_referencias = db_query( $sql_referencias );
				$i = 0;
				while( $r_referencias = db_fetch_array( $qry_referencias ) )
				{
					$array_referencias[$i] = $r_referencias; 
					$i++;
				}//end while

				?>
				<SELECT name=Referencias style="width:180px; " size="20" class="inputSelect" multiple>
					<?php
						foreach( $array_referencias as $key=>$valor )
							echo "<option value=$valor[IDPuntoVentaReferencia]>$valor[Numero] :   $". number_format($valor[ValorVenta],0,',','.') . " $valor[Descuento]% </option>";
					?>
				</SELECT><br>
				<br>
				<input type="button" value="Agregar" onclick="if(document.form.Referencias.selectedIndex >= 0)additem(window.opener.document.frm.Referencias,form.Referencias.options[form.Referencias.selectedIndex].value,form.Referencias.options[form.Referencias.selectedIndex].text);">	<input type="button" value="Cerrar" onclick="window.close();">
			</div>
		</form>
</body>
</html>