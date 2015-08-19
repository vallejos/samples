<?php

/**
 * Simple logger Klass!! ;)
 * v1.0
 */

class klogger {
    private $mode;
    private $logFile;
    private $fp;
    private $log;
    private $timestamp;
    private $data;
    private $delimeter;
    
    function __construct($logFile, $ts=FALSE) {
        $this->logFile = $logFile;
        $this->timestamp = $ts;
        $this->delimeter = "\n";
        $this->data[] = "\n\n";
    }
    
    function __destruct() {
        
    }
    
    public function setDelimiter($delimeter) {
        $this->delimeter = $delimeter;        
    }
    
    private function getDelimeter() {
        return $this->delimeter;
    }

    private function getTimestamp() {
        return $this->timestamp;
    }

    public function add($msg) {
        $ts = date("Y/m/d H:i:s");
        if ($this->getTimestamp() == TRUE) $data = $ts." ".$msg;
        else $data = $msg;
        $this->data[] = $data;
    }
    
    public function display() {
        foreach ($this->data as $data) {
            echo "$data".$this->getDelimeter;
        }
    }
    
    public function save($flush=FALSE) {
        $this->fp = fopen($this->logFile, "a+");
        if (!$this->fp) die ("cannot create log file");
        foreach ($this->data as $ln => $ld) {
            $data = $ld.$this->getDelimeter();
            fwrite($this->fp, $data);            
        }
        fclose($this->fp);
        
        if ($flush == TRUE) unset($this->data);
    }
    
    
}





?>
