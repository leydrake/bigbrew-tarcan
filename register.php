
<?php
    session_start();

    include("databaseConnection.php");
    include("navigationBarLogoOnly.php");
    include("applyTheme.php");

    if (isset($_POST["register_button"])){

        // kinukuha mga value ng sessions
        $firstName = filter_input(INPUT_POST, "firstName_txt", FILTER_SANITIZE_SPECIAL_CHARS);
        $lastName= filter_input(INPUT_POST, "lastName_txt", FILTER_SANITIZE_SPECIAL_CHARS);
        $emailAddress = filter_input(INPUT_POST, "email_txt", FILTER_VALIDATE_EMAIL);
        $phone = filter_input(INPUT_POST, "phone_number", FILTER_SANITIZE_NUMBER_INT);
        $address = filter_input(INPUT_POST, "address_txt", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password_txt", FILTER_SANITIZE_SPECIAL_CHARS);
        $confirmPass = filter_input(INPUT_POST, "confirmPass_txt", FILTER_SANITIZE_SPECIAL_CHARS);

     

        $sql = "SELECT * FROM login_credentials WHERE emailAddress = '$emailAddress'";
        $result = mysqli_query($conn, $sql);

        if (empty($firstName) || empty($lastName)||empty($emailAddress)||empty($password) ||empty($confirmPass)){
            echo "Please fill in the blank field/s.";
        }else if(mysqli_num_rows($result) > 0){ // test if may kamuha nang username
            echo "Email address already exist.";
        }else if (strlen($password) < 8){
            echo "<script> 
                    alert('Password must have at least 8 characters');
                </script> ";
        }else if($password != $confirmPass){
                echo "<script> 
                    alert('Password did not match');
                </script> ";
        }else {

            $_SESSION['firstName'] = $firstName;
            $_SESSION['lastName'] = $lastName;
            $_SESSION['emailAddress'] = $emailAddress;
            $_SESSION['Phone'] = $phone ;
            $_SESSION['Address'] = $address;
            $_SESSION['Passwords'] = $password ;

            echo "<script> 
                    window.location.href = './registrationSecond.php';
                </script> ";        
            }


    }
   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./register1.css">
    <script>
        // REGISTER FUNCTIONS
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
            let eyeIcon2 = document.getElementById("eye-icon2");

            if(confirm.type == "password"){
               
                confirm.type = "text";
                eyeIcon2.src ="./pictures/eye-open.png";
            }else{
                
                confirm.type = "password";
                eyeIcon2.src ="./pictures/eye-close.png";
            }
        }
        function back(){
            window.location.href = "./login.php";
        }

    </script>
</head> 
<body>

       
      

    <center>
        <div class="register-container" id="register-container">
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
            <br><br>
            <div class="data double-text icons">
                    <input type="text" name="firstName_txt" placeholder="First name" required>
                    <img src="./pictures/user.png" 
                    id= "eye-icon">
                </div>
                <div class="data double-text icons">
                    <input type="text" name="lastName_txt" placeholder="Last name" required>
                    <img src="./pictures/user.png" 
                    id= "eye-icon">
                </div>
                <div class="data icons">
                    <input type="email" name="email_txt" placeholder="Email address" required>
                    <img src="./pictures/email.png" 
                    id= "eye-icon">
                    
                </div>
                <div class="data icons" style="margin-top:-20px">
                    <input type="number" name="phone_number" placeholder="Phone number" class="phone"  maxlength="11" required>
                    <img src="./pictures/telephone.png" 
                    id= "eye-icon">
                </div>
                <div class="data icons" style="margin-top:-20px">
                    <input type="text" name="address_txt" placeholder="Address" required>
                    <img src="./pictures/address.png" 
                    id= "eye-icon">
                </div>
                <div class="data double-text eye-flex">
                    <input type="password" name="password_txt" 
                    id= "password_text" placeholder="Password" required>

                    <img src="./pictures/eye-close.png" 
                    id= "eye-icon">
                </div>
                <div class="data double-text eye-flex">
                    <input type="password" name="confirmPass_txt"
                    id= "confirmPass_text"  placeholder="Confirm password" required>
                    <img src="./pictures/eye-close.png" 
                    id= "eye-icon2" onclick="seePassword2()">
                    
                </div>
                <div class="button-container">
                    <input type="submit" value="SIGN UP" name="register_button" class="signup-button">
                </div>
        </form>  
        </div>
    </center> 
    

</body>
</html>

<!-- PHP -->

