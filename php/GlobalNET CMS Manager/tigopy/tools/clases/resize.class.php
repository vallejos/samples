<?PHP

class ResizeJpg{

	protected $altoMax = 82;
	protected $anchoMax = 120;
	protected $compresion = 75;
	protected $imgDest = null;
	protected $imgOrig = null;
	protected $imgWatermark =  null;
	protected $watermarkHeight = null;
	protected $watermarkWidth = null;
	protected $watermarkTop = 0;
	protected $watermarkLeft = 0;
	protected $ancho = null;
	protected $alto = null;
	protected $imgOrigPath = null;

	public function getAltoMax(){
		return $this->altoMax;
	}

	public function getAnchoMax(){
		return $this->anchoMax;
	}

	public function getCompresion(){
		return $this->compresion;
	}

	public function getAlto(){
		return $this->alto;
	}

	public function getAncho(){
		return $this->ancho;
	}

	public function getWatermarkHeight()
	{
		return $this->watermarkHeight;
	}
	
	public function getWatermarkWidth()
	{
		return $this->watermarkWidth;
	}

	public function setCompresion($compresion){
		$this->compresion = $compresion;
	}

	public function setAltoMax($valor){
		$this->altoMax = $valor;
	}

	public function setAnchoMax($valor){
		$this->anchoMax = $valor;
	}
	
	public function setWatermarkPos($top, $left)
	{
		$this->watermarkTop = $top;
		$this->watermarkLeft = $left;
	}

	public function loadWatermark($watermarkPath)
	{
		$this->imgWatermark = imagecreatefrompng($watermarkPath) or die("no se pudo crear el png");
		list($this->watermarkWidth, $this->watermarkHeight)= getimagesize($watermarkPath);
	}
	
	//carga la imagen original en imgOrig, hace los calculos para setear el Alto y el Ancho del destino segun alotMax y anchoMax
	public function loadImage($filePath){
	
		$this->imgOrigPath = $filePath;

		 list($ancho, $alto)= getimagesize($this->imgOrigPath);

		if($alto>$ancho){
			$divisor = $this->altoMax / $alto;
		}else{
			$divisor = $this->anchoMax / $ancho;
		}
		//
		if($ancho > $this->anchoMax || $alto > $this->altoMax){
			$this->ancho = $ancho * $divisor;
			$this->alto = $alto * $divisor;
		}
		$this->imgOrig=ImageCreateFromJPEG($this->imgOrigPath) or die('Problema leyendo la imagen subida');
	}

	public function reloadImage(){
		$this->loadImage($this->imgOrigPath);
	}


	public function process(){

		$this->imgDest=ImageCreateTrueColor( intval($this->anchoMax), intval($this->altoMax) ) or die('Problema en la creaci�n de la imgen :(');
		
		//print ( "imagen de origen: " . file_exists($this->imgOrig) ) . "<br />";
		
		imagecopyresampled($this->imgDest, $this->imgOrig,0,0,0,0,$this->anchoMax,$this->altoMax, ImageSX($this->imgOrig),ImageSY($this->imgOrig)) or die('Problema redimensionando imagen.');
		
		if($this->imgWatermark != null){
			imagecopy($this->imgDest, $this->imgWatermark, $this->watermarkLeft, $this->watermarkTop,  0, 0, $this->watermarkWidth, $this->watermarkHeight) or die("la mezcla sali� pal orto");
		}
	}

	public function writeJpg($targetPath){
		imagejpeg($this->imgDest, $targetPath, $this->compresion);
	}
}

?>