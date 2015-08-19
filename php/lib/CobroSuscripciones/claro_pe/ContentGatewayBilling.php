<?php
ini_set("soap.wsdl_cache_enabled", "0");

define("WS_URL", dirname(__FILE__)."/MTChargingService.wsdl");

/* Wap auth */
define("WS_USER", "user");
define("WS_PWD", "pass");

/**
 Extendemos soapClient para poder enviar el header de seguridad
*/
class ClienteSoap extends SoapClient {
  function __doRequest($request, $location, $action, $version) {
    $dom = new DomDocument('1.0', 'UTF-8');
    $dom->preserveWhiteSpace = false;
    $dom->loadXML($request);
    $hdr = $dom->createElement('soapenv:Header');
    $secNode = $dom->createElement("wsse:Security");
    $secNode->setAttribute("soapenv:mustUnderstand", "1");
    $secNode->setAttribute("xmlns:wsse", "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd");
    $usernameTokenNode = $dom->createElement("wsse:UsernameToken");
    $usernameTokenNode->setAttribute("wsu:Id", "UsernameToken-1");
    $usernameTokenNode->setAttribute("xmlns:wsu", "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd");

    $usrNode = $dom->createElement("wsse:Username");
    $usrNode->appendChild($dom->createTextNode(WS_USER));

    $pwdNode = $dom->createElement("wsse:Password");
    $pwdNode->setAttribute("Type", "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText");
    $pwdNode->appendchild($dom->createTextnode(WS_PWD));

    $usernameTokenNode->appendChild($usrNode);
    $usernameTokenNode->appendChild($pwdNode);

    $secNode->appendChild($usernameTokenNode);
    $hdr->appendChild($secNode);

    $dom->documentElement->insertBefore($hdr, $dom->documentElement->firstChild);
    $request = $dom->saveXML();
    $request = str_replace("SOAP-ENV","soapenv", $request);
    $request = str_replace("ns1","cgt", $request);
    $request = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $request);
  //  echo "Este es el nuevo XML: " . print_r($request, true);


    return parent::__doRequest($request, $location, $action, $version);
  }
}




class ContentGatewayBilling {
	var $db;

	var $celular;
	var $tipo;
	var $track;
	var $debug;

    var $retCode;
    var $retMsg;
    var $tariffId;
    var $idTransaccion;


    public function __construct($db,$celular,$id_tarifa){
		$this->db = $db;
        $this->tariffId = $id_tarifa;


		$this->celular = $celular;

		$this->debug = true;
	//	$this->debug = new kmail("[DEBUG] MAS GW"); // debug started :)

	}





	function log($txt) {
		if ($this->debug) {
			$fp = @fopen("/var/www/tmp/content_gw_clarope/logs".date("m_d").".txt", "a+");
			if ($fp) {
				fwrite($fp, "\n".date("Y-m-d::H:i:s").":::".$txt);
				fclose($fp);
			}
		}
	}



    function processResultado($res) {
        $this->retCode = $res->doMTChargeReturn;
        switch($this->retCode) {
            case 0:
                $this->retMsg = "Cobro realizado";
            break;
            case 100:
            case 2000:
                $this->retMsg = "Nro de movil invalido";
            break;
            case 101:
                $this->retMsg = "Tarifa invalida";
            break;
            case 102:
            case 103:
            case 2001:
            case 3000:
            case 4000:
            case 4001:
            case 5000:
                $this->retMsg = "Error intentelo nuevamente en unos minutos";
            break;
            case 1000:
                $this->retMsg = "Fondos insuficientes";
            break;
        }
    }

    function registrarInicioTransaccion(){
        $sql = "INSERT INTO claroPeru.content_gw_billing
                (celular, fecha, hora, tariffId)
                VALUES
                ('".$this->celular."', CURDATE(), CURTIME(), ".$this->tariffId.")";
        $rs = mysql_query($sql, $this->db);
        if($rs) {
            $this->idTransaccion = mysql_insert_id($this->db);
            return true;
        } else {
            $this->log("ERROR SQL guardando transaccion::".mysql_error()."::".$sql);
            return false;
        }

    }

    function registrarFinalTransaccion($cobrado) {

        $sql = "UPDATE claroPeru.content_gw_billing
                SET cobrado = ".intval($cobrado).",
                retCode = '".$this->retCode."',
                retMsg = '".$this->retMsg."'
                WHERE id = ".$this->idTransaccion;

        $rs = mysql_query($sql, $this->db);
        if($rs) {
            return true;
        } else {
            $this->log("ERROR SQL guardando final::".$sql."::".mysql_error());
            return false;
        }
    }


	// proceso para suscriptions claro pe
	function process() {
		if (!empty($this->celular)) {
		    if($this->registrarInicioTransaccion()) {

    			$resultado = $this->_callWS("doMTChargingRequest",
	    		                            array("tariffId" => $this->tariffId
		    	                                  ,"msisdn" => $this->celular));

                $this->processResultado($resultado);
                $cobrado = $this->retCode == 0;
                $this->registrarFinalTransaccion($cobrado);
	        	return $cobrado;
	        } else {
	            return false;
	        }
		} else {
			$this->log("*************** ERROR EN EL ENVIO DEL SMS*****************");
    		return false;
		}
	}

    /**
     * Hace la llamada al WS
     *
     * @param String $method Nombre del m�todo
     * @param Array $params
     * @return String  CODIGO|ID
     */

    private function _callWS($method, $params) {
         $client = new ClienteSoap(WS_URL, array("login" => WS_USER,
                                                   "password" => WS_PWD,
                                                   "trace" => true,
                                                   "exception" => true)
                                                   );

        try {

            $result = $client->__soapCall($method, array("parameters" => $params));

            return $result;
        } catch (SoapFault $ex) {
            $this->log(":: ERROR WS :: " . $client->__getLastRequest());
            return false;
        }
    }
}

?>