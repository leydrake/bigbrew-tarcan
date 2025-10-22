<?php
    include("databaseConnection.php");
    include 'navigationBar.php';
    include 'applyTheme.php';

$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;



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




     // Fetch product category from the database
     $productId = isset($_GET['product_id']) ? $_GET['product_id'] : 0;
     $productId = mysqli_real_escape_string($conn, $productId);
 
     $sqlProduct = "SELECT * FROM product WHERE product_id = '$productId'";
     $resultProduct = mysqli_query($conn, $sqlProduct);
 
     if (mysqli_num_rows($resultProduct) > 0) {
         $product = mysqli_fetch_assoc($resultProduct);
         $productCategory = $product['category']; // Get the category of the product
     }
 
     // Fetch flavors (Customize) from the database
     $sqlFlavors = "SELECT * FROM invent_flavors";
     $resultFlavors = mysqli_query($conn, $sqlFlavors);
     $flavors = [];
     if ($resultFlavors) {
         while ($row = mysqli_fetch_assoc($resultFlavors)) {
             $flavors[] = $row;
         }
     }


     
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="pos_product_details.css">

</head>

<script>
    // toggle profile
    function toggleProfile() {
            const profileContainer = document.getElementById("profileContainer");
            const headerContainer = document.getElementById("header");
            const body = document.getElementById("body");

            profileContainer.classList.toggle("open");


            if (profileContainer.classList.contains("open")) {
                headerContainer.style.position = "fixed";
                headerContainer.style.top = "0";
                headerContainer.style.width = "100%";
                body.style.marginTop = "245px";

            } else {
                headerContainer.style.position = "";
                headerContainer.style.top = "";
                headerContainer.style.width = "";
                body.style.marginTop = "";
            }
        }
</script>
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
        echo '<img src="../img/' . $row['image'] . '" alt="Product Image">';
        echo '</div>';

        echo '<div class="product-info">';
        // echo '<h2 id="productName">' . $row['name'] . '</h2>';
        // echo '<p>' . $row['description'] . '</p>';
        echo '<h3>Price: ₱<span id="price">' . $basePrice . '</span></h3>';
        echo '<p> Available Stocks: <b id="availableStocks">' . $availableStocks . '</b></p>';

        // Add quantity input and buttons for buy now and add to cart
        echo '<form action="pos_addToCart.php" method="POST">';
        echo '<input type="hidden" name="productName" value="' . $row['name'] . '">';
        echo '<input type="number" id="quantity" name="quantity" value="1" min="1" oninput="updateToppingsAndPrice()">';
        echo '<input type="submit" id="addtocart" value="Add to Cart">';


        // Cup sizes radio buttons
        echo "<div class='size-container'>";
        echo "<div class='custom-radio'>";
        $i = 0; // Counter for radio buttons
    foreach ($cupSizes as $cupSize) {
    $isChecked = ($cupSize['name'] === 'Medio') ? "checked" : ($i === 0 ? "checked" : "");
    echo "
        <input type='radio' id='radio-$i' name='cupSize' value='" . $cupSize['name'] . "' data-price='" . $cupSize['price'] . "' data-qty='" . $cupSize['qty'] . "' $isChecked onchange='updateToppingsAndPrice()'>
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
    <h2>Add ons:</h2>
    <?php
    foreach ($groupedAddOns as $category => $categoryAddOns) {
        echo "<div class='category-column'>";
        foreach ($categoryAddOns as $addOn) {
            echo "
                <div class='checkbox-option'>
                    <input type='checkbox' id='addon-" . $addOn['id'] . "' name='addOns[" . $addOn['id'] . "][selected]' value='1' data-price='" . $addOn['price'] . "' onchange='updateToppingsAndPrice()'>
                    <label for='addon-" . $addOn['id'] . "'>" . $addOn['name'] . " (₱" . $addOn['price'] . ")</label>
                    <input type='number' name='addOns[" . $addOn['id'] . "][quantity]' value='1' min='1' id='addOnQuantity" . $addOn['id'] . "' class='addOnQuantity' oninput='updateToppingsAndPrice()'>
                </div>
            ";
        }
        echo "</div>";
    }
    ?>
</div>
</div>

<input type="hidden" id="calculatedPrice" name="calculatedPrice" value="">

      <!-- Display Flavors (Customize Section) only if category is 'Customize' -->
      <?php if ($productCategory == "Customize") { ?>

      

        <div class="customize-items-container">

            <div class="flavors-container">
                <h2>Customize:</h2>
                <?php
                foreach ($flavors as $flavor) {
                    echo "
                        <div class='checkbox-option'>
                            <input type='checkbox' id='flavor-" . $flavor['id'] . "' name='flavors[" . $flavor['id'] . "][selected]' value='1' data-price='" . $flavor['price'] . "' data-image='../img/" . $flavor['image'] . "' onchange='updateToppingsAndPrice()'>
                            <label for='flavor-" . $flavor['id'] . "'>" . $flavor['name'] . " (₱" . $flavor['price'] . ")</label>
                            <input type='number' name='flavors[" . $flavor['id'] . "][quantity]' value='1' min='1' id='flavorQuantity" . $flavor['id'] . "' class='flavorQuantity' oninput='updateToppingsAndPrice()'>
                        </div>
                    ";
                }
                ?>
            </div>
        </div>

    <?php } ?>
    


    </form> <!-- Close the form -->
</div>
</body>
</html>


<script>

function updateToppingsAndPrice() {
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

    // Get selected flavors and calculate their total price
    let flavorInputs = document.querySelectorAll('input[name^="flavors["][type="checkbox"]:checked');
    let flavorPrice = 0;
    let productDiv = document.querySelector('.product');

    // Clear any existing topping images    
    let toppingImages = productDiv.querySelectorAll('.topping-image');
    toppingImages.forEach(img => img.remove());

    // Add topping images and calculate flavor price
    flavorInputs.forEach(function(flavor) {
        let flavorId = flavor.id.split('-')[1];
        let flavorQuantity = parseInt(document.getElementById('flavorQuantity' + flavorId).value) || 1;
        flavorPrice += parseFloat(flavor.dataset.price) * flavorQuantity;

        // Create image for topping
        let toppingImage = document.createElement('img');
        toppingImage.src = flavor.dataset.image;
        toppingImage.classList.add('topping-image');
        toppingImage.style.position = 'absolute'; // Overlap the images
        toppingImage.style.width = '650px';       // Adjust width to fit product
        toppingImage.style.height = '100%';      // Adjust height to fit product
        toppingImage.style.left = '0';      // Adjust height to fit product
        toppingImage.style.zIndex = 10;          // Ensure proper stacking

        productDiv.appendChild(toppingImage);
    }
);

    // Calculate the total price
    let totalPrice = (basePrice + cupSizePrice + addOnPrice + flavorPrice) * quantity;
    document.getElementById('price').innerText = totalPrice.toFixed(2);

    // Update available stocks
    let productQty = <?php echo $productQty; ?>;
    let availableStocks = Math.min(productQty, cupQty);
    document.getElementById('availableStocks').innerText = availableStocks;
}

// Bind updateToppingsAndPrice function to each flavor checkbox and quantity input field
document.querySelectorAll('input[name^="flavors["]').forEach(function(input) {
    input.addEventListener('input', updateToppingsAndPrice);
});


document.getElementById('quantity').addEventListener('input', updatePrice);

// Bind updatePrice function to each add-on quantity input field
document.querySelectorAll('.addOnQuantity').forEach(function(input) {
    input.addEventListener('input', updatePrice);
});
</script>

