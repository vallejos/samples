<?php
class kimage{
	private $id;
	private $path;
	private $logo;
	private $extension;
	private $origen_extension;
	function kimage($id, $logo="", $source_dir="", $extFrom=""){
		$this->id = $id;
		$this->logo = $logo;
		$this->path = $source_dir;
		$this->extension = ".jpg";
		$this->origen_extension = $extFrom;
	}
	public function setExtension($ext='.jpg'){
		$this->extension = $ext;
	}
	function getPath($width=NULL,$height=NULL,$background=FALSE ){
//echo "$width,$height,$background\n";
		$dimension = '';
		if ($width !=NULL && $height!=NULL) {
			$dimension = $width.'x'.$height.'/';
		}
//		$destination_dir = $this->crearDir($this->path.$dimension);
//		$filename = $destination_dir.$this->id.$this->extension;

		$destination_dir = $this->path."/";

//echo "DEST $destination_dir<br/>";

		$filename = $destination_dir.$this->id."_$width"."x$height".$this->extension;

//echo "FNAME $filename<br/>";
		if(!file_exists($filename) || TRUE){
			if ($this->origen_extension == ".gif") $origen_file = $this->path."/".$this->id.'.gif';
			else if ($this->origen_extension == ".jpg") $origen_file = $this->path."/".$this->id.'.jpg';
			else die("ERROR: Incorrect extension.");

//echo "ORIGEN $origen_file<br/>";
			if(file_exists($origen_file)){
				$this->crearImagen($origen_file,$filename,$width,$height,$background);
			}else{
				return FALSE;
			}

		}
		return $filename;
	}
	function crearImagen($origen,$destino,$ancho=NULL ,$alto=NULL,$background=FALSE ){
		$colors = array(255,255,255);
//echo "=>\n";
		list($w, $h ,$type) = getimagesize($origen);
		if($ancho == NULL ) $ancho = $w;
		if($alto == NULL ) $alto = $h;
		if ($type == 1){
			$img = @imagecreatefromgif($origen);
		}else if($type == 3){
			$img = @imagecreatefrompng($origen);
		}else if($type == 6){
			$img = @imagecreatefromwbmp($origen);
		}
		if (!empty($img)){
			@imagegif($img,$origen);
		}
		ini_set('gd.jpeg_ignore_warning', 1);

		if ($this->origen_extension == ".gif") $src_img=imagecreatefromgif($origen);
		else if ($this->origen_extension == ".jpg") $src_img=imagecreatefromjpeg($origen);
		else die("ERROR: Incorrect extension.");

//echo "ext: ".$this->extension."\n";

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
			$colors = $this->hex2rgb($background);
		}

		$posx = $thumb_w/2 - $ancho/2;
		$posy = $thumb_h/2 - $altura/2;
		$dst_img = imagecreateTRUEcolor($thumb_w,$thumb_h);

		$colorfondo = imagecolorallocate($dst_img, $colors[0], $colors[1], $colors[2]);
		imagefilledrectangle($dst_img, 0, 0, $thumb_w, $thumb_h, $colorfondo);

		imagecopyresampled($dst_img,$src_img,$posx,$posy,0,0,$ancho,$altura,$old_x,$old_y);

		if ($this->logo != '') {
			$img_logo = imagecreatefrompng ($this->logo);
			$logo_w= imageSX($img_logo);
			$logo_h = imageSY($img_logo);
			$pos_x = $ancho - $logo_w;
			$pos_y = $altura - $logo_h;
			imagecopyresampled($dst_img, $img_logo, $pos_x, $pos_y, 0, 0, $pos_x,  $pos_y, $ancho, $altura);
			imagecopy($dst_img, $img_logo, $pos_x, $pos_y, 0, 0, $logo_w, $logo_h);
		}
		switch($this->extension){
			case '.gif':
				imagegif($dst_img,$destino);
			break;
			case '.png':
				imagepng($dst_img,$destino);
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
	private function imagecreateTRUEcolortransparent($x, $y){
	  $i = imagecreateTRUEcolor($x, $y);
	  $b = imagecreatefromstring(base64_decode($this->blankpng()));
	  imagealphablending($i, FALSE);
	  imagesavealpha($i, TRUE);
	  imagecopyresized($i, $b ,0 ,0 ,0 ,0 ,$x, $y, imagesx($b), imagesy($b));
	  return $i;
	}
	private function blankpng(){
	  $c  = "iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29m";
	  $c .= "dHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAADqSURBVHjaYvz//z/DYAYAAcTEMMgBQAANegcCBNCg";
	  $c .= "dyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAAN";
	  $c .= "egcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQ";
	  $c .= "oHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAA";
	  $c .= "DXoHAgTQoHcgQAANegcCBNCgdyBAgAEAMpcDTTQWJVEAAAAASUVORK5CYII=";
	  return $c;
	}
	function hex2rgb($color){
		if ($color[0] == '#')
			$color = substr($color, 1);
		if (strlen($color) == 6)
			list($r, $g, $b) = array($color[0].$color[1],$color[2].$color[3],$color[4].$color[5]);
		elseif (strlen($color) == 3)
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		else
			return FALSE;

		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
		return array($r, $g, $b);
	}

	function crearDir($dir){
		if(!is_dir($dir) && $dir !='' ){
			$oldumask = umask(0);
			$create_dir = mkdir($dir, 0777,TRUE);
			chmod($dir,0777);
			umask($oldumask);
			if(!$create_dir){
				return FALSE;
			}
		}
		return $dir;
	}
}
?>