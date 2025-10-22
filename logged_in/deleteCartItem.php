<?php

// Database connection
include 'databaseConnection.php';

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "User not logged in. Please log in first.";
    exit();
}

// Check if the delete button is clicked
if (isset($_POST['deleteSelected'])) {
    $customerId = $_SESSION['customer_id'];

    // Delete selected items from the cart
    if (!empty($_POST['selectedItems'])) {
        $selectedItems = implode(',', $_POST['selectedItems']);
        $deleteItemsQuery = "DELETE FROM cart WHERE cart_id IN ($selectedItems) AND customer_id = $customerId";
        $result = mysqli_query($conn, $deleteItemsQuery);

        if (!$result) {
            echo "Error deleting items: " . mysqli_error($con);
        } else {
          
            echo '<script>alert("Selected items deleted successfully");window.location.href = "usercart.php";</script>';
        }
    } else {

        echo '<script>alert("No items selected to delete.");window.location.href = "usercart.php";</script>';
    }
}


?>

