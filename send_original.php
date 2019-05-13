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

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $touser = $_POST['username'].'@'.$_POST['server'];
    $message = $_POST['message'];
    $source = 'xmpphp';
    $conn = new XMPPHP_XMPP($host, $port, $username,
        $password, $source, 'localhost', $printlog = true, $loglevel = XMPPHP_Log::LEVEL_INFO);
    try {
        $conn->connect(10);
        $pay = $conn->processUntil(array(
            'message',
            //'presence',
            'end_stream',
            'session_start',
            //'vcard'
        ));
        foreach ($pay as $event) {
            $pl = $event[1];
            switch ($event[0]) {
                case 'message':
                    break;
                case 'presence':
                    break;
                case 'session_start':
                    $conn->presence();
                    $conn->message($touser, $message);
                    $result = "Success";
                    $_SESSION['tousername'] = $touser;
                    break;
                case 'end_stream':
                    break;
            }

        }
        $conn->disconnect();
    } catch(XMPPHP_Exception $e) {
 //       die($e->getMessage());
        $result = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<head>
    <title>SEND</title>
    <style type = "text/css">
        body {font-family: Arial, Helvetica, sans-serif;}
        * {box-sizing: border-box;}

        input[type=text], select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 6px;
            margin-bottom: 16px;
            resize: vertical;
        }

        input[type=submit], #receive_link {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type=submit]:hover, #receive_link {
            background-color: #45a049;
        }

        .container {
            border-radius: 5px;
            background-color: #f2f2f2;
            padding: 20px;
        }
    </style>
</head>
<body bgcolor = "#FFFFFF">
<div align = "center">
    <div style = "width:400px" align = "left">
        <h3 align="center">SENDING</h3>
        <div class="container">
            <form action = "" method = "post">
                <label for="username">To usernamne</label>
                <input type="text" id="username" name="username" placeholder="Username" required>

                <label for="icon">@</label>

                <label for="server">To domain</label>
                <select id="server" name="server" required>
                    <option value="localhost">localhost</option>
                    <option value="jabber.in">jabber.in</option>
                    <option value="...">...</option>
                </select>

                <label for="Message">Message</label>
                <textarea id="message" name="message" placeholder="Write something.." style="height:200px" required></textarea>

                <input type="submit" value="SEND">

                <a href="chat.php"><input style="float: right" type="button" value="Receive" id="receive_link"></a>
            </form>
            <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $result; ?></div>
        </div>
    </div>
</div>

</body>
</html>
