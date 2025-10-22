<?php
// Start the session
include("databaseConnection.php");
include("navigationBar.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <style>
        .products_frame{
            position: absolute;
            height: 100%;
            width: 40%;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            top: -0px;
        }


        .details_frame {
            position: absolute;
            left: 40%;
            width: 30%;
            height: 400px;
            border: 1px solid #ccc;
            height: 108%;
            top: -85px;
            padding: 50px;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        }

        
        .cart_frame{
            position: absolute;
            height: 100%;
            width: 33%;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            top: -0px;
            left: 70%;
        }
        
    </style>
   
    
</head>

<body>
<iframe src="./products_pos.php" class="products_frame"></iframe>

<iframe name="details_frame" class="details_frame" ></iframe>

<iframe src="./usercart.php" name= "cart_frame" class="cart_frame"></iframe>




</body>

</html>