<?php

    include("navigationBar.php");
    include 'isAdmin.php';

    include("databaseConnection.php");

    
    $result = mysqli_query($conn, "SELECT * FROM login_credentials WHERE customer_id != 1");

    // test if admin

    // query for inventory
    $result_inventory= mysqli_query($conn, "SELECT * FROM inventory");

    if (isset($_POST['submit_button'])) {
      $userInput = $_POST['password_text'];
      $adminPass = $_SESSION['Passwords'];
  
      if (!password_verify($userInput, $adminPass)) {
          echo "<script> alert('Wrong password')</script>";
      } else {
          // Handle Blocking
          if (isset($_POST['block_cb'])) {
              foreach ($_POST['block_cb'] as $userId => $checked) {
                  $status = (isset($checked)) ? 1 : 0; 
                  $updateQuery = "UPDATE login_credentials SET Blocked = $status WHERE customer_id = $userId";
                  mysqli_query($conn, $updateQuery);
              }
          }
  
          $uncheckedUsers = array_diff(
              array_column(mysqli_fetch_all(mysqli_query($conn, "SELECT customer_id FROM login_credentials")), 0),
              array_keys($_POST['block_cb'])
          );
          foreach ($uncheckedUsers as $userId) {
              $updateQuery = "UPDATE login_credentials SET Blocked  = 0, login_attempt = 0 WHERE customer_id = $userId";
              mysqli_query($conn, $updateQuery);
             
          }
  
          // Handle Deletion
          if (isset($_POST['select'])) {
              foreach ($_POST['select'] as $userId) {
                  $deleteQuery = "DELETE FROM login_credentials WHERE customer_id = $userId";
                  mysqli_query($conn, $deleteQuery);
              }
          }
  
          // Redirect after successful operation
          echo "<script>alert('Changes saved successfully!');
              window.location.href = './admin_settings.php';
          </script>";
      }
  }

  // MGA UPLOAD CONTAINER BUTTONS
    // FIRST CONTAINER
  if(isset($_POST['upload-button-1'])) {
    // pag kuha name tsaka img
    $image = $_FILES['image']['tmp_name'];
    $imgContent = addslashes(file_get_contents($image));

    // pag insert
    // $sql = "INSERT INTO IMGHOLDER (NAME, IMAGES) VALUES ('$name', '$imgContent')";
    $sql = "UPDATE image_index SET image = '$imgContent' WHERE id = '1' ";
    
    if(mysqli_query($conn, $sql)) {
        echo "Image uploaded successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
  }
  // SECOND CONTAINER
  if(isset($_POST['upload-button-2'])) {
    // pag kuha name tsaka img
    $image = $_FILES['image2']['tmp_name'];
    $imgContent = addslashes(file_get_contents($image));

    // pag insert
    // $sql = "INSERT INTO IMGHOLDER (NAME, IMAGES) VALUES ('$name', '$imgContent')";
    $sql = "UPDATE image_index SET image = '$imgContent' WHERE id = '2' ";
    
    if(mysqli_query($conn, $sql)) {
        echo "Image uploaded successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
  }
  
  // THIRD CONTAINER
  if(isset($_POST['upload-button-3'])) {
    // pag kuha name tsaka img
    $image = $_FILES['image3']['tmp_name'];
    $imgContent = addslashes(file_get_contents($image));

    // pag insert
    // $sql = "INSERT INTO IMGHOLDER (NAME, IMAGES) VALUES ('$name', '$imgContent')";
    $sql = "UPDATE image_index SET image = '$imgContent' WHERE id = '3' ";
    
    if(mysqli_query($conn, $sql)) {
        echo "Image uploaded successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
  }
  // fourth CONTAINER
  if(isset($_POST['upload-button-4'])) {
    // pag kuha name tsaka img
    $image = $_FILES['image4']['tmp_name'];
    $imgContent = addslashes(file_get_contents($image));

    // pag insert
    // $sql = "INSERT INTO IMGHOLDER (NAME, IMAGES) VALUES ('$name', '$imgContent')";
    $sql = "UPDATE image_index SET image = '$imgContent' WHERE id = '4' ";
    
    if(mysqli_query($conn, $sql)) {
        echo "Image uploaded successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
  }
  
  // FIFTH CONTAINER
  if(isset($_POST['upload-button-5'])) {
    // pag kuha name tsaka img
    $image = $_FILES['image5']['tmp_name'];
    $imgContent = addslashes(file_get_contents($image));

    // pag insert
    // $sql = "INSERT INTO IMGHOLDER (NAME, IMAGES) VALUES ('$name', '$imgContent')";
    $sql = "UPDATE image_index SET image = '$imgContent' WHERE id = '5' ";
    
    if(mysqli_query($conn, $sql)) {
        echo "Image uploaded successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
  }
  


// PRODUCTS
// Database connection

// Handle Add Product Form Submission
if (isset($_POST['submit_prod'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name_prod']);
    $description = mysqli_real_escape_string($conn, $_POST['description_prod']);
    $category = mysqli_real_escape_string($conn, $_POST['category_prod']);
    $qty = intval($_POST['qty_prod']);
    $price = floatval($_POST['price_prod']);

    // File Upload Handling
    $imageName = $_FILES['image_prod']['name'];
    $imageTmpName = $_FILES['image_prod']['tmp_name'];
    $imagePath = '../img/' . basename($imageName);

    if (move_uploaded_file($imageTmpName, $imagePath)) {
        $sql = "INSERT INTO product (name, description, category, qty, price, image) 
                VALUES ('$name', '$description', '$category', $qty, $price, '$imageName')";

        if (mysqli_query($conn, $sql)) {
            echo "Product added successfully.";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Failed to upload image.";
    }
}

// Handle Update Product Form Submission
if (isset($_POST['updateProduct_prod'])) {
    $id = intval($_POST['id_prod']);
    $name = $_POST['name_prod'];
    $description = mysqli_real_escape_string($conn, $_POST['description_prod']);
    $category = mysqli_real_escape_string($conn, $_POST['category_prod']);
    $qty = intval($_POST['qty_prod']);
    $price = floatval($_POST['price_prod']);

    $imageName = $_FILES['image_prod']['name'];
    $imageTmpName = $_FILES['image_prod']['tmp_name'];



    // Check if an image is uploaded
    if ($imageName) {
        $imagePath = '../img/' . basename($imageName);
        // Debugging: Check file path
        echo "Image Path: $imagePath<br>";

        // Move the uploaded image
        if (move_uploaded_file($imageTmpName, $imagePath)) {
            echo "Image uploaded successfully.<br>";
        } else {
            echo "Error uploading image.<br>";
        }

        $updateQuery = "UPDATE product SET 
                        name='$name', 
                        description='$description', 
                        category='$category', 
                        qty=$qty, 
                        price=$price, 
                        image='$imageName' 
                        WHERE product_id=$id";
    } else {
        $updateQuery = "UPDATE product SET 
                        name='$name', 
                        description='$description', 
                        category='$category', 
                        qty=$qty, 
                        price=$price 
                        WHERE product_id=$id";
    }

    // Debugging: Print query before execution
    echo "SQL Query: $updateQuery<br>";

    // Execute the query
    if (mysqli_query($conn, $updateQuery)) {
        echo "Product updated successfully.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}


// Handle Delete Selected Products
if (isset($_POST['delete_selected_prod'])) {
    if (isset($_POST['delete_prod'])) {
        $deleteIds = implode(",", array_map('intval', $_POST['delete_prod']));
        $deleteQuery = "DELETE FROM product WHERE product_id IN ($deleteIds)";

        if (mysqli_query($conn, $deleteQuery)) {
            echo "Selected products deleted successfully.";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "No products selected for deletion.";
    }
}

// Fetch Categories for Dropdown
$categories = mysqli_query($conn, "SELECT DISTINCT category FROM category");

// Fetch Products for Display
$categoryFilter = isset($_POST['category_filter_prod']) ? mysqli_real_escape_string($conn, $_POST['category_filter_prod']) : '';
$productQuery = "SELECT * FROM product";
if ($categoryFilter) {
    $productQuery .= " WHERE category='$categoryFilter'";
}
$products = mysqli_query($conn, $productQuery);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel = "stylesheet" href = "./admin_settings.css">

  <script>
    document.querySelector('[name="delete_button"]').addEventListener('click', function (e) {
        if (!confirm('Are you sure you want to delete the selected users?')) {
            e.preventDefault();
        }
      });

    function showEnterPassword(){
      document.getElementById("enter-password").style.display = "block";
    }
    function closeEnterPassword(){
      document.getElementById("enter-password").style.display = "none";
    }
    // ENTER PASSWORD
    function showEnterPasswordTheme(){
      // event.preventDefault();
      document.getElementById("enter-password-theme").style.display = "block";
    }
    function closeEnterPasswordTheme(){

      document.getElementById("enter-password-theme").style.display = "none";
    }

    // SA MGA NAVIGATIONS
    function showCustomerAccounts(){
      document.getElementById("customer-accounts").style.display="block";


      document.getElementById("products-container").style.display="none";
      document.getElementById("theme-settings").style.display="none";
      document.getElementById("inventory-container").style.display="none";

      document.getElementById("category-container").style.display="none";




    }
    function showThemeSettings(){
        document.getElementById("inventory-container").style.display="none";
      document.getElementById("customer-accounts").style.display="none";
      document.getElementById("products-container").style.display="none";
      document.getElementById("category-container").style.display="none";

      document.getElementById("theme-settings").style.display="block";

    }
    function showProducts(){
        document.getElementById("products-container").style.display="block";


      document.getElementById("customer-accounts").style.display="none";
      document.getElementById("theme-settings").style.display="none";
      document.getElementById("category-container").style.display="none";
      document.getElementById("inventory-container").style.display="none";



      
    }
    
    function showCategory(){
      document.getElementById("category-container").style.display="block";

      document.getElementById("customer-accounts").style.display="none";
      document.getElementById("theme-settings").style.display="none";
      document.getElementById("products-container").style.display="none";
      document.getElementById("inventory-container").style.display="none";


      
    }
    
    function showInventory(){
      document.getElementById("inventory-container").style.display="block";

      document.getElementById("customer-accounts").style.display="none";
      document.getElementById("theme-settings").style.display="none";
      document.getElementById("products-container").style.display="none";
      document.getElementById("category-container").style.display="none";

      
    }
    



    //CHOOSE PICTURE UPLOAD CONTAINER
    function openUploadContainer1(){
      document.getElementById("upload-container-1").style.display = "block";
    }
    function closeUploadContainer1(){
      document.getElementById("upload-container-1").style.display = "none";
    }
    // SECOND CONTAINER
    function openUploadContainer2(){
      document.getElementById("upload-container-2").style.display = "block";
    }
    function closeUploadContainer2(){
      document.getElementById("upload-container-2").style.display = "none";
    }
    // THIRD CONTAINER
    function openUploadContainer3(){
      document.getElementById("upload-container-3").style.display = "block";
    }
    function closeUploadContainer3(){
      document.getElementById("upload-container-3").style.display = "none";
    }
    // 4th CONTAINER
    function openUploadContainer4(){
      document.getElementById("upload-container-4").style.display = "block";
    }
    function closeUploadContainer4(){
      document.getElementById("upload-container-4").style.display = "none";
    }
    // 5th CONTAINER
    function openUploadContainer5(){
      document.getElementById("upload-container-5").style.display = "block";
    }
    function closeUploadContainer5(){
      document.getElementById("upload-container-5").style.display = "none";
    }

    // THEME ENTER PASSWORD CAHNGE
// Validate password before submission

  </script>
</head>
<body>

  <div class="left-navigation">
    
    <h1>ADMIN SETTINGS</h1>
    
    <img src="avatars/<?php echo $_SESSION['Avatar']?>" alt=""  class="avatar" id = "image">

    <div class="link-container">
            <button onclick="showCustomerAccounts()">
                <img src="./pictures/customer.png" alt="">
                Customer Accounts
            </button>
        </div>

        <div class="link-container">
            <a href="./theme_settings.php">
                <button>
                    <img src="./pictures/theme.png" alt="">
                    Theme Settings
                </button>
            </a>
        </div>

        <div class="link-container">
            <button onclick="showProducts()">
                <img src="./pictures/soft-drink.png" alt="">
                Products
            </button>
        </div>

        <div class="link-container">
            <button onclick="showCategory()">
                <img src="./pictures/categories.png" alt="">
                Category
            </button>
        </div>

        <div class="link-container">
            <a href="./admin1_addson.php">
                <button>
                    <img src="./pictures/product-management.png" alt="">
                    Inventory
                </button>
            </a>
        </div>

        <div class="link-container">
            <a href="./printreport.php">
                <button>
                    <img src="./pictures/seo-report.png" alt="">
                    Print Reports
                </button>
            </a>
        </div>

        <div class="link-container">
            <a href="./fastslowitems.php">
                <button>
                    <img src="./pictures/growth.png" alt="">
                    Fast / Slow Moving Items
                </button>
            </a>
        </div>

        <div class="link-container">
            <a href="./paymenthistory.php">
                <button>
                    <img src="./pictures/transaction-history.png" alt="">
                    Payment History
                </button>
            </a>
        </div>
        <div class="link-container">
            <a href="./POS.php">
                <button>
                    <img src="./pictures/point-of-sale.png" alt="">
                    Point of Sales
                </button>
            </a>
        </div>

        <div class="link-container">
            <a href="./queue.php">
                <button>
                    <img src="./pictures/line.png" alt="">
                    Queue
                </button>
            </a>
        </div>

  </div>
  
  <div class="content-containers">

      <div class="customer-accounts" id="customer-accounts">
      <h1>Account Manager</h1>

          <div class="table-container">
          <form action="<?php  $_SERVER['PHP_SELF'] ?>" method="POST" id="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Email address</th>
                    <th>Block Status</th>
                    <th>Delete</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <?php
                        $temp1 = $row['customer_id'];
                        $temp2 = $row['firstName'];
                        $temp3 = $row['lastName'];
                        $temp4 = $row['emailAddress'];
                        $blockStatus = $row['Blocked']; // Assuming the column is 'block'
                        ?>
                        <td> <?php echo $temp1; ?></td>
                        <td> <?php echo $temp2; ?></td>
                        <td> <?php echo $temp3; ?></td>
                        <td> <?php echo $temp4; ?></td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" name="block_cb[<?php echo $temp1; ?>]" 
                                <?php echo ($blockStatus == 1) ? 'checked' : ''; ?>>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td><input type="checkbox" name="select[]" value="<?php echo $temp1; ?>"></td>

                    </tr>
                <?php } ?>
            </table>
                  <!-- ENTER PASSWORD -->
            <div class="enter-password" id="enter-password">
                <div class="text-container" >
                  <h3>Enter Admin Password</h3>
                  <img src="./pictures/cross.png" alt="" onclick="closeEnterPassword()">
                </div>
                <div class="data">
                  <input type="password" name="password_text" id="password_text" placeholder="Password">
                </div>
                <div class="data">
                  <input type="submit" value="Submit" name="submit_button" class="button">
                </div>
            </div>
        </form>

        <input type="submit" value="Save"  id="save_button" onclick="showEnterPassword()">

        </div>
    </div>
    


<div class="theme-settings" id="theme-settings">
    <!-- ENTER PASSWORD THEME-->
    
   
</div>      <!-- CLOSING NG THEWME -->



<!-- PRODCUTS DIV -->
  <div class="products-container" id="products-container">
                    

<div class="content-container">
    <div class="all">
        <div class="popup-overlay" id="popupOverlay">
            <div class="popup-content">
                <button class="close-btn" onclick="hidepopup()">&times;</button>
                <h1>PRODUCT MANAGEMENT</h1>
                <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
                    <div class="form-row">
                        <label for="name">Product Name:</label>
                        <input type="text" name="name_prod" id="name" required>
                    </div>

                    <div class="form-row">
                        <div class="form-column">
                            <label for="image">Product Image:</label>
                            <input type="file" name="image_prod" id="image" accept=".jpg, .jpeg, .png" required>
                        </div>
                        <div class="form-column">
                            <label for="category">Category:</label>
                            <select name="category_prod" id="category" required>
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
                        <textarea name="description_prod" id="description" rows="4" required></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-column">
                            <label for="qty">Quantity:</label>
                            <input type="number" name="qty_prod" id="qty" required>
                        </div>
                        <div class="form-column">
                            <label for="price">Price:</label>
                            <input type="text" name="price_prod" id="price" required>
                        </div>
                    </div>

                    <button class="addprod" type="submit" name="submit_prod">Add Product</button>
                </form>
            </div>
        </div>

        <!-- Product List Section -->
        <div class="dynamic_flexible">
            <div class="view">
                <h3 class="prod">PRODUCTS</h3>
                <button class="deletebtn" id="add" onclick="showpopup()">Add Product</button>

                <!-- Category Filter Dropdown -->
                <form method="POST" style="margin-left: 25px;">
                    <select name="category_filter_prod">
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
                                    <input type="checkbox" id="delete_<?php echo $product['product_id']; ?>" name="delete_prod[]" value="<?php echo $product['product_id']; ?>" class="delete-checkbox">
                                    <label for="delete_<?php echo $product['product_id']; ?>"></label>
                                </div>
                                <img src="../img/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-image">
                                <h3 class="product-name"><?php echo $product['name']; ?></h3>
                                <p class="product-category"><?php echo $product['category']; ?></p>
                                <button type="button" class="editbtn" onclick="showEditPopup1(<?php echo htmlspecialchars(json_encode($product)); ?>)">Update</button>
                                </div>
                        <?php } ?>
                    </div>
                    <button type="submit" name="delete_selected_prod" id="del" class="deletebtn">Delete Selected</button>
                </form>
            </div>
        </div>
       
        

        <!-- Edit Product Popup -->
        <div class="popup-overlay" id="editPopup">
            <div class="popup-content">
                <button class="close-btn" onclick="hideEditPopup()">&times;</button>
                <h1>Edit Product</h1>
                <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_prod" id="editId">

                    <div class="form-row">
                        <label for="editName">Product Name:</label>
                        <input type="text" name="name_prod" id="editName" required>
                    </div>

                    <div class="form-row">
                        <div class="form-column">
                            <label for="editImage">Product Image:</label>
                            <input type="file" name="image_prod" id="editImage" accept=".jpg, .jpeg, .png">
                        </div>
                        <div class="form-column">
                            <label for="editCategory">Category:</label>
                            <select name="category_prod" id="editCategory" required>
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
                        <textarea name="description_prod" id="editDescription" rows="4" required></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-column">
                            <label for="editQty">Quantity:</label>
                            <input type="number" name="qty_prod" id="editQty" required>
                        </div>
                        <div class="form-column">
                            <label for="editPrice">Price:</label>
                            <input type="text" name="price_prod" id="editPrice" required>
                        </div>
                    </div>

                    <button class="addprod" type="submit" name="updateProduct_prod">Update Product</button>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
            function showEditPopup1(product) {
    console.log(product); // Check the data being passed to the function
    document.getElementById('editId').value = product.product_id; // Ensure product.id is passed correctly
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






                    
  </div>  <!-- CLOSING TAG FOR PRODUCTS -->




  <!-- CCCATEGORRYY -->
<div class="category-container" id="category-container" style="display: none;">
  <?php
    if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $categories = explode(',', $name);

    foreach ($categories as $category) {
        $category = trim($category);
        if (!empty($category)) {
            $query = "INSERT INTO category (category) VALUES('$category')";
            mysqli_query($conn, $query);
        }
    }
    echo "<script>alert('Categories Successfully Added');</script>";
    }

    if (isset($_POST["edit"])) {
    $editCategoryId = $_POST["edit_id"];
    $editCategoryName = mysqli_real_escape_string($conn, $_POST["edit_name"]);

    $oldCategoryQuery = "SELECT category FROM category WHERE id = $editCategoryId";
    $oldCategoryResult = mysqli_query($conn, $oldCategoryQuery);

    if ($oldCategoryResult && mysqli_num_rows($oldCategoryResult) > 0) {
        $oldCategory = mysqli_fetch_assoc($oldCategoryResult)['category'];

        $editQuery = "UPDATE category SET category = '$editCategoryName' WHERE id = $editCategoryId";
        mysqli_query($conn, $editQuery);

        $updateProductQuery = "UPDATE product SET category = '$editCategoryName' WHERE category = '$oldCategory'";
        mysqli_query($conn, $updateProductQuery);

        echo "<script>alert('Category and related products updated successfully.');</script>";
    } else {
        echo "<script>alert('Category not found. Update failed.');</script>";
    }
 }



 if (isset($_POST["delete_selected"])) {
    if (isset($_POST["selected_categories"]) && is_array($_POST["selected_categories"])) {
        foreach ($_POST["selected_categories"] as $selectedCategoryId) {
            $categoryQuery = "SELECT category FROM category WHERE id = $selectedCategoryId";
            $categoryResult = mysqli_query($conn, $categoryQuery);

            if ($categoryResult && mysqli_num_rows($categoryResult) > 0) {
                $categoryName = mysqli_fetch_assoc($categoryResult)['category'];

                $deleteProductsQuery = "DELETE FROM product WHERE category = '$categoryName'";
                mysqli_query($conn, $deleteProductsQuery);

                $deleteCategoryQuery = "DELETE FROM category WHERE id = $selectedCategoryId";
                mysqli_query($conn, $deleteCategoryQuery);
            }
        }
        echo "<script>alert('Selected categories and their associated products deleted successfully.');</script>";
    } else {
        echo "<script>alert('No categories selected for deletion.');</script>";
    }
 }


    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $searchQuery = "SELECT * FROM category WHERE category LIKE '%$searchTerm%'";
    $result = mysqli_query($conn, $searchQuery);
    ?>
 <div class="category-body">
    <div class="adds">


        <div class="popup-overlay" id="popupOverlay1">
            <div class="popup-content">
                <button class="close-btn" onclick="hidepopup1()">&times;</button>
                <h1>CATEGORY MANAGEMENT</h1>
                <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
                    <div class="form-row">
                        <label for="name">Category Name:</label><br>
                        <input type="text" name="name" placeholder="Enter category" required><br><br>
                    </div>

                    <button type="submit" class="add" name="submit" style="background: #d38e40; margin:10px">Add Category</button>
                </form>
            </div>
        </div>


    </div>

    <div>
        <button class="addito" onclick="showpopup1()">Add Category</button>

        <div class="tabl">

            <h1>CATEGORY LIST</h1>



            <h3 class="prod">Category Management</h3>

            <div class="product-cards">
                <!-- <h3 class="prod">Category Management</h3> -->
                <form action="" method="POST">
                    <button type="submit" id="del" name="delete_selected">Delete Selected</button>
                    
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Update</th>
                            <th>Delete</th>
                        </tr>
                        <?php
                        while ($row = mysqli_fetch_assoc($result)) :
                        ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td>
                                    <form action="" method="post">
                                    <div class="edit-name-container">

                                        <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                        <!-- <label for="edit_name">Category Name:</label> -->
                                        <label for="edit_name">Edit Category:</label>

                                        <input type="text" name="edit_name" value="<?php echo $row['category']; ?>" required>
                                        <button type="submit" name="edit" class="update-btn">Update</button>
                                    </form>
                                    </div>


                                </td>
                                <td>
        <div class="checkbox-container">
            <input type="checkbox" id="category_<?php echo $row['id']; ?>" name="selected_categories[]" value="<?php echo $row['id']; ?>">
            <label for="category_<?php echo $row['id']; ?>"></label>
        </div>
        </td>

                            </tr>
                        <?php endwhile; ?>
                    </table>
                </form>
            </div>
        </div>

        <script>
            const popupOverlay = document.getElementById('popupOverlay');

            function showpopup() {
                document.getElementById('popupOverlay').style.display = 'flex';
            }

            function hidepopup() {
                document.getElementById('popupOverlay').style.display = 'none';
            }


            const popupOverlay1 = document.getElementById('popupOverlay1');

            function showpopup1() {
                document.getElementById('popupOverlay1').style.display = 'flex';
            }

            function hidepopup1() {
                document.getElementById('popupOverlay1').style.display = 'none';
            }
        </script>

        <div id="passwordPopup" class="popup1">
            <div class="popup-content1">
                <h2>Enter Admin Password</h2>
                <form method="post" name="deleteForm" action="">
                    <label for="adminPassword">Please enter the admin password to delete account(s).</label>

                    <div class="input-container">
                        <i class="fas fa-lock search-icon"></i> 
                        <input type="password" id="adminPassword" name="adminPassword" placeholder="Enter your password" required>
                    </div>

                    <button type="button" onclick="verifyPassword()">Submit</button>
                </form>

                <div class="cancel-btn">
                    <a href="admin_custo.php">Cancel</a>
                </div>
            </div>
        </div>


        <script>
            function toggleSelectAll(source) {
                const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
                checkboxes.forEach(checkbox => checkbox.checked = source.checked);
            }

            function openPopup() {
                document.getElementById('passwordPopup').style.display = 'flex';
            }

            function closePopup() {
                document.getElementById('passwordPopup').style.display = 'none';
            }

            function verifyPassword() {
                var enteredPassword = document.getElementById('adminPassword').value;  
                var adminPassword = '<?php echo $adminPassword; ?>';  

                if (enteredPassword === adminPassword) {
                    alert("Account has been successfully removed.");
                    document.getElementById('deleteForm').submit();
                } else {
                    alert("Incorrect password. Please try again.");
                }
            }
        </script>
        <div class="gilid">

            <div class="popup-overlay" id="editPopup">
                <div class="popup-content">
                    <button class="close-btn" onclick="hideEditPopup()">&times;</button>
                    <h1>Edit Category</h1>
                    <form action="" method="post">
                        <input type="hidden" name="edit_id" id="editCategoryId">
                        <label for="editCategoryName">Category Name:</label>
                        <input type="text" name="edit_name" id="editCategoryName" required>
                        
                        <button type="submit" name="edit" style="margin-top: 50px;">Update</button>
                    </form>
                </div>
            </div>

        </div>

    </div>
    </body>
    <script>
        function showEditPopup(categoryId, categoryName) {
            
        document.getElementById('editCategoryId').value = categoryId;
        document.getElementById('editCategoryName').value = categoryName;

        document.getElementById('editPopup').style.display = 'flex';
    }

    function hideEditPopup() {
        document.getElementById('editPopup').style.display = 'none';
    }
    </script>

    </div>

</div><!-- CLOSING TAG FOR CATEGORY-->




</div><!-- CLOSING TAG FOR CONTENTS-->
</body>
</html>
