<?php

class MensajeDescargaAlerta extends WapComponent{

	var $href_si;
	var $href_no;
	var $precio;

	function MensajeDescargaAlerta($href_si, $href_no, $precio){
		$this->template = "templates/mensajeDescargaAlerta.tpl";
		$this->href_si 	= $href_si;
		$this->href_no 	= $href_no;
		$this->precio 	= $precio;
	}

	function Display(){
		$contenidoTpl = $this->_loadTemplate();	
	
		$html = str_replace("#RESPUESTASI#", $this->href_si, $contenidoTpl);
		$html = str_replace("#RESPUESTANO#", $this->href_no, $html);
		$html = str_replace("#PRECIO#", $this->precio, $html);
		
		return $html;
	}
}
?>
