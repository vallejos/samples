<?php

include_once($globalIncludeDir."/includes.php");

$tipo = $_POST["tipo"];
$ids = $_POST["ids"];
$cats = $_POST['cats'];
$rango_i = $_POST["rango_i"];
$rango_f = $_POST["rango_f"];

$workingTipoCarga = $_POST['workingTipoCarga'];

$categorias_seleccionadas = explode(",",$_POST["categorias"]);



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
  case "midlet":
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
                $isPoly = true;
		include_once($globalIncludeDir."/process-realtone.php");
	break;
	case "RT":
		// realtones
		$catmig = $_POST["catmigrt"];
		$subcatmig = $_POST["subcatmigrt"];
		include_once($globalIncludeDir."/process-realtone.php");
	break;
	case "VD":
		// videos
		$catmig = $_POST["catmigvd"];
		$subcatmig = $_POST["subcatmigvd"];
		include_once($globalIncludeDir."/process-video.php");
	break;
	case "WP":
		// wallpapers
		$catmig = $_POST["catmigwp"];
		$subcatmig = $_POST["subcatmigwp"];
		include_once($globalIncludeDir."/process-wallpaper.php");
	break;
	case "SS":
		// screensavers
		$catmig = $_POST["catmigss"];
		$subcatmig = $_POST["subcatmigss"];
		include_once($globalIncludeDir."/process-screensaver.php");
	break;
	case "FT":
		// fulltrack
		$catmig = $_POST["catmigft"];
		$subcatmig = $_POST["subcatmigft"];
		include_once($globalIncludeDir."/process-fulltrack.php");
	break;
	case "TH":
		// themes
		$catmig = $_POST["catmigth"];
		$subcatmig = $_POST["subcatmigth"];
		include_once($globalIncludeDir."/process-theme.php");
	break;
	case "midlet":
		// juegos
		include_once($globalIncludeDir."/process-game.php");
	break;
	default:
		echo "ERROR: Tipo desconocido";
}
?>