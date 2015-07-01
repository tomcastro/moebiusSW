<script>

	function alertok ()
	{
		alert("La alerta ha sido ignorada.");
	}

</script>

<?php


$db_connection = pg_connect("host=192.168.40.129 port=5432 dbname=AlertCentral user=postgres password=postgres");

	if (!$db_connection)
	{
		die('Error: Could not connect: ' . pg_last_error());
	}

	if(isset($_REQUEST['a'])){
	
		$id = $_REQUEST['a'];
		$query = "INSERT INTO moebiusos(idalert, moebius_status, moebius_timeraised) VALUES ($id, 4, NOW())";
		$result = pg_query($query);
		
		header('Location: http://localhost:8080/index.php?mess=2');
	}


?>