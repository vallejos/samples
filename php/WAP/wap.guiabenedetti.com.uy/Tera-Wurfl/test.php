<?php
// Include the Tera-WURFL file
require_once('./TeraWurfl.php');
 
// instantiate the Tera-WURFL object
$wurflObj = new TeraWurfl();
 
// Get the capabilities of the current client.
$matched = $wurflObj->getDeviceCapabilitiesFromAgent();
 
// see if this client is on a wireless device (or if they can't be identified)
if(!$wurflObj->getDeviceCapability("is_wireless_device")){
	echo "<h2>You should not be here</h2>";
}
 
// see what this device's preferred markup language is
echo "Markup: ".$wurflObj->getDeviceCapability("preferred_markup");
 
// see the display resolution
$width = $wurflObj->getDeviceCapability("resolution_width");
$height = $wurflObj->getDeviceCapability("resolution_height");
echo "<br/>Resolution: $width x $height<br/>";
?>
