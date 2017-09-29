<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Dividend_model extends Asi_Model {
	
    function Dividend_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_dividend';
        $this->id = 'dividend_no';
        $this->date_columns = array();
    }
	
	function getLastDivProcessingDate()
	{
		$sql = "SELECT DISTINCT accounting_period
				FROM t_dividend 
				INNER JOIN t_transaction
				ON t_dividend.accounting_period=t_transaction.transaction_date 
				AND t_dividend.dividend_code=t_transaction.transaction_code";
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
}

?>