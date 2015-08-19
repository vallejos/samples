<?php
include("includes.php");

//------------------------------------
// Conexion con DB
//------------------------------------
$miC = new coneXion("Web", true);
$db = $miC->db;
$nombre_wap = $_GET["push"];

///////////////////////////////////////////////////////7
/////       LOGICA
///////////////////////////////////////////////////////7

$type = $_GET['tipoCat'];
$xxx = (isset($_GET['xxx']))?$_GET['xxx']:0;
$pagina = $_GET['p'];
$pagina2 = $_GET['p2'];
$paso = (isset($_GET['step']))?$_GET['step']:0;
$idCat = $_GET['cat'];
$idCont = $_GET['id'];
$back = $_GET['b']; //Por si llegamos desde otra p�gina que no sea la normal

switch($paso) {

	default: //el primer paso: mostramos las categorias
        $titulo='Categorias';
		
		$cant_por_pagina = 5;
		$cats = obtenerCategorias_mms($db,$pagina,$cant_por_pagina,$type,OPERADORA_MCM,NOMBRE_MCM);	
		$lista = array();
		$total = $cats['total'];
		unset($cats['total']);
		
		foreach($cats as $cat) {
			$nombre_cat = str_replace("&", "&amp;", $cat['descripcion']);
			$lista[] = new Link("ringtones.php?p=$pagina&amp;cat=".$cat['id']."&amp;push=$nombre_wap&amp;tipoCat=$type&amp;step=1",$nombre_cat);
		}

		$estilo_lista = LISTA_NUMERADA_LINKS;
		$alineado_texto = "left";
		$paginado = new Paginado($cant_por_pagina, $pagina, $total, $_GET, "ringtones.php");
		$volver_link = "index.php?push=$nombre_wap";
		$home_link = "index.php?push=$nombre_wap";
	break;
	case 1: //el segundo paso: mostramos el contenido para una categor�a
		
		
		$titulo = "Mp3"; 
		$items_por_pagina = 6;
	
		$conts = obtenerContenidosPorCat_mms($idCat, $type, $pagina2, $items_por_pagina);
		
		$total = $conts['total'];
		unset($conts['total']);
		
		$lista = array();
		foreach($conts as $cont) {
    		$lista[] = new Link(urlDruttAncel($msisdn, $cont['id'], false, servicio3G(), NOMBRE_PORTAL_DESCARGA), utf8_encode(str_replace("&", "&amp;", stripslashes($cont['nombre'] ))));
			$idCont = $cont['id'];
		}
	//chanchada, lo se... lo hace con el ultimo idCont pq da igual pq son todos de la misma cat
		
		$estilo_lista = LISTA_NUMERADA_LINKS;
		$alineado_texto = "left";

		$volver_link = "index.php?push=$nombre_wap";
		$home_link = "index.php?push=$nombre_wap";
		
		$paginado = new Paginado($items_por_pagina, $pagina2, $total, $_GET, "ringtones.php","p2");

	break;
	case 2: //el tercer paso: mostramos el mensaje con las opciones "SI" o "NO"
	
		$datos_cat = obtenerIdCat_mms($db,$idCont,OPERADORA_MCM, NOMBRE_MCM);
		$titulo = $datos_cat["autor"];
		$idCat = $datos_cat["id"];
		
		if($back=='c'){
			$volver_link = "ringtones.php?p=$pagina&amp;cat=$idCat&amp;push=$nombre_wap&amp;tipoCat=$type&amp;step=1";
		}else{
			$volver_link = "index.php";
		}
			
		$url = "hacer_descarga.php?tipoCat=$type&amp;push=$nombre_wap&amp;id=$idCont";
		$no_url = "url_no.php?b=$back&amp;p=$pagina&amp;cat=$idCat&amp;tipoCat=$type&amp;id=$idCont";
		$texto = new MensajeDescarga($url, $no_url, $type, $idCont);
		$texto->setWapName("halloween_wap");
		$texto->forceSiUrl($url);
		$home_link = "index.php";
		$alineado_texto = "center";
	
		$titulo="Descarga Contenido";	
	break;

}

///////////////////////////////////////////
/////    PRESENTACION
///////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL);

$seccion = new Seccion($titulo, $alineado_texto, SMALL_FONT_SIZE);

if($texto) {
	$seccion->AddComponent($texto);
}

if($lista) { //Si hay una lista de links
	$listaLinks = new ListaLinks();
	$listaLinks->AddComponent($lista);
	if($estilo_lista) {
		$listaLinks->SetStyle($estilo_lista);
	}
	$seccion->AddComponent($listaLinks);
}

$pie = new Seccion("", "center", NORMAL_FONT_SIZE, SECCION_SIN_TITULO);

if($paginado) { //Si hay paginado
	$pie->AddComponent($paginado);
}
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