<?php

/**
 * KLIB1
 * Funciones Varias para una vida mejor :P
 * v1.2 - by kmS
 *
 * FEATURES
 *---------------------
 * khtml = html class
 * ktemplate = basic template
 * konvert = utf8 sanitice utilities
 * kmail = guess what?... envia mail ^^
 *
 * HISTORY
 *----------------
 * Julio 08, v1.2: incorporados khtml,ktemplate y konvert
 * Julio 08, v1.1: implementado el kimport (inluye solo lo necesario)
 * Julio 08, v1.0: RC, incluye kmail v1.0
 */

define("KLIB_DIR", dirname(__FILE__)."/klib");

function kimport ($module) {
	$module = strtolower($module);

	switch ($module) {
		case "kmail":
			include_once(KLIB_DIR."/kmail.klass.php");
			break;
		case "konvert":
			include_once(KLIB_DIR."/konvert.function.php");
			break;
		case "ktemplate":
			include_once(KLIB_DIR."/ktemplate.klass.php");
			break;
		case "khtml":
			include_once(KLIB_DIR."/khtml.klass.php");
			break;
		default:
			// no se incluye nada
	}
}


?>