<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
define('FPDF_FONTPATH',BASEPATH.'/fonts/font/');
require_once 'fpdf.php';
require_once 'Asi_excel.php';

class Asi_pdf extends FPDF{
	
	public $xls;
	public $excel = false;
	public $outputfile ="";
	public $subheader = "";
	public $subheader_require = "";
	public $filter = "";
	public $caller;
	
	public $display_header = true;
	public $display_footer = true;
	
	public $old_x;
	public $old_y;
	
	public $params;
	public $autoFill = false;
	
    function Asi_pdf($orientation='P', $unit='mm', $format='A4') {
        parent::fpdf($orientation,$unit,$format);
		$this->xls = new Asi_excel();
		$this->SetFillColor(255,255,255);
		$CI =& get_instance();
		$CI->load->helper('date');
    }
    
	public function savePosition() {
		$this->old_x = $this->x;
		$this->old_y = $this->y;
	}
	
	public function restorePosition() {
		$this->x = $this->old_x;
		$this->y = $this->old_y;
	}
	
	public function getTempFilename($prefix = "file") {
		return $prefix."-".$_SERVER["REMOTE_ADDR"]."-".$_SERVER["REMOTE_PORT"]."-".$_SERVER["REQUEST_TIME"];
	}
	
	public function getURL($filename = "") {
		//!!! TODO: https or http SERVER_PROTOCOL
		$filename = strtolower($filename);
		if ($filename == "") {
			if ($this->outputfile == "") 
				$this->outputfile = $this->getTempFilename()."pdf";
			$filename = $this->outputfile;
		} 
		$filename = rtrim($filename,'.pdf').".pdf";
		$this->outputfile = $filename;

		//echo "http://".$_SERVER["HTTP_HOST"].'CodeIgniter'."/public/$filename";
		
		return "http://".$_SERVER["HTTP_HOST"]."/public/$filename";
	}
	
	public function Header() {
		if ($this->display_header) { 
			$this->SetFillColor(255,255,255);
			$this->SetFont('Arial','B',14);
			$this->Cell(200,5,'Company Name');
			$this->Ln();
			$this->SetFont('Arial','',8);
			$this->Cell(200,5,'Company Address');
			$this->Ln();
			$this->SetFont('Arial','B',10);
			$this->Cell(200,5,$this->title);
			$this->Ln();
	
			if ($this->filter != "") {
				$this->SetFont('Arial','',8);
				$this->Cell(200,5,"Filter: ".$this->filter);
				$this->Ln();
			} 
			$this->Ln();
		}

		//This is version 5.2xx, deprecated in v5.3
		if (PHP_VERSION < '5.2.6') {
			if ($this->caller && $this->subheader != "")
				call_user_method("subheader", $this->caller, $this);
		}
		else {
			if ($this->subheader != "")
				call_user_func_array($this->subheader, array(&$this)); 
		}
		$this->setXY($this->x,$this->y + 1);
	}
	
	public function Footer() {
		if (!$this->display_footer) return;
		$this->SetFillColor(255,255,255);
		
		$this->SetFont('Arial','',6);
		if ($this->y < $this->PageBreakTrigger) 
			$this->setY($this->PageBreakTrigger);
		$this->Ln();
		$this->Cell(200,5,"PRINTED: ".'test user'." ".standard_date('DATE_ISO8601', time())); 
		$this->Cell($this->w - $this->rMargin - 200 - $this->lMargin, 5,"Page ".$this->PageNo(),0,0,'R');
	}
	
	public function createHeaderBorder($w,$h) {
		
		if ($this->excel) return;
		$x = $this->x;
		$y = $this->y;

		$this->SetFillColor(225,225,225);
		$this->setXY($x-1,$y-1);
		$this->Cell($w + 3,$h + 1,"",1,0,'',true);
		$this->setXY($x,$y);
	}
    
	public function AddPage() {
		//Trap for excel operation
		if ($this->excel) {
			$this->xls->filename = $this->getTempFilename();
			$this->xls->begin();
			$this->Header();
			return;
		}
		parent::AddPage();
		
	}

	public function dump() {
		if ($this->excel) {
			$this->xls->dump();
			return;
		}
		
		$this->Output("public/".$this->outputfile,"D");
	}
	
	public function isNumber($s) {
		if ($s == "") return false;
		$s = preg_replace('/[\$,]/', '', $s);
		return is_numeric($s);
		
		$s = trim($s, '+-.,0123456789');
		return $s == "";
	}
	
	public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=true, $link='') {
		
		if ($fill == null) $fill = $this->autoFill;
		if ($this->excel) {
			if ($align == "R" && $this->isNumber($txt)) 
				$this->xls->Celln($w,$h,$txt,$border,$ln,$align,$fill,$link);
			else
				$this->xls->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
			return;
		}
		parent::Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
	}
   
	public function Cells($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=true, $link='') {

		if ($this->excel) {
			$this->xls->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
			return;
		}
		parent::Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
	}

	public function Celln($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=true, $link='') {
		if ($this->excel) {
			$this->xls->Celln($w,$h,$txt,$border,$ln,$align,$fill,$link);
			return;
		}
		parent::Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
	}


	public function Ln($h=null) {
		if ($this->excel) {
			$this->xls->Ln();
			return;
		}
		parent::Ln($h);
		
	}
}