
<?php
    include("databaseConnection.php");
    
    $themeResult = mysqli_query($conn, "SELECT * FROM theme_table");
    $themeRow = mysqli_fetch_assoc($themeResult);
   
    $imageDataLogo = base64_encode($themeRow['currentLogo']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./navigationBar1.css">
    <script>
         function goToHome(){
            window.location.href = "./index.php";
        }
    </script>
    <style>
        header{
            background-color: <?php echo $themeRow['navigatorColor'] ?>;
        }

        header > button {
            background-color: <?php echo $themeRow['navigatorButton'] ?>;

        }
        .logo h1  {
            color: <?php echo $themeRow['navigatorFont'] ?>;
        }
    </style>
    
</head>
<body>
    <header>
        <div class="logo">
            <img src="data:image/png;base64,<?php echo $imageDataLogo; ?>" onclick="goToHome()">
            <h1 onclick="goToHome()">BIGBREW</h1>
        </div>
        
    
    </header>
</header>
</body>
</html>


