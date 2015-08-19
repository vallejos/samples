<?php
include_once(dirname(__FILE__)."/constantes.php");

/**
 * La lista de links, contiene links... (big surprise there uh?)
 */
class ListaLinks extends WapComponent {

	var $estilo = LISTA_LINKS;
	var $listItemTemplate;
	var $has_menu_items;
	var $bullet;
	var $extra_class;
	var $titulo;


	/**
	 * Constructor
	 * Compo parametros puede recibir una lista de variables, que pueden ser objetos Links y/o Arrays de links
	 * ejemplo:
	 *    $l = new ListaLink(new Link("target"), new Link("target2"), array(new Link("target3"), new Link("target4")));
	 */
	function ListaLinks(){
		$this->template 		= "templates/listaLinks.tpl";
		$this->listItemTemplate = "templates/listItem.tpl";
		$this->has_menu_items   = false;
		$this->bullet 		    = "";
		$this->extra_class      = "";
		$this->titulo 			= "";

		//Si tiene links como parametros, los agregamos directamente
		for($i = 0; $i < func_num_args(); $i++) {
			$arg = func_get_arg($i);
			if(is_array(func_get_arg($i))) {
				foreach($arg as $argumento) {
					$this->AddComponent($argumento);	
				}	
			} else {
				$this->AddComponent($arg);
			}
		}
	}
	
	function setTitulo($t){
		$this->titulo = $t;
	}


	function _loadListItemTemplate(){
		$fp  = fopen(dirname(__FILE__)."/".$this->listItemTemplate, "r");
		$c = fread($fp, filesize(dirname(__FILE__)."/".$this->listItemTemplate));
		fclose($fp);
		return $c;
	}

	
	function setExtraClass($c){
		$this->extra_class = $c;
	}
	
	
	function hasMenuItems($value){
		$this->has_menu_items = $value;
	}
	
	/**
	 * Setea el elemento que se va a poner como bullet en el elemento de la lista
	 * si se elige como estilo, la lista intercalada.
	 * Se le puede pasar un Componente o texto plano
	 *
	 * @param mixed $item   El bullet, puede ser un componente o texto plano
	 */
	function setBullet($item){
		$this->bullet = $item;
	}
	/**
	 * Setea el estilo de la lista de links. Esto influirï¿½ en la forma en que la lista se 
	 * muestre en pantalla.
	 * 
	 * @param int  $estilo  El estilo a utilizar en la lista, valores: LISTA_LINKS, LISTA2X2_LINKS, NAVEGACION_LINKS
	 */
	function SetStyle($estilo){
		$this->estilo = $estilo;	
	}

	function Display(){
		global $db;	
		global $ua;
		$html = $this->_loadTemplate();
		$listItem = $this->_loadListItemTemplate();
		$html_links = "";
		$nro_columnas = 2;

		foreach($this->contenido as $i => $cont) {
			
			switch($this->estilo) {
				default:
				case LISTA_LINKS:
					//
					
					if(!$this->_soportaXHTML()) {
					  $html_links .= "<tr><td>".$cont->Display()."</td></tr>";
						//$html_links .= "<br/>";
					  $nro_columnas = 1;
					} else {
					    $html_links .= str_replace("#ITEM#", $cont->Display(), $listItem);
						$par_impar  = (($i+1) % 2 == 0)?"par":"impar";
					    $html_links = str_replace("#PAR_IMPAR#", "item-".$par_impar, $html_links);


						if(!$this->has_menu_items) {
							//$html_links .= "<tr><td><div class='clear' ></div></td></tr>";
						}
					}
				break;
				case LISTA_COLOR_LINKS:
					if($this->_soportaXHTML()) {
						$html_links .= str_replace("#ITEM#", $cont->Display(), $listItem);
						$par_impar  = (($i+1) % 2 == 0)?"par":"impar";
						$html_links = str_replace("#PAR_IMPAR#", "item-".$par_impar, $html_links);
					  	$nro_columnas = 1;
					} else {
						$html_links .= "<tr><td>".($i + 1).$cont->Display()."</td></tr>";
						//$html_links .= ($i + 1)."- ".$cont->Display()."<br/>";
						
					}
				break;
				case LISTA_NUMERADA_LINKS:
					if($this->_soportaXHTML()) {
						$html_links .= str_replace("#ITEM#", ($i + 1)."- ".$cont->Display(), $listItem);
						$par_impar  = (($i+1) % 2 == 0)?"par":"impar";
						$html_links = str_replace("#PAR_IMPAR#", "item-".$par_impar, $html_links);
						$nro_columnas = 1;
					} else {
					      $html_links .= "<tr><td>".($i + 1)."- ".$cont->Display()."</td></tr>";
						//$html_links .= ($i + 1)."- ".$cont->Display()."<br/>";
						
					}
				
				break;
				case LISTA_INTERCALADA_LINKS:
					$html_bullet = is_object($this->bullet)?$this->bullet->Display():$this->bullet;
					if($this->_soportaXHTML()) {
						$html_links .= str_replace("#ITEM#", $html_bullet." ".$cont->Display(), $listItem);
						$par_impar  = (($i+1) % 2 == 0)?"par":"impar";
						$html_links = str_replace("#PAR_IMPAR#", "item-".$par_impar, $html_links);
						$nro_columnas = 1;
					} else {
						$html_links .= "<tr><td>".$html_bullet." ".$cont->Display()."</td></tr>";
						//$html_links .= $html_bullet." ".$cont->Display()."<br/>";
						
					}
				  
				break;
				case LISTA2X2_LINKS:					
					if(get_class($cont) == "Link") { //si es un link, preguntamos si tiene una imagen dentro
						if($cont->hasImage()) { //si tiene una imagen dentro,  tenemos que ver el ancho máximo que podemos llegar a tener en la pantalla
									//porque se puede dar que tengamos 2 links, con preview, y la suma de sus anchos, sea mayor al ancho máximo de la pantalla
							if(miscFunctions::soportaAnchoChico($db, $ua)) {
								$cont->setImageWidth(40); //Seteado a fuego a 40px de ancho
							} else if(miscFunctions::soportaAnchoMedio($db, $ua)) {
								$cont->setImageWidth(50);
							}

						}	
					}
					if($this->_soportaXHTML()) { //Si estamos en XHTML-MP usamos el template del item de la lista
						$html_links .= str_replace( "#ITEM#", $cont->Display(), $listItem);
					  } else {
						if(count($cont->getText()) > 0) {
							$cont->removeText();	
						}
					     $html_links .= str_replace( "#ITEM#", $cont->Display(), $listItem);
					}
						if( (($i + 1) % 2) == 0) {
						      //si es par, entonces, dejamos el segundo TR, para cerrar el par, y sacamos el primero
						         $html_links = str_replace("#TRF#", "",   $html_links);
						         $html_links = str_replace("#/TRF#", "",   $html_links);
						         $html_links = preg_replace("/#TRI#.+#\/TRI#/", "", $html_links);
						//	 $html_links .= "<div class=\"clear\" ></div>";
						} else {
						      //Si es impar, dejamos el primer TR y sacamos el final
							$html_links = str_replace("#TRI#", "",   $html_links);
						        $html_links = str_replace("#/TRI#", "",   $html_links);

						      $html_links = preg_replace("/#TRF#.+#\/TRF#/", "", $html_links);
						}
						$nro_columnas = 2;
				break;
				case NAVEGACION_LINKS:
					$html_links .= $cont->Display()." | ";
					$html_links = trim(trim($html_links), "|");
				
				break;
			}
		}

	      
	      $html_links = str_replace("#TRF#", "",   $html_links);
	      $html_links = str_replace("#/TRF#", "",   $html_links);
	      $html_links = str_replace("#TRI#", "",   $html_links);
	      $html_links = str_replace("#/TRI#", "",   $html_links);
	      if(count($this->contenido) % 2 != 0 && $this->estilo == LISTA2X2_LINKS ){ 
		      $html_links .= "</tr>"; 
	      }

		if($this->_soportaXHTML()) { //Si estamos en XHTML-MP usamos el template del item de la lista
		//	$html_links .= "<div class=\"clear\" ></div>";
		}
		$html_links = str_replace("#PAR_IMPAR#", "", $html_links);
		$html = str_replace("#TITULO#", $this->titulo, $html);
		$html = str_replace("#COLUMNAS#", $nro_columnas,   $html);
		$html = str_replace("#LINKS#", $html_links, $html);
		$html = str_replace("#CLASE-EXT#", $this->extra_class, $html);

		return $html;
	
	}


}


?>
