<?php

class Profile extends WapComponent{

	var $img_path;
	var $link;
	var $txt;
	var $foot;
	var $extra_class;
	var $extra_contenido;

	function Profile($img, $link, $txt){
		$this->template = "templates/profile.tpl";
		$this->img_path = $img;
		$this->link = $link;
		$this->txt = $txt;
		
		$this->extra_contenido = null;
		$this->foot = null;
		$this->extra_class = null;
	}

	function setExtraContenido($c){
		$this->extra_contenido = $c;
	}
	function setFooter($f){
		$this->foot = $f;
	}

	function setExtraClass($c) {
		$this->extra_class = $c;
	}
	
	
	function Display(){		
		$contenidoTpl = $this->_loadTemplate();	
		$contenido = "";
		$contenido_extra = "";
		if($this->img_path != "") {
			$img = new Imagen($this->img_path);
			$contenidoTpl = str_replace("#IMG-PROF#", $img->Display(), $contenidoTpl);			
		} else {
			$contenidoTpl = str_replace("#IMG-PROF#", "", $contenidoTpl);			
		}
		
		if($this->extra_class != null) {
			$contenidoTpl = str_replace("#EXTRA-CLASS#", $this->extra_class, $contenidoTpl);
		} else {
			$contenidoTpl = str_replace("#EXTRA-CLASS#", "", $contenidoTpl);
		}
		
		if($this->link != null) {
			$contenido = $this->link->Display()."<br/>";
		}
		
		
		$contenido .= $this->txt;
		
		if($this->extra_contenido != null){
			$contenido_extra = $this->extra_contenido;
		}
		
		if($this->foot != null) {
			$contenido .= "<br/>".$this->foot->Display();
		}
		
		$contenidoTpl = str_replace("#CONTENIDO#", $contenido, $contenidoTpl);
		$contenidoTpl = str_replace("#EXTRA-CONTENIDO#", $contenido_extra, $contenidoTpl);
		
		return $contenidoTpl;
	}


}


?>
