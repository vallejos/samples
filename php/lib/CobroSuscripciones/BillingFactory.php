<?php
include_once(dirname(__FILE__)."/MAS_GW_Billing.php");
include_once(dirname(__FILE__)."/ContentGWBilling.php");

// campo carrier (suscriptions) + campo tipo (cobros) => nombre clase
$CLASS_MAPPING = array(
    "claro_pe_wazzup_2_masgw" => "MAS_GW_Billing", //Por ahora la unica
    "claro_pe_wazzup_2_cntgw" => "ContentGWBilling" //Por ahora la unica

);

/**
 * Description of BillingFactory
 *
 */
class BillingFactory {


    function getBilling($tipo, $operadora) {
        global $CLASS_MAPPING;
        if(array_key_exists($operadora."_".$tipo, $CLASS_MAPPING)) {
            $nombreClase = $CLASS_MAPPING[$operadora."_".$tipo];
            return new $nombreClase();
        } else {
            return null;
        }
    }




}
?>
