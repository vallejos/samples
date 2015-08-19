<?php

$debug = TRUE;
$total = 0;

$xmlFile = date("Ymd")."_Wazzup_".DRUTT_WALLPAPER."_asset.xml";
$zipFile = date("Ymd")."_Wazzup_".DRUTT_WALLPAPER.".zip";

$tmpDir = TMP_DIR_WP;
if (!is_writable($tmpDir)) die("ERROR: ".$tmpDir." is not writable\n");
else {
    $sessDir = $tmpDir."/".$_SESSION["folder"];
    if (!file_exists($sessDir)) exec("mkdir $sessDir");
    $tmpDir = $sessDir;
    if (!is_writable($tmpDir)) die("ERROR: ".$tmpDir." is not writable\n");
    else {
        $log .= "borrando tmp dir\n";
        exec("rm -rf ".$tmpDir."/*");
    }
}


if (!is_writable(ZIP_DIR)) die("ERROR: ".ZIP_DIR." is not writable\n");


$xmlContent = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>'."\n";
$xmlContent .= '<asset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\n";


foreach ($listaIds as $contentId) {
	$contentId = trim($contentId);
	$log .= "\tprocessando $contentId\n";

	$wallpaper = new migWallpaper($dbc, $debug);
	try {
		$wallpaper->loadContent($contentId);
	} catch (Exception $e) {
		$log .= "loadContent: ".$e->getMessage()."\n";
	}

	try {
		$content_download = FALSE;
		$wallpaper->setTag($catdrutt);
		// descargo contenido por FTP
		$log .= "\tdescargando contenido...\n";
		$ftpCon = new Ftp();
		$conectado = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
		if ($conectado === TRUE) {
			$to = $tmpDir."/".$wallpaper->getContentFilename();
			$from = $wallpaper->getContent();
			$bajado = $ftpCon->bajar_r($from, $to, FTP_DOWN_RETRIES);
			if ($bajado === TRUE) {
				$log .= "\tdescargando preview...\n";
				$to = $tmpDir."/preview_tmp.gif";
				$from = $wallpaper->getPreview();
				$bajado = $ftpCon->bajar_r($from, $to, FTP_DOWN_RETRIES);
				if ($bajado === TRUE) {
					$origen_file = $to;
					$destino_file = $tmpDir."/".$wallpaper->getPreviewFilename();
					$width = 100;
					$height = 100;
					$background = FALSE;
					$extension = ".gif";
					crearImagen($to,$destino_file,$width,$height,$background,$extension);
					unlink($origen_file);
					$content_download = TRUE;
				} else {
					$log .= "\tgif preview not found...\n";
					$log .= "\ttrying preview (jpg)...\n";
					$to = $tmpDir."/preview_tmp.gif";
					$from = str_replace(".gif", ".jpg", $wallpaper->getPreview());
					$bajado = $ftpCon->bajar_r($from, $to, FTP_DOWN_RETRIES);
					if ($bajado === TRUE) {
						$origen_file = $to;
						$destino_file = $tmpDir."/".$wallpaper->getPreviewFilename();
						$width = 100;
						$height = 100;
						$background = FALSE;
						$extension = ".jpg";
						crearImagen($to,$destino_file,$width,$height,$background,$extension);
						unlink($origen_file);
						$content_download = TRUE;
					} else {
						echo "ERROR: descargando el preview del ftp\n";
						exit;
					}
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
			$xmlContent .= $wallpaper->genXML();
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
if (!$fp = fopen($tmpDir."/$xmlFile", "a+")) {
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
		$zipFile = date("Ymd")."_Wazzup_".WAZZUP_WALLPAPER."_$i.zip";
	} else {
		$existe = FALSE;
	}
}

// creo file para ftp
if ($FTPProcess == "new") createEmptyFile($tmpDir."/create.start");
else if ($FTPProcess == "update") createEmptyFile($tmpDir."/edit.start");

// zipeo y muevo a carpeta de "envios"
$shellCmd = "cd ".$tmpDir."; zip ../../../".ZIP_DIR."/$zipFile * ";
$log .= exec ($shellCmd);
$log .= "\tZip ".ZIP_DIR."/$zipFile generado exitosamente\n";

// chequeo zip filesize
$ds = filesize(ZIP_DIR."/$zipFile");
$ds = formatBytes($ds);
echo "generado <a href='".ZIP_DIR."/$zipFile'>".ZIP_DIR."/$zipFile</a> con $total contenidos, <b>$ds</b><br/>\n";


echo "<hr/><h3>Log:</h3>";
echo "<textarea cols=50 rows=10>$log</textarea>";

?>