<?php
class RecorridoFactory implements RecorridoInterface {
	private static $recorridos = array(
		'cv' => '', // ciudad vieja
		'ce' => '', // centro
		'co' => '', // cordon
		'ag' => '', // aguada
		'pp' => '', // parque rodo y punta carretas
		'cp' => '', // capurro y prado
		);
	
	static public function Create($recorrido) {
		if (!isset(self::$recorridos[$recorrido]) {
			throw new Exception('El tipo de Recorrido '.$recorrido.' es desconocido.');
		} 

		switch (self::$recorridos[$recorrido]) {
			case 'cv': return new RecorridoCV($recorrido);
			case 'ce': return new RecorridoCE($recorrido);
			case 'co': return new RecorridoCO($recorrido);
			case 'ag': return new RecorridoAG($recorrido);
			case 'pp': return new RecorridoPP($recorrido);
			case 'cp': return new RecorridoCP($recorrido);
			default:
				throw new Exception('Tipo de recorrido desconocido.');
		}
	}
}

?>