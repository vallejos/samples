<?php
include("includes.php");

//------------------------------------
// Conexion con DB
//------------------------------------
$miC = new coneXion("Web", true);
$db = $miC->db;

///////////////////////////////////////////////////////7
/////       LOGICA
///////////////////////////////////////////////////////7
$nombre_wap = "juegos2x1_wap";
$type = $_GET['tipoCat'];
$idCont = $_GET['id'];
$gratis = isset($_GET['gratis'])?$_GET['gratis']:0;

///////////////////////////////////////////
/////    PRESENTACION
///////////////////////////////////////////

if($gratis==1){
	$seccion = new Seccion("Descarga Gratis", "center", SMALL_FONT_SIZE);
	restarDescargasGratis($db,$msisdn);
 	$url=urlWapMuestra($idCont, "", "Juegos2x1", $operador="");
	$seccion->AddComponent("Haz clic sobre el siguiente link para obtener tu descarga gratis:<br/>");
	$seccion->AddComponent('<br/><a href="'.$url.'" >Descargar Juego</a><br/>');
}else{
	$seccion = new Seccion("Descargas", "center", SMALL_FONT_SIZE);
	$cobro = new CobroComcelCo($db, $celular);
	$cobro->setNombreWap($nombre_wap);
	$retorno = $cobro->comprarContenido($idCont, $ua);
	$id_ventas_wap_billing = $cobro->id_transaccion;
	if ($retorno === true) {
		aumentarDescargasGratis($db,$msisdn);
		$url_desc = urlGenerica($celular, $idCont, $id_ventas_wap_billing, "comcel_wap");
		$seccion->AddComponent(new Link($url_desc, "Descarga contenido"));
	} else {
		$seccion->AddComponent($retorno);
	}
}
	
///////////////////////////////////////////////////////
/////       LOGICA
///////////////////////////////////////////////////////

$pagina  = new Pagina(NOMBRE_PORTAL." - Descarga tu contenido");

$seccion->AddComponent(new Link("home.php?push=".$nombre_wap, "<br/>Inicio"));

$pie = new Seccion("", "center", NORMAL_FONT_SIZE, SECCION_SIN_TITULO);
$pie->AddComponent(new Imagen("images/wazzupcom.gif", "Wazzup.com.co"));
$pagina->AddComponent($seccion);


$pagina->WriteHeaders();
echo $pagina->Display();

?>