<?php

class Barrios {
	
	function editar($id, $barrio, $title) {
		$error = false;
		if ($this->existe($barrio, $title, $id)) {
			$error = true;
			$edesc = "error_existe";
		} else {
			$sql = sprintf("UPDATE ".DATABASE.".wap_barrios SET barrio='%s', title='%s' WHERE id='%u'", 
					mysql_real_escape_string($barrio), 
					mysql_real_escape_string($title), 
					mysql_real_escape_string($id));
			$res = mysql_query($sql) or die("SQL: ".$sql."<br/>".mysql_error());
			if (!$res) {
				$error = true;
				$edesc = "error_sql";
			}
		}
		return ($error) ? (object) array("error" => $error, "desc" => $edesc) : (object) array("error" => false);
	}
	
	function existe($barrio, $title, $id=null) {
		if ($id != null) {
			$sql = sprintf("SELECT * FROM ".DATABASE.".wap_barrios WHERE (barrio='%s' OR title='%s') AND id<>'%u'", 
				mysql_real_escape_string($barrio), 
				mysql_real_escape_string($title), 
				mysql_real_escape_string($id));
		} else {
			$sql = sprintf("SELECT * FROM ".DATABASE.".wap_barrios WHERE barrio='%s' OR title='%s'", 
				mysql_real_escape_string($barrio), 
				mysql_real_escape_string($title));
		}
		$res = mysql_query($sql) or die("SQL: ".$sql."<br/>".mysql_error());
		return (mysql_num_rows($res) > 0) ? true : false;
	}

	function listar() {
		$sql = "SELECT * FROM ".DATABASE.".wap_barrios";
		$res = mysql_query($sql) or die("SQL: ".$sql."<br/>".mysql_error());
		$lista = array();
		while ($data = mysql_fetch_object($res)) {
			$b = (object) array("id" => $data->id, "barrio" => $data->barrio, "title" => $data->title, "puntos" => $data->puntos);
			array_push($lista, $b);
		}
		return $lista;
	}
	
	function getPuntos($id) {
		$sql = sprintf("SELECT DISTINCT punto FROM ".DATABASE.".wap_contenidos WHERE barrio='%u' ORDER BY punto ASC", mysql_real_escape_string($id));
		$res = mysql_query($sql) or die("SQL: ".$sql."<br/>".mysql_error());
		$ret = array();
		while ($data = mysql_fetch_object($res)) {
			$p = $data->punto;
			array_push($ret, $p);
		}
		return $ret;
	}
	
}

?>