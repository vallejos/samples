<?php

class druttVideo {
	// estos cambian
	private $externalId;
	private $uniqueId;
	private $service = ""; // obtener de tpim
	private $key;
	private $name;
	private $title;
	private $tag;
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
	private $logFile;

	// mandatory fields
//      private $mandatoryXMLFields = array("externalId", "deployed", "promotiononly", "startDate", "validUntil", "service", "adult", "name", "title", "tag", "premiumResource");
	private $mandatoryXMLFields = array("externalId", "deployed", "promotiononly", "startDate", "validUntil", "adult", "title", "tag");





	// constructor
	function __construct($dbc, $debug=FALSE) {
                global $logDir;
                $this->logFile = $logDir."/video.class.log";
		$this->addLog("************************************ C O M E N Z A N D O *****************************************************************");
		global $servicesDrutt, $prtdrutt;
		$this->service = $servicesDrutt["video"];
		$this->premiumResource = $prtdrutt;


		if ($dbc === NULL) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: DB Null");
		else $this->dbc = $dbc;

		$this->debug_mode = $debug;

		$this->deployed = "false";
		$this->promotiononly = "false";
		$this->promotionkey = "";
		$this->startDate = "2008-12-01T00:00:00.000";
		$this->validUntil = "2099-12-31T00:00:00.000";
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

		$sql = "SELECT c.*, cc.descripcion nombreCat, cc.xxx FROM Web.contenidos c INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria) WHERE c.id=$this->contentId  AND c.tipo='".WAZZUP_VIDEO."'" ;
		$this->addLog("SQL: ".$sql);
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) throw new Exception("ERROR: no se pueden obtener datos del contenido $this->contentId ");
		else $obj = mysql_fetch_object($rs);

		$this->adult = ($obj->xxx == "0") ? "false" : "true";

		$this->contentType = $obj->tipo;

		// genero external Id
		$this->setExternalId();

		// genero nombres unicos para contenidos y preview
		$this->setFilenames();

		$this->title = konvert("$obj->nombre");
		$this->description = konvert(getContentDescription($this->contentId, $this->contentType, $obj->categoria));

		$this->archivo = str_replace("netuy", "contenido", $obj->archivo);
//		$this->archivo = str_replace("videos", "videos3gh263", $this->archivo);
$this->archivo = str_replace("videos", "videos/drutt", $this->archivo);
                
//		$this->preview = $obj->referencia;
                $this->preview = str_replace("/netuy", "/previews", $obj->referencia); // nuevo path migracion previews a 241

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

		$xml = '<video externalId="'.$this->externalId.'">'."\n";
		$xml .= '<deployed>'.$this->deployed.'</deployed>'."\n";
		$xml .= '<promotiononly>'.$this->promotiononly.'</promotiononly>'."\n";
		$xml .= '<promotionkey>'.$this->promotionkey.'</promotionkey>'."\n";
		$xml .= '<startDate>'.$this->startDate.'</startDate>'."\n";
		$xml .= '<validUntil>'.$this->validUntil.'</validUntil>'."\n";
		$xml .= '<service>'.$this->service.'</service>'."\n";
		$xml .= '<adult>'.$this->adult.'</adult>'."\n";
		$xml .= '<key>'.$this->key.'</key>'."\n";
		$xml .= '<title>'.$this->title.'</title>'."\n";
		$xml .= '<tag>'.$this->tag.'</tag>'."\n";
		$xml .= '<description>'.$this->description.'</description>'."\n";
		$xml .= '<screenshot>'."\n";
		$xml .= '<variant>'."\n";
		$xml .= '<item uri="'.$this->preview_uri.'" />'."\n";
		$xml .= '</variant>'."\n";
		$xml .= '</screenshot>'."\n";
		$xml .= '<premium3gpp premiumResource="'.$this->premiumResource.'">'."\n";
		$xml .= '<variant>'."\n";
		$xml .= '<item uri="'.$this->variantUri.'" />'."\n";
		$xml .= '</variant>'."\n";
		$xml .= '</premium3gpp>'."\n";
		$xml .= '</video>'."\n";

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
		$this->contentFilename = "content_".$this->uniqueId.".3gp";
		$this->previewFilename = "preview_".$this->uniqueId.".gif";

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

	    $this->externalId = PROVIDER_CODE."-".DRUTT_VIDEO."-".$this->dataFormat($this->uniqueId, 5, "0");

	    $sql = "INSERT INTO admins.ancel_drutt SET time=CURTIME(), date=CURDATE(), type='$this->contentType', external_id='$this->externalId' ";
	    $this->addLog("SQL: ".$sql);
	    $rs = mysql_query($sql, $this->dbc->db);
	    if (!$rs) throw new Exception("<".__LINE__."> ".__METHOD__."ERROR: no se puede insertar nuevo id procesado");
      }



}




?>