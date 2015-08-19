<?php

define ("DEBUG_LVL", 0); 	// debug lvl
define ("LIBDIR", "{$_SERVER['DOCUMENT_ROOT']}/../lib/"); 	// base path para nusoap y otros php incluidos
define ("URL_TRANSACTION", "http://170.51.255.226:8000/srs/services/Rate?Wsdl"); // desarrollo :)
//define ("URL_TRANSACTION", "http://170.51.255.224:8000/srs/services/Rate?Wsdl"); // produccion :)
define ("UID_UNIVERSAL", ""); 	// user universal
define ("PWD_UNIVERSAL", ""); 	// pass universal
define ("UID_WAZZUP", "wazzup"); // user wazzup
define ("PWD_WAZZUP", ""); // pass wazzup

/*
include_once(LIBDIR."debug_def.php");
if (ARRIBA) include_once(LIBDIR."nusoap/nusoap.php");
include_once(LIBDIR."multiversion_soap.php");
include_once(LIBDIR."kmSlib-0.0.1.php");
include_once(LIBDIR."../rewrite_lib.php");
*/


include_once("debug_def.php");
include_once("nusoap/nusoap.php");
include_once("multiversion_soap.php");
include_once("kmSlib-0.0.1.php");
include_once("../rewrite_lib.php");


include_once("klib1.php");	// klib =)
kimport("kmail");

class CobroClaroAr {
	var $dbc;
	var $msisdn;
	var $debug;
	var $ua;
	var $nombre_wap;
	var $content_id;
	var $content_name;
	var $content_type;
	var $error_msg;
	var $provider;
	var $provider_name;
	var $application_id;
	var $billing_type = NULL; // wap, sms o alerta, seteada de acuerdo al metodo de compra invocado

	var $id_transaccion; 		// * comprar_contenido
	var $id_cobro;
	var $srsRatingId; 			// * comprar_contenido
	var $urlOk;
	var $urlCancel;
	var $urlError;
	var $extraparam;
	var $sia_transaction_id; 	// el id devuelto por claro
	var $warning;

	// tablas de provider_id, service_id, application_id, origin_id para WAP, key=content_type
	// $IDS_WAP => proveedor => tipo
	var $IDS_WAP = Array(
		"wazzup" => Array(
			"29" => Array(
				"provider_id" => "194213",
				"origin_id" => "100042",
				"service_id" => "1037",
			),
			"23" => Array(
				"provider_id" => "194213",
				"origin_id" => "100042",
				"service_id" => "1040",
			),
			"31" => Array(
				"provider_id" => "194213",
				"origin_id" => "100042",
				"service_id" => "1042",
			),
			"5" => Array(
				"provider_id" => "194213",
				"origin_id" => "100042",
				"service_id" => "1038",
			),
			"7" => Array(
				"provider_id" => "194213",
				"origin_id" => "100042",
				"service_id" => "1038",
			),
			"62" => Array(
				"provider_id" => "194213",
				"origin_id" => "100042",
				"service_id" => "1041",
			),
		),
	);


	var $IDS_SMS_SHORTCODES = Array(
		"2525" => Array(
			"provider_id" => "194213",
			"origin_id" => "2525",
			"service_id" => "941",
		),
		"7788" => Array(
			"provider_id" => "194213",
			"origin_id" => "7788",
			"service_id" => "942",
		),
	);

	// tablas de provider_id, service_id, application_id, origin_id para SMS, key=shortcode
	// $IDS_SMS => proveedor => shortcode => tipo
	var $IDS_SMS = Array(
		"wazzup" => Array(
			"2525" => Array(
				"29" => Array(
					"provider_id" => "194213",
					"origin_id" => "2525",
					"service_id" => "1028",
				),
				"23" => Array(
					"provider_id" => "194213",
					"origin_id" => "2525",
					"service_id" => "1031",
				),
				"31" => Array(
					"provider_id" => "194213",
					"origin_id" => "2525",
					"service_id" => "1033",
				),
				"5" => Array(
					"provider_id" => "194213",
					"origin_id" => "2525",
					"service_id" => "1029",
				),
				"7" => Array(
					"provider_id" => "194213",
					"origin_id" => "2525",
					"service_id" => "1029",
				),
				"62" => Array(
					"provider_id" => "194213",
					"origin_id" => "2525",
					"service_id" => "1032",
				),
			),
			"7788" => Array(
				"29" => Array(
					"provider_id" => "194213",
					"origin_id" => "7788",
					"service_id" => "1044",
				),
				"23" => Array(
					"provider_id" => "194213",
					"origin_id" => "7788",
					"service_id" => "1047",
				),
				"31" => Array(
					"provider_id" => "194213",
					"origin_id" => "7788",
					"service_id" => "1049",
				),
				"5" => Array(
					"provider_id" => "194213",
					"origin_id" => "7788",
					"service_id" => "1045",
				),
				"7" => Array(
					"provider_id" => "194213",
					"origin_id" => "7788",
					"service_id" => "1045",
				),
				"62" => Array(
					"provider_id" => "194213",
					"origin_id" => "7788",
					"service_id" => "1048",
				),
			),
		),
	);


	// constructor
	function CobroClaroAr($dbc, $msisdn, $application_id=1) {
		global $nombre_wap;
		$this->dbc = $dbc;
		$this->msisdn = str_replace("+", "", $msisdn);
		$this->application_id = $application_id;
		if ($nombre_wap != "") $this->nombre_wap = $nombre_wap;
		else $this->nombre_wap = "";

		$this->debug = new kmail("CLARO AR DEBUG"); // debug started :)
		$this->debug->add("INITIALIZING CLARO AR BILLING...\n\tdb: ".var_export($this->dbc,TRUE)."\n\tmsisdn: ".var_export($this->msisdn,TRUE)."\n\tnombre_wap: ".var_export($this->nombre_wap,TRUE));

		$sql = "INSERT INTO claroArgentina.billing SET
			msisdn='$this->msisdn',
			time=CURTIME(),
			date=CURDATE() ";
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) {
			$this->debug->add("ENGINE INIT FAILED!!\n\tsql: $sql\n\terror: ".mysql_error($this->dbc->db));
			$this->debug->send("***** PROCESS ABORTED :( *****");
			$this->error_msg = "No se pudo procesar la compra."; //error insertando en billing
			return FALSE;
		} else {
			$this->operation_id = mysql_insert_id($this->dbc->db);
			$this->debug->add("ENGINE STARTED...\n\tsql: $sql\n\toperation_id: $this->operation_id");
		}
	}

	// setea el nombre de la wap
	function set_nombre_wap($nombre) {
		$this->debug->add("SETTING UP WAP NAME...\n\tnombre_wap: $nombre_wap");
		$this->nombre_wap = $nombre;
	}


	/**
	* Chequeo el largo de los campos que envio en la peticion.
	* Genera warning en caso de error.
	*
	* @param array $peticion
	* @return boolean (true|false)
	*/
	function check_peticion($peticion) {
		$this->debug->add("INITIALIZING check_peticion...");

		// 5000=sin limite
		$plength = array(
			"providerId" 		=> "10",
			"applicationId" 		=> "10",
			"serviceId" 			=> "10",
			"originId" 			=> "10",
			"operationId" 		=> "40",
			"msisdn" 			=> "15",
			"amount" 			=> "5000",
			"contentId" 		=> "5000",
			"contentDescription" 	=> "5000",
		);

		$warning = false;

		foreach ($plength as $k => $v) {
			if (strlen($peticion[$k]) > intval($v)) {
				$this->warning .= "$k,";
				$warning=true;
			}
		}
		return $warning;
	}


	/**
	* Genera el array con los datos para soap
	*
	* @return array
	*/
	function generar_peticion() {
		$this->debug->add("INITIALIZING generar_peticion...");

		$peticion = array(
			"providerId" 		=> $this->get_provider_id(),
			"applicationId" 		=> $this->application_id,
			"serviceId" 			=> $this->get_service_id(),
			"originId" 			=> $this->get_origin_id(),
			"operationId" 		=> $this->operation_id,
			"msisdn" 			=> $this->msisdn,
			"amount" 			=> "",
			"contentId" 		=> $this->content_id,
			"contentDescription" 	=> $this->content_name,
		);

		$this->debug->add("Peticion Generada...\n\tpeticion: ".var_export($peticion,TRUE));

		// chequeo peticion (reviso el largo de los datos del array)
		if (!$this->check_peticion($peticion)) {
			$this->debug->add("WARNING check_peticion: \n\t$this->warning");
		} else {
			$this->debug->add("check_peticion OK");
		}

		return $peticion;
	}


	/**
	* Devuelve los srsRating para la peticion
	* ---------
	* UNIVERSAL
	* ---------
	* 203 - Tonos Premium
	* 205 - Imagenes a Color
	* 206 - Tonos Reales
	* 207 - Videos
	* ---------
	* WAZZUP
	* ---------
	* n/a
	*
	*
	* 208 Tonos Premium
	* 209 Tonos Reales
	* 210 Imagenes a Color
	* 211 Juegos
	* 212 Juegos Premium
	* 213 Videos
	* 214 Sonidos Especiales
	*
	* @param int $tipo_contenido
	* @return int $srsRating
	*/
	function get_srs_rating($tipo_contenido) {
		$tipo = intval($tipo_contenido);
		if ($tipo == 31 || $tipo == 35 || $tipo == 57 || $tipo == 59 || $tipo == 61) {
			$tipo = 31;
		}

		if ($this->provider == "27") {
			// 27=UNIVERSAL
			switch ($tipo){
				case 17:
				case 29:
					return "203"; // tonos premium (17-soundfx, 29-polifonico)
					break;
				case 5:
				case 7:
					return "205"; // imagenes a color (5-screensaver, 7-wallpaper)
					break;
				case 23:
					return "206"; // tonos reales (23-mp3)
					break;
				case 62:
				case 65:
					return "207"; // videos (62-video)
					break;
				case 31:
					return "212"; // videos (31-juego) ****** es el de wazzup *****
					break;
				default:
					avisoMail("UNIVERSAL: srsRating desconocido ($tipo)");
					return "0"; // error
					break;
			}
		} else {
			// 0=WAZZUP
			switch ($tipo){
				case 17:
				case 29:
					return "1654"; // tonos premium (17-soundfx, 29-polifonico)
					break;
				case 5:
				case 7:
					return "1650"; // imagenes a color (5-screensaver, 7-wallpaper)
					break;
				case 23:
					return "1653"; // tonos reales (23-mp3)
					break;
				case 63:
				case 62:
				case 65:
					return "1649"; // videos (62-video)
					break;
				case 31:
					return "1652"; // videos (31-juego)
					break;
				default:
					avisoMail("WAZZUP: srsRating desconocido ($tipo)");
					return "0"; // error
					break;
			}
		}
	}


	/**
	* Regresa true o false de acuerdo al resultado de la operacion
	*
	* @param int $response
	*/
	function is_error_transaction($code) {
		$ok = false;

		switch ($code) {
			case "1":
				$ok = true;
				$ERR = "Transaccion Registrada. Requiere autorizacion del usuario.";
				break;
			case "4":
				$ok = true;
				$ERR = "Transaccion Registrada y Cobrada.";
				break;
			case "-1":
				$ERR = "User/Pass no validos.";
				break;
			case "-2":
				$ERR = "RatingId no existe.";
				break;
			case "-3":
				$ERR = "TransactionId en uso por otra transaccion.";
				break;
			case "-4":
				$ERR = "Error desconocido en SIA.";
				break;
			case "-5":
				$ERR = "Faltan Parametros.";
				break;
			case "-6":
				$ERR = "IP no valida.";
				break;
			case "-7":
				$ERR = "El proveedor no esta activado.";
				break;
			case "-8":
				$ERR = "Demasiadas suscripciones con el mismo RatingId.";
				break;
			case "-10":
				$ERR = "Una suscripcion con el mismo ContentId ya se ha activado.";
				break;
			case "-11":
				$ERR = "Servicio de Profiles no esta disponible.";
				break;
			case "-17":
				$ERR = "Rating suspendido.";
				break;
			case "-18":
				$ERR = "El usuario no tiene permiso para usar este webservice.";
				break;
			default:
				$ERR = "Internal Error";
		}

		return $ok;
    }


	/**
	* Regresa true o false de acuerdo al resultado del status
	*
	* @param int $response
	*/
	function is_error_status($code) {
		$ok = false;

		switch ($code) {
			case "0":
				$ok = true;
				$ERR = "Increiblemente todo bien";
				break;
			case "1":
				$ERR = "";
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


	/**
	* Genera la peticion SOAP
	*
	* @return unknown
	*/
	function comprar() {
		$this->debug->add("INITIALIZING comprar...");

		$cobrado = false;

		// genero y chequeo la peticion
		$aTransaction = $this->generar_peticion();

		// preparo conexion soap
		$oClient = soap_client_connect(URL_TRANSACTION); 	// cliente soap

		if (soap_client_error($oClient, $result)) {
			$this->debug->add("Preparando conexion soap...\n\tError al establacer conexion soap.");
		} else {
			$this->debug->add("Preparando conexion soap...\n\tConexion soap exitosa.");
		}

		// inicio transaccion soap
		$result = soap_call_method($oClient, "doCharge", $aTransaction);

		// Falla en el servicio webservice?
		if (!soap_client_error($oClient, $result)) {
			$this->debug->add("Iniciando transacción soap...\n\tTransacción OK\n\t<b>Billing DONE!</b>");
			// Respuesta ok, analicemos que paso...
			if ($this->is_error_transaction(soap_errorcode($result))) {
				// todo ok
				$transaction_id = soap_errormsg($result);
				$cod_resultado = soap_errorcode($result);

				// actualizo db con el transaction_id
				$sql = "UPDATE claroArgentina.billing";
				$sql .= " SET transaction_id='$transaction_id',cod_resultado='$cod_resultado'";
				$sql .= " WHERE pk_ventas_wap_billing=" . $this->id_transaccion;
				//$rs = mysql_query($sql, $this->db);

				// aca deberia redireccionar al tipo a http://<sia-host>/sia/descarga.jsp?id=$transaction_id
				$this->sia_transaction_id = $transaction_id;

				$cobrado = true;
			} else {
				$this->debug->add("Iniciando transacción soap...\n\tTransacción OK\n\t<b>Billing REJECTED!</b>");
				// error
				// guardo el error en db
				$sql = "UPDATE claroArgentina.ventas_wap_billing";
				$sql .= " SET cod_resultado='".soap_errorcode($result)."', ";
				$sql .= " error_msg='".soap_errormsg($result)."'";
				$sql .= " WHERE pk_ventas_wap_billing=".$this->id_transaccion;
				//$rs = mysql_query($sql, $this->db);

				$this->sia_transaction_id = null;
			}
		} else {
			// error en soap
			$this->debug->add("Iniciando transacción soap...\n\tTransacción FAILED!! (VPN down / host inaccesible)");
		}

		if ($cobrado !== true) {
			$this->debug->send("***** PROCESS COMPLETE. BILLING FAILED :( *****");
			return $this->mensaje_error($cod_resultado);
		} else {
			$this->debug->send("***** PROCESS COMPLETE. BILLING SUCCESS :) *****");
			return $cobrado;
		}
	}


	/**
	* De acuerdo a la respuesta obtenida, genera error
	*
	* @param unknown_type $error_code
	* @return unknown
	*/
	function mensaje_error($error_code) {
		switch(intval($error_code)) {
			case -1:
			case -2:
			case -3:
			case -4:
			case -5:
			case -6:
			case -7:
			case -8:
			case -10:
			case -11:
			case -17:
			case -18:
			default:
				$mensaje = "En este momento su descarga no se pudo realizar, por favor intente mas tarde";
				break;
		}
		return $mensaje;
	}


	/**
	* Devuelve el transaction_id de claro si todo esta bien
	*
	* @return int id
	*/
	function get_transaction_id() {
		return $this->sia_transaction_id;
	}


	/**
	* Realiza la compra del contenido
	*
	* @param int $id_contenido
	* @param varchar $user_agent
	* @return boolean
	*/
	function comprar_contenido($id_contenido, $user_agent) {
		$this->content_id = $id_contenido;
		$this->ua = $user_agent;
		$this->billing_type = "wap";
		$this->debug->add("INITIALIZING comprar_contenido...\n\tua: ".var_export($this->ua,TRUE)."\n\tid contenido: ".var_export($this->content_id,TRUE)."\n\tbilling type: ".var_export($this->billing_type,TRUE));

		// busco contenido
		$sql = "SELECT * FROM Web.contenidos WHERE id=$this->content_id ";
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) {
			$this->debug->add("COULDN'T GET CONTENT DATA!!\n\tsql: $sql\n\terror: ".mysql_error($this->dbc->db));
			$this->error_msg = "No se pudo procesar la compra."; //contenido no encontrado
			self::update_billing_status("ERROR");

			$this->debug->send("***** PROCESS ABORTED :( *****");
			return FALSE;
		} else {
			$this->debug->add("CONTENT DATA LOADED...\n\tsql: $sql");
			// obtengo datos del contenido
			$obj = mysql_fetch_object($rs);
			$this->content_name = substr(encoding($obj->nombre), 0, 29);
			$this->content_type = $obj->tipo;

			$this->provider = $obj->proveedor;
			if ($this->provider == "27") $this->provider_name = "universal";
			else $this->provider_name = "wazzup";

			// guardo datos del contenido a comprar
			$sql = "INSERT INTO claroArgentina.billing_wap SET
				billing_id='$this->operation_id',
				id_contenido='$this->content_id',
				useragent='$this->ua',
				nombre_wap='$this->nombre_wap',
				proveedor='$this->provider' ";
			$rs = mysql_query($sql, $this->dbc->db);
			if (!$rs) {
				$this->debug->add("ERROR SAVING CONTENT DATA!!\n\tsql: $sql\n\terror: ".mysql_error($this->dbc->db));
				$this->error_msg = "No se pudo procesar la compra."; //contenido no encontrado
				self::update_billing_status("ERROR");

				$this->debug->send("***** PROCESS ABORTED :( *****");
				return FALSE;
			} else {
				$this->debug->add("BILLING CONTENT DATA SAVED...\n\tsql: $sql");
				// actualizo billing
				$sql = "UPDATE claroArgentina.billing SET
					status='PENDING',
					origin='wap',
					application_id='$this->application_id',
					provider_id='{$this->get_provider_id()}',
					origin_id='{$this->get_origin_id()}',
					service_id='{$this->get_service_id()}',
					time=CURTIME(),
					date=CURDATE()
					WHERE id='$this->operation_id' ";
				$rs = mysql_query($sql, $this->dbc->db);
				if (!$rs) {
					$this->debug->add("ERROR UPDATING BILLING INFORMATION!!\n\tsql: $sql\n\terror: ".mysql_error($this->dbc->db));
					self::update_billing_status("ERROR");

					$this->debug->send("***** PROCESS ABORTED :( *****");
					return FALSE;
				} else {
					$this->debug->add("WAP BILLING INFORMATION UPDATED...\n\tsql: $sql");
					return $this->comprar();
				}
			}
		}
	}


	function get_provider_id() {
		switch ($this->billing_type) {
			case "wap":
				return $this->IDS_WAP[$this->provider_name][$this->content_type]['provider_id'];
				break;
			case "sms":
				return $this->IDS_SMS[$this->provider_name][$this->shortcode][$this->content_type]['provider_id'];
				break;
			case "alerta":
				return $this->IDS_SMS_SHORTCODES[$this->shortcode]['provider_id'];
				break;
			default:
				return NULL;
		}
	}


	function get_origin_id() {
		switch ($this->billing_type) {
			case "wap":
				return $this->IDS_WAP[$this->provider_name][$this->content_type]['origin_id'];
				break;
			case "sms":
				return $this->IDS_SMS[$this->provider_name][$this->shortcode][$this->content_type]['origin_id'];
				break;
			case "alerta":
				return $this->IDS_SMS_SHORTCODES[$this->shortcode]['origin_id'];
				break;
			default:
				return NULL;
		}
	}


	function get_service_id() {
		switch ($this->billing_type) {
			case "wap":
				return $this->IDS_WAP[$this->provider_name][$this->content_type]['service_id'];
				break;
			case "sms":
				return $this->IDS_SMS[$this->provider_name][$this->shortcode][$this->content_type]['service_id'];
				break;
			case "alerta":
				return $this->IDS_SMS_SHORTCODES[$this->shortcode]['service_id'];
				break;
			default:
				return NULL;
		}
	}


	// devuelve un error para mostrar al usuario
	function get_error() {
		return $this->error_msg;
	}


	// actualiza la compra con el status (OK,ERROR,PENDING)
	function update_billing_status($status) {
		$this->debug->add("INITIALIZING update_billing_status...\n\tstatus: ".var_export($status,TRUE));

		if (!$this->operation_id) {
			$this->debug->add("INVALID operation_id\n\toperation_id: ".var_export($this->operation_id,TRUE));
			$this->debug->send("***** PROCESS ABORTED :( *****");
			die("ERROR: invalid operation_id ($this->operation_id)");
		} else {
			$sql = "UPDATE claroArgentina.billing SET status='$status' WHERE id='$this->operation_id' ";
			$rs = mysql_query($sql, $this->dbc->db);
			if (!$rs) {
				$this->debug->add("ERROR UPDATING BILLING INFORMATION!!\n\tsql: $sql\n\terror: ".mysql_error($this->dbc->db));
				$this->debug->send("***** PROCESS ABORTED :( *****");
			} else {
				$this->debug->add("BILLING INFORMATION UPDATED...\n\tstatus: $status\n\tsql: $sql");
			}
		}
	}

















}


?>