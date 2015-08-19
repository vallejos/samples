<?php
//if(!class_exists("FormElement")) {
	//include_once(dirname(__FILE__."/formElement.php"));
///}

class Input extends FormElement {

	var $tipo;
	var $ancho;
	var $value;

	function Input($label, $nombre, $id = "", $tipo = "text"){
	
		$this->template = "templates/input.tpl";
		$this->nombre = $nombre;
		$this->label = $label;
		$this->id = ($id)?$id:$nombre; //Si no le pasamos un id entonces tiene el mismo que el nombre
		$this->tipo = $tipo;
		$this->value = "";
	}

	function setNoEditable() {
		$this->template = "templates/input_static.tpl";
	}
	function setEditable() {
		$this->template = "templates/input.tpl";
	}
	function setAncho($a){
		$this->ancho = $a;
	}
	
	function setValue($v){
		$this->value = $v;
	}
	
	
	function Display(){
	
		$html = $this->_loadTemplate();

		if($this->label == "") {		
			$html = str_replace("<label>#LABEL#</label>", "", $html);
		}
		$html = str_replace("#LABEL#", $this->label, $html);
		$html = str_replace("#ID#", $this->id, $html);
		$html = str_replace("#NOMBRE#", $this->nombre, $html);
		$html = str_replace("#TIPO#", $this->tipo, $html);
		$html = str_replace("#ANCHO#", $this->ancho, $html);
		$html = str_replace("#VALUE#", $this->value, $html);

		return $html;
	}

}

?>
