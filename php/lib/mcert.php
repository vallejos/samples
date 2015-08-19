<?

/**
 * MCert v 1.0
 * by kmS
 */

include_once("/var/www/lib/email.class.php");

class MCert {
    private $db;
    private $url        = "http://www.musiccert.com:8080/mcert/servlet/CGI.CGICertificacion";
    private $params     = "id_obra=%ID_OBRA%&fono=%FONO%&id_tipo=%ID_TIPO%&user=%USER%&password=%PASSWORD%&pais=%PAIS%&precio=%PRECIO%&id_aplicacion=%ID_APLICACION%&fecha=%FECHA%";
    private $user       = "zaup";
    private $password   = "";
    private $to 		= "leonardo.hernandez@globalnetmobile.com";

    private $id_tipo;
    private $pais;
    private $id_branding;

    private $id_obra;
    private $id_aplicacion;
    private $fono;
    private $precio;
    private $fecha;
    private $operadora;

    private $url_certificacion;
    private $result;
    private $array_obra = array();

    private $status;
    private $ok_code;           // aca llega el transaction_id
    private $nok_code;
    private $nok_msg;

    private $debug;
    private $debug_msg;

	private $content_type;
	private $provider;

    /**
     * MCert
     *
     * @param dbName $db
     * @param int $content_id
     * @param int $fono
     * @param int $precio
     * @param enum $operadora
     * @param int $id_tipo
     * @param string $pais
     * @param int $id_branding
     * @return MCert
     */
    public function MCert($db,$content_id,$fono,$precio,$operadora,$id_tipo="2",$pais="co",$id_branding="", $debug=false) {
    	$this->debug 			= $debug;
    	if ($this->debug) $this->debug_msg .= "[DEBUG] Iniciando MCert\n";
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - ($db,$content_id,$fono,$precio,$operadora,$id_tipo,$pais,$id_branding,$debug)\n";
        $this->content_id       = $content_id;

        // ------------
        // patch para id_contenidos cambiados - 20 Feb 2008 // kmS
        // ------------
        switch ($this->content_id) {
        	// contenidos que cambian
        	case "2075":
        		$this->content_id = "12439";
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - Encontrado content_id viejo: $content_id cambia a $this->content_id\n";
        		break;
        	case "8438":
        		$this->content_id = "12434";
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - Encontrado content_id viejo: $content_id cambia a $this->content_id\n";
        		break;
        	case "8444":
        		$this->content_id = "10389";
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - Encontrado content_id viejo: $content_id cambia a $this->content_id\n";
        		break;

        	// contenidos que no van mas!!
        	case "2693":
        	case "8439":
        	case "7737":
		    	if ($this->debug) $this->debug_msg .= "[CRIT] - Encontrado content_id viejo: $content_id cambia a $this->content_id\n";
        		break;

        	// este contenido lol tiene que verigicar Olano
        	case "7722":
		    	if ($this->debug) $this->debug_msg .= "[WARN] - Encontrado content_id desconocido: $this->content_id (preguntar a Ale Triunfo)\n";
        		break;
        }
        // ------------

        $this->db               = $db;
        $this->fono             = substr($fono, -7);
        $this->precio           = $precio;
        $this->id_tipo          = $id_tipo;
        $this->operadora        = $operadora;
        $this->pais             = $pais;
        $this->id_branding      = $id_branding;
        $this->id_obra          = $this->getObraID();
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - Chequeando tipo/proveedor\n";
		list ($this->content_type,$this->provider) = $this->getContentType();

		if ($this->provider != "27") if ($this->debug) $this->debug_msg .= "[DEBUG] - CONTENT PROVIDER: $this->provider OK!\n";
		else if ($this->debug) $this->debug_msg .= "[DEBUG] - CONTENT PROVIDER: $this->provider NOK!\n";

		if ($this->content_type == "29") if ($this->debug) $this->debug_msg .= "[DEBUG] - CONTENT TYPE: $this->content_type OK!\n";
		else if ($this->debug) $this->debug_msg .= "[DEBUG] - CONTENT TYPE: $this->content_type NOK!\n";

		if ($this->content_type == "29" && $this->provider != "27") {
	        $this->id_aplicacion    = "{$this->getAplicationID()}";
	        $this->fecha            = $this->getFecha();
			if ($this->debug) $this->debug_msg .= "[DEBUG] - ID OBRA: $this->id_obra\n";
			if ($this->debug) $this->debug_msg .= "[DEBUG] - ID APLICACION: $this->id_aplicacion\n";
			if ($this->debug) $this->debug_msg .= "[DEBUG] - FECHA: $this->fecha\n";
		} else {
			if ($this->debug) $this->debug_msg .= "[DEBUG] - content_type/provider incorrectos.\n";
		}
    }


    /**
     * Envia la peticion
     *
     * @return boolean
     */
    public function Certificar() {
    	if ($this->debug) $this->debug_msg .= "[DEBUG] Iniciando MCert::Certificar()\n";

		if ($this->content_type == "29" && $this->provider != "27") {
			if (($this->id_obra != "") && ($this->provider!="27") && ($this->content_type=="29")) {
				$this->params = eregi_replace("%ID_OBRA%", $this->id_obra, $this->params);
				$this->params = eregi_replace("%FONO%", sprintf("%04s", $this->fono), $this->params);
				$this->params = eregi_replace("%ID_TIPO%", $this->id_tipo, $this->params);
				$this->params = eregi_replace("%USER%", $this->user, $this->params);
				$this->params = eregi_replace("%PASSWORD%", $this->password, $this->params);
				$this->params = eregi_replace("%PAIS%", $this->pais, $this->params);
				$this->params = eregi_replace("%PRECIO%", $this->precio, $this->params);
				$this->params = eregi_replace("%ID_APLICACION%", $this->id_aplicacion, $this->params);
				$this->params = eregi_replace("%FECHA%", $this->fecha, $this->params);

				if ($this->debug) $this->debug_msg .= "[DEBUG] - Parametros: $this->params\n";
				$this->url_certificacion = $this->url."?".$this->params;
				if ($this->debug) $this->debug_msg .= "[DEBUG] - URL: $this->url_certificacion\n";
				$session_curl = curl_init();
				curl_setopt($session_curl, CURLOPT_URL, $this->url_certificacion);
				curl_setopt($session_curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($session_curl, CURLOPT_TIMEOUT, 10);
				$this->result = curl_exec($session_curl);
				if ($this->debug) $this->debug_msg .= "[DEBUG] - Resultado CURL: $this->result\n";
				curl_close($session_curl);

				// proceso resultado
				$this->ProcessResult();

				// actualizo db
				$this->UpdateTransaction();

				// devuelvo true si todo bien o false en caso contrario
				return $this->ReturnResult();
			} else {
				if ($this->debug) $this->debug_msg .= "[CRITICAL] No se encontro ID de OBRA para el contenido: $this->content_id\n";
				$this->status = "NOK";
				return $this->ReturnResult();
			}
		} else {
			$this->debug_msg .= "[DEBUG] - content_type/provider ($this->content_type/$this->provider) incorrectos.\n";
			$this->debug_msg .= "[DEBUG] - Aborting!\n";
			$this->status = "NOK";
			return $this->ReturnResult();
		}
    }


    /**
     * Analiza el resultado de la peticion
     *
     * @return boolean
     */
    private function ProcessResult() {
    	if ($this->debug) $this->debug_msg .= "[DEBUG] Iniciando MCert::ProcessResult()\n";
        $success = false;
        // proceso result
        preg_match("/<Status>([a-z][A-Z].*)<\/Status>/i", $this->result, $matches);
        $this->status = $matches[1];
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - Status: $this->status\n";

        switch ($this->status) {
            case "OK":
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - OK detectado\n";
                preg_match("/<TransactionID>([0-9].*)<\/TransactionID>/i", $this->result, $matches);
                $this->ok_code = $matches[1];
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - OK code: $this->ok_code\n";
                $success = true;
                break;
            case "ERROR":
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - ERROR detactado\n";
                preg_match("/<ErrorNumber>([0-9].*)<\/ErrorNumber>/i", $this->result, $matches);
                $this->nok_code = $matches[1];
                preg_match("/<ErrorMsg>([a-z][A-Z].*)<\/ErrorMsg>/i", $this->result, $matches);
                $this->nok_msg = $matches[1];
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - NOK code: $this->nok_code\n";
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - NOK message: $this->nok_msg\n";
                $success = false;
                break;
            default:
                // error
		    	if ($this->debug) $this->debug_msg .= "[CRIT] *** STATUS NO RECONOCIDO ***\n";
                $success = false;
        }

        // return whatever
        return $success;
    }


    /**
     * Actualiza la base con resultado de la operacion
     *
     */
    private function UpdateTransaction() {
    	if ($this->debug) $this->debug_msg .= "[DEBUG] Iniciando MCert::UpdateTransaction()\n";
        switch ($this->status) {
            case "OK":
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - OK detectado:\n";
                $sql = "UPDATE admins.mcert SET transaction_id='$this->ok_code', status='$this->status' WHERE id=$this->id_aplicacion ";
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - SQL: $sql\n";
                $rs = mysql_query($sql, $this->db);
                break;
            case "ERROR":
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - ERROR detectado:\n";
                $sql = "UPDATE admins.mcert SET error_code='$this->nok_code', error_msg='$this->nok_msg', status='$this->status' WHERE id=$this->id_aplicacion ";
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - SQL: $sql\n";
                $rs = mysql_query($sql, $this->db);
                break;
            default:
		    	if ($this->debug) $this->debug_msg .= "[CRIT] *** MSG NO RECONOCIDO ***\n";
                $sql = "UPDATE admins.mcert SET error_msg='CRITICAL: Mensaje no reconocido por el sistema ($this->status)!' WHERE id=$this->id_aplicacion ";
		    	if ($this->debug) $this->debug_msg .= "[DEBUG] - SQL: $sql\n";
                $rs = mysql_query($sql, $this->db);
        }
    }


    /**
     * Devuelve estado de la operacion
     *
     * @return boolean
     */
    private function ReturnResult() {
    	if ($this->debug) $this->debug_msg .= "[DEBUG] Iniciando MCert::ReturnResult()\n";
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - Procesando Log Event\n";
    	if ($this->debug) $this->debug_msg .= " - FIN -\n";

    	$msg = "[".date("Y-m-d H:i:s")."]\n".$this->debug_msg;
/*
	    $headers = "";
    	mail($this->to,"(DEBUG) MCert ", $msg, $headers);
    	*/

		$from = "sebastian.cabriotto@globalnetmobile.com";
		$from_name = "(DEBUG) MCert ";
		$to = "leonardo.hernandez@globalnetmobile.com";
//		$cc = "sebastian.cabriotto@globalnetmobile.com";
		$m = new Email($from_name, $from, $to, "(DEBUG) MCert ", $msg,"", $cc);
//		$m->send();



        if ($this->status == "OK") return true;
        else return false;
    }


    /**
     * Obtiene el id_aplicacion requerido para la peticion
     *
     * @return int
     */
    private function getAplicationID() {
    	if ($this->debug) $this->debug_msg .= "[DEBUG] Iniciando MCert::getAplicationID()\n";
        $sql = "INSERT INTO admins.mcert SET
                    id_obra = '{$this->id_obra}',
                    fono='{$this->fono}',
                    precio='{$this->precio}',
                    id_tipo='{$this->id_tipo}',
                    operadora='{$this->operadora}',
                    pais='{$this->pais}',
                    id_branding='{$this->id_branding}',
                    content_id='{$this->content_id}',
                    fecha=CURDATE(),
                    hora=CURTIME()
                ";
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - SQL: $sql\n";
        $rs = mysql_query($sql, $this->db);
        $id = mysql_insert_id();
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - ID: $id\n";
        return $id;
    }


    /**
     * Devuelve el id_obra asociado al contenido
     *
     * @return string
     */
    private function getObraID() {
    	if ($this->debug) $this->debug_msg .= "[DEBUG] Iniciando MCert::getObraID()\n";
        $sql = "SELECT id_obra FROM admins.mcert_obra WHERE content_id=$this->content_id AND active='1' ";
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - SQL: $sql\n";
        $rs = mysql_query($sql, $this->db);
        $row = mysql_fetch_array($rs);
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - id_obra: {$row["id_obra"]}\n";
        return $row["id_obra"];
    }

    /**
     * Devuelve tipo/proveedor asociado al contenido
     *
     * @return array
     */
    private function getContentType() {
    	if ($this->debug) $this->debug_msg .= "[DEBUG] Iniciando MCert::getContentType()\n";
        $sql = "SELECT tipo,proveedor FROM Web.contenidos WHERE id=$this->content_id ";
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - SQL: $sql\n";
        $rs = mysql_query($sql, $this->db);
        $row = mysql_fetch_array($rs);
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - Web.contenidos.tipo: {$row["tipo"]}\n";
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - Web.contenidos.proveedor: {$row["proveedor"]}\n";
    	$aContData=array($row["tipo"],$row["proveedor"]);
        return $aContData;
    }



    /**
     * Devuelve el id_obra asociado al contenido
     *
     * @return string
     */
    private function getFecha() {
    	if ($this->debug) $this->debug_msg .= "[DEBUG] Iniciando MCert::getFecha()\n";
        $sql = "SELECT fecha,hora FROM admins.mcert WHERE id=$this->id_aplicacion ";
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - SQL: $sql\n";
        $rs = mysql_query($sql, $this->db);
        $row = mysql_fetch_array($rs);
    	if ($this->debug) $this->debug_msg .= "[DEBUG] - fecha: {$row["fecha"]} {$row["hora"]}\n";
        return $row["fecha"]."+".$row["hora"];
    }



}


?>
