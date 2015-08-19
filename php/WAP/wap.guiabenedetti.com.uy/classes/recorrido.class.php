<?php

abstract class Recorrido {
	protected $barrio = NULL;
	protected $punto = NULL;

	public function __construct($recorrido) {
		if (!empty($recorrido)) {
			$this->barrio = substr($recorrido, 0, 2);
			$puntoSize = sizeof($recorrido) - sizeof($this->barrio); // should be sizeof($id) -2;
			$this->punto = substr($recorrido, -$puntoSize;
		}
	}

	public function getPunto() {
		return $this->punto;
	}

	public function getBarrio() {
		return $this->barrio;
	}
	
}

?>