<?php
include_once(dirname(__FILE__)."/konvert.php");


/**
 * La pagina WAP. El componente principal, que contrendr� el resto de los
 * elementos de la p�gina
 */
class Pagina extends WapComponent{

	var $titulo = "";
	var $conn;
	var $mode;
	var $marca_blanca;
	var $extras_logos_tpl;
	var $extras_pie_tpl;
	var $css_src;

	function Pagina($titulo ){
		$this->template = "templates/pagina.tpl";
		$this->titulo = $titulo;
		$this->mode = MIXED_MODE;
		$this->marca_blanca = false;


		if(defined("CSS_SRC_FILE")) {
			$this->css_src = CSS_SRC_FILE;
		} else {
			$this->css_src = "estilos.css";
		}
	 	$this->extras_logos_tpl = "logos_extras.tpl";
	    $this->extras_pie_tpl = "pie_extras.tpl";

		$this->conn = new coneXion("Web", true);
	}



	function setMarcaBlanca(){
		$this->marca_blanca = true;
	}

	function setStyleSheet($src = ""){

		if($src != "") {
			$this->css_src = $src;
		}

	}


  	function loadExtraTemplates($f){
		global $CELULARES_WAP;
		global $ua;
		$reemplazar_doble_pesos = false;

		$path = $_SERVER['SCRIPT_NAME'];
		$path = explode("/", $path);
		unset($path[count($path) - 1]);
		$path = implode("/", $path);



		$tpl_filename = dirname(__FILE__);
		if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
			//$tpl_filename .= "/../";
		}
		$tpl_filename = $_SERVER['DOCUMENT_ROOT'].$path."/extras/".$f;


		if($this->_soportaXHTML() && !in_array($ua, $CELULARES_WAP)) {

			if($this->mode == XHTML_MODE || $this->mode == MIXED_MODE) {
				$temp = str_replace(".tpl", "_xhtml.tpl", $tpl_filename);
			} else {
				$temp = $tpl_filename;
			}
		} else {
			//$temp = str_replace(".tpl", ".tpl", $this->template);
			if($this->mode == XHTML_MODE ) {
				$temp = str_replace(".tpl", "_xhtml.tpl", $tpl_filename);
			} else {
				$temp = $tpl_filename;
			}
		}
		//$temp = dirname(__FILE__)."/".$temp;
		$contenido = "";
		if(file_exists($temp) && filesize($temp) > 0 ) {
			$fp = @fopen($temp, "r");
			if($fp) {
				$contenido = fread($fp, filesize($temp));
				fclose($fp);
			}
		}
		return $contenido;
	}
	/**
	 * Manda los headers HTTP  de la WAP y escribe el cabezal XML y el Doc-Type
	 */
	function WriteHeaders(){
		global $CELULARES_WAP;
		global $ua;

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache"); // HTTP/1.0



		if(!($ct = $this->_soportaXHTML()) || $this->mode == WML_MODE) {
			header("Content-type: text/vnd.wap.wml");
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">';
//			echo '<!DOCTYPE WML PUBLIC "-//WAPFORUM//DTD WML 1.0//EN" "http://www.wapforum.org/DTD/wml.xml">';
			echo "\n";

		} else {
			header("Content-type: ".$ct);

			//ISO-8859-1
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">';
		}


	}

	function loadStyleSheet(){
		$txt = "";
		$path = $_SERVER['SCRIPT_NAME'];
		$path = explode("/", $path);
		unset($path[count($path) - 1]);
		$path = implode("/", $path);
		$s_file = dirname(__FILE__);
		if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
			$s_file .= "/../../".$path;
		} else {
			$s_file = $_SERVER['DOCUMENT_ROOT'].$path;
		}
		$s_file .= "/estilos/".$this->css_src;

		$fp = fopen($s_file, "r");
		if($fp) {
			$txt = fread($fp, filesize($s_file));
			fclose($fp);
		}
		return $txt;
	}

	function forceWMLMode(){

		$this->mode = WML_MODE;
	}

	function forceXHTMLMode(){
		$this->mode = XHTML_MODE;
	}

	function forceMIXEDMode(){
		$this->mode = MIXED_MODE;
	}

	function Display(){

		global $ua; //utilizamos la variable global "$ua" que se declara en el archivo "getCelularHeader.php"
		global $CELULARES_PESO_SIMPLE;

		$html = $this->_loadTemplate();
		$estilos = $this->loadStyleSheet();
		$html = str_replace("#ESTILOS#", $estilos, $html);
		$html = str_replace("#BLACKBERRY_STYLES#", '<link rel="stylesheet" type="text/css" media="handset" href="./estilos/estilos.css" />', $html);

		$tipo_cabezal = "chico"; //Por si el modelo no es soportado o alguna otra cosa, asumimos que el m�s chico de todos se puede ver
		if(miscFunctions::soportaAnchoMayor($this->conn->db, $ua)) {
			$tipo_cabezal = "grande";
			$html = str_replace("#ANCHO_PANTALLA#", ANCHO_PANTALLA_GRANDE, $html);
		}

		if(miscFunctions::soportaAnchoMedio($this->conn->db, $ua)) {
			$tipo_cabezal = "mediano";
			$html = str_replace("#ANCHO_PANTALLA#", ANCHO_PANTALLA_MEDIANA, $html);
		}

		if(miscFunctions::soportaAnchoChico($this->conn->db, $ua)) {
			$tipo_cabezal = "chico";
			$html = str_replace("#ANCHO_PANTALLA#", ANCHO_PANTALLA_CHICA, $html);
		}

		//Cargamos los LOGOS-EXTRAS
		$tpl_extras_logos = $this->loadExtraTemplates($this->extras_logos_tpl);
		$html = str_replace("#LOGOS-EXTRAS#", $tpl_extras_logos, $html);

		//Cargamos los PIE-EXTRAS
		$tpl_extras_urls = $this->loadExtraTemplates($this->extras_pie_tpl);
		$html = str_replace("#PIE-EXTRAS#", $tpl_extras_urls, $html);

		if($this->marca_blanca || MARCA_BLANCA == 1) {
			$html = preg_replace("/#LOGO-WZZP#.+#LOGO-WZZP#/", "", $html);
		} else {
			$html = preg_replace("/#LOGO-WZZP#/", "", $html);
		}

		//la constante HIDE_HEADER se debe definir en el archivo constantes.php de cada wap
		if(HIDE_HEADER === 1) {
			$html = preg_replace("/#IMG-HEADER#.+#IMG-HEADER#/", "", $html);
		} else {
			$html = preg_replace("/#IMG-HEADER#/", "", $html);
		}

		if(ANCEL == 1) {
			if(SHOW_LINK_DALE == 1) {
				$seccion_pie_dest = new Seccion("", "center", "", SECCION_SIN_TITULO);
				$link_dale = null;
				if(servicio3G()) {
					$link_dale = new Link("http://www.3g.dale.com.uy", "<br/>Volver a portal 3G");
				} else {
					$link_dale = new Link("http://www.dale.com.uy", "<br/>Volver a portal DALE");
				}
				$html = str_replace("#LINK-DALE#", $link_dale->Display(), $html);
			}
		}
		$html = str_replace("#LINK-DALE#", "", $html);

		//super HACZOR malo:
		$html = str_replace(".list-item {float:left;  width: 65px; ", ".list-item {", $html);

		$html = str_replace("#TIPO_CABEZAL#", $tipo_cabezal, $html);
		$html = str_replace("#TITULO#", $this->titulo, $html);

		$html_interior = parent::Display();

		if(TIGO_GT == 1 || VIVA_BO == 1 || CLARO_PA == 1) {
			$html_interior = str_replace("$", "", $html_interior);
		}

		if(AGREGAR_EXTRA_PESOS ==1){
			$html_interior = str_replace("$", "$$", $html_interior);
		}


		/* * /
		if(in_array($ua, $CELULARES_PESO_SIMPLE)) {
			$html_interior = str_replace("$$", "$ ", $html_interior);
		}
		//*/

		$html =  str_replace("#COMPONENTES#", $html_interior, $html);

	        $html = str_replace("#HTTP_REFERER#", $_SERVER['HTTP_REFERER'], $html);

		$html = str_replace("&amp;", "&", $html);
		///$html = str_replace("&", "&amp;", $html);
		$html = konvert($html);
		$html = str_replace("�", "&#241;", $html);
		$html = str_replace("�", "&#225;", $html);
		$html = str_replace("é", "&#233;", $html);
		$html = str_replace("í", "&#237;", $html);
		$html = str_replace("ó", "&#243;", $html);
		$html = str_replace("ú", "&#250;", $html);
		$html = str_replace("Ñ", "&#209;", $html);
		$html = str_replace("Á", "&#193;", $html);
		$html = str_replace("É", "&#201;", $html);
		$html = str_replace("Í", "&#205;", $html);
		$html = str_replace("Ó", "&#211;", $html);
		$html = str_replace("Ú", "&#218;", $html);
		$html = str_replace("¿", "&#191;", $html);
		$html = str_replace(chr(173), "", $html);
/*
		$html = str_replace("&", "&amp;", $html);
		$html = str_replace("�", "&#241;", $html);
		$html = str_replace("�", "&#225;", $html);
		$html = str_replace("é", "&#233;", $html);
		$html = str_replace("í", "&#237;", $html);
		$html = str_replace("ó", "&#243;", $html);
		$html = str_replace("ú", "&#250;", $html);
		$html = str_replace("Ñ", "&#209;", $html);
		$html = str_replace("Á", "&#193;", $html);
		$html = str_replace("É", "&#201;", $html);
		$html = str_replace("Í", "&#205;", $html);
		$html = str_replace("Ó", "&#211;", $html);
		$html = str_replace("Ú", "&#218;", $html);
		$html = str_replace("¿", "&#191;", $html);
		**/

	        $html = str_replace("&amp;nbsp;", "&#32;", $html);


		$partes = explode("id=\"paginado\"", $html);
		$nuevo_html = "";
		foreach($partes as $i => $p) {
			if($i == 0) {
				$i = "";
			}
			$nuevo_html .= $p." id=\"paginado$i\" ";
			$largo_cola = strlen(" id=\"paginado$i\"");
		}
		$nuevo_html = substr($nuevo_html, 0, strlen($nuevo_html) - $largo_cola);
		$html = $nuevo_html;



      //  $html = strtolower($html);

		return $html;
	}
}

?>
