<?php
//error_reporting(E_ERROR);
ini_set('display_errors', '1');

include_once($globalIncludeDir."/includes.php");

$dbc = new conexion("Web");
//include_once("prt.php");

$step2 = "";

$nombre_tipos = array(
    "PT" => "Polytone",
    "RT" => "Realtone",
    "VD" => "Video",
    "WP" => "Wallpaper",
//    "SS" => "Screensaver",
//    "FT" => "Fulltrack",
//    "TH" => "Theme",
    "JG" => "Game/App",
);

$cssActivoNew = "";
$cssActivoUpdate = "";
$cssActivoJamaica = "";

$tipocarga = (isset($_GET["tipocarga"])) ? $_GET["tipocarga"] : $_POST["workingTipoCarga"];
if (empty($tipocarga)) $tipocarga = "new";
if ($tipocarga == "update") {
    $cssActivoUpdate = "class='activo'";
    $mensajeTipoCarga = "La actualización es para cargar contenido en nuevos mercados y/o modificar datos de contenidos ya ingestados.";
} else if ($tipocarga == "jamaica") {
    $cssActivoJamaica = "class='activo'";
    $mensajeTipoCarga = "El Update Jamaica es para cargar contenido en inglés para Jamaica ya ingestado previamente.";
} else {
    $cssActivoNew = "class='activo'";
    $mensajeTipoCarga = "Se cargará nuevo contenido.";
}

$sql = "SELECT * FROM Web.contenidos_proveedores WHERE americaMovil = 1 order by id asc";
$rs = mysql_query($sql, $dbc->db) or die(mysql_error());
$providers = array();
while($row = mysql_fetch_object($rs)){
    $providers[$row->id] = $row->nombre;
}


if ($tipocarga == "update") include_once($globalIncludeDir."/index-update.php");
else {
?>
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

                // muestro el loading...
                $('#loading').css('display','block');

		document.filtros.action = 'index' + ".php";
		document.filtros.submit();
	}
        function toggleMerchant(c) {
            var lang = $('#idioma'+c).is(':checked');
            var merch = $('#pais'+c).is(':checked');

            if (lang === true) {
                $('#flag'+c).css('display','block');
                $('#pais'+c).attr('checked', true);
            } else {
                $('#flag'+c).css('display','none');
                $('#pais'+c).attr('checked', false);
            }
        }
        function toggleLang(c) {
            var lang = $('#idioma'+c).is(':checked');
            var merch = $('#pais'+c).is(':checked');

            if (merch === true) {
                $('#flag'+c).css('display','block');
                $('#idioma'+c).attr('checked', true);
            } else {
                $('#flag'+c).css('display','none');
                $('#idioma'+c).attr('checked', false);
            }
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

		tipocarga = $('#tipocarga').val();
		if (tipocarga!= '') html += '<li>Tipo Carga: '+tipocarga+'</li>';

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

//                tipocarga = $('#tipocarga').val();

                if (tipo == "VD") $('#divTipoVideo').css('display','block');

		if (tipo != '') {
			$('#workingCat').val(tipo);
			$('#workingType').val(tipo);
			$('#workingProvider').val(provider);
//                        $('#workingTipoCarga').val(tipocarga);
			$('#t2').click();
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
			$('#workingCatLvl').val(catLvl);
			$('#workingWebCat').val(webCat);
			$('#t4').click();
			setHtml();
		} else {
			alert('Debe seleccionar el Category Level y Website Category (ambos)');
			return false;
		}
	}
	function gotoStep5() {
		rating = $('#rating').val();
                festivo = $('#festivo').val();
                marca = $('#marca').val();
                tipoVideo = $('#tipovideo').val();
		$('#opcionesDiv5').css('display','block');

		if (rating != '') {
                        $('#workingFestivo').val(festivo);
                        $('#workingMarca').val(marca);
			$('#workingRating').val(rating);
                        $('#workingTipoVideo').val(tipoVideo);
			$('#t5').click();
			setHtml();
		} else {
			alert('Debe seleccionar un rating');
			return false;
		}
	}
</script>
<!--<script type="text/javascript" src="amdocs/js/jquery.js"></script>-->
<!--<script type="text/javascript" src="amdocs/js/jquery.expander.js"></script>-->
<!--<script type="text/javascript" src="amdocs/js/jquery.tools.min.js"></script>-->
<!--<script type="text/javascript" src="amdocs/js/init.js"></script>-->

        <div id="titletop"><h1>Generador de archivos XML para Ideas America Movil</h1></div>

        <div id="content">
        <div>
            <div id="widg-nav">
                <ul class="widg-menu"><b>TIPO DE CARGA: </b>
                    <li><a href="?cms=amdocs&tipocarga=new" <?=$cssActivoNew;?> id="">Nuevo Contenido</a></li>
                    <li><a href="?cms=amdocs&tipocarga=update" <?=$cssActivoUpdate;?> id="">Actualización</a></li>
<!--                    <li><a href="?cms=amdocs&tipocarga=jamaica" <?=$cssActivoJamaica;?> id="">Update Jamaica</a></li>-->
                </ul>
                </div>
            <p>ATENCION: <?=$mensajeTipoCarga;?></p>
        </div>


            <form action="" name="filtros" method="post">
                <input type="hidden" name="jiden" value="se" />
                <input type="hidden" name="workingProvider" id="workingProvider" value="" />
                <input type="hidden" name="workingType" id="workingType" value="" />
                <input type="hidden" name="workingCat" id="workingCat" value="" />
                <input type="hidden" name="workingSubCat" id="workingSubCat" value="" />
                <input type="hidden" name="workingCatLvl" id="workingCatLvl" value="" />
                <input type="hidden" name="workingWebCat" id="workingWebCat" value="" />
                <input type="hidden" name="workingRating" id="workingRating" value="" />
                <input type="hidden" name="workingTipoCarga" id="workingTipoCarga" value="<?=$tipocarga;?>" />
                <input type="hidden" name="workingMarca" id="workingMarca" value="" />
                <input type="hidden" name="workingFestivo" id="workingFestivo" value="" />
                <input type="hidden" name="workingTipoVideo" id="workingTipoVideo" value="" />
                <!--<input type="hidden" name="workingShortDesc" id="workingShortDesc" value="" />
                <input type="hidden" name="workingLongDesc" id="workingLongDesc" value="" />
                <input type="hidden" name="workingKeywords" id="workingKeywords" value="" />-->
                <div id="optionstabs">
                    <ul class="tabs">
                        <li><a id="t1" href="#cont-tipos">1- Tipo y Proveedor</a></li>
                        <li><a id="t2" href="#cont-cats">2- Categorias</a></li>
                        <li><a id="t3" href="#cont-migcat">3- Nivel y Website Category</a></li>
                        <li><a id="t4" href="#cont-infoadicional">4- Rating/Marca</a></li>
                        <li><a id="t5" href="#cont-ids">5- Contenidos</a></li>
                    </ul>
                    <!-- tab "panes" -->
                    <div class="panes">
                        <div id="get-tipos">
                            <noscript><a name="cont-tipos"></a></noscript>
                            <h2>Paso 1/5:</h2>
                            <h2> Seleccione el Tipo de Contenido y proveedor.</h2><br/>
			    Debe seleccionar obligatoriamente el tipo de contenido que va a procesar. 
                            <p><h3>Tipo:</h3>
                                <select id="contentType" name="tipo">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($nombre_tipos as $key => $value) {
                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></p>
                            <p><h3>Proveedor:</h3>
                                <select id="provider" name="provider">
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
                                <select name="subcatmigwp" id="subcatmigwp">
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
                                <select name="subcatmigrt" id="subcatmigrt">
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
                                <select name="subcatmigpt" id="subcatmigpt">
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
                                <select name="subcatmigvd" id="subcatmigvd">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($sub_categorias_videos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>

                            <div id='ssCat'><h3>Categoria Screensavers:</h3>
                                <select name="catmigss" id="catmigss">
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
                                <select name="catmigjg" id="catmigjg">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($categorias_juegos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></div>
                            <div id='jgSubCat'><h3>Sub-Categoria JavaGames:</h3>
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
                                <select name="webCat_vd" id="webCat_vd" >
                                    <option value="">Seleccionar uno...</option>
                                    <option value="Ideas Video">Ideas Video</option>
                                    <option value="Videos Musicales">Videos Musicales</option>
                                    <option value="Videoclips">Videoclips</option>
                                    <option value="Lo más Sexy">Lo m&aacute;s Sexy</option>
                                </select></div>
			    <div id='webCatDiv_wp'><h3>Website Category:</h3>
                                <select name="webCat_wp" id="webCat_wp" >
                                    <option value="">Seleccionar uno...</option>
				    <option value="Animadas">Animadas</option>
				    <option value="A Color">A Color</option>
                                    <option value="Comics MMS">Comics MMS</option>
                                    <option value="Postales MMS">Postales MMS</option>
                                    <option value="Deportivas">Deportivas</option>
                                    <option value="Lo más Sexy">Lo m&aacute;s Sexy</option>
                                </select></div>
                             <div id='webCatDiv_rt'><h3>Website Category:</h3>
                                <select name="webCat_rt" id="webCat_rt" >
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
                                <select name="webCat_jg" id="webCat_jg" >
                                    <option value="">Seleccionar uno...</option>
				    <option value="Básicos">Básicos</option>
				    <option value="Juegos Premium">Juegos Premium</option>
                                    <option value="Juegos y Test Para Ellas">Juegos y Test Para Ellas</option>
                                    <option value="Juegos 3D">Juegos 3D</option>
                                    <option value="Lo más Sexy">Lo más Sexy</option>
                                    <option value="Touch and Sensor">Touch and Sensor</option>
                                </select></div>

				<div id='webCatDiv_pt'><h3>Website Category:</h3>
                                <select name="webCat_pt" id="webCat_pt" >
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
                            <h2>Rating de edad, Marca y Eventos Festivos...</h2><br/>

                            <div id='ratingTitle'>
                                <p>
                                 <h3>Rating:</h3>
                                 Los numeros representan las edades para las cuales el contenido debe ser ofrecido. Para contenidos adultos indicar 18+.<br/>
                                     <select name="rating" id="rating" >
                                        <option value="">Seleccionar uno...</option>
                                        <option value="3+">3+</option>
                                        <option value="7+">7+</option>
                                        <option value="12+">12+</option>
                                        <option value="16+">16+</option>
                                        <option value="18+">18+</option>
                                    </select>
                                 </p>
                                <p>ATENCION: Si el contenido es de adulto, se marcará automáticamente como 18+ sin importar lo que se seleccione!!!</p>

                               <!-- <h3>Keywords:</h3>
                                <textarea name="keywords" id="keywords"></textarea>
                                <h3>Short Description:</h3>
                                <textarea name="shortDesc" id="shortDesc"></textarea>
                                <h3>Long Description:</h3>
                                <textarea name="longDesc" id="longDesc"></textarea> -->
                                <p>
                                <h3>Marca:</h3>
                                Si se deja vacío se setea "Ideas". Ej: Garfield<br/>
                                <input type="text" name="marca" id="marca" />
                                </p>

                                <p>
                                <h3>Festivo:</h3>
                                Si es un contenido Festivo, detallar el mismo (se agrega en Creator y Short Description). Ej: Navidad<br/>
                                <input type="text" name="festivo" id="festivo" />
                                </p>

                                <p>
                                <div id="divTipoVideo">
                                    <h2> Tipo de Video</h2>
                                    <p>Seleccione el tipo de video:</p>
                                        <input type="radio" name="tipovideo" id="tipovideo" value="clip" checked/> Clip (hasta 30 segs)<br/>
                                        <input type="radio" name="tipovideo" id="tipovideo" value="full" /> Full (más de 30 segs)
                                    <br/>
                                </div>
                                </p>

                                <p><a href="#" class="big-button" onClick="gotoStep5();">Siguiente >></a></p>
                            </div>
                        </div>


                        <div id="get-ids">
                            <noscript><a name="cont-ids"></a></noscript>
                            <h2>Paso 5/5: Seleccione los Contenidos/Paises..</h2><br/>

                            <div id="opcionesDiv5">
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
                                        <td>keywords (separadas por espacio)</td>
                                        <td>Short Description</td>
                                        <td>Long Description</td>
                                    </tr>
                                    <?php for($i = 1; $i <= 10; $i++) {?>
                                    <tr>
                                        <td> <input type="text" name="id_<?= $i ?>" id="id_<?= $i ?>" /></td>
                                        <td> <input type="text" name="keywords_<?= $i ?>" id="keywords_<?= $i ?>" /></td>
                                        <td><textarea name="shortDesc_<?= $i ?>" id="shortDesc_<?= $i ?>"></textarea></td>
                                        <td><textarea name="longDesc_<?= $i ?>" id="longDesc_<?= $i ?>"></textarea></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                                -->
                                <h3>Merchants / Idiomas:</h3>
                                <table>
                                <tr>
                                <td></td><td>ARG</td><td>BRA</td><td>CHL</td><td>COL</td><td>ECU</td><td>GTM</td><td>HND</td><td>JAM</td><td>MEX</td>
                                <td>NIC</td><td>PAN</td><td>PRY</td><td>PER</td><td>PRI</td><td>DOM</td><td>SLV</td><td>URY</td>
                                </tr>
                                <tr>
                                    <td>Merchants ></td>
                                <td><input type="checkbox" name="paises[]" id="paisAR" value="AR" onclick="toggleLang(this.value);" /></td>
                                <td><input type="checkbox" name="paises[]" id="paisBR" value="BR" onclick="toggleLang(this.value);" disabled /></td>
                                <td><input type="checkbox" name="paises[]" id="paisCL" value="CL" onclick="toggleLang(this.value);" /></td>
                                <td><input type="checkbox" name="paises[]" id="paisCO" value="CO" onclick="toggleLang(this.value);" /></td>
                                <td><input type="checkbox" name="paises[]" id="paisEC" value="EC" onclick="toggleLang(this.value);" /></td>
                                <td><input type="checkbox" name="paises[]" id="paisGT" value="GT" onclick="toggleLang(this.value);" /></td>
                                <td><input type="checkbox" name="paises[]" id="paisHN" value="HN" onclick="toggleLang(this.value);" /></td>
                                <td><input type="checkbox" name="paises[]" id="paisJM" value="JM" onclick="toggleLang(this.value);" disabled /></td>
                                <td><input type="checkbox" name="paises[]" id="paisMX" value="MX" disabled /></td>
                                <td><input type="checkbox" name="paises[]" id="paisNI" value="NI" onclick="toggleLang(this.value);" /></td>
                                <td><input type="checkbox" name="paises[]" id="paisPA" value="PA" onclick="toggleLang(this.value);" /></td>
                                <td><input type="checkbox" name="paises[]" id="paisPY" value="PY" onclick="toggleLang(this.value);" disabled /></td>
                                <td><input type="checkbox" name="paises[]" id="paisPE" value="PE" onclick="toggleLang(this.value);" /></td>
                                <td><input type="checkbox" name="paises[]" id="paisPR" value="PR" onclick="toggleLang(this.value);" /></td>
                                <td><input type="checkbox" name="paises[]" id="paisDO" value="DO" onclick="toggleLang(this.value);" /></td>
                                <td><input type="checkbox" name="paises[]" id="paisSV" value="SV" onclick="toggleLang(this.value);" /></td>
                                <td><input type="checkbox" name="paises[]" id="paisUY" value="UY" onclick="toggleLang(this.value);" /></td>
                                </tr>
                                <tr>
                                    <td>Idiomas ></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaAR" value="AR" onclick="toggleMerchant(this.value);" /><span id="flagAR" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaBR" value="BR" onclick="toggleMerchant(this.value);" disabled /><span id="flagBR" style="background-color:#66cc33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">PT</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaCL" value="CL" onclick="toggleMerchant(this.value);" /><span id="flagCL" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaCO" value="CO" onclick="toggleMerchant(this.value);" /><span id="flagCO" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaEC" value="EC" onclick="toggleMerchant(this.value);" /><span id="flagEC" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaGT" value="GT" onclick="toggleMerchant(this.value);" /><span id="flagGT" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaHN" value="HN" onclick="toggleMerchant(this.value);" /><span id="flagHN" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaJM" value="JM" onclick="toggleMerchant(this.value);" disabled /><span id="flagJM" style="background-color:#3db1ff;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">EN</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaMX" value="MX" checked disabled /><span id="flagMX" style="background-color:#ffff33;float:right;display:block;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaNI" value="NI" onclick="toggleMerchant(this.value);" /><span id="flagNI" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaPA" value="PA" onclick="toggleMerchant(this.value);" /><span id="flagPA" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaPY" value="PY" onclick="toggleMerchant(this.value);" disabled /><span id="flagPY" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaPE" value="PE" onclick="toggleMerchant(this.value);" /><span id="flagPE" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaPR" value="PR" onclick="toggleMerchant(this.value);" /><span id="flagPR" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaDO" value="DO" onclick="toggleMerchant(this.value);" /><span id="flagDO" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaSV" value="SV" onclick="toggleMerchant(this.value);" /><span id="flagSV" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                <td><input type="checkbox" name="idiomas[]" id="idiomaUY" value="UY" onclick="toggleMerchant(this.value);" /><span id="flagUY" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                </tr>
                                </table>
                                <p>ATENCION: Para los idiomas EN y BR, si no hay traducción quedarán los datos en blanco en el XML!!</p>
                                <p>ATENCION: El idioma ES para México va siempre obligatorio en el XML por defecto!</p>
                                <p>ATENCION: Si es una actualización, se borrarán los contenidos de los merchants que no se seleccionen y se hayan seleccionado en la ingestión inicial!!!</p>


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
<?php
}
?>