<?php

class Choise extends WapComponent {

	var $value;
	var $text;
	var $selected;

	function Choise($value, $text, $selected = ""){
		$this->value = $value;	
		$this->text = $text;
		$this->template = "templates/choise.tpl";
		$this->selected = $selected;
	}

	function Display(){
	
		$html = $this->_loadTemplate();

		$html = str_replace("#VALUE#", $this->value, $html);
		$html = str_replace("#LABEL#", $this->text, $html);
		if($this->selected) {
			$html = str_replace("#CHECKED#", 'checked="checked"', $html);
		} else {
			$html = str_replace("#CHECKED#", '', $html);
		}

		return $html;
	}


}


?>
