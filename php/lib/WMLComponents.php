<?php

/**
 * Clase Padre de todos los componentes 
 * Contiene el metodo "toString" y el "AddComponent".
 */
class Component{
	var $xmlObj;

	/**
	 * Devuelve la representación en XML del objeto
	 */
	function toString(){
		return $this->xmlObj->toString();
	}

	/**
	 * Agrega un componente al componente actual.
	 */
	function AddComponent($comp){
		$this->xmlObj->AddNode($comp->xmlObj);	
	}
}



/**
 * Componente TEXTO
 */
class Text extends Component{

	function Text($texto){
		$this->xmlObj = new TextNode($texto);	
	}
}

/**
 * Componente INPUT
 */
class Input extends Component{

	/**
	 * Constructor:
	 * 
	 * @param string  $name  	El nombre que va a tener el campo en el código
	 * @param string  $value 	El valor por defecto que tendrá el campo (Por defecto es vacío)
	 * @param enum    $type  	El tipo del campo, puede ser "text" para los campos normales, o "password" para los campos de password. 
	 * (Por defecto es "text)
	 * NOTA: Si el tipo del campo es "passoword" el formato debe ser "N" (mirar más abajo) 
	 * @param int  	  $maxlength    El largo máximo del contenido del campo. (Por defecto es 140)
	 * @param enum    $format       El formato de entrada que acepta el campo, 'M' = alfanumerico, 'N' = numerico (Por defecto es "M")
	 */
	function Input($name, $value = "", $type = "text", $maxlength = 140, $format = 'M' ){
		$this->xmlObj = new XMLNode("input", array("name" => $name));
	}
	
}

/**
 * Componenten SELECT
 */
class Select extends Component{

	/**
	 * Constructor:
	 * 
	 * @param   string   $name   	El nombre que tendrá el campo en el código
	 * @param   array    $options   Un array con las opciones, cada elemento del array, es un array asociativo, 
	 *	 			con  la clave "value" para el valor y la clave "text" para el label de la opción.
	 */
	function Select($name, $options = array()){
		$xmlSel = new XMLNode("select", array("name" => $name));
		foreach($options as $opt){
			$xmlOpt = new XMLNode("option", array("value" => $opt['value']));
			$xmlOpt->AddNode(new TextNode($opt['text']));
			$xmlSel->AddNode($xmlOpt);
		}
		$this->xmlObj = &$xmlSel;
	}
}

/**
 * Componente ANCHOR (link)
 */
class Anchor extends Component {
	
	/**
	 * Constructor:
	 * 
	 * @param  string  $text   El texto que se convertirá en el link.
	 */
	function Anchor($text,$attribs=array()){
		$this->xmlObj = new XMLNode("anchor",$attribs);
		$this->xmlObj->AddNode(new TextNode($text));
	}

}

/**
 * Componente A (link)
 */
class A extends Component {
	
	/**
	 * Constructor:
	 * 
	 * @param  string  $text   El texto que se convertirá en el link.
	 */
	function A($text,$attribs=array(), $img = ""){
		$this->xmlObj = new XMLNode("a",$attribs);
		$this->xmlObj->AddNode(new TextNode($text));
		if($img != "") {
			$this->xmlObj->AddNode(new XMLNode("img", array("src" => $img, "alt" => "")));	
		}
	}

}

/**
 * Componente P (Parrafo)
 */
class P extends Component {
	
	/**
	 * Constructor:
	 * 
	 */
	function P($atributos = array()){
		$this->xmlObj = new XMLNode("p", $atributos);
	}

}

/**
 * Componenten GO
 */
class Go extends Component {

	/**
	 * Constructor:
	 *
	 * @param  string  $href    	 El destino hacia donde redirecciona este elemento. Puede ser una URL, URI o un ID dentro de la misma página.
	 * @param  enum    $method  	 El método con el que se enviarán los datos, puede ser "post" o "get" (Por defecto es GET)
	 * @param  enum    $send_referer Si value "true" se envía la URI del referer al servidor. (Por defecto es "false")
	 */
	function Go($href, $method = "post", $send_referer = "false"){
		$this->xmlObj = new XMLNode("go", array("method" => $method, "href" => $href/*, "sendreferer" => $send_referer*/));	
	}

}

/**
 * Componentes POSTFIELD
 * 
 * El postfield representa a los elementos a ser enviados al servidor mediante el método elegido (post o get).
 * Para utilizar el valor de variables dentro de la tarjeta, se utiliza la sintaxis:
 * 
 * <postfield name="foo" value="$(varname)" />
 */
class PostField extends Component {

	/**
	 * Constructor:
	 *
	 * @param  string  $name    El nombre con el que se enviará el campo al servidor.
	 * @param  string  $value   El valor que se envía al servidor.
	 */
	function PostField($name, $value){
		$this->xmlObj = new XMLNode("postfield", array("name" => $name, "value" => $value));
	}
}


class Imagen extends Component{

	function Imagen($src, $alt = "") { 
		$this->xmlObj = new XMLNode("img", array("src" => $src, "alt" => $alt));
	}
}
/**
 * Componentes SETVAR
 * 
 * El setvar sirve para setear el valor de las variables a usar en el archivo satml.
 * 
 * <setvar name="var" value="$(value)" />
 */
class SetVar extends Component {

	/**
	 * Constructor:
	 *
	 * @param  string  $name    El nombre con el que se enviará el campo al servidor.
	 * @param  string  $value   El valor que se envía al servidor.
	 */
	function SetVar($name, $value){
		$this->xmlObj = new XMLNode("setvar", array("name" => $name, "value" => $value));
	}
}
?>
