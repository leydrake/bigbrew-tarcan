<?php
    session_start();

    include("databaseConnection.php");
    include ("navigationBarLogoOnly.php");
    $emailAddress = $_SESSION['emailAddress'];
   
    $sql = "UPDATE login_credentials SET Verified = '1' WHERE emailAddress = '$emailAddress'";
    mysqli_query($conn, $sql);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP</title>
    <link rel="stylesheet" href="./verifyFinal.css">
    <script>
        function goToLogin(){
            window.location.href = "./index.php";
        }
    </script>

</head>

<body>
<center>
    <div class="container">
        <h1>Successfully Verified</h1>
        <p>You are now a fully verified member and can now fully access the Big Brew Website<br> <br>
        </p>
       

        <button onclick="goToLogin()">
            CONTINUE
        </button>
    </div>

</center>
</body> 

</html>

