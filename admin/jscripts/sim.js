$(document).ready(function(){



		$( ".onlynumber" ).keypress(function( evt ){
			 var charCode = (evt.which) ? evt.which : event.keyCode
			 if (charCode > 31 && (charCode < 48 || charCode > 57))
				return false;

			 return true;
		});


		$( ".proveedor_pedido" ).change(function(){
			var IDProveedor = $(this).val();
				$.ajax({
					   type: "POST",
					   url: "ajax/ConsultaProveedor.php",
					   data: "IDProveedor="+IDProveedor,
					   dataType: "json",
					   success: function(data){
							 $('#NombreProveedor').html(data["Nombre"]);
							 $('#DireccionProveedor').html(data["Direccion"]);
							 $('#TelefonoProveedor').html(data["Telefono"]);
							 $('#CiudadProveedor').html(data["Ciudad"]);
							 $('#EmailProveedor').html(data["Email"]);
					   }
				});
			 return false;
		});

		$("input[class='btnelectronica']").change(function(){
				var valor=$(this).val();
				var IDFactura=$(this).attr("idfactura");
				var IDPuntoVenta=$(this).attr("idpuntoventa");

					$("#msgupdate"+IDFactura).html("<span style='color:#FF0004'>Guardando!...</span>");
					jQuery.ajax( {
						"type" : "POST",
						"data" : { "IDFactura" : IDFactura,"Valor": valor,"IDPuntoVenta": IDPuntoVenta },
						"dataType" : "json",
						"url" : "Factura/actualizarelectronica.php",
						"success" : function( data ){
								$("#msgupdate"+IDFactura).html("");
						}
					});
		});


		$('.btnreenviarfac').click(function(){
			var valor=$(this).val();
			var IDFactura=$(this).attr("idfactura");
			var IDPuntoVenta=$(this).attr("idpuntoventa");
			$("#msgupdate"+IDFactura).html("<span style='color:#FF0004'>generando por favor espere!...</span>");
					jQuery.ajax( {
						"type" : "POST",
						"data" : { "IDFactura" : IDFactura,"Valor": valor,"IDPuntoVenta": IDPuntoVenta },
						"dataType" : "json",
						"url" : "../cron/ConsultarFactura.php",
						"success" : function( data ){
								$("#msgupdate"+IDFactura).html("");
						}
					});

		});

		$('.reenviar_bono').click(function(){
			var IDCliente=$(this).attr("IDCliente");
			var IDBono=$(this).attr("IDBono");			
					jQuery.ajax( {
						"type" : "POST",
						"data" : { "IDCliente" : IDCliente,"IDBono": IDBono },
						"dataType" : "json",
						"url" : "../cron/reenviarbono.php",
						"success" : function( data ){
								var IDBono="";
						}
					});
			alert("Bono reenviado con exito");

		});

		$( ".validacargo" ).change(function(){
			var IDCargo = $("#Cargo").val();
			var IDPuntoVenta = $("#PuntoVenta").val();
				$.ajax({
					   type: "POST",
					   url: "ajax/ConsultaCargo.php",
					   data: "IDCargo="+IDCargo+"&IDPuntoVenta="+IDPuntoVenta,
					   dataType: "json",
					   success: function(data){
							 if(data["Encontrado"]=="S"){
								 	alert("ATENCION!!!: Ya existe administradores en este punto de venta: " + data["DatosAdmin"]);
							 }
					   }
				});
			 return false;
		});



		$('.btnmetodoenvio').click(function(){
			var seleccionado = $(this).val();
			if(seleccionado=="Empleado"){
				$('#empleadoentrega').show();
			}
			else{
				$('#empleadoentrega').hide();
			}

		});

		// Para campos creados dinamicamente
		$("#detalle-cont").on("change", ".verifica_referencia", function(){
			var color_referencia = $(this).val();
			var posicion = $(this).attr("alt");
			var cod_referencia = $("#ReferenciaCaprino"+posicion).val();
			var resultado;
			resultado  = consulta_referencia(cod_referencia,$(this),posicion,color_referencia);
		});


		$('#agrega_detalle').click(function(){
			 var detalle_actuales = $("#ITEMSDETALLE").val();
			 var nuevo_numero = parseInt(detalle_actuales)+1;
			 var campodetalle;
			 $("#ITEMSDETALLE").val(nuevo_numero);
			 // Obtenemos el numero de filas (td) que tiene la primera columna
            // (tr) del id "tabla"
            var tds=$("#tabla_detalle_pedido tr:first td").length;
            // Obtenemos el total de columnas (tr) del id "tabla"
            var trs=$("#tabla_detalle_pedido tr").length;
            var nuevaFila="<tr>";
			var campo_texto = "";
            for(var i=0;i<tds;i++){
                // añadimos las columnas
				switch(i){
					case 0:
						campo_texto = '<input type=text size=10 class=input name="ReferenciaProveedor'+nuevo_numero+'" id="ReferenciaProveedor'+nuevo_numero+'" value="">';
					break;
					case 1:
						campo_texto = '<input type=text size=10 class="input verifica_referencia" name="ReferenciaCaprino'+nuevo_numero+'" id="ReferenciaCaprino'+nuevo_numero+'" alt="'+nuevo_numero+'" value="">';
					break;
					case 2:
						campo_texto = '<input type=text size=10 class=input name="CodigoColor'+nuevo_numero+'" id="CodigoColor'+nuevo_numero+'" alt="'+nuevo_numero+'" value="">';
					break;
					case 3:
						campo_texto = '<input type=text size=10 class=input name="CueroColor'+nuevo_numero+'" id="CueroColor'+nuevo_numero+'" alt="'+nuevo_numero+'" value="">';
					break;
					case 4:
						campo_texto = '<input type=text size=10 class=input name="Suela'+nuevo_numero+'" id="Suela'+nuevo_numero+'" alt="'+nuevo_numero+'" value="">';
					break;
					case 5:
						campo_texto = '<input type=text size=10 class=input name="Tacon'+nuevo_numero+'" id="Tacon'+nuevo_numero+'" alt="'+nuevo_numero+'" value="">';
					break;
					case 6:
						campo_texto = '<input type=text size=10 class=input name="Altura'+nuevo_numero+'" id="Altura'+nuevo_numero+'" alt="'+nuevo_numero+'" value="">';
					break;
					case 7:
						campo_texto = '<input type=text size=10 class=input name="Horma'+nuevo_numero+'" id="Horma'+nuevo_numero+'" alt="'+nuevo_numero+'" value="">';
					break;
					case 8:
						campo_texto = '<input type=text size=10 class=input name="Precio'+nuevo_numero+'" id="Precio'+nuevo_numero+'" alt="'+nuevo_numero+'" value="">';
					break;
					case 9:
						campo_texto = '<textarea name="Observacion'+nuevo_numero+'" class="input" title="Observacion" id="Observacion'+nuevo_numero+'" cols="20" rows="2"></textarea>';
					break;

				}

                nuevaFila+="<td>"+campo_texto+"</td>";
            }
            nuevaFila+="</tr>";
            $("#tabla_detalle_pedido").append(nuevaFila);

			 return false;
		});


		$('#genera_pedido').click(function(){
			var id_pedido_tercero = $(this).attr('alt');
			var rel = $(this).attr('rel');
			if (confirm("Esta seguro de generar el pedido?, No podra realizar ningun cambio")){
				jQuery.ajax( {
						"type" : "POST",
						"data" : "id_pedido_tercero=" + id_pedido_tercero+"&rel="+rel,
						"url" : "ajax/envia_pedido.php" ,
						"dataType" : "json",
						"success" : function( data ){
							var respuesta_ok = (data == "ok" || (data && data["status"] == "ok"));
							if (respuesta_ok){
								alert("Pedido reenvioado con exito");
								document.location.href = "?mod=PedidoTercero";
							}
							else{
								alert("Ocurrio un problema al tratar de generar el pedido");
								return false;
							}
						}
				});
				//document.location.href = "?mod=PedidoTercero&id=1&tab=detalle&action=genera_pedido";
			}


			 return false;
		});

		$('#actualizar_fecha_entrega').click(function(){
			var id_pedido_tercero = $(this).attr('alt');
			var FechaEntrega = $("#FechaEntrega").val();				
			if (confirm("Esta seguro que desea cambiar la fecha de entrega?")){
				jQuery.ajax( {
						"type" : "POST",
						"data" : "id_pedido_tercero=" + id_pedido_tercero+"&FechaEntrega="+FechaEntrega,
						"url" : "ajax/cambiar_fecha_entrega.php" ,
						"dataType" : "json",
						"success" : function( data ){
							var respuesta_ok = (data == "ok" || (data && data["status"] == "ok"));
							if (respuesta_ok){
								alert("Fecha cambiada con exito");								
							}
							else{
								alert("Ocurrio un problema al tratar de cambiar la fecha");
								return false;
							}
						}
				});
				//document.location.href = "?mod=PedidoTercero&id=1&tab=detalle&action=genera_pedido";
			}


			 return false;
		});




		$('#frmPedidoTercero').submit(function() {


			var items = $("#ITEMSDETALLE").val();
			var valida_item=0;
			var mensaje_error="";

			//verifico que en la fila donde haya datos este todo seleccionado
			for (i=1;i<items;i++){
				if ($("#ReferenciaCaprino"+i).val()!=''){
					valida_item=1;
					var IDCurvaTercero = $("#IDCurvaTercero"+i).prop("selectedIndex");
					var CodigoColor = $("#CodigoColor"+i).val();
					var Precio = $("#Precio"+i).val();

					if (CodigoColor =="" || Precio =="" ){
						mensaje_error="Debe digitar por lo menos el color y el precio en el item " + i;
						break;
					}

					if (IDCurvaTercero == "0"){
						mensaje_error="Debe seleccionar la curva del item " + i;
						break;
					}




				}
			}

			if (valida_item==0){
				mensaje_error="Debe diligenciar por lo menos un producto";
			}


			if (mensaje_error!=''){
				alert(mensaje_error);
				return false;
			}

			return true;
		});




});


function consulta_referencia(numero_referencia,cajatexto,posicion,color_referencia) {
				jQuery.ajax( {
						"type" : "POST",
						"data" : "numero_referencia=" + numero_referencia+"&Color="+color_referencia,
						"url" : "ajax/verifica_referencia.php" ,
						"dataType" : "json",
						"success" : function( data ){
							if (data!="no_existe"){
								//$("#CodigoColor"+posicion).val(data["CodigoColor"]);
								$("#ReferenciaProveedor"+posicion).val(data["ReferenciaProveedor"]);
								$("#CueroColor"+posicion).html(data["CueroColor"]);
								$("#Producto"+posicion).val(data["Producto"]);
								$("#Suela"+posicion).val(data["Suela"]);
								$("#Tacon"+posicion).val(data["Tacon"]);
								$("#Altura"+posicion).val(data["Altura"]);
								$("#Horma"+posicion).val(data["Horma"]);
								$("#Precio"+posicion).val(data["Precio"]);
								$("#Observacion"+posicion).html(data["Observacion"]);
							}
						}
					});
}
