<?php
	class VideoConvert{
		var $origen;
		var $destino;
		var $formato_destino;
		var $width;
		var $height;
		var $duracion;
		var $salida;
		var $run_background;
		function VideoConvert($origen,$destino,$formato_destino="flv"){
			$this->origen = $origen;
			$this->destino = $destino;
			$this->formato_destino = $formato_destino;
			
		}
		function convertir($video_name){
			$path = "-y -i ".$this->origen.' -vcodec flv -ab 56 -ar 22050 -r 21 '.$this->destino.$video_name.'.'.$this->formato_destino;
			return $this->ejecutar($path);
		}
		function extraerMp3($mp3_name){
			if(!file_exists($this->destino.'.mp3')){
				$path = '-i '.$this->origen.' -f mp3 '.$this->destino.$mp3_name.'.mp3';
				return $this->ejecutar($path);
			}		
		}
		function extraerFrames($image_name,$ancho=80,$alto=60,$inicio='00:00:01', $fin='00:00:01', $fps=1,$fcount=false){			
			$path = '-i '.$this->origen.' ';
			$path .= '-ss '.$inicio.' ';
			$path .= '-s '.$ancho.'x'.$alto.' ';
			$path .= '-t '.$fin.' ';
			$path .= '-r '.$fps.' ';
			$path .= '-an ';
			if($fcount !== false){
				$path .= '-vframes '.$fcount.' ';
				$path .= '-y '.$this->destino.$image_name.'_%d.jpg';
			}else{
				$path .= '-vframes 1 ';
				$path .= '-f mjpeg ';
				$path .= '-y '.$this->destino.$image_name.'.jpg';
			}
			return $this->ejecutar($path);
		}
		function extraerFrame($image_name,$tiempo='00:00:01',$ancho=80,$alto=60){
			return $this->extraerFrames($image_name,$ancho,$alto,$tiempo, $tiempo);
		}
		function info(){
			$path = '-i '.$this->origen;
			$this->ejecutar($path);			
			$duracion_exp = "/Duration: [^,.]+/";
			preg_match($duracion_exp,$this->salida,$array);
			$temp = split("Duration: ",$array[0]);
			$duracion = trim($temp[1]);								
			$video_exp = "/Video: .*./";
			preg_match($video_exp,$this->salida,$array);
			$temp = split(":",$array[0]);
			$w_h = split(",",$temp[1]);
			$temp = split("x",$w_h[2]);
			$w = trim($temp[0]);
			$h = trim($temp[1]);
			$this->width = $w;
			$this->height = $h;
			$this->duracion = $duracion;			
			return true;
		}
		function ejecutar($path){
			$output = $this->runExternal(FFMPEG.' '.$path);
		//	echo $output->salida;
			$this->salida = $output->salida;
			if($output->code){
				return false;
			}else{
				return true;
			}
		}
		function runExternal($cmd) {
			$descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w"));
			$pipes= array();
			$process = proc_open($cmd, $descriptorspec, $pipes);
			$salida = "";
			if (!is_resource($process)) return false;
			fclose($pipes[0]);

			stream_set_blocking($pipes[1],false);
			stream_set_blocking($pipes[2],false);
			$todo= array($pipes[1],$pipes[2]);

			while( true ) {
				$read= array();
				if( !feof($pipes[1]) ) $read[]= $pipes[1];
				if( !feof($pipes[2]) ) $read[]= $pipes[2];

				if (!$read) break;
				$ready= stream_select($read, $write=NULL, $ex= NULL, 2);

				if ($ready === false) {
					break; #should never happen - something died
				}

				foreach ($read as $r) {
					$s= fread($r,1024);
					$salida .= $s;
				}
			}
			
			fclose($pipes[1]);
			fclose($pipes[2]);
			$code= proc_close($process);
			$retorno = (object) array('code'=>$code,'salida'=>$salida);
			return $retorno;
		}
	}
?>