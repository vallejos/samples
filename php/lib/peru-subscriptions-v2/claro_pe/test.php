<?php
include($_SERVER['DOCUMENT_ROOT']."/smsc/classes/sms.php");
include($_SERVER['DOCUMENT_ROOT']."/lib/conexion.php");
include("ContentGatewayBilling.php");

$dbc = new coneXion("Web");
$db = $dbc->db;

$celular = "51997103416";

$operador = "claro_pe_new";
$app = 12314;
$mensaje ="Test";

$sms = new sms($db,$operador,$app,$celular,$mensaje);
if($sms->enviar()) {
    echo "Enviando correctamente";
} else {
    echo "No se mandó el mensaje";
}

/*
$cgb = new ContentGatewayBilling($db, $celular, 2809);

$cgb->process(1);

*/

?>