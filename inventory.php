<html>
    <head> 
        <link rel="stylesheet" href="allCss.css">
    </head>
    <body>
        <div class="invTitle">Inventory</div>
        <div class="invContainer">
        <div class="side">
            <div class="adminlogo"><img src="image/coffeespotlogo.png" alt="" class="logoadmin"></div>
            <div class="admintitle">Coffee Spot</div>
            <a href="admindashboard.php"><div class="Dasboard">Dashboard</div></a>
            <a href="admininventory.html"><div class="Manage-product">Manage Product</div></a>
            <a href="adminprofile.html"><div class="sales">Sales</div></a>
            <a href="admincustomer.php"><div class="customerAcc">Customer Account</div></a>
            <a href="inventory.php"><div class="inventory">Inventory</div></a>
            <a href=""><div class="reports">Reports</div>


                <div class="dropdown">
                    <div class="adminpic"><img src="image/coffeespotlogo.png" alt="" class="picadmin"></div>
                    <span class="adminname">Lanz Paulo Abolac</span>
                    <div class="dropdown-content">
                        <a href="account_settings.html" class="dropdown-item">Account Settings</a>
                        <a href="login.html" class="dropdown-item">Log Out</a>
                    </div>
                </div>
                
        </div>
            <div class="addbtn">
                <a href="add.php"><button class="addbtn2">Add Product</button></a>
            </div>

        <?php
        // Database connection
        $conn = mysqli_connect('localhost', 'root', '', 'coffeespot');

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Fetch products from the inventory table
        $sql = "SELECT * FROM inventory";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Loop through each product and display it
        while ($row = mysqli_fetch_assoc($result)) {
            $productID = $row['id'];
            $prodName = $row['product_name'];
            $prodPrice = $row['product_price'];
            $quantity = $row['product_quantity'];
            $type = $row['product_type'];
            $image = base64_encode($row['product_img']);

            echo "
            <div class='invprod'>
                <div class='invimg'><img src='data:image/jpeg;base64,$image' alt='$prodName'></div>
                <div class='prodtitle'>$prodName</div>
                <div class='item'>
                    <table>
                        <tr>
                            <td>Product ID:</td>
                            <td>$productID</td>
                        </tr>
                        <tr>
                            <td>Product Price:</td>
                            <td>₱$prodPrice</td>
                        </tr>
                        <tr>
                            <td>Product Quantity:</td>
                            <td>$quantity</td>
                        </tr>
                        <tr>
                            <td>Product Type:</td>
                            <td>$type</td>
                        </tr>
                    </table>
                </div>
                <div class='delete-btn-container'>
                    <form action='inventory.php' method='post' onsubmit='return confirmDelete();'>
                        <input type='hidden' name='productID' value='$productID'>
                        <input type='submit' value='Delete' class='btndelete'>
                    </form>
                </div>
            </div>
            ";
        }
    } else {
        echo "<div>No products found in the inventory.</div>";
    }

    // Close the connection
    mysqli_close($conn);
    ?>
</div>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $conn = mysqli_connect('localhost', 'root', '', 'coffeespot');

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get the product ID from the form
    $productId = $_POST['productID'];

    // SQL query to delete the product from the inventory table
    $sql = "DELETE FROM inventory WHERE id = '$productId'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Product deleted successfully!'); window.location.href = 'inventory.php';</script>";
    } else {
        echo "<script>alert('Error deleting product: " . mysqli_error($conn) . "'); window.location.href = 'inventory.php';</script>";
    }

    // Close the database connection
    mysqli_close($conn);
}
?>