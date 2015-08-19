<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");

/**
 * Description of logger
 *
 * @author fernando
 */
class LoggerCobroBo {
    private $err;
    private $debug;

    private $file;


    public function  __construct($path) {
        $this->file = $path;
        $this->err = "";
        $this->debug = "";
    }

    public function debug($txt) {
        $this->debug .= "\n" . date("Y-m-d H:i:s") . "::" . $txt;
    }

    public function error($txt) {
        $this->err .= "\n" . date("Y-m-d H:i:s") . "::" . $txt;
    }

    public function showDebug() {
        return $this->debug;
    }
    public function showError() {
        return $this->err;
    }

    public function saveDebug() {
        $filename = explode(".", $this->file);
        $filename[count($filename) - 2] .= "_debug";
        $filename = implode(".", $filename);

        $fp = fopen($filename, "a");
        if($fp) {
            fwrite($fp, $this->debug);
            fclose($fp);
        }
    }


    public function saveError() {
        $filename = explode(".", $this->file);
        $filename[count($filename) - 2] .= "_error";
        $filename = implode(".", $filename);

        $fp = fopen($filename, "a");
        if($fp) {
            fwrite($fp, $this->err);
            fclose($fp);
        }
    }

}
?>
