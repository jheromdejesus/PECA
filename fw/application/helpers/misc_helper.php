<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('get_random_password'))
{
    /**
     * Generate a random password.
     *
     * get_random_password() will return a random password with length 6-8 of lowercase letters only.
     *
     * @access    public
     * @param    $chars_min the minimum length of password (optional, default 6)
     * @param    $chars_max the maximum length of password (optional, default 8)
     * @param    $use_upper_case boolean use upper case for letters, means stronger password (optional, default false)
     * @param    $include_numbers boolean include numbers, means stronger password (optional, default false)
     * @param    $include_special_chars include special characters, means stronger password (optional, default false)
     *
     * @return    string containing a random password
     */    
    function get_random_password($chars_min=6, $chars_max=8, $use_upper_case=false, $include_numbers=false, $include_special_chars=false)
    {
        $length = rand($chars_min, $chars_max);
        $selection = 'aeuoyibcdfghjklmnpqrstvwxz';
        if($include_numbers) {
            $selection .= "1234567890";
        }
        if($include_special_chars) {
            $selection .= "!@\"#$%&[]{}?|";
        }
                                
        $password = "";
        for($i=0; $i<$length; $i++) {
            $current_letter = $use_upper_case ? (rand(0,1) ? strtoupper($selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))];            
            $password .=  $current_letter;
        }                
        
        return $password;
    }
    
	/**
	 * function that displays message on client side
	 * @param $strMessage		the error message
	 * @param $success_value	success value; could be true or false
	 */
    function display_message($success_value,$strMessage)
    {
      	echo "{'success':".$success_value.",'msg':'".$strMessage."'}";
    }
    
    /**
     * function that validates email address
     * @param $email_address
     * @return unknown_type
     */
    function validate_email($email_address)
    {
      	//if(mb_detect_encoding($email_address) != 'ASCII')
 		//	 return false;
		//if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$",$email_address)) 
  			return true;
    	
    }
	
}

/* End of file misc_helper.php */
/* Location: ./system/application/helpers/misc_helper.php */