<?php

session_start();

require('XMPPHP/XMPP.php');

$result = '';
$host = 'localhost';
$port = 5222;

$username = $_SESSION['username'];
$password = $_SESSION['password'];
if ($username == null || $password == null){
    header("Location: index.php");
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['message'])) {
    $touser = $_POST['id'];
    $message = $_POST['message'];
    $source = 'xmpphp';
    $conn = new XMPPHP_XMPP($host, $port, $username,
        $password, $source, 'localhost', $printlog = true, $loglevel = XMPPHP_Log::LEVEL_INFO);

    try {
        $conn->connect();
        $pay = $conn->processUntil(array(
            'message',
            'end_stream',
            'session_start',
        ));
        foreach ($pay as $event) {
            $pl = $event[1];
            switch ($event[0]) {
                case 'session_start':
                    $conn->message($touser, $message);
                    $result = "success";
                    $_SESSION['tousername'] = $touser;
                    $conn->disconnect();
                    exit(0);
                    break;
                default:
                    break;
            }
        }
        echo $result;
    } catch(XMPPHP_Exception $e) {
        echo $e->getMessage();
    }
    $conn->disconnect();
}
?>
