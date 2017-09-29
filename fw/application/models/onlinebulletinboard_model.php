<?php
/*
 * Created on Apr 20, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Onlinebulletinboard_model extends Asi_Model {

    function Onlinebulletinboard_model()
    {
        parent::Asi_Model();
        $this->table_name = 'o_bulletin_board';
        $this->id = 'topic_id';
        $this->date_columns = array();
    }  
}

?>
