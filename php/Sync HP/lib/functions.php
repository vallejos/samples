<?php


function loadGenreList($dbc) {
    $genreList = array();
    $sql = "SELECT * FROM ".DB_NAME.".generos";
    $rs = mysql_query($sql, $dbc->db);
    if (!$rs) { 
	// error mysql
	return FALSE;
    } else { 
	while ($obj = mysql_fetch_object($rs)) {
	    $genreList[] = $obj;
	}
    }
    return $genreList;
}


function loadIcpnListFromAlbums($dbc) {
    $genreList = array();
    $sql = "SELECT * FROM ".DB_NAME.".albums";
    $rs = mysql_query($sql, $dbc->db);
    if (!$rs) { 
	// error mysql
	return FALSE;
    } else { 
	while ($obj = mysql_fetch_object($rs)) {
	    $genreList[] = $obj;
	}
    }
    return $genreList;
}


function loadIcpnListFromTracks($dbc, $offsetStart) {
    $genreList = array();

    $sql = "SELECT DISTINCT * FROM ".DB_NAME.".temas WHERE upc != '' LIMIT $offsetStart, ".DB_SELECT_QUERYLIMIT;

    /*
    $sql = "SELECT t.upc FROM ".DB_NAME.".temas t  
	LEFT JOIN ".DB_NAME.".albums a ON (a.upc=t.upc) 
	LEFT JOIN ".DB_NAME.".albums_artistas aa ON (aa.idalbum=a.id) 


";
*/
    
//    $sql = "SELECT t.upc FROM ".DB_NAME.".temas t WHERE t.upc NOT IN (SELECT DISTINCT ";
    
    $rs = mysql_query($sql, $dbc->db);
    if (!$rs) { 
	// error mysql
	return FALSE;
    } else { 
	while ($obj = mysql_fetch_object($rs)) {
	    $genreList[] = $obj;
	}
    }
    return $genreList;
}


function getNumberOfTracks($dbc) {
    $total = 0;
    $sql = "SELECT count(*) total FROM ".DB_NAME.".temas ";
    $rs = mysql_query($sql, $dbc->db);
    if (!$rs) { 
	// error mysql
	return FALSE;
    } else {
	$obj = mysql_fetch_object($rs);
	$total = $obj->total;
    }
    return $total;
}


function getMatchedIcpn($dbc, $icpn) {
    $total = 0;
    $sql = "SELECT count(*) total FROM ".DB_NAME.".albums WHERE upc='$icpn' ";
    $rs = mysql_query($sql, $dbc->db);
    if (!$rs) { 
	// error mysql
	return FALSE;
    } else {
	$obj = mysql_fetch_object($rs);
	$total = $obj->total;
    }
    return $total;
}



function emptyTable($dbc, $tblName) {
    $sql = "TRUNCATE TABLE ".DB_NAME.".$tblName ";
    $rs = mysql_query($sql, $dbc->db);
    $rs = mysql_query($sql, $dbc->db);
    if (!$rs) { 
	// error mysql
	return FALSE;
    } else {
	return TRUE;
    }
}


function avisoCel($msg, $to="8609380k189@ancelinfo.com.uy") {
	mail($to, APP_NAME.":".$msg, "");
}


function linkAlbumArtist($dbc, $idAlbum, $idArtist, $useDelay=FALSE) {
    $sql = ($useDelay === TRUE) ? "INSERT DELAYED INTO " : "INSERT INTO ";
    $sql .= DB_NAME.".albums_artistas SET ".
	    "idalbum='$idAlbum', ".
	    "idartista='$idArtist' ";

    $rs = mysql_query($sql, $dbc->db);
    if (!$rs) { 
	// error mysql
	return FALSE;
    } else {
	return TRUE;
    }
    
}


?>
