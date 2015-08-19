<?php

include_once("/var/www/lib/conexion.php");

$archivos = array(
	"20090520-VA-OP3576" => "2009-05-20",
	"20090807-VA-OP3955" => "2009-08-04",
	"20091014-VA-OP4446" => "2009-10-14",
	"20091028-Miranda-OP4515" => "2009-10-28",
);

$dbc = new conexion("admins");

foreach ($archivos as $file => $fecha) {
	$data = file("metadata/$file.csv");
	foreach ($data as $ln => $dat) {
		list ($grid,$artista,$titulo,$tipo,$rbt) = split(";", $dat);
		$sql = "INSERT INTO admins.warner_metadata SET
			code='".trim($grid)."',
			artista='".trim($artista)."',
			titulo='".trim($titulo)."',
			tipoContenido='".trim($tipo)."',
			rbt='".trim($rbt)."',
			ftpFolder='".trim($file)."',
			fecha='".trim($fecha)."'
			";
		$rs = mysql_query($sql, $dbc->db);
	}
}


?>