<?php

/**
 * Description of Genre
 *
 * @author kAmuS
 */
class Genre {
    private $id;
    private $key;
    private $position;
    private $parentKey;
    private $name;

    private $children = array();

    function __construct($id=null, $key=null, $position=null, $parentKey=null, $name=null) {
	$this->id = $id;
	$this->key = $key;
	$this->position = $position;
	$this->parentKey = $parentKey;
	$this->name = $name;
    }

    public function getChildren() {
	return $this->children;
    }

    public function isFather() {
	if (sizeof($this->children)<1) return FALSE;
	else return TRUE;
    }

    public function addChild($g){
	if(!$this->hasChild($g)) {
	    $this->children[] = $g;
	}
    }

    public function hasChild($g) {
	foreach($this->children as $child) {
	    if($child == $g) {
		return true;
	    }
	}
	return false;
    }

    public function getName() {
	return $this->name;
    }

    public function setName($name) {
	$this->name = $name;
    }

    public function getParentKey() {
	return $this->parentKey;
    }

    public function setParentKey($parentKey) {
	$this->parentKey = $parentKey;
    }

    public function getId() {
	return $this->id;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function getKey() {
	return $this->key;
    }

    public function setKey($key) {
	$this->key = $key;
    }

    public function getPosition() {
	return $this->position;
    }

    public function setPosition($position) {
	$this->position = $position;
    }

    
    public function save($dbc, $dbTable, $useDelay=FALSE) {
	$sql = ($useDelay === TRUE) ? "INSERT DELAYED INTO " : "INSERT INTO ";
	$sql .= DB_NAME.".".$dbTable." SET ".
	    "nombre='".mysql_real_escape_string($this->getName())."', ".
	    "activo='1', ". // activo por defecto
	    "idgrupo='".$this->getId()."' ".
	    "";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) { 
	    // error mysql
	    return FALSE;
	} else return TRUE;
	
    }
    
    
}

?>
