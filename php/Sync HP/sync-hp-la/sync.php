<?php

/**
 * 
 * 1- Obtener generos del WS
 * http://maxx.me.net-m.net/me/maxx/2146850/contentGroups?contentTypeKey=FULLTRACK
 * 2- por cada genero, obtener temas
 * http://maxx.me.net-m.net/me/maxx/2146850/items?start=-499&contentTypeKey=FULLTRACK&maxSize=500&contentGroupId=15649
 * 3- por cada tema, asociar a album/artist 
 * select * from bd
 * 4- si no tengo asociacion album/artista, obtener artista y album
 * http://maxx.me.net-m.net/me/maxx/2146850/items?contentTypeKey=FT_BUNDLE&icpn=0602527529042
 * 
 */

include_once "config.php";
include_once "includes.php";

$logFile = LOG_DIR."/la-".date("Ymd").".log";
$log = new klogger($logFile, TRUE);
$dbc = new dbcAmazon();

$contractId = WS_CONTRACTID_LA; // LA

$log->add("*** Started sync process, using contractId=$contractId ***");


$tableName = "generos";
$log->add("cleaning up temporary db table $tableName...");
if (emptyTable($dbc, $tableName) === TRUE) {
    $log->add("success!");
} else {
    $log->add("fail!");
    
    // enviar aviso cel
    $log->add("sending sms notification");
    avisoCel("could not clean up temp db: $tableName");
}


$tableName = "temas";
$log->add("cleaning up temporary db table $tableName...");
if (emptyTable($dbc, $tableName) === TRUE) {
    $log->add("success!");
} else {
    $log->add("fail!");
    
    // enviar aviso cel
    $log->add("sending sms notification");
    avisoCel("could not clean up temp db: $tableName");
}


$tableName = "artistas";
$log->add("cleaning up temporary db table $tableName...");
if (emptyTable($dbc, $tableName) === TRUE) {
    $log->add("success!");
} else {
    $log->add("fail!");

    // enviar aviso cel
    $log->add("sending sms notification");
    avisoCel("could not clean up temp db: $tableName");
}


$tableName = "albums";
$log->add("cleaning up temporary db table $tableName...");
if (emptyTable($dbc, $tableName) === TRUE) {
    $log->add("success!");
} else {
    $log->add("fail!");
    
    // enviar aviso cel
    $log->add("sending sms notification");
    avisoCel("could not clean up temp db: $tableName");
}


$tableName = "albums_artistas";
$log->add("cleaning up temporary db table $tableName...");
if (emptyTable($dbc, $tableName) === TRUE) {
    $log->add("success!");
} else {
    $log->add("fail!");

    // enviar aviso cel
    $log->add("sending sms notification");
    avisoCel("could not clean up temp db: $tableName");
}


$tableName = "albums_generos";
$log->add("cleaning up temporary db table $tableName...");
if (emptyTable($dbc, $tableName) === TRUE) {
    $log->add("success!");
} else {
    $log->add("fail!");

    // enviar aviso cel
    $log->add("sending sms notification");
    avisoCel("could not clean up temp db: $tableName");
}


$tableName = "albums_temas";
$log->add("cleaning up temporary db table $tableName...");
if (emptyTable($dbc, $tableName) === TRUE) {
    $log->add("success!");
} else {
    $log->add("fail!");

    // enviar aviso cel
    $log->add("sending sms notification");
    avisoCel("could not clean up temp db: $tableName");
}


// PROCESAR GENEROS
sleep(5); // pause 5 secs for db flush
include_once "sync-groups.php";

// TODO: PROCESAR TEMAS
sleep(5); // pause 5 secs for db flush
include_once "sync-tracks.php";


$log->add("*** finished sync process ***");
$log->save(TRUE);

?>
