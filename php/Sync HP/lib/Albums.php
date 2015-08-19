<?php

/**
 * Description of Album
 *
 * @author kAmuS
 */
class Album {
    private $contractId;
    private $id;
    private $matches;
    private $albumId;
    private $artistId;
    private $title;
    private $artistName;
    private $tariffClass;
    private $bundleOrderId;
    private $orderId;
    private $icpn;
    private $trackNumber;
    private $volume;
    private $length;
    private $licenseProviderId;

    function __construct($type="brasil") {
	$this->matches = 0;
	if ($type == "brasil") $this->contractId = WS_CONTRACTID_BRASIL;
	else $this->contractId = WS_CONTRACTID_LA;
    }

    public function getId() {
	return $this->id;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function getAlbumId() {
	return $this->trackId;
    }

    public function setAlbumId($albumId) {
	$this->trackId = $albumId;
    }

    public function getArtistId() {
	return $this->artistId;
    }

    public function setArtistId($artistId) {
	$this->artistId = $artistId;
    }
    
    public function getTitle() {
	return $this->title;
    }

    public function setTitle($title) {
	$this->title = $title;
    }

    public function getArtist() {
	return $this->artistName;
    }

    public function setArtist($artistName) {
	$this->artistName = $artistName;
    }

    public function getTariffClass() {
	return $this->tariffClass;
    }

    public function setTariffClass($tariffClass) {
	$this->tariffClass = $tariffClass;
    }

    public function getBundleOrderId() {
	return $this->bundleOrderId;
    }

    public function setBundleOrderId($bundleOrderId) {
	    $this->bundleOrderId = $bundleOrderId;
    }

    public function getOrderId() {
	    return $this->orderId;
    }

    public function setOrderId($orderId) {
	    $this->orderId = $orderId;
    }

    public function getIcpn() {
	    return $this->icpn;
    }

    public function setIcpn($icpn) {
	    $this->icpn = $icpn;
    }

    // get number of tracks
    public function getTrack() {
	    return $this->trackNumber;
    }

    // sets number of tracks
    public function setTrack($trackNumber) {
	    $this->trackNumber = $trackNumber;
    }

    public function getVolume() {
	    return $this->volume;
    }

    public function setVolume($volume) {
	    $this->volume = $volume;
    }

    public function getLength() {
	    return $this->length;
    }

    public function setLength($length) {
	    $this->length = $length;
    }

    public function getLicenseProviderId() {
	    return $this->licenseProviderId;
    }

    public function setLicenseProviderId($licenseProviderId) {
	    $this->licenseProviderId = $licenseProviderId;
    }

    public function getSelOnlyInBundle() {
	    return $this->selOnlyInBundle;
    }

    public function setSelOnlyInBundle($selOnlyInBundle) {
	    $this->selOnlyInBundle = $selOnlyInBundle;
    }

    // devuelve la cantidad de matches encontrados tras un load
    public function getMaches() {
	return $this->matches;
   }
    
    public function loadFromIcpn($dbc, $icpn) {
	$this->matches = 0;
	$result = array();
	$sql = "SELECT a.*, ar.nombre nombre_artista FROM ".DB_NAME.".albums a 
		LEFT JOIN ".DB_NAME.".artistas ar ON (ar.id=a.idartista) 
		WHERE a.upc='$icpn' ";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) { 
	    // error mysql
	    return FALSE;
	} else { 
	    while ($obj = mysql_fetch_object($rs)) {
		$this->setId($obj->id);
		$this->setTitle($obj->prd_title);
		$this->setArtist($obj->nombre_artista);
		$this->setIcpn($obj->upc);
		$this->setLength($obj->prd_length);
		$this->setOrderId($obj->bundle_id);
		$this->matches++;
	    }
	}
	return TRUE;
    }
 
    
    /*			
    <items>
    <searchResult count="1"/>
    <item orderId="6380525" title="Tropa De Elite" artist="Tihuana" tariffClass="1879" contentTypeKey="FT_BUNDLE" bundleOrderId="0" icpn="0602527560380" track="0" volume="0" length="311" licenseProviderId="2131264" 
     * providerId="30000">
    <imageUrl>/ms/pub/media/meft128/6380525.jpg</imageUrl>
    <mobileDeviceReferences/>
    </item>
    </items>
     */
    public function setFromXML($xmlData) { 
	// obtengo valores de los atributos que necesito
	foreach ($xmlData->attributes as $attrName => $attrValue ) {
	    if ($attrName == "orderId") $this->setOrderId($attrValue->nodeValue); // 
	    if ($attrName == "title") $this->setTitle($attrValue->nodeValue); // 
	    if ($attrName == "artist") $this->setArtist($attrValue->nodeValue); // 
	    if ($attrName == "tariffClass") $this->setTariffClass($attrValue->nodeValue); // 
	    if ($attrName == "bundleOrderId") $this->setBundleOrderId($attrValue->nodeValue); // 
	    if ($attrName == "icpn") $this->setIcpn($attrValue->nodeValue); // 
	    if ($attrName == "track") $this->setTrack($attrValue->nodeValue); // 
	    if ($attrName == "volume") $this->setVolume($attrValue->nodeValue); // 
	    if ($attrName == "length") $this->setLength($attrValue->nodeValue); // 
	    if ($attrName == "licenseProviderId") $this->setLicenseProviderId($attrValue->nodeValue); // 
	}
    }    
    
    
    public function save($dbc, $dbTable, $useDelay=FALSE) {	
	$sql = ($useDelay === TRUE) ? "INSERT DELAYED INTO " : "INSERT INTO ";
	$sql .= DB_NAME.".".$dbTable." SET ".
	    "prd_title='".mysql_real_escape_string($this->getTitle())."', ".
	    "upc='".$this->getIcpn()."', ".
	    "prd_length='".$this->getLength()."', ".
	    "activo='1', ". // activo por defecto
	    "idsello='".ID_SELLO."', ". // id sello 1 para brasil
	    "bundle_id='".$this->getOrderId()."', ".
	    "idartista='".$this->getArtistId()."' ";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) {
	    // error mysql
	    return FALSE;
	} else {
	    $this->setId(mysql_insert_id());
	    return TRUE;
	}
    }
    

    public function assocGroup($dbc, $id) {
	$sql = "INSERT INTO ".DB_NAME.".albums_generos SET ".
	    "idalbum='".$this->getId()."', ".
	    "idgenero='".$id."' ";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) { 
	    // error mysql
	    return FALSE;
	} else {
	    return TRUE;
	}
    }

    public function assocArtist($dbc, $id) {
	$sql = "INSERT INTO ".DB_NAME.".albums_artistas SET ".
	    "idalbum='".$this->getId()."', ".
	    "idartista='".$id."' ";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) { 
	    // error mysql
	    return FALSE;
	} else {
	    return TRUE;
	}
    }
    
    public function assocTrack($dbc, $id) {
	$sql = "INSERT INTO ".DB_NAME.".albums_temas SET ".
	    "idalbum='".$this->getId()."', ".
	    "idtema='".$id."' ";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) {
	    // error mysql
	    return FALSE;
	} else {
	    return TRUE;
	}
    }
    
    public function fetchXML($icpn, $oLog=NULL) {
	$log = $oLog;
	$finished = FALSE;
	$fetched = 0;
	$contractId = $this->contractId;

	while (!$finished) {
	    $count = FALSE;
	    $intentos = 0;

	    while ($count == FALSE && $intentos<3) {
		$intentos++;
		$url = "http://maxx.me.net-m.net/me/maxx/$contractId/items?contentTypeKey=FT_BUNDLE&icpn=$icpn";
		$log->add("reading $url (try #$intentos)");
		$xmlContents = file_get_contents($url);

		$doc = new DOMDocument();
		$doc->loadXML($xmlContents);

		$count = getTrackCountResult($doc);
		if ($count != FALSE) {
		    $log->add("got $count results");
		} else {
		    $log->add("error reading results");
		    avisoCel("could not read xml (albums)");
		    
		    // pausa de 3 secs (posible server restart o error en netm, esperamos)
		    sleep(3);
		}
	    }

	    if ($count === FALSE) {
		// si no se pudo leer resultados, y se superaron los reintentos, termino el proceso
		$finished = TRUE;
		$log->add("too many retries with error");

		// enviar aviso cel
		$log->add("sending sms notification");
		avisoCel("could not read xml (artists-albums)");
		$newXML = FALSE;
	    } else {
		// se obtuvo un nuevo resultado
		if ($count > 0) $fetched++;
		$newXML = TRUE;
	    }

	    // si no hay resultado de busqueda, termino el proceso
	    if ($count == 0) $finished = TRUE;

	    if ($newXML == TRUE) {
		// si se obtuvieron resultados y no se termino, guardo el xml y sigo buscando mas
		$xmlName = date("Ymd")."-albums_".$fetched."-".date("His").".xml";
		$fName = TMP_DIR."/".$xmlName;
		$log->add("saving xml content to $fName");
		$doc->save($fName);
	    }

	    $log->save(TRUE);

	    if ($newXML == TRUE) {
		$result = $doc->getElementsByTagName("item"); // cada item corresponde a un album (o bundle en lenguaje netm)

		if ($result->length > 0) {
		    $ok = 0;
		    $error= 0;
		    // si tengo albums para recorrer
		    foreach ($result as $itemNumber => $xmlTrack) {
			$xmlItem = $result->item($itemNumber);
			if ($xmlItem->hasAttributes()) {
			    $this->setFromXML($xmlItem);
			    $ok++;
			} else {
			    // no se pudo leer los datos del track
			    $log->add("ERROR: no se pudieron obtener datos del track en: $xmlName");
			    $err++;
			}
		    }

		    $log->add("finished processing $xmlName... total=$count, Ok=$ok, error=$error");
		    $finished = TRUE; // todo ok, salimos ;)
		} else {
		    // no hay items para procesar
		    $log->add("ERROR: no se encontraron items para procesar en: $xmlName");
		}

	    } else {
		// no hay mas resultados y se termino el proceso, no hay que guardar nada
		$log->add("finished getting tracks for icpn $icpn (".$icpnData->prd_title.")");
	    }

	}

//	$log->add("album fetched $fetched xml");
//	$log->save(TRUE);
    }
    
    
    

}

?>