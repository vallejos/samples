<?php

/**
 * Define los modos de la WAP, estas constantes pueden usarse para forzar a la wap
 * que se muestre de una forma especifica.
 *
 */
define("WML_MODE",   1); //Se fuerza a la WAP a que se muestre en su versión WML
define("XHTML_MODE", 2); //Se fuerza a la WAP a que se muestre en su versión XHTML
define("MIXED_MODE", 3); //Se fuerza a la WAP a que eliga su versión, dependiendo del celular que lo soporte.

/**
 * La clase base de todos los componentes
 */
class WapComponent{

	var $contenido = array();
	var $template;

	/**
	 * Agrega un componente al actual.
	 * Puede pasarsele cualquier tipo y cantidad de parametros, pueden ser objetos (en esta caso
	 * deberían ser objetos que hereden de WapComponent) o texto.
	 * También se le puede pasar un array de elementos.
	 * 
	 * Ej.: $obj->AddComponent(array($obj1, $obj2, "texto"), "texto 2", $obj3);
	 *
	 */
	function AddComponent(){
		for($i = 0; $i < func_num_args(); $i++) {
			if(is_array(func_get_arg($i))) {
				foreach(func_get_arg($i) as $item) {
					$this->contenido[] = $item;
				}
			} else {
				$this->contenido[] = func_get_arg($i);
			}
		}
	}


	/**
	 * (Privado)
	 * Chequea los ACCEPT HEADERS para ver si el browser soporta XHTML
	 *
	 * @return mixed  FALSE si no soporta XHTML MP o el content-type soportado en caso de que lo acepte
	 */
	function _soportaXHTML(){
		//Hack para el DRUTT de ANCEL que SOLO SOPORTA XHTML... (n00bs...)
		$allHeaders = getallheaders();

		if(ANCEL_DRUTT == 1 || (isset($allHeaders["DRUTT"]) && strtoupper($allHeaders["DRUTT"]) == "SI")) {
			return "application/vnd.wap.xhtml+xml";
		}

		$headers = $_SERVER['HTTP_ACCEPT'];
		global $ua;
		global $CELULARES_WAP;

		if(defined('WAP_MODE')) {
			if(WAP_MODE == WML_MODE) {
				return false;
			}
		}
		
		//primero chequeamos si es una excepción....
		$ua_cortado = explode("/", $ua);
		$ua_cortado = $ua_cortado[0];
 		if(in_array($ua_cortado, $CELULARES_WAP)) {
 			return false;	
 		}
 		
 		if(strstr($headers, "application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5")) {
     		return "text/html";
 		} else if(strstr($headers, "vnd.wap.xhtml+xml") || strstr($headers, "*/*") ) {
			return "application/vnd.wap.xhtml+xml";
		} else if( strstr($headers, "xhtml+xml")) {
			return "application/xhtml+xml";	
		} else if (strste($headers, "application/xhtml+xml")) {
		    return "application/xhtml+xml";
		} else {
			return false;	
		}
	}

	/**
  	 * Metodo privado (o debería ser si php4 lo permitiera...): 
	 * Carga el contenido del template y lo retorna
	 */
	function _loadTemplate(){
		global $CELULARES_WAP;
		global $ua;
		$reemplazar_doble_pesos = false;
		if($this->_soportaXHTML() && !in_array($ua, $CELULARES_WAP)) {

			if(!isset($this->mode) || $this->mode == XHTML_MODE || $this->mode == MIXED_MODE || $this->mode == "") {
				$temp = str_replace(".tpl", "_xhtml.tpl", $this->template);
			} else {				
				$temp = $this->template;
			}			
		} else {
	
			//$temp = str_replace(".tpl", ".tpl", $this->template);
			if($this->mode == XHTML_MODE || $this->_soportaXHTML()) {
				$temp = str_replace(".tpl", "_xhtml.tpl", $this->template);
			} else {
				
				$temp = $this->template;
			}
		}
		$temp = dirname(__FILE__)."/".$temp;
		$fp = fopen($temp, "r");
		$contenido = fread($fp, filesize($temp));
		fclose($fp);
		return $contenido;	
	}



	/**
	 * Recorre a todos los componentes que hay dentro del actual y les ejecuta el metodo "display", 
	 * siempre que sean un objeto, de lo contrario, simplemente agrega el valor del componente al 
	 * resultado
	 */
	function Display(){
		$html = "";

		foreach($this->contenido as $cont) {
			if(is_object($cont)) {
				$cont->mode = $this->mode;
				$html .= $cont->Display();
			} else {
				$html .= $cont;
			}
			
		}
		return $html;
	}



}


?>
