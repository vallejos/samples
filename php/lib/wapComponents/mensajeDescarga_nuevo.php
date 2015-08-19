<?php
include_once(dirname(__FILE__)."/../PrecioContenidos/BillingContenidoFunctions.php");
include_once(dirname(__FILE__)."/../PrecioContenidos/BillingContenido.php");

class MensajeDescarga_nuevo extends WapComponent{

	var $href_si;
	var $href_no;
	var $tipo;
	var $idCont;
	var $nombre_wap;
	var $show_preview;
	var $force_si;

	function MensajeDescarga_nuevo($href_si, $href_no, $tipo, $idCont = null){
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





/*
	function Display(){
		global $msisdn; //Esto es chancho, lo s茅... pido perd贸n... :(
		global $ua;
		global $db;
		if($msisdn != "" || IDEAS == 1) {
			$contenidoTpl = $this->_loadTemplate();

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
					if($datos['nombre_cont'] == "Juego" && !check_game_compat($db, $ua, $this->idCont)) {
						$html = "Este contenido no est谩 disponible para su m贸vil.";
					} else {
						$datos_cont = obtenerDatosContenido(null, $this->idCont, $datos['nombre_cont'] == "Juego");
						if($datos_cont['screenshots'] && $this->show_preview) {
							$imgPreview = new Imagen("getimage.php?path=http://www.wazzup.com.uy/".$datos_cont['screenshots'], $datos_cont['nombre']);
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

				$html = "Este contenido no est谩 disponible para su m贸vil.";
			}
		} else {
			$html =<<<HTML
				El contenido no se puede descargar en este momento, por favor intentelo m谩s tarde
HTML;
		}

		return $html;
	}
*/
function Display(){
		global $msisdn; //Esto es chancho, lo s茅... pido perd贸n... :(
		global $ua;
		global $db;
		if($msisdn != "" || IDEAS == 1) {
			$contenidoTpl = $this->_loadTemplate();

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
					 $datos = obtenerDatosCompra($this->tipo, $this->idCont);

					//Nuevo super-multi-loco sistema para obtener precio de los contenidos
					$id_wap = obtenerIDInteraccionWap($db, NOMBRE_MCM, OPERADORA_MCM);

					$pc = new BillingContenido($db, $this->idCont, $id_wap);
					$pc->ProcesarWap();
					$precio_contenido = $pc->ObtenerPrecio();


					if($precio_contenido == "") { //Si no tenemos un precio seteado para el precio, lo buscamos como lo haciamos antes.
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
					}




					if($datos['nombre_cont'] == "Juego" && !check_game_compat($db, $ua, $this->idCont)) {
						$html = "Este contenido no est嚒 disponible para su m梫il.";
					} else {
						$datos_cont = obtenerDatosContenido(null, $this->idCont, $datos['nombre_cont'] == "Juego");
						if($datos_cont['screenshots'] && $this->show_preview) {
							$imgPreview = new Imagen("getimage.php?path=http://www.wazzup.com.uy/".$datos_cont['screenshots'], $datos_cont['nombre']);
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

				$html = "Este contenido no est&#225; disponible para su m&#243;vil.";
			}
		} else {
			$html =<<<HTML
				El contenido no se puede descargar en este momento, por favor intentelo m&#225;s tarde
HTML;
		}

		return $html;
	}

}


?>
