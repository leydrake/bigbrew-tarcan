<?php
try {
    $servername = "localhost"; // or check hPanel — it might say something like "srv123.main-hosting.eu"
    $username = "u561950571_bigbrew"; // your MySQL username
    $password = "@Zendskiex17"; // the password you set when creating the database user
    $database = "u561950571_bigbrewDatabas"; // your database name

    $conn = mysqli_connect($servername, $username, $password, $database);

    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }
} catch (Exception $e) {
    echo "Could not connect to the database.<br>Error: {$e->getMessage()}";
}
?>
