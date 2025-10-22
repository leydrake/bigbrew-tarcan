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
        <label for="image">Select Image:</label>
        <input type="file" name="image" id="image" required>
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
        $image = $_FILES['image']['tmp_name'];
        $imgContent = addslashes(file_get_contents($image));

        // pag insert
        // $sql = "INSERT INTO IMGHOLDER (NAME, IMAGES) VALUES ('$name', '$imgContent')";
        $sql = "INSERT INTO slide_show (image) VALUES ('$imgContent')";// palitan mo nalang to
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
