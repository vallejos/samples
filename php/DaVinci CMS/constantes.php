<?php

define("HOST_PREVIEW", "216.150.27.11");
define("USR_PREVIEW", "wmast");
define("PASS_PREVIEW", "hulkverde");

define("LOCALHOST_PREVIEW", "10.0.0.241");
define("LOCALUSR_PREVIEW", "contenido");
define("LOCALPASS_PREVIEW", "wyibsun0Ob");

if (!defined(MIG_POLYTONE)) define(MIG_POLYTONE, "PT");
if (!defined(MIG_REALTONE)) define(MIG_REALTONE, "RT");
if (!defined(MIG_VIDEO)) define(MIG_VIDEO, "VD");
if (!defined(MIG_WALLPAPER)) define(MIG_WALLPAPER, "WP");
if (!defined(MIG_SCREENSAVER)) define(MIG_SCREENSAVER, "SS");
if (!defined(MIG_FULLTRACK)) define(MIG_FULLTRACK, "FT");
if (!defined(MIG_THEME)) define(MIG_THEME, "TH");
if (!defined(MIG_GAME)) define(MIG_GAME, "JG");

if (!defined(WAZZUP_POLYTONE)) define(WAZZUP_POLYTONE, "29");
if (!defined(WAZZUP_REALTONE)) define(WAZZUP_REALTONE, "23");
if (!defined(WAZZUP_VIDEO)) define(WAZZUP_VIDEO, "62");
if (!defined(WAZZUP_WALLPAPER)) define(WAZZUP_WALLPAPER, "7");
if (!defined(WAZZUP_SCREENSAVER)) define(WAZZUP_SCREENSAVER, "5");
if (!defined(WAZZUP_FULLTRACK)) define(WAZZUP_FULLTRACK, "26");
if (!defined(WAZZUP_THEME)) define(WAZZUP_THEME, "63");
if (!defined(WAZZUP_GAME)) define(WAZZUP_GAME, "31");

if (!defined(TMP_DIR)) define(TMP_DIR, "tmp");
if (!defined(TMP_DIR_PT)) define(TMP_DIR_PT, "tmpPT");
if (!defined(TMP_DIR_RT)) define(TMP_DIR_RT, "tmpRT");
if (!defined(TMP_DIR_VD)) define(TMP_DIR_VD, "tmpVD");
if (!defined(TMP_DIR_WP)) define(TMP_DIR_WP, "tmpWP");
if (!defined(TMP_DIR_SS)) define(TMP_DIR_SS, "tmpSS");
if (!defined(TMP_DIR_FT)) define(TMP_DIR_FT, "tmpFT");
if (!defined(TMP_DIR_TH)) define(TMP_DIR_TH, "tmpTH");
if (!defined(TMP_DIR_JG)) define(TMP_DIR_JG, "tmpJG");
if (!defined(ZIP_DIR)) define(ZIP_DIR, "zip");


// FOLDERS
$destino_dir = "zip";
//$work_dir = "temp";
$origen_dir = "";
$template_dir = "templates";
$zip_path = "zip";
$watermark_dir = "watermark";
//$targetDir = $work_dir;

// CONFIG
$MAX_ZIP_SIZE = 45000; // bytes, 90MB (aprox. 50% zipeado)

// MAP TEMPLATES
$xml_map = Array(
	"truetone_tpl" => array(
		"name" => "%NAME%",
		"provider" => "%PROVIDER%",
		"royalty" => "%ROYALTY%",
		"cat" => "%CAT%",
		"subcat" => "%SUBCAT%",
		"code" => "%CODE%",
		"operator" => "%OPERATOR%",
		"searchkeywords" => "%SEARCHKEYWORDS%",
		"musiclabel" => "%MUSICLABEL%",
		"movie" => "%MOVIE%",
		"album" => "%ALBUM%",
		"artist" => "%ARTIST%",
		"file_webpreview" => "%FILE_WEBPREVIEW%",
		"file_wappreview" => "%FILE_WAPPREVIEW%",
		"file_objects" => "%FILE_OBJECTS%",
	),
	"truetone_file_subtpl" => array(
		"file" => "%FILENAME%",
	),

	"video_tpl" => array(
		"nombre_contenido" => "%NOMBRE_CONTENIDO%",
		"proveedor" => "%PROVEEDOR%",
		"royalty" => "%ROYALTY%",
		"categoria" => "%CATEGORIA%",
		"subcategoria" => "%SUBCATEGORIA%",
		"code" => "%CODE%",
		"type" => "%TYPE%",
		"operator" => "%OPERATOR%",
		"search_keywords" => "%SEARCH_KEYWORDS%",
		"musiclabel" => "%MUSICLABEL%",
		"movie" => "%MOVIE%",
		"album" => "%ALBUM%",
		"artista" => "%ARTISTA%",
		"webpreview" => "%WEB_PREVIEW%",
		"wappreview" => "%WAP_PREVIEW%",
		"objects" => "%OBJECTS%",
	),

	"wallpaper_tpl" => array(
		"nombre_contenido" => "%NOMBRE_CONTENIDO%",
		"proveedor" => "%PROVEEDOR%",
		"royalty" => "%ROYALTY%",
		"categoria" => "%CATEGORIA%",
		"subcategoria" => "%SUBCATEGORIA%",
		"code" => "%CODE%",
		"operator" => "%OPERATOR%",
		"search_keywords" => "%SEARCH_KEYWORDS%",
		"musiclabel" => "%MUSICLABEL%",
		"movie" => "%MOVIE%",
		"album" => "%ALBUM%",
		"provider_code" => "%PROVIDER_CODE%",
		"cla" => "%CLA%",
		"expirydate" => "%EXPIRYDATE%",
		"activatedate" => "%ACTIVATEDATE%",
		"artista" => "%ARTISTA%",
		"webpreview" => "%WEB_PREVIEW%",
		"wappreview" => "%WAP_PREVIEW%",
		"objects" => "%OBJECTS%",
	),
	"wallpaper_file_subtpl" => array(
		"file" => "%FILENAME%",
	),

	"game_tpl" => array(
		"nombre_contenido" => "%NOMBRE_CONTENIDO%",
		"proveedor" => "%PROVEEDOR%",
		"type" => "%TYPE%",
		"categoria" => "%CATEGORIA%",
		"subcategoria" => "%SUBCATEGORIA%",
		"code" => "%CODE%",
		"operator" => "%OPERATOR%",
		"search_keywords" => "%SEARCH_KEYWORDS%",
		"shortdesc" => "%SHORTDESC%",
		"longdesc" => "%LONGDESC%",
		"cls" => "%CLS%",
		"provider_code" => "%PROVIDER_CODE%",
		"cla" => "%CLA%",
		"webpreview" => "%WEB_PREVIEW%",
		"wappreview" => "%WAP_PREVIEW%",
		"handsets" => "%HANDSETS%",
	),
	"game_file_subtpl" => array(
		"handsets" => "%HANDSETS%",
	),

);


$game_webpreview_formats = array(
	"jpg" => 1,
	"gif" => 1,
	"bmp" => 0,
	"png" => 1,
);
$game_wappreview_formats = array(
	"jpg" => 0,
	"gif" => 1,
	"bmp" => 0,
	"png" => 1,
);
$game_dimensions_wap_preview = array(
	"50x50" => 1,
	"40x40" => 0,
	"25x25" => 0,
);
$game_dimensions_web_preview = array(
	"100x100" => 0,
	"101x80" => 0,
	"50x50" => 1,
	"96x96" => 1,
);

$video_webpreview_formats = array(
	"jpg" => 1,
	"gif" => 1,
	"bmp" => 0,
	"png" => 0,
);
$video_wappreview_formats = array(
	"jpg" => 0,
	"gif" => 1,
	"bmp" => 0,
	"png" => 1,
);
$video_dimensions_wap_preview = array(
	"50x50" => 1,
	"40x40" => 0,
	"25x25" => 0,
);
$video_dimensions_web_preview = array(
	"100x100" => 0,
	"101x80" => 1,
	"50x50" => 0,
	"96x96" => 0,
);
$video_dimensions = array(
	"96x96" => 1,
	"176x144" => 1, // archivo
	"128x96" => 1,
);


$wallpaper_formats = array(
	"jpg" => 1,
	"gif" => 1,
	"bmp" => 0,
	"png" => 0,
);
$wallpaper_webpreview_formats = array(
	"jpg" => 1,
	"gif" => 1,
	"bmp" => 0,
	"png" => 0,
);
$wallpaper_wappreview_formats = array(
	"jpg" => 0,
	"gif" => 1,
	"bmp" => 0,
	"png" => 1,
);
$wallpaper_dimensions_wap_preview = array(
	"50x50" => 1,
	"40x40" => 0,
	"25x25" => 0,
);
$wallpaper_dimensions_web_preview = array(
	"100x100" => 0,
	"101x80" => 1,
);
$wallpaper_dimensions = array(
	"352x288" => 0,
	"96x128" => 0,
	"176x176" => 0,
	"128x144" => 1,
	"128x96" => 1,
	"120x140" => 0,
	"352x416" => 0,
	"640x200" => 0,
	"220x220" => 0,
	"176x144" => 1,
	"96x65" => 1,
	"240x200" => 0,
	"120x160" => 1,
	"208x256" => 0,
	"640x640" => 0,
	"160x128" => 0,
	"150x140" => 0,
	"128x128" => 1,
	"101x80" => 0,
	"128x160" =>1,
	"132x176" => 0,
	"174x132" => 1,
	"176x208" => 1,
	"176x220" => 0,
	"208x144" => 1,
	"208x208" => 1,
	"110x110" => 1,
	"128x115" => 1,
	"500x500" => 1,
);




?>