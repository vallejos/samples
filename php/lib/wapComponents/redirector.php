<?php

class Redirector extends WapComponent  {

	var $timeout;
	var $href;
	var $titulo;
	var $texto;

	function Redirector($href, $timeout, $titulo, $texto){
		$this->template = "templates/redirector.tpl";
		$this->timeout = $timeout;
		$this->href = $href;
		$this->titulo = $titulo;
		$this->texto = $texto;
	}
	
	function WriteHeaders(){
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache"); // HTTP/1.0
	
		header("Content-type: text/vnd.wap.wml");
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">';
		echo "\n";
	}

	function Display(){
		$temp = dirname(__FILE__)."/".$this->template;
		$fp = fopen($temp, "r");
		$html = fread($fp, filesize($temp));
		fclose($fp);
		
		$html = str_replace("#URL#", $this->href, $html);
		$html = str_replace("#TEXTO#", $this->texto, $html);
		$html = str_replace("#TITULO#", $this->titulo, $html);
		/*
		if(!$this->_soportaXHTML()) {
			$timeout = $this->timeout * 10;	
		} else {
			$timeout = $this->timeout;	
		}*/
		$timeout = $this->timeout;
		$html = str_replace("#TIMEOUT#", $timeout, $html);
		
		return $html;
	}
}


?>
