<?php
session_start();
include("databaseConnection.php"); // ito database ko
include("navigationBarLogoOnly.php");


function ReSendOTP(){ // ito yung method para mag ggenerate ng random code
    $length = 6;
    $characters = '0123456789';
    $_SESSION["otp"] = '';

    for ($x = 0; $x < $length; $x++) {
        $_SESSION["otp"] .=  $characters[random_int(0, strlen($characters) - 1)];
    }
}
ReSendOTP();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\htdocs\finalProject\PHPMailer\PHPMailer\src\Exception.php'; // need mo palitan yung tatlo na to based sa file path mo
require 'C:\xampp\htdocs\finalProject\PHPMailer\PHPMailer\src\PHPMailer.php';
require 'C:\xampp\htdocs\finalProject\PHPMailer\PHPMailer\src\SMTP.php';

$email = $_SESSION['emailAddress']; //palitan mo to ito yung session na input ng user

$mail = new PHPMailer(true);

try {

    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'bigbrewpinagbarilan0@gmail.com'; // ito palit mo email mo na pang send
    $mail->Password   = 'kebz vztr gcol peqq';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;


    $mail->setFrom($email, 'Big Brew Pinagbarilan'); // dito yung email nung nag register saka pangalan nung email address mo
    $mail->addAddress($email, $_SESSION['emailAddress']); // pati ito 


    $mail->isHTML(true);
    $mail->Subject = 'OTP Verification Code';
    $mail->Body    = "
    <html>
        <head>
            <style>
                .cont1{
    
                    width: 700px;
                    height: 850px;
    
                    background-color: #d37b1c;
                    border-radius: 20px;
                    
                }
                .cont1 a {
                    color: #FFF8F0; 
                    text-decoration: none; 
                    font-weight: bold;
                }
                .cont1 a:visited {
                    color:#FFF8F0; 
                }
                body{
                        margin: 0;
                        padding: 0;
                        display: flex;
                        justify-content: center; 
                        align-items: center; 
                        color: #FFF8F0;
    
                } 
                .maintxt1{
                    padding: 0px;
                    margin: 0px;
                    font-size: 40px;
                    color: #FFF8F0;
                }  
                .maintxt2{
                    padding: 0px;
                    margin: 0px;
                    font-size: 20px;
                    color: #FFF8F0;
                } 
                .maintxt3{
                    color: #FFF8F0;
                    font-weight: lighter;
                    font-size: 15px;
                    width: 480px;
                }
                .maintxt4{
                    color: #FFF8F0;
                }
                .maintxt5{
                    color: #FFF8F0;
                    font-weight: lighter;
                    font-size: 15px;
                    width: 480px;
                    text-align: left;
                    padding-top: 20px;
                }
                .maintxt6{
                    color: #FFF8F0;
                    font-weight: lighter;
                    font-size: 15px;
                    width: 480px;
                    text-align: left;
                    padding-top: 15px;
                }
                
    
    
    
    
    
                .cont2{
                    width: 500px;
                    height: 150px;
                    background-color: #e5943c;
                    margin: 20px auto;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    color: #FFF8F0;
                    font-size: 36px;
                    font-weight: bold;
                    text-align: center;
                    line-height: 150px;
                    padding: 70px 0; 
                }
                
                body a:visited{
                    color:#FFF8F0; 
                } 
                
                body a{
                    color: #FFF8F0; 
                    text-decoration: none; 
                    font-weight: bold;
                }
                .pos{ 
                    margin: 20px auto;
                    display: flex;
                    justify-content: center;
                    align-items: center;
    
                    text-align: center;
                }
                .td1{
                margin-top: 0px;
                margin-bottom: 30px;
                padding-bottom: 150px;
                }
                .subtxt1{
                    margin: 0;
                    margin-left: 130px;
                    display: inline-block; 
                }
            </style>
        </head>
    
        <body>
        <table width='100%' height='100%' border='0' cellspacing='0' cellpadding='0'>
            <td align='center' valign='middle'> 
            <div class='cont1'>
                <img src='https://res.cloudinary.com/dbk1nswsg/image/upload/v1733746467/logo_white_xpfozx.png' width='190px'>
                <h1 class='maintxt1'>HELLO</h1>
                <h1 class='maintxt2'>$email</h1>
                <h2 class='maintxt3'>Thank you for choosing Big Brew. Your One-Time Password (OTP) is provided below</h2>
                <h2 class='maintxt3'>Please use this code to complete your authentication. For security purposes, do not share this code with anyone. </h2>
                <div class='cont2'>
                    <h1 class='subtxt1'> $_SESSION[otp]</h1>
                </div>
    
            </div>   
        </td>
     
        </table>
        
    </html>
        " ;    $mail->AltBody = '';


    $mail->send();
} catch (Exception $e) {
    echo "Failed To Send {$mail->ErrorInfo}";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP</title>
    <link rel="stylesheet" href="./mail1.css">
    <script>
        function Validate(){
           
            let userInput = document.getElementById("first").value + document.getElementById("second").value + 
                                    document.getElementById("third").value + document.getElementById("fourth").value + 
                                    document.getElementById("fifth").value + document.getElementById("sixth").value;
                            
            let OTP = "<?php echo $_SESSION['otp']; ?>";
  
          if(userInput == OTP){
            <?php $_SESSION["Verified"] = 1; ?>
                window.location.href = "verifyFinal.php";  
            }else{
            alert("Wrong OTP.");
          }
        }

        function moveFocus(currentInput, nextInputId) {
            if (currentInput.value.length >= currentInput.maxLength) {
                const nextInput = document.getElementById(nextInputId);
                if (nextInput) {
                    nextInput.focus();
                }
            }
        }
        
    </script>

</head>

<body>
    <center>
    <div id="container">
        <div id="otp-container">
                <img src="./pictures/otp picture.png" alt="">
                <div class="text-container">
                    <p>Enter the OTP sent to your email.</p>
                </div>
               
                <div id="input-container">
                    <input type="text" maxlength="1" name="first" id="first" oninput="moveFocus(this, 'second')" required>
                    <input type="text" maxlength="1" name="second" id="second" oninput="moveFocus(this, 'third')" required>
                    <input type="text" maxlength="1" name="third" id="third" oninput="moveFocus(this, 'fourth')" required>
                    <input type="text" maxlength="1" name="fourth" id="fourth" oninput="moveFocus(this, 'fifth')" required>
                    <input type="text" maxlength="1" name="fifth" id="fifth" oninput="moveFocus(this, 'sixth')" required>
                    <input type="text" maxlength="1" name="sixth" id="sixth" required>

                    <div id="button-container">
                        <input type="submit" value="VERIFY" onclick="Validate()">
                    </div>
                </div>

                <div class="resend">
                    <a href="./mail.php">Didn't receive the OTP? <span>RESEND OTP</span></a>
                </div>
               
        </div>
    </div>
</center>
</body> 

</html>
