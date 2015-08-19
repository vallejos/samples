<?php

class druttFulltrack {
	// estos cambian
	private $externalId;
	private $uniqueId;
	private $service = ""; // obtener de tpim
	private $key;
	private $album;
	private $title;
	private $tag;
	private $artist;
	private $publicationYear;
	private $description;
	private $premiumResource = ""; // obtener de tpim
	private $preview_uri;
	private $variantUri;

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
	private $debug_log;
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
		global $servicesDrutt, $prtdrutt;
		$this->service = $servicesDrutt["fulltrack"];
		$this->premiumResource = $prtdrutt;

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

		$sql = "SELECT c.*, cc.descripcion nombreCat, ft.al_titulo album, ft.al_artista artista, ft.al_id, ft.tr_archivo, ft.tr_archivo_preview, cc.xxx FROM Web.contenidos c INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria) INNER JOIN Web.fulltracks ft ON (ft.contenido=c.id) WHERE c.id=$this->contentId  AND c.tipo='".WAZZUP_FULLTRACK."'" ;
		$this->addLog("SQL: ".$sql);
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) throw new Exception("ERROR: no se pueden obtener datos del contenido $this->contentId ");
		else $obj = mysql_fetch_object($rs);

		$this->contentType = $obj->tipo;

		$this->adult = ($obj->xxx == "0") ? "false" : "true";

		// genero external Id
		$this->setExternalId();

		// genero nombres unicos para contenidos y preview
		$this->setFilenames();

		$this->artist = konvert($obj->artista);
		$this->artist = konvert($obj->album);
		$this->title = konvert("$obj->nombre");
		$this->description = konvert(getContentDescription($this->contentId, $this->contentType, $obj->categoria));

		$this->publicationYear = date("Y");

		// v1.0
//		$this->archivo = trim($obj->al_id."/".$obj->tr_archivo);
//		$this->preview = trim($obj->al_id."/".$obj->tr_archivo_preview);

		// v1.3
		$this->archivo = trim("/contenido/fulltracks/".$obj->archivo);
		$this->preview = trim("/contenido/fulltracks/".$obj->referencia);

		$this->preview_uri = $this->getPreviewFilename();
		$this->variantUri = $this->getContentFilename();
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

		if ($this->checkMandatoryField() !== TRUE) $this->genException("CRIT", __LINE__, __METHOD__, "Faltan datos mandatorios");

		$xml = '<fulltrack externalId="'.$this->externalId.'">'."\n";
		$xml .= '<deployed>'.$this->deployed.'</deployed>'."\n";
		$xml .= '<promotiononly>'.$this->promotiononly.'</promotiononly>'."\n";
		$xml .= '<promotionkey>'.$this->promotionkey.'</promotionkey>'."\n";
		$xml .= '<startDate>'.$this->startDate.'</startDate>'."\n";
		$xml .= '<validUntil>'.$this->validUntil.'</validUntil>'."\n";
		$xml .= '<service>'.$this->service.'</service>'."\n";
		$xml .= '<adult>'.$this->adult.'</adult>'."\n";
		$xml .= '<key>'.$this->key.'</key>'."\n";
		$xml .= '<title>'.$this->title.'</title>'."\n";
		$xml .= '<artist>'.$this->artist.'</artist>'."\n";
		$xml .= '<tag>'.$this->tag.'</tag>'."\n";
		$xml .= '<album>'.$this->album.'</album>'."\n";
		$xml .= '<publicationYear>'.$this->publicationYear.'</publicationYear>'."\n";
		$xml .= '<description>'.$this->description.'</description>'."\n";
		$xml .= '<fulltrackPrelisten>'."\n";
		$xml .= '<variant>'."\n";
		$xml .= '<item uri="'.$this->preview_uri.'" />'."\n";
		$xml .= '</variant>'."\n";
		$xml .= '</fulltrackPrelisten>'."\n";
		$xml .= '<fulltrackPremium premiumResource="'.$this->premiumResource.'">'."\n";
		$xml .= '<variant>'."\n";
		$xml .= '<item uri="'.$this->variantUri.'" />'."\n";
		$xml .= '</variant>'."\n";
		$xml .= '</fulltrackPremium>'."\n";
		$xml .= '</fulltrack>'."\n";

		$this->addLog("XML Generado:\n".$xml);

		return $xml;
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
		$this->contentFilename = "content_".$this->uniqueId.".mp3";
		$this->previewFilename = "preview_".$this->uniqueId.".mp3";

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

	    $this->externalId = PROVIDER_CODE."-".DRUTT_FULLTRACK."-".$this->dataFormat($this->uniqueId, 5, "0");

	    $sql = "INSERT INTO admins.ancel_drutt SET time=CURTIME(), date=CURDATE(), type='$this->contentType', external_id='$this->externalId' ";
	    $this->addLog("SQL: ".$sql);
	    $rs = mysql_query($sql, $this->dbc->db);
	    if (!$rs) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: no se puede insertar nuevo id procesado");
      }



}




?>