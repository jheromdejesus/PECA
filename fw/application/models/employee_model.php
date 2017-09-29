<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Employee_model extends Asi_Model {
	
    function Employee_model()
    {
        parent::Asi_Model();
        $this->table_name = 'm_employee';
        $this->id = 'employee_id';
        $this->date_columns = array('');
    }
    
}

?>
