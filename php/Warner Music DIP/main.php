<?php

include_once("includes.php");

// obtengo datos para reporte en una fecha dada
//$fileDate = "dates1009.csv";
//$fileTone = "tones1009.csv";

/*
$files = array(
	"dates0509.csv" => "tones0509.csv",
	"dates0609.csv" => "tones0609.csv",
	"dates0709.csv" => "tones0709.csv",
	"dates0809.csv" => "tones0809.csv",
	"dates0909.csv" => "tones0909.csv",
	"dates1009.csv" => "tones1009.csv",
);

$files = array(
	"dates1109.csv" => "tones1109.csv",
	"dates1209.csv" => "tones1209.csv",
);
*/
/*
$files = array(
	"dates0110.csv" => "tones0110.csv",
	"dates0210.csv" => "tones0210.csv",
	"dates0310.csv" => "tones0310.csv",
	"dates0410.csv" => "tones0410.csv",
	"dates0510.csv" => "tones0510.csv",
	"dates0610.csv" => "tones0610.csv",
	"dates0710.csv" => "tones0710.csv",
	"dates0810.csv" => "tones0810.csv",
);
*/
$files = array(
	"dates0310.csv" => "tones0310.csv",
);

foreach ($files as $fileDate => $fileTone) {

$dbc = new conexion("admins");

// antes de procesar la info, leo los archivos y guardo la info en base de datos
// se inicia con un insert para generar un id de sesion, el reportId que identifica esta sesion
// new warner report
$DIP = new DIP($dbc);
$reportId = $DIP->getReportId();

$status = parseIntoDB($dbc, $reportId, $fileDate, $fileTone);
if ($status === FALSE) {
	// borrar de base de datos para reportId
	echo "borrando db\n";
}

// obtengo total ventas en ese periodo
//$totalSales = $DIP->getTotalSales();


// por cada venta, genero un detail
$oDetails = new DIPDetail($dbc, $reportId);
$details = $oDetails->toString();

// genero header
$oHeader = new DIPHeader($dbc, $reportId);
$header = $oHeader->toString();

// agrego header al dip
$DIP->addHeader($header);

// agrego details al dip
$DIP->addDetail($details);

// genero archivos
$incremental = $reportId;
$reportFile = PATH_REPORTS."/".DSPMSP."_WM_".$incremental."_".date("Ymd").".txt";
if ($DIP->toFile($reportFile) === TRUE) {
	echo "OK\n";

} else {
	echo "ERROR\n";

}


}


?>