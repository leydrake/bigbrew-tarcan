<?php
    include("databaseConnection.php");
    include("navigationBar.php");

    
    $sqlCount = "SELECT COUNT(*) as totalSlides FROM slides";
    $resultCount = mysqli_query($conn, $sqlCount);
    $rowCount = mysqli_fetch_assoc($resultCount);
    $totalSlides = $rowCount['totalSlides'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bigbrew Tarcan</title>
    <link rel="icon" type="image/x-icon" href="./pictures/logo white.png">
    <link rel="stylesheet" href = "./index1.css">
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
<script>
    function goRegister(){
        window.location.href ="./register.php";
    }

    let slideId = 1; // Initialize the first slide ID
    const totalSlides = <?php echo $totalSlides; ?>; // Total number of slides (update this number as needed)

    function showSlide(slideId) {
        // Hide all slides
        const slides = document.querySelectorAll('.slide');
        slides.forEach(slide => slide.style.display = 'none');

        // Show the selected slide
        const selectedSlide = document.getElementById(`slider${slideId}`);
        if (selectedSlide) {
            selectedSlide.style.display = 'block';
        }
    }

    // Auto slide function
    function autoSlide() {
        showSlide(slideId);  // Show the current slide
        slideId++;  // Move to the next slide
        if (slideId > totalSlides) {
            slideId = 1;  // Reset to the first slide after the last one
        }
    }

    // Start the auto slide show
    setInterval(autoSlide, 5000);  // Change slide every 3 seconds
</script>
</script>
<body>
    <center>
        <div class="content-container">
        
        <div class="slider-wrapper">
                <div id="slider" class="slider">
                    <?php 
                    $sql = "SELECT * FROM slides";
                    $result = mysqli_query($conn, $sql);
                    $slideId = 1;

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $slide = $row;
                    ?>
                            <div class="slide" id="slider<?php echo $slideId; ?>" style="display: <?php echo $slideId === 1 ? 'block' : 'none'; ?>;">
                                <br>
                                <img src="<?php echo substr($slide['image_path'],1); ?>" alt="slide" class="slide-image">
                            </div>
                    <?php
                            $slideId++;
                        }
                    } else {
                        echo "No images found.";
                    }
                    ?>
                </div>

                
                <div class="slider-nav">
                    <?php for ($i = 1; $i <= $totalSlides; $i++) { ?>
                        <a href="#" onclick="showSlide(<?php echo $i; ?>)"></a>
                    <?php } ?>
                </div>
            </div>



                <!-- AFTER SLIDER -->


            <div class="coffee-section">
                <img src="./pictures/half border.png" alt="" class="half-border">
                <img src="./pictures/hot brew.jpg" alt="" class="coffee-image">
                <h1 class="header-font">BIG IN TASTE</h1>
                <p>    
                Bigbrew started out as a small business, and their unique flavor has made a bigimpact on our customers. Because the Filipinos are known as “coffee lovers” andbecause milktea was influenced by other countries. The owner of Bigbrew createdvarious types and blends of milktea that our customers love it so much that theykeep coming back again.
                <br>
                <br>
                Our beans are carefully selected from local farms, allowing us to support sustainable agriculture while delivering a cup that’s as fresh as it is flavorful.
                Perfect for the modern coffee lover, BigBrew offers a variety of roasts to suit every taste. From the bold and full-bodied to the bright and aromatic, we’re here to elevate your daily coffee ritual.
                Join us in celebrating the best of Filipino coffee – BigBrew, where local roots meet global flavor.</p>
            </div>

            <!-- milktea section -->

            <div class="milktea-section">
                <img src="./pictures/hald border2.png" alt="" class="half-border2">
                <img src="./pictures/milk tea 2.jpg" alt="" class="milktea-image">
                <h1 class="header-font">BIT IN PRICE</h1>
                <p>
                Bigbrew's   success can be   attributed to its ability  to adapt its  menu  andofferings to meet local tastes while maintaining core Filipino flavors. This strategyhas   been   really   important   for   us   to   get   into   new   markets   and   reach   out   todifferent types of customers    
                <br>
                <br>
                The Bigbrew company also embarks on social responsibility programs. The bigbrew must be able to provide the desired flavors of the customers in order andfollow it to make the customer satisfied in their services
                 </p>

            </div>

            <div class="multi-pictures">
                 <img src="./pictures/ice candy milk tea.jpg" alt="" class="multi-pic1">
                 <img src="./pictures/moca praf.jpg" alt="" class="multi-pic2">
                 <img src="./pictures/diving cookies.png" alt="" class="multi-pic3">
                 <img src="./pictures/cookies and cream praf.jpg" alt="" class="multi-pic4">
                 <img src="./pictures/hotbrew ice cubes.jpg" alt="" class="multi-pic5">
                <!--  <img src="./pictures/coffee jelly.jpg" alt="" class="multi-pic6"> -->
            </div>
        </div>
    </center>
    
</body>
</html>