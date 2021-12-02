<?php

$showError1 = isset($_SESSION['error_message1']); // invalid login

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
        <form action="login.php" method="post" id='login'>
            <div class="form-group">
                <label for="usernameOrEmail">Username or Email:</label>
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