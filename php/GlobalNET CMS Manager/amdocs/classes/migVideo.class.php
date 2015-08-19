<?php

class migVideo {
// estos cambian
    private $title;
    private $tag;
    private $subTag;
    private $tagEng;
    private $subTagEng;
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
    private $logFile = "/mnt/storage/www/tools/cms/amdocs/logs/logsvideo.class.log";
    private $formatos = array();
    private $formatosClip = array("threegppAacQcifResource" => "176x144aac3gp",
                              "threegppAmrSqcifResource" => "128x96");
    private $formatosFull = array("threegppAacQvgaResource" =>"AM_3gp_320x240_aac",
                              "threegppAmrQvgaResource" => "AM_3gp_320x240_amr",
                              "threegppAmrQcifResource" => "AM_3gp_176x144_amr",
                              "realVideoQcifResource" => "176x144rm",
                              "threegpMp4QcifResource" => "AM_3gp_176x144_aac");
    private $marca;
    private $festivo;
    private $esClip;
    private $langs;
    private $merchants;
    private $abstract;
    private $creator;


    // constructor
//    function __construct($dbc, $debug=FALSE, $catLvl = "", $webCat = "", $marca="", $festivo="") {
    function __construct($dbc, $debug=FALSE, $catLvl = "", $webCat = "", $rating="", $marca="", $festivo="", $tipoVideo = NULL) {
        $this->addLog("************************************ C O M E N Z A N D O *********************************************");
        $this->catLvl = $catLvl;
        $this->webCat = $webCat;
        $this->rating = $rating;
        if ($dbc === NULL) {
            throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: DB Null");
        } else {
            $this->dbc = $dbc;
        }
        $this->debug_mode = $debug;
//        $this->marca = (empty($marca)) ? " Ideas" : " $marca";
//        $this->festivo = (empty($festivo)) ? "" : " ($festivo)";
        $this->marca = $marca;
        $this->festivo = $festivo;
        $this->esClip = ($tipoVideo == "clip") ? TRUE : FALSE;
        if ($this->esClip == TRUE) $this->formatos = $this->formatosClip;
        else $this->formatos = $this->formatosFull;
        $this->tagEng = "" ;
        $this->subTagEng = "";
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
        $this->setTitle($obj->nombre);
       

//        $this->title = str_replace("-", " ",  konvert("$obj->nombre"));

        $this->keywords = array_map("trim", explode(",", konvert($obj->keywords)));
//        $this->keywords = array_map("sanitizeString", $this->keywords);

        $this->archivo = str_replace("netuy", "contenido", $obj->archivo);
        $helper = explode("/", $this->archivo);

        $this->prefixSearch = $helper[1] . "/" . $helper[2];
        $this->idRange = $helper[3];
        $this->contentName = konvert(substr($helper[4], 0, -4));
        $this->provider = $obj->provider;
        $this->preview = $obj->referencia;

        $this->setShortDescription($this->title.$this->marca.$this->festivo);

        $this->setLongDescription($this->title.$this->marca.$this->festivo);

        $this->setCreator();
        
        $this->setAbstract();
        
        $this->setFilenames();
    }

    
    /**
     * SETEA EL ABSTRACT (SHORTDESCRIPTION) DE ACUERDO A VARIAS CONDICIONES
     */
    private function setAbstract() {
        $abstract = "";

        // si es sonido especial
        if ($this->webCat == "Videos Musicales") {
            // sino setear artista
            $abstract = $this->artist;
        } else {
            if (empty($this->marca)) {
                // si marca vacio, setear ideas
                $abstract = "Ideas";
            } else {
                // sino setear marca
                $abstract = $this->marca;
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

        // si es video musical
        if ($this->webCat == "Videos Musicales") {
            // sino setear artista
            $creator = $this->artist;
        } else {
            if (empty($this->marca)) {
                // si marca vacio, setear ideas
                $creator = "Ideas";
            } else {
                // sino setear marca
                $creator = $this->marca;
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
        
        $this->title = $title;
        
        if((trim($this->webCat) == "Lo m&#255;s Sexy") || (konvert($this->webCat) == konvert("Lo más Sexy")) || (trim($this->webCat) == "Videos Musicales")) {
            if ($this->esClip == TRUE) $this->title .= " (Clip)";
            else $this->title .= " (Completo)";

            if($this->webCat == "Videos Musicales") {
                 $this->artist = konvert($obj->autor);
            }
        }
        
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

    public function genXML() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $this->addLog("Generando XML para $this->contentId");
        //$keywordStr = "\"" . implode("\r\n", $this->keywords) . "\"";
        $keywordStr = implode("<br />", $this->keywords);

/*        $this->arraySubForm = array("CP name" => CP_NAME,
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
                           );*/

        $this->arraySubForm = array(
                            "Content Title" => $this->getTitle(),
                            "Website Category" => $this->webCat,
                            "Genres" => $this->tag,
                            "Creator" =>  $this->getCreator(),
                           );

        if(strcasecmp($this->webCat, "Lo más Sexy") == 0) {
            $this->arraySubForm["Thumbnail"] = $this->thumbnailPath;
        } else {
            $this->arraySubForm["Thumbnail"] = "";
        }

        if (!is_numeric($this->contentId) || $this->contentId<=0) $this->genException("CRIT", __LINE__, __METHOD__, ": content_id no valido");

        $carriers = explode(",", CARRIERS);
        $langs = explode(",", LANGS);

        $xmlstr = XML_HEADER."\n".'<mgdVideoProduct xmlns="http://www.qpass.net/telcel/mgdVideo"
                            xmlns:qpass="http://www.qpass.com/content" xmlns:jcr="http://www.jcp.org/jcr/1.0">'."\n";


        // TAG:MERCHANT
        foreach($this->merchants as $merchant) {
            $carrier = $this->obtainCarrier($merchant);
            $xmlstr .= "\t<qpass:merchant>$carrier</qpass:merchant>\r\n";
        }


		$rating = ($this->adult == "true") ? "18+" : $this->rating;
        $xmlstr .=<<<XML
                <qpass:rating>
                <qpass:scheme>Mexico</qpass:scheme>
                <qpass:value>$rating</qpass:value>
                </qpass:rating>
XML;
        $xmlstr .= "\t<providerGivenContentId>{$this->contentId}</providerGivenContentId>";

        // TAG:TITLE
        foreach($this->langs as $lang) {
            if ($lang == "JM") $xmlstr .= "\t<title qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getTitle()}</title>\r\n"; // TODO: obtener version traducida
            else $xmlstr .= "\t<title qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getTitle()}</title>\r\n";
        }

        $xmlstr .= "\t<genres>{$this->tag}</genres>\r\n";
        $xmlstr .= "\t<subgenres1>{$this->subTag}</subgenres1>\r\n";
        //$xmlstr .= "\t<publisher>{$this->provider}</publisher>\r\n";
        $xmlstr .= "\t<creators>{$this->getCreator()}</creators>\r\n";
        $xmlstr .= "\t<studio>{$this->provider}</studio>\r\n";

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
		global $tmpDir;
        $this->dirToWrite = $tmpDir."/" . $this->contentId;
        if (!is_writable($tmpDir)) {
            die("ERROR: ".$tmpDir." is not writable\n");
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
//            echo "FROM: $from <br />";
// echo "====$formato====";
            $bajado = $ftpCon->bajar_r($from, $to, FTP_DOWN_RETRIES);
            if($bajado){
                $alguno = true;
                if ($formato == "threegppAacQcifResource") $this->duration = $this->getDurationStr($to); // obtengo la duracion del formato mas grande
            } else {
                reportError( '<br/>Error bajando formato '.$formato.' from '.$from.'.<br />');
            }

            $result[$formato] = $bajado;
        }

        if(!$alguno){
            $log .= "Borrando el directorio para los videos. NO HAY NINGUN FORMATO.\n";
            reportError( '<br/>Error bajando VIDEOS.');
            exec("rm -r {$this->dirToWrite}");
        }

        // Generamos el thumb
        if(!($this->thumbnailExists = $this->generateThumbnail())) {
            reportError("ERROR: No se pudo bajar el thumbnail para \"" . $this->getTitle() . "\"");
        }
        return $result;
    }

    public function updateXML() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $this->addLog("Generando XML UPDATE para $this->contentId");
        //$keywordStr = "\"" . implode("\r\n", $this->keywords) . "\"";
        $keywordStr = implode(" ", $this->keywords);

        if($this->webCat == "Videos Musicales") {
            $creator = $this->artist;
        } else {
            $creator = $this->artist.$this->marca.$this->festivo;
        }

        $this->arraySubForm = array(
                            "Content Title" => $this->getTitle(),
                            "Website Category" => $this->webCat,
                            "Genres" => $this->tag,
                            "Creator" => $this->getCreator(),
                           );
        $this->arraySubForm["Thumbnail"] = "";

        if (!is_numeric($this->contentId) || $this->contentId<=0) $this->genException("CRIT", __LINE__, __METHOD__, ": content_id no valido");

        $carriers = explode(",", CARRIERS);
        $langs = explode(",", LANGS);

        $xmlstr = XML_HEADER.'<mgdVideoUpdate xmlns="http://www.qpass.net/telcel/mgdVideo"
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

        // TAG:TITLE
        foreach($this->langs as $lang) {
            if (mustTranslateToEN($lang) === TRUE) {
                $trans = translate($this->dbc, $this->contentId, 'nombre');
                if (($trans === FALSE) || ($trans === NULL)) reportError("ERROR: Ocurrio un error traduciendo $this->contentId => 'nombre'");
                else $xmlstr .= "\t<title qpass:lang=\"{$this->obtainLang($lang)}\">".konvert($trans)."</title>\r\n";
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
            if (mustTranslateToEN($lang) === TRUE) {
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
            if (mustTranslateToEN($lang) === TRUE) {
                $trans = translate($this->dbc, $this->contentId, 'short_desc');
                if (($trans === FALSE) || ($trans === NULL)) reportError("ERROR: Ocurrio un error traduciendo $this->contentId => 'short_desc'");
                else $xmlstr .= "\t<shortDescription qpass:lang=\"{$this->obtainLang($lang)}\">".konvert($trans)."</shortDescription>\r\n";
            } else $xmlstr .= "\t<shortDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getAbstract()}</shortDescription>\r\n";
        }

        // TAG:LONGDESCRIPTION
        foreach($this->langs as $lang) {
            if (mustTranslateToEN($lang) === TRUE) {
                $trans = translate($this->dbc, $this->contentId, 'long_desc');
                if (($trans === FALSE) || ($trans === NULL)) reportError("ERROR: Ocurrio un error traduciendo $this->contentId => 'long_desc'");
                else $xmlstr .= "\t<longDescription qpass:lang=\"{$this->obtainLang($lang)}\">".konvert($trans)."</longDescription>\r\n";

            } else $xmlstr .= "\t<longDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->getLongDescription()}</longDescription>\r\n";
        }

        // TAG:WEBSITECATEGORY
        if (!empty($this->webCat)) $xmlstr .= "\t<websiteCategory>{$this->webCat}</websiteCategory>\n";


        $xmlstr .= "</mgdVideoUpdate>";

        $this->addLog("XML Generado:\n".$xml);
        return $xmlstr;
    }




    public function getDirToWrite() {
        return $this->dirToWrite;
    }

    public function setDirToWrite($dirToWrite) {
        $this->dirToWrite = $dirToWrite;
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
        $this->webCat = $webCat;
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
        $from = "/www.wazzup.com.uy/".$from;

        $tmp = $this->dirToWrite."/thumbnail" . sanitizeString($this->getTitle()) . ".gif";
        $this->thumbnailPath = $tmp;
//        echo "***$from => $tmp***";
        $bajado = $ftpCon->bajar_r($from, $tmp, FTP_DOWN_RETRIES);
        return $bajado;
    }

    private function genException($type, $line, $method, $msg) {
        $this->addLog("$type <$line> $method $msg");
        if ($type == "CRIT") throw new Exception("$type <$line> $method $msg");
    }

    private function getDurationStr($pathToFile) {
        $pathToFile = escapeshellcmd("/mnt/storage/www/tools/cms/".$pathToFile);
        $retorno = exec("ffmpeg -i $pathToFile 2>&1 | grep \"Duration\" | cut -d ' ' -f 4 | sed s/,//", $lines, $returnCode);
//        echo "ffmpeg -i $pathToFile 2>&1 | grep \"Duration\" | cut -d ' ' -f 4 | sed s/,//";
        if(!$returnCode) {
//            echo "***$retorno***";
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
        $this->contentFilename = ereg_replace("[^A-Za-z0-9]", "",sanitizeString($this->getTitle()))  . ".$ext";
        //$this->previewFilename = "preview" . str_replace(" ", "", sanitizeString($this->title) . ".gif";
        $this->thumbnailFilename = "thumbnail" . ereg_replace("[^A-Za-z0-9]", "",sanitizeString($this->getTitle())) . ".gif";
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