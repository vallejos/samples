<?php


class DIPDetail {
	private $dbc;
	private $DSPMSP;
	private $dateReport;
	private $firstDate;
	private $lastDate;
	private $operationType;
	private $saleType;
	private $distributionChannel;
	private $originalProductCode;
	private $productCode;
	private $artist;
	private $title;
	private $totalSoldUnits;
	private $priceClient;
	private $priceDSPMSP;
	private $aditionalIncome;
	private $revenue;
	private $chargingEntity;
	private $DSPMSPName;
	private $clientCountry;
	private $priceLevel;
	private $currencyCode;

	private $detailDIP;

	// default constructor
	function __construct($dbc, $reportId) {
		$this->dbc = $dbc;
		$this->reportId = $reportId;
		$this->DSPMSP = DSPMSP;
		$this->detailDIP = "";
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

		$this->totalDetails = $this->getTotalDetails();
		$this->dateReport = str_replace("/", "-", $this->getDateReport());
		$time = strtotime($this->dateReport);
		$this->dateReport = date("d/m/Y", $time);
		list ($dd,$mm,$yyyy) = split("/", $this->dateReport);

		$sql = "SELECT id,idWarner,nombre,autor,comprar,regalar,descargar FROM admins.warner_detail_tones WHERE reportId='$this->reportId' ";
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) {
			// error querying db
			echo "error db ($sql):".mysql_error($this->dbc)."";
		}
		while ($obj = mysql_fetch_object($rs)) {
			$this->operationType = $this->getOperationType();
			$this->saleType = $this->getSaleType();
			$this->distributionChannel = $this->getDistributionChannel();
			$this->originalProductCode = "";
			$this->artist = $obj->autor;
			$this->title = $obj->nombre;
			$this->productCode = $this->getGridCode();

			if ($this->productCode == "") {
				$this->productCode = getGrid($this->title);
			}
//			if ($this->productCode == "") echo "$this->title\n";

			$this->totalSoldUnits = $obj->comprar;
$precio_sin_impuestos = PRECIO - (PRECIO*0.18);
			$this->priceClient = number_format(bob2usd($precio_sin_impuestos, $mmCotizacion, $yyCotizacion), 2);
$precio_warner_sin_impuestos = $precio_sin_impuestos * 0.50;
			$this->priceDSPMSP = number_format(bob2usd($precio_warner_sin_impuestos, $mmCotizacion, $yyCotizacion), 4);
			$this->aditionalIncome = 0;
			$this->revenue = 0; // warner revenue, 50%
			$this->chargingEntity = $this->DSPMSP;
			$this->DSPMSPName = "Wazzup";
			$this->DSPMSPCountry = "UY";
			$this->clientCountry = "BO";
			$this->priceLevel = "STD";
			$this->currencyCode = "USD";

			$this->save();

			$string = $this->DSPMSP."\t".
			$this->dateReport."\t".
			$this->firstDate."\t".
			$this->lastDate."\t".
			$this->operationType."\t".
			$this->saleType."\t".
			$this->distributionChannel."\t".
			$this->originalProductCode."\t".
			$this->productCode."\t".
			$this->artist."\t".
			$this->title."\t".
			$this->totalSoldUnits."\t".
			$this->priceClient."\t".
			$this->priceDSPMSP."\t".
			$this->aditionalIncome."\t".
			$this->revenue."\t".
			$this->chargingEntity."\t".
			$this->DSPMSPName."\t".
			$this->DSPMSPCountry."\t".
			$this->clientCountry."\t".
			$this->priceLevel."\t".
			$this->currencyCode."\n";

			$this->detailDIP .= $string;

		}
	}


	private function getFirstDate() {
//global $fechaI, $fechaF;
//return $fechaI;
		$sql = "SELECT MIN(fecha) firstDate FROM admins.warner_detail_date WHERE reportId='$this->reportId' ";
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



	private function getOperationType() {
		return "DOWNLOAD";
	}

	private function getSaleType() {
		return "OTHER";
	}

	private function getDistributionChannel() {
		return "WIRELESS";
	}

	private function getGridCode() {
//		$sql = "SELECT code FROM admins.warner_metadata WHERE artista LIKE '%$this->artist%' AND titulo LIKE '%$this->title%' ";
		$sql = "SELECT code FROM admins.warner_metadata WHERE titulo LIKE '%$this->title%' ";
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) {
			// error querying db
			echo "error db ($sql):".mysql_error($this->dbc)."";
		}
		$obj = mysql_fetch_object($rs);

		return $obj->code;
	}

	private function save() {
		$sql = "INSERT INTO admins.warner_detail SET
			reportId = '$this->reportId',
			dspmsp = '$this->DSPMSP',
			dateReport = '$this->dateReport',
			dateFirstTransaction = '$this->firstDate',
			dateLastTransaction = '$this->lastDate',
			operationType = '$this->operationType',
			saleType = '$this->saleType',
			channel = '$this->distributionChannel',
			productCodeOriginal = '$this->originalProductCode',
			productCode = '$this->productCode',
			artist = '$this->artist',
			title = '$this->title',
			soldUnits = '$this->totalSoldUnits',
			finalPrice = '$this->priceClient',
			dspmspPrice = '$this->priceDSPMSP',
			aditionalIncome = '$this->aditionalIncome',
			percentage = '$this->revenue',
			entity = '$this->chargingEntity',
			dspmspName = '$this->DSPMSPName',
			dspmspCountry = '$this->DSPMSPCountry',
			customerCountry = '$this->clientCountry',
			priceLevel = '$this->priceLevel',
			currency = '$this->currencyCode'
		";
		$rs = mysql_query($sql, $this->dbc->db);
		if (!$rs) echo "error ($sql): ".mysql_error()."\n";
	}


	public function toString() {
		return $this->detailDIP;
	}



}



?>