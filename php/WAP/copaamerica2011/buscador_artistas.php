<?php
include("includes.php");

//------------------------------------
// Conexion con DB
//------------------------------------
$miC = new coneXion("Web", true);
$cw = new CelularWurfl($miC->db, $ua);
$miC->cambiarDB("Web");
$db = $miC->db;

$nombre_wap = isset($_GET['push'])?$_GET['push']:"";
$pagina = $_GET['p'];
$busqueda = isset($_GET['a'])?$_GET['a']:"";
$paso = isset($_GET['step'])?$_GET['step']:0;
$tipo = $_GET['tipo'];
$tipo_bus = $_GET['tipo_bus'];

///////////////////////////////////////////////////////7
/////       LOGICA
///////////////////////////////////////////////////////7

switch($paso) {
	case 0:
		$home_link = "home.php?push=$nombre_wap";		
	break;
	case 1:
	
	if($tipo==7){
		$items_por_pagina = 4;
	}else{
		$items_por_pagina = 6;
	}
	
	if($tipo_bus=='a'){
		$resultados = buscarContenidosPorArtistas_mcm($db,$pagina,$items_por_pagina,$tipo,$busqueda,OPERADORA_MCM,NOMBRE_MCM);	
	}else{
		$resultados = buscarContenidos_mcm($db,$pagina,$items_por_pagina,$busqueda,$tipo,$only_cont,OPERADORA_MCM,NOMBRE_MCM);
	}

	$total = $resultados['total'];
	
	$paginado = new Paginado($items_por_pagina, $pagina, $total, $_GET, "buscador_artistas.php");
	
	$lista = array();
			
	$cont=0;
	
	foreach($resultados as $res) {
		$cont+=1;
		if($cont > 1){
		$id = $res['id'];
			$nombre = $res['autor']." - ".$res['nombre'];
		$idCat = $res['idCat'];
		
		switch ($tipo){
			case 7:
				$datos = obtenerDatosContenido($db, $id);
				$url = "images.php";				
				$lista[] = new Link($url."?push=$nombre_wap&amp;b=ba&amp;step=2&amp;cat=$idCat&amp;tipoCat=".$tipo."&amp;id=".$id, utf8_encode(str_replace("&", "&amp;", stripslashes($nombre))), PATH_PREVIEW."/".$datos['screenshots'], TOP_SIDE);
			break;
			case 23:
			case 29:
				$url = "ringtones.php";
				$lista[] = new Link($url."?push=$nombre_wap&amp;b=ba&amp;step=2&amp;cat=$idCat&amp;tipoCat=".$tipo."&amp;id=".$id, utf8_encode(str_replace("&", "&amp;", stripslashes($nombre))));
			break;
					
			case 62:
				$url = "videos.php";
				$lista[] = new Link($url."?push=$nombre_wap&amp;b=ba&amp;step=2&amp;cat=$idCat&amp;tipoCat=".$tipo."&amp;id=".$id, utf8_encode(str_replace("&", "&amp;", stripslashes($nombre))));
				break;
		}
		}
	}
	
	$num=1;

	if(count($resultados)  == $num) {
				$texto = "No se ha encontrado ningun resultado para tu busqueda, pero te ofrecemos las siguientes descargas.";
				$contenidos_fijos=true;
				
				switch ($tipo){
					case 23:
						$only_cont = array(14203,14376,14375);
					break;
					case 29:
						$only_cont = array(14691,14688,14689);
					break;
					case 62:
						$only_cont = array(14419,14424);
					break;	
				} 
				
				$resultados = buscarContenidos_mcm($db,$pagina,$items_por_pagina,$busqueda,$tipo,$only_cont,OPERADORA_MCM,NOMBRE_MCM);
					
				$cont=0;
				
				foreach ($resultados as $res) {
						$id = $res['id'];
						$nombre = $res['nombre'];
						$idCat = $res['idCat'];
				$cont++;
				if($cont<>1){
					switch ($tipo){
						case 7:
							$datos = obtenerDatosContenido($db, $id);
							$url = "images.php";
							$lista[] = new Link($url."?push=$nombre_wap&amp;b=ba&amp;step=2&amp;cat=$idCat&amp;tipoCat=".$tipo."&amp;id=".$id, utf8_encode(str_replace("&", "&amp;", stripslashes($nombre))), PATH_PREVIEW."/".$datos['screenshots'], TOP_SIDE);
						break;
						
						case 23:
						case 29:
							$url = "ringtones.php";
							$lista[] = new Link($url."?push=$nombre_wap&amp;b=ba&amp;step=2&amp;cat=$idCat&amp;tipoCat=".$tipo."&amp;id=".$id, utf8_encode(str_replace("&", "&amp;", stripslashes($nombre))));
						break;
						
						case 62:
							$url = "videos.php";
							$lista[] = new Link($url."?push=$nombre_wap&amp;b=ba&amp;step=2&amp;cat=$idCat&amp;tipoCat=".$tipo."&amp;id=".$id, utf8_encode(str_replace("&", "&amp;", stripslashes($nombre))));
						
						break;
					}
				  }
				}
			  }
	break;
}
$home_link = "home.php?push=$nombre_wap";
	
///////////////////////////////////////////
/////    PRESENTACION
///////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL);

//////////////////////////////////////////////////////
////////// BUSCADOR POR ARTISTAS
//////////////////////////////////////////////////////
	$seccion = new Seccion("Buscador", "center");
	$frmB = new Formulario("buscador_artistas.php", "Buscar");
	$frmB->addHidden("step", 1);
	$frmB->addHidden("push", $nombre_wap);
	
	$selTiposBus = new Select("", "tipo_bus");
	$selTiposBus->AddOption("a","Artistas");
	$selTiposBus->AddOption("t", "Tema");
	
	$selTipos = new Select("", "tipo");
	if(soportaContenidoPorTipo($db, $ua, 23)) {
		$selTipos->AddOption(23, "Truetones");
	}
	if(soportaContenidoPorTipo($db, $ua, 29)) {
		$selTipos->AddOption(29, "Polifonicos");
	}
	if(soportaContenidoPorTipo($db, $ua, 62)) {
		$selTipos->AddOption(62, "Videos");
	}
	$frmB->AddComponent($selTiposBus);
	$frmB->AddComponent(new Input("", "a"));
	$frmB->AddComponent($selTipos);
	$seccion->AddComponent($frmB);
//////////////////////////////////////////////////////
////////// BUSCADOR POR ARTISTAS
//////////////////////////////////////////////////////

if($menu) {
	$listaLinks = new ListaLinks();
	$listaLinks->SetStyle(LISTA_NUMERADA_LINKS);
	foreach($menu as $item) {
		$listaLinks->AddComponent(new Link($item['href'], $item['nombre']));
	}
	$seccion->AddComponent($listaLinks);
}

if(count($resultados) > 0) {
	if($contenidos_fijos){
		$titulo = 'Posibles Descargas';
	}else{
		$titulo='Resultados';
	}
	$seccionWalls = new Seccion($titulo, "left");
	$resultadosWalls = new ListaLinks($lista);
	if($tipo == 7) {
		$resultadosWalls->SetStyle(LISTA2X2_LINKS);
	} else {
		$resultadosWalls->SetStyle(LISTA_NUMERADA_LINKS);
	}
	
	if($texto) {
		$seccionWalls->AddComponent($texto);
	}
	
	$seccionWalls->AddComponent($resultadosWalls);
	if($paginado) { //Si hay paginado
		$seccionWalls->AddComponent($paginado);
	}
	$pagina->AddComponent($seccionWalls);
}

$pie = new Seccion("", "center", NORMAL_FONT_SIZE, SECCION_SIN_TITULO);
$volver_link = "home.php";

if($volver_link) {
	$pie->AddComponent(new Link($home_link, "<br/>".TEXT_VOLVER));
}
if($home_link) {
	$pie->AddComponent(new Link($home_link, "<br/>".TEXT_HOME));
}

$pagina->AddComponent($seccion);
$pagina->AddComponent($pie);

$pagina->WriteHeaders();
echo $pagina->Display();

?>