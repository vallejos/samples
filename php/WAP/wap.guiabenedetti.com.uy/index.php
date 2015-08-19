<?php

// includes y config
include_once("config.php");
include_once("includes.php");

// CONTROL DE ACCESO
/*
if ($_SERVER["REMOTE_ADDR"] !== "200.40.206.110") {
	$pagina = new Pagina(NOMBRE_PORTAL);
	$seccion = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
	$seccion->AddComponent("Coming Soon...<br/><br/>");
	$pagina->AddComponent($seccion);

	$pagina->WriteHeaders();
	die($pagina->Display());
}
*/


$id = trim($_GET["id"]);
$error = TRUE;
$dbc = new coneXion(DB_NAME, true);


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
	$pagina = new Pagina(NOMBRE_PORTAL);

//	$seccion = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
//	$seccion->AddComponent("ID OK: $id ($barrio => $punto)<br/><br/>");
//	$pagina->AddComponent($seccion);

	// primero veo si hay imagen a mostrar
	$tieneImagen = tieneImagen($dbc, $punto, $barrio);
	if (($tieneImagen !== FALSE) && ($tieneImagen > 0)) {
		$contsImg = obtenerImagen($dbc, $punto, $barrio);
		foreach($contsImg as $cont) {
			$seccion = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);

//			$img = str_replace(".jpg", "_gr.jpg", $cont["file"]);
//			$texto="<img src='$img' />";
//			$seccion->AddComponent($texto);
						
//			$pagina->AddComponent(new Banner($ua,"http://wap.wazzup.com.uy/reggaetondrutt_wap/home.php","images/reggaeton.gif"));
						
			$seccion->AddComponent(new Banner($ua,"#","uploads/".$cont["file"]));
			$pagina->AddComponent($seccion);
		}
	}

	// 2do: los textos
	$tieneTexto = tieneTexto($dbc, $punto, $barrio);
	if (($tieneTexto !== FALSE) && ($tieneTexto > 0)) {
		$contsTxt = obtenerTexto($dbc, $punto, $barrio);
		foreach($contsTxt as $cont) {
			$seccion = new Seccion("", "center", SMALL_FONT_SIZE, $cont["title"]);
			$texto=$cont["summary"]."<br/><br/>".$cont["body"];
			$seccion->AddComponent($texto);
			$pagina->AddComponent($seccion);
		}
	}

	// 3ro: links a audio? // TODO
	$tieneAudio = tieneAudio($dbc, $punto, $barrio);
	if (($tieneAudio !== FALSE) && ($tieneImagen > 0)) {
		// todo: como mostrar audio?
	}


	$pagina->WriteHeaders();
	echo $pagina->Display();
} else {
	$pagina = new Pagina(NOMBRE_PORTAL);
	$seccion = new Seccion("", "center", SMALL_FONT_SIZE, SECCION_SIN_TITULO);
	$seccion->AddComponent("ERROR: $errorMsg $id<br/><br/>");
	$pagina->AddComponent($seccion);

	$pagina->WriteHeaders();
	echo $pagina->Display();
}



?>