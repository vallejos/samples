<?php
include_once("includes.php");

//------------------------------------
// Conexion con DB
//------------------------------------
$miC = new coneXion("Web", true);
$db = $miC->db;

///////////////////////////////////////////////////////7
/////       LOGICA
///////////////////////////////////////////////////////7

$type = (isset($_GET['tipoCat']))?$_GET['tipoCat']:62;
$xxx = (isset($_GET['xxx']))?$_GET['xxx']:0;
$pagina = (isset($_GET['p']))?$_GET['p']:0;
$paso = (isset($_GET['step']))?$_GET['step']:0;
$idCat = (isset($_GET['cat']))?$_GET['cat']:0;
$idCont = $_GET['id'];
$autor = $_GET['autor'];
$back = $_GET['b'];

switch($paso) {

	default: //el primer paso: mostramos las categorias

		$titulo = 'Categorias';

		$cant_por_pagina = 5;

		$cats = obtenerCategorias_mms($pagina,$cant_por_pagina,$type);

		$total = $cats['total'];
		unset($cats['total']);
		$lista = array();

		foreach($cats as $cat) {
			$lista[] = new Link("videos.php?cat={$cat['id']}&amp;xxx=$xxx&amp;tipoCat=$type&amp;step=1", $cat['descripcion']);
		}

		$paginado = new Paginado($cant_por_pagina, $pagina, $total, $_GET, "videos.php");
		$tipoLista = LISTA_NUMERADA_LINKS;
		$alineacion_seccion = "left";
		$volver_link = "index.php";
		$home_link = "index.php";
	break;
	 case 1: //el segundo paso: mostramos el contenido para una categoría

	    $titulo = obtenerNombreCat($idCat);
		$cant_p_pagina = 4;

		$conts = obtenerContenidosPorCat_mms($idCat, $type, $pagina,  $cant_p_pagina);

		$total = $conts['total'];
		unset($conts['total']);

		$lista = array();
		foreach($conts as $cont) {
			$id = $cont['id'];
			$nombre = $cont['nombre'];
			$preview = explode(".",$cont['referencia']);
			$imagen = $preview[0]."_p.".$preview[1];
			$nombre = utf8_encode ( str_replace ( "&", "&amp;", stripslashes ( $nombre ) ) );
        
            $url = urlDruttAncel($msisdn, $cont['id'], false, servicio3G(), NOMBRE_PORTAL_DESCARGA);

			
			$lista [] = new Link ( $url, $nombre, PATH_PREVIEW."/".$imagen, TOP_SIDE  );
		}
		$tipoLista = LISTA2X2_LINKS; //Seteamos el tipo de la lista de links, para que se muestren las imagenes de 2 en 2

		$volver_link = "videos.php";
		$home_link = "index.php";

		$paginado = new Paginado($cant_p_pagina, $pagina, $total, $_GET, "videos.php");
		$alineacion_seccion = "center";

	break;
	case 2: //el tercer paso: mostramos el mensaje con las opciones "SI" o "NO"
		header("Location: hacer_descarga.php?id=$idCont");
		die();
		$datos_cat = obtenerIdCat_mms($db,$idCont,OPERADORA_MCM, NOMBRE_MCM);
		$titulo = str_replace("&","&amp;",$datos_cat["autor"]);
		$idCat = $datos_cat["id"];
		switch($back){
			default:
				$volver_link="./";
			break;
			case 'c':
				$volver_link = "videos.php?p=$pagina&amp;cat=$idCat&amp;push=$nombre_wap&amp;tipoCat=$type&amp;step=1";
			break;
			case 'b':
				$volver_link = "buscador.php?tipo=$type&amp;step=1&amp;push=$push";
			break;
		}
		$no_url = "url_no.php?b=$back&amp;p=$pagina&amp;cat=$idCat&amp;tipoCat=$type&amp;id=$idCont";
		$texto = new MensajeDescarga("hacer_descarga.php?tipoCat=$type&amp;id=$idCont",$no_url , $type, $idCont);

		$texto->setWapName("catalina_wap");

		$home_link = "./";
		$alineacion_seccion = "center";

		$titulo = "Descarga Contenido";

	break;

}


///////////////////////////////////////////
/////    PRESENTACION
///////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL." - Videos");
//$nombreSeccion = ($type == 62)?"Videos":"Contenidos";

/*$pagina->AddComponent(new Banner($ua, "", "images/banner.gif"));*/

/*if($cw->drm != 1) {
	$pagina->AddComponent("Este contenido solo puede ser descargado en un celular con soporte DRM");
}
*/

$seccion = new Seccion($titulo, $alineacion_seccion, SMALL_FONT_SIZE);

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

if($paginado) { //Si hay paginado
	$seccion->AddComponent($paginado);
}

$pie = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);

/*$pie->AddComponent(new Link($descarga_link, "<br/>Seccion de descarga"));*/
if($volver_link) {
	$pie->AddComponent(new Link($volver_link, "<br/>Atrás"));
}

if($home_link) {
	$pie->AddComponent(new Link($home_link, "Inicio"));
}

$pagina->AddComponent($seccion);
$pagina->AddComponent($pie);

$pagina->WriteHeaders();
echo $pagina->Display();
?>
