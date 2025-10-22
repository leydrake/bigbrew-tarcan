<?php
    include("databaseConnection.php");
    include("navigationBar.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href = "./about.css">
</head>
<script>
    function goRegister(){
        window.location.href ="./register.php";
    }
</script>
<body>

    <center>
        <div class="content-container">
             <div class="header-container">
                <img src="./pictures/Untitled design (6).png" alt="">
                <div class="text-holder">
                    <p>BIG IN TASTE,</BR>BIT IN PRICE.</p>
                </div>
            </div>
           

            <div class="company-section">

                <img src="./pictures/WALANG PASOK (1).png"alt="" class="company-image">
                <h1>COMPANY PROFILE</h1>
                <p>
                    Bigbrew   launched  in 2019,   is   a   Filipino-founded   company   committed   todelivering high-quality, budget-friendly beverages such as coffee, tea, and snacks.Big brew created milk tea which can be considered as a snack as well as beverage.Milk   tea  is  an  iced   tea  with   tapioca   balls  at   the  bottom  that   is  pleasant   andflavorful.
                <br>
                <br>
                    Bigbrew still expanding aside from their main location in Maypajo, Caloocanthey also serve other parts of Valenzuela and other areas of Caloocan, as well asother parts of Manila and other areas of the country. The strategic acquisition of
                     Bigbrew   ensures   customer   satisfaction   with   its   fast,   courteous   service   andwelcoming, spotless environment.
                </p>
                <div class="opacity2"></div>
                <div class="franchise">
                    <img src="./pictures/facebook logo.png" alt="">
                    <p>FOR INQUIRIES <br>CONTACT US:</p>
                </div>
                <a href="https://www.facebook.com/bigbrew.franchise" target="_blank"><button>CONTACT US</button></a>
            </div>

            <!-- milktea section -->

            <div class="milktea-section">
                <img src="./pictures/hot or cold.jpg" alt="" class="milktea-image">
                <br><br>
                <h1>What's behind our success?</h1>
                <p>   
                Bigbrew's   success can be   attributed to its ability  to adapt its  menu  andofferings to meet local tastes while maintaining core Filipino flavors. This strategyhas   been   really   important   for   us   to   get   into   new   markets   and   reach   out   todifferent types of customers 
                <br>
                <br>
                Bigbrew is facing the difficulties of growing competition and locating a suitablelocation that is conducive to the sale of their product. To address these issues,Bigbrew   has   franchised   in   various   regions   in   order   to   better   delineate   theirbusiness. Additionally, they are continuing to introduce new flavors.
               </p>

            </div>

            <div class="multi-pictures">
                 <img src="./pictures/red velvet.jpg" alt="" class="multi1">
                 <img src="./pictures/choco berry.jpg" alt="" class="multi2">
                 <img src="./pictures/straberry milktea.jpg" alt="" class="multi3">
            </div>
        </div>
    </center>
    
</body>
</html>

