<?php
class Ftp {
    var $host;
	var $port;
    var $user;
    var $pass;
	var $id_conn;
	var $pass_mode;
    function Ftp($host="10.0.0.241",$user="sms",$pass="gagsUrt]",$port=21) {
        $this->host = $host;
		$this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
		$this->pass_mode = true;
		$this->id_conn = ftp_connect($this->host,$this->port);
    }
	function login($user=null,$pass=null){
		if($user) $this->user = $user;
		if($pass) $this->pass = $pass;
		$login = ftp_login($this->id_conn, $this->user, $this->pass);
		$this->passive_mode($this->pass_mode);
		return $login;
	}

	function login_r($user=null,$pass=null, $retries = 1){
		if($user) $this->user = $user;
		if($pass) $this->pass = $pass;
		$tries = 0;
		$login = false;
		while(!$login && ($tries < $retries)){
		  $login = ftp_login($this->id_conn, $this->user, $this->pass);
		  $tries++;
		}
		$this->passive_mode($this->pass_mode);
		return $login;
	}

	function passive_mode($turn){
		$this->pass_mode = $turn;
		ftp_pasv($this->id_conn,$this->pass_mode);
	}
	function copiar($from,$to){
		$tmp_file = "tmp".date('d-m-y-his');
		if($this->bajar($from,$tmp_file)){
			if($this->subir($tmp_file,$to)){
				unlink ($tmp_file);
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}		
	}
	function bajar($from,$to){
		return ftp_get($this->id_conn,$to,$from,FTP_BINARY);
	}
	
	function bajar_r($from,$to, $retries = 1){
	  $tries = 0;
	  $bajado = false;
	  while(!$bajado && ($tries < $retries)){
	    $bajado = @ftp_get($this->id_conn,$to,$from,FTP_BINARY);
	    $tries++;
	  }
	  return $bajado;
	
	}
	
	function subir($from,$to){
		return ftp_put($this->id_conn, $to, $from, FTP_BINARY);
	}
	function logout(){
		ftp_close($this->id_conn);
	}	
}
?>