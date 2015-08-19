<?php

/**
OBTIENE CONTENIDOS DEL TIPO IMAGEN PARA EL RECORRIDO DADO
 */
function obtenerImagen($dbc, $pp, $bb) {
	$dbName = DB_NAME;
	$type = TYPE_IMAGE;
	$sql = "SELECT c.id, i.title, i.file file FROM $dbName.wap_imagen i 
		INNER JOIN $dbName.wap_contenidos c ON (c.content_id=i.id) 
		INNER JOIN $dbName.wap_barrios b ON (b.id=c.barrio) 
		WHERE c.tipo=$type AND c.activo=1 AND c.punto='$pp' AND b.barrio='$bb' "  ;
//		echo $sql;
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) {
		// todo: log error
		return NULL;
	} else {
		$conts[] = mysql_fetch_assoc($rs);
	}
	return $conts;
}


/**
OBTIENE CONTENIDOS DEL TIPO texto PARA EL RECORRIDO DADO
 */
function obtenerTexto($dbc, $pp, $bb) {
	$dbName = DB_NAME;
	$type = TYPE_TEXT;
	$sql = "SELECT c.id, t.title, t.summary, t.body FROM $dbName.wap_texto t 
		INNER JOIN $dbName.wap_contenidos c ON (c.content_id=t.id) 
		INNER JOIN $dbName.wap_barrios b ON (b.id=c.barrio) 
		WHERE c.tipo=$type AND c.activo=1 AND c.punto='$pp' AND b.barrio='$bb' "  ;
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) {
		// todo: log error
		return NULL;
	} else {
		$conts[] = mysql_fetch_assoc($rs);
	}
	return $conts;
}



/**
BUSCA CONTENIDOS DEL TIPO IMAGEN PARA EL RECORRIDO DADO
 */
function tieneImagen($dbc, $pp, $bb) {
	$dbName = DB_NAME;
	$type = TYPE_IMAGE;
	$sql = "SELECT COUNT(*) conts FROM $dbName.wap_contenidos c 
		INNER JOIN $dbName.wap_barrios b ON (b.id=c.barrio) 
		WHERE c.tipo=$type AND c.activo=1 AND c.punto='$pp' AND b.barrio='$bb' "  ;
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) {
		// todo: log error
		return FALSE;
	} else {
		$obj = mysql_fetch_object($rs);
		return $obj->conts;
	}
}



/**
BUSCA CONTENIDOS DEL TIPO texto PARA EL RECORRIDO DADO
 */
function tieneTexto($dbc, $pp, $bb) {
	$dbName = DB_NAME;
	$type = TYPE_TEXT;
	$total = NULL;
	$sql = "SELECT COUNT(*) conts FROM $dbName.wap_contenidos c 
		INNER JOIN $dbName.wap_barrios b ON (b.id=c.barrio) 
		WHERE c.tipo=$type AND c.activo=1 AND c.punto='$pp' AND b.barrio='$bb' "  ;
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) {
		// todo: log error
	} else {
		$obj = mysql_fetch_object($rs);
		$total = $obj->conts;
	}
	return $total;
}

/**
BUSCA CONTENIDOS DEL TIPO audio PARA EL RECORRIDO DADO
 */
function tieneAudio($dbc, $pp, $bb) {
	$dbName = DB_NAME;
	$type = TYPE_AUDIO;
	$total = NULL;
	$sql = "SELECT COUNT(*) conts FROM $dbName.wap_contenidos c 
		INNER JOIN $dbName.wap_barrios b ON (b.id=c.barrio) 
		WHERE c.tipo=$type AND c.activo=1 AND c.punto='$pp' AND b.barrio='$bb' "  ;
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) {
		// todo: log error
	} else {
		$obj = mysql_fetch_object($rs);
		$total = $obj->conts;
	}
	return $total;
}



/**
PUNTO VALIDO?
 */
function isValidPunto($punto, $barrio) {
	$valid = FALSE;

	if (isValidBarrio($barrio) && is_numeric($punto) && $punto<=getPuntos($barrio)) {
		$valid = TRUE;
	}

	return $valid;
}

/** 
BARRIO VALIDO?
 */
function isValidBarrio($barrio) {
	global $PUNTOS_VALIDOS;
	if (in_array($barrio, $PUNTOS_VALIDOS)) return TRUE;
	else return FALSE;
}


/** 
 OBTIENE LOS PUNTOS DE UN BARRIO
*/
function getPuntos($barrio) { 
	switch ($barrio) {
		case "ag":
			$puntos = PUNTOS_AG;
		break;
		case "ce":
			$puntos = PUNTOS_CE;
		break;
		case "co":
			$puntos = PUNTOS_CO;
		break;
		case "cv":
			$puntos = PUNTOS_CV;
		break;
		case "cp":
			$puntos = PUNTOS_CP;
		break;
		case "pp":
			$puntos = PUNTOS_PP;
		break;
		default: 
			$puntos = NULL;
	}

	return $puntos;
}



?>