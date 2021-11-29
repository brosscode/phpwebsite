<?php
session_start();

// create connection to database.
$servername = "localhost";
$password = file_get_contents("password.txt","r");
$username = "Admin2";
$dbname = "meatgrinder";

// create/check connection to database.
$conn = mysqli_connect($servername,$username,$password,$dbname);
if ($conn === false) {
    http_response_code(502);
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

if ($conn ->connect_error){
    http_response_code(502);
    die("Oh No." . mysqli_connect_error());
}

// if a post is the method this happens.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $accountname = $_POST['uname'];
    $userpassword = $_POST['pw'];

    if (empty($accountname) || strlen($accountname) < 6 || strlen($accountname) > 45) {
        $_SESSION['error_message1'] = "Username Does Not Meet Length Requirements";
    }
    if (empty($userpassword) || strlen($userpassword) < 8 || strlen($userpassword) > 255) {
        $_SESSION['error_message2'] = "Password Below Length Requirements";
    }
    if (!preg_match('/^[0-9A-Z]*([0-9][A-Z]|[A-Z][0-9])[0-9A-Z]*$/i', $userpassword)) {
        $_SESSION['error_message3'] = "Password Invalid, Missing Character / Numeric";
    }

    $emailPresent = false;

    if (isset($_POST['email'])) {
        $emailPresent = true;
        $email = $_POST['email'];
        // sanitize it first
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            $_SESSION['error_message4'] = "Invalid Email";
        }
    }

    if (isset($_SESSION['error_message1']) || isset($_SESSION['error_message2']) || isset($_SESSION['error_message3']) || isset($_SESSION['error_message4'])) {
        http_response_code(400);
        $_SESSION['error_message5'] = 'Please correct errors and submit again.';
        // header('Location: '.$_SERVER['PHP_SELF']);
        die();
    }

    else {
        // set account name param
        $param_accountname = $accountname;
        // take the username/email/password and run a check in the database for them.
        // boolean for valid, check for variables using a prepared statment
        $sql = "SELECT * FROM accounts WHERE username=?";

        // prepare failue
        if(!$stmt = mysqli_prepare($conn,$sql)){
            http_response_code(503);
            echo 'sql statement failed';
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
                $_SESSION['error_message6'] = "User already exists";
                die();
            }
            mysqli_stmt_close($stmt);

            if ($emailPresent == true) {
                
                $sql = "SELECT * FROM accounts WHERE email=?";

                // prepare failue
                if(!$stmt = mysqli_prepare($conn,$sql)){
                    echo 'sql statement failed';  
                    exit();
                }
                // did not fail again.
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
                        $_SESSION['error_message7'] = "Email Invalid";
                        die();
                    }
                }
            }
            // add errors to if statement
            if (isset($_SESSION['error_message6']) || isset($_SESSION['error_message7'])) {
                http_response_code(500);
                header('Location: '.$_SERVER['PHP_SELF']);
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
                            echo 'sql statement failed';  
                            exit();
                    }
                    mysqli_stmt_bind_param($stmt,"sss",$param_accountname,$param_email,$param_userpassword);
                }

                else {
                    // Insert into
                    $sql = "INSERT INTO accounts (username,pwd) VALUES (?,?)";

                    if(!$stmt = mysqli_prepare($conn,$sql)){
                            echo 'sql statement failed';  
                            exit();
                    }
                    mysqli_stmt_bind_param($stmt,"ss",$param_accountname,$param_userpassword);
                }

                // verify it worked
                if(mysqli_stmt_execute($stmt)) {
                    $conn->close();
                    http_response_code(200);
                    header("Location: http://localhost/register/accountCreated.php");
                    exit();
                } 
                else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                    http_response_code(502);
                    $conn->close();
                }
            }
        }
    }
}

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

<?php
session_unset();
session_destroy();
?>