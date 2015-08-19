<?php

$ids = (isset($_POST["cids"])) ? $_POST["cids"] : $_GET["cids"];
$tipo = (isset($_POST["type"])) ? $_POST["type"] : $_GET["type"];

if (empty($ids)) die ("ERROR: ID's vacio");

switch ($tipo) {
	case "wallpapers":
		include_once("process_wallpapers.php");
		break;
	case "truetones":
		include_once("process_truetones.php");
		break;
	case "polifonicos":
		include_once("process_polifonicos.php");
		break;
	case "juegos":
		include_once("process_juegos.php");
		break;
	case "videos":
		include_once("process_videos.php");
		break;
	default:
		die ("ERROR: tipo no soportado");
}


?>