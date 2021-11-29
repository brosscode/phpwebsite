<?php
session_start();

// create connection
$servername = "localhost";
$password = file_get_contents("password.txt","r");
$username = "Admin2";
$dbname = "meatgrinder";

// create/check connection to database.
$conn = mysqli_connect($servername,$username,$password,$dbname);
if ($conn === false) {
    die("Connection Failed.");
}

if ($conn ->connect_error){
    http_response_code(502);
    die("Connection Failed.");
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && count($_GET) > 0) {

    $userlogin = $_GET['uname'];
    $userpassword = $_GET['pw'];
    $param_userlogin = $userlogin;
    $param_password = $userpassword;

    if (empty($userlogin) || strlen($userlogin) < 6 || strlen($userlogin) > 45) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        // header('Location: '.$_SERVER['PHP_SELF']);
        die("Invalid Credentials");
    }
    if (empty($userpassword) || strlen($userpassword) < 8 || strlen($userpassword) > 255) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        // header('Location: '.$_SERVER['PHP_SELF']);
        die("Invalid Credentials");
    }

    $sql = "SELECT pwd FROM accounts WHERE username=?";
    
    if($stmt = mysqli_prepare($conn,$sql)){

        mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $pwd);

        if(mysqli_stmt_fetch($stmt)){

            if(password_verify($param_password, $pwd)){
                http_response_code(200);
                mysqli_stmt_close($stmt);

                $sql = "SELECT usersID,username,email,accountCreated FROM accounts WHERE username=?";

                if($stmt = mysqli_prepare($conn,$sql)){

                    mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
                    mysqli_stmt_execute($stmt);
                    // look at the comment here FUCKER you need to not fuck this up AGAIN :FingerPointngDown:
                    $result = mysqli_stmt_get_result($stmt);
                    $row =  mysqli_stmt_num_rows($stmt);

                    while ($row = mysqli_fetch_assoc($result)) { 
                        $myObj = $row;
                    }
                    $myJSON = json_encode($myObj, JSON_PRETTY_PRINT);

                    echo $myJSON;
                }
            }
            else {
                http_response_code(503);
                $_SESSION['error_message1'] = "Invalid Credentials";
                // header('Location: '.$_SERVER['PHP_SELF']);
                die("Invalid Credentials");
            }
        }
        else {
            http_response_code(503);
            $_SESSION['error_message1'] = "Invalid Credentials";
            // header('Location: '.$_SERVER['PHP_SELF']);
            die("Invalid Credentials");
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "PUT") {

    $_POST = json_decode(file_get_contents('php://input'), true);

    $userlogin = $_POST['uname'];
    $userpassword = $_POST['pw'];

    // pretty sure this is not needed with the check below
    if(!empty($_POST['newEmail'])) {
        $newEmail = $_POST['newEmail'];
        $param_newEmail = $newEmail;
    }

    if(!empty($_POST['newPw'])) {
        $newPW = $_POST['newPw'];
        $param_newpw = $newPW;
    }

    $param_userlogin = $userlogin;
    $param_password = $userpassword;

    if (empty($userlogin) || strlen($userlogin) < 6 || strlen($userlogin) > 45) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        // header('Location: '.$_SERVER['PHP_SELF']);
        die("Invalid Credentials.");
    }
    if (empty($userpassword) || strlen($userpassword) < 8 || strlen($userpassword) > 255) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        // header('Location: '.$_SERVER['PHP_SELF']);
        die("Invalid Credentials.");
    }
    if (!empty($newPW)) {
        if (strlen($newPW) < 8 || strlen($newPW) > 255) {
            http_response_code(503);
            $_SESSION['error_message1'] = "Invalid New Password";
            // header('Location: '.$_SERVER['PHP_SELF']);
            die("Invalid Credentials.");
        }
    }
    if (empty($newEmail) && empty($newPW)){
        http_response_code(500);
        $_SESSION['error_message2'] = "Enter Either a New Password or New Email.";
        // header('Location: '.$_SERVER['PHP_SELF']);
        die("No New Information Submitted.");
    }
    if (!empty($newEmail) && strlen($newEmail) > 45) {
        http_response_code(500);
        $_SESSION['error_message2'] = "New Email Invalid Length. 45 or Fewer Characters.";
        // header('Location: '.$_SERVER['PHP_SELF']);
        die("New Email Invalid Length. 45 or Fewer Characters.");
    }
    if (!empty($newEmail) && !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        $_SESSION['error_message3'] = "New Email Invalid";
        // header('Location: '.$_SERVER['PHP_SELF']);
        die("New Email is Malformed.");
    }


    $sql = "SELECT pwd FROM accounts WHERE username=?";
    
    if($stmt = mysqli_prepare($conn,$sql)){

        mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $pwd);

        if(mysqli_stmt_fetch($stmt)){

            if(password_verify($param_password, $pwd)){
                http_response_code(200);
                mysqli_stmt_close($stmt);

                if (!empty($param_newpw) && !empty($param_newEmail)) {


                    $hashed_password = password_hash($newPW, PASSWORD_DEFAULT);

                    $sql = "UPDATE accounts SET email = ?, pwd = ? WHERE username=?";

                    if($stmt = mysqli_prepare($conn,$sql)){

                        mysqli_stmt_bind_param($stmt,"sss", $param_newEmail, $hashed_password, $userlogin);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);

                        $sql = "SELECT usersID,username,email,accountCreated FROM accounts WHERE username=?";

                        if($stmt = mysqli_prepare($conn,$sql)){

                            mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $row =  mysqli_stmt_num_rows($stmt);
    
                            while ($row = mysqli_fetch_assoc($result)) { 
                                $myObj = $row;
                            }
                            $myJSON = json_encode($myObj, JSON_PRETTY_PRINT);
    
                            echo $myJSON;
                        }

                    }
                }
                else if (!empty($param_newpw)) {

                    $hashed_password = password_hash($newPW, PASSWORD_DEFAULT);

                    $sql = "UPDATE accounts SET pwd = ? WHERE username=?";

                    if($stmt = mysqli_prepare($conn,$sql)){

                        mysqli_stmt_bind_param($stmt,"ss", $hashed_password, $userlogin);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);

                        $sql = "SELECT usersID,username,email,accountCreated FROM accounts WHERE username=?";

                        if($stmt = mysqli_prepare($conn,$sql)){

                            mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $row =  mysqli_stmt_num_rows($stmt);
    
                            while ($row = mysqli_fetch_assoc($result)) { 
                                $myObj = $row;
                            }
                            $myJSON = json_encode($myObj, JSON_PRETTY_PRINT);
    
                            echo $myJSON;
                        }
                    }
                }
                else {

                    $sql = "UPDATE accounts SET email = ? WHERE username=?";

                    if($stmt = mysqli_prepare($conn,$sql)){

                        mysqli_stmt_bind_param($stmt,"ss", $param_newEmail, $userlogin);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);

                        $sql = "SELECT usersID,username,email,accountCreated FROM accounts WHERE username=?";

                        if($stmt = mysqli_prepare($conn,$sql)){

                            mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $row =  mysqli_stmt_num_rows($stmt);
    
                            while ($row = mysqli_fetch_assoc($result)) { 
                                $myObj = $row;
                            }
                            $myJSON = json_encode($myObj, JSON_PRETTY_PRINT);
    
                            echo $myJSON;
                        }
                    }
                }
            }
            else {
                http_response_code(503);
                $_SESSION['error_message1'] = "Invalid Credentials";
                // header('Location: '.$_SERVER['PHP_SELF']);
                die("Invalid Credentials.");
            }
        }
        else {
            http_response_code(503);
            $_SESSION['error_message1'] = "Invalid Credentials";
            // header('Location: '.$_SERVER['PHP_SELF']);
            die("Invalid Credentials.");
        }
    }

}

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

    $_POST = json_decode(file_get_contents('php://input'), true);

    $userlogin = $_POST['uname'];
    $userpassword = $_POST['pw'];
    $param_userlogin = $userlogin;
    $param_password = $userpassword;

    if (empty($userlogin)) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        // header('Location: '.$_SERVER['PHP_SELF']);
        die("Invalid Credentials");
    }

    if (empty($userpassword)) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        // header('Location: '.$_SERVER['PHP_SELF']);
        die("Invalid Credentials");
    }

    $sql = "SELECT pwd FROM accounts WHERE username=?";

    if($stmt = mysqli_prepare($conn,$sql)){

        mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $pwd);

        if(mysqli_stmt_fetch($stmt)){

            if(password_verify($param_password, $pwd)){

                mysqli_stmt_close($stmt);
                http_response_code(200);


                // register more accounts for further testing
                $sql = "SELECT usersID,username,email,accountCreated FROM accounts WHERE username=?";

                if($stmt = mysqli_prepare($conn,$sql)){

                    mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $row =  mysqli_stmt_num_rows($stmt);

                    while ($row = mysqli_fetch_assoc($result)) { 
                        $myObj = $row;
                    }
                    $myJSON = json_encode($myObj, JSON_PRETTY_PRINT);

                    echo $myJSON;
                }

                $sql = "DELETE FROM accounts WHERE username=?";

                if($stmt = mysqli_prepare($conn,$sql)){

                    mysqli_stmt_bind_param($stmt,"s", $userlogin);
                    mysqli_stmt_execute($stmt);

                }
            }
            else {
                http_response_code(503);
                $_SESSION['error_message1'] = "Invalid Credentials";
                // header('Location: '.$_SERVER['PHP_SELF']);
                die("Invalid Credentials");
            }
        }
        else {
            http_response_code(503);
            $_SESSION['error_message1'] = "Invalid Credentials";
            // header('Location: '.$_SERVER['PHP_SELF']);
            die("Invalid Credentials");
        }
        mysqli_stmt_close($stmt);
    }
}

$showError1 = isset($_SESSION['error_message1']); // invalid login
$showError2 = isset($_SESSION['error_message2']); // no new email or password
$showError3 = isset($_SESSION['error_message3']); // malformed new email

// fix PUT and DELETE HTML
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