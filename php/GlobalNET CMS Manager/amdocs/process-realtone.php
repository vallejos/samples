<?php

$debug = TRUE;
$total = 0;
$arrayMuestra = array();

$tmpDir = ($isPoly) ? TMP_DIR_PT : TMP_DIR_RT;
$type = ($isPoly) ? MIG_POLYTONE : MIG_REALTONE;

if ($tipocarga == "update") {
    $xmlFile = "metadata.xml";
    $zipFile = "Wazzup_".$type."_".date("Ymd")."_update.zip";
} else {
    $xmlFile = "metadata.xml";
    $zipFile = "Wazzup_".$type."_".date("Ymd").".zip";
}

$SFTitle = array("Content Title","Website Category","Genero","Brand (Creator)",
    "AR","BR","CL","CO","EC","GT","HN","JM","MX","NI","PA","PY","PE","PR","DOM","SAL","UY");

$sessDir = $tmpDir."/".$_SESSION["folder"];
if (!file_exists($sessDir)) exec("mkdir $sessDir");
$tmpDir = $sessDir;

if (!is_writable($tmpDir)) die("ERROR: ".$tmpDir." is not writable\n");
else {
    $log .= "borrando tmp dir\n";
    exec("rm -rf ".$tmpDir."/*");
}

// si es update de metadata, va todo en un folder nuevo
//if ($tipoCarga == "update") $sessDir .= "/metadata_update";
if (!file_exists($sessDir)) exec("mkdir $sessDir");

if (!is_writable(ZIP_DIR)) die("ERROR: ".ZIP_DIR." is not writable\n");

if ($tipoCarga == "new") {
        //------------------------
        // si es contenido nuevo
        //------------------------
        foreach ($listaIds as $i => $contentId) {
            $contentId = trim($contentId);
            $log .= "\tprocessando $contentId con rating $rating \n";

            $realtone = new migRealtone($dbc, $debug,$catLvl, $webCat, $isPoly, $rating, $marca, $festivo);
            try {
                $realtone->loadContent($contentId);
            } catch (Exception $e) {
                $log .= "loadContent: ".$e->getMessage()."\n";
            }

            $realtone->setTag($catmig);
            $realtone->setSubTag($subcatmig);
            $realtone->setLangs(array_keys($idiomas_elegidos));
            $realtone->setMerchants(array_keys($paises_elegidos));

            //$realtone->setKeywords($keywords[$i]);
           // $realtone->setShortDesc($shortDesc[$i]);
           // $realtone->setLongDesc($longDesc[$i]);

            try {
                $content_download = FALSE;
                $log .= "\tdescargando contenido...\n";
                $ftpCon = new Ftp();
                $conectado = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
                if ($conectado === TRUE) {
                    $content_download = $realtone->downloadContent($ftpCon);
                } else {
                    echo "ERROR: no se puede loguear al ftp\n";
                }

                if ($content_download === TRUE) {
                    $xmlContent = $realtone->genXML();
                    $arrayMuestra[] = $realtone->getArraySubForm();
                    $total++;
                    $written = file_put_contents($realtone->getDirToWrite(). "/" .$xmlFile, $xmlContent);
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
                $zipFile = "Wazzup_".$type."_".date("Ymd")."_$i" . "_" .".zip";
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
            $log .= "\tprocessando $contentId  \n";

            $realtone = new migRealtone($dbc, $debug,"", "", $isPoly);
            try {
                $realtone->loadContent($contentId);
            } catch (Exception $e) {
                $log .= "loadContent: ".$e->getMessage()."\n";
            }

            try {
                $realtone->setLangs(array_keys($idiomas_elegidos));
                $realtone->setMerchants(array_keys($paises_elegidos));
                $realtone->setTag($catmig);
                $realtone->setSubTag($subcatmig);
                $realtone->setTag($catmigen, 'en');
                $realtone->setSubTag($subcatmigen, 'en');
                $xmlContent = $realtone->updateXML();
                $arrayMuestra[] = $realtone->getArraySubForm();
                $total++;
                $d2 = $tmpDir."/$contentId/";
                $realtone->setDirToWrite($d2);
                if (!file_exists($d1)) exec("mkdir $d1");
                if (!file_exists($d2)) exec("mkdir $d2");
                $written = file_put_contents($realtone->getDirToWrite().$xmlFile, $xmlContent);
            } catch (Exception $e) {
                $log .= "updateXML: ".$e->getMessage()."\n";
            }
        }

        // genero nombre para el zip
        $i=0;
        $existe = TRUE;
        while ($existe === TRUE) {
            if (file_exists(ZIP_DIR."/$zipFile")) {
                $i++;
                $zipFile = "Wazzup_".$type."_".date("Ymd")."_$i" . "_update" .".zip";
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
//echo "generado <a href='".ZIP_DIR."/$zipFile'>".ZIP_DIR."/$zipFile</a> con $total contenidos, <b>$ds</b><br/>\n";
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