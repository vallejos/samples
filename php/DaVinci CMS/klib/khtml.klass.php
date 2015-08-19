<?php

include_once("khtml.config.php");

// je! by kmS ofc ;) 
class khtml {
	var $khtml;
	var $head;
	var $css;
	var $js;
	var $body;
	var $modules = array("img");


	// CONSTRUCTOR :: init basic elements
	function khtml() {
		foreach ($this->modules as $module) {
			$mod = KHTML_CLASS_DIR."/".$mod."_khtml.class.php";
			if (!file_exist($mod)) die("module not found: $module: $mod");
			include_once($mod);
		}

		$this->khtml .= "<html><head>%HEAD% %CSS% %JS%</head><body>%BODY%</body></html>";
	}

	function display() {
		str_replace("%HEAD%", $this->head, $this->khtml);
		str_replace("%CSS%", $this->css, $this->khtml);
		str_replace("%JS%", $this->js, $this->khtml);
		str_replace("%BODY%", $this->body, $this->khtml);
		echo $khtml;
	}


	function string_to_array() {

	}


	function image_tag($path, $params) {
		$options = isset($params['params']) ? self::string_to_array($params['params']) : array();

		$img = new img($options);

		$html = "<img src='$img->path' {$img->alt} {$img->class} {$img->id} />";


		return $html;
	}


	function link_to($text, $link) {

	}

	function email_to($text, $link) {

	}



}


?>