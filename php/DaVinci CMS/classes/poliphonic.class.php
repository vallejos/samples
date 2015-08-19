<?php

/**
 *
 *
 *
 *
 *
 */

class poliphonic {
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
	var $providercode;
	var $cla;
	var $expirydate;
	var $activatedate;
	var $file_webpreview = array();
	var $file_objects = array();

	function poliphonic () {
	}

	function set($name, $value) {
		if (!isset($this->$name)) return FALSE;
		else $this->$name = $value;
	}

}

?>