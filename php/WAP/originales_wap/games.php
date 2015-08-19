<?php 
include("includes.php");


$miC = new coneXion("Web", true);
$db = $miC->db;

$step = isset($_GET['step'])?$_GET['step']:0;
$pagina = isset($_GET['p'])?$_GET['p']:0;
$idCat = isset($_GET['cat'])?$_GET['cat']:0;
$listaTops = array();
$type = $_GET['tipoCat'];
$idCont = $_GET['id'];
$tipo_juegos = $_GET['tipo_juegos'];
$back = $_GET['b'];

switch($step) {
	
	case 1:
		$titulo="Juegos";
		
		$text_align = "center";
		$por_pagina = 4;
		
		$juegos=obtenerJuegosPorCat_mms($db, $ua, $idCat, $pagina, $por_pagina, OPERADORA_MCM,NOMBRE_MCM);	
		
		$total = $juegos['total'];
		unset($juegos['total']);
		
		
		if(count($juegos) == 0) {
			$texto = "Lo sentimos, pero su celular no soporta ninguno de los juegos del portal";
		}
		$lista = array();
		foreach($juegos as $item){
			$href = "game_desc.php?b=c&amp;step=1&amp;cat=$idCat&amp;p=$pagina&amp;id=".$item['id'];
			$lista[] = new Link($href, $item['nombre'], "getimage.php?path=".PREVIEW_HOST.$item['screenshots'], TOP_SIDE);
		}
		$paginado = new Paginado($por_pagina, $pagina, $total, $_GET, "games.php");
		$volver_link = "home.php";
		$home_link = "home.php";
		
		$tipoLista = LISTA2X2_LINKS;
	break;
	case 2: //el tercer paso: mostramos el mensaje con las opciones "SI" o "NO"
	
	$datos_cat = obtenerIdCat_mms($db,$idCont,OPERADORA_MCM, NOMBRE_MCM);
	$titulo = $datos_cat["autor"];
	$idCat = $datos_cat["id"];
	
	$titulo="Descarga Contenido";
	$text_align = "center";
	
	$url="hacer_descarga.php?tipoCat=$type&amp;id=$idCont&amp;gratis=0";
	$url_no = "url_no.php?b=$back&amp;p=$pagina&amp;cat=$idCat&amp;tipoCat=$type&amp;id=$idCont";
	$texto = new MensajeDescargaAncel($url,$url_no,"home.php", $type, $idCont);
	$texto->forceSiUrl($url);
	$texto->setWapName("halloween_wap");
	
	$volver_link = "games.php?p=$pagina&amp;step=1&amp;cat=$idCat";
	$home_link = "home.php";
	
	break;
}


/////////////////////////////////////////////
//// PRESENTACION
/////////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL);


if(count($tops) > 0) {
	$seccionTop = new Seccion("Top Juegos", "center", SMALL_FONT_SIZE);
	$listaTop = new ListaLinks($tops);
	$listaTop->SetStyle(LISTA2X2_LINKS);
	$seccionTop->AddComponent($listaTop);
	$pagina->AddComponent($seccionTop);
}

$seccion = new Seccion($titulo, $text_align, SMALL_FONT_SIZE);
$listaLinks = new ListaLinks();
$listaLinks->SetStyle($tipoLista);

if($texto) {
	$seccion->AddComponent($texto);
}

if($lista) {
	$listaLinks->AddComponent($lista);
}

$seccion->AddComponent($listaLinks);
if($paginado) {
	$seccion->AddComponent($paginado);
}

$pie = new Seccion("", "center", SMALL_FONT_SIZE,SECCION_SIN_TITULO);
if($volver_link) {
	$pie->AddComponent(new Link($volver_link, "<br/>".TEXT_VOLVER));
}

if($home_link) {
	$pie->AddComponent(new Link($home_link, TEXT_HOME));
}

$pagina->AddComponent($seccion);
$pagina->AddComponent($pie);

$pagina->WriteHeaders();
echo $pagina->Display();

?>
