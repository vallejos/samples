<?php

include_once($globalIncludeDir."/includes.php");

$tipo = $_POST["tipo"];
$ids = $_POST["ids"];
$cats = $_POST["cats"];
$rango_i = $_POST["rango_i"];
$rango_f = $_POST["rango_f"];

$FTPProcess = "new"; // new o update


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

//include_once("index.php");


echo "<hr/>";
echo "<h3>2/2: Resultado...</h3>";


// ahora proceso
switch ($tipo) {
	case "PT":
		// polytones
		$catdrutt = $_POST["catdruttpt"];
		$prtdrutt = $_POST["prtpt"];
		include_once($globalIncludeDir."/process-polytone.php");
	break;
	case "RT":
		// realtones
		$catdrutt = $_POST["catdruttrt"];
		$prtdrutt = $_POST["prtrt"];
		include_once($globalIncludeDir."/process-realtone.php");
	break;
	case "VD":
		// videos
		$catdrutt = $_POST["catdruttvd"];
		$prtdrutt = $_POST["prtvd"];
		include_once($globalIncludeDir."/process-video.php");
	break;
	case "WP":
		// wallpapers
		$catdrutt = $_POST["catdruttwp"];
		$prtdrutt = $_POST["prtwp"];
		include_once($globalIncludeDir."/process-wallpaper2.php");
	break;
	case "SS":
		// screensavers
		$catdrutt = $_POST["catdruttss"];
		$prtdrutt = $_POST["prtss"];
		include_once($globalIncludeDir."/process-screensaver.php");
	break;
	case "FT":
		// fulltrack
		$catdrutt = $_POST["catdruttft"];
		$prtdrutt = $_POST["prtft"];
		include_once($globalIncludeDir."/process-fulltrack.php");
	break;
	case "TH":
		// themes
		$catdrutt = $_POST["catdruttth"];
		$prtdrutt = $_POST["prtth"];
		include_once($globalIncludeDir."/process-theme.php");
	break;
	case "JG":
		// juegos
		$catdrutt = $_POST["catdruttjg"];
		$prtdrutt = $_POST["prtjg"];
		include_once($globalIncludeDir."/process-game.php");
	break;
	default:
		echo "ERROR: Tipo desconocido";
}



?>
