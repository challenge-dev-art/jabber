<?php
session_start();
// activate full error reporting
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

include 'XMPPHP/XMPP.php';

ignore_user_abort(true);
set_time_limit(0);

$host = 'localhost';
$port = 5222;

$username = $_SESSION['username'];
$password = $_SESSION['password'];
if ($username == null || $password == null){
    header("Location: index.php");
}
//include_once dirname(__FILE__) . '/xmppdefine.php';
//include_once (dirname(__FILE__) . '/XMPPHP/XMPP.php');

#Use XMPPHP_Log::LEVEL_VERBOSE to get more logging for error reports
#If this doesn't work, are you running 64-bit PHP with < 5.2.6?
$conn = new XMPPHP_XMPP($host, $port, $username, $password, 'xmpphp', 'localhost', $printlog = false,
    $loglevel = XMPPHP_Log::LEVEL_INFO);
// $conn->autoSubscribe();
$conn->connect(10);

try {
    //while (!$conn->isDisconnected()) {
    if (!$conn->isDisconnected()) { //
        $payloads = $conn->processUntil2(
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
                    echo $_SESSION['result'];
                    $conn->disconnect();
                    exit(0);
                    break;
                case 'session_start':
                    break;
                default:
                    $conn->disconnect();
                    exit(0);
                    break;
            }
        }
        //       sleep(1);
    }
}
catch (XMPPHP_Exception $e) {
    echo "Error\n";
    die("error:".$e->getMessage());
}
$conn->disconnect();