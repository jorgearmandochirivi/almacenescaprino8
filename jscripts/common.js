/**
*Procedimientos y funciones de uso general
*
*/
var nav4 = window.event ? true : false;
var tipmage = "";

$( document ).ready(function(){



	$('.IDBonoFidelizacion').click(function() {
		var valor_bono = parseInt($('#ValorBonoParametro').val());
		var suma_bono = parseInt($('#SumaBonoNumero').val());
		var identificador_bono = $(this).attr('IdentificadorBono'); 
		
		 if($(this).is(':checked')) {
			var suma_bono_utilizado = valor_bono + suma_bono;
			$('#CodigoBarrasBono'+identificador_bono).show();
			$('#BotonVerificar'+identificador_bono).show();
		 }
		 else{
			var suma_bono_utilizado =   suma_bono - valor_bono;
			$('#CodigoBarrasBono'+identificador_bono).hide();
			$('#BotonVerificar'+identificador_bono).hide();
		}
		

		$('#SumaBono').val(suma_bono_utilizado);
		$('#SumaBonoNumero').val(suma_bono_utilizado);
        //alert($(this).val());
		//RECALCULO VALORES FACTURA
		recalcular_valores_factura_con_bono();



    });

	$('.IDBonoIva').click(function() {
		var valor_bono_iva = parseInt($(this).attr("ValorBono"));
		var bono_iva = parseInt($(this).val());
		var AplicaBono = $('#ProductoNoAplica').val();
		
		$('#div_factura_redimir'+bono_iva).show();


		$('#ObservacionDescuento').val("");
		$('#DescuentoCumple').val("N");
		$('.descuento_linea').val("");
		

		

		if(AplicaBono!="S"){
			valor_bono_iva=0;
			bono_iva=0;
		}

		$('#SumaBono').val(valor_bono_iva);
		$('#SumaBonoNumero').val(valor_bono_iva);
		$('#BonoIva').val(bono_iva);
        //alert($(this).val());
		//RECALCULO VALORES FACTURA
		
		promo_segundo_par_con_talla();
		calculatotal(1);
		calculatotal(2);
		calculatotal(3);
		recalcular_valores_factura_con_bono();
		


    });




	$( ".tarjeta_digitada" ).blur(function(){
		if(valida_num_tarjeta()){
			var resultado = "ok";
		}
		else{
			return false;
		}

	});//end function


	$('#envia_entrada').click(function() {

		$('#bloquea_confirmar').hide();
		return true;

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
					"url" : "cron/ConsultarFactura.php",
					"success" : function( data ){
							$("#msgupdate"+IDFactura).html("<span style='color:#FF0004'>Factura reenviada!</span>");
					}
				});

	});

	$("input[class='btnpayu']").change(function(){
		var valor=$(this).val();
		var IDFactura=$(this).attr("idfactura");
		var IDPuntoVenta=$(this).attr("idpuntoventa");

			$("#msgupdatepayu"+IDFactura).html("<span style='color:#FF0004'>Guardado!...</span>");
			jQuery.ajax( {
				"type" : "POST",
				"data" : { "IDFactura" : IDFactura,"Valor": valor,"IDPuntoVenta": IDPuntoVenta },
				"dataType" : "json",
				"url" : "cron/actualizarpayu.php",
				"success" : function( data ){
						$("#msgupdatepayu"+IDFactura).html("");
				}
			});
});


	$('.CajaCodigoBarras').focus(function() {
		var NumeroBonoSeleccionado=$(this).attr("IdentificadorBono");
		$('#Verificado'+NumeroBonoSeleccionado).val('N');
		$('#CodigoBarrasBono'+NumeroBonoSeleccionado).css('background-color', '#FFF');
	});

	$('.VerificarBono').click(function() {
		var NumeroBonoSeleccionado=$(this).attr("IdentificadorBono");
		var NumeroBonoDigitado=$("#CodigoBarrasBono"+NumeroBonoSeleccionado).val();		
		var ValorReal= parseInt(NumeroBonoSeleccionado)*21+133;
		var NumeroBonoDigitado = NumeroBonoDigitado.replace('BonoCaprino-', '');
		NumeroBonoDigitado = NumeroBonoDigitado.replace('BonoCaprino,', '');
		NumeroBonoDigitado = NumeroBonoDigitado.replace("BonoCaprino'", '');
		if(ValorReal==NumeroBonoDigitado){
			$("#Verificado"+NumeroBonoSeleccionado).val("S");
			alert("Bono Verificado");
			$('#CodigoBarrasBono'+NumeroBonoSeleccionado).css('background-color', '#89AC76');
		}
		else{
			$("#Verificado"+NumeroBonoSeleccionado).val("N");
			alert("Bono incorrecto");
			$('#CodigoBarrasBono'+NumeroBonoSeleccionado).css('background-color', '#FF0000');
		}


	});

	$('#envia_factura').click(function() {
		$('#bloquea_confirmar').hide();

		var contador=0;
		var valorbono = 0 ;
		var ValorTotalFactura = 0;
		var total_bonos = 0;
		var numeroregistro=document.frm.elements["NumeroRegistro"].value;
		var referencia1=document.frm.elements["Numero1"].value;
		var referencia2=document.frm.elements["Numero2"].value;
		var ObservacionDescuento=document.frm.elements["ObservacionDescuento"].value;
		var BonosVerificados="S";
		  
		if( (referencia1=="ZSE1****" || referencia1=="zse1****"  || referencia1=="ZSE2****" || referencia1=="zse2****" ||  referencia2=="ZSE1****" || referencia2=="zse1****" || referencia2=="ZSE2****" || referencia2=="zse2****") && ObservacionDescuento == "")
		{
			alert("Debe agregar el numero  del registro al que corresponde el servicio y su observacion!");
			$("#ObservacionDescuento").attr("readonly", "");			
			return false;
		}
		else{
			//$("#ObservacionDescuento").val("");			
		}

		

		if(valida_num_tarjeta()){
			var resultado = "ok";
		}
		else{
			return false;
		}


		valorbono = $("#ValorBonoParametro").val();
		ValorTotalFactura = $("#TotalFacturaNumero").val();

		$('#frmfactura input[type=checkbox]').each(function(){
            if (this.checked) {
                contador=contador+1;
				var IDBonoUtilizado = $(this).attr('IdentificadorBono');				
				var ValorVerificado= $("#Verificado"+IDBonoUtilizado).val();				
				if(ValorVerificado=="N"){					
					alert("Debe verificar el bono "+IDBonoUtilizado+" escaneandolos con la pistola");
					BonosVerificados="N";					
				}
            }
        });

		if(BonosVerificados=="N"){			
			return false;
		}

		if(numeroregistro!="" && ObservacionDescuento == ""){
			alert("Si agregó un numero de registro debe digitar una observacion");
			return false;
		}
		
		//El bono deble ser del doble del valor de la compra
		if(parseInt(contador)>0){
			total_valor_bonos = (valorbono * contador)*2;
			//alert(total_valor_bonos);
			if(parseInt(ValorTotalFactura)>=parseInt(total_valor_bonos)){
				//alert(ValorTotalFactura + ">="+total_valor_bonos);
				return true;
			}
			else{
				alert("Si va a cancelar con bonos el valor de la compra debe ser mayor a " + total_valor_bonos + " ,por favor verifique FAC " + ValorTotalFactura);
				return false;
			}
		}
		else{
			return true;
		}

    });


	$('#mostrar_devolucion').click(function() {
		$("#div_devolucion").toggle();
		return false;

    });



	$('.btnmismocliente').click(function() {
		var valor = $('input:radio[name=MismoCliente]:checked').val();
		switch(valor){
			case "S":
				$("#divcliente").show();
				$("#divclientenuevo").hide();
			break
			case "N":
				$("#divcliente").hide();
				$("#divclientenuevo").show();
			break;
		}

		return true;

    });

	$('.btndeseaelectronica').click(function() {		
		var valor = $('input:radio[name=DeseaElectronica]:checked').val();
		var correo = $('#Email').val();
		switch(valor){
			case "S":				
					$('#Email').val("");	
					$("#TablaSuavidad").show();	
			break
			case "N":						
				$("#TablaSuavidad").hide();					
				if(correo != ""){
					$('#Email').val("");	
				}	
				else{
					$('#Email').val("felectronica@calzadocaprino.com");					
					$("#Email").attr("readonly", "readonly");		
				}			
			break;
		}

		return true;

    });

	$('.btncsuavidad').click(function() {		
		var valor = $('input:radio[name=CSuavidad]:checked').val();
		switch(valor){
			case "S":
				$("#otrosdatosfactura").show();				
			break
			case "N":
				$("#otrosdatosfactura").hide();				
			break;
		}

		return true;

    });



		$( "#frmcambio" ).submit(function(){
		var valor = $('input:radio[name=MismoCliente]:checked').val();
		var IDEmpleado=$('#Empleado').val();

		$("#btn_guardar_cambio").attr("disabled",true);


		if(valor!="S" && valor !='N'){
			alert("Por favor seleccione si El cliente que realiza el cambio es el mismo que compro.?");
			$("#btn_guardar_cambio").attr("disabled",false);
			return false;
		}
		else if(IDEmpleado==""){
			alert("Por favor seleccione el empleado");
			$("#btn_guardar_cambio").attr("disabled",false);
			return false;
		}
		else{
			return true;

		}

		return false;

    });



	

	$('.seleccion_alianza').change(function() {
		var descuento = $(this).find('option:selected').attr("class");
		var tipo_producto = $(this).find('option:selected').attr("title");
		var texto_alianza = $(this).find('option:selected').html();
		var tipo_referencia = $(this).find('option:selected').attr("idtiporeferencia");
		var minimo_productos = $(this).find('option:selected').attr("minimoproductos");
		
		$("#DescuentoAlianza").val(descuento);
		$("#TipoProductoAlianza").val(tipo_producto);
		$("#notificacion_alianza").html("<li>"+texto_alianza+"</li>");
		if(texto_alianza!=""){
			$("#ObservacionDescuento").val("Se aplica descuento por alianza "+texto_alianza);
			$("#ObservacionDescuento").attr("readonly", "readonly");
		}
		else{
			$("#ObservacionDescuento").val("");
			$("#ObservacionDescuento").attr("readonly", "");
		}

		recalcular_valores_factura_con_alianza(descuento,tipo_producto,"",tipo_referencia,minimo_productos);
		return false;
    });

	$('.descuento_adicional').change(function() {
		var  IDAlianza = parseInt($("#IDAlianza").val());
		var seleccion = $(this).val();
		var descuento_adicional=5;
		var texto_adicional=" -5% adicional por cupón Prensa";
		
			if (isNaN(IDAlianza)) {			
				alert("Debe seleccionar primero una alianza");
			}
			else{	
				if(seleccion!="S"){
					descuento_adicional=0;					
					texto_adicional="";
				}
				else{

				}

				var descuento = parseInt($("#IDAlianza").find('option:selected').attr("class")) + descuento_adicional;								
				var tipo_producto = $("#IDAlianza").find('option:selected').attr("title");
				var texto_alianza = $("#IDAlianza").find('option:selected').html();
				$("#DescuentoAlianza").val(descuento);
				$("#TipoProductoAlianza").val(tipo_producto);
				$("#notificacion_alianza").html("<li>"+texto_alianza+texto_adicional+"</li>");
				if(texto_alianza!=""){
					$("#ObservacionDescuento").val("Se aplica descuento por alianza "+texto_alianza+texto_adicional);
				}
				else{
					$("#ObservacionDescuento").val("");
				}
				recalcular_valores_factura_con_alianza(descuento,tipo_producto);
			}
				
		return false;		
    });


$('.seleccion_cuota_pago').click(function() {
		var id = $(this).val();
		var nombre = $(this).attr("id");
		var idcuota = $(this).attr("alt");
		var fullDate = new Date();
		var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
		var currentDate = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + fullDate.getDate();
		var hora = fullDate.getHours() + ":" + fullDate.getMinutes() + ":" + fullDate.getSeconds();

		if($("#"+nombre).is(':checked')) {
            $("#FechaPago"+idcuota).val(currentDate + " "+hora);
        } else {
            $("#FechaPago"+idcuota).val("");
        }

    });




	$('#BuscarBono').click(function() {
		var numero_bono = $('#BuscarNumero').val();
		var cedula_tercero = $('#BuscarCedula').val();
		var cliente_factura = $('#ClienteFactura').val();
		if (numero_bono==""){
			alert("Debe digitar el numero del bono");
			return false;
		}

		if (cedula_tercero==""){
			alert("Debe digitar la cedula al que pertenece el bono");
			return false;
		}

		document.location.href="?mod=GenerarFactura&action=edit&id="+cliente_factura+"&BuscarNumero="+numero_bono+"&BuscarCedula="+cedula_tercero+"&idnot=";
    });

		$('#BuscarBonoFacBono').click(function() {
			var numero_bono = $('#BuscarNumero').val();
			var cedula_tercero = $('#BuscarCedula').val();
			var cliente_factura = $('#ClienteFactura').val();
			if (numero_bono==""){
				alert("Debe digitar el numero del bono");
				return false;
			}

			if (cedula_tercero==""){
				alert("Debe digitar la cedula al que pertenece el bono");
				return false;
			}

			document.location.href="?mod=GenerarFacturaBono&action=edit&id="+cliente_factura+"&BuscarNumero="+numero_bono+"&BuscarCedula="+cedula_tercero+"&idnot=";
	    });


$('#BuscarReferente').click(function() {
		$("#BuscarReferente").attr("value","Buscando..");
		$("#BuscarReferente").attr("disabled",true);
		var numero_cedula = $('#CedulaReferente').val();
		if (numero_cedula==""){
			alert("Debe digitar el numero  de cedula");
			$("#BuscarReferente").attr("value","Buscar");
			$("#BuscarReferente").attr("disabled",false);
			return false;
		}
		else{
			$.ajax({

					async : false,
					type: 'POST',
					url: 'includes/referido/buscar_referente.async.php',
					dataType : "json",
					data : "NumeroDocumento="+numero_cedula,
					success: function(data) {
							if(data == "no_existe") {
									alert("El numero de documento no existe, por favor verifique");
									$("#IDClienteReferente").val("");
									$("#TipoAlianza").val("");
									$('#IDAlianza > option[value=""]').attr('selected', 'selected');
									respuesta=1;
							}
							else{
								//$('#IDAlianza option').filter('[value="16"]').prop('selected', 'selected');
									$("#IDClienteReferente").val(data);
									$("#TipoAlianza").val("Referido");
									$("#IDAlianza").append('<option value=16 class=10>Referidos Primer Compra	</option>');
									$('#IDAlianza > option[value="16"]').attr('selected', 'selected');
									$(".seleccion_alianza").change();
									alert("Alianza aplicada correctamente");
									respuesta=1;

							}
					}
				});

		}
		$("#BuscarReferente").attr("value","Buscar");
		$("#BuscarReferente").attr("disabled",false);
    });




	$( "#frmproducto_garantia" ).submit(function(){
		var opcion_producto=$('input:radio[name=IDProductoGarantia]:checked').val();
		if (opcion_producto==undefined || opcion_producto==""){
			alert("Seleccione un producto");
			return false;
		}
		else{
			return true;
		}
	});


		$( "#frmFormaPago" ).submit(function(){
			$('#btn_enviarpago_fac').attr("disabled", true);
			$( "#frmFormaPago" ).submit();
			return false;
		});




		$( "#frmEntradaTercero" ).submit(function(){

			$('#btnConfirmarEntrada').attr("disabled", true);
			$("#btnConfirmarEntrada").attr('value', 'Enviando, por favor espere'); //versions older than 1.6
			$( "#frmEntradaTercero" ).submit();
			return false;
		});



	$( "#frmgarantia" ).submit(function(){
		var TipoRegistro = $('input:radio[name=TipoRegistro]:checked').val();
		var TipoProducto = $('input:radio[name=TipoProducto]:checked').val();
		var CantidadVeces = $('input:radio[name=CantidadVeces]:checked').val();
		var Cambios =  $("#Cambios").val();
		var Bonos =  $("#Bonos").val();
		var NumeroOrdenProduccion =  $("#NumeroOrdenProduccion").val();


		if (TipoRegistro=="Reproceso"){
			var IDReferencia = $('#IDReferencia').val();
			var IDTalla = $('#IDTalla').val();
			var Tipo_Producto = $('input:radio[name=TipoProducto]:checked').val();
			if (IDReferencia=="" || IDTalla=="" || Tipo_Producto =="" ){
				alert("Debe seleccionar la referencia, la talla y el tipo de producto");
				return false;
			}
		}


		if (TipoRegistro=="Restauracion"){
			var IDReferencia = $('#IDReferencia').val();
			var IDTalla = $('#IDTalla').val();
			var Tipo_Producto = $('input:radio[name=TipoProducto]:checked').val();
			var AutorizaPago = $('input:radio[name=PagoRestauracion]:checked').val();
			var ValorBasica =  $('#ValorBasica').val();
			var ValorPremium =  $('#ValorPremium').val();

			if (IDReferencia=="" || IDTalla=="" || Tipo_Producto =="" ){
				alert("Debe seleccionar la referencia, la talla y el tipo de producto");
				return false;
			}


			if(AutorizaPago=="" || AutorizaPago==undefined){
				alert("Debe seleccionar si el cliente acepta pagar el valor de la restauracion");
				return false;
			}

			if(AutorizaPago=="S" &&  ValorBasica == "" && ValorPremium == ""){
				alert("Si el cliente acepta pagar el valor debe ser mayor a $0");
				return false;
			}

			if(AutorizaPago=="N" && ( ValorBasica>0 || ValorPremium>0 )){
				alert("Si el cliente no acepta pagar el valor debe ser $0");
				return false;
			}


		}

		if (parseInt(Cambios)>0){
			var detalle_cambio = $('input:radio[name=IDDetalleCambio]:checked').val();
			if (detalle_cambio==undefined || detalle_cambio==""){
				alert("Seleccione la referencia que se va a ingresar por garantia.");
				return false;

			}
		}

		if (parseInt(Bonos)>0){
			var detalle_bono = $('input:radio[name=IDDetalleFacturaBono]:checked').val();
			if (detalle_bono==undefined || detalle_bono==""){
				alert("Seleccione la referencia que se va a ingresar por garantia.");
				return false;

			}
		}

		if (NumeroOrdenProduccion==""){
			alert("El Numero de Orden de Produccion es obligatorio.");
			return false;
		}

		if (TipoRegistro==undefined || TipoRegistro==""){
			alert("Seleccione si es una Garantia, Restauracion o Reproceso.");
			return false;
		}

		if (TipoProducto==undefined || TipoProducto==""){
			alert("Seleccione si el producto es de Caprino o tercero.");
			return false;
		}

		if (CantidadVeces==undefined || CantidadVeces==""){
			alert("Seleccione si es primera, segunda o tercera vez.");
			return false;
		}

		if(	$('input[name=CueroPelado]').is(':checked') == ''
				&& $('input[name=CueroManchado]').is(':checked') == ''
				&& $('input[name=CueroRayado]').is(':checked') == ''
				&& $('input[name=ForroManchado]').is(':checked') == ''
				&& $('input[name=ForroRoto]').is(':checked') == ''
				&& $('input[name=SuelaDesgastada]').is(':checked') == ''
				&& $('input[name=ViraDanada]').is(':checked') == ''
				&& $('input[name=TaconDesgastado]').is(':checked') == ''
				&& $('input[name=TaconPelado]').is(':checked') == ''
				&& $('input[name=Ojetes]').is(':checked') == ''
				&& $('input[name=Punteras]').is(':checked') == ''
				&& $('#OtroDescripcion').val()== ''){
					alert("Debes seleccionar alguna descripcion del estado como se recibe el producto");
					return false;
		}



		/*
		var remonta = $('input:checkbox[name=Remonta]:checked').val();
		var numerofacturaremonta = $('#NumeroFacturaRemonta').val();
		if (remonta=="S" && numerofacturaremonta==""){
			alert("Digite el numero de factura de adelanto de la remonta ");
			return false;
		}
		*/

		var descripcion=$('#Descripcion').val();
		if (descripcion==""){
			alert("Escriba una descripcion de la causa de la garantia.");
			return false;
		}


		var id_empleado=$('#Empleado').val();
		if (id_empleado==""){
			alert("Selecione el empleado");
			return false;
		}


		if (opcion_producto==undefined || opcion_producto==""){
			alert("Seleccione un producto");
			return false;
		}
		else{
			return true;
		}

		return false;

	});



	$( "#frmdetalle" ).submit(function(){
		var Descripcion = $('#Descripcion').val();
		var NumeroGuia = $('#NumeroGuia').val();
		TipoProducto2 = $('#TipoProducto2').val();

		if (TipoProducto2=="T"){
			if(	$('input[name=TipoContrafuerte]').is(':checked') == ''
				&& $('input[name=TipoCuero]').is(':checked') == ''
				&& $('input[name=TipoPlantilla]').is(':checked') == ''
				&& $('input[name=TipoCremallera]').is(':checked') == ''
				&& $('input[name=TipoDespegue]').is(':checked') == ''
				&& $('input[name=TipoCambrion]').is(':checked') == ''
				&& $('input[name=TipoTacon]').is(':checked') == ''
				&& $('input[name=TipoCerco]').is(':checked') == ''
				&& $('input[name=TipoCardado]').is(':checked') == ''
				&& $('input[name=TipoSuela]').is(':checked') == ''
				&& $('input[name=TipoGuarnicion]').is(':checked') == ''
				&& $('input[name=TipoPuntera]').is(':checked') == ''
				&& $('input[name=TipoHerraje]').is(':checked') == ''
				&& $('#TipoOtro').val()== ''){
					alert("Debes seleccionar la Identificacion de la causa de la garantia");
					return false;
				}
		}

		if (NumeroGuia==""){
			alert("Numero de Guia o  Persona a quien entrega es obligatorio");
			return false;
		}

		if (Descripcion==""){
			alert("Digite un comentario al cambio realizado a la garantia");
			return false;
		}


		//Valido el orden logico de cambios de estado
		var estado_garantia_anterior = $('#IDEstadoGarantiaAnt').val();
		var estado_garantia_nuevo = $('#IDEstadoGarantia').val();
		var tipo_producto = $('#TipoProductoGarantia').val();

		if(estado_garantia_nuevo==9){
			var TipoRegistroG = $('input:radio[name=TipoRegistro]:checked').val();
			if(TipoRegistroG=="Restauracion"){
				var NumeroFacturaRestauracion = $('#NumeroFacturaRestauracion').val();
				if(NumeroFacturaRestauracion==""){
					alert("No se puede cambiar de estado, si no digita el número de la factura");
					return false;
				}

			}
		}

		if (estado_garantia_anterior != estado_garantia_nuevo ){
				switch(estado_garantia_anterior) {
					case "1":
						if (estado_garantia_nuevo ==2 || estado_garantia_nuevo ==3 || estado_garantia_nuevo ==4 ){
							return true;
						}
						else{
							alert("No puede cambiar la garantia al estado seleccionado debe seguir el orden de procedimiento.");
							return false;
						}
					break;
					case "2":
							alert("No puede cambiar el estado debe seguir el orden de procedimiento.");
							return false;
					break;
					case "3":
						if (estado_garantia_nuevo==8 ){
							return true;
						}
						else{
							alert("No puede cambiar la garantia al estado seleccionado debe seguir el orden de procedimiento.");
							return false;
						}
					break;
					case "4":
							alert("No puede cambiar el estado debe seguir el orden de procedimiento.");
							return false;
					break;
					case "5":
							if (tipo_producto=="T" && estado_garantia_nuevo==8){
								return true;
							}
							else{
								alert("No puede cambiar el estado debe seguir el orden de procedimiento.");
								return false;
							}
					break;
					case "6":
							alert("No puede cambiar el estado debe seguir el orden de procedimiento.");
							return false;
					break;

					case "7":
						if (estado_garantia_nuevo==8 ){
							return true;
						}
						else{
							alert("No puede cambiar la garantia al estado seleccionado debe seguir el orden de procedimiento.");
							return false;
						}
					break;
					case "8":
						if (estado_garantia_nuevo==9 || estado_garantia_nuevo==11 ){
							return true;
						}
						else{
							alert("No puede cambiar la garantia al estado seleccionado, debe seguir el orden de procedimiento");
							return false;
						}
					break;

					case "9":
						if (estado_garantia_nuevo!=9 ){
							alert("No puede cambiar la garantia al estado seleccionado, debe seguir el orden de procedimiento");
							return false;
						}
					break;


					case "10":
						if (estado_garantia_nuevo==9 ){
							return true;
						}
						else{
							alert("No puede cambiar la garantia al estado seleccionado, debe seguir el orden de procedimiento");
							return false;
						}
					break;



					default:
						return true;
				}
		}
		return false;

	});



	$( "form.formvalida" ).submit(function(){

		var AceptaFidelizar=$('input:radio[name=ClubSuavidad]:checked').val();
		var VerDatosFidelizacion=$('input:radio[name=CSuavidad]:checked').val();
		var ClubSuavidad=$('#ClubSuavidad').val();
		var IDCliente=$('#IDCliente').val();
		var Telefono=$('#Telefono').val().length;
		var Direccion=$('#Direccion').val();
		var CorreoElect=$('#Email').val();
		

		// SI NO ES DEL CLUB SOLO VALIDO POCOS DATOS
		if (AceptaFidelizar=="N" || ClubSuavidad=="N"){
			var caracter_cedula=$('.cedula_no_club').val().length;
			var TelefonoNoClub=$('#telefono_no_club').val().length;
			if (caracter_cedula<5){
				alert ("El numero de Documento debe contener minimo 5 carateres");
				return false;
			}
			else{

			}

			if (TelefonoNoClub<7 || TelefonoNoClub > 11){
				alert ("El numero de telefono debe contener entre 7 y 11 carateres.");
				return false;
			}

			let subcadena = "caprino";
			var respuesta_correo=CorreoElect.toLowerCase().includes(subcadena.toLowerCase());
			if(respuesta_correo){
				alert("El correo no puede contener la palabra calzadocaprino");
				return false;
			}
			


			// valido que no sea el mismo caracter en la cedula
			var cedula_validar=$('.cedula_no_club').val();
			var todas_igual=1;
			var primera_letra = cedula_validar.charAt(0); // letra = H
			for(i=1;i<=caracter_cedula-1;i++){
				caracter=cedula_validar.charAt(i);
				if (caracter==primera_letra && todas_igual==1 ){
					todas_igual=1;
				}
				else{
					todas_igual=0;
				}
			}

			if(todas_igual==1 && cedula_validar !="222222222222"){ // la cedula tiene el mismo caracter
				alert ("Numero de documento invalido, no puede contener el mismo digito");
				return false;
			}



			var nombre_no_club=$('#nombre_no_club').val();
			if (nombre_no_club==""){
				alert ("El nombre es obligatorio" );
				return false;
			}
			if (apellido_no_club==""){
				alert ("El apellido es obligatorio" );
				return false;
			}

			return true;

		}


		if (AceptaFidelizar=="S" || ClubSuavidad=="S"){
		var caracter_cedula=$('.cedula_guarda').val().length;
		var numero_documento_cliente=$('.cedula_guarda').val();

		if (caracter_cedula<5){
			alert ("El numero de Documento debe contener minimo 5 carateres");
			return false;
		}

		if (Telefono<7 || Telefono > 11){
			alert ("El numero de telefono debe contener entre 7 y 11 carateres");
			$('#Telefono').attr("readonly", false);

			return false;
		}

			let subcadena = "caprino";
			var respuesta_correo=CorreoElect.toLowerCase().includes(subcadena.toLowerCase());
			if(respuesta_correo && CorreoElect !="felectronica@calzadocaprino.com" && CorreoElect !="tiendacaprino@gmail.com"){
				alert("El correo no puede contener la palabra calzadocaprino.");
				return false;
			}
			

		var primera_letra_dir = Direccion.charAt(0); // letra = H
		if (!isNaN(primera_letra_dir) && primera_letra_dir!=""){		
			alert("Direccion no valida");
			$('#Direccion').attr("readonly", false);
			return false;
		}

		if (VerDatosFidelizacion=="S"){
			if (Direccion < 13){
				alert ("La direccion debe contener mas de  13 carateres");
				$('#Direccion').attr("readonly", false);
				return false;
			}
		}	

		// valido que no sea el mismo caracter en la cedula
		var cedula_validar=$('.cedula_guarda').val();
		var todas_igual=1;
		var primera_letra = cedula_validar.charAt(0); // letra = H
	    for(i=1;i<=caracter_cedula-1;i++){
			caracter=cedula_validar.charAt(i);
			if (caracter==primera_letra && todas_igual==1 ){
				todas_igual=1;
			}
			else{
				todas_igual=0;
			}
		}

		if(todas_igual==1 && cedula_validar !="222222222222"){ // la cedula tiene el mismo caracter
			alert ("Numero de documento invalido, no puede contener el mismo digito");
			return false;
		}


		var id_empleado_fideliza=$('#IDEmpleadoFideliza').val();
		var id_tarjeta_fidelizacion=$('#IDTarjetaFidelizacion').val();
		var Email_dato=$('#Email').val();
		var NuevoNumeroTarjeta=$('#NumeroTarjetaNuevo').val();
		var Celular_dato=$('#Celular').val();
		var Direccion_dato=$('#Direccion').val();
		var numero_tarjeta=$('#NumeroTarjeta').val();
		var caracter_tarjeta=$('#NumeroTarjeta').val().length;
		var respuesta_valida="";

		if(Email_dato=="" && Celular_dato=="" && Direccion_dato=="" ){
			alert ("Debe ingresar el Email o Celular o Direccion.");
			return false;
		}

		if (id_empleado_fideliza=="" || id_empleado_fideliza=="undefined"){
					alert("Seleccione el empleado");
					return false;
		}


				var Genero=$('input:radio[name=Genero]:checked').val();
				if (Genero!="M" && Genero!="F" ){
					alert("El campo Genero es obligatorio");
					return false;
				}

				/*
				var AutorizaMail=$('input:radio[name=AutorizaMail]:checked').val();
				if (AutorizaMail!="S" && AutorizaMail!="N" ){
					alert("El campo Autoriza Mail es obligatorio");
					return false;
				}

				var AceptaSMS=$('input:radio[name=AceptaSMS]:checked').val();
				if (AceptaSMS!="S" && AceptaSMS!="N" ){
					alert("El campo Acepta SMS es obligatorio");
					return false;
				}

				var AceptaTerminos=$('input:radio[name=AceptaTerminos]:checked').val();
				if (AceptaTerminos!="S"){
					alert("Debe aceptar los terminos y condiciones para poder registrarse");
					return false;
				}

				
				*/

				var AceptaHabeas=$('input:radio[name=AceptaHabeas]:checked').val();
				if (AceptaHabeas!="S" && AceptaHabeas!="N" ){
					alert("El campo Acepta Ley Habeas data es obligatorio");
					return false;
				}

				/*
				//Valido el numero de la tarjeta
				if (caracter_tarjeta!=10){
					alert ("El numero de tarjeta no es valido, por favor verifique");
					return false;
				}

				//verifico que exista la tarjeta y que no haya sido asignada a nadie cuando es nuevo

				if (NuevoNumeroTarjeta!="" && (typeof NuevoNumeroTarjeta != "undefined")){
					if (NuevoNumeroTarjeta==numero_tarjeta){
						alert("El nuevo numero de la tarjeta no puede ser igual al actual");
						return false;
					}
					var motivo_cambio=$('#Observacion').val();
					if (motivo_cambio==""){
						alert("Debe digitar el motivo del cambio");
						return false;
					}

					var verifica_tarjeta_nuevo="";
					verifica_tarjeta_nueva=validar_numero_tarjeta(NuevoNumeroTarjeta,cedula_validar);
					if (verifica_tarjeta_nueva!=""){
						return false;
					}
				}

					var verifica_tarjeta="";
					verifica_tarjeta=validar_numero_tarjeta(numero_tarjeta,cedula_validar,IDCliente);
					if (verifica_tarjeta!=""){
						return false;
					}
			*/

			/*		
			if (numero_tarjeta!=numero_documento_cliente && id_tarjeta_fidelizacion=="" ){
					alert("El numero de tarjeta debe ser el numero de documento del cliente");
					return false;
			}
			*/



		}


		if( EvaluaReg( this ) )
		{
			//eliminar los repetidos del formulario de creacion de clientes

			if( $( ".radioClubSuavidad:checked" ).val() === "S" )
			{
				$(".noFidelizar input").remove();
				$(".noFidelizar select").remove();
			}//end if
			else
				if( $( ".radioClubSuavidad:checked" ).val() === "N" )
				{
					$(".siFidelizar input").remove();
					$(".siFidelizar select").remove();

				}//end else

			return true;

		}//end if
		else
			return false;
	});

	$( ".btnPuntos a" ).click(function(){
		$( ".tblPuntos" ).toggle();
		return false;
	});

	function interval(indice){
		var n = 0;
		var l = document.getElementById("segundos");
		window.setInterval(function(){			
			$("#segundos"+indice).val(n);
			n++;			
		},1000);
	};

	$("#NumeroRegistro").keypress(function( evt ){

		var key = nav4 ? evt.keyCode : evt.which;
		var NumCaracteres=$(this).val().length;
		if(NumCaracteres>=3 ){
			$("#ObservacionDescuento").attr("readonly", "");
		}	
		else{
			$("#ObservacionDescuento").val("");
			$("#ObservacionDescuento").attr("readonly", "readonly");			
		}
	});

	$("#NumeroRegistro").keydown(function( event ){
		var NumCaracteres=$("#NumeroRegistro").val().length;
        // Verifica si la tecla presionada es Backspace
        if(NumCaracteres>=3 ){
			$("#ObservacionDescuento").attr("readonly", "");
		}	
		else{
			$("#ObservacionDescuento").val("");
			$("#ObservacionDescuento").attr("readonly", "readonly");			
		}
    });
	

	$( "input.tboxReferencia" ).keypress(function( evt ){

		var key = nav4 ? evt.keyCode : evt.which;

		var NumCaracteres=$(this).val().length;
		var valor_texto = $(this).val();
		var resultado = valor_texto.indexOf("$%&");
		var puntoventa = String($("#IDPuntoVenta").val());
		var indice = $(this).attr("rel");

		//if(NumCaracteres==1){
			interval(indice);
		//}	

		if(valor_texto=="990011" || resultado>=0 || valor_texto=="CREM" || valor_texto=="crem" || valor_texto=="TARJETA1"){
				$("#btn_referencia"+indice).show();
				if(valor_texto=="CREM" || valor_texto=="crem"){
					window.open('Referencia/popReferencias.php?field=Numero&QryString=crem&action=list&mod=busuario&cont='+indice+'&IDPuntoVenta='+puntoventa+'&submit=Buscar','','scrollbars = yes,width=600,height=500');
				}
				if(valor_texto=="TARJETA1"){
					document.frm.elements["CodigoTarjetaDigitado"+indice].style.display = "block";
				}
				else{
					document.frm.elements["CodigoTarjetaDigitado"+indice].style.display = "none";
				}
				return false;
			}
			else{

				if( key == 13  )
				{

					get_referencia( $(this).val(), $(this).attr("rel") );
					return false;
				}//end if


				if( key == 9  )
				{

					get_referencia( $(this).val(), $(this).attr("rel") );
					return false;
				}//end if
			}

	});

	$( "input.tboxReferencia" ).blur(function( evt ){
		var valor_texto = $(this).val();
		var indice = $(this).attr("rel");
		var resultado = valor_texto.indexOf("$%&");
		var puntoventa = String($("#IDPuntoVenta").val());
		var codiftarjeta = $("#TarjetaCodificacionEspecifica").val();
		var segundos_tardo = parseInt($("#segundos"+indice).val());		



			//if(valor_texto=="123456" || valor_texto=="!\"#$%&\'()*"){
			if(valor_texto=="990011" || resultado>=0 || valor_texto=="CREM" || valor_texto=="crem" || valor_texto=="TARJETA1"){
				$("#btn_referencia"+indice).show();
				if(valor_texto=="CREM" || valor_texto=="crem"){
					window.open('Referencia/popReferencias.php?field=Numero&QryString=crem&action=list&mod=busuario&cont='+indice+'&IDPuntoVenta='+puntoventa+'&submit=Buscar','','scrollbars = yes,width=600,height=500');
				}
				if(valor_texto=="TARJETA1"){
					document.frm.elements["CodigoTarjetaDigitado"+indice].style.display = "block";
					$(this).val("TARJETA");
					document.frm.elements["Talla"+indice].value = "1";
					document.frm.elements["Nombre"+indice].value = "TARJETA";
					$('#ValorU'+indice).attr('readonly', false);
					document.frm.elements["CodigoTarjeta"+indice].value = $("#TarjetaConsecutivo"+indice).val();
					$('#IDCodificacion'+indice).val(codiftarjeta);

				}
				else{
					document.frm.elements["CodigoTarjetaDigitado"+indice].style.display = "none";
					document.frm.elements["Talla"+indice].value = "";
					document.frm.elements["Nombre"+indice].value = "";
					$('#ValorU'+indice).attr('readonly', true);
					$('#IDCodificacion'+indice).val("");
				}


				return false;
			}
			else{
				if(segundos_tardo>=1){
					//alert("Debe utilizar la pistola de código : "+segundos_tardo+" segundos");
					alert("Debe utilizar la pistola de codigo : "+segundos_tardo);					
					$(this).val("");
					location.reload();
				}
				$("#btn_referencia"+indice).hide();
				get_referencia( $(this).val(), $(this).attr("rel") );
			}

		return false;
	});//end function

	$( "input.tboxCodTarjeta" ).change(function( evt ){
					var valor_texto = $(this).val();
					if(valor_texto!=""){						
						$.ajax( {
								type : "POST",
								data : { "CodTarjeta" : valor_texto },
								dataType : "json",
								url : "includes/bono/tarjeta.async.php" ,
								success : function( data ){
									data = data.column;
									var IDTarjeta = data.CodigoTarjeta
									var ValorTarjeta = data.ValorTarjeta
									
									if(IDTarjeta==""){
										alert("La tarjeta "+valor_texto+" no existe, por favor verifique");
										document.frm.elements["CodigoTarjeta"].value = "";
										document.frm.elements["ValorBono"].value = "";										
										return false;
									}
									else{
										document.frm.elements["ValorBono"].value = ValorTarjeta;
									}
								},
								error: function(jqXHR, textStatus, errorThrown){
									alert("error!");
								},
								complete: function(jqXHR, textStatus){

								}
							});
			}

			
		return false;
	});//end function

	

	$( "input.tboxBonoRegalo" ).change(function( evt ){

		$( ".tboxBonoRegalo" ).each(function( index ) {
					var valor_texto = $(this).val();
					if(valor_texto!=""){
						var consecutivo = $(this).attr("consecutivo");
						document.frm.elements["BonoValido"].value = "S";
						$.ajax( {
								type : "POST",
								data : { "Bono" : valor_texto },
								dataType : "json",
								url : "includes/bono/bono.async.php" ,
								success : function( data ){
									data = data.column;
									var IDBono = data.IDBonoZarate
									var BonoDisponible = data.Disponible

									if(IDBono==""){
										alert("El bono "+consecutivo+" no existe, por favor verifique");
										document.frm.elements["ValorBono"].value = "";
										document.frm.elements["BonoValido"].value = "N";
										return false;
									}

									if(BonoDisponible!="S"){
											alert("El bono "+consecutivo+" ya fue utilizado, por favor verifique");
											document.frm.elements["ValorBono"].value = "";
											document.frm.elements["BonoValido"].value = "N";
											return false;
									}




								},
								error: function(jqXHR, textStatus, errorThrown){
									alert("error!");
								},
								complete: function(jqXHR, textStatus){

								}
							});

					}

		});


			var valor_texto = $(this).val();


		return false;
	});//end function

	//para fidelizar o no

	$( ".radioClubSuavidad" ).click(function(){
		if( $( this ).val() === "S" )
		{
			$(".siFidelizar").show();
			$(".siFidelizar input[type='text']").addClass("mandatory");

			$(".noFidelizar").hide();
			$(".noFidelizar input[type='text']").removeClass("mandatory");
		}//end if
		else
		{
			$(".siFidelizar").hide();
			$(".siFidelizar input[type='text']").removeClass("mandatory");

			$(".noFidelizar").show();
			$(".noFidelizar input[type='text']").addClass("mandatory");
		}//end else
	});





		$( ".TipoRegistroGarantia" ).click(function(){
		if( $( this ).val() == "Servicio" )
		{
			document.getElementById('divreproceso').style.display = 'block';
		}
		else{
			if($( this ).val() == "Restauracion" ){
				document.getElementById('divrestauracion').style.display = 'block';
			}
			else{
				document.getElementById('divreproceso').style.display = 'none';
			}
		}

	});




		var ctx = document.getElementById("canvas").getContext("2d");
		window.myBar = new Chart(ctx).Bar(barChartData, {
			responsive : true
		});

		var ctx_pie = document.getElementById("chart-area").getContext("2d");
		window.myPie = new Chart(ctx_pie).Pie(pieData);






});


function get_referencia( referencia, rel )
{

$.ajax( {
		type : "POST",
		data : { "Referencia" : referencia },
		dataType : "json",
		url : "includes/referencia/referencia.async.php" ,
		success : function( data ){
			data = data.column;

			selreferencia(data.referencia, data.nombre, data.talla, data.codificacion, rel, data.existencias, data.valor, data.descuento,'',data.sexo,data.tipotalla,data.tiporeferencia);
			$("#Cantidad"+rel).val( 1 );
			$("#Cantidad"+rel).focus();
			$("#Cantidad"+rel).blur();
			//console.log(data);
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert("error!");
		},
		complete: function(jqXHR, textStatus){

		}
	});

	return false;

}//end function


function validar_numero_tarjeta(numero_tarjeta,cedula,IDCliente){
	var respuesta="";

				$.ajax({

					async : false,
					type: 'POST',
					url: 'includes/tarjeta/verifica_tarjeta.async.php',
					dataType : "json",
					data : "Codigo="+numero_tarjeta+"&Documento="+cedula+"&IDCliente="+IDCliente,
					success: function(data) {
							if(data == "no_existe") {
									alert("El numero de tarjeta asignado no es valido");
									respuesta=1;
							}
							if (data == "asignado"){
									alert("Numero de tarjeta invalido, ya fue asignado anteriormente");
									respuesta=1;
							}
					}
				});

return 	respuesta;
}



function valida_num_tarjeta(){
		var consecutivo = $(this).attr("rel");
		var i;
		var CodigoTarjeta=0;
		var CodigoTarjetaDigitado;
		var CodigoConsecutivo = 0;

		//Valido si en la compra viene alguna tarjeta de regalo
		for(i=1;i<=10;i++){
			CodigoTarjeta = $("#CodigoTarjeta"+i).val();
			CodigoConsecutivo = $("#TarjetaConsecutivo"+i).val();
			if(CodigoTarjeta!=""){
				CodigoTarjetaDigitado = $("#CodigoTarjetaDigitado"+i).val();
				if(CodigoConsecutivo!=CodigoTarjetaDigitado){
					alert("El numero de tarjeta digitado ("+CodigoTarjetaDigitado+") no corresponde al siguiente que debe entregar, por favor verifique COD_0001AAAB::"+CodigoConsecutivo+"::B333X0");
					$("#CodigoTarjetaDigitado"+i).val("");
					return false;
				}
			}

		}
		return true;
}


function EvaluaReg( formEval )
{

	var fields = $( formEval ).find( ".obligatorio" ).get();

	for( var i = 0 ; i < fields.length ; i++ )
	{

		if( fields[i].type == 'checkbox' )//if( $( fields[i] + ":checkbox" ) )
    	{
    		if( !$(fields[i]).is(":checked") )
    		{
    			alert( "El campo " + $( fields[i] ).attr( "title" ) + " se encuentra vacio y es obligatorio" );
				$( fields[i] ).focus();
				return false
    		}
    	}
		else
		{

			if( $( fields[i] ).val() == "" )
			{



				alert( "El campo " + $( fields[i] ).attr( "title" ) + " se encuentra vacio y es obligario." );
				//$("p.error").show();
				return false;
			}


		}

		if ($(fields[i]).hasClass("valmail"))//Validar mail
    	{
    		var expresion = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
			if (!expresion.test($(fields[i]).val()))
			{
				alert("El campo " + $(fields[i]).attr("title") + " no es un mail valido");
				$(fields[i]).focus();
				return false;
			}
		}


	}






	return true;
}
