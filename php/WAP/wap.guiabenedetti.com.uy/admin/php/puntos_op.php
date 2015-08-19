<?php
include_once("includes.php");

$puntos = new Puntos();
$json = new JSON();
$accion = (isset($_POST['accion'])) ? $_POST['accion'] : null;

switch ($accion) {
	
	case "editar":
		$id_barrio = $_POST['id_barrio'];
		$old_punto = $_POST['old_punto'];
		$punto = $_POST['punto'];
		$editar = $puntos->editar($id_barrio, $old_punto, $punto);
		if ($editar->error) {
			switch ($editar->desc) {
				case "error_existe":
					$res = "Ya existe el punto ".$punto." para este barrio";
					break;
				case "error_sql":
					$res = "Error en la consulta";
					break;
			}
		} else {
			$res = "Punto editado correctamente";
		}
		echo $res;
		break;
	
	case "getContenidos":
		$id_barrio = $_POST['id_barrio'];
		$punto = $_POST['punto'];
		$data = $puntos->getContenidos($id_barrio, $punto);
		$res = $json->serialize($data);
		echo $res;
		break;
	
	case "editarContenidos":		
		$id_barrio = $_POST['id_barrio'];
		$punto = $_POST['punto'];
		
		$img_id = $_POST['img_id'];
		$imgFlag = (bool) $_POST['imgFlag'];
		$img_title = $_POST['img_title'];
		$img = $_FILES['img'];
		
		$txt_id = $_POST['txt_id'];
		$txtFlag = (bool) $_POST['txtFlag'];
		$txt_title = $_POST['txt_title'];
		$summary = $_POST['summary'];
		$body = $_POST['body'];
		
		$contenidoTxt = ($txtFlag) ? $puntos->updateTxt($txt_id, $txt_title, $summary, $body) : $puntos->addTxt($id_barrio, $punto, $txt_title, $summary, $body);		
		$contenidoImg = ($imgFlag) ? $puntos->updateImg($img_id, $img_title, $img) : $puntos->addImg($id_barrio, $punto, $img_title, $img);
		
		$salidaTxt = ($contenidoTxt->error) ? $contenidoTxt->desc : "exitoTxt";
		$salidaImg = ($contenidoImg->error) ? $contenidoImg->desc : "exitoImg";
		
		echo "<script>alert('".$salidaTxt." - ".$salidaImg."');</script>";
		break;

}

?>