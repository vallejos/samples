<?php

include_once("config.php");
include_once("includes.php");
include_once("functions.php");


checkLock();


$dbName = DB_NAME;
$dbc = new coneXion(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$sql = " SELECT * FROM $dbName.imagenes WHERE procesado=0 LIMIT ".PROCS_PER_RUN;
$rs = mysql_query($sql, $dbc->db);
if (!$rs) {    
        die ("DB ERROR");
}

while ($obj= mysql_fetch_object($rs)) {
        $id = $obj->id;
        $celular = $obj->celular;

        $qrImgName = $id.".jpg";

        $downloadOk = getFtpFiles(FTP_PATH."/$qrImgName");

        if ($downloadOk === TRUE) {        
                // leer qr
                $qrRead = readQRCode($qrImgName);

                // parse result
                if (qrFound($qrRead) === TRUE) {
                        $qrFound = TRUE;

                        $urlPush = readUrl($qrRead);

                        // valido url
                        if (isValidUrl($urlPush) === TRUE) {
                                // send sms push
                                $msgPush = "Ir a";
                                $push = new pushAntel($dbc->db, SMS_SC, $celular, $msgPush);
                                $sent = $push->enviarPush($urlPush, $msgPush);
                        } else {
                                // send sms
                                $msgSms = ERROR_MSG_BADURL;
                                $sent = enviarSMS($celular, $msgSms);
                        }
                                
                        // actualizo enviados
                        if ($sent === TRUE) $enviado = "OK";
                        else $enviado = "ERR";
                        $insSql = " INSERT INTO $dbName.envios SET enviado=1, idimagen='$id', app='".SMS_SC."', celular='$celular', url='$urlPush', accion='push', status='$enviado', fecha_alta=CURDATE(), hora_alta=CURTIME() ";
                        $rsIns = mysql_query($insSql, $dbc->db);

                        // preparo query
                        $sqlUpdate = " UPDATE $dbName.imagenes SET procesado=1, status='OK', url='$urlPush' WHERE id=$id ";
                } else {
                        $qrFound = FALSE;

                        // send sms
                        $msgSms = ERROR_MSG_DEFAULT;
                        $sent = enviarSMS($celular, $msgSms);

                        if ($sent === TRUE) $enviado = "OK";
                        else $enviado = "ERR";

                        $insSql = " INSERT INTO $dbName.envios SET enviado=1, idimagen='$id', app='".SMS_SC."', celular='$celular', url='', accion='sms', status='$enviado', fecha_alta=CURDATE(), hora_alta=CURTIME() ";
                        $rsIns = mysql_query($insSql, $dbc->db);

                        // preparo query
                        $sqlUpdate = " UPDATE $dbName.imagenes SET procesado=1, status='ERR' WHERE id=$id ";
                }

                // update db
                $rsUpdate = mysql_query($sqlUpdate, $dbc->db);
                if (!$rsUpdate) {    
                        die ("DB ERROR");
                }
                
                
        }
}


clearLock();


?>