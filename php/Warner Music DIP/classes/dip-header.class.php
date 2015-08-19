<?php




class DIPHeader {
	private $dbc;
	private $reportId;
	private $DSPMSP;
	private $dateReport;
	private $firstDate;
	private $lastDate;
	private $totalAmount;
	private $totalDetails;



	// default constructor
	function __construct($dbc, $reportId) {
		$this->dbc = $dbc;
		$this->reportId = $reportId;
		$this->DSPMSP = DSPMSP;
		$this->load();
	}


	// default destructor
	function __destruct() {

	}


	private function load() {
		$this->firstDate = str_replace("/", "-", $this->getFirstDate());
		if (strlen($this->firstDate) == 8) {
			// fecha con year en formato corto
			list ($mm, $dd, $yy) = split("-", $this->firstDate);
			$this->firstDate = "$dd-$mm-20$yy";
			$time = strtotime($this->firstDate);
			$this->firstDate = date("d/m/Y", $time);
		}

		$this->lastDate = str_replace("/", "-", $this->getLastDate());
		if (strlen($this->lastDate) == 8) {
			// fecha con year en formato corto
			list ($mm, $dd, $yy) = split("-", $this->lastDate);
			$this->lastDate = "$dd-$mm-20$yy";
			$time = strtotime($this->lastDate);
			$this->lastDate = date("d/m/Y", $time);

			$mmCotizacion = $mm;
			$yyCotizacion = "20".$yy;
		}
		$precio_sin_impuestos = PRECIO - (PRECIO*0.18);
		$precio_warner_sin_impuestos = $precio_sin_impuestos * 0.50;
		$this->totalAmount = number_format($this->getTotalSales() * bob2usd($precio_warner_sin_impuestos, $mmCotizacion, $yyCotizacion), 2);

		$this->totalDetails = $this->getTotalDetails();

		$this->dateReport = str_replace("/", "-", $this->getDateReport());
		$time = strtotime($this->dateReport);
		$this->dateReport = date("d/m/Y", $time);
//		list ($dd,$mm,$yyyy) = split("/", $this->dateReport);

		$this->update();
	}


	public function toString() {
		$string = $this->DSPMSP."\t".$this->dateReport."\t".$this->firstDate."\t".$this->lastDate."\t".$this->totalAmount."\t".$this->totalDetails."\n";
		return $string;
	}


	private function getFirstDate() {
//global $fechaI, $fechaF;
//return $fechaI;
		$sql = "SELECT MIN(fecha) firstDate FROM admins.warner_detail_date WHERE reportId='$this->reportId' ";
echo $sql;
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) {
			// error querying db
			echo "error db ($sql):".mysql_error($this->dbc)."";
		}
		$obj = mysql_fetch_object($rs);

		return $obj->firstDate;
	}

	private function getLastDate() {
//global $fechaI, $fechaF;
//return $fechaF;
		$sql = "SELECT MAX(fecha) lastDate FROM admins.warner_detail_date WHERE reportId='$this->reportId' ";
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) {
			// error querying db
			echo "error db ($sql):".mysql_error($this->dbc)."";
		}
		$obj = mysql_fetch_object($rs);

		return $obj->lastDate;
	}


	private function getTotalSales() {
		$sql = "SELECT SUM(comprar) totalSales FROM admins.warner_detail_tones WHERE reportId='$this->reportId' ";
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) {
			// error querying db
			echo "error db ($sql):".mysql_error($this->dbc)."";
		}
		$obj = mysql_fetch_object($rs);

		return $obj->totalSales;
	}


	private function getTotalDetails() {
		$sql = "SELECT COUNT(*) totalDetails FROM admins.warner_detail_tones WHERE reportId='$this->reportId' ";
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) {
			// error querying db
			echo "error db ($sql):".mysql_error($this->dbc)."";
		}
		$obj = mysql_fetch_object($rs);

		return $obj->totalDetails;
	}


	private function getDateReport() {
		$sql = "SELECT dateReport FROM admins.warner_report WHERE id='$this->reportId' ";
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) {
			// error querying db
			echo "error db ($sql):".mysql_error($this->dbc)."";
		}
		$obj = mysql_fetch_object($rs);

		return $obj->dateReport;
	}


	private function update() {
		$sql = "UPDATE admins.warner_report SET
			dspmsp='$this->DSPMSP',
			dateReport='$this->dateReport',
			dateFistTransaction='$this->firstDate',
			dateLastTransaction='$this->lastDate',
			totalSold='$this->totalAmount',
			totalDetails='$this->totalDetails'
			WHERE id='$this->reportId' ";
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) {
			// error querying db
			echo "error db ($sql):".mysql_error($this->dbc)."";
		}
	}



}





?>