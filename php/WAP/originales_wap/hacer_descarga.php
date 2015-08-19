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

$idCont = $_GET['id'];
$nombre_wap = 'halloween_wap';
/*if($idCont==5660){
	$url_download = urlWapMuestra($idCont, "halloween_wap", $nombre_wap,"ancel");
	header("Location: $url_download");
}*/
if($msisdn) {

	$sql = "SELECT reintentos, pk_ventas_wap_billing as id
		FROM ancel.ventas_wap_billing
		WHERE descarga = $idCont
		AND celular = '$celular'
		AND useragent = '$ua'
		AND reintentos < 3 
		AND nombre_wap = '$nombre_wap'
		AND fecha >= DATE_SUB(curdate(), INTERVAL 24 HOUR)";
	$rs = mysql_query($sql, $db);
	if($rs) {
		$row = mysql_fetch_assoc($rs);
		if($row['reintentos'] > 0 && $row['reintentos'] < 3) {
			$idCompra = $row['id'];
			$url_descarga = urlWapPushAncel($msisdn, $idCont, $idCompra, servicio3G(), $nombre_wap, false);
		} else {
			$url_descarga = urlWapPushAncel($msisdn, $idCont, false, servicio3G(), $nombre_wap, false);
		}
	
		header("Location: $url_descarga");
		exit;
	} else {
		$texto = ("Hubo un error en la compra del contenido, porfavor, intentelo más tarde.");
	}

} else {
	$texto = ("Hubo un error en la compra del contenido, porfavor, intentelo más tarde.");
}

///////////////////////////////////////////
/////    PRESENTACION
///////////////////////////////////////////

$pagina = new Pagina(NOMBRE_PORTAL." - Descarga tu contenido");
$seccion = new Seccion("Descargas", "center", SMALL_FONT_SIZE);

if($pagina->_soportaXHTML()) {
	$texto = str_replace("$$", "$", $texto);	
}
if($texto) {
	$seccion->AddComponent($texto);
}

$seccion->AddComponent(new Link("home.php", TEXT_HOME));

$pagina->AddComponent($seccion);

$pagina->WriteHeaders();
echo $pagina->Display();


?>
