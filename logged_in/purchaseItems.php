<?php
include 'navigationBar.php';
include 'databaseConnection.php';
include 'applyTheme.php';

if (!isset($_SESSION['customer_id'])) {
    echo "User not logged in. Please log in first.";
    exit();
}

$customerId = $_SESSION['customer_id'];

// Check if the selected items are provided in the session
if (!isset($_SESSION['selectedItems'])) {
    echo "No items selected for purchase.";
    exit();
}

$selectedItems = $_SESSION['selectedItems'];

// Retrieve detailed information about the selected items
$getSelectedItemsQuery = "SELECT product.product_id, product.name, product.image, cart.quantity, product.price, cart.cup_size, cart.add_ons, cart.total_price,cart.custom_flavor,cart.custom_flavor_quantities
                     FROM cart
                     INNER JOIN product ON cart.product_id = product.product_id
                     WHERE cart.customer_id = $customerId
                     AND cart.cart_id IN ($selectedItems)";

$result = mysqli_query($conn, $getSelectedItemsQuery);

if (!$result) {
    echo "Error retrieving selected items: " . mysqli_error($conn);
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    
<link rel="shortcut icon" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="stylez.css">
    <link rel="stylesheet" href="istitik.css">
    <style>


        .checkout-container {
            width: 70%;
            border: 2px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            box-sizing: border-box;
            margin-top: 20px;
            margin: 0 auto;
        }

        .item-summary {
                display: flex;
                align-items: center;
                margin-bottom: 15px;
                border: 1px solid #ddd; /* Add border to each cart item */
                padding: 10px;
        }
        .order-details{
            display: block;
        }

        .item-image {
            width: 100px;
            height: 100px;
            margin-right: 10px;
            object-fit: cover;
        }

        .checkout-button {
            margin-top: 20px;
            text-align: right;
        }
        .checkout-button button{
                font-size:20px;
                background-color: #d38e40; 
                color: white; 
                padding: 9px 20px; 
                border: none; 
                border-radius: 5px;
                cursor: pointer;
                border: 3px solid #d38e40;

                transition: all .5s ease;

            }
         
            .checkout-button p{
                font-size: 20px;
                font-weight: bold;
            }
    </style>
</head>
<body>
    


    <h1>Checkout</h1>

    <div class="checkout-container" style="background-color:white">
        <?php
        // Initialize total variable
        $totalPurchaseValue = 0;

        // Loop through selected items and display each item
        while ($row = mysqli_fetch_assoc($result)) {
            $totalPrice = $row['quantity'] * $row['total_price'];
            $totalPurchaseValue += $totalPrice;
            $_SESSION['total_purchase'] = $totalPurchaseValue;
            ?>
            <div class="item-summary">
                <img src="../img/<?php echo $row['image']; ?>" alt="Product Image" class="item-image">

                <div class="order-details">
                    <p> <?php echo $row['name']; ?></p>
                    <p> Quantity: <?php echo $row['quantity']; ?></p>
                    <p>Total Price: ₱<?php echo $totalPrice; ?></p>

                    <div class="add-ons-details">
                    <p>Add ons: <?php if($row['add_ons'] == "") echo "None"; 
                        else
                            echo $row['add_ons']; ?></p>
                    </div>  
                    
                    <!-- Custom Flavor -->
                    <div class="custom-flavor-details">
                                <p>Custom Flavor: <?php echo (isset($row['custom_flavor']) && $row['custom_flavor'] != "") ? $row['custom_flavor'] : "None"; ?></p>
                            </div>  

                </div>
            </div>

          
        <?php
        }
        ?>
        <div class="checkout-button">
        
         
        <a
            <?php if ($_SESSION['customer_id'] == 1){
            echo "href= pos_payment.php";
            echo " target='cart_frame'";
        }else{
            echo " href=payment.php";
        }?>
        
        ><button>Proceed To Payment</button>
        </a>
            
            <p>Total Purchase Value: ₱<?php echo $totalPurchaseValue; ?></p>
        </div>
    </div>


</body>
</html>




