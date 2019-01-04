<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

function send_notification_android($android_ids = array(), $android_payload = array(), $fcm_key = '') {
    $url = 'https://fcm.googleapis.com/fcm/send';

    $fields = array(
        'registration_ids' => $android_ids,
        'data' => $android_payload,
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
    
    echo '<pre>';
    print_r($result);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }

    // Close connection
    curl_close($ch);
    return $result;
}

//$token = $_GET["token"];
$token = !empty($_REQUEST['token']) ? $_REQUEST['token'] : "c-iMnsn5wag:APA91bGa6bj4Kj7-hS5hm7ECZqf-_xJHr5V8L5vysc5O87rclL8H1AibmMXhvsSA3dqsWkU5PtwzH5icq2ohKj0AQVc4_eicp6V4nZBOQcFMDu_KUFwYQmlqZQTyQuoBDSDyktY_0KlU";
$key = !empty($_REQUEST['key']) ? $_REQUEST['key'] : "AIzaSyCoNWEk5ckrRka71iawB5fBqmhUl283ZrA";
$registatoin_ids = array($token);

$android_payload = array();
$android_payload['android']['icon'] = 'appicon';
$android_payload['android']['vibrate'] = 'true';
$android_payload['android']['badge'] = 0;
$android_payload['android']['sound'] = "default";
$android_payload['android']['message'] = "Hey, If you got push then please inform to Nitin Vaghani.";
$android_payload['android']['data'] = array('type' => 1);
send_notification_android($registatoin_ids, $android_payload, $key);
?>
