<?php

class Formulario extends WapComponent {

	var $action;
	var $method;
	var $submit_caption;
	var $hidden;

	function Formulario($action, $submit_caption = "Enviar", $method = "get"){
	
		$this->template = "templates/formulario.tpl";
		$this->action = $action;
		$this->method = $method;
		$this->submit_caption  = $submit_caption;
		$this->hidden = array();
	}

	function addHidden($name, $value){
 		$blank = new FormElement();
		$blank->nombre = $name;
		$blank->value = $value;
		$this->hidden[] = $blank;
	
	}

	function Display(){
	
		$html = $this->_loadTemplate();

		$html = str_replace("#ACTION#", $this->action, $html);
		$html = str_replace("#METHOD#", $this->method, $html);
		$html = str_replace("#SUBMIT_CAPTION#", $this->submit_caption, $html);

		$elementos = parent::Display();

		if(!$this->_soportaXHTML()) {
			$post_fields = "";
			foreach($this->contenido as $item) {
				$post_fields .= is_object($item)?$item->getPostField():$item;
			}
			foreach($this->hidden as $item) {
				$post_fields .= is_object($item)?$item->getPostField():$item;
			}
			$html = str_replace("#POSTFIELDS#", $post_fields, $html);
		} else {
			foreach($this->hidden as $h) {
				$elementos .= $h->getHidden();	
			}
		}

		$html = str_replace("#COMPONENTES#", $elementos, $html);


		return $html;
	}



}
?>
