<?php
include_once(dirname(__FILE__)."/constantes.php");
include_once(CONEXION_PATH);
include_once(RUTAS_PATH);
include_once(SMS_PATH);
include_once($_SERVER['DOCUMENT_ROOT']."/../lib/claro_ar/CobroClaroAr.php");
include_once(dirname(__FILE__)."/../wap_common/common_functions.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/../lib/CelularWurfl.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/../lib/rewrite_lib.php");

include_once(dirname(__FILE__) . "/../wap_common/getCelularHeader.php");

include_once(dirname(__FILE__) . "/functions.php");
include_once(dirname(__FILE__) . "/funciones_descarga.php");

/*$dirname = dirname(__FILE__ ) . "/../wap_common/wapComponents";
include_once(dirname(__FILE__) . "/../wap_common/wapComponents/wapComponent.php");
include_once(dirname(__FILE__) . "/../wap_common/wapComponents/formElement.php");*/

include_once($_SERVER['DOCUMENT_ROOT'] . "/../lib/wapComponents/wapComponent.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/../lib/wapComponents/formElement.php");
$dirname = $_SERVER['DOCUMENT_ROOT'] . "/../lib/wapComponents";

$d = dir($dirname);
while( ($entry = $d->Read()) !== false){
	if(substr($entry, 0, 1) != "." && !is_dir($dirname."/".$entry)) {
		include_once($dirname."/".$entry);
	}
}

include_once($_SERVER['DOCUMENT_ROOT']."/../lib/tops.mod.php");
include_once($_SERVER['DOCUMENT_ROOT']."/../lib/wap_functions.php");
include_once($_SERVER['DOCUMENT_ROOT']."/../lib/wapSession.php");

?>