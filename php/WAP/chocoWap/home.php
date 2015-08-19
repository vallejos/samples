<?php
include("includes.php");

//------------------------------------
// Conexion con DB
//------------------------------------
$miC = new coneXion("Web", true);
$db = $miC->db;

$soportaAlgo = false;

if(soportaContenidoPorTipo($db, $ua, 31)) {
	$soportaAlgo = true;
}

//Top Juegos//Top Juegos//Top Juegos
if(soportaContenidoPorTipo($db, $ua, "31")) {
	$juegos = obtenerTopDeJuegos_mms($db,$ua,0,0,OPERADORA_MCM,NOMBRE_MCM);
	$cantCajas = $juegos['cantCajas'];
	unset($juegos['cantCajas']);
	$lista_j = array();
	$cont=0;
	foreach($juegos as $item){
		$href = "game_desc.php?id=".$item['id'];
		if($cont<$cantCajas){
			$link = new Link($href, $item['nombre'], PREVIEW_HOST."/netuy/java/cajas/".$item['id'].".gif", TOP_SIDE);
			switch($item['attr']){
				case "hot":
					$link->setHot(true);
				break;
				case "hit":
					$link->setHit(true);
				break;
				case "new":
					$link->setNew(true);
				break;
			}
			$lista_j[] = $link;
		}else{
			$menuItem = new MenuItem("images/bullet.gif",$item['nombre'],$href);
			switch($item['attr']){
				case "hot":
					$menuItem->setHot(true);
				break;
				case "hit":
					$menuItem->setHit(true);
				break;
				case "new":
					$menuItem->setNew(true);
				break;
			}
			$lista_js[] = $menuItem;
		}
		$cont++;
	}


	$cats  = obtenerCategoriasJuegos_mms($db, $ua, 0,50,OPERADORA_MCM,NOMBRE_MCM);
	$total = $cats['total'];
	unset($cats['total']);

	$lista_cats = array();
	foreach ($cats as $c) {
		$lista_cats[] = new Link("games.php?push=$nombre_wap&amp;titulo=".$c['descripcion']."&amp;cat=".$c['id']."&amp;step=1", $c['descripcion']);
	}

}
//Top Juegos//Top Juegos//Top Juegos

///////////////////////////////////////////
/////    PRESENTACION
///////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL);

//$cant = cantDescargasGratis($db,$celular);
// $gratis =  obtenerGratis($ua,$db,$celular);
// if ($gratis !== "false"){
// $secFree = new Seccion("$gratis descarga(s) gratis", $text_align, SMALL_FONT_SIZE);
// $pagina->AddComponent($secFree);
// }

// $msg_promo= new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
// $msg_promo->AddComponent("Por la compra de un juego  te obsequiamos el  otro GRATIS.<br/>
// 	Despues de descargar el primer juego Regresa a esta página luego de que
// 	hagas la compra y encontraras tu juego gratis");
// if($gratis !== "false"){
// 	$msg_promo->AddComponent(new Banner($ua,"games.php?cat=1&amp;step=1","images/gratis_banner.gif"));
// }
// $pagina->AddComponent($msg_promo);

//Top Juegos//Top Juegos//Top Juegos
if($lista_j) {
	$seccion = new Seccion("Top Juegos","center",SMALL_FONT_SIZE);
	$listaLinks = new ListaLinks();
	$listaLinks->SetStyle(LISTA_COLOR_LINKS);
	$listaLinks->AddComponent($lista_j);
	$seccion->AddComponent($listaLinks);
	$pagina->AddComponent($seccion);
	$ver_todos = true;
}
if($lista_js) {

	$seccion = new Seccion("","center",SMALL_FONT_SIZE,SECCION_SIN_TITULO);
	$listaLinks = new ListaLinks();
	//$listaLinks->SetStyle(LISTA_COLOR_LINKS);
	$listaLinks->AddComponent($lista_js);
	$seccion->AddComponent($listaLinks);
	$pagina->AddComponent($seccion);
	$ver_todos = true;
}
//Top Juegos//Top Juegos//Top Juegos
if($ver_todos){
	$seccion_ver = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
	$ver_todos = new Link("games.php", "ver todos");
	$seccion_ver->AddComponent($ver_todos);
	//$pagina->AddComponent($seccion_ver);
}


if($lista_cats) {
	$seccion = new Seccion("Categorias", "left", SMALL_FONT_SIZE);
	$listaLinks = new ListaLinks();
	$listaLinks->SetStyle(LISTA_NUMERADA_LINKS);
	$listaLinks->AddComponent($lista_cats);
	$seccion->AddComponent($listaLinks);
	$pagina->AddComponent($seccion);
}


//$pagina->AddComponent($seccion);

if(!$soportaAlgo || (sizeof($lista_j)==0 && sizeof($lista_js)==0)) {
	$seccion = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
	$seccion->AddComponent("Lo sentimos, su celular no soporta ninguno de los contenidos del portal");
	$pagina->AddComponent($seccion);
}
$pagina->AddComponent(new Banner($ua,"http://claro.wazzup.com.ar/fonik_wap/home.php","images/fonik.gif"));
//$pie = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
//$pie->AddComponent(new Link("descargar_contenido.php", "Descargas Pendientes"));
//$pie->AddComponent(new Link("http://wap.ola.com.co/wap2/portal.php", "<br/>Home Tigo"));
//$pagina->AddComponent($pie);

//MOSQUITO2_WAP ==== MOSQUITO_WAP
$pagina->WriteHeaders();
echo $pagina->Display();

?>
