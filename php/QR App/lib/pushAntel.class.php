<?php

class pushAntel {
    var $db;
    var $app;
    var $celular;
    var $mensaje_original;
    var $respuesta;
    var $url_push;
    var $pk_push_enviados;
    var $mensaje_push;
    var $sms_adicional;

    function pushAntel($db,$app,$celular,$mensaje_original){
        $this->db = $db;
        $this->app = $app;
        $this->celular = "+5989" . substr($celular, -7);
        $this->mensaje_original = $mensaje_original;
    }
    
    function enviarPush($url_push, $mensaje_push="Seleccione IR A", $sms_adicional=false){
        $this->url_push = $url_push;
        $this->mensaje_push = $mensaje_push;
        $this->respuesta = $sms_adicional;
        $this->sms_adicional = $sms_adicional;
        
        $sql = "INSERT INTO ancel.push_enviados
                (app,celular,mensaje,url_push,fecha,hora)
                VALUES ('".$this->app."','".$this->celular."','" .
                $this->mensaje_push."','".$this->url_push."',CURDATE(),CURTIME())";
        
        mysql_query($sql,$this->db);
        $this->pk_push_enviados = mysql_insert_id($this->db);
        
        if (!empty($this->app) && !empty($this->celular)){
            $xml = $this->generarPeticion();
            $this->enviar_peticion_ancel($xml);
            
            if ($this->sms_adicional !== false){
                $sql = "UPDATE ancel.push_enviados
                SET sms_adicional='" . $this->sms_adicional . "'
                WHERE pk_push_enviados=" . $this->pk_push_enviados;
                
                header("PUSH: incorrecto");
                echo($sms_adicional);
            }
            
            return true;
        } else {
            $mensaje_al_usuario = "Ha ocurrido un error. Por favor intente mas tarde.";
            $sql = "UPDATE ancel.push_enviados
                SET error='ERROR SOCKET',mensaje='" . $mensaje_al_usuario . "'
                WHERE pk_push_enviados=" . $this->pk_push_enviados;
            mysql_query($sql,$this->db);
            $this->avisoMail("ERROR SOCKET", "HAGAN ALGOOOOOO...");
            
            header("PUSH: incorrecto");
            echo($mensaje_al_usuario);
        }
    }
    
    function enviarSms($respuesta){
        $this->respuesta = $respuesta;
        
        header("PUSH: incorrecto");
        echo($this->respuesta);
        
        $sql = "INSERT INTO ancel.push_enviados
                (app,celular,fecha,hora,mensaje)
                VALUES ('".$this->app."','".$this->celular."',
                CURDATE(),CURTIME(),'" . $this->respuesta . "')";
        mysql_query($sql, $this->db);
        
        return true;
    }    
    
    function generarPeticion(){
//ppg-notify-requested-to=\"http://localhost:8090/servlet/pushNotif\" 
        $xml = "
--DELIOS
Content-Disposition: form-data; name=\"pushRequest\"
Content-Type: application/xml\n
<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<!DOCTYPE pap PUBLIC \"-//WAPFORUM//DTD PAP 1.0//EN\" \"http://www.wapforum.org/DTD/wml_1.1.xml\">\n
<pap>
<push-message push-id=\"" . $this->pk_push_enviados . "@wazzup.com.uy\" source-reference=\"" . $this->mensaje_original . "\" progress-notes-requested=\"true\">
<address address-value=\"wappush=" . $this->celular . "/type=PLMN@ppg.operator.com\"/>
<quality-of-service priority=\"high\" delivery-method=\"unconfirmed\" network=\"GSM\" network-required=\"true\" bearer=\"CSD\" bearer-required=\"false\">
</quality-of-service>
</push-message>
</pap>\n
--DELIOS
Content-Disposition: form-data; name=\"PushContent\"
Content-Type: text/vnd.wap.si\n
<?xml version=\"1.0\"?>
<!DOCTYPE si PUBLIC \"-//WAPFORUM//DTD si 1.0//EN\" \"http://www.wapforum.org/DTD/si.dtd\">\n
<si>
<indication href=\"" . $this->url_push . "\" si-id=\"1\" action=\"signal-high\">" . $this->mensaje_push . "</indication>
</si>
--DELIOS--";

        return $xml;    
    }
    
    function enviar_peticion_ancel($xml, $tipo_envio="push"){
        /*
        $fp = @fopen("/var/www/tmp/push_ancel_logs/xml".date("Y_m_d").".txt", "a+");
        if($fp) {
            fwrite($fp, "XML-----\n" .$xml . "\n---------------");
            
            fclose($fp);            
        }
        */
        
        $host = "200.40.246.18";
        $port = "50004";
        $script = "/servlet/ppg";

        $linea = "";
        // abro la conexión
        $socket = fsockopen($host, $port, $errno, $errstr);
        $retorno_leido = false;
        if ($socket){ // Si está abierta...
            fputs($socket, "POST $script HTTP/1.1\n");
            fputs($socket, "Host: $host\n");
            fputs($socket, "Pragma: no-cache\n");
            fputs($socket, "Content-Type: multipart/related; boundary=DELIOS\n");
            fputs($socket, "Connection: keep-alive\n");
            fputs($socket, "PUSH: correcto\n");
            fputs($socket, "Content-length: ".strlen($xml)."\n\n");
            fputs($socket, $xml . "\r");
            //Logeo del request:
            $fp = @fopen(APP_LOG_DIR."/push-logs-respuesta".date("Y_m_d").".log", "a+");            
            if($fp) {
                fwrite($fp, date("H:i:s")."---- Inicio XML de request ---- ");
                fwrite($fp, date("H:i:s")."$xml\n");
                fwrite($fp, date("H:i:s")."---- Fin XML de request ---- ");                
                fclose($fp);            
            }
            
            
            do {
                $linea .= fread($socket, 80);
                $stat = socket_get_status($socket);
            }
            while($stat["unread_bytes"]);

            fclose($socket);
            
            $fp = @fopen(APP_LOG_DIR."/push-logs-respuesta".date("Y_m_d").".log", "a+");            
            if($fp) {
                fwrite($fp, date("H:i:s")."$linea\n");
                
                fclose($fp);            
            }
        } else{

            $this->avisoMail("ERROR SOCKET", $errno . "---" . $errstr);

            $fp = @fopen(APP_LOG_DIR."/push-logs".date("Y_m_d").".log", "a+");
            if($fp) {
                fwrite($fp, date("H:i:s")."\nERROR SOCKET " . $errno . "---" . $errstr . "\n");
                
                fclose($fp);            
            }
            $error_code = "ERROR_SOCKET";
        }
    }
    
    function avisoMail($asunto, $msg){
        $from = "leonardo.hernandez@globalnetmobile.com";
        $from_name = "QR Push";
        $to = "leonardo.hernandez@globalnetmobile.com";
        //$cc = "fernando.doglio@globalnetmobile.com";

        //$m = new Email($from_name, $from, $to, $asunto, $msg,"", $cc);
//        $m = new Email($from_name, $from, $to, $asunto, $msg,"");
//        $m->send();
	mail($to, $asunto, $msg);
    }
}
?>
