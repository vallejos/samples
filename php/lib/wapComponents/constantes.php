<?php

//Tipos de listas de links
define("LISTA_LINKS", 1); //Lista de links, uno arriba del otro
define("LISTA2X2_LINKS", 2); //Lista de links, de 2 en 2
define("NAVEGACION_LINKS", 3); //Usada para la barra de navegaci�n: volver | home
define("LISTA_NUMERADA_LINKS", 4);
define("LISTA_INTERCALADA_LINKS", 5);
define("LISTA_COLOR_LINKS", 6);


///Tama�os del texto
define("NORMAL_FONT_SIZE", "");
define("BIG_FONT_SIZE", "big");
define("SMALL_FONT_SIZE", "small");

//Tipos de Secciones
define("SECCION_TITULO", 1);
define("SECCION_NORMAL", 1);
define("SECCION_SIN_TITULO", 2);

//Posiciones de las imagenes con respecto a los links
define("LEFT_SIDE", 1);
define("TOP_SIDE", 2);
define("BOTTOM_SIDE", 3);
define("RIGHT_SIDE", 4);

define("ANCHO_PANTALLA_CHICA", "96");
define("ANCHO_PANTALLA_MEDIANA", "114");
define("ANCHO_PANTALLA_GRANDE", "160");

define("IMAGEN_NEW", "images/ico_new.gif");
define("TITLE_NEW", "Nuevo");

define("IMAGEN_HOT", "images/ico_hot.gif");
define("TITLE_HOT", "Hot");

define("IMAGEN_HIT", "images/ico_hit.gif");
define("TITLE_HIT", "Hit");


//Lista de UA de celulares que deben forzarse a ser WAP 1.X en lugar de 2.0
$CELULARES_WAP = array("LG-MG110 AU", "SonyEricssonT290","SonyEricssonT290i", "SonyEricssonT290a", "SonyEricssonT290c", "SonyEricssonZ300i","Nokia6100", "Nokia6100A", "SEC-SGHE715", "SEC-SGHE715A", "SEC-SGHE715e", "SEC-SGHE715i","Alcatel OT-C630","Alcatel-OT-C630", "Alcatel-OH2");

$CELULARES_PESO_SIMPLE = array("Alcatel OT-C630","Alcatel-OT-C630");

//Tipos de contenidos usados normalmente en las WAPS
define("CNT_WALLPAPER", 	7);
define("CNT_TRUETONE",	 	23);
define("CNT_POLIFONICO", 	29);
define("CNT_SCREENSAVER", 	5);
define("CNT_JUEGO", 		31);
define("CNT_VIDEO", 		62);
define("CNT_THEME", 		63);

?>
