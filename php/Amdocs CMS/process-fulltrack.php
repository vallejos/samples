<?php

$debug = TRUE;
$total = 0;

$xmlFile = date("Ymd")."_Wazzup_".DRUTT_FULLTRACK."_asset.xml";
$zipFile = date("Ymd")."_Wazzup_".DRUTT_FULLTRACK.".zip";

if (!is_writable(TMP_DIR_FT)) die("ERROR: ".TMP_DIR_FT." is not writable\n");
else {
	$log .= "borrando tmp dir\n";
	exec("rm -rf ".TMP_DIR_FT."/*");
}

if (!is_writable(ZIP_DIR)) die("ERROR: ".ZIP_DIR." is not writable\n");


$xmlContent = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>'."\n";
$xmlContent .= '<asset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\n";


foreach ($listaIds as $contentId) {
	$contentId = trim($contentId);
	$log .= "\tprocessando $contentId\n";

	$fulltrack = new druttFulltrack($dbc, $debug);
	try {
		$fulltrack->loadContent($contentId);
	} catch (Exception $e) {
		$log .= "loadContent: ".$e->getMessage()."\n";
	}

	try {
		$content_download = FALSE;
		$fulltrack->setTag($catdrutt);
		// descargo contenido por FTP
		$log .= "\tdescargando contenido...\n";
		$ftpCon = new Ftp();
		$conectado = $ftpCon->login();
		if ($conectado !== TRUE) $conectado = $ftpCon->login();
		if ($conectado !== TRUE) $conectado = $ftpCon->login();
		if ($conectado !== TRUE) $conectado = $ftpCon->login();
		if ($conectado === TRUE) {
			$to = TMP_DIR_FT."/".$fulltrack->getContentFilename();
			$from = $fulltrack->getContent();
//			$bajado = copy($from, $to);
$bajado = $ftpCon->bajar($from, $to);
			if ($bajado === TRUE) {
				$log .= "\tdescargando preview...\n";
				$to = TMP_DIR_FT."/".$fulltrack->getPreviewFilename();
				$from = $fulltrack->getPreview();
//				$bajado = copy($from, $to);
$bajado = $ftpCon->bajar($from, $to);
				if ($bajado === TRUE) {
					$content_download = TRUE;
				} else {
					echo "ERROR: descargando el preview del ftp\n";
					exit;
				}
			} else {
				echo "ERROR: descargando el contenido $from del ftp a $to\n";
				exit;
			}
		} else {
			echo "ERROR: no se puede loguear al ftp\n";
		}

		if ($content_download === TRUE) {
			// obtengo y genero el XML
			$xmlContent .= $fulltrack->genXML();
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
if (!$fp = fopen(TMP_DIR_FT."/$xmlFile", "a+")) {
	echo "Cannot open file ($filename)";
	exit;
}
if (fwrite($fp, $xmlContent) === FALSE) {
	echo "Cannot write to file ($filename)";
	exit;
}
//      echo "Success, wrote ($somecontent) to file ($filename)";
fclose($fp);

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
$shellCmd = "cd ".TMP_DIR_FT."; zip -r ../".ZIP_DIR."/$zipFile * ";
$log .= exec ($shellCmd);
$log .= "\tZip ".ZIP_DIR."/$zipFile generado exitosamente\n";

$ds = filesize(ZIP_DIR."/$zipFile");
$ds = formatBytes($ds);
echo "generado <a href='".ZIP_DIR."/$zipFile'>".ZIP_DIR."/$zipFile</a> con $total contenidos, <b>$ds</b><br/>\n";

echo "<hr/><h3>Log:</h3>";
echo "<textarea cols=50 rows=10>$log</textarea>";

?>