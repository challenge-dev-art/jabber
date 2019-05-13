<?php
session_start();

$message = $_GET['message'];
$client_name = $_GET['client_name'];

if (empty($_SESSION['chat_id'])) {
    $_SESSION['chat_id'] = md5(time(). mt_rand(0, 999999));
}

if (empty($_SESSION['supporter'])) {
    // how do you select the supporter?
    // only choose a free?
    // We send first message to all supporter and the first who grapped got the chat (where only 3 gues)
}

$irc_host = "127.0.0.1";
$irc_port = 6667; // Port of PsyBnc
$irc_password = "password_from_psy_bnc";
$irc_user = "username_from_psy_bnc";

include_once('Net/SmartIRC.php');

class message_reader
{
    private $messages = array();

    public function receive_messages(&$irc, &$data)
    {
        // result is send to #smartirc-test (we don't want to spam #test)
        $this->messages[] = array(
            'from' => $data->nick,
            'message' => $data->message,
        );
    }

    public function get_messages() {
        return $this->messages;
    }
}

$bot = &new message_reader();
$irc = &new Net_SmartIRC();
$irc->setDebug(SMARTIRC_DEBUG_ALL);
$irc->setUseSockets(TRUE);
$irc->registerActionhandler(SMARTIRC_TYPE_QUERY|SMARTIRC_TYPE_NOTICE, '^' . $_SESSION['chat_id'], $bot, 'receive_messages');
$irc->connect($irc_host, $irc_port);
$irc->login($_SESSION['chat_id'], $client_name, 0, $irc_user, $irc_password);
$irc->join(array('#bitlbee'));
$irc->listen();
$irc->disconnect();

// Send new Message to supporter
if (!empty($message)) {
    $irc->message(SMARTIRC_TYPE_QUERY, $_SESSION['supporter'], $message);
}

echo json_encode(array('messages' => $bot->get_messages()));