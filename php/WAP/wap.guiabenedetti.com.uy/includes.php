<?php

include_once("functions.php");

include_once(dirname(__FILE__)."/../wap_common/common_functions.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/../lib/CelularWurfl.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/../lib/rewrite_lib.php");
include_once(dirname(__FILE__) . "/../wap_common/getCelularHeader.php");
include_once($_SERVER['DOCUMENT_ROOT']."/../lib/generalFunctions.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/../lib/wapComponents/wapComponent.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/../lib/wapComponents/formElement.php");
$dirname = $_SERVER['DOCUMENT_ROOT'] . "/../lib/wapComponents";
$d = dir($dirname);
while( ($entry = $d->Read()) !== false){	
	if(substr($entry, 0, 1) != "." && !is_dir($dirname."/".$entry)) {
		include_once($dirname."/".$entry);
	}
}

include_once($_SERVER['DOCUMENT_ROOT']."/../lib/wap_functions.php");

?>