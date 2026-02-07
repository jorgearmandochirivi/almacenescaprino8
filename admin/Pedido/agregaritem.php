<head>
<title>CAPRINO</title>
<link rel="stylesheet" href="../styles.css" type="text/css">
</head>
<body>

<?php
include ("../config.inc.php3");
print_form($idsugerido);


function print_form($idsugerido){
Global $numfotos;
?>

<table border=1 cellpadding=1 cellspacing=0 bordercolor=#9DAAC6 align=center style="border-collapse: collapse">
	<tr>
		<td class=titlemedium bgcolor=#9daac6>&nbsp;Seleccione las imagenes</td>
	</tr>
	<tr>
		<td>
			<table width=300 border=0 cellspacing=1 cellpadding=1 class=texto>
				<tr class=row2>
					<td colspan="2">
						<form action="../index.php3?mod=generarpedidos" method="Post" name="frm" onsubmit="opener.location.reload(true);window.close();">
							<div align="right">
								Generar 
								<input type="text" size="21" name="Referencia" id="Referencia" class="post"> 
								<input type="hidden" name="IDSugerido" value="<?=$idsugerido?>">
								<input type="submit" name="submit" value="Generar">
								<input type="hidden" name="action" value="agregaritem">
							</div>
						</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table><?php
} // END function print_form($idsugerido)
?></BODY></HTML></body> 