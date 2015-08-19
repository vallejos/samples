<?php
include_once($_SERVER['DOCUMENT_ROOT']."/../lib/CelularWurfl.php");
/******************
  **************
  ESTAS FUNCIONES NO HAY QUE BORRARLAS, LO DEM�S SIIIII
  ******************
  *******************/
function obtenerTopDeJuegos_mms($db,$ua,$idCat=0,$step,$operadora,$nombre){

	$tiposJuegos = array(31,35,57,59,61,63);
	$id_celular = obtenerIDCelular($ua, $db);

	mysql_select_db("Web");

	$sql = "select id from Web.mcm_portal where operadora = '$operadora' and nombre = '$nombre'";
	$rs = mysql_query($sql,$db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;
		exit;
	}
	$obj = mysql_fetch_object($rs);

	$id_wap = $obj->id;

	$sql = "select cantMuestro,cantCajas from Web.mcm_topJuegosAdm where idPortal = $id_wap and step = $step";
//echo $sql;
	$rs = mysql_query($sql,$db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;
		exit;
	}
	$num_rows = mysql_num_rows($rs);
	if($num_rows==0){
		$por_pagina = 1;
		$juegos["cantCajas"] = 1;
	}else{
		$obj = mysql_fetch_object($rs);
	   	$por_pagina = $obj->cantMuestro;
		$juegos["cantCajas"] = $obj->cantCajas;
	}

	$sql = "select c.nombre, gc.archivo,t.idCont as id,t.step,t.orden,t.attr, gi.screenshots, c.referencia from Web.mcm_topJuegos t
			inner join Web.contenidos c on c.id = t.idCont
			inner join gamesInfo gi on gi.game = t.idCont
			inner join gamecomp gc ON gc.juego = t.idCont
			inner join Web.contcol_whitelist cw on cw.contenido = t.idCont
			inner join Web.mcm_contenidos_cat cc on cc.id_cont = t.idCont
			where
			cw.claro_ar = 1
			and c.tipo in (".implode(",", $tiposJuegos).")
			and t.step = $step
			and t.idPortal = $id_wap
			and gc.celular = $id_celular
			and cc.id_portal = $id_wap";
	if($idCat<>0){
	    $sql .= " and idCat=$idCat";
	}

    $sql .= " group by t.idCont
			  order by orden limit $por_pagina";

// echo $sql;
	$rs = mysql_query($sql,$db);
    if(!$rs) {
   		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;
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
// print_r($juegos);
    return $juegos;
}


function obtenerUrlConfirmacion($db, $cobro, $ua, $idCont, &$err){
	if(($retorno = $cobro->comprarContenido($idCont, $ua)) === true) {		
		return URL_CONFIRMACION.$cobro->get_transaction_id();		
	} else {
		$err = $retorno;
		return false;
	}		
}

function obtenerCategoriasJuegos_mms ($db,$ua,$pagina_actual,$por_pagina,$operadora,$nombre){

	$cats = array();
	$init = $pagina_actual * $por_pagina;
	$id_celular = obtenerIDCelular($ua, $db);
	
	$tiposJuegos = array(31,35,57,59,61,63);
	
	if($id_celular != "") {
		$sql = "select id from Web.mcm_portal where operadora='$operadora' and nombre='$nombre'"; 
		$rs = mysql_query($sql,$db);
		if(!$rs) {
			echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
			exit;
		}
		$obj = mysql_fetch_object($rs);
		
		$id_wap = $obj->id;
		
		$sql = "select count(Cat.id_cat) as cant 
				from Web.mcm_contenidos_cat Cont 
				inner join Web.contenidos C on Cont.id_cont = C.id   
				inner join Web.mcm_categoria Cat on Cat.id_cat = Cont.id_cat  
		   		inner join Web.gamecomp GC on GC.juego = Cont.id_cont
				inner join Web.contcol_whitelist CW ON CW.contenido = Cont.id_cont
				where 
				CW.claro_ar = 1 
			    and Cat.id_portal = $id_wap
				and Cont.id_portal = $id_wap 
				and C.tipo in (".implode(",", $tiposJuegos).")
				and GC.celular = $id_celular
				GROUP BY Cat.id_cat
			    HAVING cant > 0";  
	    
		$res = mysql_query($sql, $db);
		if(!$res) {
			echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
			exit;
		}
		
		$cats['total'] = mysql_num_rows($res);
		
			 $sql = "select count(Cat.id_cat) as cant, Cat.id_cat as id, Cat.nombre as descripcion  
				from Web.mcm_contenidos_cat Cont 
				inner join Web.contenidos C on Cont.id_cont = C.id   
				inner join Web.mcm_categoria Cat on Cat.id_cat = Cont.id_cat  
		   		inner join Web.gamecomp GC on GC.juego = Cont.id_cont
				inner join Web.contcol_whitelist CW ON CW.contenido = Cont.id_cont
				where 
				CW.claro_ar = 1 
				and Cat.id_portal = $id_wap
				and Cont.id_portal = $id_wap  
				and C.tipo in (".implode(",", $tiposJuegos).")
				and GC.celular = $id_celular
				GROUP BY Cat.id_cat
				HAVING cant > 0
				ORDER BY Cat.orden desc
			    LIMIT $init, $por_pagina";
	    
		
		$rs = mysql_query($sql,$db);
	    if(!$rs) {
			echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
			exit;
		}
		while($row = mysql_fetch_assoc($rs)) {
			$cats[] = $row;
		}
	}
	return $cats;
}  
  
function obtenerJuegosPorCat_mms($db, $ua, $idCat, $pagina_actual, $por_pagina, $operadora, $nombre){
	
    $juegos = array();
    $init = $pagina_actual * $por_pagina;
	$id_celular = obtenerIDCelular($ua, $db);
	
	$tiposJuegos = array(31,35,57,59,61,63);
	
	$sql = "select id from Web.mcm_portal where operadora = '$operadora' and nombre = '$nombre'";
	$rs = mysql_query($sql,$db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	}
	$obj = mysql_fetch_object($rs);
    
	$id_wap = $obj->id;
	
	$sql = "select count(*) as cant  
	        from Web.mcm_contenidos_cat Cmcm
			inner join Web.contenidos C on C.id = Cmcm.id_cont  
			inner join gamesInfo GI ON GI.game = Cmcm.id_cont
			inner join gamecomp GC ON GC.juego = Cmcm.id_cont
			inner join Web.contcol_whitelist CW ON CW.contenido = Cmcm.id_cont
			where 
			CW.claro_ar = 1
			and id_portal = $id_wap 
			and C.tipo in (".implode(",", $tiposJuegos).")
			and id_cat = $idCat 
			and GC.celular = $id_celular";
	
	$rs = mysql_query($sql,$db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	}
	$obj = mysql_fetch_object($rs);
	
	$juegos['total'] = $obj->cant;
	
	$sql = "select C.nombre, Cmcm.id_cont as id, GC.archivo, C.referencia, GI.screenshots 
	        from Web.mcm_contenidos_cat Cmcm
			inner join Web.contenidos C on C.id = Cmcm.id_cont  
			inner join gamesInfo GI ON GI.game = Cmcm.id_cont
			inner join gamecomp GC ON GC.juego = Cmcm.id_cont
			inner join Web.contcol_whitelist CW ON CW.contenido = Cmcm.id_cont
			where 
			CW.claro_ar = 1
			and id_portal = $id_wap 
			and C.tipo in (".implode(",", $tiposJuegos).")
			and id_cat = $idCat 
			and GC.celular = $id_celular
			GROUP BY Cmcm.id_cont 
    		ORDER BY Cmcm.orden desc,C.id DESC 
    	    LIMIT $init, $por_pagina";
			   
			   
	$rs = mysql_query($sql,$db);
    if(!$rs) {
   		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
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
    return $juegos;
}


function obtenerIdsCat($db,$cats){
	for($x=0; $x<=count($cats)-1;$x++){
		
		echo $sql="insert into Web.mcm_categoria (id_portal, id_cat, nombre) 
        values (1, ".$cats[$x]['id'].", '".$cats[$x]['descripcion']."');";
		echo '<br/><br/>';
		
		$sql = "SELECT C.tipo, C.nombre, C.id as id_cont, GC.archivo, C.referencia, GI.screenshots
				FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
			INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
			INNER JOIN gamecomp GC ON C.id = GC.juego
			INNER JOIN gamesInfo GI ON GI.game = C.id
				WHERE 
			claro_ar = 1 AND (movistar = 0 AND tigo_co = 0)
			AND (
			CC.descripcion like (SELECT descripcion FROM contenidos_cat WHERE id = '".$cats[$x]['id']."')";
	
		$sql .= ")";
	
		$sql.= " GROUP BY C.id 
				ORDER BY C.orden DESC, C.nombre ASC";
		
		$rs=mysql_query($sql,$db);
		$cont=1;
		while($obj=mysql_fetch_object($rs)){
		
			echo $sql = "insert into Web.mcm_contenidos_cat (id_portal,id_cat,id_cont) values (1,".$cats[$x]['id'].",$obj->id_cont);";
			echo "<br/>";
			$cont+=1;
		}
		echo '<br/>'; 
	}  
} 

function obtenerContPorCat($idCat, $db, $pag = 0, $cant = 5, $type, $ids_prohibidos = array(), 
$cats_prohibidas=array(),$ids_only=array()){
	$imgs = array();
	$pag *= $cant;

	 	mysql_select_db(Web);   
		
		$sql = "SELECT COUNT(id) as c
		FROM contenidos  CO 
		INNER JOIN contcol_whitelist CW ON CW.contenido = CO.id
		WHERE 
		 CO.tipo = $type
		AND CW.claro_ar = 1";
	
	if(count($ids_only) > 0) {
		$sql .= " AND CO.id IN (".implode(",", $ids_only).")";
	} 
	
	if($idCat <> -1){
		 $sql .= " AND CO.categoria = $idCat";
	}
	
	
	if(count($cats_prohibidas) > 0) {
		 $sql .= " AND CO.categoria NOT IN (".implode(",", $cats_prohibidas).")";	
	}
	

	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	}
	$row = mysql_fetch_assoc($rs);
	$imgs['total'] = $row['c'];
	
	$sql = "SELECT autor,id, nombre, referencia, archivo, tipo, archivo 
		FROM contenidos  CO INNER JOIN contcol_whitelist CW ON CW.contenido = CO.id
	WHERE 
		 CO.tipo = $type
		AND CW.claro_ar = 1";

	if(count($ids_only) > 0) {
		$sql .= " AND CO.id IN (".implode(",", $ids_only).")";
	} 
	
	if($idCat <> -1){
		 $sql .= " AND CO.categoria = $idCat";
	}
	
	if(count($cats_prohibidas) > 0) {
		 $sql .= " AND CO.categoria NOT IN (".implode(",", $cats_prohibidas).")";	
	}

	$sql .= " ORDER BY nombre 
		 LIMIT $pag, $cant";
	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	}
	while($row = mysql_fetch_assoc($rs)) {
		$imgs[] = $row;	
	}

	return $imgs;

}


function buscarJuegosCompatiblesConCel($db, $ua, $idJuego){
    $sql = "select categoria from Web.contenidos where id=".$idJuego;
	
	$rs=mysql_query($sql,$db);
	if(!$rs) {
			echo $sql."::".mysql_error();
	}
	if($obj=mysql_fetch_object($rs)){
		$idCat=$obj->categoria;
	}else{
		$idCat=-1; //Por las dudas, cosa que no creo ya que tiene que tener categoria...
	}
	
	$sql="select count(*) as cant from Web.gamecomp g
	inner join MCM.celulares_modelos_wurfl c on g.celular=c.fk_celulares_web
	inner join MCM.celulares_ua_wurfl ua on ua.pk_fk_celulares_modelos_wurfl=pk_celulares_modelos_wurfl
	inner join Web.contenidos co on co.id = g.juego
	where ua.pk_descripcion='$ua' and co.tipo and co.categoria=$idCat
	and co.tipo in (31)"; 
	
	$rs=mysql_query($sql,$db);
	if(!$rs) {
			echo $sql."::".mysql_error();
	}
	$obj=mysql_fetch_object($rs);
	$total=$obj->cant;
	
	$sql="select g.juego as juego from Web.gamecomp g
	inner join MCM.celulares_modelos_wurfl c on g.celular=c.fk_celulares_web
	inner join MCM.celulares_ua_wurfl ua on ua.pk_fk_celulares_modelos_wurfl=pk_celulares_modelos_wurfl
	inner join Web.contenidos co on co.id = g.juego
	where ua.pk_descripcion='$ua' 
	and co.tipo in (31)";
		
	if($total>0){
		$sql.=' and co.categoria='.$idCat;
	}	 	
	
	 $sql.=' order by rand() limit 4';
		
	$rs=mysql_query($sql,$db);
	if(!$rs) {
		echo $sql."::".mysql_error();
	}
	$ids= array();
	while ($obj=mysql_fetch_object($rs)){
		  $ids[]=$obj->juego;
	}
	   
	return $ids;
};


function soportaContenidoPorTipo($db, $ua, $tipo){
    $celularWurfl = new CelularWurfl($db, $ua);

    return $celularWurfl->soportaContenidoPorTipo($tipo);
}


function soportaJuego($db, $ua, $id) {
	$id_cel = obtenerIDCelular($ua, $db);
	$sql = "SELECT COUNT(C.id) as cont
			FROM Web.contenidos C INNER JOIN Web.gamecomp GC ON C.id = GC.juego
			WHERE celular = $id_cel
			AND C.id = $id";
	
	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo mysql_error();
	} else {
		$row = mysql_fetch_assoc($rs);
		return $row['cont'] > 0;
	}
}

function soportaCabezalGrande($db, $ua){
	$cw = new CelularWurfl($db, $ua);
    	return ($cw->pantalla_ancho >= ANCHO_PANTALLA_GRANDE);
}

function soportaCabezalMediano($db, $ua){ 
	$cw = new CelularWurfl($db, $ua);
    	return ($cw->pantalla_ancho >=ANCHO_PANTALLA_MEDIANA && $cw->pantalla_ancho < ANCHO_PANTALLA_GRANDE);
}

function soportaCabezalChico($db, $ua){
    $cw = new CelularWurfl($db, $ua);
    return $cw->pantalla_ancho < ANCHO_PANTALLA_MEDIANA;
}


function obtenerDatosContenido($db, $id, $es_juego = null){

	if(!$db) {
		$conn = new coneXion("Web", true);
		$db = $conn->db;
	}
	
	$sql = "SELECT * 
		FROM contenidos
		WHERE id = $id";

	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo "Error en la consulta 5 ::$sql:: ".mysql_error($rs);	
	}
	$datos = mysql_fetch_assoc($rs);
	if($es_juego) {
 
		    $sql = "SELECT C.tipo, C.categoria, C.nombre, C.id, GC.archivo, C.referencia, GI.screenshots, GI.descr, GI.descr_wap
			    FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
			    INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
			    INNER JOIN gamecomp GC ON C.id = GC.juego
			    INNER JOIN gamesInfo GI ON GI.game = C.id
			    WHERE 
			    C.id = '$id'
			    GROUP BY C.id";

		    $rs = mysql_query($sql,$db);
		    if(!$rs) {
			echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
			exit;
		    }

		$row = mysql_fetch_assoc($rs);
		$datos = $row;
		$datos['descr_wap'] = ($datos['descr_wap'] == "")?substr($datos['descr'],0, 119)."...":$datos['descr_wap'];
		$datos['descr_wap'] = utf8_encode($datos['descr_wap']);
		$screens = explode(",", $row['screenshots']);
		$pathTo = $screens[0];
		$screen = $screens[1];
		$path = explode(".", $screen);
		$path[0][strlen($path[0])-1]=2;
		$path = $path[0]."_p.gif";
		$datos['screenshots'] = $pathTo.$path;

		$screen = $screens[2];
		$path = explode(".", $screen);
		$path = $path[0]."_p.gif";
		$datos['previews'] = array();
		$datos['previews'][] = PREVIEW_HOST."/".$pathTo.$path;

		$screen = $screens[3];
		$path = explode(".", $screen);
		$path = $path[0]."_p.gif";
		$datos['previews'][] = PREVIEW_HOST."/".$pathTo.$path;

	} else {
		if($datos['archivo'] == "") {
			$datos['archivo'] = $datos['referencia'];	
		}
		$datos['archivo'] = str_replace(".3gp", ".gif", $datos['archivo']);
		if(strpos($datos['archivo'], ".gif") !== false) {
			$datos['screenshots'] = str_replace("128x128", "50x50", $datos['archivo']);
			switch($datos['tipo']) {
				case 5:
					$datos['screenshots'] = str_replace("50x50", "40x32", $datos['screenshots']);	
				break;
				case 63:
					$datos['screenshots'] = str_replace(".gif", "_wap.gif", $datos['screenshots']);	
				break;
				case 65:
				case 62:
					$datos['screenshots'] = str_replace(".gif", "_p.gif", $datos['referencia']);	
				break;
			}
		} else {
			$datos['screenshots'] = null;	
		}

	}
	return $datos;
    

}




/**
 * Devuelve una lista de los juegos asociados a una categoria especifica
 *
 * @param string 	$nombreCat El nombre de la categoriua
 * @param resource 	$db    El link a la base de datos
 * @param int 		$pag   El n�mero de p�gina en la que estamos (para hacer el paginado, por defecto vale 0).
 * @param int 		$cant  La cantidad de resultados a traer por p�gina (para el paginado, por defecto vale 5);
 * @return array 	Un array con los datos de los diferentes juegos.
 */
function obtenerJuegos($db, $ua, $ids_juegos, $pagina, $por_pagina) {
	
    $juegos = array();
    $idCelu = obtenerIDCelular($ua, $db);
    $init = $pagina * $por_pagina;
   
    if($idCelu) {

	$sql = "SELECT count(distinct C.id) as cont
		    FROM contenidos C 
		    INNER JOIN gamecomp GC ON C.id = GC.juego
		    INNER JOIN gamesInfo GI ON GI.game = C.id
		    WHERE 
		    C.id IN (".implode(",", $ids_juegos).")
		    AND GC.celular = $idCelu
	    	    ";

	    $rs = mysql_query($sql,$db);
	    if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	    }
	    $row = mysql_fetch_assoc($rs);
	    $juegos['total'] = $row['cont'];

	    $sql = "SELECT C.id, C.nombre, GC.archivo, C.referencia, GI.screenshots
		    FROM contenidos C 
		    INNER JOIN gamecomp GC ON C.id = GC.juego
		    INNER JOIN gamesInfo GI ON GI.game = C.id
		    WHERE 
		    C.id IN (".implode(",", $ids_juegos).")
		    AND GC.celular = $idCelu
		    GROUP by C.id
	    	    ORDER BY C.nombre ASC
		    LIMIT $init, $por_pagina";

	    $rs = mysql_query($sql,$db);
	    if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
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
    }
    return $juegos;
}

function obtenerDescJuego($db, $id, $largo_maximo = 200){

	$sql = "SELECT nombre, descr, screenshots
		FROM gamesInfo GI INNER JOIN contenidos C ON GI.game = C.id
		WHERE game = $id";

	$rs = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($rs);

	$texto = $row['descr'];
	
	if(strlen($texto) > $largo_maximo) {
		$texto = substr($texto, 0, $largo_maximo)."...";
	}

	$screens = explode(",", $row['screenshots']);
	$pathTo = $screens[0];
	$screens = $screens[1];
	$path = explode(".", $screens);
	
	$path[0][strlen($path[0])-1]=2;

	$path = $path[0]."_p".".gif";
	$screen = $pathTo.$path;

	return array("nombre" => $row['nombre'], "texto" => ($texto), "screen" => $screen);
}

/**
 * Para detectar si un celular soporta determinado tipo de contenido, usando las tablas del WURLF del MCM
 *
 * @param obj 	$db  La conexi�n a la base de datos
 * @param string  $ua  El user-agent del celular
 * @param string  $cont  El nombre del contenido, tiene que coincidir con el nombre del campo
 * @return boolean TRUE si el contenido es soportado, FALSE de lo contrario
 */
function soportaContenido($db, $ua, $cont){

	$sql = "SELECT pk_celulares_modelos_wurfl, mp3, wallpaper, screensaver, video
		FROM MCM.celulares_ua_wurfl CUW INNER JOIN MCM.celulares_modelos_wurfl CMW ON CUW.pk_fk_celulares_modelos_wurfl = CMW.pk_celulares_modelos_wurfl
		WHERE CUW.pk_descripcion = '$ua'";

	$rs = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($rs);
	return $row[$cont] == 1;
}

function obtenerIDCelular($user_agent, $db){
	return getCelId($user_agent, $db);
}

function obtenerCatTipoCont($db, $tipo, $p_actual, $cant_pagina, $xxx = 0, $only_cats = array()) {
	$init = $p_actual * $cant_pagina;
	$lista = array();

	$sql = "select count(C.id) as cont 
		 FROM contenidos_cat CC INNER JOIN contenidos C ON CC.id = C.categoria 
		 INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
		 WHERE C.tipo = $tipo
		 AND CC.xxx = $xxx
		 AND CW.claro_ar = 1";

	if(count($only_cats) > 0) {
		$sql .= " AND CC.id IN (".implode(",", $only_cats).")";	
	}

	$sql .= " GROUP BY CC.id
		 HAVING cont > 0";
	$rs = mysql_query($sql,$db);
	if(!$rs) {
		echo "Error en la consulta 1 ::$sql:: ".mysql_error($db);	
	}

	$row = mysql_fetch_assoc($rs);
	

	$lista['total'] = mysql_num_rows($rs); 


	$sql =  "SELECT CC.*, count(C.id) as cant 
		 FROM contenidos_cat CC INNER JOIN contenidos C ON CC.id = C.categoria 
		 INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
		 WHERE C.tipo = $tipo
		 AND CW.claro_ar = 1
		 AND CC.xxx = $xxx";

	if(count($only_cats) > 0) {
		$sql .= " AND CC.id IN (".implode(",", $only_cats).")";	
	}

	$sql .= " GROUP BY CC.id
		 HAVING cant > 0
		 LIMIT $init, $cant_pagina";

	$rs = mysql_query($sql,$db);
	if(!$rs) {
		echo "Error en la consulta 2 ::$sql:: ".mysql_error($db);	
	}
	while($row = mysql_fetch_assoc($rs)) {
		$lista[] = array("nombre" => $row['descripcion'], "id" => $row['id']);	
	}
	return $lista;

}


/**
 * Obtiene los nombres de las categorias para los juegos
 * y las devuelve en un array.
 *
 * @param resource  $db 	El link a la conexi�n con la base
 * @param array     $ids 	Un array con los ids de las categorias a devolver
 * @param bool 	    $adultos 	Indica si buscar o no categor�as para adultos, por defecto vale FALSE
 * @param int 	    $pagina_actual El numero de la p�gina actual, para el paginado
 * @param int 	    $por_pagina   La cantidad de elementos a mostrar por p�gina
 * @param array     $cats_only   ***** Para hacer chanchadas, con esto mostramos solo las categor�as que se pasan en este array ****
 * @return array  Un array con los nombres de las categorias y los IDS
 */
function getCatJuegosJava($db, $ids, $adultos = false, $pagina_actual, $por_pagina, $ua,$cats_only = null){
	$xxx = ($adultos)?1:0;

	$cats = array();
	$init = $pagina_actual * $por_pagina;
	$id_celular = obtenerIDCelular($ua, $db);
	
	if($id_celular != "") {
		$sql = "SELECT count(C.id) as cant
			FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
			INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
			INNER JOIN gamecomp GC ON GC.juego = C.id
			WHERE C.tipo IN (".implode(",", $ids).")
			AND CC.free=0 
			AND xxx=$xxx
		AND claro_ar = 1 AND (movistar = 0 AND tigo_co = 0)
			AND GC.celular = '$id_celular'";
	
		if($cats_only) {
			$sql .= " AND CC.id IN (".implode(",", $cats_only).") ";	
		}
	
		 $sql .= " GROUP BY CC.descripcion
			  HAVING cant > 0
			  ORDER BY CC.descripcion
			  ";
	
		$res = mysql_query($sql, $db);
		if(!$res) {
			echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
			exit;
		}
		$cats['total'] = mysql_num_rows($res);
	
		$sql = "SELECT CC.descripcion,CC.id, count(C.id) as cant
			FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
			INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
			INNER JOIN gamecomp GC ON GC.juego = C.id
			WHERE C.tipo IN (".implode(",", $ids).")
			AND CC.free=0 
			AND xxx=$xxx
			AND claro_ar = 1 AND (movistar = 0 AND tigo_co = 0)
			AND GC.celular = '$id_celular'";
	
		if($cats_only) {
			$sql .= " AND CC.id IN (".implode(",", $cats_only).") ";	
		}
	
		$sql .= " GROUP BY CC.descripcion
			  HAVING cant > 0
			  ORDER BY CC.descripcion
			  LIMIT $init, $por_pagina
			  ";
	
		$res = mysql_query($sql, $db);
		if(!$res) {
			echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
			exit;
		}
	
		while($row = mysql_fetch_assoc($res)) {
			$cats[] = $row;
		}
	}
	return $cats;

}

/**
 * Devuelve una lista de los juegos asociados a una categoria especifica
 *
 * @param string 	$nombreCat El nombre de la categoriua
 * @param resource 	$db    El link a la base de datos
 * @param int 		$pag   El n�mero de p�gina en la que estamos (para hacer el paginado, por defecto vale 0).
 * @param int 		$cant  La cantidad de resultados a traer por p�gina (para el paginado, por defecto vale 5);
 * @param array 	$ids_prohibidos Ids que no pueden mostrarse
 * @return array 	Un array con los datos de los diferentes juegos.
 */
function obtenerJuegosPorCat($idCat, $idCelu, $db, $pag = 0, $cant = 5, $ids_prohibidos = array()) {
	
    $juegos = array();
    $pag *= $cant;
    $sql = "SELECT count(DISTINCT C.id) as cont
    	    FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
	    INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
	    INNER JOIN gamecomp GC ON C.id = GC.juego
	    INNER JOIN gamesInfo GI ON GI.game = C.id
    	    WHERE 
	    GC.celular = $idCelu
	    AND claro_ar = 1 AND (movistar = 0 AND tigo_co = 0)
	    AND (
	      CC.descripcion like (SELECT descripcion FROM contenidos_cat WHERE id = '$idCat')";
    if(count($ids_prohibidos) > 0 ) {
   	$sql .= " AND C.id NOT IN (".implode(",", $ids_prohibidos).")"; 
    }
    
    $sql .= ")
	    ";

    $rs = mysql_query($sql, $db);
    if(!$rs) {
   	echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
	exit;
    }
    $row = mysql_fetch_assoc($rs);
    $juegos['total'] = $row['cont'];

    
    $sql = "SELECT C.tipo, C.nombre, C.id, GC.archivo, C.referencia, GI.screenshots
    	    FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
	    INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
	    INNER JOIN gamecomp GC ON C.id = GC.juego
	    INNER JOIN gamesInfo GI ON GI.game = C.id
    	    WHERE 
	     GC.celular = $idCelu
	   AND claro_ar = 1 AND (movistar = 0 AND tigo_co = 0)
	    AND (
		CC.descripcion like (SELECT descripcion FROM contenidos_cat WHERE id = '$idCat')";

    if(count($ids_prohibidos) > 0 ) {
   	$sql .= " AND C.id NOT IN (".implode(",", $ids_prohibidos).")"; 
    }
    
    $sql .= ")";

    $sql.= " GROUP BY C.id 
    		ORDER BY C.orden DESC, C.nombre ASC
    	     LIMIT " . $pag. "," . $cant;

    $rs = mysql_query($sql,$db);
    if(!$rs) {
   	echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
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
    return $juegos;
}

function obtenerContenidos($ua, $db, $pag = 0, $cant = 10, $xxx = 1, $tipo_contenido = 63, $idCat = -1, $only_ids = null){
	$themes = array();

	$sql = "SELECT COUNT(C.id) as c
		FROM Web.contenidos C INNER JOIN MCM.tipos_contenidos TC ON C.tipo = TC.pk_tipos_contenidos
		INNER JOIN MCM.formatos_contenidos FC ON TC.pk_tipos_contenidos = FC.fk_tipos_contenidos
		INNER JOIN MCM.rel_formatos_contenidos_celulares_modelos_wurfl RMW ON FC.pk_formatos_contenidos = RMW.pk_fk_formatos_contenidos
		INNER JOIN MCM.celulares_modelos_wurfl CW ON RMW.pk_fk_celulares_modelos_wurfl = CW.pk_celulares_modelos_wurfl
		INNER JOIN MCM.celulares_ua_wurfl UA ON UA.pk_fk_celulares_modelos_wurfl = RMW.pk_fk_celulares_modelos_wurfl
		INNER JOIN Web.contenidos WC ON WC.id = C.id
		INNER JOIN Web.contenidos_cat WCC ON WC.categoria = WCC.id
		INNER JOIN Web.contcol_whitelist CW ON C.id = CW.contenido
		WHERE UA.pk_descripcion ='$ua'
		AND TC.pk_tipos_contenidos = $tipo_contenido
		AND WCC.xxx = $xxx ";

	if($idCat != -1) {
		$sql .= " AND C.categoria = $idCat";	
	}

	if($only_ids) {
		$sql .= " and C.id IN (".implode(",", $only_ids).") ";
	}

	$res = mysql_query($sql, $db);
	if(!$res) {
		echo "Error en la consulta 3 ::$sql:: ".mysql_error($db);
		exit;
	}
	$row = mysql_fetch_assoc($res);
	$themes['total'] = $row['c'];

	$pag *= $cant;
	$sql = "SELECT C.nombre, C.id as pk_contenidos, C.referencia
		FROM Web.contenidos C INNER JOIN MCM.tipos_contenidos TC ON C.tipo = TC.pk_tipos_contenidos
		INNER JOIN MCM.formatos_contenidos FC ON TC.pk_tipos_contenidos = FC.fk_tipos_contenidos
		INNER JOIN MCM.rel_formatos_contenidos_celulares_modelos_wurfl RMW ON FC.pk_formatos_contenidos = RMW.pk_fk_formatos_contenidos
		INNER JOIN MCM.celulares_modelos_wurfl CW ON RMW.pk_fk_celulares_modelos_wurfl = CW.pk_celulares_modelos_wurfl
		INNER JOIN MCM.celulares_ua_wurfl UA ON UA.pk_fk_celulares_modelos_wurfl = RMW.pk_fk_celulares_modelos_wurfl
		INNER JOIN Web.contenidos WC ON WC.id = C.id
		INNER JOIN Web.contenidos_cat WCC ON WC.categoria = WCC.id
		INNER JOIN Web.contcol_whitelist CW ON C.id = CW.contenido
		WHERE UA.pk_descripcion ='$ua'
		AND TC.pk_tipos_contenidos = $tipo_contenido
		AND WCC.xxx = $xxx ";
	if($idCat != -1) {
		$sql .= " AND C.categoria = $idCat";	
	}

	if($only_ids) {
		$sql .= " and C.id IN (".implode(",", $only_ids).") ";
	}

	$sql .= " ORDER BY C.id
		LIMIT $pag, $cant";
	$res = mysql_query($sql, $db);
	if(!$res) {
		echo "Error en la consulta 4 ::$sql:: ".mysql_error($db);
		exit;
	}

	while($row = mysql_fetch_assoc($res)) {
		$themes[] = $row;	
	}

	return $themes;
}

/**
 * Hace lo mismo que la funci�n "implode" pero utiliza los indices 
 * del array.
 *
 * @param $glue  	El string con el que se pegar�n los distintos elementos del array
 * @param $inglue 	El string con el que se pegar�n el indice con su correspondiente valor
 * @param $array 	El array sobre el cual se trabaja
 */
function implode_with_keys($glue = " ", $inglue = "=", $array){
	$str = "";
	foreach($array as $key => $value){
		$str .= $key.$inglue.$value.$glue;
	}
	return substr($str, 0, strlen($str) - strlen($glue));
}




?>
