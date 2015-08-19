<?php
include("includes.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/../lib/rewrite_lib.php");

//------------------------------------
// Conexion con DB
//------------------------------------
$miC = new coneXion("tigoColombia", true);
$db = $miC->db;



///////////////////////////////////////////////////////7
/////       LOGICA
///////////////////////////////////////////////////////7

$type = $_GET['tipoCat'];
$xxx = (isset($_GET['xxx']))?$_GET['xxx']:0;
$pagina = $_GET['p'];
$paso = (isset($_GET['step']))?$_GET['step']:0;
$idCat = $_GET['cat'];
$idCont = $_GET['id'];
$lista = array();



$sql = "SELECT descarga, tipo, nombre, pk_ventas_wap_billing
	FROM ventas_wap_billing WB INNER JOIN Web.contenidos C ON C.id = WB.descarga
	WHERE celular = '$msisdn'
	AND useragent = '$ua'
	AND reintentos < 3
	AND cobrado = 1
    AND WB.tipo_venta = 'contenido'
	AND CONCAT(fecha, ' ', hora) >= DATE_ADD(NOW(), INTERVAL -24 HOUR)
	ORDER BY fecha, hora DESC";

if(!$rs = mysql_query($sql, $db)) {
	echo "Error en la consulta::$sql::".mysql_error($db);
}


$contenidos = array();
while($row = mysql_fetch_assoc($rs)) {

	switch(intval($row['tipo'])){
            case 7:
                $nombre_tipo = "Wallpaper";
		$volver_link = "images.php?step=0&amp;tipoCat=7";
            break;
            case 5:
                $nombre_tipo = "Screensaver";
		$volver_link = "images.php?tipoCat=5&amp;cat=77&amp;step=1";
            break;

            case 63:
                $nombre_tipo = "Theme";
		$volver_link = "themes.php";
            break;
            case 23:
                $nombre_tipo = "Mp3";
		$volver_link = "ringtones.php?tipoCat=23&amp;step=0";
            break;
            case 29:
                $nombre_tipo = "Polifonico";
		$volver_link = "ringtones.php?tipoCat=29&amp;step=0";
            break;
            case 62:
                $nombre_tipo = "Video";
		$volver_link = "videos.php?cat=274&amp;step=1";
            break;
            case 31:
            case 35:
            case 57:
            case 59:
            case 61:
                $nombre_tipo = "Juego";
		$volver_link = "games.php?step=0";
            break;
        }


	$url_desc = urlGenerica($msisdn, $row['descarga'], $row['pk_ventas_wap_billing'], "tigo_co_wap");

	$lista[] = array("href" => str_replace("&", "&amp;",$url_desc), "nombre" => str_replace("&", "&amp;", $row['nombre']));
}


$texto = "Descarga el contenido que tengas pendiente:<br/>";

///////////////////////////////////////////
/////    PRESENTACION
///////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL." - Descarga tu contenido");
$seccion = new Seccion("Descargas", "center", SMALL_FONT_SIZE);

if($pagina->_soportaXHTML()) {
	$texto = str_replace("$$", "$", $texto);	
}
if($texto) {
	$seccion->AddComponent($texto);
}

$listaLinks = new ListaLinks();
//$listaLinks->SetStyle(LISTA_NUMERADA_LINKS);
foreach($lista as $item) {
	$listaLinks->AddComponent(new Link($item['href'], $item['nombre']));
}


$seccion->AddComponent($listaLinks);
$seccion->AddComponent(new Link("home.php", "<br/>Inicio"));

$pie = new Seccion("", "center", NORMAL_FONT_SIZE, SECCION_SIN_TITULO);
$pagina->AddComponent($seccion);

$pagina->WriteHeaders();
echo $pagina->Display();


?>
