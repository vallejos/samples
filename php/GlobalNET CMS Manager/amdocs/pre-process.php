<?php

include_once($globalIncludeDir."/includes.php");

$tipo = $_POST["tipo"];
$ids = $_POST["ids"];
$cats = $_POST["cats"];
$rango_i = $_POST["rango_i"];
$rango_f = $_POST["rango_f"];
$marca = $_POST["workingMarca"];
$festivo = $_POST["workingFestivo"];
$tipoCarga = $_POST["workingTipoCarga"];
$tipoVideo = $_POST["workingTipoVideo"];

$paises = $_POST["paises"];
$idiomas = $_POST["idiomas"];

$paises_elegidos = array();
$idiomas_elegidos = array();
foreach ($paises as $pais) {
	$sfCountryList[$pais] = "X";
        $paises_elegidos[$pais] = 1;
}
foreach ($idiomas as $idioma) {
        $idiomas_elegidos[$idioma] = 1;
}
$idiomas_elegidos["MX"] = 1; // haxor para setear MX siempre por defecto

$isPoly = FALSE;

//$keywords = array();
//$shortDesc = array();
//$longDesc = array();

/*
for($i = 1; $i <= 10; $i++){
    if(!empty($_POST["id_" . $i])){
        $listaIds[] = $_POST["id_" . $i];
       // $keywords[] = explode(" ", $_POST['keywords_' . $i]);
      //  $shortDesc[] = $_POST['shortDesc_' . $i];
       // $longDesc[] = $_POST['longDesc_' . $i];
    }
}
*/

// primero preparo los id's contenido a procesar
// en un array de id
// para todos los tipos es igual y la variable es la misma
if (!empty($ids)) $listaIds = explode(",", $ids);
if (!empty($rango_i) && !empty($rango_f) && $rango_f>=$rango_i) {
	// rango ok detectado, construyo array
	for ($i=$rango_i; $i<=$rango_f; $i++) {
		$listaIds[] = $i;
	}
}
if (!empty($cats)) {
	$sqlCats = "SELECT id FROM Web.contenidos WHERE categoria IN ($cats) AND tipo IN ($wzzpTypes) ";
	$rs = mysql_query($sqlCats, $dbc->db);
	while ($obj = mysql_fetch_object($rs)) {
		$listaIds[] = $obj->id;
	}
}


$catLvl = $_POST['catLvl'];
$webCat = $_POST['webCat_' . strtolower($tipo)];
$provider = $_POST['provider'];
$rating = $_POST['rating'];
$dbc = new conexion("Web");

switch ($tipo) {
  case "PT":
    $wzzpTypes = "29";
  break;
  case "RT":
    $wzzpTypes = "23";
  break;
  case "VD":
    $wzzpTypes = "62";
  break;
  case "SS":
    $wzzpTypes = "5";
  break;
  case "WP":
    $wzzpTypes = "7";
  break;
  case "FT":
    $wzzpTypes = "23";
  break;
  case "TH":
    $wzzpTypes = "63";
  break;
  case "JG":
    $wzzpTypes = "31";
  break;

}
echo "<hr/>";
echo "<h3>Resultado...</h3>";
// ahora proceso
switch ($tipo) {
	case "PT":
		// polytones
		$catmig = $_POST["catmigpt"];
		$subcatmig = $_POST["subcatmigpt"];
		$catmigen = $_POST["catmigpten"];
		$subcatmigen = $_POST["subcatmigpten"];
                $isPoly = true;
		include_once($globalIncludeDir."/process-realtone.php");
	break;
	case "RT":
		// realtones
		$catmig = $_POST["catmigrt"];
		$subcatmig = $_POST["subcatmigrt"];
		$catmigen = $_POST["catmigrten"];
		$subcatmigen = $_POST["subcatmigrten"];
		include_once($globalIncludeDir."/process-realtone.php");
	break;
	case "VD":
		// videos
		$catmig = $_POST["catmigvd"];
		$subcatmig = $_POST["subcatmigvd"];
		$catmigen = $_POST["catmigvden"];
		$subcatmigen = $_POST["subcatmigvden"];
		include_once($globalIncludeDir."/process-video.php");
	break;
	case "WP":
		// wallpapers
		$catmig = $_POST["catmigwp"];
		$subcatmig = $_POST["subcatmigwp"];
		$catmigen = $_POST["catmigwpen"];
		$subcatmigen = $_POST["subcatmigwpen"];
		include_once($globalIncludeDir."/process-wallpaper.php");
	break;
	case "SS":
		// screensavers
		$catmig = $_POST["catmigss"];
		$subcatmig = $_POST["subcatmigss"];
		$catmigen = $_POST["catmigssen"];
		$subcatmigen = $_POST["subcatmigssen"];
		include_once($globalIncludeDir."/process-screensaver.php");
	break;
	case "FT":
		// fulltrack
		$catmig = $_POST["catmigft"];
		$subcatmig = $_POST["subcatmigft"];
		$catmigen = $_POST["catmigften"];
		$subcatmigen = $_POST["subcatmigften"];
		include_once($globalIncludeDir."/process-fulltrack.php");
	break;
	case "TH":
		// themes
		$catmig = $_POST["catmigth"];
		$subcatmig = $_POST["subcatmigth"];
		$catmigen = $_POST["catmigthen"];
		$subcatmigen = $_POST["subcatmigthen"];
		include_once($globalIncludeDir."/process-theme.php");
	break;
	case "JG":
		// juegos
		$catmig = $_POST["catmigjg"];
		$subcatmig = $_POST["subcatmigjg"];
		$catmigen = $_POST["catmigjgen"];
		$subcatmigen = $_POST["subcatmigjgen"];
		include_once($globalIncludeDir."/process-game.php");
	break;
	default:
		echo "ERROR: Tipo desconocido";
}
?>