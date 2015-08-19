<?php
class topJuegos extends WapComponent{
	var $juegos;
	var $cantCajas;
	var $archivo;
	var $preview;
	var $conexion;
	var $useragent;

	function topJuegos($db,$ua,$a){
		$this->conexion = $db;
		$this->useragent = $ua;
		$this->archivo = $a;
		if(file_exists("getimage.php")){
			$this->preview = "getimage.php?path=http://www.wazzup.com.uy/netuy/java/cajas/";
		}else{
			$this->preview = "http://www.wazzup.com.uy/netuy/java/cajas/";
		}
	}


	function start($step,$operadora,$nombre, $idCat=0,$debug=false){

		$tiposJuegos = array(31,35,57,59,61,63);
		mysql_select_db("MCM");
		$sql = "";
		$sql .= " SELECT CM.fk_celulares_web ";
		$sql .= " FROM MCM.celulares_ua_wurfl CU INNER JOIN MCM.celulares_modelos_wurfl CM";
		$sql .= " ON CU.pk_fk_celulares_modelos_wurfl=CM.pk_celulares_modelos_wurfl INNER JOIN MCM.celulares_marcas_wurfl CMA";
		$sql .= " ON CM.fk_celulares_marcas_wurfl=CMA.pk_celulares_marcas_wurfl";
		$sql .= " WHERE pk_descripcion ='" . $this->useragent . "'";
		$rs = mysql_query($sql, $this->conexion);

		if(!$rs) {
		   echo "Error en el query: ".$sql."::".mysql_error($this->conexion)."::".__FILE__."::".__LINE__;	
		   exit;
		}
		if (mysql_num_rows($rs) >0){
		    $celular = mysql_fetch_object($rs);
		    $id_celular = $celular->fk_celulares_web;
		}

		mysql_select_db("Web");
		$sql = "select id from Web.mcm_portal where operadora = '$operadora' and nombre = '$nombre'";

		$rs = mysql_query($sql,$this->conexion);
		if(!$rs) {
			echo "Error en el query: ".$sql."::".mysql_error($this->conexion)."::".__FILE__."::".__LINE__;
			exit;
		}
		$obj = mysql_fetch_object($rs);

		$id_wap = $obj->id;

		$sql = "select cantMuestro,cantCajas from Web.mcm_topJuegosAdm where idPortal = $id_wap and step = $step";

		$rs = mysql_query($sql,$this->conexion);
		if(!$rs) {
			echo "Error en el query: ".$sql."::".mysql_error($this->conexion)."::".__FILE__."::".__LINE__;
			exit;
		}
		$num_rows = mysql_num_rows($rs);
		if($num_rows==0){
			$por_pagina = 1;
			$this->cantCajas = 1;
		}else{
			$obj = mysql_fetch_object($rs);
		   	$por_pagina = $obj->cantMuestro;
			$this->cantCajas = $obj->cantCajas;
		}

		$sql = "select c.nombre, gc.archivo,t.idCont as id,t.step,t.orden,t.attr, gi.screenshots, c.referencia from Web.mcm_topJuegos t
				inner join Web.contenidos c on c.id = t.idCont
				inner join Web.gamesInfo gi on gi.game = t.idCont
				inner join Web.gamecomp gc ON gc.juego = t.idCont
				inner join Web.contcol_whitelist cw on cw.contenido = t.idCont
				inner join Web.mcm_contenidos_cat cc on cc.id_cont = t.idCont
				where
				c.tipo in (".implode(",", $tiposJuegos).")
				and t.step = $step
				and c.activo = 1
				and t.idPortal = $id_wap
				and gc.celular = $id_celular
				and cc.id_portal = $id_wap";
		if($idCat<>0){
		    $sql .= " and idCat=$idCat";
		}

	   	$sql .= " group by t.idCont
				  order by orden limit $por_pagina";
	
//		
		if($debug===true){
			echo $sql; die();
		}
		$rs = mysql_query($sql,$this->conexion);

		if(!$rs) {
			echo "Error en el query: ".$sql."::".mysql_error($this->conexion)."::".__FILE__."::".__LINE__;
			exit;
		}

		while($row = mysql_fetch_assoc($rs)) {
			$screens = explode(",", $row['screenshots']);
			$pathTo = $screens[0];
			$screens = $screens[1];
			$path = explode(".", $screens);
			$path = $path[0]."_p".".gif";
			$row['screenshots'] = $pathTo.$path;

	   		$juegos[] = $row;
		}

		$this->juegos = $juegos;
	}


	function showBoxes($nombre_seccion="Top Juegos",$seccion_sin=false){
		$i=1;
		foreach($this->juegos as $item){
			if($i <= $this->cantCajas){
				if(ANCEL_DRUTT==1){
					include_once("rewrite_lib.php");
					include_once(dirname(__FILE__)."/../wap.wazzup.com.uy/wap_common/getCelularHeader.php");
					$href = urlDruttAncel($msisdn, $item['id'], false, servicio3G(), NOMBRE_PORTAL_DESCARGA);
				}else{
					$href = $this->archivo ."?id=".$item['id'];
				}
				$link = new Link($href,$item["nombre"], $this->preview.$item['id'].".gif", TOP_SIDE);
				switch($item['attr']){
					case "hot":
						$link->setHot(true);
					break;
					case "hit":
						$link->setHit(true);
					break;
					case "new":
						$link->setNew(true);
					break;
				}
				$lista_j[] = $link;
			}			
			$i++;
		}
		if($lista_j){
			if(!$seccion_sin){
				$seccion = new Seccion($nombre_seccion,"center",SMALL_FONT_SIZE);
			}else{
				$seccion = new Seccion($nombre_seccion,"center",SMALL_FONT_SIZE,SECCION_SIN_TITULO);
			}
			$listaLinks = new ListaLinks();
			$listaLinks->AddComponent($lista_j);
			$seccion->AddComponent($listaLinks);
			return $seccion;
       		}else{
			return "";
		}
	}

	function showLinks($nombre_seccion="", $align="left",$style=LISTA_INTERCALADA_LINKS){
		$cantidad = count($this->juegos);
		for($i=$this->cantCajas; $i < $cantidad; $i++){
			if(ANCEL_DRUTT==1){
				include_once("rewrite_lib.php");
				include_once(dirname(__FILE__)."/../wap.wazzup.com.uy/wap_common/getCelularHeader.php");
				$href = urlDruttAncel($msisdn, $this->juegos[$i]['id'], false, servicio3G(), NOMBRE_PORTAL_DESCARGA);
			}else{
				$href = $this->archivo ."?id=".$this->juegos[$i]['id'];
			}
			$menuItem = new Link($href,$this->juegos[$i]["nombre"]);
			switch($this->juegos[$i]['attr']){
				case "hot":
					$menuItem->setHot(true);
				break;
				case "hit":
					$menuItem->setHit(true);
				break;
				case "new":
					$menuItem->setNew(true);
				break;
			}
			$lista_js[] = $menuItem;
		}
		if($lista_js){
			if($nombre_seccion){
				$seccion = new Seccion($nombre_seccion,$align,SMALL_FONT_SIZE);
			}else{
				$seccion = new Seccion($nombre_seccion,$align,SMALL_FONT_SIZE,SECCION_SIN_TITULO);
			}
			$listaLinks = new ListaLinks();
			$listaLinks->SetStyle($style);
			$imagen = new Imagen("./images/bullet.gif","*");
			$listaLinks->setBullet($imagen);	
			$listaLinks->AddComponent($lista_js);		
			$seccion->AddComponent($listaLinks);
			return $seccion;
		}else{
			return "";
		}

	}
}
?>
