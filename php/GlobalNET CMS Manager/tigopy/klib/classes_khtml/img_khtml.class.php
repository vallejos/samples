<?php 

class img {
	var $path;
	var $alt;
	var $width;
	var $height;
	var $id;
	var $class;

	function img($options) {
		$this->path = $options["path"];
		$this->alt = $options["alt"];
		$this->width = $options["width"];
		$this->height = $options["height"];
		$this->id = $options["id"];
		$this->class = $options["class"];
	}

}

?>