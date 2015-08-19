<?php
include("includes.php");
$miC = new coneXion("Web", true);
$db = $miC->db;

$lista = array();
$ua = "mot-v3/";
$por_pagina = 10000;
$tipos_juegos = array(31, 57, 59, 35, 61);
$cats  = getCatJuegosJava($db, $tipos_juegos, 0, 0, 1000, $ua);

$total = $cats['total'];
unset($cats['total']);


$lista = array();
foreach ($cats as $c) {
    $lista[$c['descripcion']] = array();
    $juegos = obtenerJuegosPorCat($c['id'], obtenerIDCelular($ua, $db), $db, 0, 1000);
    $total = $juegos['total'];
    unset($juegos['total']);

    foreach($juegos as $item){
      $lista[$c['descripcion']][] = $item['id'];
    }
}

foreach($lista as $catname => $categoria){
  $listaids = implode(",", $categoria);
  echo "$catname:<br/>$listaids";
  echo "<br /><br />";
}
?>