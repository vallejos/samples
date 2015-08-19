<?php

if (!defined(PROVIDER_CODE)) define (PROVIDER_CODE, "0002");

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

if (!defined(FULLTRACK_DIR)) define(FULLTRACK_DIR, "/home/kamus/Web/uy/ancel/FULLTRACKS");

if (!defined(XML_HEADER)) define(XML_HEADER, '<?xml version="1.0" encoding="utf-8" standalone="yes"?>');

if (!defined(FTP_CONN_RETRIES)) define(FTP_CONN_RETRIES, 4);
if (!defined(FTP_DOWN_RETRIES)) define(FTP_DOWN_RETRIES, 4);
if (!defined(FTP_USA)) define(FTP_USA, "216.150.27.11");
if (!defined(FTP_USA_USR)) define(FTP_USA_USR, "wmast");
if (!defined(FTP_USA_PWD)) define(FTP_USA_PWD, "hulkverde");

if (!defined(CARRIERS)) define(CARRIERS, "comcelcolombia,claropanama,claroperu,portaecuador");
if (!defined(LANGS)) define(LANGS, "es-CO,es-EC,es-PA,es-PE,es-MX");
if (!defined(PREVIEW_WIDTH)) define(PREVIEW_WIDTH, "100");
if (!defined(PREVIEW_HEIGHT)) define(PREVIEW_HEIGHT, "100");

if (!defined(CP_NAME)) define(CP_NAME, "Wazzup_cp");

$servicesMig = array(
	"wallpaper" => "S-SyCo1sKAIlV",
	"realtone" => "S-OyCo1sKAIlV",
	"polytone" => "S-NyCo1sKAIlV",
	"game" => "S-MyCo1sKAIlV",
	"theme" => "S-QyCo1sKAIlV",
	"video" => "S-RyCo1sKAIlV",
	"fulltrack" => "S-LyCo1sKAIlV",
	"screensaver" => "S-PyCo1sKAIlV",
);

$premiumResourcesMig = array(
	"wallpaper" => "wazzup-imagenes",
	"realtone" => "wazzup-mp3",
	"polytone" => "wazzup-ringtones",
	"game" => "wazzup-juegos",
	"theme" => "wazzup-temas",
	"video" => "wazzup-videos",
	"fulltrack" => "wazzup-fulltracks",
	"screensaver" => "wazzup-animaciones",
);


?>