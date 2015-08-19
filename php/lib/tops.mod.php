<? 

/**
 * Tops
 */
class Top extends WapComponent {
    private $cant_tops;
    private $array_contenidos = array();
    private $array_tops = array();
    private $array_tipos = array();
    private $array_marcas = array();
    private $array_titulos_tops = array();
    private $hay_top;
    private $weed;
    private $debug; // 0=sin debug, 1=solo warnings y errores, 2=print total de procesos
    private $debug_msg;
    private $db;
    private $ua;
    private $num_top;
    private $html;
    private $to;
    private $universal_push;
    
    private $array_show_preview = array();
    private $array_align_notitle = array();
    private $array_text_notitle = array();
    private $array_font_size = array();
    private $array_align_items = array();
    private $array_style_items = array();
    private $array_xxx = array();
    private $array_subtexto = array();
    
    private $array_textos_links_dl = array();
    private $operadora;

    /**
     * Tops Constructor
     *
     * @param dblink $db
     * @param int $weed
     * @return Tops
     */
    function Top($db, $weed, $num_top, $ua, $debug=0, $to="leonardo.hernandez@globalnetmobile.com") {
        global $nombre_wap;
        global $celular; //para el debug nada mas
        
        // modo debug
        $this->debug = $debug;
        $this->to = $to;
        
        // WAP ID
        $this->weed = $weed;
        if ($this->debug == 2) $this->debug_msg .= "[DEBUG] WEED: $this->weed.\n";
        $this->universal_push = "&amp;push=$nombre_wap";
        
        // db
        $this->db = $db;
        
        // seteos de la wap
        $this->ua = $ua;
        $this->num_top = $num_top; // numero de top a mostrar
        if ($this->debug == 2) $this->debug_msg .= "[DEBUG] USERAGENT: $this->ua.\n";
        if ($this->debug == 2) $this->debug_msg .= "[DEBUG] MSISDN: $celular.\n";
        if ($this->debug == 2) $this->debug_msg .= "[DEBUG] MOSTRANDO TOP: $this->num_top.\n";
        
        // cargo datos
        $sql = "SELECT wts.id_contenido, wt.tipo_contenido, wts.marca, wts.id_top, wt.titulo_top, wt.style_items, wt.align_items, wt.font_size, wt.text_notitle, wt.align_notitle, wt.show_preview, cc.xxx, wt.texto_link_dl, wt.subtexto, w.operadora   
            FROM admins.wtm_tops_secciones wts, admins.wtm_tops wt, admins.wtm_waps w, Web.contenidos_cat cc, Web.contenidos c 
            WHERE wt.visible=1 AND wts.visible=1 AND wt.id_wap=w.id AND wts.id_top=wt.id AND wts.id_contenido=c.id AND c.categoria=cc.id 
            AND w.id=$this->weed 
            ORDER BY wt.orden,wts.orden ASC";
        if ($this->debug == 2) $this->debug_msg .= "[DEBUG] SQL: $sql.\n";
        $dbr = mysql_query($sql, $this->db);
        
        if (!$dbr) {
            $this->hay_top = false; // hay o no hay top?
            if ($this->debug == 2) $this->debug_msg .= "[WARN] No se encontro el top especificado, id $weed.\n";
        }

        $i=0;
        while ($row = mysql_fetch_array($dbr)) {
            list ($id_contenido,$tipo_contenido,$marca,$id_top,$titulo_top,$style_items,$align_items,$font_size,$text_notitle,$align_notitle,$show_preview,$xxx,$texto_link_dl,$subtexto,$operadora) = $row;

            $this->operadora = $operadora;
            $this->array_align_notitle[$id_top] = $align_notitle;
            $this->array_text_notitle[$id_top] = $this->escape4Wap($text_notitle);
            $this->array_font_size[$id_top] = $font_size;
            $this->array_align_items[$id_top] = $align_items;
            $this->array_style_items[$id_top] = $style_items;
            $this->array_show_preview[$id_top] = $show_preview;
            $this->array_subtexto[$id_top] = $this->escape4Wap($subtexto);
            
            if (!in_array($id_top, $this->array_tops)) {
                $i++;
                $this->array_tops[$i] = $id_top;
            }
            $this->array_contenidos[$id_top][] = $id_contenido;
            $this->array_titulos_tops[$id_top] = $this->escape4Wap($titulo_top);
            $this->array_tipos_tops[$id_top] = $tipo_contenido;
            $this->array_marcas[$id_contenido] = $marca;
            $this->array_xxx[$id_contenido] = $xxx;
            $this->array_textos_links_dl[$id_top] = $this->escape4Wap($texto_link_dl);
        }
        if ($i>0) $this->hay_top = true;
        else $this->hay_top = false;
        if ($this->debug == 2) $this->debug_msg .= "[DEBUG] Tops cargados: $i.\n";
    }

    /**
     * Dark Function MuaHaHahaha >:)
     *
     * @param string $style
     * @return unknown
     */
    function getStyle($style) {
        switch ($style) {
            case "CENTER":
                $css = "center";
                break;
            case "LEFT":
                $css = "left";
                break;
            case "RIGHT":
                $css = "right";
                break;
            case "SMALL_FONT_SIZE":
                $css = SMALL_FONT_SIZE;
                break;
            case "NORMAL_FONT_SIZE":
                $css = NORMAL_FONT_SIZE;
                break;
            case "BIG_FONT_SIZE":
                $css = BIG_FONT_SIZE;
                break;
            case "LISTA2X2_LINKS":
                $css = LISTA2X2_LINKS;
                break;
            case "LISTA_LINKS":
                $css = LISTA_LINKS;
                break;
            case "LISTA_NUMERADA_LINKS":
                $css = LISTA_NUMERADA_LINKS;
                break;
            case "SECCION_SIN_TITULO":
                $css = SECCION_SIN_TITULO;
                break;
        }
        return $css;
    }
    
    
    /**
     * Envia por mail toda la info recolectada en el debug
     *
     * @return mail
     */
    function logMyDebug() {
        $headers = "";
        $msg = "[".date("Y-m-d H:i:s")."]\n".$this->debug_msg;
//        mail($this->to,"(DEBUG) tops.mod.php", $msg, $headers);
        return true;
    }
    
    
    
    function escape4Wap($text) { 
	    $text = utf8_encode($text);
        $text = str_replace("&", "&amp;", $text);
        $text = str_replace("%26", "&amp;", $text);
        $text = str_replace("¿", "&#191;", $text);
        $text = str_replace("ñ", "&#241;", $text);
        $text = str_replace("á", "&#225;", $text);
        $text = str_replace("é", "&#233;", $text);
        $text = str_replace("í", "&#237;", $text);
        $text = str_replace("ó", "&#243;", $text);
        $text = str_replace("ú", "&#250;", $text);
        $text = str_replace("Ñ", "&#209;", $text);
        $text = str_replace("Á", "&#193;", $text);
        $text = str_replace("É", "&#201;", $text);
        $text = str_replace("Í", "&#205;", $text);
        $text = str_replace("Ó", "&#211;", $text);
        $text = str_replace("Ú", "&#218;", $text);

    	return $text;
    }
    
    
    /**
     * Muestra un top en la wap
     *
     * @param dblink $db
     * @param int $num_top
     */
    function Display() {
        $id_top = $this->array_tops[$this->num_top]; // id del top
        if ($this->debug == 2) $this->debug_msg .= "[DEBUG] ID Top: $id_top.\n";
        if ($id_top>0) {
            $titulo_top = $this->array_titulos_tops[$id_top]; // titulo del top
            $cant_items = sizeof($this->array_contenidos[$id_top]); // cantidad de contenidos en el top
            $tipo_top = $this->array_tipos_tops[$id_top];

            if ($this->debug == 2) $this->debug_msg .= "[DEBUG] Titulo Top: $titulo_top\n";
            if ($this->debug == 2) $this->debug_msg .= "[DEBUG] Tipo Top: $tipo_top.\n";
            if ($this->debug == 2) $this->debug_msg .= "[DEBUG] Items en Top: $cant_items.\n";
            
            if ($cant_items > 0) {
                if (soportaContenidoPorTipo($this->db, $this->ua, $tipo_top)) {
                    if ($this->debug == 2) $this->debug_msg .= "[DEBUG] El celular $this->ua soporta el tipo de contenido $tipo_top.\n";
                    switch ($tipo_top) {
                        case 31:
                            // Juegos
                            if ($this->debug == 2) $this->debug_msg .= "[DEBUG] Tipo Detectado: $tipo_top-Juegos.\n";
                            return $this->displayTopJuegos($id_top);
                            break;
                        case 28:
                            // Monofonicos
                            $this->debug_msg .= "[CRIT] Tipo No soportado por el sistema: $tipo_top-Monofonicos.\n";
                            return "";
                            break;
                        case 23:
                            // MP3/Truetones
                            if ($this->debug == 2) $this->debug_msg .= "[DEBUG] Tipo Detectado: $tipo_top-MP3/Truetones.\n";
                            return $this->displayTopMP3Truetones($id_top);
                            break;
                        case 29:
                            // Polifonicos
                            if ($this->debug == 2) $this->debug_msg .= "[DEBUG] Tipo Detectado: $tipo_top-Polifonicos.\n";
                            return $this->displayTopPolifonicos($id_top);
                            break;
                        case 5:
                            // Screensavers
                            if ($this->debug == 2) $this->debug_msg .= "[DEBUG] Tipo Detectado: $tipo_top-Screensavers.\n";
                            return $this->displayTopScreensavers($id_top);
                            break;
                        case 17:
                            // Sounds FX
                            $this->debug_msg .= "[CRIT] Tipo No soportado por el sistema: $tipo_top-Sound FX.\n";
                            return "";
                            break;
                        case 62:
                            // Videos
                            if ($this->debug == 2) $this->debug_msg .= "[DEBUG] Tipo Detectado: $tipo_top-Videos.\n";
                            return $this->displayTopVideos($id_top);
                            break;
                        case 65:
                            // VideoTones
                            if ($this->debug == 2) $this->debug_msg .= "[DEBUG] Tipo Detectado: $tipo_top-VideoTones.\n";
                            return $this->displayTopVideoTones($id_top);
                            break;
                        case 7:
                            // Wallpapers
                            if ($this->debug == 2) $this->debug_msg .= "[DEBUG] Tipo Detectado: $tipo_top-Wallpapers.\n";
                            return $this->displayTopWallpapers($id_top);
                            break;
                        default:
                            // otro error, tipo no soportado
                            if ($this->debug > 0) $this->debug_msg .= "[WARN] El top $id_top contiene tipos no soportados: $tipo_top.\n";
                    }
                } else {
                    // el celular no soporta el tipo de contenido
                    if ($this->debug > 0) $this->debug_msg .= "El celular $ua no soportada el tipo de contenido $tipo_top.\n";
                }
            } else {
                // no hay elementos a mostrar en este top
                if ($this->debug > 0) $this->debug_msg .= "[WARN] El top $id_top no contiene elementos.\n";
            }
        } else {
            // no existe el top especificado
            if ($this->debug > 0) $this->debug_msg .= "[WARN] No existe el top especificado: $id_top.\n";
        }
    }


    /**
     * Muestra el Top de Screensavers
     *
     * @param Iint $id_top
     */
    function displayTopScreensavers($id_top) {
        $ids_tops = $this->array_contenidos[$id_top]; // array con los id a mostrar
        
        if (!$this->array_titulos_tops[$id_top]) {
            $extraparam = "SECCION_SIN_TITULO";
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]), $extraparam);
        } else {
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]));
        }
    	$listaTop = new ListaLinks();
    	if ($this->array_style_items[$id_top] != "BULLETS") $listaTop->SetStyle($this->getStyle($this->array_style_items[$id_top]));
    	foreach ($ids_tops as $id) {
    		$datos = obtenerDatosContenido($this->db, $id);
    		if ($this->operadora == "claro" || $this->operadora == "porta") {
        		$href = "hacer_descarga.php?xxx={$this->array_xxx[$id]}&amp;step=2&amp;tipoCat=5{$this->universal_push}&amp;cat=".$datos['categoria']."&amp;id=".$id;
    		} else {
                
                
                
                
                
        		$href = "images.php?xxx={$this->array_xxx[$id]}&amp;step=2&amp;tipoCat=5{$this->universal_push}&amp;cat=".$datos['categoria']."&amp;id=".$id;
    		}
    		($this->array_textos_links_dl[$id_top] != "") ? $nombre = $this->array_textos_links_dl[$id_top] : $nombre = $this->escape4Wap($datos['nombre']);
    		($this->array_subtexto[$id_top] == "1") ? $autor = "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$this->escape4Wap($datos["autor"]) : $autor = "";
    		if ($this->array_style_items[$id_top] == "BULLETS") {
    		    $link = new MenuItem("images/bullet.gif", $nombre, $href, $autor);
    		} else {
                if ($this->array_show_preview[$id_top] == "1") {
                    if (file_exists("getimage.php")){
                        $link = new Link($href, $nombre, "getimage.php?path=".PATH_PREVIEW.$datos['screenshots'], TOP_SIDE, null, $autor);
                    } else{
            		    $link = new Link($href, $nombre, PATH_PREVIEW.$datos['screenshots'], TOP_SIDE, null, $autor);
                    }
                } else {
            		$link = new Link($href, $nombre, null, null, null, $autor);
                }
    		}
			switch($this->array_marcas[$id]){
				case 'new':
					$link->setNew(true);
					break;
				case 'hot':
					$link->setHot(true);
					break;
				case 'hit':
					$link->setHit(true);
					break;
			}    		
    		$listaTop->AddComponent($link);
    	}
    	
    	$seccionTop->AddComponent ($listaTop);
    	$this->html .= $seccionTop->Display();

    	if (($this->array_text_notitle[$id_top] != "")) {
        	$seccionVerMas = new Seccion ("", $this->getStyle($this->array_align_notitle[$id_top]), $this->getStyle($this->array_font_size[$id_top]), SECCION_SIN_TITULO);
        	$seccionVerMas->AddComponent (new Link ("images.php?tipoCat=5{$this->universal_push}&amp;step=0", $this->array_text_notitle[$id_top]));
        	$this->html .= $seccionVerMas->Display();
    	}
        	
    	if ($this->debug == "2") $this->logMyDebug();
    	return $this->html;
    }
    
    
    /**
     * Muestra el Top de Polifonicos
     *
     * @param Iint $id_top
     */
    function displayTopPolifonicos($id_top) {
        $ids_tops = $this->array_contenidos[$id_top]; // array con los id a mostrar
    
        if (!$this->array_titulos_tops[$id_top]) {
            $extraparam = "SECCION_SIN_TITULO";
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]), $extraparam);
        } else {
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]));
        }
    	$listaTop = new ListaLinks();
    	if ($this->array_style_items[$id_top] != "BULLETS") $listaTop->SetStyle($this->getStyle($this->array_style_items[$id_top]));
    	foreach($ids_tops as $id) {
    		$datos = obtenerDatosContenido($this->db, $id);
    		if ($this->operadora == "claro" || $this->operadora == "porta") {
        		$href = "hacer_descarga.php?xxx={$this->array_xxx[$id]}&amp;step=2&amp;tipoCat=29{$this->universal_push}&amp;id=$id&amp;cat={$datos['categoria']}";
    		} else {
        		$href = "ringtones.php?xxx={$this->array_xxx[$id]}&amp;step=2&amp;tipoCat=29{$this->universal_push}&amp;id=$id&amp;cat={$datos['categoria']}";
    		}
    		($this->array_textos_links_dl[$id_top] != "") ? $nombre = $this->array_textos_links_dl[$id_top] : $nombre = $this->escape4Wap($datos['nombre']);
    		($this->array_subtexto[$id_top] == "1") ? $autor = "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$this->escape4Wap($datos["autor"]) : $autor = "";
    		if ($this->array_style_items[$id_top] == "BULLETS") {
    		    $link = new MenuItem("images/bullet.gif", $nombre, $href, $autor);
    		} else {
        		$link = new Link($href, $nombre, null, null, null, $autor);
    		}
            switch($this->array_marcas[$id]){
				case 'new':
					$link->setNew(true);
					break;
				case 'hot':
					$link->setHot(true);
					break;
				case 'hit':
					$link->setHit(true);
					break;
			}    		
    		$listaTop->AddComponent($link);
    	}
    	$seccionTop->AddComponent($listaTop);
    	$this->html .= $seccionTop->Display();

    	if (($this->array_text_notitle[$id_top] != "")) {
        	$seccionVerMas = new Seccion("", $this->getStyle($this->array_align_notitle[$id_top]), $this->getStyle($this->array_font_size[$id_top]), SECCION_SIN_TITULO);
        	$seccionVerMas->AddComponent(new Link("ringtones.php?step=0{$this->universal_push}&amp;tipoCat=29", $this->array_text_notitle[$id_top]));
        	$this->html .= $seccionVerMas->Display();
    	}
        	
    	if ($this->debug == "2") $this->logMyDebug();
    	return $this->html;        
    }
    
    
    /**
     * Muestra el Top de Juegos
     *
     * @param Iint $id_top
     */
    function displayTopJuegos($id_top) {
        $ids_tops = $this->array_contenidos[$id_top]; // array con los id a mostrar
        $i=0;
        
        if (!$this->array_titulos_tops[$id_top]) {
            $extraparam = "SECCION_SIN_TITULO";
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]), $extraparam);
        } else {
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]));
        }
    	$listaTop = new ListaLinks();
    	if ($this->array_style_items[$id_top] != "BULLETS") $listaTop->SetStyle($this->getStyle($this->array_style_items[$id_top]));
    	foreach ($ids_tops as $id) {
    	    if(check_game_compat($this->db, $this->ua, $id)) {
    			$datos = obtenerDatosContenido($this->db, $id, true);
        		if ($this->operadora == "ancel") $img =  "getimage.php?path=".PREVIEW_HOST."/netuy/java/cajas/".$id.".gif";
			else $img = PATH_PREVIEW."/netuy/java/cajas/".$id.".gif";
        		if ($this->operadora == "claro" || $this->operadora == "porta") {
            			$href = "hacer_descarga.php?xxx={$this->array_xxx[$id]}&amp;b=h&amp;step=2{$this->universal_push}&amp;id=".$id."&amp;cat=".$datos['categoria']."&amp;tipoCat=".$datos['tipo'];
        		} else {
        			$href = "games.php?xxx={$this->array_xxx[$id]}&amp;b=h&amp;step=2{$this->universal_push}&amp;id=".$id."&amp;cat=".$datos['categoria']."&amp;tipoCat=".$datos['tipo'];
        		}
        		($this->array_textos_links_dl[$id_top] != "") ? $nombre = $this->array_textos_links_dl[$id_top] : $nombre = $this->escape4Wap($datos['nombre']);
        		($this->array_subtexto[$id_top] == "1") ? $autor = "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$this->escape4Wap($datos["autor"]) : $autor = "";
        		if ($this->array_style_items[$id_top] == "BULLETS") {
        		    $link = new MenuItem("images/bullet.gif", $nombre, $href, $autor);
        		} else {
				if ($this->array_show_preview[$id_top] == "1") {
					$link = new Link($href, $nombre, $img, TOP_SIDE, null, $autor);
				} else {
					$link = new Link($href, $nombre, null, null, null, $autor);
				}
			}
                switch($this->array_marcas[$id]) {
    				case 'new':
    					$link->setNew(true);
    					break;
    				case 'hot':
    					$link->setHot(true);
    					break;
    				case 'hit':
    					$link->setHit(true);
    					break;
    			}
        		$listaTop->AddComponent($link);
        		$i++;
    	    } else {
    	        // debug
    	        
    	    }
    	}
    	
    	if ($i>0) {
        	$seccionTop->AddComponent ($listaTop);
        	$this->html .= $seccionTop->Display();
    	
        	if (($this->array_text_notitle[$id_top] != "")) {
            	$seccionVerMas = new Seccion ("", $this->getStyle($this->array_align_notitle[$id_top]), $this->getStyle($this->array_font_size[$id_top]), SECCION_SIN_TITULO);
            	$seccionVerMas->AddComponent (new Link ("games.php?step=0{$this->universal_push}&amp;tipoCat=31", $this->array_text_notitle[$id_top]));
            	$this->html .= $seccionVerMas->Display();
        	}

        }
            	
    	if ($this->debug == "2") $this->logMyDebug();
    	return $this->html;
    }
    
    
    /**
     * Muestra el Top de Wallpapers
     *
     * @param int $id_top
     */
    function displayTopWallpapers($id_top) {
        $ids_tops = $this->array_contenidos[$id_top]; // array con los id a mostrar
        
        if (!$this->array_titulos_tops[$id_top]) {
            $extraparam = "SECCION_SIN_TITULO";
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]), $extraparam);
        } else {
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]));
        }
    	$listaTop = new ListaLinks();
    	if ($this->array_style_items[$id_top] != "BULLETS") $listaTop->SetStyle($this->getStyle($this->array_style_items[$id_top]));
    	foreach ($ids_tops as $id) {
    		$datos = obtenerDatosContenido($this->db, $id);
    		if ($this->operadora == "claro" || $this->operadora == "porta") {
        		$href = "hacer_descarga.php?xxx={$this->array_xxx[$id]}&amp;step=2&amp;tipoCat=7{$this->universal_push}&amp;cat=".$datos['categoria']."&amp;id=".$id;
    		} else {
        		$href = "images.php?xxx={$this->array_xxx[$id]}&amp;step=2&amp;tipoCat=7{$this->universal_push}&amp;cat=".$datos['categoria']."&amp;id=".$id;
    		}
    		($this->array_textos_links_dl[$id_top] != "") ? $nombre = $this->array_textos_links_dl[$id_top] : $nombre = $this->escape4Wap($datos['nombre']);
    		($this->array_subtexto[$id_top] == "1") ? $autor = "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$this->escape4Wap($datos["autor"]) : $autor = "";
    		if ($this->array_style_items[$id_top] == "BULLETS") {
    		    $link = new MenuItem("images/bullet.gif", $nombre, $href, $autor);
    		} else {
                if ($this->array_show_preview[$id_top] == "1") {
			if (file_exists("getimage.php")) $link = new Link($href, $nombre, "getimage.php?path=".PATH_PREVIEW.$datos['screenshots'], TOP_SIDE, null, $autor);
			else $link = new Link($href, $nombre, PATH_PREVIEW.$datos['screenshots'], TOP_SIDE, null, $autor);
                } else {
            		$link = new Link($href, $nombre, null, null, null, $autor);
                }
    		}
			switch($this->array_marcas[$id]){
				case 'new':
					$link->setNew(true);
					break;
				case 'hot':
					$link->setHot(true);
					break;
				case 'hit':
					$link->setHit(true);
					break;
			}
    		$listaTop->AddComponent($link);
    	}
    	
    	$seccionTop->AddComponent ($listaTop);
    	$this->html .= $seccionTop->Display();

    	if (($this->array_text_notitle[$id_top] != "")) {
        	$seccionVerMas = new Seccion ("", $this->getStyle($this->array_align_notitle[$id_top]), $this->getStyle($this->array_font_size[$id_top]), SECCION_SIN_TITULO);
        	$seccionVerMas->AddComponent (new Link ("images.php?tipoCat=7{$this->universal_push}&amp;step=0", $this->array_text_notitle[$id_top]));
        	$this->html .= $seccionVerMas->Display();
    	}

    	if ($this->debug == "2") $this->logMyDebug();
    	return $this->html;
    }


    /**
     * Muestra el top de Videos
     *
     * @param unknown_type $id_top
     * @return unknown
     */
    function displayTopVideos($id_top) {
        $ids_tops = $this->array_contenidos[$id_top]; // array con los id a mostrar
        
        if (!$this->array_titulos_tops[$id_top]) {
//            $extraparam = "SECCION_SIN_TITULO";
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]), $extraparam);
        } else {
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]));
        }
    	$listaTop = new ListaLinks();
    	if ($this->array_style_items[$id_top] != "BULLETS") $listaTop->SetStyle($this->getStyle($this->array_style_items[$id_top]));
    	foreach ($ids_tops as $id) {
    		$datos = obtenerDatosContenido($this->db, $id);
    		$cat = $datos['categoria'];
    		$id = $datos['id'];
    		if ($this->operadora == "claro" || $this->operadora == "porta") {
        		$href = "hacer_descarga.php?xxx={$this->array_xxx[$id]}&amp;b=h&amp;step=2{$this->universal_push}&amp;cat=$cat&amp;tipoCat=62&amp;id=".$id."&amp;b=h";
    		} else {
        		$href = "videos.php?xxx={$this->array_xxx[$id]}&amp;b=h&amp;step=2{$this->universal_push}&amp;cat=$cat&amp;tipoCat=62&amp;id=".$id."&amp;b=h";
    		}
    		$ruta = $datos['screenshots'];
    		$carpeta = calcularCarpeta($id);
    		($this->array_textos_links_dl[$id_top] != "") ? $nombre = $this->array_textos_links_dl[$id_top] : $nombre = $this->escape4Wap($datos['nombre']);
    		($this->array_subtexto[$id_top] == "1") ? $autor = "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$this->escape4Wap($datos["autor"]) : $autor = "";
    		if ($this->array_style_items[$id_top] == "BULLETS") {
    		    $link = new MenuItem("images/bullet.gif", $nombre, $href);
    		} else {
			if ($this->array_show_preview[$id_top] == "1") {
				if (file_exists("getimage.php")) $link = new Link($href, $nombre, "getimage.php?path=http://www.wazzup.com.uy/$ruta", TOP_SIDE, null, $autor); 
				else $link = new Link($href, $nombre, "http://www.wazzup.com.uy/$ruta", TOP_SIDE, null, $autor); 
			} else {
			$link = new Link($href, $nombre, null, null, null, $autor); 
			}
		}
            switch($this->array_marcas[$id]){
				case 'new':
					$link->setNew(true);
					break;
				case 'hot':
					$link->setHot(true);
					break;
				case 'hit':
					$link->setHit(true);
					break;
			}    		
    		$listaTop->AddComponent($link);
    	}
    	$seccionTop->AddComponent ($listaTop);
    	$this->html .= $seccionTop->Display();
    	
    	if (($this->array_text_notitle[$id_top] != "")) {
        	$seccionVerMas = new Seccion ("", $this->getStyle($this->array_align_notitle[$id_top]), $this->getStyle($this->array_font_size[$id_top]), SECCION_SIN_TITULO);
        	$seccionVerMas->AddComponent (new Link ("videos.php?step=0{$this->universal_push}&amp;tipoCat=62", $this->array_text_notitle[$id_top]));
        	$this->html .= $seccionVerMas->Display();
    	}
        	
    	if ($this->debug == "2") $this->logMyDebug();
    	return $this->html;
    }


    /**
     * Muestra el top de MP3/Truetones
     *
     * @param int $id_top
     * @return unknown
     */
    function displayTopMP3Truetones($id_top) {
        $ids_tops = $this->array_contenidos[$id_top]; // array con los id a mostrar
    
        if ($this->array_titulos_tops[$id_top] == "") {
            $extraparam = "SECCION_SIN_TITULO";
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]), $this->getStyle($extraparam));
        } else {
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]));
        }
    	$listaTop = new ListaLinks();
    	if ($this->array_style_items[$id_top] != "BULLETS") $listaTop->SetStyle($this->getStyle($this->array_style_items[$id_top]));
    	foreach($ids_tops as $id) {
    		$datos = obtenerDatosContenido($this->db, $id);
    		if ($this->operadora == "claro" || $this->operadora == "porta") {
        		$href = "hacer_descarga.php?xxx={$this->array_xxx[$id]}&amp;step=2{$this->universal_push}&amp;tipoCat=23&amp;id=$id&amp;cat={$datos['categoria']}";
    		} else {
        		$href = "ringtones.php?xxx={$this->array_xxx[$id]}&amp;step=2{$this->universal_push}&amp;tipoCat=23&amp;id=$id&amp;cat={$datos['categoria']}";
    		}
    		($this->array_textos_links_dl[$id_top] != "") ? $nombre = $this->array_textos_links_dl[$id_top] : $nombre = $this->escape4Wap($datos['nombre']);
    		($this->array_subtexto[$id_top] == "1") ? $autor = "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$this->escape4Wap($datos["autor"]) : $autor = "";
    		if ($this->array_style_items[$id_top] == "BULLETS") {
    		    $link = new MenuItem("images/bullet.gif", $nombre, $href, $autor);
    		} else {
    		  $link = new Link($href, $nombre, null, null, null, $autor);
    		}
            switch($this->array_marcas[$id]){
				case 'new':
					$link->setNew(true);
					break;
				case 'hot':
					$link->setHot(true);
					break;
				case 'hit':
					$link->setHit(true);
					break;
			}
    		$listaTop->AddComponent($link);
    	}
    	$seccionTop->AddComponent($listaTop);
    	$this->html .= $seccionTop->Display();
    	
    	if (($this->array_text_notitle[$id_top] != "")) {
        	$seccionVerMas = new Seccion("", $this->getStyle($this->array_align_notitle[$id_top]), $this->getStyle($this->array_font_size[$id_top]), SECCION_SIN_TITULO);
        	$seccionVerMas->AddComponent(new Link("ringtones.php?step=0{$this->universal_push}&amp;tipoCat=23", $this->array_text_notitle[$id_top]));
        	$this->html .= $seccionVerMas->Display();
    	}
    	
    	if ($this->debug == "2") $this->logMyDebug();
    	return $this->html;
    }



    /**
     * Muestra el top de VideoTones
     *
     * @param unknown_type $id_top
     * @return unknown
     */
    function displayTopVideoTones($id_top) {
        $ids_tops = $this->array_contenidos[$id_top]; // array con los id a mostrar

        if (!$this->array_titulos_tops[$id_top]) {
//            $extraparam = "SECCION_SIN_TITULO";
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]), $extraparam);
        } else {
        	$seccionTop = new Seccion ($this->array_titulos_tops[$id_top], $this->getStyle($this->array_align_items[$id_top]), $this->getStyle($this->array_font_size[$id_top]));
        }
    	$listaTop = new ListaLinks();
    	if ($this->array_style_items[$id_top] != "BULLETS") $listaTop->SetStyle($this->getStyle($this->array_style_items[$id_top]));
    	foreach ($ids_tops as $id) {
    		$datos = obtenerDatosContenido($this->db, $id);
    		$cat = $datos['categoria'];
    		$id = $datos['id'];
    		if ($this->operadora == "claro" || $this->operadora == "porta") {
        		$href = "hacer_descarga.php?xxx={$this->array_xxx[$id]}&amp;b=h&amp;step=2{$this->universal_push}&amp;cat=$cat&amp;tipoCat=65&amp;id=".$id."&amp;b=h";
    		} else {
        		$href = "videos.php?xxx={$this->array_xxx[$id]}&amp;b=h&amp;step=2{$this->universal_push}&amp;cat=$cat&amp;tipoCat=65&amp;id=".$id."&amp;b=h";
    		}
    		$ruta = $datos['screenshots'];
    		$carpeta = calcularCarpeta($id);
    		($this->array_textos_links_dl[$id_top] != "") ? $nombre = $this->array_textos_links_dl[$id_top] : $nombre = $this->escape4Wap($datos['nombre']);
    		($this->array_subtexto[$id_top] == "1") ? $autor = "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$this->escape4Wap($datos["autor"]) : $autor = "";
    		if ($this->array_style_items[$id_top] == "BULLETS") {
    		    $link = new MenuItem("images/bullet.gif", $nombre, $href);
    		} else {
                if ($this->array_show_preview[$id_top] == "1") {
		    if (file_exists("getimage.php")) $link = new Link($href, $nombre, "getimage.php?path=http://www.wazzup.com.uy/$ruta", TOP_SIDE, null, $autor); 
                    else $link = new Link($href, $nombre, "http://www.wazzup.com.uy/$ruta", TOP_SIDE, null, $autor); 
                } else {
                    $link = new Link($href, $nombre, null, null, null, $autor); 
                }
    		}
            switch($this->array_marcas[$id]){
				case 'new':
					$link->setNew(true);
					break;
				case 'hot':
					$link->setHot(true);
					break;
				case 'hit':
					$link->setHit(true);
					break;
			}    		
    		$listaTop->AddComponent($link);
    	}
    	$seccionTop->AddComponent ($listaTop);
    	$this->html .= $seccionTop->Display();
    	
    	if (($this->array_text_notitle[$id_top] != "")) {
        	$seccionVerMas = new Seccion ("", $this->getStyle($this->array_align_notitle[$id_top]), $this->getStyle($this->array_font_size[$id_top]), SECCION_SIN_TITULO);
        	$seccionVerMas->AddComponent (new Link ("videos.php?step=0{$this->universal_push}&amp;tipoCat=65", $this->array_text_notitle[$id_top]));
        	$this->html .= $seccionVerMas->Display();
    	}
        	
    	if ($this->debug == "2") $this->logMyDebug();
    	return $this->html;
    }










}

?>
