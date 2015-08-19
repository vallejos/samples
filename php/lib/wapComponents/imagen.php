<?php

/**
 * Representa la una imagen el la WAP
 *
 */
class Imagen extends WapComponent {

	var $src;
	var $alt;
	var $estilo;
	var $width;


	/**
	 * Constructor
	 *
	 * @param string $src  El path a la imagen
	 * @param string $alt  El valor de atributo ALT de la imagen, por defecto es vacío
	 * @param string $estilo Un estilo especial a ponerle al SPAN que engloba la imagen.
	 */
	function Imagen($src, $alt = "", $estilo = ""){
		$this->width = 0;
		$this->src = $src;
		$this->alt = str_replace("<br/>", "", $alt);		
		$this->estilo = $estilo;
		$this->template = "templates/imagen.tpl";
	}

	function setWidth($w){
		$this->width = $w;
	}
	
	

	function Display(){
/*
		$fp = fopen(dirname(__FILE__)."/".$this->template, "r");
		$html = fread($fp, filesize(dirname(__FILE__)."/".$this->template));
		*/
		$html = $this->_loadTemplate();

		$html = str_replace("#SRC#", $this->src, $html);
		$html = str_replace("#ALT#", $this->alt, $html);
		$html = str_replace("#CLASE#", $this->estilo, $html);
		if($this->width != 0) {
			$html = str_replace("#WIDTH#", $this->width, $html);
		} 
		$html = str_replace("width=\"#WIDTH#\"", "", $html); //Borramos todo rastro del atributo "width" si no tenemos un ancho seteado 

		return $html;
	}

}




?>
