<?php

$debug = TRUE;
$total = 0;

$xmlFile = date("Ymd")."_Wazzup_".DRUTT_POLYTONE."_asset.xml";
$zipFile = date("Ymd")."_Wazzup_".DRUTT_POLYTONE.".zip";

if (!is_writable(TMP_DIR_PT)) die("ERROR: ".TMP_DIR_PT." is not writable\n");
else {
	$log .= "borrando tmp dir\n";
	exec("rm -f ".TMP_DIR_PT."/*");
}

if (!is_writable(ZIP_DIR)) die("ERROR: ".ZIP_DIR." is not writable\n");


$xmlContent = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>'."\n";
$xmlContent .= '<asset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\n";


foreach ($listaIds as $contentId) {
	$contentId = trim($contentId);
	$log .= "\tprocessando $contentId\n";

	$polytone = new druttPolytone($dbc, $debug);
	try {
		$polytone->loadContent($contentId);
	} catch (Exception $e) {
		$log .= "loadContent: ".$e->getMessage()."\n";
	}

	try {
		$content_download = FALSE;
		$polytone->setTag($catdrutt);
		// descargo contenido por FTP
		$log .= "\tdescargando contenido full...\n";
		$ftpCon = new Ftp();
		$conectado = $ftpCon->login();
		if ($conectado !== TRUE) $conectado = $ftpCon->login();
		if ($conectado !== TRUE) $conectado = $ftpCon->login();
		if ($conectado !== TRUE) $conectado = $ftpCon->login();
		if ($conectado === TRUE) {
			$to = TMP_DIR_PT."/".$polytone->getContentFilenameFull();
			$from = $polytone->getContentFull();
			$bajado = $ftpCon->bajar($from, $to);
			if ($bajado !== TRUE) $bajado = $ftpCon->bajar($from, $to);
			if ($bajado !== TRUE) $bajado = $ftpCon->bajar($from, $to);
			if ($bajado !== TRUE) $bajado = $ftpCon->bajar($from, $to);
			if ($bajado === TRUE) {
				$to = TMP_DIR_PT."/".$polytone->getPreviewFilename();
				$from = $polytone->getPreview();
				$log .= "\tdescargando preview $from > $to\n";
				$bajado = $ftpCon->bajar($from, $to);
				if ($bajado === TRUE) {
					$log .= "\tdescargando contenido 4 canales...\n";
					$to = TMP_DIR_PT."/".$polytone->getContentFilename();
					$from = $polytone->getPreview();
					$bajado = $ftpCon->bajar($from, $to);
					if ($bajado === TRUE) {
						$content_download = TRUE;
					} else {
						echo "ERROR: descargando el contenido 4 canales del ftp\n";
						exit;
					}
				} else {
					echo "ERROR: descargando el preview del ftp\n";
					exit;
				}
			} else {
				echo "ERROR: descargando el contenido full $from del ftp a $to\n";
				exit;
			}
		} else {
			echo "ERROR: no se puede loguear al ftp\n";
		}

		if ($content_download === TRUE) {
			// obtengo y genero el XML
			$xmlContent .= $polytone->genXML();
			$total++;
		} else {
			$log .= " ";
		}

	} catch (Exception $e) {
		$log .= "genXML: ".$e->getMessage()."\n";
	}

}

$xmlContent .= '</asset>'."\n";

// escribo el contenido ahora
if (!$fp = fopen(TMP_DIR_PT."/$xmlFile", "a+")) {
	echo "Cannot open file ($filename)";
	exit;
}
if (fwrite($fp, $xmlContent) === FALSE) {
	echo "Cannot write to file ($filename)";
	exit;
}
//      echo "Success, wrote ($somecontent) to file ($filename)";
fclose($fp);

// genero nombre para el zip
$i=0;
$existe = TRUE;
while ($existe === TRUE) {
	if (file_exists(ZIP_DIR."/$zipFile")) {
		$i++;
		$zipFile = date("Ymd")."_Wazzup_".DRUTT_POLYTONE."_$i.zip";
	} else {
		$existe = FALSE;
	}
}

// zipeo y muevo a carpeta de "envios"
$shellCmd = "cd ".TMP_DIR_PT."; zip ../".ZIP_DIR."/$zipFile * ";
$log .= exec ($shellCmd);
$log .= "\tZip ".ZIP_DIR."/$zipFile generado exitosamente\n";

// chequeo zip filesize
$ds = filesize(ZIP_DIR."/$zipFile");
$ds = formatBytes($ds);
echo "generado <a href='".ZIP_DIR."/$zipFile'>".ZIP_DIR."/$zipFile</a> con $total contenidos, <b>$ds</b><br/>\n";



echo "<hr/><h3>Log:</h3>";
echo "<textarea cols=50 rows=10>$log</textarea>";

?>