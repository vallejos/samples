<?php


$devices_obligatorios = array(
"sonyericsson_w580i_ver0",
"sonyericsson_w200a_ver1",
"sonyericsson_w395_ver1",
"nokia5530_ver1",
"sonyericsson_c510a_ver1",
"nokia_5300_ver1",
"samsung_s5230",
"samsung_gt_m2310_ver1",
"nokia_5130",
"sonyericsson_w380_ver1",
"sonyericsson_w705a_ver1",
"sonyeric_w300i_verr9a",
"nokia_5610expressmusic_ver1",
"sonyericsson_k550i_ver1",
"nokia_5310_xpressmusic_ver1",
"sonyericsson_w760i_subr3aa",
"sonyericsson_w350a_ver1",
"blackberry_8520",
"nokia_n95_ver1_sub_8gb_fl3",
"samsung_sgh-e215l",
"lg_kp215_ver1",
"lg_kp570_ver1",
"nokia_n97",
"sonyericsson_s500i_ver1",
"nokia_6131_ver1",
"sonyericsson_c905_ver1_suba",
"nokia_5200_ver1",
"nokia_6300_ver1",
"samsung_sgh_f275l_ver1",
"mot_z6_ver1",
"sonyericsson_k790_ver1_sub1",
"nokia_2760_ver1",
"sonyericsson_w880i_r6bc",
"sonyericsson_w610i_ver1",
"nokia_5220_expressmusic_ver1",
"sonyericsson_k850i_ver1",
"sonyericsson_c902_ver1",
"nokia_1680c_ver1_sub2b",
"nokia_5800d_ver1",
"sonyericsson_w595",
"sonyericsson_k310a_ver1",
"lg_kf600d_ver1",
"samsung_sgh_f480l_ver1",
"sonyericsson_z530i_ver1",
"nokia_6120c-moz",
"nokia_2630_ver1",
"samsung_sgh_f250l_ver1",
"nokia_e71_ver1",
"samsung_sgh_j700_ver1",
"mot_v8xx_ver1",
"samsung_sgh_a736_ver1",
"samsung_sgh_e236_ver1",
"lg_me970d_ver1",
"lg_mg800_ver1",
"sonyericsson_w810i_subr4ea",
"samsung_e2210_ver1",
"blackberry9000_ver1",
"sonyericsson_z750i_ver1",
"sonyericsson_r300a_ver1",
"sonyericsson_w910i_ver1",
"nokia_3220_ver1",
"Samsung_gt_s5233t",
"sonyericsson_z550a",
"nokia_6101_ver1",
"lg_kf755",
"sonyericsson_z310a_ver1",
"nokia_3500_ver1_sub0660",
"sonyericsson_w710i_ver1",
"nokia_6061_ver1",
"mot_em28_ver1",
"mot_a1200eam_ver1",
"sonyericsson_z750a_ver1",
"nokia_5700_ver1_sub",
"sonyericsson_z710i_ver1",
"sonyericsson_k510a_ver1",
"nokia_3120c_ver_2_sub0716",
"samsung_sgh_t519_ver1",
"sonyericsson_w600i_ver1",
"lg_kf510_ver1",
"nokia_5070b_ver1",
"nokia_2220",
"nokia_n85_ver1",
"nokia_2690",
"sonyericsson_t303_ver1_subr2cc001",
"sonyericsson_w995",
"samsung_m8800l_ver1",
"samsung_sgh_u600_ver1",
"nokia_3555c_ver1",
"samsung_sgh_x640_ver1",
"mot_w396_ver1",
"samsung_j700i_ver1",
"nokia_2660_ver1",
"nokia_n73_ver1_20628001",
"nokia_7610_supernova_ver1",
"lg_kf350_ver1",
"nokia_3250_ver1",
"lg_km500_ver1",
"samsung_sgh_e496_ver1",
"lg_gw300",
"nokia_n75_ver1",
"blackberry_9700"
);

$devices_extras = array(
"samsung_gt_e25501",
"samsung_gt_s8500l",
"lg_gt540f",
"lg_gs500",
"alcatel_ot_255a",
"messagephone_qs150",
"motorola_mb511"
);

$debug = TRUE;
$totalCont = 0;
$totalModels = 0;

$xmlFile = "metadata.xml";
$zipFile = "Wazzup_".MIG_GAME."_".date("Ymd").".zip";
$tmpDir = TMP_DIR_JG;

if (!is_writable(TMP_DIR_JG)) die("ERROR: <b>".TMP_DIR_JG."</b> is not writable<br/>");
else {
	$log .= "borrando tmp dir<br/>";
	exec("rm -rf ".TMP_DIR_JG."/*");
}

if (!is_writable(ZIP_DIR)) die("ERROR: <b>".ZIP_DIR."</b> is not writable<br/>");

$datosTabla = array();
foreach ($listaIds as $i => $contentId) {
	$contentId = trim($contentId);
	echo "<ul>Processing <b>$contentId</b><br/>";

	// obtengo lista de archivos y celulares en gamecomp
	echo "<li> loading jad/jar...</li>";
	$juegos = array();
	$sql = "SELECT c.id, gc.archivo, CONCAT(m.descripcion, ' ', cels.modelo) as marca_modelo, gc.celular idCel
		FROM Web.contenidos c
		INNER JOIN Web.gamecomp gc ON c.id = gc.juego
		INNER JOIN Web.celulares cels ON cels.id = gc.celular
		INNER JOIN Web.marcas m ON cels.marca = m.id
		WHERE c.id='$contentId' ";
	$sql = "SELECT DISTINCT gc.archivo FROM Web.gamecomp gc WHERE gc.juego='$contentId' ";
	echo $sql;
	$rs = mysql_query($sql, $dbc->db);

	while($obj = mysql_fetch_object($rs)) {
//		$jad = explode("/", $obj->archivo);
//		$jad = $jad[count($jad) - 1];
//		$juegos[$obj->id][$jad] = array("modelo" => $obj->marca_modelo, "archivo" => $obj->archivo, "idcel" => $obj->idCel);
		$juegos[] = $obj->archivo;
	}



	// creo el objeto
	echo "<li> creating objects...</li>";
	$game = new migGame($dbc, $debug, $catLvl, $webCat);

        $game->addFiles($contentId, $juegos);
        
	try {
		// cargo el contenido
		echo "<li> loading content...</li>";
		$game->loadContent($contentId);
	} catch (Exception $e) {
		$log .= "loadContent: ".$e->getMessage()."<br/>";
	}

	$game->setTag($catmig);
	$game->setSubTag($subcatmig);
	$game->setKeywords($keywords[$i]);
	//$game->setShortDesc($shortDesc[$i]);
//	$game->setLongDesc($longDesc[$i]);
	$uniqueId = $game->getUniqueId();

	// creo carpeta destino
	$dirToWrite = TMP_DIR_JG."/$uniqueId";
	echo "<li> creating temp dir <b>$dirToWrite</b>...</li>";
	exec("mkdir $dirToWrite");

	try {
		$content_download = FALSE;
		// descargo contenido por FTP
		$ftpCon = new Ftp();
		$retries=0; $i=0;
		$conectado = FALSE;

		echo "<li> connecting to FTP...</li>";
		$conectado = $ftpCon->login_r(null, null, FTP_CONN_RETRIES);
		if ($conectado === TRUE) {
			echo "+ connection Ok!";
                        $download_errors = 0;
			foreach ($juegos as $archivo) {
//				$modelId = $datos["idcel"];
//				$archivo = getArchivo($dbc, $modelId, $contentId);

				$jad = str_replace("netuy", "contenido", $archivo);
				$jar = str_replace(".jad", ".jar", $jad);

				$info = pathinfo($jad);
				$toJadName = $info['basename'];
				$info = pathinfo($jar);
				$toJarName = $info['basename'];
				$to = $dirToWrite."/".$toJadName;
				echo "<li>descargando jad <b>$jad</b> => <b>$to</b>...</li>";
				$bajado = $ftpCon->bajar_r($jad, $to, FTP_DOWN_RETRIES);
				if ($bajado === TRUE) {
					echo "+ jad Ok!<br/>";
					$to = $dirToWrite."/".$toJarName;
					echo "<li>descargando jar <b>$jar</b> => <b>$to</b>...</li>";
					$bajado = $ftpCon->bajar_r($jar, $to, FTP_DOWN_RETRIES);
					if ($bajado === TRUE) {
						echo "+jar Ok!<br/>";
						$content_download = TRUE;
					} else {
						// jar no encontrado; intentando leer jar del jad
						$toJad = $dirToWrite."/".$toJadName;
						$jadLines = file($toJad);
						$jarName = getJarNamefromJad($jadLines);
						$pathName = pathinfo($jar);
						$newJar = $pathName['dirname']."/".$jarName;

						echo "- jar <b>$jar</b> not found...<br/>";
						echo "- trying <b>$newJar</b>...<br/>";

						$bajado = $ftpCon->bajar_r($newJar, $to, FTP_DOWN_RETRIES);
						if ($bajado === TRUE) {
							echo "+ jar <b>$newJar</b> Ok!<br/>";
							$content_download = TRUE;
						} else {
							echo "<li>ERROR: descargando el jar <b>$newJar</b> del ftp</li>";
						}
					}
				} else {
					echo "- jad <b>$jad</b> not found...<br/>";
					$content_download = FALSE;
                                        $download_errors++;
				}

				echo "<li>loading devices...</li>";
				$models = getAllModels($dbc, $archivo);
				$newCompatElement = generateVariantXml($dbc, $contentId, $jad, $uniqueId, $models, $xml);

				if ($newCompatElement === FALSE) {
					echo "- could not generate variant for <b>$modelId</b> <br/>";
				} else {
					$xmlToAdd .= $newCompatElement;
					$newAdded++;
				}
			}
		} else {
			echo "- ERROR: no se puede conectar al ftp<br/>";
		}

		if ($content_download != FALSE || ($download_errors != count($juegos))) {
			$game->downloadContent($ftpCon);

/*
			$jad = $game->getJadFilename($i);
			$jar = $game->getJarFilename($i);
//			$device = str_replace(" ", "_", $modelo);
//			$device = strtolower($device);
//			$game->addDevice($device, $jad, $jar);
*/
			$totalModel++;
		} else {
			$log .= " ";
		}

		$i++;

             //   if ($content_download === TRUE) {
               //     echo "<li><b>CONTENIDO DESCARGADO CORRECTAMENTE</b></li>";
                    // obtengo y genero el XML
                    $xmlContent = $game->genXML();
                    $xmlContent = str_replace("%%PREMIUM%%", $xmlToAdd, $xmlContent);
                    $datosTabla[] = $game->getArrayDatos();
                    //print_r($game->getArrayDatos());
                    $total++;
                /*} else {
                    echo '<li><b>Error descargando el contenido </b></li>';
                    $log .= " <b>Error descargando el contenido </b> ";
                }*/

/*
		$to = TMP_DIR_JG."/preview_tmp.gif";
		$from = $game->getPreview();
		$log .= "descargando preview $from > $to<br/>";

		$ftpConUSA = new Ftp("216.150.27.11", "wmast", "hulkverde");
		$connectUSA = $ftpConUSA->login_r(null, null, FTP_CONN_RETRIES);

		$bajado = $ftpConUSA->bajar_r($from, $to, FTP_DOWN_RETRIES);
		if ($bajado === TRUE) {
			$origen_file = $to;
			$destino_file = TMP_DIR_JG."/".$game->getPreviewFilename();
			$width = 100;
			$height = 100;
			$background = FALSE;
			$extension = ".gif";
			crearImagen($to,$destino_file,$width,$height,$background,$extension);
			unlink($origen_file);
			$content_download = TRUE;
		} else {
			echo "ERROR: descargando el preview del ftp<br/>";
			exit;
		}
*/

	} catch (Exception $e) {
		$log .= "genXML: ".$e->getMessage()."<br/>";
	}
	$totalCont++;
}



// escribo el contenido ahora
if (!$fp = fopen("$dirToWrite/$xmlFile", "a+")) {
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
        //$zipFile = date("Ymd")."_Wazzup_".WAZZUP_WALLPAPER."_$i.zip";
        $zipFile = "Wazzup_".MIG_GAME."_".date("Ymd")."_$i" . "_" .".zip";
    } else {
        $existe = FALSE;
    }
}

// zipeo y muevo a carpeta de "envios"
$shellCmd = "cd ".TMP_DIR_JG."; zip -r ../".ZIP_DIR."/$zipFile * ";
$log .= exec ($shellCmd);
echo "<li> Zip ".ZIP_DIR."/$zipFile generado exitosamente</li>";

$ds = filesize(ZIP_DIR."/$zipFile");
$ds = formatBytes($ds);


$headers = array_keys($datosTabla[0]);

if(count($datosTabla) == 0) {
    echo "<h3><b>No hay datos de compatibilidad</b></h3>";
}

echo '<table><tr>';
foreach ($headers as $head) {
    echo '<th>'.$head.'</th>';
}
echo '</tr>';

    $devices_compatibles = array();
foreach($datosTabla as $meta) {
    $array_devices_compatibles = $meta['Compatible Devices'];
    
    foreach($array_devices_compatibles as $dupla) {
        $devices_compatibles[] = $dupla['Compatible Devices'];
    }
    //$devices_compatibles = explode("<br>", $devices_compatibles);
    $meta["Zip Filename"] = $zipFile;
    echo '<tr>';
    foreach ($meta as $key => $field) {
        if($key == "Binary" || $key == "Compatible Devices") {
            echo '<td>'.$field[0][$key].'</td>';
        } else {
            echo '<td>'.$field.'</td>';
        }
    }
    echo '</tr>';
}

//print_r($datosTabla);

//foreach($datosTabla as $i => $meta) {
$lista = $datosTabla[0]['Compatible Devices'];


foreach ($lista as $key => $meta) {
    if($i == 0) {
        continue;
    }

    echo '<tr>';
    echo '<td colspan="12"></td>';
    foreach($meta as $key => $data) {
        echo '<td >'.$data.'</td>';
    }
    echo '<td colspan="30"></td>';
    /*
    foreach ($meta as $key => $field) {
        echo '<br/>'.$key;
        if($key == "Binary" || $key == "Compatible Devices") {
            echo '<td>'.$field[0][$key].'</td>';
        } else {
            echo '<td></td>';
        }
    }
     * */
         echo '</tr>';
}
echo '</table>';

echo '<table>';
echo '<tr><th>Ranking</th><th>Pais</th><th>Formato(y)</th></tr>';
foreach ($devices_obligatorios as $i => $id) {
    echo '<tr><td>'.($i+1)."</td><td>$id</td><td>";
    if(in_array($id, $devices_compatibles)) {
        echo 'Y';
    }
    echo '</td></tr>';
}
echo '</table>';

echo '<table>';
echo '<tr><th>Pais</th><th>Formato(y)</th></tr>';
foreach ($devices_extras as $i => $id) {
    echo "<tr><td>$id</td><td>";
    if(in_array($id, $devices_compatibles)) {
        echo 'Y';
    }
    echo '</td></tr>';
}
echo '</table>';

//echo "generado <a href='".ZIP_DIR."/$zipFile'>".ZIP_DIR."/$zipFile</a> con $total contenidos, <b>$ds</b><br/>\n";
echo "<div style=\"margin: 0 auto; text-align: center; font-size: 20px\">Nombre del Zip generado ".ZIP_DIR."/<a href='".ZIP_DIR."/$zipFile'>$zipFile</a> con $total contenidos, <b>$ds</b></div><br/>\n";


echo "<hr/><h3>Log:</h3>";
echo "<textarea cols=50 rows=10>$log</textarea>";

?>