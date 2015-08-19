<?php

$finished = FALSE;
$fetched = 0;
$start = WS_START_GENRE;
$size = WS_SIZE_GENRE;

$log->add("loading groups... using start=$start, size=$size");

while (!$finished) {
    $count = FALSE;
    $intentos = 0;

    while ($count == FALSE && $intentos<3) {
	$intentos++;
	$url = "http://maxx.me.net-m.net/me/maxx/$contractId/contentGroups?start=$start&contentTypeKey=FULLTRACK&maxSize=$size";
	$log->add("reading $url (try #$intentos)");
	$xmlContents = file_get_contents($url);

	$doc = new DOMDocument();
	$doc->loadXML($xmlContents);

	$count = getGenreCountResult($doc);
	if ($count != FALSE) {
	    $log->add("got $count results");
	} else {
	    $log->add("error reading results");
	    avisoCel("could not read xml (groups)");
		    
	    // pausa de 3 secs (posible server restart o error en netm, esperamos)
	    sleep(3);
	}
    }

    if ($count === FALSE) {
	// si no se pudo leer resultados, y se superaron los reintentos, termino el proceso
	$finished = TRUE;
	$log->add("too many retries with error");

	// enviar aviso cel
	$log->add("sending sms notification");
	avisoCel("too many retries with error (genres,start=$start,size=$size)");
    } else {
	// se obtuvo un nuevo resultado
	if ($count > 0) $fetched++;
    }

    // si no hay resultado de busqueda, termino el proceso
    if ($count == 0) $finished = TRUE;
    
    if ($finished === FALSE && $count>0) {
	// si se obtuvieron resultados y no se termino, guardo el xml y sigo buscando mas
	$xmlName = date("Ymd")."-groups_".$fetched."-".date("His").".xml";
	$fName = TMP_DIR."/".$xmlName;
	$log->add("saving xml content to $fName");
	$doc->save($fName);
    } else if ($finished === FALSE) {
	// no hay mas resultados y se termino el proceso, no hay que guardar nada
	$log->add("finished getting genres");
    }

    // si hay menos items que size, se termina de procesar 
    if ($count < $size) $finished = TRUE;
    
    $start += $size;
    $log->save(TRUE);
    
    // SAVE TO DABATASE
    $ok = 0;
    $error = 0;
    $skipped = 0;

    $log->add("saving $xmlName content to database...");
    $result = $doc->getElementsByTagName("contentGroup"); // cada contentGroup corresponde a un genero

    if ($result->length > 0) {
	// si tengo generos para recorrer
	foreach ($result as $itemNumber => $xmlGenre) {
	    $xmlItem = $result->item($itemNumber);
	    if ($xmlItem->hasAttributes()) {
		$genre = new Genre();

		// obtengo valores de los atributos que necesito
		foreach ($xmlItem->attributes as $attrName => $attrValue ) {
		    if ($attrName == "id") $genre->setId($attrValue->nodeValue); // 
		    if ($attrName == "name") $genre->setName($attrValue->nodeValue); // 
		}

		// estos grupos no se guardan en la database
		// deberian guardarse con activo=0 pero voy a skippear porque no se si la web lo toma en cuenta... je :S
		if (($genre->getId() != "15680") // TOP 10 DRM - skipped
		&& ($genre->getId() != "15679")) // KEEP Your Favorites - skipped
		{
		    $saved = $genre->save($dbc, "generos");

		    if ($saved === TRUE) {
			$ok++;
		    } else {
			$error++;
			$log->add("ERROR SQL: ".mysql_error());
		    }
		} else {
		    $skipped++;
		    $log->add("Skipping group #".$genre->getId());
		}
		

	    } else {
		// no se pudo leer los datos del genero
		$log->add("ERROR: no se pudieron obtener datos del genero en: $xmlName");
	    }
	}
	
	$log->add("finished processing $xmlName... total=$count, Ok=$ok, error=$error, skipped=$skipped");	
	
    } else {
        // no hay items para procesar
        $log->add("ERROR: no se encontraron items para procesar en: $xmlName");
    }

    
}




$log->add("fetched $fetched xml");
$log->save(TRUE);


?>
