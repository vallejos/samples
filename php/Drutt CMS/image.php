<?php

function crearImagen($origen,$destino,$ancho=null ,$alto=null,$background=false,$extension=".jpg" ){
	$colors = array(255,255,255);
	list($w, $h ,$type) = getimagesize($origen);
	if($ancho == null ) $ancho = $w;
	if($alto == null ) $alto = $h;
	if ($type == 1){
		$src_img = @imagecreatefromgif($origen);
	}else if($type == 3){
		$src_img = @imagecreatefrompng($origen);
	}else if($type == 6){
		$src_img = @imagecreatefromwbmp($origen);
	}
	if (!empty($src_img)){
		@imagegif($src_img,$origen);
	}		
	ini_set('gd.jpeg_ignore_warning', 1);

	$old_x=imageSX($src_img);
	$old_y=imageSY($src_img);

	$thumb_w = $ancho;
	$thumb_h = $alto;

	$wRatio = $thumb_w / $old_x;
	$hRatio = $thumb_h / $old_y;		
	if($thumb_w>$old_x && $thumb_h>$old_y){
		$ancho = $old_x;
		$altura = $old_y;
	}else{
		if(($wRatio * $old_y) < $thumb_h){
			$altura = ceil($wRatio * $old_y);
			$ancho = $thumb_w;
		}else if(($hRatio * $old_x) < $thumb_w){
			$ancho = ceil($hRatio * $old_x);
			$altura = $thumb_h;
		}else{
			$ancho = $thumb_w;
			$altura = $thumb_h;
		}
	}		
	if(!$background){
		$thumb_w = $ancho;
		$thumb_h = $altura;
	}else{
		$colors = hex2rgb($background);
	}
	
	$posx = $thumb_w/2 - $ancho/2;
	$posy = $thumb_h/2 - $altura/2;		
	$dst_img = imagecreatetruecolor($thumb_w,$thumb_h);
	
	$colorfondo = imagecolorallocate($dst_img, $colors[0], $colors[1], $colors[2]);
	imagefilledrectangle($dst_img, 0, 0, $thumb_w,$thumb_h, $colorfondo);		
	imagecopyresampled($dst_img,$src_img,$posx,$posy,0,0,$ancho,$altura,$old_x,$old_y);
	
	switch($extension){
		case '.gif':
			imagegif($dst_img,$destino);
		break;
		case '.jpg':
		default:
			imagejpeg($dst_img,$destino,90);
		break;
	}
	
	imagedestroy($dst_img);
	imagedestroy($src_img);
	$oldumask = umask(0);
	chmod($destino,0777);
	umask($oldumask);
}

function hex2rgb($color){
	if ($color[0] == '#')
		$color = substr($color, 1);
	if (strlen($color) == 6)
		list($r, $g, $b) = array($color[0].$color[1],$color[2].$color[3],$color[4].$color[5]);
	elseif (strlen($color) == 3)
		list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
	else
		return false;

	$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
	return array($r, $g, $b);
}
?>