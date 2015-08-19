<?php

class migRealtone {
// estos cambian
    private $album;
    private $title;
    private $tag;
    private $artist;
    private $publicationYear;
    private $provider;
    private $shortDesc;
    private $longDesc;
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
    private $logFile = "/home/kamus/Web/mig/logs/realtone.class.log";

    // constructor
    function __construct($dbc, $debug=FALSE, $catLvl = "", $webCat = "", $isPolytone = false, $rating = "") {
        $this->addLog("************************************ C O M E N Z A N D O *************************************************");

        if ($dbc === NULL) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: DB Null");
        else $this->dbc = $dbc;

        $this->debug_mode = $debug;
        $this->album = "";
        $this->publicationYear = date("Y");
        $this->catLvl = $catLvl;
        $this->webCat = konvert($webCat);
        $this->isPolytone = $isPolytone;
        $this->rating = $rating;
    }

    function __destruct() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $this->addLog("************************************** F I N ************************************************************");
        if ($this->debug_mode === TRUE) {
            $this->logDebug();
        } else {
        }
    }

    // carga los datos del contenido
    public function loadContent($content_id) {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        if (!is_numeric($content_id) || $content_id<=0) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: content_id no valido");
        else $this->contentId = $content_id;

        $wazzupType = ($this->isPolytone) ? WAZZUP_POLYTONE : WAZZUP_REALTONE;

        $sql = "SELECT c.*, cc.descripcion nombreCat, cc.xxx, cp.nombre provider
                FROM Web.contenidos c 
                INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria)
                INNER JOIN Web.contenidos_proveedores cp ON (c.proveedor = cp.id)
                WHERE c.id=$this->contentId  AND c.tipo='".$wazzupType."'" ;
        $this->addLog("SQL: ".$sql);
        $rs = mysql_query($sql, $this->dbc->db);
        if (!$rs) throw new Exception("ERROR: no se pueden obtener datos del contenido $this->contentId ");
        else $obj = mysql_fetch_object($rs);

        $this->contentType = $obj->tipo;
        $this->provider = $obj->provider;
        $this->adult = ($obj->xxx == "0") ? "false" : "true";
        //$this->keywords = explode(",", $obj->keywords);
        $this->keywords = array_map("trim", explode(",", konvert($obj->keywords)));

        $this->keywords = array_map("sanitizeString", $this->keywords);

        $this->artist = konvert($obj->autor);
        //$this->title = konvert("$obj->nombre");
        $this->title = str_replace("-", " ",  konvert("$obj->nombre"));
        $this->archivo = str_replace("netuy", "contenido", $obj->archivo);

        $this->shortDesc = $this->artist;
        $this->longDesc = "Tema " . $this->title . " de " . $this->artist;

        $this->setFilenames();
    }

    private function genException($type, $line, $method, $msg) {
        $this->addLog("$type <$line> $method $msg");
        if ($type == "CRIT") throw new Exception("$type <$line> $method $msg");
    }

    public function getArraySubForm(){
        return $this->arraySubForm;
    }

    public function genXML() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $this->addLog("Generando XML para $this->contentId");
        //$keywordStr = "\"" . implode("\r\n", $this->keywords) . "\"";
        $keywordStr = implode(" ", $this->keywords);

        $this->arraySubForm = array("CP name" => CP_NAME,
                            "Date of Ingest" => date("d.m.Y"),
                            "Zip File Name" => "",
                            "Product Type" => "mgdTone",
                            "ProviderContentGivenId" => $this->contentId,
                            "Content Title" => $this->title,
                            "Keywords" => $keywordStr,
                            "Genres" => $this->tag,
                            "Website Category" => $this->webCat,
                            "Short Description" => $this->shortDesc,
                            "Length of short decription" => "",
                            "Creator" => $this->artist,
                            "Thumbnail" => "",
                            "Mexico" => "",
                            "Colombia" => "X",
                            "Ecuador" => "X",
                            "Panama" => "X",
                            "Peru" => "X"
                           );

        if (!is_numeric($this->contentId) || $this->contentId<=0) $this->genException("CRIT", __LINE__, __METHOD__, ": content_id no valido");

        $carriers = explode(",", CARRIERS);
        $langs = explode(",", LANGS);

        $xmlstr = XML_HEADER.'<mgdToneProduct xmlns="http://www.qpass.net/telcel/mgdTone"
	xmlns:qpass="http://www.qpass.com/content" xmlns:jcr="http://www.jcp.org/jcr/1.0">'."\n";

        foreach($carriers as $carrier) {
            $xmlstr .= "\t<qpass:merchant>$carrier</qpass:merchant>\r\n";
        }

       // $rating = ($this->adult) ? "18+" : $this->rating;
        $rating = $this->rating;
        $xmlstr .=<<<XML
                <qpass:rating>
                <qpass:scheme>Mexico</qpass:scheme>
                <qpass:value>$rating</qpass:value>
                </qpass:rating>
XML;
        $xmlstr .= "\t<providerGivenContentId>{$this->contentId}</providerGivenContentId>";

        foreach($langs as $lang) {
            $xmlstr .= "\t<title qpass:lang=\"$lang\">{$this->title}</title>\r\n";
        }

        $xmlstr .= "\t<genres>{$this->tag}</genres>\r\n";
        $xmlstr .= "\t<subgenres1>{$this->subTag}</subgenres1>\r\n";
        $xmlstr .= "\t<artist>{$this->artist}</artist>\r\n";
        $xmlstr .= "\t<label>{$this->provider}</label>\r\n";

        foreach($langs as $lang) {
            foreach($this->keywords as $keyword) {
                $xmlstr .= "\t<keywords qpass:lang=\"$lang\">{$keyword}</keywords>\r\n";
            }
        }
            
        foreach($langs as $lang) {
            $xmlstr .= "\t<shortDescription qpass:lang=\"$lang\">{$this->shortDesc}</shortDescription>\r\n";
        }

        foreach($langs as $lang) {
            $xmlstr .= "\t<longDescription qpass:lang=\"$lang\">{$this->longDesc}</longDescription>\r\n";
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

    public function downloadContent($ftpCon) {
        $tmpDir = ($this->isPolytone) ? TMP_DIR_PT : TMP_DIR_RT;
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

    public function setTag($cat) {
        $this->tag = konvert($cat);
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

    public function setShortDesc($shortDescription) {
        
        $this->shortDesc = konvert($shortDescription);
    }

    public function setLongDesc($longDescription) {
        $this->longDesc = konvert($longDescription);
    }

    public function setSubTag($cat) {
        $this->subTag = konvert($cat);
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
        $this->contentFilename = sanitizeString($this->title) . $ext;
    }
}
?>