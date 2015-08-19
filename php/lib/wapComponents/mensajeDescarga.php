<?php

class MensajeDescarga extends WapComponent{

	var $href_si;
	var $href_no;
	var $tipo;
	var $idCont;
	var $nombre_wap;
	var $show_preview;
	var $force_si;

	function MensajeDescarga($href_si, $href_no, $tipo, $idCont = null){
		$this->template = "templates/mensajeDescarga.tpl";
		$this->href_si = $href_si;
		$this->href_no = $href_no;
		$this->tipo = $tipo;
		$this->idCont = $idCont;
		$this->nombre_wap = "";
		$this->show_preview = true;
		$this->force_si = false;
	}

	function setWapName($n){
		$this->nombre_wap = $n;
	}

	function hidePreview(){
		$this->show_preview = false;
	}

	function forceSiUrl($url){
		$this->force_si = true;
		$this->href_si = $url;
	}

	

	function Display(){
		global $msisdn; //Esto es chancho, lo sé... pido perdón... :(
		global $ua;
		global $db;
		if($msisdn != "" || IDEAS == 1) {
			$contenidoTpl = $this->_loadTemplate();
		/*
			if(!$this->force_si) {
				$url_descarga = urlWapPushAncel($msisdn, $this->idCont, false, servicio3G(), $this->nombre_wap);
			} else {
				$url_descarga = $this->href_si;
			}
			*/
			$url_descarga = $this->href_si;

			$html = str_replace("#RESPUESTASI#", $url_descarga, $contenidoTpl);
			if(IDEAS == 1) { //Para el caso de IDEAS, se debe obtener el ID del tipo de contenidos de WAZZUP en lugar del de Ideas
				$datos_ideas = obtenerDatosCont($db, $this->idCont);
				$tipo_ideas = $datos_ideas['tipo_ideas'];
				$this->tipo = $datos_ideas['tipo'];

			}

//echo $this->tipo."..";
			if(soportaContenidoPorTipo($db, $ua, $this->tipo)) {

				if($this->idCont != 0) {
					if(IDEAS == 1) {
						$datos = obtenerDatosCompra($tipo_ideas, $this->idCont);
					} else {
				        $datos = obtenerDatosCompra($this->tipo, $this->idCont);
					    if(TIGO_CO == 1) {
					        $oPrecio = new PreciosContenidos($db);
					        $precio_contenido = $oPrecio->devolverPrecio("tigo_co", $this->idCont);
					     } else {	  					        
					        $precio_contenido = $datos["precio"];
					     }
					} 
					if( ($datos['nombre_cont'] == "Juego" || $datos['nombre'] == "Juego") && !check_game_compat($db, $ua, $this->idCont)) {
						$html = "Este contenido no esta disponible para su movil.";
					} else {
						$datos_cont = obtenerDatosContenido(null, $this->idCont, ($datos['nombre_cont'] == "Juego" || $datos['nombre'] == 'Juego'));
    
                        if($datos_cont['screenshots'] && $this->show_preview) {
						    if(OPERADORA == 'personal_ar') {

						        $url_img_preview = $datos_cont['caja'];
                            } else {
                                $url_img_preview = "http://www.wazzup.com.uy/".$datos_cont['screenshots'];
                            }
							$imgPreview = new Imagen("getimage.php?path=".$url_img_preview, $datos_cont['nombre']);
							$html = str_replace("#IMG_PREVIEW#", $imgPreview->Display(), $html);
						} else {
							$html = str_replace("#IMG_PREVIEW#", "", $html);
						}
						$html = str_replace("#NOMBRE_CONTENIDO#", $datos_cont['nombre'], $html);
					}
				}

				$html = str_replace("#IMG_PREVIEW#", "", $html);

				$html = str_replace("#RESPUESTANO#", $this->href_no, $html);
				if(TIGO_CO == 1) { 
				      $html = str_replace("#PRECIO#", $precio_contenido, $html);
				} else { 
				    $version_wap = ($this->_soportaXHTML()) ? "xhtml" : "wml";
				    $precio = (isset($datos["precio_$version_wap"])) ? $datos["precio_$version_wap"] : $datos['precio'];
				    $html = str_replace("#PRECIO#", $precio, $html);
				}
				$html = str_replace("#NOMBRETIPO#", $datos['nombre_cont'], $html);
			} else {

				$html = "Este contenido no está disponible para su móvil.";
			}
		} else {
			$html =<<<HTML
				El contenido no se puede descargar en este momento, por favor intentelo más tarde
HTML;
		}

		return $html;
	}


}


?>
