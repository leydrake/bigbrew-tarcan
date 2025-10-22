<?php
    include("databaseConnection.php");
    include 'navigationBar.php';
    include 'applyTheme.php';


    // Fetch add-ons from the database
    $sqlAddOns = "SELECT * FROM invent_addson";
    $resultAddOns = mysqli_query($conn, $sqlAddOns);
    $addOns = [];
    if ($resultAddOns) {
        while ($row = mysqli_fetch_assoc($resultAddOns)) {
            $addOns[] = $row;
        }
    }

    // Group add-ons by category
    $groupedAddOns = [];
    foreach ($addOns as $addOn) {
        $groupedAddOns[$addOn['category']][] = $addOn;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="product_details.css">

</head>
<body>

<div class="product-details">
    <?php
    // Get product ID from the URL
    $productId = isset($_GET['product_id']) ? $_GET['product_id'] : 0;
    $productId = mysqli_real_escape_string($conn, $productId);





    // Retrieve product data
    $sql = "SELECT * FROM product WHERE product_id = '$productId'";
    $result = mysqli_query($conn, $sql);


    if (mysqli_num_rows($result) > 0) {
        // Display product details
        $row = mysqli_fetch_assoc($result);
        $basePrice = $row['price'];
        $productQty = $row['qty'];

        // Fetch cup sizes and their quantities
        $sqlCup = "SELECT * FROM invent_cupsize";
        $resultCup = mysqli_query($conn, $sqlCup);
        $cupSizes = [];
        while ($rowCup = mysqli_fetch_assoc($resultCup)) {
            $cupSizes[] = $rowCup;
        }

        // Determine the available stocks based on the selected cup size
        $selectedCupQty = $cupSizes[0]['qty']; // Default to the first cup size quantity
        $availableStocks = min($productQty, $selectedCupQty);

        echo '<div class="product">';
        echo '<img src="./img/' . $row['image'] . '" alt="Product Image">';
        echo '</div>';

        echo '<div class="product-info">';
        echo "BIG BREW - Big in taste, Bit in Price";
        echo '<h2 id="productName">' . $row['name'] . '</h2>';
        echo '<p>' . $row['description'] . '</p>';
        echo '<h3>Price: ₱<span id="price">' . $basePrice . '</span></h3>';
        echo '<p> Available Stocks: <b id="availableStocks">' . $availableStocks . '</b></p>';

        // Add quantity input and buttons for buy now and add to cart
        echo '<form action="addToCart.php" method="POST" onsubmit = "return loginFirst()">';
        echo '<input type="hidden" name="productName" value="' . $row['name'] . '">';
        echo '<input type="number" id="quantity" name="quantity" value="1" min="1" oninput="updatePrice()">';
        echo '<input type="submit" id="addtocart" value="Add to Cart">';

        // Cup sizes radio buttons
        echo "<div class='size-container'>";
        echo "<div class='custom-radio'>";
        $i = 0;
        foreach ($cupSizes as $cupSize) {
            echo "
                <input type='radio' id='radio-$i' name='cupSize' value='" . $cupSize['name'] . "' data-price='" . $cupSize['price'] . "' data-qty='" . $cupSize['qty'] . "' " . ($i === 0 ? "checked" : "") . " onchange='updatePrice()'>
                <label class='radio-label' for='radio-$i'>
                    <div class='radio-circle'></div>
                    <span class='radio-text'>" . $cupSize['name'] . "</span>
                </label>
            ";
            $i++;
        }
        echo "</div>";
        echo "</div>";
    } else {
        echo 'Product not found.';
    }
?>

<!-- Add-ons selection section -->
<div class="select-optionnn">
<div class="add-ons-container">
    <?php
    foreach ($groupedAddOns as $category => $categoryAddOns) {
        echo "<div class='category-column'>";
        foreach ($categoryAddOns as $addOn) {
            echo "
                <div class='checkbox-option'>
                    <input type='checkbox' id='addon-" . $addOn['id'] . "' name='addOns[" . $addOn['id'] . "][selected]' value='1' data-price='" . $addOn['price'] . "' onchange='updatePrice()'>
                    <label for='addon-" . $addOn['id'] . "'>" . $addOn['name'] . " (₱" . $addOn['price'] . ")</label>
                    <input type='number' name='addOns[" . $addOn['id'] . "][quantity]' value='1' min='1' id='addOnQuantity" . $addOn['id'] . "' class='addOnQuantity' oninput='updatePrice()'>
                </div>
            ";
        }
        echo "</div>";
    }
    ?>
</div>
</div>

<script>

    function loginFirst(){
        alert("Please login first to add to cart");
        return false;

    }
function updatePrice() {
    // Get base price from PHP
    let basePrice = <?php echo $basePrice; ?>;

    // Get selected quantity of the product
    let quantity = parseInt(document.getElementById('quantity').value) || 1;

    // Get selected cup size and its price
    let cupSizeInput = document.querySelector('input[name="cupSize"]:checked');
    let cupSizePrice = parseFloat(cupSizeInput.dataset.price) || 0;
    let cupQty = parseInt(cupSizeInput.dataset.qty) || 0;

    // Get selected add-ons and calculate their total price
    let addOnInputs = document.querySelectorAll('input[name^="addOns["][type="checkbox"]:checked');
    let addOnPrice = 0;
    addOnInputs.forEach(function(addOn) {
        let addOnId = addOn.id.split('-')[1];
        let addOnQuantity = parseInt(document.getElementById('addOnQuantity' + addOnId).value) || 1;
        addOnPrice += parseFloat(addOn.dataset.price) * addOnQuantity;
    });

    // Calculate the total price
    let totalPrice = (basePrice + cupSizePrice + addOnPrice) * quantity;
    document.getElementById('price').innerText = totalPrice.toFixed(2);

    // Update available stocks
    let productQty = <?php echo $productQty; ?>;
    let availableStocks = Math.min(productQty, cupQty);
    document.getElementById('availableStocks').innerText = availableStocks;
}

// Bind updatePrice function to quantity input field
document.getElementById('quantity').addEventListener('input', updatePrice);

// Bind updatePrice function to each add-on quantity input field
document.querySelectorAll('.addOnQuantity').forEach(function(input) {
    input.addEventListener('input', updatePrice);
});
</script>

    </form> <!-- Close the form -->
</div>
