<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // change isset to check for $_POST info in this case

    $accountname = $_POST['uname'];
    $userpassword = $_POST['pw'];

    if (!isset($accountname) || strlen($accountname) < 6 || strlen($accountname) > 45) {
        $error1 = "Username Does Not Meet Length Requirements";
    }
    if (!isset($userpassword) || strlen($userpassword) < 8 || strlen($userpassword) > 255) {
        $error2 = "Password Below Length Requirements";
    }
    if (!preg_match('/^[0-9A-Z]*([0-9][A-Z]|[A-Z][0-9])[0-9A-Z]*$/i', $userpassword)) {
        $error3 = "Password Invalid, Missing Character / Numeric";
    }

    $emailPresent = false;

    if (isset($_POST['email'])) {
        $emailPresent = true;
        $email = $_POST['email'];
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            $error4 = "Invalid Email";
        }
    }

    if (isset($error1) || isset($error2) || isset($error3) || isset($error4)) {
        http_response_code(400);
        die();
    }

    else {

        // create connection to database.
        $servername = "localhost";
        $password = file_get_contents("password.txt","r");
        $username = "Admin2";
        $dbname = "meatgrinder";

        // create/check connection to database.
        $conn = mysqli_connect($servername,$username,$password,$dbname);
        if ($conn === false) {
            http_response_code(500);
            die();
        }

        if ($conn ->connect_error){
            http_response_code(500);
            die();
        }

        $param_accountname = $accountname;

        $sql = "SELECT * FROM accounts WHERE username=?";

        if(!$stmt = mysqli_prepare($conn,$sql)){
            http_response_code(503);
            exit();
        }
        else{
            // bind query
            mysqli_stmt_bind_param($stmt,"s",$param_accountname);
            // execute query
            mysqli_stmt_execute($stmt);
            // store results in statment
            mysqli_stmt_store_result($stmt);
            // generate a row count
            $row =  mysqli_stmt_num_rows($stmt);

            // check to see if there was a match for the name
            if ($row > 0) {
                http_response_code(500);
                $error5 = "User already exists";
                die();
            }
            mysqli_stmt_close($stmt);

            if ($emailPresent == true) {
                
                $sql = "SELECT * FROM accounts WHERE email=?";

                if(!$stmt = mysqli_prepare($conn,$sql)){
                    exit();
                }
                else {   
                    // query statement
                    mysqli_stmt_bind_param($stmt,"s",$param_email);
                    $param_email = $email;
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);
                    $row =  mysqli_stmt_num_rows($stmt);
                    mysqli_stmt_close($stmt);

                    // check to see if there was a match for the email.
                    if($row > 0) {
                        http_response_code(500);
                        $error6 = "Email Invalid";
                        die();
                    }
                }
            }
            // add errors to if statement
            if (isset($error5) || isset($error6)) {
                http_response_code(500);
                exit();
            }

            else {
                // hash password for insertion
                $hashed_password = password_hash($userpassword, PASSWORD_DEFAULT);

                $param_userpassword = $hashed_password;

                if ($emailPresent == true){
                    // Insert into
                    $sql = "INSERT INTO accounts (username,email,pwd) VALUES (?,?,?)";

                    if(!$stmt = mysqli_prepare($conn,$sql)){
                            exit();
                    }
                    mysqli_stmt_bind_param($stmt,"sss",$param_accountname,$param_email,$param_userpassword);
                }

                else {
                    // Insert into
                    $sql = "INSERT INTO accounts (username,pwd) VALUES (?,?)";

                    if(!$stmt = mysqli_prepare($conn,$sql)){
                            exit();
                    }
                    mysqli_stmt_bind_param($stmt,"ss",$param_accountname,$param_userpassword);
                }

                // verify it worked
                if(mysqli_stmt_execute($stmt)) {
                    $conn->close();
                    exit();
                } 
                else {
                    http_response_code(500);
                    $conn->close();
                }
            }
        }
    }
}

?>