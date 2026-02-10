<?php
	include("../admin/config.inc.php");
	Encabezado();
	$datos = Verifica_SesionCliente();
	//print_r($datos);
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	$IVA = $datos["IVA"];
	$IDPuntoVenta = $datos["IDPuntoVenta"];

 	 	
?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
		<title><?php echo pv($app_title); ?> Especialidades</title>
		<link href="../styles.css" rel="stylesheet" media="screen">
		<link href="../admin/styles.css" rel="stylesheet" media="screen">
	
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
		<td class="navpic"  align="left"><strong><font ><?php echo pv($app_title); ?></font></strong></td>
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
				<?php
				
				/*
				$sql_referencias = "SELECT R.IDReferencia, R.Numero, PVR.IDPuntoVentaReferencia
										FROM Referencia R, PuntoVentaReferencia PVR, Pendientes P 
										WHERE PVR.IDPuntoVenta = '$IDPuntoVenta' 
										AND PVR.IDReferencia = R.IDReferencia
										AND R.Publicar <> 'N'
										AND PVR.IDPuntoVentaReferencia = P.IDPuntoVentaReferencia
										GROUP BY P.IDPuntoVentaReferencia
										ORDER BY R.Numero";
				*/

				
				/*
				$sql_referencias = "SELECT R.IDReferencia, R.Numero, PVR.IDPuntoVentaReferencia
										FROM Referencia R, PuntoVentaReferencia PVR
										WHERE PVR.IDPuntoVenta = '$IDPuntoVenta' 
										AND PVR.IDReferencia = R.IDReferencia
										AND R.Publicar <> 'N'
										AND (R.Numero not like 'Z%' and R.Numero not like 'O%'
										and R.Numero not like 'R0%' and R.Numero not like 'R1%' and R.Numero not like 'R2%' and R.Numero not like 'R3%' and R.Numero not like 'R4%' and R.Numero not like 'R4%' and R.Numero not like 'R5%' and R.Numero not like 'R6%' and R.Numero not like 'R7%' and R.Numero not like 'R8%'
										and R.Numero not like 'RA%' and R.Numero not like 'RB%' and R.Numero not like 'RC%' and R.Numero not like 'RD%' and R.Numero not like 'RE%' and R.Numero not like 'RF%' and R.Numero not like 'RG%' and R.Numero not like 'RY%'
										) 
										GROUP BY R.Numero
										ORDER BY R.Numero";
				*/						
										
				$sql_referencias = "SELECT R.IDReferencia, R.Numero, PVR.IDPuntoVentaReferencia
										FROM Referencia R, PuntoVentaReferencia PVR
										WHERE PVR.IDPuntoVenta = '$IDPuntoVenta' 
										AND PVR.IDReferencia = R.IDReferencia
										AND R.Publicar <> 'N'
										AND (R.Numero not like 'ZA%' and R.Numero not like 'ZB%' and R.Numero not like 'ZC%' and R.Numero not like 'ZD%'
										and R.Numero not like 'ZE%' and R.Numero not like 'ZF%' and R.Numero not like 'ZG%' and R.Numero not like 'ZH%'
										and R.Numero not like 'ZI%' and R.Numero not like 'ZJ%' and R.Numero not like 'ZK%' and R.Numero not like 'ZL%'
										and R.Numero not like 'ZM%' and R.Numero not like 'ZN%' and R.Numero not like 'ZP%'
										and R.Numero not like 'ZQ%' and R.Numero not like 'ZR%' and R.Numero not like 'ZT%'
										and R.Numero not like 'Z1%' and R.Numero not like 'Z2%' 
										
										and R.Numero not like 'Z4%'
										and R.Numero not like 'Z5%' and R.Numero not like 'Z6%' and R.Numero not like 'Z7%' and R.Numero not like 'Z8%'
										
										and R.Numero not like 'ZU%' and R.Numero not like 'ZV%' 
										and R.Numero not like 'ZW1%' and R.Numero not like 'ZW2%' and R.Numero not like 'ZW3%' and R.Numero not like 'ZW4%' and R.Numero not like 'ZW5%' and R.Numero not like 'ZW6%' and R.Numero not like 'ZW7%' and R.Numero not like 'ZW8%' and R.Numero not like 'ZW9%' and R.Numero not like 'ZWS%'
										and R.Numero not like 'ZS0%' and R.Numero not like 'ZS1%' and R.Numero not like 'ZS2%' and R.Numero not like 'ZS3%' and R.Numero not like 'ZS4%' and R.Numero not like 'ZS5%' and R.Numero not like 'ZS6%' and R.Numero not like 'ZS7%' and R.Numero not like 'ZS8%' and R.Numero not like 'ZS9%'
										
										and R.Numero not like 'O%'
										and R.Numero not like 'ZX%'
										
										and R.Numero not like 'ZY%' and R.Numero not like 'ZZ%' 
										and R.Numero not like 'R0%' and R.Numero not like 'R1%' and R.Numero not like 'R2%' and R.Numero not like 'R3%' and R.Numero not like 'R4%' and R.Numero not like 'R4%' and R.Numero not like 'R5%' and R.Numero not like 'R6%' and R.Numero not like 'R7%' and R.Numero not like 'R8%'
										and R.Numero not like 'RA%' and R.Numero not like 'RB%' and R.Numero not like 'RC%' and R.Numero not like 'RD%' and R.Numero not like 'RE%' and R.Numero not like 'RF%' and R.Numero not like 'RG%' and R.Numero not like 'RY%'
										)
										
										
										 
										
										GROUP BY R.Numero
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
							echo "<option value={$valor['IDPuntoVentaReferencia']}>{$valor['Numero']}</option>";
					?>
				</SELECT><br>
				<br>
				<input type="button" value="Agregar" onclick="if(document.form.Referencias.selectedIndex >= 0)additem(window.opener.document.frm.Referencias,form.Referencias.options[form.Referencias.selectedIndex].value,form.Referencias.options[form.Referencias.selectedIndex].text);">	<input type="button" value="Cerrar" onclick="window.close();">
			</div>
		</form>
</body>
</html>