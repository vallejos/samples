<?php


class Paginado extends WapComponent {

	var $por_pagina;
	var $pagina_actual;
	var $total;
	var $query_arr;
	var $url_pagina;
	var $param_pagina;

	/**
	 * Constructor
	 *
	 * @param $cant_p_tta 	 Cantidad de elementos por tarjeta
	 * @param $p_actual 	 Página actual
	 * @param $total 	 Cantidad total de elementos 
	 * @param $query_arr     El array $_GET que se pasa como parametro, para no perder los otros posibles parametros de la página actual.
	 * @return string 	 Devuelve un string con todos los links necesarios para pasar de página en página.
	 */
	function Paginado($cant_p_tta, $p_actual = 0, $total, $query_arr, $pagina, $param_pagina = 'p'){
		$this->template = "templates/paginado.tpl";
		$this->por_pagina = $cant_p_tta;
		$this->pagina_actual = $p_actual;
		$this->total = $total;
		$this->query_arr = $query_arr;
		$this->url_pagina = $pagina;
		$this->param_pagina = $param_pagina;
	}

	/**
	 * Dibuja el paginado de una lista 
	 *
	 */
	function Display(){
		$contenidoTpl = $this->_loadTemplate();

		$salida = "";
		$init = ($this->por_pagina * $this->pagina_actual); 
		$fin = $init + $this->por_pagina;

		$this->query_arr[$this->param_pagina] = (isset($this->query_arr[$this->param_pagina]))?$this->query_arr[$this->param_pagina]:0;
		//$total_paginas = round((($this->total +1)/ $this->por_pagina));
		//echo '<!-- total: '. $this->total . " / por pagina: " . $this->por_pagina." -->";
		$total_paginas = (int) ceil($this->total / $this->por_pagina);
		
		if($total_paginas <= 1) {
			return "";	
		}

		
		$ant = $this->query_arr[$this->param_pagina] - 1;
		$sig = $this->query_arr[$this->param_pagina] + 1;
		if($this->pagina_actual > 0) {
			$this->query_arr[$this->param_pagina] = $ant;
			$query_str = implode_with_keys("&amp;", "=", $this->query_arr);
			$contenidoTpl = str_replace("#ANTERIOR#", $this->url_pagina.'?'.$query_str, $contenidoTpl);
			$contenidoTpl = str_replace("#TEXTOANTERIOR#", 'Anterior', $contenidoTpl);
		} else {
			$contenidoTpl = str_replace("#ANTERIOR#", '', $contenidoTpl);
			$contenidoTpl = str_replace("#TEXTOANTERIOR#", '', $contenidoTpl);

		}
		if(ceil($total_paginas) > 1) {
			$progress = " ".($this->pagina_actual + 1)."/".ceil($total_paginas)." ";
			$contenidoTpl = str_replace("#PROGRESS#", $progress, $contenidoTpl);
		} else {
			$contenidoTpl = str_replace("#PROGRESS#", "", $contenidoTpl);
		}
		if(ceil($this->pagina_actual) < ($total_paginas - 1)) {
			$this->query_arr[$this->param_pagina] = $sig;
			$query_str = implode_with_keys("&amp;", "=", $this->query_arr);
			$contenidoTpl = str_replace("#SIGUIENTE#", $this->url_pagina.'?'.$query_str, $contenidoTpl);
			$contenidoTpl = str_replace("#TEXTOSIGUIENTE#", 'Siguiente', $contenidoTpl);
		} else {
			$contenidoTpl = str_replace("#SIGUIENTE#", '', $contenidoTpl);
			$contenidoTpl = str_replace("#TEXTOSIGUIENTE#", '', $contenidoTpl);

		}
		return $contenidoTpl;
	}
}
