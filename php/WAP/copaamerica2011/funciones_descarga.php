<?php
include_once("constantes.php");
function obtenerDatosCompra($tipo){
	$db = mysql_pconnect("10.0.0.240", "pablo", "pablok4");
	$sql = "SELECT * FROM datos_descarga." . OPERADORA . " WHERE tipo = $tipo LIMIT 1";
	$rs = mysql_query($sql, $db);
	return mysql_fetch_array($rs);
}
?>