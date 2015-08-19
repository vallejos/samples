<?php

/**
 * El banner es b�sicamente un Link con una Imagen
 */
class Banner extends WapComponent{

	var $link;
	var $template;
	var $conn;

	/**
	 * Constructor.
	 * 
	 * @param srting $ua 		El User-Agent del celular
	 * @param string $href 		El destino del link
	 * @param string $img_src 	El path a la imagen que tendr� el banner
	 * @param string $alt 		El parametro ALT de la imagen del banner
	 */
	function Banner($ua, $href, $img_src, $alt = ""){
		    $this->conn = new coneXion("Web", true);
		    $tam = "chico";
		    if(miscFunctions::soportaAnchoChico($this->conn->db, $ua)) {
			    $tam = "chico";
		    }
		    if(miscFunctions::soportaAnchoMedio($this->conn->db, $ua)) {
			    $tam = "mediano";
		    }
		    if(miscFunctions::soportaAnchoMayor($this->conn->db, $ua)) {
			    $tam = "grande";
		    }
/*
		    $img_src = explode(".", $img_src);
		    $pre = "";
		    for($i = 0; $i < count($img_src) - 1; $i++) {
			$pre .= $img_src[$i];
		    }
		    $img_src = $pre."_".$tam.".".$img_src[count($img_src) - 1];
*/
		    $img_src = str_replace(".gif", "_".$tam.".gif", $img_src);
		    $img_src = str_replace(".jpg", "_".$tam.".jpg", $img_src);



		    if($href != "") {
			$this->link = new Link($href);
		        $this->link->AddComponent(new Imagen($img_src, $alt));	
		    } else {
			$this->link = new Imagen($img_src, $alt);
		   }
		$this->template = "templates/banner.tpl";
	}

	function Display(){
		$html = $this->_loadTemplate();

		$html = str_replace("#CONTENIDO#", $this->link->Display(), $html);	
		return $html;
	}
	
}

?>
