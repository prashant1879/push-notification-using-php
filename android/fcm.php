<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

//function send_notification_android($registatoin_ids, $message, $gcm_key) {
//    /* 	if(IS_ANDROID_PUSH_ON == 'false')
//      {
//      return true;
//      } */
//    // Set POST variables
//    $url = 'https://fcm.googleapis.com/fcm/send';
//
//    $fields = array(
//        'registration_ids' => $registatoin_ids,
//        'data' => $message,
//    );
//
//    $headers = array(
//        'Authorization: key=' . $gcm_key,
//        'Content-Type: application/json'
//    );
//    // Open connection
//    $ch = curl_init();
//
//    // Set the url, number of POST vars, POST data
//    curl_setopt($ch, CURLOPT_URL, $url);
//
//    curl_setopt($ch, CURLOPT_POST, true);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//    // Disabling SSL Certificate support temporarly
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//
//    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
//
//    // Execute post
//    $result = curl_exec($ch);
//    if ($result === FALSE) {
//        die('Curl failed: ' . curl_error($ch));
//    }
//
//    // Close connection
//    curl_close($ch);
//    echo $result;
//}
//
//function push_notification($registatoin_ids, $messageText, $key) {
//
//    $message['android']['title'] = $messageText;
//    $message['android']['message'] = $messageText;
//    $message['android']['icon'] = 'appicon';
//    $message['android']['sound'] = 'default';
//    $message['android']['vibrate'] = 'true';
//    $message['android']['badge'] = '1';
//    // Set POST variables
//    $url = 'https://fcm.googleapis.com/fcm/send';
//
//    $fields = array(
//        'registration_ids' => $registatoin_ids,
//        'data' => $message,
//    );
//    //echo "<pre>";print_r(json_encode($fields));exit;
//
//
//    $headers = array(
//        "Authorization: key=" . $key . "",
//        'Content-Type: application/json'
//    );
////    $headers = array(
////        'Authorization: key=AIzaSyDH5efekwhH3xxASQioinCH01BypOeSNUs',
////        'Content-Type: application/json'
////    );
//
//    // Open connection
//    $ch = curl_init();
//
//    // Set the url, number of POST vars, POST data
//    curl_setopt($ch, CURLOPT_URL, $url);
//
//    curl_setopt($ch, CURLOPT_POST, true);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//    // Disabling SSL Certificate support temporarly
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//
//
//    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
//
//
//
//    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
//
//    // Execute post
//    $result = curl_exec($ch);
//
//    //echo '<pre>';print_r($result);exit;
//    if ($result === FALSE) {
//        die('Curl failed: ' . curl_error($ch));
//    }
//
//    // Close connection
//    curl_close($ch);
//    echo $result;
//}
function push_notification($android_ids = array(), $android_payload = array(), $fcm_key = '') {
    $url = 'https://fcm.googleapis.com/fcm/send';


    $message['android']['title'] = "test";
    $message['android']['message'] = "test";
    $message['android']['icon'] = 'appicon';
    $message['android']['sound'] = 'default';
    $message['android']['vibrate'] = 'true';
    $message['android']['badge'] = '1';
    
    $fields = array(
        'registration_ids' => $android_ids,
        'data' => $message,
    );

    $headers = array(
        'Authorization: key=' . $fcm_key,
        'Content-Type: application/json'
    );
    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    // Execute post
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }

    // Close connection
    curl_close($ch);
    echo $result;
}

//$token = $_GET["token"];
$token = !empty($_REQUEST['token']) ? $_REQUEST['token'] : "eXWFhTnCzNQ:APA91bHbdwnzwgEkCK80bKMLcTcs4hIzhnjqWcs6VQ7Ylzm3JRZEMrw4Er-logNWcdYFUKcS5P54aCTArNbkZzJyCaTY6uOD-wJPMmiQ61SChhKrScC7rc_fKtd5eB0FJ2tYygYrbXiF";
//$token = !empty($_REQUEST['token']) ? $_REQUEST['token'] : "08b5b8833576d134aaf5e80b397cb21f6622a9b5b3d39edbadaa69166bfce8b2";
$key = !empty($_REQUEST['key']) ? $_REQUEST['key'] : "AIzaSyCdSmraT39EjjMPtoI6uoKZrwHTzWdIT5Y";
$registatoin_ids = array("dFuKAVKcyqY:APA91bFw0aoBOyh8Cajf0nnxIIEwZpy30ISFhWBrfWmSbnpefJgo9-yd7VtSHwE-AnInbIrV3caJIO602veOEeu4w3g_47k13S9PhcACVbICM4ZBRwg-tNRdxzq1OJ629LlHXVoSFlm0",
    "eu8zF8187TM:APA91bEZmB120mkP6Dgmy2HFJgkhebR5yoveUwpI2HcClZOH7XQHJhc-wFPv6Ca6hUoNuGL7hSV1wZlGSf2hWsuiyW3IpJkVNpiK_EDSaQa78G5Ml-z59ZLXvIAbu8vleWwhjoxbddPQ",
    "eVJVqcT2pfs:APA91bGSpv-EHcq0aQciw97g0pJM6rVX3G1fxd7t5-ISL4JEHwF5XvHMmvjKO-GphdxFVRs9rJxGORa96tdAkv4AZNvheZRJfhnnxBs-hpP6IuomrPktGb4-368iT0RJynYVLR_dO1zG",
    "eXWFhTnCzNQ:APA91bHbdwnzwgEkCK80bKMLcTcs4hIzhnjqWcs6VQ7Ylzm3JRZEMrw4Er-logNWcdYFUKcS5P54aCTArNbkZzJyCaTY6uOD-wJPMmiQ61SChhKrScC7rc_fKtd5eB0FJ2tYygYrbXiF");
$android_payload = array();
$android_payload['android']['icon'] = 'appicon';
$android_payload['android']['vibrate'] = 'true';
$android_payload['android']['badge'] = 0;
$android_payload['android']['sound'] = "default";
$android_payload['android']['message'] = "Hello";
push_notification($registatoin_ids, $android_payload, $key);
?>
