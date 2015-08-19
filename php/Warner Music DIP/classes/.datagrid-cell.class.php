<?php


class datagridCell {
	private $idCell;
	private $cssStyle;
	private $content;
	private $row;
	private $col;


	// default constructor
	function __construct($row, $col) {
		$this->row = $row;
		$this->col = $col;
	}


	// default destructor
	function __destruct() {

	}


	public function setStyle($css) {
		$this->cssStyle = $css;
	}


	public function setContent($data) {
		$this->content = $data;
	}

	public function getId() {
		return $this->idCell;
	}



}



?>