<?php
//include_once("includes.php"); //no se hace el include, porque esta p�gina se incluye desde el "includes.php"
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

$texto = "Este portal no est&#225; disponible para su m&#243;vil";

if($texto) {
	$seccion->AddComponent($texto);
}

$seccion->AddComponent(new Link("home.php", "<br/>".TEXT_HOME));

$pagina->AddComponent($seccion);

$pagina->WriteHeaders();
echo $pagina->Display();

?>