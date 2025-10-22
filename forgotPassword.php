<!-- pPHPPH -->
 <?php
    session_start();
    include("databaseConnection.php");

    include ("navigationBarLogoOnly.php");
    include("applyTheme.php");

    if (isset($_POST["back_button"])){
        exit();
    }

    if(isset($_POST['recovery_button'])){

        $email = filter_input(INPUT_POST, "email_txt", FILTER_SANITIZE_SPECIAL_CHARS);

        try{
            $sql = "SELECT * FROM login_credentials WHERE emailAddress = '$email' ";
            $result = mysqli_query($conn, $sql);
            //test if may nakuhang row
            if (mysqli_num_rows($result) == 0){
                $row = mysqli_fetch_assoc($result);
                // test if yung nakuhang row is magkapareho sa inputted ni user
                echo "Credentials not found.";
            }else{
                $_SESSION['email_address'] = $email;
                echo "<script> window.location.href = './forgotPasswordMail.php'; </script>";

                
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
    <link rel="icon" type="image/x-icon" href="./pictures/logo white.png">
    <title>Account recovery</title>
    <link rel="stylesheet" href="./forgotpassword1.css">
    <script>
    function disableButtonAndSubmit(button) {
        button.disabled = true;                // Disable the button immediately
        button.value = "Processing...";        // Update button text to indicate submission
        return true;                           // Allow the form to proceed with submission
    }
</script>

</head>
<body>

    
    
<center> 
        <div class="text-container">
        
        </div>
      <div class="container" >
      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

            <div class="forgotPass-container" id="forgotPass-container">
                <div class="data" >
                    <input type="email" name="email_txt" placeholder="Email address" required>
                </div>

                <div class="button-container">
                     <input type="submit" value="SUBMIT" class="recovery-button" name="recovery_button" onclick="return disableButton(this)">
                </div>
                </div>
            </div>
        </form>
</center>

</body>
</html>

