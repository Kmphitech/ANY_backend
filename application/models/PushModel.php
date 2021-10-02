<?php
class PushModel extends CI_Model {
   
  public function  pushforAdd($device_type,$token,$message,$msgCount=1,$notiCount=1,$badge=1){
    $url = 'https://fcm.googleapis.com/fcm/send';
    $title =server_name;
    $sound="default";

    if($device_type=="android"){
      
      $fields = array(
        'to' => $token,
        'data' => array('title' => $title, 'message' => $message,"msgCount"=>$msgCount,"notiCount"=>$notiCount),               
        'sound' => $sound
     );

      $headers = array(
        'Authorization:key='.push_key ,
        'Content-Type:application/json'
      );
                       
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);       
      $result = curl_exec($ch);
      curl_close($ch);
    }
    
    else if($device_type=="iOS"){
      $notification = array('title' =>$title , 'text' => $message, 'sound' => $sound, 'badge' => $badge,"msgCount"=>$msgCount,"notiCount"=>$notiCount);
      $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
      $json = json_encode($arrayToSend);
      $headers = array();
      $headers[] = 'Content-Type: application/json';
      $headers[] = 'Authorization: key='. push_key;
              
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      //Send the request
      $response = curl_exec($ch);
      curl_close($ch); 
    }
   
  }


  public function  pushforMsg($device_type,$token,$message,$title,$msgCount=1,$notiCount=1,$badge=1){
    $url = 'https://fcm.googleapis.com/fcm/send';
     
    $sound="default";

    if($device_type=="android"){
      $fields = array(
        'to' => $token,
        'data' => array('title' => $title, 'message' => $message,'type'=>"message","msgCount"=>$msgCount,"notiCount"=>$notiCount),               
        'sound' => $sound
     );

      $headers = array(
        'Authorization:key='.push_key ,
        'Content-Type:application/json'
      );
                       
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);       
      $result = curl_exec($ch);
      curl_close($ch);
    }
    
    else if($device_type=="iOS"){
      $notification = array('title' =>$title , 'text' => $message,'type'=>"message", 'sound' => $sound, 'badge' => $badge,"msgCount"=>$msgCount,"notiCount"=>$notiCount);
      $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
      $json = json_encode($arrayToSend);
      $headers = array();
      $headers[] = 'Content-Type: application/json';
      $headers[] = 'Authorization: key='. push_key;
              
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      //Send the request
      $response = curl_exec($ch);
      curl_close($ch); 
    }
   
  }

  public function checkFbMSgCount($userId)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://bless-48760.firebaseio.com/BadgeCounter/$userId.json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    return $result = curl_exec($ch);
    curl_close ($ch);
  }

  public function addNotiFb($userId,$totalNoti)
  { 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://bless-48760.firebaseio.com/BadgeCounter/$userId/.json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $fields = array('notiCount' => $totalNoti);

    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($fields));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    $headers = array();
    $headers[] = "Content-Type: application/x-www-form-urlencoded";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
  }

  public function addMsgFb($userId,$totalMsg)
  { 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://bless-48760.firebaseio.com/BadgeCounter/$userId/.json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $fields = array('msgCount' => $totalMsg);

    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($fields));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    $headers = array();
    $headers[] = "Content-Type: application/x-www-form-urlencoded";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);
  }

}  