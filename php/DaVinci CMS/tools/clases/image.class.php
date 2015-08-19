<?php

/**
 * Class that uses GD library to maintain and make various operations to an image
 *
 * @author Marcos Luiz Cassarini Taranta <marcos.taranta@gmail.com>
 * @since 2009-01-20
 * @package view
 */

define("GIF", "image/gif");
define("JPEG", "image/jpeg");
define("PNG", "image/png");

class Image {
	private $handler; // GD handler of the image
	private $height; // Height of the image
	private $mimetype; // Mimetype of the image
	public static $allowedMimetypes = array(
		"image/gif", "image/jpeg", "image/png", 
	); // Allowed mimetypes
	private $width; // Width of the image
	
	/**
	 * @return the GD image handler to the image
	 */
	public function getHandler() {
		return $this->handler;
	}
	
	/**
	 * @return the image current height
	 */
	public function getHeight() {
		return $this->height;
	}
	
	/**
	 * @return the image mimetype
	 */
	public function getMimetype() {
		return $this->mimetype;
	}
	
	/**
	 * @return the image current width
	 */
	public function getWidth() {
		return $this->width;
	}
	
	/**
	 * Sets the handler of the image file from the filename or create a new image from the width and height
	 *
	 * @param string $filename filename to the existing image file
	 * @param integer $width width of the new image
	 * @param integer $height height of the new image
	 */
	function __construct($filename = "", $width = 0, $height = 0) {
		if((empty($filename) || gettype($filename) != "string") && ($width == 0 || gettype($width) != "integer") && ($height == 0 || gettype($height) != "integer")) {
			throw new Exception("Parámetro/s incorrectos");
			return;
		}
		
		if(!empty($filename)) {
			if(file_exists($filename)) {
				$fileExtension = explode(".", $filename);
				$fileExtension = $fileExtension[count($fileExtension) - 1];
				
				if(!empty($fileExtension)) {
					switch($fileExtension) {
						case "gif":
							$this->handler = imagecreatefromgif($filename);
							$this->mimetype = "image/gif";
							list($this->width, $this->height) = getimagesize($filename);
						break;
						case "jpeg":
						case "jpg":
							$this->handler = imagecreatefromjpeg($filename);
							$this->mimetype = "image/jpeg";
							list($this->width, $this->height) = getimagesize($filename);
						break;
						case "png":
							$this->handler = imagecreatefrompng($filename);
							$this->mimetype = "image/png";
							list($this->width, $this->height) = getimagesize($filename);
						break;
						default:
							unset($this->mimetype);
						break;
					}
				}
				else {
					throw new ErrorException("Tipo de archivo no permitido");
				}
			}
			else {
				throw new ErrorException("El archivo no existe");
			}
		}
		else {
			$this->handler = imagecreatetruecolor($width, $height);
			$this->widht = $width;
			$this->height = $height;
		}
		
		if(!isset($this->handler))
			throw new ErrorException("No se pudo crear la imágen, filename: $filename");
	}
	
	/**
	 * Only makes sure that the image will be destroyed to free the memory
	 */
	function __destruct() {
		if(isset($this->handler))
			imagedestroy($this->handler);
	}
	
	/**
	 * Output the object image, if a filename is especified the image will be saved, if dont, the image will be displayed
	 *
	 * @param string $type type of the output image, use GIF, JPEG or PNG constants
	 * @param string $filename filename of the image file to be saved
	 * @param integer $quality quality of the output image for JPEG need to be between 0~100, for PNG is used for compression, need to be between 0~9
	 */
	public function output($type = "", $filename = "", $quality = "") {
		if(!isset($this->handler)) {
			throw new ErrorException("Tipo de archivo incorrecto");//Invalid image handler
		}
		
		if((empty($type) || gettype($type) != "string") && !empty($this->mimetype) && gettype($this->mimetype) == "string")
			$type = $this->mimetype;
		
		if(!empty($filename) && gettype($filename) == "string") {
			switch($type) {
				case GIF:
					imagegif($this->handler, $filename);
				break;
				
				case JPEG:
					if(empty($quality) || gettype($quality) != "integer")
						$quality = 75;
					if($quality >= 0 && $quality <= 100) {
						imagejpeg($this->handler, $filename, $quality);
					}
					else {
						imagejpeg($this->handler, $filename);
					}
				break;
				
				case PNG:
					if(empty($quality) || gettype($quality) != "integer")
						$quality = 9;
					if($quality >= 0 && $quality <= 9) {
						imagepng($this->handler, $filename, $quality);
					}
					else {
						imagepng($this->handler, $filename);
					}
				break;
				
				default:
					throw new ErrorException("Invalid image output type");
				break;
			}
		}
		else {
			switch($type) {
				case GIF:
					imagegif($this->handler);
				break;
				
				case JPEG:
					if($quality >= 0 && $quality <= 100) {
						imagejpeg($this->handler, NULL, $quality);
					}
					else {
						imagejpeg($this->handler);
					}
				break;
				
				case PNG:
					if($quality >= 0 && $quality <= 9) {
						imagepng($this->handler, NULL, $quality);
					}
					else {
						imagepng($this->handler);
					}
				break;
				
				default:
					throw new ErrorException("Invalid image output type");
				break;
			}
		}
	}
	
	/**
	 * Simply resample the object image to the desired width and height without maintaining any proportion
	 *
	 * @param integer $width new width of the image
	 * @param integer $height new height of the image
	 */
	public function resample($width, $height) {
		if(!isset($this->handler)) {
			throw new ErrorException("Invalid image handler");
		}
		else if($this->width <= 0 || !isset($this->width) || gettype($this->width) != "integer") {
			throw new ErrorException("Invalid image width");
		}
		else if($this->height <= 0 || !isset($this->height) || gettype($this->height) != "integer") {
			throw new ErrorException("Invalid image height");
		}
		else if($width <= 0 || gettype($width) != "integer") {
			throw new ErrorException("Invalid new width");
		}
		else if($height <= 0 || gettype($height) != "integer") {
			throw new ErrorException("Invalid new height");
		}
		
		$resampledImage = imagecreatetruecolor($width, $height);
		imagecopyresampled($resampledImage, $this->handler, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
		if(!isset($resampledImage)) {
			throw new ErrorException("The image could not be resampled");
		}
		else {
			$this->handler = $resampledImage;
			$this->width = $width;
			$this->height = $height;
		}
	}
	
	/**
	 * Crops and resample the object image so it will have the proportion of the new width and height
	 *
	 * @param integer $width new width of the image
	 * @param integer $height new height of the image
	 */
	public function resampleCropProportion($width, $height) {
		if(!isset($this->handler)) {
			throw new ErrorException("Invalid image handler");
		}
		else if($this->width <= 0 || !isset($this->width) || gettype($this->width) != "integer") {
			throw new ErrorException("Invalid image width");
		}
		else if($this->height <= 0 || !isset($this->height) || gettype($this->height) != "integer") {
			throw new ErrorException("Invalid image height");
		}
		else if($width <= 0 || gettype($width) != "integer") {
			throw new ErrorException("Invalid new width");
		}
		else if($height <= 0 || gettype($height) != "integer") {
			throw new ErrorException("Invalid new height");
		}
		
		$resampledImage = imagecreatetruecolor($width, $height);
		if(($width / $height) < ($this->width / $this->height)) {
			$newWidth = floor($this->height * ($width / $height));
			$difference = $this->width - $newWidth;
			$x = floor($difference / 2);
			imagecopyresampled($resampledImage, $this->handler, 0, 0, $x, 0, $width, $height, $newWidth, $this->height);
		}
		else if(($width / $height) > ($this->width / $this->height)) {
			$newHeight = floor($this->width * ($height / $width));
			$difference = $this->height - $newHeight;
			$y = floor($difference / 2);
			imagecopyresampled($resampledImage, $this->handler, 0, 0, 0, $y, $width, $height, $this->width, $newHeight);
		}
		else {
			imagecopyresampled($resampledImage, $this->handler, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
		}
		
		if(!isset($resampledImage)) {
			throw new ErrorException("The image could not be resampled");
		}
		else {
			$this->handler = $resampledImage;
			$this->width = $width;
			$this->height = $height;
		}
	}
	
	/**
	 * Resample the object image to the new height maintaining the original proportion
	 *
	 * @param integer $height new height of the image
	 */
	public function resampleProportionHeight($height) {
		if(!isset($this->handler)) {
			throw new ErrorException("Invalid image handler");
		}
		else if($this->width <= 0 || !isset($this->width) || gettype($this->width) != "integer") {
			throw new ErrorException("Invalid image width");
		}
		else if($this->height <= 0 || !isset($this->height) || gettype($this->height) != "integer") {
			throw new ErrorException("Invalid image height");
		}
		else if($height <= 0 || gettype($height) != "integer") {
			throw new ErrorException("Invalid new height");
		}
		
		$newWidth = floor($height * ($this->width / $this->height));
		$resampledImage = imagecreatetruecolor($newWidth, $height);
		imagecopyresampled($resampledImage, $this->handler, 0, 0, 0, 0, $newWidth, $height, $this->width, $this->height);
		if(!isset($resampledImage)) {
			throw new ErrorException("The image could not be resampled");
		}
		else {
			$this->handler = $resampledImage;
			$this->width = $newWidth;
			$this->height = $height;
		}
	}
	
	/**
	 * Resample the object image to the new width maintaining the original proportion
	 *
	 * @param integer $width new width of the image
	 */
	public function resampleProportionWidth($width) {
		if(!isset($this->handler)) {
			throw new ErrorException("Invalid image handler");
		}
		else if($this->width <= 0 || !isset($this->width) || gettype($this->width) != "integer") {
			throw new ErrorException("Invalid image width");
		}
		else if($this->height <= 0 || !isset($this->height) || gettype($this->height) != "integer") {
			throw new ErrorException("Invalid image height");
		}
		else if($width <= 0 || gettype($width) != "integer") {
			throw new ErrorException("Invalid new width");
		}
		
		$newHeight = floor($width * ($this->height / $this->width));
		$resampledImage = imagecreatetruecolor($width, $newHeight);
		imagecopyresampled($resampledImage, $this->handler, 0, 0, 0, 0, $width, $newHeight, $this->width, $this->height);
		if(!isset($resampledImage)) {
			throw new ErrorException("The image could not be resampled");
		}
		else {
			$this->handler = $resampledImage;
			$this->width = $width;
			$this->height = $newHeight;
		}
	}
}
?>
