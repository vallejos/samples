<?php
include_once(dirname(__FILE__)."/Genre.php");
include_once(dirname(__FILE__)."/ContentGroup.php");
include_once(dirname(__FILE__)."/Track.php");
include_once(dirname(__FILE__)."/Artist.php");
include_once(dirname(__FILE__)."/News.php");
include_once(dirname(__FILE__)."/User.php");
include_once(dirname(__FILE__)."/Albums.php");



/**
 * Description of WSMaxxClient
 *
 * @author feñaño
 */
class WSMaxxClient {
    const ENDPOINT = "http://maxx.me.net-m.net/me/maxx/<contractId>/";
//    const CONTRACTID = "2100947";  //Prueba
//const CONTRACTID = "2143588"; // choreado
    const CONTRACTID = "2146850";  //Producción (se supone)

    private $errMsg;
    private $nContractId = null;

    public function getErroMsg(){
        return $this->errMsg;
    }


    /**
     * Obtiene un listado de noticias (objetos News)
     *
     * @return array de objetos News
     */
    public function getNews() {
        $methodName = "news";
        $params = array();

        $xmlResult = $this->call($methodName, $params);
        $result = array();

        foreach($xmlResult->children() as $item) {
            $files = $item->files;

            $imgName = "";
            if($files->file != null) {
                $imgName = $files->file['storageKey'];
            }

            $objNews = new News($item['id'], $imgName.".jpg", $item->title, $item->summary,$item->body);
            $objNews->setValidFrom($item->validFrom);
            $objNews->setValidTo($item->validTo);
            $result[] = $objNews;
        }
        return $result;
    }

	public function setNewContractId($n) { $this->nContractId = $n; }


    /**
     * Retorna un array lleno de objetos "Track".
     *
     * @param int $itemsPerPage Cantidad de items a traer, se recomienda un máximo de 500 (50 por defecto)
     * @param int $currentPage  Pagina actual en el paginado a obtener (si el total de tracks es > 500, se recomienda no traer todo en una página)
     * @param String $genre     Códidgo de genero, que se puede obtener, con el método "getGenres"
     * @param String $parentGenre Código padre del genero al que pertenecerán los tracks
     */
    public function getItems($itemsPerPage = 50, $currentPage = 1,$contentGroupId = "",  $genre = "", $parentGenre = "") {
        $methodName = "items";
        $init = ( ($currentPage - 1) * $itemsPerPage) + 1;
        $params = array("contentTypeKey" => "FULLTRACK",
                        "maxSize" => $itemsPerPage,
                        "start" => $init,
			"orderBy" => "position");

	if($contentGroupId != "") {
		$params["contentGroupId"] = $contentGroupId;
	}
        if($genre != "") {
            $params["genreKey"] = $genre;
        }
        if($parentGenre != "") {
            $params['mainGenreKey'] = $parentGenre;
        }

         $xmlResult = $this->call($methodName, $params);
         $results = array();
         $items = $xmlResult->xpath("//item");

         foreach($items as $item) {
             $results[] = new Track($item['orderId'],
                                    $item['title'],
                                    $item['artist'],
                                    $item['tariffClass'],
                                    $item['bundleOrderId'],
                                    $item['isrc'],
                                    $item['icpn'],
                                    $item['track'],
                                    $item['volume'],
                                    $item['length'],
                                    $item['licenseProviderId'],
                                    $item['sellOnlyInBundle']
			);

	}
	unset($items);

/*
		foreach($groups as $group) {
			$results['album'][] = new Album($group['id'],
				$group['name']
			);
		}
*/

         return $results;

    }



    public function getAlbumItems($upc) {
        $methodName = "items";
        $params = array("contentTypeKey" => "FT_BUNDLE",
			"icpn" => "$upc"
			);

         $xmlResult = $this->call($methodName, $params);
         $results = array();
         $items = $xmlResult->xpath("//item");

         foreach($items as $item) {
             $results[] = new Album($item['orderId'],
                                    $item['title'],
                                    $item['artist'],
                                    $item['tariffClass'],
                                    $item['bundleOrderId'],
                                    $item['icpn'],
                                    $item['track'],
                                    $item['volume'],
                                    $item['length'],
                                    $item['licenseProviderId']
			);
	}

         return $results;
    }









    /**
     *
     * @param String $genre Clave del Genero padre de todos los generos a los que pertenecen los artista a obtener,
     * @return Array de objetos Artist
     */
    public function getArtists($genre){
        $methodName = "contributors";
        $params = array("isArtist" => "true", "mainGenreKey" => $genre);

        $xmlResult = $this->call($methodName, $params);
        $results = array();
        foreach($xmlResult->children() as $item) {
            $results[] = new Artist($item['id'], $item['name']);
        }
        return $results;

    }

    /**
     * Funciona igual que "getArtists", pero en lugar de solicitar el genero Padre de todos los generos
     * a los que pertenece cada artista, busca por genero especifico.
     *
     * @param String $genre Clave del Genero de los artista a obtener,
     * @return Array de objetos Artist

     */
    function getArtistsByGenre($genre) {
        $methodName = "contributors";
        $params = array("isArtist" => "true", "genreKey" => $genre);

        $xmlResult = $this->call($methodName, $params);
        $results = array();
        foreach($xmlResult->children() as $item) {
            $results[] = new Artist($item['id'], $item['name']);
        }
        return $results;
    }


	function registerUser($userId) {
		$methodName = "endUser";
		$params = array("remoteEndUser" => $userId);

		$xmlResult = $this->call($methodName, $params);
		$results = array();
		foreach($xmlResult->children() as $item) {
		$results[] = new User($item['id'], $item['username']);
		}
		return $results;
	}



    /**
     * Obtiene el listado de generos (Una lista de objetos Genre, aninados)
     *
     * @param String $generoPadre   String identificando el genero padre de los generos a solicitar
     * @param Bool $soloGenerosPadres  Si es TRUE, solo devuelve generos padres
     */
    public function getGenres($generoPadre = "", $soloGenerosPadres = false) {
        $methodName = "genres";
        $params = array();
        if($soloGenerosPadres != false) {
            $params["mainGenresOnly"] = "true";
        }
        if($generoPadre != "") {
            $params["mainGenreKey"] = $generoPadre;
        }

        $xmlResult = $this->call($methodName, $params);
        $result = array();
        if($xmlResult != false) {

            $parents = $xmlResult->xpath("//genre[@mainGenreKey = '']");
            $children = $xmlResult->xpath("//genre[@mainGenreKey != '']");

            foreach($parents as $item) {
                $g = new Genre($item['id'][0], $item['key'][0], $item['position'][0], $item['mainGenreKey'][0], $item['name'][0]);
                $result[(String)$item['key']] = $g;
            }

            foreach($children as $item) {
                $parentKey = (String)$item->attributes()->mainGenreKey;
                $g = new Genre($item['id'][0], $item['key'][0], $item['position'][0], $item['mainGenreKey'][0], $item['name'][0]);
                $result[$parentKey]->addChild($g);
            }

        }
        return $result;


    }

    /**
     * Realiza la llamada al XML y obtiene su contenido.
     *
     *
     * @param String $methodName Nombre del metodo a ejecutar
     * @param Array<String>=String $params  Array con los parametros (Como keys) y sus valores (como values)
     * @return SimpleXMLObject si logra abrir la URL o FALSE si hay error de comunicación .
     */
    private function call($methodName, $params = array()){
	if($this->nContractId != null) {
        	$url = str_replace("<contractId>", $this->nContractId, WSMaxxClient::ENDPOINT);
	} else {
        	$url = str_replace("<contractId>", WSMaxxClient::CONTRACTID, WSMaxxClient::ENDPOINT);
	}
        $url .= $methodName."?";


        foreach($params as $name => $value) {
            $url .= $name."=".$value."&";
        }
        $url = substr($url, 0, strlen($url) - 1);


//die($url);


        $fp = fopen($url,"r");
        $xmlResponse = "";
        if($fp) {
            while(!feof($fp)) {
                $xmlResponse .= fread($fp, 256);
            }

            $simpleXMLObj = simplexml_load_string($xmlResponse);
            return $simpleXMLObj;
        } else {
            $this->errMsg = "Hubo un error al conectarse con la URL :" . $url;
            return false;
        }

    }


    public function getContentGroups(){
	$methodName = "contentGroups";
	$params = array("contentTypeKey" => "FULLTRACK");

	$xmlResult = $this->call($methodName, $params);
	$results = array();
	foreach($xmlResult as $items) {
		$g = new ContentGroup($items['id'][0], $items['name'][0]);
                $results[] = $g;
	}
	return $results;
    }


}
?>
