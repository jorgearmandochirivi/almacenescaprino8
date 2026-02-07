<script>
/***********************************************
* DD Tab Menu II script- ? Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

//Set tab to intially be selected when page loads:
//[which tab (1=first tab), ID of tab content to display]:
<?
	switch($mod)
	{
		case "NovedadBanco":
			$tab = 2;
			$m="Banco";
		break;
		case "Empleado":
			$tab = 4;
		break;
		case "Movimiento":
			$tab = 2;
			$m = "Movimiento";
		break;
		case "MovimientoTercero":
			$tab = 2;
			$m = "Movimiento";
		break;
		case "IngresoOtros":
			$tab = 2;
			$m = "Movimiento";
		break;
		case "SalidaMerca":
			$tab = 2;
			$m = "Movimiento";
		break;
		
		case "cambioreferencia":
			$tab = 2;
			$m = "Movimiento";
		break;
		case "FacturaBono":
			$tab = 2;
			$m = "Movimiento";
		break;
		case "vercambios":
			$tab = 2;
			$m = "Movimiento";
		break;
		case "verentrada":
			$tab = 2;
			$m = "Movimiento";
		break;
		case "VerMovimiento":
			$tab = 2;
			$m = "Movimiento";
		break;
		case "Pedido":
			$tab = 3;
			$m = "Pedido";
		break;
		case "Traslado":
			$tab = 4;
			$m = "Traslado";
		break;
		case "vertraslado":
			$tab = 4;
			$m = "Traslado";
		break;
		case "RecibirTraslado":
			$tab = 4;
			$m = "Traslado";
		break;
		case "Inventario":
		
			$tab = 5;
			$m = "Inventario";
		break;
		case "InventarioCon":
		
			$tab = 5;
			$m = "Inventario";
		
		case "InventarioConalm":
		
			$tab = 5;
			$m = "Inventario";
		break;
		case "BuscReferencia":
		
			$tab = 5;
			$m = "Inventario";
		break;
		case "diario":
		
			$tab = 6;
			$m = "Reporte";
		break;
		
		case "diariocredito":
			$tab = 6;
			$m = "Reporte";
		break;
		
		case "mensual":
		
			$tab = 6;
			$m = "Reporte";
		break;
		
		case "vendedores":
		
			$tab = 6;
			$m = "Reporte";
		break;

		case "Garantia":		
			$tab = 7;
			$m = "Garantia";
		break;

		case "SeguimientoGarantia":		
			$tab = 7;
			$m = "Garantia";
		break;

		case "GarantiaReporte":		
			$tab = 7;
			$m = "Garantia";
		break;
		
		case "Pqr":		
			$tab = 8;
			$m = "Pqr";
		break;
		
		case "SeguimientoPqr":		
			$tab = 8;
			$m = "Pqr";
		break;



		default :
			$tab = 1;
			$m = "Factura";
		break;
	}
?>
	var initialtab=[<?=$tab?>, "<?=$m?>"]



//Turn menu into single level image tabs (completely hides 2nd level)?
var turntosingle=0 //0 for no (default), 1 for yes

//Disable hyperlinks in 1st level tab images?
var disabletablinks=0 //0 for no (default), 1 for yes


////////Stop editting////////////////

var previoustab=""

if (turntosingle==1)
document.write('<style type="text/css">\n#tabcontentcontainer{display: none;}\n</style>')

function expandcontent(cid, aobject){
if (disabletablinks==1)
aobject.onclick=new Function("return false")
if (document.getElementById && turntosingle==0){
highlighttab(aobject)
if (previoustab!="")
document.getElementById(previoustab).style.display="none"
document.getElementById(cid).style.display="block"
previoustab=cid
}
}

function highlighttab(aobject){
if (typeof tabobjlinks=="undefined")
collectddimagetabs()
for (i=0; i<tabobjlinks.length; i++)
tabobjlinks[i].className=""
aobject.className="current"
}

function collectddimagetabs(){
var tabobj=document.getElementById("ddimagetabs")
tabobjlinks=tabobj.getElementsByTagName("A")
}

function do_onload(){
collectddimagetabs()
expandcontent(initialtab[1], tabobjlinks[initialtab[0]-1])
}

if (window.addEventListener)
window.addEventListener("load", do_onload, false)
else if (window.attachEvent)
window.attachEvent("onload", do_onload)
else if (document.getElementById)
window.onload=do_onload
</script>