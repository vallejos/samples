<?php

/**
 *
 *
 *
 *
 *
 */

class video {
	var $nombre_contenido;
	var $proveedor;
	var $type;
	var $categoria;
	var $subcategoria;
	var $code;
	var $operator;
	var $search_keywords;
	var $royalty;
	var $musiclabel;
	var $album;
	var $artista;
	var $movie;
	var $icons;
	var $video;

	var $_nombre_xml;
	var $dbc;
	var $filename;

	var $webpreview;
	var $wappreview;
	var $objects;

	var $tipo_cont = "video";

	function video($oDbc, $id) {
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

		// seteo categorias
		$search_keywords = "$obj->nombre, $obj->nombre_categoria, video, clip, $obj->autor, $obj->nombre_proveedor, $obj->genero";

		$this->icons = $obj->referencia;
		$this->video = $obj->archivo;
		// descomentar la sgte linea para ftp local (240)
//		$this->icons = str_replace("/netuy", "", $this->icons);
		$this->video = str_replace("/netuy", "", $this->video);


		// preparo map al objeto
		$this->set("nombre_contenido", konvert($obj->nombre));
		$this->set("proveedor", "Globalnet");
		$this->set("type", "video");
//		list ($cat, $subcat) = $this->get_tigo_categories($obj->id, $this->tipo_cont);
		$this->set("categoria", konvert($cat));
		$this->set("subcategoria", konvert($subcat));
		$this->set("operator", "");
		$this->set("search_keywords", konvert("$search_keywords"));
		$this->set("royalty", "");
		$this->set("album", "");
		$this->set("artista", konvert($obj->autor));
		$this->set("movie", "");

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