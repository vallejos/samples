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

if (!defined(XML_HEADER)) define(XML_HEADER, '<?xml version="1.0" encoding="utf-8" standalone="yes"?>');

if (!defined(FTP_CONN_RETRIES)) define(FTP_CONN_RETRIES, 4);
if (!defined(FTP_DOWN_RETRIES)) define(FTP_DOWN_RETRIES, 4);
//if (!defined(FTP_USA)) define(FTP_USA, "216.150.27.11");
//if (!defined(FTP_USA_USR)) define(FTP_USA_USR, "wmast");
//if (!defined(FTP_USA_PWD)) define(FTP_USA_PWD, "hulkverde");
// hax para descargar previews de la 241
if (!defined(FTP_USA)) define(FTP_USA, "10.0.0.241");
if (!defined(FTP_USA_USR)) define(FTP_USA_USR, "sms");
if (!defined(FTP_USA_PWD)) define(FTP_USA_PWD, "gagsUrt]");

if (!defined(FTP_241)) define(FTP_241, "10.0.0.241");
if (!defined(FTP_241_USR)) define(FTP_241_USR, "sms");
if (!defined(FTP_241_PWD)) define(FTP_241_PWD, "gagsUrt]");


if (!defined(CARRIERS)) define(CARRIERS, "comcelcolombia,claropanama,claroperu,portaecuador,clarouruguay,claroargentina");
//if (!defined(LANGS)) define(LANGS, "es-CO,es-EC,es-PA,es-PE,es-MX");
if (!defined(LANGS)) define(LANGS, "es-AR,pt-BR,es-CL,es-CO,es-EC,es-GT,es-HN,es-JM,es-MX,es-NI,es-PA,es-PY,es-PE,es-PR,es-DO,es-SA,es-UY");
if (!defined(PREVIEW_WIDTH)) define(PREVIEW_WIDTH, "100");
if (!defined(PREVIEW_HEIGHT)) define(PREVIEW_HEIGHT, "100");

if (!defined(CP_NAME)) define(CP_NAME, "Wazzup_cp");

/**
 * TODO:
 * Redimensionar el 1er screenshot
 * - a jpg
 * - después a gif en 80x80
 */

define("PERSONAL_SER", "0250066000113RJ");
define("NROCORTO", "999125");

define("MAX_KB_ZIP", 8000); //Tamaño maximo de los paquetes de zips..


$categorias_personal = array(
"Home:Juegos:Carreras y Velocidad",
"Home:Juegos:Deportes",
"Home:Juegos:Pelis y TV",
"Home:Juegos:Mesa y Puzzle",
"Home:Juegos:Accion",
"Home:Juegos:Estrategia",
"Home:Juegos:Arcade y Clásico",
"Home:Juegos:Varios",
"Home:Juegos:Cartas y Casino",
"Home:Juegos:Aventuras",
"Home:Juegos:Simulación:Mascota Virtual",
"Home:Juegos:Simulación:Simulacion",
"Home:Juegos:Fútbol",
"Home:Juegos:Tom Clancy",
"Home:Juegos:Prince of Persia",
"Home:Juegos:Sexy",
"Home:Juegos:Free Gameloft",
"Home:Juegos:infantiles",
"Home:Juegos:Hannah Montana",
"Home:Juegos:Juegos TOUCH",
);

$categorias_personal2 = array(
"Home:Videos:Chicas",
"Home:Videos:Humor:Cargadas Futbol",
"Home:Videos:Humor:Loco Cupido",
"Home:Videos:Humor:Petee and Jaydee",
"Home:Videos:Humor:Spy Games",
"Home:Videos:Humor:Alakran",
"Home:Videos:Humor:Bloopers",
"Home:Videos:Humor:Parodias",
"Home:Videos:Humor:Bernard",
"Home:Videos:Deportes Extremos:Dakar",
"Home:Videos:Deportes Extremos:Rally",
"Home:Videos:Deportes Extremos:Kite Surf",
"Home:Videos:Deportes Extremos:Skate",
"Home:Videos:Deportes Extremos:Bike",
"Home:Videos:Videos 3D",
"Home:Videos:Avatar",
"Home:Videos:Alejo y Valentina",
"Home:Videos:Bob Esponja",
"Home:Videos:Dora La Exploradora",
"Home:Videos:Meteoro",
"Home:Videos:Video names",
"Home:Videos:Caleidoscopio",
"Home:Videos:Varios",
"Home:Videos:Happy Tree Friends",
"Home:Juegos:Carreras y Velocidad",
"Home:Juegos:Deportes",
"Home:Juegos:Pelis y TV",
"Home:Juegos:Mesa y Puzzle",
"Home:Juegos:Accion",
"Home:Juegos:Estrategia",
"Home:Juegos:Arcade y Clásico",
"Home:Juegos:Varios",
"Home:Juegos:Cartas y Casino",
"Home:Juegos:Aventuras",
"Home:Juegos:Simulación:Mascota Virtual",
"Home:Juegos:Simulación:Simulacion",
"Home:Juegos:Fútbol",
"Home:Juegos:Tom Clancy",
"Home:Juegos:Prince of Persia",
"Home:Juegos:Sexy",
"Home:Juegos:Free Gameloft",
"Home:Juegos:infantiles",
"Home:Juegos:Hannah Montana",
"Home:Juegos:Juegos TOUCH",
"Home:Crazy Tones:Divertidos",
"Home:Crazy Tones:Celular con personalidad",
"Home:Crazy Tones:Animotones",
"Home:Crazy Tones:Animales",
"Home:Crazy Tones:Tonos Claudia Albertario",
"Home:Crazy Tones:Alertas SMS:Cool",
"Home:Crazy Tones:Alertas SMS:Sonidos Urbanos",
"Home:Crazy Tones:Varios",
"Home:Crazy Tones:Imitaciones",
"Home:Crazy Tones:Psicodelicos",
"Home:Crazy Tones:Rally",
"Home:Crazy Tones:Cine y TV",
"Home:Crazy Tones:Nombres",
"Home:Crazy Tones:Futbol",
"Home:Crazy Tones:Tonos Cumbieros",
"Home:Crazy Tones:Ariel Tarico",
"Home:Crazy Tones:Osvaldo Principi",
"Home:Crazy Tones:Candombe con tu nombre",
"Home:Crazy Tones:Goles Personalizados",
"Home:Imágenes:Modelos - Actores:Evangelina Anderson",
"Home:Imágenes:Modelos - Actores:Adabel Guerrero",
"Home:Imágenes:Modelos - Actores:Belen Francese",
"Home:Imágenes:Modelos - Actores:Mariana de Melo",
"Home:Imágenes:Modelos - Actores:Mellis Victoria y Soledad",
"Home:Imágenes:Modelos - Actores:Claudia Albertario",
"Home:Imágenes:Modelos - Actores:Diego Diaz",
"Home:Imágenes:Modelos - Actores:Roxana Zarecki",
"Home:Imágenes:Modelos - Actores:Carola Kirkby",
"Home:Imágenes:Cine y TV:Simpson",
"Home:Imágenes:Cine y TV:Avatar",
"Home:Imágenes:Cine y TV:Meteoro",
"Home:Imágenes:Cine y TV:Paris Hilton",
"Home:Imágenes:Cine y TV:Transformers",
"Home:Imágenes:Cine y TV:Family Guy",
"Home:Imágenes:Cine y TV:Bernard",
"Home:Imágenes:Cine y TV:Mr Bean",
"Home:Imágenes:Deportes",
"Home:Imágenes:Infantiles:Bob Esponja",
"Home:Imágenes:Infantiles:Jimmy Neutron",
"Home:Imágenes:Infantiles:Los Rugrats",
"Home:Imágenes:Infantiles:Isa",
"Home:Imágenes:Infantiles:I Carly",
"Home:Imágenes:Infantiles:Pantera Rosa",
"Home:Imágenes:Amor",
"Home:Imágenes:Urbano",
"Home:Imágenes:Paraisos",
"Home:Imágenes:Animales",
"Home:Imágenes:Arte",
"Home:Imágenes:Actitud",
"Home:Imágenes:Neones",
"Home:Imágenes:Digital",
"Home:Imágenes:Zodíaco",
"Home:Imágenes:Simbolos",
"Home:Imágenes:Varios",
"Home:Imágenes:Rally",
"Home:Imágenes:Happy Tree Friends",
"Home:Screensavers:Rally",
"Home:Screensavers:Cool",
"Home:Screensavers:Amor",
"Home:Screensavers:Infantiles",
"Home:Screensavers:Bob Esponja",
"Home:Screensavers:Avatar",
"Home:Screensavers:Jimmy Neutron",
"Home:Screensavers:Los Rugrats",
"Home:Screensavers:Meteoro",
"Home:Screensavers:I Carly",
"Home:Screensavers:Isa",
"Home:Screensavers:Chicas",
"Home:Promo Recarga:Tonos MP3 Recarga",
"Home:Promo Recarga:Videos Recarga",
"Home:Promo Recarga:Imágenes Recarga",
"Home:Novelas Java:Varios",
"Home:Coca Cola",
"Home:Aplicaciones:Finanzas y Empresas",
"Home:Aplicaciones:Utilidades",
"Home:Aplicaciones:Multimedia",
"Home:Aplicaciones:Salud",
"Home:Aplicaciones:Estilo de Vida",
"Home:Aplicaciones:Entretenimientos",
"Home:Aplicaciones:Noticias",
"Home:Aplicaciones:Viajes & Mapas",
"Home:Aplicaciones:Redes Sociales"

);
?>