<?php


function avisoMail($msg, $to="8609380k189@ancelinfo.com.uy") {
	mail($to, "QR App", "$msg");
}

function checkLock() { 
        if (file_exists(LOCKFILE)) {
		avisoMail("Process LOCKED!!");
		die();
	} else return createLock();
}


function createLock() {
        if (!file_exists(LOCKFILE)) return touch(LOCKFILE);
        else return FALSE;
}


function clearLock() {
        if (file_exists(LOCKFILE)) return unlink (LOCKFILE);
        else return FALSE;
}


/**
 * 
 * @param type $from
 * @return type 
 */
function getFtpFiles($from) {
        $info = pathinfo($from);
        $name = trim($info['basename']);

        $ftpCon = new Ftp(FTP_HOST, FTP_USER, FTP_PASS, FTP_PORT);

        $content_download = FALSE;

        $connected = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
        if ($connected === TRUE) {
                $to = TMP_QR_DIR."/".$name;
                $downloaded = $ftpCon->bajar_r($from, $to, FTP_CONN_RETRIES);

                if ($downloaded === TRUE) {
                    $content_download = TRUE;
                } else {
                    $content_download = FALSE;
                }

        } else {
                $content_download = FALSE;
        }

        $ftpCon->logout();

        return $content_download;    
}

/**
 *
 * @param type $from 
 */
function readQRCode($from) {
        $img = TMP_QR_DIR."/$from";
        $lineToRun = "java -cp ".ZXING_QR_DIR."/javase/javase.jar:".ZXING_QR_DIR."/core/core.jar com.google.zxing.client.j2se.CommandLineRunner $img ".QR_ADVANCED_PARAMS ;
        $out = shell_exec($lineToRun);

        return $out;
}


/**
 * Devuelve TRUE si se encontro un barcode, FALSE en caso contrario
 * @param type $out 
 */
function qrFound($out) {    
        if (preg_match("/\bNo barcode found\b/i", $out)) {
                return FALSE;
        } else {
                return TRUE;
        }
        
}


/**
 *
 * @param type $out
 * @return type 
 * 
 * Ejemplo de Parsing: 
 * 1: 'file:/root/www/tools/batch/qrApp/tmp/2.jpg (format: QR_CODE, type: URI):'
2: 'Raw result:'
3: 'http://www.dale.com.uy'
4: 'Parsed result:'
5: 'http://www.dale.com.uy'
6: 'Found 4 result points.'
7: '  Point 0: (186.5,385.5)'
8: '  Point 1: (193.0,97.5)'
9: '  Point 2: (519.5,100.0)'
10: '  Point 3: (470.0,348.0)'
11: ''
 * 
 */
function readUrl($out) {
        $url = "";
        $rows = explode("\n", $out);

        $i=0;
        foreach ($rows as $row) {
                $i++;
                if ($i == 5) $url = trim($row); // la linea 5 en el output contiene la url procesada
        }
        
        return $url;
}


/**
 *
 * @param type $cel
 * @param type $msg
 * @return type 
 */
function enviarSMS($cel, $msg) {
        $url = "http://www.ancelutil.com.uy:8090/admEmpresa?operacion=altaServicio&servicio=".SMS_NRO_SERVICIO."&celular=".$cel."&nroTramite=1";
        $fp = @fopen($url, "r");

        $url2 = "http://www.ancelutil.com.uy:8090/envioSMS?txtCelularNumero=".substr($cel, -7)."&txtMensaje=".str_replace(" ", "%20", $msg)."&txtNroServicio=".SMS_NRO_SERVICIO;
        $fp2 = @fopen($url2, "r");

        $url3 = "http://www.ancelutil.com.uy:8090/admEmpresa?operacion=bajaServicio&servicio=".SMS_NRO_SERVICIO."&celular=".$cel."&nroTramite=1";
        $fp3 = @fopen($url3, "r");

        return TRUE;
}


/**
 *
 * @param type $url
 * @return type 
 * 
 * // valid urls
$url = "https://user:pass@www.somewhere.com:8080/login.php?do=login&style=%23#pagetop";
$url = "http://user@www.somewhere.com/#pagetop";
$url = "https://somewhere.com/index.html";
$url = "ftp://user:****@somewhere.com:21/";
$url = "http://somewhere.com/index.html/"; //this is valid!!
 * 
 */
function isValidUrl($url) {
        $urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
        if (eregi($urlregex, $url)) return TRUE;
        else return FALSE;
}


?>
