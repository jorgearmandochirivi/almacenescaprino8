<body> 
<?php
$TitleMod ="PQR";

$Table = "Pqr";
$TableJoin = "";
$Key = "IDPqr";
$MOD = "Pqr";
$m = "Pqr";

require($libdir."filelib.php");

?>

 <?php

$permisos = get_permiso($ID_Usuario,$m,$Table);


if($permisos[0] >= 2)
{
		switch (nvl($action)) {
			case "add" :
				print_form("","insert","Nuevo Registro $TitleMod","Agregar Registro");
			break;
			
			case "insert" :
				$frm= vars_LOG($HTTP_POST_VARS);
				
				
				
				$id_cliente = get_field("Cliente","IDCliente","Cedula",$frm["NumeroDocumento"]);
				if(empty($id_cliente)):
					window_alert("ATENCION: El cliente no existe debe crearlo primero. ");
					echo "
						<script>
							location.href='?mod=".$MOD."&action=add'
						</script>
					";	
					exit;
				else:
					$frm["IDCliente"] = 	$id_cliente;	
				endif;
				
				$sql_max_numero = string;
				$sql_max_numero = "Select MAX(Numero) as NumeroMaximo From Pqr Where 1";
				$result_numero = db_query($sql_max_numero);
				$row_numero = db_fetch_array($result_numero);
				$siguiente_consecutivo = (int)$row_numero["NumeroMaximo"]+1;	
				$frm["Numero"] = $siguiente_consecutivo;
				$frm["IDPuntoVenta"] = $IDPuntoVenta;
				
				//Subir archivo
				$tamano_archivo="10000000";
				$frm = copy_files($frm,$_FILES,"files/pqr/");
				
				$id=insert_width_table($frm,"Pqr","IDPqr");
				
				notificar_nuevo_pqr($id);
				
				
				
				
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
				$frm= vars_LOG($HTTP_POST_VARS);
				
				$id_cliente = get_field("Cliente","IDCliente","Cedula",$frm["NumeroDocumento"]);
				if(empty($id_cliente)):
					window_alert("ATENCION: El cliente no existe debe crearlo primero. ");
					echo "
						<script>
							location.href='?mod=".$MOD."&action=edit&id=".$frm[$Key]."'
						</script>
					";	
				else:
					$frm["IDCliente"] = 	$id_cliente;	
				endif;
				
				
					$notificar_cliente=$_POST["NotificarCliente"];
					if (!empty($_POST[Cuerpo])){	
						if ($notificar_cliente=="S"):
							envia_respuesta_cliente($frm[IDPqr],$_POST[Cuerpo]);	
						endif;	
						
						$sql_inserta_respuesta="INSERT INTO Detalle_Pqr (IDPqr, IDEmpleado, IDPqrEstado, Fecha, Respuesta,UsuarioTrCr, FechaTrCr)
												VALUES ('".$frm[IDPqr]."','".$frm[IDEmpleado]."','".$frm[IDPqrEstado]."','".date("Y-m-d")."','".$_POST[Cuerpo]."','Admin',NOW())";
						db_query($sql_inserta_respuesta);
					}
					
					//Si se reasigna el pqr envio el mail de confirmación
					if($frm["IDMotivoPqrAnt"]!=$frm["IDMotivoPqr"]){
						notificar_nuevo_pqr(SIMNet::reqInt("id"));						
					}
					
				
				//Subir imagenes
				//$frm = copy_files($frm,$_FILES);
				
				
				update($frm);
			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;
			case "delete" :
				$HTTP_GET_VARS[action]="";
				delete($ID);
			break;
			case "list" :	
			
				if(!empty($_GET[NumeroDocumento])):
					$condiciones .=" and C.Cedula LIKE '%".$_GET[NumeroDocumento]."%'";
					$tabla_join = ",Cliente C";
					$condicion_join = " and C.IDCliente = Pqr.IDCliente";
				endif;	
				
				if(!empty($_GET[Nombre])):
					$condiciones .=" and ( C.Nombre LIKE '%".$_GET[Nombre]."%' or C.Apellido LIKE '%".$_GET[Nombre]."%')";
					$tabla_join = ",Cliente C";
					$condicion_join = " and C.IDCliente = Pqr.IDCliente";
				endif;	
					
				if(!empty($_GET[Numero]))
					$condiciones.=" and Pqr.Numero = '".$_GET[Numero]."'";
	
				if(!empty($_GET[IDTipoPqr]))
					$condiciones.=" and Pqr.IDTipoPqr = '".$_GET[IDTipoPqr]."'";
					
				if(!empty($_GET[IDMotivoPqr]))
					$condiciones.=" and Pqr.IDMotivoPqr = '".$_GET[IDMotivoPqr]."'";
					
				if(!empty($_GET[IDFuentePqr]))
					$condiciones.=" and Pqr.IDFuentePqr = '".$_GET[IDFuentePqr]."'";
					
				if(!empty($_GET[IDPqrEstado]))
					$condiciones.=" and Pqr.IDPqrEstado = '".$_GET[IDPqrEstado]."'";
					
				if(!empty($_GET[TipoProducto]))
					$condiciones.=" and G.TipoProducto = '".$_GET[TipoProducto]."'";
					
				
				$sql = " SELECT Pqr.*
							 FROM Pqr ".$tabla_join."
							 WHERE 1 ". $condicion_join . $condiciones ."
							Group By IDPqr
							ORDER BY IDPqr DESC";
			
			list_r($sql);
			break;
			default : 
					list_r();
			break;
		
		} // End switch

}//end if(permisos[0] > 2)
else
	echo Mensaje_Info("No tiene Permisos Suficientes","col2");



/*******************************************************************************************
		funtcion Print_form
*******************************************************************************************/
function print_form($id="",$newmode,$title,$submit_caption) {

	GLOBAL $TitleMod,$Table,$MOD,$Key,$ID_Usuario;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' ");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('IDMotivoPqr','IDFuentePqr','NumeroDocumento','IDTipoPqr','Descripcion','Fecha','IDPqrEstado');
</script>
<br>
<form name="frm" action="<?=$PHP_SELF?>" method="post"  enctype="multipart/form-data" <?php if($newmode!="delete"){?>onsubmit="return EvaluaReg2(this,Check);disable(this);"<?php } ?>>
	
<table cellpadding=1 cellspacing=0 class=bordertable align=center >
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		<table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
						<tr class=row2>
			<td> Numero </td>
			<td><input type=text size=25 class=input   name=Numero id=Numero value="<?=$r->Numero ?>" readonly> </td>
			<td>Fecha</td>
			<td><input type=text size=25 class=input   name=Fecha id=Fecha value="<?php if($newmode=="update") echo $r->Fecha; else echo date("Y-m-d"); ?>" readonly>
			 </td>
			            </tr>
          <tr class=row2>
            <td>Documento Cliente</td>
            <td><?php 						
						  $nombre_cliente = get_field("Cliente","Nombre","IDCliente",$r->IDCliente). " " . get_field("Cliente","Apellido","IDCliente",$r->IDCliente); 
						  $telefono_cliente = get_field("Cliente","Telefono","IDCliente",$r->IDCliente). " " . get_field("Cliente","Celular","IDCliente",$r->IDCliente); 
						  $correo_cliente = get_field("Cliente","EMail","IDCliente",$r->IDCliente); 
						  
                          $documento_cliente = get_field("Cliente","Cedula","IDCliente",$r->IDCliente); 
						  ?>
              <input type=text size=25 class=input   name=NumeroDocumento id=NumeroDocumento value="<?=$documento_cliente ?>" required>              
              <input type=hidden size=25 class=input   name=IDCliente id=IDCliente value="<?=$r->IDCliente ?>"></td>
		                  <td>Datos Cliente</td>
		                  <td><?php echo "<br>" . $nombre_cliente . "<br> Telefono: " .  $telefono_cliente . " <br>" .  $correo_cliente; ?></td>
          </tr>
          <tr class=row2>
            <td>Motivo</td>
            <td colspan="3"><?php echo formpopup("MotivoPqr","Nombre","Nombre","IDMotivoPqr",$r->IDMotivoPqr,"input\" id=\"Motivo Pqr"); ?></td>
          </tr>
          <tr class=row2>
            <td>Tipo</td>
            <td><?php echo formpopup("TipoPqr","Nombre","Nombre","IDTipoPqr",$r->IDTipoPqr,"input\" id=\"Tipo Pqr"); ?></td>
            <td>Fuente</td>
            <td><?php echo formpopup("FuentePqr","Nombre","Nombre","IDFuentePqr",$r->IDFuentePqr,"input\" id=\"Fuente Pqr"); ?></td>
          </tr>
          <tr class=row2>
						  <td>Estado</td>
						  <td><?php 
						    if(!empty($r->IDPqrEstado))
							  	echo get_field("PqrEstado","Nombre","IDPqrEstado",$r->IDPqrEstado);
							else
								echo "Activo";	
								
						  ?>
                          <input type="hidden" name="IDPqrEstado" id="IDPqrEstado" value="<?php if($newmode=="update") echo $r->IDPqrEstado; else echo "1";  ?>">
						  </td>
						  <td>Archivo</td>
						  <td><?php if (!empty($r->Archivo1)): ?>
						    <a target="_blank" href="<?php echo "files/pqr/". $r->Archivo1; ?>" ><?php echo $r->Archivo1; ?></a>
						    <!--<a href="?mod=<?php echo $MOD; ?>&action=delfoto&campo=Foto1&id=<?php echo $r->$Key ?>&idnot="><img src='images/trash.gif' border='0'></a>-->
						    <input type="hidden" name="Archivo1" id="Archivo1" class=input value="<?php echo $r->Archivo1; ?>">
						    <?php else: ?>
						    <input type="file" name="Archivo1" id="Archivo1" class=input>
						    <?php endif; ?></td>
		  </tr>
          <tr class=row2>
            <td>Solucion Final</td>
            <td><?php echo get_field("PqrSolucion","Nombre","IDPqrSolucion",$r_comentario->IDPqrSolucion) ?></td>
            <td>Creado por</td>
            <td><?php echo  $datos_empleado = get_field("Empleado","Nombre","IDEmpleado",$r->IDEmpleado). " " . get_field("Empleado","Apellidos","IDEmpleado",$r->IDEmpleado); 
						$id_pto_vta = get_field("Empleado","IDPuntoVenta","IDEmpleado",$r->IDEmpleado);
				  echo " - " . $id_pto_vta = get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_pto_vta);
			?></td>
          </tr>
          <tr class=row2>
            <td colspan="4">Descripcion</td>
          </tr>
          <tr class=row2>
            <td colspan="4"><textarea name="Descripcion" class="" title="Descripcion" id="Descripcion" cols="80" rows="8" <?php if($newmode=="update") echo "readonly"; ?> required ><?php echo $r->Descripcion; ?></textarea></td>
          </tr>
            
            <?php if($newmode=="update"): ?>
						<!--
                        <tr class=row2>
						  <td colspan="4">Agregar Respuesta</td>
		  </tr>
          -->
		
        <!--
        <tr class=row2>
						  <td colspan="4"><textarea name="Cuerpo" class="" title="Cuerpo" id="Cuerpo" cols="80" rows="8"><?php echo $r->Cuerpo; ?></textarea></td>
		  </tr>
          -->
          <!--
						<tr class=row2>
						  <td colspan="4">
                          <input type="checkbox" name="NotificarCliente" id="NotificarCliente" <?php if($r->IDArea!="0"){  ?> checked="checked" <?php } ?> value="S" />
                                          <b>Notificar v&iacute;a email al Cliente la respuesta</b>
                          </td>
		  </tr>
          -->
						<tr class=row2>
						  <td colspan="4">
                          
                          
                          <table cellpadding="1" cellspacing="2" width="100%" border="0">
        <?php
		  $sql_comentario="SELECT * FROM Detalle_Pqr Where IDPqr = '".$r->IDPqr."' Order by IDDetallePqr DESC";
		 $qry_comentario=db_query($sql_comentario);
		 while($r_comentario=db_fetch_object($qry_comentario)){
		 ?>
        	<tr style="background-color: #E4E4E4">
            	<td align="left" >
                	<b>Fecha Respuesta:</b>
                </td>
            	<td align="left">
                	<?php echo $r_comentario->Fecha;  ?>
               </td>                         
        	 	<td align="left">
                	<b>Usuario:</b> 
                </td>
            	<td align="left">
                	<?php echo get_field("Empleado","Nombre","IDEmpleado",$r_comentario->IDEmpleado);  ?> 
                </td>
            	<td align="left"><strong>Nuevo Estado</strong></td>
            	<td align="left"><?php echo get_field("PqrEstado","Nombre","IDPqrEstado",$r_comentario->IDPqrEstado);  ?></td>
                
            </tr>
        	<tr>
        	  <td colspan="6" align="left"><?php echo $r_comentario->Respuesta;  ?></td>
       	  </tr>
          <?php } ?>
            
            
        </table>
                          
                          
                          </td>
		  </tr>
          
          <?php endif; ?>
          
						<tr>
			<td colspan=4 align=center class=row2><input type=hidden name=IDPqr value="<?=$r->IDPqr ?>">
            	<input type=hidden name=UsuarioTrCr value="<?=$r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?=$r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?=$r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?=$r->FechaTrEd ?>">
                <input type=hidden name=IDMotivoPqrAnt value="<?=$r->IDMotivoPqr ?>">                
				<input type=hidden name=ID value="<?php echo $r->$Key ?>">
                <input type=hidden name=IDEmpleado value="<?php echo $ID_Usuario ?>">
				<input type=hidden name=action value=<?=$newmode?>>
                <?php if($newmode=="insert"): ?>
					<input type=submit name=submit value="<?php echo $submit_caption ?>" class=submit>
                <?php endif; ?>    
                    
			</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
<?php
}// End function print_form()

/*******************************************************************************************
		funcion Listar
*******************************************************************************************/
	function list_r($sql=""){
		Global $TitleMod,$MOD,$Table,$Key,$listar,$IDPuntoVenta;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table Where IDPuntoVenta = '".$IDPuntoVenta."' ORDER BY Numero Desc";
	 	
		
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=50;
   		$nav->execute($sql,$dblink);
		$total_records =  $nav->total_result;
		$rows = $nav->rows;
		$result = $nav->sql_result;
		$row = $offset;
		$startrow = $offset + 1;
		$finalrow = ($row * $nav->limit) + $rows;
	
	 	$pages = $nav->show_num_pages('&laquo;','&laquo; prev','&raquo;','next &raquo;','|','class=navvar');   // show pages
		
		$info = $nav->show_info(); 

 if($_GET['in_order']=="ASC" || $_GET['in_order']==""){
								$img="down.png";
								$order="DESC";
							}else if($_GET['in_order']=="DESC"){
								$img="up.png";
								$order="ASC";
							}
							
							?>

<?php
		if($rows > 0){
?>		
<br>
<table width=500 cellpadding=0 cellspacing=0 align=center class=bordertable>
	<tr>
		<td class=titlemedium bgcolor=#9daac6><b>Listar <?php echo $TitleMod ?></b></td>
	</tr>
	<?php filtrar();?>	
	<tr>
		<td class=titlemedium  bgcolor=#9daac6><?php echo $info;;?></td>
	</tr>
	<tr>
		<td class=texto bgcolor=#DBEAF5 colspan= nowrap>
		<?php
			print $pages;
		?>
		</td>
	</tr>
	<tr><td>
<table width=100% border=0 cellspacing=1 cellpadding=0>
<tr>
						<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Numero&nbsp;
					    <?php if($_GET['order_by']=="Nombre"){ ?><img src="images/<?php echo $img;?>" border=0><?php } ?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Descripcion&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Fecha</a><a style="color: #3A4F6C;text-decoration: none" href='<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Descripcion&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>&nbsp;
					    <?php if($_GET['order_by']=="Descripcion"){ ?><img src="images/<?php echo $img;?>" border=0><?php } ?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Tipo&nbsp;
					    <?php if($_GET['order_by']=="Publicar"){ ?><img src="images/<?php echo $img;?>" border=0><?php } ?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Fuente</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Cliente</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Motivo</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Estado</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Alerta</td>
						<td align=center  class=rowform valign=middle bgcolor=#DBEAF5 width=69>Eliminar</td>
					</tr>

<?php while($r = db_fetch_object($result)){
?>
  	
<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
	&nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap class=row1><?php echo $r->Numero ?></td>
						<td nowrap class=row1><?php echo $r->Fecha ?></td>
						<td nowrap class=row1><?php echo get_field("TipoPqr","Nombre","IDTipoPqr",$r->IDTipoPqr); ?></td>
						<td nowrap class=row1><?php echo get_field("FuentePqr","Nombre","IDFuentePqr",$r->IDFuentePqr); ?></td>
						<td nowrap class=row1><?php echo get_field("Cliente","Nombre","IDCliente",$r->IDCliente) . " ".get_field("Cliente","Apellido","IDCliente",$r->IDCliente); ?></td>
						<td nowrap class=row1><?php echo substr(get_field("MotivoPqr","Nombre","IDMotivoPqr",$r->IDMotivoPqr),0,20); ?></td>
						<td nowrap class=row1><?php echo get_field("PqrEstado","Nombre","IDPqrEstado",$r->IDPqrEstado); ?></td>
						<td nowrap class=row1>
                        		<?php
										// Consulto si ya se le dio una primera respuesta
										$sql_respuesta = "Select * From Detalle_Pqr Where IDPqr = '".$r->IDPqr."' Order by Fecha Desc Limit 1";
										$result_respuesta = db_query($sql_respuesta);
										$total_respuesta = db_num_rows($result_respuesta);
									
										$fecha = $r->Fecha;
										$nuevafecha = strtotime ( '+5 day' , strtotime ( $fecha ) ) ;
										$fecha_vencimiento = date ( 'Y-m-d' , $nuevafecha );											
										$hoy=date("Y-m-d");
										$diferencia_dias=strtotime ( $fecha_vencimiento ) - strtotime ( $hoy );
										$dias=intval($diferencia_dias/60/60/24) ;						
										if ($dias <0 && (int)$total_respuesta<=0){ 
											//echo "Vencida hace " . abs($dias) . " dias";	?>	
											<img src="../admin/images/campanaalerta.jpg" width="15" height="15" > 
                                            <?php
											echo "<br><span style='color: red;'> Vencido " . abs($dias) . " dias";
										}		
									
										
									
									?>
                        </td>
						<td align=center valign=middle nowrap width=60 class=row2>
	&nbsp;&nbsp;<a href='<?php echo "?mod=$MOD&action=del&id="; echo $r->$Key; ?>'><img src='images/trash.gif' border='0'></a>	
</td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=10 nowrap>
	<?php
		print $pages;
		?>
</td>
</tr>		
</table></td>
</tr>
</table>	

<?php
}// End if$rows
else
	echo "<br><br><span class=subtitle><b>No existen registros en  $TitleMod </b></span>";
}// Enf function list()				

/*******************************************************************************************
		funcion filtrar
*******************************************************************************************/
	function filtrar(){
	Global $dblink,$total_records,$row,$numtoshow,$MOD;
?>
	<form name="frm" action="./" method="get" onSubmit="return valbuscar(document.frm)">
	<tr>
				<td class="rowform" align="center" colspan=8>
					<table width="100%" border="0">
					  <tbody>
					    <tr>
					      <td>Numero</td>
					      <td><input type="text" size="20" name="Numero" id="Numero" class="post"></td>
					      <td>Tipo</td>
					      <td><?php echo formpopup("TipoPqr","Nombre","Nombre","IDTipoPqr",$r->IDTipoPqr,"input\" id=\"Tipo Pqr"); ?></td>
					      <td>Fuente</td>
					      <td><?php echo formpopup("FuentePqr","Nombre","Nombre","IDFuentePqr",$r->IDFuentePqr,"input\" id=\"Fuente Pqr"); ?></td>
					      <td>Estado</td>
					      <td><?php echo formpopup("PqrEstado","Nombre","Nombre","IDPqrEstado",$r->IDPqrEstado,"input\" id=\"Estado Pqr"); ?></td>
				        </tr>
					    <tr>
					      <td>Motivo</td>
					      <td colspan="3"><?php echo formpopup("MotivoPqr","Nombre","Nombre","IDMotivoPqr",$r->IDMotivoPqr,"input\" id=\"Motivo Pqr"); ?></td>
					      <td>Documento Cliente</td>
					      <td><input type="text" size="20" name="NumeroDocumento" id="NumeroDocumento" class="post"></td>
					      <td>Nombre Cliente</td>
					      <td><input type="text" size="20" name="Nombre" id="Nombre" class="post"></td>
				        </tr>
					    <tr>
					      <td colspan="8" align="center"><input type="hidden" name="mod" value="<?=$MOD?>">
                            <input type="hidden" name="action" value="list">
                          <input type="submit" name="submit" value="Buscar" class="submit"></td>
				        </tr>
				      </tbody>
				  </table>
					
				  
				</td>
			</tr>
	</form>
<?php
	}//End function filtrar
?>
