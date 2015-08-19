<?php

/**
 *
 *
 *
 *
 *
 */

class mp3 {
	var $nombre_contenido;
	var $proveedor;
	var $categoria;
	var $subcategoria;
	var $code;
	var $operator;
	var $search_keywords;
	var $musiclabel;
	var $movie;
	var $album;
	var $artista;
	var $provider_code;
        var $icons;

	var $_nombre_xml;
	var $dbc;
	var $filename;

	var $wappreview;
	var $objects;

	var $tipo_cont = "mp3";


	var $TIGO_CATEGORIES_MAP = array(

	);

	var $TIGO_CATEGORIES = array(

        );


	function mp3($oDbc, $id) {
		$this->dbc = $oDbc->db;
		$this->code = $id;
		$this->filename = substr(md5($this->code),5,5); // uso un md5 como nombre del xml
	}

	function load($cat="",$subcat="") {
		$sql = "SELECT c.*, p.nombre nombre_proveedor, cc.descripcion as nombre_categoria
		FROM Web.contenidos c
		INNER JOIN Web.contenidos_proveedores p ON (p.id=c.proveedor)
		INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria)
		WHERE c.id=$this->code ";
		$rs = mysql_query($sql, $this->dbc);
		if (!$rs) die ("ERROR SQL: $sql -> ".mysql_error($this->dbc));
		$obj = mysql_fetch_object($rs);

		// seteo cateogiras
		$search_keywords = "$obj->nombre, $obj->nombre_categoria, mp3, sonido, $obj->autor";

		// archivo en ftp
		if($obj->archivo == "") {
			$this->icons = $obj->referencia;
		} else {
			$this->icons = $obj->archivo;
		}
		$this->icons = str_replace("128x128", "320x320", $this->icons);
		$this->icons = str_replace(".gif", ".jpg", $this->icons);
		// descomentar la sgte linea para ftp local (240)
		$this->icons = str_replace("/netuy", "", $this->icons);


		// preparo map al objeto
		$this->set("nombre_contenido", konvert($obj->nombre));
		$this->set("proveedor", "Globalnet");
//		list ($cat, $subcat) = $this->get_tigo_categories($obj->id, $this->tipo_cont);
		$this->set("categoria", konvert($cat));
		$this->set("subcategoria", konvert($subcat));
		$this->set("operator", "");
		$this->set("search_keywords", konvert("$search_keywords"));
		$this->set("musiclabel", konvert($obj->autor));
		$this->set("movie", konvert($obj->autor));
		$this->set("album", konvert($obj->autor));
                $this->set("artista", konvert($obj->autor));
		//$this->set("provider_code", "");
                $this->set("royalty", "Globalnet");
                $this->set("code", konvert($obj->id));
	}


	function set($name, $value) {
		$this->$name = $value;
	}


	function add($name, $value) {
		$this->$name .= $value;
	}


	function get_tigo_categories($cont, $type) {
		$cats = array();

		$arr = $this->TIGO_CATEGORIES[$type];

		foreach ($arr as $cat => $subcats) {
			foreach ($subcats as $sub => $subdata) {
				if (in_array($cont, $subdata)) {
					$cats[] = $this->TIGO_CATEGORIES_MAP[$cat];
					$cats[] = $this->TIGO_CATEGORIES_MAP[$sub];
				}
			}
		}

		return $cats;
	}


}

?>