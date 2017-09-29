<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('getMIRandEIR'))
{


    /**
     * Get MIR and EIR for a certain loan
     * author: NERUBIA
     *
     */    
    function getMIRandEIR( $loan_no = '') {     

        // Get a reference to the controller object
        $CI = get_instance();

        //if $_REQUEST is not set, then return an error message.  Otherwise, process $_REQUEST
        if ( $loan_no == '' ) { 
            echo("{'success':false,'msg':'No request parameters.','error_code':'19'}"); 
        } 
        else {  
            //=========================================================
            //array of values for computation purposes
            $data = null;
            //array of values for display purposes
            $display = null;
            //array of payments made so far for this loan
            $loan_payments = null;
            //==========================================================
            
            $data = getLoanAmortizationScheduleReportParameters($loan_no);
            $display = $data;
            
            initializeData($data);
            
            computeFromPeriod($data);
            
            /*//if after amortization enddate, then return
            if($data["fromPeriod"] === -1) {
                echo("{'success':false,'msg':'Cannot generate report after amortization end date.','error_code':'19'}");
                return;
            }*/
            
            //check whether this loan is payroll deducted or not
            $data["payroll_deduction"] = getPayrollDeductionField($data["loan_code"]);
            
            //get employee name for display
            $employee_full_name = getEmployeeName($data["employee_id"]);
            $display["employee_name"] = $employee_full_name["last_name"].", ".$employee_full_name["first_name"];
            
            //if not payroll deducted, generate "schedule" like report
            if($data["payroll_deduction"] === "N") {
                $display["term_for_display"] = $data["original_term"];
                
                //compute to period
                if(($data["fromPeriod"] + 11) <= $data["term"]) {
                    $data["toPeriod"] = $data["fromPeriod"] + 11;
                } 
                else {
                    $data["toPeriod"] = $data["term"];
                }
                
                $periods = null;
                //compute the balance for this loan period
                $data["balance"] =  $data["principal"] - (($data["principal"] / ($data["original_term"]/12)) * (($data["fromPeriod"]-1)/12));
                
                //compute for the initial cashflow for this loan period
                if(((($data["fromPeriod"]-1)/12)+1) === 1) { 
                    //if first loan period
                    $data["initial_balance"] = $data["principal"];
                    $data["initial_cashflow"] = $data["loan_proceeds"];
                }
                else {
                    $data["initial_balance"] = $data["balance"];
                    $data["initial_cashflow"] = $data["balance"];
                }
                
                //initialize cashflow array
                $data["cashflow_values"] = array();
                array_push($data["cashflow_values"], (float)$data["initial_cashflow"]);
                
                generateGuideSchedule($data);
            }
            //else, consider previous payments in generating the report
            else if($data["payroll_deduction"] === "Y") {
                //recompute term (adjust base on balloon) and compute end period====================
                $data["toPeriod"] = ($data["fromPeriod"] + 11 <= $data["term"]) ? $data["fromPeriod"] + 11 : $data["term"]; 
                
                //get all payments made for this loan
                $loan_payments = getLoanPayments($loan_no);
                
                //get starting balance for this loan year       
                $first_period_of_current_loan_year = date("Ymd", strtotime(date("Ymd", strtotime($data["amortization_startdate"]))."+".($data["fromPeriod"]-1)." month"));
                $data["balance"] = getBalanceAtPeriod($data["principal"], $loan_payments, $first_period_of_current_loan_year);
                //get outstanding balance for this period (month)
                $data["current_balance"] = getBalanceAtPeriod($data["principal"], $loan_payments, date("Ymd", $data["current_date_without_time"]));
                
                //true when user attempts to generate within loan duration but loan is already paid full.
                /*if($data["balance"] == 0) {
                    echo("{'success':false,'msg':'Cannot generate report when outssssssstanding loan balance is zero.','error_code':'19'}");
                    //so it wont proceed computing
                    return;
                }*/
                
                //compute for the initial cashflow for this loan period
                if(((($data["fromPeriod"]-1)/12)+1) === 1) { 
                    //if first loan period
                    $data["initial_balance"] = $data["principal"];
                    $data["initial_cashflow"] = $data["loan_proceeds"];
                }
                else {
                    $data["initial_balance"] = $data["balance"];
                    $data["initial_cashflow"] = $data["balance"];
                }
                
                //initialize cashflow array
                $data["cashflow_values"] = array();
                array_push($data["cashflow_values"], (float)$data["initial_cashflow"]);
                                
                generateActualSchedule($loan_payments, $data, $display);
            }
                    
            computeMIRandEIR($data);
            
            //formatDisplay($data, $display);
            //printPDF($data, $display);
            
            /*echo json_encode(array(
                'data' => $data
                ,'display' => $display
            ));*/

            return array( 'MIR' => $data['MIR'], 'EIR' => $data['EIR']);
        }
    }
    
    function getLoanAmortizationScheduleReportParameters($loan_no) {        
        $CI = get_instance();
        $data = $CI->mloan_model->get(
            array('loan_no' => $loan_no)
            ,array(
                'loan_code'
                ,'employee_id'
                ,'principal' //loan amount
                ,'interest_rate' //annual contractual rate 
                ,'term' 
                ,'employee_principal_amort' //principal amount
                ,'amortization_startdate'
                ,'service_fee_amount'
                //[mantis-0009122] Start
                ,'initial_interest'
                //[mantis-0009122] End
				,'loan_proceeds'
            )
        );
        
        return $data["list"][0];
    }
    
    function getEmployeeName($employee_id) {
        $CI = get_instance();
        $data = $CI->member_model->get(
            array('employee_id' => $employee_id)
            ,array('first_name', 'middle_name', 'last_name')
        );
        
        return $data["list"][0];
    }
    
    function getCurrentDateFromSysParam() {
        $CI = get_instance();
        $current_date_param_name = "CurrentDate";
        $data = $CI->parameter_model->get(
            array('parameter_name' => $current_date_param_name)
            ,array('parameter_value')
        );
        
        return $data["list"][0]["parameter_value"];
    }
    
    function printPDF($data, $display) {    
        //load->library('asi_pdf_ext');
    
        $objPdf = new Asi_pdf_ext();
        $objPdf->init("portrait", 0.7, 0.75);   
        
        $amort_header_info = array(             
            //format for display
            "loan_period" => $display["loan_period"]
            ,"run_date" => $display["run_date"]
            ,"report_title" => "Amortization Scheduled Report"
            ,"employee_id" => $display["employee_id"]
            ,"employee_name" => $display["employee_name"]
            ,"loan_amount" => $display["loan_amount"]
            ,"annual_contractual_rate" => $display["annual_contractual_rate"]
            ,"terms" => $display["term_for_display"]
            ,"other_charges" => $display["other_charges"]
            ,"principal_amount" => $display["principal_amount"]
            ,"initial_interest" => $display["initial_interest"]
            ,"service_charge" => $display["service_charge"]
            ,"other_charges_amount" => $display["other_charges_amount"]
            ,"loan_proceeds" => $display["loan_proceeds"]
            ,"total_interest_paid" => $display["total_interest_paid"]
        );
        
        $objPdf->writeAmortHeaderInfo($amort_header_info);
        $objPdf->writeSubheader(array("Period" => 4     
                                            ,"Payment Amount" => 7
                                            ,"Interest" => 6
                                            ,"Principal" => 7
                                            ,"Balance" => 7
                                            ,"Cash Flow" => 7)
                                    ,array("Period" => "C"      
                                            ,"Payment Amount" => "R"
                                            ,"Interest" => "R"
                                            ,"Principal" => "R"
                                            ,"Balance" => "R"
                                            ,"Cash Flow" => "R"));      
        $pdf_column_width = array("period" => 4     
                                            ,"payment_amount" => 7
                                            ,"interest" => 6
                                            ,"principal" => 7
                                            ,"balance" => 7
                                            ,"cashflow" => 7);
        $pdf_column_align = array("period" => "C"       
                                            ,"payment_amount" => "R"
                                            ,"interest" => "R"
                                            ,"principal" => "R"
                                            ,"balance" => "R"
                                            ,"cashflow" => "R");
        $objPdf->writeAmortSchedSubheader(
            number_format((float)$data["initial_balance"], 0, '.', ',')
            ,number_format((float)($data["initial_cashflow"]), 0, '.', ',')
        );
        $count = $objPdf->writeTableData($data["periods"]
                                ,$pdf_column_width
                                ,$pdf_column_align);
                                
        $objPdf->writeMIR(number_format((float)($data["MIR"]*100), 2, '.', ',')."%");
        $objPdf->writeEIR(number_format((float)($data["EIR"]*100), 2, '.', ',')."%");
        
        //Ask Jherom about the file name.
        $objPdf->Output("Amortization_Schedule_".date("Ymd"));      
    }
    
    function addOrdinalSuffix($num) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if (($num %100) >= 11 && ($num%100) <= 13)
           return $num.'th';
        else
           return $num.$ends[$num % 10];
    }
    
    function getLoanPayments($loan_no) {        
        $CI = get_instance();   
        $data = $CI->mloanpayment_model->get_list(
            array('loan_no' => $loan_no)
            ,null
            ,null
            ,array(
                'payment_date'
                ,'transaction_code'
                ,'amount'
                ,'interest_amount'
                ,'balance'
                ,'source'
            )
            ,'payment_date'
        );
        
        return $data["list"];
    }

    function getBalanceAtPeriod($initial_balance, $loan_payments, $period) {
        foreach($loan_payments as $payment) {
            if($payment["payment_date"] < $period) {
                $initial_balance -= $payment["amount"];
            }
            else {
                break;
            }
        }
        return $initial_balance;
    }
    
    function getTotalPaymentMadeAtPeriod($loan_payments, $fromPeriod, $toPeriod) {
        $total_payment = "0.00";
        foreach($loan_payments as $payment) {
            if($payment["payment_date"] >= $fromPeriod && $payment["payment_date"] < $toPeriod) {
                $total_payment += $payment["amount"];
            }
            else if($payment["payment_date"] > $toPeriod) {
                break;
            }
        }
        return $total_payment;
    }
    
    function dateDifference($startDate, $endDate) {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        if ($startDate === false || $startDate < 0 || $endDate === false || $endDate < 0 || $startDate > $endDate)
            return false;
           
        $years = date('Y', $endDate) - date('Y', $startDate);
       
        $endMonth = date('m', $endDate);
        $startMonth = date('m', $startDate);
       
        // Calculate months
        $months = $endMonth - $startMonth;
        if ($months <= 0)  {
            $months += 12;
            $years--;
        }
        if ($years < 0)
            return false;
       
        // Calculate the days
        $offsets = array();
        if ($years > 0)
            $offsets[] = $years . (($years == 1) ? ' year' : ' years');
        if ($months > 0)
            $offsets[] = $months . (($months == 1) ? ' month' : ' months');
        $offsets = count($offsets) > 0 ? '+' . implode(' ', $offsets) : 'now';

        $days = $endDate - strtotime($offsets, $startDate);
        $days = date('z', $days);   
                   
        return array($years, $months, $days);
    }
    
    function getPayrollDeductionField($loan_code) {
        $CI = get_instance();
        $data = $CI->loancodeheader_model->get(
            array('loan_code' => $loan_code)
            ,array(
                'payroll_deduction'
            )
        );
        
        return $data["list"][0]["payroll_deduction"];
    }
    
    function computeFromPeriod(&$data) {
        $period = 0;
        $fromPeriod = -1;
        
        //before amortization
        if($data["current_date_without_time"] <= strtotime($data["amortization_startdate"])) {
            $fromPeriod = 1;
        }
        //during amortization
        else if($data["current_date_without_time"] <= $data["amortization_enddate"]) {
            $current_date = date("Ymd", $data["current_date_without_time"]);
            $amortization_startdate = $data["amortization_startdate"];
            $period = dateDifference($amortization_startdate, $current_date);
            $period = (($period[0])*12) + $period[1] + ((($period[2]) + 1)> 0? 1: 0);               
            //determine nth loan year
            $fromPeriod = (intval((($period - 1) / 12)) * 12) + 1;
        }
        
        $data["period"] = $period;
        $data["fromPeriod"] = $fromPeriod;
    }
    
    function initializeData(&$data) {
        $data["original_term"] = $data["term"];     
        $data["current_date"] = date("F d, Y G:i:s", strtotime(getCurrentDateFromSysParam()));
        $data["employee_principal_amort_db"] = $data["employee_principal_amort"];
        $data["employee_principal_amort"] = $data["principal"] / $data["term"];
        $data["other_charges_amount"] = $data["initial_interest"] + $data["service_fee_amount"];
        //other charges(percentage)
        $data["other_charges"] = (abs($data["other_charges_amount"])/$data["principal"])*100;
        //$data["loan_proceeds"] = $data["principal"] - abs($data["other_charges_amount"]);
        $data["total_interest_paid"] = 0;
        //current load period checking checks the date only, time is not included
        $data["current_date_without_time"] = strtotime(date("Ymd", strtotime($data["current_date"])));
        //amortization_startdate + number of terms = amortization_enddate   
        $data["amortization_enddate"] = strtotime(date("Ymd", strtotime($data["amortization_startdate"]))."+".$data["term"]." month"); 
    }
    
    function generateGuideSchedule(&$data) {
        $principal = $data["principal"] / $data["term"];
        $temp_periods = null;
        $periods = null;
        
        for($i = $data["fromPeriod"] ; $i <= $data["term"] ; $i++) {                        
            $balance = $data["balance"];    
            $temp_periods[$i]["period"] = $i;
            $temp_periods[$i]["principal"] = round($principal);     
            $temp_periods[$i]["balance"] = round($balance - $principal);                
            $temp_periods[$i]["interest"] = $balance * ($data["interest_rate"] * 0.01) / 12;    
            $temp_periods[$i]["payment_amount"] = $principal + $temp_periods[$i]["interest"];   
            $temp_periods[$i]["interest"] = round($temp_periods[$i]["interest"]);
            //round off for display
            $temp_periods[$i]["cashflow"] = $temp_periods[$i]["payment_amount"]  * (-1);    
            array_push($data["cashflow_values"], (float)$temp_periods[$i]["cashflow"]);
            $temp_periods[$i]["cashflow"] = round($temp_periods[$i]["cashflow"]);
            $temp_periods[$i]["payment_amount"] = round($principal + $temp_periods[$i]["interest"]);                        
            
            //arrange for report display
            if($i <= $data["toPeriod"]) {
                $data["total_interest_paid"] = $data["total_interest_paid"] + round($temp_periods[$i]["interest"]);
                $periods[$i]["period"] = number_format((float)$temp_periods[$i]["period"], 0, '.', ',');
                $periods[$i]["payment_amount"] = number_format((float)$temp_periods[$i]["payment_amount"], 0, '.', ',');
                $periods[$i]["interest"] = number_format((float)$temp_periods[$i]["interest"], 0, '.', ',');
                $periods[$i]["principal"] = number_format((float)$temp_periods[$i]["principal"], 0, '.', ',');
                $periods[$i]["balance"] = number_format((float)abs($temp_periods[$i]["balance"]), 0, '.', ',');
                $periods[$i]["cashflow"] = number_format((float)$temp_periods[$i]["cashflow"], 0, '.', ',');
            }
                            
            $data["balance"] = $data["balance"] - $principal;               
        }
        
        $data["periods"] = $periods;
    }
    
    function generateActualSchedule($loan_payments, &$data, &$display) {
        $periods = null;
        
        //initial period
        $periods[$data["fromPeriod"] - 1]["period"] = "";
        $periods[$data["fromPeriod"] - 1]["payment_amount"] = "";
        $periods[$data["fromPeriod"] - 1]["interest"] = "";
        $periods[$data["fromPeriod"] - 1]["principal"] = "";
        $periods[$data["fromPeriod"] - 1]["balance"] = number_format((float)$data["balance"], 0, '.', ',');
        $periods[$data["fromPeriod"] - 1]["cashflow"] = number_format((float)$data["initial_cashflow"], 0, '.', ',');
    
        for($i = $data["fromPeriod"], $period_counter = $data["fromPeriod"]; /*$i <= $data["term"] && */floor($data["balance"]) != 0; $i++, $period_counter++) {
            //if before current period
            if($period_counter < $data["period"]) {
                $payment_from = date("Ymd", strtotime(date("Ymd", strtotime($data["amortization_startdate"]))."+".($period_counter - 1)." month"));             
                $payment_to = date("Ymd", strtotime(date("Ymd", strtotime($data["amortization_startdate"]))."+".($period_counter)." month"));
                $total_payment = intval(getTotalPaymentMadeAtPeriod($loan_payments, $payment_from, $payment_to));
                $payments = getPaymentsMadeAtPeriod($loan_payments, $payment_from, $payment_to);
                    
                computePastTerm($i, $period_counter, $periods, $data, $total_payment, $payments);
            }
            //if during current period
            else if($period_counter == $data["period"]) {
                $payment_from = date("Ymd", strtotime(date("Ymd", strtotime($data["amortization_startdate"]))."+".($period_counter - 1)." month"));             
                $payment_to = date("Ymd", strtotime(date("Ymd", $data["current_date_without_time"])."+1 day"));
                $total_payment = intval(getTotalPaymentMadeAtPeriod($loan_payments, $payment_from, $payment_to));
                $payments = getPaymentsMadeAtPeriod($loan_payments, $payment_from, $payment_to);
                
                computeDuringTerm($i, $period_counter, $periods, $data, $total_payment, $payments);
            }       
            //after current period
            else {                      
                computeFutureTerm($i, $period_counter, $periods, $data);
            }
            
            $display["term_for_display"] = $period_counter;
        }
        
        //skip first period which contains the initial balance and initial cashflow
        unset($periods[$data["fromPeriod"] - 1]);
        //only display this loan year
        foreach($periods as $key => $temp_period) {
            if($temp_period["period"] > $data["toPeriod"]) {
                unset($periods[$key]);
            }
        }
        
        $data["periods"] = $periods;
    }
    
    function computeMIRandEIR(&$data) {
        //compute MIR
        $excel_functions = new PHPExcel_Calculation_Functions();                
        $data["MIR"]= $excel_functions->IRR($data["cashflow_values"]);
        if($data["MIR"] === "#VALUE!") {
            $GUESS_RANGE_FROM = 1;
            $GUESS_RANGE_TO = 5;
            for($i = $GUESS_RANGE_FROM ; $i <= $GUESS_RANGE_TO ; $i++) {
                $data["MIR"] = $excel_functions->IRR($data["cashflow_values"], ($i/100));                       
                if($data["MIR"] !== "#VALUE!") {    
                    $data["MIR"] = ($data["MIR"]*100);
                    break;
                }
            }
        }
        else {
            $data["MIR"] = $data["MIR"];
        }
        
        //compute EIR
        $data["EIR"] = pow(1 + $data["MIR"], 12) - 1;
    }
    
    function formatDisplay(&$data, &$display) { 
        $display["loan_period"] = addOrdinalSuffix(((($data["fromPeriod"]-1)/12)+1))." Loan Year";
        $display["run_date"] = date("F d, Y G:i:s");
        $display["loan_amount"] = "PHP".number_format((float)round($data["principal"]), 2, '.', ',');
        $display["annual_contractual_rate"] = number_format((float)$data["interest_rate"], 2, '.', '')."%";
        $display["terms"] = number_format((float)$display["term_for_display"], 2, '.', '');
        $display["other_charges"] = number_format((float)$data["other_charges"], 2, '.', '')."%";
        $display["principal_amount"] = "PHP ".number_format((float)round($data["employee_principal_amort"]), 2, '.', ',');
        $display["initial_interest"] = number_format((float)round($data["initial_interest"]), 2, '.', ',');
        $display["service_charge"] = number_format((float)round($data["service_fee_amount"]), 2, '.', ',');
        $display["other_charges_amount"] = number_format((float)round($data["other_charges_amount"]), 2, '.', ',');
        $display["loan_proceeds"] = number_format((float)round($data["loan_proceeds"]), 2, '.', ',');
        $display["total_interest_paid"] = number_format((float)round($data["total_interest_paid"]), 2, '.', ',');
    }

    function computePastTerm(&$i, $period_counter, &$periods, &$data, $total_payment, $payments) {
        if(count($payments) === 0) {
            //no payments
            $period_principal = 0;              
            $periods[$i]["period"] = $period_counter;
            $interest = $data["balance"] * ($data["interest_rate"] / 100) / 12;                     
            if($period_counter <= $data["toPeriod"]) {
                $data["total_interest_paid"] += round($interest);
            }
            $payment_amount = $period_principal + $interest;
            $periods[$i]["payment_amount"] = number_format((float)(round($period_principal) + round($interest)), 0, '.', ',');
            $periods[$i]["interest"] = number_format((float)$interest, 0, '.', ',');
            $periods[$i]["principal"] = number_format((float)$period_principal, 0, '.', ',');
            $period_date = date("Ymd", strtotime(date("Ymd", strtotime($data["amortization_startdate"]))."+".($period_counter)." month"));
            $data["balance"] -= $period_principal;
            $periods[$i]["balance"] = number_format((float)abs($data["balance"]), 0, '.', ',');
            $periods[$i]["cashflow"] = number_format((float)round($payment_amount * (-1)), 0, '.', ',');
            array_push($data["cashflow_values"], (float)$payment_amount * (-1));
            return;
        }
        
        foreach($payments as $payment) {
            if($payment["source"] == "P") { 
                $period_principal = $payment["amount"];             
                $periods[$i]["period"] = $period_counter;
                $interest = $data["balance"] * ($data["interest_rate"] / 100) / 12;                     
                if($period_counter <= $data["toPeriod"]) {
                    $data["total_interest_paid"] += round($interest);
                }
                $payment_amount = $period_principal + $interest;
                $periods[$i]["payment_amount"] = number_format((float)(round($period_principal) + round($interest)), 0, '.', ',');
                $periods[$i]["interest"] = number_format((float)$interest, 0, '.', ',');
                $periods[$i]["principal"] = number_format((float)$period_principal, 0, '.', ',');
                $period_date = date("Ymd", strtotime(date("Ymd", strtotime($data["amortization_startdate"]))."+".($period_counter)." month"));
                $data["balance"] -= $period_principal;
                $periods[$i]["balance"] = number_format((float)abs($data["balance"]), 0, '.', ',');
                $periods[$i]["cashflow"] = number_format((float)round($payment_amount * (-1)), 0, '.', ',');
                array_push($data["cashflow_values"], (float)$payment_amount * (-1));
                
                if($payment !== end($payments)) {
                    $i++;
                }
            }
            //balloon payment
            else {              
                $periods[$i]["period"] = "";
                $periods[$i]["payment_amount"] = number_format((float)($payment["amount"]), 0, '.', ',');
                $periods[$i]["interest"] = "0";
                $periods[$i]["principal"] = "0";
                $periods[$i]["balance"] = number_format((float)abs($data["balance"] - $payment["amount"]), 0, '.', ',');
                $periods[$i]["cashflow"] = number_format((float)round(str_replace(",", "", $periods[$i]["payment_amount"]) * (-1)), 0, '.', ',');
                array_push($data["cashflow_values"], (float)str_replace(",", "", $periods[$i]["cashflow"]));
                
                $data["balance"] -= $payment["amount"];
                $i++;
            }
        }
    }
    
    function computeDuringTerm(&$i, $period_counter, &$periods, &$data, $total_payment, $payments) {
        if(count($payments) === 0) {
            //no payments
            $period_principal = $data["balance"] <= $data["employee_principal_amort"]? $data["balance"] : $data["employee_principal_amort"];
            $ALLOWANCE_FOR_DECIMALS = .5 * 180;
            if($data["balance"] - $period_principal <= $ALLOWANCE_FOR_DECIMALS) {
                $period_principal = $data["balance"];
            }           
            $periods[$i]["period"] = $period_counter;
            $interest = $data["balance"] * ($data["interest_rate"] / 100) / 12;                     
            if($period_counter <= $data["toPeriod"]) {
                $data["total_interest_paid"] += round($interest);
            }
            $payment_amount = $period_principal + $interest;
            $periods[$i]["payment_amount"] = number_format((float)(round($period_principal) + round($interest)), 0, '.', ',');
            $periods[$i]["interest"] = number_format((float)$interest, 0, '.', ',');
            $periods[$i]["principal"] = number_format((float)$period_principal, 0, '.', ',');
            $period_date = date("Ymd", strtotime(date("Ymd", strtotime($data["amortization_startdate"]))."+".($period_counter)." month"));
            $data["balance"] -= $period_principal;
            $periods[$i]["balance"] = number_format((float)abs($data["balance"]), 0, '.', ',');
            $periods[$i]["cashflow"] = number_format((float)round($payment_amount * (-1)), 0, '.', ',');
            array_push($data["cashflow_values"], (float)$payment_amount * (-1));
            return;
        }
        
        foreach($payments as $payment) {
            if($payment["source"] == "P") { 
                $period_principal = $payment["amount"];             
                $periods[$i]["period"] = $period_counter;
                $interest = $data["balance"] * ($data["interest_rate"] / 100) / 12;                     
                if($period_counter <= $data["toPeriod"]) {
                    $data["total_interest_paid"] += round($interest);
                }
                $payment_amount = $period_principal + $interest;
                $periods[$i]["payment_amount"] = number_format((float)(round($period_principal) + round($interest)), 0, '.', ',');
                $periods[$i]["interest"] = number_format((float)$interest, 0, '.', ',');
                $periods[$i]["principal"] = number_format((float)$period_principal, 0, '.', ',');
                $period_date = date("Ymd", strtotime(date("Ymd", strtotime($data["amortization_startdate"]))."+".($period_counter)." month"));
                $data["balance"] -= $period_principal;
                $periods[$i]["balance"] = number_format((float)abs($data["balance"]), 0, '.', ',');
                $periods[$i]["cashflow"] = number_format((float)round($payment_amount * (-1)), 0, '.', ',');
                array_push($data["cashflow_values"], (float)$payment_amount * (-1));
                
                if($payment !== end($payments)) {
                    $i++;
                }
            }
            //balloon payment
            else {              
                $periods[$i]["period"] = "";
                $periods[$i]["payment_amount"] = number_format((float)($payment["amount"]), 0, '.', ',');
                $periods[$i]["interest"] = "0";
                $periods[$i]["principal"] = "0";
                $periods[$i]["balance"] = number_format((float)abs($data["balance"] - $payment["amount"]), 0, '.', ',');
                $periods[$i]["cashflow"] = number_format((float)round(str_replace(",", "", $periods[$i]["payment_amount"]) * (-1)), 0, '.', ',');
                array_push($data["cashflow_values"], (float)str_replace(",", "", $periods[$i]["cashflow"]));
                
                $data["balance"] -= $payment["amount"];
                $i++;
            }
        }
    }
    
    function computeFutureTerm(&$i, $period_counter, &$periods, &$data) {
        $periods[$i]["period"] = $period_counter;
        $interest = $data["balance"] * ($data["interest_rate"] / 100) / 12;
        if($period_counter <= $data["toPeriod"]) {
            $data["total_interest_paid"] += round($interest);
        }
        $period_principal = $data["balance"] <= ceil($data["employee_principal_amort"])? $data["balance"] : $data["employee_principal_amort"];  
        $ALLOWANCE_FOR_DECIMALS = .5 * 180;
        if($data["balance"] - $period_principal <= $ALLOWANCE_FOR_DECIMALS) {
            $period_principal = $data["balance"];
        }       
        $payment_amount = $period_principal + $interest;                        
        $periods[$i]["payment_amount"] = number_format((float)(round($period_principal) + round($interest)), 0, '.', ',');
        $periods[$i]["interest"] = number_format((float)$interest, 0, '.', ',');
        $periods[$i]["principal"] = number_format((float)$period_principal, 0, '.', ',');
        $data["balance"] -= $period_principal;
        $periods[$i]["balance"] = number_format((float)abs($data["balance"]), 0, '.', ',');
        $periods[$i]["cashflow"] = number_format((float)round($payment_amount * (-1)), 0, '.', ',');
        array_push($data["cashflow_values"], (float)str_replace(",", "", $periods[$i]["cashflow"]));
    }
    
    function getPaymentsMadeAtPeriod($loan_payments, $fromPeriod, $toPeriod) {
        $payments = array();
        foreach($loan_payments as $payment) {
            if($payment["payment_date"] >= $fromPeriod && $payment["payment_date"] < $toPeriod) {
                array_push($payments, $payment);
            }
            else if($payment["payment_date"] > $toPeriod) {
                break;
            }
        }
        return $payments;
    }
}

/* End of file compute_mir_eir_helper.php */
/* Location: ./system/application/helpers/compute_mir_eir_helper.php */