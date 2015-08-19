<?php
/*
define("USER", "a4101924_motion");
define("PASS", "banca1234rota");
define("HOST", "mysql15.000webhost.com");
define("DATABASE", "a4101924_motion");
$cnx = mysql_connect(HOST, USER, PASS);
mysql_select_db(DATABASE);
*/

include_once("class/conexion.php");
define("DATABASE", "qrapp_antel");
$cxn = new coneXion(DATABASE);

include_once("class/JSON.php");
include_once("class/Puntos.class.php");
include_once("class/Barrios.class.php");

?>