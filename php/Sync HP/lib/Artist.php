<?php

/**
 * Description of Artist
 *
 * @author kAmuS
 */
class Artist {
    private $id;
    private $name;
    private $matches;

    function __construct() {
	$this->matches = 0;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    // devuelve la cantidad de matches encontrados tras un load
    public function getMaches() {
	return $this->matches;
   }
    
    public function loadFromName($dbc, $name) {
	$this->matches = 0;
	$result = array();
	$sql = "SELECT * FROM ".DB_NAME.".artistas WHERE nombre='$name' ";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) { 
	    // error mysql
	    return FALSE;
	} else { 
	    while ($obj = mysql_fetch_object($rs)) {
		$this->setId($obj->id);
		$this->setName($obj->nombre);
		$this->matches++;
	    }
	}
	return TRUE;
    }

    public function loadFromId($dbc, $id) {
	$this->matches = 0;
	$result = array();
	$sql = "SELECT * FROM ".DB_NAME.".artistas WHERE id='$id' ";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) { 
	    // error mysql
	    return FALSE;
	} else { 
	    while ($obj = mysql_fetch_object($rs)) {
		$this->setId($obj->id);
		$this->setName($obj->nombre);
		$this->matches++;
	    }
	}
	return TRUE;
    }
    
    
    public function save($dbc, $dbTable, $useDelay=FALSE) {
	$sql = ($useDelay === TRUE) ? "INSERT DELAYED INTO " : "INSERT INTO ";
	$sql .= DB_NAME.".".$dbTable." SET ".
	    "nombre='".mysql_real_escape_string($this->getName())."', ".
	    "activo='1' ". // activo por defecto
	    "";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) { 
	    // error mysql
	    return FALSE;
	} else {
	    $this->setId(mysql_insert_id());
	    return TRUE;
	}
    }
    
    

}
?>
