<?php
abstract class Suscription {
	protected $susc = NULL;

	public function __construct($susc) {
		$this->susc = $susc;
	}

	public function getSusc() {
		return $this->susc;
	}
	
	function isActive() {
		return TRUE;
	}

}
?>