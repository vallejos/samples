<?

/**
 * WAP BILLING - Viva Bolivia
 * by kAmuS
 * last update 22.Aug.07
 */

define ("DEBUG_LVL", 					0); 	// debug lvl (1=mail, 2=mail y log, 3=mail, log y pantalla)
define ("LIBDIR", 						"");
define ("IP", 							"216.184.113.56");
define ("PORT", 						"7653");
define ("TIMEOUT", 						5); 	// timeout (Seconds)
define ("COD_APP", 						"wazup");
define ("PIN_APP", 						"");
define ("ALERT_SUBJECT",				"BILLING VIVA_BO");
define ("LOGFILE", 						"/var/www/tmp/billing_viva_bo/billing_viva_bo.log");
define ("FS", 							"|");
define ("EOL", 							"\r\n");


include_once(LIBDIR."kmSlib-0.0.1.php");


class CobroVivaBo {
	var $db; 					//
    var $numero_celular; 		//
    var $id_transaccion; 		//
    var $provider; 				//
    var $id_contenido;			//
    var $nombre_contenido; 		//
	var $user_agent; 			//
	var $tariff_code; 			//
	var $descripcion;
	var $_debug = array();
	var $nombre_wap;


	// constructor
	function CobroVivaBo($db,$numero_celular) {
        $this->db = $db;
        $this->numero_celular = str_replace("+", "", $numero_celular);
        $this->provider = "0";
        $this->descripcion = "";
		$this->nombre_wap = FALSE;
	}


	/**
	 * Volcado de debug via mail
	 */
	function dump_debug() {
		$txt = "";
		foreach ($this->_debug as $k => $v) {
			$txt .= "$v\n";
		}
		avisoMail($txt);
	}


	/**
	 * Genera la peticion con los datos
	 *
	 * @return string
	 */
    function generarPeticion() {
    	// ---------
    	// Protocolo
    	// ---------
    	// Peticion: DISCNT2<FS><COD_APP_CLIENTE><FS><PIN_APP_CLIENTE><FS><ID_TRANS><FS><TELEFONO><FS><TARIFF_CODE><FS><GLOSA><EOL>
    	//       Ok: DISCNT2<FS>OK<FS><ID_TRANS><EOL>
    	//    Error: DISCNT2<FS>ERROR<FS><ID_TRANS><FS><COD_ERROR><FS><MSG_ERROR><EOL>
    	// --------
    	// Ejemplos
    	// --------
    	// Peticion: DISCNT2|user1|xxxx|1|59170751998|1234001|test
    	//       Ok: DISCNT2|OK|1

    	$peticion =
		    "DISCNT2".
		    FS . COD_APP.
		    FS . PIN_APP.
		    FS . $this->id_transaccion.
		    FS	. $this->numero_celular.
		    FS . $this->tariff_code .
		    FS . $this->nombre_contenido .
			EOL;

        // $fplog = fopen("/var/www/tmp/billing_claro_pe/billing_comcel_co.log","a");
        // fwrite($fplog,date("Y-m-d H:i:s")."\n $peticion \n \n");
        // fclose($fplog);

        array_push($this->_debug, "(DBG) - [$this->id_transaccion] Peticion Generada: $peticion");

        return $peticion;
    }


    /**
     * Devuelve los shortcodes para la peticion
     * ---------
     * 3223-Descargas
     * 3223001-Monofonicos
     * 3223002-Polifonicos
     * 3223003-Logos ByN
     * 3223004-Wallpapers
     * 3223005-Wallpapers Premium
     * 3223006-True Tones
     * 3223007-Sonidos Especiales
     * 3223008-Name Tonos
     * 3223009-Video Tones
     * 3223010-Video clips
     * 3223011-Juegos
     * 3223012-Juegos Premium
     * 3223013-Karaoke
     * 3223014-Reloj
     * 3223015-Calendarios
     * 3223016-MMS
     * 3223017-MMS Premium
     * 3223018-Alertas SMS
     *
     * @param int $tipo_contenido
     * @return int $shortcode
     */
    function get_Tariff($tipo_contenido) {
        $tipo = intval($tipo_contenido);

    	if ($this->provider == "0") {
    		// WAZZUP
	        switch ($tipo){
	        	// MONOFONICOS
				case 28: // monofonicos
	        	case 22:
	        	case 51:
	        	case 53:
	        	case 54:
	        		return "3223001"; // monofonicos
	        		break;
	        	// SONIDOS ESPECIALES
	            case 17: // soundfx
	            	return "3223007";
	            	break;
	            // POLIFONICOS
	            case 29: // polifonicos
	                return "3223002";
	            	break;
	            // WALLPAPERS PREMIUM
	            case 7: // wallpapers
	            case 5: // screensaver
			    case 13: // logos
	                return "3223005";
	            	break;
	            // TRUETONES
	            case 23: // mp3
	                return "3223006";
	            	break;
	            // VIDEO CLIPS
	            case 62: // videos
			    case 63: // themes
	                return "3223010";
	            	break;
	            // JUEGOS PREMIUM
				case 35: // Mophun
				case 57: // Java Clasicos
				case 59: // Java Standard
				case 61: // Mophun Premium
				case 31: // Java Premium
	                return "3223012";
	            	break;
	            default:
	            	array_push($this->_debug, "(CRIT) - [$this->id_transaccion] SHORTCODE NO ENCONTRADO PARA PROVEEDOR $this->provider ($tipo)");
	            	return "0"; // error
	            	break;
	        }
    	}
    }


	/**
	 * Genera la conexion al SOCKET
	 *
	 * @return boolean
	 */
    function comprar() {
        $cobrado = false;

        $parametros = $this->generarPeticion();

        // abro la conexion
        $socket = fsockopen(IP, PORT, $errno, $errstr, TIMEOUT);

		$linea = "";
        $retorno_leido = false;

        if ($socket) { // Si esta abierta...
        	array_push($this->_debug, "(DBG) - [$this->id_transaccion] Socket OK. Iniciando secuencia...");

            fputs($socket, $parametros);

            do {
	            $linea .= fread($socket, 80);
	            $stat = socket_get_status($socket);
            }
            while ($stat["unread_bytes"]);

            if(strpos($linea, FS) !== false) {
			    $retorno_leido = true;
		    }

            fclose($socket);

			array_push($this->_debug, "(DBG) - [$this->id_transaccion] Socket Leido y Cerrado");
        } else {
        	array_push($this->_debug, "(CRIT) - [$this->id_transaccion] Error en Socket!! [errno: $errno; errstr:$errstr]");
            $cobrado = false;
        }

        if ($retorno_leido === true) {
	    	//       Ok: DISCNT2<FS>OK<FS><ID_TRANS><EOL>
	    	//    Error: DISCNT2<FS>ERROR<FS><ID_TRANS><FS><COD_ERROR><FS><MSG_ERROR><EOL>
	        $arr_retorno = explode(FS, trim($linea));

            $resultado = $arr_retorno[1];
            switch ($resultado) {
            	case "OK":
            		$cobrado = true;
            		$cod_resultado = 1;
            		$id_trans = $arr_retorno[2];
            		break;
            	case "ERROR":
            		$cod_resultado = intval($arr_retorno[3]);
            		$msg_error = $arr_retorno[4];
            		array_push($this->_debug, "(CRIT) - [$this->id_transaccion] ERROR: $cod_resultado [$msg_error]");
            		break;
            	default:
            		$cod_resultado = -1;
            		$msg_error = "Imposible conectar a ".IP.":".PORT." (Connection timed out)";
            		array_push($this->_debug, "(CRIT) - [$this->id_transaccion] ".$msg_error);
            		break;
            }

        } else {
        	if ($errno > 0) {
        		$msg_error = $errstr;
        		$cod_resultado = $errno;
        	} else {
            	$cod_resultado = $error_code;
        	}
        }


        if ($cobrado === true) {
			// actualizo db con el transaction_id
            $sql = "UPDATE vivaBolivia.ventas_wap_billing ";
            $sql .= " SET cobrado=1,cod_resultado='$cod_resultado' ";
            $sql .= " WHERE pk_ventas_wap_billing=" . $this->id_transaccion;
            array_push($this->_debug, "(DBG) - [$this->id_transaccion] ".$sql);
            $rs = mysql_query($sql, $this->db);

            if (DEBUG_LVL) $this->dump_debug();
            return $cobrado;
        } else {
			// guardo el error en db
            $sql = "UPDATE vivaBolivia.ventas_wap_billing";
            $sql .= " SET cod_resultado='$cod_resultado', ";
            $sql .= " error_msg='$msg_error' ";
            $sql .= " WHERE pk_ventas_wap_billing=".$this->id_transaccion;
            array_push($this->_debug, "(DBG) - [$this->id_transaccion] ".$sql);

            $rs = mysql_query($sql, $this->db);

            if (DEBUG_LVL) $this->dump_debug();
			return $this->mensajeError($cod_resultado);
        }
    }


	/**
	 * De acuerdo a la respuesta obtenida, genera error
	 *
	 * @param int $error_code
	 * @return string
	 */
    function mensajeError($error_code) {
        switch(intval($error_code)) {
        	case -1:
            default:
                $mensaje = "En este momento su descarga no se pudo realizar, por favor intente mas tarde.";
            break;
        }

        return $mensaje;
    }


	/**
	 * Realiza la compra del contenido
	 *
	 * @param int $id_contenido
	 * @param varchar $user_agent
	 * @return boolean
	 */
    function comprarContenido($id_contenido, $user_agent) {
        array_push($this->_debug, "Iniciando...");
	    $this->id_contenido = $id_contenido;
        $this->user_agent = $user_agent;

        $sql = "SELECT * FROM vivaBolivia.ventas_wap_billing WHERE ";
        $sql .= " descarga=$this->id_contenido ";
        $sql .= " AND celular='$this->numero_celular' ";
        $sql .= " AND useragent='$this->user_agent' ";
        $sql .= " AND CONCAT(fecha, ' ', hora) >= DATE_SUB(NOW(), INTERVAL 1 HOUR) ";
        $sql .= " AND cobrado=1 ";

        array_push($this->_debug, "(DBG) - [$this->id_transaccion] $sql");
        $rs = mysql_query($sql, $this->db);
        if ($row = mysql_fetch_array($rs)) {
        	$this->id_transaccion = $row["pk_ventas_wap_billing"];
        	array_push($this->_debug, "(DBG) - [$this->id_transaccion] DOBLE-CLICK Detected! Returning =)");
        	if (DEBUG_LVL) $this->dump_debug();
            return true;
        }
        $cobrado = false;
        $err_msg = "";

        $sql = "SELECT * FROM Web.contenidos";
        $sql .= " WHERE id=$id_contenido";
        $rs = mysql_query($sql, $this->db);
        array_push($this->_debug, "(DBG) - [$this->id_transaccion] $sql");

        $row = mysql_fetch_array($rs);
        $this->nombre_contenido = encoding($row["nombre"]);
        $tipo = $row["tipo"];

        switch ($row["proveedor"]) {
        	default:
        		$this->provider = "0";
        }

        $this->tariff_code = $this->get_Tariff($tipo);

        if ($this->tariff_code == "0") {
        	// error en shortcode
        	array_push($this->_debug, "(CRIT) - SHORTCODE NO EXISTE PARA EL id_contenido=$id_contenido");
        	if (DEBUG_LVL) $this->dump_debug();
			return mensajeError();
        } else {
			// shortcode ok
	        $sql = "INSERT INTO vivaBolivia.ventas_wap_billing SET ";
	        $sql .= "descarga=$this->id_contenido, ".
	        		"celular='$this->numero_celular', ".
	        		"useragent='$this->user_agent', ".
	        		"id_cobro='$this->tariff_code',".
	        		"fecha=CURDATE(), ".
	        		"hora=CURTIME(), ".
	        		"error_msg='$err_msg' ";
	        array_push($this->_debug, "(DBG) - [$this->id_transaccion] $sql");
	        $rs = mysql_query($sql, $this->db);
	        $pk_ventas_wap_billing = mysql_insert_id($this->db);

	        $this->id_transaccion = $pk_ventas_wap_billing;
	        array_push($this->_debug, "(DBG) - ID_TRANSACCION: $pk_ventas_wap_billing");

			if ($this->nombre_wap !== FALSE) {
				$sql = "UPDATE vivaBolivia.ventas_wap_billing SET nombre_wap='{$this->nombre_wap}'";
				$sql .= " WHERE pk_ventas_wap_billing={$this->id_transaccion} ";
				mysql_query($sql, $this->db);
			}

			if ($this->id_transaccion == "0") {
				// error sql
		        array_push($this->_debug, "(CRIT) - BAD_ID_TRANSACCION :: Posible error en SQL.");
	        	if (DEBUG_LVL) $this->dump_debug();
				return mensajeError();
			} else {
				// todo bien
	        	return $this->comprar();
			}
        }
    }


    function comprarAlerta($id_contenido, $user_agent) {
        array_push($this->_debug, "Iniciando...");
	    $this->id_contenido = $id_contenido;
        $this->user_agent = $user_agent;

        $sql = "SELECT * FROM vivaBolivia.ventas_wap_billing WHERE ";
        $sql .= " descarga=$this->id_contenido ";
        $sql .= " AND celular='$this->numero_celular' ";
        $sql .= " AND useragent='$this->user_agent' ";
        $sql .= " AND CONCAT(fecha, ' ', hora) >= DATE_SUB(NOW(), INTERVAL 1 HOUR) ";
        $sql .= " AND cobrado=1 ";

        array_push($this->_debug, "(DBG) - [$this->id_transaccion] $sql");
        $rs = mysql_query($sql, $this->db);
        if ($row = mysql_fetch_array($rs)) {
        	$this->id_transaccion = $row["pk_ventas_wap_billing"];
        	array_push($this->_debug, "(DBG) - [$this->id_transaccion] DOBLE-CLICK Detected! Returning =)");
        	if (DEBUG_LVL) $this->dump_debug();
            return true;
        }
        $cobrado = false;
        $err_msg = "";

        $sql = "";
        $sql .= "SELECT p.* FROM alertas.alertas_precios p, alertas.alertas_alertas a ";
        $sql .= " WHERE p.fk_id_alerta=" . $this->id_contenido;
        $sql .= " AND a.operadora='viva_bo' ";
        $sql .= " AND a.id=p.fk_id_alerta ";
        $rs = mysql_query($sql, $this->db);
        array_push($this->_debug, "(DBG) - TARIFF_CODE QUERY: $sql");

        while ($obj = mysql_fetch_object($rs)){
            $this->tariff_code = $obj->wap;  // sc alertas sms
        }

        $this->nombre_contenido = "Alerta WAP";
		$this->provider = "0";

		// shortcode ok
        $sql = "INSERT INTO vivaBolivia.ventas_wap_billing SET ";
        $sql .= "descarga=$this->id_contenido, ".
        		"celular='$this->numero_celular', ".
        		"useragent='$this->user_agent', ".
        		"id_cobro='$this->tariff_code',".
        		"fecha=CURDATE(), ".
        		"hora=CURTIME(), ".
        		"tipo_venta='alerta', ".
        		"error_msg='$err_msg' ";
        array_push($this->_debug, "(DBG) - [$this->id_transaccion] $sql");
        $rs = mysql_query($sql, $this->db);
        $pk_ventas_wap_billing = mysql_insert_id($this->db);

        $this->id_transaccion = $pk_ventas_wap_billing;
        array_push($this->_debug, "(DBG) - ID_TRANSACCION: $pk_ventas_wap_billing");

		if ($this->id_transaccion == "0") {
			// error sql
	        array_push($this->_debug, "(CRIT) - BAD_ID_TRANSACCION :: Posible error en SQL.");
        	if (DEBUG_LVL) $this->dump_debug();
			return mensajeError();
		} else {
			// todo bien
        	return $this->comprar();
		}
    }


    function setNombreWap($nombre){
        $this->nombre_wap = "$nombre";
    }


}


?>