<?php
    session_start();
    include("databaseConnection.php");
    include("navigationBarLogoOnly.php");


    if(isset($_POST['later_button'])){

        echo "<script> 
        window.location.href = './registerFinal.php'
    </script> ";        
    exit();
    }
    if(isset($_POST['now_button'])){
        echo "<script> 
        window.location.href = './mail.php'
    </script> ";        
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel ="stylesheet" href = "./registrationSecond.css">

</head>
<body>
    <div class="contents">
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="left-container">
                <h1>VERIFY <br> &nbsp;LATER</h1>
                <input type="submit" value="Verify Later" name="later_button">


            </div>
            <div class="right-container">
                <h1>VERIFY <br>NOW</h1>
                <input type="submit" value="Verify Now" name="now_button">
            </div>
        </form>
    </div>
</body>
</html>