<?php



class personalGame {
	// estos cambian
	private $uniqueId;
	private $service = ""; // obtener de tpim
	private $name;
	private $title;
	private $preview_uri;
	private $variantUri;
	private $manufacturer;
	private $pubYear;
	private $multiplayer;

	private $language;
        private $tag;
        private $subTag;
        private $longDescription;
        private $shortDescription;
        private $keywords;
        private $provider;
        private $availableContent = array();
        private $dirToWrite;
        private $previewExists;
        private $thumbnailExists;
        private $catLvl;
        private $webCat;

	private $variants;
	private $archivo;
	private $preview;
	private $previewFilename;
	private $contentFilename;
        private $thumbnailFilename;

	// estos estan predefinidos y nunca cambian
	private $deployed;
	private $promotiononly;
	private $promotionkey;
	private $startDate;
	private $validUntil;
	private $adult;

	// internos de la clase
	private $debug_log;
	private $contentId;
	private $contentType;
	private $dbc;
	private $logFile = "/root/www/tools/cms/amdocs/logs/game.class.log";
	private $sizes = array("480X320","360X640","320X240","240X320","220X176","128X160","128X128","128X90","300X300");
                           //,"480X800","480X360","240X432","240X400","176X200");

	// mandatory fields
//      private $mandatoryXMLFields = array("externalId", "deployed", "promotiononly", "startDate", "validUntil", "service", "adult", "name", "title", "tag", "premiumResource");
	private $mandatoryXMLFields = array("externalId", "deployed", "promotiononly", "startDate", "validUntil", "adult", "name", "title", "tag");




        private $arrayDatos = array();
        private $files = array();
        private $nombre_cat = "";
        private $arrayDevicesCompatibles = array();

        //Agregados para personal
        private $developerContentId;
        private $previewFilenames = array();
        private $screenshotsFilenames = array();
        private $categorias = array();
        private $tipoCarga;
        private $xml_template_file;
        private $version;
        private $filename;
        private $oldEditioName;

	// constructor
	function __construct($dbc, $idC, $tipoCarga, $debug=FALSE) {
                  global $globalIncludeDir;
		$this->addLog("************************************ C O M E N Z A N D O *****************************************************************");
		global $servicesMig, $prtmig;
	
		if ($dbc === NULL) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: DB Null");
		else $this->dbc = $dbc;

                if($tipoCarga == "") {
                    throw new Exception("<".__LINE__."> ".__METHOD__."ERROR, el tipo de carga no puede ser NULO");
                }

                $this->developerContentId = PERSONAL_SER."|".NROCORTO."|".$idC;
                $this->tipoCarga = ucfirst(strtolower($tipoCarga));
                if(strtolower($tipoCarga) == "new") {
                    $this->version = "1.0";
                } else {
                    $this->version = $this->getEditionVersion();
                }
                $this->contentId = $idC;
                $this->uniqueId = $idC;

                if(strtolower($tipoCarga) == "new") {
                    $this->xml_template_file = "$globalIncludeDir/templates/game.xml";
                } elseif(strtolower($tipoCarga) == "add") {
                    $this->xml_template_file = "$globalIncludeDir/templates/game_add.xml";
                }

		$this->debug_mode = $debug;
	
	}


      public function setCategorias($cats) {
          $this->categorias = $cats;
      }


      public function getEditionVersion() {
          //TODO: Ver como manejar el versionado automatico...
          return "1.1";
      }


      /**
       * Setea el edition name de la versión anterior, para ser utilizada en los Add
       * @param <type> $n
       */
      public function setOldEditionName($n) {
          $this->oldEditioName = $n;
      }

      /**
       * Devuelve el edition name para la versión actual
       * @return <type>
       */
      public function getEditionName(){
          return str_replace(" ", "", $this->name).str_replace(".", "", $this->version);
      }



      // destructor
      function __destruct() {
	    $this->addLog("<".__LINE__."> ".__METHOD__);
	    $this->addLog("************************************** F I N *****************************************************************************");
	    if ($this->debug_mode === TRUE) {
		  $this->logDebug();
	    } else {
//		  echo "no+ log";
	    }
      }


	public function getUniqueId() {
		return $this->uniqueId;
	}


        public function cargarCompatibilidad($datos) {
            $this->files = $datos;
        }

	// carga los datos del contenido
	public function loadContent() {
		$this->addLog("<".__LINE__."> ".__METHOD__);
	
	//	$providerSql = ($provider == "") ? "" : " AND c.proveedor = $provider";

		$sql = "SELECT c.*, cc.descripcion nombreCat, gi.screenshots,
                        gi.descr, gi.descr_wap
                        FROM Web.contenidos c INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria)
                        INNER JOIN Web.gamesInfo gi ON (gi.game=c.id)
                        INNER JOIN Web.contenidos_proveedores cp ON (cp.id=c.proveedor)
                        WHERE c.id=$this->contentId
                        AND c.tipo='".WAZZUP_GAME."' $providerSql";
//echo $sql;
		$this->addLog("SQL: ".$sql);
		$rs = mysql_query($sql, $this->dbc->db);
                
		if (!$rs) throw new Exception("ERROR: no se pueden obtener datos del contenido $this->contentId ");
		else $obj = mysql_fetch_object($rs);

		$this->contentType = $obj->tipo;			
                $this->nombre_cat = $obj->nombreCat;

		//$this->adult = ($obj->xxx == "0") ? "false" : "true";

		// genero nombres unicos para contenidos y preview
		$this->setFilenames();

		$this->name = konvert($obj->nombre);
		$this->title = str_replace("-", " ",  konvert("$obj->nombre"));
		$this->longDescription = strip_tags(substr(konvert($obj->descr),0, 253)."...");
                $this->shortDescription = konvert(substr($obj->descr_wap,0,37)."...");
                if($this->shortDescription == "") {
                    $this->addLog("La descripción corta es nula, usando nombre de categoria...");
                    $this->shortDescription = $obj->nombreCat;
                }

		$this->archivo = str_replace("netuy", "contenido", $obj->archivo);
		$this->archivo = str_replace("128x128", "600x600", $this->archivo);

		//$this->language = "English";

		$screens = explode(",", $obj->screenshots);
		$pathTo = $screens[0];
		$screens = $screens[1];
		$path = explode(".", $screens);
		$path = $path[0].".gif";
		$this->preview = $pathTo.$path;

                $carpetaID = (ceil($this->contentId/500)*500);

                $this->previewFTPFilename = "/contenido/java/100x100/" . $carpetaID . "/" . $this->contentId . ".gif";
                $this->previewFilename = $this->contentId.".gif";
//		$this->preview = str_replace("128x128", "110x110", $this->preview);

		//$this->manufacturer = $obj->manufacturer;
	//	$this->pubYear = date("Y");
	//	$this->multiplayer = "false";

//		$this->preview_uri = $this->getPreviewFilename();
	//	$this->variantUri = $this->getContentFilename();
	}

	public function setTag($cat) {
		$this->tag = konvert($cat);
	}

	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	public function setShortDesc($shortDescription) {
		$this->shortDescription = konvert($shortDescription);
	}

	public function setLongDesc($longDescription) {
		$this->longDescription = konvert($longDescription);
	}

	public function setSubTag($cat) {
		$this->subTag = konvert($cat);
	}

	public function getPreview() {
		return $this->preview;
	}

	public function getContent() {
		return $this->archivo;
	}

	public function getPreviewFilename() {
		return $this->previewFilename;
	}

	public function getContentFilename() {
		return $this->contentFilename;
	}

	public function getJadFilename($i) {
		return "content_".$this->uniqueId."_$i.jad";
	}

	public function getJarFilename($i) {
		return "content_".$this->uniqueId."_$i.jar";
	}

      private function genException($type, $line, $method, $msg) {
	    $this->addLog("$type <$line> $method $msg");
	    if ($type == "CRIT") throw new Exception("$type <$line> $method $msg");
      }

      public function getName() {
          return $this->name;
      }


      // revisa que los parametros que son obligatorios esten definidos
      // devuelve TRUE si esta todo ok
      private function checkMandatoryField() {
	    $ok = TRUE;
	    foreach ($this->mandatoryXMLFields as $fields) {
		  if (!isset($this->$fields) || $this->$fields=="" || is_null($this->$fields)) {
			$ok = FALSE;
			$this->addLog("mandatory field $fields not found");
		  }
	    }
	    return $ok;
      }

      public function setVersion($v) {
          $this->version = $v;
      }


      public function getArrayDatos() {
          return $this->arrayDatos;
      }

      public function getCompatibleDevices() {
        return $this->arrayDevicesCompatibles;
      }

	public function genXML() {
          
		$this->addLog("<".__LINE__."> ".__METHOD__);
		$this->addLog("Generando XML para $this->contentId");

		if (!is_numeric($this->contentId) || $this->contentId<=0) $this->genException("CRIT", __LINE__, __METHOD__, ": content_id no valido");

                $fp = fopen($this->xml_template_file, "r");
                $xml_template = "";
                $xml = "";
                if($fp) {
                    $xml_template = fread($fp, filesize($this->xml_template_file));
                    fclose($fp);
                }

                if($xml_template == "") {
                    $this->genException("CRIT", __LINE__, __METHOD__, ": No se encuentra el template del XML : " . $xml_template_filename);
                }

                $xml = str_replace("%OLD_EDITION_NAME%", $this->oldEditioName, $xml_template);
                $xml = str_replace("%DISPLAY_NAME%", $this->name, $xml);
                $xml = str_replace("%DEVELOPER_CONTENT_ID%", $this->developerContentId, $xml);
                $xml = str_replace("%ACTION_TYPE%", $this->tipoCarga, $xml);
                $xml = str_replace("%SHORT_DESC%", $this->shortDescription, $xml);
                $xml = str_replace("%LONG_DESC%", $this->longDescription, $xml);

                $xml = str_replace("%VERSION%", $this->version, $xml);
                $ed_name = $this->getEditionName();
                $xml = str_replace("%EDITION_NAME%", $ed_name, $xml);


                $cats_xml = "";
                foreach($this->categorias as $cat) {
                    $cats_xml .= "<Category>" . $cat. "</Category>";
                }
                $xml = str_replace("%CATEGORIAS%", $cats_xml, $xml);


                $devices_xml = "";
                foreach($this->files as $compat_data) {
                    $devices_xml .= "<TargetDevice>".$compat_data['modelo']."</TargetDevice>";
                }
                $xml = str_replace("%TARGET_DEVICES%", $devices_xml, $xml);



                $wap_prev_tag = '<File src="/previews/'.$this->previewFilenames[0].'" />';
                $wap_prev_tag .= '<File src="/previews/'.$this->previewFilenames[1].'" />';
                $xml = str_replace("%PREVIEWS_WAP%", $wap_prev_tag, $xml);


                $icons_xml = '<SmallIcon src="/previews/'.$this->previewFilenames[1].'" />';
                $icons_xml .= '<LargeIcon src="/previews/'.$this->previewFilenames[0].'" />';
                $xml = str_replace("%ICONS%", $icons_xml, $xml);


                $web_prev_tags = "";
                foreach($this->screenshotsFilenames as $fn) {
                    $web_prev_tags .= '<File src="/previews/'.$fn.'" />';
                }

                $xml = str_replace("%PREVIEWS_WEB%", $web_prev_tags, $xml);


		$this->addLog("XML Generado:\n".$xml);

		return $xml;
	}



function downloadBinaries($dirToWrite){

    $content_download = FALSE;
    // descargo contenido por FTP
    $ftpCon = new Ftp();
    $retries=0;
    $i=0;
    $conectado = FALSE;

    echo "<li> connecting to FTP...</li>";
    $conectado = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
    if ($conectado === TRUE) {
            echo "+ connection Ok!";
            $download_errors = 0;
            //foreach ($this->files as $datos) {
                    $archivo = $this->filename; //$datos['archivo'];

                    $jad = str_replace("netuy", "contenido", $archivo);
                    $jar = str_replace(".jad", ".jar", $jad);

                    $info = pathinfo($jad);
                    $toJadName = $info['basename'];
                    $info = pathinfo($jar);
                    $toJarName = $info['basename'];
                    $to = $dirToWrite."/".$toJadName;
                    echo "<li>descargando jad <b>$jad</b> => <b>$to</b>...</li>";
                    $bajado = $ftpCon->bajar_r($jad, $to, FTP_DOWN_RETRIES);
                    if ($bajado === TRUE) {
                            echo "+ jad Ok!<br/>";
                            $to = $dirToWrite."/".$toJarName;
                            echo "<li>descargando jar <b>$jar</b> => <b>$to</b>...</li>";
                            $bajado = $ftpCon->bajar_r($jar, $to, FTP_DOWN_RETRIES);
                            if ($bajado === TRUE) {
                                    echo "+jar Ok!<br/>";
                                    $content_download = TRUE;
                            } else {
                                    // jar no encontrado; intentando leer jar del jad
                                    $toJad = $dirToWrite."/".$toJadName;
                                    $jadLines = file($toJad);
                                    $jarName = getJarNamefromJad($jadLines);
                                    $pathName = pathinfo($jar);
                                    $newJar = $pathName['dirname']."/".$jarName;

                                    echo "- jar <b>$jar</b> not found...<br/>";
                                    echo "- trying <b>$newJar</b>...<br/>";

                                    $bajado = $ftpCon->bajar_r($newJar, $to, FTP_DOWN_RETRIES);
                                    if ($bajado === TRUE) {
                                            echo "+ jar <b>$newJar</b> Ok!<br/>";
                                            $content_download = TRUE;
                                    } else {
                                            echo "<li>ERROR: descargando el jar <b>$newJar</b> del ftp</li>";
                                    }
                            }
                    } else {
                            echo "- jad <b>$jad</b> not found...<br/>";
                            $content_download = FALSE;
                            $download_errors++;
                    }

                  //  $newAdded++;

        //    }
    } else {
            echo "- ERROR: no se puede conectar al ftp<br/>";
    }

    if ($content_download != FALSE || ($download_errors != count($juegos))) {
        if(strtolower($this->tipoCarga) == "new") {
            $this->downloadContent($ftpCon);
        }
         return true;
    } else {
        $log .= " <h2>Hubo un error al descargar el JAD o el JAR</h2>";
        return false;
    }
}


    public function getVersion() {
        return $this->version;
    }

    public function downloadContent($ftpCon) {
        //TODO: ver como obtener los archivos "extras"
        
	global $tmpDir;
        $this->dirToWrite = $tmpDir."/" . $this->uniqueId."/previews";
        if (!is_writable($tmpDir)) {
            die("ERROR: ".$tmpDir." is not writable\n");
        } else {
            echo " creando el directorio para las imagenes...\n";
            exec("mkdir {$this->dirToWrite}");
        }

        if (!is_writable($this->dirToWrite)) {
            die("ERROR: {$this->dirToWrite} is not writable\n");
        }

        // Generamos el preview
        if(!($this->previewExists = $this->generatePreview())) {
            echo "No se pudo bajar el preview para \"" . $this->title . "\"<br />";
        }



          // Generamos el thumb
     /*   if(!($this->thumbnailExists = $this->generateThumbnail())) {
            echo "No se pudo bajar el thumbnail para \"" . $this->title . "\"<br />";
        }*/
//        return $this->availableContent;*/
    }

    public function getDirToWrite() {
        return $this->dirToWrite;
    }


	public function addDevice($device, $jad, $jar) {
		$this->variants .= '<variant>'."\n";
		$this->variants .= '<device>'.$device.'</device>'."\n";
		$this->variants .= '<jar uri="'.$jar.'" />'."\n";
		$this->variants .= '<jad uri="'.$jad.'" />'."\n";
		$this->variants .= '</variant>'."\n";
	}


////////////////////////////////////////////////////////////
//
// TODAS LA FUNCIONALIDAD PRIVADA DE LA CLASE
//
////////////////////////////////////////////////////////////

	private function generatePreview() {
		$ftpCon = new Ftp(FTP_USA, FTP_USA_USR, FTP_USA_PWD);
		$connected = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
		if(!$connected) {
			return false;
		}
		$from = str_replace("%RESOLUTION%", PREVIEW_WIDTH."x".PREVIEW_HEIGHT, $this->getPreview());
		$from = str_replace("contenido", "netuy", $from);
                $from = "/www.wazzup.com.uy/".$from;
		$previewFilename = "preview" .str_replace(" ", "_", sanitizeString($this->title)) . ".gif";
		$tmp = $this->dirToWrite."/".$previewFilename;
                echo "preview $from => $tmp";
		$bajado = $ftpCon->bajar_r($from, $tmp, FTP_DOWN_RETRIES);

                $targetDir = $this->dirToWrite;
                $prev_sin_ext = str_replace(".gif", "", $previewFilename);
                $ret = copy_format_img($prev_sin_ext, $targetDir, 80, 80, "",".gif",".gif");
                echo '<li>Retorno de la copia del preview: ' . print_r($ret, true);
                $ret = copy_format_img($prev_sin_ext, $targetDir, 40, 40, "",".gif",".gif");
                echo '<li>Retorno de la copia del preview: ' . print_r($ret, true);

                //Borro el preview original, porque no se sube
                $shellCmd = "cd ".$this->dirToWrite."; rm -f $previewFilename";
                exec($shellCmd);
                $this->previewFilenames[] = $prev_sin_ext."_80x80.gif";
                $this->previewFilenames[] = $prev_sin_ext."_40x40.gif";

                $carpeta = $this->calcularCarpeta($this->contentId);
                $pathScreenshots = "/previews/java/240x320/".$carpeta."/";
                $this->screenshotsFilenames[] = $this->contentId."_1.jpg";
                $this->screenshotsFilenames[] = $this->contentId."_2.jpg";
                $this->screenshotsFilenames[] = $this->contentId."_3.jpg";

                $ftpCon = new Ftp(FTP_241, FTP_241_USR, FTP_241_PWD);
                $connected = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
                foreach($this->screenshotsFilenames as $fn) {
                    
                    $tmp = $this->dirToWrite."/".$fn;
                    $from = $pathScreenshots."".$fn;
                    echo '<li>Bajando screenshot: '.$from.' => '. $tmp.'</li>';
                    $bajado = $ftpCon->bajar_r($from, $tmp, FTP_DOWN_RETRIES);
                    //echo '<li>Resultado del FTP: ' . print_r($bajado, true).'</li>';
                    $cnvrt = new convert($tmp,"jpg", 240, 320);
                    echo '<li>Convirtiendo screenshot para que pese menos....</li>';
                    $cnvrt->saveIMG($tmp);

                }
         

		return $bajado;
	}

        private function calcularCarpeta($id){
            $carpeta = (ceil($id/500)*500);
            return $carpeta;
        }

	private function generateThumbnail() {
		$ftpCon = new Ftp(FTP_USA, FTP_USA_USR, FTP_USA_PWD);
		$connected = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
		if(!$connected) {
			return false;
		}
		$from = str_replace("%RESOLUTION%", PREVIEW_WIDTH."x".PREVIEW_HEIGHT, $this->getPreview());
		$from = str_replace("contenido", "netuy", $from);
		$this->thumbnailFilename = "thumbnail" .str_replace(" ", "_", sanitizeString($this->title)) . ".gif";
		$tmp = $this->dirToWrite."/".$this->thumbnailFilename;
echo "thumb $from => $tmp";
		$bajado = $ftpCon->bajar_r($from, $tmp, FTP_DOWN_RETRIES);

		return $bajado;
	}

	// log
	private function addLog($msg) {
		$this->logmsg .= $msg."\n";
	}



	// blah blah
	private function logDebug() {
		$fp = fopen($this->logFile, "a+");
		fwrite($fp, $this->logmsg);
		fclose($fp);
//		echo "<textarea cols=50 rows=10>$this->logmsg</textarea>";
	}


      public function setFilename($arc) {
        $this->filename = $arc;
      }



      // formatea un valor
      // ej: "000023000", "2300000", "0000023", "     23", etc
      private function dataFormat($data, $length, $padding="0", $align="") {
	    $format = "%{$align}{$padding}{$length}s";
	    $formated_data = sprintf($format, $data);
	    return $formated_data;
      }



	// genera el external Id y guarda el id nuevo en la base
	private function setFilenames() {
		$this->addLog("<".__LINE__."> ".__METHOD__);
//		$this->contentFilename = str_replace(" ", "", $this->title) . "_%RESOLUTION%.gif";
		$this->previewFilename = "preview" . str_replace(" ", "", sanitizeString($this->title)) . ".gif";
		$this->thumbnailFilename = "thumbnail" . str_replace(" ", "", sanitizeString($this->title)) . ".gif";
	}


      // genera el external Id y guarda el id nuevo en la base
      private function setExternalId() {
	    $this->addLog("<".__LINE__."> ".__METHOD__);
	    $sql = "SELECT MAX(id) lastId FROM admins.ancel_drutt ";
	    $this->addLog("SQL: ".$sql);
	    $rs = mysql_query($sql, $this->dbc->db);
	    if (!$rs) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: no se puede obtener lastId procesado");
	    else $obj = mysql_fetch_object($rs);

	    if (!is_numeric($obj->lastId) || $obj->lastId<=0) throw new Exception("<".__LINE__."> ".__METHOD__." ERROR: uniqueId no valido");
	    else $this->uniqueId = $obj->lastId + 1;

	    // formateo el uniqueId, ej: "00005"
	    $format = "%{$this->uniqueId_align}{$this->uniqueId_padding}{$this->uniqueId_length}s";

	    $this->externalId = PROVIDER_CODE."-".DRUTT_GAME."-".$this->dataFormat($this->uniqueId, 5, "0");

	    $sql = "INSERT INTO admins.ancel_drutt SET time=CURTIME(), date=CURDATE(), type='$this->contentType', external_id='$this->externalId' ";
	    $this->addLog("SQL: ".$sql);
	    $rs = mysql_query($sql, $this->dbc->db);
	    if (!$rs) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: no se puede insertar nuevo id procesado");
      }


	public function getThumbnailFilename() {
		return $this->thumbnailFilename;
	}


}




?>