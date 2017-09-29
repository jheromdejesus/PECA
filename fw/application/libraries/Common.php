<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Common {
	
	function number_to_words($number) { 
		if (($number < 0) || ($number > 999999999)) 
		{ 
		throw new Exception("Number is out of range");
		} 

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
			$res .= $this->number_to_words($Gn) . " Million"; 
		} 

		if ($kn) 
		{ 
			$res .= (empty($res) ? "" : " ") . 
				$this->number_to_words($kn) . " Thousand"; 
		} 

		if ($Hn) 
		{ 
			$res .= (empty($res) ? "" : " ") . 
				$this->number_to_words($Hn) . " Hundred"; 
		} 
		//[start] 0007882 edited by asi466 on 20110901 (wrong spelling of numbers)
		$ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
			"Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
			"Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", 
			"Nineteen"); 
		$tens = array("", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", 
			"Seventy", "Eighty", "Ninety"); 
		//[start] 0007882 edited by asi466 on 20110901 (wrong spelling of numbers)
		if ($Dn || $n) 
		{ 
			if (!empty($res)) 
			{ 
				$res .= " "; 
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
	
	/**
	 * @desc Check if date is valid. Accepted format is MM/DD/YYYY
	 * @param String $date
	 * @return boolean true - date is valid; false - date is not valid
	 */
	function checkReportDateFormat($date) {
		if (preg_match("/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/", $date, $parts)) {
			if(checkdate($parts[1],$parts[2],$parts[3])) 
				return true;
			else 
				return false;
		} else {
			return false;
		}	
	}
	
}
?>