<?php
error_reporting(E_ERROR);
ini_set('display_errors', '1');

include_once("includes.php");

$dbc = new conexion("Web");
//include_once("prt.php");

$step2 = "";

$nombre_tipos = array(
    "PT" => "Polytone",
    "RT" => "Realtone",
    "VD" => "Video",
    "WP" => "Wallpaper",
    "SS" => "Screensaver",
    "FT" => "Fulltrack",
    "TH" => "Theme",
    "JG" => "Game/App",
);

$sql = "SELECT * FROM Web.contenidos_proveedores WHERE americaMovil = 1 order by id asc";
$rs = mysql_query($sql, $dbc->db) or die(mysql_error());
$providers = array();
while($row = mysql_fetch_object($rs)){
    $providers[$row->id] = $row->nombre;
}

$categorias_wallpapers = array(
    "Amor y Amistad","Animales y Naturaleza","Automovilismo","Deportes","Chicas sexy","Chicos sexy","Deportes extremos","Electrónica","Futbol Internacional","Futbol Nacional","Grupero / Norteño","Infantiles","Lucha libre","Festivas","Peliculas","Pop","Ranchero / Regional","Reggaeton","Rock","Salsa / Cumbia / Tropical","TV","Personalizables","Zona Playboy","+ideas imágenes"
);

$sub_categorias_wallpapers = array(
    "1ra Division","2da Division","Accion y Aventura","Acuaticos","Aereos","Americano","Amistad","Amor","Año Nuevo","Banda","Basquetball","Beisbol","Bosque","Caricaturas","Ciencia y Ficción","Cumbia","Cumpleaños","Deportivas","Disco","Domesticos","Drama","Duranguense","Europeo","Femenino","Formula 1","Golf","Grupero","Grupos","Halloween","Hijos","House","Independencia","Infantil","Internacional","Lenceria","Mar","Masculino","Musicales","Nacional","Nascar","Navidad","New age","Norteño","Novelas","Otros","Padres","Progressive","Ranchero","Regional","Salsa","Salvajes","Selección Mexicana","Selecciones","Selva","Series","Sin camisa","Techno","Terrestres","Terror/Suspenso","Traje de baño","Tropical","Trova","T-shirt","Underwear",
);

$categorias_realtones = array(
    "Alternativo","Amor y Amistad","Animales y Naturaleza","Artistonos","Balada","Deportivos","Divertidos","Electrónica","Grupero / Norteño","Infantiles","Lucha libre","Música Internacional","Nombretonos","Peliculas","Pop Español","Pop Inglés","R and B/ Clásico/ Jazz","Ranchero / Regional","Rap y Hip-Hop","Reggae","Reggaeton","Rock en Inglés","Rock Español","Salsa / Cumbia / Tropical","Sexy","TV","Ultrasonikos"
);

$sub_categorias_realtones = array(
    "Accion y Aventura","Amistad","Amor","Automovilismo","Banda","Bosque","Caricaturas","Ciencia y Ficcion","Clasico","Clasicos","Comicos","Cumbia","Dance","Deportivos","Disco","Domesticos","Drama","Duranguense","Ellas","Ellos","Femenino","Filarmonicas","Futbol soccer","Grunge","Grupero","Grupos","Regional","Rythm and Blues","Salsa","Salvajes","Selva","Series","Ska","Techno","Terror/Suspenso","Tonos SMS","Tropical","Trova"
);

$categorias_polytones = array(
    "Alternativo","Amor y Amistad","Animales y Naturaleza","Artistonos","Balada","Deportivos","Divertidos","Electrónica","Grupero / Norteño","Infantiles","Lucha libre","Música Internacional","Nombretonos","Peliculas","Pop Español","Pop Inglés","R and B/ Clásico/ Jazz","Ranchero / Regional","Rap y Hip-Hop","Reggae","Reggaeton","Rock en Inglés","Rock Español","Salsa / Cumbia / Tropical","Sexy","TV","Ultrasonikos"
);

$sub_categorias_polytones = array(
    "Accion y Aventura","Amistad","Amor","Automovilismo","Banda","Bosque","Caricaturas","Ciencia y Ficcion","Clasico","Clasicos","Comicos","Cumbia","Dance","Deportivos","Disco","Domesticos","Drama","Duranguense","Ellas","Ellos","Femenino","Filarmonicas","Futbol soccer","Grunge","Grupero","Grupos","Regional","Rythm and Blues","Salsa","Salvajes","Selva","Series","Ska","Techno","Terror/Suspenso","Tonos SMS","Tropical","Trova"
);

$categorias_videos = array(
    "Alternativo","Amor y Amistad","Animales y Naturaleza","Balada","Chicas Sexys","Chicos Sexys","Deportivos","Divertidos","Electrónica","Futbol Nacional","Futbol Internacional","Grupero / Norteño","Infantil","Musica Internacional","Peliculas","Pop Español","Pop Inglés","R and B/ Clásico/ Jazz","Ranchero / Regional","Rap y Hip-Hop","Reggae","Reggaeton","Rock en Inglés","Rock Español","Salsa / Cumbia / Tropical","TV",
);

$sub_categorias_videos = array(
    "1ra Division","2da Division","Accion y Aventura","Americano","Amistad","Amor","Banda","Basquetball","Beisbol","Bosque","Caricaturas","Ciencia y Ficcion","Clasico","Cumbia","Deportivas","Disco","Domesticos","Drama","Duranguense","Europeo","Femenino","Golf","Grunge","Grupero","Grupos","Hijos","House","Indie","Infantil","Instrumental","Jazz","Lenceria","Mar","Masculino","Musicales","Norteño","Novelas","Otros","Padres","Progressive","Punk","Ranchero","Regional","Rythm and Blues","Salsa","Salvajes","Selección Mexicana","Selecciones","Selva","Series","Sin camisa","Ska","Techno","Terror/Suspenso","Traje de baño","Tropical","Trova","T-shirt","Underwear"
);

$categorias_screensaver = array(
    "animales", "aviones", "deportes", "zodiaco", "paisajes", "amor", "autos", "humor", "famosos", "cineytv", "arteysimbologia", "banderas", "espacio", "smilesypins", "cdcovers", "caricaturas", "fantasiaycienciaficcion", "chicassexy", "chicossexy", "pechosycolas", "famosos", "kamasutra"
);

$categorias_fulltrack = array(
    "poplatino", "oldhits", "pop", "rockargentino", "rocknacional", "cumbia", "rock", "hiphop", "reggaeton", "electronica", "rap", "reggae", "crazytones", "voicetones", "tango", "murga", "jazz", "clasica", "tvseriescine", "salsa", "sexytones"
);


$categorias_juegos = array(
    "Acción y Aventura","Carreras","Combate / Lucha libre","Deportes","Destreza","Infantil, Peliculas y T.V","Musica","Para Chicas","Sexys","Zona Gameloft","Zona Glu","ideas juegos"
);

$sub_categorias_juegos = array("Accion","Acuaticas","Americano","Autos","Aventura","Basquetbol","Beisbol","Bikini","Billar","Boliche","Caricaturas","Cartas","Combate","Deportes","Destreza","DJ","Domino","Espaciales","Estrategia","Futbol","Game city","Golf","Guerra","Habilidad Mental","I-juegos","Infantil","Lucha Libre","Mascotas","Mastergames","Melate","Moda","Motocross","Motos","Olimpicas","Peliculas","Revista","Rompecabezas","Series","Simuladores","Teniss","Test","TV","Ultramob");

$categorias_themes = array(
    "animales", "aviones", "deportes", "zodiaco", "paisajes", "amor", "autos", "humor", "famosos", "cineytv", "arteysimbologia", "banderas", "espacio", "smilesypins", "cdcovers", "caricaturas", "fantasiaycienciaficcion", "chicassexy", "chicossexy", "pechosycolas", "famosos", "kamasutra"
);


?>
<html>
    <head>
        <title>Generador ZIP - America Movil v1.0</title>
        <script>
            function setAction(tipo) {
                cat = $('#workingCat').val();
                subcat = $('#workingSubCat').val();
                tipo = $('#workingType').val();
                catLvl = $('#workingCatLvl').val();
                webCat = $('#workingWebCat').val();

                cont = false;
                contentId = $('#ids').val();
                contentCat = $('#cats').val();
                contentRangeI = $('#rango_i').val();
                contentRangeF = $('#rango_f').val();

                if (contentId != '' || contentCat != '') {
                    cont = true;
                } else {
                    if (contentRangeI == '' || contentRangeF == '') {
                        alert('Debe indicar el/los contenidos o categorias a enviar.');
                        return false;
                    } else if (contentRangeF<contentRangeI) {
                        alert('El valor de rango final debe ser mayor al inicial.');
                        return false;
                    } else {
                        cont=true;
                    }
                }

                if (cat=='' || tipo=='' || catLvl=='' || webCat=='' || cont==false) {
                    alert('Debe completar todos los datos para poder generar el XML.\nRevise el Setup Inicial.');
                    $('#t1').click();
                    return false;
                }

                document.filtros.action = 'index' + ".php";
                document.filtros.submit();
            }
            function viewHiddenCat(idT) {
                idTipo = '#'+idT.toLowerCase()+'Cat';
                $('#wpCat').css('display','none');
                $('#ssCat').css('display','none');
                $('#thCat').css('display','none');
                $('#jgCat').css('display','none');
                $('#ptCat').css('display','none');
                $('#ftCat').css('display','none');
                $('#rtCat').css('display','none');
                $('#vdCat').css('display','none');
                $(idTipo).css('display','block');
            }
            function viewHiddenSubCat(idT) {
                idTipo = '#'+idT.toLowerCase()+'SubCat';
                $('#wpSubCat').css('display','none');
                $('#ssSubCat').css('display','none');
                $('#thSubCat').css('display','none');
                $('#jgSubCat').css('display','none');
                $('#ptSubCat').css('display','none');
                $('#ftSubCat').css('display','none');
                $('#rtSubCat').css('display','none');
                $('#vdSubCat').css('display','none');
                $(idTipo).css('display','block');
            }

            function viewHiddenClientCats(idT) {
		idTipo = '#webCatDiv_'+idT.toLowerCase();
                $('#catLvlDiv').css('display','block');
		$('#webCatDiv_vd').css('display','none');
                $(idTipo).css('display','block');
            }

            function getTipo(tipo) {
                var t='';
                switch (tipo) {
                    case 'PT':
                        t='Polytones (PT)';
                        break;
                    case 'WP':
                        t='Wallpapers/Imagenes (WP)';
                        break;
                    case 'SS':
                        t='Screensavers/Animaciones (SS)';
                        break;
                    case 'VD':
                        t='Videos (VD)';
                        break;
                    case 'RT':
                        t='Realtones/MP3 (RT)';
                        break;
                    case 'FT':
                        t='Fulltracks (FT)';
                        break;
                    case 'JG':
                        t='Java Games (JG)';
                        break;
                    case 'TH':
                        t='Themes (TH)';
                        break;
                }
                return t;
            }

            function setHtml() {
                html = '<h3>1/2 Setup Inicial...</h3><ul>';

                provider = $('#workingProvider').val();
                if (provider!= '') html += '<li>Proveedor: '+getTipo(proveedor)+'</li>';

                tipo = $('#workingCat').val();
                if (tipo != '') html += '<li>Tipo: '+getTipo(tipo)+'</li>';

                wc = $('#contentType').val();
                idWc = '#catmig'+wc.toLowerCase();
                cat = $(idWc).val();
                if (cat != '') html += '<li>Categoria: '+cat+'</li>';

                idWsc = '#subcatmig'+wc.toLowerCase();
                subcat = $(idWsc).val();
                if (subcat != '') html += '<li>Sub-categoria: '+subcat+'</li>';

                catLvl = $('#workingCatLvl').val();
                if (catLvl != '') html += '<li>Category Level: '+catLvl+'</li>';

                webCat = $('#workingWebCat').val();
                if (webCat != '') html += '<li>Website Category: '+webCat+'</li>';

                rating = $('#workingRating').val();
                if (rating != '') html += '<li>Rating: '+rating+'</li>';



               html += '</ul>';
                $('#working-on').html(html);
            }

            function gotoStep2() {
                $('#workingCatLvl').val('');
                $('#workingWebCat').val('');
                tipo = $('#contentType').val();
                idTipo = '#'+tipo.toLowerCase()+'Cat';
                idStyle = $(idTipo).css('display');
                provider = $('#provider').val();
                viewHiddenCat(tipo);
                viewHiddenSubCat(tipo);
		viewHiddenClientCats(tipo);

                if (tipo != '') {
                    $('#t2').click();
                    $('#workingCat').val(tipo);
                    $('#workingType').val(tipo);
                    $('#workingProvider').val(provider);
                    setHtml();
                } else {
                    alert('Debe seleccionar un Tipo de contenido y un proveedor.');
                    return false;
                }
            }

            function gotoStep3() {
                wc = $('#contentType').val();
                idWc = '#catmig'+wc.toLowerCase();
                cat = $(idWc).val();

                idWsc = '#subcatmig'+wc.toLowerCase();
                subcat = $(idWsc).val();

                if (wc != '' && cat != '' && subcat != '') {
                    $('#t3').click();
                    setHtml();
                } else {
                    alert('Debe seleccionar una Categoria y Sub-Categoria asociada en Mig.');
                    return false;
                }
            }
            function gotoStep4() {
                tipo = $('#contentType').val();
                catLvl = $('#catLvl').val();
                webCat = $('#webCat_' + tipo.toLowerCase()).val();
                $('#ratingTitle').css('display','block');

                if (catLvl != '' && webCat != '') {
                    $('#t4').click();
                    $('#workingCatLvl').val(catLvl);
                    $('#workingWebCat').val(webCat);
                    setHtml();
                } else {
                    alert('Debe seleccionar el Category Level y Website Category (ambos)');
                    return false;
                }
            }
            function gotoStep5() {
                rating = $('#rating').val();
                $('#cont-ids').css('display','block');

                if (rating != '') {
                    $('#t5').click();
                    $('#workingRating').val(rating);
                    setHtml();
                } else {
                    alert('Debe seleccionar un rating');
                    return false;
                }
            }
        </script>
        <link rel="stylesheet" type="text/css" media="all" href="css/fedora-styles.css">
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.expander.js"></script>
        <script type="text/javascript" src="js/jquery.tools.min.js"></script>
        <script type="text/javascript" src="js/init.js"></script>
    </head>
    <body>

        <h1>Generador de archivos XML para Ideas America Movil</h1>
        <hr/>

        <div id="content">

            <form action="" name="filtros" method="post">
                <input type="hidden" name="jiden" value="se" />
                <input type="hidden" name="workingProvider" id="workingProvider" value="" />
                <input type="hidden" name="workingType" id="workingType" value="" />
                <input type="hidden" name="workingCat" id="workingCat" value="" />
                <input type="hidden" name="workingSubCat" id="workingSubCat" value="" />
                <input type="hidden" name="workingCatLvl" id="workingCatLvl" value="" />
                <input type="hidden" name="workingWebCat" id="workingWebCat" value="" />
                <input type="hidden" name="workingRating" id="workingRating" value="" />
                <!--<input type="hidden" name="workingShortDesc" id="workingShortDesc" value="" />
                <input type="hidden" name="workingLongDesc" id="workingLongDesc" value="" />
                <input type="hidden" name="workingKeywords" id="workingKeywords" value="" />-->
                <div id="optionstabs">
                    <ul class="tabs">
                        <li><a id="t1" href="#cont-tipos">1- Tipo y Proveedor</a></li>
                        <li><a id="t2" href="#cont-cats">2- Categorias</a></li>
                        <li><a id="t3" href="#cont-migcat">3- Nivel y Website Category</a></li>
                        <li><a id="t4" href="#cont-infoadicional">4- Rating por edad</a></li>
                        <li><a id="t5" href="#cont-ids">5- Contenidos</a></li>
                    </ul>
                    <!-- tab "panes" -->
                    <div class="panes">
                        <div id="get-tipos">
                            <noscript><a name="cont-tipos"></a></noscript>
                            <h2>Paso 1/5: Seleccione el Tipo de Contenido y proveedor.</h2><br/>
			    Debe seleccionar obligatoriamente el tipo de contenido que va a procesar. 
                            <p>Tipo:
                                <select id="contentType" name="tipo">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($nombre_tipos as $key => $value) {
                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></p>
                            <p>Proveedor:
                                <select id="provider" name="provider" onchange="gotoStep2();">
                                    <option value="">Cualquiera</option>
                                    <?php
                                    foreach ($providers as $key => $value) {
                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></p>
                            <p><a href="#" class="big-button" onClick="return gotoStep2();">Siguiente >></a></p>
                        </div>
                        <div id="get-cats">
                            <noscript><a name="cont-cats"></a></noscript>
                            <h2>Paso 2/5: Seleccione la Categoria y Sub-Categoria</h2><br/>
			    Ambas selecciones son obligatorias.
                            <div id='wpCat'><h3>Categoria Wallpapers:</h3>
                                <select name="catmigwp" id="catmigwp">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_wallpapers as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>
                            <div id='wpSubCat'><h3>Sub-Categoria Wallpapers:</h3>
                                <select name="subcatmigwp" id="subcatmigwp" onchange="gotoStep3();">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($sub_categorias_wallpapers as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <div id='rtCat'><h3>Categoria Realtones:</h3>
                                <select name="catmigrt" id="catmigrt">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_realtones as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <div id='rtSubCat'><h3>Sub-Categoria Realtones:</h3>
                                <select name="subcatmigrt" id="subcatmigrt" onchange="gotoStep3();">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($sub_categorias_realtones as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <div id='ptCat'><h3>Categoria Polytones:</h3>
                                <select name="catmigpt" id="catmigpt">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_polytones as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

				<div id='ptSubCat'><h3>Sub-Categoria Polytones:</h3>
                                <select name="subcatmigpt" id="subcatmigpt" onchange="gotoStep3();">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($sub_categorias_polytones as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <div id='vdCat'><h3>Categoria Videos:</h3>
                                <select name="catmigvd" id="catmigvd">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_videos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>
                            <div id='vdSubCat'><h3>Sub-Categoria Videos:</h3>
                                <select name="subcatmigvd" id="subcatmigvd" onchange="gotoStep3();">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($sub_categorias_videos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <div id='ssCat'><h3>Categoria Screensavers:</h3>
                                <select name="catmigss" id="catmigss" onchange="gotoStep3();">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_screensaver as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <div id='ftCat'><h3>Categoria Fulltracks:</h3>
                                <select name="catmigft" id="catmigft" onchange="gotoStep3();">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_fulltrack as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <div id='jgCat'><h3>Categoria Juegos/Apps:</h3>
                                <select name="catmigjg" id="catmigjg">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_juegos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>
                            <div id='jgSubCat'><h3>Sub-Categoria JavaGames:</h3>
                                <select name="subcatmigjg" id="subcatmigjg" onchange="gotoStep3();">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($sub_categorias_juegos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>


                            <div id='thCat'><h3>Categoria Themes:</h3>
                                <select name="catmigth" id="catmigth" onchange="gotoStep3();">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_themes as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <p><a href="#" class="big-button" onClick="return gotoStep3();">Siguiente >></a></p>
                        </div>
                        <div id="get-migcat">
                            <noscript><a name="cont-migcat"></a></noscript>
                            <h2>Paso 3/5: Seleccione las opciones de Category Level y Website Category</h2><br/>
			    Ambas selecciones son obligatorias.
                            <div id='catLvlDiv'><h3>Category Level:</h3>
                                <select name="catLvl" id="catLvl">
                                    <option value="">Seleccionar uno...</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Silver">Silver</option>
                                    <option value="Bronze">Bronze</option>
                                </select></div>

                            <div id='webCatDiv_vd'><h3>Website Category:</h3>
                                <select name="webCat_vd" id="webCat_vd" onchange="gotoStep4();">
                                    <option value="">Seleccionar uno...</option>
                                    <option value="Ideas Video">Ideas Video</option>
                                    <option value="Videos Musicales">Videos Musicales</option>
                                    <option value="Videoclips">Videoclips</option>
                                    <option value="Lo más Sexy">Lo m&aacute;s Sexy</option>
                                </select></div>
			    <div id='webCatDiv_wp'><h3>Website Category:</h3>
                                <select name="webCat_wp" id="webCat_wp" onchange="gotoStep4();">
                                    <option value="">Seleccionar uno...</option>
				    <option value="Animadas">Animadas</option>
				    <option value="A Color">A Color</option>
                                    <option value="Comics MMS">Comics MMS</option>
                                    <option value="Postales MMS">Postales MMS</option>
                                    <option value="Deportivas">Deportivas</option>
                                    <option value="Lo más Sexy">Lo m&aacute;s Sexy</option>
                                </select></div>
                             <div id='webCatDiv_rt'><h3>Website Category:</h3>
                                <select name="webCat_rt" id="webCat_rt" onchange="gotoStep4();">
                                    <option value="">Seleccionar uno...</option>
				    <option value="Artistonos">Artistonos</option>
				    <option value="Evolutonos">Evolutonos</option>
                                    <option value="Sonidos Especiales">Sonidos Especiales</option>
                                    <option value="Tonos Premium">Tonos Premium</option>
                                    <option value="Tonos Reales">Tonos Reales</option>
                                    <option value="Tonos SMS">Tonos SMS</option>
                                    <option value="Tonos Ultrasónikos">Tonos Ultrasónikos</option>
                                    <option value="Deportivos">Deportivos</option>
                                    <option value="Lo más Sexy">Lo m&aacute;s Sexy</option>
                                </select></div>
                             <div id='webCatDiv_jg'><h3>Website Category:</h3>
                                <select name="webCat_jg" id="webCat_jg" onchange="gotoStep4();">
                                    <option value="">Seleccionar uno...</option>
				    <option value="Básicos">Básicos</option>
				    <option value="Juegos Premium">Juegos Premium</option>
                                    <option value="Juegos y Test Para Ellas">Juegos y Test Para Ellas</option>
                                    <option value="Juegos 3D">Juegos 3D</option>
                                    <option value="Lo más Sexy">Lo más Sexy</option>
                                    <option value="Touch and Sensor">Touch and Sensor</option>
                                </select></div>

				<div id='webCatDiv_pt'><h3>Website Category:</h3>
                                <select name="webCat_pt" id="webCat_pt" onchange="gotoStep4();">
                                    <option value="">Seleccionar uno...</option>
				    <option value="Artistonos">Artistonos</option>
				    <option value="Evolutonos">Evolutonos</option>
                                    <option value="Sonidos Especiales">Sonidos Especiales</option>
                                    <option value="Tonos Premium">Tonos Premium</option>
                                    <option value="Tonos Reales">Tonos Reales</option>
                                    <option value="Tonos SMS">Tonos SMS</option>
                                    <option value="Tonos Ultrasónikos">Tonos Ultrasónikos</option>
                                    <option value="Deportivos">Deportivos</option>
                                    <option value="Lo más Sexy">Lo m&aacute;s Sexy</option>
                                </select></div>
                            <p><a href="#" class="big-button" onClick="return gotoStep4();">Siguiente >></a></p>
                        </div>

                        <div id="get-infoadicional">
                            <noscript><a name="cont-infoadicional"></a></noscript>
                            <h2>Paso 4/5: Seleccione el rating de edad.</h2><br/>
			    Los numeros representan las edades para las cuales el contenido debe ser ofrecido. Para contenidos adultos indicar 18+.
                             <div id='ratingTitle'><h3>Rating:</h3>
                                 <select name="rating" id="rating" onchange="gotoStep5()">
                                    <option value="">Seleccionar uno...</option>
				    <option value="3+">3+</option>
				    <option value="7+">7+</option>
                                    <option value="12+">12+</option>
                                    <option value="16+">16+</option>
                                    <option value="18+">18+</option>
                                </select></div>
                           <!-- <h3>Keywords:</h3>
                            <textarea name="keywords" id="keywords"></textarea>
                            <h3>Short Description:</h3>
                            <textarea name="shortDesc" id="shortDesc"></textarea>
                            <h3>Long Description:</h3>
                            <textarea name="longDesc" id="longDesc"></textarea> -->
                            <p><a href="#" class="big-button" onClick="gotoStep5();">Siguiente >></a></p>
                        </div>

                        <div id="get-ids">
                            <noscript><a name="cont-ids"></a></noscript>
                            <h2>Paso 5/5: Seleccione los Contenidos...</h2><br/>
                            <h3>IDs:</h3>
                            <textarea name="ids" id="ids"></textarea>
                            <h3>Categorias:</h3>
                            <textarea name="cats" id="cats"></textarea>
                            <h3>Rango de IDs contenidos:</h3>
                            <p>Del <input type="text" name="rango_i" id="rango_i" /> al <input type="text" name="rango_f" id="rango_f" /></p>

                            <!--
                            <table cellpadding="0" cellspacing="0" style="border: 0px !important" >
                                <tr>
                                    <td>Id</td>
                                    <!--<td>keywords (separadas por espacio)</td> 
                                    <td>Short Description</td>
                                    <td>Long Description</td>
                                </tr>
                                <?php for($i = 1; $i <= 10; $i++) {?>
                                <tr>
                                    <td> <input type="text" name="id_<?= $i ?>" id="id_<?= $i ?>" /></td>
                                   <!-- <td> <input type="text" name="keywords_<?= $i ?>" id="keywords_<?= $i ?>" /></td> 
                                    <td><textarea name="shortDesc_<?= $i ?>" id="shortDesc_<?= $i ?>"></textarea></td>
                                    <td><textarea name="longDesc_<?= $i ?>" id="longDesc_<?= $i ?>"></textarea></td>
                                </tr>
                                <?php } ?>
                            </table>
                            -->
                            <p><a href="#" class="big-button" onClick="return setAction('xml');">Generar XML</a></p>
                        </div>
                    </div>
                </div>

            </form>

            <div id="working-on">

            </div>

            <?php
            if ($_POST["jiden"] == "se") include_once("pre-process.php");
            ?>

        </div>

    </body>
</html>
