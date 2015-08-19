<?php



function parseIntoDB($dbc, $reportId, $fileDate, $fileTone) {
	$dates = file($fileDate);
	$tones = file($fileTone);
	$errorDates = FALSE;
	$errorTones = FALSE;

	// primero los dates
	foreach ($dates as $lineNumber => $lineContent) {
		$fecha="0000-00-00";
		$comprar=0;
		$regalar=0;
		$descargar=0;
		$lineContent = trim($lineContent);

		if ((stripos($lineContent, "total") === FALSE) && (stripos($lineContent, "comprar") === FALSE)) {
			list($fecha,$comprar,$regalar,$descargar) = split(";", $lineContent);
			$sql = "INSERT INTO admins.warner_detail_date SET reportId='$reportId',fecha='$fecha',comprar='$comprar',regalar='$regalar',descargar='$descargar' ";
			$rs = mysql_query($sql, $dbc->db);
			if (!$rs) {
				// error saving to db
				echo "Error guardando en db: $sql (".mysql_error().")\n";
				$errorDates = TRUE;
			}
		}
	}

	// primero los dates
	foreach ($tones as $lineNumber => $lineContent) {
		$id = "";
		$nombre = "";
		$autor = "";
		$comprar = 0;
		$regalar = 0;
		$descargar = 0;
		$lineContent = trim($lineContent);

		if ((stripos($lineContent, "total") === FALSE) && (stripos($lineContent, "autor;comprar") === FALSE)) {
			list($id,$nombre,$autor,$comprar,$regalar,$descargar) = split(";", $lineContent);
			$sql = "INSERT INTO admins.warner_detail_tones SET reportId='$reportId',idWarner='$id',nombre='$nombre',autor='$autor',comprar='$comprar',regalar='$regalar',descargar='$descargar' ";
			$rs = mysql_query($sql, $dbc->db);
			if (!$rs) {
				// error saving to db
				echo "Error guardando en db: $sql (".mysql_error().")\n";
				$errorTones = TRUE;
			}
		}
	}

	if ($errorTones === FALSE && $errorDates === FALSE) return TRUE;
	else return FALSE;
}


function getGrid($song) {
	$song = str_replace("(", "", $song);
	$song = str_replace(")", "", $song);
	$song = trim($song);

	$a = array(
		"Right Round "=>"A10302B0000817525U",
		"New York, New York Master Ringback"=>"A10302B0000228611R",
		"Know Your Enemy"=>"A10302B0000855318M",
		"I'm Yours"=>"A10302B00004917589",
		"Let's Do It"=>"A10302B0000493607B",
		"Llegaste Tu"=>"A10302B0000251965S",
		"Primavera anticipada it is my song duet with James Blunt"=>"A10302B0000802404C",
		"New Divide So Give Me Reason"=>"A10302B0000895902A",
		"O Tu  O Ninguna"=>"A10302B0000129646G",
		"Vivir sin aire En vivo"=>"A10302B0000510756X",
		"Labios compartidos En vivo"=>"A10302B00005106822",
		"Si no te hubieras ido En vivo 2"=>"A10302B0000512590F",
		"Amante bandido Dueto 2007"=>"A10302B0000359905Z",
		"Clavado en un bar En vivo"=>"A10302B0000510771Z",
		"Dejame entrar En vivo "=>"A10302B0000510645C",
		"Nada Particular"=>"A10302B0000374684A",
		"Como Un Lobo"=>"A10302B0000374698X",
		"Nena Dueto 2007"=>"A10302B0000359745Z",
		"Morena Mía"=>"A10302B00003747021",
		"Sevilla Audio RBT"=>"A10302B0000253057T",
		"Ignorance"=>"A10302B0000911509N",
		"Muevelo Original Version - Rap Muevelo Mijita"=>"A10302B0000404964E",
		"Muevelo Original Version - Coro Muevelo Muy"=>"A10302B0000404941S",
		"Es Este Amor Audio RBT"=>"A10302B0000301253W",
		"Eres Mia Audio RBT"=>"A10302B0000301247S",
		"Aquello Que Me Diste Audio RBT"=>"A10302B0000301220D",
		"12 por 8 Audio RBT"=>"A10302B0000301197G",
		"Completamente Loca Audio RBT"=>"A10302B00003012309",
		"Hicimos Un Trato Audio RBT"=>"A10302B0000302304Y",
		"Ese Ultimo Momento Audio RBT"=>"A10302B00003022698",
		"Ese Que Me Dio La Vida Audio RBT"=>"A10302B0000301261W",
		"Ole"=>"A10302B0000372015D",
		"One Of The Brightest Stars Intro"=>"A10302B0000425370N",
		"Every Rose Has Its Thorn Acoustic"=>"A10302B0000727457C",
		"Amate y salvate"=>"A10302B0000101956W",
		"Aqui"=>"A10302B0000102614N",
		"Ellos Son Asi Audio RBT"=>"A10302B00003012413",
		"Duelo Al Amanecer Audio RBT"=>"A10302B0000301237W",
		"Hay Un Universo De Pequeas Cos"=>"A10302B0000302283C",
		"Intro Pa' Mi Ponce"=>"A10302B0000392799M",
		"Bambu"=>"A10302B00003746962",
		"Los chicos"=>"A10302B0000421336D",
		"Jump"=>"A10302B0000078720M",
		"Summer Hair = Forever Young"=>"A10302B00006495659",
		"Bureo, Bureo Ringback"=>"A10302B0000284502K",
		"Payaso Featuring Eddie Dee & Voltio Ringback"=>"A10302B0000284538V",
		"Llévatelo Todo Ringback"=>"A10302B00002845176",
		"Extremidades Ringback"=>"A10302B0000284512G",
		"Pon La Cara Ringback"=>"A10302B00002845408",
		"Si tu no estas"=>"A10302B0000415647R",
		"Yo Puedo Hacer"=>"A10302B00008482714",
		"Es Asi"=>"A10302B0000848287N",
		"Besame"=>"A10302B0000848285R",
		"Sin Ti Sin Mi"=>"A10302B0000771541U",
		"Niña Buena"=>"A10302B0000771561M",
		"Ni Tu Ni Yo Feat Paquita la del Barrio"=>"A10302B0000901855L",
		"Hoy llueve hoy duele"=>"A10302B0000302307S",
		"Ill Take Everything Intro"=>"A10302B00004253891",
		"Fragilidad feat Joy"=>"A10302B0000969062Q",
		"Go jump"=>"A10302B0000078720M",
		"Solo tu feat Raquel del Rosari"=>"A10302B0000969050Y",
		"Por siempre en mi mente"=>"A10302B0000432877M",
		"Loco Calamaro"=>"A10302B0000116710U",
		"Its alright"=>"A10302B00008442512",
		"Right Round Featuring Keha"=>"A10302B0000817525U",
		"Know Your Enemys"=>"A10302B0000855318M",
		"Im Yours"=>"A10302B00004917589",
		"Ya no quiero ver tu foto"=>"A10302B0000351002T",
		"Lets do it"=>"A10302B0000493607B",
		"La misma de ayer la incondicio"=>"A10302B0000078742A",
		"No se tu pero yo"=>"A10302B0000129366W",
		"New York New York Master Ringb"=>"A10302B0000228611R",
		"O Tu O Ninguna"=>"A10302B0000129646G",
		"Suave como me mata tu mirada"=>"A10302B0000123041M",
		"Four minutes"=>"A10302B0000498403T",
		"Morena mia"=>"A10302B00003747021",
		"Si Tu No Vuelves shakira"=>"A10302B0000359917R",
		"That What You Get"=>"A10302B00003970400",
		"Nia Buena"=>"A10302B0000771561M",
		"Sin mi sin Ti"=>"A10302B0000771541U",
		"Por siempre en mi mente"=>"A10302B0000432877M",
		"Te amo mas que a un mundo nuev"=>"A10302B0000514212X",
		"Loco Calamaro"=>"A10302B0000116710U",
		"Its alright"=>"A10302B00008442512",
		"Right Round Featuring Keha"=>"A10302B0000817525U",
		"New York New York Master Ringb"=>"A10302B0000228611R",
		"Know Your Enemys"=>"A10302B0000855318M",
		"Im Yours"=>"A10302B00004917589",
		"Ya no quiero ver tu foto"=>"A10302B0000351002T",
		"Lets do it"=>"A10302B0000493607B",
		"La misma de ayer la incondicio"=>"A10302B0000078742A",
		"No se tu pero yo"=>"A10302B0000129366W",
		"O Tu O Ninguna"=>"A10302B0000129646G",
		"Suave como me mata tu mirada"=>"A10302B0000123041M",
		"Four minutes"=>"A10302B0000498403T",
		"Morena mia"=>"A10302B00003747021",
		"Si Tu No Vuelves shakira"=>"A10302B0000359917R",
		"That What You Get"=>"A10302B00003970400",
		"Nia Buena"=>"A10302B0000771561M",
		"Sin mi sin Ti"=>"A10302B0000771541U",
		"Besa mi vida"=>"A10302B0000848285R",
		"Es asi es asi"=>"A10302B0000848287N",
		"Yo puedo hacer que tus tristez"=>"A10302B00008482714",
		"Si tu no estas aqui"=>"A10302B0000415647R",
		"Llevatelo Todo Ringback"=>"A10302B00002845176",
		"Payaso Featuring Eddie Dee Vol"=>"A10302B0000284538V",
		"A Mi Papa Ringback"=>"A10302B0000284497F",
		"Bureo Bureo Ringback"=>"A10302B0000284502K",
		"Summer Hair Forever Young"=>"A10302B00006495659",
		"Everithing we had"=>"A10302B0000368301W",
		"Si Tu No Vuelves shakira"=>"A10302B0000359917R",
		"No se tu pero yo"=>"A10302B0000129366W",
		"Morena mia"=>"A10302B00003747021",
		"O Tu O Ninguna"=>"A10302B0000129646G",
		"La misma de ayer la incondicio"=>"A10302B0000078742A",
		"Four minutes"=>"A10302B0000498403T",
		"Llevatelo Todo Ringback"=>"A10302B00002845176",
		"A Mi Papa Ringback"=>"A10302B0000284497F",
		"Sin mi sin Ti"=>"A10302B0000771541U",
		"Payaso Featuring Eddie Dee Vol"=>"A10302B0000284538V",
		"Everithing we had"=>"A10302B0000368301W",
		"Nia Buena"=>"A10302B0000771561M",
		"That What You Get"=>"A10302B00003970400",
		"Si tu no estas aqui"=>"A10302B0000415647R",
		"Yo puedo hacer que tus tristez"=>"A10302B00008482714",
		"Besa mi vida"=>"A10302B0000848285R",
		"Es asi es asi"=>"A10302B0000848287N",
		"Its alright"=>"A10302B00008442512",
		"Right Round Featuring Keha"=>"A10302B0000817525U",
		"New York New York Master Ringb"=>"A10302B0000228611R",
		"Intro Pa Mi Ponce"=>"A10302B0000392799M",
		"Te amo mas que a un mundo nuev"=>"A10302B0000514212X",
		"Loco Calamaro"=>"A10302B0000116710U",
		"Por siempre en mi mente"=>"A10302B0000432877M",
		"Volvere por siempre"=>"A10302B00002523973",
		"Lets do it"=>"A10302B0000493607B",
		"Know Your Enemys"=>"A10302B0000855318M",
		"Ya no quiero ver tu foto"=>"A10302B0000351002T",
		"Im Yours"=>"A10302B00004917589",
		"Por siempre en mi mente"=>"A10302B0000432877M",
		"Te amo mas que a un mundo nuev"=>"A10302B0000514212X",
		"Loco Calamaro"=>"A10302B0000116710U",
		"Its alright"=>"A10302B00008442512",
		"Colgando en tus manos (Remix)"=>"A10302B00009348839",
		"Corazon de Acero"=>"A10302B0000863602E",
		"Right Round Featuring Keha"=>"A10302B0000817525U",
		"Te extrano 2"=>"A10302B00009175858",
		"New York New York Master Ringb"=>"A10302B0000228611R",
		"Know Your Enemys"=>"A10302B0000855318M",
		"Love Love"=>"A10302B0000728519Z",
		"Im Yours"=>"A10302B00004917589",
		"Ya no quiero ver tu foto"=>"A10302B0000351002T",
		"Adios 2"=>"A10302B0000925080P",
		"Volvere por siempre"=>"A10302B00002523973",
		"Chocolate 2"=>"A10302B00009565236",
		"Si te vas 2"=>"A10302B0000966440H",
		"Lets do it"=>"A10302B0000493607B",
		"No se tu pero yo"=>"A10302B0000129366W",
		"Suave como me mata tu mirada"=>"A10302B0000123041M",
		"O Tu O Ninguna"=>"A10302B0000129646G",
		"La misma de ayer la incondicio"=>"A10302B0000078742A",
		"Celebration Haven t I Seen You"=>"A10302B0000938317D",
		"Four minutes"=>"A10302B0000498403T",
		"Celebration It s A Celebration"=>"A10302B0000938318B",
		"Revolver feat Lil Wayne"=>"A10302B0000958761K",
		"Haven t Met You Yet"=>"A10302B0000942500Y",
		"Morena mia"=>"A10302B00003747021",
		"Si Tu No Vuelves shakira"=>"A10302B0000359917R",
		"That What You Get"=>"A10302B00003970400",
		"Tocando Fondo-"=>"A10302B0000924135A",
		"Nia Buena"=>"A10302B0000771561M",
		"Sin mi sin Ti"=>"A10302B0000771541U",
		"Yo puedo hacer que tus tristez"=>"A10302B00008482714",
		"Besa mi vida"=>"A10302B0000848285R",
		"Es asi es asi"=>"A10302B0000848287N",
		"Si tu no estas aqui"=>"A10302B0000415647R",
		"Summer Hair Forever Young"=>"A10302B00006495659",
		"Solo tu feat Raquel del Rosari"=>"A10302B0000969050Y",
		"Fragilidad feat Joy"=>"A10302B0000969062Q",
		"Colgando en tus manos Remix"=>"A10302B00009348839",
		"Mentira 2"=>"A10302B00009431165",
		"Slo Mo Ringback"=>"A10302B00002845504",
		"Misery Business"=>"A10302B00003793996",
	);

	if (empty($a[$song])) echo "\nNOT FOUND: '$song'";
	return $a[$song];

}



function bob2usd ($bob, $mm, $yyyy) {
	global $tablaConversion2008, $tablaConversion2009, $tablaConversion2010, $tablaConversion2011;

	switch ($yyyy) {
		case "2008":
			$tableToUse = $tablaConversion2008;
		break;
		case "2009":
			$tableToUse = $tablaConversion2009;
		break;
		case "2010":
			$tableToUse = $tablaConversion2010;
		break;
		default:
		case "2011":
			$tableToUse = $tablaConversion2011;
	}

	$cotizacion = $tableToUse[$mm];

	return $bob/$cotizacion;
}



?>