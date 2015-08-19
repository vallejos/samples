<?php


// configuracion de puntos por barrio
define('PUNTOS_CE', 14);
define('PUNTOS_CO', 10);
define('PUNTOS_CV', 10);
define('PUNTOS_CP', 7);
define('PUNTOS_AG', 4);
define('PUNTOS_PP', 8);

// barrios validos
$PUNTOS_VALIDOS = Array("ag", "ce", "cp", "cp", "cv", "pp", "co");

// database name
define('DB_NAME', "qrapp_antel");

// tipos contenidos
define('TYPE_TEXT', 1);
define('TYPE_IMAGE', 2);
define('TYPE_AUDIO', 3);


// config de wap
define("NOMBRE_PORTAL", "Guia Benedetti");
define("NOMBRE_PORTAL_DESCARGA", "Guia Benedetti");
define("PATH_PREVIEW", "http://www.wazzup.com.uy/");
define("PREVIEW_HOST", "http://www.wazzup.com.uy/");

include_once(dirname(__FILE__) . "/../wap.wazzup.com.uy/wap_common/getCelularHeader.php");

define("CONEXION_PATH", $_SERVER['DOCUMENT_ROOT'] . "/../lib/conexion.php");
define("RUTAS_PATH", $_SERVER['DOCUMENT_ROOT'] . "/../lib/rutas.php");
define("SMS_PATH", $_SERVER['DOCUMENT_ROOT'] . "/../lib/sms.php");

define("OPERADORA", "ancel_uy");
define("PATH_IMAGENES", $_SERVER['DOCUMENT_ROOT'] . "/..");

define("HIDE_HEADER", 0);
define("SHOW_LINK_DALE", 0);
define("ANCEL",1);
define("ANCEL_DRUTT",1);

define("PREFIX_TITULO_SECCION", "nuevo");
define("SECCION_TIT_FOLDER", "imgs_titulo");
define("TIT_SECCION_FONT_COLOR", "#FFFFFF");
define("TIT_SECCION_PADDING", 0); //Padding lateral que tendr� la imagen del titulo
define("GENERAR_IMAGEN_TITULO_SECCION", 0);

define("OPERADORA_WHLST", "ancel");

define("OPERADORA_MCM", "ancel.uy");
define("NOMBRE_MCM","Guia Benedetti");

define("SUSCRIPTION_ID_VIDEO", 45);
define("SUSCRIPTION_ID_WALLPAPER", 46);

// wap ID 
define ("WEED", "1438");

define("MARCA_BLANCA", 0); 

// habilito medidas adicionales para la wap (BETA)
define("USE_EXTENDED_LAYOUT", TRUE); 

// habilito ocultar logo wazzup para la wap (BETA)
define("HIDE_WAZZUP_LOGO", TRUE); 

?>