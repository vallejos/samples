<?php

if (!defined(PROVIDER_CODE)) define (PROVIDER_CODE, "0002");

if (!defined(DRUTT_POLYTONE)) define(DRUTT_POLYTONE, "PT");
if (!defined(DRUTT_REALTONE)) define(DRUTT_REALTONE, "RT");
if (!defined(DRUTT_VIDEO)) define(DRUTT_VIDEO, "VD");
if (!defined(DRUTT_WALLPAPER)) define(DRUTT_WALLPAPER, "WP");
if (!defined(DRUTT_SCREENSAVER)) define(DRUTT_SCREENSAVER, "SS");
if (!defined(DRUTT_FULLTRACK)) define(DRUTT_FULLTRACK, "FT");
if (!defined(DRUTT_THEME)) define(DRUTT_THEME, "TH");
if (!defined(DRUTT_GAME)) define(DRUTT_GAME, "JG");

if (!defined(WAZZUP_POLYTONE)) define(WAZZUP_POLYTONE, "29");
if (!defined(WAZZUP_REALTONE)) define(WAZZUP_REALTONE, "23");
if (!defined(WAZZUP_VIDEO)) define(WAZZUP_VIDEO, "62");
if (!defined(WAZZUP_WALLPAPER)) define(WAZZUP_WALLPAPER, "7");
if (!defined(WAZZUP_SCREENSAVER)) define(WAZZUP_SCREENSAVER, "5");
if (!defined(WAZZUP_FULLTRACK)) define(WAZZUP_FULLTRACK, "26");
if (!defined(WAZZUP_THEME)) define(WAZZUP_THEME, "63");
if (!defined(WAZZUP_GAME)) define(WAZZUP_GAME, "31");

if (!defined(TMP_DIR)) define(TMP_DIR, $globalIncludeDir."/tmp");
if (!defined(TMP_DIR_PT)) define(TMP_DIR_PT, $globalIncludeDir."/tmpPT");
if (!defined(TMP_DIR_RT)) define(TMP_DIR_RT, $globalIncludeDir."/tmpRT");
if (!defined(TMP_DIR_VD)) define(TMP_DIR_VD, $globalIncludeDir."/tmpVD");
if (!defined(TMP_DIR_WP)) define(TMP_DIR_WP, $globalIncludeDir."/tmpWP");
if (!defined(TMP_DIR_SS)) define(TMP_DIR_SS, $globalIncludeDir."/tmpSS");
if (!defined(TMP_DIR_FT)) define(TMP_DIR_FT, $globalIncludeDir."/tmpFT");
if (!defined(TMP_DIR_TH)) define(TMP_DIR_TH, $globalIncludeDir."/tmpTH");
if (!defined(TMP_DIR_JG)) define(TMP_DIR_JG, $globalIncludeDir."/tmpJG");
if (!defined(ZIP_DIR)) define(ZIP_DIR, $globalIncludeDir."/zip");

if (!defined(FULLTRACK_DIR)) define(FULLTRACK_DIR, "/home/kamus/Web/uy/ancel/FULLTRACKS");


$servicesDrutt = array(
	"wallpaper" => "S-SyCo1sKAIlV",
	"realtone" => "S-OyCo1sKAIlV",
	"polytone" => "S-NyCo1sKAIlV",
	"game" => "S-MyCo1sKAIlV",
	"theme" => "S-QyCo1sKAIlV",
	"video" => "S-RyCo1sKAIlV",
	"fulltrack" => "S-LyCo1sKAIlV",
	"screensaver" => "S-PyCo1sKAIlV",
);

$premiumResourcesDrutt = array(
	"wallpaper" => "wazzup-imagenes",
	"realtone" => "wazzup-mp3",
	"polytone" => "wazzup-ringtones",
	"game" => "wazzup-juegos",
	"theme" => "wazzup-temas",
	"video" => "wazzup-videos",
	"fulltrack" => "wazzup-fulltracks",
	"screensaver" => "wazzup-animaciones",
);


if (!defined(FTP_CONN_RETRIES)) define(FTP_CONN_RETRIES, 4);
if (!defined(FTP_DOWN_RETRIES)) define(FTP_DOWN_RETRIES, 4);

// hax para descargar previews de la 241
if (!defined(FTP_USA)) define(FTP_USA, "10.0.0.241");
if (!defined(FTP_USA_USR)) define(FTP_USA_USR, "user");
if (!defined(FTP_USA_PWD)) define(FTP_USA_PWD, "pass");


?>