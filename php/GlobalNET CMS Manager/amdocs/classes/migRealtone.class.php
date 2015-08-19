<?php

class migRealtone {
// estos cambian
    private $album;
    private $title;
    private $tag;
    private $subTag;
    private $tagEng;
    private $subTagEng;
    private $artist;
    private $publicationYear;
    private $provider;
    private $shortDescription;
    private $longDescription;
    private $keywords;
    private $catLvl;
    private $webCat;
    private $duration;
    private $dirToWrite;
    private $isPolytone = false;
    private $rating;
    private $archivo;
    private $contentFilename;
    private $adult;
    private $arraySubForm;
    

    // internos de la clase
    private $contentId;
    private $contentType;
    private $dbc;
    private $logFile;

    private $marca;
    private $festivo;
    private $strTipo;
    private $langs;
    private $merchants;
    private $abstract;
    private $creator;
    
    private $dgp;
    private $isrc;

    // constructor
    function __construct($dbc, $debug=FALSE, $catLvl = "", $webCat = "", $isPolytone = FALSE, $rating = "", $marca="", $festivo="") {
        $this->addLog("************************************ C O M E N Z A N D O *************************************************");

	$this->logFile = "/mnt/storage/www/tools/cms/amdocs/logs/realtone.class.log";
	
        if ($dbc === NULL) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: DB Null");
        else $this->dbc = $dbc;

        $this->debug_mode = $debug;
        $this->album = "";
        $this->publicationYear = date("Y");
        $this->catLvl = $catLvl;
        $this->webCat = konvert($webCat);
        $this->isPolytone = $isPolytone;
        $this->rating = $rating;

        /*
        if($this->webCat == "Tonos Premium") {
            $this->strTipo = "(Poli)";
        } elseif($this->webCat == "Tonos Reales") {
            $this->strTipo = "(Real)";
        } else {
            $this->strTipo = "";
        }
*/
        /*
        if ($this->isPolytone === FALSE) $this->strTipo = "(Real)";
        else $this->strTipo = "(Poli)";
*/

        /*
        $this->marca = (empty($marca)) ? " Ideas" : " $marca";
        $this->festivo = (empty($festivo)) ? "" : " ($festivo)";
*/
        
        $this->marca = $marca;
        $this->festivo = $festivo;
        
        $this->tagEng = "" ;
        $this->subTagEng = "";
    }

    function __destruct() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $this->addLog("************************************** F I N ************************************************************");
        if ($this->debug_mode === TRUE) {
            $this->logDebug();
        } else {
	    
        }
    }

    public function setLangs($langs) {
        $this->langs = $langs;
    }

    public function setMerchants($merchants) {
        $this->merchants = $merchants;
    }

    private function obtainCarrier($pais) {
        global $_MERCHANTS;
        return $_MERCHANTS[$pais];
    }

    private function obtainLang($pais) {
        global $_LANGS;
        return $_LANGS[$pais];
    }


    // carga los datos del contenido
    public function loadContent($content_id) {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        if (!is_numeric($content_id) || $content_id<=0) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: content_id no valido");
        else $this->contentId = $content_id;

        $wazzupType = ($this->isPolytone) ? WAZZUP_POLYTONE : WAZZUP_REALTONE;

        $sql = "SELECT c.*, cc.descripcion nombreCat, cc.xxx, cp.nombre provider, u.dgp, u.isrc 
                FROM Web.contenidos c 
                INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria)
                INNER JOIN Web.contenidos_proveedores cp ON (c.proveedor = cp.id)
                LEFT JOIN Web.universal u ON (u.contenido=c.id) 
                WHERE c.id=$this->contentId  AND c.tipo='".$wazzupType."'" ;
        $this->addLog("SQL: ".$sql);
  //      echo $sql;
        $rs = mysql_query($sql, $this->dbc->db);
        if (!$rs) throw new Exception("ERROR: no se pueden obtener datos del contenido $this->contentId ");
        else $obj = mysql_fetch_object($rs);

        $this->contentType = $obj->tipo;
        $this->provider = $obj->provider;
        $this->adult = ($obj->xxx == "0") ? "false" : "true";
        //$this->keywords = explode(",", $obj->keywords);
        if (empty($obj->keywords)) echo "ERROR: keywords no encontrados";
        $this->keywords = array_map("trim", explode(",", konvert($obj->keywords)));

//        $this->keywords = array_map("sanitizeString", $this->keywords);

        $this->artist = konvert($obj->autor);
        //$this->title = konvert("$obj->nombre");
        $this->setTitle($obj->nombre);
        $this->archivo = str_replace("netuy", "contenido", $obj->archivo);

        $this->shortDescription = $this->artist.$this->festivo;
        $this->longDescription = "Tema " . $this->title . " de " . $this->artist;

        $this->isrc = trim($obj->isrc);
        $this->dgp = trim($obj->dgp);
        
        $this->setCreator();
        
        $this->setAbstract();
        
        $this->setFilenames();
    }

    private function genException($type, $line, $method, $msg) {
        $this->addLog("$type <$line> $method $msg");
        if ($type == "CRIT") throw new Exception("$type <$line> $method $msg");
    }

    public function getArraySubForm(){
        return $this->arraySubForm;
    }

    public function setTag($cat, $lang='es') {
            if ($lang == "en") $this->tagEng = $cat;
            else $this->tag = $cat;
    }

    public function setSubTag($cat, $lang='es') {
            if ($lang == "en") $this->subTagEng = $cat;
            else $this->subTag = $cat;
    }


    public function genXML() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $this->addLog("Generando XML para $this->contentId");
        //$keywordStr = "\"" . implode("\r\n", $this->keywords) . "\"";
        $keywordStr = implode(" ", $this->keywords);

        $this->arraySubForm = array(
                            "Content Title" => $this->getTitle(),
                            "Website Category" => $this->webCat,
                            "Genres" => $this->tag,
                            "Creator" => $this->getCreator(),
                           );

        if (!is_numeric($this->contentId) || $this->contentId<=0) $this->genException("CRIT", __LINE__, __METHOD__, ": content_id no valido");

        $carriers = explode(",", CARRIERS);
        $langs = explode(",", LANGS);

        $xmlstr = XML_HEADER.'<mgdToneProduct xmlns="http://www.qpass.net/telcel/mgdTone"
	xmlns:qpass="http://www.qpass.com/content" xmlns:jcr="http://www.jcp.org/jcr/1.0">'."\n";

        // TAG:MERCHANT
        foreach($this->merchants as $merchant) {
            $carrier = $this->obtainCarrier($merchant);
            $xmlstr .= "\t<qpass:merchant>$carrier</qpass:merchant>\r\n";
        }

//		$rating = ($this->adult == "true") ? "18+" : $this->rating;
        $rating = $this->rating;
        $xmlstr .=<<<XML
                <qpass:rating>
                <qpass:scheme>Mexico</qpass:scheme>
                <qpass:value>$rating</qpass:value>
                </qpass:rating>
XML;
        $xmlstr .= "\t<providerGivenContentId>{$this->contentId}</providerGivenContentId>";

        // TAG:TITLE
        foreach($this->langs as $lang) {
            if ($lang == "JM") $xmlstr .= "\t<title qpass:lang=\"{$this->obtainLang($lang)}\">{$this->title} {$this->strTipo}</title>\r\n"; // TODO: obtener version traducida
            else $xmlstr .= "\t<title qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getTitle()} {$this->strTipo}</title>\r\n";
        }

        $xmlstr .= "\t<genres>{$this->tag}</genres>\r\n";
        $xmlstr .= "\t<subgenres1>{$this->subTag}</subgenres1>\r\n";
        $xmlstr .= "\t<artist>{$this->getCreator()}</artist>\r\n";
        $xmlstr .= "\t<label>{$this->provider}</label>\r\n";

//        $xmlstr .= "\t<grid>{$this->dgp}</grid>\r\n";
//        $xmlstr .= "\t<isrc>{$this->isrc}</isrc>\r\n";

        // TAG:KEYWORDS
        foreach($this->langs as $lang) {
            foreach($this->keywords as $keyword) {
                if ($lang == "JM") $xmlstr .= "\t<keywords qpass:lang=\"{$this->obtainLang($lang)}\">{$keyword}</keywords>\r\n"; // TODO: obtener version traducida
                else $xmlstr .= "\t<keywords qpass:lang=\"{$this->obtainLang($lang)}\">{$keyword}</keywords>\r\n";
            }
        }

        // TAG:SHORTDESCRIPTION
        foreach($this->langs as $lang) {
            if ($lang == "JM") $xmlstr .= "\t<shortDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getAbstract()}</shortDescription>\r\n"; // TODO: obtener version traducida
            else $xmlstr .= "\t<shortDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getAbstract()}</shortDescription>\r\n";
        }

        // TAG:LONGDESCRIPTION
        foreach($this->langs as $lang) {
            if ($lang == "JM") $xmlstr .= "\t<longDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getLongDescription()}</longDescription>\r\n"; // TODO: obtener version traducida
            else $xmlstr .= "\t<longDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getLongDescription()}</longDescription>\r\n";
        }


        $xmlstr .= "\t<categoryLevel>{$this->catLvl}</categoryLevel>\r\n";

        $curdate = date("d-M-Y");
        $xmlstr .= "\t<creationDate>$curdate</creationDate>\r\n";
        $xmlstr .= "\t<releaseDate>$curdate</releaseDate>\r\n";
        $xmlstr .= "\t<duration>{$this->duration}</duration>\n";
        $xmlstr .= "<websiteCategory>{$this->webCat}</websiteCategory>\r\n";

        $filename = $this->getContentFilename();
        $xmlstr .= "\t<premium>";
        $nodename = (!$this->isPolytone) ? "mp3Resource" : "midiResource";
        $mime = (!$this->isPolytone) ? "audio/mpeg" : "audio/midi";

        $xmlstr .=<<<XML
                <$nodename>
			<qpass:resourceFilename>$filename</qpass:resourceFilename>
			<qpass:mimeType>$mime</qpass:mimeType>
		</$nodename>
XML;
        $xmlstr .= "\t</premium>";
        $xmlstr .= "</mgdToneProduct>";

        $this->addLog("XML Generado:\n".$xml);
        return $xmlstr;
    }



    public function updateXML() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $this->addLog("Generando XML UPDATE para $this->contentId");
        //$keywordStr = "\"" . implode("\r\n", $this->keywords) . "\"";
        $keywordStr = implode(" ", $this->keywords);

        $this->arraySubForm = array(
                            "Content Title" => $this->getTitle(),
                            "Website Category" => $this->webCat,
                            "Genres" => $this->tag,
                            "Creator" => $this->getCreator(),
                           );

        if (!is_numeric($this->contentId) || $this->contentId<=0) $this->genException("CRIT", __LINE__, __METHOD__, ": content_id no valido");

        $carriers = explode(",", CARRIERS);
        $langs = explode(",", LANGS);

        $xmlstr = XML_HEADER.'<mgdToneUpdate xmlns="http://www.qpass.net/telcel/mgdTone"
	xmlns:qpass="http://www.qpass.com/content" xmlns:jcr="http://www.jcp.org/jcr/1.0">'."\n";

        $xmlstr .= "\t<qpass:ppuid>$this->contentId</qpass:ppuid>\r\n";

        // TAG:MERCHANT
        foreach($this->merchants as $merchant) {
            $carrier = $this->obtainCarrier($merchant);
            $xmlstr .= "\t<qpass:merchant>$carrier</qpass:merchant>\r\n";
        }

        // TAG:RATING
        if (!empty($this->rating)) {
            $rating = ($this->adult == "true") ? "18+" : $this->rating;
            $xmlstr .= "
            <qpass:rating>
                    <qpass:scheme>Mexico</qpass:scheme>
                    <qpass:value>$this->rating</qpass:value>
                    <qpass:comment>Comment</qpass:comment>
            </qpass:rating>\n";
        }

//        $xmlstr .= "\t<grid>{$this->dgp}</grid>\r\n";
//        $xmlstr .= "\t<isrc>{$this->isrc}</isrc>\r\n";
        
        // TAG:TITLE
        foreach($this->langs as $lang) {
            if (mustTranslateToEN($lang, $this->contentType) === TRUE) {
                $trans = translate($this->dbc, $this->contentId, 'nombre');
                if (($trans === FALSE) || ($trans === NULL)) reportError("ERROR: Ocurrio un error traduciendo $this->contentId => 'nombre'");
                else $xmlstr .= "\t<title qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getTitle()}</title>\r\n";
            } else $xmlstr .= "\t<title qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getTitle()}</title>\r\n";
        }

        // TAG:GENRES
        if (!empty($this->tag)) $xmlstr .= "\t<genres>{$this->tag}</genres>\n";
        if (!empty($this->tagEng)) $xmlstr .= "\t<genres>{$this->tagEng}</genres>\n";

        // TAG:SUBGENRES1
        if (!empty($this->subTag)) $xmlstr .= "\t<subgenres1>{$this->subTag}</subgenres1>\n";
        if (!empty($this->subTagEng)) $xmlstr .= "\t<subgenres1>{$this->subTagEng}</subgenres1>\n";

        // TAG:KEYWORDS
        foreach($this->langs as $lang) {
            // en el caso de los keywords es diferente
            if (mustTranslateToEN($lang, $this->contentType) === TRUE) {
                $trans = translate($this->dbc, $this->contentId, 'keywords');
                if (($trans === FALSE) || ($trans === NULL)) {
                    reportError("ERROR: Ocurrio un error traduciendo $this->contentId => 'keywords'");
                } else {
                    // cargo array con keywords traducidos
                    $keywords = explode(",", $trans);
                    foreach($keywords as $kw) {
                        $kw = trim($kw);
                        if (!empty($kw)) $xmlstr .= "\t<keywords qpass:lang=\"{$this->obtainLang($lang)}\">".konvert($kw)."</keywords>\r\n";
                    }
                }
            } else {
                foreach($this->keywords as $keyword) {
                    $keyword = trim($keyword);
                    if (!empty($keyword)) {
                        $xmlstr .= "\t<keywords qpass:lang=\"{$this->obtainLang($lang)}\">{$keyword}</keywords>\r\n";
                    }
                }
            }
        }

        // TAG:SHORTDESCRIPTION
        foreach($this->langs as $lang) {
            if (mustTranslateToEN($lang, $this->contentType) === TRUE) {
                $trans = translate($this->dbc, $this->contentId, 'short_desc');
                if (($trans === FALSE) || ($trans === NULL)) reportError("ERROR: Ocurrio un error traduciendo $this->contentId => 'short_desc'");
                else $xmlstr .= "\t<shortDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getAbstract()}</shortDescription>\r\n";
            } else $xmlstr .= "\t<shortDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getAbstract ()}</shortDescription>\r\n";
        }

        // TAG:LONGDESCRIPTION
        foreach($this->langs as $lang) {
            if (mustTranslateToEN($lang, $this->contentType) === TRUE) {
                $trans = translate($this->dbc, $this->contentId, 'long_desc');
                if (($trans === FALSE) || ($trans === NULL)) reportError("ERROR: Ocurrio un error traduciendo $this->contentId => 'long_desc'");
                else $xmlstr .= "\t<longDescription qpass:lang=\"{$this->obtainLang($lang)}\">".konvert($trans)."</longDescription>\r\n";

            } else $xmlstr .= "\t<longDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getLongDescription()}</longDescription>\r\n";
        }

        // TAG:WEBSITECATEGORY
        if (!empty($this->webCat)) $xmlstr .= "\t<websiteCategory>{$this->webCat}</websiteCategory>\n";

        $xmlstr .= "</mgdToneUpdate>";

        $this->addLog("XML Generado:\n".$xml);
        return $xmlstr;
    }



    public function downloadContent($ftpCon) {
//        $tmpDir = ($this->isPolytone) ? TMP_DIR_PT : TMP_DIR_RT;
		global $tmpDir;
        $this->dirToWrite = $tmpDir."/" . $this->contentId;
        if (!is_writable($tmpDir)) {
            die("ERROR: ".$tmpDir." is not writable\n");
        } else {
            $log .= "Creando el directorio para los realtones...\n";
            exec("mkdir {$this->dirToWrite}");
        }
        if (!is_writable($this->dirToWrite)) {
            die("ERROR: {$this->dirToWrite} is not writable\n");
        }


        $to = $this->dirToWrite."/".$this->getContentFilename();
        $from = $this->getContent();
        echo "<br/>Bajando desde $from hacia $to <br />";

        $bajado = $ftpCon->bajar_r($from, $to, FTP_DOWN_RETRIES);
        if($bajado) {
            $this->duration = $this->getDurationStr($to);
        } else {
            exec("rm -r {$this->dirToWrite}");
           echo '<br/><span style="color:red;font-size:14px;">Error bajando '.$from.'</span>';

        }

        return $bajado;
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

    public function setKeywords($keywords) {
        $this->keywords = $keywords;
    }

   public function getLongDescription() {
        return str_replace("-", " ",  konvert("$this->longDescription"));
    }

    public function setLongDescription($longDescription) {
        $this->longDescription = $longDescription;
    }

    public function getShortDescription() {
        return str_replace("-", " ",  konvert("$this->shortDescription"));
    }

    public function setShortDescription($shortDescription) {
        $this->shortDescription = konvert($shortDescription);
    }

    public function getCatLvl() {
        return $this->catLvl;
    }

    public function setCatLvl($catLvl) {
        $this->catLvl = $catLvl;
    }

    public function getWebCat() {
        return $this->webCat;
    }

    public function setWebCat($webCat) {
        $this->webCat = konvert($webCat);
    }

    public function getDirToWrite() {
        return $this->dirToWrite;
    }

    public function setDirToWrite($dirToWrite) {
        $this->dirToWrite = $dirToWrite;
    }


    ////////////////////////////////////////////////////////////
    //
    // TODAS LA FUNCIONALIDAD PRIVADA DE LA CLASE
    //
    ////////////////////////////////////////////////////////////

    private function getDurationStr($pathToFile) {
       if(!$this->isPolytone){
            $pathToFile = escapeshellcmd($pathToFile);
            $retorno = exec("ffmpeg -i $pathToFile 2>&1 | grep \"Duration\" | cut -d ' ' -f 4 | sed s/,//", $lines, $returnCode);
            if(!$returnCode) {
                return substr($retorno, 0, -3);
            } else {
                $this->addLog("No se pudo obtener la duracion del realtone\n");
                $this->genException("CRIT", __LINE__, __METHOD__, ": No se pudo obtener la duracion del realtone" . $this->uniqueId);
            }
        } else {
            $midi = new MidiDuration();
            $midi->importMid($pathToFile);
            return $midi->getDuration();
        }
        
    }

    private function addLog($msg) {
        $this->logmsg .= $msg."\n";
    }

    private function logDebug() {
		$fp = fopen($this->logFile, "a+");
		fwrite($fp, $this->logmsg);
		fclose($fp);
    }

    // genera el external Id y guarda el id nuevo en la base
    private function setFilenames() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $ext = (!$this->isPolytone) ? ".mp3" : ".mid";
        $nombreArchivo =  ereg_replace("[^A-Za-z0-9]", "", $this->title); 
        $this->contentFilename = sanitizeString($nombreArchivo) . $ext;
    }

    
    
    /**
     * SETEA EL ABSTRACT (SHORTDESCRIPTION) DE ACUERDO A VARIAS CONDICIONES
     */
    private function setAbstract() {
        $abstract = "";

        if ($this->isPolytone === TRUE) {
            $abstract = $this->artist;
        } else {
            // si es sonido especial
            if ($this->webCat == "Sonidos Especiales") {
                if (empty($this->marca)) {
                    // si marca vacio, setear ideas
                    $abstract = "Ideas";
                } else {
                    // sino setear marca
                    $abstract = $this->marca;
                }
            } else {
                // sino setear artista
                $abstract = $this->artist;
            }
        }

        // si festivo, agregar festivo
        if ($this->festivo) {
            $abstract .= " (".$this->festivo.")";
        }
        
        $this->abstract = $abstract;
    }
    
    /**
     * SETEA EL CREATOR 
     */
    private function setCreator() {
        $creator = "";

        if ($this->isPolytone === TRUE) {
            $creator = $this->artist;
        } else {
            // si es sonido especial
            if ($this->webCat == "Sonidos Especiales") {
                if (empty($this->marca)) {
                    // si marca vacio, setear ideas
                    $creator = "Ideas";
                } else {
                    // sino setear marca
                    $creator = $this->marca;
                }
            } else {
                // sino setear artista
                $creator = $this->artist;
            }
        }

        // si festivo, agregar festivo
        if ($this->festivo) {
            $creator .= " (".$this->festivo.")";
        }
        
        $this->creator = $creator;
    }

    private function setTitle($nombre) {
        $title = $nombre;
        
        if ($this->isPolytone === TRUE) {
            $title .= " (Poli)";
        } else {
            $title .= " (Real)";
        }
        
        $this->title = $title;
    }
    
    private function getAbstract() {
        return str_replace("-", " ",  konvert("$this->abstract"));
    }
    
    private function getCreator() {
        return str_replace("-", " ",  konvert("$this->creator"));
    }
    
    private function getTitle() {
        return str_replace("-", " ",  konvert("$this->title"));
    }

    
    
    
}
?>