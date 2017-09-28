<?php

class Batch_or extends Controller 
{
	var $filename;
	var $image = "images/or_duplicate.gif";
	var $width = 170;
	var $height = 5;

	function Batch_or() 
	{
		parent::Controller();
		$this->load->model('mcaptranheader_model');
		$this->load->model('mloanpayment_model');
		$this->load->model('minvestmentheader_model');
		$this->load->model('Parameter_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('asi_pdf');
		$this->load->library('fpdf');
		$this->load->helper('date');	
	}
	
	function index(){
		// $date = $this->Parameter_model->retrieveValue('CURRDATE');
		// $date = date('m/d/Y',strtotime($date));
		
		// echo json_encode(array(
            // 'data' => array(array('currdate' => $date))
        // ));
		
		// echo date('Ymdhis') . '</br>';
		// echo date('Ymdhis',strtotime("+5 minute")) . '</br>';
		// echo time() . '</br>';
		// echo date('Ymdhis', strtotime("+5 minute", strtotime("20100526070000")));
		
		// $_REQUEST['capcon']['transaction_no'] = '0000032742';
		// $this->printORCapCon();
		
		// $_REQUEST['lp']['loan_no'] = '7681';
		// $_REQUEST['lp']['transaction_code'] = 'CONP';
		// $_REQUEST['lp']['payor_id'] = '01517086';
		// $_REQUEST['lp']['payment_date'] = '20100702';
		 $this->printOR();
		// $or_no = $this->Parameter_model->retrieveValue('LASTORNO');
		// echo ++$or_no;
	}
	function printOR() {	
		$this->filename = 'ALL OR_' . date('Ymd') . ".pdf";
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		$objPdf = new fpdf('P','mm','A4');
		$objPdf->SetMargins(20,20);
		
		//whoah very bad approach, no choice
		$result = $this->mcaptranheader_model->generateOR($date);
		$result2 = $this->mloanpayment_model->generateOR($date);
		$result3 = $this->minvestmentheader_model->generateOR($date);
		
		$captranlist = $result['list'];
		$lplist = $result2['list'];
		$invlist = $result3['list'];
		
		//ready for sorting
		$all_trans = array();
		foreach ($captranlist as $row){
			$row['trans_type'] = 'captran';
			$all_trans[$row['or_no']] = $row;
		}
		
		foreach ($lplist as $row){
			$row['trans_type'] = 'lptran';
			$all_trans[$row['or_no']] = $row;
		}
		
		foreach ($invlist as $row){
			$row['trans_type'] = 'invtran';
			$all_trans[$row['or_no']] = $row;
		}
		
		ksort($all_trans);
		// print_r($all_trans);
		
		if ($result['count'] + $result2['count'] + $result3['count'] > 0){		
			foreach ($all_trans as $row){
				if ($row['trans_type'] == 'captran'){
					$objPdf->AddPage();
					$objPdf->SetFont('Arial','',10);				
					$details['Capital Savings'] = number_format($row['transaction_amount'], 2, '.', ',');
					$details['Fee'] = number_format($row['charges'], 2, '.', ',');
					$row['total'] = $row['transaction_amount'] + $row['charges'];
					$row['total'] = strtoupper($this->convert_number($row['total']) . ' pesos ') . ' (Php ' . number_format($row['total'], 2, '.', ',') . ')';
					$this->printPDF($objPdf,0,$row,$details);
					$this->printPDF($objPdf,1,$row,$details);
					unset($details);
				} else if ($row['trans_type'] == 'lptran'){
					$objPdf->AddPage();
					$objPdf->SetFont('Arial','',10);				
					$details['Loan'] = number_format($row['amount'], 2, '.', ',');
					$details['Interest'] = number_format($row['interest_amount'], 2, '.', ',');
					$details['Fee'] = number_format($row['charges'], 2, '.', ',');
					$row['total'] = $row['amount'] + $row['interest_amount'] + $row['charges'];
					$row['total'] = strtoupper($this->convert_number($row['total']) . ' pesos ') . ' (Php ' . number_format($row['total'], 2, '.', ',') . ')';
					$this->printPDF($objPdf,0,$row,$details);
					$this->printPDF($objPdf,1,$row,$details);	
					unset($details);
				} else{
					$objPdf->AddPage();
					$objPdf->SetFont('Arial','',10);				
					$details['Loan'] = number_format($row['investment_amount'], 2, '.', ',');
					$details['Interest'] = number_format($row['interest_amount'], 2, '.', ',');
					$row['total'] = $row['investment_amount'] + $row['interest_amount'];
					$row['total'] = strtoupper($this->convert_number($row['total']) . ' pesos ') . ' (Php ' . number_format($row['total'], 2, '.', ',') . ')';
					$this->printPDF($objPdf,0,$row,$details);
					$this->printPDF($objPdf,1,$row,$details);	
					unset($details);
				}
			}
			// foreach ($captranlist as $row){
				// $objPdf->AddPage();
				// $objPdf->SetFont('Arial','',10);				
				// $details['Capital Savings'] = number_format($row['transaction_amount'], 2, '.', ',');
				// $details['Fee'] = number_format($row['charges'], 2, '.', ',');
				// $row['total'] = $row['transaction_amount'] + $row['charges'];
				// $row['total'] = strtoupper($this->convert_number($row['total']) . ' pesos ') . ' (Php ' . number_format($row['total'], 2, '.', ',') . ')';
				// $this->printPDF($objPdf,0,$row,$details);
				// $this->printPDF($objPdf,1,$row,$details);
				// unset($details);
			// }
			
			// foreach ($lplist as $row){
				// $objPdf->AddPage();
				// $objPdf->SetFont('Arial','',10);				
				// $details['Loan'] = number_format($row['amount'], 2, '.', ',');
				// $details['Interest'] = number_format($row['interest_amount'], 2, '.', ',');
				// $details['Fee'] = number_format($row['charges'], 2, '.', ',');
				// $row['total'] = $row['amount'] + $row['interest_amount'] + $row['charges'];
				// $row['total'] = strtoupper($this->convert_number($row['total']) . ' pesos ') . ' (Php ' . number_format($row['total'], 2, '.', ',') . ')';
				// $this->printPDF($objPdf,0,$row,$details);
				// $this->printPDF($objPdf,1,$row,$details);	
				// unset($details);
			// }
			
			// foreach ($invlist as $row){
				// $objPdf->AddPage();
				// $objPdf->SetFont('Arial','',10);				
				// $details['Loan'] = number_format($row['investment_amount'], 2, '.', ',');
				// $details['Interest'] = number_format($row['interest_amount'], 2, '.', ',');
				// $row['total'] = $row['investment_amount'] + $row['interest_amount'];
				// $row['total'] = strtoupper($this->convert_number($row['total']) . ' pesos ') . ' (Php ' . number_format($row['total'], 2, '.', ',') . ')';
				// $this->printPDF($objPdf,0,$row,$details);
				// $this->printPDF($objPdf,1,$row,$details);	
				// unset($details);
			// }
			
			$objPdf->output($this->filename);
			
		} else{
			echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
		}				
	}
	
	function printORCapCon() {	
		$trans_no = isset($_REQUEST['transaction_no']) ? $_REQUEST['transaction_no'] : '';
		
		$this->filename = 'CAPCON_' . $trans_no . "_" . date('Ymd') . ".pdf";
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		$objPdf = new fpdf('P','mm','A4');
		$objPdf->SetMargins(20,20);
		
		$result = $this->mcaptranheader_model->generateOR($date, $trans_no);
		$captranlist = $result['list'];
		
		if ($result['count'] > 0){			
			foreach ($captranlist as $row){
				$objPdf->AddPage();
				$objPdf->SetFont('Arial','',10);				
				$details['Capital Savings'] = number_format($row['transaction_amount'], 2, '.', ',');
				$details['Fee'] = number_format($row['charges'], 2, '.', ',');
				$row['total'] = $row['transaction_amount'] + $row['charges'];
				$row['total'] = strtoupper($this->convert_number($row['total']) . ' pesos ') . ' (Php ' . number_format($row['total'], 2, '.', ',') . ')';
				$this->printPDF($objPdf,0,$row,$details);
				$this->printPDF($objPdf,1,$row,$details);
				unset($details);
			}
			
			$objPdf->output($this->filename);
			
		} else{
			echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
		}				
	}
	
	function printORLoanPayment() {	
		$loan_no = isset($_REQUEST['loan_no']) ? $_REQUEST['loan_no'] : '';
		$tran_code = isset($_REQUEST['transaction_code']) ? $_REQUEST['transaction_code'] : '';
		$payor = isset($_REQUEST['payor_id']) ? $_REQUEST['payor_id'] : '';
		$pay_date = isset($_REQUEST['payment_date']) ? date('Ymd', strtotime($_REQUEST['payment_date'])) : '';
		
		//log_message('debug', $loan_no . $tran_code . $payor . $pay_date);
		
		$this->filename = 'LP_' . $loan_no . $tran_code . $payor . $pay_date . "_" . date('Ymd') . ".pdf";
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		$objPdf = new fpdf('P','mm','A4');
		$objPdf->SetMargins(20,20);
		
		$lp_params[] = $loan_no;
		$lp_params[] = $tran_code;
		$lp_params[] = $payor;
		$lp_params[] = $pay_date;
		$result2 = $this->mloanpayment_model->generateOR($date, $lp_params);
		$lplist = $result2['list'];
		
		if ($result2['count'] > 0){						
			foreach ($lplist as $row){
				$objPdf->AddPage();
				$objPdf->SetFont('Arial','',10);				
				$details['Loan'] = number_format($row['amount'], 2, '.', ',');
				$details['Interest'] = number_format($row['interest_amount'], 2, '.', ',');
				$details['Fee'] = number_format($row['charges'], 2, '.', ',');
				$row['total'] = $row['amount'] + $row['interest_amount'] + $row['charges'];
				$row['total'] = strtoupper($this->convert_number($row['total']) . ' pesos ') . ' (Php ' . number_format($row['total'], 2, '.', ',') . ')';
				$this->printPDF($objPdf,0,$row,$details);
				$this->printPDF($objPdf,1,$row,$details);	
				unset($details);
			}
			
			$objPdf->output($this->filename);
			
		} else{
			echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
		}		
	}
	
	function printPDF($objPdf, $duplicate=0, $row=array(), $details=array()){
		$objPdf->Cell($this->width,$this->height,'SAVINGS & LOAN ASSOCIATION OF P&G PHILS EMPLOYEES (PECA) INC.',0,0,'C');
		$objPdf->Ln();
		$objPdf->Cell($this->width,$this->height,'20/F 6750 Ayala Office Tower, Ayala Center, Makati City',0,0,'C');
		$objPdf->Ln();
		$objPdf->Cell($this->width,$this->height,'Tel. 558-8270 or 8276; Fax 558-8330',0,0,'C');
		$objPdf->Ln();
		$objPdf->Ln();
		
		$objPdf->Cell($this->width,$this->height,"NO. {$row['or_no']}",0,0,'R');
		$objPdf->Ln();
		$objPdf->Ln();
		
		$objPdf->Cell($this->width,$this->height,'OFFICIAL RECEIPT',0,0,'C');
		$objPdf->Ln();
		$objPdf->Ln();
		
		$format_date = date('F d, Y',strtotime($row['or_date']));
		$objPdf->Cell($this->width,$this->height,"DATE: {$format_date}",0,0,'R');
		$objPdf->Ln();
		$objPdf->Ln();
		
		$emp_name = $row['first_name'] . ' ' . $row['last_name'];
		$objPdf->Cell($this->width,$this->height,"RECEIVED From :     {$emp_name}",0,0,'L');
		$objPdf->Ln();
		$objPdf->Ln();
		
		$objPdf->Cell(25,$this->height,'The sum of ',0,0,'L');
		$objPdf->SetFont('Arial','U',10);
		$objPdf->Cell($this->width,$this->height,$row['total'],0,0,'L');
		$objPdf->Ln();
		$objPdf->Ln();
		
		$objPdf->SetFont('Arial','',10);
		$objPdf->Cell($this->width,$this->height,'In payment of:',0,0,'L');
		$objPdf->Ln();
		$objPdf->Ln();
		
		foreach ($details as $key=>$value){
			if ($value == 0){
				continue;
			}
			$objPdf->Cell(100,$this->height,$key,0,0,'L');
			$objPdf->Cell($this->width,$this->height,$value,0,0,'L');
			$objPdf->Ln();
		}
		/*$objPdf->Cell(100,$this->height,"Capital Savings",0,0,'L');
		$objPdf->Cell($this->width,$this->height,$row['capcon'],0,0,'L');
		$objPdf->Ln();
		$objPdf->Cell(100,$this->height,'Fee',0,0,'L');
		$objPdf->Cell($this->width,$this->height,$row['charges'],0,0,'L');*/
		$objPdf->Ln();
		$objPdf->Ln();
		$objPdf->Ln();
		
		$objPdf->Cell($this->width,$this->height,'This is a computer generated Official Receipt, therefore, no signature is required.',0,0,'C');
		$objPdf->Ln();
		$objPdf->Ln();
		
		if ($duplicate == 0){
			$objPdf->Cell($this->width,0,'',1,0,'C');
			$objPdf->Cell($this->width,5,'',0,0,'C');
			$objPdf->Ln();
			$objPdf->Ln();
			$objPdf->Image($this->image,0,$objPdf->getY(),$this->width+40,130);
		} else{
			$objPdf->Cell($this->width,$this->height,'DUPLICATE COPY',0,0,'C');
		}
		
	}
	
	function testPDF(){
		$pdf = new FPDF('P', 'pt', array(1240, 1754));

		$pdf->AddPage();

		$pdf->Image('C:\logo.gif', 0, 0, 400, 400);

		$pdf->SetFont('Arial', 'B', 23);

		$name = "sdfsdf  adfasdfasdfasf";

		$pdf->Text(500, 457, $name);

		$pdf->Text(500, 1268, date('jS F Y'), 'asfasfasfd');

		$pdf->Output('mypdf.pdf');  
	}
	
	function convert_number($number) { 
		if (($number < 0) || ($number > 999999999)) 
		{ 
		throw new Exception("Number is out of range");
		} 
		
		$number = round($number);

		$Gn = floor($number / 1000000);  /* Millions (giga) */ 
		$number -= $Gn * 1000000; 
		$kn = floor($number / 1000);     /* Thousands (kilo) */ 
		$number -= $kn * 1000; 
		$Hn = floor($number / 100);      /* Hundreds (hecto) */ 
		$number -= $Hn * 100; 
		$Dn = floor($number / 10);       /* Tens (deca) */ 
		$n = $number % 10;               /* Ones */ 

		$res = ""; 

		if ($Gn) 
		{ 
			$res .= $this->convert_number($Gn) . " Million"; 
		} 

		if ($kn) 
		{ 
			$res .= (empty($res) ? "" : " ") . 
				$this->convert_number($kn) . " Thousand"; 
		} 

		if ($Hn) 
		{ 
			$res .= (empty($res) ? "" : " ") . 
				$this->convert_number($Hn) . " Hundred"; 
		} 

		$ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
			"Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
			"Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", 
			"Nineteen"); 
		$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 
			"Seventy", "Eigthy", "Ninety"); 

		if ($Dn || $n) 
		{ 
			if (!empty($res)) 
			{ 
				$res .= " and "; 
			} 

			if ($Dn < 2) 
			{ 
				$res .= $ones[$Dn * 10 + $n]; 
			} 
			else 
			{ 
				$res .= $tens[$Dn]; 

				if ($n) 
				{ 
					$res .= "-" . $ones[$n]; 
				} 
			} 
		} 

		if (empty($res)) 
		{ 
			$res = "zero"; 
		} 

		return $res; 
	} 
	
}

?>