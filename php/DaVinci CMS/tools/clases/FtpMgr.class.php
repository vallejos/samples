<?php
class FtpMgr{
    
    var $host = "";
    var $user = "";
    var $pass = "";
    var $source = "";
    var $destination = "";
    var $conn_id = "";
    var $login_result = "";
    
    function FtpMgr($h,$u,$p,$s,$d)
    {
        $this->host = $h;
        $this->user = $u;
        $this->pass = $p;
        $this->source = $s;
        $this->destination = $d;
	
	//print_r($this->host,true);
	
    }
    
    function openConn()
    {	
        $this->conn_id = ftp_connect($this->host);
        $this->login_result = ftp_login($this->conn_id, $this->user, $this->pass);
        ftp_pasv ($this->conn_id, true);
    }
	
	function getConn()
    {
        return $this->conn_id;
    }
    
    function closeConn()
    {
        ftp_close($this->conn_id);
    }
        
    function setUser($u)
    {
        $this->user = $u;
    }
    
    function setPass($p)
    {
        $this->pass = $p;
    }
    
    function setSource($s)
    {
        $this->source = $s;
    }
    
    function setDestination($d)
    {
        $this->destination = d;
    }
    
    function upload()
    {
        $msg = "ARCHIVO SUBIDO";
        if ($this->conn_id && $this->login_result)
        {
			
			echo "Destino: ".$this->destination;
			echo "<br/><br/>";
			echo "Fuente: ".$this->source;
			echo "<br/><br/>";
			echo "-------------------------------------";
			echo "<br/><br/>";
			
			/**/
			if (!ftp_put($this->conn_id, $this->destination, $this->source, FTP_BINARY))
                $msg = "ERROR AL SUBIR ARCHIVO";
			/**/
        }
        else
        {
            $msg = "ERROR DE CONEXION FTP";
        }
        return $msg;
    }
	
	function makeFolder($source_file,$dest_path){
		$aux = explode ("/", $source_file);
		echo "me cambio al directorio: ".$dest_path."/".$aux[0];
		echo "<br><br>";
		ftp_chdir($this->getConn(),$dest_path."/".$aux[0]);
		echo "hago el directorio: ".str_replace(" ", "", $aux[1]."/");
		echo "<br><br>";
		@ftp_mkdir($this->getConn(), str_replace(" ", "", $aux[1]."/"));
		ftp_chdir($this->getConn(),"/");
	}
	
	function makeFolderV($source,$dest){
		echo "Me cambio al: ".$source;
		echo "<br><br>";
		echo "creo el: ".$dest;
		echo "<br><br>";
		/**/
		ftp_chdir($this->getConn(), $source);
		@ftp_mkdir($this->getConn(), $dest);
		ftp_chdir($this->getConn(), "/");
		/**/
	}
}
?>
