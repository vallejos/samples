<?PHP

$_SESSION['marcas'] = array( 44 => 'PANTECH', 57 => 'HUAWEI', 47 => 'BENQ-SIEMENS', 1 => 'NOKIA', 2 => 'SIEMENS', 3 => 'SAMSUNG', 4 => 'MOTOROLA', 5 => 'ALCATEL', 6 => 'SONY-ERICSSON', 7 => 'PANASONIC', 11 => 'SAGEM', 12 => 'LG', 13 => 'SHARP', 29 => 'AUDIOVOX', 30 => 'MITSUBISHI', 32 => 'PHILIPS', 33 => 'VITEL-MOVISTAR', 34 => 'SENDO', 35 => 'INNOSTREAM', 37 => 'PRIMUS', 38 => 'BENQ', 39 => 'NEC', 40 => 'QTEK', 41 => 'DESCARGA X MO', 42 => 'HP', 43 => 'BLACKBERRY',  45 => 'UT STARCOM', 46 => 'TOSHIBA', 48 => 'SANYO', 55 => 'T-MOBILE', 58 => 'FLY', 59 => 'AMOI', 54 => 'VK-MOBILE', 61 => 'TELIT', 62 => 'HYUNDAI', 63 => 'ONDA', 56 => 'KYOCERA', 65 => 'HAIER', 66 => 'HITACHI', 52 => 'ZTE', 68 => 'BELLWAVE', 69 => 'VITELCOM', 70 => 'ORANGE', 71=> 'O2', 72 => 'MDA' , 73 => 'HTC', 75=> 'PORSCHE', 78=>'MOBISTEL', 79=>'TSM', 80=>'MAXON', 81=>'VODAFONE', 82=>'I-MATE', 83=>'CAPITEL',87=>'SONY');


function getMarca($texto){
	$texto= trim( strtolower( eregi_replace(" |\t|_", "", $texto) ) );
	$texto= trim( strtolower( eregi_replace("VK MOBILE|VKMOBILE|VK_MOBILE", "VK-MOBILE", $texto) ) );
	$texto= trim( strtolower( eregi_replace("BenQ_Siemens", "BENQ-SIEMENS", $texto) ) );
	$texto= trim( strtolower( eregi_replace("sony ericsson|sony eriksson|sonyericcson|sony_ericsson","sony-ericsson", $texto) ) );
	$aux=array();
	foreach($_SESSION['marcas'] as $id=>$marca){
		if( strstr(strtolower($texto), strtolower($marca)) ) {
			$aux[0] = $id;			
			$aux[1] = $marca;
			$aux[2] = trim( substr( $texto, strpos($texto, "/")+strlen($marca)) );
			
			return $aux;
		}
	}
}


function getIdModelo(&$modelo, $idMarca){
	extract($GLOBALS);
	if(!$modelo){
		EXIT( "modelo no encontrado en getIdModelo\nmodelo: $modelo <br><br>marca: $idMarca<hr>");
	}else if(!$idMarca){
		EXIT( "marca no encontrada en getIdModelo\nmodelo: $modelo <br><br>marca: $idMarca<hr>");
	}
	switch($idMarca){
		
		case 2://siemens
		$modelo = eregi_replace("EMOTY|VODAFONE","",$modelo);
		break;
		
		case 5:// alcatel
		$modelo = eregi_replace("-|OT|_","",$modelo);
		break;
		
		case 1://nokia		
		$modelo = eregi_replace("-rle|n–|XpressMusic|Navigator|Classic|slide|Luna|NFC|3nam|prism|arte|sapphirearte|edition|music|_","",$modelo);
		break;
		
		case 3://samsung
		$modelo = eregi_replace("carbon", "D900", trim($modelo) );
		$modelo = eregi_replace("-|SGH|Zesty","",$modelo);
		break;
		
		case 4://motorola
		$modelo = eregi_replace("l6v280","l6",$modelo);
		$modelo = eregi_replace("v3maxx|v3max|maxv3","maxxv3",$modelo);
		$modelo = eregi_replace("v6maxx|v6max|maxv6","MAXXV6",$modelo);
		$modelo = eregi_replace("MOTO|rzr|vodafone|mot|verizon|wireless|blk|black|Razr|rarz|rokr|RAZR2|razer|raz|Volans|SLVR|rizr|KRZR|PEBL|\(|\)|_","",$modelo);
		break;
		
		case 13://sharp
		$modelo = eregi_replace("Vodafone|small|SH|_","",$modelo);
		break;
		
		case 6://sonyEricsson
		$modelo = eregi_replace("melinda", "W200", trim( $modelo ) );
		$modelo = eregi_replace("MR2|MR1|Vodafone|\(|\)|_","",$modelo);
		break;
		
		case 38://benq
		$modelo = eregi_replace("siemens|_","",$modelo);
		break;
		
		case 48://SANYO
		$modelo = eregi_replace("katana", "SCP6600", trim( $modelo ) );
		break;
		
		case 11://SAGEM
		$modelo = eregi_replace("orange","",$modelo);
		break;
		
		case 12://LG
		$modelo = eregi_replace("shine|vibecam|butterfly|chocolate|\(|\)|_","",$modelo);
		break;
	}
	$modelo1 = eregi_replace("-| ","",$modelo);

	$sql=	"SELECT id FROM Web.celulares WHERE marca=$idMarca 
	AND (modelo LIKE '$modelo'
	OR modelo LIKE '$modelo %' 	
	OR modelo LIKE '%$modelo %' 	
	OR modelo LIKE '%$modelo' 	
	OR REPLACE( REPLACE(modelo, ' ', ''), '-', '' ) LIKE '$modelo1' )"; 
	
	$rs = mysql_query($sql, $db);
	$o = mysql_fetch_object($rs);
	return $o->id;
}












?>