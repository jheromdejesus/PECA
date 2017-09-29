<?php
include 'PHPExcel.php';
include 'PHPExcel/Writer/Excel5.php';
include 'PHPExcel/Writer/PDF.php';

class Asi_excel_ext {

	const LAYOUT_LANDSCAPE	= "landscape";
	const LAYOUT_PORTRAIT 	= "portrait";
	const LAYOUT_PAPER_SIZE = PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4;

	var $page_layout 		= Asi_excel_ext::LAYOUT_LANDSCAPE;
	var $php_excel_obj 		= null;
	var $column_width 		= 1.9; 		//1.8 in actual xls file
	var $row_height			= 12.75;
	var $l_bottom_margin	= 2.75;		//in inches
	var $p_bottom_margin	= 6.18;		//in inches
	var $column_matrix 		= array();
	var $column_definition	= array();
	var $report_title		= "";
	var $row_start_totals	= 0;		//used to compute the number of transactions and set the row start of Totals-row
	var $row_start_data		= 0;		//used to compute the number of transactions

	/**
	 * @param String $layout
	 */
	function Asi_excel_ext($layout = Asi_excel_ext::LAYOUT_LANDSCAPE)
	{
		$this->php_excel_obj = new PHPExcel();
		$this->page_layout = $layout;
	}

	/**
	 * @param int $column_count
	 * @param int $header_end
	 */
	function init($column_count = 64, $header_end = 5, $side_margin = .7, $isbottommargin = false)
	{		
		$this->setColumnWidth($column_count);
		$this->php_excel_obj->getActiveSheet()->getDefaultRowDimension()->setRowHeight($this->row_height);
		$this->php_excel_obj->getActiveSheet()->getPageSetup()->setFitToPage(false);
		$this->php_excel_obj->getActiveSheet()->getPageSetup()->setOrientation($this->page_layout);
		$this->php_excel_obj->getActiveSheet()->getPageSetup()->setPaperSize(Asi_excel_ext::LAYOUT_PAPER_SIZE);
		if ($isbottommargin==true) {
			if ($this->page_layout=="landscape") {
				$this->php_excel_obj->getActiveSheet()->getPageMargins()->setBottom($this->l_bottom_margin); //sets the bottom margin, so that there will only be 20 rows after the Header
			}
			else {
				$this->php_excel_obj->getActiveSheet()->getPageMargins()->setBottom($this->p_bottom_margin); //sets the bottom margin, so that there will only be 20 rows after the Header
			}
		}
		
		$this->php_excel_obj->getActiveSheet()->getPageMargins()->setRight($side_margin);
		$this->php_excel_obj->getActiveSheet()->getPageMargins()->setLeft($side_margin);	
		$this->php_excel_obj->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1,$header_end);		
	}

	/**
	 * @param int $column_count
	 */
	function setColumnWidth($column_count)
	{		
		$FC = range('A','Z');
		$k = -1;
		$alpha_repeat = $column_count%26 + 1;
		
		for($i = 0; $i<26 && $k < $alpha_repeat; $i++) {
			$index = "";
			if($k < 0 ){
				$index = $FC[$i];
			} else {
				$index = $FC[$k].$FC[$i];
			}

			$this->column_matrix[] = $index;

			if($i%25 == 0 && $i > 0){
				$k++;
				$i = -1;
			}

			$this->php_excel_obj->getActiveSheet()->getColumnDimension($index)->setWidth($this->column_width);
		}		
	}

	/** Prints Header of the report
	 * @param String $report_title
	 * @param String $report_date
	 * @param String $report_id
	 * @param String $run_date
	 */
	function writeHeaderInfo($report_title, $report_date, $report_id, $report_range =null)
	{
		$run_date = date("F j, Y H:i:s");
		$this->report_title = $report_title;
		$this->php_excel_obj->setActiveSheetIndex(0);
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A2', 'PECA Savings and Loans Monitoring System');
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A3', $report_title);
		if ($report_date!=null) {
			$this->php_excel_obj->getActiveSheet()->SetCellValue('A4', $report_date);
		}
		if ($report_range!=null) {
			$this->php_excel_obj->getActiveSheet()->SetCellValue('A5', $report_range);
			$this->php_excel_obj->getActiveSheet()->getStyle('A5:X5')->applyFromArray(
			array('font'=> array('name'=>'Arial',
											'bold'=>false,
											'italic'=>false,
											'size'=>9)
			));
		}
		$this->php_excel_obj->getActiveSheet()->SetCellValue('X2', "Report ID: " . $report_id);
		$this->php_excel_obj->getActiveSheet()->SetCellValue('X3', "Run Date: " . $run_date);
		$this->php_excel_obj->getActiveSheet()->getStyle('A2:X4')->applyFromArray(
			array('font'=> array('name'=>'Arial',
											'bold'=>false,
											'italic'=>false,
											'size'=>9)
			));

		$this->php_excel_obj->getActiveSheet()->getStyle('A2')->applyFromArray(
			array('font'=> array('name'=>'Arial',
											'bold'=>true,
											'italic'=>false,
											'size'=>9)
			));	
	}
	
	/** This prints transaction title (optional). 
	 * @param String $title
	 * @param int $col_start
	 * @param int $row_start
	 * @param String $fontsize
	 * @param String $alignment
	 * added @param $alignment
	 */
	//[START] Modified by Vincent Sy for 8th Enhancement 2013/07/04
	function writeSubheaderTitle($title, $col_start = 0, $row_start, $fontsize = 9, $alignment=PHPExcel_Style_Alignment::HORIZONTAL_LEFT) 
	{
	//[END] 8th Enhancement
		$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow($col_start, $row_start, $title);
		$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($col_start, $row_start)->applyFromArray(
			array('font'=> array('name'=>'Arial',
										'bold'=>true,
										'italic'=>false,
										'size'=>$fontsize),
						'alignment'=>array('horizontal'=>$alignment,
						//[END] 8th Enhancement
										'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
										'wrap'=>false)
				)
			);
	}
	
	/** Used to display subheaders
	 * @param array $headers
	 * @param int $col_start
	 * @param int $row_start
	 */
	function writeSubheader($headers, $col_start = 0, $row_start = 7, $row_height = 12.75, $alignment = null, $fill=true, $row_end = 7)
	{
		$this->column_definition = null;
		$index_start = $col_start;
		$index_end = $col_start;
		$this->php_excel_obj->getActiveSheet()->getRowDimension($row_start)->setRowHeight($row_height);
		foreach ($headers as $label => $width){
			$index_end = (($index_end * $this->column_width) + ($width * $this->column_width))/$this->column_width;
			$index_end = round($index_end);
			$this->php_excel_obj->getActiveSheet()->mergeCellsByColumnAndRow($index_start,$row_start,$index_end-1,$row_end);
			$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow($index_start, $row_start, $label);
			$this->column_definition[] = array("start" => $index_start, "end" => $index_end);
			if ($alignment==null) {
				$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($index_start, $row_start)->applyFromArray(
					array('font'=> array('name'=>'Arial',
												'bold'=>true,
												'italic'=>false,
												'size'=>7),
						  'alignment'=>array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
												'wrap'=>true)
						)
				);
			}
			else {
				$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($index_start, $row_start)->applyFromArray(
					array('font'=> array('name'=>'Arial',
												'bold'=>true,
												'italic'=>false,
												'size'=>7),
						  'alignment'=>array('horizontal'=>$alignment[$label],
												'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
												'wrap'=>true)
						)
				);
			}
			
			if ($fill==true) {
				$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($index_start,$row_start)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFC0C0C0');
			}
			$index_start = $index_end;
		}	
	}
	
	/**
	 * @param array $data
	 * @param array $alignment
	 * @param array $format
	 * @param int $row_start
	 */
	function writeTableData($data, $alignment, $format, $row_start = 8)
	{		
		$this->row_start_data = $row_start;
		foreach ($data as $row){
			$i = 0;
			foreach ($row as $key => $value){
				$this->php_excel_obj->getActiveSheet()->mergeCellsByColumnAndRow($this->column_definition[$i]["start"],$row_start,$this->column_definition[$i]["end"]-1,$row_start);
				$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow($this->column_definition[$i]["start"], $row_start, $value);
				if($format[$key] != "s"){
					$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($this->column_definition[$i]["start"], $row_start)->getNumberFormat()->setFormatCode($format[$key]);
				}
				$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($this->column_definition[$i]["start"], $row_start)->applyFromArray(
					array('font'=> array('name'=>'Arial',
											'bold'=>false,
											'italic'=>false,
											'size'=>7),
						  'alignment'=>array('horizontal'=>$alignment[$key],
											'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
											'wrap'=>false)
					)
				);
				$i++;
			}
			$row_start++;
		}
		$this->row_start_totals = $row_start;
		
	}
	
	/**
	 * @param array $data
	 * @param array $alignment
	 * @param array $format
	 * @param int $column_start
	 * @param int $column_end
	 * @param string $column_name
	 */
	function writeTotals($data, $alignment, $format, $column_start='A', $column_end='A', $border = true)
	{		
		$i = 0;
		foreach ($data AS $key => $value){
			$this->php_excel_obj->getActiveSheet()->mergeCellsByColumnAndRow($this->column_definition[$i]["start"],$this->row_start_totals,$this->column_definition[$i]["end"]-1,$this->row_start_totals);
			$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow($this->column_definition[$i]["start"], $this->row_start_totals, $value);
			if($border == true)
				$this->php_excel_obj->getActiveSheet()->getStyle($column_start.$this->row_start_totals.':'.$column_end.$this->row_start_totals)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
			if($format[$key] != "s"){
				$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($this->column_definition[$i]["start"], $this->row_start_totals)->getNumberFormat()->setFormatCode($format[$key]);
			}
			$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($this->column_definition[$i]["start"], $this->row_start_totals)->applyFromArray(
				array('font'=> array('name'=>'Arial',
										'bold'=>true,
										'italic'=>false,
										'size'=>7),
						'alignment'=>array('horizontal'=>$alignment[$key],
										'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
										'wrap'=>false)
				)
			);
			$i++;
		}
		$this->row_start_totals = $this->row_start_totals + 1;
	}
	
	function writeDoubleTotals($data, $alignment, $format, $column_start='A', $column_end='A', $border_style = 'thick')
	{		
		$i = 0;
		foreach ($data AS $key => $value){
			$this->php_excel_obj->getActiveSheet()->mergeCellsByColumnAndRow($this->column_definition[$i]["start"],$this->row_start_totals,$this->column_definition[$i]["end"]-1,$this->row_start_totals);
			$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow($this->column_definition[$i]["start"], $this->row_start_totals, $value);
			if($border_style != 'thin'){
				$this->php_excel_obj->getActiveSheet()->getStyle($column_start.$this->row_start_totals.':'.$column_end.$this->row_start_totals)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->php_excel_obj->getActiveSheet()->getStyle($column_start.$this->row_start_totals.':'.$column_end.$this->row_start_totals)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);
			}	
			else
				$this->php_excel_obj->getActiveSheet()->getStyle($column_start.$this->row_start_totals.':'.$column_end.$this->row_start_totals)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
			if($format[$key] != "s"){
				$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($this->column_definition[$i]["start"], $this->row_start_totals)->getNumberFormat()->setFormatCode($format[$key]);
			}
			$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($this->column_definition[$i]["start"], $this->row_start_totals)->applyFromArray(
				array('font'=> array('name'=>'Arial',
										'bold'=>true,
										'italic'=>false,
										'size'=>7),
						'alignment'=>array('horizontal'=>$alignment[$key],
										'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
										'wrap'=>false)
				)
			);
			$i++;
		}
		$this->row_start_totals = $this->row_start_totals + 1;
	}
	
	function writeDetail($prepared, $checked, $position)
	{
		$this->row_start_totals = $this->row_start_totals + 2;
		$i = $this->row_start_totals;
		
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, 'Prepared By:');
		$this->php_excel_obj->getActiveSheet()->SetCellValue('F'.$this->row_start_totals, $prepared);
		$this->row_start_totals++;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, 'Checked By:');
		$this->php_excel_obj->getActiveSheet()->SetCellValue('F'.$this->row_start_totals, $checked);
		$this->row_start_totals++;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, 'Position:');
		$this->php_excel_obj->getActiveSheet()->SetCellValue('F'.$this->row_start_totals, $position);
		$this->php_excel_obj->getActiveSheet()->getStyle('A'.$i.':F'.$this->row_start_totals)->applyFromArray(
			array('font'=> array('name'=>'Arial',
											'bold'=>false,
											'italic'=>false,
											'size'=>9)
			));
	}
	
	function writeFooter()
	{
		$this->php_excel_obj->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B&8' . $this->report_title . '&CPage &P of &N');
	}
	
	function outputExcel($filename)
	{
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");;
		header("Content-Disposition: attachment;filename=$filename ");
		header("Content-Transfer-Encoding: binary ");
	}
	
	function writeVoucherHeaderInfo($report_title, $report_date, $voucher_no)
	{
		$this->php_excel_obj->setActiveSheetIndex(0);
		$this->php_excel_obj->getActiveSheet()->mergeCellsByColumnAndRow(0,1,36,1);
		$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'SAVINGS & LOAN ASSOCIATION OF P & GP EMPLOYEES, INC.');
		$this->php_excel_obj->getActiveSheet()->mergeCellsByColumnAndRow(0,4,36,4);
		$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow(0, 4, $report_title);	
		$this->php_excel_obj->getActiveSheet()->getStyle('A1:A4')->applyFromArray(
			array('font'=> array('name'=>'Arial',
								 'bold'=>true,
								 'italic'=>false,
								 'size'=>9)
				  ,'alignment'=>array('horizontal'=>'center',
								      'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
									  'wrap'=>false)
			));
		$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow(0, 7, 'Pay to:');
		$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow(0, 8, 'Address:');
		$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow(27, 7, 'No:');
		$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow(27, 8, 'Date:');	
		$this->php_excel_obj->getActiveSheet()->mergeCellsByColumnAndRow(29,7,32,7);
		$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow(29, 7, $voucher_no);
		$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow(29, 8, $report_date);	
		$this->php_excel_obj->getActiveSheet()->getStyle('A7:AJ8')->applyFromArray(
			array('font'=> array('name'=>'Arial',
								 'bold'=>false,
								 'italic'=>false,
								 'size'=>9)
				  ,'alignment'=>array('horizontal'=>'left',
								      'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
									  'wrap'=>false)
			));		
	}
	
	function writeVoucherDetail()
	{
		$this->row_start_totals = $this->row_start_totals + 2;
		$i = $this->row_start_totals;
		
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, 'Reference:');
		$this->php_excel_obj->getActiveSheet()->SetCellValue('U'.$this->row_start_totals, 'Prepared by:');
		$this->row_start_totals++;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, 'Checked No:');
		$this->php_excel_obj->getActiveSheet()->SetCellValue('U'.$this->row_start_totals, 'Checked by:');
		$this->row_start_totals++;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('U'.$this->row_start_totals, 'Approved by:');
		$this->row_start_totals++;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('U'.$this->row_start_totals, 'Posted by:');
		$this->php_excel_obj->getActiveSheet()->SetCellValue('AC'.$this->row_start_totals, 'to CDJ');
		$this->php_excel_obj->getActiveSheet()->getStyle('A'.$i.':AC'.$this->row_start_totals)->applyFromArray(
			array('font'=> array('name'=>'Arial',
											'bold'=>false,
											'italic'=>false,
											'size'=>9)
			));
	}
	
	function writeGuarantorlistDetail($co_count, $loan_count)
	{
		$this->row_start_totals = $this->row_start_totals + 2;
		$i = $this->row_start_totals;
		
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, 'Total No. of Unqualified Guarantors:');
		$this->php_excel_obj->getActiveSheet()->SetCellValue('M'.$this->row_start_totals, $co_count);
		$this->row_start_totals++;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, 'Total No. of Co Made Loans:');
		$this->php_excel_obj->getActiveSheet()->SetCellValue('M'.$this->row_start_totals, $loan_count);
		$this->php_excel_obj->getActiveSheet()->getStyle('A'.$i.':M'.$this->row_start_totals)->applyFromArray(
			array('font'=> array('name'=>'Arial',
											'bold'=>true,
											'italic'=>false,
											'size'=>9)
			));
	}
	
	/**
	 * @desc Gets the column name. This will be used in writeTotals() 
	 */
	function getColumnName($column_count)
	{		
		$FC = range('A','Z');
		$k = -1;
		$alpha_repeat = $column_count%26 + 1;
		
		for($i = 0; $i<26 && $k < $alpha_repeat; $i++) {
			$index = "";
			if($k < 0 ){
				$index = $FC[$i];
			} else {
				$index = $FC[$k].$FC[$i];
			}

			$this->column_matrix[] = $index;

			if($i%25 == 0 && $i > 0){
				$k++;
				$i = -1;
			}
		}

		$index = $this->column_matrix[$column_count-1];
		return $index;	
	}
	
	/**
	 * @param array $data
	 * @param array $alignment
	 * @param array $format
	 * @param int $column_start
	 * @param int $column_end
	 * @param string $column_name
	 */
	function writeCustomizedTotals($data, $alignment, $format, $column_start='A', $column_end='A', $border =true)
	{		
		$i = 0;
		$index_end = 0;
		$index_start =0;
		foreach ($data AS $key => $width){
			$index_end = (($index_end * $this->column_width) + ($width * $this->column_width))/$this->column_width;
			$index_end = round($index_end);
			$this->php_excel_obj->getActiveSheet()->mergeCellsByColumnAndRow($index_start,$this->row_start_totals,$index_end-1,$this->row_start_totals);
			$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow($index_start, $this->row_start_totals, $key);
			if($border == true)
				$this->php_excel_obj->getActiveSheet()->getStyle($column_start.$this->row_start_totals.':'.$column_end.$this->row_start_totals)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			if($format[$key] != "s"){
				$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($index_start, $this->row_start_totals)->getNumberFormat()->setFormatCode($format[$key]);
			}
			$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($index_start, $this->row_start_totals)->applyFromArray(
				array('font'=> array('name'=>'Arial',
										'bold'=>true,
										'italic'=>false,
										'size'=>7),
						'alignment'=>array('horizontal'=>$alignment[$key],
										'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
										'wrap'=>false)
				)
			);
			$i++;
			$index_start = $index_end;
		}
		$this->row_start_totals = $this->row_start_totals + 1;
	}
	
	function _setCell( $cell, $value, $isbold = false, $fontsize = 9){
		$this->php_excel_obj->getActiveSheet()->SetCellValue($cell, $value);
		$this->php_excel_obj->getActiveSheet()->getStyle($cell)->applyFromArray(
			array('font'=> array('name'=>'Arial',
								'bold'=>$isbold,
								'italic'=>false,
								'size'=>$fontsize)
			));
	}
	
	function _setCellByRowCol($row, $col, $value, $isbold = false, $fontsize = 9, $border_style='thin',$format=null){
		$this->php_excel_obj->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
		$this->php_excel_obj->getActiveSheet()->mergeCellsByColumnAndRow($col,$row,$col+15,$row);
		if ($format!=null)
			$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($col,$row)->getNumberFormat()->setFormatCode($format);
		if ($border_style!='thin')
			$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($col,$row)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);
		$this->php_excel_obj->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray(
			array('font'=> array('name'=>'Arial',
								'bold'=>$isbold,
								'italic'=>false,
								'size'=>$fontsize)
			));
	}
	
	function writeFinancialLastPart($row_add = null)
	{
		$this->row_start_totals = $this->row_start_totals + 2;
		if ($row_add != null){
			$this->row_start_totals = $this->row_start_totals + $row_add;
		}
		
		$i = $this->row_start_totals;
		
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, 'REPUBLIC OF THE PHILIPPINES');
		$this->row_start_totals+=3;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, 'I, ___________________________ of the above named savings and loan association, do solemnly swear that the');
		$this->row_start_totals++;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, "foregoing report is true to the best of my knowledge.");
		$this->row_start_totals+=2;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('W'.$this->row_start_totals, "________________________________");
		$this->row_start_totals++;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('W'.$this->row_start_totals, "________________________________");
		$this->row_start_totals+=2;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, "SUBSCRIBED AND SWORN to before me this ____th day of ____________, _______ affiant exhibiting to me her");
		$this->row_start_totals++;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, "Community Tax No. _______________ issued at ___________________ on __/__/____.");
		$this->row_start_totals+=3;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, "NOTARY PUBLIC");
		$this->row_start_totals+=3;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, "Doc No.    ________________________________");
		$this->row_start_totals++;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, "Page No.   ________________________________");
		$this->row_start_totals++;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, "Book No.   ________________________________");
		$this->row_start_totals++;
		$this->php_excel_obj->getActiveSheet()->SetCellValue('A'.$this->row_start_totals, "Series of   ________________________________");
		
		$this->php_excel_obj->getActiveSheet()->getStyle('A'.$i.':F'.$this->row_start_totals)->applyFromArray(
			array('font'=> array('name'=>'Arial',
											'bold'=>false,
											'italic'=>false,
											'size'=>9)
			));
	}
}

?>