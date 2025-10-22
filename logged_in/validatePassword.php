<!-- filepath: /c:/xampp/htdocs/finalProject/logged_in/validatePassword.php -->
<?php
include("databaseConnection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];

    // Fetch the hashed password from the database
    $query = "SELECT Passwords FROM login_credentials WHERE emailAddress = 'dranskyxd@gmail.com'"; // Adjust the query as needed
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row['Passwords'];

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            echo 'success';
        } else {
            echo 'failure';
        }
    } else {
        echo 'failure';
    }
}

mysqli_close($conn);
?>