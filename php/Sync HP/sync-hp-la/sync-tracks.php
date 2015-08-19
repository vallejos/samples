<?php

$start = WS_START_TRACK;
$size = WS_SIZE_TRACK;

$log->add("loading tracks... using start=$start, size=$size");


$genreList = loadGenreList($dbc);

if ($genreList !== FALSE) {
    foreach ($genreList as $genreData) {
	$genre = $genreData->idgrupo;

	$log->add("fetching track info for genre $genre (".$genreData->nombre.")");
	$finished = FALSE;
	$fetched = 0;
		
	while (!$finished) {
	    $count = FALSE;
	    $intentos = 0;

	    while ($count == FALSE && $intentos<3) {
		$intentos++;
		$url = "http://maxx.me.net-m.net/me/maxx/$contractId/items?start=$start&contentTypeKey=FULLTRACK&maxSize=$size&contentGroupId=$genre";
		$log->add("reading $url (try #$intentos)");
		$xmlContents = file_get_contents($url);

		$doc = new DOMDocument();
		$doc->loadXML($xmlContents);

		$count = getTrackCountResult($doc);
		if ($count != FALSE) {
		    $log->add("got $count results");
		} else {
		    $log->add("error reading results");
		    avisoCel("could not read xml (tracks)");
		    
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
		avisoCel("too many retries with error (tracks,start=$start,size=$size)");
	    } else {
		// se obtuvo un nuevo resultado
		if ($count > 0) $fetched++;
	    }

	    // si no hay resultado de busqueda, termino el proceso
	    if ($count == 0) $finished = TRUE;

	    if ($finished === FALSE && $count>0) {
		// si se obtuvieron resultados y no se termino, guardo el xml y sigo buscando mas
		$xmlName = date("Ymd")."-tracks_".$fetched."-".date("His").".xml";
		$fName = TMP_DIR."/".$xmlName;
		$log->add("saving xml content to $fName");
		$doc->save($fName);
	    } else if ($finished === FALSE) {
		// no hay mas resultados y se termino el proceso, no hay que guardar nada
		$log->add("finished getting tracks fo genre $genre (".$genreData->nombre.")");
	    }

	    $start += $size;
	    $log->save(TRUE);


	    // SAVE TO DABATASE
	    $ok = 0;
	    $error = 0;

	    $log->add("saving $xmlName content to database...");
	    $result = $doc->getElementsByTagName("item"); // cada item corresponde a un album (o bundle en lenguaje netm)

	    if ($result->length > 0) {
		// si tengo albums para recorrer
		foreach ($result as $itemNumber => $xmlTrack) {
		    $xmlItem = $result->item($itemNumber);
		    if ($xmlItem->hasAttributes()) {
			$track = new Track("la");
			$track->setFromXML($xmlItem);
			$saved = $track->save($dbc, "temas");

			if ($saved === TRUE) {
			    // *************************
			    // OBTENGO DATOS DEL TRACK
			    $log->add("getting track id...");
			    $trackId = $track->getId();
			    // *************************

			    // *************************
			    // OBTENGO DATOS DEL ARTISTA
			    $log->add("getting artist id...");
			    if ($trackId != NULL) {
				$artist = new Artist();
				$log->add("searching db for artist=".$track->getArtistName());
				$artist->loadFromName($dbc, $track->getArtistName());
				if ($artist->getMaches() == "1") {
				    $artistId = $artist->getId();
				    $log->add("found artist=$artistId");
				} else {
				    $log->add("artist not found");
				    $log->add("saving new artist info...");
				    $artist->setName($track->getArtistName());
				    $savedArtist = $artist->save($dbc, "artistas");
				    if ($savedArtist == TRUE) {
					$artistId = $artist->getId();
					$log->add("new artist found=$artistId...");
				    } else {
					$artistId = NULL;
					$log->add("ERROR saving artist");
				    }
				}
			    }
			    // *************************
			    
			    // *************************
			    // OBTENGO DATOS DEL ALBUM
			    $log->add("getting album id...");
			    if ($artistId != NULL) {
				$album = new Album("la");
				$log->add("searching db for icpn=".$track->getIcpn());
				$album->loadFromIcpn($dbc, $track->getIcpn()); // veo si ya esta ingresado en la db

				if ($album->getMaches() == "1") {
				    // ya esta ingresado, obtengo id
				    $albumId = $album->getId();
				    $log->add("found album=$albumId");
				} else {
				    $log->add("album not found");
				    $log->add("fetching album info for icpn=".$track->getIcpn());
				    $album->setArtistId($artistId);
				    $album->fetchXML($track->getIcpn(), $log);
				    $savedAlbum = $album->save($dbc, "albums");

				    if ($savedAlbum === TRUE) {
					$albumId = $album->getId();
				    } else {
					$albumId = NULL;
					$log->add("ERROR saving album");
				    }
				}
			    }
			    // *************************
			    
			    // *************************
			    // ASOCIO ALBUM con GENERO
			    $log->add("linking album=$albumId to group={$genreData->id}...");
			    $album->assocGroup($dbc, $genreData->id);
			    // *************************

			    // *************************
			    // ASOCIO ALBUM con ARTISTA
			    $log->add("linking album=$albumId to artist=$artistId...");
			    $album->assocArtist($dbc, $artistId);
			    // *************************

			    // *************************
			    // ASOCIO ALBUM con TEMA
			    $log->add("linking album=$albumId to track=$trackId...");
			    $album->assocTrack($dbc, $trackId);
			    // *************************
			    
			    $ok++;
			} else {
			    $error++;
			    $log->add("ERROR SQL: ".mysql_error());
			}

		    } else {
			// no se pudo leer los datos del track
			$log->add("ERROR: no se pudieron obtener datos del track en: $xmlName");
		    }
		}

		$log->add("finished processing $xmlName... total=$count, Ok=$ok, error=$error");	

	    } else {
		// no hay items para procesar
		$log->add("ERROR: no se encontraron items para procesar en: $xmlName");
	    }


	}

	// reinicializo start y size para el proximo grupo (genero)
	$start = WS_START_TRACK;
	$size = WS_SIZE_TRACK;
    }
    
} else {
    // no se pudo obtener una lista de generos de la database
    $log->add("ERROR: cannot load genre list from databse");    
}



$log->add("fetched $fetched xml");
$log->save(TRUE);


?>
