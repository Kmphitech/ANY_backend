<?php
header('Content-type: application/json; charset=utf-8');
require_once("Comman_controller.php");
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
//ob_start();

class User extends CI_Controller {
  public function __construct()
  {
    parent::__construct();
  }

  public function sendWhatsAppOTP($value='')
  {
    $post_field = array("To"=>"whatsapp:+919081360241","From"=>"whatsapp:+14155238886","Body"=>"Hey Mk, Your OTP code is 1238432");
    $post_field = http_build_query($post_field);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.twilio.com/2010-04-01/Accounts/ACf5c8f64669f8f78d822754f8bf4283aa/Messages.json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($post_field));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_USERPWD, 'ACf5c8f64669f8f78d822754f8bf4283aa:7cf3dff236e9ba7b410e4caba1ae4e5c');
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
      return false;
    }
    // $result = json_decode($result);
    print_r($result);

  }

  public function getWordsList()
  {
      try{
          comman_controller::varifyMethod("POST");
          extract($_POST);
          
          $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
         
          comman_controller::requiredValidation([
              'key' => $key                      
           ]);

          if($this->model->checkKeyExist($key)==0){
            return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
          }
            
                  
          $getWords = $this->model->querydata("SELECT word_id,word_text FROM secret_word_master ORDER BY rand() LIMIT 12");
          $data['data']=$getWords;
          return comman_controller::successResponse($data,1,'get word list successfully','True');    
             
      } 
      catch (Exception $e) {
          return comman_controller::responseMessage(0, "Something went wrong while get word, please try again.", "False");
      }

  }

  public function getAllWordsList()
  {
      try{
          comman_controller::varifyMethod("POST");
          extract($_POST);
          
          $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
         
          comman_controller::requiredValidation([
              'key' => $key                      
           ]);

          if($this->model->checkKeyExist($key)==0){
            return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
          }
            
                  
          $getWords = $this->model->querydata("SELECT word_id,word_text FROM secret_word_master ORDER BY rand()");
          $data['data']=$getWords;
          return comman_controller::successResponse($data,1,'get word list successfully','True');    
             
      } 
      catch (Exception $e) {
          return comman_controller::responseMessage(0, "Something went wrong while get word, please try again.", "False");
      }

  }
  
  public function signUp()
   {
    comman_controller::varifyMethod("POST");
    extract($_POST);
    
    $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];  
    $profile=$_FILES['profile']['name']; 

    comman_controller::requiredValidation([
      'key' => $key,
      "word_ids"=>$word_ids
    ]);

    if($this->model->checkKeyExist($key)==0){
      return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
    }
      
      if($phone==""){
        $phone="";
      }
      if($givenName==""){
        $givenName="";
      }
      if($familyName==""){
        $familyName="";
      }
      if($device_type==""){
        $device_type="";
      }

      if($device_token==""){
        $device_token="";
      } 

     
      if($profile!="" || $profile!=NULL){         
        $ext= pathinfo($profile, PATHINFO_EXTENSION);
        $newname=uniqid()."_profile_".time().".".$ext;
        $tmpname=$_FILES['profile']['tmp_name'];
        move_uploaded_file($tmpname, "assets/profile/".$newname);
      }
      else{
        $newname="";
      }
      
      $user_word = $word_ids;
      $word_ids = explode(",", $word_ids);
      if (count($word_ids)>12) {
        return comman_controller::responseMessage(0, "You can not set word more than 12 words .", "False");
      }
      
      $chk_wordSecret=$this->model->sel_row("user_master",array("word_ids"=>$user_words));
      if (count($chk_wordSecret)>0) {
        return comman_controller::responseMessage(0, "Word secret is already selected by other user,please try another one.", "False");
      }

      $insdata=array("givenName"=>$givenName,"familyName"=>$familyName,"phone"=>$phone,"profile"=>$newname,"word_ids"=>$user_word,"device_type"=>$device_type,"device_token"=>$device_token,"created_date"=>cur_date_time,"updated_date"=>cur_date_time);

      

      $generateToken = comman_controller::generateToken();

      $insid=$this->model->insert("user_master",$insdata);
      foreach ($word_ids as $value) {
        $this->model->insert("user_secret_word_master",array("word_id"=>$value,"user_id"=>$insid,"created_date"=>cur_date_time,"updated_date"=>cur_date_time));
      }
      $this->model->insert('key_token_master',array("key"=>$key,"token"=>$generateToken,"cdate"=>cur_date_time));

      $user_detail=$this->model->sel_row("user_master",array("user_id"=>$insid));
      
      if(count($user_detail)>0){
        $user_data=$this->getUserDtl($user_detail,$generateToken);
        $data['data'] = $user_data; 
        
        return comman_controller::successResponse($data,1,"registration Successfully",'True');
               
      }
      else{
        return comman_controller::responseMessage(0, "Something went wrong while Registration, please try again.", "False");
      }
    
  }

  public function getUserDtl($user_detail,$generateToken)
  {
            
    if($user_detail->profile!=""){
      $user_detail->profile = server_url."assets/profile/".$user_detail->profile;
    }
    else{
       $user_detail->profile = "";
    }
    $getMySecret = $this->model->querydata("SELECT sw.word_id,sw.word_text FROM user_secret_word_master usw JOIN secret_word_master sw ON sw.word_id=usw.word_id WHERE usw.user_id=$user_detail->user_id ");
    $user_detail->myword_list = $getMySecret;
    $user_detail->token = $generateToken; 
    unset($user_detail->created_date);
    unset($user_detail->updated_date);
    
    return $user_detail;
  }
  
   
  
  public function isRegister()
  {
    comman_controller::varifyMethod("POST");
    extract($_POST);
    
    $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];  
    
    comman_controller::requiredValidation([
      'key' => $key
    ]);

    if(($is_fb=="" || $is_fb==NULL) && ($is_google=="" || $is_google==NULL) && ($email=="" || $email==NULL)){
       return comman_controller::responseMessage(0, "Please enter is_fb or is_google or email.", "False");
    }

    else if($is_fb==1){
      comman_controller::requiredValidation([
            'fb_id' => $fb_id
      ]);
    }
    else if($is_google==1){
      comman_controller::requiredValidation([
            'google_id'=>$google_id
      ]);
    } 
   
    if($this->model->checkKeyExist($key)==0){
      return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
    }
      
      if($email!=""){
        $user_detail=$this->model->sel_row("user_master",array("email"=>$email));
      }
       else {
        if($is_fb==1){
          $user_detail=$this->model->sel_row("user_master",array("fb_id"=>$fb_id));
        
        }

        else if($is_google==1){
          $user_detail=$this->model->sel_row("user_master",array("google_id"=>$google_id));
        }
      }

        if(count($user_detail)>0 && $user_detail->is_confirm==0){
          return comman_controller::responseMessage(2, "Please click on the confirmation link sent to your email to activate this account.", "False");
        }
        else if(count($user_detail)>0 && $user_detail->is_fb==0 && $user_detail->is_google==0){
          comman_controller::responseMessage(4,"$user_detail->email is registered as a normal user. Please login as a normal user or try with different email id.",'True');
        }               
        else if(count($user_detail)>0 && $is_fb==1 && $user_detail->is_google==1){
           comman_controller::responseMessage(4,"$user_detail->email is registered as a google user. Please login as a google user or try with different email id.",'True');
        }
        else if(count($user_detail)>0 && $is_google==1 && $user_detail->is_fb==1){
           comman_controller::responseMessage(4,"$user_detail->email is registered as a facebook user. Please login as a facebook user or try with different email id.",'True');
        }
      
        else if(count($user_detail)>0){  
          $userData=$this->getReguserData($device_type,$device_token,$user_detail);
          $getUserSocialData = $this->model->querydata("SELECT * FROM user_social_login_master WHERE user_id = $userData->user_id");
          $userData->social_login= $getUserSocialData;
          $data['data'] = $userData; 
                  
          comman_controller::successResponse($data, 1,'Login successful.','True');
        }
        else{
          return comman_controller::responseMessage(3, "This user is not registered with us.", "False");
        }             
  }
 

 
  public function getReguserData($device_type,$device_token,$user_detail)
  {
      if($device_type==""){
        $device_type="";
      }

      if($device_token==""){
        $device_token="";
      }

      $upddata=array("device_type"=>$device_type,"device_token"=>$device_token,"updated_date"=>cur_date_time);
      $this->model->update("user_master",$upddata,array("user_id"=>$user_detail->user_id));

        
        if($user_detail->profile!=""){
          $user_detail->profile = server_url."assets/profile/".$user_detail->profile;
        }
        else{
           $user_detail->profile = "";
        }
        $getMySecret = $this->model->querydata("SELECT sw.word_id,sw.word_text FROM user_secret_word_master usw JOIN secret_word_master sw ON sw.word_id=usw.word_id WHERE usw.user_id=$user_detail->user_id ");
        $user_detail->myword_list = $getMySecret;

        $tokenRet=$this->model->querydatar("select * from key_token_master order by rand()");
        $user_detail->token = $tokenRet->token;
        unset($user_detail->created_date);
        unset($user_detail->updated_date);
        return $user_detail; 
  }
  
  
  public function login(){

      try{
      comman_controller::varifyMethod('POST');
      extract($_POST);
      $key=empty($_SERVER['HTTP_KEY']) ? '' :$_SERVER['HTTP_KEY'];

      comman_controller::requiredValidation([
          'Key'=>$key,
          'word_ids'=>$word_ids
      ]);

          if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0,"Please enter correct key, eneterd key doesn’t match with provided key.","False");
              }
          else{

            $user_words = $word_ids;
            $word_ids = explode(",", $word_ids);
            if (count($word_ids)>12) {
              return comman_controller::responseMessage(0, "You can not allow to enter more than 12 word.", "False");
            }
            
            $user_detail=$this->model->sel_row("user_master",array("word_ids"=>$user_words));
            if (count($user_detail) == 0) {
              return comman_controller::responseMessage(0, "Your word secret is wrong,please try again.", "False");
            }

            $userData=$this->getReguserData($device_type,$device_token,$user_detail);
          $getUserSocialData = $this->model->querydata("SELECT * FROM user_social_login_master WHERE user_id = $userData->user_id");
          $userData->social_login= $getUserSocialData;
          $data['data'] = $userData; 
                    
            comman_controller::successResponse($data, 1,'Login successful','True');

          }  
      }  
      catch (Exception $e) {
           return comman_controller::responseMessage(0, "Something went wrong while login, please try again.", "False");
      }
    } 
  

  
    
   
    public function updateProfile()
    {
    
          try{
              comman_controller::varifyMethod("POST");
              extract($_POST);
              
              $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
              $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
              $profile=$_FILES['profile']['name'];
              comman_controller::requiredValidation([
                  'key' => $key,
                  'token' => $token,
                  'user_id'=>$user_id
               ]);

          if($this->model->checkKeyExist($key)==0){
            return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
          }
          else if($this->model->checkKeyTokenExist($key,$token)==0){
              return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
          }            

              $userdtl=$this->model->sel_row('user_master',array("user_id"=>$user_id));  
              
               if(count($userdtl)>0){
                  if($profile!="" || $profile!=NULL){         
                    $ext= pathinfo($profile, PATHINFO_EXTENSION);
                    $newname=uniqid()."_profile_".time().".".$ext;
                    $tmpname=$_FILES['profile']['tmp_name'];
                    move_uploaded_file($tmpname, "assets/profile/".$newname);
                    
                  }
                  else{
                    $newname=$userdtl->profile;
                  } 
                  
                  if($phone=="" || $phone==NULL){
                      $phone=$userdtl->phone;
                  }

                  if($givenName=="" || $givenName==NULL){
                      $givenName=$userdtl->givenName;
                  }

                  if($familyName=="" || $familyName==NULL){
                      $familyName=$userdtl->familyName;
                  }
    
                  $upddata=array("familyName"=>$familyName,"phone"=>$phone,"profile"=>$newname,"updated_date"=>cur_date_time);
                  
                  $this->model->update("user_master",$upddata,array("user_id"=>$user_id));
                
                  $user_detail=$this->model->sel_row("user_master",array("user_id"=>$user_id));

                  if(count($user_detail)>0){
                    $tokenRet=$this->model->querydatar("select * from key_token_master order by rand()");
                    $user_data=$this->getUserDtl($user_detail,$tokenRet->token);                       
                    $data['data'] = $user_data; 
                    comman_controller::successResponse($data, 1,'Profile has been updated successfully','True');
                  }
                  else{
                      return comman_controller::responseMessage(0, "Something went wrong while update rofile, please try again.", "False");
                  }
              }
              else{
                  return comman_controller::responseMessage(0, "Unable to update profile because of Invalid user_id or account not confirm.", "False");
              }    
          }
     
      catch (Exception $e) {
          return comman_controller::responseMessage(0, "Something went wrong while update profile, please try again.", "False");
      }  
          
    }   
    
    public function logOut()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id'=>$user_id                          
             ]);

            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }
            else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
               
              $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
                        
             if($count_user>0){
                    
              $this->model->update("user_master",array("device_type"=>"","device_token"=>"","updated_date"=>cur_date_time),array("user_id"=>$user_id));
              return comman_controller::responseMessage(1,'LogOut successfully','True');    
            }
            else{
                return comman_controller::responseMessage(0, "Unable to logout because of Invalid user_id.", "False");
            }    
        } 
      catch (Exception $e) {
          return comman_controller::responseMessage(0, "Something went wrong while logout, please try again.", "False");
      }

    }

    public function getUserList()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id'=>$user_id                          
             ]);

            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }
            else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
               
              $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
                        
             if($count_user>0){
              
              $imagepath = base_url()."assets/profile/";
              $getUserList = $this->model->sel_fld_res("user_id,name,phone,IF(profile != '',concat('$imagepath', profile),'') as profile","user_master",array("user_id != "=>$user_id));
              //  $getUserList = $this->model->querydata("SELECT um.* FROM `user_master` um join `user_favorite_master` ufm where um.user_id != 1 AND ufm.favorite_user_id != um.user_id");
              foreach ($getUserList as $key => $value) {
                $chackuser_id = $value->user_id;
                $count_favorite_user=$this->model->record_count('user_favorite_master',array('user_id'=>$user_id,'favorite_user_id'=>$chackuser_id));  
                if($count_favorite_user>0){
                   $value->is_favorite = 1;
                }else if($count_favorite_user == 0){
                  $value->is_favorite = 0;
                }
                $value->myword_list = $this->model->querydata("SELECT sw.word_id,sw.word_text FROM user_secret_word_master usw JOIN secret_word_master sw ON sw.word_id=usw.word_id WHERE usw.user_id=$chackuser_id ");
                $value->social_login = $this->model->querydata("SELECT uslm.us_id,uslm.user_id,uslm.connect_id,uslm.social_type,uslm.connect_point,uslm.is_public,nm.status,IF((nm.noti_id != '' AND nm.status = 'Accepted'),1,0) as is_connected  FROM user_social_login_master as uslm LEFT JOIN notification_master as nm ON (nm.us_id=uslm.us_id AND nm.user_id='$user_id') WHERE uslm.user_id='$chackuser_id' ");
              }
              $data['data']= $getUserList;
              if(isset($contact_list) && $contact_list != "" && $contact_list != NULL){
                $data['contact_list']= json_decode(base64_decode($contact_list),true);    
              }
              
              return comman_controller::successResponse($data,1,'get user list successfully','True');    
            }
            else{
                return comman_controller::responseMessage(0, "Unable to get user because of Invalid user_id.", "False");
            }    
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while get user, please try again.", "False");
        }
    }

    public function addSocialLogin()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id'=>$user_id,
                'connect_id'=>$connect_id,
                'social_type'=>$social_type,
                'connect_point'=>$connect_point                       
             ]);

            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }
            else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
               
              $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
                        
             if($count_user>0){
              $chkRegister = $this->model->sel_fld_res("*","user_social_login_master",array("connect_id"=>$connect_id));
              if (count($chkRegister)>0) {
                return comman_controller::responseMessage(0, "user has already register with same id.", "False");
              }

              $this->model->insert("user_social_login_master",array("user_id"=>$user_id,"connect_id"=>$connect_id,"social_type"=>$social_type,"connect_point"=>$connect_point,"created_date"=>cur_date_time,"updated_date"=>cur_date_time));

              $getSocialData = $this->model->sel_fld_res("us_id,connect_id,social_type,connect_point,is_public","user_social_login_master",array("user_id"=>$user_id));
              $data['data']= $getSocialData;
              return comman_controller::successResponse($data,1,'social account added successfully','True');    
            }
            else{
                return comman_controller::responseMessage(0, "Unable to addSocialLogin because of Invalid user_id.", "False");
            }    
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while addSocialLogin, please try again.", "False");
        }

    }

    public function updateConnectPoint()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id'=>$user_id,
                'us_id'=>$us_id,
                'connect_point'=>$connect_point                          
             ]);

            $is_public = isset($is_public)?$is_public:0;
            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }
            else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
               
              $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
                        
             if($count_user>0){
              $chkSocialAccount = $this->model->sel_fld_res("*","user_social_login_master",array("us_id"=>$us_id));
              if (count($chkSocialAccount)==0) {
                return comman_controller::responseMessage(0, "user has not added social, please add first and then update point.", "False");
              }
              
              $this->model->update("user_social_login_master",array("connect_point"=>$connect_point,"is_public"=>$is_public,"updated_date"=>cur_date_time),array("us_id"=>$us_id));

              $getSocialData = $this->model->sel_fld_res("us_id,connect_id,social_type,connect_point,is_public","user_social_login_master",array("user_id"=>$user_id));
              $data['data']= $getSocialData;
              return comman_controller::successResponse($data,1,'social account point updated successfully','True');    
            }
            else{
                return comman_controller::responseMessage(0, "Unable to updateConnectPoint because of Invalid user_id.", "False");
            }    
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while updateConnectPoint, please try again.", "False");
        }

    }

    public function removeSocialLogin()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id'=>$user_id,
                'us_id'=>$us_id
             ]);

            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }
            else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
               
              $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
                        
             if($count_user>0){
              $chkSocialAccount = $this->model->sel_fld_res("*","user_social_login_master",array("us_id"=>$us_id));
              if (count($chkSocialAccount)==0) {
                return comman_controller::responseMessage(0, "Invalid us_id,please try again.", "False");
              }
              
              $this->model->delete("user_social_login_master",array("us_id"=>$us_id));

              $getSocialData = $this->model->sel_fld_res("us_id,connect_id,social_type,connect_point,is_public","user_social_login_master",array("user_id"=>$user_id));
              $data['data']= $getSocialData;
              return comman_controller::successResponse($data,1,'social account deleted successfully','True');    
            }
            else{
                return comman_controller::responseMessage(0, "Unable to removeSocialLogin because of Invalid user_id.", "False");
            }    
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while removeSocialLogin, please try again.", "False");
        }

    }

    public function sendConnectRequest()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id'=>$user_id,
                'to_user_id'=>$to_user_id,
                'us_id'=>$us_id,
                'requested_point'=>$requested_point
             ]);

            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }
            else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
               
              $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
                        
             if($count_user>0){
              $chkSocialAccount = $this->model->sel_fld_row("*","user_social_login_master",array("us_id"=>$us_id,"user_id"=>$to_user_id));
              if (count($chkSocialAccount)==0) {
                return comman_controller::responseMessage(0, "Invalid us_id,please try again.", "False");
              }

              $chkRequest = $this->model->sel_fld_res("*","notification_master",array("us_id"=>$us_id,"user_id"=>$user_id));
              if (count($chkRequest)>0) {
                return comman_controller::responseMessage(0, "you are already requested.", "False");
              }

              $getUser = $this->model->sel_row("user_master",array("user_id"=>$user_id));
              if ($getUser->points < $requested_point) {
                return comman_controller::responseMessage(0, "You have insufficient point.", "False");
              }
              $updatedpoint = $getUser->points - $requested_point;
              $this->model->update("user_master",array("points"=>$updatedpoint),array("user_id"=>$user_id));

              if ($requested_point < $chkSocialAccount->connect_point) {
                return comman_controller::responseMessage(0, "Your connect request has decline.", "False");
              }
              
              $this->model->insert("notification_master",array("user_id"=>$user_id,"us_id"=>$us_id,"requested_point"=>$requested_point,"to_user_id"=>$to_user_id,"msg"=>"Request to add you to contact list"));
              $getNotiData = $this->model->sel_fld_res("*","notification_master",array("user_id"=>$user_id));
              $data['data']= $getNotiData;
              return comman_controller::successResponse($data,1,'request sent successfully','True');    
            }
            else{
                return comman_controller::responseMessage(0, "Unable to sendConnectRequest because of Invalid user_id.", "False");
            }    
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while sendConnectRequest, please try again.", "False");
        }

    }

    public function acceptRejectRequest()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id'=>$user_id,
                'noti_id'=>$noti_id,
                'status'=>$status
             ]);

            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }
            else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
               
              $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
                        
             if($count_user>0){
              

              $chkRequest = $this->model->sel_fld_row("*","notification_master",array("noti_id"=>$noti_id,"user_id"=>$user_id));
              if (count($chkRequest)==0) {
                return comman_controller::responseMessage(0, "notification not found.", "False");
              }

              if ($status == "Rejected") {
                return comman_controller::responseMessage(0, "Your connect request has decline.", "False");
              }
              if ($status == "Accepted") {
                $getUser = $this->model->sel_row("user_master",array("user_id"=>$chkRequest->to_user_id));
                $updatedpoint = $getUser->points + $chkRequest->requested_point;
                $this->model->update("user_master",array("points"=>$updatedpoint),array("user_id"=>$getUser->user_id));
              }
              $this->model->update("notification_master",array("status"=>$status,"msg"=>"Great News! accept your contact request. you can able to contact with him"),array("noti_id"=>$noti_id));
              $getSocialData = $this->model->sel_fld_res("*","notification_master",array("user_id"=>$user_id));
              $data['data']= $getSocialData;
              return comman_controller::successResponse($data,1,'request $status successfully','True');    
            }
            else{
                return comman_controller::responseMessage(0, "Unable to acceptRejectRequest because of Invalid user_id.", "False");
            }    
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while acceptRejectRequest, please try again.", "False");
        }

    }

    public function getNotification()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id'=>$user_id
             ]);

            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }
            else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
               
              $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
              if($count_user>0){
              $imagepath = base_url()."assets/profile/";
               $getNotiData = $this->model->querydata("SELECT nm.noti_id,IF(nm.user_id=$user_id,0,1) as noti_type,nm.user_id,nm.to_user_id,nm.us_id,nm.status,nm.msg,nm.requested_point,nm.updated_date,um.name,um.profile,IF(um.profile != '',concat('$imagepath', um.profile),'') as profile FROM notification_master nm LEFT JOIN user_master um ON ((um.user_id=nm.user_id AND nm.to_user_id=$user_id) OR (um.user_id=nm.to_user_id AND nm.user_id=$user_id) ) WHERE ((nm.to_user_id=$user_id AND nm.status = 'Pending') OR (nm.user_id=$user_id AND nm.status != 'Pending'))");
              $data['data']= $getNotiData;
              return comman_controller::successResponse($data,1,'get Notification List successfully','True');    
            }
            else{
                return comman_controller::responseMessage(0, "Unable to sendConnectRequest because of Invalid user_id.", "False");
            }    
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while sendConnectRequest, please try again.", "False");
        }

    }
    
     public function getUserProfile()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id'=>$user_id,
                'current_user_id' => $current_user_id, 
             ]);

            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }
            else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
               
              $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
              if($count_user>0){
              $imagepath = base_url()."assets/profile/";
              $getUserData = $this->model->querydatar("SELECT *,profile,IF(profile != '',concat('$imagepath', profile),'') as profile FROM user_master WHERE user_id = $user_id");
              $getUserSocialData = $this->model->querydata("SELECT * FROM user_social_login_master WHERE user_id = $user_id");
               $getMySecret = $this->model->querydata("SELECT sw.word_id,sw.word_text FROM user_secret_word_master usw JOIN secret_word_master sw ON sw.word_id=usw.word_id WHERE usw.user_id=$user_id ");
              $data['data']= $getUserData;
              $count_favorite_user=$this->model->record_count('user_favorite_master',array('user_id'=>$current_user_id,'favorite_user_id'=>$user_id));  
                if($count_favorite_user>0){
                   $data['data']->is_favorite = 1;
                }else if($count_favorite_user == 0){
                  $data['data']->is_favorite = 0;
                }
              $data['data']->myword_list = $getMySecret;
              $data['data']->social_login= $getUserSocialData;

              return comman_controller::successResponse($data,1,'get User Profile successfully','True');    
            }
            else{
                return comman_controller::responseMessage(0, "Unable to sendConnectRequest because of Invalid user_id.", "False");
            }    
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while sendConnectRequest, please try again.", "False");
        }

    }
    
    public function favoriteProfile()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id'=>$user_id
            ]);
            if ($contact_id == "" || $contact_id == NULL) {
              $contact_id = 0;
            }
            if ($favorite_user_id == "" || $favorite_user_id == NULL) {
              $favorite_user_id = 0;
            }
            if ($favorite_user_id == 0 && $contact_id == 0) {
              return comman_controller::responseMessage(0, "Please enter atleast one either favorite_user_id or contact_id.", "False");
            }
            
            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }
            else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
               
              $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
              if($count_user>0){
                  if ($contact_id != 0) {
                    $chkfavorite_user=$this->model->record_count('user_contact_list_master',array("contact_id"=>$contact_id));  
                  }
                  else
                  {
                    $chkfavorite_user=$this->model->record_count('user_master',array("user_id"=>$favorite_user_id));  
                  }
                  if($chkfavorite_user>0){
                      $chkAlreadyfavorite=$this->model->record_count('user_favorite_master',array('user_id'=>$user_id,'favorite_user_id'=>$favorite_user_id,"contact_id"=>$contact_id));  
                      if($chkAlreadyfavorite>0){
                        $this->model->delete("user_favorite_master",array('user_id'=>$user_id,'favorite_user_id'=>$favorite_user_id,"contact_id"=>$contact_id));
                        return comman_controller::responseMessage(1, "User remove from favorite list successfully.", "True");
                      }
                      $insdata = array("user_id"=>$user_id,"favorite_user_id"=>$favorite_user_id,"contact_id"=>$contact_id);
                      $insid=$this->model->insert("user_favorite_master",$insdata);
                      return comman_controller::responseMessage(1, "User add in favorite list successfully.", "True");
                 
                  }
                  else{
                    return comman_controller::responseMessage(0, "Unable to Favorite because of Invalid favorite user id or contact id .", "False");
                  }  
             }
            else{
                return comman_controller::responseMessage(0, "Unable to Favorite because of Invalid user id.", "False");
            }    
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while sendConnectRequest, please try again.", "False");
        }

    }


    public function getFavoriteList()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id'=>$user_id,
              ]);

            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }
            else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
               
              $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
              if($count_user>0){
                $favorite_user=$this->model->select_ser('user_favorite_master',array("user_id"=>$user_id));
                $resData = array();  
                foreach ($favorite_user as $key => $value) {
                  
                  $favorite_user_id = $value->favorite_user_id;
                  $getUserDtl = "";
                  if ($value->contact_id != 0 && $favorite_user_id == 0) {
                    $getUserids =$this->model->sel_row("user_contact_list_master",array("contact_id"=>$value->contact_id));
                    $favorite_user_id = $getUserids->user_id;
                    $getUserDtl = $this->getContactDtlById(0,$getUserids->givenName,$getUserids->familyName);
                  }
                  else{
                    $chkConnected = $this->model->sel_row("notification_master",array("user_id"=>$user_id,"to_user_id"=>$favorite_user_id,"status"=>"Accepted"));
                    if (count($chkConnected)>0) {
                      $getUserDtl = $this->getContactDtlById($favorite_user_id);
                    }
                  }
                  if ($getUserDtl != "" && $getUserDtl != null) {
                    $resData[] = $getUserDtl;
                  }
                  /*$imagepath = base_url()."assets/profile/";
                  $getUserData = $this->model->querydatar("SELECT *,profile,IF(profile != '',concat('$imagepath', profile),'') as profile FROM user_master WHERE user_id = $favorite_user_id");
                  $getUserSocialData = $this->model->querydata("SELECT * FROM user_social_login_master WHERE user_id = $favorite_user_id");
                  $getMySecret = $this->model->querydata("SELECT sw.word_id,sw.word_text FROM user_secret_word_master usw JOIN secret_word_master sw ON sw.word_id=usw.word_id WHERE usw.user_id=$favorite_user_id");
                  $objData = new stdClass();
                  $objData= $getUserData;
                  $objData->myword_list = $getMySecret;
                  $objData->social_login= $getUserSocialData;
                  $resData[] = $objData;*/
                }  
                $data['data'] = $resData;

                return comman_controller::successResponse($data,1,'get favorite user list successfully','True');                    
              }
              else{
                return comman_controller::responseMessage(0, "Unable to sendConnectRequest because of Invalid user id.", "False");
              }    
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while sendConnectRequest, please try again.", "False");
        }

    }
    
    public function syncContactList()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id' => $user_id,
                'contact_list' => $contact_list
             ]);

            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
              
            $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
            if($count_user>0){        
              $contactData = json_decode(base64_decode($contact_list),true);
              if ($contactData == "" || $contactData == null) {
                return comman_controller::responseMessage(0, "Incorrect contact list json.", "False");
              }
              foreach ($contactData as $key => $value) {

                $contactobj = (object)$value;
                $subvalue = $value;

                  $chkContact = $this->model->sel_row("user_contact_list_master",array("givenName"=>$contactobj->givenName,"familyName"=>$contactobj->familyName,"user_id"=>$user_id));
                  $birthday = (isset($contactobj->birthday))?$contactobj->birthday:'';
                  $insData = array("givenName"=>$contactobj->givenName,"jobTitle"=>$contactobj->jobTitle,"organizationName"=>$contactobj->organizationName,"familyName"=>$contactobj->familyName,"birthday"=>$birthday,"user_id"=>$user_id);
                  // print_r($insData);
                  $contact_id = 0;
                  if (count($chkContact)==0) {
                    $insData['created_date']=cur_date_time;
                    $insData['updated_date']=cur_date_time;
                    $contact_id = $this->model->insert("user_contact_list_master",$insData);
                  }
                  else
                  {
                    $insData['updated_date']=cur_date_time;
                    $contact_id = $chkContact->contact_id;
                    $this->model->update("user_contact_list_master",$insData,array("contact_id"=>$contact_id));
                  }
                  $arrayField = array("postalAddresses","socialProfiles","phoneNumbers","emailAddresses");
                  foreach ($arrayField as $fieldname) {
                    if ($subvalue[$fieldname]) {
                      $arrData = $subvalue[$fieldname];
                      if (count($arrData)>0) {
                        foreach ($arrData as $arrkey => $arrvalue) {
                          if ($fieldname == "postalAddresses") {
                            $this->model->delete("contact_postal_addresses",array("user_id"=>$user_id,"contact_id"=>$contact_id));
                            $insertDt = array("subAdministrativeArea"=>$arrvalue['subAdministrativeArea'],"subLocality"=>$arrvalue['subLocality'],"street"=>$arrvalue['street'],"isoCountryCode"=>$arrvalue['isoCountryCode'],"country"=>$arrvalue['country'],"city"=>$arrvalue['city'],"label"=>$arrvalue['label'],"state"=>$arrvalue['state'],"postalCode"=>$arrvalue['postalCode'],"user_id"=>$user_id,"contact_id"=>$contact_id,"created_date"=>cur_date_time,"updated_date"=>cur_date_time);
                            // print_r($insertDt);
                            $this->model->insert("contact_postal_addresses",$insertDt);
                          }
                          else if ($fieldname == "socialProfiles") {
                            $this->model->delete("contact_social_profiles",array("user_id"=>$user_id,"label"=>$arrvalue['label']));
                            $insertDt = array("service"=>$arrvalue['service'],"label"=>$arrvalue['label'],"urlString"=>$arrvalue['urlString'],"username"=>$arrvalue['username'],"user_id"=>$user_id,"contact_id"=>$contact_id,"created_date"=>cur_date_time,"updated_date"=>cur_date_time);
                            // print_r($insertDt);
                            $this->model->insert("contact_social_profiles",$insertDt);
                          }
                          else if ($fieldname == "phoneNumbers") {
                            $this->model->delete("contact_phonenumbers",array("user_id"=>$user_id,"stringValue"=>$arrvalue['stringValue']));
                            $insertDt = array("stringValue"=>$arrvalue['stringValue'],"label"=>$arrvalue['label'],"user_id"=>$user_id,"contact_id"=>$contact_id,"created_date"=>cur_date_time,"updated_date"=>cur_date_time);
                            // print_r($insertDt);
                            $this->model->insert("contact_phonenumbers",$insertDt);
                          }
                          else if ($fieldname == "emailAddresses") {
                            $this->model->delete("contact_email_addresses",array("user_id"=>$user_id,"value"=>$arrvalue['value']));
                            $insertDt = array("value"=>$arrvalue['value'],"label"=>$arrvalue['label'],"user_id"=>$user_id,"contact_id"=>$contact_id,"created_date"=>cur_date_time,"updated_date"=>cur_date_time);
                            // print_r($insertDt);
                            $this->model->insert("contact_email_addresses",$insertDt);
                          }
                          
                        }  
                      }
                    }  
                  }
              }
              $data['data']=$contactData;
              return comman_controller::successResponse($data,1,'Contact syncing successfully','True');    
            }
            else{
                return comman_controller::responseMessage(0, "Unable to sendConnectRequest because of Invalid user id.", "False");
            }
               
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while get word, please try again.", "False");
        }

    }

    public function getHome()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id'=>$user_id                          
             ]);

            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }
            else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
               
              $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
                        
             if($count_user>0){
              
              $getContactList = $this->getContactByUser($user_id);
              $data['data']=$getContactList;
              if(isset($contact_list) && $contact_list != "" && $contact_list != NULL){
                $data['contact_list']= $contact_list;    
              }
              return comman_controller::successResponse($data,1,'get home data successfully','True');    
            }
            else{
                return comman_controller::responseMessage(0, "Unable to get home because of Invalid user_id.", "False");
            }    
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while get home, please try again.", "False");
        }
    }

    public function getContactByUser($user_id)
    {
      $getContactList = $this->model->sel_fld_res("*","user_contact_list_master",array("user_id"=>$user_id));
      foreach ($getContactList as $key => $value) {
        $chkOlaUser = $this->model->sel_row("user_master",array("givenName"=>$value->givenName,"familyName"=>$value->familyName));
        $value->is_ola_user = "0";
        $value->ola_user_id= "0";
        if (count($chkOlaUser)>0) {
          $value->is_ola_user = "1";
          $value->ola_user_id = $chkOlaUser->user_id;
        }
        $value->emailAddresses=$this->model->sel_fld_res("label,value","contact_email_addresses",array("user_id"=>$user_id,"contact_id"=>$value->contact_id));
        $value->phoneNumbers=$this->model->sel_fld_res("label,stringValue","contact_phonenumbers",array("user_id"=>$user_id,"contact_id"=>$value->contact_id));
        $value->postalAddresses=$this->model->sel_fld_res("subAdministrativeArea,subLocality,street,isoCountryCode,country,city,label,state,postalCode","contact_postal_addresses",array("user_id"=>$user_id,"contact_id"=>$value->contact_id));
        $value->socialProfiles=$this->model->sel_fld_res("label,service,urlString,username","contact_social_profiles",array("user_id"=>$user_id,"contact_id"=>$value->contact_id));
        unset($value->created_date);
        unset($value->updated_date);
        unset($value->user_id);
      }
      return $getContactList;
    }

    public function getContactDtlById($user_id=0,$givenName="",$familyName="")
    {
      if ($user_id != 0) {
        $getContactList = $this->model->sel_fld_row("*","user_contact_list_master",array("user_id"=>$user_id));
      }
      else
      {
        $getContactList = $this->model->sel_fld_row("*","user_contact_list_master",array("givenName"=>$givenName,"familyName"=>$familyName));
      }
      $value = $getContactList;
      $chkOlaUser = $this->model->sel_row("user_master",array("givenName"=>$value->givenName,"familyName"=>$value->familyName));
      $value->is_ola_user = "0";
      $value->ola_user_id= "0";
      if (count($chkOlaUser)>0) {
        $value->is_ola_user = "1";
        $value->ola_user_id = $chkOlaUser->user_id;
      }
      $value->emailAddresses=$this->model->sel_fld_res("label,value","contact_email_addresses",array("user_id"=>$user_id,"contact_id"=>$value->contact_id));
      $value->phoneNumbers=$this->model->sel_fld_res("label,stringValue","contact_phonenumbers",array("user_id"=>$user_id,"contact_id"=>$value->contact_id));
      $value->postalAddresses=$this->model->sel_fld_res("subAdministrativeArea,subLocality,street,isoCountryCode,country,city,label,state,postalCode","contact_postal_addresses",array("user_id"=>$user_id,"contact_id"=>$value->contact_id));
      $value->socialProfiles=$this->model->sel_fld_res("label,service,urlString,username","contact_social_profiles",array("user_id"=>$user_id,"contact_id"=>$value->contact_id));
      unset($value->created_date);
      unset($value->updated_date);
      unset($value->user_id);
      
      return $getContactList;
    }

    public function updateContactDetail()
    {
        try{
            comman_controller::varifyMethod("POST");
            extract($_POST);
            
            $key = empty($_SERVER["HTTP_KEY"]) ? "" : $_SERVER["HTTP_KEY"];
            $token = empty($_SERVER["HTTP_TOKEN"]) ? "" : $_SERVER["HTTP_TOKEN"];
           
            comman_controller::requiredValidation([
                'key' => $key,
                'token' => $token,
                'user_id' => $user_id,
                'contact_data' => $contact_data
             ]);

            if($this->model->checkKeyExist($key)==0){
              return comman_controller::responseMessage(0, "Please enter correct key, eneterd key doesn’t match with provided key.", "False");
            }else if($this->model->checkKeyTokenExist($key,$token)==0){
                return comman_controller::responseMessage(0, "You are not authorized to access associated web-services; it seems there is mismatch in key-token pair.", "False");
            }
              
            $count_user=$this->model->record_count('user_master',array("user_id"=>$user_id));  
            if($count_user>0){        
              $contactData = json_decode(base64_decode($contact_data),true);
              // print_r($contactData);
              // exit();
              if ($contactData == "" || $contactData == null) {
                return comman_controller::responseMessage(0, "Incorrect contact list json.", "False");
              }

              $contactobj = (object)$contactData;
              $subvalue = $contactData;

              $chkContact = $this->model->sel_row("user_contact_list_master",array("contact_id"=>$contactobj->contact_id));
              $birthday = (isset($contactobj->birthday))?$contactobj->birthday:'';
              $updateData = array("givenName"=>$contactobj->givenName,"jobTitle"=>$contactobj->jobTitle,"organizationName"=>$contactobj->organizationName,"familyName"=>$contactobj->familyName,"birthday"=>$birthday,"user_id"=>$user_id);
              $contact_id = 0;
              if (count($chkContact)==0) {
                $updateData['created_date']=cur_date_time;
                $updateData['updated_date']=cur_date_time;
                $contact_id = $this->model->insert("user_contact_list_master",$updateData);
              }
              else
              {
                $updateData['updated_date']=cur_date_time;
                $contact_id = $chkContact->contact_id;
                $this->model->update("user_contact_list_master",$updateData,array("contact_id"=>$contact_id));
              }
              $arrayField = array("postalAddresses","socialProfiles","phoneNumbers","emailAddresses");
              foreach ($arrayField as $fieldname) {
                if ($subvalue[$fieldname]) {
                  $arrData = $subvalue[$fieldname];
                  if (count($arrData)>0) {
                    foreach ($arrData as $arrkey => $arrvalue) {
                      if ($fieldname == "postalAddresses") {
                        $this->model->delete("contact_postal_addresses",array("user_id"=>$user_id,"contact_id"=>$contact_id));
                        $insertDt = array("subAdministrativeArea"=>$arrvalue['subAdministrativeArea'],"subLocality"=>$arrvalue['subLocality'],"street"=>$arrvalue['street'],"isoCountryCode"=>$arrvalue['isoCountryCode'],"country"=>$arrvalue['country'],"city"=>$arrvalue['city'],"label"=>$arrvalue['label'],"state"=>$arrvalue['state'],"postalCode"=>$arrvalue['postalCode'],"user_id"=>$user_id,"contact_id"=>$contact_id,"created_date"=>cur_date_time,"updated_date"=>cur_date_time);
                        // print_r($insertDt);
                        $this->model->insert("contact_postal_addresses",$insertDt);
                      }
                      else if ($fieldname == "socialProfiles") {
                        $this->model->delete("contact_social_profiles",array("user_id"=>$user_id,"label"=>$arrvalue['label']));
                        $insertDt = array("service"=>$arrvalue['service'],"label"=>$arrvalue['label'],"urlString"=>$arrvalue['urlString'],"username"=>$arrvalue['username'],"user_id"=>$user_id,"contact_id"=>$contact_id,"created_date"=>cur_date_time,"updated_date"=>cur_date_time);
                        // print_r($insertDt);
                        $this->model->insert("contact_social_profiles",$insertDt);
                      }
                      else if ($fieldname == "phoneNumbers") {
                        $this->model->delete("contact_phonenumbers",array("user_id"=>$user_id,"stringValue"=>$arrvalue['stringValue']));
                        $insertDt = array("stringValue"=>$arrvalue['stringValue'],"label"=>$arrvalue['label'],"user_id"=>$user_id,"contact_id"=>$contact_id,"created_date"=>cur_date_time,"updated_date"=>cur_date_time);
                        // print_r($insertDt);
                        $this->model->insert("contact_phonenumbers",$insertDt);
                      }
                      else if ($fieldname == "emailAddresses") {
                        $this->model->delete("contact_email_addresses",array("user_id"=>$user_id,"value"=>$arrvalue['value']));
                        $insertDt = array("value"=>$arrvalue['value'],"label"=>$arrvalue['label'],"user_id"=>$user_id,"contact_id"=>$contact_id,"created_date"=>cur_date_time,"updated_date"=>cur_date_time);
                        // print_r($insertDt);
                        $this->model->insert("contact_email_addresses",$insertDt);
                      }
                      
                    }  
                  }
                }  
              }
              
              $data['data']=$contactData;
              return comman_controller::successResponse($data,1,'Contact syncing successfully','True');    
            }
            else{
                return comman_controller::responseMessage(0, "Unable to sendConnectRequest because of Invalid user id.", "False");
            }
               
        } 
        catch (Exception $e) {
            return comman_controller::responseMessage(0, "Something went wrong while get word, please try again.", "False");
        }

    }  
 
}