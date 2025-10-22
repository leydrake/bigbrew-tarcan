<?php
session_start();   

    include("databaseConnection.php");  
    include("navigationBar.php");
    include("applyTheme.php");



    if(isset($_POST['forgot_button'])){
        header("location: forgotPassword.php");
        exit();
   }

   if(isset($_POST['register_button'])){
    header("location: register.php");
    exit();
   }


//    LOGGING IN
if(isset($_POST['signin_button'])){
    $email = filter_input(INPUT_POST, "email_txt", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password_txt", FILTER_SANITIZE_SPECIAL_CHARS);
    // KUNIN MO 'YUNG EMAIL/ WHATEVER MAN 'YUNG IPANG LOGIN MO
    try{
        $sql = "SELECT * FROM login_credentials WHERE emailAddress = '$email'";
        $result = mysqli_query($conn, $sql);

        //test if may nakuhang row
        if (mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);

            // test if yung nakuhang row is magkapareho sa inputted ni user
            if ($row['login_attempt'] >=5){
                mysqli_query($conn, "UPDATE login_credentials SET Blocked = 1 WHERE emailAddress = '$email'");
            }
            
            if ($row['Blocked'] == 1){
                echo "<script> alert ('Your account is blocked, please contact the admin for assistance.'); </script>";
            }
            else{
                if (password_verify($password, $row["Passwords"])){
                    $_SESSION ['emailAddress'] = $row["emailAddress"];
                    mysqli_query($conn, "UPDATE login_credentials SET login_attempt = 0 WHERE emailAddress = '$email'");
                    echo "<script> window.location.href = './logged_in/index.php' </script>";

                    exit();
             }else{
                echo "<script> alert ('Invalid email or password.'); </script>";
                // ONLY NECCESSARY if same table si admin and users. para hindi ma block si admin
                if($email == 'dranskyxd@gmail.com'){

                }
                else{
                    mysqli_query($conn, "UPDATE login_credentials SET login_attempt = (SELECT login_attempt FROM login_credentials WHERE emailAddress = '$email') +1  WHERE emailAddress = '$email'");
                }
                session_destroy();
            }
            }
        }else{
            echo "Credentials not found.";
        }
    } catch(Exception $e){
        echo "error" . $e;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./login1.css">
    <script>
     // Prevent the user from going back to the previous page after logging out
    if (window.history && window.history.pushState) {
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, null, window.location.href);
        };
    }

        function showRegister(){
            window.location.href = 'register.php';
        }

        function forgotPassword(){ 
            window.location.href = 'forgotPassword.php';
        }
        function seePassword(){
            let password = document.getElementById("password_text");
            let confirm = document.getElementById("confirmPass_text");
            let eyeIcon = document.getElementById("eye-icon");

            if(password.type == "password"){
                password.type = "text";
                
                eyeIcon.src ="./pictures/eye-open.png";
            }else{
                password.type = "password";
                
                eyeIcon.src ="./pictures/eye-close.png";
            }
        }
    </script>
</head> 
<body>
    
    
    <center> 
      <div class="register-container" id="register-container">
            <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                <div class="login-container" id="login-container">
                <div class="text-header">Glad to have <br>you back!</div>
                
                <div class="data" >
                    <input type="email" name="email_txt" placeholder="Email address" required>
                </div>

                <div class="data eye-flex">
                    <input type="password" name="password_txt" id="password_text" placeholder="Password" required>
                    <img src="./pictures/eye-close.png" 
                    id= "eye-icon" onclick="seePassword()">
                </div>
                
                <div class="button-container">
                    <input type="submit" value="SIGN IN" name="signin_button" class="signin-button">
                </div>

            </form>  
                <div class="data register-container">
                    <a href="./register.php">Don't have an account? <span>Register here!</span></a>
                </div>
                <div class="forgot-container">
                        <a href="./forgotPassword.php">Forgot password?</a>
                </div>
        </div>

       
    </center>
</body>
</html>

   