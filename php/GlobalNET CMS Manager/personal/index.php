<?php
error_reporting(E_ERROR);
ini_set('display_errors', '1');


include_once($globalIncludeDir."/includes.php");

$dbc = new coneXion("Web");
//include_once("prt.php");

$step2 = "";

$nombre_tipos = array(
    //"PT" => "Polytone",
   // "RT" => "Realtone",
   // "VD" => "Video",
   // "WP" => "Wallpaper",
//    "SS" => "Screensaver",
//    "FT" => "Fulltrack",
//    "TH" => "Theme",
    "midlet" => "Game/App",
);

$sql = "SELECT * FROM Web.contenidos_proveedores WHERE americaMovil = 1 order by id asc";
$rs = mysql_query($sql, $dbc->db) or die(mysql_error());
$providers = array();
while($row = mysql_fetch_object($rs)){
    $providers[$row->id] = $row->nombre;
}


?>
<script>
        var categorias = new Array();
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
			case 'midlet':
				t='Juego Java';
				break;
			case 'TH':
				t='Themes (TH)';
				break;
		}
		return t;
	}

	function setHtml() {
		html = '<h3>1/2 Setup Inicial...</h3><ul>';

		tipocarga = $('#tipocarga').val();
		if (tipocarga!= '') html += '<li>Tipo Carga: '+tipocarga+'</li>';

		tipo = $('#workingCat').val();
		if (tipo != '') html += '<li>Tipo: '+getTipo(tipo)+'</li>';

		wc = $('#contentType').val();

                var idx;
                html += '<li>Categoria(s): </li>';
                for(idx = 0; idx < categorias.length; idx++) {
                    html += '<li>&nbsp;&nbsp; ' + categorias[idx].text + '</li>';
                }


                /*
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

*/

	   html += '</ul>';
		$('#working-on').html(html);
	}

	function gotoStep2() {
		$('#workingCatLvl').val('');
		$('#workingWebCat').val('');
		tipo = $('#contentType').val();
		idTipo = '#'+tipo.toLowerCase()+'Cat';
		idStyle = $(idTipo).css('display');
	
		//viewHiddenCat(tipo);
                $('#cats').show();
		viewHiddenSubCat(tipo);
                viewHiddenClientCats(tipo);

                tipocarga = $('#tipocarga').val();

		if (tipo != '') {
			$('#workingCat').val(tipo);
			$('#workingType').val(tipo);
	
                        $('#workingTipoCarga').val(tipocarga);
			$('#t2').click();
			setHtml();
		} else {
			alert('Debe seleccionar un Tipo de contenido y un proveedor.');
			return false;
		}
	}

	function gotoStep3() {
                categorias = $('#cats > option:selected');
		//alert(categorias.length);
                
                $('#campos-ids').show();
		if (categorias.length > 0) {
                        var cats = new Array();
                        $.each(categorias, function(idx, option) {
                            cats.push(option.text);
                        })
                

                        $('#categorias').val(cats.join(","));
                
			$('#t3').click();
			setHtml();
		} else {
			alert('Debe seleccionar almenos una categoría.');
			return false;
		}
	}
	/*function gotoStep4() {
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
		$('#opcionesDiv5').css('display','block');

		if (rating != '') {
                        $('#workingFestivo').val(festivo);
                        $('#workingMarca').val(marca);
			$('#workingRating').val(rating);
			$('#t5').click();
			setHtml();
		} else {
			alert('Debe seleccionar un rating');
			return false;
		}
	*/
</script>
<!--<script type="text/javascript" src="amdocs/js/jquery.js"></script>-->
<!--<script type="text/javascript" src="amdocs/js/jquery.expander.js"></script>-->
<!--<script type="text/javascript" src="amdocs/js/jquery.tools.min.js"></script>-->
<!--<script type="text/javascript" src="amdocs/js/init.js"></script>-->

<a href="personal/cargarCelulares.php">Cargar celulares homologados</a><br/>
        <div id="titletop"><h1>Generador de archivos XML para Ideas America Movil</h1></div>

        <div id="content">
            <form action="" name="filtros" method="post">
                <input type="hidden" name="jiden" value="se" />
                <input type="hidden" name="workingProvider" id="workingProvider" value="" />
                <input type="hidden" name="workingType" id="workingType" value="" />
                <input type="hidden" name="workingCat" id="workingCat" value="" />
                <input type="hidden" name="categorias" id="categorias" value="" />
                <input type="hidden" name="workingTipoCarga" id="workingTipoCarga" value="" />
                <input type="hidden" name="workingMarca" id="workingMarca" value="" />
                <input type="hidden" name="workingFestivo" id="workingFestivo" value="" />
                <!--<input type="hidden" name="workingShortDesc" id="workingShortDesc" value="" />
                <input type="hidden" name="workingLongDesc" id="workingLongDesc" value="" />
                <input type="hidden" name="workingKeywords" id="workingKeywords" value="" />-->
                <div id="optionstabs">
                    <ul class="tabs">
                        <li><a id="t1" href="#cont-tipos">1- Tipo y Proveedor</a></li>
                        <li><a id="t2" href="#cont-cats">2- Categorias</a></li>
                        <li><a id="t3" href="#cont-ids">3- Contenidos</a></li>
                    </ul>
                    <!-- tab "panes" -->
                    <div class="panes">
                        <div id="get-tipos">
                            <noscript><a name="cont-tipos"></a></noscript>
                            <h2>Paso 1/3:</h2>
                            <h2> Seleccione el Tipo de Contenido.</h2><br/>
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
                            

                            <h2> Seleccione si es carga nueva o update</h2>
                            <p>Tipo de Carga:</p>
                                <input type="radio" name="tipocarga" id="tipocarga" value="new" checked/>New<br/>
                                <input type="radio" name="tipocarga" id="tipocarga" value="update" />Update
                            <br/>

                                <p><a href="#" class="big-button" onClick="return gotoStep2();">Siguiente >></a></p>
                        </div>
                        <div id="get-cats">
                            <noscript><a name="cont-cats"></a></noscript>
                            <h2>Paso 2/3: Seleccione la Categoria y Sub-Categoria</h2><br/>
			    Ambas selecciones son obligatorias.
                            <div id="cats">
                                <h3>Categorias</h3>
                                <h4>Seleccione varias dejando apretado "CONTROL"</h4>
                                <select multiple="multiple" id="cats" name="cats[]" height="200">
                                    <?php
                                        foreach($categorias_personal as $cat) {
                                            echo '<option value="'.urlencode($cat).'">'.$cat.'</option>';
                                        }

                                    ?>
                                </select>

                            </div>
                    

                            <p><a href="#" class="big-button" onClick="return gotoStep3();">Siguiente >></a></p>
                        </div>
                        <div id="get-ids">
                            <noscript><a name="cont-ids"></a></noscript>
                          
                            <h2>Paso 3/3: Seleccione los Contenidos/Paises..</h2><br/>

                            <div id="campos-ids">
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


                        <div id="get-ids">
                        
                        </div>
                </div>

            </form>

            <div id="working-on">

            </div>

            <?php
            if ($_POST["jiden"] == "se") include_once($globalIncludeDir."/pre-process.php");
            ?>

        </div>
