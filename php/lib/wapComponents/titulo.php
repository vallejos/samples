<?php
class Titulo{
	private $titulo;
	private $width;
	private $height;
	private $destino;
	
	private $left_img_src;
	private $right_img_src;
	private $center_img_src;
	private $left_img_src2;
	private $right_img_src2;
	private $center_img_src2;
	private $fuente;
	private $color_fuente;
	private $rebuild;
	private $underlined;

	function Titulo($titulo, $width, $font_color, $destino,$rebuild=false ){
		$this->titulo = $titulo;
		$this->width = $width;
		$this->destino = $destino;
		$this->fuente = 3;
		$this->color_fuente = $font_color;
		$this->rebuild = $rebuild;
		$this->underlined = false;
	}
	public function setImages($src){
		$path_to 		= $this->getBasicPath();
		$this->left_img_src 	= $path_to.$src.'_left.gif';
		$this->right_img_src 	= $path_to.$src.'_right.gif';
		$this->center_img_src 	= $path_to.$src.'_center.gif';
		
		$this->left_img_src2 	= $path_to.$src.'_left2.gif';
		$this->right_img_src2 	= $path_to.$src.'_right2.gif';
		$this->center_img_src2 	= $path_to.$src.'_center2.gif';
	}

	public function setUnderline($u){
		$this->underlined = $u;
	}
	public function setFont($fuente){
		if(is_nan($fuente)){
			$fuente = imageloadfont($fuente);
		}
		$this->fuente = $fuente;
	}
	public function getPath(){
		$path_to_imgs = $this->getBasicPath().$this->destino;
		$this->destino = $path_to_imgs;

		if(!file_exists( $this->destino ) || $this->rebuild) {
		   $path = explode("/", $this->buildImage());
		   $path = $path[count($path)-1];
		   return SECCION_TIT_FOLDER."/".$path;
		}else{
			//return $this->buildImage();
			$path = explode("/", $this->destino);
			$path = $path[count($path)-1];
			return SECCION_TIT_FOLDER."/".$path;
		}
	}

	/**
	  Retorna el path a la carpeta en donde est�n las imagenes

	  La constante SECCION_TIT_FOLDER se define en el archivo constantes.php dentro de la carpeta
	  propia de la WAP. Especifica el nombre de la carpeta en donde se guardaran los titulos generados y en donde
	  se encuentran las imagenes para generar los titulos (debe ser la misma carpeta obviamente, duh!)
	  */
	private function getBasicPath(){
		$path = $_SERVER['SCRIPT_NAME'];
		$path = explode("/", $path);
		unset($path[count($path) - 1]);
		$path = implode("/", $path);	

		$doc_root_folder = $_SERVER['DOCUMENT_ROOT'];

		$path_to_imgs = $doc_root_folder."/".$path."/".SECCION_TIT_FOLDER."/";
		return $path_to_imgs;
	}


	private function buildImage(){
		
		//Levantamos las imagenes a utilizar
			$img_left = imagecreatefromgif($this->left_img_src);
			if(!$img_left) {
				exit;
			}
			$img_left_width = ImageSX($img_left);
			$img_left_height =  ImageSY($img_left);
			
			$img_right = imagecreatefromgif($this->right_img_src);
			$img_right_width = ImageSX($img_right);
			$img_right_height =  ImageSY($img_right);
			
			$img_center = imagecreatefromgif($this->center_img_src);
			$img_center_width = ImageSX($img_center);
			$img_center_height =  ImageSY($img_center);
			
		//Termina-------
		
		
		$f_h = imagefontheight($this->fuente);
		$f_w = imagefontwidth($this->fuente);
		$ancho = $this->width - $img_left_width - $img_right_width;
		
		$txt_lines = explode("\n", wordwrap($this->titulo, ($ancho / $f_w), "\n"));
		$lines = count($txt_lines);
		
		$text_height = $f_h * $lines;
		
		
		if($img_left_height < $text_height){
			//Levantamos las imagenes a utilizar
			$img_left = @imagecreatefromgif($this->left_img_src2);
			$img_left_width = ImageSX($img_left);
			$img_left_height =  ImageSY($img_left);
			
			$img_right = @imagecreatefromgif($this->right_img_src2);
			$img_right_width = ImageSX($img_right);
			$img_right_height =  ImageSY($img_right);
			
			$img_center = @imagecreatefromgif($this->center_img_src2);
			$img_center_width = ImageSX($img_center);
			$img_center_height =  ImageSY($img_center);
			
			//Termina-------
		}
		$this->height = $img_left_height;
		
		
		$emptyImage = $this->imagecreatetruecolortransparent($this->width, $this->height);
		
		//Agregamos las imagenes al dise�o ---
			imagecopyresampled($emptyImage, $img_left, 0, 0, 0, 0, $img_left_width,  $img_left_height, $img_left_width, $img_left_height);
			imagecopy($emptyImage, $emptyImage, 0, 0, 0, 0, $this->width, $this->height);
				
			imagecopyresampled($emptyImage, $img_right, $this->width - $img_right_width, 0, 0, 0, $img_right_width,  $img_right_height, $img_right_width, $img_right_height);
			imagecopy($emptyImage, $emptyImage, 0, 0, 0, 0, $this->width, $this->height);	
			$bg_width = $this->width - $img_right_width;
			for ( $bg_x = $img_left_width; $bg_x < $bg_width; $bg_x += $img_center_width ) {
				$dst_x = $bg_x;
				$src_x = 0;
				$w = $img_center_width;
				if($img_center) {
				    imagecopyresampled($emptyImage, $img_center, $dst_x, 0, 0, 0, $img_center_width,  $img_center_height, $img_center_width, $img_center_height);
    				imagecopy($emptyImage, $emptyImage, 0, 0, 0, 0, $this->width, $this->height);
    				}
			}
		//Termina ------------
		
		//Agregamos el texto
		$f_color = $this->hex2rgb($this->color_fuente);
		$font_color = imagecolorallocate($emptyImage, $f_color[0], $f_color[1], $f_color[2]);
		
		$y = (count($txt_lines) == 1)? 3 : 1;
		foreach ($txt_lines as $text) {
			imagestring($emptyImage, $this->fuente, $img_left_width+3, $y, $text, $font_color);
			if($this->underlined) {
				$alto_fuente = imagefontheight($this->fuente);
				$ancho_fuente = imagefontwidth($this->fuente);
				$ancho_texto = strlen($text) * $ancho_fuente;
				/**
				  alto_fuente = imagefontheight($this->fuente);

				  x1 = img_left_width + 2
				  y1 = y + alto_fuente
				  x2 = img_left_width + ancho_texto + 2
				  y2 = y1
				   */
				imageline($emptyImage, $img_left_width + 2, $y + $alto_fuente, $img_left_width + $ancho_texto + 2, $y + $alto_fuente, $font_color);
			}
			$y += $f_h;
		}
		//Termina ---
		
		imagegif($emptyImage,$this->destino);		
		return $this->destino;		
	}
	
	private function imagecreatetruecolortransparent($x, $y){ 
	  $i = imagecreatetruecolor($x, $y); 
	  $b = imagecreatefromstring(base64_decode($this->blankpng())); 
	  imagealphablending($i, false); 
	  imagesavealpha($i, true); 
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
	private function hex2rgb($color){
		if ($color[0] == '#')
			$color = substr($color, 1);
		if (strlen($color) == 6)
			list($r, $g, $b) = array($color[0].$color[1],$color[2].$color[3],$color[4].$color[5]);
		elseif (strlen($color) == 3)
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		else
			return false;

		$r = hexdec($r); 
		$g = hexdec($g); 
		$b = hexdec($b);
		
		return array($r, $g, $b);
	}
}
?>
