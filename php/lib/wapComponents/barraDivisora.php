<?php

class BarraDivisora extends WapComponent{

	var $barra;

	function BarraDivisora(){
		$this->barra = new Imagen("images/lin_2px.gif");	
	}

	function Display(){
		return $this->barra->Display();
	} 
}


?>
