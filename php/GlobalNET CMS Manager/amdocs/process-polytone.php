<?php

$debug = TRUE;
$total = 0;

$xmlFile = date("Ymd")."_Wazzup_".MIG_POLYTONE."_asset.xml";
$zipFile = date("Ymd")."_Wazzup_".MIG_POLYTONE.".zip";

$tmpDir = TMP_DIR_PT;
$sessDir = $tmpDir."/".$_SESSION["folder"];
if (!file_exists($sessDir)) exec("mkdir $sessDir");
$tmpDir = $sessDir;

if (!is_writable($tmpDir)) die("ERROR: ".$tmpDir." is not writable\n");
else {
	$log .= "borrando tmp dir\n";
	exec("rm -rf ".$tmpDir."/*");
}

if (!is_writable(ZIP_DIR)) die("ERROR: ".ZIP_DIR." is not writable\n");

if ($tipoCarga == "new") {
        //------------------------
        // si es contenido nuevo
        //------------------------
        foreach ($listaIds as $i => $contentId) {
                $contentId = trim($contentId);
                $log .= "\tprocessando $contentId\n";

                $tone = new migRealtone($dbc, $debug,$catLvl, $webCat, true, $rating, $marca, $festivo);
                try {
                        $tone->loadContent($contentId);
                } catch (Exception $e) {
                        $log .= "loadContent: ".$e->getMessage()."\n";
                }

                try {
                        $content_download = FALSE;
                        $tone->setTag($catmig);
                        $tone->setLangs(array_keys($idiomas_elegidos));
                        $tone->setMerchants(array_keys($paises_elegidos));

                        // descargo contenido por FTP
                        $log .= "\tdescargando contenido full...\n";
                        $ftpCon = new Ftp();
                        $conectado = $ftpCon->login();
                        if ($conectado !== TRUE) $conectado = $ftpCon->login();
                        if ($conectado !== TRUE) $conectado = $ftpCon->login();
                        if ($conectado !== TRUE) $conectado = $ftpCon->login();
                        if ($conectado === TRUE) {
                                $to = $tmpDir."/".$tone->getContentFilenameFull();
                                $from = $tone->getContentFull();
                                $bajado = $ftpCon->bajar($from, $to);
                                if ($bajado !== TRUE) $bajado = $ftpCon->bajar($from, $to);
                                if ($bajado !== TRUE) $bajado = $ftpCon->bajar($from, $to);
                                if ($bajado !== TRUE) $bajado = $ftpCon->bajar($from, $to);
                                if ($bajado === TRUE) {
                                        $to = $tmpDir."/".$tone->getPreviewFilename();
                                        $from = $tone->getPreview();
                                        $log .= "\tdescargando preview $from > $to\n";
                                        $bajado = $ftpCon->bajar($from, $to);
                                        if ($bajado === TRUE) {
                                                $log .= "\tdescargando contenido 4 canales...\n";
                                                $to = $tmpDir."/".$tone->getContentFilename();
                                                $from = $tone->getPreview();
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
                                $xmlContent .= $tone->genXML();
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
                        $zipFile = date("Ymd")."_Wazzup_".MIG_POLYTONE."_$i.zip";
                } else {
                        $existe = FALSE;
                }
        }

        // zipeo y muevo a carpeta de "envios"
        $shellCmd = "cd ".$tmpDir."; zip -r ../../../".ZIP_DIR."/$zipFile * ";
        $log .= exec ($shellCmd);
        $log .= "\tZip ".ZIP_DIR."/$zipFile generado exitosamente\n";

        
} else if ($tipoCarga == "update") {
        //------------------------
        // si es update
        //------------------------
        foreach ($listaIds as $i => $contentId) {
                $contentId = trim($contentId);
                $log .= "\tprocessando $contentId\n";

                $tone = new migRealtone($dbc, $debug,$catLvl, $webCat, true, $rating, $marca, $festivo);
                try {
                        $tone->loadContent($contentId);
                } catch (Exception $e) {
                        $log .= "loadContent: ".$e->getMessage()."\n";
                }

                try {
                    $xmlContent = $realtone->updateXML();
                    $arrayMuestra[] = $realtone->getArraySubForm();
                    $total++;
                    $d2 = $tmpDir."/metadata_update/";
                    $realtone->setDirToWrite($d2);
                    if (!file_exists($d1)) exec("mkdir $d1");
                    if (!file_exists($d2)) exec("mkdir $d2");
                    $written = file_put_contents($realtone->getDirToWrite().$xmlFile, $xmlContent);
                } catch (Exception $e) {
                        $log .= "genXML: ".$e->getMessage()."\n";
                }

        }

        // genero nombre para el zip
        $i=0;
        $existe = TRUE;
        while ($existe === TRUE) {
                if (file_exists(ZIP_DIR."/$zipFile")) {
                        $i++;
                        $zipFile = date("Ymd")."_Wazzup_".MIG_POLYTONE."_$i.zip";
                } else {
                        $existe = FALSE;
                }
        }

        // zipeo y muevo a carpeta de "envios"
        $shellCmd = "cd ".$tmpDir."; zip -r ../../../".ZIP_DIR."/$zipFile * ";
        $log .= exec ($shellCmd);
        $log .= "\tZip ".ZIP_DIR."/$zipFile generado exitosamente\n";




} else {
    die ("TIPO DE CARGA DESCONOCIDO!!!");
}

// chequeo zip filesize
$ds = filesize(ZIP_DIR."/$zipFile");
$ds = formatBytes($ds);
echo "<div style=\"margin: 0 auto; text-align: center; font-size: 20px\">Nombre del Zip generado ".ZIP_DIR."/<a href='".ZIP_DIR."/$zipFile'>$zipFile</a> con $total contenidos, <b>$ds</b></div><br/>\n";


echo "<hr/><h3>Submission Form:</h3>";
echo "<table>";
echo "<tr>";
foreach ($SFTitle as $title) {
    echo "<th>$title</th>";
}
echo "</tr>";
foreach($arrayMuestra as $linea){
//    $linea['Zip File Name'] = $zipFile;
    echo "<tr>";
    foreach($linea as $key => $columna){
        echo "<td valign=\"top\">$columna</td>";
    }
    foreach ($sfCountryList as $p) echo "<td>$p</td>";
}
echo "</table>";



echo "<hr/><h3>Log:</h3>";
echo "<textarea cols=50 rows=10>$log</textarea>";

?>