<%
include("../../admin/config.inc.php3");
$datos = Verifica_SesionCliente();
	$Nombre_Usuario = $datos["Nombre"];
	$IDUsuario=$datos["IDUsuario"];
	$Nivel =  $datos["Nivel"];
	
$qry_verificarvoto = db_query("SELECT * FROM $poll_userTableName WHERE pollID='$poll_id' AND IDUsuario='$IDUsuario'");
	
	if(db_num_rows($qry_verificarvoto)<1)
		{
			
			$poll_result = mysql_db_query($poll_dbName, "UPDATE $poll_dataTableName SET optionCount=optionCount+1 WHERE (pollID=$poll_id) AND (voteID=$poll_voteNr)");
			$result_user= mysql_db_query($poll_dbName, "INSERT $poll_userTableName (pollID,voteID,IDUsuario) VALUES ($poll_id,$poll_voteNr,'$IDUsuario')");	
			if($poll_result && $result_user)
			{
	
				%>
				<script languaje ="JavaScript"> 
					location.href='<%echo $posicion;%>';
				</script>
				<%
			}
		}//end if($num<0)
		else
		{
			window_alert("Ud ya opino en esta encuesta!")
		%>
			<script languaje ="JavaScript"> 
					location.href='<%echo $posicion;%>';
			</script>
		<%
		}
%>

