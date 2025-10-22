<?php
// Start the session
include("databaseConnection.php");
include("navigationBar.php");

$themeResult = mysqli_query($conn, "SELECT * FROM theme_table");
    $themeRow = mysqli_fetch_assoc($themeResult);


error_reporting(E_ALL);
ini_set('display_errors', 1);

$email = $_SESSION['emailAddress'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout']) && $_POST['logout'] == 1) {
    session_unset(); 
    session_destroy(); 
    setcookie(session_name(), '', time() - 3600, '/');  
    header("Location: log.php");  
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel= "stylesheet" href="./product_pos.css">

    
    <style>
       
     body{
            background-color: <?php echo $themeRow['backgroundColor'] ?>;
        }
        p{
            color: <?php echo $themeRow['fontColor'] ?> !important;
        }
        .header-font{
            color: <?php echo $themeRow['fontHeader'] ?> !important ;

        }
        button{
            background-color: <?php echo $themeRow['buttonColor'] ?> ;
        }
 </style>
</head>

<body>
    <div class="content-categories">
        <div class="categories-container">
            <?php
            $query = "SELECT * FROM category";
            $result = mysqli_query($conn, $query);

            if (!$result) {
                die("Error in SQL query: " . mysqli_error($conn));
            }

            $firstCategory = '';
            if ($row = mysqli_fetch_assoc($result)) {
                $firstCategory = $row['category'];
            }

            mysqli_data_seek($result, 0);

            $selectedCategory = isset($_GET['category']) ? urldecode($_GET['category']) : $firstCategory;

            while ($row = mysqli_fetch_assoc($result)) {
                $categoryName = $row['category'];
                $categoryLink = "products_pos.php?category=" . urlencode($categoryName) . "&email=" . urlencode($email);

                $isActive = ($selectedCategory === $categoryName) ? "active" : "";

                echo "<a href='$categoryLink' class='$isActive' >
                <i class='fa-solid fa-mug-saucer' style='font-size: 20px; margin-right: 8px;'></i>
                {$categoryName}
              </a>";
            }   
            ?>
        </div>
    </div>

    <div class="grid-items">
        <?php
        $itemsPerPage = 6;

        $filterType = isset($_GET['filter']) ? $_GET['filter'] : 'all';

        switch ($filterType) {
            case 'all':
                $category = isset($_GET['category']) ? urldecode($_GET['category']) : $firstCategory;
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $offset = ($page - 1) * $itemsPerPage;

                if ($category === 'Best Seller') {
                    $query = "
            SELECT p.product_id, p.name, p.image, p.description, p.price, p.qty, SUM(o.order_quantity) AS total_quantity_sold
            FROM product p
            JOIN orders o ON p.product_id = o.product_id
            GROUP BY p.product_id, p.name, p.image, p.description, p.price, p.qty
            ORDER BY total_quantity_sold DESC
            LIMIT 6
            ";
                } else {
                    $query = "SELECT * FROM product WHERE category = '$category' LIMIT $offset, $itemsPerPage";
                }
                break;

            default:
                $category = '';
                $query = '';
                break;
        }

        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Error in SQL query: " . mysqli_error($conn));
        }

        while ($product = mysqli_fetch_assoc($result)) {
            echo '<a href="pos_product_details.php?product_id=' . $product['product_id'] . '" class="product-link" target="details_frame">';
            echo "<div class='card'>";
            echo "<div class='card-img' style='background-image: url(../img/" . str_replace(' ', '%20', $product['image']) . "); background-size: cover;'></div>";
            echo "<div class='card-info'>";
            echo "<p class='text-title'>{$product['name']}</p>";
            echo "<p class='text-body'>{$product['description']}...</p>";
            echo "</div>";
            echo "<div class='card-footer'>";
            echo "<span class='text-title'>₱{$product['price']}</span>";
                echo "<div class='card-button'>";
                echo "<svg class='svg-icon' viewBox='0 0 20 20'>";
                echo "<path d='M17.72,5.011H8.026c-0.271,0-0.49,0.219-0.49,0.489c0,0.271,0.219,0.489,0.49,0.489h8.962l-1.979,4.773H6.763L4.935,5.343C4.926,5.316,4.897,5.309,4.884,5.286c-0.011-0.024,0-0.051-0.017-0.074C4.833,5.166,4.025,4.081,2.33,3.908C2.068,3.883,1.822,4.075,1.795,4.344C1.767,4.612,1.962,4.853,2.231,4.88c1.143,0.118,1.703,0.738,1.808,0.866l1.91,5.661c0.066,0.199,0.252,0.333,0.463,0.333h8.924c0.116,0,0.22-0.053,0.308-0.128c0.027-0.023,0.042-0.048,0.063-0.076c0.026-0.034,0.063-0.058,0.08-0.099l2.384-5.75c0.062-0.151,0.046-0.323-0.045-0.458C18.036,5.092,17.883,5.011,17.72,5.011z'></path>";
                echo "<path d='M8.251,12.386c-1.023,0-1.856,0.834-1.856,1.856s0.833,1.853,1.856,1.853c1.021,0,1.853-0.83,1.853-1.853S9.273,12.386,8.251,12.386z M8.251,15.116c-0.484,0-0.877-0.393-0.877-0.874c0-0.484,0.394-0.878,0.877-0.878c0.482,0,0.875,0.394,0.875,0.878C9.126,14.724,8.733,15.116,8.251,15.116z'></path>";
                echo "<path d='M13.972,12.386c-1.022,0-1.855,0.834-1.855,1.856s0.833,1.853,1.855,1.853s1.854-0.83,1.854-1.853S14.994,12.386,13.972,12.386z M13.972,15.116c-0.484,0-0.878-0.393-0.878-0.874c0-0.484,0.394-0.878,0.878-0.878c0.482,0,0.875,0.394,0.875,0.878C14.847,14.724,14.454,15.116,13.972,15.116z'></path>";
                echo "</svg>";
                echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</a>";
        }
        ?>
    </div>

    <?php
    if ($filterType == 'all') {
        echo "<br> <div class='page'>";
        $totalProductsQuery = "SELECT COUNT(*) AS total FROM product WHERE category = '$category'";
        $totalResult = mysqli_query($conn, $totalProductsQuery);

        if (!$totalResult) {
            die("Error in SQL query: " . mysqli_error($conn));
        }
        $totalProducts = mysqli_fetch_assoc($totalResult)['total'];
        $totalPages = ceil($totalProducts / $itemsPerPage);

        for ($i = 1; $i <= $totalPages; $i++) {
            echo "<a href='?filter=$filterType&category=$category&page=$i&email=$email' class='pagination-link'>$i</a>";
        }

        echo "</div>";
    }

    mysqli_close($conn);
    ?>


</body>

<script>
    function toggleProfile() {
        const profileContainer = document.getElementById("profileContainer");
        profileContainer.classList.toggle("open");
    }

    function confirmLogout() {
        var result = confirm("Are you sure you want to log out?");
        if (result) {
            document.getElementById("logoutForm").submit();
        }
    }

    var slideIndex = 1;
    var autoPlayInterval;
    var isPlaying = true;

    showDivs(slideIndex);
    startAutoPlay();

    function plusDivs(n) {
        showDivs(slideIndex += n);
    }

    function showDivs(n) {
        var i;
        var x = document.getElementsByClassName("mySlides");
        if (n > x.length) {
            slideIndex = 1
        }
        if (n < 1) {
            slideIndex = x.length
        }
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        x[slideIndex - 1].style.display = "block";
    }

    function startAutoPlay() {
        autoPlayInterval = setInterval(function() {
            plusDivs(1);
        }, 3000);
    }

    function stopAutoPlay() {
        clearInterval(autoPlayInterval);
    }

    function togglePlayPause() {
        var button = document.getElementById("playPauseButton");
        if (isPlaying) {
            stopAutoPlay();
            button.innerHTML = "&#9658;";
            button.style.color = "#6f4e37";
            button.style.top = "20px";
        } else {
            startAutoPlay();
            button.innerHTML = "&#10074;&#10074;";
            button.style.color = "#6f4e37";
        }
        isPlaying = !isPlaying;
    }

    function orderNow() {
        window.location.href = "#";
    }

    function login() {
        window.location.href = "#";
    }
</script>

</html>