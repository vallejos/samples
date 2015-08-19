<?php
include_once("includes.php");

//------------------------------------
// Conexion con DB
//------------------------------------
$miC = new coneXion("Web", true);
$db = $miC->db;

$nombre_wap = isset($_GET["push"])?$_GET['push']:"";

///////////////////////////////////////////////////////7
/////       LOGICA
///////////////////////////////////////////////////////7

$type = isset($_GET['tipoCat'])?$_GET['tipoCat']:7;
$xxx = (isset($_GET['xxx']))?$_GET['xxx']:0;
$pagina = $_GET['p'];
$paginaCont = $_GET['pCont'];
$paso = (isset($_GET['step']))?$_GET['step']:0;
$idCat = (isset($_GET['cat']))?$_GET['cat']:0;
//$idCat = 1;
$idCont = $_GET['id'];
$back = $_GET['b']; //por si llegamos aca por otra pagina que no sea la normal

switch($paso) {

	default: //Obtenemos el contenido "destacado"
	
		$cant_por_pagina = 6;
		$cats = obtenerCategorias_mms ($pagina, $cant_por_pagina, $type);
		
		$lista = array();
		$total = $cats['total'];
		unset($cats['total']);
		
		foreach($cats as $cat) {
			$lista[] = new Link("images.php?pCat=$paginaCat&amp;cat=".$cat['id']."&amp;tipoCat=$type&amp;step=1&amp;autor=".str_replace("&", "&amp;", $cat['descripcion']), str_replace("&", "&amp;", $cat['descripcion']));
		}

		$tipoLista = LISTA_NUMERADA_LINKS;
		$titulo = "Imagenes";
		$alineado_texto = "left";
		$paginado = new Paginado($cant_por_pagina, $pagina, $total, $_GET, "images.php");
		$volver_link = "index.php";
		$home_link = "index.php";
	break;
	case 1: //el segundo paso: mostramos el contenido para una categoria

		$cant_img_pagina = 4;
		
		if($type==7){
			$titulo = "Wallpapers";
		}else{
			$titulo = "Screensavers";
		}
		
		$conts = obtenerContenidosPorCat_mms($idCat, $type, $pagina, $cant_img_pagina);
		$total = $conts['total'];
		unset($conts['total']);

		$lista = array();
		foreach($conts as $cont) {
			///--------------- CHANCHADAS A CONTINUACION
			$nombre = str_replace("128x128", "50x50", $cont['archivo']);
			//$nombre = str_replace(".gif", "_p.gif", $nombre);
			//$nombre = str_replace("101x80", "40x32", $nombre);
			$nombre = str_replace("/netuy", "", $nombre);
			if($type == 5) { //esto es una chanchada... todo por no guardar los archivos donde van1!!! ñia!!!
				$nombre = str_replace( "50x50", "40x32",$nombre);
			}
			//--------------- FIN DE LAS CHANCHADAS
			$nombreDesc = utf8_encode(str_replace("&", "&amp;", $cont['nombre']));
			$lista[] = new Link(urlDruttAncel($msisdn, $cont['id'], false, servicio3G(), NOMBRE_PORTAL_DESCARGA),$nombreDesc, "getimage.php?path=http://www.wazzup.com.uy/netuy/$nombre", TOP_SIDE);
//"images.php?b=c&amp;tipoCat=$type&amp;id=".$cont['id']."&amp;xxx=0&amp;step=2&amp;cat=$idCat","Descargar"
		}
		$tipoLista = LISTA2X2_LINKS; 
		$volver_link = "images.php?tipoCat=$type";
		$home_link = "index.php";
		$text_align = "center";
		
		$paginado = new Paginado($cant_img_pagina, $pagina, $total, $_GET, "images.php");
	
	break;
	case 2: //el tercer paso: mostramos el mensaje con las opciones "SI" o "NO"
		
		if($back=='c'){
			$volver_link="images.php?push=$nombre_wap&amp;tipoCat=$type&amp;cat=$idCat&amp;step=1";
		}else{
			$volver_link = "index.php";
		}
		
		$url = "hacer_descarga.php?tipoCat=$type&amp;id=".$idCont."&amp;b=i&amp;push=$nombre_wap";
		$no_url = "url_no.php?b=$back&amp;p=$pagina&amp;cat=$idCat&amp;tipoCat=$type&amp;id=$idCont";
		$texto = new MensajeDescarga($url, $no_url, $type, $idCont);
		$texto->forceSiUrl($url);
		$texto->setWapName("navidad_wap");
		$home_link = "index.php?push=$nombre_wap";
		$text_align = "center";
		
		$titulo = "Descarga Contenido";
	break;

}

///////////////////////////////////////////
/////    PRESENTACION
///////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL."  Imagenes");

if(count($destacados_images) > 0) {
	$masDescSec = new Seccion("+ Descargados", "left", SMALL_FONT_SIZE);
	$listaDesc = new ListaLinks($destacados_images);
	$listaDesc->SetStyle(LISTA2X2_LINKS);
	$masDescSec->AddComponent($listaDesc);
	$pagina->AddComponent($masDescSec);
}

$seccion = new Seccion($titulo, $alineado_texto, SMALL_FONT_SIZE);
if($texto) {
	$seccion->AddComponent($texto);
}

if($lista) { //Si hay una lista de links
	$listaLinks = new ListaLinks();
	$listaLinks->AddComponent($lista);
	if($tipoLista) {
		$listaLinks->SetStyle($tipoLista);
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
$codigo_generado = $pagina->Display();
if($pagina->_soportaXHTML()) {
	$codigo_generado = str_replace("$$", "$", $codigo_generado);	
}

echo $codigo_generado;

?>
