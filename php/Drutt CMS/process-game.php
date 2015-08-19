<?php

$debug = TRUE;
$totalCont = 0;
$totalModels = 0;
$ziplog = "";
set_time_limit(0);

$xmlFile = date("Ymd")."_Wazzup_".DRUTT_GAME."_asset.xml";
$zipFile = date("Ymd")."_Wazzup_".DRUTT_GAME.".zip";

foreach ($listaIds as $contentId) {
        $xmlToAdd = "";
        if (!is_writable(TMP_DIR_JG)) die("ERROR: ".TMP_DIR_JG." is not writable\n");
        else {
                $log .= "borrando tmp dir\n";
                exec("rm -f ".TMP_DIR_JG."/*");
        }

        if (!is_writable(ZIP_DIR)) die("ERROR: ".ZIP_DIR." is not writable\n");

        $xmlContent = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>'."\n";
        $xmlContent .= '<asset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\n";

        $contentId = trim($contentId);
	$log .= "\tprocessando $contentId\n";
	$log .= "\tcargando lista de jad/jar...\n";

        // cargo todos los jad/jar 
        $juegos = array();
	$sql = "SELECT DISTINCT gc.archivo FROM Web.gamecomp gc WHERE gc.juego='$contentId' ";
	$rs = mysql_query($sql, $dbc->db);
	while($obj = mysql_fetch_object($rs)) {
            $juegos[] = $obj->archivo;
        }

	$game = new druttGame($dbc, $debug);
	try {
		$game->loadContent($contentId);
	} catch (Exception $e) {
		$log .= "loadContent: ".$e->getMessage()."\n";
	}

	try {
		$content_downloaded = FALSE;
		$game->setTag($catdrutt);
		// descargo contenido por FTP
		$log .= "\tdescargando contenido $contentId...\n";

		$i=0;
		$conectado = FALSE;
		foreach ($juegos as $archivo) {
                        $content_downloaded = getFtpFiles($archivo);
			if ($content_downloaded === TRUE) {
                                echo "($contentId) Armando lista de devices para $archivo <br/>";
				$models = getAllModels($dbc, $archivo);

                                echo "($contentId) Generando XML variants... <br/>";
				$newCompatElement = generateVariantXml($dbc, $contentId, $archivo, $uniqueId, $models, $xml);

				if ($newCompatElement === FALSE) {
					echo "($contentId) - could not generate variant for <b>$modelId</b> <br/>";

                                        //----------------------------------------------------------------------------
                                        // new v1.4: delete jad/jars (by kmS)
                                        // borra jad/jar descargado previamente si no existen devices compatibles
                                        // lease: no manda en el zip archivos jad/jar a los que no se hace referencia
                                        // en el xml
                                        //----------------------------------------------------------------------------
                                        echo "Devices not Found for archivo=$archivo!<br/>";
                                        echo "Cleaning not used jad/jar files...<br/>";
                                        $info = pathinfo($archivo);
                                        $toJad = $info['basename'];
                                        $del1 = TMP_DIR_JG."/".$toJad;
                                        $toJar = str_replace(".jad", ".jar", $toJad);
                                        $del2 = TMP_DIR_JG."/".$toJar;
                                        exec("rm -f ".TMP_DIR_JG."/$toJad");
                                        exec("rm -f ".TMP_DIR_JG."/$toJar");
                                        echo "Unused jad/jar files successfully erased<br/>";
                                        //----------------------------------------------------------------------------

                                } else {
                                        echo "($contentId)  variants Ok! <br/>";
					$xmlToAdd .= $newCompatElement;
					$newAdded++;
				}
  			} else {
				echo "($contentId) Contenido no descargado. Devices no agregados. Variant no generado.<br/>";
			}
			$i++;
		}

		$to = TMP_DIR_JG."/preview_tmp.gif";
		$from = $game->getPreview();
		$log .= "\tdescargando preview $from > $to\n";

		$ftpConUSA = new Ftp("216.150.27.11", "wmast", "hulkverde");
		$connectUSA = $ftpConUSA->login_r(null, null, FTP_CONN_RETRIES);

		$bajado = $ftpConUSA->bajar_r($from, $to, FTP_DOWN_RETRIES);
		if ($bajado === TRUE) {
			$origen_file = $to;
			$destino_file = TMP_DIR_JG."/".$game->getPreviewFilename();
			$width = 100;
			$height = 100;
			$background = FALSE;
			$extension = ".gif";
			crearImagen($to,$destino_file,$width,$height,$background,$extension);
			unlink($origen_file);
			$content_download = TRUE;
		} else {
			echo "($contentId) ERROR: descargando el preview del ftp\n";
			exit;
		}

	} catch (Exception $e) {
		$log .= "genXML: ".$e->getMessage()."\n";
	}
	$totalCont++;


        if ($content_download === TRUE) {
                // obtengo y genero el XML
                $xmlContent .= $game->genXML();
                $xmlContent = str_replace("%%PREMIUM%%", $xmlToAdd, $xmlContent);
                $total++;
        } else {
                $log .= " ";
        }

        $xmlContent .= '</asset>'."\n";

        // escribo el contenido ahora
        if (!$fp = fopen(TMP_DIR_JG."/$xmlFile", "a+")) {
                echo "($contentId) Cannot open file ($filename)";
                exit;
        }
        if (fwrite($fp, $xmlContent) === FALSE) {
                echo "($contentId) Cannot write to file ($filename)";
                exit;
        }
        //      echo "($contentId) Success, wrote ($somecontent) to file ($filename)";
        fclose($fp);

        // genero nombre para el zip
        $i=0;
        $existe = TRUE;
        while ($existe === TRUE) {
                if (file_exists(ZIP_DIR."/$zipFile")) {
                        $i++;
                        $zipFile = date("Ymd")."_Wazzup_".DRUTT_GAME."_$i.zip";
                } else {
                        $existe = FALSE;
                }
        }

        // zipeo y muevo a carpeta de "envios"
        $shellCmd = "cd ".TMP_DIR_JG."; zip ../".ZIP_DIR."/$zipFile * ";
        $log .= exec ($shellCmd);
        $log .= "\tZip ".ZIP_DIR."/$zipFile generado exitosamente\n";

        // chequeo zip filesize
        $ds = filesize(ZIP_DIR."/$zipFile");
        $ds = formatBytes($ds);
        echo "($contentId) generado <a href='".ZIP_DIR."/$zipFile'>".ZIP_DIR."/$zipFile</a> con $totalCont contenidos y $totalModel modelos, <b>$ds</b><br/>\n";
        $zipLog .= "generado <a href='".ZIP_DIR."/$zipFile'>".ZIP_DIR."/$zipFile</a> con $totalCont contenidos y $totalModel modelos, <b>$ds</b><br/>\n";

}

/*
if ($content_download === TRUE) {
	// obtengo y genero el XML
	$xmlContent .= $game->genXML();
        $xmlContent = str_replace("%%PREMIUM%%", $xmlToAdd, $xmlContent);
	$total++;
} else {
	$log .= " ";
}

$xmlContent .= '</asset>'."\n";

// escribo el contenido ahora
if (!$fp = fopen(TMP_DIR_JG."/$xmlFile", "a+")) {
	echo "($contentId) Cannot open file ($filename)";
	exit;
}
if (fwrite($fp, $xmlContent) === FALSE) {
	echo "($contentId) Cannot write to file ($filename)";
	exit;
}
//      echo "($contentId) Success, wrote ($somecontent) to file ($filename)";
fclose($fp);

// genero nombre para el zip
$i=0;
$existe = TRUE;
while ($existe === TRUE) {
	if (file_exists(ZIP_DIR."/$zipFile")) {
		$i++;
		$zipFile = date("Ymd")."_Wazzup_".DRUTT_GAME."_$i.zip";
	} else {
		$existe = FALSE;
	}
}

// zipeo y muevo a carpeta de "envios"
$shellCmd = "cd ".TMP_DIR_JG."; zip ../".ZIP_DIR."/$zipFile * ";
$log .= exec ($shellCmd);
$log .= "\tZip ".ZIP_DIR."/$zipFile generado exitosamente\n";

// chequeo zip filesize
$ds = filesize(ZIP_DIR."/$zipFile");
$ds = formatBytes($ds);
echo "($contentId) generado <a href='".ZIP_DIR."/$zipFile'>".ZIP_DIR."/$zipFile</a> con $totalCont contenidos y $totalModel modelos, <b>$ds</b><br/>\n";
*/

echo "($contentId) <hr/><h3>Log:</h3>";
echo "($contentId) <textarea cols=50 rows=10>$log</textarea>";

echo $zipLog;

?>