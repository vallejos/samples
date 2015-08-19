<?php

// donde esta instalada la app
define("APP_DIR", "/root/www/tools/batch/qrApp");

// zxing qr library path
define("ZXING_QR_DIR", "/root/www/tools/batch/zxing");

// zxing engine advanced parameters
define("QR_ADVANCED_PARAMS", "--try_harder");


// shortcode a utilizar para envio de sms push
define("SMS_SC", "770");
define("SMS_NRO_SERVICIO", "18");

// total de mms a procesar por cada run del cron
define("PROCS_PER_RUN", 10);

// database
define("DB_HOST","10.0.0.240");
define("DB_USER","user");
define("DB_PASS","pass");
define("DB_NAME","qrapp_antel");

// ftp
define("FTP_HOST","10.0.0.241");
define("FTP_PORT","21");
define("FTP_USER","user");
define("FTP_PASS","pass");
define("FTP_PATH","/tmp/mm7/qrantel");
define("FTP_CONN_RETRIES",5);

// mensaje de error
define("ERROR_MSG_DEFAULT", "No se pudo procesar la imagen. Intente captar nuevamente la imagen con mayor nitidez.");
define("ERROR_MSG_BADURL", "URL no valida.");

// local dir for downloaded images
define("TMP_QR_DIR", APP_DIR."/tmp");

// local log dir
define("APP_LOG_DIR", "/var/log/qrApp");

// app lockfile
define("LOCKFILE", TMP_QR_DIR."/lock");

?>
