<?php

$themeResult = mysqli_query($conn, "SELECT * FROM theme_table");
    $themeRow = mysqli_fetch_assoc($themeResult);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
     body{
            background-color: <?php echo $themeRow['backgroundColor'] ?>;
        }
        p{
            color: <?php echo $themeRow['fontColor'] ?> !important;
        }
        .header-font{
            color: <?php echo $themeRow['fontHeader'] ?> !important ;

        }
        button{
            background-color: <?php echo $themeRow['buttonColor'] ?> ;
        }
 </style>
</head>
<body>
    
</body>
</html>