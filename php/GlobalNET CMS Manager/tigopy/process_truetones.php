<?php

// content id - vienen por get/post/whatever de la herramienta
//$ids = array(6995,6996,6997,6998,7000,7001,7003,7004,7005,7006,14791,14792,14796,14797,14798,14799,14800,14803,14802,14801,14804);

// includes
include_once($globalIncludeDir."/classes/truetone.class.php");

$USE_LOCAL_FTP = TRUE;

kimport("kmail");

$ids = $listaIds;
$work_dir = TMP_DIR_RT."/".$_SESSION["folder"];
$sessDir = $work_dir;
if (!file_exists($sessDir)) exec("mkdir $sessDir");
$targetDir = $work_dir;

$debug = new kmail("TIGO.PY CONTENT MP3");
$debug->set_tracking();
$debug->set_logfile($globalIncludeDir."/logs/mp3");
$debug->add("\n--------------------------------------------\nStarting...");

$oDbc = new konexion("Web");
$debug->add("oDbc: ".var_export($oDbc, TRUE));

$DEBUG_LVL = 2;
$todo = count($ids);

($USE_LOCAL_FTP === TRUE) ? $debug->add("Using Local FTP - 241") : $debug->add("Using Remote FTP - USA");
$debug->add("Preparing to process $todo contents...");

// borro work_dir
$debug->add("Cleaning working dir $work_dir...");
exec("rm -rf $work_dir/*");
$i = 0;
$j = 0;
foreach ($ids as $id) {
    $i++;
    $debug->add("******* Processing content #$id, $i of $todo...");
    $map = $xml_map["mp3_tpl"];
    $wp = new mp3($oDbc, $id);
    $wp->load($workingCat, $workingSubCat);

    // copio archivos
    $debug->add("## Copying mp3 sources...");
    $download_ok = FALSE;
    $file = $wp->icons;
    $debug->add("Downloading $file...to " . "$work_dir/".$wp->filename.".mp3");
    if (get_preview($file, "$work_dir/".$wp->filename.".mp3") === TRUE) {
        $debug->add("Download OK!");
        $download_ok = TRUE;
    } else {
        $debug->add("Download ERROR!!!");
    }

    if ($download_ok === TRUE) {
        $debug->add("Success!!! " . $wp->filename.".mp3");
        $wp->add("objects", "<file>" . $wp->filename.".mp3</file>\n");
    } else {
        $debug->add("Skipping processing $id");
    }

    $f2p = "$work_dir/".$wp->filename.".mp3";
//var_dump($f2p);
    $debug->add("## Copying wap previews...");
    foreach ($mp3_wappreview_formats as $dimm => $active) {
        $download_ok = FALSE;
        if ($active == "1") {
            $debug->add("Downloading $dimm...");
            $archive_name = basename($wp->icons);
            $archive_name = str_replace(".mp3", "", $archive_name);
            $archive_name = str_replace(".wma", "", $archive_name);

            if($dimm == "mp3"){
                $file =  $wp->icons;
            } else {
//                $file =  "/mp3/_wmaPreview/$archive_name.wma";
            }
            //$file = $wp->icons;

            $file_dest = ($dimm == "mp3") ? $wp->filename."_prev.$dimm" : $wp->filename.".$dimm";

            $fileToPost = "$work_dir/".$wp->filename.".mp3";
            echo "$f2p => http://10.0.1.36/tools/scripts/convertir_a_WMA.php = <br>";
     
            $result = postFile($f2p, "http://10.0.1.36/tools/scripts/convertir_a_WMA.php");
//            $result = postFile($f2p, "http://10.0.0.250/tp.php");
//            $result = postFile($file, "http://10.0.0.250/tools/");

            var_dump($result);
            
            
            die();


            if (get_preview($file, "$work_dir/$file_dest") === TRUE) {
                $debug->add("Download OK!");
                $download_ok = TRUE;
            } else {
                $debug->add("Download ERROR!!!");
            }

            if ($download_ok === TRUE) {
                $debug->add("Success!!! - $dimm");
                $wp->add("wappreview", "<file>$file_dest</file>\n");
            } else {
                $debug->add("Skipping processing $dimm");
            }
        } else {
            $debug->add("Skipping $dimm.");
        }
    }

//    $debug->add("Cleaning up dir...");
  //  exec("rm -rf $work_dir/".$wp->filename.".mp3");

    // escribo xml
    $data = map($wp, $map, "mp3_tpl.xml");
    $xmlresult = NULL;
    $xml_fname = $wp->filename.".xml"; // <-- nombre para el xml
    $fxml = fopen($work_dir."/".$xml_fname, "a");
    if ($fxml) {
        $debug->add("Writing XML file $work_dir/$xml_fname");
        $xmlresult = fwrite($fxml, $data);
        fclose($fxml);
    } else {
        $debug->add("ERROR WRITING XML");
    }

    if (($xmlresult != NULL) && ($xmlresult === FALSE)) {
        $debug->add("ERROR creating XML, cannot write to file $work_dir/$xml_fname");
    } else if ($xmlresult != NULL) {
        $debug->add("Succesfully created!");
    }

    // insert db para tracking de operacion

    // check para zip size
    $must_split = FALSE;
    $hdFreeSpace = trim(str_replace($work_dir, "", system("du $work_dir")));
    if ($hdFreeSpace >= $MAX_ZIP_SIZE) $must_split = TRUE;

    if ($must_split === TRUE) {
        $j++;
        $debug->add("MAX ZIP Size reached!!! Splitting...");
        $debug->add("Creating ZIP Volume file...");

        $existe = TRUE;
        while ($existe === TRUE) {
            if (file_exists(ZIP_DIR."/$zipFile")) {
                $i++;
                $zipFile = "Wazzup_".MIG_REALTONE."_".date("Ymd")."_$i" . "_" .".zip";
            } else {
                $existe = FALSE;
            }
        }

        // zipeo y muevo a carpeta de "envios"
        $shellCmd = "cd ".$work_dir."; zip -r ../../../".ZIP_DIR."/$zipFile * ";
        $log .= exec ($shellCmd);
        $log .= "\tZip ".ZIP_DIR."/$zipFile generado exitosamente\n";

        // chequeo zip filesize
        $ds = filesize(ZIP_DIR."/$zipFile");
        $ds = formatBytes($ds);
        echo "<div style=\"margin: 0 auto; text-align: center; font-size: 20px\">Nombre del Zip generado ".ZIP_DIR."/<a href='".ZIP_DIR."/$zipFile'>$zipFile</a> con $total contenidos, <b>$ds</b></div><br/>\n";

        $must_split = FALSE;

        $debug->add("Cleaning up previous volume data...");
        exec("rm -rf $work_dir/*");
    }
}


/*
// zipeo
$debug->add("Creating ZIP file...");
$zipname = "mp3_globalnet.zip";
$cmd = "cd $work_dir\n
            zip $zipname ./* \n";
exec($cmd);

// muevo zip
$debug->add("Moving ZIP file...");
if(!copy("$work_dir/$zipname", "$zip_path/$zipname")) {
    $debug->add("Could not copy ZIP file $work_dir/$zipname => $zip_path/ ");
} else {
    $debug->add("ZIP copied.");
}

if ($DEBUG_LVL > 1) $debug->send("Ending.\n--------------------------------------------\n\n");
*/

// genero nombre para el zip
$existe = TRUE;
while ($existe === TRUE) {
    if (file_exists(ZIP_DIR."/$zipFile")) {
        $i++;
        //$zipFile = date("Ymd")."_Wazzup_".MIG_FULLTRACK."_$i.zip";
        //$zipFile = "Wazzup_".MIG_VIDEO."_".date("Ymd").".zip";
        $zipFile = "Wazzup_".MIG_REALTONE."_".date("Ymd")."_$i" . "_" .".zip";
    } else {
        $existe = FALSE;
    }
}

// zipeo y muevo a carpeta de "envios"
$shellCmd = "cd ".$work_dir."; zip -r ../../../".ZIP_DIR."/$zipFile * ";
$log .= exec ($shellCmd);
$log .= "\tZip ".ZIP_DIR."/$zipFile generado exitosamente\n";

// chequeo zip filesize
$ds = filesize(ZIP_DIR."/$zipFile");
$ds = formatBytes($ds);
echo "<div style=\"margin: 0 auto; text-align: center; font-size: 20px\">Nombre del Zip generado ".ZIP_DIR."/<a href='".ZIP_DIR."/$zipFile'>$zipFile</a> con $total contenidos, <b>$ds</b></div><br/>\n";

if ($DEBUG_LVL > 1) $debug->send("Ending.\n--------------------------------------------\n\n");


?>