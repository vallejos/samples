<?php
include_once($_SERVER['DOCUMENT_ROOT']."/lib/PrecioContenidos/BillingContenidoFunctions.php");
include_once($_SERVER['DOCUMENT_ROOT']."/lib/PrecioContenidos/BillingContenido.php");
include_once(dirname(__FILE__)."/BillingFactory.php");

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProcesoCobro
 *
 * @author fernando
 */
class ProcesoCobro {
    private $id;
    private $operadora;
    private $suscriptionId;
    private $cantidadIntentos;
    private $tipo;
    private $dias_reintentos; //Cantidad de días durante los que se intentara cobrar si el billing sigue fallando
    private $db;

    private $logger;

    function __construct($id, $carrier, $suscriptionId, $cantidad_intentos, $tipo, $dias_reintentos, $db) {
        $this->id = $id;
        $this->operadora = $carrier;
        $this->suscriptionId = $suscriptionId;
        $this->cantidadIntentos = $cantidad_intentos;
        $this->tipo = $tipo;
        $this->dias_reintentos = $dias_reintentos;
        $this->db = $db;

       $this->logger = new PHPLogger("/var/www/tmp/cobro_suscripciones/proceso_cobro.log");
    }


    public function getId() {
        return $this->id;
    }

    public function getOperadora() {
        return $this->operadora;
    }

    

      /*
         *
         1. Cargar mensajes a cobrar
         2. obtener el BC para cada caso
         *  (usar checkBillingHistory si $this->tieneReintentos == TRUE)
         3. Cobrar cada caso con el BC (usar charge())
    */
    public function chargeAll() {
        $this->logger->debug(" == chargeAll ==");
        $this->logger->info(" == chargeAll ==");
      
       $sql = "SELECT m.id, m.mobile
                FROM suscriptions.messages m inner join suscriptions.suscription_entries se on m.idEntries = se.id
                inner join suscriptions.suscriptions s on s.id=  se.suscriptionId
               WHERE m.suscriptionId = ".$this->suscriptionId."
               AND billed = 0
               AND se.active=1  AND
              (
              (m.billing_status='PENDING' AND (DATE_FORMAT(CONCAT(m.insertDate,' ',m.insertTime), '%Y-%m-%d %H:%i:%s') >= DATE_SUB(DATE_FORMAT(CONCAT(CURDATE(),' ',CURTIME()), '%Y-%m-%d %H:%i:%s'), INTERVAL ".$this->dias_reintentos." DAY)))
                OR 
              (m.billing_status='ERROR' AND (DATE_FORMAT(CONCAT(m.insertDate,' ',m.insertTime), '%Y-%m-%d %H:%i:%s') >= DATE_SUB(DATE_FORMAT(CONCAT(CURDATE(),' ',CURTIME()), '%Y-%m-%d %H:%i:%s'), INTERVAL ".$this->dias_reintentos." DAY))
              )) ";

       $this->logger->debug("SQL Generado: " . $sql);
       $rs = mysql_query($sql, $this->db);
       if($rs) {         
           while($row = mysql_fetch_assoc($rs)) {
               $celular = $row['mobile'];
               $mId = $row['id'];
               $nro_intento = 1;
               if($this->cantidadIntentos > 1) {
                   $reintentos = $this->checkBillingHistory($mId, $celular);
                   if($reintentos >=$this->cantidadIntentos) {
                       $nro_intento = $this->cantidadIntentos;
                   } else {
                       $nro_intento = $reintentos + 1;
                   }
               }
  //             $this->logger->debug(" Nro intentos: " . $nro_intento);
               $billingCode = $this->getBillingCode($mId, $nro_intento);
//               $this->logger->debug(" Billing Code: " . print_r($billingCode, true));
               if($billingCode != null) {
                $this->charge($mId, $celular, $billingCode);
               } else {
                   $this->logger->error("No se encontro un billing code adecuado");
               }
           }
       } else {
           $this->logger->error(" == ERROR Cargando mensajes a cobrar: " . mysql_error()." :: $sql");
       }
    
     //  $this->logger->saveDebug();
       //$this->logger->saveError();

    }

    /**
     * Devuelve la cantidad de reintentos para el dia actual
     * que se realizaron para el usuario $celular
     *
     * @param <type> $celular
     */
    private function checkBillingHistory($messageid, $celular) {

        $this->logger->debug(" == checkBillingHistory ==");
        $this->logger->info(" == checkBillingHistory ==");
        $sql = "SELECT COUNT(*) as intentos
               FROM suscriptions.billing_history
               WHERE messagesId = ". $messageid . "
               AND mobile = '$celular'
               AND date = CURDATE() ";

        $this->logger->debug("SQL Generado: ". $sql);
        $rs = mysql_query($sql, $this->db);
        if($rs) {
            $row = mysql_fetch_assoc($rs);
            return $row['intentos'];
        } else {
           $this->logger->error(" == ERROR chequeando billing history: " . mysql_error()." :: $sql");
        }
        return 0;

    }

    private function getBillingCode($mid,$reintento) {
        $this->logger->debug(" == getBillingCode ==");
        $this->logger->info(" == getBillingCode ==");
        $id_int = obtenerIDInteraccionSus($this->db, $this->suscriptionId);
        $this->logger->debug(" id interaccion: " . $id_int);
        $bc = new BillingContenido($this->db, 0, $id_int);
        $bc->ProcesarSuscripcion($reintento);

        return $bc->ObtenerBillingCode();
    }
 

    /**
     * Obtiene el cobro correcto e intenta descontar saldo del $celular
     *
     * @param <type> $messageId
     * @param <type> $celular
     * @param <type> $billing_code
     */
    private function charge($messageId, $celular, $billing_code) {
        $this->logger->debug(" == charge == ");
        $this->logger->info(" == charge == ");
        $bf = new BillingFactory();
        $billingObj = $bf->getBilling($this->tipo, $this->operadora);
        if($billingObj != null) {
            $this->logger->debug("Billing encontrado (woohoo!)");
            if( ($billingID = $billingObj->Bill($this->db, $celular, $billing_code, $this->suscriptionId,$messageId))) {
                $this->logger->debug("Woohooo! Proceso realizado correectamente, billingID: " . $billingID);

                $sql = "INSERT DELAYED INTO suscriptions.billing_history
                    (messagesId,mobile,date, time, status, billingId)
                    VALUES ('$messageId','$celular', curdate(), curtime(), 'PENDING', $billingID)";

                $this->logger->debug("SQL para billing history: $sql");
                $rs_insert = mysql_query($sql, $this->db);
                if($rs_insert) {
                    if($billingObj->UpdateBillingHistory()) {
                        $this->logger->debug("Datos guardados en billing history");
                        if($billingObj->UpdateMessages($messageId, $billingObj->getStatus())) {
                            $this->logger->debug("Datos de Messages actualizados...");
                        } else {
                            $this->logger->error("Problemas actualizando messages");
                        }
                    } else {
                        $this->logger->error("Hubo un error actualizando billing history");
                    }
                } else {
                    $this->logger->error("Error insertando datos en billing_history: " . mysql_error()."::$sql");
                }

            } else {
                $this->logger->debug("Hubo un problema al cobrar");
            }



        } else {
            $this->logger->error(" Error al obtener el billing: tipo: " . $this->tipo . " - operadora: " . $this->operadora);
        }
    }


}
?>
