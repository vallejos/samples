<?php

/**
 * KTEMPLATE v1.0
 * by kmS
 *
 * Basic template
 */

class Template {
	var $filename;
	var $content;
	var $vars;
	var $DEBUG_LVL = 1;
	var $DEBUG_INFO;

	// constructor
	function Template($filename) {
		$this->filename = "$filename.template.khtml";

		if (!defined("TEMPLATE_DIR")) {
			die("CRITICAL: TEMPLATE_DIR NO DEFINIDO: ".TEMPLATE_DIR);
		} else {
			if (!file_exists(TEMPLATE_DIR."/$this->filename")) {
				die("CRITICAL: TEMPLATE NO EXISTE: ".TEMPLATE_DIR."/$this->filename");
			} else {
				// cargo template
				$this->content = file_get_contents(TEMPLATE_DIR."/$this->filename");
				if ($this->DEBUG_LVL) $this->debug_add("Template loaded: $this->filename");

				$this->vars = array();
				if ($this->DEBUG_LVL) $this->debug_add("Template vars initialized.");
			}
		}
	}


	function set_var($name, $value) {
		$name = strtoupper($name);
		$this->content = str_replace("%$name%", $value, $this->content);
		if ($this->DEBUG_LVL) $this->debug_add("var set: $name => $value");
	}

	function assign_vars($arr_vars) {

	}


	// display template
	function display($print=FALSE) {
		if ($print !== TRUE) return $this->content;
		else {
			// header html
			header("Expires: Mon, 01 Febl 1977 21:25:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache"); // HTTP/1.0
			header("Content-Type: text/html");
			if ($this->DEBUG_LVL) $this->debug_add("HTML headers sent");

			echo $this->content;
		}
		if ($this->DEBUG_LVL) $this->debug_add("Template content displayed");
	}


	// basic debug method
	function debug_add($msg) {
		$this->DEBUG_INFO .= "[".date("Y-m-d H:i:s")."] ".$msg;
	}


}



?>