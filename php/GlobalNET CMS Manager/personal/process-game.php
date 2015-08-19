<?php


$debug = TRUE;
$totalCont = 0;
$totalModels = 0;

$xmlFile = "ContentSubmission.xml";

$tmpDir = TMP_DIR_JG;
$sessDir = $tmpDir."/".$_SESSION["folder"];
if (!file_exists($sessDir)) exec("mkdir $sessDir");
$tmpDir = $sessDir;


if (!is_writable($tmpDir)) die("ERROR: <b>".$tmpDir."</b> is not writable<br/>");
else {
	$log .= "borrando tmp dir<br/>";
	exec("rm -rf ".$tmpDir."/*");
}

if (!is_writable(ZIP_DIR)) die("ERROR: <b>".ZIP_DIR."</b> is not writable<br/>");


$datosTabla = array();
$datosDevices = array();




foreach ($listaIds as $i => $contentId) {
        $tipoCarga = $workingTipoCarga;
        $version = 1.0; //Version inicial
        $zips_generados = array();
        $main_zip = "";
	$contentId = trim($contentId);
	echo "<ul>Processing <b>$contentId</b><br/>";

	// obtengo lista de archivos y celulares en gamecomp
	echo "<li> loading jad/jar...</li>";
	$datos = array();
	$sql = "SELECT c.id, gc.archivo, gc.celular idCel, marca_modelo
		FROM Web.contenidos c
		INNER JOIN Web.gamecomp gc ON c.id = gc.juego
                INNER JOIN MCM.celulares_modelos_wurfl cmw on cmw.fk_celulares_web = gc.celular
                INNER JOIN personalArg.celulares_homologados_marcablanca chm on chm.id_wurfl = cmw.pk_celulares_modelos_wurfl
		WHERE c.id='$contentId'
                AND chm.activo= 1 
                GROUP BY marca_modelo";
	//$sql = "SELECT DISTINCT gc.archivo FROM Web.gamecomp gc WHERE gc.juego='$contentId' ";
	echo $sql;
	$rs = mysql_query($sql, $dbc->db);

	while($obj = mysql_fetch_object($rs)) {
		//$datos[$obj->id][$jad] = array("modelo" => $obj->marca_modelo,
                $datos[$obj->archivo][]  = array("modelo" => $obj->marca_modelo,
                                                 "idcel" => $obj->idCel);
	}

        $primera_version = true;
        $firstEditionName = "";
        foreach($datos as $archivo => $compat_data) {
            echo "<li> creating objects...</li>";
            if(!$primera_version) {
                $tipoCarga = "Add";
            }
            $game = new personalGame($dbc, $contentId, $tipoCarga, $debug);

            try {
                echo "<li> loading content...</li>";
                $game->loadContent();

                $game->setOldEditionName($firstEditionName);
                $game->setFilename($archivo);
                $game->cargarCompatibilidad($compat_data);
                $game->setCategorias($categorias_seleccionadas);
                $game->setVersion($version);

                if($primera_version) {
                    $firstEditionName = $game->getEditionName();
                }

            } catch (Exception $e) {
                    $log .= "loadContent: ".$e->getMessage()."<br/>";
            }

            $uniqueId = $game->getUniqueId();

          // creo carpeta destino
            $dirToWrite = $tmpDir."/$uniqueId";
            echo "<li> creating temp dir <b>$dirToWrite</b>...</li>";
            exec("mkdir $dirToWrite");

            echo "<li> creating meta dir <b>$dirToWrite/meta</b>...</li>";
            exec("mkdir $dirToWrite/meta");


            try {
                    //SI y solo SI se descargan JAD Y JAR puedo crear el XML y el ZIP
                    if($game->downloadBinaries($dirToWrite)) {
                        $i++;
                        $xmlContent = $game->genXML();                        

                        writeXML($xmlContent, $dirToWrite."/meta/".$xmlFile);
                        $ds =  writeZIP($dirToWrite, $contentId, $game->getVersion());
                        if($primera_version) {
                            /*echo '<h2>Subir este zip PRIMERO</h2>';
                            echo "<div style=\"margin: 0 auto; text-align: center; font-size: 20px\">Nombre del Zip generado <a href='".$ds['path']."'>".$ds['path']."</a>, <b>".$ds['size']."</b></div><br/>\n";
                            */
                            $main_zip = $ds['path'];
                        } else {
                            $zips_generados[] = $ds;
                   //         echo "<div style=\"margin: 0 auto; text-align: center; font-size: 20px\">
                     //               Nombre del Zip generado ".$ds['path']."<b>".$ds['size']."</b></div><br/>\n";
                        }
                        emptyDir($dirToWrite);
                    } else {
                        echo '<li>ERROR al descargar binarios, zip no generado...</li>';
                    }
            } catch (Exception $e) {
                    $log .= "genXML: ".$e->getMessage()."<br/>";
            }
            $totalCont++;
            $primera_version = false;
            $version += 0.1;
        }
        echo '<li><h1>Zip principal (subir este primero): <a href="'.$main_zip.'">Descargar</a></h1></li>';
        echo '<li><h1>Generando paquetes de Zips...</h1></li>';
        $tam_actual = 0;
        $nro_paquete = 0;
        $zip_a_empaquetar = array();
        foreach($zips_generados as $zip_data) {
            if( ($tam_actual + $zip_data['size']) <= MAX_KB_ZIP) {
                $tam_actual += $zip_data['size'];
                $pathinfo = explode("/", $zip_data['path']);
                $zip_a_empaquetar[] = $pathinfo[2];
            } else {
                $nro_paquete++;
                echo '<li>Generando paquete #'.$nro_paquete.'</li>';
                $lista_zips = implode(" ", $zip_a_empaquetar);
                $paquete_filename = 'paquete_'.$contentId.'_'.date("ymdhsm").'nro'.$nro_paquete.'.zip';
                $shellCmd = 'cd personal/zip; zip '.$paquete_filename.' '.$lista_zips;
                exec($shellCmd);
                $zip_a_empaquetar = array();
                $tam_actual = 0;
                echo '<li><h2><a href="personal/zip/'.$paquete_filename.'">Descargar paquete</a></h2></li>';
                
            }
        }

        if($tam_actual < MAX_KB_ZIP) { //Para generar el último paquete
            $nro_paquete++;
            echo '<li>Generando paquete #'.$nro_paquete.'</li>';
            $lista_zips = implode(" ", $zip_a_empaquetar);
            $paquete_filename = 'paquete_'.$contentId.'_'.date("ymdhsm").'nro'.$nro_paquete.'.zip';
            $shellCmd = 'cd personal/zip; zip '.$paquete_filename.' '.$lista_zips;
            exec($shellCmd);
            echo '<li><h2><a href="personal/zip/'.$paquete_filename.'">Descargar paquete</a></h2></li>';

        }
        echo '</ul>';
	
	
}



//echo "generado <a href='".ZIP_DIR."/$zipFile'>".ZIP_DIR."/$zipFile</a> con $total contenidos, <b>$ds</b><br/>\n";


echo "<hr/><h3>Log:</h3>";
echo "<textarea cols=50 rows=10>$log</textarea>";

?>