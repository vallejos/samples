<?php

$step2 = "";

$nombre_tipos = array(
   "RT" => "Truetones",
   "FT" => "Fulltracks",
   "VD" => "Videos",
   "WP" => "Wallpapers",
   "JG" => "Games",
//   "TH" => "Theme",
);


$sub_categorias_truetones = array(
        "131" => "Universal Music (Adult)",
        "109" => "Universal Music (Alternative)",
        "138" => "Universal Music (B)",
        "99" => "Universal Music (Blues)",
        "30" => "Tones (Bollywood)",
        "119" => "Universal Music (Children's)",
        "130" => "Universal Music (Christian)",
        "116" => "Universal Music (Classical)",
        "115" => "Universal Music (Comedy)",
        "105" => "Universal Music (Country)",
        "133" => "Universal Music (Cristianas)",
        "139" => "Cristiano (Cristiano)",
        "33" => "Kchiporros (Cumbia)",
        "108" => "Universal Music (Dance)",
        "120" => "Universal Music (Easy Listening)",
        "125" => "Universal Music (Electronic)",
        "135" => "Universal Music (Fado)",
        "35" => "Carnaval (Festivos)",
        "37" => "Halloween (Festivos)",
        "36" => "Navidad (Festivos)",
        "38" => "San Valentin (Festivos)",
        "111" => "Universal Music (Flamenco)",
        "128" => "Universal Music (Folk)",
        "121" => "Universal Music (Gospel)",
        "107" => "Universal Music (Hip Hop)",
        "119" => "Universal Music (Holiday)",
        "124" => "Universal Music (Indian Classical)",
        "118" => "Universal Music (Inspirational)",
        "136" => "Universal Music (Instrumental)",
        "137" => "Clasico (Jazz)",
        "102" => "Universal Music (Jazz)",
        "110" => "Universal Music (Latin)",
        "127" => "Universal Music (Metal)",
        "65" => "Alternativos (Musicas Completas)",
        "95" => "Bachata (Musicas Completas)",
        "66" => "Blues (Musicas Completas)",
        "67" => "Brasileros (Musicas Completas)",
        "68" => "Cachaca (Musicas Completas)",
        "69" => "Clasicos (Musicas Completas)",
        "71" => "Country (Musicas Completas)",
        "72" => "Cristianos (Musicas Completas)",
        "73" => "Cumbia (Musicas Completas)",
        "74" => "Dance (Musicas Completas)",
        "76" => "Electronica (Musicas Completas)",
        "79" => "Hip Hop (Musicas Completas)",
        "80" => "Infantiles (Musicas Completas)",
        "81" => "Jazz (Musicas Completas)",
        "82" => "Juegos (Musicas Completas)",
        "83" => "Latinas (Musicas Completas)",
        "94" => "Merengue (Musicas Completas)",
        "96" => "Musicas Paraguayas (Musicas Completas)",
        "84" => "Pop (Musicas Completas)",
        "85" => "Reggae (Musicas Completas)",
        "86" => "Reggaeton (Musicas Completas)",
        "87" => "Rock (Musicas Completas)",
        "88" => "Rock Argentino (Musicas Completas)",
        "89" => "Rock Uruguayo (Musicas Completas)",
        "90" => "Romanticos (Musicas Completas)",
        "93" => "Salsa (Musicas Completas)",
        "92" => "Tangos (Musicas Completas)",
        "134" => "Universal Music (New Age)",
        "100" => "Universal Music (Pop) ",
        "123" => "Universal Music (Punk) ",
        "114" => "Universal Music (Reggae) ",
        "113" => "Universal Music (Reggaeton) ",
        "103" => "Universal Music (Rock) ",
        "129" => "Universal Music (Schlager) ",
        "122" => "Universal Music (Soul) ",
        "104" => "Universal Music (Soundtrack) ",
        "13" => "Classica (Spanish Tones) ",
        "132" => "Universal Music (Spoken Word) ",
        "126" => "Universal Music (Urban latino) ",
        "101" => "Universal Music (Vocal) ",
        "112" => "Universal Music (World) ",
);


$categorias_truetones = array(
        "269" => "Adult",
        "40" => "Alternative",
        "279" => "B",
        "41" => "Blues",
        "23" => "Bollywood",
        "63" => "Brasileros",
        "61" => "Cachaca",
        "256" => "Children's",
        "268" => "Christian",
        "44" => "Clasicos",
        "255" => "Classical",
        "45" => "Comedy",
        "46" => "Country",
        "43" => "Cristianas",
        "274" => "Cristiano",
        "27" => "Cumbia",
        "47" => "Dance",
        "259" => "Easy Listening",
        "60" => "Efectos Sonido",
        "48" => "Electronic",
        "275" => "Electronica",
        "49" => "Entretenimiento",
        "272" => "Fado",
        "52" => "Festivos",
        "253" => "Flamenco",
        "266" => "Folk",
        "260" => "Gospel",
        "51" => "Hip Hop",
        "276" => "HipHop",
        "258" => "Holiday",
        "263" => "Indian Classical",
        "42" => "Infantiles",
        "257" => "Inspirational",
        "273" => "Instrumental",
        "277" => "Instrumentales",
        "53" => "Jazz",
        "50" => "Juegos",
        "24" => "Latest Bollywood",
        "55" => "Latin",
        "278" => "Latinas",
        "265" => "Metal",
        "246" => "Musicas completas",
        "67" => "Nametonos",
        "271" => "New Age",
        "57" => "Pop",
        "262" => "Punk",
        "58" => "Reggae",
        "62" => "Reggaeton",
        "59" => "Rock",
        "65" => "Rock Argentino",
        "66" => "Rock Uruguayo",
        "64" => "Romanticos",
        "267" => "Schlager",
        "68" => "Sonidos Graciosos",
        "261" => "Soul",
        "251" => "Soundtrack",
        "11" => "Spanish Tones",
        "270" => "Spoken Word",
        "54" => "Tangos",
        "56" => "Tono de mensajes",
        "241" => "Tonos de llamada",
        "264" => "Urban Latino",
        "250" => "Vocal",
        "254" => "World",
);

$sub_categorias_fulltracks = array(
        "131" => "Universal Music (Adult)",
        "109" => "Universal Music (Alternative)",
        "138" => "Universal Music (B)",
        "99" => "Universal Music (Blues)",
        "30" => "Tones (Bollywood)",
        "119" => "Universal Music (Children's)",
        "130" => "Universal Music (Christian)",
        "116" => "Universal Music (Classical)",
        "115" => "Universal Music (Comedy)",
        "105" => "Universal Music (Country)",
        "133" => "Universal Music (Cristianas)",
        "139" => "Cristiano (Cristiano)",
        "33" => "Kchiporros (Cumbia)",
        "108" => "Universal Music (Dance)",
        "120" => "Universal Music (Easy Listening)",
        "125" => "Universal Music (Electronic)",
        "135" => "Universal Music (Fado)",
        "35" => "Carnaval (Festivos)",
        "37" => "Halloween (Festivos)",
        "36" => "Navidad (Festivos)",
        "38" => "San Valentin (Festivos)",
        "111" => "Universal Music (Flamenco)",
        "128" => "Universal Music (Folk)",
        "121" => "Universal Music (Gospel)",
        "107" => "Universal Music (Hip Hop)",
        "119" => "Universal Music (Holiday)",
        "124" => "Universal Music (Indian Classical)",
        "118" => "Universal Music (Inspirational)",
        "136" => "Universal Music (Instrumental)",
        "137" => "Clasico (Jazz)",
        "102" => "Universal Music (Jazz)",
        "110" => "Universal Music (Latin)",
        "127" => "Universal Music (Metal)",
        "65" => "Alternativos (Musicas Completas)",
        "95" => "Bachata (Musicas Completas)",
        "66" => "Blues (Musicas Completas)",
        "67" => "Brasileros (Musicas Completas)",
        "68" => "Cachaca (Musicas Completas)",
        "69" => "Clasicos (Musicas Completas)",
        "71" => "Country (Musicas Completas)",
        "72" => "Cristianos (Musicas Completas)",
        "73" => "Cumbia (Musicas Completas)",
        "74" => "Dance (Musicas Completas)",
        "76" => "Electronica (Musicas Completas)",
        "79" => "Hip Hop (Musicas Completas)",
        "80" => "Infantiles (Musicas Completas)",
        "81" => "Jazz (Musicas Completas)",
        "82" => "Juegos (Musicas Completas)",
        "83" => "Latinas (Musicas Completas)",
        "94" => "Merengue (Musicas Completas)",
        "96" => "Musicas Paraguayas (Musicas Completas)",
        "84" => "Pop (Musicas Completas)",
        "85" => "Reggae (Musicas Completas)",
        "86" => "Reggaeton (Musicas Completas)",
        "87" => "Rock (Musicas Completas)",
        "88" => "Rock Argentino (Musicas Completas)",
        "89" => "Rock Uruguayo (Musicas Completas)",
        "90" => "Romanticos (Musicas Completas)",
        "93" => "Salsa (Musicas Completas)",
        "92" => "Tangos (Musicas Completas)",
        "134" => "Universal Music (New Age)",
        "100" => "Universal Music (Pop) ",
        "123" => "Universal Music (Punk) ",
        "114" => "Universal Music (Reggae) ",
        "113" => "Universal Music (Reggaeton) ",
        "103" => "Universal Music (Rock) ",
        "129" => "Universal Music (Schlager) ",
        "122" => "Universal Music (Soul) ",
        "104" => "Universal Music (Soundtrack) ",
        "13" => "Classica (Spanish Tones) ",
        "132" => "Universal Music (Spoken Word) ",
        "126" => "Universal Music (Urban latino) ",
        "101" => "Universal Music (Vocal) ",
        "112" => "Universal Music (World) ",
);


$categorias_fulltracks = array(
        "269" => "Adult",
        "40" => "Alternative",
        "279" => "B",
        "41" => "Blues",
        "23" => "Bollywood",
        "63" => "Brasileros",
        "61" => "Cachaca",
        "256" => "Children's",
        "268" => "Christian",
        "44" => "Clasicos",
        "255" => "Classical",
        "45" => "Comedy",
        "46" => "Country",
        "43" => "Cristianas",
        "274" => "Cristiano",
        "27" => "Cumbia",
        "47" => "Dance",
        "259" => "Easy Listening",
        "60" => "Efectos Sonido",
        "48" => "Electronic",
        "275" => "Electronica",
        "49" => "Entretenimiento",
        "272" => "Fado",
        "52" => "Festivos",
        "253" => "Flamenco",
        "266" => "Folk",
        "260" => "Gospel",
        "51" => "Hip Hop",
        "276" => "HipHop",
        "258" => "Holiday",
        "263" => "Indian Classical",
        "42" => "Infantiles",
        "257" => "Inspirational",
        "273" => "Instrumental",
        "277" => "Instrumentales",
        "53" => "Jazz",
        "50" => "Juegos",
        "24" => "Latest Bollywood",
        "55" => "Latin",
        "278" => "Latinas",
        "265" => "Metal",
        "246" => "Musicas completas",
        "67" => "Nametonos",
        "271" => "New Age",
        "57" => "Pop",
        "262" => "Punk",
        "58" => "Reggae",
        "62" => "Reggaeton",
        "59" => "Rock",
        "65" => "Rock Argentino",
        "66" => "Rock Uruguayo",
        "64" => "Romanticos",
        "267" => "Schlager",
        "68" => "Sonidos Graciosos",
        "261" => "Soul",
        "251" => "Soundtrack",
        "11" => "Spanish Tones",
        "270" => "Spoken Word",
        "54" => "Tangos",
        "56" => "Tono de mensajes",
        "241" => "Tonos de llamada",
        "264" => "Urban Latino",
        "250" => "Vocal",
        "254" => "World",
);

$categorias_videos = array(
        "28" => "Animaciones",
        "29" => "Autos y Vehiculos",
        "30" => "Comedia",
        "37" => "Deportes",
        "31" => "Entretenimientos",
        "18" => "FunnyVideos",
        "34" => "Gente",
        "35" => "Mascotas y Animales",
        "32" => "Musicas",
        "33" => "Novedades y Politica",
        "26" => "Portal Videos",
        "36" => "Sexy",
        "13" => "Spanish",
        "38" => "Tecnologia",
        "39" => "Tutoriales",
);

$categorias_juegos = array(
        "112" => "3D",
        "96" => "Accion",
        "104" => "Animales",
        "98" => "Aplicaciones",
        "100" => "Arcade",
        "97" => "Aventura",
        "101" => "Casino",
        "99" => "Comics",
        "108" => "Consola",
        "107" => "Deportes",
        "110" => "Estrategia",
        "102" => "Guerra",
        "103" => "Hollywood",
        "111" => "Logica",
        "12" => "Premium Games",
        "105" => "Puzzle",
        "106" => "Racing",
        "109" => "Sexy",
);

$sub_categorias_juegos = array(
        "pg1" => "Accion",
        "pg2" => "Arcade",
        "pg3" => "Aventura",
        "pg4" => "Juego de Roles",
        "pg5" => "Puzzle",
        "pg6" => "Racing",
);

$categorias_wallpapers = array(
        "w1" => "Abstractos",
        "w2" => "Amor",
        "w3" => "Anime",
        "w4" => "Autos",
        "w5" => "Motocicletas",
        "w6" => "Celebridades",
        "w7" => "Deportes",
        "w8" => "Dibujos Animados",
        "w9" => "Disenhos",
        "w10" => "Entretenimiento",
        "w11" => "Cine y TV",
        "w12" => "Festivos",
        "w13" => "Floogers",
        "w14" => "Juegos",
        "w15" => "Mascotas y animales",
        "w16" => "Naturaleza",
        "w17" => "Otros",
        "w18" => "Signos y refranes",
        "w19" => "Signs",
        "w20" => "Tatuajes",
        "w21" => "Tecnologia",
        "w22" => "Tunning",
        "w23" => "Tunning Caricaturas",
);


$sub_categorias_wallpapers = array(
        "w4001" => "Ferrari",
        "-1" => "-",
        "w5001" => "Harley-Davidson",
        "-2" => "-",
        "w11000" => "Clone Wars",
        "w11001" => "Indiana Jones",
        "w11002" => "Star Treck",
        "w11003" => "Super Agente F86",
        "w11004" => "Wall E",
        "w11005" => "Wolverine - X Man",
        "w12000" => "Dia de la Madre",
        "w12001" => "Dia del Padre",
        "w12002" => "Hallowen",
        "w12003" => "Navidad",
        "w12004" => "Pascuas",
        "w12005" => "San Valentin",
        "w12006" => "Semana Santa",
);

?>
<html>
<head>
<title>Generador XML DaVinci Tigo v1.1</title>
<script>
	function setAction(tipo) {
		var cat = $('#workingCat').val();
                var subcat = $('#workingSubCat').val
		var tipo = $('#workingType').val();

		var cont = false;
		var contentId = $('#ids').val();
		var contentCat = $('#cats').val();
		var contentRangeI = $('#rango_i').val();
		var contentRangeF = $('#rango_f').val();

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

		if (cont==false) {
			alert('Debe completar todos los datos para pdoer generar el XML.\nRevise el Setup Inicial.');
			$('#t1').click();
			return false;
		}

                // muestro el loading...
                $('#loading').css('display','block');

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
	function viewHiddenPrt(idT) {
		idPrt = '#'+idT.toLowerCase()+'Prt';
		$('#wpPrt').css('display','none');
		$('#ssPrt').css('display','none');
		$('#thPrt').css('display','none');
		$('#jgPrt').css('display','none');
		$('#ptPrt').css('display','none');
		$('#ftPrt').css('display','none');
		$('#rtPrt').css('display','none');
		$('#vdPrt').css('display','none');
		$(idPrt).css('display','block');
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
		html = '<h3>Setup Inicial...</h3><ul>';

                tipo = $('#workingType').val();
                if (tipo != '') html += '<li>Tipo: '+getTipo(tipo)+'</li>';

                wc = $('#contentType').val();
                idWc = '#catmig'+wc.toLowerCase();
                cat = $(idWc).val();
                if (cat != '') html += '<li>Categoria: '+cat+'</li>';

                idWsc = '#subcatmig'+wc.toLowerCase();
                subcat = $(idWsc).val();
                if (subcat != '') html += '<li>Subcategoria: '+subcat+'</li>';


		html += '</ul>';
		$('#working-on').html(html);
	}
	function gotoStep2() {
		$('#workingPrt').val('');
		tipo = $('#contentType').val();
		idTipo = '#'+tipo.toLowerCase()+'Cat';
		idStyle = $(idTipo).css('display');
		viewHiddenCat(tipo);
                if (tipo.toLowerCase() == 'wp') {
                    viewHiddenSubCat(tipo);
                } else if (tipo.toLowerCase() == 'rt') {
                    viewHiddenSubCat(tipo);
                } else if (tipo.toLowerCase() == 'ft') {
                    viewHiddenSubCat(tipo);
                }

		viewHiddenPrt(tipo)
		if (tipo != '') {
                    $('#workingType').val(tipo);
			$('#t2').click();
			setHtml();
		} else {
			alert('Debe seleccionar un Tipo de contenido.');
			return false;
		}
	}
	function gotoStep3() {
		wc = $('#contentType').val();
		idWc = '#catmig'+wc.toLowerCase();
                idSubCat = '#subcatmig'+wc.toLowerCase();

		cat = $(idWc).val();
                subcat = $(idSubCat).val();
		if (wc != '' && cat != '') {
                        $('#workingCat').val(cat);
                        $('#workingSubCat').val(subcat);
			$('#t3').click();
			setHtml();
		} else {
			alert('Debe seleccionar una Categoria Tigo.');
			return false;
		}
	}
	function gotoStep4() {
		tipo = $('#contentType').val();
		idPrt = '#prt'+tipo.toLowerCase();
		prt = $(idPrt).val();
		if (prt != '') {
			$('#t4').click();
			$('#workingPrt').val(prt);
			setHtml();
		} else {
			alert('Seleccione el Premium Resource Type a asignar.');
			return false;
		}
	}
</script>
<!--<link rel="stylesheet" type="text/css" media="all" href="css/fedora-styles.css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.expander.js"></script>
<script type="text/javascript" src="js/jquery.tools.min.js"></script>
<script type="text/javascript" src="js/init.js"></script>-->
</head>
<body>
<div id="titletop"><h1>Generador de archivos XML para DaVinci Tigo</h1></div>

<div id="content">

<form action="" name="filtros" method="post">
	<input type="hidden" name="jiden" value="se" />
	<input type="hidden" name="workingType" id="workingType" value="" />
	<input type="hidden" name="workingCat" id="workingCat" value="" />
        <input type="hidden" name="workingSubCat" id="workingSubCat" value="" />
	<div id="optionstabs">
		<ul class="tabs">
			<li><a id="t1" href="#cont-tipos">1- Tipo</a></li>
			<li><a id="t2" href="#cont-cats">2- Categorias</a></li>
			<li><a id="t3" href="#cont-ids">3- Contenidos</a></li>
		</ul>
		<!-- tab "panes" -->
		<div class="panes">
			<div id="get-tipos">
				<noscript><a name="cont-tipos"><hr></a></noscript>
				<h2>Paso 1/3: Seleccione el Tipo de Contenido...</h2><br/>
				<p>Tipo:
				<select id="contentType" name="tipo">
					<option value="">Seleccionar uno...</option>
					<?php
					foreach ($nombre_tipos as $key => $value) {
        					echo '<option value="'.$key.'">'.$value.'</option>';
					}
					?>
				</select></p>

				<p><a href="#" class="big-button" onClick="return gotoStep2();">Siguiente >></a></p>
			</div>
			<div id="get-cats">
				<noscript><a name="cont-cats"><hr></a></noscript>
				<h2>Paso 2/3: Seleccione la Categoria Tigo...</h2><br/>
				<p>Categoria:
                            <div id='wpCat'><h3>Categoria Wallpapers:</h3>
                                <select name="catmigwp" id="catmigwp" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_wallpapers as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>
                            <div id='wpSubCat'><h3>Sub-Categoria Wallpapers:</h3>
                                Solo para las categorias que permiten Subcategorias (Autos,Motocicletas, Cine y TV).<br/>
                                <select name="subcatmigwp" id="subcatmigwp" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($sub_categorias_wallpapers as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <div id='rtCat'><h3>Categoria Realtones:</h3>
                                <select name="catmigrt" id="catmigrt" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_truetones as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>
                            <div id='rtSubCat'><h3>Sub-Categoria Truetones:</h3>
                                Solo para categorias que permiten Subcategoría. La categoría padre se muestra entre paréntesis<br/>
                                <select name="subcatmigrt" id="subcatmigrt" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($sub_categorias_truetones as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <div id='ftCat'><h3>Categoria Fulltracks:</h3>
                                <select name="catmigft" id="catmigft" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_fulltracks as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>
                            <div id='ftSubCat'><h3>Sub-Categoria Fulltracks:</h3>
                                Solo para categorias que permiten Subcategoría. La categoría padre se muestra entre paréntesis<br/>
                                <select name="subcatmigft" id="subcatmigft" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($sub_categorias_fulltracks as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <div id='ptCat'><h3>Categoria Polytones:</h3>
                                <select name="catmigpt" id="catmigpt" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_polytones as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>
                            <div id='vdCat'><h3>Categoria Videos:</h3>
                                <select name="catmigvd" id="catmigvd" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_videos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>
                            <div id='ssCat'><h3>Categoria Screensavers:</h3>
                                <select name="catmigss" id="catmigss" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_screensaver as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>
                            <div id='ftCat'><h3>Categoria Fulltracks:</h3>
                                <select name="catmigft" id="catmigft" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_fulltrack as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>
                            <div id='jgCat'><h3>Categoria Juegos/Apps:</h3>
                                <select name="catmigjg" id="catmigjg" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_juegos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>
                            <div id='jgSubCat'><h3>Sub-Categoria Juegos/Apps:</h3>
                                Solo para las categorias que permiten Subcategorias (Premium Games)<br/>
                                <select name="subcatmigjg" id="subcatmigjg" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($sub_categorias_juegos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <div id='thCat'><h3>Categoria Themes:</h3>
                                <select name="catmigth" id="catmigth" >
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_themes as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>


				<p><a href="#" class="big-button" onClick="return gotoStep3();">Siguiente >></a></p>
			</div>
			<div id="get-ids">
				<noscript><a name="cont-ids"><hr></a></noscript>
				<h2>Paso 3/3: Seleccione los Contenidos...</h2><br/>
				<h3>IDs:</h3>
				<textarea name="ids" id="ids"></textarea>
				<h3>Categorias:</h3>
				<textarea name="cats" id="cats"></textarea>
				<h3>Rango de IDs contenidos:</h3>
				<p>Del <input type="text" name="rango_i" id="rango_i" /> al <input type="text" name="rango_f" id="rango_f" /></p>

				<p><a href="#" class="big-button" onClick="return setAction('xml');">Generar XML</a></p>
			</div>
		</div>
	</div>

</form>

<div id="working-on">

</div>

<?php
if ($_POST["jiden"] == "se") include_once($globalIncludeDir."/pre-process.php");
?>

</div>

</body>
</html>
