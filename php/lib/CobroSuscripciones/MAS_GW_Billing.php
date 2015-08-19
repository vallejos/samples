<?php
include_once($_SERVER['DOCUMENT_ROOT']."/lib/mas_gw/mas_gw.class.php");
include_once(dirname(__FILE__)."/Billing.php");


define("PAUSE_TIME", 1);

/**
 * Description of MAS_GW_Billing
 *
 */
class MAS_GW_Billing extends Billing {


    public function Bill($db, $celular, $billing_code, $suscriptionId, $messageId) {
         $sms = new MAS_GW ($db, $billing_code->obtenerShortcode(), $celular, "suscription", $billing_code->obtenerIdTarifa());
         $enviado = $sms->process($messageId);
         sleep(PAUSE_TIME);
         return $sms->sms_id;
    }

}
?>
