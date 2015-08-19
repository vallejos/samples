<?php

class druttFulltrack {
    private $key;
    private $album;
    private $title;
    private $tag;
    private $artist;
    private $publicationYear;
    private $description;
    private $premiumResource = ""; // obtener de tpim
    private $provider;

    private $archivo;
    private $preview;
    private $previewFilename;
    private $contentFilename;

    // estos estan predefinidos y nunca cambian
    private $deployed;
    private $promotiononly;
    private $promotionkey;
    private $startDate;
    private $validUntil;
    private $adult;

    // internos de la clase
    private $contentId;
    private $contentType;
    private $dbc;
    private $logFile = "/home/kamus/Web/uy/ancel/drutt-ancel/logs/fulltrack.class.log";

    // mandatory fields
    //      private $mandatoryXMLFields = array("externalId", "deployed", "promotiononly", "startDate", "validUntil", "service", "adult", "name", "title", "tag", "premiumResource");
    private $mandatoryXMLFields = array("externalId", "deployed", "promotiononly", "startDate", "validUntil", "adult", "artist", "title", "tag");

    // constructor
    function __construct($dbc, $debug=FALSE) {
        $this->addLog("************************************ C O M E N Z A N D O *****************************************************************");
        if ($dbc === NULL) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: DB Null");
        else $this->dbc = $dbc;

        $this->debug_mode = $debug;
        $this->deployed = "false";
        $this->promotiononly = "false";
        $this->promotionkey = "";
        $this->startDate = "2008-12-01T00:00:00.000";
        $this->validUntil = "2099-12-31T00:00:00.000";
        $this->album = "";
        $this->publicationYear = date("Y");
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



    // carga los datos del contenido
    public function loadContent($content_id) {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        if (!is_numeric($content_id) || $content_id<=0) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: content_id no valido");
        else $this->contentId = $content_id;

        $sql = "SELECT c.*, cp.nombre as provider, cc.descripcion nombreCat, ft.al_titulo album, ft.al_artista artista, ft.al_id, ft.tr_archivo, ft.tr_archivo_preview, cc.xxx
                FROM Web.contenidos c INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria)
                INNER JOIN Web.fulltracks ft ON (ft.contenido=c.id)
                INNER JOIN Web.contenidos_proveedores cp ON (c.proveedor = cp.id)
                WHERE c.id=$this->contentId
                AND c.tipo='".WAZZUP_FULLTRACK."'" ;
        $this->addLog("SQL: ".$sql);
        $rs = mysql_query($sql, $this->dbc->db);
        if (!$rs) throw new Exception("ERROR: no se pueden obtener datos del contenido $this->contentId ");
        else $obj = mysql_fetch_object($rs);

        $this->contentType = $obj->tipo;
        $this->adult = ($obj->xxx == "0") ? "false" : "true";
        $this->artist = konvert($obj->artista);
        $this->provider = konvert($obj->provider);
        $this->album = konvert($obj->album);
        $this->title = konvert("$obj->nombre");
        $this->description = konvert(getContentDescription($this->contentId, $this->contentType, $obj->categoria));
        $this->publicationYear = date("Y");

        // v1.3
        $this->archivo = trim("/contenido/fulltracks/".$obj->archivo);
        $this->preview = trim("/contenido/fulltracks/".$obj->referencia);
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


    private function genException($type, $line, $method, $msg) {
        $this->addLog("$type <$line> $method $msg");
        if ($type == "CRIT") throw new Exception("$type <$line> $method $msg");
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

    public function genXML() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $this->addLog("Generando XML para $this->contentId");

        if (!is_numeric($this->contentId) || $this->contentId<=0) $this->genException("CRIT", __LINE__, __METHOD__, ": content_id no valido");

        $carriers = explode(",", CARRIERS);
        $langs = explode(",", LANGS);

        $xmlstr = XML_HEADER."\n".'<mgdToneProduct xmlns="http://www.qpass.net/telcel/mgdImage"
                            xmlns:qpass="http://www.qpass.com/content" xmlns:jcr="http://www.jcp.org/jcr/1.0">'."\n";

        foreach($carriers as $carrier) {
            $xmlstr .= "\t<qpass:merchant>$carrier</qpass:merchant>\r\n";
        }

        $rating = ($this->adult) ? "18+" : "3+";
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
        $xmlstr .= "\t<label>{$this->provider}</label>\r\n"; // NO tenemos esta info, pongo el privder "pa poner algo"

        foreach($langs as $lang) {
            $xmlstr .= "\t<keywords qpass:lang=\"$lang\">{$this->tag}</keywords>\r\n";
            $xmlstr .= "\t<keywords qpass:lang=\"$lang\">{$this->subTag}</keywords>\r\n";
        }

        foreach($langs as $lang) {
            $xmlstr .= "\t<shortDescription qpass:lang=\"$lang\">{$this->description}</shortDescription>\r\n";
            $xmlstr .= "\t<longDescription qpass:lang=\"$lang\">{$this->description}</longDescription>\r\n";
        }

        $xmlstr .= "\t<albumName>{$this->album}</albumName>\r\n";
        $xmlstr .= "\t<trackName>{$this->title}</trackName>\r\n";
        $xmlstr .= "\t<categoryLevel>{$this->catLvl}</categoryLevel>\r\n";

        $curdate = date("d-M-Y");
        $xmlstr .= "\t<creationDate>$curdate</creationDate>\r\n";
        $xmlstr .= "\t<releaseDate>$curdate</releaseDate>\r\n";
        $duration = $this->getDurationStr($filename);
        $xmlstr .= "\t<duration>$duration</duration>\n";
        $xmlstr .= "<websiteCategory>{$this->webCat}</websiteCategory>\r\n";


        // TODO PREVIEW

        // TODO THUMBNAIL

        $filename = $this->getContentFilename();
        $xmlstr .= "\t<premium>";
        // TODO
        $xmlstr .= "\t</premium>";
        $xmlstr .= "</mgdToneProduct>";

        $this->addLog("XML Generado:\n".$xml);
        return $xmlstr;
    }


    ////////////////////////////////////////////////////////////
    //
    // TODAS LA FUNCIONALIDAD PRIVADA DE LA CLASE
    //
    ////////////////////////////////////////////////////////////
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

    private function getDurationStr($pathToFile) {
        $retorno = exec("ffmpeg -i $pathToFile 2>&1 | grep \"Duration\" | cut -d ' ' -f 4 | sed s/,//", $lines, $returnCode);
        if(!$returnCode) {
            return substr($retorno, 0, -3);
        } else {
            $this->addLog("No se pudo obtener la duracion del audio\n");
            $this->genException("CRIT", __LINE__, __METHOD__, ": No se pudo obtener la duracion del audio" . $this->contentId);
        }
    }

   private function getBitrateStr($pathToFile) {
        $retorno = exec("ffmpeg -i $pathToFile 2>&1 | grep \"Duration\" | cut -d ' ' -f 8", $lines, $returnCode);
        if(!$returnCode) {
            return $retorno;
        } else {
            $this->addLog("No se pudo obtener la duracion del audio\n");
            $this->genException("CRIT", __LINE__, __METHOD__, ": No se pudo obtener la duracion del audio" . $this->contentId);
        }
    }

}
?>