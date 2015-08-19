<?php
include("includes.php");

$miC = new coneXion("Web", true);
$db = $miC->db;

$pagina = isset($_GET['p'])?$_GET['p']:0;
$idCat = isset($_GET['cat'])?$_GET['cat']:1;
$step = isset($_GET['step'])?$_GET['step']:0;

$text_align = "center";
$por_pagina = 4;

//$idCat = 1;

switch($step){
    case 0:
    case 1:
        $juegos = obtenerJuegosPorCat_mms($db, $ua, $idCat, $pagina, $por_pagina, OPERADORA_MCM,NOMBRE_MCM);
        $total = $juegos['total'];
        unset($juegos['total']);

        $lista = array();

        foreach($juegos as $item){
                if(soportaJuego($db, $ua, $item['id'])) {
			$href = "game_desc.php?id=".$item['id']."&amp;p=$pagina&amp;cat=$idCat&amp;p=$pagina";// . $isFree;
			$lista[] = new Link($href, $item['nombre'], PREVIEW_HOST.$item['screenshots'], TOP_SIDE);
		}
        }

        $paginado = new Paginado($por_pagina, $pagina, $total, $_GET, "games.php");
        $home_link = "home.php";
        $tipoLista = LISTA2X2_LINKS;
        $text_align = "center";
        break;
}

/////////////////////////////////////////////
//// PRESENTACION
/////////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL);


	$seccion = new Seccion("Juegos", "left", SMALL_FONT_SIZE);

	$listaLinks = new ListaLinks();
	$listaLinks->SetStyle($tipoLista);

	if($texto) {
		$seccion->AddComponent($texto);
	}

	if($lista) {
		$listaLinks->AddComponent($lista);
	}else{
		$no_soporta = "Lo sentimos. Su celular no soporta ninguno de nuestros contenidos.";
		$seccion->AddComponent($no_soporta);
	}

	$seccion->AddComponent($listaLinks);

	$pie = new Seccion("", "center", SMALL_FONT_SIZE,SECCION_SIN_TITULO);
	if($paginado) {
		$pie->AddComponent($paginado);
	}
	if($volver_link) {
		$pie->AddComponent(new Link($volver_link, "<br/>".TEXT_VOLVER));
	}

	if($home_link) {
		$pie->AddComponent(new Link($home_link, "<br/>".TEXT_HOME));
	}
$pagina->AddComponent($seccion);
$pagina->AddComponent($pie);

$pagina->WriteHeaders();
echo $pagina->Display();

?>