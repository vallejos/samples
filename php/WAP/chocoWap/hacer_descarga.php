<?php
include("includes.php");
//------------------------------------
// Conexion con DB
//------------------------------------
$miC = new coneXion("Web", true);
$db = $miC->db;
$nombre_wap = "chocolate_wap";

///////////////////////////////////////////////////////7
/////       LOGICA
///////////////////////////////////////////////////////7

$type 		= isset($_GET['tipoCat']) ? $_GET['tipoCat'] : 0;
$xxx  		= isset($_GET['xxx']) ? $_GET['xxx'] : 0;
$idCat 		= isset($_GET['cat']) ? $_GET['cat'] : 0;
$idCont 	= isset($_GET['id']) ? $_GET['id'] : 0;
$back		= isset($_GET['b']) ? $_GET['b'] : "h";

// Link Para volver
if($back == "h"){
	$volver_link = "home.php?push=$nombre_wap";
} elseif ($back == "b"){
	$volver_link = "buscador.php?push=$nombre_wap";
} else {
	$pagesByType = array(31 => "games", 62 => "videos", 63 => "themes", 7 => "images", 5 => "images",
						 29 => "ringtones", 23 => "ringtones");
	$page = (isset($pagesByType[$type])) ? $pagesByType[$type] : "home";
	$volver_link = "$page.php?push=$nombre_wap&amp;cat=$idCat&amp;tipoCat=$type&amp;step=1";
}

$home_link = "home.php?push=$nombre_wap";
$texto 	   = "";

$cobro = new CobroClaroAr($miC, $msisdn);
if($cobro !== FALSE) {
	$cobro->set_nombre_wap($nombre_wap);
	$retorno = $cobro->comprar_contenido($idCont, $ua);
	if($retorno === true) {
		// aca va redireccion a descarga contenido
		$url_download = urlGenerica($msisdn, $idCont, $cobro->get_billing_id(), "claro_ar");
//		$pagina = new Redirector($url_download, 1, "","Lo estamos redireccionando a su compra..." );
		header("location: $url_download");
		die();
		$pagina->WriteHeaders();
		echo $pagina->Display();
		die();
	} else {
		$texto = $retorno;
	}
} else {
	$texto 	 = "Ha ocurrido un error, por favor intentalo mas tarde";
	logeo("*******ERROR - NO VIENE EL MSISDN ***********");
	logeo("    HEADERS::".print_r(getallheaders(), true));
	logeo("	   ARRAY SERVER::".print_r($_SERVER, true));
	logeo("*********************************************");
}

$pagina  = new Pagina(NOMBRE_PORTAL." - Descarga tu contenido");
$seccion = new Seccion("Descargas", "center", SMALL_FONT_SIZE);

if($texto) {
	$seccion->AddComponent($texto);
}

if($seccion) {
	$seccion->AddComponent(new Link($home_link, "<br/>" . TEXT_HOME));
	$pagina->AddComponent($seccion);
}

$pagina->WriteHeaders();
echo $pagina->Display();
?>