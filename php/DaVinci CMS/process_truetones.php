<?php

include_once("constantes.php");
include_once("lib/conexion.php");
include_once("lib/functions.php");
include_once("classes/truetone.class.php");

// laion, 17 abril 2009: soy puto y me la como doblada!!


$dbc = new conexion("Web");

$sql = "SELECT c.*, p.nombre nombre_proveedor, cc.descripcion as nombre_categoria
	FROM Web.contenidos c
	INNER JOIN Web.contenidos_proveedores p ON (p.id=c.proveedor)
	INNER JOIN Web.contenidos_cat cc ON (cc.id=c.categoria)
	WHERE c.id IN ($ids) ";
$rs = mysql_query($sql, $dbc->db);

if (!$rs) die ("ERROR: $sql -> ".mysql_error());

$tt = new truetone();
$map = $xml_map["truetone_tpl"];
$submap = $xml_map["truetone_file_subtpl"];


while ($obj = mysql_fetch_object($rs)) {
	// seteo vars
	$nombre = $obj->nombre;
	$proveedor = $obj->nombre_proveedor;
	$categoria = $obj->nombre_categoria;
	$code = $obj->id;
	$operador = "";

	// preparo map al objeto
	$tt->set("name", $nombre);
	$tt->set("provider", $proveedor);
	$tt->set("royalty", "");
	$tt->set("cat", $categoria);
	$tt->set("subcat", "");
	$tt->set("code", $code);
	$tt->set("operator", $operador);
	$tt->set("searchkeywords", $keywords);
	$tt->set("musiclabel", $musiclabel);
	$tt->set("movie", $nombre);
	$tt->set("album", $nombre);
	$tt->set("artist", $nombre);
	$tt->set("file_webpreview", $nombre);
	$tt->set("file_wappreview", $nombre);
	$tt->set("file_objects", $nombre);

	$data = map($tt, $map, "truetone_tpl.xml");


	// copio archivos


	// escribo xml
	$xml_fname = $obj->id; // <-- nombre para el xml
	$fxml = fopen($work_dir."/".$xml_fname);


	// insert db para tracking de operacion


	// zipeo
	$zipname = ""; // <-- id insert anterior


	// muevo zip


	// borro work_dir


}




?>