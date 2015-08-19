<?php

class SBFactory {
	var $obj;
	function Factory(){

	}

	function create($type){
		$this->obj = new $type();
		return $this->obj;
	}


}

?>