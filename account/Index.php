<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET" && count($_GET) > 0) {

    $userlogin = $_GET['uname'];
    $userpassword = $_GET['pw'];
    $param_userlogin = $userlogin;
    $param_password = $userpassword;


    if (!isset($username)) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        die("Invalid Credentials");
    }

    if (!isset($userpassword)) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        die("Invalid Credentials");
    }

    $sql = "SELECT pwd FROM accounts WHERE username=?";

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
    
    if($stmt = mysqli_prepare($conn,$sql)){

        mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $pwd);

        if(mysqli_stmt_fetch($stmt)){

            if(password_verify($param_password, $pwd)){

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

    if (!isset($username)) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        die("Invalid Credentials");
    }

    if (!isset($userpassword)) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        die("Invalid Credentials");
    }

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

    // validation of new information

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
        $_SESSION['error_message3'] = "New Email Malformed";
        // header('Location: '.$_SERVER['PHP_SELF']);
        die("New Email is Malformed.");
    }

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

    $sql = "SELECT pwd FROM accounts WHERE username=?";
    
    if($stmt = mysqli_prepare($conn,$sql)){

        mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $pwd);

        if(mysqli_stmt_fetch($stmt)){

            if(password_verify($param_password, $pwd)){
                
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

    if (!isset($username)) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        die("Invalid Credentials");
    }

    if (!isset($userpassword)) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        die("Invalid Credentials");
    }

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

    $sql = "SELECT pwd FROM accounts WHERE username=?";

    if($stmt = mysqli_prepare($conn,$sql)){

        mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $pwd);

        if(mysqli_stmt_fetch($stmt)){

            if(password_verify($param_password, $pwd)){

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

?>