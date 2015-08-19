<?php

$debug = TRUE;
$total = 0;
$content_download = false;
$arrayMuestra = array();

//$xmlFile = date("Ymd")."_Wazzup_".MIG_WALLPAPER."_asset.xml";
$xmlFile = "metadata.xml";
$zipFile = "Wazzup_".MIG_WALLPAPER."_".date("Ymd").".zip";

$tmpDir = TMP_DIR_WP;
$sessDir = $tmpDir."/".$_SESSION["folder"];
if (!file_exists($sessDir)) exec("mkdir $sessDir");
$tmpDir = $sessDir;
$SFTitle = array("Content Title","Website Category","Genero","Brand (Creator)","Thumbnail",
    "AR","BR","CL","CO","EC","GT","HN","JM","MX","NI","PA","PY","PE","PR","DOM","SAL","UY");

if (!is_writable($tmpDir)) die("ERROR: ".$tmpDir." is not writable\n");
else {
    $log .= "borrando tmp dir\n";
    exec("rm -rf ".$tmpDir."/*");
}

if (!is_writable(ZIP_DIR)) die("ERROR: ".ZIP_DIR." is not writable\n");

foreach ($listaIds as $i => $contentId) {
    $contentId = trim($contentId);
    $log .= "\tprocesando $contentId\n";

    $wallpaper = new migWallpaper($dbc, $debug, $catLvl, $webCat, $rating);
    try {
        $wallpaper->loadContent($contentId);
    } catch (Exception $e) {
        $log .= "loadContent: ".$e->getMessage()."\n";
    }

    $wallpaper->setTag($catmig);
    $wallpaper->setSubTag($subcatmig);
    //$wallpaper->setKeywords($keywords[$i]);
    //$wallpaper->setShortDesc($shortDesc[$i]);
   // $wallpaper->setLongDesc($longDesc[$i]);

    try {
        $content_download = FALSE;
        // descargo contenido por FTP
        $log .= "\tdescargando contenido...\n";
        $ftpCon = new Ftp();
        $retries = 0;
        $conectado = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
        $todos = true;
        if ($conectado === TRUE) {
            $result = $wallpaper->downloadContent($ftpCon);
            foreach($result as $size => $data){
                $status = $data['status'];
                $__contentId = $data['id'];
                $todos = ($todos && $status);
                $statusStr = ($status) ? "fue bajada correctamente" : "no fue encontrada";
                $statusColor = ($status) ? "black" : "red";
                $content_download = ($content_download || $status);
                echo "<font color=\"$statusColor\">La imagen ($__contentId) con tama&ntilde;o $size $statusStr </font><br>\n";
            }
        } else {
            echo "ERROR: no se puede loguear al ftp\n";
        }

        if ($content_download === TRUE) {
            $xmlContent = $wallpaper->genXML();
            $arrayMuestra[] = $wallpaper->getArraySubForm();
            $total++;
            $written = file_put_contents($wallpaper->getDirToWrite(). "/" .$xmlFile, $xmlContent);
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
        //$zipFile = date("Ymd")."_Wazzup_".WAZZUP_WALLPAPER."_$i.zip";
        $zipFile = "Wazzup_".MIG_WALLPAPER."_".date("Ymd")."_$i" . "_" .".zip";
    } else {
        $existe = FALSE;
    }
}

// zipeo y muevo a carpeta de "envios"
$shellCmd = "cd ".$tmpDir."; zip -r ../../../".ZIP_DIR."/$zipFile * ";
$log .= exec ($shellCmd);
$log .= "\tZip ".ZIP_DIR."/$zipFile generado exitosamente\n";
//$log .= exec("rm -fr ".$tmpDir . "/*");

// chequeo zip filesize
$ds = filesize(ZIP_DIR."/$zipFile");
$ds = formatBytes($ds);
//echo "generado <a href='".ZIP_DIR."/$zipFile'>".ZIP_DIR."/$zipFile</a> con $total contenidos, <b>$ds</b><br/>\n";
echo "<div style=\"margin: 0 auto; text-align: center; font-size: 20px\">Nombre del Zip generado ".ZIP_DIR."/<a href='".ZIP_DIR."/$zipFile'>$zipFile</a> con $total contenidos, <b>$ds</b></div><br/>\n";

if(!$todos){
    echo "<div style=\"margin: 0 auto; text-align: center; font-size: 24px; color: red; font-weight: bold;\">Faltaron formatos de imagenes por bajar, revisar log</div><br />";
}

echo "<hr/><h3>Submission Form:</h3>";
/*echo "<textarea cols=200 rows=10>";
foreach($arrayMuestra as $linea){
    $linea['Zip File Name'] = $zipFile;
    echo implode("\t", $linea) . "\r\n";
}
echo "</textarea>";*/
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
        if($key !== "Thumbnail" && $key !== "Preview"){
            echo "<td valign=\"top\">$columna</td>";
        } else {
            echo "<td><img src=\"$sessDir/$columna\" /></td>";
        }
    }
    foreach ($sfCountryList as $p) echo "<td>$p</td>";

}
echo "</table>";



echo "<hr/><h3>Log:</h3>";
echo "<textarea cols=50 rows=10>$log</textarea>";

?>