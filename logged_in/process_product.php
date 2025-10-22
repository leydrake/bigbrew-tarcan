<?php
include 'isadminlogin.php';

// Database connection
$host = 'localhost:3306';
$username = 'root';
$password = '';
$database = 'shopin';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Escape user inputs to prevent SQL injection
    $productName = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $categoryName = mysqli_real_escape_string($conn, $_POST['category']);

    // Check if the entered category exists
    $checkCategoryQuery = "SELECT category_id FROM category WHERE category_name = '$categoryName'";
    $result = mysqli_query($conn, $checkCategoryQuery);

    if (!$result || mysqli_num_rows($result) === 0) {
        echo '<script>alert("Category does not exist");window.location.href = "create_product.php";</script>';
        mysqli_close($conn);
        exit(); // Terminate script execution
    }

    // Fetch the category_id associated with the selected category_name
    $categoryRow = mysqli_fetch_assoc($result);
    $categoryID = $categoryRow['category_id'];

    // Upload and handle product image
    $targetDir = "picuploads/";
    $targetFile = $targetDir . basename($_FILES["product_image"]["name"]);
    move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile);
    $productImage = basename($_FILES["product_image"]["name"]); // Use only the filename

    // Insert into database
    $sql = "INSERT INTO products (product_name, description, price, quantity, category_id, product_image)
            VALUES ('$productName', '$description', $price, $quantity, '$categoryID', '$productImage')";

    if (mysqli_query($conn, $sql)) {
        echo '<script>alert("Product added successfully!");window.location.href = "create_product.php";</script>';
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>