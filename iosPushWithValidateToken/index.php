<?php

/*
 * Read Error Response when sending Apple Enhanced Push Notification
 *
 * This assumes your iOS devices have the proper code to add their device tokens
 * to the db and also the proper code to receive push notifications when sent.
 *
 */
$filename = 'pem/apns-dev.pem';
$app_is_live = 'false';
$result = array(
    array('id' => 1, 'token' => '32131'),
    array('id' => 2, 'token' => '7D11E6416B954D939BAEAD1674EC4CBCB1A0A55F307B200EEFDE08EF1A8157DD'),
    array('id' => 3, 'token' => 'DSFSDFSDF'),
    array('id' => 4, 'token' => '070EC8135C549F0BED0F5700E3A277D765AE497B5ACAF79068CD3E9D1172EEC8'),
);

//Setup notification message
$body = array();
$body['aps']['alert'] = 'My push notification message!';
$body['aps']['sound'] = 'default';
$body['aps']['badge'] = (string) 1;
$body['aps']['icon'] = 'appicon';
$body['aps']['vibrate'] = 'true';
$body['aps']['notification_id'] = (string) 1;

//Setup stream (connect to Apple Push Server)
$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', $filename);
if ($app_is_live == 'true') {
    $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
} else {
    $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
}

stream_set_blocking($fp, 0);
if (!$fp) {
    return TRUE;
} else {
    $apple_expiry = time() + (90 * 24 * 60 * 60); //Keep push alive (waiting for delivery) for 90 days
    foreach ($result as $value) {
        $apple_identifier = $value["id"];
        $device_token = $value["token"];
        $payload = json_encode($body);
        //Enhanced Notification
        $msg = pack("C", 1) . pack("N", $apple_identifier) . pack("N", $apple_expiry) . pack("n", 32) . pack('H*', str_replace(' ', '', $device_token)) . pack("n", strlen($payload)) . $payload;
        //SEND PUSH
        fwrite($fp, $msg);
        checkAppleErrorResponse($fp, $device_token);
    }
    usleep(500000); //Pause for half a second. and the error message was still available to be retrieved
    checkAppleErrorResponse($fp, $device_token);
    echo 'DONE!';
    fclose($fp);
}

//FUNCTION to check if there is an error response from Apple
//Returns TRUE if there was an error and FALSE if there was not
function checkAppleErrorResponse($fp) {

    //byte1=always 8, byte2=StatusCode, bytes3,4,5,6=identifier(rowID). Should return nothing if OK.
    $apple_error_response = fread($fp, 6);

    //NOTE: Make sure you set stream_set_blocking($fp, 0) or 
    //else fread will pause your script and wait forever when there is no response to be sent.

    if ($apple_error_response) {
        //unpack the error response (first byte 'command" should always be 8)
        $error_response = unpack('Ccommand/Cstatus_code/Nidentifier', $apple_error_response);
        if ($error_response['status_code'] == '0') {
            $error_response['status_code'] = '0-No errors encountered';
        } else if ($error_response['status_code'] == '1') {
            $error_response['status_code'] = '1-Processing error';
        } else if ($error_response['status_code'] == '2') {
            $error_response['status_code'] = '2-Missing device token';
        } else if ($error_response['status_code'] == '3') {
            $error_response['status_code'] = '3-Missing topic';
        } else if ($error_response['status_code'] == '4') {
            $error_response['status_code'] = '4-Missing payload';
        } else if ($error_response['status_code'] == '5') {
            $error_response['status_code'] = '5-Invalid token size';
        } else if ($error_response['status_code'] == '6') {
            $error_response['status_code'] = '6-Invalid topic size';
        } else if ($error_response['status_code'] == '7') {
            $error_response['status_code'] = '7-Invalid payload size';
        } else if ($error_response['status_code'] == '8') {
            $error_response['status_code'] = '8-Invalid token';
        } else if ($error_response['status_code'] == '255') {
            $error_response['status_code'] = '255-None (unknown)';
        } else {
            $error_response['status_code'] = $error_response['status_code'] . '-Not listed';
        }

        echo '<br><b>+ + + + + + ERROR</b> Response Command:<b>' . $error_response['command'] . '</b>&nbsp;&nbsp;&nbsp;Identifier:<b>' . $error_response['identifier'] . '</b>&nbsp;&nbsp;&nbsp;Status:<b>' . $error_response['status_code'] . '</b><br>';
        echo 'Identifier is the rowID (index) in the database that caused the problem, and Apple will disconnect you from server. To continue sending Push Notifications, just start at the next rowID after this Identifier.<br>';
        return true;
    }
    return false;
}

?>
