<?php
    include ("navigationBarLogoOnly.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP</title>
    <link rel="stylesheet" href="./registerFinal.css">
    <script>
        function goToLogin(){
            window.location.href = "./login.php";
        }
    </script>

</head>

<body>
<center>
    <div class="container">
        <h1 style="font-size: 2.5rem;">Password Reset Successful</h1>
        <p>Your password has been successfully updated. You can now log in with your new password. <br> <br>
        </p>
       

        <button onclick="goToLogin()">
            GO TO LOGIN
        </button>
    </div>

</center>
</body> 

</html>

