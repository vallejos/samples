<?php

$debug = TRUE;
$total = 0;
$arrayMuestra = array();

if ($tipocarga == "update") {
    $xmlFile = "metadata.xml";
    $zipFile = "Wazzup_".MIG_VIDEO."_".date("Ymd")."_update.zip";
} else {
    $xmlFile = "metadata.xml";
    $zipFile = "Wazzup_".MIG_VIDEO."_".date("Ymd").".zip";
}

$tmpDir = TMP_DIR_VD;
$sessDir = $tmpDir."/".$_SESSION["folder"];
if (!file_exists($sessDir)) exec("mkdir $sessDir");
$tmpDir = $sessDir;

$SFTitle = array("Content Title","Website Category","Genero","Brand (Creator)", "Thumbnail",
    "AR","BR","CL","CO","EC","GT","HN","JM","MX","NI","PA","PY","PE","PR","DOM","SAL","UY");

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

            $video = new migVideo($dbc, $debug, $catLvl, $webCat, $rating, $marca, $festivo, $tipoVideo);
            try {
                $video->loadContent($contentId);
            } catch (Exception $e) {
                $log .= "loadContent: ".$e->getMessage()."\n";
            }

            $video->setTag($catmig);
            $video->setLangs(array_keys($idiomas_elegidos));
            $video->setMerchants(array_keys($paises_elegidos));

//            var_dump($paises_elegidos);
//            var_dump($idiomas_elegidos);

            $video->setSubTag($subcatmig);
            //$video->setKeywords($keywords[$i]);
            //$video->setShortDescription($shortDesc[$i]);
            //$video->setLongDescription($longDesc[$i]);

            $todos = true;
            $alguno = false;
            try {
                $content_download = FALSE;
                $log .= "\tdescargando contenido...\n";
                $ftpCon = new Ftp();
                $conectado = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
                if ($conectado === TRUE) {
                    $content_download = $video->downloadContent($ftpCon);
                    foreach($content_download as $formato => $bajado){
                        $todos = ($todos && $bajado);
                        if(!$bajado){
                            echo "<font color=\"red\">No se pudo bajar el video de formato $formato</font><br />";
                        } else {
                            $alguno = true;
                        }
                    }
                } else {
                    echo "ERROR: no se puede loguear al ftp\n";
                }

                if ($alguno === TRUE) {
                    $xmlContent = $video->genXML();
                    $arrayMuestra[] = $video->getArraySubForm();
                    $total++;
                    $written = file_put_contents($video->getDirToWrite(). "/" .$xmlFile, $xmlContent);
                } else {
                    $log .= " ";
                }
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
                //$zipFile = date("Ymd")."_Wazzup_".MIG_FULLTRACK."_$i.zip";
                //$zipFile = "Wazzup_".MIG_VIDEO."_".date("Ymd").".zip";
                $zipFile = "Wazzup_".MIG_VIDEO."_".date("Ymd")."_$i" . "_" .".zip";
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
            $video = new migVideo($dbc, $debug, $catLvl, $webCat, $marca, $rating, $festivo, $tipoVideo);
            try {
                $video->loadContent($contentId);
            } catch (Exception $e) {
                $log .= "loadContent: ".$e->getMessage()."\n";
            }

            try {
                $video->setLangs(array_keys($idiomas_elegidos));
                $video->setMerchants(array_keys($paises_elegidos));
                $video->setTag($catmig);
                $video->setSubTag($subcatmig);
                $video->setTag($catmigen, 'en');
                $video->setSubTag($subcatmigen, 'en');
                $xmlContent = $video->updateXML();
                $arrayMuestra[] = $video->getArraySubForm();
                $total++;
                $d2 = $tmpDir."/$contentId/";
                $video->setDirToWrite($d2);
                if (!file_exists($d1)) exec("mkdir $d1");
                if (!file_exists($d2)) exec("mkdir $d2");
                $written = file_put_contents($video->getDirToWrite().$xmlFile, $xmlContent);
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
                //$zipFile = date("Ymd")."_Wazzup_".MIG_FULLTRACK."_$i.zip";
                //$zipFile = "Wazzup_".MIG_VIDEO."_".date("Ymd").".zip";
                $zipFile = "Wazzup_".MIG_VIDEO."_".date("Ymd")."_$i" . "_update" .".zip";
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

if(!$todos && ($tipocarga=="new")){
    reportError("Faltaron formatos de videos por bajar, revisar log");
}

echo "<hr/><h3>Submission Form:</h3>";
/*
echo "<textarea cols=200 rows=10>";
foreach($arrayMuestra as $linea){
    $linea['Zip File Name'] = $zipFile;
    echo implode("\t", $linea) . "\r\n";
}
echo "</textarea>";
*/
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
        if($key != "Thumbnail" && $key != "Preview"){
            echo "<td>$columna</td>";
        } else {
            echo "<td><img src=\"$columna\" />";
        }
    }
    foreach ($sfCountryList as $p) echo "<td>$p</td>";

}
echo "</table>";

echo "<hr/><h3>Log:</h3>";
echo "<textarea cols=50 rows=10>$log</textarea>";

?>