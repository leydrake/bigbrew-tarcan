<?php
    session_start();

    include("databaseConnection.php");
    include ("navigationBarLogoOnly.php");
    $firstName = $_SESSION['firstName'];
    $lastName  =  $_SESSION['lastName'];
    $emailAddress = $_SESSION['emailAddress'];
    $address =  $_SESSION['Address'];
    $phone = $_SESSION['Phone'];
    $password =  $_SESSION['Passwords'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO login_credentials (firstName, lastName,emailAddress,Passwords,Address,Phone) VALUES ('$firstName', '$lastName','$emailAddress', '$hash','$address','$phone')";
    mysqli_query($conn, $sql);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bigbrew Tarcan</title>
    <link rel="icon" type="image/x-icon" href="./pictures/logo white.png">
    <link rel="stylesheet" href="./registerFinal.css">
    <script>
        function goToLogin(){
            window.location.href = "./logged_in/index.php";
        }
    </script>

</head>

<body>
<center>
    <div class="container">
        <h1>Successfully Registered</h1>
        <p>You are now a member of BigBrew Community and now will be logged in, <span style="color: red"> but you will not be able to fully access the site because you are unverified.</span> <br> <br>
        </p>
       

        <button onclick="goToLogin()">
            CONTINUE
        </button>
    </div>

</center>
</body> 

</html>

