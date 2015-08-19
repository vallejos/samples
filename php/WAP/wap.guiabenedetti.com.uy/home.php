<?php 

// includes y config
include_once("config.php");
include_once("includes.php");

$id = trim($_GET["id"]);
$error = TRUE;
$dbc = new coneXion(DB_NAME, true);

$title = NOMBRE_PORTAL;

if (!empty($id)) {
	// comienzo
	$barrio = substr($id, 0, 2);
	$puntoSize = strlen($id) - strlen($barrio); // should be sizeof($id) -2;
	$punto = substr($id, -$puntoSize);
	if (!isValidPunto($punto, $barrio)) $errorMsg = "ID is invalid";
	else $error = FALSE;
} else {
	$errorMsg = "could not get id";
}


if ($error !== TRUE) {
	$header = NOMBRE_PORTAL;

	// primero veo si hay imagen a mostrar
	$tieneImagen = tieneImagen($dbc, $punto, $barrio);
	if (($tieneImagen !== FALSE) && ($tieneImagen > 0)) {
		$contsImg = obtenerImagen($dbc, $punto, $barrio);

		// new: nuevas medidas de pantalla para wap
		if (defined("USE_EXTENDED_LAYOUT") && (USE_EXTENDED_LAYOUT===TRUE)) {
			$tipo_cabezal = "_".maxAnchoSoportado($dbc->db, $ua).".gif";
		} else if (defined("USE_EXTENDED_LAYOUT_BETA") && (USE_EXTENDED_LAYOUT_BETA===TRUE)) {
			$tipo_cabezal = "_".maxExtendedLayout($dbc->db, $ua).".gif";
		} else {
			$tipo_cabezal = "_gr.gif";
		}

		foreach($contsImg as $cont) {
			$img = str_replace(".gif", "$tipo_cabezal", $cont["file"]);
			$content = "<img src='uploads/$img' />";
							
		}
	}

	// 2do: los textos
	$tieneTexto = tieneTexto($dbc, $punto, $barrio);
	if (($tieneTexto !== FALSE) && ($tieneTexto > 0)) {
		$contsTxt = obtenerTexto($dbc, $punto, $barrio);
		foreach($contsTxt as $cont) {
			// cabezal
			$img = str_replace(".gif", "$tipo_cabezal", "cabezal.gif");
			$header = "<img src='images/$img' />";	

			$content .= "<br/>".$cont["summary"]."<br/><br/>".$cont["body"];
		}
	}

	// 3ro: links a audio? // TODO
	$tieneAudio = tieneAudio($dbc, $punto, $barrio);
	if (($tieneAudio !== FALSE) && ($tieneImagen > 0)) {
		// todo: como mostrar audio?
	}

	// logo antel
	$content .= "<br/><br/><br/><img src='images/logo_ancel.gif' />";	
	
	$footer = "";

} else {
	$header = "ERROR";
	$content = "$errorMsg $id<br/><br/>";
	$footer = "";

}




function maxAnchoSoportado($db, $ua) {
	$EXTENDED_LAYOUT_SETUP = array(460,300,165,226,116,92,80,50); // las nuevas medidas a utilizar
	
	$soportado = FALSE;
	$cw = new CelularWurfl($db, $ua);
	arsort($EXTENDED_LAYOUT_SETUP);
	$i=0; $cantMedidas = sizeof($EXTENDED_LAYOUT_SETUP);
	$maxAncho = $anchos[$cantMedidas - 1]; // just use the smallest one by default

	while ($soportado !== TRUE) {
		$soportado = ($cw->pantalla_ancho > $EXTENDED_LAYOUT_SETUP[$i]);
		if ($soportado === TRUE) {
			$maxAncho = $EXTENDED_LAYOUT_SETUP[$i];
		} else {
			// just in case we find a device that doesn't support anything :S
			if ($i > $cantMedidas) $soportado = TRUE;
		}
		$i++;
	}
	return $maxAncho;
}

// version 2 de las medidas extendidas!!
function maxExtendedLayout($db, $ua) {
	$EXTENDED_LAYOUT_SETUP = array(360,240,160,128,96); // las nuevas medidas a utilizar
	
	$soportado = FALSE;
	$cw = new CelularWurfl($db, $ua);
	arsort($EXTENDED_LAYOUT_SETUP);
	$i=0; $cantMedidas = sizeof($EXTENDED_LAYOUT_SETUP);
	$maxAncho = $anchos[$cantMedidas - 1]; // just use the smallest one by default

	while ($soportado !== TRUE) {
		$soportado = ($cw->pantalla_ancho > $EXTENDED_LAYOUT_SETUP[$i]);
		if ($soportado === TRUE) {
			$maxAncho = $EXTENDED_LAYOUT_SETUP[$i];
		} else {
			// just in case we find a device that doesn't support anything :S
			if ($i > $cantMedidas) $soportado = TRUE;
		}
		$i++;
	}
	return $maxAncho;
}


?>
<!DOCTYPE html>
<html>
<head>
<title><?=$title;?></title>
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<link rel="stylesheet" href="jquery.mobile-1.0b2.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.5.min.js"></script>
<script type="text/javascript" src="jquery.mobile-1.0b2.min.js"></script>
</head>
<body>
<div data-role="page" id="home">
 <div data-role="header" data-theme="k">
  <h1><?=$header;?></h1>
 </div>
 <div data-role="content" data-theme="k">
  <p><?=$content;?></p>
 </div>
 <div data-role="footer" data-theme="k">
  <h4><?=$footer;?></h4>
 </div>
</div>
</body>
</html>