    <?php
    include 'navigationBar.php';
    include 'databaseConnection.php';
    include 'isAdmin.php';


    if (!isset($_SESSION['customer_id'])) {
        echo "User not logged in. Please log in first.";
        exit();
    }

 
    $cartAction = $_POST['cartAction'] ?? '';

    $customerId = $_SESSION['customer_id'];

    // if (!isset($_SESSION['selectedItems'])) {
    //     echo "No items selected for purchase.";
    //     exit();
    // }

    if(!isset($_POST['selectedTimestamps'])){
        echo "No items selected for purchase.";
            exit();
    }
    $timestamps = $_POST['selectedTimestamps'];
        
    $placeholders = implode(',', array_fill(0, count($timestamps), '?'));

    // $selectedItems = $_SESSION['selectedItems'];


    if($cartAction=='buy'){

            // Calculate total purchase value
            $totalPurchaseValue = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $totalPurchaseValue += $row['quantity'] * $row['total_price'];
            }
            // COMPLETED ORDER IN ORDERS TABLE
            if (isset($_POST['selectedTimestamps'])) {
                $selectedTimestamps = $_POST['selectedTimestamps'];

                // $status = "";
                // if ($_SESSION['customer_id'] == 1){
                //     $status = "completed";
                // }else{
                //     $status = "pending";
                // }
        
                // Loop through each selected timestamp
                foreach ($selectedTimestamps as $timestamp) {
                    // Update order status to 'completed' for the given time_stamp
                    $saveOrderQuery = "UPDATE orders SET status = 'completed' WHERE order_date = ?";
                    $stmt = mysqli_prepare($conn, $saveOrderQuery);
        
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "s", $timestamp); // Bind the time_stamp
                        $resultUpdateOrder = mysqli_stmt_execute($stmt);
        
                        if ($resultUpdateOrder) {
                            // echo "Order with time_stamp $timestamp status updated to 'completed'.<br>";
                        } else {
                            echo "Error updating order with time_stamp $timestamp: " . mysqli_stmt_error($stmt) . "<br>";
                        }
        
                        mysqli_stmt_close($stmt);
                    } else {
                        echo "Error preparing query for time_stamp $timestamp: " . mysqli_error($conn) . "<br>";
                    }
                    
                    // Update order status to 'completed' for the given time_stamp
                    $savePaymentQuery = "UPDATE payment SET status = 'completed' WHERE payment_date = ?";
                    $stmtPay = mysqli_prepare($conn, $savePaymentQuery);
        
                    if ($stmtPay) {
                        mysqli_stmt_bind_param($stmtPay, "s", $timestamp); // Bind the time_stamp
                        $resultUpdatePayment = mysqli_stmt_execute($stmtPay);
        
                        if ($resultUpdatePayment) {
                            // echo "Order with time_stamp $timestamp status updated to 'completed'.<br>";
                        } else {
                            echo "Error updating order with time_stamp $timestamp: " . mysqli_stmt_error($stmtPay) . "<br>";
                        }
        
                        mysqli_stmt_close($stmtPay);
                    } else {
                        echo "Error preparing query for time_stamp $timestamp: " . mysqli_error($conn) . "<br>";
                    }

                      // Update customer history status
                      $deliveryQuery = "UPDATE customer_history SET status = 'Out for Delivery' WHERE time_stamp = ?";
                      $stmtPay = mysqli_prepare($conn, $deliveryQuery);
      
                      if ($stmtPay) {
                      mysqli_stmt_bind_param($stmtPay, "s", $timestamp); // Bind the time_stamp
                      $resultUpdatePayment = mysqli_stmt_execute($stmtPay);
      
                      if ($resultUpdatePayment) {
                          // echo "Order with time_stamp $timestamp status updated to 'completed'.<br>";
                      } else {
                          echo "Error updating order with time_stamp $timestamp: " . mysqli_stmt_error($stmtPay) . "<br>";
                      }
      
                      mysqli_stmt_close($stmtPay);
                  } else {
                      echo "Error preparing query for time_stamp $timestamp: " . mysqli_error($conn) . "<br>";
                  }
        
        
                    $getSelectedItemsQuery = "SELECT product.product_id, product.name, product.price, queue.quantity, queue.cup_size, queue.add_ons, queue.total_price, queue.add_ons_quantities,
                     queue.custom_flavor_quantities
                    FROM queue
                    INNER JOIN product ON queue.product_id = product.product_id
                    WHERE queue.time_stamp = ?";
                    $stmt = mysqli_prepare($conn, $getSelectedItemsQuery);
                
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $timestamp); // "s" means string
                    $result = mysqli_stmt_execute($stmt);
                
                    if ($result) {
                        $result = mysqli_stmt_get_result($stmt);
                    } else {
                        echo "Error executing query: " . mysqli_stmt_error($stmt);
                    }
                
                    mysqli_stmt_close($stmt);
                } else {
                    echo "Error preparing query: " . mysqli_error($conn);
                }
        
                // Fetch selected items
                mysqli_data_seek($result, 0); // Reset result pointer
                while ($row = mysqli_fetch_assoc($result)) {
                    $productId = $row['product_id'];
                    $quantity = $row['quantity'];
                    $cupSize = $row['cup_size'];
                    $addOnsNames = $row['add_ons'];
                    $addOnsQuantities = json_decode($row['add_ons_quantities'], true); // Decode add-ons quantities
                    $flavorsQuantities = json_decode($row['custom_flavor_quantities'], true); // Decode add-ons quantities
            
                    // Deduct product quantity from the product table
                    $deductProductQuantityQuery = "UPDATE product SET qty = qty - $quantity WHERE product_id = $productId";
                    $resultDeductProductQuantity = mysqli_query($conn, $deductProductQuantityQuery);
                    if (!$resultDeductProductQuantity) {
                        echo "Error deducting product quantity: " . mysqli_error($conn);
                    } 
                    // CUP INVENT DEDUCT
                    $deductCupSizeQuantityQuery = "UPDATE invent_cupsize SET qty = qty -  $quantity WHERE name = '$cupSize'";
                    $resultDeductCupSizeQuantity = mysqli_query($conn, $deductCupSizeQuantityQuery);
                    if (!$resultDeductCupSizeQuantity) {
                        echo "Error deducting cup size quantity: " . mysqli_error($conn);
                    }
        
            
                 // Process each add-on quantity and deduct from the invent_addson table
                    // Process each add-on quantity and deduct from the invent_addson table
                    if (is_array($addOnsQuantities)) {
                    foreach ($addOnsQuantities as $addOnName => $addOnQuantity) {
                        if (!empty($addOnName) && is_numeric($addOnQuantity) && $addOnQuantity > 0) {
                            // Deduct add-on quantity from invent_addson
                            $deductAddOnQuantityQuery = "UPDATE invent_addson SET qty = qty - ? WHERE name = ?";
                            $stmtDeductAddOn = mysqli_prepare($conn, $deductAddOnQuantityQuery);
        
                            if ($stmtDeductAddOn) {
                                mysqli_stmt_bind_param($stmtDeductAddOn, "is", $addOnQuantity, $addOnName);
                                $executionResult = mysqli_stmt_execute($stmtDeductAddOn);
        
                                if (!$executionResult) {
                                    echo "Error deducting quantity for add-on '$addOnName': " . mysqli_stmt_error($stmtDeductAddOn);
                                } else {
                                    echo "Successfully deducted $addOnQuantity from add-on '$addOnName'.<br>";
                                }
        
                                mysqli_stmt_close($stmtDeductAddOn);
                            } else {
                                echo "Error preparing query for add-on '$addOnName': " . mysqli_error($conn);
                            }
                        } else {
                            echo "Invalid add-on name or quantity for '$addOnName'.<br>";
                        }
                    }
                    } else {
                        echo "Invalid add-on quantities format.";
                    }

                        // DEDUCTION IN CUSTOM FLAVORS
                    if (is_array($flavorsQuantities)) {
                    foreach ($flavorsQuantities as $addOnName => $addOnQuantity) {
                        if (!empty($addOnName) && is_numeric($addOnQuantity) && $addOnQuantity > 0) {
                            // Deduct add-on quantity from invent_addson
                            $deductAddOnQuantityQuery = "UPDATE invent_flavors SET qty = qty - ? WHERE name = ?";
                            $stmtDeductAddOn = mysqli_prepare($conn, $deductAddOnQuantityQuery);
        
                            if ($stmtDeductAddOn) {
                                mysqli_stmt_bind_param($stmtDeductAddOn, "is", $addOnQuantity, $addOnName);
                                $executionResult = mysqli_stmt_execute($stmtDeductAddOn);
        
                                if (!$executionResult) {
                                    echo "Error deducting quantity for add-on '$addOnName': " . mysqli_stmt_error($stmtDeductAddOn);
                                } else {
                                    echo "Successfully deducted $addOnQuantity from add-on '$addOnName'.<br>";
                                }
        
                                mysqli_stmt_close($stmtDeductAddOn);
                            } else {
                                echo "Error preparing query for add-on '$addOnName': " . mysqli_error($conn);
                            }
                        } else {
                            echo "Invalid add-on name or quantity for '$addOnName'.<br>";
                        }
                    }
                    } else {
                        echo "Invalid add-on quantities format.";
                    }
                        

                        
        
                }
               
            }
        

        }

        // Delete the completed order from the queue

        
        $deleteItemsQuery = "DELETE FROM queue WHERE time_stamp IN ($placeholders)";
        $stmtDelete = mysqli_prepare($conn, $deleteItemsQuery);
        mysqli_stmt_bind_param($stmtDelete, str_repeat('s', count($timestamps)), ...$timestamps);
        mysqli_stmt_execute($stmtDelete);
        echo "<script>alert('Order completed successfully!');window.location.href = 'queue.php';</script>";
    }

    
    

 if($cartAction=='delete'){
        $deleteItemsQuery = "DELETE FROM queue WHERE time_stamp IN ($placeholders)";
        $stmtDelete = mysqli_prepare($conn, $deleteItemsQuery);
        mysqli_stmt_bind_param($stmtDelete, str_repeat('s', count($timestamps)), ...$timestamps);
        mysqli_stmt_execute($stmtDelete);
        // Redirect or display success 
        

          // COMPLETED ORDER IN ORDERS TABLE
          if (isset($_POST['selectedTimestamps'])) {
            $selectedTimestamps = $_POST['selectedTimestamps'];
    
            // Loop through each selected timestamp
            foreach ($selectedTimestamps as $timestamp) {
                // Update order status to 'completed' for the given time_stamp
                $saveOrderQuery = "UPDATE orders SET status = 'cancelled' WHERE order_date = ?";
                $stmt = mysqli_prepare($conn, $saveOrderQuery);
    
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $timestamp); // Bind the time_stamp
                    $resultUpdateOrder = mysqli_stmt_execute($stmt);
    
                    if ($resultUpdateOrder) {
                        // echo "Order with time_stamp $timestamp status updated to 'completed'.<br>";
                    } else {
                        echo "Error updating order with time_stamp $timestamp: " . mysqli_stmt_error($stmt) . "<br>";
                    }
    
                    mysqli_stmt_close($stmt);
                    } else {
                        echo "Error preparing query for time_stamp $timestamp: " . mysqli_error($conn) . "<br>";
                    }
                
                    // Update order status to 'completed' for the given time_stamp
                    $savePaymentQuery = "UPDATE payment SET status = 'cancelled' WHERE payment_date = ?";
                    $stmtPay = mysqli_prepare($conn, $savePaymentQuery);
    
                    if ($stmtPay) {
                    mysqli_stmt_bind_param($stmtPay, "s", $timestamp); // Bind the time_stamp
                    $resultUpdatePayment = mysqli_stmt_execute($stmtPay);
    
                    if ($resultUpdatePayment) {
                        // echo "Order with time_stamp $timestamp status updated to 'completed'.<br>";
                    } else {
                        echo "Error updating order with time_stamp $timestamp: " . mysqli_stmt_error($stmtPay) . "<br>";
                    }
    
                    mysqli_stmt_close($stmtPay);
                } else {
                    echo "Error preparing query for time_stamp $timestamp: " . mysqli_error($conn) . "<br>";
                }

                    // Update customer history status
                    $savePaymentQuery = "UPDATE customer_history SET status = 'cancelled' WHERE time_stamp = ?";
                    $stmtPay = mysqli_prepare($conn, $savePaymentQuery);
    
                    if ($stmtPay) {
                    mysqli_stmt_bind_param($stmtPay, "s", $timestamp); // Bind the time_stamp
                    $resultUpdatePayment = mysqli_stmt_execute($stmtPay);
    
                    if ($resultUpdatePayment) {
                        // echo "Order with time_stamp $timestamp status updated to 'completed'.<br>";
                    } else {
                        echo "Error updating order with time_stamp $timestamp: " . mysqli_stmt_error($stmtPay) . "<br>";
                    }
    
                    mysqli_stmt_close($stmtPay);
                } else {
                    echo "Error preparing query for time_stamp $timestamp: " . mysqli_error($conn) . "<br>";
                }
    

           
            }
            echo "<script>alert('Order cancelled successfully!'); window.location.href = 'customerhistory.php';</script>";


        }

    }
    mysqli_close($conn);
    ?>

