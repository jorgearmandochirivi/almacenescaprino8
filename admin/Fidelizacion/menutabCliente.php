<link rel="stylesheet" href="../default.css" type="text/css">
<body><script language=javascript><!--
var clrOver = "02387A"; 
var clrIn = "6bb2d4"; 
var TABSelected = "TB" + <%=$TABsel%>;

function mOvr(src) {
	if(document.getElementById(TABSelected) != src)
		src.bgColor = clrOver;
}
function mOut(src) {

	if(document.getElementById(TABSelected) != src)
		src.bgColor = clrIn;
}
function selTAB(TABSelected){
	src.bgColor = clrOver;
}
function TABoff()
{
	for(i=1;i<=2;i++)
	{
       if(i != <%=$TABsel%>){
     		var TAB = "TB" + i;
	    	document.getElementById(TAB).bgColor = clrIn;
		}
    }
}
-->
</script>

	
		
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td>
					<table border="0" cellspacing="0" cellpadding="0" bgcolor="#02387A" id=TB1>
						<tr height="16">
							<td class="LeftCurve" valign="top" align="left" width="13" height="16" nowrap>
								&nbsp;&nbsp;&nbsp;
							</td>
							<td valign="top" nowrap  height="16"><a href="./?mod=ClienteFidelizado&action=edit&id=<?=$idCliente?>" class="TAB" onMouseOut="mOut(TB1);" onMouseOver="mOvr(TB1);">Clientes <?php $r->ClubSuavidad ?> </a>&nbsp;
</td>
							<td align="right" class="RightCurve" width="10" nowrap height="16">&nbsp;&nbsp;</td>
						</tr>
					</table>
				</td>
				<td width="4"></td>

				<?php if($r->ClubSuavidad=="S"){ ?>
				<td>
					<table border="0" cellspacing="0" cellpadding="0" bgcolor="#02387A" id=TB2>
						<tr height="16">
							<td class="LeftCurve" valign="top" align="left" width="12" height="16" nowrap>&nbsp;&nbsp;&nbsp;</td>
							<td valign="top" nowrap onMouseOut="mOut(TB2);" onMouseOver="mOvr(TB2);" height="16"><a href="./?mod=PuntosClienteFidelizacion&idCliente=<?=$idCliente?>" class="TAB">Puntos Fidelizaci&oacute;n</a></td>
							<td align="right" class="RightCurve" width="10" nowrap height="16">&nbsp;&nbsp;</td>
						</tr>
					</table>
				</td>
				<td width="4"></td>
				<td>
					<table border="0" cellspacing="0" cellpadding="0" bgcolor="#02387A" id=TB3>
						<tr height="16">
							<td class="LeftCurve" valign="top" align="left" width="12" height="16" nowrap>&nbsp;&nbsp;&nbsp;</td>
							<td valign="top" nowrap onMouseOut="mOut(TB3);" onMouseOver="mOvr(TB3);" height="16"><a href="./?mod=ExcedenteFidelizacion&idCliente=<?=$idCliente?>" class="TAB">$$ Excedentes Dinero</a></td>
							<td align="right" class="RightCurve" width="10" nowrap height="16">&nbsp;&nbsp;</td>
						</tr>
					</table>
				</td>
				<td width="4"></td>
				<td>
					<table border="0" cellspacing="0" cellpadding="0" bgcolor="#02387A" id=TB4>
						<tr height="16">
							<td class="LeftCurve" valign="top" align="left" width="12" height="16" nowrap>&nbsp;&nbsp;&nbsp;</td>
							<td valign="top" nowrap onMouseOut="mOut(TB4);" onMouseOver="mOvr(TB3);" height="16"><a href="./?mod=BonoFidelizacionCliente&idCliente=<?=$idCliente?>" class="TAB">Bonos</a></td>
							<td align="right" class="RightCurve" width="10" nowrap height="16">&nbsp;&nbsp;</td>
						</tr>
					</table>
				</td>
				<?php } ?>
				<td width="4"></td>

                
			</tr>
		</table>
<script language=javascript>
TABoff();
</script>