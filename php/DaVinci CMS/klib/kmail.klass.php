<?php

/**
 * kmail - a kmS aproximation to mail :P
 * v1.3
 *
 * HISTORY
 * ----------
 * v1.3: added real time log file tracking
 * v1.2.1: fixed minor bug in ::send()
 * v1.2: HTML mode
 * v1.1: CC enable
 * v1.0: RC
 */



class kmail {
	var $msg;
	var $to;
	var $subject;
	var $from = "leonardo.hernandez@globalnetmobile.com";
	var $cc;
	var $bcc;
	var $headers;
	var $LOG_DIR = "/tmp/kmail_logs";
	var $realtime_tracking;
	var $logfile;

	function kmail($subject, $to="alertas.debug@globalnetmobile.com") {
		$this->headers = "From: {$this->from}\r\n".
			"Reply-To: {$this->from}\r\n".
			"Return-Path: {$this->from}\r\n".
			"%CC%".
			"Content-type: text/html\r\n".
			"X-Mailer: KMAIL/1.3";

		$this->to = $to;
		$this->subject = $subject;
		$this->realtime_tracking = FALSE;
		$this->logfile = "";
	}

	function set_tracking() {
		$this->realtime_tracking = TRUE;
	}

	function set_logfile($logfile) {
		$this->logfile = $logfile;
	}

	function add_cc($ato) {
		foreach($ato as $m) {
			$to .= "$m, ";
		}
		$this->headers = str_replace("%CC%", "CC:$to\r\n", $this->headers);
	}


	function add($msg) {
		$this->msg = $this->msg."<br/><br/>[".date("Y-m-d H:i:s")."]<br/>".$msg;

		if ($this->realtime_tracking === TRUE) {
			$tmsg = str_replace("<br/>", "\n", $msg);
			$file = (empty($this->logfile)) ? $this->LOG_DIR."/".$filename.date("m_d").".txt" : $this->logfile.date("m_d").".txt";
			$fp = @fopen($file, "a+");
//			$tmsg = str_replace("<br/>", "\n", $this->msg);
			if ($fp) {
				fwrite($fp, "\n".date("Y-m-d::H:i:s").":::".$tmsg);
				fclose($fp);
			}
		}
	}


	function send($msg="") {
		$this->msg = $this->msg."<br/><br/>[".date("Y-m-d H:i:s")."]<br/>".$msg;
		$this->headers = str_replace("%CC%", "", $this->headers);
//		mail($this->to, $this->subject ,html_entity_decode(utf8_decode($this->msg)), $this->headers);
		mail($this->to, $this->subject ,$this->msg, $this->headers);

		if ($this->realtime_tracking === TRUE) {
			$tmsg = str_replace("<br/>", "\n", $msg);
			$file = (empty($this->logfile)) ? $this->LOG_DIR."/".$filename.date("m_d").".txt" : $this->logfile.date("m_d").".txt";
			$fp = @fopen($file, "a+");
			if ($fp) {
				fwrite($fp, "\n".date("Y-m-d::H:i:s").":::".html_entity_decode(utf8_decode($tmsg)));
				fclose($fp);
			}
		}

	}


	function tofile($msg="", $filename="") {
		$this->msg = $this->msg."<br/><br/>[".date("Y-m-d H:i:s")."]<br/>".$msg;
		$this->headers = str_replace("%CC%", "", $this->headers);

		$fp = @fopen($this->LOG_DIR."/".$filename.date("m_d").".txt", "a+");
		$tmsg = str_replace("<br/>", "\n", $this->msg);
		if ($fp) {
			fwrite($fp, "\n".date("Y-m-d::H:i:s").":::".html_entity_decode(utf8_decode($tmsg)));
			fclose($fp);
		}

	}


}



?>