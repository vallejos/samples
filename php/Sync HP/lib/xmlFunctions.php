<?php

/**
 * funciones para simplificar el manejo de xml a traves de DOM
 * by kmS
 */


// ALBUMS
// DEVUELVE EL NUMERO DE RESULTADOS DE BUSQUEDA DEL XML 
function getAlbumCountResult($xml) {
    $count = 0;
    
    $result = $xml->getElementsByTagName("searchResult");
  
    if ($result->length == 1) {
        // tiene item searchResult, obtenemos el valor para saber la cantidad de items que trajo del ws
        $item = $result->item(0);
        if ($item->hasAttributes()) {
            // obtengo cantidad de resultados
            foreach ($item->attributes as $attrName => $attrValue ) {
                if ($attrName == "count") $count = $attrValue->nodeValue;
            }
        } else {
            // no se pudo leer la cantida de items obtenidos en la busqueda
            $count =  FALSE;
        }
    } else { 
        // sino, intentamos nuevamente (hasta 3 veces)
        $count =  FALSE;
    }   

    return $count;
}



// TRACKS
// DEVUELVE EL NUMERO DE RESULTADOS DE BUSQUEDA DEL XML 
function getTrackCountResult($xml) {
    $count = 0;
    
    $result = $xml->getElementsByTagName("searchResult");
  
    if ($result->length == 1) {
        // tiene item searchResult, obtenemos el valor para saber la cantidad de items que trajo del ws
        $item = $result->item(0);
        if ($item->hasAttributes()) {
            // obtengo cantidad de resultados
            foreach ($item->attributes as $attrName => $attrValue ) {
                if ($attrName == "count") $count = $attrValue->nodeValue;
            }
        } else {
            // no se pudo leer la cantida de items obtenidos en la busqueda
            $count =  FALSE;
        }
    } else { 
        // sino, intentamos nuevamente (hasta 3 veces)
        $count =  FALSE;
    }   

    return $count;
}



// GENEROS
// DEVUELVE EL NUMERO DE RESULTADOS DE BUSQUEDA DEL XML 
function getGenreCountResult($xml) {
    $count = 0;
    
    $result = $xml->getElementsByTagName("contentGroup");

    $count = ($result->length > 0) ? $result->length : FALSE;

    return $count;
}



?>
