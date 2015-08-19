<?php

include_once($globalIncludeDir."/prt.php");

$step2 = "";

$nombre_tipos = array(
   "PT" => "Polytone",
   "RT" => "Realtone",
   "VD" => "Video",
   "WP" => "Wallpaper",
   "SS" => "Screensaver",
   "FT" => "Fulltrack",
//   "TH" => "Theme",
   "JG" => "Game/App",
);

$categorias_wallpapers = array(
"animales", "aviones", "deportes", "zodiaco", "paisajes", "amor", "autos", "humor", "famosos", "cineytv", "arteysimbologia", "banderas", "espacio", "smilesypins", "cdcovers", "caricaturas", "fantasiaycienciaficcion", "chicassexy", "chicossexy", "pechosycolas", "famosos", "kamasutra"
);

$categorias_realtones = array(
"poplatino", "oldhits", "pop", "rockargentino", "rocknacional", "cumbia", "rock", "hiphop", "reggaeton", "electronica", "rap", "reggae", "crazytones", "voicetones", "murga", "tango", "jazz", "clasica", "tvseriescine", "salsa", "sexytones"
);

$categorias_polytones = array(
"poplatino", "oldhits", "pop", "rockargentino", "rocknacional", "cumbia", "rock", "hiphop", "reggaeton", "electronica", "rap", "reggae", "murga", "tango", "jazz", "clasica", "tvseriescine", "salsa", "sexytones"
);

$categorias_videos = array(
"deportes", "animales", "entretenimiento", "musica", "humor", "cineytv", "infantiles", "animaciones", "kamasutra", "mujeressexy", "bikini", "topless", "desnudos"
);

$categorias_screensaver = array(
"animales", "aviones", "deportes", "zodiaco", "paisajes", "amor", "autos", "humor", "famosos", "cineytv", "arteysimbologia", "banderas", "espacio", "smilesypins", "cdcovers", "caricaturas", "fantasiaycienciaficcion", "chicassexy", "chicossexy", "pechosycolas", "famosos", "kamasutra"
);

$categorias_fulltrack = array(
"poplatino", "oldhits", "pop", "rockargentino", "rocknacional", "cumbia", "rock", "hiphop", "reggaeton", "electronica", "rap", "reggae", "crazytones", "voicetones", "tango", "murga", "jazz", "clasica", "tvseriescine", "salsa", "sexytones"
);

$categorias_juegos = array(
"accion", "aventura", "arcade", "deportes", "logica", "casino", "estrategia", "rpg", "otros", "karaoke"
);

$categorias_themes = array(
"animales", "aviones", "deportes", "zodiaco", "paisajes", "amor", "autos", "humor", "famosos", "cineytv", "arteysimbologia", "banderas", "espacio", "smilesypins", "cdcovers", "caricaturas", "fantasiaycienciaficcion", "chicassexy", "chicossexy", "pechosycolas", "famosos", "kamasutra"
);


?>
<script>
	function setAction(tipo) {
		cat = $('#workingCat').val();
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
		idWc = '#catdrutt'+wc.toLowerCase();
		cat = $(idWc).val();
		if (cat != '') html += '<li>Categoria: '+cat+'</li>';

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
		idWc = '#catdrutt'+wc.toLowerCase();
		cat = $(idWc).val();
		if (wc != '' && cat != '') {
			$('#t3').click();
			setHtml();
		} else {
			alert('Debe seleccionar una Categoria asociada en Drutt.');
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

<div id="titletop"><h1>Generador de archivos XML para Drutt Ancel v1.5</h1></div>

<div id="content">

<form action="" name="filtros" method="post">
	<input type="hidden" name="jiden" value="se" />
	<input type="hidden" name="workingType" id="workingType" value="" />
	<input type="hidden" name="workingCat" id="workingCat" value="" />
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

				<div id='wpCat'><h3>Categoria Drutt Wallpapers:</h3>
				<select name="catdruttwp" id="catdruttwp" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_wallpapers as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='rtCat'><h3>Categoria Drutt Realtones:</h3>
				<select name="catdruttrt" id="catdruttrt" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_realtones as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='ptCat'><h3>Categoria Drutt Polytones:</h3>
				<select name="catdruttpt" id="catdruttpt" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_polytones as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='vdCat'><h3>Categoria Drutt Videos:</h3>
				<select name="catdruttvd" id="catdruttvd" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_videos as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='ssCat'><h3>Categoria Drutt Screensavers:</h3>
				<select name="catdruttss" id="catdruttss" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_screensaver as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='ftCat'><h3>Categoria Drutt Fulltracks:</h3>
				<select name="catdruttft" id="catdruttft" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_fulltrack as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='jgCat'><h3>Categoria Drutt Juegos/Apps:</h3>
				<select name="catdruttjg" id="catdruttjg" onchange="gotoStep3();">
					<option value="">Seleccionar uno...</option>
					<?php
						foreach ($categorias_juegos as $key => $value) {
						echo '<option value="'.$value.'">'.$value.'</option>';
						}
					?>
				</select></div>

				<div id='thCat'><h3>Categoria Drutt Themes:</h3>
				<select name="catdruttth" id="catdruttth" onchange="gotoStep3();">
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
if ($_POST["jiden"] == "se") include_once($globalIncludeDir."/pre-process.php");
?>

</div>
