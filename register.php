<?php
// create connection to database.
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

// new php, new tools, when we post this php shit happens.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

// Take in user inputs
if (isset($_POST['username'])) {
    $accountname = $_POST['username'];
    if (strlen($accountname) < 1) {
        $error_message1 = "Username Below Length Requirements";
    }
}

if (isset($_POST['password'])) {
    $userpassword = $_POST['password'];
    if (strlen($userpassword) < 8) {
        $error_message2 = "Password Below Length Requirements";
    }
    if (!preg_match('~[0-9]+~', $userpassword)) {
        $error_message4 = "Password Invalid, Missing Character / Numeric";
    }
}


if (isset($_POST['email'])) {
    $email = $_POST['email'];
    // if (!filter_var('email', FILTER_VALIDATE_EMAIL)) {
    //     $error_message3 = "Invalid Email";
    // }
    if (strlen($email) < 1) {
        $error_message5 = "Email Below Length Requirements";
    }
}

if (isset($error_message1) || isset($error_message2) || isset($error_message3) || isset($error_message4) || isset($error_message5)) {
    echo 'Please correct errors and submit again.';
}

else {
    $param_accountname = $accountname;


    // take the username/email/password and run a check in the database for them.
    // boolean for valid, check for variables using a prepared statment
    $sql = "SELECT * FROM accounts WHERE username=?";

    // prepare failue
    if(!$stmt = mysqli_prepare($conn,$sql)){
        echo 'sql statement failed';  
        exit();
    }

        // did not fail to prepare
        else{
            // query statement
            mysqli_stmt_bind_param($stmt,"s",$param_accountname);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $row =  mysqli_stmt_num_rows($stmt);

            // check to see if there was a match in the name or the email.
            if ($row > 0) {
                $error_message6 = "User already exists";
            }
            mysqli_stmt_close($stmt);

            $sql = "SELECT * FROM accounts WHERE email=?";

            // prepare failue
            if(!$stmt = mysqli_prepare($conn,$sql)){
                echo 'sql statement failed 1';  
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

            // check to see if there was a match in the name or the email.
            if($row > 0) {
                $error_message7 = "Email already in use";
            }
            
            // add errors to if statement
            if (isset($error_message6) || isset($error_message7)) {
                echo 'Check information or attempt a login.';
            }
            else {
                // hash password for insertion
                $hashed_password = password_hash($userpassword, PASSWORD_DEFAULT);

                $param_userpassword = $hashed_password;

                // Insert into
                $sql = "INSERT INTO accounts (username,email,pwd) VALUES (?,?,?)";

                if(!$stmt = mysqli_prepare($conn,$sql)){
                    echo 'sql statement failed';  
                    echo '<form action="signup.php" method="post">
                    </form>';
                    exit();
                }
                mysqli_stmt_bind_param($stmt,"sss",$param_accountname,$param_email,$param_userpassword);

                
                // verify it worked
                if(mysqli_stmt_execute($stmt)) {
                    $conn->close();
                    header("Location: http://localhost/login.php");
                    exit();
                } 
                else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                    $conn->close();
                }
                }
            }
        }
    }
}

$showError1 = isset($error_message1);
$showError2 = isset($error_message2);
$showError3 = isset($error_message3);
$showError4 = isset($error_message4);
$showError5 = isset($error_message5);
$showError6 = isset($error_message6);
$showError7 = isset($error_message7);

?>

<!DOCTYPE html>
<html>
    <head>
        <style>
        </style>
    </head>
    <body>
        <form action="register.php" method="post" id='register'>
            <div class="form-group">
                <label for="username">Name:</label>
                    <input type="text" name="username" class="form-control" id="usr" value='<?php if (isset($accountname)) echo $accountname; else; ?>'>
                        <p style='display:<?php if (!$showError1) echo "none"; else echo "block"; ?>;'> <?php echo $error_message1; ?></p>
                        <p style='display:<?php if (!$showError6) echo "none"; else echo "block"; ?>;'> <?php echo $error_message6; ?></p>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                    <input type="text" name="email" class="form-control" id="email" value='<?php if (isset($email)) echo $email; else; ?>'>
                        <p style='display:<?php if (!$showError3) echo "none"; else echo "block"; ?>;'> <?php echo $error_message3; ?></p>
                        <p style='display:<?php if (!$showError5) echo "none"; else echo "block"; ?>;'> <?php echo $error_message5; ?></p>
                        <p style='display:<?php if (!$showError7) echo "none"; else echo "block"; ?>;'> <?php echo $error_message7; ?></p>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                    <input type="password" name="password" class="form-control" id="pwd" value='<?php if (isset($userpassword)) echo $userpassword; else; ?>'>
                        <p style='display:<?php if (!$showError4) echo "none"; else echo "block"; ?>;'> <?php echo $error_message4; ?></p>
                        <p style='display:<?php if (!$showError2) echo "none"; else echo "block"; ?>;'> <?php echo $error_message2; ?></p>
            </div>
            <input type="submit"></input>
        </form>
    </body>
</html>