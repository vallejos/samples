<?php

// content id - vienen por get/post/whatever de la herramienta
//$ids = array();


//$ids = array(6995,6996,6997,6998,7000,7001,7003,7004,7005,7006,14791,14792,14796,14797,14798,14799,14800,14803,14802,14801,14804);
//$ids = array(14804);

$ids = $listaIds;
$work_dir = TMP_DIR_VD."/".$_SESSION["folder"];
$sessDir = $work_dir;
if (!file_exists($sessDir)) exec("mkdir $sessDir");
$targetDir = $work_dir;

$USE_LOCAL_FTP = TRUE;

// includes
include_once($globalIncludeDir."/classes/video.class.php");

kimport("kmail");

$debug = new kmail("TIGO.PY CONTENT VIDEO");
$debug->set_tracking();
$debug->set_logfile("logs/videos");
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
	$map = $xml_map["video_tpl"];
	$vid = new video($oDbc, $id);
	$vid->load($workingCat, $workingSubCat);

	// copio archivos
	$debug->add("## Copying img sources...");

	$debug->add("Switching to Remote FTP - USA");
	$USE_LOCAL_FTP = FALSE;

	$download_ok = FALSE;
	$debug->add("Downloading source img...");
	$file = $vid->icons;
	if (get_preview($file, "$work_dir/".$vid->filename.".gif") === TRUE) {
		$debug->add("Download OK!");
		$download_ok = TRUE;
	} else {
		$debug->add("Download ERROR!!!");
		$debug->add("Giving up :( ");
		echo "$id - $file (preview)\n";
	}

	if ($download_ok === TRUE) {
		$debug->add("## Creating wap previews...");
		// WEB PREVIEWS
		$debug->add("Formatting $work_dir/".$vid->filename.".gif");
		foreach ($video_dimensions_web_preview as $dimm => $dactive) {
			if ($dactive == "1") {
				// FORMATOS - GIF,JPEG,PNG,BMP
				$debug->add("Formatting $work_dir/".$vid->filename.".gif");
				foreach ($video_webpreview_formats as $format => $factive) {
					if ($factive == "1") {
						list ($width,$height) = explode("x", $dimm);

						$debug->add("Creating $format $width"."x$height");

						if (copy_format_img($vid->filename, $width, $height, "",".gif",".$format","#FFFFFF") === FALSE) {
							$debug->add("ERROR creating $format $width"."x$height");
						} else {
							$debug->add("Success!!!");
							$vid->add("webpreview", "<file_$dimm>".$vid->filename."_$dimm.$format</file_$dimm>\n");
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
		$debug->add("## Creating wap previews...");
		$debug->add("Formatting $work_dir/".$vid->filename.".gif");
		foreach ($video_dimensions_wap_preview as $dimm => $dactive) {
			if ($dactive == "1") {
				// FORMATOS - GIF,JPEG,PNG,BMP
				$debug->add("Formatting $work_dir/".$vid->filename.".gif");
				foreach ($video_wappreview_formats as $format => $factive) {
					if ($factive == "1") {
						list ($width,$height) = explode("x", $dimm);

						$debug->add("Creating $format $width"."x$height");

						if (copy_format_img($vid->filename, $width, $height, "",".gif",".$format","#FFFFFF") === FALSE) {
							$debug->add("ERROR creating $format $width"."x$height");
						} else {
							$debug->add("Success!!!");
							$vid->add("wappreview", "<file_$dimm>".$vid->filename."_$dimm.$format</file_$dimm>\n");
						}

					} else {
						$debug->add("Skipping processing $format, $dimm");
					}
				}
			} else {
				$debug->add("Skipping $dimm.");
			}
		}

		$debug->add("Switching to Remote FTP - USA");
		$USE_LOCAL_FTP = TRUE;

		$video_download_ok = TRUE;
		$debug->add("## Downloading 3gp video to $work_dir");
		foreach ($video_dimensions as $dimm => $dactive) {
			if ($dactive == "1") {
				switch ($dimm) {
					case "96x96":
						$file = "/videos/96x96_3gp/".basename($vid->video);
						break;
					case "176x144":
						$file = $vid->video;
						break;
					case "128x96":
						$folder = calcularCarpeta(basename($vid->video, ".3gp"));
						$file = "/videos/128x96/$folder/".basename($vid->video);
						break;
					default:
						// jamas deberia entrar aca
						echo "OOPS! :S\n";
				}
				if (get_preview($file, "$work_dir/".basename($vid->filename)."_$dimm.3gp") === TRUE) {
					$debug->add(" > $dimm ".basename($vid->filename)." 3gp Download OK!");
					$debug->add("Video ok, adding objects...");
					$vid->add("objects", "<file_$dimm>".$vid->filename."_$dimm.3gp</file_$dimm>\n");
				} else {
					// hax0r para 128x96, contenidos viejos video_tones
					$folder = calcularCarpeta($id);
					$file = "/videos/128x96/$folder/$id.3gp";

					if (get_preview($file, "$work_dir/".basename($vid->filename)."_$dimm.3gp") === TRUE) {
						$debug->add(" > $dimm ".basename($vid->filename)." 3gp Download OK!");
						$debug->add("Video ok, adding objects...");
						$vid->add("objects", "<file_$dimm>".$vid->filename."_$dimm.3gp</file_$dimm>\n");
					} else {
						$video_download_ok = FALSE;
						$debug->add(" > $dimm ".basename($vid->filename)." 3gp Download ERROR - $id -> $dimm!!!");
						echo "'$id' => '$dimm',\n";
						$debug->add("Giving up :( ");
					}
				}
			} else {
				$debug->add("Skipping $dimm.");
			}
		}

	} else {
		$debug->add("Skipping $file.");
	}

	$debug->add("Cleaning up dir...");
	exec("rm -f $work_dir/".$vid->filename.".gif");

	// escribo xml
	$data = map($vid, $map, "video_tpl.xml");
	$xmlresult = NULL;
	$xml_fname = $vid->filename.".xml"; // <-- nombre para el xml
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
		$zipname = "video_globalnet".$j.".zip";
		$cmd = "cd $work_dir\n
				zip -r $zipname ./* \n";
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
$shellCmd = "cd ".$work_dir."; zip -r ../../../".ZIP_DIR."/$zipFile * ";
$log .= exec ($shellCmd);
$log .= "\tZip ".ZIP_DIR."/$zipFile generado exitosamente\n";

// chequeo zip filesize
$ds = filesize(ZIP_DIR."/$zipFile");
$ds = formatBytes($ds);
echo "<div style=\"margin: 0 auto; text-align: center; font-size: 20px\">Nombre del Zip generado ".ZIP_DIR."/<a href='".ZIP_DIR."/$zipFile'>$zipFile</a> con $total contenidos, <b>$ds</b></div><br/>\n";

if ($DEBUG_LVL > 1) $debug->send("Ending.\n--------------------------------------------\n\n");

?>
