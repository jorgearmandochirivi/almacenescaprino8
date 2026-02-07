autoDetect = false;
timeOutSecs = 5; 	// luego de 5 segundos, el bot—n se habilitar‡ de nuevo, 
					// para el caso de que el servidor deje de responder y el usuario 
					// necesite volver a submitir. 


function iniciar(e){
	if (autoDetect){
		for (var a=0;a<document.forms.length;a++){
			document.forms[a].onsubmit = disable; // adjunta la funci—n disable a todos los forms
		}
	}
}

function enable(el)
{
		for (var b=0;b<el.elements.length;b++){ // por cada elemento del form
			var formEl = el.elements[b];
			// si el elemento es un bot—n de submit
			if ((formEl.tagName == "INPUT") && (formEl.getAttribute("type") != null) && ((formEl.getAttribute("type").toLowerCase() == "submit") )) {
				formEl.disabled = false; // desactivar bot—n
				document.getElementsByTagName("body")[0].style.cursor = 'default'; // relojito
			}
		}
}

function disable(e){ // asigna funci—n al submit y pasa el evento como argumento
	if (document.getElementById) { // chekea que el navegador soporte. Sino lo hace, se ignora el efecto.
		if (autoDetect){ // toma el objeto FORM desde el evento
			if (!e) {e = document.parentWindow.event;} // mozilla pasa (e) pero IE no, as’ que tambiŽn usamos su forma de obtener el evento
			var el = e.target || e.srcElement; // obtener el elemento de donde sali— el evento, para mozilla o explorer
		} else { // toma el objeto FORM si se pasa manualmente
			el = e;
		}
		while (el.tagName != "FORM"){ el = el.parentNode;} // mozilla pasa el input como source del submit. busco entonces el form de ese input.
		for (var b=0;b<el.elements.length;b++){ // por cada elemento del form
			var formEl = el.elements[b];
			// si el elemento es un bot—n de submit
			if ((formEl.tagName == "INPUT") && (formEl.getAttribute("type") != null) && ((formEl.getAttribute("type").toLowerCase() == "submit") )) {
				formEl.disabled = true; // desactivar bot—n
				document.getElementsByTagName("body")[0].style.cursor = 'wait'; // relojito
				setTimeout(function(){formEl.disabled = false;document.body.style.cursor = 'default';},timeOutSecs*1000)
			}
		}
	}
}
