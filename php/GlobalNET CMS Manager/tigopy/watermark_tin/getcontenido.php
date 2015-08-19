<?
include_once("./Contenido.class.php");
$filename = '';
if(!empty($_GET['id']) ){
	$id = $_GET['id'];
	$width = (!empty($_GET['width']))? $_GET['width']: null;
	$height = (!empty($_GET['height']))? $_GET['height']: null;
	$logo = (!empty($_GET['logo']))? $_GET['logo']: '';
	$ext = (!empty($_GET['ext']))? $_GET['ext']: '';
	$contenido = new Contenido($id,$logo);
	if($ext!="") $contenido->setExtension($ext);
	$filename = $contenido->getPath($width,$height,"#ff00ff");
}else{
	echo 'falta el id.';
	exit();
}
header('Content-type: image/jpeg');
header('Content-length: '.filesize($filename));
readfile($filename);

?>