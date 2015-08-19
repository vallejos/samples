<? 

/*************************
 ************************
 * API SOAP MULTIVERSION 
 * para php 5.2.x y php 5.0.x
 * 
 * by kmS
 * 
 * PHP 5.2.x ya incluye librerias soap
 * PHP 5.0.x no > requiere nusoap lib
 * 
 ************************
 *************************/

$WSDL = "http://descargas.claro.com.pe:8080/sia/services/Transaction?wsdl";


// api soap multiversion :: error - kmS
function soap_client_error($client,$result) {
	if (ARRIBA) {
		if (DEBUG_LVL) avisoMail("REQUEST\n-----------------------------\n\n".htmlspecialchars($client->request, ENT_QUOTES));
		if (DEBUG_LVL) avisoMail("RESPONSE\n-----------------------------\n\n".htmlspecialchars($client->response, ENT_QUOTES));
		if (DEBUG_LVL) avisoMail("DEBUG\n-----------------------------\n\n".htmlspecialchars($client->debug_str, ENT_QUOTES));
		
		return $client->geterror();
	} else {
		if (DEBUG_LVL) avisoMail("REQUEST\n-----------------------------\n\n".htmlspecialchars($client->__getLastRequest(), ENT_QUOTES));
		if (DEBUG_LVL) avisoMail("RESPONSE\n-----------------------------\n\n".htmlspecialchars($client->__getLastResponse(), ENT_QUOTES));
		if (DEBUG_LVL) avisoMail("DEBUG\n-----------------------------\n\n".htmlspecialchars($result->faultcode, ENT_QUOTES));
		if (DEBUG_LVL) avisoMail("DEBUG\n-----------------------------\n\n".htmlspecialchars($result->faultstring, ENT_QUOTES));

		return is_soap_fault($result);
	}
}


// api soap multiversion :: report error - kmS
function soap_report_error($result) {
	if (ARRIBA) {

	} else {
	    trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})", E_ERROR);
	}
}


// api soap multiversion :: call method - kmS
function soap_call_method($oClient, $method, $array_datos) {
	if (ARRIBA) {
		$result = $oClient->Call($method, $array_datos, '', '', false, true);
	} else {
		$result = $oClient->__call($method, $array_datos);
	}
	return $result;
}


// api soap multiversion :: create client - kmS
function soap_client_connect($url) {
	if (ARRIBA) {
		$oClient = new soapclient($url, true);
		$err = $oClient->getError();
		if ($err) {
			avisoMail("ERROR Conectando: $err");
		}		
	} else {
		$oClient = new soapclient($url, array("trace" => 1));
	}
	return $oClient;
}


// api soap multiversion :: get response - kmS
function soap_get_response($result) {
	$ok = false;
	list($error_code, $error_msg) = explode("|",$result);
	
	switch ($error_code) {
		case "1":
			$ok = true;
			$ERR = "Increiblemente todo bien";
			break;
		case "4": 
			$ERR = "";
			break;
		case "-1": 
			$ERR = "";
			break;
		case "-2": 
			$ERR = "";
			break;
		case "-3": 
			$ERR = "";
			break;
		case "-4": 
			$ERR = "";
			break;
		case "-5": 
			$ERR = "";
			break;
		case "-6": 
			$ERR = "";
			break;
		case "-7": 
			$ERR = "";
			break;
		case "-8": 
			$ERR = "";
			break;
		case "-10": 
			$ERR = "";
			break;
		case "-11": 
			$ERR = "";
			break;
		case "-17": 
			$ERR = "";
			break;
		case "-18": 
			$ERR = "";
			break;
		default: 
			$ERR = "Internal Error";
	}
	return $ok; 
}


// api soap multiversion :: get error code - kmS
function soap_errorcode($result) {
	list($error_code, $error_msg) = explode("|",$result);
	return $error_code;
}


// api soap multiversion :: get error msg - kmS
function soap_errormsg($result) {
	list($error_code, $error_msg) = explode("|",$result);
	return $error_msg;
}



?>