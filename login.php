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

    // don't need to check input for errors outside of blank
    if (!isset($userlogin) || !isset($userpassword)) {
        $error_message1 = "Empty Field. Try again.";
    }
    // email tracker
    $isEmail = false;
    // check to see if they used an email or name by looking for an @
    if (preg_match("/^([\@]+)$/", $userlogin)) {
        $isEmail = true;
    }
    // begin prepping for a query.
    if (!isset($error_message1)){
        // sql statement
        if ($isEmail == true) {
            $sql = "SELECT pwd FROM accounts WHERE email=?";
        }
        else {
            $sql = "SELECT pwd FROM accounts WHERE username=?";
        }
        if($stmt = mysqli_prepare($conn,$sql)){
            // bind
            mysqli_stmt_bind_param($stmt,"s", $param_userlogin);
            // execute the query
            mysqli_stmt_execute($stmt);
            // bind results
            mysqli_stmt_bind_result($stmt, $pwd);
            // fetch
            if(mysqli_stmt_fetch($stmt)){
                if(password_verify($param_password, $pwd)){
                    header("Location: http://localhost/landing.php");
                    exit();
                }
                else {
                    $error_message2 = "No Combination Found.";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
}


// error handling
$showError1 = isset($error_message1);
$showError2 = isset($error_message2);

?>

<!DOCTYPE html>
<html>
    <head>
        <style>
        </style>
    </head>
    <body>
        <form action="login.php" method="post" id='login'>
            <div class="form-group">
                <label for="usernameOrEmail">Username or Email:</label>
                <input type="text" name="loginID" class="form-control" id="loginID">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control" id="password">
                    <p style='display:<?php if (!$showError1) echo "none"; else echo "block"; ?>;'> <?php echo $error_message1; ?></p>
                    <p style='display:<?php if (!$showError2) echo "none"; else echo "block"; ?>;'> <?php echo $error_message2; ?></p>
            </div>
            <input type="submit"></input>
        </form>
    </body>
</html>