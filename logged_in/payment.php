<?php
include 'navigationBar.php';
include 'databaseConnection.php';

include 'applyTheme.php';


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

       
        // PAG KUHA NG RECEIPT
        $src = $_FILES['receipt']['tmp_name'];
        $imageName = uniqid() . $_FILES['receipt']['name'];
        $target = "receipt/" . $imageName;
        move_uploaded_file($src, $target);
        $imageName = mysqli_real_escape_string($conn, $imageName);

          

    // Check if 'payment_mode' is set in the $_POST array
    $paymentMode = isset($_POST['paymentMode']) ? $_POST['paymentMode'] : null;

    // Save order details to the orders table
    $saveOrderQuery = "INSERT INTO orders (customer_id, product_id, order_date, total_amount, order_quantity, status)
        VALUES ($customerId, ?, CURRENT_TIMESTAMP, ?, ?, 'pending')";

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

    $savePaymentQuery = "INSERT INTO payment (order_id, customer_id, payment_mode, amount_paid, status, payment_receipt) VALUES (?, ?, ?, ?, 'pending' , '$imageName')";
    $stmtPayment = mysqli_prepare($conn, $savePaymentQuery);
    
    if ($stmtPayment) {
        mysqli_stmt_bind_param($stmtPayment, "iisd", $orderId, $customerId, $paymentMode, $totalPurchaseValue);
    
        // Assuming $orderId is available in your script
        // Replace it with the actual order ID or adjust your logic accordingly
    
        $orderId = mysqli_insert_id($conn); // Get the last inserted order ID
    
        $resultPayment = mysqli_stmt_execute($stmtPayment);
    
        if ($resultPayment) {
            // echo "Payment details saved successfully.";
        } else {
            echo "Error saving payment details: " . mysqli_error($conn);
        }
    
        mysqli_stmt_close($stmtPayment);
    } else {
        echo "Error preparing payment details statement: " . mysqli_error($conn);
    }



    
    // Fetch selected items from the cart
    $getItemsCartQuery = "SELECT product_id, product_name, quantity, cup_size, add_ons, add_ons_quantities, add_ons_price, custom_flavor, custom_flavor_quantities, custom_price, total_price FROM cart WHERE cart_id IN ($selectedItems) AND customer_id = $customerId";
    $resultItemsCart = mysqli_query($conn, $getItemsCartQuery);
    
    while ($rowCart = mysqli_fetch_assoc($resultItemsCart)) {
        $productId = $rowCart['product_id'];
        $productName = $rowCart['product_name'];
        $quantity = $rowCart['quantity'];
        $selectedCupSize = $rowCart['cup_size'];
        $addOnsNames = $rowCart['add_ons'];
        $addOnsQuantitiesJson = $rowCart['add_ons_quantities'];
        $addOnsPrice = $rowCart['add_ons_price'];

        $customFlavor = $rowCart['custom_flavor'];
        $customQty = $rowCart['custom_flavor_quantities'];
        $customPrice = $rowCart['custom_price'];
        $imageName = mysqli_real_escape_string($conn, $imageName);
        $totalPrice = $rowCart['total_price'];

        
        // INSERT SA queue table
        $insertCartQuery = "INSERT INTO queue (customer_id, product_id, product_name, quantity, cup_size, add_ons, add_ons_quantities, add_ons_price, total_price, custom_flavor, custom_flavor_quantities, custom_price, payment_receipt) 
        VALUES ($customerId, $productId, '$productName', $quantity, '$selectedCupSize', '$addOnsNames', '$addOnsQuantitiesJson', $addOnsPrice, $totalPrice, '$customFlavor', '$customQty', $customPrice, '$imageName')";

        mysqli_query($conn, $insertCartQuery);


        // INSERT SA customer history table
        $insertCartQuery = "INSERT INTO customer_history (customer_id, product_id, product_name, quantity, cup_size, add_ons, add_ons_quantities, add_ons_price ,total_price, custom_flavor, custom_flavor_quantities, custom_price,status) 
        VALUES ($customerId, $productId, '$productName', $quantity, '$selectedCupSize', '$addOnsNames', '$addOnsQuantitiesJson', $addOnsPrice, $totalPrice, '$customFlavor', '$customQty', $customPrice,'pending')";
        mysqli_query($conn, $insertCartQuery);
    }
    echo '<script>alert("Items added to queue successfully!");   window.location.href = "products.php";</script>';

   // Clear cart after successful purchase
   $clearCartQuery = "DELETE FROM cart WHERE cart_id IN ($selectedItems) AND customer_id = $customerId";
   $resultClearCart = mysqli_query($conn, $clearCartQuery);

   if (!$resultClearCart) {
       echo "Error clearing cart: " . mysqli_error($conn);
   }


}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="stylez.css">
    <style>
      
        .customer-information{
            width: 50%;
            border: 2px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            box-sizing: border-box;
            margin-top: 70px;
            margin: 50px auto;
        }

        .customer-information label{
            font-weight: bold;
            margin-bottom: 0;
        }
        .customer-information p,.customer-information select {
            margin-bottom: 10px;
        }
        .payment-container {
            width: 50%;
            border: 2px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            box-sizing: border-box;
            margin: 50px auto;
            margin-top: -30px;
        }

        .payment-form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input {
            padding: 8px;
            font-size: 16px;
        }

        .payment-button {
            margin-top: 20px;
            text-align: right;
        }
        label {
        display: block;
        font-size: 16px;
        margin-bottom: 8px;
        }

        select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .payment-button button{
            font-size:20px;
                background-color: #d38e40; 
                color: white; 
                padding: 7px 20px; 
                border: none; 
                border-radius: 5px;
                cursor: pointer;
                border: 3px solid #d38e40;

                transition: all .5s ease;
        }

        .image-container{
            top: 50%;
            width: 50%;
            border: 2px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            box-sizing: border-box;
            margin: -30px auto;
            display: flex;
            
            justify-content: space-evenly;
        }

        .image-container img{
           width: 400px;
           height: 450px;
           object-fit: cover;

        }
        .receipt{
            font-size:20px;
                color: white; 
                padding: 7px 20px; 
                border: none; 
                border-radius: 5px;
                cursor: pointer;
                transition: all .5s ease;
                position: relative;
                top: 75px;
                left: 450px;
                width: 250px;
        }


     
    </style>
</head>


<body>

    <div class="customer-information" id="customer-information">
        <label for="">Name:</label>
        <p><?php echo $_SESSION['firstName'] ." ". $_SESSION['lastName']; ?> </p>
        <label for="">Contact #:</label>
        <p><?php echo $_SESSION['Phone'] ?></p>
        <label for="">Address:</label>
        <select name="" id="">
            <option value=""><?php echo $_SESSION['Address'] ?><br></option>
            <option value=""><?php echo $_SESSION['Address2'] ?><br></option>
        </select>
    </div>


    <div class="payment-container">
        <form method="post" enctype="multipart/form-data" action="<?php  echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" class="payment-form">
            <div class="form-group">
                <label for="paymentMode">Payment Mode:</label>
                <select name="paymentMode" id="paymentMode" required>
                    <option value="Gcash">GCash</option>
                    <option value="Gcash">BDO Online</option>
                    <option value="Paymaya">PayMaya</option>
                    <option value="Paypal">PayPal</option>
                </select>
            </div>
            
            <div class="receipt">
                <label style="font-weight: bold;" >Payments Receipt:</label>
                <input type="file"  name="receipt" required>
            </div>

            <div class="payment-button">
            <button type="submit">Place Order</button>
            </div>
        </form>

    </div>

    
    <div class="image-container">
        <img src="./pictures/gcash.png" alt="" class="gcash">
        <img src="./pictures/bdo pay.png" alt="" class="bdo">
       </div>
</body>
</html>
    