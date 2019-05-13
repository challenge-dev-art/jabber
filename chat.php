<?php
session_start();

$tousername = $_SESSION['tousername'];

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0 auto;
            max-width: 800px;
            padding: 0 20px;
        }
        .container {
            border: 2px solid #dedede;
            background-color: #f1f1f1;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
        }
        .darker {
            border-color: #ccc;
            background-color: #ddd;
        }
        .container::after {
            content: "";
            clear: both;
            display: table;
        }
        .container img {
            float: left;
            max-width: 60px;
            width: 100%;
            margin-right: 20px;
            border-radius: 50%;
        }
        .container img.right {
            float: right;
            margin-left: 20px;
            margin-right:0;
        }
        .time-right {
            float: right;
            color: #aaa;
        }
        .time-left {
            float: left;
            color: #999;
        }
        .form-inline {
            display: flex;
            flex-flow: row wrap;
            align-items: center;
            width: 100%;
        }
        .form-inline label {
            margin: 5px 10px 5px 0;
        }
        .form-inline input {
            vertical-align: middle;
            margin: 5px 10px 5px 0;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ddd;
        }
        .form-inline button, button {
            padding: 10px 20px;
            background-color: dodgerblue;
            border: 1px solid #ddd;
            color: white;
            cursor: pointer;
        }
        .form-inline button:hover, button:hover {
            background-color: royalblue;
        }
        @media (max-width: 800px) {
            .form-inline input {
                margin: 10px 0;
            }
            .form-inline {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
    <script src="js/jquery.min.js"></script>
</head>
<body">

<h2><img src="images/logo.png" alt="Avatar" style="width:30px;"> <?php echo $_SESSION['username']; ?></h2>

<div class="container">
    <div id="chat">
    </div>
    <form class="form-inline" method="post">
        <label style="width: 3%">To:</label>
        <input type="text" id="id" placeholder="jabber ID: ex. jabber@localhost" name="id" style="width: 20%" value="<?php echo $_SESSION['tousername']; ?>" required>
        <label style="width: 8%">Message: </label>
        <input type="text" id="message" placeholder="Writting something..." style="width: 35%" name="message" required>
    </form>

    <button id="submit" style="float: right">Submit</button>
    <div></div>
    <h4 id="error" style="color: red;float: right"></h4>
</div>
<script>
    function sender(content='') {
        var currentdate = new Date();
        var datetime = currentdate.getDate() + "/"
            + (currentdate.getMonth()+1)  + "/"
            + currentdate.getFullYear() + " "
            + currentdate.getHours() + ":"
            + currentdate.getMinutes();

        var div = document.createElement('div');
        div.className = 'container dark';
        div.innerHTML = '<img src="images/woman.jpg" alt="Avatar" class="right" style="width:100%;">' +
            '<p>'+content+'</p>' +
            '<span class="time-left">'+datetime+'</span>';
        document.getElementById('chat').appendChild(div);
    }
    function receiver(content_='') {
        var currentdate = new Date();
        var datetime = currentdate.getDate() + "/"
            + (currentdate.getMonth()+1)  + "/"
            + currentdate.getFullYear() + " "
            + currentdate.getHours() + ":"
            + currentdate.getMinutes();

        var div = document.createElement('div');
        div.className = 'container';
        div.innerHTML = '<img src="images/man.jpg" alt="Avatar" class="left" style="width:100%;">' +
            '<p>'+content_+'</p>' +
            '<span class="time-right">'+datetime+'</span>';
        document.getElementById('chat').appendChild(div);
    }

    function send(flag){
        if (flag === 0) {
            $.ajax({
                type: "POST",
                url: "request.php",
                data: {
                },
                success: function (data) {
                    if (data !== '') {
                        receiver(data);
                    }
                    send(0);
                }
            });
        } else {
            $id_ = $('#id').val();
            $message = $('#message').val();
            $('#error').html('');
            if ($id_ === '' || $message === '') {
                $('#error').html('Input Error!');
                return;
            }
            sender($message);
            $.ajax({
                type: "POST",
                url: "send.php",
                data: {
                    id: $id_,
                    message: $message
                },
                success: function (data) {
                    $('#message').val('');
                }
            })
        }
    }
    $('#submit').click(function () {
        send(1);
    });
    $(document).ready(function () {
        setTimeout(function () {
            send(0);
        }, 5);
    });
</script>
</body>
</html>

