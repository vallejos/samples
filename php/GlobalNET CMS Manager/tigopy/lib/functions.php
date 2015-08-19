<?php



function postFile($file, $url) {


    $file_to_upload = array('file_contents'=>'@'.$file);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $file_to_upload);
$result=curl_exec ($ch);
curl_close ($ch);
return  $result;

    $ch = curl_init();
    	$post = array(
		"file"=>"@$file",
	);
    curl_setopt($ch, CURLOPT_VERBOSE, 1); // set url to post to
    curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
    curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: audio/x-mp3"));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 40); // times out after 4s
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // add POST fields
    curl_setopt($ch, CURLOPT_POST, 1);
    $result = curl_exec($ch); // run the whole process
    if (empty($result)) {
        // some kind of an error happened
        die(curl_error($ch));
        curl_close($ch); // close cURL handler
    } else {
        $info = curl_getinfo($ch);
        curl_close($ch); // close cURL handler
        if (empty($info['http_code'])) {
            die("No HTTP code was returned");
       } else {
           // load the HTTP codes
//           $http_codes = parse_ini_file("response.inc");

           // echo results
//           echo $info['http_code'] . " " . $http_codes[$info['http_code']];
           echo $info['http_code'] ;
       }
    }
    //echo $result; //contains response from server

return $result;

/*
        $ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	// same as <input type="file" name="file_box">
	$post = array(
		"file"=>"@$file",
	);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$response = curl_exec($ch);
        curl_close($ch);

	return $response;
*/
 }


function read_template($template) {
	global $template_dir;

	if (!file_exists($template_dir."/".$template)) die("ERROR: Cannot read template - ".$template_dir."/".$template);
	$tpl = file_get_contents($template_dir."/".$template);
	return $tpl;
}



function map($obj, $array_map, $template) {
	$mapped_string = read_template($template);

	foreach ($array_map as $var => $tag) {
		$mapped_string = str_replace($tag,$obj->$var,$mapped_string);
	}

	return $mapped_string;
}


function get_preview($src, $dst){
	global $debug, $USE_LOCAL_FTP;
//	$ftp = new Ftp(LOCALHOST_PREVIEW, LOCALUSR_PREVIEW, LOCALPASS_PREVIEW);
//	$ftp = new Ftp(HOST_PREVIEW, USR_PREVIEW, PASS_PREVIEW);

	$ftp = ($USE_LOCAL_FTP === TRUE) ? new Ftp(LOCALHOST_PREVIEW, LOCALUSR_PREVIEW, LOCALPASS_PREVIEW) : new Ftp(HOST_PREVIEW, USR_PREVIEW, PASS_PREVIEW);
        $prePath = ($USE_LOCAL_FTP === FALSE) ? "/www.wazzup.com.uy/netuy" : "";

        $src = str_replace("netuy/netuy", "netuy", $prePath.$src);

//        if ($USE_LOCAL_FTP === TRUE) echo "Using local FTP connection <br/>";
	if ($ftp === FALSE) echo "ERROR FTP: Cannot connect to FTP server. <br/>";
	if ($ftp->login() !== TRUE) echo "ERROR FTP: Cannot authenticate (bad user/pass)) <br/>";
	$result = $ftp->bajar($src, $dst);
	if ($result !== TRUE) echo "ERROR FTP: Cannot download file $src => $dst <br/>";
	$ftp->logout();
	return $result;
}


function copy_format_img($img_source, $width, $height, $watermark_logo="",$extOrig="",$ext="",$background="") {
	global $targetDir;

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



function getCelId($user_agent, $db){
	$sql = " SELECT CM.fk_celulares_web ";
	$sql .= " FROM MCM.celulares_ua_wurfl CU INNER JOIN MCM.celulares_modelos_wurfl CM";
	$sql .= " ON CU.pk_fk_celulares_modelos_wurfl=CM.pk_celulares_modelos_wurfl INNER JOIN MCM.celulares_marcas_wurfl CMA";
	$sql .= " ON CM.fk_celulares_marcas_wurfl=CMA.pk_celulares_marcas_wurfl";
	$sql .= " WHERE pk_descripcion ='" . $user_agent . "'";
	$rs = mysql_query($sql, $db);
	if(!$rs) {
            return FALSE;
//	   echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;
//	   exit;
	}
	if (mysql_num_rows($rs) >0){
	    $celular = mysql_fetch_object($rs);
	    return $celular->fk_celulares_web;
	} else {
	    return 0;
	}
}


function obtenerIDCelular($user_agent, $db){
	return getCelId($user_agent, $db);
}


function soportaJuego($db, $id_cel, $id) {
//	$id_cel = obtenerIDCelular($ua, $db);
	$sql = "SELECT COUNT(C.id) as cont
			FROM Web.contenidos C INNER JOIN Web.gamecomp GC ON C.id = GC.juego
			WHERE celular = $id_cel
			AND C.id = $id";

	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo "ERROR: Error on query ($sql): ".mysql_error()."<br/>";
	} else {
		$row = mysql_fetch_assoc($rs);
		return $row['cont'] > 0;
	}
}



function calcularCarpeta($id){
	$carpeta = (ceil($id/500)*500);
	return $carpeta;
}


function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}


function dumpArray($array) {
	echo "<table><tr><td>LINE</td><td>DATA</td></tr>";
	foreach ($array as $ln => $ld) {
		echo "<tr><td>$ln</td><td>$ld</td></tr>";
	}
	echo "</table>";
}


?>