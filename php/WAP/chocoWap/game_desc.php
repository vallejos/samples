<?php 
include("includes.php");

$miC = new coneXion("Web", true);
$db = $miC->db;

$push = isset($_GET["push"])?$_GET['push']:"chocolate_wap";

$idGlobalnet = $_GET['id'];
$step = intval($_GET['step']);


switch($step) {

	case 0:
		$datos = obtenerDatosContenido($db, $idGlobalnet, true);
		$info = obtenerDescJuego($db, $idGlobalnet);
		$texto = str_replace("&", "&amp;", $info['texto']);
		
		$screen = $info['screen'];
	
		$url="hacer_descarga.php?push=$push&amp;tipoCat=".$datos['tipo']."&amp;id=$idGlobalnet";
		$volver_link = "games.php?push=$push&amp;step=1";
		$texto_desc  = new MensajeDescarga($url,$volver_link, $datos['tipo'], $idGlobalnet);
		//$texto_desc2 = new MensajeDescarga2($url,$volver_link, $datos['tipo'], $idGlobalnet);
		
		$nombre_juego = $info['nombre'];
		$href = "game_desc.php?push=$push&amp;step=1&amp;id=".$idGlobalnet;
		$link_descarga = new Link($href, "Descargar");
	
	break;
	case 1:
	 	$datos = obtenerDatosContenido($db, $idGlobalnet, true);
		//el tercer paso: mostramos el mensaje con las opciones "SI" o "NO"
		$volver_link = "game_desc.php?push=$push&amp;step=0&amp;id=$idGlobalnet";
		$url="hacer_descarga.php?push=$push&amp;tipoCat=".$datos['tipo']."&amp;id=$idGlobalnet";
		$texto = new MensajeDescarga($url,$volver_link, $datos['tipo'], $idGlobalnet);
		
		$home_link = "home.php?push=$push";
	break;
}

/////////////////////////////////////////////
//// PRESENTACION
/////////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL);

$seccion = new Seccion("Datos del Juego", "center", SMALL_FONT_SIZE);

if($nombre_juego) {
	$seccion->AddComponent("<b>$nombre_juego</b><br/><br/>");
}

if($screen) {
	$seccion->AddComponent(new Imagen(PREVIEW_HOST.$screen));
}

if($texto_desc) {
	$seccion->AddComponent($texto_desc);
}

if($texto) {
	$seccion->AddComponent($texto, "<br/>");
}

if($texto_desc2) {
	$seccion->AddComponent("<br/>", $texto_desc2);
}

$pie_seccion = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
if($volver_link) {
	$pie_seccion->AddComponent(new Link($volver_link, TEXT_VOLVER));
}
$pie_seccion->AddComponent(new Link("home.php", "<br/>".TEXT_HOME));

$pagina->AddComponent($seccion);
$pagina->AddComponent($pie_seccion);


$pagina->WriteHeaders();
echo $pagina->Display();

?>