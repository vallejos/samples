<?php
//include_once("includes.php"); //no se hace el include, porque esta página se incluye desde el "includes.php"
//------------------------------------
// Conexion con DB
//------------------------------------
$miC = new coneXion("Web", true);
$db = $miC->db;



///////////////////////////////////////////////////////
/////       LOGICA
///////////////////////////////////////////////////////

$pagina  = new Pagina(NOMBRE_PORTAL);
$seccion = new Seccion("Ha ocurrido un error", "center", SMALL_FONT_SIZE);

$texto = "Su equipo no se encuentra habilitado para descargas de contenidos.";

if($texto) {
	$seccion->AddComponent($texto);
}

$seccion->AddComponent(new Link("home.php", "<br/>Inicio"));

$pagina->AddComponent($seccion);

$pagina->WriteHeaders();
echo $pagina->Display();

?>