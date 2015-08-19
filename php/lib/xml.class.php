<?php

class TextNode {
	var $text;

	function TextNode($t){
		$this->text = $t;
	}

	function toString(){
		return $this->text;
	}

}

class XMLNode {

	var $nombre;
	var $atributos;
	var $contenido;

	function XMLNode($nombre, $atributos = array()){

		$this->nombre = $nombre;
		$this->atributos = $atributos;
		$this->contenido = array();
	}


	function AddNode($nodo){
		$this->contenido[] = $nodo;
	}


	function toString(){
		$str = "<".$this->nombre;
		foreach($this->atributos as $nombre => $valor){
			$str .= " $nombre=\"$valor\"";
		}

		if(count($this->contenido) > 0) {
			$str .= ">";
			foreach($this->contenido as $nodo){
				$str .= $nodo->toString();
			}
			$str .= "</".$this->nombre.">";
		} else {
			$str .= "/>";
		}
		return $str;
	}
}


class XMLObject {
	var $nombre;
	var $contenido;
	var $atributos;



	function XMLObject($main, $atributos = array(), $contenido = array()){
		$this->nombre = $main;
		$this->atributos = $atributos;
		$this->contenido = $contenido;
	}

	function AddNode($nodo){
		$this->contenido[] = $nodo;
	}

	function toString(){
		/*
		$str  = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
		*/
		$str = "";
		$str .= "<".$this->nombre;

		foreach($this->atributos as $nombre => $valor) {
			$str .= " ".$nombre."=\"$valor\"";
		}
		$str .= ">";

		foreach($this->contenido as $nodo){
			$str .= $nodo->toString();
		}

		$str .= "</".$this->nombre.">";

		return $str;
	}
}



?>
