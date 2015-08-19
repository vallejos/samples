<?php
if(!class_exists("Logger")) {
    include_once(dirname(__FILE__)."/logger.php");
}
include_once(dirname(__FILE__)."/constantes.php");
include_once(dirname(__FILE__)."/ProcesoCobro.php");
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


class ManejadorProcesos {

    private $procesos;
    private $db;

    private $logger;


    function __construct($db) {

        $this->db = $db;
        $this->logger = new PHPLogger(LOG_FILE);
        $this->procesos = array();
        $this->logger->debug("== Iniciando proceso principal == ");
    }


    /**
     * Carga los procesos de cobro, dependiendo de la hora
     *
     */
    public function loadChargeProcesses() {
        $this->logger->debug("== En loadchargeProcesses == ");

        $sql = "SELECT cs.id, carrier, suscriptionId, cantidad_intentos, tipo, dias_reintentos
                FROM suscriptions.cobros_suscripciones cs INNER JOIN suscriptions.horarios_cobros hc ON cs.id = hc.id_cobro_suscripcion
                inner join suscriptions.suscriptions s on s.id = cs.suscriptionId
                WHERE hour(hora) = hour(curtime())";
        //WHERE concat(hour(hora), ':', minute(hora)) = concat(hour(curtime()),':',minute(curtime()))";

        $this->logger->debug("SQL generado: " . $sql);
        $rs = mysql_query($sql, $this->db);
        if($rs) {
               while($row = mysql_fetch_assoc($rs)) {
                   $proceso = new ProcesoCobro($row['id'], $row['carrier'], $row['suscriptionId'],
                                               $row['cantidad_intentos'], $row['tipo'],
                                               $row['dias_reintentos'],
                                               $this->db);
                   $this->procesos[] = $proceso;
               }
        } else {
            $this->logger->error("SQL ERROR: " . mysql_error() ." :: $sql ");
        }

        $this->logger->debug("== Cantidad de procesos cargados: " . count($this->procesos));
        $this->logger->info("== Cantidad de procesos de cobro cargados: " . count($this->procesos));
    }

    /**
     * Ejecuta el charge de todos los proceso cargados
     */
    public function billAll() {
        $this->logger->debug(" == billAll == ");
        if(count($this->procesos) > 0) {
            foreach($this->procesos as $proc) {
                $this->logger->debug("Procesando bill job - Id:" . $proc->getId() . ", operadora: " . $proc->getOperadora());
                $this->logger->info("Procesando bill job - Id:" . $proc->getId() . ", operadora: " . $proc->getOperadora());
                $proc->chargeAll();
            }
            $this->logger->debug(" == Proceso de cobro finalizado == ");
            $this->logger->saveDebug();
            $this->logger->saveError();
            return true;
        } else {
            $this->logger->debug(" == No hay procesos de cobro para correr == ");
            $this->logger->saveDebug();
            $this->logger->saveError();
            return false;
        }
    }

}
?>
