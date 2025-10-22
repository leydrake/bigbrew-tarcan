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
    <script>
        function goToHome(){
            window.location.href = "./index.php";
        }
        function goToSignin(){
            window.location.href = "./login.php";
        }
        function goToProducts(){
            
        }
        function goToAbout(){
            
        }
        function goToContact(){
            
        }
    </script>
    <link rel="stylesheet" href="./navigationBar1.css">

    <style>
        header{
            background-color: <?php echo $themeRow['navigatorColor'] ?>;
        }
        .nav_links li a, .logo h1  {
            color: <?php echo $themeRow['navigatorFont'] ?>;
        }
        header > button {
            background-color: <?php echo $themeRow['navigatorButton'] ?>;

        }
    </style>

</head>
<body>
    <header>
        <div class="logo">
        <img src="data:image/png;base64,<?php echo $imageDataLogo; ?>" onclick="goToHome()">

            <h1 onclick="goToHome()">BIGBREW</h1>
        </div>
        
        <nav>
            <ul class="nav_links">
                <li><a href="./products.php">PRODUCTS</a></li>
                <li><a href="#">ABOUT US</a></li>
                <li><a href="#">CONTACT US</a></li>
            </ul>
        </nav> 
        <div class="cart-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="22" height="">
                        <path d="M0 24C0 10.7 10.7 0 24 0L69.5 0c22 0 41.5 12.8 50.6 32l411 0c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3l-288.5 0 5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5L488 336c13.3 0 24 10.7 24 24s-10.7 24-24 24l-288.3 0c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5L24 48C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96zM252 160c0 11 9 20 20 20l44 0 0 44c0 11 9 20 20 20s20-9 20-20l0-44 44 0c11 0 20-9 20-20s-9-20-20-20l-44 0 0-44c0-11-9-20-20-20s-20 9-20 20l0 44-44 0c-11 0-20 9-20 20z" />
                    </svg>
                </div>

        <button onclick="goToSignin()"> <img src="./pictures/user.png" alt="User Icon" class="user-icon">
            SIGN IN</button>
    </header>
</header>
</body>
</html>


