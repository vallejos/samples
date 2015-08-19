<?php

set_time_limit(0);

// css styles for menu
$activeTabs = array(
	"home" => "",
	"amdocs" => "",
	"drutt" => "",
	"tigopy" => "",
	"personal" => "",
	"logout" => "",
);

// ------------------------------------------------------------------------------------------------
// -- comienzo simple session
// ------------------------------------------------------------------------------------------------
session_start();
$loggedUser = FALSE;
$justLogged = FALSE;
$cms = (empty($_GET["cms"])) ? $_SESSION["cms"] : $_GET["cms"];

if (!empty($_SESSION["email"])) {
	$loggedUser = TRUE;
	$_SESSION["cms"] = $cms;
} else {
	$user = (empty($_POST["email"])) ? FALSE : $_POST["email"];
	$password = (empty($_POST["password"])) ? FALSE : $_POST["password"];
	$justLogged = (empty($_POST["submit"])) ? FALSE : TRUE;

	// permisos de usuario
	switch ($user) {
		case "leonardo.hernandez@globalnetmobile.com":
			$loggedUser = ($password == "kamus") ? TRUE : FALSE;
		break;
		case "enrique.sosa@globalnetmobile.com":
			$loggedUser = ($password == "enrique") ? TRUE : FALSE;
		break;
		case "pablo.mariani@globalnetmobile.com":
			$loggedUser = ($password == "pablo") ? TRUE : FALSE;
		break;
		case "javier.freire@globalnetmobile.com":
			$loggedUser = ($password == "javier") ? TRUE : FALSE;
		break;
		case "fernando.doglio@globalnetmobile.com":
			$loggedUser = ($password == "fernando") ? TRUE : FALSE;
		break;
		case "leon.barboza@globalnetmobile.com":
			$loggedUser = ($password == "leon") ? TRUE : FALSE;
		break;
	}

	if ($loggedUser !== FALSE) {
		$_SESSION["email"] = $user;
		$_SESSION["cms"] = $cms;
	}
}
//-- fin simple session
// ------------------------------------------------------------------------------------------------


// ------------------------------------------------------------------------------------------------
// -- opciones de navegacion en menu
// ------------------------------------------------------------------------------------------------
if (($loggedUser === TRUE) && ($justLogged === FALSE)) {
	// si user previamente logeado, navegando por el menu
	switch ($cms) {
		case "home":
			$pageToLoad = "logged.php";
			$activeTabs["home"] = 'class="current"';
                        $widget = "css/widget-azul.css";
		break;
		case "amdocs":
			$globalIncludeDir = "amdocs";
			$pageToLoad = $globalIncludeDir."/index.php";
			$activeTabs["amdocs"] = 'class="current"';
                        $logDir = dirname(__FILE__)."/".$globalIncludeDir."/logs";
                        $widget = "css/widget-azul.css";
		break;
		case "drutt":
			$globalIncludeDir = "drutt";
			$activeTabs["drutt"] = 'class="current"';
			$pageToLoad = $globalIncludeDir."/index.php";
                        $logDir = dirname(__FILE__)."/".$globalIncludeDir."/logs";
                        $widget = "css/widget-azul.css";
		break;
		case "tigopy":
			$globalIncludeDir = "tigopy";
			$activeTabs["tigopy"] = 'class="current"';
			$pageToLoad = $globalIncludeDir."/index.php";
                        $logDir = dirname(__FILE__)."/".$globalIncludeDir."/logs";
                        $widget = "css/widget-azul.css";
		break;
		case "personal":
			$globalIncludeDir = "personal";
			$activeTabs["personal"] = 'class="current"';
			$pageToLoad = $globalIncludeDir."/index.php";
                        $logDir = dirname(__FILE__)."/".$globalIncludeDir."/logs";
                        $widget = "css/widget-azul.css";
		break;
		case "logout":
			$activeTabs["logout"] = 'class="current"';
			$_SESSION["email"] = "";
			$_SESSION["cms"] = "";
			$_SESSION["folder"] = "";
			session_destroy();
			$pageToLoad = "login.php";
		break;
		default:
			$pageToLoad = "error.php";
			$activeTabs["home"] = 'class="current"';
	}
} else if ($loggedUser === TRUE) {
	// si user recien logeado
	$activeTabs["home"] = 'class="current"';
	$pageToLoad = "logged.php";
	$_SESSION["folder"] = $password;
        $widget = "css/widget-azul.css";
} else {
	// si user recien carga la page
	$activeTabs["home"] = 'class="current"';
	$pageToLoad = "login.php";
        $widget = "css/widget-azul.css";
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>GlobalNET CMS Manager, v2.0.1 - by kmS</title>
<link rel="stylesheet" type="text/css" media="all" href="css/fedora-styles.css" />
<link rel="stylesheet" href="css/menu_style.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="css/widget.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?=$widget;?>" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.expander.js"></script>
<script type="text/javascript" src="js/jquery.tools.min.js"></script>
<script type="text/javascript" src="js/init.js"></script>
<script language="javascript" type="text/javascript">
    // muestro el loading...
    $('#loading').css('display','block');
</script>
</head>
<body>

<div id="loading">
<table width="100%">
    <tr><td width="100%" align="center"><br/><br/><br/><br/><img src="images/35.gif" alt="Loading..." /></td></tr>
    <tr><td width="100%" align="center"><h1>Espere...</h1></td></tr>
    <tr><td width="100%" align="center"><p>El proceso puede demorar varios minutos.</p></td></tr>
</table>
</div>

<div id="menu">
<ul>
<li><a href="index.php?cms=home" title="Home" <?=$activeTabs["home"];?>><span>Home</span></a></li>
<li><a href="index.php?cms=amdocs" title="America Movil" <?=$activeTabs["amdocs"];?>><span>America Movil</span></a></li>
<li><a href="index.php?cms=drutt" title="Drutt Uruguay" <?=$activeTabs["drutt"];?>><span>Drutt Uruguay</span></a></li>
<li><a href="index.php?cms=tigopy" title="Tigo Paraguay" <?=$activeTabs["tigopy"];?>><span>Tigo Paraguay</span></a></li>
<li><a href="index.php?cms=personal" title="Personal Argentina" <?=$activeTabs["personal"];?>><span>Personal Argentina</span></a></li>
<li><a href="index.php?cms=logout" title="Logout" <?=$activeTabs["logout"];?>><span>Logout</span></a></li>
</ul>
</div>

<?php
include_once($pageToLoad);
?>

<script language="javascript" type="text/javascript">
  $(window).load(function() {
    $('#loading').hide();
  });
</script>

</body>
</html>
