<?php
include "navigationBar.php";
include "databaseConnection.php";

if (!isset($_SESSION['customer_id'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <body>
        <div class="message-container">
            <img src="images/no.png" alt="Not Logged In">
            <h3>Log In First</h3>
            <a href="signin.php">Click here to log in</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}


$customerId = $_SESSION['customer_id'];

// Group products by time_stamp
$getCartItemsQuery = "SELECT customer_history.status, customer_history.time_stamp,customer_history.custom_flavor, customer_history.queue_id, GROUP_CONCAT(customer_history.cart_id) AS cart_ids, GROUP_CONCAT(product.name) AS product_names,
                      GROUP_CONCAT(product.image) AS product_images, GROUP_CONCAT(customer_history.quantity) AS quantities,
                      GROUP_CONCAT(customer_history.total_price) AS total_prices, GROUP_CONCAT(customer_history.cup_size) AS cup_sizes,
                      GROUP_CONCAT(customer_history.add_ons) AS add_ons
                      FROM customer_history
                      INNER JOIN product ON customer_history.product_id = product.product_id
                      WHERE customer_history.customer_id = $customerId
                      GROUP BY customer_history.time_stamp
                      ORDER BY customer_history.time_stamp ASC";

$result = mysqli_query($conn, $getCartItemsQuery);


if (!$result) {
    echo "Error retrieving queue items: " . mysqli_error($conn);
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
<head>
        <link rel="shortcut icon" href="images/logo.png">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>queue</title>
        <link rel="stylesheet" href="stylez.css">
        <style>
            .cart-container {
                width: 70%;
                margin: 20px auto;
                border: 2px solid #ccc;
                height: 800px;
                border-radius: 10px;
                padding: 20px;
                overflow-y: auto ;
                box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            }

            .cart-item {
                display: flex;
                flex-direction: column;
                margin-bottom: 20px;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 5px;
                box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            }

            .cart-item img {
                width: 80px;
                height: 80px;
                margin-right: 10px;
                object-fit: cover;
            }

            .cart-item-group {
                display: flex;
                align-items: center;
                margin-bottom: 15px;

            }
            .cart-item-details {
                margin: 5px;
                padding: 5px;
            }
            .checkbox-cart {
                width: 20px;
                height: 20px;
                margin-right: 10px;
                cursor: pointer;
            }

            .cart-total {
                text-align: right;
                margin-top: 20px;
            }

            .cart-total button {
                background-color: #d38e40;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }
            h1{
                text-align: center;
                padding: 50px 0px 10px 0px;
            }
        </style>
</head>
    <body>
    <a href="./index.php" style="left: 100px; top: 200px;position:absolute"> <!-- Arrow Link to Homepage -->
        <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 12H4" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 18L4 12L10 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <h1>Transactions</h1>
        <div class="cart-container">
            <form method="post" action="customer_history_process.php" id="cartForm">
                <input type="hidden" name="cartAction" id="cartAction" value="">
                <?php

                    $totalCartValue=0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $timeStamp = $row['time_stamp'];
                        $status = $row['status']; // Fetch the status field
                        $productNames = explode(',', $row['product_names']);
                        $productImages = explode(',', $row['product_images']);
                        $quantities = explode(',', $row['quantities']);
                        $totalPrices = explode(',', $row['total_prices']);
                        $cupSizes = explode(',', $row['cup_sizes']);
                        $addOns = explode(',', $row['add_ons']);
                        $customFlavor = explode(',', $row['custom_flavor']);

                        $groupTotal = array_sum(array_map('floatval', $totalPrices));
                        $totalCartValue += $groupTotal;
                    ?>
                        <div class="cart-item">
                            <div class="cart-item-group">
                                <!-- Add the conditional check for the 'disabled' attribute -->
                                <input 
                                    type="checkbox" 
                                    name="selectedTimestamps[]" 
                                    value="<?php echo $timeStamp; ?>" 
                                    class="checkbox-cart" 
                                    data-status="<?php echo $status; ?>" 
                                    onchange="updateTotal(this)" 
                                    <?php echo ($status === 'cancelled' || $status === 'Order received') ? 'disabled' : ''; ?> 
                                    required
                                >
                                <div>
                                    <p><strong>Order Time:</strong> <?php echo $timeStamp; ?></p>
                                    <p><strong>Status:</strong> <?php echo $status; ?></p>
                                    <?php foreach ($productNames as $index => $name) { ?>
                                        <div class="cart-item-details">
                                            <img src="../img/<?php echo $productImages[$index]; ?>" alt="Product Image">
                                            <p><?php echo $name; ?> (₱<?php echo $totalPrices[$index]; ?>, <?php echo $quantities[$index]; ?>x)</p>
                                            <p>Size: <?php echo isset($cupSizes[$index]) ? $cupSizes[$index] : 'N/A'; ?> | Add ons: <?php echo empty($addOns[$index]) ? "None" : $addOns[$index]; ?></p>
                                            <p>Custom Flavor: <?php echo empty($customFlavor[$index]) ? "None" : $customFlavor[$index]; ?></p>
                                        </div>
                                        <hr style="border:1px solid #ccc; margin-top: 3px; position:relative; width: 1200px;">
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>

                    <div class="cart-total">
                        <button type="submit" name="buySelected" onclick="setAction('buy')">
                            Order received
                        </button>
                        <button type="submit" name="deleteSelected" onclick="setAction('delete')">Cancel Order</button>
                    </div>


            </form>
        </div>

        <script>
            function updateTotal(checkbox) {
                const checkboxes = document.querySelectorAll('.checkbox-cart');
                let total = 0;

                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        const cartItem = cb.closest('.cart-item');
                        const groupTotal = parseFloat(cartItem.querySelector('p strong:last-child').textContent.replace(/[^\d.]/g, ''));
                        total += groupTotal;
                    }
                });

                document.getElementById('totalCartValue').textContent = total.toFixed(2);
            }

            function setAction(action) {
                document.getElementById('cartAction').value = action;
                console.log('Action set:', action);
                document.getElementById('cartForm').submit();
}
        </script>

        <script>            
            function setAction(action) {
                if (action === 'delete') {
                    const checkboxes = document.querySelectorAll('.checkbox-cart:checked');
                    let valid = false;

                    checkboxes.forEach(cb => {
                        if (cb.dataset.status === 'Out for Delivery') {
                            valid = true;
                        }
                    });

                    if (valid) {
                        alert("You can't cancel orders that are 'Out for delivery'.");
                        event.preventDefault();
                        return;
                    }

                    const confirmDelete = confirm("Are you sure you want to remove the selected items from the cart?");
                    if (!confirmDelete) {
                        alert("Cancelled");
                        event.preventDefault();
                        return;
                    }
                }

                // Set the action and submit the form
                document.getElementById('cartAction').value = action;
                document.getElementById('cartForm').submit();
            }


        </script>
    </body>
    </html>
    <?php
}

mysqli_close($conn);
?>