<script>

function evalua_valor(valor){
		if( valor == "10" ) //Autorizacion especial
		{
			document.getElementById('divtipofinalizacion').style.display = 'block';
		}
		else{
			if(valor == "7"){
				document.getElementById('divmetodoenvio').style.display = 'block';
			}
			else{
				document.getElementById('divtipofinalizacion').style.display = 'none';
				document.getElementById('divmetodoenvio').style.display = 'none';
			}

		}



}

function evalua_valor_especial(valor){
		if( valor == "58888888" ) //Autorizacion especial
		{
			document.getElementById('divnotacredito').style.display = 'block';
		}
		else{
			document.getElementById('divnotacredito').style.display = 'none';
		}
}




function valida_datos(){


	var estado_garantia=document.getElementById("IDEstadoGarantia").value;
	var tipo_autorizacion=document.getElementById("IDTipoFinalizacionGarantia").value;
	var numeronotacredito=document.getElementById("NumeroNotaCredito").value;
	var descripcion=document.getElementById("Descripcion").value;
	var rol=document.getElementById("Rol").value;
	var nivel=document.getElementById("Nivel").value;

	//Valido el orden logico de cambios de estado
	var estado_garantia_anterior = document.getElementById("IDEstadoGarantiaAnt").value;
	var estado_garantia_nuevo = document.getElementById("IDEstadoGarantia").value;


	if (estado_garantia_nuevo==5 || estado_garantia_nuevo==10){ // Enviar a reparacion
		if (
			//document.getElementById('TipoRemonta').checked ||
			document.getElementById('tmpContrafuerte').value!="" ||
			document.getElementById('tmpTipoCuero').value!="" ||
			document.getElementById('tmpTipoPlantilla').value!="" ||
			document.getElementById('tmpTipoCremallera').value!="" ||
			document.getElementById('tmpTipoDespegue').value!="" ||
			document.getElementById('tmpTipoCambrion').value!="" ||
			document.getElementById('tmpTipoTacon').value!="" ||
			document.getElementById('tmpTipoCerco').value!="" ||
			document.getElementById('tmpTipoCardado').value!="" ||
			document.getElementById('tmpTipoSuela').value!="" ||
			document.getElementById('tmpTipoGuarnicion').value!="" ||
			document.getElementById('tmpTipoPuntera').value!="" ||
			document.getElementById('tmpTipoHerraje').value!="" ||
			document.getElementById('tmpTipoOtro').value !=""
		){
			var validacion_estado=1;
		}
		else{
			alert("Debe primero seleccionar la Identificacion de la Causa de la Garantia para cambiar a este estado y Realizar los cambios, por favor verifique");
			//window.location.reload(true);
			return false;
		}




		if (nivel!=0 && rol!=7 && rol!=8 ){ //solo superadmin o coordinador pueden cambiar a este estado
			alert("No tiene los permisos para cambiar a este estado");
			return false;
		}
	}




	//Valido que las autorizacion de par nuevo dev dinero etc sea hecha por los roles permitidos
	switch(tipo_autorizacion) {
					case "1":
							if (nivel!=0 && rol!=8 && rol!=9 && rol!=10 ){
								alert("No tiene permisos para realizar la autorizacion");
								return false;
							}

					break;
					case "2":
							if (nivel!=0 && rol!=7 && rol!=8  ){
								alert("No tiene permisos para realizar la autorizacion");
								return false;
							}
					break;
					case "3":
							if (nivel!=0 && rol!=8 && rol!=10  ){
								alert("No tiene permisos para realizar la autorizacion");
								return false;
							}
					break;
					case "4":
							if (nivel!=0 && rol!=8 && rol!=9 && rol!=10  ){
								alert("No tiene permisos para realizar la autorizacion");
								return false;
							}
					break;
					case "5":
							if (nivel!=0 && rol!=7 && rol!=8 ){
								alert("No tiene permisos para realizar la autorizacion");
								return false;
							}
					break;


				}




	if(estado_garantia==10 && tipo_autorizacion==""){
		alert("Debe seleccionar el tipo de autorizacion que se realiza");
		return false;
	}

	var requiere_devolucion=0;
	if(frmdetalle.RequiereDevolucion[1].checked || frmdetalle.RequiereDevolucion[0].checked){
		requiere_devolucion=1;
	}

	var requiere_notacredito=0;
	if(frmdetalle.RequiereNotaCredito[1].checked || frmdetalle.RequiereNotaCredito[0].checked){
		requiere_notacredito=1;
	}


	if(estado_garantia==10 && requiere_devolucion=="0"){
		alert("Debe seleccionar si se requiere devolucion del producto");
		return false;
	}

	if(estado_garantia==10 && requiere_notacredito=="0"){
		alert("Debe seleccionar si se requiere nota credito");
		return false;
	}



	if(tipo_autorizacion==5 && notacredito==""){
		alert("Debe digitar la nota credito");
		return false;
	}


	if(estado_garantia==""){
		alert("Debe seleccionar el estado de la garantia");
		return false;
	}


	if (descripcion==""){
		alert("Digite la descripcion del proceso realizado");
		return false;

	}





if (estado_garantia_anterior != estado_garantia_nuevo ){
				switch(estado_garantia_anterior) {
					case "1":
							//if (estado_garantia_nuevo==5 || estado_garantia_nuevo==10 ){
							if (estado_garantia_nuevo==10 ){
							 return true;
							}
							else{
							 alert("No puede cambiar la garantia al estado seleccionado debe seguir el orden de procedimiento.");
							 return false;
							}
					break;
					case "2":
						//if (estado_garantia_nuevo==3 || estado_garantia_nuevo==4 || estado_garantia_nuevo==5 || estado_garantia_nuevo==7 || estado_garantia_nuevo==10 ){
							if (estado_garantia_nuevo==3 || estado_garantia_nuevo==4 || estado_garantia_nuevo==5 || estado_garantia_nuevo==10 ){
							return true;
						}
						else{
							alert("No puede cambiar la garantia al estado seleccionado debe seguir el orden de procedimiento.");
							return false;
						}
					break;
					case "3":
						if (estado_garantia_nuevo==2 || estado_garantia_nuevo==4 || estado_garantia_nuevo==5 || estado_garantia_nuevo==6 || estado_garantia_nuevo==7 || estado_garantia_nuevo==10 ){
							return true;
						}
						else{
							alert("No puede cambiar la garantia al estado seleccionado debe seguir el orden de procedimiento.");
							return false;
						}
					break;
					case "4":
						if (estado_garantia_nuevo==2 || estado_garantia_nuevo==3 || estado_garantia_nuevo==5 || estado_garantia_nuevo==6 || estado_garantia_nuevo==7 || estado_garantia_nuevo==10 ){
							return true;
						}
						else{
							alert("No puede cambiar la garantia al estado seleccionado debe seguir el orden de procedimiento.");
							return false;
						}
					break;
					case "5":
						if (estado_garantia_nuevo==6 || estado_garantia_nuevo==7 || estado_garantia_nuevo==10 ){
							return true;
						}
						else{
							alert("No puede cambiar la garantia al estado seleccionado debe seguir el orden de procedimiento.");
							return false;
						}
					break;
					case "6":
							if (estado_garantia_nuevo==7 || estado_garantia_nuevo==10){
							return true;
						}
						else{
							alert("No puede cambiar la garantia al estado seleccionado debe seguir el orden de procedimiento.");
							return false;
						}
					break;

					case "7":
						if (estado_garantia_nuevo==10){
							return true;
						}
						else{
							alert("No puede cambiar el estado debe seguir el orden de procedimiento.");
							return false;
						}
					break;
					case "8":
						if (estado_garantia_nuevo==10){
							return true;
						}
						else{
							alert("No puede cambiar el estado debe seguir el orden de procedimiento.");
							return false;
						}
					break;

					case "9":
						if (estado_garantia_nuevo==10 || estado_garantia_nuevo==12 || estado_garantia_nuevo==5 ){
							return true;
						}
						else{
							alert("No puede cambiar el estado debe seguir el orden de procedimiento.");
							return false;
						}
					break;


					case "10":
						if (estado_garantia_nuevo==2 || estado_garantia_nuevo==3 || estado_garantia_nuevo==4 || estado_garantia_nuevo==5 || estado_garantia_nuevo==6 || estado_garantia_nuevo==7 || estado_garantia_nuevo==12){
							return true;
						}
						else{
							alert("No puede cambiar la garantia al estado seleccionado debe seguir el orden de procedimiento.");
							return false;
						}
					break;

					case "11":
						if (estado_garantia_nuevo==12){
							return true;
						}
						else{
							alert("No puede cambiar la garantia al estado seleccionado debe seguir el orden de procedimiento.");
							return false;
						}
					break;



					default:
						return true;
				}
		}
		else{
			alert("Debe seleccionar un estado diferente al actual");
			return false;
		}





}
</script>


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


//envia_nuevo_garantia("951");

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
				// Actuali el valor de la remonta y la factura que son los unicos datos que se pueden modificar




				$sql_actualiza_remonta="Update Garantia
										Set ValorRemonta = '".$frm[ValorRemonta]."',
											NumeroFacturaRemonta = '".$frm[NumeroFacturaRemonta]."',
											NumeroNotaCredito = '".$frm[NumeroNotaCredito]."',
											FechaNotaCreditoContabilidad = '".$frm[FechaNotaCreditoContabilidad]."',
											NotaCreditoAplicada = '".$frm[NotaCreditoAplicada]."',
											TipoContrafuerte = '".$frm[TipoContrafuerte]."',
											TipoCuero = '".$frm[TipoCuero]."',
											TipoPlantilla = '".$frm[TipoPlantilla]."',
											TipoCremallera = '".$frm[TipoCremallera]."',
											TipoDespegue = '".$frm[TipoDespegue]."',
											TipoCambrion = '".$frm[TipoCambrion]."',
											TipoTacon = '".$frm[TipoTacon]."',
											TipoCerco = '".$frm[TipoCerco]."',
											TipoCardado = '".$frm[TipoCardado]."',
											TipoSuela = '".$frm[TipoSuela]."',
											TipoGuarnicion = '".$frm[TipoGuarnicion]."',
											TipoPuntera = '".$frm[TipoPuntera]."',
											TipoHerraje = '".$frm[TipoHerraje]."',
											TipoOtro = '".$frm[TipoOtro]."',
											FechaSalidaAlmacen ='".$frm[FechaSalidaAlmacen]."',
											FechaEntradaAlmacen ='".$frm[FechaEntradaAlmacen]."',
											NumeroFacturaRestauracion ='".$frm[NumeroFacturaRestauracion]."',
											FechaEntregaCliente ='".$frm[FechaEntregaCliente]."',
											Referencia = '".$frm["NombreReferencia"]."'
											Where IDGarantia = '".$frm[ID]."' ";
				db_query($sql_actualiza_remonta);
				print_form($frm[ID],"update","Actualizar $TitleMod","Realizar Cambios");
				//update($frm);
			break;
			case "del":
				print_form($id,"delete","Eliminar $TitleMod","Remover Registro");
			break ;

			case "insertarcomentario":
				$frm= vars_LOG($HTTP_POST_VARS);


				if (!empty($frm[Descripcion])  || !empty($txt_cambio_fecha) ){

					if(!empty($frm[MetodoEnvio])):
						$frm[Descripcion] .= " Metodo Envio: " . $frm[MetodoEnvio];
					endif;

					if(!empty($frm[EmpleadoEnvio])):
						$frm[Descripcion] .= " Empleado Envio: " . $frm[EmpleadoEnvio];
					endif;


					$sql_inserta_comentario="INSERT INTO ComentarioGarantia (IDGarantia, IDEmpleado, IDEstadoGarantia, IDTipoFinalizacionGarantia, Descripcion, FechaComentario, UsuarioTrCr, FechaTrCr) Values ('".$frm[IDGarantia]."','".$ID_Usuario."','".$frm[IDEstadoGarantia]."', '".$frm[IDTipoFinalizacionGarantia]."','".$frm[Descripcion] . "\r" .$txt_cambio_fecha ."',NOW(),'".$ID_Usuario."',NOW())";
					$qry_inserta_comentario=db_query($sql_inserta_comentario);

					//actualizo el estado de la garantia
					$sql_actualiza_estado="UPDATE Garantia SET IDEstadoGarantia = '".$frm[IDEstadoGarantia]."', IDTipoFinalizacionGarantia = '".$frm[IDTipoFinalizacionGarantia]."', RequiereDevolucion = '".$frm[RequiereDevolucion]."', RequiereNotaCredito = '".$frm[RequiereNotaCredito]."', NumeroNotaCredito = '".$frm[NumeroNotaCredito]."'  Where IDGarantia = '".$frm[IDGarantia]."'";
					$qry_actualiza_estado=db_query($sql_actualiza_estado);

					// Envio notificacion
					//envia_comentario_garantia($id,$frm,$ID_Usuario);

					// Envio notificacion soli si el estado es autorizacion especial e segunda, dotacion o par nuevo
					if($frm["IDTipoFinalizacionGarantia"]==1 || $frm["IDTipoFinalizacionGarantia"]==2 || $frm["IDTipoFinalizacionGarantia"]==6 || $frm["IDTipoFinalizacionGarantia"]==8):
						envia_comentario_garantia_almacen($id,$frm,$ID_Usuario);
					endif;


					//Si se requiere nota credito envio correo de notificacion
					if ($frm["IDTipoFinalizacionGarantia"]==1 || $frm["IDTipoFinalizacionGarantia"]==2 || $frm["IDTipoFinalizacionGarantia"]==5):
						envia_nota_credito ($frm[IDGarantia]);
					endif;

					window_alert("Comentario agregado con exito ");

				}


				print_form($id,"update","Actualizar $TitleMod","Realizar Cambios");
			break ;


			case "delete" :
				$HTTP_GET_VARS[action]="";
				delete($ID);
			break;
			case "list" :

				if(!empty($_GET[NumeroFactura])):
					$condiciones .=" and F.Numerofactura LIKE '%".$_GET[NumeroFactura]."%'";
					$tabla_join = ",Factura F";
					$condicion_join = " and G.IDFactura = F.IDFactura";
				endif;

				if(!empty($_GET[IDGarantia]))
					$condiciones.=" and G.IDGarantia = '".$_GET[IDGarantia]."'";

				if(!empty($_GET[TipoRegistro]))
					$condiciones.=" and G.TipoRegistro = '".$_GET[TipoRegistro]."'";

				if(!empty($_GET[IDEstadoGarantia]))
					$condiciones.=" and G.IDEstadoGarantia = '".$_GET[IDEstadoGarantia]."'";

				if(!empty($_GET[IDPuntoVenta]))
					$condiciones.=" and G.IDPuntoVenta = '".$_GET[IDPuntoVenta]."'";

				if(!empty($_GET[CantidadVeces]))
					$condiciones.=" and G.CantidadVeces = '".$_GET[CantidadVeces]."'";

				if(!empty($_GET[TipoProducto]))
					$condiciones.=" and G.TipoProducto = '".$_GET[TipoProducto]."'";

				if(!empty($_GET[NumeroReferencia])):
					$sql_ref="SELECT IDReferencia FROM Referencia WHERE Numero like '%".$_GET[NumeroReferencia]."%'";
					$r_ref=db_query($sql_ref);
					while($row_ref=db_fetch_array($r_ref)){
						$array_ref[]=$row_ref["IDReferencia"];
					}
					if(count($array_ref)>0){
						$id_ref_buscar=implode(",",$array_ref);
					}
					else{
						$id_ref_buscar="0";
					}
					//$id_referencia = get_field("Referencia","IDReferencia","Numero",$_GET[NumeroReferencia]);
					//$condiciones.=" and G.IDReferencia = '".$id_referencia."'";
				$condiciones.=" and ( G.IDReferencia in (".$id_ref_buscar.") or  Referencia = '".$_GET[NumeroReferencia]."') ";

				//Consulto las garantias con factura con esa ref
				$sql_ref="SELECT IDGarantia
									FROM Garantia G, Factura F, Detallefactura DF
									WHERE G.IDFactura=F.IDFactura and F.IDFactura = DF.IDFactura and
												and ";


				endif;

				if(!empty($_GET[Alerta])):
					switch($_GET[Alerta]):
						case "V":
						  $condiciones .= " and G.IDEstadoGarantia not in (9,8,10,12)";
						  $condiciones .= " and FechaEstimadaEntrega < CURDATE()";
						break;
						case "PV":
						  $condiciones .= " and G.IDEstadoGarantia not in (9,8,10,12)";
						  $condiciones .= " and FechaEstimadaEntrega BETWEEN CURDATE() and DATE_ADD( CURDATE() , INTERVAL 5 DAY )";
						break;
						case "NC":
							$condiciones .= " and G.RequiereNotaCredito='S' and G.NumeroNotaCredito=''";
						break;
						case "PF":
							$condiciones.=" and G.IDEstadoGarantia = '11'";
						break;
					endswitch;

				endif;


				if (!empty($_GET[limit1]) && !empty($_GET[limit2]) )
					$condiciones.=" and G.FechaTrCr between '".$_GET[limit1]."' and '".$_GET[limit2]."'";



				$sql = " SELECT G.*
							 FROM Garantia G ".$tabla_join."
							 WHERE 1 ". $condicion_join . $condiciones ."
							Group By IDGarantia
							ORDER BY IDGarantia DESC";


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

	GLOBAL $TitleMod,$Table,$MOD,$Key,$ID_Usuario;
	$qid = db_query(" SELECT * FROM $Table WHERE $Key = '$id' Order by IDGarantia DESC");
	$r = db_fetch_object($qid);



?>
<script>
var Check = new Array('Nombre','Publicar');
</script>
<table cellspacing='0' cellpadding='2' border='0' align='center' width='100%' bgcolor='#FFFFFF'>
		<tr>
			<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
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






        <table class="forumline" width="100%" cellspacing="1" border="0" align="center">
	<tr>
	<td>
		<table width=100% border=0 cellspacing=1 cellpadding=1 class="texto forumline"  >

                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="center" class=rowform><table width="100%" border="0">
                    <tr>
                      <td>REGISTRO NUMERO</td>
                      <td colspan="3" class="row2"><?php echo $r->IDGarantia; ?></td>
                      </tr>
                    <tr>
                      <td>ESTADO</td>
                      <td colspan="3" class="row2">
                      <span style="color:#F00">
                      <?php echo strtoupper(get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$r->IDEstadoGarantia)); ?>
                      </span>
                      </td>
                    </tr>
                    <tr>
                      <td>TIPO AUTORIZACION</td>
                      <td colspan="3" class="row2"><span style="color:#F00"><?php echo strtoupper(get_field("TipoFinalizacionGarantia","Nombre","IDTipoFinalizacionGarantia",$r->IDTipoFinalizacionGarantia)); ?>
                        <?php if ($r->RequiereDevolucion=="S") echo "(Se requiere devolver producto no aceptado  a fabrica)"?>
                        </span></td>
                    </tr>
                    <tr>
                      <td>Fecha Estimada para resolver garantia</td>
                      <td colspan="3" class="row2"><span class="<?php echo $class?>"><?php echo formatofecha(substr($r->FechaEstimadaEntrega,0,10)) ?></span></td>
                    </tr>
                    <tr>
                      <td>Almac&eacute;n Compra</td>
                      <td class="row2"><?php

					  if ($r->TipoFactura=="facturabono"):
					  	$sql_datos_factura=db_query("Select * From FacturaBono Where IDFacturaBono = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
					  else:
						  $sql_datos_factura=db_query("Select * From Factura Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
					  endif;

					  $r_factura_compra=db_fetch_array($sql_datos_factura);



					  $id_punto_venta=$r->IDPuntoVentaFactura;
					  echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$id_punto_venta);
					  ?></td>
                      <td>Tel&eacute;fono Almacen Compra</td>
                      <td class="row2"><?php
					  echo get_field("PuntoVenta","Telefono","IDPuntoVenta",$id_punto_venta);
					  ?></td>
                    </tr>
                    <tr>
                      <td>Almac&eacute;n Registra Garantia</td>
                      <td class="row2"><?php
					  echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta);
					  ?></td>
                      <td>Tel&eacute;fono Almacen Registra Garantia</td>
                      <td class="row2"><?php
					  echo get_field("PuntoVenta","Telefono","IDPuntoVenta",$r->IDPuntoVenta);
					  ?></td>
                    </tr>
                    <tr>
                      <td>Cliente o Proveedor</td>
                      <td class="row2"><?php

									if ($r->TipoRegistro=="Reproceso"){
										$id_proveedor=get_field("Referencia","IDProveedor","IDReferencia",$r->IDReferencia);
										echo get_field("Proveedor","Nombre","IDProveedor",$id_proveedor);

									}
									elseif($r->Mayorista=="S" || $r->Dotacion=="S"){
										echo $r->NombreMayorista;
									}
									else{
									  $id_cliente=$r_factura_compra[IDCliente];
									  echo get_field("Cliente","Nombre","IDCliente",$id_cliente) ." ". get_field("Cliente","Apellido","IDCliente",$id_cliente) .  " - " . get_field("Cliente","Cedula","IDCliente",$id_cliente);
									}

									  ?>


                                      </td>
                      <td>Tel&eacute;fono Cliente</td>
                      <td class="row2">&nbsp;<?php echo get_field("Cliente","Telefono","IDCliente",$id_cliente); ?></td>
                    </tr>
                    <tr>
                      <td>Ciudad</td>
                      <td class="row2"><?php echo $r->CiudadMayorista; ?></td>
                      <td>Direccion Mayorista</td>
                      <td class="row2"><?php echo $r->DireccionMayorista; ?></td>
                    </tr>
                    <tr>
                      <td>Telefono Cliente</td>
                      <td class="row2"><?php echo $r->Telefono; ?></td>
                      <td>Celular Cliente</td>
                      <td class="row2"><?php echo $r->Celular; ?></td>
                    </tr>
                    <tr>
                      <td>Factura de Venta N&ordm;</td>
                      <td class="row2"><?php
					  if ($r->TipoFactura=="facturabono"):
						  echo $r_factura_compra[NumeroFacturaBono] . " (bono) ";
					  else:
					  	echo get_field("Factura","NumeroFactura","IDFactura",$r->IDFactura);
					  endif;
						?></td>
                      <td>Fecha Compra</td>
                      <td class="row2"><?php
					  if ($r->TipoFactura=="facturabono"):
					  	echo substr(get_field("FacturaBono","FechaFacturaBono","IDFacturaBono",$r->IDFactura),0,10);
					  else:
					  	echo substr(get_field("Factura","FechaFactura","IDFactura",$r->IDFactura),0,10);
					  endif;
					?></td>
                    </tr>

										<?php if(!empty($r->IDDetalleCambio)){
											$array_cambio=explode("|",$r->IDDetalleCambio);
											?>
										<tr>
                      <td>Cambio N&ordm;</td>
                      <td class="row2"><?php
					  						echo $array_cambio[0];
						?></td>
                      <td>Fecha Cambio</td>
                      <td class="row2"><?php
					  					echo substr(get_field("Cambio","FechaCambio","IDCambio",$array_cambio[0]),0,10);
					?></td>
                    </tr>
									<?php } ?>


                    <tr>
                      <td>Fecha Reclamo</td>
                      <td colspan="3" class="row2"><?php echo $r->FechaTrCr; ?></td>
                      </tr>
                    <tr>
                      <td>Producto</td>
                      <td colspan="3">
                      <?php
					  // datos producto
					  if ($r->TipoFactura=="facturabono"):
					  	$sql_producto="select * from DetalleFacturaBono Where IDDetalleFacturaBono='".$r->IDDetalleFactura."' and IDFacturaBono = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'";
					  else:
					    $sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'";
					  endif;

					  $qry_producto=db_query($sql_producto);
					  $r_detalle=db_fetch_object($qry_producto);
					  ?>

                      <table width="100%" border="0">
                        <tr>
                          <td>Referencia</td>
                          <td>Talla</td>
                          <td>Tipo</td>
                        </tr>
                        <tr bgcolor="#dfe3e7" class="texto forumline">
                          <td align="left" class="<?php echo $class?>">&nbsp;
                            <?php

							if ($r->TipoRegistro=="Reproceso" || $r->Mayorista=="S" || $r->Dotacion=="S"){
									echo $nombre_ref= get_field("Referencia","Numero","IDReferencia",$r->IDReferencia);
									$ReferenciaG=$r->IDReferencia;
									$NombreReferenciaG=$nombre_ref;

						  }
						  elseif(!empty($r->IDDetalleFacturaBono)){

							  $array_bono_detalle=explode("|",$r->IDDetalleFacturaBono);
									if (count($array_bono_detalle)>0):
										$sql_bono=db_query("Select * From DetalleFacturaBono Where IDDetalleFacturaBono	 = '".$array_bono_detalle[1]."' and IDFacturaBono = '".$array_bono_detalle[0]."'");
										$r_bono=db_fetch_array($sql_bono);

								   		$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"])));
										$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
										$NombreReferenciaG=$nombre_referencia;
										$ReferenciaG=$id_referencia_item;
									endif;
						  }
						  elseif(empty($r->IDDetalleCambio)){ ?>
                            <?php
									 $id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))) ;
									if ($id_referencia_item==160){ // Cuando son excedentes consulto la referencia de la compra
										$sql_facturabono=db_query("Select * from FacturaBono Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
										$r_facturabono=db_fetch_array($sql_facturabono);
										if (!empty($r_facturabono[IDFacturaBono])){
											$sql_detallefacturabono=db_query("Select * from DetalleFacturaBono Where IDFacturaBono = '".$r_facturabono[IDFacturaBono]."'");
											$r_detallefacturabono=db_fetch_array($sql_detallefacturabono);
											$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));
											$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"]));
										}
									  }

									 $id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
									 echo $nombre_ref=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
									 $NombreReferenciaG=$nombre_ref;
									 $ReferenciaG=$id_referencia_item;

									 ?>
                            <?php } else{ // ES UNA REFERNCIA DE ALGUN CAMBIO Y LA CONSULTO
							   		$array_cambio_detalle=explode("|",$r->IDDetalleCambio);
									if (count($array_cambio_detalle)>0):
										$sql_cambio=db_query("Select * From DetalleCambio Where IDDetalleCambio = '".$array_cambio_detalle[1]."' and IDCambio = '".$array_cambio_detalle[0]."'");
										$r_cambio=db_fetch_array($sql_cambio);

								   		$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"])));
										$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"]));
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
										$NombreReferenciaG=$nombre_referencia;
										$ReferenciaG=$id_referencia_item;
									endif;

								}


								if($r->Mayorista=="S" || $r->Dotacion=="S"):
										echo $r->ColorMayorista;
								endif;


								 ?></td>



                          <td align="left" class="<?php echo $class?>">&nbsp;

                          <?php  if ($r->TipoRegistro=="Reproceso" || $r->Mayorista=="S" || $r->Dotacion=="S"){
									echo $nombre_talla=get_field("Talla","Nombre","IDTalla",$r->IDTalla);

						  } elseif ($nombre_talla!=""){ echo $nombre_talla; } else { ?>
		                          <?php echo $nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica))?>
                           <?php } ?>

                          </td>
                          <td align="left" class="<?php echo $class?>"><?php  if ($r->TipoRegistro=="Reproceso"){
									$tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$r->IDReferencia);;
									echo get_field("TipoReferencia","Descripcion","IDTipoReferencia",$tipo_ref);

						  }
						   elseif($r->Mayorista=="S" || $r->Dotacion=="S"){
								echo  $r->TipoProductoMayorista;
						  }

						  else{ ?>
                            <?php echo get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref); ?>
                            <?php } ?></td>
                        </tr>
                      </table></td>
                    </tr>
                  </table></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Garantia por</td>
                  <td align="left" class="row2"><input type="radio" name="CantidadVeces"  value="1" <?php if($r->CantidadVeces=="1") { echo "checked"; } ?>   disabled class="" />
				 <label for="radio4" class="css-label radGroup2">Primera vez</label>
  <input type="radio" name="CantidadVeces"  value="2" <?php if($r->CantidadVeces=="2") { echo "checked"; } ?> disabled class=""  />
<label for="radio4" class="css-label radGroup2">Segunda Vez</label>
<input type="radio" name="CantidadVeces"  value="3" <?php if($r->CantidadVeces=="3") { echo "checked"; } ?> disabled class=""  />
<label for="radio4" class="css-label radGroup2">Tercera Vez</label>
</td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Clasificacion</td>
                  <td align="left" class="row2">

                    <input type="radio" class="" name="TipoProducto"  value="C" <?php if($r->TipoProducto=="C") { echo "checked"; } ?> disabled />
                    <label for="radio4" class="css-label radGroup2">Es producto de Caprino</label>
                    <input type="radio" class="" name="TipoProducto"  value="T" <?php if($r->TipoProducto=="T") { echo "checked"; } ?> disabled />
                    <label for="radio4" class="css-label radGroup2">Es producto de tercero</label>


                    </td>
                </tr>

                <?php  if ($r->TipoRegistro=="Reproceso"){ ?>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Proveedor</td>
                  <td align="left" class="row2">
                  	<?php echo get_field("Proveedor","Nombre","IDProveedor",$r->IDProveedor); ?>
                  </td>
                </tr>
                <?php }?>


                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Numero Orden Produccion</td>
                  <td align="left" class="row2"><?php echo $r->NumeroOrdenProduccion; ?></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td width="18%" align="left" class=rowform>Registro de </td>
                  <td width="82%" align="left" class="row2">
                  	<input type="radio"  name="TipoRegistro" class="TipoRegistroGarantia " value="Garantia" <?php if($r->TipoRegistro=="Garantia") { echo "checked"; } ?>   disabled  />
                     <label for="radio4" class="css-label radGroup2">Garant&iacute;a</label>

										<!--
                    <input type="radio"  name="TipoRegistro" class="TipoRegistroGarantia " value="Servicio" <?php if($r->TipoRegistro=="Servicio") { echo "checked"; } ?> disabled  />
                    <label for="radio4" class="css-label radGroup2">Servicios </label>
									-->
									<input type="radio"  name="TipoRegistro" class="TipoRegistroGarantia " value="Servicio" <?php if($r->TipoRegistro=="Restauracion") { echo "checked"; } ?> disabled  />
									<label for="radio4" class="css-label radGroup2">Restauracion </label>

                    <input type="radio"  name="TipoRegistro" class="TipoRegistroGarantia " value="Reproceso" <?php if($r->TipoRegistro=="Reproceso") { echo "checked"; } ?> disabled  />
                    <label for="radio4" class="css-label radGroup2">Reprocesos </label>




                  <?php if($r->TipoRegistro=="Servicio"){ ?>
                              <div id="divreproceso">
                              <table width="100%" cellpadding="2" cellspacing="1">
                                <tr>
                                  <td>Remonta</td>
                                  <td><input type="checkbox" name="Remonta" value="Rem" <?php if($r->Remonta=="S"){ echo "checked"; } ?> disabled /></td>
                                  <td>Valor</td>
                                  <td>$
                                    <input type="text" class="input" name="ValorRemonta" value="<?php echo $r->ValorRemonta; ?>" size="10" /></td>
                                    <td width="14%">&nbsp;</td>
                                    <td width="5%">&nbsp;</td>
                                    <td width="12%">&nbsp;</td>
                                    <td width="27%">&nbsp;</td>
                                </tr>
                                <tr>
                                  <td colspan="4">El cliente acepta pagar el valor de la remonta</td>
                                  <td colspan="2">Si
                                    <input type="radio" name="PagoRemonta" value="S" <?php if($r->PagoRemonta=="S"){ echo "checked"; } ?> disabled  /></td>
                                  <td>Numero Factura Remonta</td>
                                  <td><input type="text" class="input" name="NumeroFacturaRemonta" id="NumeroFacturaRemonta" value="<?php echo $r->NumeroFacturaRemonta; ?>" size="10" /></td>
                                </tr>
                              </table>

                  </div>
                  <?php } ?>


									<?php if($r->TipoRegistro=="Restauracion"){ ?>
															<div id="divrestauracion">
																<table width="100%" cellpadding="2" cellspacing="1">
							                    <tr>
							                      <td width="8%">
																			<table width="100%" cellpadding="2" cellspacing="1" border="0">
							                        <tr>
							                          <td colspan="2" bgcolor="#D5F0DA"> B&aacute;sica <?php if($r->Basica=="S") echo "SI"; else echo "NO";  ?></td>
							                          <td bgcolor="#D5F0DA">Valor $</td>
							                          <td bgcolor="#D5F0DA"><?php echo number_format($r->ValorBasica);  ?></td>
							                          <td bgcolor="#D5ECF0" width="20%" colspan="2">Premium <?php if($r->Premium=="S") echo "SI"; else echo "NO";  ?></td>
							                          <td bgcolor="#D5ECF0" width="15%">Valor $ </td>
							                          <td bgcolor="#D5ECF0" width="20%"><?php echo number_format($r->ValorPremium);  ?></td>
							                        </tr>
							                        <tr>
							                          <td colspan="5">El cliente acepta pagar el valor de la restauraci&oacute;n</td>
							                          <td colspan="1">
																					<?php if($r->PagoRestauracion=="S") echo "SI"; else echo "NO";  ?>
																				</td>
							                          <td>Nro factura con la que pag&oacute:</td>
							                          <td><input type="text" name="NumeroFacturaRestauracion" id="NumeroFacturaRestauracion" value="<?php echo $r->NumeroFacturaRestauracion; ?>"  /></td>
							                        </tr>
							                      </table></td>
							                    </tr>
							                  </table>

									</div>
									<?php } ?>


                  </td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform>DESCRIPCION DEL ESTADO EN EL QUE SE RECIBE EL PRODUCTO</td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform>
                  <table width="100%"  cellpadding="2" cellspacing="3">
                    <tr>
                      <td><strong>CUERO</strong></td>
                      <td class="row2">
                        <input type="checkbox" class="" name="CueroPelado" value="S" <?php if ($r->CueroPelado=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Pelados</label>
                      </td>
                      <td><strong>SUELA</strong></td>
                      <td class="row2"><input type="checkbox" class="" name="SuelaDesgastada" value="S" <?php if ($r->SuelaDesgastada=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Desgastada</label></td>
                      <td class="row2">&nbsp;</td>
                      <td><strong>OTROS</strong></td>
                      <td class="row2"><input type="checkbox" class="" name="Ojetes" value="S" <?php if ($r->Ojetes=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Ojetes cedidos</label></td>
                      <td class="row2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td class="row2"><input type="checkbox" class="" name="CueroManchado" value="S" <?php if ($r->CueroManchado=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Manchados</label></td>
                      <td>&nbsp;</td>
                      <td class="row2"><input type="checkbox" class="" name="ViraDanada" value="S" <?php if ($r->ViraDanada=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Vira Da&ntilde;ada</label></td>
                      <td class="row2">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td class="row2"><input type="checkbox" class="" name="Punteras" value="S" <?php if ($r->Punteras=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Punteras hundidas</label></td>
                      <td class="row2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td class="row2"><input type="checkbox" class="" name="CueroRayado" value="S" <?php if ($r->CueroRayado=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Rayados</label></td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td colspan="2">
                      <textarea name="OtroDescripcion" id="OtroDescripcion" placeholder="Otro" rows="2" disabled><?php echo $r->OtroDescripcion ?></textarea>

                      </td>
                    </tr>
                    <tr>
                      <td height="27"><strong>FORRO</strong></td>
                      <td class="row2"><input type="checkbox" class="" name="ForroManchado" value="S" <?php if ($r->ForroManchado=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Manchado</label></td>
                      <td><strong>TAC&Oacute;N</strong></td>
                      <td class="row2"><input type="checkbox" class="" name="TaconDesgastado" value="S" <?php if ($r->TaconDesgastado=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Desgastado</label></td>
                      <td class="row2">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td class="row2"><input type="checkbox" class="" name="ForroRoto" value="S" <?php if ($r->ForroRoto=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Roto</label></td>
                      <td>&nbsp;</td>
                      <td class="row2"><input type="checkbox" class="" name="TaconPelado" value="S" <?php if ($r->TaconPelado=="S"){ echo "checked"; } ?> disabled />
                        <label for="CueroPelado" class="css-label2">Pelado/Rayado</label></td>
                      <td class="row2">&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform>IDENTIFICACION DE LA CAUSA DE LA GARANTIA </td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform><table width="100%"  cellpadding="2" cellspacing="3">
                    <tr>
                      <td class="row2"><input type="checkbox" class="" name="TipoContrafuerte" id="TipoContrafuerte" value="S" <?php if ($r->TipoContrafuerte=="S"){ echo "checked"; } ?>  />
                        <label for="TipoContrafuerte" class="css-label2">Contrafuerte</label></td>
                      <td class="row2"><input type="hidden" name="tmpContrafuerte" id="tmpContrafuerte" value="<?php echo $r->TipoContrafuerte; ?>"></td>
                      <td ><input type="checkbox" class="" name="TipoDespegue" id="TipoDespegue" value="S" <?php if ($r->TipoDespegue=="S"){ echo "checked"; } ?>  />
                        <label for="TipoDespegue" class="css-label2">Despegue</label></td>
                      <td ><input type="hidden" name="tmpTipoDespegue" id="tmpTipoDespegue" value="<?php echo $r->TipoDespegue; ?>"></td>
                      <td class="row2"><input type="checkbox" class="" name="TipoCardado" id="TipoCardado" value="S" <?php if ($r->TipoCardado=="S"){ echo "checked"; } ?>  />
                        <label for="TipoCardado" class="css-label2">Cardado</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoCardado" id="tmpTipoCardado" value="<?php echo $r->TipoCardado; ?>"></td>
                    </tr>
                    <tr>
                      <td class="row2"><input type="checkbox" class="" name="TipoCuero" id="TipoCuero" value="S" <?php if ($r->TipoCuero=="S"){ echo "checked"; } ?>  />
                        <label for="TipoCuero" class="css-label2">Cuero</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoCuero" id="tmpTipoCuero" value="<?php echo $r->TipoCuero; ?>"></td>
                      <td ><input type="checkbox" class="" name="TipoCambrion" id="TipoCambrion" value="S" <?php if ($r->TipoCambrion=="S"){ echo "checked"; } ?>  />
                        <label for="TipoCambrion" class="css-label2">Cambrion</label></td>
                      <td><input type="hidden" name="tmpTipoCambrion" id="tmpTipoCambrion" value="<?php echo $r->TipoCambrion; ?>"></td>
                      <td class="row2"><input type="checkbox" class="" name="TipoSuela" id="TipoSuela" value="S" <?php if ($r->TipoSuela=="S"){ echo "checked"; } ?>  />
                        <label for="TipoSuela" class="css-label2">Suela Rota</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoSuela" id="tmpTipoSuela" value="<?php echo $r->TipoSuela; ?>"></td>
                    </tr>
                    <tr>
                      <td class="row2"><input type="checkbox" class="" name="TipoPlantilla" id="TipoPlantilla" value="S" <?php if ($r->TipoPlantilla=="S"){ echo "checked"; } ?>  />
                        <label for="TipoPlantilla" class="css-label2">Plantilla estructural</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoPlantilla" id="tmpTipoPlantilla" value="<?php echo $r->TipoPlantilla; ?>"></td>
                      <td >
                        <input type="checkbox" class="" name="TipoTacon" id="TipoTacon" value="S" <?php if ($r->TipoTacon=="S"){ echo "checked"; } ?>  />
                        <label for="TipoTacon" class="css-label2">Tacon</label></td>
                      <td ><input type="hidden" name="tmpTipoTacon" id="tmpTipoTacon" value="<?php echo $r->TipoTacon; ?>"></td>
                      <td class="row2"><input type="checkbox" class="" name="TipoGuarnicion" id="TipoGuarnicion" value="S" <?php if ($r->TipoGuarnicion=="S"){ echo "checked"; } ?>  />
                        <label for="TipoGuarnicion" class="css-label2">Guarnicion</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoGuarnicion" id="tmpTipoGuarnicion" value="<?php echo $r->TipoGuarnicion; ?>"></td>
                    </tr>
                    <tr>
                      <td height="27" class="row2"><input type="checkbox" class="" name="TipoCremallera" id="TipoCremallera" value="S" <?php if ($r->TipoCremallera=="S"){ echo "checked"; } ?>  />
                        <label for="TipoCremallera" class="css-label2">Cremallera</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoCremallera" id="tmpTipoCremallera" value="<?php echo $r->TipoCremallera; ?>"></td>
                      <td ><input type="checkbox" class="" name="TipoCerco" id="TipoCerco" value="S" <?php if ($r->TipoCerco=="S"){ echo "checked"; } ?>  />
                        <label for="TipoCerco" class="css-label2">Cerco</label></td>
                      <td><input type="hidden" name="tmpTipoCerco" id="tmpTipoCerco" value="<?php echo $r->TipoCerco; ?>"></td>
                      <td class="row2"><input type="checkbox" class="" name="TipoPuntera" id="TipoPuntera" value="S" <?php if ($r->TipoPuntera=="S"){ echo "checked"; } ?>  />
                        <label for="TipoPuntera" class="css-label2">Puntera</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoPuntera" id="tmpTipoPuntera" value="<?php echo $r->TipoPuntera; ?>"></td>
                    </tr>
                    <tr>
                      <td class="row2"><input type="checkbox" class="" name="TipoHerraje" id="TipoHerraje" value="S" <?php if ($r->TipoHerraje=="S"){ echo "checked"; } ?>  />
                        <label for="TipoHerraje" class="css-label2">Herraje</label></td>
                      <td class="row2"><input type="hidden" name="tmpTipoHerraje" id="tmpTipoHerraje" value="<?php echo $r->TipoHerraje; ?>"></td>
                      <td class="row2">&nbsp;</td>
                      <td class="row2"><strong>OTROS</strong></td>
                      <td class="row2"><textarea name="TipoOtro" id="TipoOtro" placeholder="Otro" rows="2" cols="30"><?php echo $r->TipoOtro ?></textarea>
                        <input type="hidden" name="tmpTipoOtro" id="tmpTipoOtro" value="<?php echo $r->TipoOtro; ?>"></td>
                      <td class="row2">&nbsp;</td>
                      </tr>
                  </table></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform>DESCRIPCION DETALLADA DE LA SITUACION
                  </td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class="row2"><span class="row2">
                    <?php echo $r->Descripcion ?>
                  </span></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform>COMENTARIOS CLIENTE
                  </td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class="row2"><span class="row2">
                    &nbsp; <?php echo $r->ComentarioCliente ?>
                  </span></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="left" class=rowform><table width="100%" border="0">
                    <tr>
                      <td><?php
					  // datos producto
					  $sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."'";					  $qry_producto=db_query($sql_producto);
					  $r_detalle=db_fetch_object($qry_producto);
					  ?>
                        <table width="100%" border="0">
                          <tr>
                            <td>Referencia</td>
                            <td>Talla</td>
                          </tr>
                          <tr bgcolor="#dfe3e7" class="texto forumline">
                            <td align="left" class="<?php echo $class?>">&nbsp;<?php echo $nombre_ref; ?></td>
                            <td align="left" class="<?php echo $class?>">&nbsp;<?php echo $nombre_talla ?></td>
                          </tr>
                        </table></td>
                    </tr>
                  </table></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Atendido por:</td>
                  <td align="left" class="row2"><?php
					  if($r->Mayorista=="S" || $r->Dotacion=="S"):
					  	echo $r->IngresadoPor;
					  else:
					  	echo get_field("Empleado","Nombre","IDEmpleado",$r->IDEmpleado) . " " . get_field("Empleado","Apellidos","IDEmpleado",$r->IDEmpleado);
					  endif;
					  ?></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Numero Guia o  Persona a quien entrega</td>
                  <td align="left" class="row2"><input type="text" class="input" name="NumeroGuia" id="NumeroGuia" value="<?php echo $r->NumeroGuia; ?>" disabled ></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Fecha Salida Almac&eacute;n</td>
                  <td align="left" class="row2"><span class="col2">
                    <input type="input" name="FechaSalidaAlmacen" id="FechaSalidaAlmacen" class="tbox" value="<?php if ($r->FechaSalidaAlmacen!="0000-00-00") { echo $r->FechaSalidaAlmacen; }?>" >
                    <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaSalidaAlmacen,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//-->
								</script>
                  </span></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Fecha Entrada Almac&eacute;n</td>
                  <td align="left" class="row2"><span class="col2">
                    <input type="input" name="FechaEntradaAlmacen" id="FechaEntradaAlmacen" class="tbox" value="<?php if ($r->FechaEntradaAlmacen!="0000-00-00") { echo $r->FechaEntradaAlmacen; }?>"   >
                    <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaEntradaAlmacen,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//-->
								</script>
                  </span></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>Fecha en la que se entrega el producto al cliente..</td>
                  <td align="left" class="row2"><span class="col2">
                    <input type="input" name="FechaEntregaCliente" id="FechaEntregaCliente" class="tbox" value="<?php if ($r->FechaEntregaCliente!="0000-00-00") { echo $r->FechaEntregaCliente; }?>"  >
                    <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaEntregaCliente,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//-->
								</script>
                  </span></td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>FECHA ENVIO CONTABILIDAD NOTA CREDITO/DEBITO</td>
                  <td align="left" class="row2">

                  <input type="input" name="FechaNotaCreditoContabilidad" id="FechaNotaCreditoContabilidad" class="tbox" value="<?php if ($r->FechaNotaCreditoContabilidad!="0000-00-00") { echo $r->FechaNotaCreditoContabilidad; }?>" >
                    <script language="JavaScript1.2">
									<!--
										if (!document.layers)
											document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.FechaNotaCreditoContabilidad,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
									//-->
								</script>

                  </td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>NUMERO NOTA CREDITO/DEBITO</td>
                  <td align="left" class="row2">
                  <input type="text" class="input" name="NumeroNotaCredito" id="NumeroNotaCredito" value="<?php echo $r->NumeroNotaCredito; ?>">
                  </td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td align="left" class=rowform>NOTA CREDITO APLICADA PROVEEDOR</td>
                  <td align="left" class="row2">

                  <input type="radio" name="NotaCreditoAplicada" <?php if($r->NotaCreditoAplicada=="S") echo "checked"; ?> value="S">Si
         		 <input type="radio" name="NotaCreditoAplicada" <?php if($r->NotaCreditoAplicada=="N") echo "checked"; ?> value="N">No

                  </td>
                </tr>
                <tr bgcolor="#dfe3e7">
                  <td colspan="2" align="center" class=rowform><span class="row2">
                    <input type=hidden name=IDGarantia value="<?php echo $r->IDGarantia ?>">
                    <input type=hidden name=UsuarioTrCr value="<?php echo $r->UsuarioTrCr ?>">
                    <input type=hidden name=FechaTrCr value="<?php echo $r->FechaTrCr ?>">
                    <input type=hidden name=UsuarioTrEd value="<?php echo $r->UsuarioTrEd ?>">
                    <input type=hidden name=FechaTrEd value="<?php echo $r->FechaTrEd ?>">
                    <input type=hidden name=IDDetalleFactura value="<?php echo $r->IDDetalleFactura ?>">
                    <input type=hidden name=IDFactura value="<?php echo $r->IDFactura ?>">
                    <input type=hidden name=IDPuntoVenta value="<?php echo $r->IDPuntoVenta ?>">
                    <input type=hidden name=ID value="<?php echo $r->$Key ?>">
                    <input type=hidden name="IDEstadoGarantiaAnt" id="IDEstadoGarantiaAnt" value="<?php echo $r->IDEstadoGarantia ?>">
                    <input type=hidden name=action value=<?php echo $newmode?>>
										<input type=hidden name=NombreReferencia value=<?php echo $NombreReferenciaG?>>

                    <!-- <input type=submit name=submit value="<?php echo $submit_caption ?>" class=submit> -->
                    <input type=submit name=submit value="<?php echo $submit_caption ?>" class=submit>
                  </span></td>
                </tr>


			<tr>
			  <td colspan=2 align=center class="col2list">&nbsp;</td>
			  </tr>
			</table>
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


      <form name="frmdetalle" action="<?php echo $PHP_SELF?>" method="post" enctype="multipart/form-data"  onSubmit="return valida_datos(this)">
      <table width="100%" border=0 cellspacing=1 cellpadding=1 class=texto>
	    <tr class=row2>
	      <td>Estado garantia</td>
	      <td align="left"> &nbsp;
          <select name="IDEstadoGarantia" id="IDEstadoGarantia" onChange="evalua_valor(this.value)">
          	<option value="">[Seleccione]</option>
            <?php
			$sql_estados=db_query("Select * from EstadoGarantia Where IDEstadoGarantia in ( 5, 6, 7, 10, 12)");
			while($row_estado=db_fetch_array($sql_estados)){
				?>
                <option value="<?php echo $row_estado["IDEstadoGarantia"]; ?>"><?php echo $row_estado["Nombre"]; ?></option>
			<?php
			}
			?>
          </select>

          <div id="divtipofinalizacion" style="display:none"  >
          <br>SE AUTORIZA:
          <?php //echo formpopup("TipoFinalizacionGarantia","Nombre","IDTipoFinalizacionGarantia","IDTipoFinalizacionGarantia","","input\" id=\"IDTipoFinalizacionGarantia",""); ?>
         <select name="IDTipoFinalizacionGarantia" id="IDTipoFinalizacionGarantia" class="input" onChange="evalua_valor_especial(this.value)">
         		  <option value="">[Seleccione]</option>
				  <?php
                  $sql_finalizacion=db_query("Select * From TipoFinalizacionGarantia Where 1 and Publicar = 'S' Order By Nombre");
                  while($row_finalizacion=db_fetch_array($sql_finalizacion)){
                  ?>
                    <option value="<?php echo $row_finalizacion['IDTipoFinalizacionGarantia']; ?>"><?php echo $row_finalizacion['Nombre']; ?></option>
                  <?php
                  }
                  ?>
          </select>


          <br>
          SE REQUIERE DEVOLUCION DE PRODUCTO A FABRICA?:
          <input type="radio" name="RequiereDevolucion" value="S">Si
          <input type="radio" name="RequiereDevolucion" value="N">No
          <br>
          REQUIERE NOTA CREDITO?
          <input type="radio" name="RequiereNotaCredito" value="S">
          Si
          <input type="radio" name="RequiereNotaCredito" value="N">No
         <!--<br>NUMERO NOTA CREDITO/DEBITO:
          <input type="text" name="NumeroNotaCredito" id="NumeroNotaCredito">
-->
          </div>

           <div id="divmetodoenvio" style="display:none"  >
          <br>METODO ENVIO:
          <br>
          <input type="radio" name="MetodoEnvio" class="btnmetodoenvio" value="Conductor">Conductor<br>
          <input type="radio" name="MetodoEnvio" class="btnmetodoenvio" value="Moto">Moto<br>
          <input type="radio" name="MetodoEnvio" class="btnmetodoenvio" value="Transportadora">Transportadora<br>
          <input type="radio" name="MetodoEnvio" class="btnmetodoenvio" value="Empleado">Empleado<br>
              <div id="empleadoentrega" <?php echo 'style="display:none";' ?> >
              A quien se entrega?
              <input type="text" name="EmpleadoEnvio" id="EmpleadoEnvio">
              </div>
          </div>

          <div id="divnotacredito" style="display:none" >

          </div>


          </td>
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
	      <td colspan=2 align=center class=row2><input type=hidden name=IDGarantia value="<?php echo $r->IDGarantia ?>">
            <input type=hidden name=IDPuntoVenta value="<?php echo $r->IDPuntoVenta ?>">
            <input type=hidden name=ID value="<?php echo $r->$Key ?>">
            <input type=hidden name="FechaSalidaAlmacenAnt" value="<?php echo $r->FechaSalidaAlmacen ?>">
            <input type=hidden name="FechaEntradaAlmacenAnt" value="<?php echo $r->FechaEntradaAlmacen ?>">
            <input type=hidden name="FechaEntregaClienteAnt" value="<?php echo $r->FechaEntregaCliente ?>">
            <input type=hidden name="IDEstadoGarantiaAnt" id="IDEstadoGarantiaAnt" value="<?php echo $r->IDEstadoGarantia ?>">
            <input type=hidden name=action value="insertarcomentario">
            <?php
			//Consulto el rol del usuario para los permisos
			$rol = get_field("RestriccionxUsuario","IDGrupoUsuarios","IDEmpleado",$ID_Usuario);
			$nivel = get_field("Empleado","Nivel","IDEmpleado",$ID_Usuario);
			?>
            <input type="hidden" name="Rol" id="Rol" value="<?php echo $rol; ?>">
            <input type="hidden" name="Nivel" id="Nivel" value="<?php echo $nivel ?>">

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
		 $sql_comentario="SELECT * FROM ComentarioGarantia Where IDGarantia = '".$r->IDGarantia."' Order by IDComentarioGarantia DESC";
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
        	  <td colspan="6" align="left">
				<?php 
				$tipo_fidelizacion=get_field("TipoFinalizacionGarantia","Nombre","IDTipoFinalizacionGarantia",$r_comentario->IDTipoFinalizacionGarantia);
				
				echo $r_comentario->Descripcion . " " . $tipo_fidelizacion;  ?>
				</td>
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
   		(!empty($listar))? $nav->limit = $listar:$nav->limit=40;
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
		<td class=nav width=76%>&nbsp;&nbsp;&nbsp;&nbsp;<img src=images/folderopen.gif border=0>
		<a href="./?mod=<?php echo $MOD?>">Administrar <?php  echo $TitleMod?></a> </td>
		<td><a href="./?mod=<?php echo $MOD?>&action=add"><img src='images/botNreg.gif' border='0'></a></td>
	</tr>
</table>
<?php 
		if($rows > 0){
?>
<br>
<table width=500 cellpadding=2 cellspacing=3 align=center class=bordertable>
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
	<tr>
	  <td>
<table width=100% border=0 cellspacing=4 cellpadding=0>
<tr>
  <td colspan="18" align=left valign=middle class=rowform><a href="Garantia/exportagarantias.php?sql=<?php echo $sql; ?>"><img src="../images/excel_icon.gif" alt="" width="20" height="20" border="0" >Exportar Registros </a></td>
  </tr>
<tr>
						<td align=center class=rowform valign=middle bgcolor=#DBEAF5 width=69>Editar</td>
						<td class=rowform nowrap bgcolor=#DBEAF5> Numero&nbsp;
						    <?php  if($_GET['order_by']=="Nombre"){?><img src="images/<?php echo $img?>" border=0><?php }?> </td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Tipo<a style="color: #3A4F6C;text-decoration: none" href="<?php  echo "?mod=$MOD&field=".$_GET['field']."&QryString=".$_GET['QryString']."&order_by=Nombre&in_order=".$order."&listar=".$nav->limit."&action=list"; ?>">&nbsp;

						</a></td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Clasif</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Por:</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Cliente
	                      <?php  if($_GET['order_by']=="Codigo"){?><img src="images/<?php echo $img?>" border=0><?php }?></td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Ref</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Talla</td>
						<td class=rowform nowrap bgcolor=#DBEAF5>Tipo</td>
						<td class=navpic nowrap bgcolor=#DBEAF5>NumeroGuia / persona</td>
						<td class=navpic nowrap bgcolor=#DBEAF5>Factura&nbsp;
						  <?php  if($_GET['order_by']=="FechaFacturaBono"){?>
						  <img src="images/<?php echo $img?>" alt="" border=0>
		  <?php }?>						  </td>
						<td class=navpic nowrap bgcolor=#DBEAF5>Fecha</td>
						<td class=navpic nowrap bgcolor=#DBEAF5>Estado</td>
						<td class=navpic nowrap bgcolor=#DBEAF5>Almacen Reg. Garantia</td>
						<td class=navpic nowrap bgcolor=#DBEAF5>&nbsp;</td>
						<td class=navpic nowrap bgcolor=#DBEAF5>Estimada de entrega</td>
						<td class=navpic nowrap bgcolor=#DBEAF5>Nota Credito?</td>
						<td class=navpic nowrap bgcolor=#DBEAF5>ALERTA</td>
					</tr>

<?php while($r = db_fetch_object($result)){
	$tallap="";
	$id_referencia_item="";
?>

<tr>
						<td align=center valign=middle nowrap width=69 class=row2>
						  &nbsp;<a href='<?php echo "?mod=$MOD&action=edit&id="; echo $r->$Key; ?>'><img src='images/edit.gif' border='0'></a>
</td>
						<td nowrap class="<?php echo $class?>"><?php echo $r->IDGarantia; ?></td>
						<td nowrap class="<?php echo $class?>"><?php echo $r->TipoRegistro; ?></td>
						<td nowrap class="<?php echo $class?>"><?php  if($r->TipoProducto=="C") echo "Caprino"; elseif($r->TipoProducto=="T") echo "Tercero" ; ?></td>
						<td nowrap class="<?php echo $class?>"><?php  if($r->CantidadVeces=="1") echo "Primera"; elseif($r->CantidadVeces=="2") echo "Segunda" ; else echo "Tercera"; ?> Vez</td>
						<td nowrap class="<?php echo $class?>"><?php 

						if(!empty($r->IDDetalleCambio)){
									  $array_cambio_detalle=explode("|",$r->IDDetalleCambio);
									  $sql_datos_factura=db_query("Select * From Cambio Where IDCambio = '".$array_cambio_detalle[0]."'");
									  $r_factura=db_fetch_array($sql_datos_factura);
									}
									else{

									  if ($r->TipoFactura=="facturabono"):
										  $sql_datos_factura=db_query("Select * From FacturaBono Where IDFacturaBono = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
									  else:
										  $sql_datos_factura=db_query("Select * From Factura Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
									  endif;

									  $r_factura=db_fetch_array($sql_datos_factura);
									}
									if ($r->TipoRegistro=="Reproceso"){
										$id_proveedor=get_field("Referencia","IDProveedor","IDReferencia",$r->IDReferencia);
										echo get_field("Proveedor","Nombre","IDProveedor",$id_proveedor);
									}
									elseif($r->Mayorista=="S" || $r->Dotacion=="S"){
										echo $r->NombreMayorista;
									}
									else{
										$id_cliente= $r_factura['IDCliente'];
										echo get_field("Cliente","Nombre","IDCliente",$id_cliente)." ".get_field("Cliente","Apellido","IDCliente",$id_cliente);
									}
									?></td>
						<td nowrap class="<?php echo $class?>"><?php


									if ($r->TipoRegistro=="Reproceso" || $r->Mayorista=="S" || $r->Dotacion=="S"){

										echo get_field("Referencia","Numero","IDReferencia",$r->IDReferencia);
										$tallap=get_field("Talla","Descripcion","IDTalla",$r->IDTalla);
										$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$r->IDReferencia);;
										$tipop= get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
									}
									elseif(!empty($r->IDDetalleFacturaBono)){
										$array_bono_detalle=explode("|",$r->IDDetalleFacturaBono);
										if (count($array_bono_detalle)>0):
											$sql_bono=db_query("Select * From DetalleFacturaBono Where IDDetalleFacturaBono	 = '".$array_bono_detalle[1]."' and IDFacturaBono = '".$array_bono_detalle[0]."'");
											$r_bono=db_fetch_array($sql_bono);

											$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"])));
											$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
											$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
											echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
											$tipop=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
											$tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_bono["IDCodificacionEspecifica"]));
										endif;


									}


									elseif(empty($r->IDDetalleCambio)){
										  $id_referencia_item="";
										  $id_punto_venta=$r->IDPuntoVentaFactura;

										  if ($r->TipoFactura=="facturabono"):
										  	$sql_producto="select * from DetalleFacturaBono Where IDDetalleFacturaBono='".$r->IDDetalleFactura."' and IDFacturaBono = '".$r->IDFactura."' and IDPuntoVenta = '".$id_punto_venta."'";
										  else:
										  	$sql_producto="select * from DetalleFactura Where IDDetalleFactura='".$r->IDDetalleFactura."' and IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$id_punto_venta."'";
										  endif;

										  $qry_producto=db_query($sql_producto);
										  $r_detalle=db_fetch_object($qry_producto);
										  $id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica)));
										  $tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detalle->IDCodificacionEspecifica));


										if ($id_referencia_item==160){ // Cuando son excedentes consulto la referencia de la compra
											$sql_facturabono=db_query("Select * from FacturaBono Where IDFactura = '".$r->IDFactura."' and IDPuntoVenta = '".$r->IDPuntoVentaFactura."'");
											$r_facturabono=db_fetch_array($sql_facturabono);
											if (!empty($r_facturabono[IDFacturaBono])){
												$sql_detallefacturabono=db_query("Select * from DetalleFacturaBono Where IDFacturaBono = '".$r_facturabono[IDFacturaBono]."'");
												$r_detallefacturabono=db_fetch_array($sql_detallefacturabono);
												$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"])));
												$tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_detallefacturabono["IDCodificacionEspecifica"]));
											}
										  }



										  $id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
										  echo get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
										  $tipop=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
									}
									else{
										$array_cambio_detalle=explode("|",$r->IDDetalleCambio);
										if (count($array_cambio_detalle)>0):
											$sql_cambio=db_query("Select * From DetalleCambio Where IDDetalleCambio = '".$array_cambio_detalle[1]."' and IDCambio = '".$array_cambio_detalle[0]."'");
											$r_cambio=db_fetch_array($sql_cambio);

											$id_referencia_item=get_field("Referencia","IDReferencia","IDReferencia",get_field("PuntoVentaReferencia","IDReferencia","IDPuntoVentaReferencia",get_field("CodificacionEspecifica","IDPuntoVentaReferencia","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"])));
											$nombre_talla=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$r_cambio["IDCodificacionEspecifica"]));
											$id_tipo_ref=get_field("Referencia","IDTipoReferencia","IDReferencia",$id_referencia_item);
											echo $nombre_referencia=get_field("Referencia","Nombre","IDReferencia",$id_referencia_item);
											$tipop=get_field("TipoReferencia","Descripcion","IDTipoReferencia",$id_tipo_ref);
										endif;

									}

									if($r->Mayorista=="S" || $r->Dotacion=="S"):
										echo $r->ColorMayorista;
									endif;



									?></td>
						<td nowrap class="<?php echo $class?>"><?php
							if ($tallap!="")
								echo $tallap;
							else
								echo $tallap=get_field("Talla","Descripcion","IDTalla",get_field("CodificacionEspecifica","IDTalla","IDCodificacionEspecifica",$id_referencia_item));


							?></td>
						<td nowrap class="<?php echo $class?>"><?php
							if($r->Mayorista=="S" || $r->Dotacion=="S"):
								echo $r->TipoProductoMayorista;
							else:
								echo $tipop;
							endif;

							?></td>
						<td nowrap class="<?php echo $class?>"><?php 
						if($r->Mayorista=="S" || $r->Dotacion=="S"):
							echo $r->IngresadoPor;
						else:
							echo $r->NumeroGuia;
						endif;

						?>

                        </td>
						<td nowrap class="<?php echo $class?>"><?php 
						if ($r->TipoFactura=="facturabono"):
							echo $r_factura['NumeroFacturaBono']  . "(bono)";
						else:
							echo $r_factura['NumeroFactura'];
						endif;
							?>
                        </td>
						<td nowrap class="<?php echo $class?>"><?php echo formatofecha(substr($r->FechaTrCr,0,10)) ?></td>
						<td nowrap class="<?php echo $class?>" style="color: #900; font-weight:bold">
							<?php
							echo get_field("EstadoGarantia","Nombre","IDEstadoGarantia",$r->IDEstadoGarantia);

							if ($r->IDEstadoGarantia==10): // si es autorizacion especial consulto quien la dio
								$sql_usuario_especial=db_query("Select * from ComentarioGarantia Where IDGarantia = '".$r->IDGarantia."' and IDEstadoGarantia = 10");
								$row_usuario_especial = db_fetch_array($sql_usuario_especial);
								echo "(<font style='color: #000;'>Por:" . get_field("Empleado","Nombre","IDEmpleado",$row_usuario_especial[IDEmpleado]) . " " . get_field("Empleado","Apellidos","IDEmpleado",$row_usuario_especial[IDEmpleado])." <br> ".$row_usuario_especial["Descripcion"]."</font>)";
							endif;



							?>
                        </td>
						<td nowrap class="<?php echo $class?>"><?php echo get_field("PuntoVenta","Nombre","IDPuntoVenta",$r->IDPuntoVenta); ?></td>
						<td nowrap class="<?php echo $class?>">&nbsp;</td>
						<td nowrap class="<?php echo $class?>"><?php echo formatofecha(substr($r->FechaEstimadaEntrega,0,10)) ?></td>
						<td nowrap class="<?php echo $class?>">
						<?php echo $r->RequiereNotaCredito;
								 if ($r->RequiereNotaCredito=="S"){ echo " Numero: " . $r->NumeroNotaCredito ; }
								 elseif(!empty($r->NumeroNotaCredito)){
									echo  "S Numero: " . $r->NumeroNotaCredito ;
								}
								  ?>
                        </td>
						<td nowrap class="<?php echo $class?>">
							<?php

							if ($r->TipoRegistro=="Reproceso"){
								if ($r->IDEstadoGarantia!=8  && $r->IDEstadoGarantia!=10 && $r->IDEstadoGarantia!=9){
									$hoy=date("Y-m-d");
									$fecha_vencimiento = $r->FechaEstimadaEntrega;
									$diferencia_dias=strtotime ( $fecha_vencimiento ) - strtotime ( $hoy );
									$dias=intval($diferencia_dias/60/60/24) ;
									if ($dias >= 0 && $dias <= 5  ){ ?>
										<img src="images/campananaranja.jpg" width="15" height="15">
									<?php
										echo "Vence en " . $dias . " dias";
									}elseif ($dias <0){ ?>
										<img src="images/campanaalerta.jpg" width="15" height="15" >
									<?php
										echo "Vencida hace " . abs($dias) . " dias";
									}

								}
							}
							elseif ($r->IDEstadoGarantia!=8 && $r->IDEstadoGarantia!=9  && $r->IDEstadoGarantia!=10 && $r->IDEstadoGarantia!=12 ){
								$hoy=date("Y-m-d");
								$fecha_vencimiento = $r->FechaEstimadaEntrega;
								$diferencia_dias=strtotime ( $fecha_vencimiento ) - strtotime ( $hoy );
								$dias=intval($diferencia_dias/60/60/24) ;
								if ($dias >= 0 && $dias <= 5  ){ ?>
                                	<img src="images/campananaranja.jpg" width="15" height="15">
                                <?php
									echo "Vence en " . $dias . " dias";
								}elseif ($dias <0){ ?>
                                	<img src="images/campanaalerta.jpg" width="15" height="15" >
                                <?php
									echo "Vencida hace: " . abs($dias) . " dias";
								}
								if($r->IDEstadoGarantia==2)
								echo "<br>Ya la recibio?";

							}

							// Si esta pendiente de finalizar garantia
							if ($r->IDEstadoGarantia=="11"){ ?>
							<br><img src="images/campanaalerta.jpg" width="15" height="15" >
                            Pendiente de finalizar garantia
                            <?php
							}


							// Si se marco que necesita numero de nota credita y no se ha digitado
							if ($r->RequiereNotaCredito=="S" && $r->NumeroNotaCredito==""){ ?>
							<br><img src="images/campananaranja.jpg" width="15" height="15" >
                            No se ha ingresado el Numero de la nota credito
                            <?php
							}

                            // Si esta pendiente de enviar producto rechazado a fabrica
							if (($r->IDEstadoGarantia=="9" || $r->IDEstadoGarantia=="10" ) && $r->RequiereDevolucion=="S"){ ?>
							<br><img src="images/campanaalerta.jpg" width="15" height="15" >
                            Pendiente de enviar producto a fabrica
                            <?php
							}


							?>




                        </td>
					</tr>
<?php } // END for
?>
<tr>
<td class=texto bgcolor=#DBEAF5 colspan=18 nowrap>
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

                 <table>
                	<tr>
                    	<td>
                        	Numero Garantia
                        </td>
                        <td>
                        	<input type="text" name="IDGarantia" id="IDGarantia">
                        </td>
                        <td>
                        	Numero de Factura
                        </td>
                        <td>
                        	<input type="text" name="NumeroFactura" id="NumeroFactura">
                        </td>
                        <td>
                        	Tipo de Proceso :
                        </td>
                        <td>
                        	<select name="TipoRegistro" id="TipoRegistro">
                                <option value=""></option>
                                <option value="Garantia">Garantia</option>
                                <option value="Reproceso">Reproceso</option>
                                <option value="Servicio">Servicio</option>
																<option value="Restauracion">Restauracion</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        Estado:
                        </td>
                        <td>
                      		<select name="IDEstadoGarantia" id="IDEstadoGarantia">
                                <option value="">[Seleccione]</option>
                                <?php
                                $sql_estados=db_query("Select * from EstadoGarantia Where 1 Order by Nombre");
                                while($row_estado=db_fetch_array($sql_estados)){
                                    ?>
                                    <option value="<?php echo $row_estado["IDEstadoGarantia"]; ?>"><?php echo $row_estado["Nombre"]; ?></option>
                                <?php
                                }
                                ?>
                            </select>

                        </td>

                         <td>
                         Punto Venta Registra

                        </td>
                        <td>
                        	 <select name="IDPuntoVenta" id="IDPuntoVenta">
                    <option value="">[Seleccione]</option>
                    <?php
                    $sql_vta=db_query("Select * from PuntoVenta Where Publicar = 'S' Order by Nombre");
                    while($row_vta=db_fetch_array($sql_vta)){
                        ?>
                        <option value="<?php echo $row_vta["IDPuntoVenta"]; ?>"><?php echo $row_vta["Nombre"]; ?></option>
                    <?php
                    }
                    ?>
          		</select>

                        </td>
                        <td>
                        Garantias por


                        </td>
                        <td>
                         <select name="CantidadVeces" id="CantidadVeces">
                            <option value=""></option>
                            <option value="1">Primera Vez </option>
                            <option value="2">Segunda Vez</option>
                            <option value="3">Tercera Vez</option>
                        </select>

                        </td>



                    </tr>


                    <tr>
                    	<td>
                        Clasificacion

                        </td>
                        <td>
                   		  <select name="TipoProducto" id="TipoProducto">
                	<option value=""></option>
                	<option value="C">Producto Caprino</option>
                    <option value="T">Producto Tercero</option>
                </select>

                        </td>

                         <td>
                        Alerta


                        </td>
                        <td><select name="Alerta" id="Alerta">
                          <option value=""></option>
                          <option value="V">Vencidos</option>
                          <option value="PV">Proximo a vencer</option>
                          <option value="NC">Sin Nota Credito</option>
                          <option value="PF">Pendiente Finalizar</option>
                        </select>


                        </td>
                        <td>Referencia</td>
                        <td><input type="text" name="NumeroReferencia" id="NumeroReferencia">


                        </td>



                    </tr>
                    <tr>
                      <td>Fecha Inicio</td>
                      <td>
                      <input type=text readonly size=10 class=input name=limit1>
			      <script language='JavaScript1.2'>
								<!--
								if (!document.layers)
								document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit1,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
								//-->
					</script>
                      </td>
                      <td>Fecha Fin</td>
                      <td>
                      <input type=text size=10 readonly class=input name=limit2>
			      <script language='JavaScript1.2'>
								<!--
								if (!document.layers)
									document.write("<img src=jscripts/imagescalendar/cal.gif onmouseover=this.style.cursor='hand' onclick='popUpCalendar(this, document.frm.limit2,\"yyyy-mm-dd\")' width=16 height=16 border=0>")
								//-->
					</script>
                      </td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                 </table>
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
