<?php
$secciones = array();
$secciones["clientes"] = (object) array("label" => "Clientes", "url" => "index.php?do=clientes");
$secciones["mms"] = (object) array("label" => "MMS", "url" => "index.php?do=mms");
$secciones["barrios"] = (object) array("label" => "Barrios", "url" => "index.php?do=barrios");
?>

<ul>
<?php
foreach($secciones as $seccion) {
	$selFlag = ($do == strtolower($seccion->label));
	$href = ($selFlag) ? '#' : $seccion->url;
	$class = ($selFlag) ? ' class="selected" ' : ' ';
	echo '<li><a'.$class.'href="'.$href.'">'.$seccion->label.'</a></li>';
}
?>
</ul>