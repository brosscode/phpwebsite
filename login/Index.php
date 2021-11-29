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
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

if ($conn ->connect_error){
    http_response_code(500);
    include('my_500.php');
    exit("Oh No." . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userlogin = $_POST['uname'];
    $userpassword = $_POST['pw'];
    $param_userlogin = $userlogin;
    $param_password = $userpassword;

    if (empty($userlogin) || strlen($userlogin) < 6 || strlen($userlogin) > 45) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        die();
    }
    if (empty($userpassword) || strlen($userpassword) < 8 || strlen($userpassword) > 255) {
        http_response_code(503);
        $_SESSION['error_message1'] = "Invalid Credentials";
        die();
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
                die();
            }
        }
        else {
            http_response_code(503);
            die();
        }
    }
}

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