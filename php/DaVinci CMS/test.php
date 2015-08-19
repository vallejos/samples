<?php


include_once("classes/truetone.class.php");
include_once("classes/poliphonic.class.php");

$tt = new truetone();

$tt->set("code", 176579);

echo $tt->code;

?>