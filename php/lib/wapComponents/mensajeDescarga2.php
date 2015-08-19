<?php

class MensajeDescarga2 extends WapComponent{

	var $href_si;
	var $href_no;
	var $tipo;
	var $idCont;

	function MensajeDescarga2($href_si, $href_no, $tipo, $idCont = null){
		$this->template = "templates/mensajeDescarga2.tpl";
		$this->href_si = $href_si;
		$this->href_no = $href_no;
		$this->tipo = $tipo;
		$this->idCont = $idCont;
	}

	function Display(){
		$contenidoTpl = $this->_loadTemplate();	
		global $ua;
		global $db;
		
		$html = str_replace("#RESPUESTASI#", $this->href_si, $contenidoTpl);

		$datos = obtenerDatosCompra($this->tipo);
		if(soportaContenidoPorTipo($db, $ua, $this->tipo)) {
			if($this->idCont) {
				$datos_cont = obtenerDatosContenido(null, $this->idCont, $datos['nombre_cont'] == "Juego");
				if($datos_cont['screenshots']) {
					$imgPreview = new Imagen("http://www.wazzup.com.uy/".$datos_cont['screenshots'], $datos_cont['nombre']);
					$html = str_replace("#IMG_PREVIEW#", $imgPreview->Display(), $html);
				} else {
					$html = str_replace("#IMG_PREVIEW#", "", $html);
				}
				$html = str_replace("#NOMBRE_CONTENIDO#", $datos_cont['nombre'], $html);
			}
	
			$html = str_replace("#IMG_PREVIEW#", "", $html);
			$html = str_replace("#RESPUESTANO#", $this->href_no, $html);
			$html = str_replace("#PRECIO#", $datos['precio'], $html);
			$html = str_replace("#NOMBRETIPO#", $datos['nombre_cont'], $html);
		} else {
			$html = "Lo sentimos, pero su modelo de móvil no soporta este tipo de contenidos";
		}
		
		return $html;
	}


}


?>
