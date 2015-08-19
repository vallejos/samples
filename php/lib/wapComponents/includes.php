<?php

$dirname = dirname(__FILE__);

include_once($dirname."/../precios_contenidos/PreciosContenidos.php");
include_once($dirname."/../sql_cache/SQLCache.class.php");
include_once($dirname."/wapComponent.php");
include_once($dirname."/formElement.php");

$d = dir($dirname);
while( ($entry = $d->Read()) !== false){
	if(substr($entry, 0, 1) != "." && !is_dir($dirname."/".$entry)) {
		include_once($dirname."/".$entry);
	}
}

?>