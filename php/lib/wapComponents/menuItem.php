<?php

class MenuItem extends WapComponent{

	var $is_new = false;
	var $is_hot = false;
	var $is_hit = false;
	var $extra_text = "";

	function MenuItem($img, $text, $href = "", $extra_text = ""){
		if (($img !=  "") && ($img !== NULL)) $this->AddComponent(new Imagen($img));
		if($href != "") {
			$this->AddComponent(new Link($href, $text));
		} else {
			$this->AddComponent($text);
		}
		//$this->AddComponent( new Link($href, $text));
		$this->template = "templates/menuItem.tpl";
		$this->extra_text = $extra_text;
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

		$codigo_interior = "";
		foreach($this->contenido as $comp){
			$codigo_interior .= is_object($comp)?$comp->Display():$comp;
		}


		if($this->is_new) {
			$imgNew = new Imagen(IMAGEN_NEW, TITLE_NEW);
			$codigo_interior .= $imgNew->Display();
		}
		if($this->is_hot) {
			$imgHot = new Imagen(IMAGEN_HOT, TITLE_HOT);
			$codigo_interior .= $imgHot->Display();
		}
		if($this->is_hit) {
			$imgHit = new Imagen(IMAGEN_HIT, TITLE_HIT);
			$codigo_interior .= $imgHit->Display();
		}
		$codigo_interior .= $this->extra_text;

		$html = str_replace("#CONTENIDO#", $codigo_interior, $html);
		return $html;
	}


}

?>
