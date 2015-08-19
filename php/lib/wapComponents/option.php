<?php

class Option extends WapComponent {

	var $value;
	var $text;
	var $selected;

	function Option($value, $text, $selected = ""){
		$this->value = $value;	
		$this->text = $text;
		$this->template = "templates/option.tpl";
		$this->selected = $selected;
	}

	function Display(){
	
		$html = $this->_loadTemplate();

		$html = str_replace("#VALUE#", $this->value, $html);
		$html = str_replace("#TEXT#", $this->text, $html);
		if($this->selected) {
			$html = str_replace("#SELECTED#", 'selected="selected"', $html);
		} else {
			$html = str_replace("#SELECTED#", '', $html);
		}

		return $html;
	}


}


?>
