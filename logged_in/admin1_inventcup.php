<?php
include("navigationBar.php");

include("databaseConnection.php");

if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $categories = explode(',', $name);

    foreach ($categories as $category) {
        $category = trim($category);
        if (!empty($category)) {
            $query = "INSERT INTO categ_cupsize (category) VALUES('$category')";
            mysqli_query($conn, $query);
        }
    }

    echo "<script>alert('Categories Successfully Added');</script>";
}

if (isset($_POST["edit"])) {
    $editCategoryId = $_POST["edit_id"];
    $editCategoryName = mysqli_real_escape_string($conn, $_POST["edit_name"]);

    $oldCategoryQuery = "SELECT category FROM categ_cupsize WHERE id = $editCategoryId";
    $oldCategoryResult = mysqli_query($conn, $oldCategoryQuery);

    if ($oldCategoryResult && mysqli_num_rows($oldCategoryResult) > 0) {
        $oldCategory = mysqli_fetch_assoc($oldCategoryResult)['category'];

        $editQuery = "UPDATE categ_cupsize SET category = '$editCategoryName' WHERE id = $editCategoryId";
        mysqli_query($conn, $editQuery);

        $updateProductQuery = "UPDATE invent_cupsize SET category = '$editCategoryName' WHERE category = '$oldCategory'";
        mysqli_query($conn, $updateProductQuery);

        echo "<script>alert('Category and related products updated successfully.');</script>";
    } else {
        echo "<script>alert('Category not found. Update failed.');</script>";
    }
}

if (isset($_POST["delete_selected"])) {
    if (isset($_POST["selected_categories"]) && is_array($_POST["selected_categories"])) {
        foreach ($_POST["selected_categories"] as $selectedCategoryId) {
            $categoryQuery = "SELECT category FROM categ_cupsize WHERE id = $selectedCategoryId";
            $categoryResult = mysqli_query($conn, $categoryQuery);

            if ($categoryResult && mysqli_num_rows($categoryResult) > 0) {
                $categoryName = mysqli_fetch_assoc($categoryResult)['category'];

                $deleteProductsQuery = "DELETE FROM invent_cupsize WHERE category = '$categoryName'";
                mysqli_query($conn, $deleteProductsQuery);

                $deleteCategoryQuery = "DELETE FROM categ_cupsize WHERE id = $selectedCategoryId";
                mysqli_query($conn, $deleteCategoryQuery);
            }
        }
        echo "<script>alert('Selected categories and their associated products deleted successfully.');</script>";
    } else {
        echo "<script>alert('No categories selected for deletion.');</script>";
    }
}

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchQuery = "SELECT id, category, product_count FROM categ_cupsize WHERE category LIKE '%$searchTerm%'";
$result = mysqli_query($conn, $searchQuery);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>CATEGORY</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;700;900&display=swap');

*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Montserrat";
    
}
     body {
            margin: 0;
            padding: 0;
        }

        .navigator {
            position: absolute;
            left: 0;
            width: 100%;
            height: 60px;
            background-color: #d38e40;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

    .navigator .nav-item {
        color: #fff;
        font-size: 18px;
        font-weight: bold;
        margin: 0 40px;
        position: relative;
        left: 400px;
        text-decoration: none;
    }

    .navigator .nav-item:hover {
        color: #d3ad7f;
    }

   

    .content-container {
        margin-left: -430px;
        position: relative;
        top: -20px;
        padding: 20px;
    }

    

    .content-container {
        margin-left: 350px;
        padding: 20px;
    }

    table {
        width: 900px;
        border-collapse: collapse;
        margin: 10px auto;
        margin-top: 100px;
        font-size: 17px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    table th,
    table td {
        padding: 12px;
        text-align: center;
        font-size: 17px;

        border: 1px solid #ddd;

    }

    table td {
        padding: 12px;
        text-align: center;
        border: 1px solid #ddd;
        font-size: 20px;

    }

    table th {
        background-color: #d38e40;
        color: #fff;
        text-align: center;
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    table td {
        color: #d38e40;
    }

    table td .update-btn {
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 100px 15px;
        cursor: pointer;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease, transform 0.2s ease;
    }


    


    .del {
        background-color: #d38e40;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
        margin: 20px auto;
        display: block;
        margin-top: -30px;
        margin-right: 10px;
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
        z-index: 1000;
    }

    .popup-content {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
    }

    .popup-content input,
    .popup-content button {
        margin: 10px;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .product-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
        padding: 20px;
        padding-top: 50px;
        padding-right: 335px;
        margin-top: -75px;
        position: relative;
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            background-color: #f9f9f9;
            border-radius: 10px;
    }

    .prod {
        font-size: 28px;
        text-align: center;
        font-weight: bold;
        color: #d38e40;
        position: absolute;
        top: 80px;
        z-index: 200;

        border-radius: 5px;
        padding: 15px;
        margin: 0px 20px;
        background-color: #e2dcd7;
        margin-top: 10px;
        margin-bottom: 70px;
        /* top: 20px; */
        position: relative;
    }

    .tabl {
        position: relative;
        left: 430px;
        top: 90px;
        width: 940px;

    }

    .gilid {
        position: absolute;
        left: 500px;
    }

    #del {
        position: absolute;
        top: 80px;
        margin-top: 20px;
        background-color: #d38e40;
        color: white;
        border: none;
        padding: 10px 20px;
        left: 760px;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .add {
        width: 330px;
        position: absolute;
    }

    .adds {
        position: absolute;
        left: 200px;
        /* top: -359px; */
        margin-top: 20px;
        z-index: 10000;
    }

    .addw {
        position: absolute;
        /* left: 470px; */

        z-index: 100px;
        margin-top: 20px;
        background-color: #d38e40;
        width: 200px;
        color: white;
        border: none;
        padding: 10px 20px;
        left: 470px;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .addito {
        position: absolute;
        top: 438px;
        /* left: 470px; */

        z-index: 1;
        margin-top: 20px;
        background-color: #d38e40;
        width: 200px;
        color: white;
        border: none;
        padding: 10px 23px;
        left: 970px;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
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
        width: 23%;
        height: 180px;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        position: relative;
        text-align: center;
    }

    .popup-content h1 {
        margin-top: 0;
    }


    .popup-content h1 {
        margin: 0 0 20px;
        font-size: 24px;
        color: #d38e40;
        text-align: center;
        text-transform: uppercase;
    }

    .popup-content .close-btn {
        position: absolute;
        top: -10px;
        right: -5px;
        background: none;
        border: none;
        font-size: 30px;
        cursor: pointer;
    }

    .popup-content .close-btn:hover {
        color: red;
    }

    .popup-content form label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        font-size: 15pt;
        color: #d38e40;
    }

    .popup-content form button[type="submit"] {
        background-color: #d38e40;
        color: #fff;
        border: none;
        padding: 10px 20px;
        margin-top: -90px;
        border-radius: 5px;
        font-size: 16px;
        position: relative;
        top: -30px;
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


    .popup1 {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .popup-content1 {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 400px;
        text-align: center;
    }

    .popup-content1 input {
        width: 100%;
        padding: 10px;
        /* margin-bottom: 10px; */
    }

    .popup-content1 button {
        background-color: #d38e40;
        color: white;
        border: none;
        padding: 10px;
        /* margin-bottom: 10px; */
        border-radius: 5px;
        width: 100%;
        cursor: pointer;
    }

    .popup-content1 button {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        background-color: #d38e40;
        color: white;
        border: none;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .popup-content1 button:hover {
        background-color: #d3ad7f;
    }

    .popup-content1 .cancel-btn a {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 10px;
        background-color: #f0f0f0;
        width: 100%;
        color: #d38e40;
        text-decoration: none;
        font-size: 16px;
        text-align: center;
        transition: background-color 0.3s ease;
    }

    .popup-content1 h2 {
        font-size: 28px;
        color: #d38e40;
        margin-bottom: 20px;
    }

    .popup-content1 label {
        font-size: 23px;
        color: #d38e40;
        font-weight: bold;
        margin-bottom: 10px;
        display: block;
    }

    .popup-content1 .input-container {
        position: relative;
        width: 100%;
    }

    .popup-content1 input[type="password"] {
        width: 328px;
        padding: 12px 35px;
        border-radius: 10px;
        border: 1px solid #d38e40;
        margin-bottom: 15px;
        font-size: 16px;
        background-color: rgba(255, 255, 255, 0.9);
        color: #595037;
        transition: 0.3s ease;
    }

    .popup-content1 input[type="password"]:focus {
        border-color: #4e3629;
        box-shadow: 0 0 5px #4e3629;
        background-color: rgba(255, 255, 255, 1);
    }

    .popup-content1 .cancel-btn a:hover {
        background-color: #d8c4a3;
    }

    .popup-content1 .cancel-btn {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }


    .popup-content1 .search-icon {
        position: absolute;
        left: 10px;
        top: 22px;
        transform: translateY(-50%);
        color: #d38e40;
        font-size: 18px;
    }

    table td .update-btn {
        background-color: #d38e40;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 10px 10px;
        cursor: pointer;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
x
    .edit-name-container {
        margin: 5px 0;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .edit-name-container label {
        font-size: 16px;
        color: #d38e40;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .edit-name-container input[type="text"] {
        width: 100%;
        max-width: 200px;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .edit-name-container input[type="text"]:focus {
        border-color: #d38e40;
        box-shadow: 0 0 5px #d38e40;
    }

    .checkbox-container {
        display: flex;
        align-items: center;
        cursor: pointer;
        position: relative;
        justify-content: center;
    }

    .checkbox-container input[type="checkbox"] {
        display: none;
    }

    .checkbox-container label {
        font-size: 14px;
        color: #d38e40;
        font-weight: bold;
        padding-left: 25px;
        position: relative;
        cursor: pointer;
    }

    .checkbox-container label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 15px;
        height: 15px;
        border: 2px solid #d38e40;
        border-radius: 4px;
        background: #fff;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .checkbox-container input[type="checkbox"]:checked+label::before {
        background: #d38e40;
        border-color: #d38e40;
    }

    .checkbox-container input[type="checkbox"]:checked+label::after {
        content: '✔';
        position: absolute;
        left: 5px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        color: #fff;
    }
    .categ{
        position: relative;
    }
    .categ {
        top: 60px;
        left: 0;
        width: 100%;
        height: 60px;
        background-color: #ebbc74;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .categ .nav-item {
        color: white;
        font-size: 23px;
        font-weight: bold;
        margin: 0 40px;
        position: relative;
        left: 120px;
        text-decoration: none;
    }

    .categ .nav-item:hover {
        color: #d3ad7f;
    }
</style>

<body>


<div class="navigator">
        <a href="admin1_cupsize.php" class="nav-item">Coffee Cup</a>
        <a href="admin1_addson.php" class="nav-item">Add-Ons</a>
        <a href="admin1_inventcateg.php" class="nav-item">Category</a>
        <!-- <a href="admin_addAcc.php" class="nav-item">Add Customer</a> -->
    </div>

    <div class="categ">
    <a href="admin1_inventcup.php" class="nav-item">Coffee Cup</a>
        <a href="admin1_inventcateg.php" class="nav-item">Add-Ons</a>
        <!-- <a href="admin1_inventflavor.php" class="nav-item">Flavors</a> -->
        <!-- <a href="admin_addAcc.php" class="nav-item">Add Customer</a> -->
    </div>
    <a href="admin_settings.php" style="left: 80px; top: 250px; position:absolute;"> <!-- Arrow Link to Homepage -->
        <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 12H4" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 18L4 12L10 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

   

    

    <div class="adds">
        <div class="popup-overlay" id="popupOverlay">
            <div class="popup-content">
                <button class="close-btn" onclick="hidepopup()">&times;</button>
                <h1>FLAVORS MANAGEMENT</h1>
                <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
                    <div class="form-row">
                        <label for="name">Category Name:</label><br>
                        <input type="text" name="name" placeholder="Enter category" required><br><br>
                    </div>

                    <button type="submit" class="add" name="submit">Add Flavor</button>
                </form>
            </div>
        </div>
    </div>

    <div>
        <button class="addito" onclick="showpopup()">Add Flavor</button>

        <div class="tabl">

            <!-- <h1>CATEGORY LIST</h1> -->

            <h3 class="prod">Flavors Management</h3>

            <div class="product-cards">
                <form action="" method="POST">
                    <button type="submit" id="del" name="delete_selected">Delete Selected</button>

                    <table>
                        <tr>
                            <!-- <th>ID</th> -->
                            <th>Name</th>
                            <th>Update</th>
                            <th>Delete</th>
                        </tr>
                        <?php
                        while ($row = mysqli_fetch_assoc($result)) :
                        ?>
                            <tr>
                                <!-- <td><?php echo $row['id']; ?></td> -->
                                <td><?php echo $row['category']; ?></td>
                                <td>
                                    <form action="" method="post">
                                        <div class="edit-name-container">
                                            <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                            <label for="edit_name">Edit Coffee Cup:</label>
                                            <input type="text" name="edit_name" value="<?php echo $row['category']; ?>" required>
                                            <button type="submit" name="edit" class="update-btn">Update</button>
                                    </form>
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
            <br>
        </div>
    </div>
    <script>
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

    <div id="passwordPopup" class="popup1">
        <div class="popup-content1">
            <h2>Enter Admin Password</h2>
            <form method="post" name="deleteForm" action="">
                <label for="adminPassword">Please enter the admin password to delete account(s).</label>

                <div class="input-container">
                    <i class="fas fa-lock search-icon"></i> <!-- Search icon -->
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
                    <button type="submit" name="edit">Update</button>
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

</html>