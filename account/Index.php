<?php

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (!isset($_GET['uname'])) {
        http_response_code(503);
        die();
    }

    if (!isset($_GET['pw'])) {
        http_response_code(503);      
        die();
    }

    $userlogin = $_GET['uname'];
    $userpassword = $_GET['pw'];
    $param_userlogin = $userlogin;
    $param_password = $userpassword;

    $sql = "SELECT pwd FROM accounts WHERE username=?";

    // create connection
    $servername = "localhost";
    $password = file_get_contents("password.txt","r");
    $username = "Admin2";
    $dbname = "meatgrinder";

    // create/check connection to database.
    $conn = mysqli_connect($servername,$username,$password,$dbname);
    if ($conn === false) {
        die();
    }

    if ($conn ->connect_error){
        http_response_code(500);
        die();
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
                die();
            }
        }
        else {
            http_response_code(503);
            die();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "PUT") {

    $_POST = json_decode(file_get_contents('php://input'), true);

    if(!isset($_POST)) {
        http_response_code(503);       
        die();
    }

    if (!isset($_POST['uname'])) {
        http_response_code(503);       
        die();
    }

    if (!isset($_POST['pw'])) {
        http_response_code(503);        
        die();
    }

    if(!empty($_POST['newEmail'])) {
        $newEmail = $_POST['newEmail'];
        $param_newEmail = $newEmail;
    }

    if(!empty($_POST['newPw'])) {
        $newPW = $_POST['newPw'];
        $param_newpw = $newPW;
    }

    $userlogin = $_POST['uname'];
    $userpassword = $_POST['pw'];
    $param_userlogin = $userlogin;
    $param_password = $userpassword;

    if (!empty($newPW)) {
        if (strlen($newPW) < 8 || strlen($newPW) > 255) {
            http_response_code(503);
            die();
        }
    }

    if (empty($newEmail) && empty($newPW)){
        http_response_code(500);
        die();
    }

    if (!empty($newEmail) && strlen($newEmail) > 45) {
        http_response_code(500);
        die();
    }

    if (!empty($newEmail) && !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        die();
    }

    // create connection
    $servername = "localhost";
    $password = file_get_contents("password.txt","r");
    $username = "Admin2";
    $dbname = "meatgrinder";

    // create/check connection to database.
    $conn = mysqli_connect($servername,$username,$password,$dbname);
    if ($conn === false) {
        die();
    }

    if ($conn ->connect_error){
        http_response_code(500);
        die();
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
                die();
            }
        }
        else {
            http_response_code(503);
            die();
        }
    }

}

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

    $_POST = json_decode(file_get_contents('php://input'), true);

    if(!isset($_POST)) {
        http_response_code(503);      
        die();
    }

    if (!isset($_POST['uname'])) {
        http_response_code(503);      
        die();
    }

    if (!isset($_POST['pw'])) {
        http_response_code(503);      
        die();
    }

    $userlogin = $_POST['uname'];
    $userpassword = $_POST['pw'];
    $param_userlogin = $userlogin;
    $param_password = $userpassword;

    // create connection
    $servername = "localhost";
    $password = file_get_contents("password.txt","r");
    $username = "Admin2";
    $dbname = "meatgrinder";

    // create/check connection to database.
    $conn = mysqli_connect($servername,$username,$password,$dbname);
    if ($conn === false) {
        die();
    }

    if ($conn ->connect_error){
        http_response_code(500);
        die();
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
                die();
            }
        }
        else {
            http_response_code(503);
            die();
        }
        mysqli_stmt_close($stmt);
    }
}

?>