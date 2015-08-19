<?php

// APP CONFIG
define("APP_NAME", "HPSYNC BRASIL");
define("APP_DIR", "/www/crons/sync-hp-brasil");
define("LIB_DIR", "/www/crons/lib");
define("LOG_DIR", "/www/crons/logs");
define("TMP_DIR", "/www/crons/tmp");


// MAXXCLIENT WS CONFIGURATION
define("WS_CONTRACTID_BRASIL", "2146850"); // Brasil
// ALBUMS
define("WS_START_ALBUM", 0);
define("WS_SIZE_ALBUM", 500); // el webservice de netm devuelve max 501 resultados
// GENRES
define("WS_START_GENRE", 0);
define("WS_SIZE_GENRE", 500); // el webservice de netm devuelve max 501 resultados
// TRACKS
define("WS_START_TRACK", 0);
define("WS_SIZE_TRACK", 500); // el webservice de netm devuelve max 501 resultados
// ARTIST
define("WS_START_ARTIST", 0);
define("WS_SIZE_ARTIST", 5); // el webservice de netm devuelve max 501 resultados


// DB CONFIG
define("DB_NAME", "hp_brasil");
define("DB_HOST", "10.210.210.16");
define("DB_USER", "user");
define("DB_PASSWORD", "pass");


// DB QUERY CONFIG
define("DB_SELECT_QUERYLIMIT", 10000); // for select * from tracks limit (evitar un php fatal error por falta de memoria en selects muy grandes)

// STORE CONFIG
define("ID_SELLO", 1);


?>
