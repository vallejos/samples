<?php
include_once(dirname(__FILE__)."/../sql_cache/SQLCache.class.php");


define("ERR_CELULAR_NO_ASOCIADO", 20); //El celular est� ingresado, pero no se asoci� a ning�n modelo en Web.celulares
define("ERR_CELULAR_NO_INGRESADO", 10); //El celular no est� ingresado en la BASE



class miscFunctions {

	const ANCHO_PANTALLA_MEDIANA = 128;
	const ANCHO_PANTALLA_GRANDE  = 160;
	const ANCHO_PANTALLA_CHICA   = 96;

	static function soportaAnchoMayor($db, $ua){
		$cw = new CelularWurfl($db, $ua);
		return ($cw->pantalla_ancho > miscFunctions::ANCHO_PANTALLA_MEDIANA);
	}

	static function soportaAnchoMedio($db, $ua){
		$cw = new CelularWurfl($db, $ua);
	/*	if($_SERVER['REMOTE_ADDR'] == "200.40.206.110") {
		    print_r($cw);
		}*/
		///echo $cw->pantalla_ancho.">".ANCHO_PANTALLA_CHICA." && " . $cw->pantalla_ancho." <= ". ANCHO_PANTALLA_MEDIANO."<br/>";
		return ($cw->pantalla_ancho > miscFunctions::ANCHO_PANTALLA_CHICA && $cw->pantalla_ancho <= miscFunctions::ANCHO_PANTALLA_MEDIANA);

	}

	static function soportaAnchoChico($db, $ua){
		$cw = new CelularWurfl($db, $ua);
		return ($cw->pantalla_ancho <= miscFunctions::ANCHO_PANTALLA_CHICA);

	}


	static function checkCelularHomologado($ua) {
		include_once($_SERVER['DOCUMENT_ROOT']."/../lib/conexion.php");
		$conn = new coneXion("MCM");
		$db   = $conn->db;

		$oCache = new SQLCache("wap_homologados");

		$err_type = 0;
		//Chequeamos si el celular est�  o no ingresado en la base del MCM
		$sql = "SELECT  pk_fk_celulares_modelos_wurfl
				FROM MCM.celulares_ua_wurfl
				WHERE pk_descripcion = '$ua'";
		$row = $oCache->doSelect($sql, 60); //cacheamos los resultados por 30 segundos

		/*$rs = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($rs);*/
		$id_celular_wurfl = $row[0]['pk_fk_celulares_modelos_wurfl'];

		if($id_celular_wurfl > 0) { //si est�, chequeamos si est� o no asociado a la base Web.celulares
			$sql = "SELECT fk_celulares_web
					FROM MCM.celulares_modelos_wurfl
					WHERE pk_celulares_modelos_wurfl = '$id_celular_wurfl'";

			$row = $oCache->doSelect($sql, 60);
/*
			$rs = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($rs);
			*/
			$id_celular_web = $row[0]['fk_celulares_web'];

			if($id_celular_web == 0) {
				$err_type = ERR_CELULAR_NO_ASOCIADO;
			}
		} else {
			$err_type = ERR_CELULAR_NO_INGRESADO;
		}


		if($err_type != 0) {
			$sql = "SELECT count(pk_ua) as cont
					FROM wap_misc.celulares_no_homologados
					WHERE pk_ua = '$ua'";
			$row = $oCache->doSelect($sql, 60);
			/*
			$rs = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($rs);
			*/
			if($row[0]['cont'] == 0) {//Si ya no ingresamos este UA lo insertamos
				$sql = "INSERT INTO wap_misc.celulares_no_homologados
						(pk_ua, fecha, hora, err_type)
						VALUES
						('$ua', CURDATE(), CURTIME(), '".$err_type."')";
				mysql_query($sql, $db);

				include_once($_SERVER['DOCUMENT_ROOT']."/../lib/email.class.php");
				$msg = "Nuevo celular encontrado, UA:: ".$ua;
				if($err_type == ERR_CELULAR_NO_ASOCIADO) {
					$msg .= "<br/>Descripcion:: El celular est� ingresado, falta asociarlo a Web.contenidos";
				} else {
					$msg .= "<br/>Descripcion:: Hay que ingresar el celular en las tablas del MCM";
				}
				$oMail = new Email("WapMan", "fromwapman@globalnetmobile.com", "fromwapman@globalnetmobile.com", "Celular no homologado encontrado", $msg, $msg);
				$oMail->send();
			}

		}
		mysql_close($db);

	}
}


?>
