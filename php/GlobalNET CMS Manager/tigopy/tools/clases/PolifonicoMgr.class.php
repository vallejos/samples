<?
class PolifonicoMgr{
	
	var $files = array();
	var $id;
	var $dirs = array(
		"13_mid"=>"4",
		"14_mid"=>"8",
		"15_mid"=>"Full",
		"preview"=>"mp3");
	var $ftp_log = array();
	
	function PolifonicoMgr($id){
		$this->id=$id;
	}
	
	function collectFILES(){
		foreach($_FILES as $key => $file){
			if(substr_count($key,"poli_")>0){
				$extension = substr($file['name'],-3);
				$file['ext'] = $extension;
				$this->files[$key] = $file;
			}
		}
	}
	
	function uploadFiles(){
		$oops = false;
		foreach($this->files as $key => $file){
			if(substr_count($key,"_mp3")>0){
				$indx = "preview";
				if(!copy($file["tmp_name"],$this->dirs[$indx]."/".$this->id.".".$file['ext'])){
					$oops = true;
				}
			}
			else{
				$aux = explode("_",$key);
				$indx = $aux[1]."_".$aux[2];
				$formato = $aux[1];
				if(!copy($file["tmp_name"],$this->dirs[$indx]."/".$this->id."_".$aux[1].".".$file['ext'])){
					$oops = true;
				}
			}
		}
		if(!$oops){
			return true;
		}
	}
	
	function eraseDir($dir){
		if(is_dir($dir."/")){
			$dir_contents = scandir($dir."/");
			foreach ($dir_contents as $item){
				if (is_dir($dir."/".$item) && $item != '.' && $item != '..'){
					$this->eraseDir($dir."/".$item.'/');
				}
				elseif(file_exists($dir."/".$item) && $item != '.' && $item != '..'){
					unlink($dir."/".$item);
				}
			}
			rmdir($dir."/");
		}
	}
	
	function eraseDirs(){
		foreach($this->dirs as $key => $dir){
			$this->eraseDir($dir);
		}
	}
	
	function createDirs()
	{
		foreach($this->dirs as $key => $dir){
			mkdir($dir,0777);
		}
	}
	
	function calcularCarpeta($id){
		$carpeta = (ceil($id/500)*500);
		return $carpeta;
	}
	
	function ftpFiles(){
		foreach($this->dirs as $key => $value){
			$aux = explode("_",$key);
			$ext = $aux[1];
			$formato = $aux[0];
			if($key=="preview"){
				$ftp = new ftpMgr(FTP_USA_HOST,FTP_USA_USER,FTP_USA_PASS,$value."/".$this->id.".mp3",FTP_USA_POLI_FOLDER.$this->calcularCarpeta($this->id)."/".$this->id.".mp3");
				$ftp->openConn();
				$ftp->makeFolderV(FTP_USA_POLI_FOLDER,$this->calcularCarpeta($this->id));
			}
			else{
				$ftp = new ftpMgr(FTP_UY_HOST,FTP_UY_USER,FTP_UY_PASS,$value."/".$this->id."_".$formato.".".$ext,FTP_UY_POLI_FOLDER.$this->calcularCarpeta($this->id)."/".$this->id."_".$formato.".".$ext);
				$ftp->openConn();
				$ftp->makeFolderV(FTP_UY_POLI_FOLDER,$this->calcularCarpeta($this->id));
			}
			$ret = $ftp->upload();
			$this->ftp_log[$key] = $ret;
			$ftp->closeConn();
		}
	}
}
?>