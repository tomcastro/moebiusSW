<html>
<head>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
   
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<link rel="src" href="index.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

	<script src="https://cdn.datatables.net/1.10.6/js/jquery.dataTables.min.js"></script>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.6/css/jquery.dataTables.min.css"</script>

</head>

<body>

<script> $(document).ready(function() {
    	$('#table').DataTable();} ); </script>

<?php

	include ("connectionConfig.php");

	if (!$db_connection)
	{
		die('Error: Could not connect: ' . pg_last_error());
	}

	$query = 'SELECT * FROM alerts ORDER BY id';
	$result = pg_query($query);

	table($result);

	function table($alertQuery)
	{
		echo '<div id="tablespace"><br><br>';
		echo '<table id="table" class="table table-bordered"><thead><tr>';
		echo "<td>ID</td>";
		echo "<td>Nodo</td>";
		echo "<td>Mensaje</td>";
		echo "<td>Última nota</td>";
		echo "<td>Objeto</td>";
		echo "<td>Fecha y hora</td>";
		echo "<td>Importancia</td>";
		//echo "<td>Estado</td>";
		echo "<td>Estado de OS</td>";
		echo "<td>ID de OS</td>";
		echo "<td>Generar OS</td>";
		echo "<td>Ignorar alerta</td>";
		echo '</tr></thead><tbody>';

		while ($alert = pg_fetch_object($alertQuery)) 
		{
			echo "<tr class=";
			if($alert->severity ==='Warning'){echo"warning";}
			else if ($alert->severity ==='CRITICAL'){echo"danger";}  
			echo ">";

			echo "<td>".$alert->id."</td>";
			echo "<td>".getNodeName($alert)."</td>";
			echo "<td>".$alert->message."</td>";
			echo "<td>".$alert->lastnote."</td>";
			echo "<td>".$alert->object."</td>";
			echo "<td>".$alert->timeraised."</td>";			
			echo "<td>".ucfirst(strtolower($alert->severity))."</td>";
			//echo "<td>".ucfirst(strtolower($alert->state))."</td>";
			echo moebiusStatus($alert->moebiusos_status);
			echo "<td>".$alert->moebiusos_id."</td>";

			if ($alert->moebiusos_status == 1 or $alert->moebiusos_status == 4){echo "<td><a class='btn btn-block btn-success' href=create.php?a=".$alert->id.">Generar</a></td>";}
			else{echo "<td></td>";}

			if ($alert->moebiusos_status == 1){echo "<td><a class='btn btn-block btn-warning' href=ignore.php?a=".$alert->id.">Ignorar</a></td>";}
			else{echo "<td></td>";}

			echo "</tr>";
		}

		echo '</tbody></table></div>';
	}


	function moebiusStatus($status)
	{
		switch ($status)
		{
			case 1: return "<td>Orden no generada</td>"; break;
			case 2: return "<td>Orden generada</td>"; break;
			case 3: return "<td>Orden completada</td>"; break;
			case 4: return "<td>Alerta ignorada</td>"; break;
		}
	}

	function getNodeName($alert)
	{
		$id = $alert->id;
		$query = "SELECT value FROM alertdata WHERE name = 'objectName' AND alert_id = $id";
		$result = pg_query($query);
		while ($alert = pg_fetch_object($result))
		{
			if ($alert->value)
			{
				return $alert->value;
			}
		}
	}
?>
</body>
</html>

