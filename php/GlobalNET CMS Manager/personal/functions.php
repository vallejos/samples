<?php


function copy_format_img($img_source, $targetDir, $width, $height, $watermark_logo="",$extOrig="",$ext="",$background="") {
	

	$id = $img_source;
	$width = (!empty($width)) ? $width : NULL;
	$height = (!empty($height)) ? $height : NULL;
	$watermark_logo = (!empty($watermark_logo)) ? $watermark_logo : "";
	$ext = (!empty($ext)) ? $ext : "";
	$extOrig = (!empty($extOrig)) ? $extOrig : ".jpg";
	$background = (!empty($background)) ? $background : NULL;
	$img = new kimage($img_source,$logo,$targetDir,$extOrig);
	if($ext!="") $img->setExtension($ext);
	return $img->getPath($width,$height,$background);
}




/**
 * Escribo el XML a disco
 *
 * @param <type> $xml
 * @param <type> $destination
 *
 */
function writeXML($xml, $destination) {
    //ESCRIBO EN DISCO
      if (!$fp = fopen("$destination", "a+")) {
        echo "Cannot open file ($destination)";
        exit;
      }
      if (fwrite($fp, $xml) === FALSE) {
        echo "Cannot write to file ($destination)";
        exit;
      }
            //      echo "Success, wrote ($somecontent) to file ($filename)";
      fclose($fp);

}

function writeZIP($targetDir, $idC, $version){
    global $log;
    $zipFile = "Wazzup_$idC"."_".$version."_".MIG_GAME."_".date("Ymd").".zip";
    // genero nombre para el zip
    $i=0;
    $existe = TRUE;
    while ($existe === TRUE) {
        if (file_exists(ZIP_DIR."/$zipFile")) {
            $i++;
            //$zipFile = date("Ymd")."_Wazzup_".WAZZUP_WALLPAPER."_$i.zip";
            $zipFile = "Wazzup_$idC"."_".$version."_".MIG_GAME."_".date("Ymd")."_$i" . "_" .".zip";
        } else {
            $existe = FALSE;
        }
    }

    // zipeo y muevo a carpeta de "envios"
    $shellCmd = "cd ".$targetDir."; zip -r ../../../../".ZIP_DIR."/$zipFile * ";
  //  echo '<li>Creando ZIP con: ' . $shellCmd.'</li>';
    $log .= exec ($shellCmd);
    echo "<li> Zip ".ZIP_DIR."/$zipFile generado exitosamente</li>";

    $ds = filesize(ZIP_DIR."/$zipFile");
    $ds = formatBytes($ds);
    return array('size' => $ds, 'path' => ZIP_DIR."/$zipFile");
}

function emptyDir($dir) {
   // echo "<li>Ejecutando: rm -f $dir</li>";
    exec("rm -rf $dir");
}


// MUST HAVE KONVERT FOR IT IS GORX
function sanitizeString($string){

    $replaceThis = array("&#38;","&#62;","&#60;","&#34;","&#180;","&#184;","&#710;","&#175;","&#183;","&#732;",
                        "&#168;","&#193;","&#225;","&#194;","&#226;","&#198;","&#230;","&#192;","&#224;","&#197;",
                        "&#229;","&#195;","&#227;","&#196;","&#228;","&#199;","&#231;","&#201;","&#233;","&#202;",
                        "&#234;","&#200;","&#232;","&#208;","&#240;","&#203;","&#235;","&#205;","&#237;","&#206;",
                        "&#238;","&#204;","&#236;","&#207;","&#239;","&#209;","&#241;","&#211;","&#243;","&#212;",
                        "&#244;","&#338;","&#339;","&#210;","&#242;","&#216;","&#248;","&#213;","&#245;","&#214;",
                        "&#246;","&#352;","&#353;","&#223;","&#222;","&#254;","&#218;","&#250;","&#219;","&#251;",
                        "&#217;","&#249;","&#220;","&#252;","&#221;","&#253;","&#255;","&#376;","&#8222;","&#171;",
                        "&#8220;","&#8249;","&#8216;","&#187;","&#8221;","&#8250;","&#8217;","&#8218;"," ","&#8722;","-",
                        "&amp;", "&#40;","&#41;","&#44;","(", ")",",",":",".",";");

    $replaceWith = array("y", "", "", "", "","","","","","","","A","a","A","a","A","a","A","a","A","a","A","a","A","a",
                        "C","c","E","e","E","e","E","e","D","d","E","e","I","i","I","i","I","i","I","i","N","n",
                        "O","o","O","o","O","o","O","o","O","o","O","o","O","o","S","s","","","","U","u","U","u",
                        "U","u","U","u","Y","y","y","Y","","","","","","","","","","","","","",
                        "","","","","","","","","","");
    $string = str_replace($replaceThis, $replaceWith, $string);
    return $string;
}


// devuelve info del contenido
function getContentDescription($contentId, $contentType, $contentCat) {
      $description = "";

      switch ($contentType) {
	    case "5":
		  $description .= "Protector de pantalla ";
	    break;
	    case "7":
		  $description .= "Wallpaper ";
	    break;
	    case "23":
		  $description .= "MP3 ";
	    break;
	    case "29":
		  $description .= "Sonido Polifonico ";
	    break;
	    case "62":
		  $description .= "Video ";
	    break;
	    case "63":
		  $description .= "Theme ";
	    break;
	    case "31":
		  $description .= "Juego java ";
	    break;
	    default:
		  $description .= "Contenido ";
      }

      return $description.getCatInformation($contentCat);
}


// devuelve info de una categoria
function getCatInformation($contentCat) {
      $description = "";

      switch ($contentCat) {
	    case "12":
		  $description .= "";
	    break;
	    default:
		  $description .= "";
      }

      return $description;
}



/**
 * Get the directory size
 * @param directory $directory
 * @return integer
 */
function dirSize($directory) {
    $size = 0;
    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
        $size+=$file->getSize();
    }
    return $size;
}


/**
 * Converts human readable file size (e.g. 10 MB, 200.20 GB) into bytes.
 *
 * @param string $str
 * @return int the result is in bytes
 * @author Svetoslav Marinov
 * @author http://slavi.biz
 */
function filesize2bytes($str) {
    $bytes = 0;

    $bytes_array = array(
        'B' => 1,
        'KB' => 1024,
        'MB' => 1024 * 1024,
        'GB' => 1024 * 1024 * 1024,
        'TB' => 1024 * 1024 * 1024 * 1024,
        'PB' => 1024 * 1024 * 1024 * 1024 * 1024,
    );

    $bytes = floatval($str);

    if (preg_match('#([KMGTP]?B)$#si', $str, $matches) && !empty($bytes_array[$matches[1]])) {
        $bytes *= $bytes_array[$matches[1]];
    }

    $bytes = intval(round($bytes, 2));

    return $bytes;
}


function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}




function getJarNamefromJad($jadLines) {
	$jarName = "";
	foreach ($jadLines as $linNum => $linContent) {
		$linContent = trim($linContent);
		if (substr($linContent, -4) == ".jar") {
			$jarName = trim(str_replace("MIDlet-Jar-URL:", "", $linContent));
		}
	}
	return $jarName;
}


function getArchivo($dbc, $pId, $contentId) {
	$sql = "SELECT archivo FROM Web.gamecomp WHERE juego=$contentId AND celular=$pId ";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) return FALSE;

	$obj = mysql_fetch_object($rs);
	return $obj->archivo;
}


function getAllModels($dbc, $archivo) {
	$aModels = array();
        $sql = "SELECT celular FROM Web.gamecomp WHERE archivo='$archivo' ";
        //echo $sql;
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) return FALSE;

	while($obj = mysql_fetch_object($rs)) {
		$aModels[] = $obj->celular;
	}
	return $aModels;
}


function generateVariantXml($dbc, $idC, $archivo, $uniqueId, $models) {
//	$archivo = loadGameContent($dbc, $idC, $idCel);

	if ($archivo !== FALSE) {
//		$downloaded = getFtpFiles($archivo);

		$info = pathinfo($archivo);
		$toJad = $info['basename'];
		$toJar = str_replace(".jad", ".jar", $toJad);

//		if ($downloaded === TRUE) {
			$migModels = addDevicesToVariant($xml, $models);

			if (!empty($migModels)) {
//				$xmlVariant = "\n\t<premium>\n";
				$xmlVariant =<<<XML
		<jarResource>
			<qpass:resourceFilename>$toJar</qpass:resourceFilename>
			<qpass:descriptorFilename>$toJad</qpass:descriptorFilename>
			<qpass:mimeType>application/java-archive</qpass:mimeType>
XML;
				$xmlVariant .= "\n".$migModels;
				$xmlVariant .= "\t\t</jarResource>\n";
//				$xmlVariant .= "\t</premium>";


//				$xmlVariant = "\t\t\t\t\t<qpass:deviceId>samsung_sgh_e496_ver1</qpass:deviceId>\n";

			}
/*
		} else {
			echo "Cannot download $archivo";
			$xmlVariant = FALSE;
		}
*/

	} else {
		$xmlVariant = FALSE;
	}

	return $xmlVariant;
}


function addDevicesToVariant($xml, $models) {
	$xmlDevice = "";

	foreach ($models as $i => $modelId) {
		$migModel = getSuggestedMigModelById($modelId);
		if ($migModel === FALSE) {
//			echo "- Not found: <b>$modelId</b><br/>";

		} else {
//			if (modelAlreadyInXml($xml, $migModel) !== TRUE) {
				$xmlDevice .= "\t\t\t<qpass:deviceId>$migModel</qpass:deviceId>\n";
/*
			} else {
				echo "> <b>$migModel</b> already in Xml. Skipping.<br/>";
				$xmlDevice = "";
			}
*/
		}
	}
	if ($xmlDevice == "") echo "- No devices found";
	return $xmlDevice;
}


function getSuggestedMigModelById($id) {
	$data = file("/home/storage/www/tools/cms/amdocs/devices-Ok.csv");
	foreach ($data as $ln => $ld) {
		list ($migModel,$webId) = explode(",",$ld);
		if (intval($webId) == intval($id)) {
			echo "<b>FOUND: $id</b><br/>";
			return $migModel;
		}
	}
	return FALSE;
}


function modelAlreadyInXml($xml, $druttModel) {
	$found = FALSE;
	$tagName = $xml->getElementsByTagName("device");

	if ($tagName->length == 0) return FALSE;

	foreach ($tagName as $tagValue) {
		$value = trim($tagValue->nodeValue);
		if ($value == $druttModel) $found = TRUE;
	}
	return $found;
}


function loadGameContent($dbc, $idC, $idCel) {
	$sql = "SELECT gc.archivo FROM Web.gamecomp gc WHERE gc.juego=$idC AND gc.celular=$idCel ";
	$rs = mysql_query($sql, $dbc->db);

	if (!$rs) die ("ERROR: no se pueden obtener datos para contenido=$idC <br/>");
	else if (mysql_num_rows($rs) > 0) {
		$obj = mysql_fetch_object($rs);
		return $obj->archivo;
	} else {
		echo "Skipping cel Id $idCel";
		return FALSE;
	}
}


function getFtpFiles($archivo) {
	global $dirToWrite;

	$info = pathinfo($archivo);
	$toJad = $info['basename'];
	$toJar = str_replace(".jad", ".jar", $toJad);

	$ftpCon = new Ftp();

	$i=0;

	$jad = str_replace("netuy", "contenido", $archivo);
	$jar = str_replace(".jad", ".jar", $jad);

	$conectado = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
	if ($conectado === TRUE) {
		$to = $tmpDir."/".$toJad;
		echo "descargando jad <b>$jad</b> => <b>$to</b>...<br/>";
		$bajado = $ftpCon->bajar_r($jad, $to, FTP_DOWN_RETRIES);
		if ($bajado === TRUE) {
			echo "jad Ok!<br/>";
			$to = $tmpDir."/".$toJar;
			echo "descargando jar $jar => $to...<br/>";
			$bajado = $ftpCon->bajar_r($jar, $to, FTP_DOWN_RETRIES);
			if ($bajado === TRUE) {
				echo "jar Ok!<br/>";
				$content_download = TRUE;
			} else {
				// jar no encontrado; intentando leer jar del jad
				$jadLines = file($toJad);
				$jarName = getJarNamefromJad($jadLines);
				$pathName = pathinfo($jar);
				$newJar = $pathName['dirname']."/".$jarName;

				echo "jar $jar no encontrado...<br/>";
				echo "intentando $newJar...<br/>";

				$bajado = $ftpCon->bajar_r($newJar, $toJar, FTP_DOWN_RETRIES);
				if ($bajado === TRUE) {
					echo "jar $newJar Ok!<br/>";
					$content_download = TRUE;
				} else {
					echo "ERROR: descargando el jar '$newJar' del ftp<br/>";
					exit;
				}
			}
		} else {
			$log .= "jad $jad not found...<br/>";
			echo "ERROR: descargando el jad '$jad' del ftp<br/>";
			exit;
		}
		$bajado = FALSE;
	} else {
		echo "ERROR: no se puede conectar al ftp<br/>";
	}

	if ($content_download === TRUE) {
		return TRUE;
	} else {
		return FALSE;
	}

}





?>