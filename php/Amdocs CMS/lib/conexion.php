<?php

class coneXion {

	var $db;

	function coneXion($dataBase = "Web"){
		$this->db = mysql_pconnect("10.0.0.240", "pablo", "pablok4");
      		mysql_select_db($dataBase,$this->db);
	}
	function getConexion(){
      		return $this->db;
    	}	

	function cambiarDB($dataBase){
		return mysql_select_db($dataBase,$this->db);
	}

    function cerrarDB(){
        return mysql_close($this->db);
    }
}

?>