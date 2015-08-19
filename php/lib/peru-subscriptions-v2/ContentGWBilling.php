<?php
//include_once($_SERVER['DOCUMENT_ROOT']."/lib/mas_gw/mas_gw.class.php");
include_once(dirname(__FILE__)."/claro_pe/ContentGatewayBilling.php");
include_once(dirname(__FILE__)."/Billing.php");
include_once(dirname(__FILE__)."/logger.php");




/**
 * Description of ContentGWBilling
 *
 * @author fernando
 */
class ContentGWBilling extends Billing {

    var $status;
    var $id_transaccion;
    var $db;
    var $logger;


    public function Bill($db, $celular, $billing_code, $suscriptionId, $messageId) {
        $cobro = new ContentGatewayBilling($db, $celular, $billing_code->obtenerIdTarifa());
        $this->db = $db;
        $this->logger = new PHPLogger("/var/www/tmp/cobro_suscripciones/contentgwbilling.log");
        if($cobro->process()) {
            $this->status = 'OK';
        } else {
            $this->status = 'ERROR';
        }
        $this->id_transaccion = $cobro->idTransaccion;
            //Actualizo el billing History

        return $this->id_transaccion;
         //$sms = new MAS_GW ($db, $billing_code->obtenerShortcode(), $celular, "suscription", $billing_code->obtenerIdTarifa());
         //$enviado = $sms->process($messageId);
         //sleep(PAUSE_TIME);
         //return $sms->sms_id;
    }

    public function getStatus() {
        return $this->status;
    }

    public function UpdateMessages($mId, $status) {
        $sql = "UPDATE suscriptions.messages SET billing_status = '$status'
                WHERE id = $mId";
        $rs = mysql_query($sql, $this->db);
        if($rs) {
            return true;
        } else {
            $this->logger->error("Error MYSQL actualizando messages: " . mysql_error());
        }
        return false;
    }

    public function UpdateBillingHistory(){

        $sql = " UPDATE suscriptions.billing_history set status = '".$this->status."' WHERE billingId = '".$this->id_transaccion."'";
        $rs = mysql_query($sql, $this->db);
        if($rs) {
            return true;
        } else {
            $this->logger->error("Error MYSQL actualizando billing history: " . mysql_error());
            return false;
        }

    }

}
?>
