<?php
session_start();
// activate full error reporting
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

include 'XMPPHP/XMPP.php';

ignore_user_abort(true);
set_time_limit(0);

//include_once dirname(__FILE__) . '/xmppdefine.php';
//include_once (dirname(__FILE__) . '/XMPPHP/XMPP.php');

#Use XMPPHP_Log::LEVEL_VERBOSE to get more logging for error reports
#If this doesn't work, are you running 64-bit PHP with < 5.2.6?
$conn = new XMPPHP_XMPP('localhost', 5222, 'ingate2', '123456', 'xmpphp', 'localhost', $printlog = false,
    $loglevel = XMPPHP_Log::LEVEL_INFO);
$conn->autoSubscribe();

try {
    $conn->connect(10);
    while (!$conn->isDisconnected()) { //
        $payloads = $conn->processUntil(
            array(
                'message',
                'session_start',
                'end_stream'
            ));
        foreach ($payloads as $event) {
            $pl = $event[1];
            switch ($event[0]) {
                case 'message':
                    $_SESSION['result'] = $pl['body'];
                    $conn->message($pl['from'], $body="Thanks for sending me \"{$pl['body']}\".", $type=$pl['type']);
                    $cmd = explode(' ', $pl['body']);
                    if($cmd[0] == 'quit') $conn->disconnect();
                    if($cmd[0] == 'break') $conn->send("</end>");
                    if($cmd[0] == 'vcard') {
                    if(!($cmd[1])) $cmd[1] = $conn->user . '@' . $conn->server;
                        // take a note which user requested which vcard
                        $vcard_request[$pl['from']] = $cmd[1];
                        // request the vcard
                        $conn->getVCard($cmd[1]);
                    }
                    echo $_SESSION['result'];
                    exit(0);
                    break;
                case 'session_start':
                    $conn->presence();
                    break;
                default:
                    break;
            }
        }
    }
}
catch (XMPPHP_Exception $e) {
    echo "Error\n";
    die("error:".$e->getMessage());
}
$conn->disconnect();