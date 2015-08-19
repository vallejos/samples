<?php
session_start();
set_time_limit(0);
//
if (empty($miC)) {
	include_once("conexion.php");
	$miC = new coneXion("Web");
	$db = $miC->db;
}

if( $_SESSION['color'] ){
	$defaultColor  = $_SESSION['color'];
}else{
	$defaultColor = "#FFFFFF";
}
	
?>
<link href="../assets/estilos.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="ajax.js"></script>
<script type="text/javascript">
String.prototype.Trim = function() {
a = this.replace(/^\s+/, '');
return a.replace(/\s+$/, '');
};
//-----------------------

var ids=new Array(0);
var campoCat = document.getElementById('categoria');

//---------------------------------MOSTRAR EL RESULTADO DE BUSCAR CATEGORIAS EN IFRAME
function showFrameCat(){
	ref = document.forms[0]._categoria;
	if(ref.value.Trim() == ""){
		alert('poné algo en el campo categoría, banana!!');
		return;
	}
	iframeContainer.style.visibility = "visible";
	iframeContainer.innerHTML = "<IFRAME src='iframes/buscarCat.php?cual="+ref.value+"&tipo=7' width='350' height='250'  scrolling='auto' frameborder='1' id='iframeCats'></IFRAME>";
}
function completarCat(p) {
	if(!p)p="";
	document.forms[0]._categoria.value = p;
}
//---------------------------------MOSTRAR EL RESULTADO DE BUSCAR AUTOR EN IFRAME
function showFrameAutor(){
	ref = document.forms[0]._autor;
	if(ref.value.Trim() == ""){
		alert('poné algo en el campo autor');
		return;
	}
	iframeContainerAutor.style.visibility = "visible";
	iframeContainerAutor.innerHTML = "<IFRAME src='iframes/buscarAutor.php?cual="+ ref.value +"&tipo=23' width='350' height='250'  scrolling='auto' frameborder='1' id='iframeCats'></IFRAME>";
}
function completarAutor(p) {
	if(!p)p="";
	document.forms[0]._autor.value = p;
}
//------------------------------------EL COLOR PICKER-------------------------------------------------
var isOpen = false;
function showColor(){
	if(isOpen == true){
		cerrar();
		return;
	}else{
		iframeColorpicker.style.visibility = "visible";
		iframeColorpicker.innerHTML = "<input type='button' name='seleccionarColor' value='seleccionar color' style='width:150px;margin:5px' onClick='selColor()' /><input type='button' name='cerrarColorPicker' value='cerrar' style='width:150px;margin:5px' onClick='cerrar()' /><IFRAME src='iframes/colorpicker/index.html' width='500' height='350' scrolling='auto' id='iframeColor'></IFRAME>";
		isOpen = true;
	};	
}

function selColor(){
	//var p = document.getElementById("cp1_Hex");
	var muestra = document.getElementById("muestraColor");
	var ifr = document.getElementById("iframeColor");
	var p = ifr.contentDocument.getElementById("cp1_Hex").value;
	document.forms[0]._color.value = "#"+p;
	muestra.style.backgroundColor = "#"+p;
	
	iframeColorpicker.innerHTML = "";
	isOpen = false;
}
function cerrar(){
	iframeColorpicker.innerHTML = "";
	isOpen = false
}

function actualizarMuestra(){
	var muestra = document.getElementById("muestraColor");
	muestra.style.backgroundColor = "<?php print $_SESSION['color']?>";
}
//-------------------------------------------------------------------------------------fin color picker
//
//
//---------------------------------------AGREGAR CAMPOS EXTRA
function agregarCampoExtra(){
	var extra = document.getElementById('extra');
	var valor = document.forms[0]._proveedor.value;
	switch(valor){
		//si es proveedor 27 ó 29 (Universal) agregamos el campo para el DGP
		case "27":
		case "29":
		
		var combo = "<td align='left' valign='middle'>DGP</td><td align='left' valign='middle'><input type='text' name='_dgp' id='_dgp' size='31'/></td>";
		extra.innerHTML = combo;
		break;
		
		case "43":
		var combo2 = "<td align='left' valign='middle'><span style='color:#990000'><b>Código EMI</b></span></td><td align='left' valign='middle'><input type='text' name='_emi' id='_emi' size='31'/></td>";
		extra.innerHTML = combo2;
		break;
		
		default:
		extra.innerHTML = "";
		break;
	}
}
//------------------ajax : verificar si existe mismo nombre+proveedor+autor
function checkNombre(){
	var f = document.forms[0];
	var nombre = document.forms[0]._nombre;
	var par = f._nombre.value+"@"+f._autor.value+"@7@"+f._proveedor.value+"@"+f._categoria;
	var url = "ajax.php?id=&operacion=chkNombre&param="+ escape(par)+ "";
	var ret = phpComSync(url);
	if(ret){
		if(ret == "ok"){						
				return true;
			}else if(ret == "no"){
				return false;
			}		
		}
	else{
		alert("no hay respuesta de ajax");
		return false;
	}
}

//---------------------------------validar los campos del formulario
function validar(){
	var f=document.forms[0];
	var largo=f.length;
	var paises = new Array();
	
	for(i=0;i<largo;i++){
	
		 switch( f[i].type) {
			case "text":
			if( !f._nombre.value ){
				alert("El campo nombre es obligatorio");
				f._nombre.focus();
				return
			}else if( !f._autor.value ){
				alert("El campo autor es obligatorio");
				f._autor.focus();
				return
			}else if( f._categoria.value ){
				if(!Number( f._categoria.value )){
					alert("El campo categoría es de valor numérico");
					f._categoria.focus();
					f._categoria.style.background = "#FF0000"
					return;
				}else{
					f._categoria.style.background = "#FFFFFF"
				}
			}else if(!f._categoria.value){
				alert("El campo categoria es obligatorio y es de valor numérico");
				f._categoria.focus();
				return
			}
			break;
			
			case "checkbox":
			if(f[i].checked == true){
				paises.push(f[i].name);
			}
			break;
			
			case "select-one":			
			if(f[i].value == "-1" && f[i].name == "_proveedor"){
				alert("Elegí un proveedor");
				f[i].focus();
				f[i].style.background = "#FF0000"
				return
			}
			break;
			
			case "file":
			if(!f[i].value){
				f[i].style.marginLeft = "100px"
				f[i].style.color = "#FF0000";
				alert("seleccione un archivo para el campo: "+f[i].name);
				return;
			}else{
				f[i].style.marginLeft = "0"
				f[i].style.color = "#000000";
			}
			
		}		
	};//---termina el for
	
	
	if(paises.length < 1){
		alert("no hay portales seleccionados");
		return;
	}
	
	if(!checkNombre()){
		alert( "No se puede usar ese nombre, ya esxiste esa combinación de Nombre, Proveedor, Autor y Tipo" );
		f._nombre.focus();
		return;
	}

	document.forms[0].submit();
	//
	
}
//---------------- verificar si están correctos los archivos para subir al ftp
function checkExt(campo,ext){
	var c = document.getElementById(campo);
	var val = c.value.substr(-4);
	if(val == ext){
		c.style.marginLeft = "0"
		c.style.color = "#000000";
		return true;
	}else{
		alert("la extensión del archivo debería ser "+ext);
		c.style.marginLeft = "100px"
		c.style.color = "#FF0000";
		return false;
	}
}

</script>
<a name='buscar'></a>
<?php
	include("menu.php");
?>
<head>
<title>Subir Wallpapers</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<script type="text/javascript">
function asignarGenero(val){
	if(val != "-1"){
		document.forms[0].genero.value = val;
	}	
}
//**************************checkear y descheckear los checkbox
var p = false;
function check(){
	var f = document.forms[0];
	var l = f.length;
	for(i=0;i<l;i++){
		if(f[i].type == "checkbox" && f[i].name.substr(0,3) != "ch_"){
			if(p){
				f[i].checked = true;
				f[i].value="CHECKED";
			}else{
				f[i].checked = false;
				f[i].value = "";
			}
		}
	}
	p = !p;
}
</script>
</head>
<body onLoad='agregarCampoExtra()'>
<hr />
<h3>Subir Wallpapers</h3>
<form id="formulario" method="post" enctype="multipart/form-data" action="" > 
  <table width="50%" align="center" cellpadding="0" cellspacing="0" class="tablaForm" id="tabla">
   <tr>
    <td align="center" valign="middle" colspan='2'>&nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="middle">Nombre</td>
    <td align="left" valign="middle">
		<input name="_nombre" type="text" id="_nombre" size="38" value="<?php print $_SESSION['nombre']?>" onDblClick="this.value = ''"/>	</td>
  </tr>
  <tr>
	    <td>Autor</td>
	    <td><input name="_autor" type="text" id="_autor" size="38" value="<?php print $_SESSION['autor']?>" onDblClick="this.value = ''"/>
	      <input name="buscar222" type="button" id="buscar22" value="buscar" onClick="javascript: showFrameAutor()" />
				<!-- el iframe con el resultado de la búsqueda de las categorías -->
				<div id="iframeContainerAutor" ></div>			</td>
      </tr><tr>
		<td align="left" valign="middle">Categor&iacute;a</td>
		<td align="left" valign="middle">
			<input name="_categoria" type="text" id="_categoria" size="38" value="<?php print $_SESSION['categoria']?>"  onDblClick="this.value = ''"/>
			<input name="buscar_btn" type="button" id="buscar_btn" value="buscar" onClick="javascript: showFrameCat()" />
			<!-- el iframe con el resultado de la búsqueda de las categorías -->
			<div id="iframeContainer" ></div>		</td>
	</tr>
	<tr>
	  <td align="left" valign="middle">Proveedor</td>
	  <td align="left" valign="middle"><select name="_proveedor" id="_proveedor" onChange="this.style.background = '#FFFFFF';javascript:agregarCampoExtra()" >
      <option  value='-1'>seleccione...</option>
      <?php
			  
				$sql = "select * FROM contenidos_proveedores order by id";
				$result = mysql_query($sql, $db);
				if($result){
					while( $row= mysql_fetch_assoc($result) ){
						$_SESSION['proveedor'] ==  $row['id'] ? $sel="selected='selected'" : $sel="";
						print "<option value='". $row['id'] ."' ".$sel.">".$row['id']." - ".$row['nombre']."</option>";
					}
				}
				?>
    </select></td>
	  </tr>
	<tr>
		<td align="left" valign="middle">Im&aacute;gen</td>
		<td align="left" valign="middle"><!--  -->
		  <input type="file" id="imagen" name="imagen" />
      <h4 style="display:inline" >.gif</h4></td>
	  </tr>
	  <tr>
	    <td>Color de fondo </td>
	    <td>
				<div id="muestraColor" style="background-color:<?php print $defaultColor ?>;border:solid 1px #000000" onDblClick="showColor()" title="doble click para cambiar el color"></div>
	      <input name="_color" type="text" id="_color" value="<?php print $defaultColor ?>" size="7" maxlength="7" onDblClick="this.value = ''">
	      <input name="color" type="button" id="color" value="cambiar color" onClick="showColor()" style="margin:5px;">
				<!-- el iframe con el color picker -->
				<div id="iframeColorpicker" ></div>	    </td>
    </tr>
	  <tr>
		<td>Publicación</td>
		<td title="Para filtrar el resultado dependiendo de lugar de publicación">
		<!-- <div style="margin-bottom:12px" align='left'>
			<a href="javascript: check()">Marcar / desmarcar todos</a>
		</div> -->
		<table align="left" cellpadding="0" cellspacing="0" border='0' style='font-size:9px' width='75%'>
		<?php
		$sql = "show columns from contcol_whitelist ";
		$result = mysql_query($sql, $db);
		if($result){
			$flag=true;
			while( $row= mysql_fetch_array($result) ){

				if($row[0] == "contenido"){					
					continue;
				}
				if( @in_array( $row[0] , $_SESSION['portales'] ) ){
					$check = "checked = 'checked'";
				}else{
					$check = "";
				}
				
				if($flag){
					$abre="<tr>";
					$cierra="";
				}else{
					$abre="";
					$cierra="</tr>";												
				}
				$flag = !$flag;
				print $abre;
				?>					
				<td style="border:0;padding:0"><label>
				<input type="checkbox" name="<?php print "chk_".$row[0] ?>" id="<?php print "chk_".$row[0] ?>" <?php print $check ?> />
				<?php print $row[0]?>
				</label></td>					
				<?php					
				print $cierra;
				
			}
		};
		?>
			
			</table>		</td>
	  </tr>
  <tr>
	<td height="50" colspan="2" align="center" valign="middle">
	<input style="width:300px;height:30px" type="button" value="subir" onClick="validar()" /></td>
	</tr>
</table>
</form>
</body>
<?PHP

if(!$_POST){	
	die();
}

/*
print $_FILES['imagen']['name']  . "<br />";
foreach( $_POST as $k=>$v ){	
	print "$k - $v<br />";
}
die();
*/

session_start();
unset($_SESSION['portales']);
include_once("clases/ftp.class.php");
include_once("clases/class.imageconverter.php");
include_once("clases/resize.class.php");
include_once("clases/image.class.php");
include_once("clases/Wallpaper.php");

define("TMP", "./wallpapers_tmp/temp/");
define("TIPO", 7);
define("DIR_WP_USA" , "netuy/wp/");
define("DIR_WP_241" , "wp/");


$servers = array();
$servers['USA'][] = "216.150.27.11";//ip de USA
$servers['USA'][] = "wmast";//user de USA
$servers['USA'][] = "hulkverde";//password de USA
$servers['241'][] = "10.0.0.241";//user de la 241
$servers['241'][] = "contenido";//user de la 241
$servers['241'][] = "wyibsun0Ob";//password de la 241

$_SESSION['subidos'] = array();

$_POST['_nombre'] ? $_SESSION['nombre'] = $_POST['_nombre'] : $_SESSION['nombre'] = "";
$_POST['_autor'] ? $_SESSION['autor'] = $_POST['_autor'] : $_SESSION['autor'] = "";
$_POST['_categoria'] ? $_SESSION['categoria'] = $_POST['_categoria'] : $_SESSION['categoria'] = "";
$_POST['_color'] ? $_SESSION['color'] = $_POST['_color'] : $_SESSION['color'] = "#FFFFFF";
$_POST['genero'] ? $_SESSION['genero'] = $_POST['genero'] : $_SESSION['genero'] = "";
$_POST['_dgp'] ? $_SESSION['hayDgp'] = $_POST['_dgp'] : $_SESSION['hayDgp'] = "";
$_POST['_emi'] ? $_SESSION['hayEmi'] = $_POST['_emi'] : $_SESSION['hayEmi'] = "";
$_POST['_proveedor']!== FALSE ? $_SESSION['proveedor'] = $_POST['_proveedor'] : $_SESSION['proveedor'] = "";

$_SESSION['portales'] = array();

$portales = array();foreach( $_POST as $k=>$v ){
	if( substr($k,0,4) == "chk_" ){
		$_SESSION['portales'][] = substr($k,4);
	}
}

?>
<script type='text/javascript'>
function nuevo(){
window.location='subirWallpapers.php'	
}
</script>

<?php
//==================los tamaños que hay que convertir y donde mandarlos
include_once("clases/cropsWallpapers.php");
//----los crops q tienen q estar
$cropArchivo = '128x128@gif@241';
$cropReferencia = '62x62@gif@USA';

if( !in_array( $cropArchivo,$crops ) )error("Falta el crop $cropArchivo para la base de datos campo 'archivo' ", true);
if( !in_array( $cropArchivo,$crops ) )error("Falta el crop $cropReferencia para la base de datos campo 'referencia' ", true);
//====================FUNCIÓN PRINCIPAL==========================
function principal(){

	print "<pre style='font-size:12px;color:#000077'><a name='resultado'></a>";	
	extract($GLOBALS);
	
	print "<div align='center'><input style='margin:25px' type='button' value='Subir otro' onClick='javascript: nuevo();'/></div>";
	
	if( ! $newId = obtenerID($_POST['_nombre'], TIPO, $_POST['_categoria'], $_POST['_autor'],$_POST['_proveedor'] ) ) {
		limpiarCagadas($newId);
		error("Error creando el contenido", true);
	}
	
	if( ! $crearSubir = $newImg = crearImagenes($newId) ){
		limpiarCagadas($newId);
		error("No se pueden crear las imagenes", true);
	}
	
	if( ! updateContenidos( $newId ) ){
		limpiarCagadas($newId);
		 error("Error actualizando la base de datos", true);
	}
	print "<div align='center'><input style='margin:25px' type='button' value='Subir otro' onClick='javascript: nuevo();'/></div>";
}
//====================FUNCIONES AUXILIARES==========================
function calcularCarpeta($id){
	$carpeta = (ceil($id/500)*500);
	return $carpeta;
};
function error($str, $salir=false){
	print "<script type='text/javascript'>alert('". $str ."');</script>";
	if($salir){
		die();
	};
}
//====================CREAR EL CONTENIDO==========================
function obtenerID($nom, $tipo, $cat, $aut, $prov ){
	print "<b>Creando contenido:</b>\n";
	extract($GLOBALS);
	$sql = "INSERT INTO contenidos set nombre='$nom' , categoria=$cat, autor='$aut' , tipo=". TIPO . ", proveedor=$prov , genero = '". $_POST['genero'] ."'";
	$result = mysql_query($sql, $db)or die("Error creando el contenido - " . mysql_error($db) );	
	$newId= mysql_insert_id($db);
	if( $newId ){
		print "<h4>id: $newId - nombre: $nom</h4>";
	}else{
		error("error en : $sql", true);
		return false;
	}
	//
	$sql = "INSERT INTO contcol_whitelist set contenido=$newId ;";
	$result = mysql_query($sql, $db)or die("Error insertando el contenido en la whitelist - " . mysql_error($db) );	
	if( $result ){
		print "Contenido insertado en whitelist: $newId\n";
		return $newId;
	}else{
		error("error en : $sql", true);
		return false;
	}
}
//====================CREAR LAS IMÁGENES==========================
function crearImagenes($id){
	extract($GLOBALS);
	$w = new Wallpaper();
	foreach( $crops as $crop ){
		$aux1 = split("@", $crop);
		$aux2 = split("x", $aux1[0]);
		$destino = $aux1[2];
		$formatoImagen = $aux1[1];
		$ancho = $aux2[0];
		$alto = $aux2[1]; 
		$destino = $aux1[0] . "@" . $destino;
		$colorFondo = $_SESSION['color'];
		$imgtmp = $_FILES['imagen']['tmp_name'];
		
		//print("destino: $destino  - formatoImagen: $formatoImagen - ancho: $ancho - alto: $alto<br />");
		//continue;
		//================ los formatos especiales (jpg con palabra demo, q no pesen más de x bytes etc....)
		switch($crop){

			case "96x64_T@gif@241"://---- no debe pesar más de 3000 bytes
			$archivo = $w->crear("imagen", $id, $ancho, $alto, $colorFondo, $formatoImagen,  3000,128,'');
			$paraBorrar = eregi_replace(".gif",".jpg", $archivo);
			$img_final = "./wallpapers_tmp/" . $id . "." . $formatoImagen;
			if( !copy($archivo, $img_final) ){
				$msg = "no se pudo copiar la imagen recién creada: $archivo";
				print("$msg<br />");
				return false;
				error($msg, true);
			}else{
				ftpFiles($id, $img_final, $destino);
				@unlink($paraBorrar);
			}
			break;
			
			case "128x128_z@gif@241"://---- no debe pesar más de 3000 bytes
			$archivo = $w->crear("imagen", $id, $ancho, $alto, $colorFondo, $formatoImagen,  30000, 256,'');
			$paraBorrar = eregi_replace(".gif",".jpg", $archivo);
			$img_final = "./wallpapers_tmp/" . $id . "." . $formatoImagen;
			if( !copy($archivo, $img_final) ){
				$msg = "no se pudo copiar la imagen recién creada: $archivo";
				print("$msg<br />");
				return false;
				error($msg, true);
			}else{
				ftpFiles($id, $img_final, $destino);
				@unlink($paraBorrar);
			}
			break;
			
			case "256x256@jpg@USA":
			$archivo = $w->crear("imagen", $id, $ancho, $alto, $colorFondo, $formatoImagen,  0, 0,'watermark/256x256.png');
			$paraBorrar = eregi_replace("jpg","gif", $archivo);
			$img_final = "./wallpapers_tmp/" . $id . "." . $formatoImagen;
			if( !copy($archivo, $img_final) ){
				$msg = "no se pudo copiar la imagen recién creada: $archivo";
				print("$msg<br />");
				return false;
				error($msg, true);
			}else{
				ftpFiles($id, $img_final, $destino);
				@unlink($paraBorrar);
			}
			break;
			
			case "120x82@jpg@USA"://---- no debe pesar más de 3000 bytes
			$archivo = $w->crear("imagen", $id, $ancho, $alto, $colorFondo, $formatoImagen,  0, 0,'watermark/120.png');
			$paraBorrar = eregi_replace(".jpg",".gif", $archivo);
			$img_final = "./wallpapers_tmp/" . $id . "." . $formatoImagen;
			if( !copy($archivo, $img_final) ){
				$msg = "no se pudo copiar la imagen recién creada: $archivo";
				print("$msg<br />");
				return false;
				error($msg, true);
			}else{
				ftpFiles($id, $img_final, $destino);
				@unlink($paraBorrar);
			}
			break;
		
			default:
			$archivo = $w->crear("imagen", $id, $ancho, $alto, $colorFondo, $formatoImagen );
			$paraBorrar = eregi_replace(".gif",".jpg", $archivo);
			$img_final = "./wallpapers_tmp/" . $id . "." . $formatoImagen;
			if( !copy($archivo, $img_final) ){
				$msg = "no se pudo copiar la imagen recién creada: $archivo";
				print("$msg<br />");
				return false;
				error($msg, true);
			}else{
				ftpFiles($id, $img_final, $destino);
				@unlink($paraBorrar);
			}
			break;
		}
	}
	return "$img_final#$destino";
}
//====================SUBIR IMAGENES AL FTP==========================
function ftpFiles($id, $archivo, $destino){
	//die("ftpFiles: $id - $archivo -  $destino<br />");
	extract($GLOBALS);
	$aux = split("@", $destino);
	$dir_crops = $aux[0] . "/";
	$server = $aux[1];
	$dir_id = calcularCarpeta($id) . "/";
	$auxFile = pathinfo($archivo);
	$localFile = $auxFile["basename"];
	
	switch($server){
		case "USA": $dir_final = DIR_WP_USA;break;		
		case "241": $dir_final = DIR_WP_241;	break;
	}
	
	$remoteFilePath = "$dir_final$dir_crops$dir_id$localFile";
	print "$dir_crops - $localFile creado!<br />";
	//------------------ftp login---------------------------------------------------------------------------------
	$ftp = new Ftp($servers[$server][0], $servers[$server][1], $servers[$server][2]);
	if( !$ftp->login() ){
		error("No se pudo conectar al FTP: $server", false);
		limpiarCagadas($newId);
	};
	
	if( !$ftp->cambiarAcarpeta($dir_final . $dir_crops ) ){
		if( !$ftp->crearCarpeta( $dir_final . $dir_crops ) ){
			error("No se pudo crear la carpeta: $dir_final . $dir_crops", false);
			limpiarCagadas($newId);
			die();
		}
		$ftp->cambiarAcarpeta($dir_final . $dir_crops);
	}
		
	if( !$ftp->cambiarAcarpeta( $dir_id ) ){
		if( !$ftp->crearCarpeta( $dir_id ) ){
			limpiarCagadas($newId);
			die("No se pudo crear la carpeta: " . $dir_final . $dir_crops . $dir_id);
		}
		$ftp->cambiarAcarpeta( $dir_id );
	}
	
	if( !$ftp->subir( $archivo, $localFile) ){
		print "no se pudo subir ($archivo a $remoteFilePath en $server)\n";
		error("No se pudo subir $archivo a $remoteFilePath en $server", false);
		limpiarCagadas($newId);
		die();
	}else{
		@unlink($archivo);
		print $localFile . " subido a: $server!!<br />";
	}

	//$ftp->logout();
	//print "$remoteFilePath@$server@$destino <br />";
	$_SESSION['subidos'][] = "$remoteFilePath@$server@$destino";
}
//====================ACTUALIZAR TABLAS==========================
function updateContenidos($newId){	
	$db_archivo = "/netuy/wp/128x128/". calcularCarpeta($newId) . "/" . $newId . ".gif";
	$db_referencia = "/netuy/wp/62x62/". calcularCarpeta($newId) . "/" . $newId . ".gif";
	//die("db_archivo: $db_archivo -- db_referencia: $db_referencia");
	print "\n<b>Completando registros en base de datos.</b><br />";
	extract($GLOBALS);
	$sql = "UPDATE contenidos SET archivo='". $db_archivo ."' , referencia='". $db_referencia ."' WHERE id=$newId  LIMIT 1; ";
	//print $sql . "<br />";
	$result = mysql_query($sql, $db);
	if( !$result ){
		print("Error insertando archivo y referencia en el contenido $newId- " . mysql_error($db) );
		limpiarCagadas($newId);
		return false;
	}	
	$portales = array();
	$sql = "UPDATE Web.contcol_whitelist SET  ";
	foreach( $_SESSION['portales'] as $portal ){
		$portales[] = $portal . "=1";		
	}
	$sql .= implode(",", $portales) ;
	$sql .=" WHERE contenido=$newId LIMIT 1; ";
	//print $sql . "<br />";
	$result = mysql_query($sql, $db)or die("Error publicando en whitelist - " . mysql_error($db) );
	if( !$result ){
		print("Error publicando en whitelist - " . mysql_error($db));
		limpiarCagadas($newId);
		return false;
	}
	print "\nContenido publicado en:\n";
	foreach( $_SESSION['portales'] as $port){
		print "$port\n";
	}
	return true;
}
 //====================NO DEJAR RASTRO EN CASO DE ERROR==========================
function limpiarCagadas($newId){
	extract($GLOBALS);
	print "<h4>Borrando archivos:</h4>";
	foreach( $_SESSION['subidos'] as $k ){
		$auxSvr = split("@", $k);
		$file = $auxSvr[0];
		$srvr = $auxSvr[1];
		print "<h4>Borrando registros</h4>";
		print "borrando: $file<br />";
		
		
		$ftp = new Ftp($servers[$srvr][0], $servers[$srvr][1], $servers[$srvr][2]);
		if( !$ftp->login() )die("no se logueó");
		if( !$ftp->borrar($file) ){
			print "No se pudo borrar: $file<br />";
			error("No se pudo borrar: $file", true);
		}
	}
	
	$sql =" DELETE FROM contenidos where id=$newId LIMIT 1; ";
	$result = mysql_query($sql, $db);	
	if(!$result){
		error("Error al borrar el contenido $newId -  sql: $sql" , true);
	}
	print "contenido $newId eliminado!\n";
	$sql =" DELETE FROM contcol_whitelist where contenido=$newId LIMIT 1; ";

	$result = mysql_query($sql, $db);	
	if(!$result){
		error("Error al borrar de witelist $newId -  sql: $sql" , true);
	}
	print "contenido $newId eliminado de whitelist!\n";
	$ftp->logout();
}
//======== ejecutar todo
principal();



?>