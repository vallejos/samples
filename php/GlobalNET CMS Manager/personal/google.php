<?php

include_once("/var/www/lib/conexion.php");

$devices_obligatorios = array(
	"sonyericsson_w580i_ver0",
	"sonyericsson_w200a_ver1",
	"sonyericsson_w395_ver1",
	"nokia5530_ver1",
	"sonyericsson_c510a_ver1",
	"nokia_5300_ver1",
	"samsung_s5230",
	"samsung_gt_m2310_ver1",
	"nokia_5130",
	"sonyericsson_w380_ver1",
	"sonyericsson_w705a_ver1",
	"sonyeric_w300i_verr9a",
	"nokia_5610expressmusic_ver1",
	"sonyericsson_k550i_ver1",
	"nokia_5310_xpressmusic_ver1",
	"sonyericsson_w760i_subr3aa",
	"sonyericsson_w350a_ver1",
	"blackberry_8520",
	"nokia_n95_ver1_sub_8gb_fl3",
	"samsung_sgh-e215l",
	"lg_kp215_ver1",
	"lg_kp570_ver1",
	"nokia_n97",
	"sonyericsson_s500i_ver1",
	"nokia_6131_ver1",
	"sonyericsson_c905_ver1_suba",
	"nokia_5200_ver1",
	"nokia_6300_ver1",
	"samsung_sgh_f275l_ver1",
	"mot_z6_ver1",
	"sonyericsson_k790_ver1_sub1",
	"nokia_2760_ver1",
	"sonyericsson_w880i_r6bc",
	"sonyericsson_w610i_ver1",
	"nokia_5220_expressmusic_ver1",
	"sonyericsson_k850i_ver1",
	"sonyericsson_c902_ver1",
	"nokia_1680c_ver1_sub2b",
	"nokia_5800d_ver1",
	"sonyericsson_w595",
	"sonyericsson_k310a_ver1",
	"lg_kf600d_ver1",
	"samsung_sgh_f480l_ver1",
	"sonyericsson_z530i_ver1",
	"nokia_6120c-moz",
	"nokia_2630_ver1",
	"samsung_sgh_f250l_ver1",
	"nokia_e71_ver1",
	"samsung_sgh_j700_ver1",
	"mot_v8xx_ver1",
	"samsung_sgh_a736_ver1",
	"samsung_sgh_e236_ver1",
	"lg_me970d_ver1",
	"lg_mg800_ver1",
	"sonyericsson_w810i_subr4ea",
	"samsung_e2210_ver1",
	"blackberry9000_ver1",
	"sonyericsson_z750i_ver1",
	"sonyericsson_r300a_ver1",
	"sonyericsson_w910i_ver1",
	"nokia_3220_ver1",
	"Samsung_gt_s5233t",
	"sonyericsson_z550a",
	"nokia_6101_ver1",
	"lg_kf755",
	"sonyericsson_z310a_ver1",
	"nokia_3500_ver1_sub0660",
	"sonyericsson_w710i_ver1",
	"nokia_6061_ver1",
	"mot_em28_ver1",
	"mot_a1200eam_ver1",
	"sonyericsson_z750a_ver1",
	"nokia_5700_ver1_sub",
	"sonyericsson_z710i_ver1",
	"sonyericsson_k510a_ver1",
	"nokia_3120c_ver_2_sub0716",
	"samsung_sgh_t519_ver1",
	"sonyericsson_w600i_ver1",
	"lg_kf510_ver1",
	"nokia_5070b_ver1",
	"nokia_2220",
	"nokia_n85_ver1",
	"nokia_2690",
	"sonyericsson_t303_ver1_subr2cc001",
	"sonyericsson_w995",
	"samsung_m8800l_ver1",
	"samsung_sgh_u600_ver1",
	"nokia_3555c_ver1",
	"samsung_sgh_x640_ver1",
	"mot_w396_ver1",
	"samsung_j700i_ver1",
	"nokia_2660_ver1",
	"nokia_n73_ver1_20628001",
	"nokia_7610_supernova_ver1",
	"lg_kf350_ver1",
	"nokia_3250_ver1",
	"lg_km500_ver1",
	"samsung_sgh_e496_ver1",
	"lg_gw300",
	"nokia_n75_ver1",
	"blackberry_9700"
);


$devices_encontrados = array(
'sonyericsson_w580i_ver0' => '2275',
'sonyericsson_w200a_ver1' => '1172',
'sonyericsson_w395_ver1' => '3900',
'nokia5530_ver1' => '4231',
'sonyericsson_c510a_ver1' => '3895',
'nokia_5300_ver1' => '753',
'samsung_s5230' => '3986',
'samsung_gt_m2310_ver1' => '3972',
'nokia_5130' => '1253',
'sonyericsson_w380_ver1' => '3187',
'sonyericsson_w705a_ver1' => '3810',
'sonyeric_w300i_verr9a' => '643',
'nokia_5610expressmusic_ver1' => '2150',
'sonyericsson_k550i_ver1' => '796',
'nokia_5310_xpressmusic_ver1' => '2540',
'sonyericsson_w760i_subr3aa' => '3079',
'sonyericsson_w350a_ver1' => '3131',
'blackberry_8520' => '4172',
'nokia_n95_ver1_sub_8gb_fl3' => '773',
'samsung_sgh-e215l' => '3293',
'lg_kp215_ver1' => '3148',
'lg_kp570_ver1' => '3970',
'nokia_n97' => '',
'sonyericsson_s500i_ver1' => '2084',
'nokia_6131_ver1' => '699',
'sonyericsson_c905_ver1_suba' => '3404',
'nokia_5200_ver1' => '704',
'nokia_6300_ver1' => '909',
'samsung_sgh_f275l_ver1' => '3369',
'mot_z6_ver1' => '2116',
'sonyericsson_k790_ver1_sub1' => '783',
'nokia_2760_ver1' => '2146',
'sonyericsson_w880i_r6bc' => '1039',
'sonyericsson_w610i_ver1' => '882',
'nokia_5220_expressmusic_ver1' => '3681',
'sonyericsson_k850i_ver1' => '2271',
'sonyericsson_c902_ver1' => '3128',
'nokia_1680c_ver1_sub2b' => '3328',
'nokia_5800d_ver1' => '4174',
'sonyericsson_w595' => '3431',
'sonyericsson_k310a_ver1' => '663',
'lg_kf600d_ver1' => '3255',
'samsung_sgh_f480l_ver1' => '3330',
'sonyericsson_z530i_ver1' => '693',
'nokia_6120c-moz' => '2420',
'nokia_2630_ver1' => '1185',
'samsung_sgh_f250l_ver1' => '2582',
'nokia_e71_ver1' => '4116',
'samsung_sgh_j700_ver1' => '3266',
'mot_v8xx_ver1' => '2115',
'samsung_sgh_a736_ver1' => '2587',
'samsung_sgh_e236_ver1' => '1189',
'lg_me970d_ver1' => '3416',
'lg_mg800_ver1' => '1823',
'sonyericsson_w810i_subr4ea' => '647',
'samsung_e2210_ver1' => '4227',
'blackberry9000_ver1' => '3323',
'sonyericsson_z750i_ver1' => '2455',
'sonyericsson_r300a_ver1' => '3075',
'sonyericsson_w910i_ver1' => '2264',
'nokia_3220_ver1' => '268',
'Samsung_gt_s5233t' => '',
'sonyericsson_z550a' => '880',
'nokia_6101_ver1' => '338',
'lg_kf755' => '3372',
'sonyericsson_z310a_ver1' => '2506',
'nokia_3500_ver1_sub0660' => '2502',
'sonyericsson_w710i_ver1' => '798',
'nokia_6061_ver1' => '655',
'mot_em28_ver1' => '3584',
'mot_a1200eam_ver1' => '1182',
'sonyericsson_z750a_ver1' => '2528',
'nokia_5700_ver1_sub' => '2158',
'sonyericsson_z710i_ver1' => '3184',
'sonyericsson_k510a_ver1' => '2532',
'nokia_3120c_ver_2_sub0716' => '3367',
'samsung_sgh_t519_ver1' => '943',
'sonyericsson_w600i_ver1' => '518',
'lg_kf510_ver1' => '3257',
'nokia_5070b_ver1' => '2317',
'nokia_2220' => '204',
'nokia_n85_ver1' => '3473',
'nokia_2690' => '4229',
'sonyericsson_t303_ver1_subr2cc001' => '3158',
'sonyericsson_w995' => '3935',
'samsung_m8800l_ver1' => '4230',
'samsung_sgh_u600_ver1' => '888',
'nokia_3555c_ver1' => '3143',
'samsung_sgh_x640_ver1' => '341',
'mot_w396_ver1' => '3329',
'samsung_j700i_ver1' => '4166',
'nokia_2660_ver1' => '2145',
'nokia_n73_ver1_20628001' => '703',
'nokia_7610_supernova_ver1' => '3386',
'lg_kf350_ver1' => '3700',
'nokia_3250_ver1' => '676',
'lg_km500_ver1' => '2690',
'samsung_sgh_e496_ver1' => '1153',
'lg_gw300' => '4228',
'nokia_n75_ver1' => '770',
'blackberry_9700' => '4219',
);

function getSuggestedWebIdByMigModel($model) {
	$data = file("devices-Ok.csv");
	foreach ($data as $ln => $ld) {
		list ($migModel, $webId) = explode(",",$ld);
		if (trim(str_replace("\n", "", $migModel)) == trim(str_replace("\n", "", $model))) {
//			echo "+ $model\n";
			return trim(str_replace("\n", "", $webId));
//		} else {
//			echo "- $model\n";
		}
	}
	return FALSE;
}


function createSqlInList() {
	global $devices_obligatorios;
	$sqlInString = "";
	$found = FALSE;
	foreach ($devices_obligatorios as $migDevice) {
		$found = getSuggestedWebIdByMigModel($migDevice);
		if ($found !== FALSE) {
//			echo "'$migDevice' => '$found',\n";
			$sqlInString .= "$found,";
		} else {
//			echo "'$migDevice' => '',\n";
		}
//		$sqlInString .= ($found !== FALSE) ? "$found," : "";
	}
	$sqlInString = substr($sqlInString, 0, -1);
	return $sqlInString;
}



function createSqlInListFromArray() {
	global $devices_encontrados;
	$sqlInString = "";
	$found = FALSE;
	foreach ($devices_encontrados as $migDevice => $idWeb) {
		$found = getSuggestedWebIdByMigModel($migDevice);
		if (!empty($idWeb)) {
//			echo "'$migDevice' => '$idWeb',\n";
			$sqlInString .= "$idWeb,";
		} else {
//			echo "'$migDevice' => '',\n";
		}
//		$sqlInString .= ($found !== FALSE) ? "$found," : "";
	}
	$sqlInString = substr($sqlInString, 0, -1);
	return $sqlInString;
}


/**
 *
 * MAIN PROCESS
 *
 */
$p = $_GET["p"];
$t = $_GET["t"];
$r = $_GET["r"];
$i=0;
$dbc = new conexion("Web");
$sqlIn = createSqlInListFromArray();
$s1 = " SELECT id, nombre FROM Web.contenidos WHERE tipo=31 order by id DESC ";
$rs1 = mysql_query($s1, $dbc->db);
if (!$rs1) die ("ERROR SQL ($s1): ".mysql_error());
echo "<table border=1 width=100%><tr><td>#</td><td>id contenido</td><td>total</td><td>nombre</td></tr>";
while ($o1 = mysql_fetch_object($rs1)) {
	$idJuego = $o1->id;
	$sql = "SELECT COUNT(*) total, gc.juego, c.nombre
		FROM Web.gamecomp gc
		INNER JOIN Web.contenidos c ON (c.id=gc.juego)
		WHERE gc.celular IN ($sqlIn) AND gc.juego=$o1->id
		GROUP BY 2 HAVING total>75 ORDER BY total asc LIMIT 100 ";
	$sql = " SELECT count(*) total  from (select distinct gc.juego, gc.celular, c.nombre FROM Web.gamecomp gc INNER JOIN Web.contenidos c ON (c.id=gc.juego) WHERE gc.celular IN ($sqlIn) and gc.juego=$o1->id) as query HAVING total>$r ORDER BY 1
	";
	$rs = mysql_query($sql, $dbc->db);
	if (!$rs) die ("ERROR SQL ($sql): ".mysql_error());
	while ($obj = mysql_fetch_object($rs)) {
		$i++;
		echo "<tr><td>$i</td><td>$o1->id</td><td>$obj->total</td><td>$o1->nombre</td></tr>";
	}
}
echo "</table>";


?>