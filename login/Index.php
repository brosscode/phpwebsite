<?php

    // change isset to check for $_POST info in this case

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userlogin = $_POST['uname'];
    $userpassword = $_POST['pw'];
    $param_userlogin = $userlogin;
    $param_password = $userpassword;

    if(!isset($userlogin)) {
        http_response_code(503);
        die();
    }

    if(!isset($userpassword)) {
        http_response_code(503);
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
        exit();
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

?>