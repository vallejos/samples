<?php


?>
<script>
	function setAction(tipo) {
		tipo = $('#workingType').val();

		cont = false;
		contentId = $('#ids').val();

		if (contentId != '' || contentCat != '') {
			cont = true;
		}

		if (cont==false) {
			alert('Debe completar todos los datos para poder generar el XML.\nRevise el Setup Inicial.');
			$('#t1').click();
			return false;
		}

                // muestro el loading...
                $('#loading').css('display','block');

		document.filtros.action = 'index' + ".php";
		document.filtros.submit();
	}
        function hasAnySelected(lang) {
            // devuelve TRUE si esta seleccionado alguno de los merchants que tienen asociado el idioma lang
            // para BR y JM el array es de un solo elemento, se hace un if directo
            // para ES hay un array de paises
            var anyFound = false;
            var langEN = ['JM'];
            var langES = ['AR','CL','CO','EC','GT','HN','NI','PY','PA','PE','PR','DO','SV','UY'];
            var langBR = ['BR'];

            // para EN veo si JM esta seleccionado (es el unico)
            if (lang == 'EN') {
                var langChk = $('#idiomaJM').is(':checked');
                var merchChk = $('#paisJM').is(':checked');

                if ((langChk == true) && (merchChk==true)) anyFound = true;
            }

            // mismo caso para BR, veo si BR esta seleccionado (es el unico)
            if (lang == 'BR') {
                var langChk = $('#idiomaBR').is(':checked');
                var merchChk = $('#paisBR').is(':checked');

                if ((langChk == true) && (merchChk==true)) anyFound = true;
            }

            // para ES es mas complejo, hay que revisar uno a uno los merchants
            if (lang == 'ES') {
                var arLen=langES.length;
                for (var i=0, len=arLen; i<len; ++i ){
                    country = langES[i];
                    var langChk = $('#idioma'+country).is(':checked');
                    var merchChk = $('#pais'+country).is(':checked');

                    if ((langChk == true) && (merchChk==true)) anyFound = true;
                }
            }

            return anyFound;
        }
        function hideAll(lang) {
		$('#wpCat'+lang).css('display','none');
		$('#ssCat'+lang).css('display','none');
		$('#thCat'+lang).css('display','none');
		$('#jgCat'+lang).css('display','none');
		$('#ptCat'+lang).css('display','none');
		$('#ftCat'+lang).css('display','none');
		$('#rtCat'+lang).css('display','none');
		$('#vdCat'+lang).css('display','none');
        }
        function uncheckAll() {
            var langs = ['AR','CL','CO','EC','GT','HN','NI','PY','PA','PE','PR','DO','SV','UY','BR','JM'];

            var arLen=langs.length;
            for (var i=0, len=arLen; i<len; ++i ){
                country = langs[i];

                $('#flag'+country).css('display','none');
                $('#pais'+country).attr('checked', false);
                $('#idioma'+country).attr('checked', false);
            }

        }
        function resetAll() {
            uncheckAll();
            hideAll('ES');
            hideAll('EN');
        }
        function toggleMerchant(country, chkLang) {
            // chequeo tipo
            tipo = $('#contentType').val();
            if (tipo == '') {
                alert('Debe seleccionar el tipo de contenido primero.');
                return false;
            }

            if (chkLang ==  'ES') hideAll('ES');
            if (chkLang ==  'EN') hideAll('EN');

            var lang = $('#idioma'+country).is(':checked');
            var merch = $('#pais'+country).is(':checked');

            if (lang === true) {
                $('#flag'+country).css('display','block');
                $('#pais'+country).attr('checked', true);

            } else {
                $('#flag'+country).css('display','none');
                $('#pais'+country).attr('checked', false);
            }
            
            if (hasAnySelected(chkLang) == true) {
                viewHiddenCat(tipo, chkLang);
            }
        }
        function toggleLang(country, chkLang) {
            // chequeo tipo
            tipo = $('#contentType').val();
            if (tipo == '') {
                alert('Debe seleccionar el tipo de contenido primero.');
                return false;
            }

            if (chkLang ==  'ES') hideAll('ES');
            if (chkLang ==  'EN') hideAll('EN');

            var lang = $('#idioma'+country).is(':checked');
            var merch = $('#pais'+country).is(':checked');

            if (merch === true) {
                $('#flag'+country).css('display','block');
                $('#idioma'+country).attr('checked', true);
            } else {
                $('#flag'+country).css('display','none');
                $('#idioma'+country).attr('checked', false);
            }

            if (hasAnySelected(chkLang) == true) {
                viewHiddenCat(tipo, chkLang);
            }
        }
	function viewHiddenCat(idT, lang) {
		idTipo = '#'+idT.toLowerCase()+'Cat'+lang;
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


</script>

        <div id="titletop"><h1>Generador de archivos XML para Ideas America Movil (Update)</h1></div>

        <div id="content">
        <div>
            <div id="widg-nav">
                <ul class="widg-menu"><b>TIPO DE CARGA: </b>
                    <li><a href="?cms=amdocs&tipocarga=new" <?=$cssActivoNew;?> id="">Nuevo Contenido</a></li>
                    <li><a href="?cms=amdocs&tipocarga=update" <?=$cssActivoUpdate;?> id="">Actualización</a></li>
                </ul>
                </div>
            <p>ATENCION: <?=$mensajeTipoCarga;?></p>
        </div>


            <form action="" name="filtros" method="post">
                <input type="hidden" name="jiden" value="se" />
                <input type="hidden" name="workingType" id="workingType" value="" />
                <input type="hidden" name="workingTipoCarga" id="workingTipoCarga" value="<?=$tipocarga;?>" />
                <div id="optionstabs">
                    <ul class="tabs">
                        <li><a id="t1" href="#cont-tipos">1- Tipo / Contenidos / Paises</a></li>
                    </ul>
                    <!-- tab "panes" -->
                    <div class="panes">
                        <div id="get-tipos">
                            <noscript><a name="cont-tipos"></a></noscript>
                            <h2> Seleccione el Tipo de Contenido y proveedor.</h2><br/>
			    Debe seleccionar obligatoriamente el tipo de contenido que va a procesar. 
                            <p><h3>Tipo:
                                <select id="contentType" name="tipo" onchange="resetAll();">
                                    <option value="">Seleccionar uno...</option>
                                    <?php
                                    foreach ($nombre_tipos as $key => $value) {
                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></h3></p>

                                <p><h3>IDs Contenido:</h3>
                                    <textarea name="ids" id="ids"></textarea></p>

                                <p><h3>Category Level:
                                    <select name="catLvl" id="catLvl">
                                        <option value="">No actualizar</option>
                                        <option value="Gold">Gold</option>
                                        <option value="Silver">Silver</option>
                                        <option value="Bronze">Bronze</option>
                                    </select></h3></p>

                                <p><h3>Rating:
                                     <select name="rating" id="rating" >
                                        <option value="">No actualizar</option>
                                        <option value="3+">3+</option>
                                        <option value="7+">7+</option>
                                        <option value="12+">12+</option>
                                        <option value="16+">16+</option>
                                        <option value="18+">18+</option>
                                    </select></h3></p>
                                <p>ATENCION: Si el contenido es de adulto, se marcará automáticamente como 18+ sin importar lo que se seleccione!!!</p>
                                
                                <p><h3>Merchants / Idiomas:</h3>
                                    <table>
                                    <tr>
                                    <td></td><td>ARG</td><td>BRA</td><td>CHL</td><td>COL</td><td>ECU</td><td>GTM</td><td>HND</td><td>JAM</td><td>MEX</td>
                                    <td>NIC</td><td>PAN</td><td>PRY</td><td>PER</td><td>PRI</td><td>DOM</td><td>SLV</td><td>URY</td>
                                    </tr>
                                    <tr>
                                        <td>Merchants ></td>
                                    <td><input type="checkbox" name="paises[]" id="paisAR" value="AR" onclick="toggleLang(this.value, 'ES');" /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisBR" value="BR" onclick="toggleLang(this.value, 'BR');" disabled /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisCL" value="CL" onclick="toggleLang(this.value, 'ES');" /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisCO" value="CO" onclick="toggleLang(this.value, 'ES');" /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisEC" value="EC" onclick="toggleLang(this.value, 'ES');" /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisGT" value="GT" onclick="toggleLang(this.value, 'ES');" /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisHN" value="HN" onclick="toggleLang(this.value, 'ES');" /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisJM" value="JM" onclick="toggleLang(this.value, 'EN');" disabled /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisMX" value="MX" disabled /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisNI" value="NI" onclick="toggleLang(this.value, 'ES');" /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisPA" value="PA" onclick="toggleLang(this.value, 'ES');" /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisPY" value="PY" onclick="toggleLang(this.value, 'ES');" disabled /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisPE" value="PE" onclick="toggleLang(this.value, 'ES');" /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisPR" value="PR" onclick="toggleLang(this.value, 'ES');" /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisDO" value="DO" onclick="toggleLang(this.value, 'ES');" /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisSV" value="SV" onclick="toggleLang(this.value, 'ES');" /></td>
                                    <td><input type="checkbox" name="paises[]" id="paisUY" value="UY" onclick="toggleLang(this.value, 'ES');" /></td>
                                    </tr>
                                    <tr>
                                        <td>Idiomas ></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaAR" value="AR" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagAR" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaBR" value="BR" onclick="toggleMerchant(this.value, 'BR');" disabled /><span id="flagBR" style="background-color:#66cc33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">PT</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaCL" value="CL" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagCL" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaCO" value="CO" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagCO" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaEC" value="EC" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagEC" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaGT" value="GT" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagGT" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaHN" value="HN" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagHN" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaJM" value="JM" onclick="toggleMerchant(this.value, 'EN');" disabled /><span id="flagJM" style="background-color:#3db1ff;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">EN</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaMX" value="MX" checked disabled /><span id="flagMX" style="background-color:#ffff33;float:right;display:block;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaNI" value="NI" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagNI" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaPA" value="PA" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagPA" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaPY" value="PY" onclick="toggleMerchant(this.value, 'ES');" disabled /><span id="flagPY" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaPE" value="PE" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagPE" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaPR" value="PR" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagPR" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaDO" value="DO" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagDO" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaSV" value="SV" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagSV" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    <td><input type="checkbox" name="idiomas[]" id="idiomaUY" value="UY" onclick="toggleMerchant(this.value, 'ES');" /><span id="flagUY" style="background-color:#ffff33;float:right;display:none;height:20px;overflow:auto;padding:0;position:relative;">ES</span></td>
                                    </tr>
                                    </table>
                                    <p>ATENCION: Para los idiomas EN y BR, si no hay traducción quedarán los datos en blanco en el XML!!</p>
                                    <p>ATENCION: El idioma ES para México va siempre obligatorio en el XML por defecto!</p>
                                    <p>ATENCION: Si es una actualización, se borrarán los contenidos de los merchants que no se seleccionen y se hayan seleccionado en la ingestión inicial!!!</p>
                                    <p>ATENCION: Si se eligen merchants en Inglés y en Español, necesariamente hay que elegir las categorías en ámbos idiomas o se borran!!</p>
                                </p>


                             <!-- categorias: JAVAGAMES -->
                            <div id='jgCatEN'><h3>Categorias en Inglés:</h3><p>
                                Categoria: <select name="catmigjgen" id="catmigjg">
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($categorias_juegos_en as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select> Subcategoria:
                                <select name="subcatmigjgen" id="subcatmigjg" >
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($sub_categorias_juegos_en as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select><br/></p></div>
                            <div id='jgCatES'><h3>Categorias en Español:</h3><p>
                                Categoria: <select name="catmigjg" id="catmigjg">
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($categorias_juegos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select> Subcategoria:
                                <select name="subcatmigjg" id="subcatmigjg" >
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($sub_categorias_juegos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></p></div>
                             <!-- end JAVAGAMES -->

                             <!-- categorias: REALTONES -->
                            <div id='rtCatEN'><h3>Categorias en Inglés para Jamaica:</h3><p>
                                Categoria: <select name="catmigrten" id="catmigrt">
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($categorias_realtones_en as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select> Subcategoria:
                                <select name="subcatmigrten" id="subcatmigrt" >
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($sub_categorias_realtones_en as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></p></div>
                            <div id='rtCatES'><h3>Categorias en Español:</h3><p>
                                Categoria: <select name="catmigrt" id="catmigrt">
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($categorias_realtones as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select> Subcategoria:
                                <select name="subcatmigrt" id="subcatmigrt" >
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($sub_categorias_realtones as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></p></div>
                             <!-- end REALTONES -->

                             <!-- categorias: POLIFONICOS -->
                            <div id='ptCatEN'><h3>Categorias en Inglés para Jamaica:</h3><p>
                                Categoria: <select name="catmigpten" id="catmigpt">
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($categorias_polytones_en as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select> Subcategoria:
                                <select name="subcatmigpten" id="subcatmigpt" >
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($sub_categorias_polytones_en as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></p></div>
                            <div id='ptCatES'><h3>Categorias en Español:</h3><p>
                                Categoria: <select name="catmigpt" id="catmigpt">
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($categorias_polytones as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select> Subcategoria:
                                <select name="subcatmigpt" id="subcatmigpt" >
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($sub_categorias_polytones as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></p></div>
                             <!-- end POLIFONICOS -->

                             <!-- categorias: WALLPAPERS -->
                            <div id='wpCatEN'><h3>Categorias en Inglés para Jamaica:</h3><p>
                                Categoria: <select name="catmigwpen" id="catmigwp">
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($categorias_wallpapers_en as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select> Subcategoria:
                                <select name="subcatmigwpen" id="subcatmigwp" >
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($sub_categorias_wallpapers_en as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></p></div>
                            <div id='wpCatES'><h3>Categorias en Español:</h3><p>
                                Categoria: <select name="catmigwp" id="catmigwp">
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($categorias_wallpapers as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select> Subcategoria:
                                <select name="subcatmigwp" id="subcatmigwp" >
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($sub_categorias_wallpapers as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></p></div>
                             <!-- end WALLPAPERS -->

                             <!-- categorias: VIDEOS -->
                            <div id='vdCatEN'><h3>Categorias en Inglés para Jamaica:</h3><p>
                                Categoria: <select name="catmigvden" id="catmigvd">
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($categorias_videos_en as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select> Subcategoria:
                                <select name="subcatmigvden" id="subcatmigvd" >
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($sub_categorias_videos_en as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></p></div>
                            <div id='vdCatES'><h3>Categorias en Español:</h3><p>
                                Categoria: <select name="catmigvd" id="catmigvd">
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($categorias_videos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select> Subcategoria:
                                <select name="subcatmigvd" id="subcatmigvd" >
                                    <option value="">No actualizar</option>
                                    <?php
                                    foreach ($sub_categorias_videos as $key => $value) {
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select></p></div>
                             <!-- end VIDEOS -->




                                <p><a href="#" class="big-button" onClick="return setAction('xml');">Generar XML de Actualización</a></p>
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
