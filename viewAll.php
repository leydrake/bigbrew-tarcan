<?php
    include("databaseConnection.php");
    $result = mysqli_query($conn, "SELECT * FROM login_credentials");
    $row = mysqli_fetch_assoc($result);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        table,tr,td,th{
            border: collapse;
            padding: 10px;
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th>ID</th>
            <th>First name</th>
            <th>Last name</th>
            <th>Email address name</th>
            <th>Password</th>
            <th>Registration date</th>
            <th>Avatar</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)){ ?> <tr>

            <?php
            $temp1 = $row['id'];
            $temp2 = $row['firstName'];
            $temp3 = $row['lastName'];
            $temp4 = $row['emailAddress'];
            $temp5 = $row['Passwords'];
            $temp6 = $row['registrationDate'];
            $temp7 = $row['avatar'];
            ?>
            <td> <?php echo $temp1 ?></td>
            <td> <?php echo $temp2 ?></td>
            <td> <?php echo  $temp3 ?></td>
            <td> <?php echo $temp4?></td>
            <td> <?php echo $temp5?></td>
            <td> <?php echo $temp6?></td>
            <td> <?php echo $temp7?></td>
        </tr> <?php } ?>

    </table>
</body>
</html>