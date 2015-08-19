<?php

define("TMPDIR", "wallpapers_tmp");
define("CALIDAD",75);

class convert {
	public $inBasename;//----nombre y extensi�n del archivo de origen
	public $inFilename;//----filename (nombre sin la extensi�n) del archivo de origen
	public $inExt;//----extension del archivo de origen
	public $inDir;//---directorio del archivo de origen
	public $inPath;//---directorio del archivo de origen
	public $inFormat;//----mimetype del del archivo de orig
	public $inWidth;//---ancho del archivo de origen
	public $inHeight;//alto del archivo de origen
	public $outFilename;//----basename del archivo de salida
	public $outFormat;//----mimetype del del archivo de salida
	public $outWidth;//---ancho del archivo de salida
	public $outHeight;//----alto del archivo de salida
	private $nColors;//--- se usa en gifXcolors, va disminuyendo hasta alcanzar el peso o colores requeridos
	public $calidadJpg = CALIDAD;
	private $actualSize;
	private $sharp;
	private $jpgGifXcolors;
	private $tiposValidos = array("gif"=>"image/gif" , "jpg"=>"image/jpeg" , "png"=>"image/png" );


	public function convert( $inPath ,  $outFormat , $outWidth, $outHeight= false, $sharp = false ){
		$sharp ? $this->sharp = true : $this->sharp = false;
		$lienzo ? $this->lienzo= true : $this->lienzo = false;
		if( !file_exists($inPath) ) die ("El archivo de origen no existe. path:" . $inPath);
		if( !$this->checkFormatIN($inPath)  ) die("El archivo original deber�a ser 'GIF' ,'JPG' o 'PNG'");
		if( !$this->checkFormatOUT($outFormat)  ) die("El formato de salida deber�a ser 'GIF' ,'JPG' o 'PNG'");
		if(trim($outWidth)){
			if( !is_int($outWidth) || $outWidth < 2) die("El 3er par�metro 'ancho de salida' no es num�rico o es menor que 2 ");
			$this->outWidth = $outWidth;
		}else if(!$outWidth){
			print "else width";
			$this->outWidth = $this->inWidth;
		}
		if(trim($outHeight)){
			if( !is_int($outHeight) || $outHeight < 2) die("El 4to par�metro 'alto de salida' no es num�rico o es menor que 2 ");
			$this->outHeight = $outHeight;
		}else if(!$outHeight){
			$this->outHeight = $this->inHeight;
		}
		$this->inPath = $inPath;
		$auxPath = pathinfo($this->inPath);
		 $this->inDir = $auxPath['dirname'];
		 $this->inBasename = $auxPath['basename'];
		 $this->inFilename = $auxPath['filename'];
		 $this->inExt = $auxPath['extension'];
	}

	private function checkFormatIN($inPath){
		list($this->inWidth, $this->inHeight, $auxFormat) = getimagesize($inPath) ;
		switch( $auxFormat ){
			case "1" : $this->inFormat = $this->tiposValidos["gif"];break;
			case "2" : $this->inFormat = $this->tiposValidos["jpg"];break;
			case "3" : $this->inFormat = $this->tiposValidos["png"];break;
		}
		return $this->inFormat;
	}

	private function checkFormatOUT($outFormat){
		$outFormat = strtolower($outFormat);
		if($outFormat=="jpeg")$outFormat="jpg";
		$formato = $this->tiposValidos[ $outFormat ];
		$this->outFormat = $formato;
		return $formato;
	}

	public function newIMG( ){
		$dest_img=imagecreatetruecolor( $this->outWidth,$this->outHeight ) or die('newIMG(): Problema en la creaci�n de la imgen nueva TRUECOLOR');
		switch($this->inFormat){
			case "image/gif":	$src_img=ImageCreateFromGIF($this->inPath) or die('Problema leyendo gif original');break;
			case "image/jpeg": $src_img=ImageCreateFromJPEG($this->inPath) or die('Problema leyendo jpg original');break;
			case "image/png": $src_img=ImageCreateFromPNG($this->inPath) or die('Problema leyendo png original');break;
		}
		imagecopyresampled($dest_img, $src_img,0,0,0,0,$this->outWidth,$this->outHeight,$this->inWidth, $this->inHeight)or die('Problema resampling');
		return $dest_img;
	}

	public function saveIMG_lienzo($path, $colorLienzo){
		list($r,$g,$b)=$this->hex2rgb($colorLienzo);
		$anchoMax = $this->outWidth;
		$altoMax = $this->outHeight;
		$ancho = $this->inWidth;
		$alto = $this->inHeight;
		$divisor = min( $altoMax / $alto , $anchoMax / $ancho );
		if($ancho >= $anchoMax || $alto >= $altoMax){
			$ancho =  $ancho * $divisor;
			$alto =  $alto * $divisor;
		};
		$dst_x = ($anchoMax - $ancho) / 2;
		$dst_y = ($altoMax - $alto) / 2;
		$src_x = 0;
		$src_y = 0;

		if($ancho / 2 !== 0) $ancho=($ancho+1);
		if($alto / 2 !== 0) $alto=($alto+1);

		$dest_img=imagecreatetruecolor( $this->outWidth,$this->outHeight ) or die('newIMG(): Problema en la creaci�n de la imgen nueva TRUECOLOR');
		$dest_img = $this->newIMG( );


		$bg = ImageColorAllocate($dest_img, $r, $g, $b);
		imagefilledrectangle($dest_img, 0, 0, $this->outWidth,$this->outHeight, $bg);
		switch($this->inFormat){
			case "image/gif":	$src_img=ImageCreateFromGIF($this->inPath) or die('Problema leyendo gif original');break;
			case "image/jpeg": $src_img=ImageCreateFromJPEG($this->inPath) or die('Problema leyendo jpg original');break;
			case "image/png": $src_img=ImageCreateFromPNG($this->inPath) or die('Problema leyendo png original');break;
		}
		imagecopyresampled( $dest_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $ancho, $alto, $this->inWidth, $this->inHeight )or die('Problema resampling');
			//---------------------------------------------------UNSHARP MASK-------------------------------------------
			if($this->sharp){
				$dest_img = $this->UnsharpMask($dest_img, 50, 1, 1);
			}
			switch($this->outFormat){
			case "image/gif": if( !imagegif($dest_img, $path) )die("Problema guardando la IMG gif en: $path");break;
			case "image/jpeg": if( !imagejpeg($dest_img, $path, $this->calidadJpg) )die("Problema guardando la IMG jpg en: $path");break;
			case "image/png": if( !imagepng($dest_img, $path) )die("Problema guardando la IMG png en: $path");break;
		}
		return true;
	}

	public function saveIMG($outPath){
		$dest_img = $this->newIMG( );
		//---------------------------------------------------UNSHARP MASK-------------------------------------------
		if($this->sharp){
			$dest_img = $this->UnsharpMask($dest_img, 50, 1, 1);
		}
		switch($this->outFormat){
			case "image/gif": if( !imagegif($dest_img, $outPath) )die("Problema guardando la IMG gif en: $outPath");break;
			case "image/jpeg": if( !imagejpeg($dest_img, $outPath, $this->calidadJpg) )die("Problema guardando la IMG jpg en: $outPath");break;
			case "image/png": if( !imagepng($dest_img, $outPath) )die("Problema guardando la IMG png en: $outPath");break;
		}
		return true;
	}

	public function pngWatermark($wtFile, $outPath){
		$dest_img=imagecreatetruecolor( $this->outWidth,$this->outHeight ) or die('pngWatermark(): imagecreatetruecolor');
		switch($this->inFormat){
			case "image/gif":	$src_img=ImageCreateFromGIF($this->inPath) or die('pngWatermark(): ImageCreateFromGIF');break;
			case "image/jpeg": $src_img=ImageCreateFromJPEG($this->inPath) or die('pngWatermark(): ImageCreateFromJPG');break;
			case "image/png": $src_img=ImageCreateFromPNG($this->inPath) or die('pngWatermark(): ImageCreateFromPNG');break;
		}
		imagecopyresampled($dest_img, $src_img,0,0,0,0,$this->outWidth,$this->outHeight,$this->inWidth, $this->inHeight)or die('pngWatermark(): imagecopyresampled');
		$src_png=ImageCreateFromPNG($wtFile) or die('pngWatermark(): ImageCreateFromPNG [src_png]');
		imagecopyresampled($dest_img, $src_png, 0, 0, 0, 0, $this->outWidth,$this->outHeight,$this->outWidth,$this->outHeight) or die("la mezcla watermark sali� pal orto");
		if( !imagejpeg($dest_img, $outPath, 80) )die("pngWatermark(): imagejpeg");
	}

	public function gifXcolors( $outPath /*rutacompleta de salida*/ ,$maxFileSize /*bytes*/, $initColors /*INT*/ ){
		$this->jpgGifXcolors = $outPath. "." . $this->inExt;
		$this->saveIMG(  $this->jpgGifXcolors );
		$this->nColors = $initColors;
		$this->maxFileSize = $maxFileSize;
		$this->colorDecrease();
	}

	private function colorDecrease( ) {
		$gifFInal = TMPDIR . "/" . $this->outWidth ."x". $this->outHeight . "_T.gif";
		if( $this->inFormat == "image/jpeg" ){
			$src_img=ImageCreateFromJPEG($this->inPath) or die('colorDecrease: Problema leyendo jpg original');
		}else{
			$src_img=ImageCreateFromGIF($this->inPath) or die('colorDecrease: Problema leyendo gif original');
		}
		$dest_img=imagecreatetruecolor( $this->outWidth,$this->outHeight ) or die('colorDecrease: Problema en la creaci�n de la imgen nueva gif');
		imagecopy($dest_img, $src_img,0,0,0,0,$this->outWidth,$this->outHeight);
		ImageTrueColorToPalette( $dest_img, true, $this->nColors );
		ImageColorMatch( $src_img, $dest_img );
		if( !imagegif($dest_img,  $gifFInal ) )die("colorDecrease: Problema guardando la IMG gif en: $outPath");
		ImageDestroy( $dest_img );
		$this->actualSize = filesize($gifFInal);
		if($this->actualSize > $this->maxFileSize){
			$this->nColors--;
			if( unlink($gifFInal) ){
				$this->colorDecrease( );
			}
		}else{
			unlink($this->jpgGifXcolors);
		}
	}

	public function copyIMG(){
		$outPath = $this->inDir . "/" . $this->inFilename . "." . array_search($this->outFormat , $this->tiposValidos);
		$dest_img=imagecreatetruecolor( $this->inWidth,$this->inHeight ) or die('copyIMG(): TRUECOLOR');
		switch($this->inFormat){
			case "image/gif":	$src_img=ImageCreateFromGIF($this->inPath) or die('Problema leyendo gif original');break;
			case "image/jpeg": $src_img=ImageCreateFromJPEG($this->inPath) or die('Problema leyendo jpg original');break;
			case "image/png": $src_img=ImageCreateFromPNG($this->inPath) or die('Problema leyendo png original');break;
		}
		imagecopyresampled($dest_img, $src_img,0,0,0,0,$this->inWidth, $this->inHeight,$this->inWidth, $this->inHeight)or die('Problema resampling');
		switch($this->outFormat){
			case "image/gif": if( !imagegif($dest_img, $outPath) )die("Problema guardando la IMG gif en: $outPath");break;
			case "image/jpeg": if( !imagejpeg($dest_img, $outPath, $this->calidadJpg) )die("Problema guardando la IMG jpg en: $outPath");break;
			case "image/png": if( !imagepng($dest_img, $outPath) )die("Problema guardando la IMG png en: $outPath");break;
		}

	}


	private	function hex2rgb($hex) {
		$hex = eregi_replace("#|&H", "", $hex);
		$cutpoint = ceil(strlen($hex) / 2) - 1;
		$rgb = explode(':', wordwrap($hex, $cutpoint, ':', $cutpoint), 3);
		$rgb[0] = (isset($rgb[0]) ? hexdec($rgb[0]) : 0);
		$rgb[1] = (isset($rgb[1]) ? hexdec($rgb[1]) : 0);
		$rgb[2] = (isset($rgb[2]) ? hexdec($rgb[2]) : 0);
		return $rgb;
	}

	//----el que hizo esto la rompi�
	public function UnsharpMask($img, $amount, $radius, $threshold)    {

////////////////////////////////////////////////////////////////////////////////////////////////
////
////                  Unsharp Mask for PHP - version 2.1.1
////
////    Unsharp mask algorithm by Torstein H�nsi 2003-07.
////             thoensi_at_netcom_dot_no.
////               Please leave this notice.
////
///////////////////////////////////////////////////////////////////////////////////////////////


    // Attempt to calibrate the parameters to Photoshop:
    if ($amount > 500)    $amount = 500;
    $amount = $amount * 0.016;
    if ($radius > 50)    $radius = 50;
    $radius = $radius * 2;
    if ($threshold > 255)    $threshold = 255;

    $radius = abs(round($radius));     // Only integers make sense.
    if ($radius == 0) {
        return $img; imagedestroy($img); break;        }
    $w = imagesx($img); $h = imagesy($img);
    $imgCanvas = imagecreatetruecolor($w, $h);
    $imgBlur = imagecreatetruecolor($w, $h);


    // Gaussian blur matrix:
    //
    //    1    2    1
    //    2    4    2
    //    1    2    1
    //
    //////////////////////////////////////////////////


    if (function_exists('imageconvolution')) { // PHP >= 5.1
            $matrix = array(
            array( 1, 2, 1 ),
            array( 2, 4, 2 ),
            array( 1, 2, 1 )
        );
        imagecopy ($imgBlur, $img, 0, 0, 0, 0, $w, $h);
        imageconvolution($imgBlur, $matrix, 16, 0);
    }
    else {

    // Move copies of the image around one pixel at the time and merge them with weight
    // according to the matrix. The same matrix is simply repeated for higher radii.
        for ($i = 0; $i < $radius; $i++)    {
            imagecopy ($imgBlur, $img, 0, 0, 1, 0, $w - 1, $h); // left
            imagecopymerge ($imgBlur, $img, 1, 0, 0, 0, $w, $h, 50); // right
            imagecopymerge ($imgBlur, $img, 0, 0, 0, 0, $w, $h, 50); // center
            imagecopy ($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);

            imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 33.33333 ); // up
            imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 25); // down
        }
    }

    if($threshold>0){
        // Calculate the difference between the blurred pixels and the original
        // and set the pixels
        for ($x = 0; $x < $w-1; $x++)    { // each row
            for ($y = 0; $y < $h; $y++)    { // each pixel

                $rgbOrig = ImageColorAt($img, $x, $y);
                $rOrig = (($rgbOrig >> 16) & 0xFF);
                $gOrig = (($rgbOrig >> 8) & 0xFF);
                $bOrig = ($rgbOrig & 0xFF);

                $rgbBlur = ImageColorAt($imgBlur, $x, $y);

                $rBlur = (($rgbBlur >> 16) & 0xFF);
                $gBlur = (($rgbBlur >> 8) & 0xFF);
                $bBlur = ($rgbBlur & 0xFF);

                // When the masked pixels differ less from the original
                // than the threshold specifies, they are set to their original value.
                $rNew = (abs($rOrig - $rBlur) >= $threshold)
                    ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))
                    : $rOrig;
                $gNew = (abs($gOrig - $gBlur) >= $threshold)
                    ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))
                    : $gOrig;
                $bNew = (abs($bOrig - $bBlur) >= $threshold)
                    ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))
                    : $bOrig;


                if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
										$pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
										ImageSetPixel($img, $x, $y, $pixCol);
								}
            }
        }
    }
    else{
        for ($x = 0; $x < $w; $x++)    { // each row
            for ($y = 0; $y < $h; $y++)    { // each pixel
                $rgbOrig = ImageColorAt($img, $x, $y);
                $rOrig = (($rgbOrig >> 16) & 0xFF);
                $gOrig = (($rgbOrig >> 8) & 0xFF);
                $bOrig = ($rgbOrig & 0xFF);

                $rgbBlur = ImageColorAt($imgBlur, $x, $y);

                $rBlur = (($rgbBlur >> 16) & 0xFF);
                $gBlur = (($rgbBlur >> 8) & 0xFF);
                $bBlur = ($rgbBlur & 0xFF);

                $rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;
                    if($rNew>255){$rNew=255;}
                    elseif($rNew<0){$rNew=0;}
                $gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
                    if($gNew>255){$gNew=255;}
                    elseif($gNew<0){$gNew=0;}
                $bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;
                    if($bNew>255){$bNew=255;}
                    elseif($bNew<0){$bNew=0;}
                $rgbNew = ($rNew << 16) + ($gNew <<8) + $bNew;
                    ImageSetPixel($img, $x, $y, $rgbNew);
            }
        }
    }
    imagedestroy($imgCanvas);
    imagedestroy($imgBlur);
    return $img;
	}

}

