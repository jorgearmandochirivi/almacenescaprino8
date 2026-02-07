<?php
	include("config.inc.php3");
	//Encabezado();
	$datos = Verifica_Sesion();
	//$Nombre_Usuario = $datos["Nombre"];
	$Nombre_Usuario = usr_datos($datos["IDUsuario"]);
	$ID_Usuario = $datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
  ?>
<link href="../../default.css" rel="stylesheet" media="screen">
<title>Empleados</title>

<?php
echo "
	<script>
		function usuario(nombre,idusuario){
			opener.document.forms['frm'].".$obj.".value+=' '+nombre+' ';			
			opener.document.forms['frm'].ID".$obj.".value=idusuario;
			window.close();
		}
	</script>
"
  ?>

<table width="511" border="0" cellspacing="2" cellpadding="0">
	<tr>
		<td align="center" class="tituloRP">
			<p><br>
			
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td class="tituloRP" align=center>[ <?php 	for($i=0;$i<25;$i++){
		if($i==14) echo " ] <br> [ ";;?><a class="textoGP" href="?val=<?=$alfabet_array[$i]?>&action=list&obj=<?=$obj?>"><?php echo $alfabet_array[$i];?></a> <?php if($i!=13)<?php | <?php };?><?php }?><a href="../?val=<?=$alfabet_array[$i]?>&action=list&obj=<?=$obj?>" class="textoGP"><?php echo $alfabet_array[$i];?></a> ]</td>
						</tr>
					</table>
					<form name="frm" action="" method="get" onsubmit="return validar(this)">
					<input type="text" name="texto" id="req">
					<input type="hidden" name="obj" value="<?php echo $obj;?>">
					<input type="hidden" name="action" value="list">
              &nbsp;&nbsp;<input type="submit" name="Buscar" value="Buscar"><!--<a href="popusr.php?"><input type="image" src="../../images/buscar.gif" width="54" height="12"></a>-->
					</form>
					<p></p>
				</td>
	</tr>
	<tr>
		<td align="center" class="menuAP">           
<?php

/*******************************************************************************************
		Listar Personal
*******************************************************************************************/

	if(!empty($val)){
		$condicionDirectorio = " AND (";

		$condicionDirectorio .= "Apellidos LIKE '$val%')";
		$titulo_busqueda = "Busqueda por la letra <b>$val</b>";
	}
	else
		$titulo_busqueda = "Seleccione el Usuario";

	if(!empty($texto)){
		$condicionDirectorio = " AND (";
		$condicionDirectorio .= makeboolean("Nombre",$texto);
		$condicionDirectorio .= " OR ".makeboolean("Apellidos",$texto);
		$condicionDirectorio .= "  ) ";

		$titulo_busqueda = "Busqueda por <b>\"$texto\"</b>";
	}

		$sql_directorio="
			SELECT * FROM Empleado 
			WHERE Nivel > 0
			$condicionDirectorio
			ORDER BY Apellidos
		";
		$nav = new buildNav;
		$nav->offset = 'offset';
  		$nav->number_type = 'number';
  		$nav->limit = 10;
  		$nav->execute($sql_directorio,$dblink);
		$total_records =  $this->total_result;
		$rows = $nav->rows;
		$result = $nav->sql_result;
		$row = $offset;
		$startrow = $offset + 1;
		$finalrow = ($row * $nav->limit) + $rows;
		$pages = $nav->show_num_pages('','','&raquo;','','|','class=navvar');   // show pages
		$info = $nav->show_info(); 
		if($rows > 0)
		{
		?>

<table width="90%" border="0" cellspacing="0" cellpadding="2">
<tr>
<td class="tituloAM"bgcolor="#FFFFCC"><?php echo $titulo_busqueda;?></td>
</tr>
<tr>
<td class="tituloAM"bgcolor="#FFFFCC"><?php	print $info; ?></td>
</tr>
</table>					
   <br>
					<table width="90%" border="0" cellspacing="0" cellpadding="2">
					<tr>
							<td class="tituloRelacionados" colspan="3" bgcolor="#FFFFCC">Usuario</td>
						</tr>
					<?php while ($directorio = db_fetch_object($result)){?>
						
						<tr>
							<td nowrap class="textoGP" onmouseover="this.style.cursor='hand'">
								<a href="#" onClick="usuario('<?=$directorio->Nombre?> <?=$directorio->Apellidos?>','<?=$directorio->IDEmpleado?>')" class="menuAP"><b><?=$directorio->Apellidos?></b>,&nbsp;<?=$directorio->Nombre?></a>
							</td>
							<td nowrap class="textoAP"></td>
						</tr>
						<?php }//END while?>
					</table>

<br>
					
					<p align="center" ><br><?php	print $pages; ?></p>
        <?php	}//end if($rows > 0)
        else 
        	{?>
			<p align="center" class=tituloAM>	
				Su busqueda no arrojo resultados
			</p>
  			<?php }?>     
<?php
//****************************************************;?>
		</td>
	</tr>
</table>