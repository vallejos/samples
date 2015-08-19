<?php


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




function loadDevicesList($dbc, $contentId) {
	// leo lista de devices
	$celulares = file("devices-Ok.csv");
	foreach ($celulares as $i => $v){
		$cel = trim($v, "\n ");
		$cel = str_replace("_drutt", "", $cel);
		$cel = str_replace("_", " ", $cel);
		$celulares[$i] = $cel;
	}

	$sql_celus = "'".implode("','", $celulares)."'";
	$sql = "SELECT c.id, gc.archivo, CONCAT(m.descripcion, ' ', cels.modelo) as marca_modelo
		FROM Web.contenidos c INNER JOIN Web.gamecomp gc ON c.id = gc.juego
		INNER JOIN Web.celulares cels ON cels.id = gc.celular
		INNER JOIN Web.marcas m ON cels.marca = m.id
		WHERE CONCAT(m.descripcion, ' ', cels.modelo) IN (".$sql_celus.")
		AND c.id='$contentId' ";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) return FALSE;

	$juegos = array();

	while($row = mysql_fetch_assoc($rs)) {
		$jad = explode("/", $row['archivo']);
		$jad = $jad[count($jad) - 1];
		$juegos[$row['id']][$jad] = array("modelo" => $row['marca_modelo'], "archivo" => $row['archivo']);
	}

	return $juegos;
}



function getAllModels($dbc, $archivo) {
	$aModels = array();
	$sql = "SELECT celular FROM Web.gamecomp WHERE archivo='$archivo' ";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) return FALSE;

	while($obj = mysql_fetch_object($rs)) {
		$aModels[] = $obj->celular;
	}
	return $aModels;
}



function generateVariantXml($dbc, $idC, $archivo, $uniqueId, $models) {
	if ($archivo !== FALSE) {
		$info = pathinfo($archivo);
		$toJad = $info['basename'];
		$toJar = str_replace(".jad", ".jar", $toJad);

                $migModels = addDevicesToVariant($xml, $models);

                if (!empty($migModels)) {
                    $xmlVariant = "<variant>";
                    $xmlVariant .= "\n".$migModels;
                    $xmlVariant .=<<<XML
        <jar uri="$toJar"/>
        <jad uri="$toJad"/>
XML;
                    $xmlVariant .= "\n</variant>\n";
                } else {
                    $xmlVariant = FALSE;
echo "ERROR on migModels=$migModels - Returning FALSE<br/>";
                }
	} else {
		$xmlVariant = FALSE;
echo "ERROR on archivo=$archivo - Returning FALSE<br/>";
	}

	return $xmlVariant;
}



function loadGameContent($dbc, $idC, $idCel) {
	$sql = "SELECT gc.archivo FROM Web.gamecomp gc WHERE gc.juego=$idC AND gc.celular=$idCel ";
	$rs = mysql_query($sql, $dbc->db);

	if (!$rs) die ("ERROR: no se pueden obtener datos para contenido=$idC en loadGameContent \n");
	else $obj = mysql_fetch_object($rs);

	return $obj->archivo;
}


function getFtpFiles($archivo) {
	$info = pathinfo($archivo);
	$toJad = $info['basename'];
	$toJar = str_replace(".jad", ".jar", $toJad);

	echo "\tdescargando contenido...<br/>";
	$ftpCon = new Ftp();

	$i=0;
	$conectado = FALSE;

	$jad = str_replace("netuy", "contenido", $archivo);
	$jar = str_replace(".jad", ".jar", $jad);

	$conectado = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
	if ($conectado === TRUE) {
		$to = TMP_DIR_JG."/".$toJad;
		echo "\tdescargando jad $jad => $to...<br/>";
                $bajado = $ftpCon->bajar_r($jad, $to, FTP_DOWN_RETRIES);
		if ($bajado === TRUE) {
			echo "\tjad Ok!<br/>";
			$to = TMP_DIR_JG."/".$toJar;
			echo "\tdescargando jar $jar => $to...<br/>";
                        $bajado = $ftpCon->bajar($jar, $to, FTP_DOWN_RETRIES);
			if ($bajado === TRUE) {
				echo "\tjar Ok!<br/>";
				$content_download = TRUE;
			} else {
				// jar no encontrado; intentando leer jar del jad
				$jadLines = file($toJad);
				$jarName = getJarNamefromJad($jadLines);
				$pathName = pathinfo($jar);
				$newJar = $pathName['dirname']."/".$jarName;

				echo "\tjar $jar no encontrado...<br/>";
				echo "\tintentando $newJar...<br/>";

                                $bajado = $ftpCon->bajar_r($newJar, $toJar, FTP_DOWN_RETRIES);
				if ($bajado === TRUE) {
					echo "\tjar $newJar Ok!<br/>";
					$content_download = TRUE;
				} else {
					echo "ERROR: descargando el jar '$newJar' del ftp<br/>";
					exit;
				}
			}
		} else {
			$log .= "\tjad $jad not found...\n";
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


function addDevicesToVariant($xml, $models) {
	$xmlDevice = "";

echo "Trying to match models...<br/>";
	foreach ($models as $i => $modelId) {
printStyle("matching modelId=$modelId with drutt list...");
                $migModel = getSuggestedDruttModelById($modelId);
		if ($migModel === FALSE) {
printStyle("Not found: modelId=<b>$modelId</b>", "notice");
		} else {
printStyle("Found: migModel=$migModel", "ok");
                    $xmlDevice .= "\t<device>$migModel</device>\n";
		}
	}
	if ($xmlDevice == "") echo "- No devices found!<br/>";
	return $xmlDevice;
}


function getSuggestedDruttModelById($id) {
	$data = file("/home/kamus/Web/uy/ancel/drutt-ancel/devices-Ok.csv");
	foreach ($data as $ln => $ld) {
		list ($dModel,$wId) = explode(";",$ld);
		if (intval($wId) == intval($id)) return $dModel;
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


?>