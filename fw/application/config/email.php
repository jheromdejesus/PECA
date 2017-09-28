<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
|EMAIL CONFING 
|--------------------------------------------------------------------------
|
|Configuration of outgoing mail server. 
| 
|
*/
$config['protocol'] = "smtp";
$config['smtp_host'] = "ssl://smtp.gmail.com";//192.36.253.2
$config['smtp_port'] = '465'; //25
$config['smtp_timeout']='30';  
$config['smtp_user'] = 'pecadmnstrtr@gmail.com';
$config['smtp_pass'] = 'pecaadmin';
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['validation'] = TRUE; // bool whether to validate email or not
$config['mailtype'] = 'html'; // or text
$config['newline'] = "\r\n";
# /* End of file email.php */  
# /* Location: ./system/application/config/email.php */  
