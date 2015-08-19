<?php
class SuscriptionFactory implements FactoryInterface {
	private static $suscriptions = array('juegos wazzup' => 'game', 'alertas cumbias' => 'sms');
	
	static public function Create($susc) {
		if (!isset(self::$suscriptions[$susc]) {
			throw new Exception('El tipo de suscripcion '.$susc.' es desconocido.');
		} 

		switch (self::$suscriptions[$susc]) {
			case 'game': return new GameSuscription($susc);
			case 'sms': return new SMSSuscription($susc);
			default:
				throw new Exception('Tipo de suscripcion desconocida.');
		}
	}
}

?>