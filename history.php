<html>
<head>

	<script src="js/jquery-1.11.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/index.css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="css/jquery.dataTables.min.css">

</head>

<body>

	<script> $(document).ready(function() {
    	$('#datatable').DataTable();} ); </script>

    <div id="title">
    	<h3 align="center">ID: <?php echo $_REQUEST['a'] ?></h3>
    </div><br>

    <div id="back" style="padding-left: 5px;">
    	<a href="index.php" class="btn btn-info" role="button">Volver</a>
    </div>

	<div id="tablespace" style="padding: 5px;"><br><br>
	<table id='datatable' class="table table-bordered"><thead><tr>
		<th>ID Nota</th>
		<th>Nota</th>
		<th>Fecha y hora</th>
		<th>Tipo</th>
		<th>Grupo</th>
		<th>Usuario</th>
	</tr></thead>

	<?php

		$db_connection = pg_connect("host=192.168.40.129 port=5432 dbname=AlertCentral user=postgres password=postgres");

	    if (!$db_connection)
	    {
	        die('Error: Could not connect: ' . pg_last_error());
	    }

	    if (isset($_REQUEST['a']))
	    {
	        $a = $_REQUEST['a'];
	    } else {

	        die ("Error");
	    }

	    $query = "SELECT * FROM alerthistory WHERE alert_id = $a ORDER BY id ASC";
	    $result = pg_query($query);

	    table($result);

	    function table ($result)
	    {
		    while ($alert = pg_fetch_object($result))
		    {
		    	echo "<tbody><tr>";

		    	$group = "";
		    	$user = "";

		    	if ($alert->group_id)
		    	{
		    		$group = getGroupName($alert->group_id);
		    	}

		    	if ($alert->user_id)
		    	{
		    		$user = getUserName($alert->user_id);
		    	}

		    	echo "<td>".$alert->id."</td>";
		    	echo "<td>".$alert->note."</td>";
		    	echo "<td>".$alert->timecreated."</td>";
		    	echo "<td>".ucfirst(strtolower($alert->type))."</td>";
		    	echo "<td>".$group."</td>";
		    	echo "<td>".$user."</td>";
		    	echo "</tr>";
		    }

		    echo "</tbody></table></div";
		}

		function getGroupName ($id)
		{
			$query = "SELECT name FROM groups WHERE id = $id";
			$result = pg_query($query);

			while ($name = pg_fetch_object($result))
			{
				$gn = $name->name;
			}

			return $gn;
		}

		function getUserName ($id)
		{
			$query = "SELECT name FROM users WHERE id = $id";
			$result = pg_query($query);

			while ($name = pg_fetch_object($result))
			{
				$un = $name->name;
			}

			return $un;
		}
	?>
</body>

</html>