<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Comman_controller extends CI_Controller
{ 
    function __construct()
    {
        parent::__construct(); 
        error_reporting(0);        
    }
    
    static function varifyMethod($method_type)
    {	
        try {
            if ($_SERVER['REQUEST_METHOD'] != $method_type){
                throw new Exception(comman_controller::responseMessage(0, "It seems wrong method type is being used. Please verify method type (GET/POST).", "False"));
                   
            }
        }catch (Exception $e){ exit;}   
    }

    static function sendMail($email, $subject, $message) 
    {
        $CI =& get_instance();
        $CI->email->set_newline("\r\n");
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';
        $CI->email->initialize($config);
        $CI->email->from(email_server,'no-reply');
        $CI->email->to($email);
        $CI->email->subject($subject);
        $CI->email->message($message);
        return $CI->email->send(); 
    }
    
    static function sendMailBCC($email,$bccEmail, $subject, $message) 
    {
        $CI =& get_instance();
        $CI->email->set_newline("\r\n");
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';
        $CI->email->initialize($config);
        $CI->email->from(email_server);
        $CI->email->to($email);
        $CI->email->bcc($bccEmail);
        $CI->email->subject($subject);
        $CI->email->message($message);
        return $CI->email->send(); 
    }
    
    static function sendMailSMTP($email, $subject, $message) 
    {
         
        $CI =& get_instance();
        $CI->email->set_newline("\r\n");

        $config = Array(
       'protocol' => 'smtp',
       'smtp_host' => 'ssl://smtp.googlemail.com',
       'smtp_port' => 465,
       'smtp_user' => gmail_email,
       'smtp_pass' => gmail_pass,
       'mailtype'  => 'html', 
       'charset'   => 'iso-8859-1'
    );
    $CI->email->initialize($config);
           $CI->email->from(gmail_email,'no-reply');
           $CI->email->to($email);
           $CI->email->reply_to('no-reply');
           $CI->email->subject($subject);
           $CI->email->message($message);
           return $CI->email->send(); 
   }
    

    static function sendMail_Attach($email, $subject, $message, $file)
    {
        $CI =& get_instance();
        $CI->email->set_newline("\r\n");
        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';
        $CI->email->initialize($config);
        $CI->email->from(email_server,server_name);
        $CI->email->to($email);
        $CI->email->reply_to('no-reply');
        $CI->email->subject($subject);
        $CI->email->message($message);
        foreach($file as $file_name)
        {
         $CI->email->attach($file_name);
        }
        //$CI->email->attach($file);
        return $CI->email->send();
    }
    static function generateToken() 
    {
        $token = openssl_random_pseudo_bytes(16);
        return $token = bin2hex($token); 
    }
     
    static function requiredValidation($elements_array) 
    {
        foreach ($elements_array as $key => $value) {
            try {
                if($key != 'pref_drink' or $key != 'fav_place'){
                if (empty($value))
                {
                    throw new Exception(comman_controller::responseMessage(0, 'Please enter ' . $key . '.', "False"));
                }    
                }
                
            }catch (Exception $e){ exit;}   
        }
    }

    
    static function successResponse($array, $response, $message, $result) 
    {  
        $array['ResponseCode'] = $response; 
        $array['ResponseMsg'] = $message;
        $array['Result'] = $result;
        $array['ServerTime'] = date('T');

        echo json_encode($array);
    }
    
    static function responseMessage($res, $responseMessage, $result)
    {
        echo json_encode(array("ResponseCode"=>$res,"ResponseMsg"=> "$responseMessage","Result"=>"$result","ServerTime"=>date('T')));
    }
    
    static function errorResponse()
    {
        return comman_controller::responseMessage(0,"It seems web-server is too much busy, please try again.","False");
    }
    
    static function generateRandomCode() 
    {
        $alphabet = 'AbCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return strtoupper(implode($pass));
    }

    static function generateRandomString()  {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '1234567890';
        $randomString = '';
        
        for ($i = 0; $i < 1; $i++) {
            $randomString .= $letters[rand(0, strlen($letters) - 1)];
        }
        for ($i = 0; $i < 2; $i++) {
            $randomString .= $digits[rand(0, strlen($digits) - 1)];
        }
        for ($i = 0; $i < 1; $i++) {
            $randomString .= $letters[rand(0, strlen($letters) - 1)];
        }
        for ($i = 0; $i < 1; $i++) {
            $randomString .= $digits[rand(0, strlen($digits) - 1)];
        }
        for ($i = 0; $i < 3; $i++) {
            $randomString .= $letters[rand(0, strlen($letters) - 1)];
        }
        return $randomString;
    }
    
    
    static function responseDate($date)
    {
        return date("M d, Y", strtotime($date));
    }

    static function backgroundPost($url){ 
       
        ignore_user_abort(true); 
        ini_set('max_execution_time', 0);       
        set_time_limit(0);       
        $parts    = parse_url($url); 
      
        // $fp     = fsockopen($parts['host'],isset($parts['port'])?$parts['port']:443, $errno, $errstr, 30);  
        
        $fp=fsockopen('ssl://'. $parts['host'], 443, $errno, $errstr, 30);
        if(!$fp) { 
            return false; 
        }else{ 
            if(!isset($parts['query'])){ 
                $query = ''; 
            }else{ 
                $query = $parts['query']; 
            } 
            $out = "POST ".$parts['path']." HTTP/1.1\r\n"; 
            $out.= "Host: ".$parts['host']."\r\n"; 
            $out.= "Content-Type: application/x-www-form-urlencoded\r\n"; 
            $out.= "Content-Length: ".strlen($query)."\r\n"; 
            $out.= "Connection: Close\r\n\r\n"; 
            $out.= $query;             
            fwrite($fp, $out); 
            fclose($fp); 
            return true; 
        } 
    }


}