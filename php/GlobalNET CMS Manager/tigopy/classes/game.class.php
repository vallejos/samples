<?php

/**
 *
 *
 *
 *
 *
 */

class game {
	var $nombre_contenido;
	var $proveedor;
	var $type;
	var $categoria;
	var $subcategoria;
	var $code;
	var $operator;
	var $search_keywords;
	var $shortdesc;
	var $longdesc;
	var $cls;
	var $provider_code;
	var $cla;
	var $icons;
	var $jars;
	var $jads;

	var $_nombre_xml;
	var $dbc;
	var $filename;

	var $webpreview;
	var $wappreview;
	var $handsets;

	var $tipo_cont = "game";
	var $devices;
	var $uaFiles;
/*
	var $TIGO_CATEGORIES_MAP = array(
		"112" => "3D",
		"g112" => "",
		"96" => "Accion",
		"g96" => "",
		"104" => "Animales",
		"g104" => "",
		"98" => "Aplicaciones",
		"g98" => "",
		"100" => "Arcade",
		"g100" => "",
		"97" => "Aventura",
		"g97" => "",
		"101" => "Casino",
		"g101" => "",
		"99" => "Comics",
		"g99" => "",
		"108" => "Consola",
		"g108" => "",
		"107" => "Deportes",
		"g107" => "",
		"110" => "Estrategia",
		"g110" => "",
		"102" => "Guerra",
		"g102" => "",
		"103" => "Hollywood",
		"g103" => "",
		"111" => "Logica",
		"g111" => "",
		"12" => "Premium Games",
		"g12" => "",
		"105" => "Puzzle",
		"g105" => "",
		"106" => "Racing",
		"g106" => "",
		"109" => "Sexy",
		"g109" => "",
	);

	var $TIGO_CATEGORIES = array(
		"game" => array(
			"112" =>  array(
				"g112" => array(10524,10934,14403,14404,14405,14406,14407,14744,14619,18547,20205,20671,13144,10922,10094,9980),
			),
			"96" => array(
				"g96" => array(20174,18329,18024,17432,16805,16806,15379,15187,13334,13031,12976,12879,12731,11417,11264,18028,20678,21112),
			),
			"104" => array(
				"g104" => array(21063,21056,21045,21036,20986,20982,20981,20980,20973,20961,20942,20927,20685,18782,18741,17658),
			),
			"98" => array(
				"g98" => array(18976,18969,18343,15301),
			),
			"100" => array(
				"g100" => array(20370,18999,18643,17744,17728,17602,17218,17129,16890,15834,15244,12808,12012),
			),
			"97" => array(
				"g97" => array(20682,20680,20681,20370,16807,15380,15379,15212,15179,14939,13317,13170,18740,21252),
			),
			"101" => array(
				"g101" => array(17788,15831,15391,15061,11994,11993,11992,11971,11970,10859,10854,10846,10834),
			),
			"99" => array(
				"g99" => array(),
			),
			"108" => array(
				"g108" => array(21327,20368,20220,20174,17754,17474,15374,12563,11729,11419,11418,11417,11468,11566,10053,10095,10104,9374,8515),
			),
			"107" => array(
				"g107" => array(20724,20357,20176,20175,19798,18867,18789,18246,17464,17446),
			),
			"110" => array(
				"g110" => array(20726,18030,16803,16802,16134,16133,15380,15201,12739,17071,17756,18026,21300,21302,21303),
			),
			"102" => array(
				"g102" => array(11883,11880,11737,15025,16805,16806,16807,16929,17073,18934,18929,21113,21233,21201),
			),
			"103" => array(
				"g103" => array(20666,20221,18728,18355,17615,17254,15805,13799,10487, 18099),
			),
			"111" => array(
				"g111" => array(22083,22086,21978,21115,20372,20215,20208,18883,18882,18861,18026,17757,17659),
			),
			"12" => array(
				"g12" => array(20666,20221,18728,18355,17615,17254,15805,13799,10487, 18099),
			),
			"105" => array(
				"g105" => array(22084,18767,17218,13800,12742,10525),
			),
			"106" => array(
				"g106" => array(12339,12158,13209,13250,14937,16965,16969,16967,20205,20679,21028,21114,21976),
			),
			"109" => array(
				"g109" => array(21981,21977,21329,21304,21161,20684,20214,18932,18739,18416),
			),
		),
	);
*/

	function game($oDbc, $id, $uaTable) {
		$this->dbc = $oDbc->db;
		$this->code = $id;
		$this->devices = $uaTable;
		$this->filename = substr(md5($this->code),5,5); // uso un md5 como nombre del xml
	}

	function load($cat="",$subcat="") {
		$sql = "SELECT c.*, p.nombre nombre_proveedor, cc.descripcion as nombre_categoria, gi.descr longdesc, gi.descr_wap shortdesc, gi.screenshots
		FROM Web.contenidos c
		INNER JOIN Web.gamesInfo gi ON (c.id=gi.game)
		INNER JOIN Web.contenidos_proveedores p ON (p.id=c.proveedor)
		INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria)
		WHERE c.id=$this->code ";

		$rs = mysql_query($sql, $this->dbc);
		if (!$rs) die ("ERROR SQL: $sql -> ".mysql_error($this->dbc));
		$obj = mysql_fetch_object($rs);

		// seteo categorias
		$search_keywords = "$obj->nombre, $obj->nombre_categoria, juego, java, imagen, $obj->autor, $obj->nombre_proveedor";

//		list($ruta,$pic1,$pic2,$pic3) = explode(",", $obj->screenshots);
		$this->icons = "/netuy/java/cajas/".$obj->id.".gif";
		// descomentar la sgte linea para ftp local (240)
//		$this->icons = str_replace("/netuy", "", $this->icons);


		// preparo map al objeto
		$this->set("nombre_contenido", konvert($obj->nombre));
		$this->set("proveedor", "Globalnet");
		$this->set("type", "Java");
//		list ($cat, $subcat) = $this->get_tigo_categories($obj->id, $this->tipo_cont);
		$this->set("categoria", konvert($cat));
		$this->set("subcategoria", konvert($subcat));
		$this->set("operator", "");
		$this->set("search_keywords", konvert("$search_keywords"));
		$this->set("shortdesc", konvert($obj->shortdesc));
		$this->set("longdesc", konvert($obj->longdesc));
		$this->set("cls", "Premium");
		$this->set("provider_code", "");
		$this->set("cla", "");

		$totalCels=0;
		$celsNotFound=0;
		$sqlErrors=0;
		$celsSupported=0;
		$celsNotSupported=0;
		foreach ($this->devices as $devUA) {
			$totalCels++;
			$idCel = obtenerIDCelular($devUA, $this->dbc);

			if ($idCel == 0) {
				$celsNotFound++;
//				echo "$this->code: id Cel no encontrado para ua=$devUA<br/>";
			} else if ($idCel === FALSE) {
				$sqlErrors++;
//				echo "$this->code: SQL error buscando idCel para ua=$devUA<br/>";
			} else if (soportaJuego($this->dbc, $idCel, $obj->id)) {
				$celsSupported++;
				// echo "$devUA SOPORTA!!!\n";
				$sql2 = " SELECT archivo FROM Web.gamecomp WHERE juego=$obj->id AND celular=$idCel ";
				$rs2 = mysql_query($sql2, $this->dbc);
				if (!$rs2) echo "ERRORRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR SQL";
				$obj2 = mysql_fetch_object($rs2);
				$this->jads[] = $obj2->archivo;
				$this->jars[] = str_replace(".jad", ".jar", $obj2->archivo);

				$kfName = basename($obj2->archivo, ".jad");
				$this->uaFiles[$kfName] .= "$devUA,";
			} else {
				$celsNotSupported++;
//				echo "$this->code: id Cel no soportado para ua=$devUA (idCel=$idCel) <br/>";
			}

		}

		// muestro resumen del proceso
		echo "<table>
		<tr><td>CONTENIDO</td><td>$obj->id ($obj->nombre)</td></tr>
		<tr><td>TOTAL CELS TIGO </td><td> $totalCels <a href='errorDetail.php?d=celstigo&id=$obj->id' target='blank'>ver detalle</a></td></tr>
		<tr><td>TOTAL CELS NO ENCONTRADOS </td><td> $celsNotFound <a href='errorDetail.php?d=notfound&id=$obj->id' target='blank'>ver detalle</a></td></tr>
		<tr><td>TOTAL ERRORES SQL </td><td> $sqlErrors </td></tr>
		<tr><td>TOTAL CELS SOPORTADOS OK </td><td> $celsSupported <a href='errorDetail.php?d=supported&id=$obj->id' target='blank'>ver detalle</a></td></tr>
		<tr><td>TOTAL CELS ENCONTRADOS PERO NO SOPORTADOS POR EL JUEGO </td><td> $celsNotSupported <a href='errorDetail.php?d=notsupported&id=$obj->id' target='blank'>ver detalle</a></td></tr>
		</table>
		";
		
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