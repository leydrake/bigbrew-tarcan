<?php
include 'navigationBar.php';
include 'databaseConnection.php';



if (!isset($_SESSION['customer_id'])) {
    echo "User not logged in. Please log in first.";
    exit();
}

$customerId = $_SESSION['customer_id'];

if (!isset($_SESSION['selectedItems'])) {
    echo "No items selected for purchase.";
    exit();
}

$selectedItems = $_SESSION['selectedItems'];

$getSelectedItemsQuery = "SELECT product.product_id, product.name, product.price, cart.quantity, cart.cup_size, cart.add_ons, cart.total_price, cart.add_ons_quantities
    FROM cart
    INNER JOIN product ON cart.product_id = product.product_id
    WHERE cart.customer_id = $customerId
    AND cart.cart_id IN ($selectedItems)";

$result = mysqli_query($conn, $getSelectedItemsQuery);

if (!$result) {
    echo "Error retrieving selected items: " . mysqli_error($conn);
    exit();
}

// Calculate total purchase value
$totalPurchaseValue = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $totalPurchaseValue += $row['quantity'] * $row['total_price'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if 'payment_mode' is set in the $_POST array
    $paymentMode = isset($_POST['paymentMode']) ? $_POST['paymentMode'] : null;

    // Save order details to the orders table
    $saveOrderQuery = "INSERT INTO orders (customer_id, product_id, order_date, total_amount, order_quantity, status)
        VALUES ($customerId, ?, CURRENT_TIMESTAMP, ?, ?, 'completed')";

    $stmt = mysqli_prepare($conn, $saveOrderQuery);

    if ($stmt) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "idd", $productId, $totalAmount, $orderQuantity);

        // Insert each selected item as a separate order
        mysqli_data_seek($result, 0); // Reset result pointer
        while ($row = mysqli_fetch_assoc($result)) {
            $productId = $row['product_id'];
            $totalAmount = $row['quantity'] * $row['total_price'];
            $orderQuantity = $row['quantity'];

            mysqli_stmt_execute($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing order query: " . mysqli_error($conn);
    }

    // Save payment mode to the payment table
    $paymentMode = $_POST['paymentMode']; // Assuming you get the payment mode from a form

    $savePaymentQuery = "INSERT INTO payment (order_id, customer_id, payment_mode, amount_paid, status) VALUES (?, ?, ?, ?, 'completed')";
    $stmtPayment = mysqli_prepare($conn, $savePaymentQuery);
    
    if ($stmtPayment) {
        mysqli_stmt_bind_param($stmtPayment, "iisd", $orderId, $customerId, $paymentMode, $totalPurchaseValue);
    
        // Assuming $orderId is available in your script
        // Replace it with the actual order ID or adjust your logic accordingly
    
        $orderId = mysqli_insert_id($conn); // Get the last inserted order ID
    
        $resultPayment = mysqli_stmt_execute($stmtPayment);
    
        if ($resultPayment) {
            echo "Payment details saved successfully.";
        } else {
            echo "Error saving payment details: " . mysqli_error($conn);
        }
    
        mysqli_stmt_close($stmtPayment);
    } else {
        echo "Error preparing payment details statement: " . mysqli_error($conn);
    }
}
    


    // Fetch selected items
    mysqli_data_seek($result, 0); // Reset result pointer
    while ($row = mysqli_fetch_assoc($result)) {
        $productId = $row['product_id'];
        $quantity = $row['quantity'];
        $addOnsNames = $row['add_ons'];
        $addOnsQuantities = json_decode($row['add_ons_quantities'], true); // Decode add-ons quantities

        // Deduct product quantity from the product table
        $deductProductQuantityQuery = "UPDATE product SET qty = qty - $quantity WHERE product_id = $productId";
        $resultDeductProductQuantity = mysqli_query($conn, $deductProductQuantityQuery);
        if (!$resultDeductProductQuantity) {
            echo "Error deducting product quantity: " . mysqli_error($conn);
        }

        // Process each add-on quantity and deduct from the invent_addson table
        if (is_array($addOnsQuantities)) {
            foreach ($addOnsQuantities as $addOnName => $addOnQuantity) {
                // Deduct add-on quantity from invent_addson
                $deductAddOnQuantityQuery = "UPDATE invent_addson SET qty = qty - ? WHERE name = ?";
                $stmtDeductAddOn = mysqli_prepare($conn, $deductAddOnQuantityQuery);

                if ($stmtDeductAddOn) {
                    mysqli_stmt_bind_param($stmtDeductAddOn, "is", $addOnQuantity, $addOnName);
                    $executionResult = mysqli_stmt_execute($stmtDeductAddOn);

                    if (!$executionResult) {
                        echo "Error deducting quantity for add-on '$addOnName': " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($stmtDeductAddOn);
                } else {
                    echo "Error preparing query for add-on '$addOnName': " . mysqli_error($conn);
                }
            }
        } else {
            echo "Invalid add-on quantities format.";
        }
    }

    // Clear cart after successful purchase
    // Fetch selected cup sizes from the cart
        $fetchCupSizesQuery = "SELECT cup_size, quantity FROM cart WHERE cart_id IN ($selectedItems)";
        $resultFetchCupSizes = mysqli_query($conn, $fetchCupSizesQuery);

if ($resultFetchCupSizes) {
    while ($cupSizeRow = mysqli_fetch_assoc($resultFetchCupSizes)) {
        $cupSize = $cupSizeRow['cup_size'];
        $quantity = $cupSizeRow['quantity'];

        // Deduct quantity from invent_cupsize table based on the cup size
        $deductCupSizeQuantityQuery = "UPDATE invent_cupsize SET qty = qty - ? WHERE name = ?";
        $stmtDeductCupSize = mysqli_prepare($conn, $deductCupSizeQuantityQuery);

        if ($stmtDeductCupSize) {
            mysqli_stmt_bind_param($stmtDeductCupSize, "is", $quantity, $cupSize);
            $executionResult = mysqli_stmt_execute($stmtDeductCupSize);

            if (!$executionResult) {
                echo "Error deducting quantity for cup size '$cupSize': " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmtDeductCupSize);
        } else {
            echo "Error preparing query for cup size '$cupSize': " . mysqli_error($conn);
        }
    }
} else {
    echo "Error fetching cup sizes: " . mysqli_error($conn);
}
   // Clear cart after successful purchase
   $clearCartQuery = "DELETE FROM cart WHERE cart_id IN ($selectedItems) AND customer_id = $customerId";
   $resultClearCart = mysqli_query($conn, $clearCartQuery);

   if (!$resultClearCart) {
       echo "Error clearing cart: " . mysqli_error($conn);
   }


    // Redirect or display success message
    echo '<script>alert("Payment Successful!"); window.location.href = "./products.php"; </script>';



mysqli_close($conn);
?>
