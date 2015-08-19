<?php

define("BORRAR_USUARIO_SERVICIO_ANCEL", 0);
define("MANTENER_USUARIO_SERVICIO_ANCEL", 1);

class Sms {

    var $db;
    var $operador;
    var $app;
    var $celular;
    var $mensaje;
    var $urlPush;
    var $tipo;
    var $nroTramite;
    var $messageId;
    var $track;
    var $startTime;
    var $serviceType;
    var $debug;
    var $serviceNmbr; //para envios de ANCEL sin MO previo
    var $comportamiento_ancel; //para envios de ANCEL sin MO previo

    function sms($db,$operador,$app,$celular,$mensaje,$urlPush="",$nroTramite=""){
        $this->db = $db;
        $this->operador = strtolower($operador);
        $this->app = $app;
        $this->celular = $celular;
        $this->mensaje = $this->parseMensaje($mensaje);
        $this->urlPush =  $urlPush;
        $this->tipo = 0;
        $this->nroTramite = $nroTramite;
        $this->debug = true;
	    $this->serviceNmbr = null;
	    $this->comportamiento_ancel = BORRAR_USUARIO_SERVICIO_ANCEL;

        $this->log("***********************");
        $this->log("Instanciando clase SMS");
        $this->log("***********************");
    }

    /**
      Marca el servicio a utilizar para el envío de mensajes sms a usuarios de ANCEL sin que ellos
      envíen un MO previamente.
      El número de servicio es lo que identifica al shortcode. Y debe ser habilitado por ANCEL.

      @nro INT Número del servicio
      @c  CONST Indica que hacer luego de mandarle el mensaje al usuario. Hay 2 opciones posibles:
      	- BORRAR_USUARIO_SERVICIO_ANCEL: Luego de enviar el mensaje, el usuario es dado de baja en el servicio especifico.
	- MANTENER_USUARIO_SERVICIO_ANCEL: El usuario no se da de baja, esto permite que se le pueda enviar otro mensaje sin darlo de alta previamente.
	-- Por defecto, el valor a utilizar es BORRAR_USUARIO_SERVICIO_ANCEL
      */
    function setServiceNumber($nro, $c = BORRAR_USUARIO_SERVICIO_ANCEL){
	    $this->serviceNmbr = $nro;
	    $this->comportamiento_ancel = $c;
	    $this->app = "--";
    }

    function log($txt){
    	if($this->debug) {
	    	// 10.0.0.254 $fp = @fopen("logs".date("m_d").".txt", "a+");
            // 10.0.0.241 y 10.0.0.243 $fp = @fopen("/var/www/tmp/sms_logs/logs".date("m_d").".txt", "a+");
            $fp = @fopen("/var/www/tmp/sms_logs/logs".date("m_d").".txt", "a+");
	    	if($fp) {
	    		fwrite($fp, "\n".date("Y-m-d::H:i:s").":::".$txt);
	    		fclose($fp);
	    	}
   		}
    }
    
    function _enviarMensaje($url, &$sockError){
	    $ret = "";
        if (!empty($url)){
            if(is_array($url)) {
                $this->log("Enviado mensaje con servicio especial. URLS:\n");
                $this->log(print_r($url, true));
                $urls = $url;
            } else {
                $urls = array($url);
            }

            foreach($urls as $url) {
                $fp = @fopen($url,"r");
                if ($fp !== false) {
                    $ret = @fread($fp,255);
		    $this->log("Linea resultado: " . $ret);
                    @fclose($fp);
                } else {
                    $sockError = true;
                }
            }
        } else {
            if ($this->operador=="ancel"){
                $this->mensaje = str_replace("%20"," ",$this->mensaje);
                $this->log("ES ANCEL, HACIENDO ECHO:: OK&mensaje=".$this->mensaje);
                echo "OK&mensaje=".$this->mensaje;
            }
        }
            
        return $ret;
    }
    
    /**
     * Itera entre los dos usuarios que hay para hacer envios con esta operadora...
     * Ellos no balancean su carga pero exigen que nosotros lo hagamos, asi que vemos el 
     * ultimo mensaje enviado y usamos el otro usuario
     */
    function getPersonalARUser(){
        $sql = "SELECT operador
                FROM smpp.enviados
                WHERE operador like 'personal_ar%'
                ORDER BY fecha desc, hora desc
                limit 1";
        
        $rs = mysql_query($sql, $this->db);
        if($rs) {
            $row = mysql_fetch_assoc($rs);
            if($row["operador"] == "personal_ar_1") {
                return "personal_ar_2";
            } else {
                return "personal_ar_1";
            }
        }
    }

    function enviar(){
    	$this->log("************** Enviando SMS *************");
    	$this->log("APP::".$this->app);
    	$this->log("CELULAR::".$this->celular);
    	$this->log("OPERADOR::".$this->operador);
    	$this->log("MENSAJE::".$this->mensaje);
        if (!empty($this->app) && !empty($this->celular) && !empty($this->operador) && !empty($this->mensaje)){
        	$this->log("USER::".$this->getUser());
            switch($this->getUser()) {
                case "ancel":
                    if (!empty($this->nroTramite)){
                        $url = "http://www.ancelinfo.com.uy:8090/envioSMS?txtCelularNumero=".substr($this->celular,-7)."&txtMensaje=".str_replace(" ", "%20", $this->mensaje).".&txtNroTramite=".$this->nroTramite."&txtAplicacion=".$this->app;
                    } elseif ($this->serviceNmbr != null) {
			            $this->tipo = 2;
			            $usuario_ancel = substr($this->celular, -7);
			            $url = array();
			            $url[] = "http://www.ancelutil.com.uy:8090/admEmpresa?operacion=altaServicio&servicio=".$this->serviceNmbr."&celular=".$usuario_ancel."&nroTramite=1";
			            $url[] = "http://www.ancelutil.com.uy:8090/envioSMS?txtCelularNumero=".$usuario_ancel."&txtMensaje=".urlencode($this->mensaje)."&txtNroServicio=".$this->serviceNmbr;		    
			            if($this->comportamiento_ancel == BORRAR_USUARIO_SERVICIO_ANCEL) {
				            $url[] = "http://www.ancelutil.com.uy:8090/admEmpresa?operacion=bajaServicio&servicio=".$this->serviceNmbr."&celular=".$usuario_ancel."&nroTramite=1";
			            } 
		            }
                    break;
                default:
                    $this->mensaje = urldecode($this->mensaje);
                    $url = "http://10.0.0.243:8080/dsmpp/http-input/submit?message=".urlencode($this->mensaje);
                    $url .= "&sourceAddress=".urlencode($this->app);
                    $url .= "&recipients=".urlencode($this->celular);
                    $url .= "&user=".urlencode($this->getUser());
                    if (!empty($this->urlPush)){
                        $url .= "&messageType=shortwappush&url=".urlencode($this->urlPush);
                        $this->tipo = 1;
                    }
                    $url .= "&".$this->getSmppParams();
                    if($this->getUser() == "tigo_py") {
                    	$url .= "&trackConfirmation=0";
                    }
                    break;
            }
            
           if($this->operador == "personal_ar") {
                $this->operador = $this->getPersonalARUser();
	            $url = str_replace("personal_ar", $this->operador, $url);
	            $this->log("Es PERSONAL AR, as’ que hay que balancear...(grrrr)");
	            
	        }

//               echo "URL: ".$url;
            $this->log("URL GENERADA::".$url);
            $ret = "";
            $sockError = false;

	        $ret = $this->_enviarMensaje($url, $sockError);
	        
	     

            // Leer retorno
            if ($sockError) {
                $result['status'] = "ERR";
                $result['code'] = "408"; // No se pudo conectar a la URL
                $result['sequence'] = "";

                $this->log("ERROR::".print_r($result, true));
            }else {
                $result = $this->parseResult($ret);
                $this->log("TODO EN ORDEN RESULT::".print_r($result, true));
            }

            // Guardar registro de envio
            $this->mensaje=str_replace("%20"," ",$this->mensaje);
            if ($this->tipo==1){
                $this->mensaje = $this->mensaje."@".$this->urlPush;
            }
	    /*
            $sql = "INSERT INTO smpp.enviados
                    (operador,app,celular,mensaje,tipo,fecha,hora,status,code,sequence)
                    Values('".$this->operador."','".$this->app."','".$this->celular."','".
                    $this->mensaje."',".$this->tipo.",CURDATE(),CURTIME(),'".$result['status']."','".
                    $result['code']."','".$result['sequence']."')";
		    */
	$sql = "INSERT INTO smpp.enviados
                    (operador,app,celular,mensaje,tipo,fecha,hora,status,code,sequence)
                    Values('".$this->operador."','".$this->app."','".$this->celular."','".
                    $this->mensaje."',".$this->tipo.",CURDATE(),CURTIME(),'".$result['status']."','".
                    $result['code']."','')";

            $this->log("GUARDANDO REGISTRO DE ENVIO::".$sql);

            mysql_query($sql,$this->db);
            $this->messageId = $result['sequence'];
            return true;
        } else {
        	$this->log("*************** ERROR EN EL ENVIO DEL SMS*****************");
            return false;
        }
    }

    function parseResult($ret){
            echo 'resultado: ';
            print_r($ret);
            $ret = trim($ret);
            $arr["status"] = "OK";
            $arr["code"] = "0";
            $arr["sequence"] = "";
            if (!empty($ret)){
                switch($this->getUser()) {
                    case "movistar_uy":
                    case "cti_uy_1111":
                    case "cti_uy_7276":
                    case "cti_uy_1110":
                    case "cti_uy_17574":
                    case "cti_uy_9390":
                    case "cti_uy_10101":
                    case "cti_uy_10100":
                    case "cti_uy_3003":
                    case "cti_uy_17572":
                        $pos = strpos($ret,"MessageID=")+10;
                        $ret = substr($ret,$pos,strpos($ret,".req")-$pos);
                        $arr["status"] = "OK";
                        $arr["code"] = "0";
                        $arr["sequence"] = $ret;
                        break;
                    case "ancel":
                        if (!empty($this->nroTramite)){
                            if (substr_count($ret,"El mensaje fue puesto en la cola para ser enviado")>0){
                                $arr["status"] = "OK";
                                $arr["code"] = "0";
                            }
                            if (substr_count($ret,"No tiene permitido enviar un SMS al celular")>0){
                                $arr["status"] = "ERR";
                                $arr["code"] = "1";
                            }
                            if (substr_count($ret,"Error de par?metros")>0){
                                $arr["status"] = "ERR";
                                $arr["code"] = "2";
                            }
                            if (empty($arr["status"])) { // Error indeterminado
                                $arr["status"] = "ERR";
                                $arr["code"] = "3";
                            }
                            $arr["sequence"] = $this->nroTramite;
                        } else {
                            $arr["status"] = "OK";
                            $arr["code"] = "0";
                            $arr["sequence"] = "0";
                        }
                        break;
                    default:
                        $ret = str_replace("ResultCode[","",$ret);
                        $ret = str_replace("]","",$ret);
                        $ret = explode(",",$ret);
                        $arr =  array("status"=>"","code"=>"","sequence"=>"");
                        if (count($ret)==3) {
                            $arr["status"] = str_replace("status=","",$ret[0]);
                            $arr["code"] = str_replace("code=","",$ret[1]);
                            $arr["sequence"] = str_replace("sequence=","",$ret[2]);
                        } else {
                            $arr["status"] = "CRITICAL";
                            $arr["code"] = "1000";
                            $arr["sequence"] = "0";
                        }
                        break;
                }
            }
        return $arr;
    }

    function getUser(){
        // movistar_pe, movistar_co, ola, comcel, cti_uy_9470  || movistar_uy, ancel
        $user = $this->operador;
        if ($this->operador=="cti_uy" || $this->operador=="claro_ar" || $this->operador=="claro_pe_wazzup_2" || $this->operador == "claro_pe_new"){
            $user = $this->operador."_".$this->app;
        }
        
        return $user;
    }

    function parseMensaje($mensaje){
	   $invalidos = array("","ˆ","","”","Ž","Ê-","»","«","û","Û","è","È","à", "À","Á","É","Í","Ó","Ú","Ü","á","é","í","ó","ú","ü","Ñ","ñ");
	   $validos   = array("e","a","c","i","e","-","-", "-","u", "U","e", "E", "a", "A", "A","E","I","O","U","U","a","e","i","o","u","u","NI","ni");
       $i = 0;
	   for($i=0;$i<count($invalidos);$i++){
	       $mensaje=str_replace($invalidos[$i],$validos[$i],$mensaje);
	   }
       //$mensaje=str_replace(" ","%20",$mensaje);
       return $mensaje;
	}

    function getSmppParams(){
        switch($this->operador){
            case "cti_uy":
                $params = array("sourceTon"=>2,"sourceNpi"=>1,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
            case "ola":
                $params = array("sourceTon"=>2,"sourceNpi"=>1,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
            case "comcel-pruebas":
            case "comcel":
                $params = array("sourceTon"=>7,"sourceNpi"=>30,"recipientsTon"=>1,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
            case "movistar_co":
                $params = array("sourceTon"=>1,"sourceNpi"=>1,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
            case "movistar_co_universal":
                $params = array("sourceTon"=>1,"sourceNpi"=>1,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
            case "movistar_pe":
                $params = array("sourceTon"=>0,"sourceNpi"=>2,"recipientsTon"=>0,"recipientsNpi"=>2,"dataCodingScheme"=>"ASCII");
                break;
            case "movistar_uy":
                $params = array("sourceTon"=>2,"sourceNpi"=>1,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
            case "viva_bo":
                $params = array("sourceTon"=>1,"sourceNpi"=>1,"recipientsTon"=>1,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
            case "claro_pe":
                $params = array("sourceTon"=>1,"sourceNpi"=>4,"recipientsTon"=>1,"recipientsNpi"=>1,"dataCodingScheme"=>"GSM7BIT");
                break;
            case "claro_pe_wazzup":
                $params = array("sourceTon"=>1,"sourceNpi"=>4,"recipientsTon"=>1,"recipientsNpi"=>1,"dataCodingScheme"=>"GSM7BIT");
                break;
            case "claro_pe_wazzup_2":
            case "claro_pe_new":
			    $params = array("sourceTon"=>1,"sourceNpi"=>4,"recipientsTon"=>1,"recipientsNpi"=>1,"dataCodingScheme"=>"GSM7BIT");
                break;
            case "tigo_gt":
                $params = array("sourceTon"=>0,"sourceNpi"=>0,"recipientsTon"=>0,"recipientsNpi"=>0,"dataCodingScheme"=>"ASCII");
                break;
            case "entel_bo":
                $params = array("sourceTon"=>2,"sourceNpi"=>1,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
            case "porta_ec":
                $params = array("sourceTon"=>1,"sourceNpi"=>1,"recipientsTon"=>1,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
            case "claro_ar":
                $params = array("sourceTon"=>2,"sourceNpi"=>1,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
            case "movistar_mx":
                $params = array("sourceTon"=>9,"sourceNpi"=>4,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
            case "claro_pa":
                $params = array("sourceTon"=>6,"sourceNpi"=>1,"recipientsTon"=>1,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
            case "claro_pa_ideas":
                $params = array("sourceTon"=>0,"sourceNpi"=>0,"recipientsTon"=>0,"recipientsNpi"=>0,"dataCodingScheme"=>"ASCII");
                break;
	    case "movistar_mx_1013":
		    //Ton y NPI están invertidos...>.<
                $params = array("sourceTon"=>9,"sourceNpi"=>4,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
             case 'tigo_cg':            
                $params = array("sourceTon"=>2,"sourceNpi"=>1,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"UTF8");             
             break;
             case 'tigo_hn':
                $params = array("sourceTon"=>1,"sourceNpi"=>2,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");             
             break;
             case 'tigo_sv':
                $params = array("sourceTon"=>1,"sourceNpi"=>2,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"ISO-8859-1");             
             break;
            default:
                $params = array("sourceTon"=>2,"sourceNpi"=>1,"recipientsTon"=>2,"recipientsNpi"=>1,"dataCodingScheme"=>"ASCII");
                break;
        }
        $smppParams = "recipientsTon=".urlencode($params['recipientsTon']);
        $smppParams .= "&recipientsNpi=".urlencode($params['recipientsNpi']);
        $smppParams .= "&sourceTon=".urlencode($params['sourceTon']);
        $smppParams .= "&sourceNpi=".urlencode($params['sourceNpi']);
        if ($this->tipo==0) {
            $smppParams .= "&dataCodingScheme=".urlencode($params['dataCodingScheme']);
        } else{
            $smppParams .= "&dataCodingScheme=GSM8BIT";
        }
        if ($this->track) {
            $smppParams .= "&trackDelivery=true";
        }
        if ($this->isValidTime($this->startTime)) {
            $smppParams .= "&startTime=".urlencode($this->startTime);
        }

        /* ----------------------------------------------------------
        En el caso de estos shortcodes para Movistar Peru, para que el MT se cobre debemos
        setearle el service type como BILL.
        Si se quisiera enviar free se debe dejar vac?o, pero al momento de las pruebas
        el mensaje nunca lleg?.
        */
        $array_service_type_bill = array("99106", "99107");
        if ($this->operador == "movistar_pe" && in_array($this->app, $array_service_type_bill)){
            $this->serviceType = "BILL";
        }
        // ----------------------------------------------------------

        if (!empty($this->serviceType)) {
            $smppParams .= "&serviceType=".urlencode($this->serviceType);
        }

        $this->log("SMPP PARAMS::".print_r($smppParams, true));

        return $smppParams;
    }

    function isValidTime($time){
         // 00:00:00
        if (preg_match('/^[0-2]{1}[0-9]{1}:[0-5]{1}[0-9]{1}:[0-5]{1}[0-9]{1}$/',$time)) {
            return true;
        } else {
            return false;
        }
    }
}
/*
int OK = 0;

Errores

int MESSAGE_REJECTED          = -1;
int MESSAGE_TIMEOUT           = -2;
int OUTPUTCHANNEL_UNAVAILABLE = -3;
int STORAGE_FAILURE           = -4;
int DSMPP_NOT_RUNNING         = -5;
int MESSAGELET_NOT_RUNNING    = -6;
int INPUTCHANNEL_NOT_RUNNING  = -7;
int MAX_SUBMIT_TRIES_EXCEEDED = -8;
int UNKNOWN_DELIVERY_NOTIFICATION_STATUS = -9;

int UNKNOWN_ORIGIN            = -201;
int UNKNOWN_MESSAGELET        = -202;
int UNKNOWN_INPUT_CHANNEL     = -203;
int NO_MESSAGELET_SELECTED    = -202;

int NOT_AUTHORIZED            = -401;

int SYSTEM_FAILURE               = -500;
int DATA_CODING_SCHEME_FAILURE   = -501;
int UNKNOWN_MESSAGE_REQUEST_TYPE = -502;
int MALFORMED_MESSAGE            = -503;
int SYSTEM_INTERRUPTED           = -504;

Codigos de error generados por filtros del sistema.

    int SYSTEM_FILTER_ERROR        = 200;
    int INVALID_PRIORITY           = SYSTEM_FILTER_ERROR + 0;
    int INVALID_DATE               = SYSTEM_FILTER_ERROR + 1;
    int START_DATE_AFTER_END_DATE  = SYSTEM_FILTER_ERROR + 2;
    int NULL_START_TIME            = SYSTEM_FILTER_ERROR + 3;
    int NULL_END_TIME              = SYSTEM_FILTER_ERROR + 4;
    int INVALID_RECIPIENTS         = SYSTEM_FILTER_ERROR + 5;
    int TRACK_DELIVERY_NOT_ALLOWED = SYSTEM_FILTER_ERROR + 6;
    int RECIPIENT_NOT_ALLOWED      = SYSTEM_FILTER_ERROR + 7;
    int PRIORITY_NOT_ALLOWED       = SYSTEM_FILTER_ERROR + 8;
    int REQUEST_INFO_NOT_ALLOWED   = SYSTEM_FILTER_ERROR + 9;
    int INVALID_MESSAGE            = SYSTEM_FILTER_ERROR + 10;
    int INVALID_ADDRESS            = SYSTEM_FILTER_ERROR + 11;
    int INVALID_MESSAGE_REFERENCE  = SYSTEM_FILTER_ERROR + 12;
    int INVALID_FRAGMENT_INDEX     = SYSTEM_FILTER_ERROR + 13;
    int INVALID_MESSAGE_TYPE       = SYSTEM_FILTER_ERROR + 14;
    int INVALID_RECIPIENT_TYPE     = SYSTEM_FILTER_ERROR + 15;
    int INVALID_PAYLOAD            = SYSTEM_FILTER_ERROR + 16;
    int SOURCE_ADDRESS_NOT_ALLOWED = SYSTEM_FILTER_ERROR + 17;
    int END_TIME_BEFORE_START_TIME = SYSTEM_FILTER_ERROR + 18;
    int INVALID_END_DATE_TIME      = SYSTEM_FILTER_ERROR + 19;

Reservados para filtros de usuario.

    int CUSTOM_FILTER_REJECT           = 400;
    int CUSTOM_FILTER_FAILED           = 500;
*/

?>
