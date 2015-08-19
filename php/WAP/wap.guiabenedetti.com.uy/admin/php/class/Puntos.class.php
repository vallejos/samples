<?php

class Puntos {
	
	private $upload_path = "../../uploads/";

	function editar($id_barrio, $old_punto, $punto) {
		$error = false;
		$sql_existe = sprintf("SELECT * FROM ".DATABASE.".wap_contenidos WHERE barrio='%u' AND punto='%u'", 
				mysql_real_escape_string($id_barrio),  
				mysql_real_escape_string($punto));
		$res_existe = mysql_query($sql_existe) or die("SQL: ".$sql_existe."<br/>".mysql_error());
		if (mysql_num_rows($res_existe) > 0) {
			$error = true;
			$edesc = "error_existe";
		} else {
			$sql = sprintf("UPDATE ".DATABASE.".wap_contenidos SET punto='%u' WHERE barrio='%u' AND punto='%u'", 
					mysql_real_escape_string($punto), 
					mysql_real_escape_string($id_barrio), 
					mysql_real_escape_string($old_punto));
			$res = mysql_query($sql) or die("SQL: ".$sql."<br/>".mysql_error());
			if (!$res) {
				$error = true;
				$edesc = "error_sql"; 
			}
		}
		return ($error) ? (object) array("error" => $error, "desc" => $edesc) : (object) array("error" => false);
	}
	
	function addTxt($id_barrio, $punto, $title, $summary, $body) {
		$error = false;
		$sql = sprintf("INSERT INTO ".DATABASE.".wap_texto SET title='%s', summary='%s', body='%s'", 
				mysql_real_escape_string($title), 
				mysql_real_escape_string($summary), 
				mysql_real_escape_string($body)); 
		$res = mysql_query($sql) or die("SQL: ".$sql."<br/>".mysql_error());
		$content_id = mysql_insert_id();
		$sql2 = sprintf("INSERT INTO ".DATABASE.".wap_contenidos SET barrio='%u', punto='%u', content_id='".$content_id."', activo='1', tipo='1'", 
				mysql_real_escape_string($id_barrio), 
				mysql_real_escape_string($punto));
		$res2 = mysql_query($sql2) or die("SQL: ".$sql2."<br/>".mysql_error());
		if (!$res || !$res2) {
			$error = true;
			$edesc = "error_sql"; 
		}
		return ($error) ? (object) array("error" => $error, "desc" => $edesc) : (object) array("error" => false);
	}
	
	function updateTxt($content_id, $title, $summary, $body) {
		$error = false;
		$sql = sprintf("UPDATE ".DATABASE.".wap_texto SET title='%s', summary='%s', body='%s' WHERE id='%u'", 
				mysql_real_escape_string($title), 
				mysql_real_escape_string($summary), 
				mysql_real_escape_string($body), 
				mysql_real_escape_string($content_id));
		$res = mysql_query($sql) or die("SQL: ".$sql."<br/>".mysql_error());
		return ($error) ? (object) array("error" => $error, "desc" => $edesc) : (object) array("error" => false);
	}
	
	function addImg($id_barrio, $punto, $title, $img) {
		$error = false;
		if ($img['error'] == 0) {
			$sql = sprintf("INSERT INTO ".DATABASE.".wap_imagen SET title='%s'", 
					mysql_real_escape_string($title));
			$res = mysql_query($sql) or die("SQL: ".$sql."<br/>".mysql_error());
			$content_id = mysql_insert_id();
			$pinfo = pathinfo($img['name']);
			$ext = $pinfo['extension'];
			$nombre_img = $content_id.".".$ext;
			$ruta = $this->upload_path.$nombre_img;
			if (!move_uploaded_file($img['tmp_name'], $ruta)) {
				$error = true;
				$edesc = "error_mover";
			} else {
				$sql2 = sprintf("INSERT INTO ".DATABASE.".wap_contenidos SET barrio='%u', punto='%u', content_id='%u', activo='1', tipo='2'", 
						mysql_real_escape_string($id_barrio), 
						mysql_real_escape_string($punto), 
						mysql_real_escape_string($content_id));
				$res2 = mysql_query($sql2) or die("SQL: ".$sql2."<br/>".mysql_error());
				$sql3 = sprintf("UPDATE ".DATABASE.".wap_imagen SET file='%s' WHERE id='%u'", 
						mysql_real_escape_string($nombre_img), 
						mysql_real_escape_string($content_id));
				$res3 = mysql_query($sql3) or die("SQL: ".$sql3."<br/>".mysql_error());
				if (!$res || !$res2 || !$res3) {
					$error = true;
					$edesc = "error_sql";
				}
			}
		} else {
			$sql = sprintf("INSERT INTO ".DATABASE.".wap_imagen SET title='%s'", 
				mysql_real_escape_string($title));
			$res = mysql_query($sql) or die("SQL: ".$sql."<br/>".mysql_error());
			if (!$res) {
				$error = true;
				$edesc = "error_sql";
			}
		}
		return ($error) ? (object) array("error" => $error, "desc" => $edesc) : (object) array("error" => false);
	}
	
	function updateImg($content_id, $title, $img) {
		$error = false;
		if ($img['error'] == 0) {
			$pinfo = pathinfo($img['name']);
			$ext = $pinfo['extension'];
			$nombre_img = $content_id.".".$ext;
			$ruta = $this->upload_path . $nombre_img;
			if (!move_uploaded_file($img['tmp_name'], $ruta)) {
				$error = true;
				$edesc = "error_mover";
			} else {
				$sql = sprintf("UPDATE ".DATABASE.".wap_imagen SET title='%s', file='%s' WHERE id='%u'",  
						mysql_real_escape_string($title), 
						mysql_real_escape_string($nombre_img), 
						mysql_real_escape_string($content_id));
				$res = mysql_query($sql) or die("SQL: ".$sql."<br/>".mysql_error());
				if (!$res) {
					$error = true;
					$edesc = "error_sql";
				}
			}
		} else {
			$sql = sprintf("UPDATE ".DATABASE.".wap_imagen SET title='%s' WHERE id='%u'", 
					mysql_real_escape_string($title), 
					mysql_real_escape_string($content_id));
			$res = mysql_query($sql) or die("SQL: ".$sql."<br/>".mysql_error());
		}
		return ($error) ? (object) array("error" => $error, "desc" => $edesc) : (object) array("error" => false);
	}
	
	function getContenidos($id_barrio, $punto) {
		$ret = array("info"=>(object) array("id_barrio"=>$id_barrio, "punto"=>$punto), "img"=>null, "txt"=>null);
		$sql = sprintf("SELECT id, content_id, activo, tipo FROM ".DATABASE.".wap_contenidos WHERE barrio='%u' AND punto='%u'", 
				mysql_real_escape_string($id_barrio), 
				mysql_real_escape_string($punto));
		$res = mysql_query($sql) or die("SQL: ".$sql."<br/>".mysql_error());
		while ($data = mysql_fetch_object($res)) {
			switch ($data->tipo) {
				case 1:
					$sql2 = "SELECT title, summary, body FROM ".DATABASE.".wap_texto WHERE id='$data->content_id'";
					$res2 = mysql_query($sql2) or die("SQL2: ".$sql2."<br/>".mysql_error());
					$data2 = mysql_fetch_object($res2);
					$ret['txt'] = (object) array("id"=>$data->content_id, "title"=>$data2->title, "summary"=>$data2->summary, "body"=>$data2->body);
					break;
				case 2:
					$sql2 = "SELECT title, file FROM ".DATABASE.".wap_imagen WHERE id='$data->content_id'";
					$res2 = mysql_query($sql2) or die("SQL: ".$sql2."<br/>".mysql_error());
					$data2 = mysql_fetch_object($res2);
					$ret['img'] = (object) array("id"=>$data->content_id, "title"=>$data2->title, "file"=>$data2->file);
					break;
			}
		}
		return $ret;
	}
	
}

?>