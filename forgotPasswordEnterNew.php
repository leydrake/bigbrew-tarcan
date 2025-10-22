<?php
    session_start();

    include("databaseConnection.php");
    include ("navigationBarLogoOnly.php");
    include("applyTheme.php");


    if (isset($_POST["register_button"])){
        $emailAddress = $_SESSION['email_address'];

        $password = filter_input(INPUT_POST, "password_txt", FILTER_SANITIZE_SPECIAL_CHARS);
        $confirmPass = filter_input(INPUT_POST, "confirmPass_txt", FILTER_SANITIZE_SPECIAL_CHARS);

        $hash = password_hash($password, PASSWORD_DEFAULT);

         if($password != $confirmPass){
                echo "Password did not match";
        }else if (strlen($password) < 8){
            echo "<script> 
                    alert('Password must have at least 8 characters');
                </script> ";
        }else {
            $sql = "UPDATE login_credentials SET Passwords = '$hash'  WHERE emailAddress = '$emailAddress'";
            mysqli_query($conn, $sql);

            echo "<script> window.location.href = './forgotPasswordFinal.php'; </script>";
            session_destroy();
        }


    }
   
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./forgotPasswordEnterNew1.css">
    <script>
       
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
        function seePassword2(){
           
            let confirm = document.getElementById("confirmPass_text");
            let eyeIcon = document.getElementById("eye-icon");

            if(confirm.type == "password"){
               
                confirm.type = "text";
                eyeIcon2.src ="./pictures/eye-open.png";
            }else{
                
                confirm.type = "password";
                eyeIcon2.src ="./pictures/eye-close.png";
            }
        }
        function back(){
            window.location.href = "./forgotPassword.php";
        }

    </script>
</head> 
<body> 

        

    <center>
        <div class="newPassword-container" id="newPassword-container">
        <form action="<?php

// use function PHPSTORM_META\sql_injection_subst;

 htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                <div class="data eye-flex">
                    <input type="password" name="password_txt" 
                    id= "password_text" placeholder="Password" required>

                    <img src="./pictures/eye-close.png" 
                    id= "eye-icon" onclick="seePassword()">
                </div>
                <div class="data eye-flex">
                    <input type="password" name="confirmPass_txt"
                    id= "confirmPass_text"  placeholder="Confirm password" required>
                    <img src="./pictures/eye-close.png" 
                    id= "eye-icon" onclick="seePassword2()">
                    
                </div>
                <div class="button-container">
                    <input type="submit" value="Submit New Password" name="register_button" class="signup-button">
                </div>
        </form> 

        </div>
    </center>

    

</body>
</html>

<!-- PHP -->


