<?php 
include_once("php/includes.php");
$do = (isset($_GET['do']) && $_GET['do'] != null) ? $_GET['do'] : "barrios";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>QR Admin</title>
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/main.js"></script>
</head>

<body>
	
<div id="contenedor">
	<div id="menu"><?php include_once("menu.php"); ?></div>
	<div id="contenido"><?php include($do.".php"); ?></div>
</div>

</body>
</html>