<?php

$img = $_GET['img'];
if (!is_file($img) || !file_exists($img)) die('No hay img');
$mw = (isset($_GET['w'])) ? $_GET['w'] : 300;

$fsize = array();
$cursize = getimagesize($img);
$cw = $cursize[0];
$ch = $cursize[1];
$ratio = $cw / $ch;
if ($cw > $mw) {
	$fsize[0] = $mw;
	$fsize[1] = $fsize[0] / $ratio;
}
$pinfo = pathinfo($img);
$dst = imagecreatetruecolor($fsize[0], $fsize[1]);
$tipos = array('jpg' => 'imagecreatefromjpeg', 
			   'jpeg' => 'imagecreatefromjpeg', 
			   'gif' => 'imagecreatefromgif', 
			   'png' => 'imagecreatefrompng');
$func = $tipos[$pinfo['extension']];
$src = $func($img);
imagecopyresampled($dst, $src, 0, 0, 0, 0, $fsize[0], $fsize[1], $cw, $ch);
header('Content-Type: image/jpeg');
imagejpeg($dst, null, 70);

?>