<?php 
include("includes.php");

$miC = new coneXion("Web", true);
$db = $miC->db;

$idGlobalnet = $_GET['id'];
$idCat = $_GET['cat'];
$pagina = $_GET['p'];
$step = $_GET['step'];

$back = $_GET['b']; 


switch($step) {
	case 0:
		$info = obtenerDescJuego($db, $idGlobalnet);
		$texto = str_replace("&", "&amp;", $info['texto']);
		$screen = $info['screen'];
		
		$nombre_juego = $info['nombre'];

		$href = "game_desc.php?p=$pagina&amp;step=1&amp;cat=$idCat&amp;id=".$idGlobalnet;
		$link_descarga = new Link($href, "Descargar");

		$volver_link = "games.php?p=$pagina&amp;step=1&amp;cat=$idCat";
	break;

	case 1:
	 	
		$datos = obtenerDatosContenido($db, $idGlobalnet, true);
 		
		if($back=='c'){
			$volver_link = "games.php?p=$pagina&amp;step=1&amp;cat=$idCat";
		}else{
			$volver_link = "home.php";
		}
		
		
		$url="hacer_descarga.php?tipoCat=".$datos['tipo']."&amp;id=$idGlobalnet";
		
		$url_no = "url_no.php?b=$back&amp;p=$pagina&amp;cat=$idCat&amp;tipoCat=31&amp;id=$idGlobalnet";
		$texto = new MensajeDescarga($url,$url_no,31,$idGlobalnet);
		$texto->forceSiUrl($url);
		$texto->setWapName("halloween_wap");
		
		$home_link = "home.php";
	
	break;
}


/////////////////////////////////////////////
//// PRESENTACION
/////////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL);

$seccion = new Seccion("Datos del Juego", "center", SMALL_FONT_SIZE);

if($screen) {
	$seccion->AddComponent(new Imagen("getimage.php?path=".PREVIEW_HOST.$screen), "<br/>");
}


if($nombre_juego) {
	$seccion->AddComponent("<b>$nombre_juego</b><br/><br/>");
}
/*
if($link_descarga) {
	$seccion->AddComponent($link_descarga);
}
*/

if($texto) {
	$seccion->AddComponent("<br/>", $texto, "<br/>");
}

if($link_descarga) {
	$seccion->AddComponent($link_descarga);
}

$pie_seccion = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
if($volver_link) {
	$pie_seccion->AddComponent(new Link($volver_link, TEXT_VOLVER));
}
$pie_seccion->AddComponent(new Link("home.php", TEXT_HOME));

$pagina->AddComponent($seccion);
$pagina->AddComponent($pie_seccion);

$pagina->WriteHeaders();
echo $pagina->Display();

?>