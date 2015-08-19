<?php

class ItemDestacado extends WapComponent{

	var $extra_style;

	function ItemDestacado(){
		$this->template = "templates/itemDestacado.tpl";

		//Si tiene links como parametros, los agregamos directamente
		for($i = 0; $i < func_num_args(); $i++) {
			$arg = func_get_arg($i);
			if(is_array(func_get_arg($i))) {
				foreach($arg as $argumento) {
					$this->AddComponent($argumento);	
				}	
			} else {
				$this->AddComponent($arg);
			}
		}
	}

	function SetStyle($s){
		$this->extra_style = $s;
	}

	function Display(){
	
		$html = $this->_loadTemplate();

		foreach($this->contenido as $i => $comp) {
			$html = str_replace("#ITEM".($i+1)."#", $comp->Display(), $html);
		}
	
		$html = str_replace("#ITEM1#", "", $html);
		$html = str_replace("#ITEM2#", "", $html);
		$html = str_replace("#ITEM3#", "", $html);
		$html = str_replace("#EXTRA_STYLE#", $this->extra_style, $html);
		$html = str_replace("#EXTRA_STYLE#", "", $html);

		return $html;
	
	}

}


?>
