<?php
include "navigationBar.php";
include "databaseConnection.php";
include 'applyTheme.php';


// Database connection


// Check if the user is logged in
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

// Retrieve cart items for the logged-in user
$getCartItemsQuery = "SELECT cart.cart_id, product.product_id, product.name, product.image, cart.quantity, product.price, cart.cup_size, cart.add_ons, cart.total_price,cart.custom_flavor,cart.custom_flavor_quantities
                     FROM cart
                     INNER JOIN product ON cart.product_id = product.product_id
                     WHERE cart.customer_id = $customerId";

$result = mysqli_query($conn, $getCartItemsQuery);

if (!$result) {
    echo "Error retrieving cart items: " . mysqli_error($conn);
} else {
    ?>
     <!DOCTYPE html>
    <html lang="en">
    <head>
<link rel="shortcut icon" href="images/logo.png">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Cart</title>
        <link rel="stylesheet" href="stylez.css">
        <style>


            .cart-container {
                width: 70%;
                border: 2px solid #ccc;
                border-radius: 10px;
                padding: 20px;
                box-sizing: border-box;
                margin-top: 20px;
                margin: 0 auto;
            }
            .checkbox-cart{
                width: 30px;
                height: 30px;
                margin-right: 10px;
                cursor: pointer;

            }
            .checkbox-cart input:checked {
                background-color: #d38e40;
            }


            .cart-item img {
                width: 100px;
                height: 100px;
                margin-right: 10px;
            }

            .cart-item {
                display: flex;
                align-items: center;
                margin-bottom: 15px;
                border: 1px solid #ddd; /* Add border to each cart item */
                padding: 10px;
            }

            .cart-item-form {
                width: 100%;
                display: flex;
                align-items: center;
            }

            .cart-item-image {
                width: 80px;
                height: 80px;
                margin-right: 10px;
                object-fit: cover;
                aspect-ratio: 16/9;
            }

            .cart-item-details {
                flex-grow: 1;
            }

            .delete-button,
            .buy-button {
                margin-left: auto; /* Move the buttons to the right */
                margin-top: 10px;
            }

            .cart-total {
                margin-top: 20px;
                text-align: right;
            }
            .cart-total button{
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
        

        </style>
    </head>
    <body>
        
   

        <h1 style="text-align: center;"><?php if($_SESSION['customer_id'] == 1){
            echo "Order List";
        }else {
            echo "Your Cart";
        }?>
        </h1>



        <div class="cart-container">
            <form method="post" action="processCartAction.php" id="cartForm">
   		         <input type="hidden" name="cartAction" id="cartAction" value="">
                <?php
                // Initialize total variable
                $totalCartValue = 0;

                // Loop through cart items and display each item
                $index = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $totalPrice = $row['quantity'] * $row['total_price'];
                    $totalCartValue += $totalPrice;
                    
                    ?>
              <div class="cart-item">
                <input type="checkbox" name="selectedItems[]" value="<?php echo $row['cart_id']; ?>" onchange="updateTotal(this)" class="checkbox-cart">
               
                        

                <img src="../img/<?php echo $row['image']; ?>" alt="Product Image" class="cart-item-image">
                        <div class="cart-item-details">
                            <p><?php echo $row['name']; ?></p>
                            <p class="price-per-unit" style="display: none;"><?php echo $row['total_price']; ?></p>
                            <label for="quantity_<?php echo $row['cart_id']; ?>">Quantity:</label>
                            <input style="width: 50px;height:30px;font-size:25px;" type="number" name="quantity_<?php echo $row['cart_id']; ?>" value="<?php echo $row['quantity']; ?>" min="1" onchange="updateQuantity(this)">
                            <p class="total-price">Total Price: ₱<?php echo $totalPrice; ?></p>

                            <div class="cup-size">
                                <p>Size: <?php echo $row['cup_size']; ?></p>
                            </div>
                            <div class="add-ons-details">
                                <p>Add ons: <?php if(isset($row['add_ons']) && $row['add_ons'] == "") echo "None"; 
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
                $index++;
                }
                ?>
                <div class="cart-total">
                <button type="submit" name="deleteSelected" onclick="setAction('delete')">Remove From Cart</button>
                <button type="submit" name="buySelected" onclick="setAction('buy')">Check Out</button>



<p style="font-weight: bold; font-size: larger; color: #333;">Total Selected Item Price: <span id="totalCartValue" style="font-weight: bold; font-size: larger; color: #d38e40;;"><?php echo $totalCartValue; ?>₱</span></p>

                </div>
            </form>
        </div>

        <script>

            
function updateTotal() {
    var checkboxes = document.getElementsByName('selectedItems[]');
    var total = 0;

    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            var cartItem = checkboxes[i].closest('.cart-item');
            var totalPriceElement = cartItem.querySelector('.total-price');
            var quantityInput = cartItem.querySelector('input[type="number"]');
            
            // Get the price per unit from .price-per-unit
            var pricePerUnit = parseFloat(cartItem.querySelector('.price-per-unit').textContent.replace(/[^\d.]/g, ''));

            var newTotalPrice = quantityInput.value * pricePerUnit;
            totalPriceElement.textContent = 'Total Price: ₱' + newTotalPrice.toFixed(2);

            total += newTotalPrice;
        }
    }

    document.getElementById('totalCartValue').textContent = total.toFixed(2);
}



function updateQuantity(input) {
    var cartItem = input.closest('.cart-item');
    var totalPriceElement = cartItem.querySelector('.total-price');
    var quantity = input.value;
    var cartId = input.name.replace('quantity_', ''); // Extract cart_id from input name

    // Get the price per unit from .price-per-unit
    var pricePerUnit = parseFloat(cartItem.querySelector('.price-per-unit').textContent.replace(/[^\d.]/g, ''));

    var newTotalPrice = quantity * pricePerUnit;
    totalPriceElement.textContent = 'Total Price: ₱' + newTotalPrice.toFixed(2);

    // Update quantity in the database using AJAX
    updateQuantityInDatabase(cartId, quantity);

    updateTotal();
}

function updateQuantityInDatabase(cartId, quantity) {
    // Make an AJAX request to update quantity in the database
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'updateQuantity.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Handle the response if needed
            console.log(xhr.responseText);
        }
    };
    xhr.send('cartId=' + cartId + '&quantity=' + quantity);
}



function setAction(action) {
    if (action === 'delete') {
        // Display a confirmation box before proceeding with deletion
        var confirmDelete = confirm("Are you sure you want to remove the selected items from the cart?");
        
        // If the user cancels the confirmation, redirect back to usercart.php
        if (!confirmDelete) {
            alert("Cancelled");
            window.location.href = "usercart.php";
            event.preventDefault();
            return;
   
          
        }
        
    }

    // Set the action and submit the form
    document.getElementById('cartAction').value = action;
    var form = document.getElementById('cartForm');
    form.submit();
    return true; // Allow form submission
}




        </script>
    </body>
    </html>
    <?php
}

mysqli_close($conn);
?>
