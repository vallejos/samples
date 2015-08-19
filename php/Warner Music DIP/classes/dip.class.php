<?php


class DIP {
	private $dbc;
	private $from;
	private $to;
	private $header;
	private $detail;

	private $dateReport;
	private $reportId;

	// default constructor
	function __construct($dbc) {
		$this->dbc = $dbc;
		$this->dateReport = date("Y-m-d");
		$this->reportId = $this->store();
	}


	// default destructor
	function __destruct() {

	}


	public function addHeader($header) {
		$this->header = $header;
	}


	public function addDetail($detail) {
		$this->detail .= $detail;
	}

	public function getReportId() {
		return $this->reportId;
	}


	// guarda registro en DB, devuelve id unico del reporte
	private function store() {
		$sql = "INSERT INTO admins.warner_report SET dateReport='$this->dateReport' ";
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) {
			// error
			return FALSE;
		}
		return mysql_insert_id($this->dbc->db);
	}


	public function toFile($file) {
		if (!$fp = fopen($file, "a+")) {
			echo "Cannot open file ($file)";
			return FALSE;
		}
		if (fwrite($fp, $this->header) === FALSE) {
			echo "Cannot write Headers to file ($file)";
			return FALSE;
		}
		if (fwrite($fp, $this->detail) === FALSE) {
			echo "Cannot write Deatils to file ($file)";
			return FALSE;
		}
		fclose($fp);
		return TRUE;
	}







}



?>