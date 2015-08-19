<?php
include("includes.php");

$miC = new coneXion("Web", true);
$db = $miC->db;

logearAcceso($db, $celular, $ua, NOMBRE_PORTAL_DESCARGA, $_SERVER['HTTP_REFERER']);

$nombre_wap = isset($_GET["push"])?$_GET['push']:NOMBRE_PORTAL_DESCARGA;
$menu = array();

$soportaAlgo = false;

if(soportaContenidoPorTipo($db, $ua, 23)) {
  $soportaAlgo = true;
  $cant_por_pagina = 5;
  $cats = obtenerCategorias_mms($pagina,9,23,false);
  unset($cats['total']);
  foreach($cats as $cat) {
    $nombre_cat = str_replace("&", "&amp;", $cat['descripcion']);
    $menu[] = new Link("ringtones.php?cat=".$cat['id']."&amp;push=$nombre_wap&amp;tipoCat=23&amp;step=1",$nombre_cat);
  }
  //$menu[] = array("href" => "ringtones.php?push=$nombre_wap&amp;tipoCat=23&amp;step=1&amp;cat=3", "nombre" => "Truetones");
}



/////////////////////////////////////////////
//// PRESENTACION
/////////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL);

$seccion = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
$seccion->AddComponent("CDs de regalo a los que más descarguen!");
$pagina->AddComponent($seccion);


$pagina->AddComponent(new Top($db, WEED, 1, $ua, 0));
$pagina->AddComponent(new Top($db, WEED, 2, $ua, 0));
$pagina->AddComponent(new Top($db, WEED, 3, $ua, 0));
$pagina->AddComponent(new Top($db, WEED, 4, $ua, 0));
$pagina->AddComponent(new Top($db, WEED, 5, $ua, 0));
$pagina->AddComponent(new Top($db, WEED, 6, $ua, 0));
$pagina->AddComponent(new Top($db, WEED, 7, $ua, 0));

if($soportaAlgo) {
	$seccion = new Seccion("Menu", "left", SMALL_FONT_SIZE);
/*
	$listaLinks = new ListaLinks();
	$listaLinks->SetStyle(LISTA_NUMERADA_LINKS);
	foreach($menu as $item){
		$link = new MenuItem("images/bullet.gif",$item['nombre'],$item['href']);
		$listaLinks->AddComponent($link);
	}
	$seccion->AddComponent($listaLinks);
*/
	if($menu) { //Si hay una lista de links
	  $listaLinks = new ListaLinks();
	  $listaLinks->AddComponent($menu);
	  $listaLinks->SetStyle(LISTA_NUMERADA_LINKS);
	  $seccion->AddComponent($listaLinks);
	}

} else {
	$seccion = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
	$seccion->AddComponent(TEXT_NO_MUESTRA_NADA);
}

$pagina->AddComponent($seccion);

$pie = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
$pie->AddComponent(new Link("http://wap.wazzup.com.uy/halloweenp_wap/home.php","Volver a Postales"));
//$pagina->AddComponent($pie);

$pagina->WriteHeaders();
echo $pagina->Display();

?>
