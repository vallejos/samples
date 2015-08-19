<?php

class SBFactory {
	var $obj;
	function SBFactory(){

	}

	function create($type){
		$this->obj = new $type();
		return $this->obj;
	}


}

?>