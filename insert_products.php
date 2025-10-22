<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
</head>
<body>
    <h1>Upload an Image</h1>
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
        <label for="name">Image Name:</label>
        <input type="text" name="name" id="name" required>
        <br><br>
        <label for="name">Price:</label>
        <input type="number" name="price" id="price" min ="0" required>
        <br><br>
        <label for="image">Select Image:</label>
        <input type="file" name="image" id="image" required>
        <br><br>
        <textarea name="description_txt" id="description_txt">
        </textarea>
        <br>
        <br>
        Categories:
        <select name="categories_select" id="">
            <option value="Coffee">Coffee</option>
            <option value="Milk Tea">Milk Tea</option>
            <option value="Iced Coffee">Iced Coffee</option>
        </select>
        <br><br>
        <input type="submit" name="submit" value="Upload Image">
    </form>

    <?php
    if(isset($_POST['submit'])) {
        // Database connection
        $conn = mysqli_connect('localhost', 'root', '', 'coffeeshopdb'); // palitan mo nalang table name

        if(!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // pag kuha name tsaka img
        $name = $_POST['name'];
        $price = $_POST['price'];
        $image = $_FILES['image']['tmp_name'];
        $imgContent = addslashes(file_get_contents($image));
        $desciption = $_POST['description_txt'];
        $categories = $_POST['categories_select'];

        // pag insert
        // $sql = "INSERT INTO products (name,images,description ) VALUES ('$name', '$imgContent','')";
        $sql = "INSERT INTO products (name,price,description,categories,image) VALUES ('$name','$price', '$desciption','categories','$imgContent')";// palitan mo nalang to
        if(mysqli_query($conn, $sql)) {
            echo "Image uploaded successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        // Close the connection
        mysqli_close($conn);
    }
    ?>
</body>
</html>
