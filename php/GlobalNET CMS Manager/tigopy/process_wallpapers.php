<?php

// content id - vienen por get/post/whatever de la herramienta
//$ids =  array(27671,27666,18791,18792,18793,18794,18796,18797,27662,27660,27658,27656,27637,27636,27605,27604,27603,6251,6240,6703);

$ids = $listaIds;
$work_dir = TMP_DIR_WP."/".$_SESSION["folder"];
$sessDir = $work_dir;
if (!file_exists($sessDir)) exec("mkdir $sessDir");
$targetDir = $work_dir;

//$ids = array(22229);

$USE_LOCAL_FTP = TRUE;

// includes
include_once($globalIncludeDir."/classes/wallpaper.class.php");

kimport("kmail");

$debug = new kmail("TIGO.PY CONTENT WALLPAPER");
$debug->set_tracking();
$debug->set_logfile("logs/wallpapers");
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
	$map = $xml_map["wallpaper_tpl"];
	$wp = new wallpaper($oDbc, $id);
	$wp->load($workingCat, $workingSubCat);

	// copio archivos
	$debug->add("## Copying img sources...");
	foreach ($wallpaper_dimensions as $dimm => $active) {
		$download_ok = FALSE;
		if ($active == "1") {
			$debug->add("Downloading $dimm...");
			
			if ($dimm == "176x144") {
				$file = str_replace("176x145", $dimm, $wp->icons);
				if (get_preview($file, "$work_dir/".$wp->filename.".jpg") === TRUE) {
					$debug->add("Download OK!");
					$download_ok = TRUE;
				} else {
					$debug->add("No 2nd alternative for 176x144.");
					$debug->add("Giving up :(");
				}
			} else {
	//			$file = str_replace("320x320", $dimm, $wp->icons);
				$file = str_replace("600x600", $dimm, $wp->icons);
				if (get_preview($file, "$work_dir/".$wp->filename.".jpg") === TRUE) {
					$debug->add("Download OK!");
					$download_ok = TRUE;
				} else {
					$debug->add("Download ERROR!!!");
					$debug->add("Forcing 320x320...");
					if (get_preview($wp->icons, "$work_dir/".$wp->filename.".jpg") === TRUE) {
						$debug->add("Alternative 320x320 download OK!");
						$download_ok = TRUE;
					} else {
						$debug->add("Alternative download ERROR!!!");
						$debug->add("2nd attempt... forcing 128x128...");
						if (get_preview(str_replace("320x320", "128x128", $wp->icons), "$work_dir/".$wp->filename.".jpg") === TRUE) {
							$debug->add("Alternative 128x128 download OK!");
							$download_ok = TRUE;
						} else {
							$debug->add("2nd alternative download ERROR!!!");
							$debug->add("Giving up :(");
						}
					}
				}
			}

			if ($download_ok === TRUE) {
				// FORMATOS - GIF,JPEG,PNG,BMP
				$debug->add("Formatting $work_dir/".$wp->filename.".jpg");
				foreach ($wallpaper_formats as $format => $factive) {
					if ($factive == "1") {
						list ($width,$height) = explode("x", $dimm);

						$debug->add("Creating $format $width"."x$height");

						//copy_format_img($img_source, $width, $height, $watermark_logo="",$ext="",$background="");
						if (copy_format_img($wp->filename, $width, $height, "",".jpg",".$format","#FFFFFF") === FALSE) {
							$debug->add("ERROR creating $format $width"."x$height");
						} else {
							$debug->add("Success!!!");
							$wp->add("objects", "<file_$dimm>".$wp->filename."_$dimm.$format</file_$dimm>\n");
						}

					} else {
						$debug->add("Skipping processing $format, $dimm");
					}
				}
			} else {
				$debug->add("Skipping processing $dimm");
			}
		} else {
			$debug->add("Skipping $dimm.");
		}
	}


	$debug->add("## Copying web previews...");
	foreach ($wallpaper_dimensions_web_preview as $dimm => $active) {
		$download_ok = FALSE;
		if ($active == "1") {
			$debug->add("Downloading $dimm...");
			$file = str_replace("320x320", $dimm, $wp->icons);
			if (get_preview($file, "$work_dir/".$wp->filename.".jpg") === TRUE) {
				$debug->add("Download OK!");
				$download_ok = TRUE;
			} else {
				$debug->add("Download ERROR!!!");
				$debug->add("Forcing 128x128...");
				if (file_exists("$work_dir/".$wp->filename.".jpg")) {
					$debug->add("Detected previous download.");
					$debug->add("Using $work_dir/".$wp->filename.".jpg");
				} else {
					if (get_preview(str_replace("320x320", "128x128", $wp->icons), "$work_dir/".$wp->filename.".jpg") === TRUE) {
						$debug->add("Alternative 128x128 download OK!");
						$download_ok = TRUE;
					} else {
						$debug->add("Alternative download ERROR!!!");
						$debug->add("Giving up :(");
					}
				}
			}

			if ($download_ok === TRUE) {
				// FORMATOS - GIF,JPEG,PNG,BMP
				$debug->add("Formatting $work_dir/".$wp->filename.".jpg");
				foreach ($wallpaper_formats as $format => $factive) {
					if ($factive == "1") {
						list ($width,$height) = explode("x", $dimm);

						$debug->add("Creating $format $width"."x$height");

						//copy_format_img($img_source, $width, $height, $watermark_logo="",$ext="",$background="");
						if (copy_format_img($wp->filename, $width, $height, "",".jpg",".$format","#FFFFFF") === FALSE) {
							$debug->add("ERROR creating $format $width"."x$height");
						} else {
							$debug->add("Success!!!");
							$wp->add("webpreview", "<file_$dimm>".$wp->filename."_$dimm.$format</file_$dimm>\n");
						}

					} else {
						$debug->add("Skipping processing $format, $dimm");
					}
				}
			} else {
				$debug->add("Skipping processing $dimm");
			}
		} else {
			$debug->add("Skipping $dimm.");
		}
	}


	$debug->add("## Copying wap previews...");
	foreach ($wallpaper_dimensions_wap_preview as $dimm => $active) {
		$download_ok = FALSE;
		if ($active == "1") {
			$debug->add("Downloading $dimm...");
			$file = str_replace("320x320", $dimm, $wp->icons);
			if (get_preview($file, "$work_dir/".$wp->filename.".jpg") === TRUE) {
				$debug->add("Download OK!");
				$download_ok = TRUE;
			} else {
				$debug->add("Download ERROR!!!");
				$debug->add("Forcing 128x128...");
				if (file_exists("$work_dir/".$wp->filename.".jpg")) {
					$debug->add("Detected previous download.");
					$debug->add("Using $work_dir/".$wp->filename.".jpg");
				} else {
					if (get_preview(str_replace("320x320", "128x128", $wp->icons), "$work_dir/".$wp->filename.".jpg") === TRUE) {
						$debug->add("Alternative 128x128 download OK!");
						$download_ok = TRUE;
					} else {
						$debug->add("Alternative download ERROR!!!");
						$debug->add("Giving up :(");
					}
				}
			}

			if ($download_ok === TRUE) {
				// FORMATOS - GIF,JPEG,PNG,BMP
				$debug->add("Formatting $work_dir/".$wp->filename.".jpg");
				foreach ($wallpaper_formats as $format => $factive) {
					if ($factive == "1") {
						list ($width,$height) = explode("x", $dimm);

						$debug->add("Creating $format $width"."x$height");

						//copy_format_img($img_source, $width, $height, $watermark_logo="",$ext="",$background="");
						if (copy_format_img($wp->filename, $width, $height, "",".jpg",".$format","#FFFFFF") === FALSE) {
							$debug->add("ERROR creating $format $width"."x$height");
						} else {
							$debug->add("Success!!!");
							$wp->add("wappreview", "<file_$dimm>".$wp->filename."_$dimm.$format</file_$dimm>\n");
						}

					} else {
						$debug->add("Skipping processing $format, $dimm");
					}
				}
			} else {
				$debug->add("Skipping processing $dimm");
			}
		} else {
			$debug->add("Skipping $dimm.");
		}
	}

	$debug->add("Cleaning up dir...");
	exec("rm -rf $work_dir/".$wp->filename.".jpg");

	// escribo xml
	$data = map($wp, $map, "wallpaper_tpl.xml");
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
		$zipname = "wallpaper_globalnet".$j.".zip";
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
        $zipFile = "Wazzup_".MIG_WALLPAPER."_".date("Ymd")."_$i" . "_" .".zip";
    } else {
        $existe = FALSE;
    }
}

// zipeo y muevo a carpeta de "envios"
$shellCmd = "cd ".$work_dir."; zip -r ../../../".ZIP_DIR."/$zipFile * ";
$log .= exec ($shellCmd);
$log .= "\tZip ".$work_dir."/$zipFile generado exitosamente\n";

echo $shellCmd;

// chequeo zip filesize
$ds = filesize(ZIP_DIR."/$zipFile");
$ds = formatBytes($ds);
echo "<div style=\"margin: 0 auto; text-align: center; font-size: 20px\">Nombre del Zip generado ".ZIP_DIR."/<a href='".ZIP_DIR."/$zipFile'>$zipFile</a> con $total contenidos, <b>$ds</b></div><br/>\n";


if ($DEBUG_LVL > 1) $debug->send("Ending.\n--------------------------------------------\n\n");

?>
