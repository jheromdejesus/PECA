<?php 
define('FPDF_FONTPATH',BASEPATH.'/fonts/font/');
require 'fpdf.php';

class Asi_pdf_ext 
{
	
	const LAYOUT_LANDSCAPE	= "landscape";
	const LAYOUT_PORTRAIT 	= "portrait";
	
	var $page_layout = Asi_pdf_ext::LAYOUT_LANDSCAPE;
	var $pdf_obj 	= null;
	var $row_height = .18; 	
	var $top_margin = .75;
	var $col_start = 0;
	var $y_ordinate = 0;
	var $data_count = 0;
	var $i 			= 0;
	var $x = 0;
	var $cellfit = '';
	
	//header variables
	var $report_title = "";
	var $report_date  = "";
	var $report_id	  = "";
	var $run_date	  = "";
	var $mode		  = "normal";
	var $hmode		  = "normal";
	
	var $headers = array();
	var $headers2 = array();
	var $headers3 = array();
	var $data = array();
	var $width = array();
	var $align = array();
	var $align2 = array();
	var $align3 = array();
	var $fit = array();
	
	var $row_count = 35;
	var $subheader_title = "";
	var $subheader_start_col = 1.5;
	var $subheader_title_every_page = false;
	var $subheader_accountsummary_every_page = false;
	var $account_name = "";
	var $image = "images/printable_loan_watermark.gif";
	var $has_watermark = false;
	
	function Asi_pdf_ext() 
	{
	}
	
	/**
	 * @param $layout
	 * @param $left_margin
	 * 
	 */
	function init($layout = "landscape", $left_margin = .7, $top_margin=.75) 
	{
		$this->page_layout = $layout;
		if($layout == Asi_pdf_ext::LAYOUT_PORTRAIT){
			$this->row_count = 50;
		}
		$this->pdf_obj = new FPDF($this->page_layout, $unit="in", $paper_size="A4");
		$this->pdf_obj->SetMargins($left_margin,$top_margin);
		//$this->pdf_obj->SetAutoPageBreak('on',3);
		 $this->HREF='';
	}
	
	/**
	 * @param String $report_title
	 * @param String $report_date
	 * @param String $report_id
	 */
	function writeHeaderInfo($report_title, $report_date=null, $report_id, $report_range=null) 
	{
		$run_date = date("F j, Y H:i:s");
		$this->run_date = $run_date;
		$this->report_title = $report_title;
		$this->report_date = $report_date;
		$this->report_id = $report_id;
		$this->report_range = $report_range;
		$this->pdf_obj->AliasNbPages();
		$this->pdf_obj->AddPage();
		$this->Header();
	}
	
	function Header() 
	{
		$this->pdf_obj->SetFont('Arial','B',9);
		$this->pdf_obj->Cell(23*$this->row_height,$this->row_height,"PECA Savings and Loans Monitoring System");
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(14*$this->row_height,$this->row_height,"Report ID: ".$this->report_id);
		$this->Ln();
		
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(23*$this->row_height,$this->row_height,$this->report_title);
		$this->pdf_obj->Cell(14*$this->row_height,$this->row_height,"Run Date: ".$this->run_date);
		$this->Ln();
		
		if ($this->report_date!=null) {
			$this->pdf_obj->SetFont('Arial','',9);
			$this->pdf_obj->Cell(23*$this->row_height,$this->row_height,$this->report_date);
			$this->Ln();
		}
		
		if ($this->report_range!=null) {
			$this->pdf_obj->SetFont('Arial','',9);
			$this->pdf_obj->Cell(23*$this->row_height,$this->row_height,$this->report_range);
			$this->Ln();
		}
		
		$this->Ln();
		if($this->has_watermark){
			$this->pdf_obj->Image("images/printable_loan_watermark.gif",2,2,4,2);
		}
	}
	
	function writeVoucherHeaderInfo($report_title, $report_date, $no,$report_range=null)
	{
		$this->report_title = $report_title;
		$this->report_date = $report_date;
		$this->report_range = $report_range;
		$this->pdf_obj->AliasNbPages();
		$this->pdf_obj->AddPage();
		$this->VoucherHeader($no);
	}
	
	function VoucherHeader($no='') 
	{
		$this->pdf_obj->SetFont('Arial','B',9);
		$this->pdf_obj->Cell(0,0,"SAVINGS & LOAN ASSOCIATION OF P & GP EMPLOYEES, INC.","","","C",false);
		$this->Ln(.5);
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(0,0,$this->report_title,"","","C",false);
		$this->Ln(.5);
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(27*$this->row_height,$this->row_height,"Pay to:");
		$this->pdf_obj->Cell(2*$this->row_height,$this->row_height,"No:");
		$this->pdf_obj->Cell(3*$this->row_height,$this->row_height,$no);
		$this->Ln();
		$this->pdf_obj->Cell(27*$this->row_height,$this->row_height,"Address:");
		$this->pdf_obj->Cell(2*$this->row_height,$this->row_height,"Date:");
		$this->pdf_obj->Cell(5*$this->row_height,$this->row_height,$this->report_date);
		$this->Ln(.5);
	}
	
	/**
	 * @param array $headers
	 * @param float $col_start
	 * @param float $row_height
	 * @param float $row_start
	 * 
	 */
	function writeSubheader($headers, $alignment=null, $col_start = .7, $row_height = null, $row_start = 1.47, $cellfit=null) 
	{
		if ($row_height != null){
			$this->row_height = $row_height;
		}	
		$this->headers = $headers;
		$this->col_start = $col_start;
		$this->align = $alignment;
		$this->fit = $cellfit;
		$this->Subheader();
	}
	
	function Subheader() 
	{
		$this->pdf_obj->SetFillColor(190,190,190);
		$this->pdf_obj->SetFont('Arial','B',7);
		$this->pdf_obj->SetX($this->col_start);
		foreach($this->headers AS $label => $width) {
			if ($this->align==null) {
				if($this->fit==null)
					$this->pdf_obj->Cell($width*$this->row_height,$this->row_height,$label,'','','C',true);
				else
					$this->pdf_obj->CellFitScale($width*$this->row_height,$this->row_height,$label,'','','C',true);			}
			else {
				if($this->fit==null)
					$this->pdf_obj->Cell($width*$this->row_height,$this->row_height,$label,'','',$this->align[$label],true);
				else
					$this->pdf_obj->CellFitScale($width*$this->row_height,$this->row_height,$label,'','',$this->align[$label],true);
			}	
		}
		$this->Ln();
	}
	
	/**
	 * @param $transaction_description
	 * @param $col_start
	 * 
	 */
	function writeSubheaderTitle($transaction_description, $col_start=.7, $font_size = 9) 
	{
		//this to ensure that subheader in a page will have atleast 1 row of data
		if ($this->i==$this->row_count - 1) {
			$this->Ln(null, false);
		}
		$this->col_start = $col_start;
		$this->pdf_obj->SetX($this->col_start);
		$this->pdf_obj->SetFont('Arial','B',$font_size);
		$this->pdf_obj->Cell($this->row_height,$this->row_height,$transaction_description,'','','L');
		$this->Ln();
		
	}

	function writeCustomizedSubheader($headers, $headers2, $headers3=null, $alignment=null, $alignment2=null, $alignment3=null, $col_start = .7, $row_height = null, $row_start = 1.47, $cellfit=null) 
	{
		if ($row_height != null){
			$this->row_height = $row_height;
		}		
		$this->headers = $headers;
		$this->headers2 = $headers2;
		$this->headers3 = $headers3;
		$this->col_start = $col_start;
		$this->align = $alignment;
		$this->align2 = $alignment2;
		$this->align3 = $alignment3;
		$this->fit = $cellfit;
		$this->mode = "customized";
		$this->CustomizedSubheader();
	}
	
	function CustomizedSubheader() 
	{
		$this->pdf_obj->SetFillColor(190,190,190);
		$this->pdf_obj->SetFont('Arial','B',7);
		$this->pdf_obj->SetX($this->col_start);
		foreach($this->headers AS $label => $width) {
			if ($this->align==null) {
				if($this->fit==null)
					$this->pdf_obj->Cell($width*$this->row_height,$this->row_height,$label,'','','C',true);
				else
					$this->pdf_obj->CellFitScale($width*$this->row_height,$this->row_height,$label,'','','C',true);			}
			else {
				if($this->fit==null)
					$this->pdf_obj->Cell($width*$this->row_height,$this->row_height,$label,'','',$this->align[$label],true);
				else
					$this->pdf_obj->CellFitScale($width*$this->row_height,$this->row_height,$label,'','',$this->align[$label],true);
			}	
		}
		//$this->Ln();
		$this->Ln(.15);
		foreach($this->headers2 AS $label => $width) {
			if ($this->align2==null) {
				if($this->fit==null)
					$this->pdf_obj->Cell($width*$this->row_height,$this->row_height,$label,'','','C',true);
				else
					$this->pdf_obj->CellFitScale($width*$this->row_height,$this->row_height,$label,'','','C',true);			}
			else {
				if($this->fit==null)
					$this->pdf_obj->Cell($width*$this->row_height,$this->row_height,$label,'','',$this->align2[$label],true);
				else
					$this->pdf_obj->CellFitScale($width*$this->row_height,$this->row_height,$label,'','',$this->align2[$label],true);
			}	
		}
		$this->Ln();
		if ($this->headers3!=null) {
			foreach($this->headers3 AS $label => $width) {
				if ($this->align3==null) {
					if($this->fit==null)
						$this->pdf_obj->Cell($width*$this->row_height,$this->row_height,$label,'','','C',true);
					else
						$this->pdf_obj->CellFitScale($width*$this->row_height,$this->row_height,$label,'','','C',true);			}
				else {
					if($this->fit==null)
						$this->pdf_obj->Cell($width*$this->row_height,$this->row_height,$label,'','',$this->align3[$label],true);
					else
						$this->pdf_obj->CellFitScale($width*$this->row_height,$this->row_height,$label,'','',$this->align3[$label],true);
				}	
			}
			$this->Ln();
		}
	}
	
	/**
	 * @param array $data
	 * @param array $width
	 * @param array $alignment
	 * @param float $col_start
	 * @return integer
	 */
	function writeTableData($data, $width, $alignment, $col_start=.7, $ln = null, $row_count = null, $fontsize=7, $with_line = false, $with_line_basis = "") 
	{
		//$this->i=0;
		$this->col_start = $col_start;
		$this->pdf_obj->SetFont('Arial','',$fontsize);
		$this->fontsize = $fontsize;
		if ($row_count != null){
			$this->row_count = $row_count;
		}
		
		foreach($data AS $row) {
			$this->pdf_obj->SetX($this->col_start);
			$y = $this->pdf_obj->GetY();
			$x1 = $this->pdf_obj->GetX();
			foreach($row AS $key => $value) {
				$this->pdf_obj->Cell($width[$key]*$this->row_height,$this->row_height,$value,'','',$alignment[$key]);
			}	
			
			if ($with_line){
				if ($row[$with_line_basis] != ""){
					$x2 = $this->pdf_obj->GetX();
					$this->pdf_obj->Line($x1,$y, $x2, $y);
				}
			}
			
			$this->Ln($ln);
			//$this->pdf_obj->Ln(.20);
		}
	
		return count($data);
	}
	
	function writeFooter() 
	{		
		if ($this->page_layout=="portrait") {
			$this->pdf_obj->SetY(58.5*$this->row_height);
		}
		else {
			$this->pdf_obj->SetY(39.5*$this->row_height);
		}
		$this->pdf_obj->Ln();
		$this->Footer();
	}
	
	
	function Footer() 
	{
		$this->pdf_obj->SetTextColor(0,0,0);
		$this->pdf_obj->SetFont('Arial','B',9);
		$this->pdf_obj->Cell(18*$this->row_height,$this->row_height,$this->report_title,0,0,'L');
		$this->pdf_obj->SetFont('Arial','',9);
		if ($this->page_layout=="landscape") {
			$this->pdf_obj->SetX(30*$this->row_height);
		}
		$this->pdf_obj->Cell(16*$this->row_height,$this->row_height,"Page ".$this->pdf_obj->pageNo()." of {nb}",0,0,'L');
		$this->pdf_obj->Ln();	
	}
	
	/**
	 * @param array $data
	 * @param array $alignment
	 * @param array $width
	 * @param float $col_start
	 * 
	 */
	function writeTotals($data, $alignment, $width, $col_start=.7, $border=true,$x=null, $fit=null,$fontsize=7) 
	{
		$this->col_start = $col_start;
		$this->pdf_obj->SetX($this->col_start);
		$this->data = $data;
		$this->alignment = $alignment;
		$this->width = $width;
		$this->Totals($border,$fontsize);
		$this->x = $x;
		$this->cellfit = $fit;
	}
	
	function writeDetail($prepared='', $checked='', $position='')
	{
		$this->Ln(); 
		$this->Ln(); 
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(6*$this->row_height,$this->row_height,"Prepared By:");
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(14*$this->row_height,$this->row_height, $prepared);
		$this->Ln();
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(6*$this->row_height,$this->row_height,"Checked By:");
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(14*$this->row_height,$this->row_height, $checked);
		$this->Ln();
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(6*$this->row_height,$this->row_height,"Position:");
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(14*$this->row_height,$this->row_height, $position);
		$this->Ln();	
	}
	
	//to fix: should do this in the controller
	function writeVoucherDetail(/*$prepared='', $checked='', $position=''*/)
	{
		$this->Ln(); 
		$this->Ln(); 
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(20*$this->row_height,$this->row_height,"Reference:");
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(20*$this->row_height,$this->row_height,"Prepared By:");
		$this->pdf_obj->SetFont('Arial','',9);
		$this->Ln();
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(20*$this->row_height,$this->row_height,"Checked No:");
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(20*$this->row_height,$this->row_height,"Checked By:");
		$this->Ln();
		$this->pdf_obj->SetX(4.3);
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(6*$this->row_height,$this->row_height,"Approved By:");
		$this->Ln();
		$this->pdf_obj->SetX(4.3);
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(8*$this->row_height,$this->row_height,"Posted By");
		$this->pdf_obj->Cell(6*$this->row_height,$this->row_height,"to CDJ");
		$this->Ln();	
	}
	
	function writeGuarantorlistDetail($co_count, $loan_count)
	{
		$this->Ln(.25); 
		$this->pdf_obj->SetFont('Arial','B',9);
		$this->pdf_obj->Cell(13*$this->row_height,$this->row_height,"Total No. of Unqualified Guarantors:");
		$this->pdf_obj->SetFont('Arial','B',9);
		$this->pdf_obj->Cell(15*$this->row_height,$this->row_height,$co_count);
		$this->Ln();
		$this->pdf_obj->SetFont('Arial','B',9);
		$this->pdf_obj->Cell(13*$this->row_height,$this->row_height,"Total No. of Co Made Loans:");
		$this->pdf_obj->SetFont('Arial','B',9);
		$this->pdf_obj->Cell(15*$this->row_height,$this->row_height,$loan_count);
		$this->Ln();
	}
	
	function Totals($border,$fontsize=7) 
	{
		$y = $this->pdf_obj->GetY();
		if ($this->x==null)
			$x1 = $this->pdf_obj->GetX();
		else 
			$x1 = $this->x;
			
		foreach($this->data AS $key => $value) {
			$this->pdf_obj->SetFont('Arial','B',$fontsize);
			//if ($this->cellfit==null)
				$this->pdf_obj->Cell($this->width[$key]*$this->row_height,$this->row_height,$value,'','',$this->alignment[$key]);
			//else
			//	$this->pdf_obj->CellFitScale($this->width[$key]*$this->row_height,$this->row_height,$value,'','',$this->alignment[$key]);
		}
		$x2 = $this->pdf_obj->GetX();
		if($border)
			$this->pdf_obj->Line($x1,$y, $x2, $y);
	}
	
	function Output($filename ='') 
	{
		$this->pdf_obj->Output($filename.".pdf", 'D');
	}
	
	function Ln($h=null, $new_sub_header = true) 
	{
		if ($this->i==$this->row_count - 1) {
			$this->writeFooter();
			$this->AddPage();
			
			if ($this->hmode=='util')
				$this->BTHeader();
			else
				$this->Header();
				
			if ($this->mode=='normal'){ 
				if ($new_sub_header){
					if($this->subheader_title_every_page){
						$this->writeSubheaderTitle($this->subheader_title,$this->subheader_start_col);
					}
					else if($this->subheader_accountsummary_every_page){
						$this->pdf_obj->SetFont('Arial','B',7);
						$this->pdf_obj->Cell(36*$this->row_height,$this->row_height,'Account No. - Account Name:','','','L',true);		
						$this->i++;	
						$this->pdf_obj->Ln($h);
						$this->_setCell(7, '');
						$this->_setCell(33, $this->account_name);
						$this->i++;	
						$this->pdf_obj->Ln($h);
					}
					$this->Subheader();
				}
			}
			else 
				$this->CustomizedSubheader();
				
			$this->pdf_obj->SetFont('Arial','',$this->fontsize);
		}
		else {
			$this->i++;	
			$this->pdf_obj->Ln($h);
		}
	}
	
	function AddPage()
	{
		$this->i = 0;
		$this->pdf_obj->AddPage();
	}
	
	function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
	{
		$this->pdf_obj->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
	}
	
	function SetFont($family, $style='', $size=0)
	{	
		$this->pdf_obj->SetFont($family, $style, $size);
	}
	
	function SetX($x)
	{
		$this->pdf_obj->SetX($x);
	}
	
	function GetY()
	{
		return $this->pdf_obj->GetY();
	}
	
	function AliasNbPages()
	{
		$this->pdf_obj->AliasNbPages();
	}
	
		/**
	 * @param array $data
	 * @param array $width
	 * @param array $alignment
	 * @param float $col_start
	 * @return integer
	 */
	function writeLoanTableData($data, $width, $alignment, $col_start=.7, $ln = null) 
	{
		$this->col_start = $col_start;
		$this->pdf_obj->SetFont('Arial','',7);
		$j=0;
		$listCount = count($data);
		foreach($data AS $row) {
			$j++;
			$this->pdf_obj->SetX($this->col_start);
			foreach($row AS $key => $value) {
				$this->pdf_obj->Cell($width[$key]*$this->row_height,$this->row_height,$value,'','',$alignment[$key]);
			}	
			$this->pdf_obj->Ln($ln);
			if ($this->i>=40) {
				$this->writeFooter();
				$this->pdf_obj->AddPage();
				$this->Header();
				if($j<$listCount){
					if ($this->mode=='normal') {
						$this->Subheader();
					}
					else {
						$this->CustomizedSubheader();
					}
				}
				$this->pdf_obj->SetFont('Arial','',7);
				$this->i=0;
			}
			else {
				$this->i++;	
			}
			//$this->pdf_obj->Ln(.20);
			$this->count++;
		}
		//$this->Ln(.10);
		//echo $this->i."<br>";
		return $this->i;
	}

	function Write($h, $txt, $link='')
	{
		$this->pdf_obj->Write($h, $txt, $link);
	}
	
	function writeBTHeader($co_code='',$acc_no='',$batch_no='', $rec_branch='',$pay_date='',$ceiling=0,$rec_count=0,$co_debit_amt=0, $copy='', $header_ceil='')
	{
		$this->co_code = $co_code;
		$this->acc_no=$acc_no;
		$this->batch_no=$batch_no; 
		$this->rec_branch=$rec_branch;
		$this->pay_date=$pay_date;
		$this->ceiling=$ceiling;
		$this->rec_count=$rec_count;
		$this->co_debit_amt=$co_debit_amt;
		$this->copy = $copy;
		$this->header_ceil = $header_ceil;
		$this->hmode = "util";
		
		$this->pdf_obj->AliasNbPages();
		$this->pdf_obj->AddPage();
		$this->BTHeader();
	}
	
	function BTHeader()
	{
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell(0,0,"BANK OF THE PHILIPPINE ISLAND","","","C",false);
		$this->pdf_obj->Ln(.2);
		$this->pdf_obj->Cell(0,0,$this->header_ceil."PAYROLL TRANSACTION PROOFLIST","","","C",false);
		$this->pdf_obj->Ln(.15);
		$this->pdf_obj->Cell(0,0,"MCC 6750","","","C",false);
		$this->pdf_obj->Ln(.05);
		$this->pdf_obj->Cell(16.5*$this->row_height,$this->row_height,$this->copy,"","","L",false);
		$this->pdf_obj->Cell(15*$this->row_height,$this->row_height,"PAYROLL DATE:   ".$this->pay_date,"","","L",false);
		
		$this->pdf_obj->SetFont('Arial','',8);
		$this->pdf_obj->Ln(.4);
		$this->pdf_obj->SetX(.8);
		$this->pdf_obj->Cell(6*$this->row_height,$this->row_height,"Company Name","","","L",false);
		$this->pdf_obj->Cell(15*$this->row_height,$this->row_height,":     PECA Saving and Loans Association","","","L",false);
		$this->pdf_obj->Cell(5*$this->row_height,$this->row_height,"Ceiling Amount","","","L",false);
		$this->pdf_obj->Cell(10*$this->row_height,$this->row_height,":     ".$this->ceiling,"","","L",false);
		
		$this->pdf_obj->Ln(.15);
		$this->pdf_obj->SetX(.8);
		$this->pdf_obj->Cell(6*$this->row_height,$this->row_height,"Company Code","","","L",false);
		$this->pdf_obj->Cell(15*$this->row_height,$this->row_height,":    ".$this->co_code,"","","L",false);
		$this->pdf_obj->Cell(5*$this->row_height,$this->row_height,"Record Count","","","L",false);
		$this->pdf_obj->Cell(10*$this->row_height,$this->row_height,":    ".$this->rec_count,"","","L",false);
		
		$this->pdf_obj->Ln(.15);
		$this->pdf_obj->SetX(.8);
		$this->pdf_obj->Cell(6*$this->row_height,$this->row_height,"Account Number","","","L",false);
		$this->pdf_obj->Cell(10*$this->row_height,$this->row_height,":    ".$this->acc_no,"","","L",false);
		$this->pdf_obj->Cell(10*$this->row_height,$this->row_height,"Company Debited Amount","","","R",false);
		$this->pdf_obj->Cell(10*$this->row_height,$this->row_height,":    ".$this->co_debit_amt,"","","L",false);
		
		$this->pdf_obj->Ln(.15);
		$this->pdf_obj->SetX(.8);
		$this->pdf_obj->Cell(6*$this->row_height,$this->row_height,"Batch Number","","","L",false);
		$this->pdf_obj->Cell(15*$this->row_height,$this->row_height,":    ".$this->batch_no,"","","L",false);
		$this->pdf_obj->Cell(5*$this->row_height,$this->row_height," ","","","L",false);
		$this->pdf_obj->Cell(10*$this->row_height,$this->row_height," ","","","L",false);
		$this->pdf_obj->Ln(.3);
	}
	
	function Line($x1,$y, $x2, $y)
	{
		$this->pdf_obj->Line($x1,$y, $x2, $y);
	}

	function _setCell($col, $value){
		$this->pdf_obj->SetFont('Arial','',9);
		$this->pdf_obj->Cell($col*($this->row_height),$this->row_height,$value);
	}
	
	function SetY($y)
	{
		$this->pdf_obj->SetY($y);
	}
	
}

?>