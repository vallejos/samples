<?php

class coneXion {
    var $db;

    function coneXion($host, $user, $pass, $dbName){
        $this->db = mysql_pconnect($host, $user, $pass);
        mysql_select_db($dbName,$this->db);
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
