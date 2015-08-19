<?php

class FormElement extends WapComponent{


	var $nombre;
	var $id;
	var $label;
	var $value;


	/**
	 * Esto se da solo en el caso de que el celular no soporte  XHTML, así que lo generamos acá....
	 */
	function getPostField(){
		$valor = ($this->value)?$this->value:'$('.$this->nombre.')';
		return '<postfield name="'.$this->nombre.'" value="'.$valor.'" />';
	}

	function getHidden(){
		return '<input type="hidden" name="'.$this->nombre.'" value="'.$this->value.'" />';
	}

}


?>
