<?php

// content id - vienen por get/post/whatever de la herramienta
//$ids = array();

// ESTE NO ANDA, TIRA EL SQL> 21327
//$ids = array(1329,21304,21161,20684,20214,18932,18739,18416);


$ids = $listaIds;
$work_dir = TMP_DIR_JG."/".$_SESSION["folder"];
$sessDir = $work_dir;
if (!file_exists($sessDir)) exec("mkdir $sessDir");
$targetDir = $work_dir;

$USE_LOCAL_FTP = FALSE;

// includes
include_once($globalIncludeDir."/classes/game.class.php");

kimport("kmail");

$debug = new kmail("TIGO.PY CONTENT GAME");
$debug->set_tracking();
$debug->set_logfile("logs/games");
$debug->add("\n--------------------------------------------\nStarting...");

$oDbc = new konexion("Web");
$debug->add("oDbc: ".var_export($oDbc, TRUE));

$DEBUG_LVL = 2;
$todo = count($ids);

($USE_LOCAL_FTP === TRUE) ? $debug->add("Using Local FTP - 241") : $debug->add("Using Remote FTP - USA");
$debug->add("Preparing to process $todo contents...");

$i = 0;
$j = 0;
foreach ($ids as $id) {
	// borro work_dir
	$debug->add("Cleaning working dir $work_dir...");
	exec("rm -rf $work_dir/*");

	$i++;
	$debug->add("******* Processing content #$id, $i of $todo...");
	$map = $xml_map["game_tpl"];
	$g = new game($oDbc, $id, $uaTigo);
	$g->load($workingCat, $workingSubCat);

	$debug->add("Preparing directory structure...");
	$targetDir = "$work_dir/".$g->filename;
	mkdir($targetDir);
//	mkdir("$targetDir/".$g->filename);
	mkdir("$targetDir/all");
//	$targetDir = "$targetDir/".$g->filename;

	// copio archivos
	$debug->add("## Copying img sources...");

	$download_ok = FALSE;
	$debug->add("Downloading source img...");
	$file = $g->icons;
	if (get_preview($file, "$targetDir/".$g->filename.".gif") === TRUE) {
		$debug->add("Download OK!");
		$download_ok = TRUE;
	} else {
		$debug->add("Download ERROR!!!");
		$debug->add("Giving up :( ");
	}

	if ($download_ok === TRUE) {
		// WEB PREVIEWS
		$debug->add("Formatting $targetDir/".$g->filename.".gif");
		foreach ($game_dimensions_web_preview as $dimm => $dactive) {
			if ($dactive == "1") {
				// FORMATOS - GIF,JPEG,PNG,BMP
				$debug->add("Formatting $targetDir/".$g->filename.".gif");
				foreach ($game_webpreview_formats as $format => $factive) {
					if ($factive == "1") {
						list ($width,$height) = explode("x", $dimm);

						$debug->add("Creating $format $width"."x$height");

						if (copy_format_img($g->filename, $width, $height, "",".gif",".$format","#FFFFFF") === FALSE) {
							$debug->add("ERROR creating $format $width"."x$height");
						} else {
							$debug->add("Success!!!");
							$g->add("webpreview", "<file_$dimm>".$g->filename."_$dimm.$format</file_$dimm>\n");
						}

					} else {
						$debug->add("Skipping processing $format, $dimm");
					}
				}
			} else {
				$debug->add("Skipping $dimm.");
			}
		}

		// WAP PREVIEWS
		$debug->add("Formatting $targetDir/".$g->filename.".gif");
		foreach ($game_dimensions_wap_preview as $dimm => $dactive) {
			if ($dactive == "1") {
				// FORMATOS - GIF,JPEG,PNG,BMP
				$debug->add("Formatting $targetDir/".$g->filename.".gif");
				foreach ($game_wappreview_formats as $format => $factive) {
					if ($factive == "1") {
						list ($width,$height) = explode("x", $dimm);

						$debug->add("Creating $format $width"."x$height");

						if (copy_format_img($g->filename, $width, $height, "",".gif",".$format","#FFFFFF") === FALSE) {
							$debug->add("ERROR creating $format $width"."x$height");
						} else {
							$debug->add("Success!!!");
							$g->add("wappreview", "<file_$dimm>".$g->filename."_$dimm.$format</file_$dimm>\n");
						}

					} else {
						$debug->add("Skipping processing $format, $dimm");
					}
				}
			} else {
				$debug->add("Skipping $dimm.");
			}
		}

		$debug->add("Switching to Local FTP - 241");
		$USE_LOCAL_FTP = TRUE;

		$jad_download_ok = FALSE;
		$debug->add("Downloading JADS to $targetDir");
		foreach ($g->jads as $file) {
			if (get_preview(str_replace("/netuy", "", $file), "$targetDir/all/".basename($file)) === TRUE) {
				$debug->add(" > ".basename($file)." JAD Download OK!");
				$jad_download_ok = TRUE;
			} else {
				$debug->add(" > ".basename($file)." JAD Download ERROR!!!");
				$debug->add("Giving up :( ");
			}
		}

		$jar_download_ok = FALSE;
		$debug->add("Downloading JARS to $targetDir");
		foreach ($g->jars as $file) {
			if (get_preview(str_replace("/netuy", "", $file), "$targetDir/all/".basename($file)) === TRUE) {
				$debug->add(" > ".basename($file)." JAR Download OK!");
				$jar_download_ok = TRUE;
			} else {
				$debug->add(" > ".basename($file)." JAR Download ERROR!!!");
				$debug->add("Giving up :( ");
			}
		}

		$debug->add("Switching to Remote FTP - USA");
		$USE_LOCAL_FTP = FALSE;

		$uaOnlyOnceh4x = array();
		$moved_first = FALSE;
		foreach ($g->jads as $file) {
			$fname = basename($file, ".jad");
			if (file_exists("$targetDir/all/$fname.jad") && file_exists("$targetDir/all/$fname.jar")) {
				$debug->add("JAD/JAR $fname OK!!");

				if (!in_array($file, $uaOnlyOnceh4x)) {
					if ($moved_first === FALSE) {
						$debug->add("Applying Crystal h4x0r #1");
						exec("mv $targetDir/all/$fname.jad $targetDir/$fname.jad");
						exec("mv $targetDir/all/$fname.jar $targetDir/$fname.jar");

						$handset_template = "<handset>
								<device>
								<ua>".substr($g->uaFiles[$fname],0,-1)."</ua>
								<file>".$fname.".jar</file>
								<file>".$fname.".jad</file>
								<screenshots>
									<file>".$g->filename."_96x96.jpg</file>
									<file>".$g->filename."_50x50.gif</file>
									<file>".$g->filename."_96x96.gif</file>
								</screenshots>
								</device>
								</handset>\n";
						$moved_first = TRUE;
					} else {

						$handset_template = "<handset>
								<device>
								<ua>".substr($g->uaFiles[$fname],0,-1)."</ua>
								<file>all\\".$fname.".jar</file>
								<file>all\\".$fname.".jad</file>
								<screenshots>
									<file>".$g->filename."_96x96.jpg</file>
									<file>".$g->filename."_50x50.gif</file>
									<file>".$g->filename."_96x96.gif</file>
								</screenshots>
								</device>
								</handset>\n";
					}
				$uaOnlyOnceh4x[] = $file;
				$g->add("handsets", "$handset_template\n");
				} else {
					$debug->add("Skipping $file useragents (already in handsets)");
				}
			} else {
				$debug->add("Skipping $targetDir/$fname... EPIC FAIL!!!!");
			}
		}

	} else {
		$debug->add("Skipping $file.");
	}



	$debug->add("Cleaning up dir...");
	exec("rm -f $targetDir/".$g->filename.".gif");

	// escribo xml
	$data = map($g, $map, "game_tpl.xml");
	$xmlresult = NULL;
	$xml_fname = $g->filename.".xml"; // <-- nombre para el xml
	$fxml = fopen($targetDir."/".$xml_fname, "a");
	if ($fxml) {
		$debug->add("Writing XML file $targetDir/$xml_fname");
		$xmlresult = fwrite($fxml, $data);
		fclose($fxml);
	} else {
		$debug->add("ERROR WRITING XML");
	}

	if (($xmlresult != NULL) && ($xmlresult === FALSE)) {
		$debug->add("ERROR creating XML, cannot write to file $targetDir/$xml_fname");
	} else if ($xmlresult != NULL) {
		$debug->add("Succesfully created!");
	}

	// insert db para tracking de operacion

/*
	// check para zip size
	$must_split = FALSE;
	$hdFreeSpace = trim(str_replace($targetDir, "", system("du $targetDir")));
	if ($hdFreeSpace >= $MAX_ZIP_SIZE) $must_split = TRUE;

	if ($must_split === TRUE) {
		$j++;
		$debug->add("MAX ZIP Size reached!!! Splitting...");
		$debug->add("Creating ZIP Volume file...");
		$zipname = $g->filename."$j.zip";
		$cmd = "cd $work_dir\n
			zip -r $zipname ./".$g->filename."/* \n";
		exec($cmd);

		// muevo zip
		$debug->add("Moving ZIP file...");
		if(!copy("$work_dir/$zipname", "$zip_path/$zipname")) {
			$debug->add("Could not copy ZIP file $work_dir/$zipname => $zip_path/ ");
		} else {
			$debug->add("ZIP copied.");
		}
		$must_split = FALSE;

		$debug->add("Cleaning up previous volume data...");
		exec("rm -rf $work_dir/*");
	}
*/

// genero nombre para el zip
$i=0;
$existe = TRUE;
while ($existe === TRUE) {
    if (file_exists(ZIP_DIR."/$zipFile")) {
        $i++;
        //$zipFile = date("Ymd")."_Wazzup_".MIG_FULLTRACK."_$i.zip";
        //$zipFile = "Wazzup_".MIG_VIDEO."_".date("Ymd").".zip";
        $zipFile = "Wazzup_".MIG_GAME."_".date("Ymd")."_$i" . "_" .".zip";
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

}


if ($DEBUG_LVL > 1) $debug->send("Ending.\n--------------------------------------------\n\n");

?>