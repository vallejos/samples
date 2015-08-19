<?php
include_once(dirname(__FILE__)."/option.php");

class Select extends FormElement {

	var $selected_value;

	function Select($label, $nombre, $id = ""){
	
		$this->template = "templates/select.tpl";
		$this->nombre = $nombre;
		$this->id = ($id)?$id:$nombre;
		$this->label = $label;
		$this->is_multiple = $multiple;
	}


	function AddOption($value, $text, $selected = ""){
		if($selected) {
			$this->selected_value = $value;
		}
		$this->AddComponent(new Option($value, $text, $selected));
	}

	function Display(){

		$html = $this->_loadTemplate();

		$html = str_replace("#LABEL#", $this->label, $html);
		$html = str_replace("#NOMBRE#", $this->nombre, $html);
		$html = str_replace("#ID#", $this->id, $html);

		$html = str_replace("#SELECTED_VALUE#", $this->selected_value, $html);
		$html = str_replace("#OPTIONS#", parent::Display(), $html);

		return $html;
	
	}

}


?>
