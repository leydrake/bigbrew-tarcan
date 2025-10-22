<?php

    try{
        $conn = mysqli_connect("localhost", "root", '', "coffeeshopdb");
    } catch(Exception $e){
        echo "Could not connect to the database. <br>
        Error: {$e}";
    }
   
?>