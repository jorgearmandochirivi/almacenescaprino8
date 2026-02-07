<body> 
<?php 
$TitleMod ="Garantia";

$Table = "Garantia";
$TableJoin = "";
$Key = "IDGarantia";
$MOD = "Garantia";
$m = "Garantia";
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
				$id = insert($frm);
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break;
			case "edit":
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;
			case "update" :
			
				$frm= vars_LOG($HTTP_POST_VARS);
				update($frm);
			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;

			case "insertarcomentario":
				$frm= vars_LOG($HTTP_POST_VARS);
				$sql_inserta_comentario="INSERT INTO ComentarioGarantia (IDGarantia, IDEmpleado, IDEstadoGarantia, Descripcion, FechaComentario, UsuarioTrCr, FechaTrCr) Values ('".$frm[IDGarantia]."','".$ID_Usuario."','".$frm[IDEstadoGarantia]."','".$frm[Descripcion]."',NOW(),'".$ID_Usuario."',NOW())";
				$qry_inserta_comentario=db_query($sql_inserta_comentario);
				
				//actualizo el estado de la garantia
				$sql_actualiza_estado="UPDATE Garantia SET IDEstadoGarantia = '".$frm[IDEstadoGarantia]."' Where IDGarantia = '".$frm[IDGarantia]."'";
				$qry_actualiza_estado=db_query($sql_actualiza_estado);
				envia_comentario_garantia($id,$frm,$ID_Usuario);
				
				window_alert("Comentario agregado con exito ");
				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;


			case "delete" :
				$HTTP_GET_VARS[action]="";
				delete($ID);
			break;
			case "list" :	
			
				if($field == "NumeroFactura")
					$condiciones=" and F.Numerofactura LIKE '%$QryString%'";
				elseif($field == "IDGarantia")
					$condiciones=" and G.IDGarantia = '$QryString'";
				elseif($field == "Cedula")
					$condiciones=" and C.Cedula like '$QryString'";
				elseif($field == "NombreGarantia")
					$condiciones=" and TG.Nombre LIKE '%$QryString%'";
				elseif($field == "EstadoNombre")
					$condiciones=" and EG.Nombre LIKE '%$QryString%'";
					
				if (!empty($_GET[limit1]) && !empty($_GET[limit2]) )	
					$condiciones=" and G.FechaTrCr between '".$_GET[limit1]."' and '".$_GET[limit2]."'";
				
					
				
				
				
			$sql = " SELECT G.*, C.*,TG.Nombre, EG.Nombre 
							 FROM Garantia G, TipoGarantia TG, EstadoGarantia EG,  Cliente C, Factura F
							 WHERE G.IDFactura = F.IDFactura and C.IDCliente = F.IDCliente and
							 	   TG.IDTipoGarantia = G.IDTipoGarantia and EG.IDEstadoGarantia = G.IDEstadoGarantia
							 	   $condiciones
							ORDER BY ".$order_by. " " . $in_order;
			
			
			//$sql = make_qry_string($HTTP_GET_VARS);
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

	GLOBAL $TitleMod,$Table,$MOD,$Key;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' Order by IDGarantia DESC");
	$r = db_fetch_object($qid);

?>
<script>
var Check = new Array('Nombre','Publicar');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
			<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
			<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
		</tr>
</table>
<br>

	
<table cellpadding=1 cellspacing=0 class=bordertable align=center >
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;<?php echo $TitleMod ?> <?php echo $r->$Key ?></td>
	</tr>
	<tr>
	<td>
		
        
        
        
        <form name="frm" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?> onSubmit="return EvaluaReg(this,Check)" <?php }?>>
        <table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
		  <tr class=row2>
						  <td><span class="col1">No. Garantia</span></td>
						  <td align="left"><input type=text size=25 class=input   name=IDGarantia id=IDGarantia value="<?php echo $r->IDGarantia ?>" readonly ></td>
		  </tr>
						<tr class=row2>
						  <td><span class="col1">No. Factura</span></td>
						  <td align="left">
                          <?php 
						  $num_factura=get_field("Factura","Numerofactura","IDFactura",$r->IDFactura); ?>
                          <input type=text size=25 class=input   name=NumeroFactura id=NumeroFactura value="<?php echo $num_factura; ?>" readonly>
                          </td>
		  </tr>
						<tr class=row2>
						  <td>Numero Documento Cliente</td>
						  <td align="left">
                          <?php 
							$id_cliente= get_field("Factura","IDCliente","IDFactura",$r->IDFactura);
							$cedula=get_field("Cliente","Cedula","IDCliente",$id_cliente);
							$nombre=get_field("Cliente","Nombre","IDCliente",$id_cliente)." ".get_field("Cliente","Apellido","IDCliente",$id_cliente);
							$telefono=get_field("Cliente","Telefono","IDCliente",$id_cliente);
						   ?>
                          <input type=text size=25 class=input   name=Cedula id=Cedula value="<?php echo $cedula; ?>" readonly>
                          
                          </td>
		  </tr>
						<tr class=row2>
						  <td>Nombre Cliente</td>
						  <td align="left">
						  <?php $num_factura=get_field("Factura","Numerofactura","IDFactura",$r->IDFactura); ?>
                          <input type=text size=25 class=input   name=Nombre id=Nombre value="<?php echo $nombre; ?>" readonly>                          
                          </td>
		  </tr>
						<tr class=row2>
						  <td>Telefono Cliente</td>
						  <td align="left">
						  <?php $num_factura=get_field("Factura","Numerofactura","IDFactura",$r->IDFactura); ?>
                          <input type=text size=25 class=input   name=Telefono id=Telefono value="<?php echo $telefono; ?>" readonly>                          
                          </td>
		  </tr>
						<tr class=row2>
						  <td>&nbsp;</td>
						  <td align="left">&nbsp;</td>
		  </tr>
						<tr class=row2>
						  <td><span class="col1">Punto de Venta</span></td>
						  <td align="left"><?php echo formpopup("PuntoVenta","Nombre","Nombre","IDPuntoVenta",$r->IDPuntoVenta,"input\" id=\"IDPuntoVenta"); ?></td>
		  </tr>
						<tr class=row2>
						  <td><span class="col1">Fecha </span>Ingreso Garantia</td>
						  <td align="left"><input type=text size=25 class=input   name=FechaTrCr id=FechaTrCr value="<?php echo $r->FechaTrCr ?>" readonly></td>
		  </tr>
						<tr class=row2>
						  <td><span class="col1">Tipo garantia</span></td>
						  <td align="left"><?php echo formpopup("TipoGarantia","Nombre","Nombre","IDTipoGarantia",$r->IDTipoGarantia,"input\" id=\"IDTipoGarantia"); ?></td>
		  </tr>
						<tr class=row2>
						  <td><span class="col1">Estado garantia</span></td>
						  <td align="left"><?php echo formpopup("EstadoGarantia","Nombre","Nombre","IDEstadoGarantia",$r->IDEstadoGarantia,"input\" id=\"IDEstadoGarantia"); ?></td>
		  </tr>
						<tr class=row2>
						  <td><span class="col1">Descripcion</span></td>
						  <td align="left"><textarea name="Descripcion" id="Descripcion" rows="5" cols="50" readonly><?php echo $r->Descripcion; ?></textarea></td>
		  </tr>
						<tr class=row2>
						  <td>&nbsp;</td>
						  <td>&nbsp;</td>
		  </tr>
						<tr>
			<td colspan=2 align=center class=row2>
            	<input type=hidden name=IDGarantia value="<?php echo $r->IDGarantia ?>"><input type=hidden name=UsuarioTrCr value="<?php echo $r->UsuarioTrCr ?>">
				<input type=hidden name=FechaTrCr value="<?php echo $r->FechaTrCr ?>">
				<input type=hidden name=UsuarioTrEd value="<?php echo $r->UsuarioTrEd ?>">
				<input type=hidden name=FechaTrEd value="<?php echo $r->FechaTrEd ?>">
                <input type=hidden name=IDDetalleFactura value="<?php echo $r->IDDetalleFactura ?>">
                <input type=hidden name=IDFactura value="<?php echo $r->IDFactura ?>">
                <input type=hidden name=IDPuntoVenta value="<?php echo $r->IDPuntoVenta ?>">                
				<input type=hidden name=ID value="<?php echo $r->$Key ?>">
				<input type=hidden name=action value=<?php echo $newmode?>>
				<input type=submit name=submit value="<?php echo $submit_caption ?>" class=submit>
			</td>
				</tr>
			</table>
            </form>
            
            
            
		</td>
	</tr>
    
	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;Seguimiento Garantia</td>
	</tr>
	<tr>
	  <td>
	<script>
    var CheckDetalle = new Array('DetalleDescripcion','IDEstadoGarantia');
    </script>
      
      
      <form name="frmdetalle" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data" <?php if($newmode!="delete"){?> onSubmit="return EvaluaReg(this,CheckDetalle)" <?php }?>>
      <table width=500 border=0 cellspacing=1 cellpadding=1 class=texto>
	    <tr class=row2>
	      <td>Estado garantia</td>
	      <td align="left"> &nbsp;<?php echo formpopup("EstadoGarantia","Nombre","Nombre","IDEstadoGarantia","","input\" id=\"IDEstadoGarantia"); ?></td>
	      </tr>
	    <tr class=row2>
	      <td><span class="col1">Descripcion proceso realizado</span></td>
	      <td align="left"><textarea name="Descripcion" id="Descripcion" cols="50" rows="5" ></textarea></td>
	      </tr>
	    <tr class=row2>
	      <td>&nbsp;</td>
	      <td>&nbsp;</td>
	      </tr>
	    <tr>
	      <td colspan=2 align=center class=row2>
          	<input type=hidden name=IDGarantia value="<?php echo $r->IDGarantia ?>">
	        <input type=hidden name=ID value="<?php echo $r->$Key ?>">
	        <input type=hidden name=action value="insertarcomentario">
	        <input type=submit name=submit value="Guardar Proceso" class=submit></td>
	      </tr>
      </table>
	</form>	      
      
      
      
      </td>
    </tr>    

	<tr>
		<td class=maintitle bgcolor=#9daac6>&nbsp;Historial</td>
	</tr>    

	<tr>
		<td>
        
		<table cellpadding="1" cellspacing="2" width="100%" border="0">
        <?php
		 $sql_comentario="SELECT * FROM ComentarioGarantia Where IDGarantia = '".$r->IDGarantia."'";
		 $qry_comentario=db_query($sql_comentario);
		 while($r_comentario=db_fetch_object($qry_comentario)){
		 ?>
        	<tr style="background-color: #E4E4E4">
            	<td align="left" >
                	<b>Fecha:</b>
                </td>
            	<td align="left">
                	<?php echo $r_comentario->FechaTrCr;  ?>
               </td>                         
        	 	<td align="left">
                	<b>Usuario:</b> 
                </td>
            	<td align="left">
                	<?php echo get_field("Empleado","Nombre","IDEmpleado",$r_comentario->IDEmpleado);  ?> 
                </td>
            	<td align="left"><strong>Nuevo Estado</strong></td>
            	<td align="left"><?php echo get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$r_comentario->IDEstadoGarantia);  ?></td>
                
            </tr>
        	<tr>
        	  <td colspan="6" align="left"><?php echo $r_comentario->Descripcion;  ?></td>
       	  </tr>
          <?php } ?>
            
            
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
		Global $TitleMod,$MOD,$Table,$Key,$listar;
	if(empty($sql))
	 	$sql =  "SELECT * FROM $Table ORDER BY $Key DESC";
	 	

		
		$nav = new buildNav;
		$nav->offset = 'offset';
   		$nav->number_type = 'number';
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=10;
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
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
	<tr>
		<td class=nav width=76?>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0> 
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
	</tr>
</table>
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
		<td class=titlemedium  bgcolor=#9daac6><?php  echo $info;?></td>
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
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Numero&nbsp;
						    <?php  if($_GET['order_by']=="Nombre"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href='<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Codigo&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>Cliente</a><a style="color: #3A4F6C;text-decoration: none" href='<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Codigo&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>'>&nbsp;
						    <?php  if($_GET['order_by']=="Codigo"){?><img src="images/<?php echo $img?>" border=0><?php }?></a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5> <a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Publicar&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Producto&nbsp;
						    <?php  if($_GET['order_by']=="Publicar"){?><img src="images/<?php echo $img?>" border=0><?php }?></a> </td>
						<td class=navpic nowrap bgcolor=#DBEAF5><a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&IDPuntoVenta=".$IDPuntoVenta."&QryString=".$_GET['QryString']."&order_by=FechaFacturaBono&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">Factura&nbsp;
						  <?php  if($_GET['order_by']=="FechaFacturaBono"){?>
						  <img src="images/<?php echo $img?>" alt="" border=0>
						  <?php }?>
						  </a></td>
						<td class=navpic nowrap bgcolor=#DBEAF5>Fecha</td>
						<td class=navpic nowrap bgcolor=#DBEAF5>Estado</td>
						<td class=navpic nowrap bgcolor=#DBEAF5>Punto de Venta</td>
					</tr>

<?php while($r = db_fetch_object($result)){
?>
  	
<tr>
						<td align=center valign=middle nowrap width=50 class=row2>
						  &nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap class="<?php echo $class?>"><?php echo $r->IDGarantia; ?></td>
						<td nowrap class="<?php echo $class?>"><?php 
									$id_cliente= get_field("Factura","IDCliente","IDFactura",$r->IDFactura);
									echo get_field("Cliente","Nombre","IDCliente",$id_cliente)." ".get_field("Cliente","Apellido","IDCliente",$id_cliente)?></td>
						<td nowrap class="<?php echo $class?>"><?php
									  $sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVenta."'";
									  $qry_producto=db_query($sql_producto);
									  $r_detalle=db_fetch_object($qry_producto);
									echo "<b>Ref:</b> " . get_field("Referencia","Numero","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
									echo " <b>Talla:</b> " .get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica));
									echo " <b>Nombre:</b> " .get_field("Referencia","Nombre","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));

									
									?></td>
						<td nowrap class="<?php echo $class?>"><?php echo get_field("Factura","Numerofactura","IDFactura",$r->IDFactura); ?></td>
						<td nowrap class="<?php echo $class?>"><?php echo formatofecha(substr($r->FechaTrCr,0,10)) ?></td>
						<td nowrap class="<?php echo $class?>"><?php echo get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$r->IDEstadoGarantia); ?></td>
						<td nowrap class="<?php echo $class?>"><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta); ?></td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=8 nowrap>
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
                <select name="field" id="Buscar por" class="popup">
				  <option value="">Buscar Por</option>
                  <option value="IDGarantia">Numero Seguimiento</option>
				  <option value="NumeroFactura">Numero Factura</option>
				  <option value="Cedula">Cedula</option>
				  <option value="NombreGarantia">Tipo garantia</option>
				  <option value="EstadoNombre">Estado garantia</option>
				  </select>
				  <input type="text" size="20" name="QryString" id="Buscar Por" class="post">
					
					ordenar por 
					<select name="order_by" class="popup">
					  <option value="IDGarantia">Numero Seguimiento</option>
                      <option value="NumeroFactura">Numero Factura</option>
					  <option value="Cedula">Cedula</option>
					  <option value="NombreGarantia">Tipo garantia</option>
					  <option value="EstadoNombre">Estado garantia</option>
				  </select>
				  <BR> de forma <select name="in_order" class="popup">
						<option value="ASC">Ascendente</option>
						<option value="DESC">Descendente</option>
					</select>
					Listar <select name="listar" class="popup">
									<option value="10">10</option>
									<option value="15">15</option>
									<option value="20">20</option>
									<option value="25">25</option>
									<option value="30">30</option>
								</select> 
					<br>
					<input type="hidden" name="mod" value="<?php echo $MOD?>">
					
					<input type="hidden" name="action" value="list">
					
					<input type="submit" name="submit" value="Buscar" class="submit">
				</td>
			</tr>
	</form>
<?php 		
	}//End function filtrar
?>
