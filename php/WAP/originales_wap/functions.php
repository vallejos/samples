<?php
/******************
  **************
  ESTAS FUNCIONES NO HAY QUE BORRARLAS, LO DEM�S SIIIII
  ******************
  *******************/
  
function obtenerDescJuego($db, $id, $largo_maximo = 200){

	$sql = "SELECT nombre, descr, descr_wap, screenshots
		FROM gamesInfo GI INNER JOIN contenidos C ON GI.game = C.id
		WHERE game = $id";

	$rs = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($rs);

	if($row['descr_wap'] != "") {
		$row['descr'] = $row['descr_wap'];
	}

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
			inner join Web.gamesInfo GI ON GI.game = Cmcm.id_cont
			inner join Web.gamecomp GC ON GC.juego = Cmcm.id_cont
			inner join Web.contcol_whitelist CW ON CW.contenido = Cmcm.id_cont
			where 
			CW.ancel = 1
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
			inner join Web.gamesInfo GI ON GI.game = Cmcm.id_cont
			inner join Web.gamecomp GC ON GC.juego = Cmcm.id_cont
			inner join Web.contcol_whitelist CW ON CW.contenido = Cmcm.id_cont
			where 
			CW.ancel = 1
			and id_portal = $id_wap 
			and C.tipo in (".implode(",", $tiposJuegos).")
			and id_cat = $idCat 
			and GC.celular = $id_celular
			GROUP BY Cmcm.id_cont 
    		ORDER BY Cmcm.orden DESC, C.nombre ASC
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
			    CW.ancel = 1
				and Cat.id_portal = $id_wap 
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
				CW.ancel = 1
				and Cat.id_portal = $id_wap 
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

function buscarContenidos_mcm($db,$pagina_actual,$por_pagina,$busqueda,$type,$only_cont,$operadora,$nombre){
	$conts = array();
    $init = $pagina_actual * $por_pagina;
	
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
			inner join Web.contcol_whitelist CW ON CW.contenido = Cmcm.id_cont  
			where 
			CW.ancel = 1";
			
	if(count($only_cont)>0){
		$sql .=  " and Cmcm.id_cont IN (".implode(",", $only_cont).")";
	}else{
		$sql .=	 " and C.nombre like '%$busqueda%'";
	}		
	
	 $sql .=	" and C.tipo=$type
			and Cmcm.id_portal = $id_wap ";
			
	$rs = mysql_query($sql,$db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	}
	$obj = mysql_fetch_object($rs);
	
	$conts['total'] = $obj->cant;
	
 	$sql = "select C.autor,Cmcm.id_cat as categoria, Cmcm.id_cont as id, C.nombre, C.referencia, C.archivo, C.tipo
	        from Web.mcm_contenidos_cat Cmcm
			inner join Web.contenidos C on C.id = Cmcm.id_cont  
			inner join Web.contcol_whitelist CW ON CW.contenido = Cmcm.id_cont  
			where 
			CW.ancel = 1";
			
	if(count($only_cont)>0){
		$sql .=  " and Cmcm.id_cont IN (".implode(",", $only_cont).")";
	}else{
		$sql .=	 " and C.nombre like '%$busqueda%'";
	}		
	
	 $sql .=	" and C.tipo=$type
			and Cmcm.id_portal = $id_wap  
			GROUP BY Cmcm.id_cont 
    		ORDER BY Cmcm.orden DESC, C.nombre ASC
    	    LIMIT $init, $por_pagina";
			   
	$rs = mysql_query($sql,$db);
    if(!$rs) {
   		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
    }
    
	while($row = mysql_fetch_assoc($rs)) {
		$conts[] = $row; 
    }
    return $conts;

}

function buscarContenidosPorArtistas_mcm($db,$pagina_actual,$por_pagina,$type,$busqueda,$operadora,$nombre){
	$conts = array();
    $init = $pagina_actual * $por_pagina;
	
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
			inner join Web.contcol_whitelist CW ON CW.contenido = Cmcm.id_cont  
			where 
			CW.ancel = 1";
			
	$sql .=	 " and C.autor like '%$busqueda%'";
			
	$sql .=	" and C.tipo=$type
			and Cmcm.id_portal = $id_wap ";
			
	$rs = mysql_query($sql,$db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	}
	$obj = mysql_fetch_object($rs);
	
	$conts['total'] = $obj->cant;
	
 	$sql = "select C.autor,Cmcm.id_cat as categoria, Cmcm.id_cont as id, C.nombre, C.referencia, C.archivo, C.tipo
	        from Web.mcm_contenidos_cat Cmcm
			inner join Web.contenidos C on C.id = Cmcm.id_cont  
			inner join Web.contcol_whitelist CW ON CW.contenido = Cmcm.id_cont  
			where 
			CW.ancel = 1";
			
	 $sql .=	 " and C.autor like '%$busqueda%'";
	 
	 $sql .=	" and C.tipo=$type
			and Cmcm.id_portal = $id_wap  
			GROUP BY Cmcm.id_cont 
    		ORDER BY Cmcm.orden DESC, C.nombre ASC
    	    LIMIT $init, $por_pagina";
			   
	$rs = mysql_query($sql,$db);
    if(!$rs) {
   		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
    }
    
	while($row = mysql_fetch_assoc($rs)) {
		$conts[] = $row; 
    }
    return $conts;
}
  
  function obtenerIdCat_mms($db,$idCont,$operadora,$nombre){
	$datos_cat = array();
	
	$sql = "select id from Web.mcm_portal where operadora = '$operadora' and nombre = '$nombre'";
	$rs = mysql_query($sql,$db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	}
	$obj = mysql_fetch_object($rs);
	$id_portal = $obj->id;
	
	$sql="select id_cat from Web.mcm_contenidos_cat where id_portal = ".$id_portal." and id_cont=".$idCont;
	
	$rs = mysql_query($sql,$db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	}
		
	$obj = mysql_fetch_object($rs);
	$cat = $obj->id_cat;
		
	$sql = "select nombre from Web.mcm_categoria where id_portal = ".$id_portal." and id_cat=".$cat;
	$rs = mysql_query($sql,$db);
	if(!$rs) {
		$cat=0;
	}
	$obj = mysql_fetch_object($rs);
	$autor = $obj->nombre;
	
	$datos_cat["id"] = $cat; 
	$datos_cat["autor"]= $autor;
	
	return $datos_cat;
}  
  
  function obtenerContenidosPorCat_mms($db, $idCat, $type, $pagina_actual, $por_pagina, $operadora, $nombre){
	
    $conts = array();
    $init = $pagina_actual * $por_pagina;
	
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
			inner join Web.contcol_whitelist CW ON CW.contenido = Cmcm.id_cont  
			where 
			CW.ancel = 1
			and C.tipo=$type
			and Cmcm.id_portal = $id_wap 
			and Cmcm.id_cat = $idCat";
	
	$rs = mysql_query($sql,$db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	}
	$obj = mysql_fetch_object($rs);
	
	$conts['total'] = $obj->cant;
	
 $sql = "select Cmcm.id_cat as categoria, Cmcm.id_cont as id, C.nombre, C.referencia, C.archivo, C.tipo
	        from Web.mcm_contenidos_cat Cmcm
			inner join Web.contenidos C on C.id = Cmcm.id_cont  
			inner join Web.contcol_whitelist CW ON CW.contenido = Cmcm.id_cont  
			where 
			CW.ancel = 1
			and C.tipo=$type
			and Cmcm.id_portal = $id_wap 
			and Cmcm.id_cat = $idCat 
			GROUP BY Cmcm.id_cont 
    		ORDER BY Cmcm.orden DESC, C.nombre ASC
    	    LIMIT $init, $por_pagina";
	
	   
	$rs = mysql_query($sql,$db);
    if(!$rs) {
   		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
    }
    while($row = mysql_fetch_assoc($rs)) {
		$conts[] = $row; 
    }
    return $conts;
}
  
function obtenerCategorias_mms ($db,$pagina_actual,$por_pagina,$type,$operadora,$nombre){

	$cats = array();
	$init = $pagina_actual * $por_pagina;
	
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
		   	inner join Web.contcol_whitelist CW ON CW.contenido = Cont.id_cont  
			where 
			CW.ancel = 1
			and Cat.id_portal = $id_wap 
			and C.tipo = $type
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
	  	inner join Web.contcol_whitelist CW ON CW.contenido = Cont.id_cont  
		where 
		CW.ancel = 1
		and Cont.id_portal = $id_wap 
		and Cat.id_portal = $id_wap  
		and C.tipo = $type
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
	
	return $cats;
} 


  function obtenerThemes( $db, $ua, $idCats, $pag = 0, $cant = 10, $xxx = 1, $only_ids = null){
	$idCelu = obtenerIDCelular($ua, $db);

	$themes = array();
	$pag *= $cant;
		
	/* */
	$sql = "SELECT count(DISTINCT C.id) as cont
    	    FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
	    INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
	    INNER JOIN gamecomp GC ON C.id = GC.juego
    	    WHERE
	    C.tipo = 63
	    AND CW.tigo_co = 1
	    AND GC.celular = $idCelu
		AND CC.xxx = $xxx
		AND C.categoria IN (".implode(",", $idCats).")";
	    // */
       if($only_ids) {
		$sql .= " and C.id IN (".implode(",", $only_ids).") ";
	}

	    $rs = mysql_query($sql, $db);
	    if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;
		exit;
	    }
	    $row = mysql_fetch_assoc($rs);
	    $themes['total'] = $row['cont'];

    

    /* */
    $sql = "SELECT C.nombre, C.id as pk_contenidos, GC.archivo
    	    FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
	    INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
	    INNER JOIN gamecomp GC ON C.id = GC.juego
    	    WHERE
	    C.tipo = 63
	    AND CW.tigo_co = 1
	    AND GC.celular = $idCelu
		AND CC.xxx = $xxx
		AND C.categoria IN (".implode(",", $idCats).")";
    $sql.= " GROUP BY C.id 
	     ORDER BY C.nombre ASC
    	     LIMIT " . $pag. "," . $cant;
	     //*/

    $rs = mysql_query($sql,$db);
    if(!$rs) {
   	echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;
	exit;
    }
    while($row = mysql_fetch_assoc($rs)) {
   	$themes[] = $row;
    }
    return $themes;
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
function getCatJuegosJava($ua, $db, $ids, $adultos = false, $pagina_actual, $por_pagina, $cats_only = null){
	$xxx = ($adultos)?1:0;

	$cats = array();
	$init = $pagina_actual * $por_pagina;

	$idCel = obtenerIDCelular($ua, $db);

	if($idCel) {
		$sql = "SELECT count(C.id) as cant
			FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
			INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
			INNER JOIN gamecomp GC ON GC.juego = C.id
			WHERE C.tipo IN (".implode(",", $ids).")
			AND CC.free=0 
			AND xxx=$xxx
			AND GC.celular = $idCel
			AND CW.tigo_co =1";

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
			AND GC.celular = $idCel
			AND xxx=$xxx
			AND CW.tigo_co =1";

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
	    AND CW.tigo_co = 1
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
	    AND CW.tigo_co = 1
	    AND (
		CC.descripcion like (SELECT descripcion FROM contenidos_cat WHERE id = '$idCat')";

    if(count($ids_prohibidos) > 0 ) {
   	$sql .= " AND C.id NOT IN (".implode(",", $ids_prohibidos).")"; 
    }
    
    $sql .= ")";

    $sql.= " GROUP BY C.id 
    		ORDER BY C.nombre ASC
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
  
  
  
function soportaCabezalGrande($db, $ua){
	$cw = new CelularWurfl($db, $ua);
    	return ($cw->pantalla_ancho >=174);
}

function soportaCabezalMediano($db, $ua){ 
	$cw = new CelularWurfl($db, $ua);
    	return ($cw->pantalla_ancho >=114 && $cw->pantalla_ancho < 174);
}

function soportaCabezalChico($db, $ua){
    $cw = new CelularWurfl($db, $ua);
    return $cw->pantalla_ancho < 114;
}


function obtenerArtistasancel($db, $ua, $tipo = 23){

	$in_tipos = array(29);
	if($tipo == 23) {
		$cw = new CelularWurfl($db, $ua);
		if($cw->drm == 1) {
			$in_tipos[] = $tipo;	
		}
	}
	mysql_select_db("Web", $db);
	$sql = "SELECT CC.id, CC.descripcion
		FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
		INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
		WHERE CC.tipo  IN (".implode(",", $in_tipos).") 
		AND CW.ancel = 1
		GROUP BY CC.descripcion
		ORDER BY CC.descripcion";
	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo mysql_error();
	}
	$artistas = array();
	while($row = mysql_fetch_assoc($rs)) {
		$artistas[] = array("id" => $row['id'], "nombre" => $row['descripcion']);
	}

	return $artistas;
}

function obtenerIdCatTT($db, $idC){
	$sql = "SELECT CC.id
		FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
		INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
		WHERE CC.tipo = 23
		AND CW.ancel = 1
		AND CC.descripcion = (SELECT descripcion FROM contenidos_cat WHERE id = $idC)
		GROUP BY CC.id";

	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo mysql_error();
	}
	$row = mysql_fetch_assoc($rs);
	return $row['id'];

}

function tienePolis($db, $idA){

	$sql = "SELECT CC.id
		FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
		INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
		WHERE CC.tipo = 29
		AND CW.ancel = 1
		AND CC.descripcion = (SELECT descripcion FROM contenidos_cat WHERE id = $idA)
		GROUP BY CC.id";

	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo mysql_error();
	}
	$row = mysql_fetch_assoc($rs);
	return $row['id'];

}

function buscarContenidosArtista($db, $texto, $idA){

	//Buscamos wallpapers
	$sql = "SELECT C.*
		FROM contenidos C INNER JOIN contcol_whitelist CW ON C.id = CW.contenido
		WHERE nombre like '%$texto%'
		AND CW.ancel = 1
		AND C.tipo = 7";
	$walls = array();
	$rs = mysql_query($sql, $db);
	if(!$rs){
		echo mysql_error();	
	}

	while($row = mysql_fetch_assoc($rs)) {
		$walls[] = array("id" => $row['id'], "nombre" => $row['nombre'], "archivo" => $row['archivo']); 	
	}

	//Buscamos truetones
	$sql = "SELECT C.*
		FROM contenidos C INNER JOIN contcol_whitelist CW ON C.id = CW.contenido
		INNER JOIN contenidos_cat CC ON C.categoria = CC.id
		WHERE nombre like '%$texto%'
		AND CW.ancel = 1
		AND CC.tipo = 23
		";
	if($idA) {
		$sql .= " AND CC.id= '$idA'";
	}
	$tt = array();
	$rs = mysql_query($sql, $db);

	while($row = mysql_fetch_assoc($rs)) {
		$tt[] = array("id" => $row['id'], "nombre" => $row['nombre'], "idCat" => $row['categoria']); 	
	}

	//Buscamos Polifonicos
	$sql = "SELECT C.*
		FROM contenidos C INNER JOIN contcol_whitelist CW ON C.id = CW.contenido
		INNER JOIN contenidos_cat CC ON C.categoria = CC.id
		WHERE nombre like '%$texto%'
		AND CW.ancel = 1
		AND CC.tipo = 29
		";
	if($idA) {
		$sql .= " AND CC.descripcion = (select descripcion from contenidos_cat where id = '$idA')";
	}
	$polis = array();
	$rs = mysql_query($sql, $db);

	while($row = mysql_fetch_assoc($rs)) {
		$polis[] = array("id" => $row['id'], "nombre" => $row['nombre'], "idCat" => $row['categoria']); 	
	}

	return array("walls" => $walls, "tts" => $tt, "polis" => $polis);

}


function soportaContenidoPorTipo($db, $ua, $tipo){
    $celularWurfl = new CelularWurfl($db, $ua);

    return $celularWurfl->soportaContenidoPorTipo($tipo);
}

function logeo($txt){

	$fp = @fopen("log.txt", "a+");
	//echo ("<br/>".date("Y-m-d H:i:s")."---".$txt);
	if($fp) {
		fwrite($fp, "\n\r".date("Y-m-d H:i:s")."---".$txt);
		fclose($fp);
	}

}


function soportaVideos($db, $ua) {

	$sql = "SELECT * 
		FROM MCM.celulares_modelos_wurfl CMW INNER JOIN MCM.celulares_ua_wurfl CUW ON CMW.pk_celulares_modelos_wurfl = CUW.pk_fk_celulares_modelos_wurfl
		WHERE CUW.pk_descripcion = '$ua'";
	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo "Error en la consulta::$sql::".mysql_error();
	}
	$row = mysql_fetch_assoc($rs);
	return $row['videotone'] == 1;
}

function obtenerVideos($cat = -1,$ua, $db, $pag = 0, $cant = 10, $xxx = 0){
	$xxx = intval($xxx);
	mysql_select_db("Web");
	$videos = array();
		$sql = "SELECT COUNT(distinct C.id) as c
			FROM Web.contenidos C INNER JOIN  Web.contenidos WC ON WC.id = C.id
			INNER JOIN Web.contenidos_cat WCC ON WC.categoria = WCC.id
			INNER JOIN Web.contcol_whitelist CW ON C.id = CW.contenido
			WHERE 
			C.tipo = 62
			AND CW.ancel = 1
			AND WCC.xxx = $xxx ";

		if($cat != -1) {
			$sql .= " AND C.categoria = $cat";	
		}
		

		$res = mysql_query($sql, $db);
		if(!$res) {
			echo "Error en la consulta: $sql :: ".mysql_error($db);
			exit;
		}
		$row = mysql_fetch_assoc($res);
		$videos['total'] = $row['c'];

		$pag *= $cant;
		$sql = "SELECT C.nombre, C.id as pk_contenidos, C.referencia
			FROM Web.contenidos C INNER JOIN Web.contenidos WC ON WC.id = C.id
			INNER JOIN Web.contenidos_cat WCC ON WC.categoria = WCC.id
			INNER JOIN Web.contcol_whitelist CW ON C.id = CW.contenido
			WHERE 
			C.tipo = 62
			AND CW.ancel = 1
			AND WCC.xxx = $xxx ";

		if($cat != -1) {
			$sql .= " AND C.categoria = $cat";	
		}

	    $sql .= " GROUP BY C.id";
		$sql .= " ORDER BY C.orden desc
			LIMIT $pag, $cant";
		$res = mysql_query($sql, $db);
		if(!$res) {
			echo "Error en la consulta: $sql :: ".mysql_error($db);
			exit;
		}

		while($row = mysql_fetch_assoc($res)) {
			$videos[] = $row;
		}
	return $videos;

}


function soportaElJuego($db, $ua, $idJuego){
	$idCelular = obtenerIDCelular($ua, $db);
	if($idCelular == 0) {
		return false;	
	}
	$sql = "SELECT juego
		FROM Web.gamecomp 
		WHERE juego = $idJuego
		AND celular = $idCelular";

	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo $sql."::".mysql_error();
	}
	$row = mysql_fetch_assoc($rs);

	return isset($row['juego']) && $row['juego'] > 0;

}

function chequerCobroMT($db, $idMensajeMt){
	$errno = "";
	$errstr = "";
	$socket = fsockopen(SOCKET_HOST, SOCKET_PORT, $errno, $errstr);
	//logeo("\nConectando al socket..");
	if($socket) {
		fputs($socket, "internal-id=".$idMensajeMt."\n");

		sleep(1);
		$state = "";
		do {
			$state .= fgets($socket);
	            	$stat = socket_get_status($socket);
		} while($stat['unread_bytes']);
		fclose($socket);


		//logeo("\nRespuesta::".$state);
		if(in_array($state, array(SM_STATE_DELIVERED, SM_STATE_DELETED, SM_STATE_REJECTED, SM_STATE_EXPIRED, SM_STATE_INVALID, SM_STATE_UNDELIVERABLE))) {
			//logeo("\nEstado correcto, terminando pedido");
			return $state;
		} else {
			return -1;
		}	
	} else {
		logeo("Error al conectar al socket::$errno::$errstr");	
		return -2;
	}

}

function obtenerCantidadDescargas($db, $celular){

	$sql = "SELECT count(pk_ventas_mt) as cont
		FROM movistarPeru.ventas_mt
		WHERE nro_celular = '$celular'";

	$rs = mysql_query($sql, $db);
	if(!$rs){
		logeo("Error en la consulta::$sql::".mysql_error());
	}

	$row = mysql_fetch_assoc($rs);
	return $row['cont'];
}

function limpiarValor($txt){

	$txt = str_replace("'", "", $txt);
	$txt = str_replace(" ", "", $txt);
	$txt = str_replace("'", "", $txt);
	$txt = str_replace("\"", "", $txt);
	$txt = str_replace("\\", "", $txt);
	return $txt;
}

function obtenerUltimoEstrenoPelotazo($db){
	$sql = "SELECT id
		FROM Web.contenidos
		WHERE categoria = 497
		ORDER BY orden DESC";
	$rs = mysql_query($sql, $db);
	
	$dias = 0;
	$id = 0;
	while($row = mysql_fetch_assoc($rs)) {
		$timestamp_incial = mktime(0,0,0,06,26,2007);
		$fecha_estreno = date("Ymd", strtotime($dias." days",$timestamp_incial ));
		if($fecha_estreno <= date("Ymd")) {
			$id = $row['id'];
		}
		$dias++;
	}
	return $id;
}


function obtenerDatosCategoria($db, $id){
	$sql = "SELECT *
		FROM Web.contenidos_cat
		WHERE id = $id";
	$rs = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($rs);
	return $row;
}

function obtenerContPorAutor($db, $nombreArtista, $pagina, $items_por_pagina, $type){

	$items = array();
	$sql = "SELECT COUNT(id) as cant
		FROM Web.contenidos
		WHERE autor like '$nombreArtista'";

	$res = mysql_query($sql, $db);
	if(!$res) {
		echo "Error en la consulta: $sql :: ".mysql_error($db);
		exit;
	}
	$row = mysql_fetch_assoc($res);
	$items['total'] = $row['cant'];

	$init = $pagina * $items_por_pagina;

	$sql = "SELECT *
		FROM Web.contenidos
		WHERE autor like '$nombreArtista%'
		ORDER BY orden, nombre
		LIMIT $init, $items_por_pagina";

	$res = mysql_query($sql, $db);
	if(!$res) {
		echo "Error en la consulta: $sql :: ".mysql_error($db);
		exit;
	}
	while($row = mysql_fetch_assoc($res)){
		$items[] = $row;
	}

	return $items;
}

function obtenerNombreCat($db, $idCat){
	$sql = "SELECT descripcion as nombre
		FROM Web.contenidos_cat
		WHERE id = ".$idCat;

	$rs = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($rs);
	return str_replace("&", "&amp;", $row['nombre']);
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

function obtenerContenido($db, $tipo = -1, $pagina, $por_pagina, $only_ids = array()){

	$sql = "SELECT count(id) as cont
		FROM contenidos C INNER JOIN contcol_whitelist CW ON C.id = CW.contenido
		WHERE CW.ancel = 1";

	if($tipo != -1) {
		$sql .= " AND C.tipo = $tipo";
	}

	if(count($only_ids) > 0) {
		$sql .= " AND C.id IN (".implode(",", $only_ids).")";
	}

	$rs = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($rs);

	$conts = array();
	$conts['total'] = $row['cont'];

	$init = $pagina * $por_pagina;
	$sql = "SELECT C.*, CC.id as id_cat, CC.descripcion
		FROM contenidos C INNER JOIN contcol_whitelist CW ON C.id = CW.contenido
		INNER JOIN contenidos_cat CC ON CC.id = C.categoria
		WHERE CW.ancel = 1 ";
	if($tipo != -1) {
		$sql .= " AND CC.tipo = $tipo
		AND C.tipo = $tipo";
	}

	if(count($only_ids) > 0) {
		$sql .= " AND C.id IN (".implode(",", $only_ids).")";
	}
	$sql .= " LIMIT $init, $por_pagina";

	$rs = mysql_query($sql, $db);
	while($row = mysql_fetch_assoc($rs)) {
		$conts[] = $row;
	}

	return $conts;
}

/**
 * Obtiene una lista paginada de las diferentes categorias de imagenes 
 * ordenadas alfabeticamente
 *
 * @param  resource  $db    	La conexi�n a  la base de datos
 * @param  int       $pag   	El n�mero de p�gina (empieza de 0)
 * @param  bool      $adultos 	Indica si las categor�as van a ser de adultos (por defecto vale false)
 * @param  int 	     $cant      Cantidad de resultados a traer en cada consulta (por defecto vale 10)
 * @params array     $only_cats Lista de las categorias que solamente van a mostrarse
 * @return array     Un array asociativo con los datos de las diferentes categor�as
 */
function obtenerCatImagenes($db, $pag, $adultos = false,  $cant = 10, $not_in = false, $only_cats = array()){
	mysql_select_db("Web",$db);
	return obtenerCatsContenido($db, $pag, $adultos, $cant, -1, 7, $not_in, $only_cats);
}

/*****************************************************************
 *** FUNCIONES PARA SCREENSAVERS
 *****************************************************************/
function obtenerCatScreensavers($db, $pag, $adultos = false, $cant = 10, $not_in = false){
	mysql_select_db("Web",$db);
	return obtenerCatsContenido($db, $pag, $adultos, $cant, -1, 5, $not_in);
}


function obtenerIDCelular($ua, $db){
	return getCelId($ua, $db);
}

function obtenerCatTipoCont($db, $tipo, $p_actual, $cant_pagina, $xxx = 0, $only_cats = array(), $not_cats = array()) {
	$init = $p_actual * $cant_pagina;
	$lista = array();

	$sql = "select count(C.id) as cont 
		 FROM contenidos_cat CC INNER JOIN contenidos C ON CC.id = C.categoria 
		 INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
		 WHERE C.tipo = $tipo
		 AND CC.xxx = $xxx
		 AND CW.movistar = 1";

	if(count($only_cats) > 0) {
		$sql .= " AND CC.id IN (".implode(",", $only_cats).")";	
	}
	
	if(count($not_cats) > 0) {
		$sql .= " AND CC.id NOT IN (".implode(",", $not_cats).")";	
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
		 AND CW.movistar = 1
		 AND CC.xxx = $xxx";

	if(count($only_cats) > 0) {
		$sql .= " AND CC.id IN (".implode(",", $only_cats).")";	
	}
	if(count($not_cats) > 0) {
		$sql .= " AND CC.id NOT IN (".implode(",", $not_cats).")";	
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
 * Obtienen una lista paginada de contenidos para una categor�a especifica.
 *
 * @param int 		$idCat   El ID de la categoria
 * @param resource  	$db  	 El link a la base de datos
 * @param int 		$pag     El n�mero de la p�gina actual (empieza en 0)
 * @param int 		$cant 	 La cantidad de resultados a mostrar por p�gina (por defecto vale 5)
 * @param array 	$ids_prohibidos  Ids de contenidos que no vamos a mostrar aunque esten en la categoria deseada
 * @return array 	Un array asociativo con los datos de las imagenes
 */
function obtenerContPorCat($idCat, $db, $pag = 0, $cant = 5, $type, $ids_prohibidos = array(), $ids_only = array()){
	$imgs = array();
	$pag *= $cant;

	$sql = "SELECT COUNT(id) as c
		FROM contenidos  CO INNER JOIN contcol_whitelist CW ON CW.contenido = CO.id
		WHERE 
		CO.tipo = $type
		AND CW.ancel = 1
		";
	
	if(count($ids_prohibidos) > 0) {
		$sql .= " AND CO.id NOT IN (".implode(",", $ids_prohibidos).")";	
	}

	if(count($ids_only) > 0) {
		$sql .= " AND CO.id IN (".implode(",", $ids_only).")";	
	} else {
		$sql .= " AND categoria = $idCat";
	}
	


	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	}
	$row = mysql_fetch_assoc($rs);
	$imgs['total'] = $row['c'];
	
	$sql = "SELECT id, nombre, referencia, archivo, tipo, archivo
		FROM contenidos  CO INNER JOIN contcol_whitelist CW ON CW.contenido = CO.id
		WHERE 
		CO.tipo = $type
		AND CW.ancel = 1
		";

	if(count($ids_prohibidos) > 0) {
		$sql .= " AND CO.id NOT IN (".implode(",", $ids_prohibidos).")";	
	}

	if(count($ids_only) > 0) {
		$sql .= " AND CO.id IN (".implode(",", $ids_only).")";	
	} else {
		$sql .= " AND categoria = $idCat";
	}


	 $sql .= " ORDER BY CO.orden desc, nombre 
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

	$sql .= " ORDER BY C.orden desc, C.id
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

function obtenerDatosContenido($db, $id, $es_juego = null, $dejar_preview_web = false){
	if(!$db) {
		$mic = new coneXion("Web", true);
		$db = $mic->db;	
	}
	
	mysql_select_db("Web");
	$sql = "SELECT * 
		FROM Web.contenidos
		WHERE id = $id";

	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo "Error en la consulta 5 ::$sql:: ".mysql_error();	
	}
	$datos = mysql_fetch_assoc($rs);
	if($es_juego) {
 
		    $sql = "SELECT C.tipo, C.nombre, C.id, GC.archivo, C.referencia, GI.screenshots, GI.descr, GI.descr_wap
			    FROM contenidos C INNER JOIN contenidos_cat CC ON C.categoria = CC.id
			    INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
			    INNER JOIN gamecomp GC ON C.id = GC.juego
			    INNER JOIN gamesInfo GI ON GI.game = C.id
			    WHERE 
			    C.id = '$id'";

		    $rs = mysql_query($sql,$db);
		    if(!$rs) {
			echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
			exit;
		    }

		$row = mysql_fetch_assoc($rs);
		$screens = explode(",", $row['screenshots']);
		$pathTo = $screens[0];
		$screens = $screens[1];
		$path = explode(".", $screens);
		$path = $path[0]."_p".".gif";

		$datos['screenshots'] = $pathTo.$path;
		$datos['description'] = ($row['descr_wap'] != "")?$row['descr_wap']:substr($row['descr'], 0, 100)."...";

	} else {
		if($datos['archivo'] == "") {
			$datos['archivo'] = $datos['referencia'];	
		}
		$datos['archivo'] = str_replace(".3gp", ".gif", $datos['archivo']);
		if(strpos($datos['archivo'], ".gif") !== false) {
			switch($datos['tipo']) {
				case 5:
					$datos['screenshots'] = str_replace("128x128", "40x32", $datos['archivo']);	
				break;
				case 7:
					$datos['screenshots'] = str_replace("128x128", "62x62", $datos['archivo']);
				break;
				case 63:
					$datos['screenshots'] = str_replace(".gif", "_wap.gif", $datos['archivo']);	
				break;
				case 65:
				case 62:
					if(!$dejar_preview_web) {
						$datos['screenshots'] = str_replace(".gif", ".gif", $datos['referencia']);	
					} else {
						$datos['screenshots'] = str_replace("128x128", "60x60", $datos['referencia']);	
					}
				break;
			}
		} else {
			$datos['screenshots'] = null;	
		}

	}
	return $datos;
    

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


/******************
  **************
  ESTAS FUNCIONES NO HAY QUE BORRARLAS, LO DEM�S SIIIII
  ******************
  *******************/

function obtenerCatsContenido($db, $pag, $adultos = false, $cant = 10, $from, $tipo, $not_in = array(), $only_cats = array()){

	$xxx = ($adultos)?1:0;
	$conts = array();
	$pag *= $cant;

	$sql = "SELECT COUNT(DISTINCT(CC.id)) AS c, COUNT(CO.id) as cont
		FROM contenidos_cat CC INNER JOIN contenidos CO ON CC.id = CO.categoria
		INNER JOIN contcol_whitelist CW ON CW.contenido = CO.id
		WHERE CO.tipo = $tipo
		AND CC.xxx= $xxx
		AND CC.free = 0
		AND CW.ancel = 1
		";

	if(count($only_cats) > 0) {
		$sql .= " AND CC.id IN (".implode(",", $only_cats).")";	
	}
	
	if(count($not_in) > 0) {
		$sql .= " AND CC.id NOT IN (".implode(",", $not_in).")";
		$sql .= " AND (CC.idPadre is null OR CC.idPadre NOT IN (".implode(",", $not_in)."))";	
	}
	
	$sql .= " HAVING cont > 0 ";

	$rs = mysql_query($sql, $db);
	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	}
	$row = mysql_fetch_assoc($rs);
	$conts['total'] = $row['c'];

	$sql = "SELECT descripcion, CC.id, count(C.id) as cont
		FROM contenidos_cat CC INNER JOIN contenidos C ON CC.id = C.categoria
		INNER JOIN contcol_whitelist CW ON CW.contenido = C.id
		WHERE C.tipo = $tipo
		AND CC.xxx= $xxx
		AND CC.free = 0 
		AND CW.ancel = 1
		";

	if(count($only_cats) > 0) {
		$sql .= " AND CC.id IN (".implode(",", $only_cats).")";	
	}
	
	if(count($not_in) > 0) {
		$sql .= " AND CC.id NOT IN (".implode(",", $not_in).")";
		$sql .= " AND (CC.idPadre is null OR CC.idPadre NOT IN (".implode(",", $not_in)."))";	
	}

	$sql .= " GROUP BY CC.id	
		HAVING cont > 0
		ORDER BY descripcion
		LIMIT $pag, $cant";
	$rs = mysql_query($sql,$db);

	if(!$rs) {
		echo "Error en el query: ".$sql."::".mysql_error($db)."::".__FILE__."::".__LINE__;	
		exit;
	}

	//$conts['total'] = mysql_num_rows($rs);
	while($row = mysql_fetch_assoc($rs)) {
		$conts[] = $row;	
	}
	return $conts;
}




?>