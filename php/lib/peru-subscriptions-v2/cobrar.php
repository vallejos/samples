<?php
include_once(dirname(__FILE__)."/ManejadorProcesos.php");
include_once(dirname(__FILE__)."/../conexion.php");

$dbc = new coneXion("suscriptions");
$db = $dbc->db;

$subject = "Proceso de cobro de suscripciones";

$mp = new ManejadorProcesos($db);
$mp->loadChargeProcesses();

if($mp->billAll()) {
    $mensaje = "Proceso de cobro de suscripciones finalizado correctamente: " . date("d-m-Y H:i:s");
    mail("fernando.doglio@globalnetmobile.com", $subject, $mensaje);
} else {
//    $mensaje = "Hubo un error durante el proceso de cobro de suscripciones, chequear Logs: " . date("d-m-Y H:i:s");
}



?>
