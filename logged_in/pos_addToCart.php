<?php
session_start();
include("databaseConnection.php");

if (!isset($_SESSION['emailAddress'])) {
    echo '<script>alert("User not logged in. Please log in first."); window.location.href = "homepage.php";</script>';
    exit();
}

$customerId = $_SESSION['customer_id'];

// Retrieve product name, quantity, and cup size from the form submission
$productName = mysqli_real_escape_string($conn, $_POST['productName']);
$quantity = intval($_POST['quantity']);
$selectedCupSize = isset($_POST['cupSize']) ? $_POST['cupSize'] : '';

// Process selected add-ons
$addOns = isset($_POST['addOns']) ? $_POST['addOns'] : [];

$addOnsPrice = 0.0;
$addOnsDetails = [];
$addOnsQuantities = [];

foreach ($addOns as $id => $details) {
    if (isset($details['selected'])) { // Check if the add-on was selected
        $addOnQuantity = intval($details['quantity']);
        $id = intval($id);

        // Fetch add-on details
        $addOnQuery = "SELECT name, price, qty FROM invent_addson WHERE id = $id";
        $addOnResult = mysqli_query($conn, $addOnQuery);

        if ($addOnResult && $addOnRow = mysqli_fetch_assoc($addOnResult)) {
            // Check if the selected quantity exceeds the available quantity
            if ($addOnQuantity > $addOnRow['qty']) {
                echo '<script>alert("Selected quantity for ' . $addOnRow['name'] . ' exceeds available stock."); window.location.href = "products.php";</script>';
                exit();
            }

            // Add to total price and details
            $addOnsPrice += $addOnRow['price'] * $addOnQuantity;
            $addOnsDetails[] = $addOnRow['name'] . " (x$addOnQuantity)";
            $addOnsQuantities[$addOnRow['name']] = $addOnQuantity; // Store quantity
        }
    }
}
$addOnsNames = implode(', ', $addOnsDetails);
$addOnsQuantitiesJson = json_encode($addOnsQuantities);



// Process selected flavors (customize section)
$flavors = isset($_POST['flavors']) ? $_POST['flavors'] : [];
$addedFlavors = [];
$addedFlavorsQuantities = [];
$customFlavorPrice = 0.0;

foreach ($flavors as $id => $details) {
    if (isset($details['selected'])) { // Check if the flavor was selected
        $flavorQuantity = intval($details['quantity']);
        $id = intval($id);

        // Fetch flavor details
        $flavorQuery = "SELECT name, price,qty FROM invent_flavors WHERE id = $id";
        $flavorResult = mysqli_query($conn, $flavorQuery);

        if ($flavorResult && $flavorRow = mysqli_fetch_assoc($flavorResult)) {

            if ($flavorQuantity > $flavorRow['qty']) {
                echo '<script>alert("Selected quantity for ' . $flavorRow['name'] . ' exceeds available stock."); window.location.href = "products.php";</script>';
                exit();
            }

            $addedFlavors[] = $flavorRow['name'] . " (x$flavorQuantity)";
            $addedFlavorsQuantities[$flavorRow['name']] = $flavorQuantity; // Store quantity
            $customFlavorPrice += $flavorRow['price'] * $flavorQuantity; // Add flavor price to custom flavor total
        }
    }
}

$addedFlavorsNames = implode(', ', $addedFlavors);
$addedFlavorsQuantitiesJson = json_encode($addedFlavorsQuantities);


// Fetch product details
$productQuery = "SELECT product_id, price, qty FROM product WHERE name = '$productName' LIMIT 1";
$productResult = mysqli_query($conn, $productQuery);

if ($productResult && mysqli_num_rows($productResult) > 0) {
    $productRow = mysqli_fetch_assoc($productResult);
    $productId = $productRow['product_id'];
    $productPrice = $productRow['price'];
    $availableQuantity = $productRow['qty'];

    if ($quantity > $availableQuantity) {
        echo '<script>alert("Sorry, product is out of stock."); history.back();</script>';
        exit();
    }

    $totalPrice = ($productPrice) + $addOnsPrice + $customFlavorPrice;

    // Check if the item already exists in the cart
    $checkCartQuery = "SELECT * FROM cart WHERE customer_id = $customerId AND product_id = $productId AND cup_size = '$selectedCupSize' AND add_ons = '$addOnsNames' AND custom_flavor = '$addedFlavorsNames'";
    $checkCartResult = mysqli_query($conn, $checkCartQuery);

    if (mysqli_num_rows($checkCartResult) > 0) {
        // Update the existing cart item, including the add-on and flavor quantities
        $updateCartQuery = "UPDATE cart SET 
                            quantity = quantity + $quantity, 
                            add_ons_price = add_ons_price + $addOnsPrice, 
                            custom_price = custom_price + $customFlavorPrice, 
                            total_price = total_price + $totalPrice,
                            add_ons_quantities = '$addOnsQuantitiesJson', 
                            custom_flavor = '$addedFlavorsNames', 
                            custom_flavor_quantities = '$addedFlavorsQuantitiesJson' 
                            WHERE customer_id = $customerId AND product_id = $productId AND cup_size = '$selectedCupSize' AND add_ons = '$addOnsNames' AND custom_flavor = '$addedFlavorsNames'";
        mysqli_query($conn, $updateCartQuery);
        echo '<script>alert("Cart updated successfully!"); history.back(); </script>';
    } else {
        // Insert a new item into the cart, including the add-on and flavor quantities
        $insertCartQuery = "INSERT INTO cart (customer_id, product_id, product_name, quantity, cup_size, add_ons, add_ons_quantities, add_ons_price, custom_price, total_price, custom_flavor, custom_flavor_quantities) 
                            VALUES ($customerId, $productId, '$productName', $quantity, '$selectedCupSize', '$addOnsNames', '$addOnsQuantitiesJson', $addOnsPrice, $customFlavorPrice, $totalPrice, '$addedFlavorsNames', '$addedFlavorsQuantitiesJson')";
        mysqli_query($conn, $insertCartQuery);
        echo '<script>alert("Item added to cart successfully!"); history.back();</script>';
    }
} else {
    echo '<script>alert("Product not found."); window.location.href = "index.php";</script>';
}

mysqli_close($conn);
?>
