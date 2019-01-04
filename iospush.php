<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


//["08b5b8833576d134aaf5e80b397cb21f6622a9b5b3d39edbadaa69166bfce8b2","7573e76752a0ce0ba692ed1f41b9c19ac4c177137b694db4ad488501c7560088"]

$message = (!empty($_POST['msg']) ? $_POST['msg'] : (!empty($_GET['msg']) ? $_GET['msg'] : "This is a test message from Nitin vaghani.. Please inform him" ) );
$udid = (!empty($_POST['udid']) ? $_POST['udid'] : (!empty($_GET['udid']) ? $_GET['udid'] : "35da6457ff7dd33571d1155c2d67d457279155aae61c2841f5a416f2b3d4de47"));

if ($message != "" && $udid != "") {
    try {
        $badge = 1;
        $sound = "default";
        // Construct the notification payload
        $body = array();
        $body['aps']['alert'] = $message;
        $body['aps']['badge'] = $badge;
        $body['aps']['sound'] = $sound;
        //$body['aps']['notificationType'] = 1;
        // End of Configurable Items 
        $payload = json_encode($body);
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'apns-prod.pem');
        stream_context_set_option($ctx, 'ssl', 'verify_peer', false);
//        stream_context_set_option($ctx, 'ssl', 'passphrase', 'codular-test');
        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
//        $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
//        echo '<pre>';
//        print_r($fp);exit;
        if (!$fp) {
            echo "connection issue";
        } else {
            $msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $udid)) . pack("n", strlen($payload)) . $payload;
//               echo '<pre>';
//        print_r($msg);exit;
            if (fwrite($fp, $msg)) {
                echo "push sent successfully";
            } else {
                echo "push not sent successfully";
            }
            echo '<pre>';
            print_r($fp);
            exit;
            fclose($fp);
        }
    } catch (Exception $e) {
        return false;
    }
}
?>
