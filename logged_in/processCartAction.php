<?php
session_start();
include 'databaseConnection.php';

if (isset($_POST['cartAction'])) {
    $customerId = $_SESSION['customer_id'];

    switch ($_POST['cartAction']) {
        case 'delete':
            include 'deleteCartItem.php';
            break;
        case 'buy':
            // Set the selected items in the session
            if (!empty($_POST['selectedItems']) && is_array($_POST['selectedItems'])) {
            // Perform stock check before proceeding with the purchase
            $selectedItems = $_POST['selectedItems'];
            $outOfStockItems = checkStockAvailability($customerId, $selectedItems);

            if (!empty($outOfStockItems)) {
                $outOfStockItemsString = implode(', ', $outOfStockItems);
                echo '<script>alert("Sorry, the following items are out of stock: ' . $outOfStockItemsString . '");window.location.href = "usercart.php";</script>';
                exit();
            }

            // No out-of-stock items, proceed with the purchase
            $_SESSION['selectedItems'] = implode(',', $selectedItems);
            header('Location: purchaseItems.php');
            exit();
        } else {
            echo '<script>alert("No items selected for purchase.");window.location.href = "usercart.php";</script>';
            exit();
        }
        break;
}
}

function checkStockAvailability($customerId, $selectedItems) {
    // Perform stock check for the selected items
    $outOfStockItems = [];

    // Connect to your database (replace these credentials with your own)

    
    $con = mysqli_connect("localhost", "root", "", "shopin", 3306);

    if (!$con) {
        die("Could not connect: " . mysqli_connect_error());
    }

    foreach ($selectedItems as $cartId) {
        // Fetch product details and check stock availability
        $checkStockQuery = "SELECT products.product_name, cart.quantity, products.quantity AS availableQuantity
                            FROM cart
                            INNER JOIN products ON cart.product_id = products.product_id
                            WHERE cart.customer_id = $customerId AND cart.cart_id = $cartId";

        $result = mysqli_query($con, $checkStockQuery);

        if ($result) {
            $row = mysqli_fetch_assoc($result);

            // Check if the requested quantity exceeds available quantity
            if ($row['quantity'] > $row['availableQuantity']) {
                // If so, add the product name to the $outOfStockItems array
                $outOfStockItems[] = $row['product_name'];
            }
        } else {
            // Handle query error
            echo "Error executing query: " . mysqli_error($con);
        }
    }

    // Close the database connection
    mysqli_close($con);

    return $outOfStockItems;
}



?>