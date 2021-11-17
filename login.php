<?php
// create connection
$servername = "localhost";
$password = file_get_contents("password.txt","r");
$username = "Admin2";
$dbname = "meatgrinder";

// create/check connection to database.
$conn = mysqli_connect($servername,$username,$password,$dbname);
if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

if ($conn ->connect_error){
    http_response_code(500);
    include('my_500.php');
    exit("Oh No." . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
// get vars
$userlogin = $_POST['loginID'];
$userpassword = $_POST['password'];
// set params
$param_userlogin = $userlogin;
$param_password = $userpassword;


    // does not show
    // don't need to check input for errors outside of blank
    if (empty($userlogin)) {
        $error_message4 = "Empty Name/Email. Try again.";
    }
    if (empty($userpassword)) {
        $error_message1 = "Empty Password. Try again.";
    }
    // email tracker
    $isEmail = false;
    // check to see if they used an email or name by looking for an @, also need a .com or something
    if (filter_var($userlogin, FILTER_VALIDATE_EMAIL)) {
        $isEmail = true;
    } else {}
    // begin prepping for a query.
    if (!isset($error_message1)){
        // sql statement
        if ($isEmail == true) {
            // does not seem to work
            // emails are not case sensititve
            $sql = "SELECT pwd FROM accounts WHERE email=?";
        }
        else {
            $sql = "SELECT pwd FROM accounts WHERE username=?";
            // echo 'using username query ';
        }
        if($stmt = mysqli_prepare($conn,$sql)){
            // bind
            mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
            // echo 'binding query... ';
            // execute the query
            mysqli_stmt_execute($stmt);
            // echo 'executing... ';
            // bind results
            mysqli_stmt_bind_result($stmt, $pwd);
            // echo 'binding result... ';
            // fetch
            if(mysqli_stmt_fetch($stmt)){
                // echo 'got results... ';
                if(password_verify($param_password, $pwd)){
                    header("Location: http://localhost/landing.php");
                    exit();
                }
                else {
                    $error_message2 = "No Combination Found.";
                }
            }
            else {
                $error_message2 = "No Combination Found.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}


// error handling
$showError1 = isset($error_message1);
$showError2 = isset($error_message2);
$showError3 = isset($error_message3);
$showError4 = isset($error_message4);

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
                <input class='userinput' type="text" name="loginID" class="form-control" id="loginID">
                <div class='loginError' style='display:<?php if (!$showError4) echo "none"; else echo "block"; ?>;'> <?php echo $error_message4; ?></div>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input class='userinput' type="password" name="password" class="form-control" id="password">
                    <div class='loginError' style='display:<?php if (!$showError1) echo "none"; else echo "block"; ?>;'> <?php echo $error_message1; ?></div>
                    <div class='loginError' style='display:<?php if (!$showError2) echo "none"; else echo "block"; ?>;'> <?php echo $error_message2; ?></div>
                    <div class='loginError' style='display:<?php if (!$showError3) echo "none"; else echo "block"; ?>;'> <?php echo $error_message3; ?></div>
            </div>
            <input class="submit" type="submit"></input>
        </form>
    </body>
</html>