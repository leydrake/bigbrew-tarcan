<?php
include "navigationBar.php";
include "databaseConnection.php";
include 'isAdmin.php';


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
$getCartItemsQuery = "SELECT queue.payment_receipt, queue.cart_id, queue.time_stamp,queue.custom_flavor, queue.queue_id, GROUP_CONCAT(queue.cart_id) AS cart_ids, GROUP_CONCAT(product.name) AS product_names,
                      GROUP_CONCAT(product.image) AS product_images, GROUP_CONCAT(queue.quantity) AS quantities,
                      GROUP_CONCAT(queue.total_price) AS total_prices, GROUP_CONCAT(queue.cup_size) AS cup_sizes,
                      GROUP_CONCAT(queue.add_ons) AS add_ons
                      FROM queue
                      INNER JOIN product ON queue.product_id = product.product_id
                    --   WHERE queue.customer_id = $customerId
                      GROUP BY queue.time_stamp
                      ORDER BY queue.time_stamp ASC";

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
            .receipt{
                position: relative;
                left: 90%;
                margin-top: -500px;
                margin-bottom: 50px;
                border: 1px solid black;
                height: 100px !important;
                cursor: pointer;

            }

            /*FOR RECEIPT  */
             /* Modal Style */
                .modal {
                    display: none; /* Hidden by default */
                    position: fixed;
                    z-index: 1; /* Sit on top */
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.8); /* Black background with opacity */
                    overflow: auto;
                    padding-top: 60px;
                    margin-top: 125px;
                }

                /* Modal Content (Image) */
                .modal-content {
                    margin: auto;
                    display: block;
                    max-width: 80%;
                    max-height: 80%;
                }

                /* Caption (Image Description) */
                #caption {
                    text-align: center;
                    color: #ccc;
                    font-size: 20px;
                    padding: 10px;
                }

                /* Close Button */
                .close {
                    color: #ccc;
                    font-size: 40px;
                    font-weight: bold;
                    position: absolute;
                    top: 10px;
                    right: 25px;
                    color: #fff;
                    cursor: pointer;
                }

                .close:hover,
                .close:focus {
                    color: #f1f1f1;
                    text-decoration: none;
                    cursor: pointer;
                }
        </style>
    </head>
    <body>

        <!-- Add Modal Structure -->
        <div id="receiptModal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <img class="modal-content" id="modalImg">
            <div id="caption"></div>
        </div>

    <a href="admin_settings.php" style="left: 100px; top: 200px;position:absolute"> <!-- Arrow Link to Homepage -->
        <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 12H4" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 18L4 12L10 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <h1>Queue</h1>

        <div class="cart-container">
            <form method="post" action="complete_process_order.php" id="cartForm">
                <input type="hidden" name="cartAction" id="cartAction" value="">
                <?php
                $totalCartValue = 0;

                while ($row = mysqli_fetch_assoc($result)) {

                    $queueID = $row['cart_id'];
                    $timeStamp = $row['time_stamp'];
                    $productNames = explode(',', $row['product_names']);
                    $productImages = explode(',', $row['product_images']);
                    $quantities = explode(',', $row['quantities']);
                    $totalPrices = explode(',', $row['total_prices']);
                    $cupSizes = explode(',', $row['cup_sizes']);
                    
                    $addOns = explode(',', $row['add_ons']);
                    $customFlavor = explode(',', $row['custom_flavor']);
                    $receipt = $row['payment_receipt'];
                    $groupTotal = array_sum(array_map('floatval', $totalPrices));
                    $totalCartValue += $groupTotal;
                    ?>
                    <div class="cart-item">
                        <div class="cart-item-group">
                            <input type="checkbox" name="selectedTimestamps[]" value="<?php echo $timeStamp; ?>" onchange="updateTotal(this)" class="checkbox-cart" required>
                            <div>

                                <p><strong>Order ID:</strong> <?php echo $queueID; ?></p>
                                <p><strong>Order Time:</strong> <?php echo $timeStamp; ?></p>
                                <?php foreach ($productNames as $index => $name) { ?>
                                    <div class="cart-item-details">
                                        <img src="../img/<?php echo $productImages[$index]; ?>" alt="Product Image">
                                        <p><?php echo $name; ?> (₱<?php echo $totalPrices[$index]; ?>, <?php echo $quantities[$index]; ?>x)</p>
                                        <p>Size: <?php echo isset($cupSizes[$index]) ? $cupSizes[$index] : 'N/A'; ?> | Add ons: <?php if(isset($row['add_ons']) && $row['add_ons'] == "") echo "None"; else echo $row['add_ons']; ?></p>
                                        <p>Custom Flavor: <?php echo (isset($row['custom_flavor']) && $row['custom_flavor'] != "") ? $row['custom_flavor'] : "None"; ?></p>
                                        <img class="receipt" src="receipt/<?php echo $row['payment_receipt']; ?>" alt="" onclick="openModal(this.src)">
                                        

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
                    <button type="submit" name="buySelected" onclick="setAction('buy')">Order Complete</button>
                    <button type="submit" name="deleteSelected" onclick="setAction('delete')">Cancel Order</button>
                </div>
            </form>
        </div>

        <script>
            // Function to open the modal
            function openModal(imageSrc) {
                const modal = document.getElementById("receiptModal");
                const modalImg = document.getElementById("modalImg");
                const caption = document.getElementById("caption");

                modal.style.display = "block";
                modalImg.src = imageSrc;  // Set the image source of the modal
                caption.innerHTML = "Receipt Image";  // Optional, can display any description

                // Close the modal when clicking on the close button
                const span = document.getElementsByClassName("close")[0];
                span.onclick = function () {
                    modal.style.display = "none";
                }
            }

            // Function to close the modal
            function closeModal() {
                const modal = document.getElementById("receiptModal");
                modal.style.display = "none";
            }

        </script>

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