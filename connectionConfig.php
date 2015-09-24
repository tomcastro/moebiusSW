<?php

	// Conexión a la base de datos de Alert Central

	$db_connection = pg_connect("host=192.168.40.129 port=5432 dbname=AlertCentral user=postgres password=postgres");

	// Conexión al servicio de testing de Moebius

	$client = new SoapClient("http://198.41.40.100/Moebius.services.incidentmanagement/WSIncident.asmx?wsdl");

?>