<?php

/**
 * Description of Track
 *
 * @author kAmuS
 */
class Track {
    private $contractId;
    private $id;
    private $matches;
    private $trackId;
    private $title;
    private $artistName;
    private $tariffClass;
    private $bundleOrderId;
    private $orderId;
    private $isrc;
    private $icpn;
    private $trackNumber;
    private $volume;
    private $length;
    private $licenseProviderId;
    private $selOnlyInBundle;

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

    public function getTrackId() {
	return $this->trackId;
    }

    public function setTrackId($trackId) {
	$this->trackId = $trackId;
    }

    public function getTitle() {
	return $this->title;
    }

    public function setTitle($title) {
	$this->title = $title;
    }

    public function getArtistName() {
	return $this->artistName;
    }

    public function setArtistName($artistName) {
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

    public function getIsrc() {
	return $this->isrc;
    }

    public function setIsrc($isrc) {
	$this->isrc = $isrc;
    }

    public function getIcpn() {
	return $this->icpn;
    }

    public function setIcpn($icpn) {
	$this->icpn = $icpn;
    }

    public function getTrackNumber() {
	return $this->trackNumber;
    }

    public function setTrackNumber($trackNumber) {
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

    public function getSellOnlyInBundle() {
	return $this->selOnlyInBundle;
    }

    public function setSellOnlyInBundle($selOnlyInBundle) {
	$this->selOnlyInBundle = $selOnlyInBundle;
    }

   public function __destruct() {
       
/*      
	 foreach ( array_keys ( get_object_vars ( &$this ) ) as $val)
          {    unset( $this->$val );    }
*/
	  }

    // devuelve la cantidad de matches encontrados tras un load
    public function getMaches() {
	return $this->matches;
   }
    
    public function loadFromIcpn($dbc, $icpn) {
	$this->matches = 0;
	$result = array();
	$sql = "SELECT * FROM ".DB_NAME.".tracks WHERE upc='$icpn' ";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) { 
	    // error mysql
	    return FALSE;
	} else { 
	    while ($obj = mysql_fetch_object($rs)) {
		$this->setId($obj->id);
		$this->setTitle($obj->track_title);
		$this->setIcpn($obj->upc);
		$this->setIsrc($obj->isrc);
		$this->setTrackNumber($obj->track_number);
		$this->setVolume($obj->volumen);
		$this->setOrderId($obj->order_id);
		$this->setLicenseProviderId($obj->license_provider_id);
		$this->setTariffClass($obj->tariff_class);
		$this->setSellOnlyInBundle($obj->sell_only_on_bundle);
		$this->setBundleOrderId($obj->bundle_order_id);		
		$this->matches++;
	    }
	}
	return TRUE;
    }
	  


    
/*			
<item orderId="6388044" title="Sä olit oikees" artist="Mikael Gabriel" tariffClass="1870" contentTypeKey="FULLTRACK" bundleOrderId="6388043" isrc="FIUM71001605" icpn="0602527529042" track="1" volume="1" 
 * length="240" licenseProviderId="2131264" providerId="30000" sellOnlyInBundle="false">
<imageUrl>/ms/pub/media/meft128/6388043.jpg</imageUrl>
<mobileDeviceReferences/>
</item>
 */

    // carga los datos del objeto en base al xml (string)
    public function setFromXML($xmlData) { 
	foreach ($xmlData->attributes as $attrName => $attrValue ) {
	    if ($attrName == "orderId") $this->setOrderId($attrValue->nodeValue); // 
	    if ($attrName == "title") $this->setTitle($attrValue->nodeValue); // 
	    if ($attrName == "artist") $this->setArtistName($attrValue->nodeValue); // 
	    if ($attrName == "tariffClass") $this->setTariffClass($attrValue->nodeValue); // 
	    if ($attrName == "bundleOrderId") $this->setBundleOrderId($attrValue->nodeValue); // 
	    if ($attrName == "isrc") $this->setIsrc($attrValue->nodeValue); // 
	    if ($attrName == "icpn") $this->setIcpn($attrValue->nodeValue); // 
	    if ($attrName == "track") $this->setTrackNumber($attrValue->nodeValue); // 
	    if ($attrName == "volume") $this->setVolume($attrValue->nodeValue); // 
	    if ($attrName == "length") $this->setLength($attrValue->nodeValue); // 
	    if ($attrName == "licenseProviderId") $this->setLicenseProviderId($attrValue->nodeValue); // 
	    if ($attrName == "sellOnlyInBundle") $this->setSellOnlyInBundle($attrValue->nodeValue); // 
	}
    }
    
    
    
    public function save($dbc, $dbTable, $useDelay=FALSE) {
	$sql = ($useDelay === TRUE) ? "INSERT DELAYED INTO " : "INSERT INTO ";
	$sql .= DB_NAME.".".$dbTable." SET ".
	    "track_title='".mysql_real_escape_string($this->getTitle())."', ".
	    "track_length='".$this->getIcpn()."', ".
	    "upc='".$this->getIcpn()."', ".
	    "isrc='".$this->getIsrc()."', ".
	    "track_number='".$this->getTrackNumber()."', ". 
	    "volumen='".$this->getVolume()."', ". 
	    "activo='1', ". // activo por defecto
	    "idsello='".ID_SELLO."', ". // id sello 1 para brasil
	    "order_id='".$this->getOrderId()."', ". 
	    "license_provider_id='".$this->getLicenseProviderId()."', ". 
	    "tariff_class='".$this->getTariffClass()."', ".
	    "sell_only_on_bundle='".$this->getSellOnlyInBundle()."', ".
	    "bundle_order_id='".$this->getBundleOrderId()."' ";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) { 
	    // error mysql
	    return FALSE;
	} else {
	    $this->setId(mysql_insert_id());
	    return TRUE;
	}
    }
	  
	  

}
?>
