<?php

/**
 * kmS Google Analytics Proxy for WAPs
 * v0.2 by kmS
 *
 * History:
 * 18.03.2010 - v0.3
 * 17.03.2010 - v0.2
 * 16.03.2010 - rc :)
 */

include_once($_SERVER["DOCUMENT_ROOT"]."../lib/conexion.php");
include_once($_SERVER["DOCUMENT_ROOT"]."../lib/kStats/classes/kga.class.php");
$kgaConn = new conexion("stats");

$kga = new kga($kgaConn);
$GA_ACCOUNT = $kga->getAccount();
if ($GA_ACCOUNT !== FALSE) {
	$GA_PIXEL = "/ga.php";
	$googleAnalyticsImageUrl = googleAnalyticsGetImageUrl();

	// imprime imagen usando componentes de la wap
	$kgaSeccion = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
	$kgaImage = new Imagen($googleAnalyticsImageUrl, "");
	$kgaSeccion->AddComponent($kgaImage);
	$pagina->AddComponent($kgaSeccion);
} else {
	// error: no se encontro account para el dominio

}

function googleAnalyticsGetImageUrl() {
	global $GA_ACCOUNT, $GA_PIXEL;
	$url = "";
	$url .= $GA_PIXEL . "?";
	$url .= "utmac=" . $GA_ACCOUNT;
	$url .= "&utmn=" . rand(0, 0x7fffffff);
	$referer = $_SERVER["HTTP_REFERER"];
	$query = $_SERVER["QUERY_STRING"];
	$path = $_SERVER["REQUEST_URI"];
	if (empty($referer)) {
		$referer = "-";
	}
	$url .= "&utmr=" . urlencode($referer);
	if (!empty($path)) {
		$url .= "&utmp=" . urlencode($path);
	}
	$url .= "&guid=ON";
	return str_replace("&", "&amp;", $url);
}

?>