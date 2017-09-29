<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Asi_excel{
	
	public $filename;
	public $row;
	public $col;
	public $mStarted = false;

	function Asi_excel($filename="export") {
		$filename = rtrim($filename,".xls");
		$this->filename = $filename;
	}

	public function begin() { 
		if ($this->mStarted) return;
		//ob_end_clean();	//Added so that it will remove unwanted characters

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");;
		header("Content-Disposition: attachment;filename=".$this->filename.".xls "); 
		header("Content-Transfer-Encoding: binary ");
		header("Cache-control: private");
	 	header('Pragma: private');
	 	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	 

		echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
		$this->row = 0; 
		$this->col = 0; 
		$this->mStarted = true;
	} 
	
	public function setrow($row) {
		$this->row = $row;
		$this->col = 0;
	}
	
	public function setrowcol($row,$col) {
		$this->row = $row;
		$this->col = $col;
		
	}

	public function dump() { 
		echo pack("ss", 0x0A, 0x00); 
		exit(); 
	} 

	public function outputn($Row, $Col, $Value) { 
		echo pack("sssss", 0x203, 14, $Row, $Col, 0x0); 
		echo pack("d", $Value); 
	} 
	
	public function outputs($Row, $Col, $Value ) { 
		$L = strlen($Value); 
		echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L); 
		echo $Value; 
	} 
	
	public function sets($value) {
		$this->outputs($this->row,$this->col,$value);
		$this->col++;
	}


	public function setn($value) {
		$this->outputn($this->row,$this->col,preg_replace('/[\$,]/', '', $value));
		$this->col++;
	}

	public function set($value,$type="s") {

		if (is_array($value)) {
			foreach($value as $data) {
				if ($type != "s") 
					$this->setn($data);
				else
					$this->sets($data);
			}
			
		} else {
			if ($type != "s") 
				$this->setn($value);
			else
				$this->sets($value);
		}
	}


	//Compatibility with PDF
	public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
		$this->sets($txt);
	}

	public function Celln($w, $h=0, $txt=0, $border=0, $ln=0, $align='', $fill=false, $link='') {
		$this->setn($txt);
	}
	
	public function Title($title) {
		$this->begin();
		$this->sets($title);
		$this->Ln();
		$this->Ln();
	}
	
	public function Ln($h=null) {
		$this->row ++;
		$this->col = 0;
	}
}
