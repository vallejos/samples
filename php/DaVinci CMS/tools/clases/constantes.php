<?php

define("DATABASE", "mms_videoblog_comcel_co");

define("CABEZAL", "");
define("APP", "3898");

define("TABLA_ENTRADA_IMAGE", "mm7.image_entrada_comcel_co");
define("TABLA_ENTRADA_VIDEO", "mm7.video_entrada_comcel_co");

define("TABLA_SALIDA_IMAGE", "mm7.image_salida_comcel_co");
define("TABLA_SALIDA_VIDEO", "mm7.video_salida_comcel_co");

define("PATH_ACCESORIOS", "../../accesorios/");
define("CUOTA",15360000); // 15 mb

define("PATH_ENTRADA_IMAGE", $_SERVER['DOCUMENT_ROOT']."/../tmp/mm7/comcel_co/entrada/image/");
define("PATH_ENTRADA_VIDEO", $_SERVER['DOCUMENT_ROOT']."/../tmp/mm7/comcel_co/entrada/video/");

define("PATH_SALIDA_IMAGE", $_SERVER['DOCUMENT_ROOT']."/../tmp/mm7/comcel_co/salida/image/");
define("PATH_SALIDA_VIDEO", $_SERVER['DOCUMENT_ROOT']."/../tmp/mm7/comcel_co/salida/video/");

define("THUMB_WIDTH",80);
define("THUMB_HEIGHT",60);

//define("FFMPEG","ffmpeg");
define("FFMPEG","c:/wamp/ffmpeg/ffmpeg");

$method = $_GET;

//contentType para pasar al mm7sender
?>