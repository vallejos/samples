<?php
	include("includes.php");

	$miC = new coneXion("Web", true);
	$db = $miC->db;

	$nombre_wap=$_GET['push'];
	if (isset($_GET["push"])) {
		$sess = new wapSession($db,$celular,$ua);
		if (!empty($_GET["push"])) $sess->register("push", $_GET["push"]);
		else $sess->destroy();
	} else {
		$sess = new wapSession($db,$celular,$ua);
		$sess->destroy();
	}
	$nombre_wap = isset($_GET["push"])?$_GET['push']:"";


	$pagina = new Pagina(NOMBRE_PORTAL);
	$cuerpo = new Seccion("", "center", SMALL_FONT_SIZE,SECCION_SIN_TITULO);
	$cuerpo->AddComponent("Por reclamos llamar al 0800-345-9987");
	$pie = new Seccion("", "center", SMALL_FONT_SIZE,SECCION_SIN_TITULO);
	$pie->AddComponent(new Link("home.php", TEXT_HOME));
	$pagina->AddComponent($cuerpo);
	$pagina->AddComponent($pie);
	$pagina->WriteHeaders();
	echo $pagina->Display();

?>
