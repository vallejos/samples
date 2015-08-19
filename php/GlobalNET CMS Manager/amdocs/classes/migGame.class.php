<?php



class migGame {
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
        private $tagEng;
        private $subTagEng;
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
	private $logFile = "/mnt/storage/www/tools/cms/amdocs/logs/game.class.log";
	private $sizes = array("480X320","360X640","320X240","240X320","220X176","128X160","128X128","128X90","300X300");
                           //,"480X800","480X360","240X432","240X400","176X200");

	// mandatory fields
//      private $mandatoryXMLFields = array("externalId", "deployed", "promotiononly", "startDate", "validUntil", "service", "adult", "name", "title", "tag", "premiumResource");
	private $mandatoryXMLFields = array("externalId", "deployed", "promotiononly", "startDate", "validUntil", "adult", "name", "title", "tag");

        private $marca;
        private $festivo;

        private $arrayDatos = array();
        private $arraySubForm = array();
        private $files = array();
        private $nombre_cat = "";
        private $arrayDevicesCompatibles = array();
        private $is3D;
        private $isPremium;
        private $rating;

        private $langs;
        private $merchants;

	// constructor
	function __construct($dbc, $debug=FALSE, $catLvl = "", $webCat = "", $rating="", $marca="", $festivo="") {
		$this->addLog("************************************ C O M E N Z A N D O *****************************************************************");
		global $servicesMig, $prtmig;
		$this->service = $servicesMig["wallpaper"];
		$this->premiumResource = $prtmig;
		$this->catLvl = $catLvl;
		$this->webCat = $webCat;
                $this->rating = $rating;

		if ($dbc === NULL) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: DB Null");
		else $this->dbc = $dbc;

		$this->debug_mode = $debug;

		$this->deployed = "false";
		$this->promotiononly = "false";
		$this->promotionkey = "";
		$this->startDate = "2008-12-01T00:00:00.000";
		$this->validUntil = "2099-12-31T00:00:00.000";
                $this->marca = (empty($marca)) ? " (Ideas)" : " $marca";
                $this->festivo = (empty($festivo)) ? "" : " ($festivo)";
                $this->is3D = ($this->webCat == "Juegos 3D") ? TRUE : FALSE;
                $this->isPremium = ($this->webCat == "Juegos Premium") ? TRUE : FALSE;
                $this->tagEng = "" ;
                $this->subTagEng = "";
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


        public function addFiles($contentId, $files) {
            $this->files[$contentId] = $files;
        }

	// carga los datos del contenido
	public function loadContent($content_id, $provider = "") {
		$this->addLog("<".__LINE__."> ".__METHOD__);
		if (!is_numeric($content_id) || $content_id<=0) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: content_id no valido");
		else $this->contentId = $content_id;

		$providerSql = ($provider == "") ? "" : " AND c.proveedor = $provider";

		$sql = "SELECT c.*, cc.descripcion nombreCat, gi.screenshots, cp.nombre manufacturer, cc.xxx, gi.descr, gi.descr_wap FROM Web.contenidos c INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria) INNER JOIN Web.gamesInfo gi ON (gi.game=c.id) INNER JOIN Web.contenidos_proveedores cp ON (cp.id=c.proveedor) WHERE c.id=$this->contentId  AND c.tipo='".WAZZUP_GAME."' $providerSql";
		$this->addLog("SQL: ".$sql);
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) throw new Exception("ERROR: no se pueden obtener datos del contenido $this->contentId ");
		else $obj = mysql_fetch_object($rs);

		$this->contentType = $obj->tipo;
		$this->provider = $obj->manufacturer;
		$this->uniqueId = $obj->id;
                $this->nombre_cat = $obj->nombreCat;

		$this->adult = ($obj->xxx == "0") ? "false" : "true";

		// genero nombres unicos para contenidos y preview
		$this->setFilenames();

		$this->name = konvert($obj->nombre);
		$this->title = str_replace("-", " ",  konvert("$obj->nombre"));
                if ($this->is3D === TRUE) $this->title .= " (3D)";
                if ($this->isPremium === TRUE) $this->title .= " (Premium)";
		$this->longDescription = strip_tags(konvert($obj->descr));
                $this->shortDescription = konvert($obj->descr_wap).$this->marca.$this->festivo;

		$this->archivo = str_replace("netuy", "contenido", $obj->archivo);
		$this->archivo = str_replace("128x128", "600x600", $this->archivo);

		$this->language = "English";
/*
		$screens = explode(",", $obj->screenshots);
		$pathTo = $screens[0];
		$screens = $screens[1];
		$path = explode(".", $screens);
		$path = $path[0].".gif";
		$this->preview = $pathTo.$path;
                */
                $carpetaID = (ceil($this->contentId/500)*500);

                $this->preview = "/contenido/java/100x100/" . $carpetaID . "/" . $this->contentId . ".gif";
//		$this->preview = str_replace("128x128", "110x110", $this->preview);

		$this->manufacturer = $obj->manufacturer;
		$this->pubYear = date("Y");
		$this->multiplayer = "false";

		$this->preview_uri = $this->getPreviewFilename();
		$this->variantUri = $this->getContentFilename();

                // cargo keywords
                $array_articulos = array("tus", ":", "(", ")", "tu", "mi", "mio", "mia", "de", "sus", "que", "en", "las", "son", "a", "y", "el", "la", "o", "u", "ella", "nosotros", "ellos");
                $desc = $this->shortDescription;
                $desc = str_replace("!", "", $desc);
                $desc = str_replace("&#161;", "", $desc);

                $desc = str_replace("¡", "", $desc);
                $desc = str_replace("'", "", $desc);
                $desc = str_replace("\"", "", $desc);
                $desc = str_replace(",", "", $desc);
                $desc = str_replace(".", "", $desc);
                $desc = str_replace("?", "", $desc);
                $desc = str_replace("¿", "", $desc);
                foreach ($array_articulos as $art) {
                    $desc = str_ireplace(" ".$art." ", " ", $desc);
                }
                $desc = str_replace(",", " ", $desc);
                $desc = str_replace("(", " ", $desc);
                $desc = str_replace(")", " ", $desc);

                //FALTA AGREGAR nombre contenido, nombre cat, proveedor
                $keywords = explode(" ", $desc);
                $keywords[] = str_replace(" ", ",", $this->name);
                $keywords[] = $this->manufacturer;
                $keywords[] = $this->nombre_cat;

                $this->setKeywords($keywords);
	}

	public function setTag($cat, $lang='es') {
                if ($lang == "en") $this->tagEng = konvert($cat);
		else $this->tag = konvert($cat);
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


        public function setDirToWrite($dirToWrite) {
            $this->dirToWrite = $dirToWrite;
        }

	public function setSubTag($cat, $lang='es') {
                if ($lang == "en") $this->subTagEng = konvert($cat);
		else $this->subTag = konvert($cat);
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

      public function getArrayDatos() {
          return $this->arrayDatos;
      }

        public function getArraySubForm(){
            return $this->arraySubForm;
        }

          public function getCompatibleDevices() {
            return $this->arrayDevicesCompatibles;
          }

	public function genXML() {

            $archivos = $this->files[$this->contentId];
            $nombres = array();
            foreach($archivos as $i => $ruta) {
                $tmp = explode("/", $ruta);
                $tmp = $tmp[count($tmp) - 1];
                $nombres[$i] = $tmp;
            }
            $binarios = implode("<br>", $nombres);
            $modelos  = "";
            $modelos_mig_str = "";
            $modelos_mig = array();
            foreach($archivos as $i => $arch) {
                $ids_celulares = getAllModels($this->dbc, $arch);
//                echo "Cantidad de modelos compatibles: " . count($ids_celulares);
                foreach ($ids_celulares as $idC) {
                    $modelo_mig = getSuggestedMigModelById($idC);
                    if(!empty($modelo_mig)) {
                        $modelos_mig[] = array("Binary" => $nombres[$i], "Compatible Devices" => $modelo_mig);
//                        $modelos_mig_str = implode("\n", $modelos_mig);
                    }

                }
            }
            $modelos_mig_str = $modelos_mig; //implode("<br>", $modelos_mig);

/*                $this->arrayDatos = array("CP Name" => "Wazzup_cp",
                                          "Date of Ingest" => date("d.m.Y"),
                                          "Zip Filename" => "",
                                          "Product Type" => "mgdGame",
                                          "ProviderGivenContentID" => $this->contentId,
                                          "Content Title" => $this->name,
                                          "Keywords" => implode("<br/>", $keywords),
                                          "Website Category" => $this->webCat,
                                          "Short Description (Abstract)" => $this->shortDescription,
                                          "Length of short description" => "", //dejar vacio
                                          "Creator" => $this->provider,
                                          "Genres" => "{$this->tag}",
                                          "Binary" => $modelos_mig_str,
                                          "Compatible Devices" => $modelos_mig_str,
                                          "Thumbnail" => '<img src="'.$this->preview_uri.'"/>',
                                          "Preview" => "",
                                          "Mexico" => "",
                                          "Colombia" => "X",
                                          "Ecuador" => "X",
                                          "Panama" => "X",
                                          "Peru" => "X",
                                          "Argentina" => "",
                                          "Paraguay" => "",
                                          "Uruguay" => "",
                                          "Brasil" => "",
                                          "Chile" => "",
                                          "Guatemala" => "",
                                          "El Salvador" => "",
                                          "Honduras" => "",
                                          "Nicaragua" => "",
                                          "Puerto Rico" => "",
                                          "República Dominicana" => "",
                                          "Jamaica" => "",
                                          "CP Revenue Share Mexico" => "",
                                          "CP Revenue Share Colombia" => "40",
                                          "CP Revenue Share Ecuador" => "40",
                                          "CP Revenue Share Panama" => "40",
                                          "CP Revenue Share Peru" => "40",
                                          "CP Revenue Share Argentina" => "",
                                          "CP Revenue Share Paraguay" => "",
                                          "CP Revenue Share Uruguay" => "",
                                          "CP Revenue Share Brasil" => "",
                                          "CP Revenue Share Chile" => "",
                                          "CP Revenue Share Guatemala" => "",
                                          "CP Revenue Share El Salvador" => "",
                                          "CP Revenue Share Honduras" => "",
                                          "CP Revenue Share Nicaragua" => "",
                                          "CP Revenue Share Puerto Rico" => "",
                                          "CP Revenue Share República Dominicana" => "",
                                          "CP Revenue Share Jamaica" => "",
                                          "Precio México (Pesos)" => "",
                                          "Precio Colombia (Pesos Colombianos)" => "4310",
                                          "Precio Ecuador (Dólar)" => "2,9",
                                          "Precio Panama (Dólar)" => "3,1",
                                          "Precio  Peru (Sol)" => "7,56",
                                          "Precio Argentina (Pesos)" => "",
                                          "Precio Paraguay (Guaraní)" => "",
                                          "Precio Uruguay (Pesos uruguayos)" => "",
                                          "Precio Brasil (R$)" => "",
                                          "Precio Chile (Pesos chilenos)" => "",
                                          "Precio Guatemala (Quetzales)" => "",
                                          "Precio El Salvador (Colón)" => "",
                                          "Precio Honduras (El Lempira)" => "",
                                          "Precio Nicaragua (Córdoba)" => "",
                                          "Precio Puerto Rico (Dólar)" => "",
                                          "Precio República Dominicana (RD$)" => "",
                                          "Precio Jamaica (J$)" => "",
                                          "Successfully Ingested" => "",
                                          "Ingestion Errors" => "");*/
                $this->arrayDatos = array(
                    "Content Title" => $this->title,
                    "Website Category" => $this->webCat,
                    "Genres" => $this->tag,
                      "Short Description" => $this->shortDescription,
                    "Creator" => $this->artist.$this->marca.$this->festivo,
               );

                $this->arrayDevicesCompatibles = array(
                    "Binary" => $modelos_mig_str,
                    "Compatible Devices" => $modelos_mig_str,
                );

//var_dump($this->arrayDevicesCompatibles);


		$this->addLog("<".__LINE__."> ".__METHOD__);
		$this->addLog("Generando XML para $this->contentId");

		if (!is_numeric($this->contentId) || $this->contentId<=0) $this->genException("CRIT", __LINE__, __METHOD__, ": content_id no valido");

		$carriers = explode(",", CARRIERS);
		$langs = explode(",", LANGS);

		$xmlstr = XML_HEADER."\n".'<mgdGamesProduct xmlns="http://www.qpass.net/telcel/mgdGames"
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
		<qpass:value>$this->rating</qpass:value>
		<qpass:comment>Comment</qpass:comment>
	</qpass:rating>
XML;
		$xmlstr .= "\n\t<providerGivenContentId>{$this->uniqueId}</providerGivenContentId>\n";

        // TAG:TITLE
        foreach($this->langs as $lang) {
            if (mustTranslateToEN($lang) === TRUE) $xmlstr .= "\t<title qpass:lang=\"{$this->obtainLang($lang)}\">{$this->title}</title>\r\n"; // TODO: obtener version traducida
            else $xmlstr .= "\t<title qpass:lang=\"{$this->obtainLang($lang)}\">{$this->title}</title>\r\n";
        }

		$xmlstr .= "\t<genres>{$this->tag}</genres>\n";
		$xmlstr .= "\t<subgenres1>{$this->subTag}</subgenres1>\n";
		$xmlstr .= "\t<creators>wazzup</creators>\n";
		$xmlstr .= "\t<publisher>wazzup</publisher>\n";
		$xmlstr .= "\t<language>{$this->language}</language>\n";

        // TAG:KEYWORDS
        foreach($this->langs as $lang) {
            foreach($this->keywords as $keyword) {
                if (!empty($keyword)) {
                    if (mustTranslateToEN($lang) === TRUE) $xmlstr .= "\t<keywords qpass:lang=\"{$this->obtainLang($lang)}\">{$keyword}</keywords>\r\n"; // TODO: obtener version traducida
                    else $xmlstr .= "\t<keywords qpass:lang=\"{$this->obtainLang($lang)}\">{$keyword}</keywords>\r\n";
                }
            }
        }

        // TAG:SHORTDESCRIPTION
        foreach($this->langs as $lang) {
            if (mustTranslateToEN($lang) === TRUE) $xmlstr .= "\t<shortDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->shortDescription}</shortDescription>\r\n"; // TODO: obtener version traducida
            else $xmlstr .= "\t<shortDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->shortDescription}</shortDescription>\r\n";
        }

        // TAG:LONGDESCRIPTION
        foreach($this->langs as $lang) {
            if (mustTranslateToEN($lang) === TRUE) $xmlstr .= "\t<longDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->longDescription}</longDescription>\r\n"; // TODO: obtener version traducida
            else $xmlstr .= "\t<longDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->longDescription}</longDescription>\r\n";
        }


		$xmlstr .= "\t<categoryLevel>{$this->catLvl}</categoryLevel>\n";

		$curdate = date("d-M-Y");
		$xmlstr .= "\t<creationDate>$curdate</creationDate>\n";
		$xmlstr .= "\t<releaseDate>$curdate</releaseDate>\n";
		$xmlstr .= "\t<websiteCategory>{$this->webCat}</websiteCategory>\n";

		$name = $this->getThumbnailFilename();
//                $this->arrayDatos["Thumbnail"] = '<img src="'.$this->getDirToWrite()."/".$name.'"/>';
		$xmlstr .=<<<XML
	<thumbnail>
		<thumbnailResource>
			<qpass:resourceFilename>$name</qpass:resourceFilename>
			<qpass:mimeType>image/gif</qpass:mimeType>
		</thumbnailResource>
	</thumbnail>
XML;

		$name = $this->getPreviewFilename();
		$xmlstr .= "\n\t<previewImage>\n";
		$xmlstr .= <<<XML
		<previewGifResource>
			<qpass:resourceFilename>$name</qpass:resourceFilename>
			<qpass:mimeType>image/gif</qpass:mimeType>
		</previewGifResource>
	</previewImage>
XML;
				$xmlstr .= "\n\t<premium>\n";
$xmlstr .= "%%PREMIUM%%";
				$xmlstr .= "\t</premium>";

/*
		$filename = $this->getContentFilename();
		$xmlstr .= "\n\t<premium>";
		foreach ($this->availableContent as $size => $available) {
			if ($available) {
				$size = strtoupper($size);
				$thisfile = str_replace("%RESOLUTION%", $size, $filename);
				$xmlstr .=<<<XML
				<jarResource>
					<qpass:resourceFilename>{$thisfile}</qpass:resourceFilename>
					<qpass:mimeType>image/gif</qpass:mimeType>
					<qpass:resourceFilename>{$thisfile}</qpass:resourceFilename>
					<qpass:descriptorFilename>{$thisfile}.jad</qpass:descriptorFilename>
					<qpass:mimeType>application/java-archive</qpass:mimeType>
					<qpass:deviceId>samsung_sgh_e496_ver1</qpass:deviceId>
				</jarResource>
XML;
			}
		}

		$xmlstr .= "\t</premium>\n";
*/
		$xmlstr .= "</mgdGamesProduct>";
		$this->addLog("XML Generado:\n".$xml);

		return $xmlstr;
	}


    public function updateXML() {
        $this->addLog("<".__LINE__."> ".__METHOD__);
        $this->addLog("Generando XML UPDATE para $this->contentId");
        //$keywordStr = "\"" . implode("\r\n", $this->keywords) . "\"";
        $keywordStr = implode(" ", $this->keywords);

        $this->arraySubForm = array(
                            "Content Title" => $this->title,
                            "Website Category" => $this->webCat,
                            "Genres" => $this->tag,
                            "Creator" => $this->artist.$this->festivo,
                           );

        if (!is_numeric($this->contentId) || $this->contentId<=0) $this->genException("CRIT", __LINE__, __METHOD__, ": content_id no valido");

        $carriers = explode(",", CARRIERS);
        $langs = explode(",", LANGS);

        $xmlstr = XML_HEADER.'<mgdGamesUpdate xmlns="http://www.qpass.net/telcel/mgdGames"
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
                if (($trans === FALSE) || ($trans === NULL)) echo "ERROR: Ocurrio un error traduciendo $this->contentId => 'nombre'";
                else $xmlstr .= "\t<title qpass:lang=\"{$this->obtainLang($lang)}\">".konvert($trans)."</title>\r\n";
            } else $xmlstr .= "\t<title qpass:lang=\"{$this->obtainLang($lang)}\">{$this->title}</title>\r\n";
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
                    echo "ERROR: Ocurrio un error traduciendo $this->contentId => 'keywords'" ;
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
                if (($trans === FALSE) || ($trans === NULL)) echo "ERROR: Ocurrio un error traduciendo $this->contentId => 'short_desc'";
                else $xmlstr .= "\t<shortDescription qpass:lang=\"{$this->obtainLang($lang)}\">".konvert($trans)."</shortDescription>\r\n";
            } else $xmlstr .= "\t<shortDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->shortDescription}</shortDescription>\r\n";
        }

        // TAG:LONGDESCRIPTION
        foreach($this->langs as $lang) {
            if (mustTranslateToEN($lang) === TRUE) {
                $trans = translate($this->dbc, $this->contentId, 'long_desc');
                if (($trans === FALSE) || ($trans === NULL)) echo "ERROR: Ocurrio un error traduciendo $this->contentId => 'long_desc'";
                else $xmlstr .= "\t<longDescription qpass:lang=\"{$this->obtainLang($lang)}\">".konvert($trans)."</longDescription>\r\n";

            } else $xmlstr .= "\t<longDescription qpass:lang=\"{$this->obtainLang($lang)}\">{$this->longDescription}</longDescription>\r\n";
        }

        // TAG:WEBSITECATEGORY
        if (!empty($this->webCat)) $xmlstr .= "\t<websiteCategory>{$this->webCat}</websiteCategory>\n";


        $xmlstr .= "</mgdGamesUpdate>";

        $this->addLog("XML Generado:\n".$xml);
        return $xmlstr;
    }



    public function downloadContent($ftpCon) {
		global $tmpDir;
        $this->dirToWrite = $tmpDir."/" . $this->uniqueId;
        if (!is_writable($tmpDir)) {
            die("ERROR: ".$tmpDir." is not writable\n");
        } else {
//            echo " creando el directorio para las imagenes...\n";
            exec("mkdir {$this->dirToWrite}");
        }

        if (!is_writable($this->dirToWrite)) {
            die("ERROR: {$this->dirToWrite} is not writable\n");
        }

        // Generamos el preview
        if(!($this->previewExists = $this->generatePreview())) {
            echo "<font color=\"red\">No se pudo bajar el preview para \"" . $this->title . "\"</font><br />";
        }

          // Generamos el thumb
        if(!($this->thumbnailExists = $this->generateThumbnail())) {
            echo "<font color=\"red\">No se pudo bajar el thumbnail para \"" . $this->title . "\"</font><br />";
        }
//        return $this->availableContent;
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
$from = "/www.wazzup.com.uy".$from;
$this->previewFilename = "preview" .str_replace(" ", "_", sanitizeString($this->title)) . ".gif";
		$tmp = $this->dirToWrite."/".$this->previewFilename;
//echo "preview $from => $tmp";
		$bajado = $ftpCon->bajar_r($from, $tmp, FTP_DOWN_RETRIES);
		return $bajado;
	}

	private function generateThumbnail() {
		$ftpCon = new Ftp(FTP_USA, FTP_USA_USR, FTP_USA_PWD);
		$connected = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
		if(!$connected) {
			return false;
		}
		$from = str_replace("%RESOLUTION%", PREVIEW_WIDTH."x".PREVIEW_HEIGHT, $this->getPreview());
		$from = str_replace("contenido", "netuy", $from);
                $from = "/www.wazzup.com.uy".$from;
		$this->thumbnailFilename = "thumbnail" .str_replace(" ", "_", sanitizeString($this->title)) . ".gif";
		$tmp = $this->dirToWrite."/".$this->thumbnailFilename;
//echo "thumb $from => $tmp";
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