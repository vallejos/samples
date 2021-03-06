<?php

$debug = TRUE;
$total = 0;

$xmlFile = date("Ymd")."_Wazzup_".DRUTT_VIDEO."_asset.xml";
$zipFile = date("Ymd")."_Wazzup_".DRUTT_VIDEO.".zip";

if (!is_writable(TMP_DIR_VD)) die("ERROR: ".TMP_DIR_VD." is not writable\n");
else { 
	$log .= "borrando tmp dir\n";
	exec("rm -f ".TMP_DIR_VD."/*");
}

if (!is_writable(ZIP_DIR)) die("ERROR: ".ZIP_DIR." is not writable\n");


$xmlContent = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>'."\n";
$xmlContent .= '<asset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\n";


foreach ($listaIds as $contentId) {
	$contentId = trim($contentId);
	$log .= "\tprocessando $contentId\n";

	$video = new druttVideo($dbc, $debug);
	try {
		$video->loadContent($contentId);
	} catch (Exception $e) {
		$log .= "loadContent: ".$e->getMessage()."\n";
	}

	try {
		$content_download = FALSE;
		$video->setTag($catdrutt);
		// descargo contenido por FTP
		$log .= "\tdescargando contenido...\n";
		$ftpCon = new Ftp();
		$conectado = $ftpCon->login();
		if ($conectado !== TRUE) $conectado = $ftpCon->login();
		if ($conectado !== TRUE) $conectado = $ftpCon->login();
		if ($conectado !== TRUE) $conectado = $ftpCon->login();
		if ($conectado === TRUE) {
			$to = TMP_DIR_VD."/".$video->getContentFilename();
			$from = $video->getContent();
			$bajado = $ftpCon->bajar($from, $to);
			if ($bajado !== TRUE) $bajado = $ftpCon->bajar($from, $to);
			if ($bajado !== TRUE) $bajado = $ftpCon->bajar($from, $to);
			if ($bajado !== TRUE) $bajado = $ftpCon->bajar($from, $to);
			if ($bajado === TRUE) {
				$to = TMP_DIR_VD."/preview_tmp.gif";
				$from = $video->getPreview();
				$log .= "\tdescargando preview $from > $to\n";

				$ftpConUSA = new Ftp("216.150.27.11", "wmast", "hulkverde");
				$connectUSA = $ftpConUSA->login();
				if ($conectado !== TRUE) $connectUSA = $ftpConUSA->login();
				if ($conectado !== TRUE) $connectUSA = $ftpConUSA->login();
				if ($conectado !== TRUE) $connectUSA = $ftpConUSA->login();

				$bajado = $ftpConUSA->bajar($from, $to);
				if ($bajado !== TRUE) $bajado = $ftpConUSA->bajar($from, $to);
				if ($bajado !== TRUE) $bajado = $ftpConUSA->bajar($from, $to);
				if ($bajado !== TRUE) $bajado = $ftpConUSA->bajar($from, $to);
				if ($bajado === TRUE) {
					$origen_file = $to;
					$destino_file = TMP_DIR_VD."/".$video->getPreviewFilename();
					$width = 100;
					$height = 100;
					$background = FALSE;
					$extension = ".gif";
					crearImagen($to,$destino_file,$width,$height,$background,$extension);
					unlink($origen_file);
					$content_download = TRUE;
				} else {
					echo "ERROR: descargando el preview del ftp\n";
					exit;
				}
				$ftpConUSA->logout();
			} else {
				echo "ERROR: descargando el contenido $from del ftp a $to\n";
				exit;
			}
		} else {
			echo "ERROR: no se puede loguear al ftp\n";
		}

		if ($content_download === TRUE) {
			// obtengo y genero el XML
			$xmlContent .= $video->genXML();
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
if (!$fp = fopen(TMP_DIR_VD."/$xmlFile", "a+")) {
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
		$zipFile = date("Ymd")."_Wazzup_".DRUTT_FULLTRACK."_$i.zip";
	} else {
		$existe = FALSE;
	}
}

// zipeo y muevo a carpeta de "envios"
$shellCmd = "cd ".TMP_DIR_VD."; zip ../".ZIP_DIR."/$zipFile * ";
$log .= exec ($shellCmd);
$log .= "\tZip ".ZIP_DIR."/$zipFile generado exitosamente\n";

// chequeo zip filesize
$ds = filesize(ZIP_DIR."/$zipFile");
$ds = formatBytes($ds);
echo "generado <a href='".ZIP_DIR."/$zipFile'>".ZIP_DIR."/$zipFile</a> con $total contenidos, <b>$ds</b><br/>\n";

echo "<hr/><h3>Log:</h3>";
echo "<textarea cols=50 rows=10>$log</textarea>";

?>