<?php

include "navigationBar.php";
include "databaseConnection.php";
include 'isAdmin.php';



if (isset($_POST['add_slide'])) {

  $userInput = $_POST['password_text'];
  $adminPass = $_SESSION['Passwords'];

    if (!password_verify($userInput, $adminPass)) {
        echo "<script> alert('Wrong password')</script>";
    } else {
    if (isset($_FILES['new_slide']) && $_FILES['new_slide']['error'] == 0) {
      $fileType = $_FILES['new_slide']['type'];
      $fileSize = $_FILES['new_slide']['size'];

      if (strpos($fileType, 'image/') === 0 && $fileSize <= 2000000000000000) { 
        $uploadDir = '../img/';
        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0777, true);
        }

        $filename = $_FILES['new_slide']['name'];
        $fileTmpName = $_FILES['new_slide']['tmp_name'];
        $filePath = $uploadDir . basename($filename);

        if (move_uploaded_file($fileTmpName, $filePath)) {
          $query = "INSERT INTO slides (image_path, theme_id) VALUES ('$filePath', 1)";
          if (mysqli_query($conn, $query)) {
            echo "<script>alert('Slide added successfully!');</script>";
          } else {
            echo "<script>alert('Error adding slide: " . mysqli_error($conn) . "');</script>";
          }
        } else {
          echo "Sorry, there was an error uploading your file.<br>";
        }
      } else {
        echo "<script>alert('Invalid file type or size. Please upload a valid image (max 2MB).');</script>";
      }
    }
  }
  }

if (isset($_POST['update_slide'])) {
  $userInput = $_POST['password_text'];
    $adminPass = $_SESSION['Passwords'];

    if (!password_verify($userInput, $adminPass)) {
        echo "<script> alert('Wrong password')</script>";
    } else {
      $slide_id = $_POST['slide_id'];
      if (isset($_FILES['updated_slide']) && $_FILES['updated_slide']['error'] == 0) {
        $fileType = $_FILES['updated_slide']['type'];
        $fileSize = $_FILES['updated_slide']['size'];

        if (strpos($fileType, 'image/') === 0 && $fileSize <= 2000000) { 
          $uploadDir = '../img/';
          if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
          }

          $filename = $_FILES['updated_slide']['name'];
          $fileTmpName = $_FILES['updated_slide']['tmp_name'];
          $filePath = $uploadDir . basename($filename);

          if (move_uploaded_file($fileTmpName, $filePath)) {
            $query = "UPDATE slides SET image_path = '$filePath' WHERE id = $slide_id";
            if (mysqli_query($conn, $query)) {
              echo "<script>alert('Slide updated successfully!');</script>";
            } else {
              echo "<script>alert('Error updating slide: " . mysqli_error($conn) . "');</script>";
            }
          } else {
            echo "Sorry, there was an error uploading your file.<br>";
          }
        } else {
          echo "<script>alert('Invalid file type or size. Please upload a valid image (max 2MB).');</script>";
        }
      }
    }
}

if (isset($_POST['delete_slide'])) {
  $userInput = $_POST['password_text'];
  $adminPass = $_SESSION['Passwords'];

  if (!password_verify($userInput, $adminPass)) {
      echo "<script> alert('Wrong password')</script>";
  } else {
    $slide_id = $_POST['slide_id']; // Get the slide_id from POST
    $query = "DELETE FROM slides WHERE id = $slide_id"; // Delete the correct slide
    if (mysqli_query($conn, $query)) {
      echo "<script>alert('Slide deleted successfully!');</script>";
    } else {
      echo "<script>alert('Error deleting slide: " . mysqli_error($conn) . "');</script>";
    }
  }
}

$query = "SELECT * FROM slides WHERE theme_id = 1";
$slides_result = mysqli_query($conn, $query);
$slides = mysqli_fetch_all($slides_result, MYSQLI_ASSOC);



//   SA THEME/COLOR BACKGROUND
  // Default settings

  $themeQuery = mysqli_query($conn, "SELECT * FROM theme_table");
  $themeResult = mysqli_fetch_assoc($themeQuery);

  $imageDataLogo = base64_encode($themeResult['currentLogo']);

  $current_navigator_color =   $themeResult['navigatorColor'];
  $current_navigator_font =   $themeResult['navigatorFont'];
  $current_navigator_button = $themeResult['navigatorButton'];

  
  $current_body_color =   $themeResult['backgroundColor'];
  $current_button_color =   $themeResult['buttonColor'];
  $current_font_color =   $themeResult['fontColor'];
  $current_font_header_color =   $themeResult['fontHeader'];

  $default_navigator_color = "#bc6e19";
  $default_navigator_font= "#fffaeb";
  $default_navigator_button = "#fffaeb";


  $default_body_color = "#f4f4f4";
  $default_button_color = "#d38e40";
  $default_font_header_color = "#bc6e19";
  $default_font_color = "#512c1d";


//   NAV CHANGE /////////////////////
if (isset($_POST['nav_save'])) {
  $userInput = $_POST['password_text_nav'];
    $adminPass = $_SESSION['Passwords'];

    if (!password_verify($userInput, $adminPass)) {
        echo "<script> alert('Wrong password')</script>";
    } else {
      $navColor = $_POST['nav_color'];
      $navFont = $_POST['nav_font'];
      $navButton = $_POST['nav_button'];
    
      $query = "UPDATE theme_table SET navigatorColor = '$navColor', navigatorFont = '$navFont', navigatorButton = '$navButton' WHERE id = 1";
      if (mysqli_query($conn, $query)) {
        echo "<script>alert('Theme colors updated successfully!');</script>";
        echo "<script>window.location.href = './theme_settings.php'; </script>";
      } else {
        echo "<script>alert('Error updating theme colors: " . mysqli_error($conn) . "');</script>";

      }
    }
  }
    if (isset($_POST['nav_default'])) {
      $query = "UPDATE theme_table SET navigatorColor = '$default_navigator_color',  navigatorFont = '$default_navigator_font', 
      navigatorButton = '$default_navigator_button'  WHERE id = 1";
    
      if (mysqli_query($conn, $query)) {
        echo "<script>alert('Theme colors reset to default successfully!');</script>";
        echo "<script>window.location.href = './theme_settings.php'; </script>";

      } else {
        echo "<script>alert('Error resetting to default colors: " . mysqli_error($conn) . "'); </script>";
      }
    }
  


  
// GENERAL CHANGE
  if (isset($_POST['general_change'])) {
      $userInput = $_POST['password_text_home'];
      $adminPass = $_SESSION['Passwords'];

      if (!password_verify($userInput, $adminPass)) {
          echo "<script> alert('Wrong password')</script>";
      } else {
      $body_color = $_POST['color_body'];
      $button_color = $_POST['color_button'];
      $header_color = $_POST['color_header'];
      $font_color = $_POST['color_font'];
    
      $query = "UPDATE theme_table SET  backgroundColor = '$body_color', buttonColor = '$button_color', fontHeader = '$header_color', fontColor = '$font_color' WHERE id = 1";
      if (mysqli_query($conn, $query)) {
        echo "<script>alert('Theme colors updated successfully!');</script>";
        echo "<script>window.location.href = './theme_settings.php'; </script>";
      

      } else {
        echo "<script>alert('Error updating theme colors: " . mysqli_error($conn) . "');</script>";
        
      }
    }
  }
  if (isset($_POST['general_default'])) {
    $query = "UPDATE theme_table SET  backgroundColor = '$default_body_color', buttonColor = '$default_button_color', fontColor = '$default_font_color', fontHeader = '$default_font_header_color' WHERE id = 1";
  
    if (mysqli_query($conn, $query)) {
      echo "<script>alert('Theme colors reset to default successfully!');</script>";
      echo "<script>window.location.href = './theme_settings.php'; </script>";

    } else {
      echo "<script>alert('Error resetting to default colors: " . mysqli_error($conn) . "');</script>";
    }
  }
  
  // UPDATE LOGO
  if(isset($_POST['logo_button'])) {
    // Database connection
    // pag kuha name tsaka img
    $userInput = $_POST['password_text_logo'];
    $adminPass = $_SESSION['Passwords'];

    if (!password_verify($userInput, $adminPass)) {
        echo "<script> alert('Wrong password')</script>";
    } else {
    $image = $_FILES['logo_image']['tmp_name'];
    $imgContent = addslashes(file_get_contents($image));

    $sql = "UPDATE theme_table SET currentLogo = '$imgContent' WHERE id = 1";// palitan mo nalang to
    mysqli_query($conn, $sql);
    echo "<script>window.location.href = './admin_settings.php'; </script>";
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Profile</title>
  <link rel="stylesheet" href="theme_settings.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
 
<script>
    function showEnterPassword(){
      document.getElementById("enter-password").style.display = "block";
    }
    function closeEnterPassword(){
      document.getElementById("enter-password").style.display = "none";
    }
    function showEnterPassword2(){
      document.getElementById("enter-password2").style.display = "block";
    }
    function closeEnterPassword2(){
      document.getElementById("enter-password2").style.display = "none";
    }
    
    function showEnterPassword3(){
      document.getElementById("enter-password3").style.display = "block";
    }
    function closeEnterPassword3(){
      document.getElementById("enter-password3").style.display = "none";
    }
    
    function showEnterPasswordUpdate(slideId) {
    document.getElementById(`enter-password-update-${slideId}`).style.display = "block";
    }

    function closeEnterPasswordUpdate(slideId) {
        document.getElementById(`enter-password-update-${slideId}`).style.display = "none";
    }
        function showEnterPasswordAdd() {
        document.getElementById(`enter-password-add`).style.display = "block";
    }

    function closeEnterPasswordAdd() {
        document.getElementById(`enter-password-add`).style.display = "none";
    }

    function showEnterPasswordDelete(slideId) {
        document.getElementById(`enter-password-delete-${slideId}`).style.display = "block";
    }

    function closeEnterPasswordDelete(slideId) {
        document.getElementById(`enter-password-delete-${slideId}`).style.display = "none";
    }

 
</script>
</head>

<body>

  <div class="slides-container">
    <?php foreach ($slides as $slide): ?>
      <div class="slide-card">
        <img src="<?php echo $slide['image_path']; ?>" alt="Slide Image" class="slide-image">
        <form action="" method="post" enctype="multipart/form-data" class="update-form">
          <input type="hidden" name="slide_id" value="<?php echo $slide['id']; ?>">
          <label for="updated_slide_<?php echo $slide['id']; ?>">Update Image:</label>
          <input type="file" id="updated_slide_<?php echo $slide['id']; ?>" name="updated_slide" accept="image/*" required>

            <div class="enter-password" id="enter-password-update-<?php echo $slide['id']; ?>">
                  <div class="text-container">
                    <h3>Enter Admin Password</h3>
                    <img src="./pictures/cross.png" alt="" onclick="closeEnterPasswordUpdate(<?php echo $slide['id']; ?>)">
                    </div>
                  <div class="data">
                    <input type="password" name="password_text" id="password_text" placeholder="Password">
                  </div>
                  <div class="data">
                   <button type="submit" name="update_slide" class="btn-update">Confirm</button>

                  </div>
              </div>

          <div class="buttons">

          <button type="button" onclick="showEnterPasswordUpdate(<?php echo $slide['id']; ?>)" class="btn-update">Update Slide</button>
          </div>

        </form>

        <form action="" method="post" class="delete-form">
  <input type="hidden" name="slide_id" value="<?php echo $slide['id']; ?>"> <!-- Ensure this holds the correct slide_id -->
  <button type="button" onclick="showEnterPasswordDelete(<?php echo $slide['id']; ?>)"  class="btndel" >Delete Slide</button>

  <!-- Password entry -->


  <div class="enter-password" id="enter-password-delete-<?php echo $slide['id']; ?>" style="display: none;">
    <div class="text-container">
        <h3>Enter Admin Password</h3>
        <img src="./pictures/cross.png" alt="" onclick="closeEnterPasswordDelete(<?php echo $slide['id']; ?>)">
        </div>
    <div class="data">
        <input type="password" name="password_text" id="password_text_<?php echo $slide['id']; ?>" placeholder="Password">
    </div>
    <div class="data">
        <button type="submit" name="delete_slide" class="btn-update">Confirm</button>
    </div>
</div>


</form>


  </div>
<?php endforeach; ?>

<div class="addslide"><br><br><br><br><br>
    <form action="" method="post" enctype="multipart/form-data">
      <label for="new_slide" class="addnew">
        <h2>Add New Slide:</h2>
      </label>
      <input type="file" id="new_slide" name="new_slide" accept="image/*" required>

      <div id="imagePreviewContainer"></div>
      <input class="btn" type="button" onclick="showEnterPasswordAdd()" value="Add Slide" >

                  <!-- ADDD -->
              <div class="enter-password" id="enter-password-add" style="position: absolute; ">
                  <div class="text-container" >
                    <h3>Enter Admin Password</h3>
                    <img src="./pictures/cross.png" alt="" onclick="closeEnterPasswordAdd()">
                  </div>
                  <div class="data">
                    <input type="password" name="password_text" id="password_text" placeholder="Password">
                  </div>
                  <div class="data">
                  <button type="submit" name="add_slide" class="btndel">Delete Slide</button>

                  </div>
              </div>
      



              <!-- DELETE
              <div class="enter-password" id="enter-password-delete" style="position: absolute; ">
                  <div class="text-container" >
                    <h3>Enter Admin Password</h3>
                    <img src="./pictures/cross.png" alt="" onclick="closeEnterPasswordDelete()">
                  </div>
                  <div class="data">
                    <input type="password" name="password_text" id="password_text" placeholder="Password">
                  </div>
                  <div class="data">
                  <button type="submit" name="delete_slide" class="btndel">Delete Slide</button>

                  </div>
              </div> -->
    </form>
  </div>
</div>

<script>
  let slideIndex = 0;
  showSlides();

  function showSlides() {
    let i;
    const slides = document.getElementsByClassName("mySlides");
    const dots = document.getElementsByClassName("dot");
    for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
    }
    slideIndex++;
    if (slideIndex > slides.length) {
      slideIndex = 1
    }
    for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
    }
    slides[slideIndex - 1].style.display = "block";
    dots[slideIndex - 1].className += " active";
    setTimeout(showSlides, 3000);
  }

  function currentSlide(n) {
    slideIndex = n - 1;
    showSlides();
  }
</script>
<br><br>



<a href="admin_settings.php" style="left: 50px; top: 180px; position:absolute;"> <!-- Arrow Link to Homepage -->
        <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 12H4" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 18L4 12L10 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

   
<!-- SA MGA COLORS AND LOGOS -->
  <div class="theme-wrapper">
    
  
    <div class="navigator-section">
            <h3>Navigation Theme</h3><br><br>
            <form  action="<?php $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data" method="post">
                <div class="navi-color">
                <label for="color_navi" id="nav_label">Navigator Color</label>  <br>
                <input type="color" id="nav_color" name="nav_color" value="<?php echo $current_navigator_color; ?>">
                </div>
                <div class="font-color">
                <label for="color_body">Nav Links</label>
                <input type="color" id="nav_font" name="nav_font" value="<?php echo $current_navigator_font?>">
                </div>
                <div class="button-color">
                <label for="color_body">Button Color:</label>
                <input type="color" id="nav_button" name="nav_button" value="<?php echo $current_navigator_button ?>">
                </div>
                <input class="btn" type="button" value="Save" name="nav_save" id="nav_save" onclick="showEnterPassword()">
                <input class="btn" type="submit" value="Default" name="nav_default" id="nav_default">


                <div class="enter-password" id="enter-password">
                  <div class="text-container" >
                    <h3>Enter Admin Password</h3>
                    <img src="./pictures/cross.png" alt="" onclick="closeEnterPassword()">
                  </div>
                  <div class="data">
                    <input type="password" name="password_text_nav" id="password_text" placeholder="Password">
                  </div>
                  <div class="data">
                    <input type="submit" value="Submit" name="nav_save" class="button">
                  </div>
              </div>



            </form>
        </div>


        <div class="home-section">
            <h3> General Theme</h3> <br><br>
          <form name="color_theme" action="<?php $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data" method="post" onsubmit="return showEnterPasswordTheme()">
          <div class="body-color">
              <label for="color_body">Background Color:</label><br>
              <input type="color" id="color_body" name="color_body" value="<?php echo $current_body_color; ?>"> 
            </div>
            <div class="header-color">
              <label for="color_body">Headings</label>
              <input type="color" id="color_header" name="color_header" value="<?php  echo  $current_font_header_color; ?>">
            </div>
            <div class="font-color">
              <label for="color_body">Subtext</label>
              <input type="color" id="color_font" name="color_font" value="<?php echo     $current_font_color ?>">
            </div>
            <div class="button-color">
              <label for="color_button">Buttons</label>
              <input type="color" id="color_button" name="color_button" value="<?php echo $current_button_color; ?>">
            </div>
            <div class="buttons">
              <input class="btn" type="button" value="Save"  id="general_change" onclick="showEnterPassword2()">
              <input class="btn" type="submit" value="Default" name="general_default" id="general_default">
            </div>


            <div class="enter-password" id="enter-password2">
                <div class="text-container" >
                  <h3>Enter Admin Password</h3>
                  <img src="./pictures/cross.png" alt="" onclick="closeEnterPassword2()">
                </div>
                <div class="data">
                  <input type="password" name="password_text_home" id="password_text" placeholder="Password">
                </div>
                <div class="data">
                  <input type="submit" value="Submit" name="general_change" class="button">
                </div>
            </div>



          </form>
        </div>



        <div class="change-logo">
            <form action="<?php $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data" method="post" onsubmit="return showEnterPasswordTheme()">
              <div class="logo-container">
                <img  src="data:image/png;base64,<?php echo $imageDataLogo; ?>" />
              </div>

            <input type="file" name="logo_image" id="image" required >
            <input type="button" name="logo_button" value="Upload Image" id="logo_save" onclick="showEnterPassword3()">


            <div class="enter-password" id="enter-password3">
                <div class="text-container" >
                  <h3>Enter Admin Password</h3>
                  <img src="./pictures/cross.png" alt="" onclick="closeEnterPassword3()">
                </div>
                <div class="data">
                  <input type="password" name="password_text_logo" id="password_text" placeholder="Password">
                </div>
                <div class="data">
                  <input type="submit" value="Submit" name="logo_button"  class="button">
                </div>
            </div>

            </form>
        </div>

  </div> <!-- theme wrapper -->

<!-- PASSWORD -->
 


  

</body>

</html>

<?php
mysqli_close($conn);
?>