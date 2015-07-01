<?php

	include ("fakeapi.php");

	$db_connection = pg_connect("host=192.168.40.129 port=5432 dbname=AlertCentral user=postgres password=postgres");

	if (!$db_connection)
	{
		die('Error: Could not connect: ' . pg_last_error());
	}
	
	if(isset($_REQUEST['a']))
	{
		$id = $_REQUEST['a'];
		$query = "SELECT a.message, m.moebius_status FROM alerts as a LEFT JOIN moebiusos as m ON a.id = m.idalert WHERE a.id = $id";
		$result = pg_query($query);

		while ($alert = pg_fetch_object($result))
		{
			$res = API($id, $alert->message);
		
			if (!$res)
			{
				echo "OS no generada";
			}
			else 
			{
				$time = new DateTime('NOW');
				$time = $time->format(DateTime::ISO8601);

				if($res)
				{
					$res = (int) $res;
				
					if($alert->moebius_status == 4)
					{	
						$query = "UPDATE moebiusos SET moebius_status = 2, moebius_timeraised = '$time', moebius_id = $res WHERE idalert = $id";
					} elseif (!$alert->moebius_status) {
						$query = "INSERT INTO moebiusos(idalert, moebius_status, moebius_timeraised, moebius_id) VALUES ($id, 2, '$time', $res)";
					}
				
					pg_query($query);

					header('Location: http://localhost:8080/index.php?mess=1');
				}
			}
		}
	}


?>