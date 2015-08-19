<?php

DEFINE("CARPETA_SALIDA", "wallpapers_tmp");
DEFINE("CALIDAD",75);

//Clase creada por Leonardo Caraballo para la creacion de imagenes y degradado de colores
//Uso combinado con las clases image converter y resize
class Wallpaper {
	private $image_post; //$image_post["tmp_name"]-->Nombre del archivo temporal $imgage_post["name"]-->Nombre del archivo
	private $image = null; //el objeto imagen creado por php a partir de una imagen verdadera
	private $bkcolor; //color de fondo que se rellena la imagen
	private $width; //Ancho de la imagen a generar
	private $height; //Alto de la imagen a generar
	private $outputFormat; //Formato de Salida
	private $watermark; //Imagen de Marca de Agua
	private $extension; //Extension de la salida
	private $kbmaximo = 0; //Maximo Kb permitido
	private $colorEmpieza=0;//cantidad inicial de colores de la paleta al crear un gif

	public function crear($post_file /*string*/, $id /*string*/, $width /*number*/, $height /*number*/, $color /*string*/, $outputFormat='' /*string*/, $kbmaximo = 0 /*number kb*/, $colorEmpieza=256 /*number*/,$waterMark ='' /*string*/) {
		$tipos_validos=array("jpg","gif","png");

		$this->watermark=$waterMark;
		
		//=========Ingreso de los parametros utilizados por el constructor=========
		$this->id 			= 		$id;
		$this->kbmaximo		=		($kbmaximo*1024);
		$this->image_post 	= 		$_FILES[$post_file];
		$this->bkcolor 		= 		$color;
		$this->extension 	= 		$this->obtenerExtension($this->image_post['type']);
		$this->width 		= 		$width;
		$this->height 		= 		$height;

		$this->colorEmpieza	=		$colorEmpieza;
		//$this->nombreSalida = $this->obtenerNombreSalida($this->extension);

		$outputFormat=trim($outputFormat);

		if (!empty($outputFormat)) {
			$this->outputFormat = $outputFormat;
		} else {
			$this->outputFormat=$this->extension;
		}
		
		//========== Borra la carpeta temporal donde se cargan las imagenes ===========
		if (!is_dir(CARPETA_SALIDA)) mkdir(CARPETA_SALIDA,0777);
	
		//========Verificacion de que el archivo submiteado sea correcto==================
		if (!in_array($this->extension,$tipos_validos) || empty($this->extension)) {
			print "<br />";
			echo "Solo se permiten las extensiones ". implode(",",$tipos_validos) . "<br />";
			return false;
		}
			
		
		//======================== Subida y creacion de imagenes  =====================
		$this->uploadLocalImage();
		$this->obtenerExtension($this->image_post["type"]);
		$ruta_imagen = $this->generateImgResolutionFill();

		
		//======================== Procesamiento posterior a la creacion de las  imagenes =========================
		if (is_string($ruta_imagen) && strlen($ruta_imagen) > 0) {
			$this->posprocessing();
			if (file_exists($this->obtenerSalida($this->outputFormat)))
			return $this->obtenerSalida($this->outputFormat);
			else
			return false;
		} else {
			return false;
		}
	}


	//===============Obtiene la extension del archivo subido
	private function obtenerExtension($tipo) {
		switch( $tipo ){
			case "image/gif": return "gif";break;
			case "image/jpeg": return "jpg";break;
			case "image/jpg": return "jpg";break;
			case "image/png": return "png";break;
			case "image/tiff": return "tiff";break;
			case "image/bmp": return "bmp";break;
			default : return false;
		}
	}
	
	//=======================convierte de hexadecimal (string) a RGB (array)
	function hexaToRgb($hex) {
		//Eliminamos el caracter # (en caso de que exista)
		if (0 === strpos($hex, '#')) {
			$hex = substr($hex, 1);
		} else
		if (0 === strpos($hex, '&H')) {
			$hex = substr($hex, 2);
		}

		//Obtenemos los 3 valores hexadecimales
		$cutpoint = ceil(strlen($hex) / 2) - 1;
		$rgb = explode(':', wordwrap($hex, $cutpoint, ':', $cutpoint), 3);

		//Los convertimos en decimal
		$rgb[0] = (isset($rgb[0]) ? hexdec($rgb[0]) : 0);
		$rgb[1] = (isset($rgb[1]) ? hexdec($rgb[1]) : 0);
		$rgb[2] = (isset($rgb[2]) ? hexdec($rgb[2]) : 0);

		return $rgb;
	}


	public function obtenerNombreSalida($ext){
		return $this->width . "x" . $this->heigth.".".$ext;
	}

	//Despues de haber generado la imagen le hace un procesamiento posterior para convertirla a otro formato o decolorarla
	public function posprocessing() {
		$formatos_validos = array("jpg", "gif", "swf");
		if (!in_array($this->outputFormat, $formatos_validos))
		return false;


		//Convierte la imagen al formato de salida
		if ($this->extension != $this->outputFormat) {
			new ImageConverter($this->obtenerSalida($this->extension), $this->
			outputFormat);
		}

		//Decolora la imagen para bajarle el peso al archivo color x color
		//die($this->outputFormat . " - tamaño: " . $this->kbmaximo);
		if (intval($this->kbmaximo) > 0) {
			$this->decolorate($this->obtenerSalida($this->outputFormat));
		}

		//Agrega marca de agua
		if ($this->watermark !="" && $this->outputFormat=="jpg") {
			$rs = new ResizeJpg();
			$rs->setAltoMax($this->height);
			$rs->setAnchoMax($this->width);			
			$rs->setCompresion(75);
			$rs->loadImage($this->obtenerSalida($this->outputFormat));
			$rs->loadWatermark($this->watermark);
			$rs->process();
			$rs->writeJpg($this->obtenerSalida($this->outputFormat));
		}
	}
	
	


	//Genera la imagen del ancho y alto, rellenando con el color estipulado el resto de la imagen en caso de que sea necesario
	function generateImgResolutionFill() {
		$x_size = $this->width;
		$y_size = $this->height;
		$newfolder_1 = CARPETA_SALIDA;
		$oldimg = $this->image_post["name"];
		$imgsrc = $this->crearImagenPhp($oldimg);


		$srcx = imagesx($imgsrc);
		$srcy = imagesy($imgsrc);

		$newimg_1 = $this->obtenerSalida($this->extension);
		@mkdir(CARPETA_SALIDA, 0777);
		//REDIMENSIONANDO RELLENANDO
		$thumb = imagecreatetruecolor($x_size, $y_size);
		$rgbcolor = $this->hexaToRgb($this->bkcolor);

		$color = imagecolorallocate($thumb, $rgbcolor[0], $rgbcolor[1], $rgbcolor[2]);
		imagefill($thumb, 0, 0, $color);
		$facx = $x_size / $srcx;
		$facy = $y_size / $srcy;
		if ($facx < $facy)
		$facred = $facx;
		else
		$facred = $facy;
		$resx = $srcx * $facred;
		$resy = $srcy * $facred;
		$center_w = round(($x_size - $resx) / 2);
		$center_h = round(($y_size - $resy) / 2);
		if (!imagecopyresampled($thumb, $imgsrc, $center_w, $center_h, 0, 0, $resx, $resy,
		$srcx, $srcy))
		return false;

		if ($this->extension == "gif") {
			if (!imagegif($thumb, $newimg_1))
			return false;
		}

		if ($this->extension == "jpg" || $this->extension == "jpeg") {
			if (!imagejpeg($thumb, $newimg_1))
			return false;
		}

		if ($this->extension == "png") {
			if (!imagepng($thumb, $newimg_1))
			return false;
		}

		return $this->obtenerSalida($this->extension);
	}

	//Decolora una imagen de a un color hasta llegar al tamanio requerido
	public function decolorate($img) {
		print "<h3>decolorate: $img<h3 />";
		
		
		/*if ($this->outputFormat == "gif") {
			copy($this->obtenerSalida("gif"), CARPETA_SALIDA."/base.gif");
			$n=new ImageConverter(CARPETA_SALIDA . "/base.gif", "jpg");
			
			print("kbmaximo: ".$this->kbmaximo . " - " . filesize(CARPETA_SALIDA."/base.gif")."<br />" );
			
			if ( filesize(CARPETA_SALIDA."/base.gif") < $this->kbmaximo ) {
				echo "El archivo ya pesa menos que el tamaño maximo<br />";
				@unlink(CARPETA_SALIDA."/base.gif");
				@unlink(CARPETA_SALIDA."/base.jpg");
				return false;
			}
			
			$tamanio_archivo	=$this->kbmaximo+1;
			$maximo_permitdo	=$this->kbmaximo;
			$cant_color=$this->colorEmpieza;
			$decolore=false;
			
			print "<hr>" . $tamanio_archivo > $maximo_permitdo . "<hr />";
			
			while ($tamanio_archivo>$maximo_permitdo) {
				print "*";
				$decolore=true;
				$image=imagecreatefromjpeg(CARPETA_SALIDA."/base.jpg") or die(" no se puede crear temporal jpg");
				
				$this->colorDecrease($image,true,$cant_color);
				imagejpeg($image,CARPETA_SALIDA."/temporal.jpg",75);
				
				
				$tamanio_archivo=filesize(CARPETA_SALIDA."/temporal.jpg");
				//die( "$tamanio_archivo<br />" );
				if ($tamanio_archivo>$maximo_permitdo) {
					@unlink(CARPETA_SALIDA . "/temporal.jpg");
					$cant_color=$cant_color-2;
					
				} else {
					break;
				}
				if ($cant_color==0) {
					echo "No funciono la decoloracion";
					@unlink(CARPETA_SALIDA."/base.jpg");
					@unlink(CARPETA_SALIDA."/temporal.jpg");
					@unlink(CARPETA_SALIDA."/temporal.gif");
					@unlink(CARPETA_SALIDA."/base.gif");
					@unlink(CARPETA_SALIDA."/".$this->id.".".$this->extension);
					@unlink(CARPETA_SALIDA."/".$this->id.".".$this->outputFormat);
					return false;
				}
			}
			if (file_exists(CARPETA_SALIDA."/temporal.jpg") && filesize(CARPETA_SALIDA."/temporal.jpg")<=$maximo_permitido) {
				$n=new ImageConverter(CARPETA_SALIDA . "temporal.jpg", "gif");
				if (copy(CARPETA_SALIDA."/temporal.gif",$this->obtenerSalida("gif"))) {
					echo "La descoloracion se realizo correctamente<br>";
				}
			}
			@unlink(CARPETA_SALIDA."/base.gif");
			@unlink(CARPETA_SALIDA."/base.jpg");
			@unlink(CARPETA_SALIDA."/temporal$cant_color.gif");
			@unlink(CARPETA_SALIDA."/temporal.gif");
			@unlink(CARPETA_SALIDA."/temporal.jpg");
		}*/
	}


	public function uploadLocalImage() {
		copy($this->image_post["tmp_name"], $this->image_post["name"]);
	}

	//Crea una imagen php a partir del archivo publicado
	public function crearImagenPhp($archivo) {

		//$extension = $this->obtenerExtension($archivo);
		$extension = substr($archivo, -3);

		if ($extension == "jpg" || $extension == "jpeg") {
			return imagecreatefromjpeg($archivo);
		} elseif ($extension == "gif" || $this->extension == "gif89") {
			return imagecreatefromgif($archivo);
		} elseif ($extension == "png") {
			return imagecreatefrompng($archivo);
		} else {
			return false;
		}
	}

	function colorDecrease( $image, $dither, $ncolors ) {
		$width = imagesx( $image );
		$height = imagesy( $image );
		$colors_handle = ImageCreateTrueColor( $width, $height );
		ImageCopyMerge( $colors_handle, $image, 0, 0, 0, 0, $width, $height, 100 );
		ImageTrueColorToPalette( $image, $dither, $ncolors );
		ImageColorMatch( $colors_handle, $image );
		ImageDestroy( $colors_handle );
	}

	public function showMe() {
		echo "<pre>";
		print_r($this);
		echo "</pre>";
	}
	
	public function obtenerSalida($ext) {
		$prefijo 		=		CARPETA_SALIDA."/".$this->width."x".$this->height;
		return $prefijo.".".$ext;
	}
}

?>