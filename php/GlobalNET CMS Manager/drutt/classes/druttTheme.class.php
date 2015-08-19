<?php

class druttWallpaper {
      // estos cambian
      private $externalId;
      private $service;
      private $key;
      private $name;
      private $title;
      private $tag;
      private $description;
      private $premiumResource;
      private $preview_uri;

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

      // constructor
      function __construct($db, $debug=FALSE) {
            global $logDir;
            $this->logFile = $logDir."/theme.class.log";
	    if ($db === NULL) throw new Exception("ERROR: DB Null");
	    $this->deployed = "false";
	    $this->promotiononly = "false";
	    $this->promotionkey = "";
	    $this->startDate = "2008-12-01T00:00:00.000";
	    $this->validUntil = "2018-12-31T00:00:00.000";
	    $this->adult = "false";
      }

      // destructor
      function __destruct() {
	    $this->addLog("[".__LINE__."]".__METHOD__);
	    if ($this->debug_mode === TRUE) {
		  $this->logDebug();
	    } else {
//		  echo "no+ log";
	    }
      }

      // carga los datos del contenido
      public load($content_id) {
	    if (!is_numeric($content_id)) throw new Exception("ERROR: content_id no valido");
	    else $this->contentId = $content_id;
      }

      // log
      private function addLog($msg) {
	    $this->logmsg .= $msg."\n";
      }

      // blah blah
      private function logDebug() {
	    echo $this->logmsg;
      }


      private setExternalId() {
	    $this->externalId = PROVIDER_CODE."-".DRUTT_WALLPAPER."-".$uniqueId;
      }


      public genXML() {
	    $this->addLog("Generando XML para $this->contentId");

	    if (!is_numeric($this->contentId)) throw new Exception("ERROR: content_id no valido");

	    $xml = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>'."\n";
	    $xml .= '<asset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\n";
	    $xml .= '<wallpaper externalId="'.$this->externalId.'">'."\n";
	    $xml .= '<deployed>'.$this->deployed.'</deployed>'."\n";
	    $xml .= '<promotiononly>'.$this->promotiononly.'</promotiononly>'."\n";
	    $xml .= '<promotionkey>'.$this->promotionkey.'</promotionkey>'."\n";
	    $xml .= '<startDate>'.$this->startDate.'</startDate>'."\n";
	    $xml .= '<validUntil>'.$this->validUntil.'</validUntil>'."\n";
	    $xml .= '<service>'.$this->service.'</service>'."\n";
	    $xml .= '<adult>'.$this->adult.'</adult>'."\n";
	    $xml .= '<key>'.$this->key.'</key>'."\n";
	    $xml .= '<name>'.$this->name.'</name>'."\n";
	    $xml .= '<title>'.$this->title.'</title>'."\n";
	    $xml .= '<tag>'.$this->tag.'</tag>'."\n";
	    $xml .= '<description>'.$this->description.'</description>'."\n";
	    $xml .= '<imagepreview>'."\n";
	    $xml .= '<variant>'."\n";
	    $xml .= '<item uri="'.$this->preview_uri.'" />'."\n";
	    $xml .= '</variant>'."\n";
	    $xml .= '</imagepreview>'."\n";
	    $xml .= '<image premiumResource="'.$this->premiumResource.'">'."\n";
	    // aca va la parte de 1 imagen o 2
	    $xml .= '</image>'."\n";
	    $xml .= '</wallpaper>'."\n";
	    $xml .= '</asset>'."\n";

	    $this->addLog("XML Generado:\n".$xml);
      }

}




?>