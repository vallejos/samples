<?php

$detailType = $_GET["d"];
$gameId = $_GET["id"];

if (empty($detailType)) die("ERROR");
if (empty($gameId)) die("ERROR");

include_once(dirname(__FILE__)."/lib/functions.php");
include_once(dirname(__FILE__)."/lib/useragents.php");
include_once(dirname(__FILE__)."/lib/konexion.php");

$dbc = new konexion("Web");

foreach ($uaTigo as $devUA) {
	$idCel = obtenerIDCelular($devUA, $dbc->db);
	if ($idCel == 0) {
		$celsNotFound[] = "$devUA";
	} else if ($idCel === FALSE) {
		$sqlErrors[] = "$devUA";
	} else if (soportaJuego($dbc->db, $idCel, $gameId)) {
		$celsSupported[] = "$devUA";
	} else {
		$celsNotSupported[] = "$devUA";
	}
}

switch ($detailType) {
	case "celstigo":
		$title = "LISTA TOTAL DE DEVICES TIGO PARAGUAY";
		$arrayToShow = $uaTigo;
	break;
	case "notfound":
		$title = "LISTA DE DEVICES NO ENCONTRADOS PARA EL CONTENIDO $gameId";
		$arrayToShow = $celsNotFound;
	break;
	case "supported":
		$title = "LISTA DE DEVICES ENCONTRADOS Y <b>SOPORTADOS</b> POR EL CONTENIDO $gameId";
		$arrayToShow = $celsSupported;
	break;
	case "notsupported":
		$title = "LISTA DE DEVICES ENCONTRADOS Y <b>NO SOPORTADOS</b> POR EL CONTENIDO $gameId";
		$arrayToShow = $celsNotSupported;
	break;
	default:
		die("ERROR");
}

echo "<h1>$title</h1>";
echo "<hr/>";

dumpArray($arrayToShow);

echo "<hr/>";

?>