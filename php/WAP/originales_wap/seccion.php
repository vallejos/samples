<?php

/**
 * La secci�n es un bloque, con titulo y contenido
 * 
 * Versi�n 1.0 - Inicial
 * Version 1.5 - Agregada generaci�n de titulos como imagenes, usando clase titulo.php de Mart�n
 */
class Seccion extends WapComponent{

	var $titulo;
	var $align;
	var $font_size;
	
	var $img_header;
	var $link_titulo;
	var $extra_class;

	/**
	 * Constructor
	 * 
	 * @param string $titulo  	El titulo que tendr� la secci�n
	 * @param string $align   	El alineamiento del texto de la secci�n, valores: left, center, right
	 * @param int    $font_size 	El tama�o de la fuente, valores: NORMAL_FONT_SIZE, BIG_FONT_SIZE, SMALL_FONT_SIZE 
	 * @param int 	 $tipo_seccion  El tipo de la secci�n, esto determinar� el template a utilizar, valores: SECCION_TITULO, SECCION_SIN_TITULO
	 */
	function Seccion($titulo, $align = "center", $font_size = NORMAL_FONT_SIZE, $tipo_seccion = SECCION_NORMAL){
		$this->titulo = $titulo;
		if($tipo_seccion == SECCION_NORMAL) {
			$this->template = "templates/seccion.tpl";
		} else {
			$this->template = "templates/seccion_sin_titulo.tpl";	
		}
		if($align == "") {
			$align = "center"; //si se deja el align vac�o, el c�digo deja el atributo con un valor vac�o en el c�digo generado y 
					  //algunos celulares no soportan eso.
		}
		$this->align = $align;
		$this->img_header = null;
		$this->font_size = $font_size;
		$this->link_titulo = false;
		$this->extra_class = "";
	}


	function setHeadLinkTo($dst){
		$this->link_titulo = $dst;
	}
	
	function addCssClass($class){
		$this->extra_class .= " ".$class;
	}

	function Display(){
		//Chancho....
		global $db;
		global $ua;
		$html = $this->_loadTemplate();

		$html = str_replace("#CLASS#", $this->extra_class, $html);
		/**
		  Constantes en el constantes.php de la carpeta wapComponents:
		  ANCHO_PANTALLA_CHICA
		  ANCHO_PANTALLA_MEDIANA
		  ANCHO_PANTALLA_GRANDE

		  Constantes en el archivo constantes.php de la carpeta de la WAP:
		  TIT_SECCION_PADDING  -  Especifica el Padding que tendr� la imagen en ambos lados (especificado en px)
		  TIT_SECCION_FONT_COLOR - El color de la fuente del titulo, especificado en formato HTML
		  PREFIX_TITULO_SECCION - Nombre base que tendr�n las imagenes para formar la imagen del titulo

		  */
		if(GENERAR_IMAGEN_TITULO_SECCION == 1) { //Si el titulo lo vamos a generar como una imagen...
			if(miscFunctions::soportaAnchoChico($db, $ua)) {
				$nombre_tam = "chica";
				$tam = ANCHO_PANTALLA_CHICA;
			}
			if(miscFunctions::soportaAnchoMedio($db, $ua)) {
				$nombre_tam = "mediana";
				$tam = ANCHO_PANTALLA_MEDIANA;
			}
			if(miscFunctions::soportaAnchoMayor($db, $ua)) {
				$nombre_tam = "grande";
				$tam = ANCHO_PANTALLA_GRANDE;
			}

			// -- Creamos el titulo 
			$width = $tam - (TIT_SECCION_PADDING * 2); //Le restamos el padding de ambos lados de la imagen
			$nombre_img = str_replace(" ", "_", $this->titulo)."_".$nombre_tam.".gif";
			$nombre_img = str_replace("<", "", $nombre_img);
			$nombre_img = str_replace(">", "", $nombre_img);
			$nombre_img = str_replace("\"", "", $nombre_img);
			$nombre_img = str_replace("\\", "", $nombre_img);
			$nombre_img = str_replace("/", "", $nombre_img);
			$nombre_img = str_replace("=", "", $nombre_img);

			$nombre_img = str_replace("&", "", $nombre_img);
			$nombre_img= str_replace(chr(241), "n", $nombre_img);
			$nombre_img= str_replace(chr(225), "a", $nombre_img);
			$nombre_img= str_replace(chr(233), "e", $nombre_img);
			$nombre_img= str_replace(chr(237), "i", $nombre_img);
			$nombre_img= str_replace(chr(243), "o", $nombre_img);
			$nombre_img= str_replace(chr(250), "u", $nombre_img);
			$nombre_img= str_replace(chr(209), "N", $nombre_img);
			$nombre_img= str_replace(chr(193), "A", $nombre_img);
			$nombre_img= str_replace(chr(201), "E", $nombre_img);
			$nombre_img= str_replace(chr(205), "I", $nombre_img);
			$nombre_img= str_replace(chr(211), "O", $nombre_img);
			$nombre_img= str_replace(chr(218), "U", $nombre_img);
			$nombre_img= str_replace(chr(191), "", $nombre_img);
			$nombre_img= str_replace(chr(63), "", $nombre_img);



			$this->titulo = strip_tags($this->titulo);
			$tit = new Titulo($this->titulo, $width, TIT_SECCION_FONT_COLOR, $nombre_img);
			if($this->link_titulo !== false) {
				$tit->setUnderline(true);
			}
			$tit->setImages(PREFIX_TITULO_SECCION);
			$img_titulo_src = $tit->getPath();
			$this->img_header = new Imagen($img_titulo_src, $this->titulo);
			if($this->link_titulo !== false) { //Si el titulo es un link, entonces metemos la imagen dentro de un link
				$titulo_seccion = new Link($this->link_titulo, "", $this->img_header->src);
			} else {
				$titulo_seccion = $this->img_header;
			}
			$html = str_replace("#TITULO#", $titulo_seccion->Display(), $html);
			// -- Fin creaci�n del titulo
		} else {
			if($this->link_titulo !== false) {
				$link = new Link($this->link_titulo, $this->titulo);
				$html = str_replace("#TITULO#", $link->Display(), $html);
			} else {
				$html = str_replace("#TITULO#", $this->titulo, $html);
			}
		}

		$html = str_replace("#ALIGN#", $this->align, $html);
		if($this->font_size == NORMAL_FONT_SIZE) {
			$html = str_replace("<#SIZE#>", "", $html);	
			$html = str_replace("</#SIZE#>", "", $html);
			$html = str_replace("#SIZE#", $this->font_size, $html);
		} else {
			$html = str_replace("#SIZE#", $this->font_size, $html);
		}

		$html_interior = parent::Display();

		$html =  str_replace("#COMPONENTES#", $html_interior, $html);

		return $html;
	}


}

?>
