<?php

$showError1 = isset($_SESSION['error_message1']); // invalid login
$showError2 = isset($_SESSION['error_message2']); // no new email or password
$showError3 = isset($_SESSION['error_message3']); // malformed new email

?>

<!DOCTYPE html>
<html>
    <head>
        <style>
            body {
                text-align: center;
            }
            .login {
                display: inline-block;
                background-color: #acacac;
                width: 500px;
                
            }
            label{
                display: inline-block;
                float: center;
                clear: left;
                width: 250px;
                text-align: right;
            }
            .userinput {
                background-color: white;
                background-position: 10px 10px;
                background-repeat: no-repeat;
                padding-left: 40px;
            }
            .userinput:focus {
                background-color: white;
            }
            .loginError {
                background-color: red;
                background-position: 10px 10px;
                background-repeat: no-repeat;
                padding-left: 40px;
            }
            .loginWarning {
                background-color: yellow;
                background-position: 10px 10px;
                background-repeat: no-repeat;
                padding-left: 40px;
            }
            .submit {
                background-color: blue;
                border: none;
                color: white;
                padding: 16px 32px;
                text-decoration: none;
                margin: 4px 2px;
                cursor: pointer;
            }
            /* The container */
            .container {
            display: block;
            position: relative;
            padding-left: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            font-size: 16px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            }

            /* Hide the browser's default radio button */
            .container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            }

            /* Create a custom radio button */
            .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
            border-radius: 50%;
            }

            /* On mouse-over, add a grey background color */
            .container:hover input ~ .checkmark {
            background-color: #ccc;
            }

            /* When the radio button is checked, add a blue background */
            .container input:checked ~ .checkmark {
            background-color: #2196F3;
            }

            /* Create the indicator (the dot/circle - hidden when not checked) */
            .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            }

            /* Show the indicator (dot/circle) when checked */
            .container input:checked ~ .checkmark:after {
            display: block;
            }

            /* Style the indicator (dot/circle) */
            .container .checkmark:after {
                top: 9px;
                left: 9px;
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: white;
            }
        </style>
        <script>
            function clickEvent() {
            if(document.getElementById('loginRadio').checked) {
                document.getElementById("login").style.display = "inline-block";
                document.getElementById("manage").style.display = "none";
                document.getElementById("delete").style.display = "none";
            }else if(document.getElementById('manageRadio').checked) {
                document.getElementById("login").style.display = "none";
                document.getElementById("manage").style.display = "inline-block";
                document.getElementById("delete").style.display = "none";
            }else if(document.getElementById('deleteRadio').checked) {
                document.getElementById("login").style.display = "none";
                document.getElementById("manage").style.display = "none";
                document.getElementById("delete").style.display = "inline-block";
            }
            }
        </script>
    </head>
    <body onload="clickEvent()">
        <label class="container">Login
            <input type="radio" id="loginRadio" checked="checked" onclick=clickEvent() name="radio">
            <span class="checkmark"></span>
        </label>
        <label class="container">Change Account Info
            <input type="radio" id="manageRadio" onclick=clickEvent() name="radio">
            <span class="checkmark"></span>
        </label>
        <label class="container">Delete Account
            <input type="radio" id="deleteRadio" onclick=clickEvent() name="radio">
            <span class="checkmark"></span>
        </label>
        <form action="http://localhost/account/" method="GET" class='login' id='login'>
            <div class="form-group">
                <label for="usernameOrEmail">Username:</label>
                <input class='userinput' type="text" name="uname" class="form-control" id="uname">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input class='userinput' type="password" name="pw" class="form-control" id="pw">
                <div class='loginError' style='display:<?php if (!$showError1) echo "none"; else echo "block"; ?>;'> <?php echo $_SESSION['error_message1']; ?></div>
            </div>
            <input class="submit" type="submit"></input>
        </form>
        <form action="http://localhost/account/" method="PUT" class='login' id='manage'>
            <div class="form-group">
                <label for="usernameOrEmail">Username:</label>
                <input class='userinput' type="text" name="uname" class="form-control" id="uname">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input class='userinput' type="password" name="pw" class="form-control" id="pw">
            </div>
            <div class="form-group">
                <label for="usernameOrEmail">New Email (Optional):</label>
                <input class='userinput' type="text" name="newEmail" class="form-control" id="newEmail">
                <div class='loginError' style='display:<?php if (!$showError3) echo "none"; else echo "block"; ?>;'> <?php echo $_SESSION['error_message3']; ?></div>
            </div>
            <div class="form-group">
                <label for="password">New Password (Optional):</label>
                <input class='userinput' type="password" name="newPw" class="form-control" id="newPw">
                <div class='loginError' style='display:<?php if (!$showError1) echo "none"; else echo "block"; ?>;'> <?php echo $_SESSION['error_message1']; ?></div>
                <div class='loginError' style='display:<?php if (!$showError2) echo "none"; else echo "block"; ?>;'> <?php echo $_SESSION['error_message2']; ?></div>
            </div>
            <input class="submit" type="submit"></input>
        </form>
        <form action="http://localhost/account/" method="DELETE" class='login' id='delete'>
            <div class="form-group">
                <label for="usernameOrEmail">Username:</label>
                <input class='userinput' type="text" name="uname" class="form-control" id="uname">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input class='userinput' type="password" name="pw" class="form-control" id="pw">
                <div class='loginError' style='display:<?php if (!$showError1) echo "none"; else echo "block"; ?>;'> <?php echo $_SESSION['error_message1']; ?></div>
            </div>
            <input class="submit" type="submit"></input>
        </form>
    </body>
</html>