<?php
// Start the session
session_start();
include("databaseConnection.php");
$email = $_SESSION['emailAddress'];


if (isset($_POST["submit"])) {
    // Retrieve form data
    $name = $_POST["name"];
    $category = $_POST["category"];
    $description = $_POST["description"];
    $qty = (int)$_POST["qty"];
    $price = $_POST["price"];

    // Handle file upload
    try {
        $fileName = $_FILES["image"]["name"];
        $fileSize = $_FILES["image"]["size"];
        $tmpName = $_FILES["image"]["tmp_name"];

        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($imageExtension, $validImageExtension)) {
            echo "<script> alert('Invalid Image Extension'); </script>";
        } else if ($fileSize > 5000000) {
            echo "<script> alert('Image Size Is Too Large'); </script>";
        } else {
            $newImageName = uniqid() . '.' . $imageExtension;
            $uploadPath = 'img/' . $newImageName;

            if (move_uploaded_file($tmpName, $uploadPath)) {
                $query = "INSERT INTO product (name, image, category, description, qty, price) 
                        VALUES ('$name', '$newImageName', '$category', '$description', $qty, '$price')";
                if (mysqli_query($conn, $query)) {
                    // Update category product count
                    $updateCategoryQuery = "UPDATE category 
                                          SET product_count = product_count + 1 
                                          WHERE category = '$category'";
                    mysqli_query($conn, $updateCategoryQuery);
                    echo "<script>alert('Product Added Successfully'); document.location.href = 'add-product.php';</script>";
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "<script> alert('Failed to upload image'); </script>";
            }
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Handle product deletion
if (isset($_POST['delete_selected'])) {
    if (isset($_POST['delete']) && !empty($_POST['delete'])) {
        $productIds = implode(',', $_POST['delete']);
        $deleteQuery = "DELETE FROM product WHERE id IN ($productIds)";
        if (mysqli_query($conn, $deleteQuery)) {
            echo "<script>alert('Selected products deleted successfully'); document.location.href = 'add-product.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Please select products to delete');</script>";
    }
}

// Fetch products based on search
$searchTerm = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';
$searchQuery = "SELECT * FROM product WHERE name LIKE '%$searchTerm%' ORDER BY id DESC";
$products = mysqli_query($conn, $searchQuery);


// Fetch products based on selected category
$categoryFilter = isset($_POST['category_filter']) ? mysqli_real_escape_string($conn, $_POST['category_filter']) : '';
$categoryQuery = "SELECT DISTINCT category FROM category";
$categories = mysqli_query($conn, $categoryQuery);

if ($categoryFilter) {
    $searchQuery = "SELECT * FROM product WHERE category = '$categoryFilter' ORDER BY id DESC";
} else {
    $searchQuery = "SELECT * FROM product ORDER BY id DESC";
}
$products = mysqli_query($conn, $searchQuery);

if (isset($_POST['updateProduct'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $imageName = $_FILES['image']['name'];
    $imageTemp = $_FILES['image']['tmp_name'];
    $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    $validExtensions = ['jpg', 'jpeg', 'png'];

    $updateQuery = "UPDATE product SET name='$name', category='$category', qty=$qty, price='$price', description='$description'";

    // Handle image update if provided
    if ($imageName && in_array($imageExtension, $validExtensions)) {
        $newImageName = uniqid() . '.' . $imageExtension;
        move_uploaded_file($imageTemp, "img/$newImageName");
        $updateQuery .= ", image='$newImageName'";
    }

    $updateQuery .= " WHERE id=$id";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Product updated successfully'); document.location.href = 'add-product.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: antiquewhite;
            font-family: "Anton SC", sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .navigator {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background-color: #6f4e37;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .navigator .nav-item {
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            margin: 0 40px;
            position: relative;
            left: 500px;
            text-decoration: none;
        }

        .navigator .nav-item:hover {
            color: #d3ad7f;
        }

        .admin-profile-container {
            position: fixed;
            left: 0;
            top: 60px;
            width: 300px;
            height: calc(100% - 60px);
            background-color: #fff;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.2);
            padding: 20px 0;
            z-index: 2000;
        }

        .admin-profile-container h2 {
            text-align: center;
            color: #6f4e37;
        }

        .admin-profile-container {
            position: fixed;
            left: 0;
            top: 60px;
            width: 300px;
            height: calc(100% - 60px);
            background-color: #fff;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.2);
            padding: 20px 0;
            z-index: 2000;
        }

        .admin-profile-container h2 {
            text-align: center;
            color: #6f4e37;
            margin-bottom: 10px;
            margin-top: 0;
        }

        .profile-image-container {
            text-align: center;
            margin-bottom: 20px;
        }

        #adminProfileImage {
            width: 150px;
            height: 150px;
            border: 5px solid #6f4e37;
            border-radius: 10px;
        }

        .admin-links {
            margin-top: 20px;
        }

        .admin-links a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #6f4e37;
            font-weight: bold;
            padding: 10px 15px;
            font-size: 18pt;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .admin-links a i {
            margin-right: 10px;
            font-size: 20px;
        }

        .admin-links a:hover {
            background-color: #6f4e37;
            color: #fff;
        }

        .content-container {
            margin-left: -430px;
            position: relative;
            top: -20px;
            padding: 20px;
        }

        .profile-image-container {
            text-align: center;
            margin-bottom: 20px;
        }

        #adminProfileImage {
            width: 150px;
            height: 150px;
            border: 5px solid #6f4e37;
            border-radius: 10px;
        }

        .content-container {
            margin-left: 330px;
            /* padding: 20px; */
            margin-top: 105px;
            width: 1098px;
            border-radius: 10px;
            height: 200px;
            background-color: #f9f9f9;
        }

        .dynamic_flexible {
            margin-left: -20px;
            /* padding: 20px; */
            margin-top: -25px;
            width: 1138px;
            height: 200px;
            /* background-color: #fff; */
        }

        .prod {
            font-size: 28px;
            text-align: center;
            font-weight: bold;
            color: #6f4e37;
            border-radius: 5px;
            padding: 15px;
            margin: 0px 20px;
            background-color: #e2dcd7;
            margin-top: 10px;
            margin-bottom: 30px;
            top: 20px;
            position: relative;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 3000;
        }

        .popup-content {
            background-color: white;
            width: 50%;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            text-align: center;
        }

        .popup-content h1 {
            margin-top: 0;
        }

        .popup-content .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        .popup-content .close-btn:hover {
            color: red;
        }

        select[name="category_filter"] {
            width: 200px;
            height: 45px;
            margin-top: 10px;

            margin-left: 20px;
            border: 2px solid #6f4e37;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 16px;
            color: #6f4e37;
            background-color: #fff;
            outline: none;
            /* transition: border-color 0.3s ease, background-color 0.3s ease; */
            font-family: "Anton SC", sans-serif;
        }

        select[name="category_filter"]:hover {
            /* border-color: #d3ad7f; */
            background-color: #f7f3ee;
        }

        .view {
            margin-top: 10px;
        }

        /* select[name="category_filter"]:focus { */
        /* border-color: #d3ad7f; */
        /* box-shadow: 0 0 5px rgba(111, 78, 55, 0.5); */
        /* } */

        option {
            font-size: 14px;
            color: #6f4e37;
            padding: 10px;
        }

        .product-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            padding: 20px;

            margin-top: 5px;
            background-color: #f9f9f9;
            border-radius: 10px;
            /* box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); */
        }

        .product-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 222px;
            height: 360px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .product-card .delete-checkbox {
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .product-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin: 5px 0;
        }

        .product-category {
            font-size: 14px;
            color: #777;
        }

        .product-price {
            font-size: 16px;
            color: #6f4e37;
            font-weight: bold;
        }

        .deletebtn {
            margin-top: 20px;
            background-color: #6f4e37;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .filt {
            margin-top: 5px;
            background-color: #6f4e37;
            color: white;
            border: none;

            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;

        }

        .deletebtn:hover {
            background-color: #d3ad7f;
        }

        #del {
            position: absolute;
            top: 92px;
            left: 970px;
        }

        #add {
            position: absolute;
            left: 300px;
            top: 92px;
            padding: 10px 15px;


        }

        .editbtn {
            /* margin-top: 20px; */
            background-color: #6f4e37;
            color: white;
            border: none;
            padding: 4px 15px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        /* .product-card {
            display: flex;
            align-items: center;
            cursor: pointer;
            position: relative;
        } */

        /* .product-card {
            position: relative;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            background-color: #fff;
        } */

        .product-card .checkbox-container {
            position: absolute;
            top: 25px;
            left: 15px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .product-card .checkbox-container input[type="checkbox"] {
            display: none;
        }

        .product-card .checkbox-container label {
            font-size: 14px;
            color: #6f4e37;
            font-weight: bold;
            padding-left: 25px;
            position: relative;
            cursor: pointer;
        }

        .product-card .checkbox-container label::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            border: 2px solid #6f4e37;
            border-radius: 4px;
            background: #fff;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .product-card .checkbox-container input[type="checkbox"]:checked+label::before {
            background: #6f4e37;
            border-color: #6f4e37;
        }

        .product-card .checkbox-container input[type="checkbox"]:checked+label::after {
            content: '✔';
            position: absolute;
            left: 4px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: #fff;
        }


        /* Overlay for the popup */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 3000;
        }

        .popup-content {
            background-color: #fff;
            width: 40%;
            max-width: 450px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            text-align: left;
            animation: fadeIn 0.3s ease;
            font-family: Arial, sans-serif;
            overflow-y: auto;
            max-height: 90vh;
        }

        .popup-content .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 30px;
            color: #333;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .popup-content .close-btn:hover {
            color: #ff0000;
        }

        .popup-content h1 {
            margin: 0 0 20px;
            font-size: 24px;
            color: #6f4e37;
            text-align: center;
            text-transform: uppercase;
        }

        .popup-content form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 15pt;
            color: #6f4e37;
        }

        .popup-content form input[type="text"],
        .popup-content form input[type="number"],
        .popup-content form input[type="file"],
        .popup-content form select,
        .popup-content form textarea {
            width: 100%;
            padding: 7px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .popup-content form textarea {
            resize: none;
        }

        .popup-content form button[type="submit"] {
            background-color: #6f4e37;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .form-column {
            flex: 1;
            margin-right: 10px;
        }

        .form-column:last-child {
            margin-right: 0;
        }

        .form-row input[type="text"],
        .form-row input[type="number"],
        .form-row input[type="file"],
        .form-row select,
        .form-row textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .addprod {
            display: block;
            width: 100%;
            margin-top: 20px;
            background-color: #6f4e37;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .addprod:hover {
            background-color: #d3ad7f;
            transform: scale(1.05);
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .form-column {
            flex: 1;
            margin-right: 10px;
        }

        .form-column:last-child {
            margin-right: 0;
        }

        /* Ensure inputs, selects, and textareas fit their containers */
        .form-row input[type="text"],
        .form-row input[type="number"],
        .form-row input[type="file"],
        .form-row select,
        .form-row textarea {
            width: 100%;
            /* Full width for columns */
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
    </style>
</head>

<body>
   

    <div class="content-container">
        <div class="all">
            <!-- Add Product Section -->
            <!-- <h1>PRODUCT MANAGEMENT</h1> -->

            <!-- <button class="deletebtn" onclick="showpopup()">Add Product</button> -->

            <!-- Popup -->
            <div class="popup-overlay" id="popupOverlay">
                <div class="popup-content">
                    <button class="close-btn" onclick="hidepopup()">&times;</button>
                    <h1>PRODUCT MANAGEMENT</h1>
                    <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
                        <div class="form-row">
                            <label for="name">Product Name:</label>
                            <input type="text" name="name" id="name" required>
                        </div>

                        <div class="form-row">
                            <div class="form-column">
                                <label for="image">Product Image:</label>
                                <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png" required>
                            </div>
                            <div class="form-column">
                                <label for="category">Category:</label>
                                <select name="category" id="category" required>
                                    <?php
                                    $categoryQuery = mysqli_query($conn, "SELECT DISTINCT category FROM category");
                                    while ($categoryRow = mysqli_fetch_assoc($categoryQuery)) {
                                        echo "<option value='" . $categoryRow['category'] . "'>" . $categoryRow['category'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <label for="description">Description:</label>
                            <textarea name="description" id="description" rows="4" required></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-column">
                                <label for="qty">Quantity:</label>
                                <input type="number" name="qty" id="qty" required>
                            </div>
                            <div class="form-column">
                                <label for="price">Price:</label>
                                <input type="text" name="price" id="price" required>
                            </div>
                        </div>

                        <button class="addprod" type="submit" name="submit">Add Product</button>
                    </form>
                </div>
            </div>


            <script>
                // JavaScript for Popup
                const popupOverlay = document.getElementById('popupOverlay');

                function showPopup() {
                    document.getElementById('popupOverlay').style.display = 'flex';
                }

                function hidepopup() {
                    document.getElementById('popupOverlay').style.display = 'none';
                }

                function showpopup() {
                    popupOverlay.style.display = 'flex';
                }

                function hidepopup() {
                    popupOverlay.style.display = 'none';
                }
            </script>
            <!-- Product List Section -->
            <div class="dynamic_flexible">
                <div class="view">
                    <h3 class="prod">Product Management</h3>
                    <button class="deletebtn" id="add" onclick="showpopup()">Add Product</button>

                    <!-- Category Filter Dropdown -->
                    <form method="POST">
                        <select name="category_filter">
                            <option value="">Select Category</option>
                            <?php while ($category = mysqli_fetch_assoc($categories)) { ?>
                                <option value="<?php echo $category['category']; ?>" <?php echo $category['category'] == $categoryFilter ? 'selected' : ''; ?>>
                                    <?php echo $category['category']; ?>
                                </option>
                            <?php } ?>
                        </select>
                        <button class="filt" type="submit">Filter</button>
                    </form>

                    <!-- Product Cards -->
                    <form method="POST">
                        <div class="product-cards">
                            <?php while ($product = mysqli_fetch_assoc($products)) { ?>
                                <div class="product-card">
                                    <div class="checkbox-container">
                                        <input type="checkbox" id="delete_<?php echo $product['id']; ?>" name="delete[]" value="<?php echo $product['id']; ?>" class="delete-checkbox">
                                        <label for="delete_<?php echo $product['id']; ?>"></label>
                                    </div> <img src="img/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-image">
                                    <h3 class="product-name"><?php echo $product['name']; ?></h3>
                                    <p class="product-category"><?php echo $product['category']; ?></p>
                                    <!-- <p class="product-price">₱<?php echo number_format($product['price'], 2); ?></p> -->
                                    <button type="button" class="editbtn" onclick="showEditPopup(<?php echo htmlspecialchars(json_encode($product)); ?>)">Update</button>
                                </div>






                            <?php } ?>
                        </div>
                        <button type="submit" name="delete_selected" id="del" class="deletebtn">Delete Selected</button>
                    </form>
                </div>
                <br><br><br><br>
            </div>
            <div class="popup-overlay" id="editPopup">
                <div class="popup-content">
                    <button class="close-btn" onclick="hideEditPopup()">&times;</button>
                    <h1>Edit Product</h1>
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="editId">

                        <div class="form-row">
                            <label for="editName">Product Name:</label>
                            <input type="text" name="name" id="editName" required>
                        </div>

                        <div class="form-row">
                            <div class="form-column">
                                <label for="editImage">Product Image:</label>
                                <input type="file" name="image" id="editImage" accept=".jpg, .jpeg, .png">
                            </div>
                            <div class="form-column">
                                <label for="editCategory">Category:</label>
                                <select name="category" id="editCategory" required>
                                    <?php
                                    $categoryQuery = mysqli_query($conn, "SELECT DISTINCT category FROM category");
                                    while ($categoryRow = mysqli_fetch_assoc($categoryQuery)) {
                                        echo "<option value='" . $categoryRow['category'] . "'>" . $categoryRow['category'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <label for="editDescription">Description:</label>
                            <textarea name="description" id="editDescription" rows="4" required></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-column">
                                <label for="editQty">Quantity:</label>
                                <input type="number" name="qty" id="editQty" required>
                            </div>
                            <div class="form-column">
                                <label for="editPrice">Price:</label>
                                <input type="text" name="price" id="editPrice" required>
                            </div>
                        </div>



                        <button class="addprod" type="submit" name="updateProduct">Update Product</button>
                    </form>
                </div>
            </div>







        </div>

        <script>
            function showEditPopup(product) {
                document.getElementById('editId').value = product.id;
                document.getElementById('editName').value = product.name;
                document.getElementById('editCategory').value = product.category;
                document.getElementById('editQty').value = product.qty;
                document.getElementById('editPrice').value = product.price;
                document.getElementById('editDescription').value = product.description;
                document.getElementById('editPopup').style.display = 'flex';
            }

            function hideEditPopup() {
                document.getElementById('editPopup').style.display = 'none';
            }

            function toggleSelectAll(source) {
                const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
                checkboxes.forEach(checkbox => checkbox.checked = source.checked);
            }
        </script>
</body>

</html>