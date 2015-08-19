<?php

class migVideo {
// estos cambian
    private $title;
    private $tag;
    private $subTag;
    private $archivo;
    private $preview;
    private $keywords;
    private $provider;
    private $previewExists;
    private $thumbnailExists;
    private $catLvl;
    private $webCat;
    private $longDescription;
    private $shortDescription;
    private $dirToWrite;
    private $contentFilename;
    private $thumbnailFilename;
    private $prefixSearch;
    private $idRange;
    private $contentName;
    private $duration;
    private $rating;
    private $arraySubForm;

    // estos estan predefinidos y nunca cambian
    private $adult;

    // internos de la clase
    private $contentId;
    private $contentType;
    private $dbc;
    private $logFile = "/home/kamus/Web/mig/logs/logsvideo.class.log";
    private $formatos = array("threegppAacQcifResource" => "176x144aac3gp",
                              "threegppAacQvgaResource" =>"AM_3gp_320x240_aac",
                              "threegppAmrQvgaResource" => "AM_3gp_320x240_amr",
                              "threegppAmrSqcifResource" => "128x96",
                              "threegppAmrQcifResource" => "AM_3gp_176x144_amr",
                              "realVideoQcifResource" => "176x144rm",
                              "threegpMp4QcifResource" => "AM_3gp_176x144_aac");

    // constructor
    function __construct($dbc, $debug=FALSE,$catLvl = "", $webCat = "") {
        $this->addLog("************************************ C O M E N Z A N D O *********************************************");
        $this->catLvl = $catLvl;
        $this->webCat = konvert($webCat);
        $this->rating = $rating;
        if ($dbc === NULL) {
            throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: DB Null");
        } else {
            $this->dbc = $dbc;
        }
        $this->debug_mode = $debug;
    }

    // destructor
    function __destruct() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $this->addLog("************************************** F I N **********************************************************");
        if ($this->debug_mode === TRUE) {
            $this->logDebug();
        }
    }

    // carga los datos del contenido
    public function loadContent($content_id) {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        if (!is_numeric($content_id) || $content_id<=0) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: content_id no valido");
        else $this->contentId = $content_id;

        $sql = "SELECT c.*, cc.descripcion nombreCat, cc.xxx,cp.nombre provider
                FROM Web.contenidos c
                INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria)
                INNER JOIN Web.contenidos_proveedores cp ON (c.proveedor = cp.id)
                WHERE c.id=$this->contentId  AND c.tipo='".WAZZUP_VIDEO."'" ;
        $this->addLog("SQL: ".$sql);
        $rs = mysql_query($sql, $this->dbc->db);
        if (!$rs) throw new Exception("ERROR: no se pueden obtener datos del contenido $this->contentId ");
        else $obj = mysql_fetch_object($rs);

        $this->adult = ($obj->xxx == "0") ? "false" : "true";
        $this->contentType = $obj->tipo;
        //$this->title = konvert("$obj->nombre");
        $this->title = str_replace("-", " ",  konvert("$obj->nombre"));
        $this->keywords = array_map("trim", explode(",", konvert($obj->keywords)));
        $this->keywords = array_map("sanitizeString", $this->keywords);

        $this->archivo = str_replace("netuy", "contenido", $obj->archivo);
        $helper = explode("/", $this->archivo);

        $this->prefixSearch = $helper[1] . "/" . $helper[2];
        $this->idRange = $helper[3];
        $this->contentName = konvert(substr($helper[4], 0, -4));
        $this->provider = $obj->provider;
        $this->preview = $obj->referencia;

        $this->shortDescription = $this->title;

        $this->setFilenames();
    }

    public function getArraySubForm(){
        return $this->arraySubForm;
    }


    public function genXML() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $this->addLog("Generando XML para $this->contentId");
        //$keywordStr = "\"" . implode("\r\n", $this->keywords) . "\"";
        $keywordStr = implode("<br />", $this->keywords);

        $this->arraySubForm = array("CP name" => CP_NAME,
                            "Date of Ingest" => date("d.m.Y"),
                            "Zip File Name" => "",
                            "Product Type" => "mgdVideo",
                            "ProviderContentGivenId" => $this->contentId,
                            "Content Title" => $this->title,
                            "Keywords" => $keywordStr,
                            "Genres" => $this->tag,
                            "Website Category" => $this->webCat,
                            "Short Description" => $this->shortDescription,
                            "Length of short decription" => "",
                            "Creator" => $this->provider,
                            "Thumbnail" => $this->getThumbnailFilename(),
                            "Mexico" => "",
                            "Colombia" => "X",
                            "Ecuador" => "X",
                            "Panama" => "X",
                            "Peru" => "X"
                           );

        if (!is_numeric($this->contentId) || $this->contentId<=0) $this->genException("CRIT", __LINE__, __METHOD__, ": content_id no valido");

        $carriers = explode(",", CARRIERS);
        $langs = explode(",", LANGS);

        $xmlstr = XML_HEADER."\n".'<mgdVideoProduct xmlns="http://www.qpass.net/telcel/mgdVideo"
                            xmlns:qpass="http://www.qpass.com/content" xmlns:jcr="http://www.jcp.org/jcr/1.0">'."\n";

        foreach($carriers as $carrier) {
            $xmlstr .= "\t<qpass:merchant>$carrier</qpass:merchant>\r\n";
        }

        $rating = ($this->adult) ? "18+" : $this->rating;
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
        //$xmlstr .= "\t<publisher>{$this->provider}</publisher>\r\n";
        $xmlstr .= "\t<creators>{$this->provider}</creators>\r\n";
        $xmlstr .= "\t<studio>{$this->provider}</studio>\r\n";

        foreach($langs as $lang) {
            foreach($this->keywords as $keyword) {
                $xmlstr .= "\t<keywords qpass:lang=\"$lang\">{$keyword}</keywords>\r\n";
            }
        }

        foreach($langs as $lang) {
            $xmlstr .= "\t<shortDescription qpass:lang=\"$lang\">{$this->shortDescription}</shortDescription>\r\n";
        }

        foreach($langs as $lang) {
            $xmlstr .= "\t<longDescription qpass:lang=\"$lang\">{$this->longDescription}</longDescription>\r\n";
        }

        $xmlstr .= "\t<categoryLevel>{$this->catLvl}</categoryLevel>\r\n";

        $curdate = date("d-M-Y");
        $xmlstr .= "\t<creationDate>$curdate</creationDate>\r\n";
        $xmlstr .= "\t<releaseDate>$curdate</releaseDate>\r\n";
        $xmlstr .= "\t<duration>{$this->duration}</duration>\n";
        $xmlstr .= "\t<websiteCategory>{$this->webCat}</websiteCategory>\r\n";

        $name = $this->getThumbnailFilename();
        $xmlstr .=<<<XML
	  <thumbnail>
                <thumbnailResource>
                        <qpass:resourceFilename>$name</qpass:resourceFilename>
                        <qpass:mimeType>image/gif</qpass:mimeType>
                </thumbnailResource>
	  </thumbnail>
XML;

        $xmlstr .= "\t<premium>\r\n";
        foreach($this->formatos as $formato => $nombre) {
            $to = $this->contentName ."_" . $formato . $this->getExt($formato);
            if($this->getExt($formato) == ".3gp"){
                $type = "video/3gpp";
            } else {
                $type = "video/vnd.rn-realvideo";
            }
            $xmlstr .=<<<XML
            <$formato>
                    <qpass:resourceFilename>$to</qpass:resourceFilename>
                    <qpass:mimeType>$type</qpass:mimeType>
            </$formato>\r\n
XML;
        }

        $xmlstr .= "\t</premium>";
        $xmlstr .= "</mgdVideoProduct>";

        $this->addLog("XML Generado:\n".$xml);
        return $xmlstr;
    }

    public function downloadContent($ftpCon) {
        $this->dirToWrite = TMP_DIR_VD."/" . $this->contentId;
        if (!is_writable(TMP_DIR_VD)) {
            die("ERROR: ".TMP_DIR_VD." is not writable\n");
        } else {
            $log .= "Creando el directorio para los videos...\n";
            exec("mkdir {$this->dirToWrite}");
        }

        if (!is_writable($this->dirToWrite)) {
            die("ERROR: {$this->dirToWrite} is not writable\n");
        }
        $alguno = false;
        foreach($this->formatos as $formato => $nombre) {
            $to = $this->dirToWrite."/".$this->contentName ."_" . $formato . $this->getExt($formato);
            $from = $this->prefixSearch;
            if($nombre != ""){
                $from .= "/" . trim($nombre);
            }
            if($this->usesIdRange($formato)){
                $from .= "/" . trim($this->idRange);
            }
            $from .= "/" . $this->contentName . $this->getExt($formato);
            echo "FROM: $from <br />";

            $bajado = $ftpCon->bajar_r($from, $to, FTP_DOWN_RETRIES);
            if($bajado){
                $alguno = true;
                $this->duration = $this->getDurationStr($to);
            } else {
                echo '<br/><span style="color:red;font-size:14px;">Error bajando formato '.$formato.' from '.$from.'.</span><br />';
            }

            $result[$formato] = $bajado;
        }

        if(!$alguno){
            $log .= "Borrando el directorio para los videos. NO HAY NINGUN FORMATO.\n";
            echo '<br/><span style="color:red;font-size:14px;">Error bajando VIDEOS.</span>';
            exec("rm -r {$this->dirToWrite}");
        }

        // Generamos el thumb
        if(!($this->thumbnailExists = $this->generateThumbnail())) {
            echo "No se pudo bajar el tumbnail para \"" . $this->title . "\"<br />";
        }
        return $result;
    }

    public function getDirToWrite() {
        return $this->dirToWrite;
    }

    public function setDirToWrite($dirToWrite) {
        $this->dirToWrite = $dirToWrite;
    }

    public function setSubTag($cat) {
        $this->subTag = konvert($cat);
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

    public function getKeywords() {
        return $this->keywords;
    }

    public function setKeywords($keywords) {
        $this->keywords = $keywords;
    }

    public function getProvider() {
        return $this->provider;
    }

    public function setProvider($provider) {
        $this->provider = $provider;
    }

    public function getWebCat() {
        return $this->webCat;
    }

    public function setWebCat($webCat) {
        $this->webCat = konvert($webCat);
    }

    public function getLongDescription() {
        return $this->longDescription;
    }

    public function setLongDescription($longDescription) {
        $this->longDescription = konvert($longDescription);
    }

    public function getShortDescription() {
        return $this->shortDescription;
    }

    public function setShortDescription($shortDescription) {
        $this->shortDescription = konvert($shortDescription);
    }

    public function getThumbnailFilename() {
        return $this->thumbnailFilename;
    }

    public function getArchivo() {
        return $this->archivo;
    }



    ////////////////////////////////////////////////////////////
    //
    // TODAS LA FUNCIONALIDAD PRIVADA DE LA CLASE
    //
    ////////////////////////////////////////////////////////////

    private function generateThumbnail() {
        $ftpCon = new Ftp(FTP_USA, FTP_USA_USR, FTP_USA_PWD);
        $connected = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
        if(!$connected) {
            return false;
        }
        $from = str_replace("contenido", "netuy", $this->prefixSearch);
        $from .= "/100x100/" . $this->idRange. "/" . $this->contentName . ".gif";

        $tmp = $this->dirToWrite."/thumbnail" . sanitizeString($this->title) . ".gif";
        $bajado = $ftpCon->bajar_r($from, $tmp, FTP_DOWN_RETRIES);
        return $bajado;
    }

    private function genException($type, $line, $method, $msg) {
        $this->addLog("$type <$line> $method $msg");
        if ($type == "CRIT") throw new Exception("$type <$line> $method $msg");
    }

    private function getDurationStr($pathToFile) {
        $pathToFile = escapeshellcmd($pathToFile);
        $retorno = exec("ffmpeg -i $pathToFile 2>&1 | grep \"Duration\" | cut -d ' ' -f 4 | sed s/,//", $lines, $returnCode);
        if(!$returnCode) {
            return substr($retorno, 0, -3);
        } else {
            $this->addLog("No se pudo obtener la duracion del video\n");
            $this->genException("CRIT", __LINE__, __METHOD__, ": No se pudo obtener la duracion del video" . $this->uniqueId);
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

    private function setFilenames() {
        $ext = substr($this->archivo, -3);
        $this->contentFilename = sanitizeString($this->title)  . ".$ext";
        //$this->previewFilename = "preview" . str_replace(" ", "", sanitizeString($this->title) . ".gif";
        $this->thumbnailFilename = "thumbnail" . sanitizeString($this->title) . ".gif";
    }




    private function usesIdRange($format){
        switch($format){
            case "threegppAacQvgaResource":
                return true;
                break;
            case "ThreegppAacQcifResource":
                return true;
                break;
            case "threegppAmrQvgaResource":
                return true;
                break;
            case "threegppAmrSqcifResource":
                return true;
                break;
            case "threegppAmrQcifResource":
                return true;
                break;
            case "realVideoQcifResource":
                return false;
                break;
            case "threegpMp4QcifResource":
                return true;
                break;
        }
    }

    private function getExt($format){
        switch($format){
            case "threegppAacQvgaResource":
            case "threegppAacQcifResource":
            case "ThreegppAacQcifResource":
            case "threegppAmrQvgaResource":
            case "threegppAmrSqcifResource":
            case "threegppAmrQcifResource":
            case "threegpMp4QcifResource":
            default:
                return ".3gp";
                break;
            case "realVideoQcifResource":
                return ".rm";
                break;
        }
    }
}
?>