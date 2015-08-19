<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of logger
 *
 * @author fernando
 */
class PHPLogger {
    private $err;
    private $debug;


    private $file;


    public function  __construct($path) {
        $this->file = $path;
        $this->err = "";
        $this->debug = "";
    }

    public function debug($txt) {
      //  $this->debug = "\n" . date("Y-m-d H:i:s") . "::" . $txt;
       // $this->saveDebug();
    }

    public function error($txt) {
        $this->err = "\n" . date("Y-m-d H:i:s") . "::" . $txt;
        $this->saveError();
    }


    public function info($txt) {
/*        $filename = explode(".", $this->file);
        $filename[count($filename) - 2] .= "_info";
        $filename = implode(".", $filename);

        $fp = fopen($filename, "a");
        if($fp) {
            fwrite($fp, "\n" . date("Y-m-d H:i:s") . "::" . $txt);
            fclose($fp);
        } */
    }

    public function showDebug() {
        return $this->debug;
    }
    public function showError() {
        return $this->err;
    }

    public function saveDebug() {
      /*  $filename = explode(".", $this->file);
        $filename[count($filename) - 2] .= "_debug";
        $filename = implode(".", $filename);

        $fp = fopen($filename, "a");
        if($fp) {
            fwrite($fp, $this->debug);
            fclose($fp);
        } else {
		echo $this->debug;
	}*/
    }


    public function saveError() {
        $to = "fernando.doglio@globalnetmobile.com";
        $from = "fernando.doglio@globalnetmobile.com";
        $headers = "From: $from\r\n";      
        mail($to, "ERROR COBRO SUSCRIPCIONES", $this->err, $headers);
        
        /*    $filename = explode(".", $this->file);
        $filename[count($filename) - 2] .= "_error";
        $filename = implode(".", $filename);

        $fp = @fopen($filename, "a");
        if($fp) {
            fwrite($fp, $this->err);
            fclose($fp);
        } else {
		echo $this->err;
	}*/
    }

}
?>
