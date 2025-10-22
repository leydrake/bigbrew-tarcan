<?php
session_start();

// Assuming the session contains the hashed password
if (isset($_POST['password'])) {
    $inputPassword = $_POST['password'];

    // Verify the entered password against the stored hash
    if (password_verify($inputPassword, $_SESSION['Passwords'])) {
        echo "success"; // Password is correct
    } else {
        echo "failure"; // Incorrect password
    }
}
?>
