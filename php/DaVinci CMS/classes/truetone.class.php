<?php

/**
 *
 *
 *
 *
 *
 */

class truetone {
	var $name;
	var $provider;
	var $royalty;
	var $cat;
	var $subcat;
	var $code;
	var $operator;
	var $searchkeywords;
	var $musiclabel;
	var $movie;
	var $album;
	var $artist;
	var $file_webpreview = array();
	var $file_wappreview = array();
	var $file_objects = array();

	function truetone () {
	}

	function set($name, $value) {
		if (!isset($this->$name)) return FALSE;
		else if (is_array($this->$name)) array_push($this->$name, $value);
		else $this->$name = $value;
	}

}

?>