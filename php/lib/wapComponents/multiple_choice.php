<?php
include_once(dirname(__FILE__)."/choise.php");

class MultipleChoise extends FormElement {

	var $selected_value;
	var $options;
	var $titulo;
	

	function MultipleChoise($nombre, $titulo = ""){
	
		$this->template = "templates/multiple_choise.tpl";
		$this->nombre = $nombre."[]";
		$this->options = array();
		$this->titulo  = $titulo;
	}
	
	function AddChoise($text, $value, $checked = false){
		$this->options[] = array("label" => $text, "value" => $value, "checked" => $checked);
	}


	function Display(){

		$html = $this->_loadTemplate();

		$html = str_replace("#NOMBRE#", $this->nombre, $html);
		$html = str_replace("#TITULO#", $this->titulo, $html);
		
		$html_choises = "";
		
		foreach ($this->options as $opt){ 
			$ch = new Choise($opt['value'], $opt['label'], $opt['checked']);
			$html_choises .= $ch->Display()."<br/>";
		}
		$html_choises = str_replace("#NOMBRE#", $this->nombre, $html_choises);
		
		$html = str_replace("#CONTENT#", $html_choises, $html);

		//$html = str_replace("#SELECTED_VALUE#", $this->selected_value, $html);
		$html = str_replace("#OPTIONS#", parent::Display(), $html);

		return $html;
	
	}

}


?>
