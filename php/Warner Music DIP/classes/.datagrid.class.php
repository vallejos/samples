<?php

/**
 * data Grid v.0.1
 * by kmS ;)
 *
 * History:
 * v0.1 - 15.dec.2009
 *
 */

//
// row 0 y col 0 son reservadas internamente, los datos arrancan en 1,1
//
//
//
//


class dataGrid {
	private $idGrid; // id unico de grid
	private $currentRow; // row donde estoy parado
	private $currentCol; // col donde estoy parado
	private $maxRows; // nro. max. de rows
	private $maxCols; // nro. max. de cols
	private $rows;

	// default constructor
	function __construct() {
		$this->idGrid = $this->genId();
		$this->maxRows = 0;
		$this->maxCols = 0;
		$this->currentRow = 0;
		$this->currentCol = 0;
		$this->rows = Array(
			"cols" => array()
		);
	}


	// add/load a row
	public function add($data) {
		$newRow = $this->addRow();


		foreach ($data as $dn => $d) {
			$this-
		}
	}





/////////////
// PRIVATE //
/////////////




	// default destructor
	function __destruct() {

	}


	private function addRow($pos, $cels) {
		$this->maxRows++;
		$this->grid["data"][] = array();
		return $this->maxRows;
	}

	private function addCol($pos, $cels) {

	}


	private function genId() {
		list($usec, $sec) = explode(" ", microtime());
		return str_replace(".","", $sec.$usec);
	}




}



?>