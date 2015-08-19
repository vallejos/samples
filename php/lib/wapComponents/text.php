<?php

/**
 * Representa el link en la página
 */
class Text extends WapComponent{

	var $href = "";
	var $img = null;
	var $img_pos;
	var $clase;
	var $extra_text;
	var $is_hot = false;
	var $is_new = false;
	var $is_hit = false;
	var $mode = null;


	/**
	 * Constructor.
	 *
	 * @param string $href  El destino del link
	 * @param string $txt   El texto que se mostrará como link
	 * @param string $img_src  El objeto imagen, que será el link, si tiene texto también, entonces el texto queda abajo de la imagen
	 * @param int    $img_pos  La posicion de la imagen del link con respecto al texto, valores: LEFT_SIDE, UP_SIDE, BOTTOM_SIDE, RIGHT_SIDE
	 */
	function Text($href, $txt = "", $img_src = null, $img_pos = LEFT_SIDE, $clase = "", $extra_text = ""){
		$this->template = "templates/texto.tpl";	
		$this->href = $href;
		$this->img_pos = $img_pos;

		// patched by kmS
		if ($clase != null) $this->clase = $clase;
        else $this->clase = "kmS";

        $this->extra_text = $extra_text;
		if($txt) {
			$this->AddComponent($txt);	
		}

		if($img_src) {
			$this->img = new Imagen($img_src, $txt);
		}
	}

	function removeText(){
	
		foreach($this->contenido as $i => $c){
			if(!is_object($c)) {
				unset($this->contenido[$i]);
			}	
		}
	}

	function SetText($txt){
		$this->AddComponent($txt);	
	}

	function getText(){
		$textos = array();
		foreach($this->contenido as $comp) {
			if(!is_object($comp)) {
				$textos[] = $comp;
			}
		}
		return $textos;
	}

	function setHot($estado){
		$this->is_hot = $estado;
	}

	function setNew($estado){
		$this->is_new = $estado;
	}

	function setHit($estado){
		$this->is_hit = $estado;
	}

	function Display(){
		
		$html = $this->_loadTemplate();

		$html = str_replace("#HREF#", $this->href, $html);
		$html = str_replace("#CLASE#", $this->clase, $html);
		$html = str_replace("#CLASE#", "", $html); //Por si no le seteamos una clase

		$html_interior = "";
		if($this->img) {
			switch($this->img_pos) {
				case LEFT_SIDE:
					$html_interior .= $this->img->Display();	
					$html_interior .= parent::Display();
				BREAK;
				case TOP_SIDE:
					$html_interior .= $this->img->Display();	
					$html_interior .= "<br/>".parent::Display();
				break;
				case RIGHT_SIDE:
					$html_interior .= parent::Display();
					$html_interior .= $this->img->Display();	
				break;
				case BOTTOM_SIDE:
					$html_interior .= parent::Display();
					$html_interior .= "<br/>".$this->img->Display();	
				break;
			}
		} else {
			$html_interior .= parent::Display();
		}

		$html =  str_replace("#CONTENIDO#", $html_interior, $html);
		$html .= $this->extra_text;
		if($this->is_new) {
			$imgNew = new Imagen(IMAGEN_NEW, TITLE_NEW);
			$html .= $imgNew->Display();	
			if($this->_soportaXHTML()) {
				$html .= "<br class=\"clear\" />";	
			} else {
				$html .= "<br/>";
			}
		}
		if($this->is_hot) {
			$imgHot = new Imagen(IMAGEN_HOT, TITLE_HOT);
			$html .= $imgHot->Display();	
			//if(!$this->_soportaXHTML()) {
				$html .= "<br/>";	
			//}
		}
		if($this->is_hit) {
			$imgHit = new Imagen(IMAGEN_HIT, TITLE_HIT);
			$html .= $imgHit->Display();	
			if($this->_soportaXHTML()) {
				$html .= "<br class=\"clear\" />";	
			} else {
				$html .= "<br/>";
			}
		}


		return $html;
	}
}



?>
