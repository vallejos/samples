<?php
include ("includes.php");

$miC = new coneXion ( "Web", true );
$db = $miC->db;

$nombre_wap = isset ( $_GET ["push"] ) ? $_GET ['push'] : "";
$menu = array ();
$soportaAlgo = false;
$soportaJuegos = false;

$total_juegos = 0;
$tipoJuego = 31;
$cantCats = 0;

if (soportaContenidoPorTipo ( $db, $ua, 31 )) {
	$top = new topJuegos($db,$ua,"game_desc.php");
	$top->start(0,OPERADORA_MCM,NOMBRE_MCM);
	$soportaJuegos = true;
	$soportaAlgo = true;
}
	
$cant_por_pagina = 10;
//$cats = obtenerCategorias_mms($db,0,$cant_por_pagina,31,OPERADORA_MCM,NOMBRE_MCM);
$cats = obtenerCategoriasJuegos_mms ( $db, $ua, 0, $cant_por_pagina, OPERADORA_MCM, NOMBRE_MCM );
/*echo "<pre>";
	print_r($cats);
	echo "</pre>";*/
$soportaAlgo = true;
$cantCats = $cats ['total'];
unset ( $cats ['total'] );

foreach ( $cats as $cat ) {
	$idCat = $cat ['id'];
	$menu [] = array ("href" => "games.php?b=c&amp;p=$pagina&amp;cat=$idCat&amp;push=$nombre_wap&amp;tipoCat=$tipoJuego&amp;id=" . $cat ['id'] . "&amp;step=1", "nombre" => utf8_encode ( str_replace ( "&", "&amp;", stripslashes ( $cat ['descripcion'] ) ) ) );
}


/////////////////////////////////////////////
//// PRESENTACION
/////////////////////////////////////////////


$pagina = new Pagina ( NOMBRE_PORTAL );


if($soportaJuegos){
	$pagina->AddComponent($top->showBoxes());
	$pagina->AddComponent($top->showLinks());
}


$pagina->AddComponent ( new Top ( $db, WEED, 1, $ua, 0 ) );
$pagina->AddComponent ( new Top ( $db, WEED, 2, $ua, 0 ) );
$pagina->AddComponent ( new Top ( $db, WEED, 3, $ua, 0 ) );
$pagina->AddComponent ( new Top ( $db, WEED, 4, $ua, 0 ) );
$pagina->AddComponent ( new Top ( $db, WEED, 5, $ua, 0 ) );
$pagina->AddComponent ( new Top ( $db, WEED, 6, $ua, 0 ) );
$pagina->AddComponent ( new Top ( $db, WEED, 7, $ua, 0 ) );

//Top Juegos//Top Juegos//Top Juegos

//Top Juegos//Top Juegos//Top Juegos


if ($soportaAlgo && ($cantCats > 0)) {
	$seccion = new Seccion ( "Categorias", "left", SMALL_FONT_SIZE );
	$listaLinks = new ListaLinks ( );
	$listaLinks->SetStyle ( LISTA_COLOR_LINKS );
	foreach ( $menu as $item ) {
		//$link = new MenuItem("images/bullet.gif",$item['nombre'],$item['href']);
		$link = new Link ( $item ['href'], $item ["nombre"] );
		$listaLinks->AddComponent ( $link );
	}
	$seccion->AddComponent ( $listaLinks );
} else {
	$seccion = new Seccion ( "", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO );
	$seccion->AddComponent ( TEXT_NO_MUESTRA_NADA );
}

$pagina->AddComponent ( $seccion );
/*$pie = new Seccion ( "", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO );
$pie->AddComponent ( new Link ( "ayuda.php", "<br/>Ayuda" ) );
$pie->AddComponent ( new Link ( "http://portalwap.ctimovil.com.ar:8582/pt/terminales/index.jsp?alias=ideas
", "<br/>volver a CLARO" ) );
$pagina->AddComponent ( new Banner ( $ua, "http://claro.wazzup.com.ar/fonik_wap/home.php", "images/fonik.gif" ) );
$pagina->AddComponent ( $pie );*/
$pagina->WriteHeaders ();
echo $pagina->Display ();
?>
