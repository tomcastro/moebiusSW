<?php

	function API($id, $message)
	{

		$client = new SoapClient("http://198.41.40.100/Moebius.services.incidentmanagement/WSIncident.asmx?wsdl");

		$id = $id + 100000;

		$params = array(
			"intCodSistema" => 15,
			"codEmpresaExterna" => "0093930000-7",
			"codUsuarioExterno" => "AGENTE_SOLARWINDS",
			"codAlertaExterna" => $id,
			"descripcion" => $message,
			"codContrato" => 975,
			"codServicio" => 192,
			"codCobertura" => 39525,
			"grupoResolutor" => 236,
			"clasificacion" => 2107741,
			"impactedUsers" => 1,
			"codTipoOS" => 1);

		$res = $client->__soapCall("CreateServiceOrder", array($params));

		$xml = generate_valid_xml_from_array($res);
		$xml = simplexml_load_string($xml);

		$result = (string) $xml->CreateServiceOrderResult;
		$string = str_replace("<?xml version='1.0' encoding='iso8859-1' ?>", "", $result);
		$xml = simplexml_load_string($string);

		if(empty($xml->codError))
		{
			$xml = (array) $xml->resultado[0];
			$response = xml_attribute($xml[0], 'serviceorderid');
		} else {print_r((string)$xml->mensajeError); die;}

		return $response;
	}

	function xml_attribute($object, $attribute)
	{
	    if(isset($object[$attribute]))
	        return (string) $object[$attribute];
	}

	function generate_xml_from_array($array, $node_name) 
	{
		$xml = '';

		if (is_array($array) || is_object($array)) 
		{
			foreach ($array as $key=>$value) 
			{
				if (is_numeric($key)) 
				{
					$key = $node_name;
				}

				$xml .= '<' . $key . '>' . "\n" . generate_xml_from_array($value, $node_name) . '</' . $key . '>' . "\n";
			}
		} else {
			$xml = htmlspecialchars($array, ENT_QUOTES) . "\n";
		}

		return $xml;
	}

	function generate_valid_xml_from_array($array, $node_block='nodes', $node_name='node') 
	{
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

		$xml .= '<' . $node_block . '>' . "\n";
		$xml .= generate_xml_from_array($array, $node_name);
		$xml .= '</' . $node_block . '>' . "\n";

		return $xml;
	}

?>