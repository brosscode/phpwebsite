<?php

$showError1 = isset($_SESSION['error_message1']); // uname error
$showError2 = isset($_SESSION['error_message2']); // pw error
$showError3 = isset($_SESSION['error_message3']); // pw error
$showError4 = isset($_SESSION['error_message4']); // email error
$showError5 = isset($_SESSION['error_message5']); // fix above errors and try again
$showError6 = isset($_SESSION['error_message6']); // uname taken
$showError7 = isset($_SESSION['error_message7']); // email taken

?>

<!DOCTYPE html>
<html>
    <head>
        <style>
            body {
                text-align: center;
            }
            form {
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
        </style>
    </head>
    <body>
        <form action="http://localhost/register/" method="post" id='login'>
        <div class='loginWarning' style='display:<?php if (!$showError5) echo "none"; else echo "block"; ?>;'> <?php echo $_SESSION['error_message5']; ?></div>
            <div class="form-group">
                <label for="usernameOrEmail">Username:</label>
                <input class='userinput' type="text" name="uname" class="form-control" id="uname">
                <div class='loginError' style='display:<?php if (!$showError1) echo "none"; else echo "block"; ?>;'> <?php echo $_SESSION['error_message1']; ?></div>
                <div class='loginError' style='display:<?php if (!$showError6) echo "none"; else echo "block"; ?>;'> <?php echo $_SESSION['error_message6']; ?></div>
            </div>
            <div class="form-group">
                <label for="usernameOrEmail">Email (Optional):</label>
                <input class='userinput' type="text" name="email" class="form-control" id="email">
                <div class='loginError' style='display:<?php if (!$showError4) echo "none"; else echo "block"; ?>;'> <?php echo $_SESSION['error_message4']; ?></div>
                <div class='loginError' style='display:<?php if (!$showError7) echo "none"; else echo "block"; ?>;'> <?php echo $_SESSION['error_message7']; ?></div>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input class='userinput' type="password" name="pw" class="form-control" id="pw">
                <div class='loginError' style='display:<?php if (!$showError2) echo "none"; else echo "block"; ?>;'> <?php echo $_SESSION['error_message2']; ?></div>
                <div class='loginError' style='display:<?php if (!$showError3) echo "none"; else echo "block"; ?>;'> <?php echo $_SESSION['error_message3']; ?></div>
            </div>
            <input class="submit" type="submit"></input>
        </form>
    </body>
</html>