<?php

class migWallpaper {
// estos cambian
    private $uniqueId;
    private $name;
    private $title;
    private $tag;
    private $subTag;
    private $longDescription;
    private $shortDescription;
    private $keywords;
    private $provider;
    private $availableContent = array();
    private $dirToWrite;
    //private $watermark = "/home/kamus/Web/mig/images/62x62.png";
    //private $watermark = "/home/kamus/Web/mig/images/logo.png";
    private $previewExists;
    private $thumbnailExists;
    private $catLvl;
    private $webCat;
    private $rating;

    private $arraySubForm;

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
    private $logFile = "/home/kamus/Web/mig/logs/logswallpaper.class.log";
  //  private $sizes = array("480X320","360X640","320X240","240X320","220X176","128X160","128X128");

 /*   private $sizes = array("128X128",
                            "128X160",
                            "128X96",
                            "176X220",
                            "240X320",
                            "240X400",
                            "240X432",
                            "320X240",
                            "360X640",
                            "320X480",
                            "360X480",
                            "480X800");
*/
private $sizes = array();
                           //,"128X90","300X300","480X800","480X360","240X432","240X400","176X200");

    function __construct($dbc, $debug=FALSE, $catLvl = "", $webCat = "", $rating = "") {
        $this->addLog("************************************ C O M E N Z A N D O *****************************************************************");
        global $servicesMig, $prtmig;
        $this->service = $servicesMig["wallpaper"];
        $this->premiumResource = $prtmig;
        $this->catLvl = $catLvl;
        $this->webCat = $webCat;

        if ($dbc === NULL) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: DB Null");
        else $this->dbc = $dbc;

        $this->debug_mode = $debug;
        $this->deployed = "false";
        $this->promotiononly = "false";
        $this->promotionkey = "";
        $this->rating = $rating;
        $this->startDate = "2008-12-01T00:00:00.000";
        $this->validUntil = "2099-12-31T00:00:00.000";

/*       $sql = "SELECT c.*, cc.descripcion nombreCat, cc.xxx, cp.nombre provider FROM Web.contenidos c
                        INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria)
                        INNER JOIN Web.contenidos_proveedores cp ON (c.proveedor = cp.id)
                        WHERE c.id=$this->contentId AND c.tipo='".WAZZUP_WALLPAPER."' $providerSql";
*/
$sql = "SELECT * FROM Web.sizes_wallpapers_amdocs ORDER BY orden asc" ;
        $this->addLog("SQL: ".$sql);
        $rs = mysql_query($sql, $this->dbc->db);
        if (!$rs) throw new Exception("ERROR: no se pueden obtener datos del contenido $this->contentId ");

	while ($o = mysql_fetch_object($rs)) {
		$this->sizes[] = $o->size;
	}


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

    public function loadContent($content_id, $provider = "") {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        if (!is_numeric($content_id) || $content_id<=0) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: content_id no valido");
        else $this->contentId = $content_id;

        $providerSql = ($provider == "") ? "" : " AND c.proveedor = $provider";

        $sql = "SELECT c.*, cc.descripcion nombreCat, cc.xxx, cp.nombre provider FROM Web.contenidos c
                        INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria)
                        INNER JOIN Web.contenidos_proveedores cp ON (c.proveedor = cp.id)
                        WHERE c.id=$this->contentId AND c.tipo='".WAZZUP_WALLPAPER."' $providerSql";
        $this->addLog("SQL: ".$sql);
        $rs = mysql_query($sql, $this->dbc->db);
        if (!$rs) throw new Exception("ERROR: no se pueden obtener datos del contenido $this->contentId ");
        else $obj = mysql_fetch_object($rs);

        $this->contentType = $obj->tipo;
        $this->provider = $obj->provider;
        $this->uniqueId = $obj->id;
        $this->keywords = array_map("trim", explode(",", konvert($obj->keywords)));
        $this->keywords = array_map("sanitizeString", $this->keywords);

        $this->adult = ($obj->xxx == "0") ? "false" : "true";

        $this->name = konvert($obj->nombre);
        //$this->title = konvert("$obj->nombre");
        $this->title = str_replace("-", " ",  konvert("$obj->nombre"));
        // $this->description = konvert(getContentDescription($this->contentId, $this->contentType, $obj->categoria));

        $this->archivo = str_replace("netuy", "contenido", $obj->archivo);
        $this->archivo = str_replace("128x128", "%RESOLUTION%", $this->archivo);
        $this->preview = str_replace("netuy", "contenido", $obj->archivo);
        $this->preview = str_replace("128x128", "%RESOLUTION%", $this->preview);

        $this->shortDescription = $this->title;
        $this->longDescription = $this->title;

        $this->setFilenames();
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

    public function getThumbnailFilename() {
        return $this->thumbnailFilename;
    }

    public function getContentFilename() {
        return $this->contentFilename;
    }

    public function getArraySubForm(){
        return $this->arraySubForm;
    }

    private function genException($type, $line, $method, $msg) {
        $this->addLog("$type <$line> $method $msg");
        if ($type == "CRIT") throw new Exception("$type <$line> $method $msg");
    }

    public function genXML() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $this->addLog("Generando XML para $this->contentId");
        //$keywordStr = "\"" . implode("\r\n", $this->keywords) . "\"";
        $keywordStr = implode("<br />", $this->keywords);

        $this->arraySubForm = array("CP name" => CP_NAME,
                                    "Date of Ingest" => date("d.m.Y"),
                                    "Zip File Name" => "",
                                    "Product Type" => "mgdImage",
                                    "ProviderContentGivenId" => $this->uniqueId,
                                    "Content Title" => $this->title,
                                    "Keywords" => $keywordStr,
                                    "Genres" => $this->tag,
                                    "Website Category" => $this->webCat,
                                    "Short Description" => $this->shortDescription,
                                    "Length of short decription" => "",
                                    "Creator" => $this->provider,
                                    "Thumbnail" => $this->getThumbnailFilename(),
                                    "Preview" => $this->getPreviewFilename(),
                                    "Mexico" => "",
                                    "Colombia" => "X",
                                    "Ecuador" => "X",
                                    "Panama" => "X",
                                    "Peru" => "X",
                                    "Revenue Mexico" => "",
                                    "40%","40%","40%","40%","", "COP 3.448,00", "0.99","$ 2.00","S/ 4,62"
                                   );

        if (!is_numeric($this->contentId) || $this->contentId<=0) $this->genException("CRIT", __LINE__, __METHOD__, ": content_id no valido");

        $carriers = explode(",", CARRIERS);
        $langs = explode(",", LANGS);

        $xmlstr = XML_HEADER."\n".'<mgdImageProduct xmlns="http://www.qpass.net/telcel/mgdImage"
                            xmlns:qpass="http://www.qpass.com/content" xmlns:jcr="http://www.jcp.org/jcr/1.0">'."\n";

        foreach($carriers as $carrier) {
            $xmlstr .= "\t<qpass:merchant>$carrier</qpass:merchant>\n";
        }

        $rating = ($this->adult) ? "18+" : $this->rating;
        $xmlstr .=<<<XML
                <qpass:rating>
                <qpass:scheme>Mexico</qpass:scheme>
                <qpass:value>$rating</qpass:value>
                </qpass:rating>
XML;
        $xmlstr .= "\t<providerGivenContentId>{$this->uniqueId}</providerGivenContentId>";

        foreach($langs as $lang) {
            $xmlstr .= "\t<title qpass:lang=\"$lang\">{$this->title}</title>\n";
        }

        $xmlstr .= "\t<genres>{$this->tag}</genres>\n";
        $xmlstr .= "\t<subgenres1>{$this->subTag}</subgenres1>\n";
        $xmlstr .= "\t<publisher>{$this->provider}</publisher>\n";
        $xmlstr .= "\t<creators>{$this->provider}</creators>\n";

        foreach($langs as $lang) {
            foreach($this->keywords as $keyword) {
                $xmlstr .= "\t<keywords qpass:lang=\"$lang\">{$keyword}</keywords>\n";
            }
        }

        foreach($langs as $lang) {
            $xmlstr .= "\t<shortDescription qpass:lang=\"$lang\">{$this->shortDescription}</shortDescription>\n";
        }
        
        foreach($langs as $lang) {
            $xmlstr .= "\t<longDescription qpass:lang=\"$lang\">{$this->longDescription}</longDescription>\n";
        }
        
        $xmlstr .= "\t<categoryLevel>{$this->catLvl}</categoryLevel>\n";

        $curdate = date("d-M-Y");
        $xmlstr .= "\t<creationDate>$curdate</creationDate>\n";
        $xmlstr .= "\t<releaseDate>$curdate</releaseDate>\n";
        $xmlstr .= "<websiteCategory>{$this->webCat}</websiteCategory>\n";

        $name = $this->getThumbnailFilename();
        $xmlstr .=<<<XML
	  <thumbnail>
                <thumbnailResource>
                        <qpass:resourceFilename>$name</qpass:resourceFilename>
                        <qpass:mimeType>image/gif</qpass:mimeType>
                </thumbnailResource>
	  </thumbnail>
XML;

        $name = $this->getPreviewFilename();
        $xmlstr .=<<<XML
      <preview>
            <previewGifResource>
                    <qpass:resourceFilename>$name</qpass:resourceFilename>
                    <qpass:mimeType>image/gif</qpass:mimeType>
            </previewGifResource>
      </preview>
XML;

        $filename = $this->getContentFilename();
        $xmlstr .= "\t<premium>";
        foreach($this->availableContent as $size => $available) {
            if($available) {
                // DO not mess with upper and lower case. Go kill yourself instead.
                $size = strtoupper($size);
                $thisfile = str_replace("%RESOLUTION%", strtolower($size), $filename);
                $xmlstr .=<<<XML
                <MR{$size}gifResource>
                    <qpass:resourceFilename>{$thisfile}</qpass:resourceFilename>
                    <qpass:mimeType>image/gif</qpass:mimeType>
                </MR{$size}gifResource>
XML;
            }
        }
        $xmlstr .= "\t</premium>";
        $xmlstr .= "</mgdImageProduct>";
        $this->addLog("XML Generado:\n".$xml);
        return $xmlstr;
    }

    public function downloadContent($ftpCon) {
        $this->dirToWrite = TMP_DIR_WP."/" . $this->uniqueId;
        if (!is_writable(TMP_DIR_WP)) {
            die("ERROR: ".TMP_DIR_WP." is not writable\n");
        } else {
            $log .= "Creando el directorio para las imagnes...\n";
            exec("mkdir {$this->dirToWrite}");
        }

        if (!is_writable($this->dirToWrite)) {
            die("ERROR: {$this->dirToWrite} is not writable\n");
        }
        foreach($this->sizes as $size) {
            $size = strtolower($size);
            $to = $this->dirToWrite."/".$this->getContentFilename();
            $to = str_replace("%RESOLUTION%", $size, $to);

            $from = $this->getContent();
            $from = str_replace("%RESOLUTION%", $size, $from);
            $bajado = $ftpCon->bajar_r($from, $to, FTP_DOWN_RETRIES);

            $this->availableContent[$size] = array("status" => $bajado, "id" => $this->contentId);
        }

        // Generamos el preview
        if(!($this->previewExists = $this->generatePreview())) {
            echo "No se pudo bajar el preview para \"" . $this->title . "\"<br />";
        }

          // Generamos el thumb
        if(!($this->thumbnailExists = $this->generateThumbnail())) {
            echo "No se pudo bajar el tumbnail para \"" . $this->title . "\"<br />";
        }
        return $this->availableContent;
    }

    public function getDirToWrite() {
        return $this->dirToWrite;
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
        $from = str_replace("%RESOLUTION%", PREVIEW_WIDTH."x".PREVIEW_HEIGHT, $this->getContent());
        $from = str_replace("contenido", "netuy", $from);
        $tmp = $this->dirToWrite."/preview" . sanitizeString($this->title) . ".gif";
        $bajado = $ftpCon->bajar_r($from, $tmp, FTP_DOWN_RETRIES);
        return $bajado;
    }

    private function generateThumbnail() {
        $ftpCon = new Ftp(FTP_USA, FTP_USA_USR, FTP_USA_PWD);
        $connected = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
        if(!$connected) {
            return false;
        }
        $from = str_replace("%RESOLUTION%", PREVIEW_WIDTH."x".PREVIEW_HEIGHT, $this->getContent());
        $from = str_replace("contenido", "netuy", $from);
        //echo "Bajando FROM: $from";
        $tmp = $this->dirToWrite."/thumbnail" . sanitizeString($this->title) . ".gif";
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
    }
    private function setFilenames() {
        /*
        $this->contentFilename = str_replace(" ", "", $this->title) . "_%RESOLUTION%.gif";
        $this->previewFilename = "preview" . str_replace(" ", "_", $this->title) . ".gif";
        $this->thumbnailFilename = "thumbnail" . str_replace(" ", "_", $this->title) . ".gif";
        */

        $this->contentFilename = sanitizeString($this->title) . "_%RESOLUTION%.gif";
        $this->previewFilename = "preview" . sanitizeString($this->title) . ".gif";
        $this->thumbnailFilename = "thumbnail" . sanitizeString($this->title) . ".gif";
    }
}
?>
