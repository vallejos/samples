<?php
include("includes.php");

$miC = new coneXion("Web", true);
$db = $miC->db;
$idGlobalnet = $_GET['id'];

$idCat = isset($_GET['cat'])?$_GET['cat']:0;
$titulo = $_GET['titulo'];
$gratis = (isset($_GET['gratis']) && cantDescargasGratis($db, $celular) > 0) ? 1 : 0;
$pagina = $_GET['p'];
$type = $_GET['tipoCat'];
$back=$_GET['b'];

$datos = obtenerDatosContenido($db, $idGlobalnet, true);
$info = obtenerDescJuego($db, $idGlobalnet);
$descripcion = str_replace("&", "&amp;", $info['texto']);
$nombre_juego = $info['nombre'];

$url="hacer_descarga.php?gratis=$gratis&amp;tipoCat=".$datos['tipo']."&amp;id=$idGlobalnet";
//$volver_link = "games.php";
$url_no = "url_no.php?b=$back&amp;p=$pagina&amp;cat=$idCat&amp;tipoCat=31&amp;id=$idGlobalnet&amp;b=$back";
$screen = $info['screen'];
if($gratis==1){
	$link_gratis1 = new Link($url,"<br/>Descarga GRATIS !!!<br/><br/>");
	$link_gratis2 = new Link($url,"<br/><br/>Descarga GRATIS !!!<br/>");
}else{
/*
	$texto1  = new MensajeDescarga($url,$volver_link, $datos['tipo'], $idGlobalnet);
	$texto2 = new MensajeDescarga2($url,$volver_link, $datos['tipo'], $idGlobalnet);
*/
	$texto2  = new MensajeDescarga($url,$url_no, $datos['tipo'], $idGlobalnet);
//var_dump($texto2);
}
$home_link = "./";
if ($_GET['b']=="g"){
$volver_link = "games.php?cat=$idCat&amp;step=1";
}else{
$volver_link ="./";
}

/////////////////////////////////////////////
//// PRESENTACION
/////////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL);

$seccion = new Seccion("Datos del Juego", "center", SMALL_FONT_SIZE);
if($nombre_juego) {
  $seccion->AddComponent("<b>$nombre_juego</b>","<br/>");
}
if($screen) {
  $seccion->AddComponent(new Imagen("getimage.php?path=".PREVIEW_HOST.$screen), "<br/>");
}
if($texto1) {
  $seccion->AddComponent($texto1);
}else{
  $seccion->AddComponent($link_gratis1);
}
if($descripcion) {
  $seccion->AddComponent($descripcion);
}
if($texto2) {
  $seccion->AddComponent("<br/><br/>",$texto2);
}else{
  $seccion->AddComponent($link_gratis2);
}

$pie = new Seccion("", "center", SMALL_FONT_SIZE,SECCION_SIN_TITULO);

if($volver_link) {
	$pie->AddComponent(new Link($volver_link, "<br/>".TEXT_VOLVER));
}

if($home_link) {
	$pie->AddComponent(new Link($home_link, "<br/>".TEXT_HOME."<br/>"));
}


$pagina->AddComponent($seccion);
$pagina->AddComponent($pie);

$pagina->WriteHeaders();
echo $pagina->Display();

?>
