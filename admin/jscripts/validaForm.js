function EvaluaReg2(Form,Check){
	var CheckMail = new Array("Mail");
	var CheckPsw = new Array("Password","RePasswd");
	for(i=0; i<Form.elements.length; i++){
		for(j=0; j< Check.length; j++)
			if(Form.elements[i].name==Check[j])
				if (Form.elements[i].value==""){
					window.alert("El campo "+ Form.elements[i].id +" es obligatorio.");
					enable(Form);
					Form.elements[i].focus();
					return false;
				}


				if ( Form.Descuento.value != "" && Form.NumeroPagare.value == ""){
					window.alert("El numero de pagare es obligatorio, por favor verifique");
					enable(Form);
					Form.NumeroPagare.focus();
					return false;
				}

				if ( Form.Descuento.value != "" && Form.NumeroPagare.value != ""){
					var numero_pagare= Form.PagareConsecutivo.value;
					if(numero_pagare!=Form.NumeroPagare.value){
						window.alert("El numero de pagare no corresponde al siguiente");
						enable(Form);
						Form.NumeroPagare.focus();
						return false;
					}
				}


		for(j=0; j< CheckMail.length; j++)
			if(Form.elements[i].name==CheckMail[j])
				if (Form.elements[i].value.indexOf ('@',0) == -1 || Form.elements[i].value.indexOf ('.',0) == -1) {
					window.alert("Verifique el "+ CheckMail[j] +" es obligatorio \n y debe ser un EMail valido");
				 	Form.disabled = false;document.body.style.cursor = 'default';
				 	Form.elements[i].focus();
					return false;
				}

      	if  (Form.elements[i].id=="psw"){
			var cadena = Form.Password.value;
			flag=formato(cadena);
			if (Form.action.value=="insert"){
					if ((Form.Password.value == "") || (Form.Password.value != Form.RePasswd.value)){
						window.alert("Por favor verifique el password");
						enable(Form);
						Form.Password.value = ''; Form.RePasswd.value = '';
						PasswdForm.Password.focus();
						return false;
					}
			}



			if ( Form.Password.value != ""){
					if (Form.Password.value != Form.RePasswd.value){
						window.alert("Por favor verifique el password");
						enable(Form);
						Form.Password.value = ''; Form.RePasswd.value = '';
						Form.Password.focus();
						return false;
					}
			}

			if(flag)
				return true;
			else {
				Form.Password.value = ''; Form.RePasswd.value = '';
				enable(Form);
				Form.Password.focus();
				return false;
			}

      	}

	}
	return true;
}



function EvaluaReg(Form,Check){


	var CheckMail = new Array("Mail");
	var CheckPsw = new Array("Password","RePasswd");
	for(i=0; i<Form.elements.length; i++){
		for(j=0; j< Check.length; j++)
			if(Form.elements[i].name==Check[j])
				if (Form.elements[i].value==""){
					window.alert("El campo "+ Form.elements[i].id +" es obligatorio.");
					//enable(Form);
					Form.elements[i].focus();
					return false;
				}

		for(j=0; j< CheckMail.length; j++)
			if(Form.elements[i].name==CheckMail[j])
				if (Form.elements[i].value.indexOf ('@',0) == -1 || Form.elements[i].value.indexOf ('.',0) == -1) {
					window.alert("Verifique el "+ CheckMail[j] +" es obligatorio \n y debe ser un EMail valido");
				 	Form.disabled = false;document.body.style.cursor = 'default';
				 	Form.elements[i].focus();
					return false;
				}

      	if  (Form.elements[i].id=="psw"){
			var cadena = Form.Password.value;
			flag=formato(cadena);
			if (Form.action.value=="insert"){
					if ((Form.Password.value == "") || (Form.Password.value != Form.RePasswd.value)){
						window.alert("Por favor verifique el password");
						enable(Form);
						Form.Password.value = ''; Form.RePasswd.value = '';
						PasswdForm.Password.focus();
						return false;
					}
			}

			if ( Form.Password.value != ""){
					if (Form.Password.value != Form.RePasswd.value){
						window.alert("Por favor verifique el password");
						enable(Form);
						Form.Password.value = ''; Form.RePasswd.value = '';
						Form.Password.focus();
						return false;
					}
			}

			if(flag)
				return true;
			else {
				Form.Password.value = ''; Form.RePasswd.value = '';
				enable(Form);
				Form.Password.focus();
				return false;
			}

      	}

	}
	return true;
}

var nav4 = window.Event ? true : false;
function acceptNum(evt){
// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57
var key = nav4 ? evt.which : evt.keyCode;
	return (key <= 13 || (key >= 48 && key <= 57));
}

function formato(cadena){
	var flag=true;
	var ind=0;
	for(i=0;i<cadena.length;i++){
		ind=cadena.charCodeAt(i);
		if(ind<48 || ind>57 && ind<65 || ind>90 && ind<96 || ind>123){
			alert("Valor no aceptado. Verifique que el texto no contenga espacios y/o caracteres especiales (@,%,$,#,etc...)")
			i=cadena.length;
			flag=false;
		}
	}
	return flag;
}

function valbuscar(Form){
	if ( Form.field.value=="" && Form.QryString.value!="" ){
		alert('En "Buscar por" escoja de la lista un campo');
		enable(Form);
		Form.field.focus();
		return false;
	}
	if ( Form.field.value!="" && Form.QryString.value=="" ){
		alert("Debe escribir un criterio de busqueda");
		enable(Form);
		Form.QryString.focus();
		return false;
	}
	return true;
}
