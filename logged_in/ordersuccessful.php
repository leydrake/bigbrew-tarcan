<?php

    include ("navigationBar.php");

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
            window.location.href = "./products.php";
        }
    </script>
    <style>
        *{
    padding: 0;
    margin: 0;
    font-family: "Montserrat";
}
.container{
    background-color: #fff;
    /* display: none; */
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    border-radius: 20px;
    width: 600px;
    height: 600px;
    box-shadow: rgba(0, 0, 0, 0.25) 0px 14px 28px, rgba(0, 0, 0, 0.22) 0px 10px 10px;

}
.container h1{
    font-size: 3rem;
    position: absolute;
    top: 10%;
    left: 50%;
    transform: translateX(-50%);
    font-weight: bold;
    color: #bc6e19;
    
}
.container p{
    position: absolute;
    top: 40%;
    left: 50%;
    transform: translateX(-50%);
}
.container p > span{
    color: red;
}
.container button{
    position: absolute;
    top: 60%;
    left: 50%;
    transform: translateX(-50%);
}
.container button {
    padding: 15px 40px;
    cursor: pointer;
    background-color: #fff;
    border-radius: 5px;
}
    </style>

</head>

<body>
<center>
    <div class="container">
        <h1>Order Completed</h1>
    
       

        <button onclick="goToLogin()">
            CONTINUE
        </button>
    </div>

</center>
</body> 

</html>

