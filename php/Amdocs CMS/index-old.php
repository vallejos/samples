<?php

include_once("prt.php");

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

$categorias_wallpapers = array(
"Amor y Amistad","Animales y Naturaleza","Automovilismo","Deportes","Chicas sexy","Chicos sexy","Deportes extremos","Electrónica","Futbol Internacional","Futbol Nacional","Grupero / Norteño","Infantiles","Lucha libre","Festivas","Peliculas","Pop","Ranchero / Regional","Reggaeton","Rock","Salsa / Cumbia / Tropical","TV","Personalizables","Zona Playboy","+ideas imágenes"
);

$sub_categorias_wallpapers = array(
"1ra Division","2da Division","Accion y Aventura","Acuaticos","Aereos","Americano","Amistad","Amor","Año Nuevo","Banda","Basquetball","Beisbol","Bosque","Caricaturas","Ciencia y Ficción","Cumbia","Cumpleaños","Deportivas","Disco","Domesticos","Drama","Duranguense","Europeo","Femenino","Formula 1","Golf","Grupero","Grupos","Halloween","Hijos","House","Independencia","Infantil","Internacional","Lenceria","Mar","Masculino","Musicales","Nacional","Nascar","Navidad","New age","Norteño","Novelas","Otros","Padres","Progressive","Ranchero","Regional","Salsa","Salvajes","Selección Mexicana","Selecciones","Selva","Series","Sin camisa","Techno","Terrestres","Terror/Suspenso","Traje de baño","Tropical","Trova","T-shirt","Underwear",
);

$categorias_realtones = array(
"poplatino", "oldhits", "pop", "rockargentino", "rocknacional", "cumbia", "rock", "hiphop", "reggaeton", "electronica", "rap", "reggae", "crazytones", "voicetones", "murga", "tango", "jazz", "clasica", "tvseriescine", "salsa", "sexytones"
);

$categorias_polytones = array(
"poplatino", "oldhits", "pop", "rockargentino", "rocknacional", "cumbia", "rock", "hiphop", "reggaeton", "electronica", "rap", "reggae", "murga", "tango", "jazz", "clasica", "tvseriescine", "salsa", "sexytones"
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
<title>Generador XML MIG America Movil v1.0</title>
<script>
	function setAction(tipo) {
		cat = $('#workingCat').val();
		subcat = $('#workingSubCat').val();
		tipo = $('#workingType').val();
		prt = $('#workingPrt').val();

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

		if (cat=='' || tipo=='' || prt=='' || cont==false) {
			alert('Debe completar todos los datos para pdoer generar el XML.\nRevise el Setup Inicial.');
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
/*
		ant = $('#step2').html();
		anterior = "<h3>Resultado Anterior...</h3>"+ant;
		if (ant != '') $('#step2').html(anterior);
*/

		html = '<h3>1/2 Setup Inicial...</h3><ul>';

		tipo = $('#workingCat').val();
		if (tipo != '') html += '<li>Tipo: '+getTipo(tipo)+'</li>';

		wc = $('#contentType').val();
		idWc = '#catmig'+wc.toLowerCase();
		cat = $(idWc).val();
		if (cat != '') html += '<li>Categoria: '+cat+'</li>';

		idWsc = '#subcatmig'+wc.toLowerCase();
		subcat = $(idWsc).val();
		if (subcat != '') html += '<li>Sub-categoria: '+subcat+'</li>';

		prt = $('#workingPrt').val();
		if (prt != '') html += '<li>Premium Resource Type: '+prt+'</li>';

		html += '</ul>';
		$('#working-on').html(html);
	}
	function gotoStep2() {
		$('#workingPrt').val('');
		tipo = $('#contentType').val();
		idTipo = '#'+tipo.toLowerCase()+'Cat';
		idStyle = $(idTipo).css('display');
		viewHiddenCat(tipo);
		viewHiddenSubCat(tipo);
		viewHiddenPrt(tipo)
		if (tipo != '') {
			$('#t2').click();
			$('#workingCat').val(tipo);
			$('#workingType').val(tipo);
			setHtml();
		} else {
			alert('Debe seleccionar un Tipo de contenido.');
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
	<input type="hidden" name="workingType" id="workingType" value="" />
	<input type="hidden" name="workingCat" id="workingCat" value="" />
	<input type="hidden" name="workingSubCat" id="workingSubCat" value="" />
	<input type="hidden" name="workingPrt" id="workingPrt" value="" />
	<div id="optionstabs">
		<ul class="tabs">
			<li><a id="t1" href="#cont-tipos">1- Tipo</a></li>
			<li><a id="t2" href="#cont-cats">2- Categorias</a></li>
			<li><a id="t3" href="#cont-prt">3- Precio/Proveedor</a></li>
			<li><a id="t4" href="#cont-ids">4- Contenidos</a></li>
		</ul>
		<!-- tab "panes" -->
		<div class="panes">
			<div id="get-tipos">
				<noscript><a name="cont-tipos"><hr></a></noscript>
				<h2>Paso 1/4: Seleccione el Tipo de Contenido...</h2><br/>
				<p>Tipo:
				<select id="contentType" name="tipo" onchange="gotoStep2();">
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
				<h2>Paso 2/4: Seleccione la Categoria...</h2><br/>

				<div id='wpCat'><h3>Categoria Mig Wallpapers:</h3>
				<select name="catmigwp" id="catmigwp">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_wallpapers as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>
				<div id='wpSubCat'><h3>Sub-Categoria Mig Wallpapers:</h3>
				<select name="subcatmigwp" id="subcatmigwp" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($sub_categorias_wallpapers as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='rtCat'><h3>Categoria Mig Realtones:</h3>
				<select name="catmigrt" id="catmigrt" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_realtones as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='ptCat'><h3>Categoria Mig Polytones:</h3>
				<select name="catmigpt" id="catmigpt" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_polytones as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='vdCat'><h3>Categoria Mig Videos:</h3>
				<select name="catmigvd" id="catmigvd" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_videos as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>
				<div id='wpSubCat'><h3>Sub-Categoria Mig Videos:</h3>
				<select name="subcatmigvd" id="subcatmigvd" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($sub_categorias_videos as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='ssCat'><h3>Categoria Mig Screensavers:</h3>
				<select name="catmigss" id="catmigss" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_screensaver as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='ftCat'><h3>Categoria Mig Fulltracks:</h3>
				<select name="catmigft" id="catmigft" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_fulltrack as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='jgCat'><h3>Categoria Mig Juegos/Apps:</h3>
				<select name="catmigjg" id="catmigjg">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_juegos as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>
				<div id='jgSubCat'><h3>Sub-Categoria Mig JavaGames:</h3>
				<select name="subcatmigjg" id="subcatmigjg" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($sub_categorias_juegos as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>


				<div id='thCat'><h3>Categoria Mig Themes:</h3>
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
			<div id="get-prt">
				<noscript><a name="cont-prt"><hr></a></noscript>
				<h2>Paso 3/4: Seleccione las opciones de Precio y Proveedor...</h2><br/>
				<p>Proveedor:
				<select name="prov" id="prov">
					<option value="wazzup">Wazzup</option>
				</select></p>

				<div id='wpPrt'><h3>PRT Wallpapers:</h3>
				<select name="prtwp" id="prtwp" onchange="gotoStep4();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($prt_WP as $key => $value) {
						echo '<option value="'.$key.'">'.$value.'</option>';
						}
					?>
				</select></div>
				<div id='ptPrt'><h3>PRT Polytones:</h3>
				<select name="prtpt" id="prtpt" onchange="gotoStep4();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($prt_PT as $key => $value) {
						echo '<option value="'.$key.'">'.$value.'</option>';
						}
					?>
				</select></div>
				<div id='ssPrt'><h3>PRT Screensavers:</h3>
				<select name="prtss" id="prtss" onchange="gotoStep4();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($prt_SS as $key => $value) {
						echo '<option value="'.$key.'">'.$value.'</option>';
						}
					?>
				</select></div>
				<div id='rtPrt'><h3>PRT Realtones:</h3>
				<select name="prtrt" id="prtrt" onchange="gotoStep4();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($prt_RT as $key => $value) {
						echo '<option value="'.$key.'">'.$value.'</option>';
						}
					?>
				</select></div>
				<div id='jgPrt'><h3>PRT Games:</h3>
				<select name="prtjg" id="prtjg" onchange="gotoStep4();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($prt_JG as $key => $value) {
						echo '<option value="'.$key.'">'.$value.'</option>';
						}
					?>
				</select></div>
				<div id='vdPrt'><h3>PRT Videos:</h3>
				<select name="prtvd" id="prtvd" onchange="gotoStep4();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($prt_VD as $key => $value) {
						echo '<option value="'.$key.'">'.$value.'</option>';
						}
					?>
				</select></div>
				<div id='ftPrt'><h3>PRT Fulltracks:</h3>
				<select name="prtft" id="prtft" onchange="gotoStep4();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($prt_FT as $key => $value) {
						echo '<option value="'.$key.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<p><a href="#" class="big-button" onClick="return gotoStep4();">Siguiente >></a></p>
			</div>
			<div id="get-ids">
				<noscript><a name="cont-ids"><hr></a></noscript>
				<h2>Paso 4/4: Seleccione los Contenidos...</h2><br/>
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
if ($_POST["jiden"] == "se") include_once("pre-process.php");
?>

</div>

</body>
</html>
