<?php

session_start();

include 'XMPPHP/XMPP.php';

ignore_user_abort(true);
set_time_limit(0);

$error = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = 'localhost';
    $port = 5222;
    $username = $_POST['username'];
    $password = $_POST['password'];
    $source = 'xmpphp';
    $conn = new XMPPHP_XMPP($host, $port, $username, $password, $source);
    $conn->autoSubscribe();

    try {
        $conn->connect(30);
        $payloads = $conn->processUntil(
            array(
                'message',
                'end_stream',
                'session_start',
            ));
        foreach ($payloads as $event) {
            $pl = $event[1];
            switch ($event[0]) {
                case 'message':
                    break;
                case 'presence':
                    break;
                case 'session_start':
                    $conn->getRoster();
                    $conn->presence($status = "presence!");
                    $_SESSION["username"] = $username;
                    $_SESSION["password"] = $password;
                    header("Location: chat.php");
                    break;
                case 'end_stream':
                    break;
            }

        }
    }
    catch (XMPPHP_Exception $e) {
        $error = $e->getMessage();
    }

    $conn->disconnect();
}
?>
<!DOCTYPE html>
<head>
    <title>LOGIN</title>
    <style>
        body {font-family: Arial, Helvetica, sans-serif;}
        form {border: 3px solid #f1f1f1;}
        input[type=text], input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            opacity: 0.8;
        }
        .container {
            padding: 16px;
        }
    </style>
</head>

<body bgcolor = "#FFFFFF">

<div align="center">
    <div style="width: 400px" align="left">
        <h2 align="center">Login</h2>
        <form action = "" method = "post">
            <div class="container">
                <label for="uname"><b>Username</b></label>
                <input type="text" placeholder="Enter Username" name="username" required>

                <label for="psw"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="password" required>

                <button type="submit">Login</button>
            </div>
        </form>

        <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
    </div>
</div>

</body>
</html>