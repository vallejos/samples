<?php 
include("includes.php");

$miC = new coneXion("MCM", true);
$db = $miC->db;


$type = isset($_GET['tipoCat'])?$_GET['tipoCat']:0;
$idCat = $_GET['cat'];
$pagina = $_GET['p'];
$idCont = $_GET['id'];
$back = $_GET['b'];


switch ($type) {
	case 7:	
	case 5:
		//$volver_link = "images.php?b=$back&amp;cat=$idCat&amp;xxx=$xxx&amp;tipoCat=$type&amp;step=1&amp;p=$pagina";
		$volver_link = "images.php?b=$back&amp;tipoCat=$type&amp;id=$idCont&amp;step=2&amp;cat=$idCat";
	break;
	case 62:
		//$volver_link = "videos.php?cat=$idCat&amp;xxx=$xxx&amp;tipoCat=$type&amp;step=1&amp;p=$pagina";
		$volver_link = "videos.php?b=$back&amp;tipoCat=$type&amp;id=$idCont&amp;step=2&amp;cat=$idCat";
	break;
	case 63:
		//$volver_link = "themes.php?cat=$idCat&amp;xxx=$xxx&amp;tipoCat=$type&amp;step=1&amp;p=$pagina";
		$volver_link = "themes.php?b=$back&amp;tipoCat=$type&amp;id=$idCont&amp;step=2&amp;cat=$idCat";
	break;
	case 59:
	case 31:
		//$volver_link = "games.php?p=$pagina&amp;cat=$idCat&amp;tipoCat=$type&amp;step=1";
		$volver_link = "game_desc.php?b=$back&amp;tipoCat=$type&amp;id=$idCont&amp;step=1&amp;cat=$idCat&amp;p=$pagina";
	break;
	case 29:
	case 23:
		//$volver_link = "ringtones.php?p=$pagina&amp;artista=".urlencode($nombreArtista)."&amp;cat=$idCat&amp;tipoCat=$type&amp;step=1";
		$volver_link = "ringtones.php?b=$back&amp;tipoCat=$type&amp;id=$idCont&amp;step=2&amp;cat=$idCat";
	break;
	
	

}

$home_link = "./?push=$nombre_wap";

/////////////////////////////////////////////
//// PRESENTACION
/////////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL);


$seccion = new Seccion("Descarga", "center", SMALL_FONT_SIZE,SECCION_SIN_TITULO);
$seccion->AddComponent(TEXT_NO);


$pagina->AddComponent($seccion);

$seccion_pie_dest = new Seccion("", "center", "", SECCION_SIN_TITULO);
$seccion_pie_dest->AddComponent(new Link($volver_link, "<br/>".TEXT_VOLVER));
$seccion_pie_dest->AddComponent(new Link($home_link, TEXT_HOME));
$pagina->AddComponent($seccion_pie_dest);


$pagina->WriteHeaders();
echo $pagina->Display();
?>



