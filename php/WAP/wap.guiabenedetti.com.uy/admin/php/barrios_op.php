<?php
include_once("includes.php");

$barrios = new Barrios();
$accion = (isset($_POST['accion'])) ? $_POST['accion'] : null;

switch ($accion) {
	
	case "editar":
		$id = $_POST['id'];
		$barrio = $_POST['barrio'];
		$title = $_POST['title'];
		$edicion = $barrios->editar($id, $barrio, $title);
		if ($edicion->error) {
			switch ($edicion->desc) {
				case "error_existe":
					$res = "Ya existe un barrio con el titulo ".$title." o con el barrio ".$barrio;
					break;
				case "error_sql":
					$res = "Error en la consulta";
					break;
			}
		} else { 
			$res = "Barrio editado correctamente";
		}
		echo $res;
		break;

	case "listar":
		$lista = $barrios->listar();
		$html = '';
		$jump = "\n";
		foreach ($lista as $b) {
			$html .= '<li>'.$jump;
			$html .= '<span class="list_itm b_id">'.$b->id.'</span>'.$jump;
			$html .= '<span class="list_itm b_bar">'.$b->barrio.'</span>'.$jump;
			$html .= '<span class="list_itm b_tit">'.$b->title.'</span>'.$jump;
			$html .= '<span class="list_itm b_acc">'.$jump;
			$html .= '<a class="" href="javascript:Barrios.getPuntos(\''.$b->id.'\')">Puntos</a>'.$jump;
			$html .= '<a class="" href="javascript:Barrios.getData(\''.$b->id.'\', \''.$b->barrio.'\', \''.$b->title.'\')">Editar</a>'.$jump;
			$html .= '<a class="" href="#">Borrar</a>'.$jump;
			$html .= '</span>'.$jump;
			$html .= '<ul class="lista_puntos" id="lp_'.$b->id.'"></ul></li>'.$jump;
		}
		echo $html;
		break;
	
	case "getPuntos":
		$id = $_POST['id'];
		$puntos = $barrios->getPuntos($id);
		$html = '';
		$jump = "\n";
		foreach ($puntos as $p) {
			$html .= '<li>'.$jump;
			$html .= 'Punto '.$p.':<br/><br/>'.$jump;
			$html .= '<a href="#">Desactivar</a>'.$jump;
			$html .= '<a rel="elegible" href="javascript:Puntos.getPuntoData(\''.$id.'\', \''.$p.'\')">Editar Punto</a>'.$jump;
			$html .= '<a rel="elegible" href="javascript:Puntos.getContenidos(\''.$id.'\', \''.$p.'\')">Editar Contenidos</a>'.$jump;
			$html .= '<a href="#">Eliminar</a>'.$jump;
			$html .= '</li>'.$jump;
		}
		echo $html;
		break;
	
}

?>