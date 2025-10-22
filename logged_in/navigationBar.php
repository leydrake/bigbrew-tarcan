<?php
    include("databaseConnection.php");
    session_start();

    $result = mysqli_query($conn, "SELECT * FROM login_credentials WHERE emailAddress = '$_SESSION[emailAddress]'");
    $row = mysqli_fetch_assoc($result);

    $_SESSION ['customer_id'] = $row["customer_id"];
    $_SESSION ['firstName'] = $row["firstName"];
    $_SESSION ['lastName'] = $row["lastName"];
    $_SESSION ['emailAddress'] = $row["emailAddress"];
    $_SESSION ['Phone'] = $row["Phone"];
    $_SESSION ['Address'] = $row["Address"];
    $_SESSION ['Address2'] = $row["Address2"];
    $_SESSION ['Passwords'] = $row["Passwords"];
    $_SESSION ['Avatar'] = $row["avatar"];
    $_SESSION ['Verified'] = $row ['Verified'];

    if($_SESSION ['emailAddress']== null){
       echo "<script> alert('Please sign in first.');
            window.location.href = '../login.php'; </script>
       ";
    }

    $firstName1 = $_SESSION ['firstName'];
    $lastName1 =  $_SESSION ['lastName'];
    $phone1 = $_SESSION ['Phone'];
    $address1 = $_SESSION ['Address'];
    $address2 = $_SESSION ['Address2'];


    // pag nag update ng information button
    if(isset($_POST['update_button'])){
        $firstName = $_POST['firstName_txt'];
        $lastName= $_POST['lastName_txt'];
        $phone= $_POST['Phone_txt'];
        $address = $_POST['Address_txt'];
        $address2= $_POST['Address_txt2'];
        $email = $_SESSION['emailAddress'];

        if(!isset($firstName) || empty($firstName)){
            $firstName = $_SESSION['firstName'];
        }
        if(!isset($lastName) || empty($lastName)){
            $lastName = $_SESSION['lastName'];
        }
        if(!isset($phone) || empty($phone)){
            $phone = $_SESSION['Phone'];
        }
        if(!isset($address) || empty($address)){
            $address = $_SESSION['Address'];
        }
        if(!isset($address2) || empty($address2)){
            $address2 = $_SESSION['Address2'];
        }
        
        $sql = "UPDATE login_credentials SET firstName = '$firstName' ,lastName = '$lastName', Phone = '$phone', 
        Address = '$address', Address2 = '$address2' WHERE emailAddress = '$email'";
        mysqli_query($conn, $sql);
        $_SESSION ['firstName'] = $firstName;
        $_SESSION ['lastName']  = $lastName;
        $_SESSION ['Phone']  = $phone;
        $_SESSION ['Address']  = $address;
        $_SESSION ['Address2']  = $address2;
    
        echo "<script> alert('Information successfully updated');
        window.location.href = './index.php' </script>";
    }
    // change password  
    if(isset($_POST['change_password'])){
        $current = $_POST['currentPass'];
        $newPass = $_POST['newPass'];
        $confirm = $_POST['confirmPass'];
        $email = $_SESSION ['emailAddress'];
        
        if(password_verify($current,$_SESSION ['Passwords'])){
            if(strlen($newPass) <8){
                echo "<script>  alert('Password lenght must be at least 8 characters'); </script>";

            }else if($newPass!= $confirm){
                echo "<script>  alert('New and confirm password did not match'); </script>";
            }else{
                $hashed = password_hash($newPass, PASSWORD_DEFAULT);
                $sql = "UPDATE login_credentials SET Passwords = '$hashed'  WHERE emailAddress = '$email'";
                mysqli_query($conn, $sql);
                echo "<script>  alert('Password changed successfully' ); </script>";
                $_SESSION ['Passwords']  = $newPass;
                echo "<script> window.location.href = './index.php' </script>";

            }
        }else{
            echo "<script>  alert('Password did not match'); </script>";

        }
    }

    //log out
    if (isset($_POST['confirm_logout'])) {
        session_destroy();
        session_unset();
        
        // Prevent caching of the page
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        // Redirect to login page
        header("location: ../login.php");
        exit();
    }
        // sa pag upload ng profile pciture
    if (isset($_FILES['fileImg']['name'])){
        $result = mysqli_query($conn, "SELECT * FROM login_credentials WHERE emailAddress = '$_SESSION[emailAddress]'");
        $row = mysqli_fetch_assoc($result);
        
        $src = $_FILES['fileImg']['tmp_name'];
        $imageName = uniqid() . $_FILES['fileImg']['name'];
        $target = "avatars/" . $imageName;
        if (move_uploaded_file($src, $target)) {
            $query = "UPDATE login_credentials SET avatar = '$imageName' WHERE emailAddress = '$_SESSION[emailAddress]'";
            mysqli_query($conn, $query);
    
            echo "<script> alert('Success') </script>";
            header("location: index.php");
        } else {
            echo "<script> alert('File upload failed') </script>";
        }
    }



    // SA THEME COLOR
    $themeResult = mysqli_query($conn, "SELECT * FROM theme_table");
    $themeRow = mysqli_fetch_assoc($themeResult);

    $imageDataLogo = base64_encode($themeRow['currentLogo']);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script>

        function goToHome(){
            window.location.href = "./index.php";
        }
        function goToSignin(){
            window.location.href = "./login.php";
        } 
        function goToProducts(){
            
        }                        
        function goToAbout(){
            
        }
        function goToContact(){
            
        }
        function goToAdmin(){
            window.location.href = "./admin_settings.php";
        }
        function goToHistory(){
            window.location.href = "./customerhistory.php";
        }
        function processForm(event) {
            event.preventDefault();

            const currentFirstName = "<?php echo $_SESSION['firstName']; ?>";
            const currentLastName = "<?php echo $_SESSION['lastName']; ?>";
            const currentPhone = "<?php echo $_SESSION['Phone']; ?>";
            const currentAddress = "<?php echo $_SESSION['Address']; ?>";
            const currentAddress2 = "<?php echo $_SESSION['Address2']; ?>";

            const firstName = document.getElementById("firstName").value;
            const lastName = document.getElementById("lastName").value;
            const phone = document.getElementById("Phone").value;
            const address = document.getElementById("Address").value;
            const address2 = document.getElementById("Address2").value;

            let formChanged = (
                firstName !== currentFirstName ||
                lastName !== currentLastName ||
                phone !== currentPhone ||
                address !== currentAddress ||
                address2 !== currentAddress2
            );

            if (!formChanged) {
                alert("No changes detected. Information remains the same.");
                return;
            }

            // Enable all inputs to ensure they are submitted
            document.querySelectorAll("#profileForm input:disabled").forEach(input => {
                input.removeAttribute("disabled");
            });

            // Submit the form
            console.log("Submitting form...");
            
            document.getElementById("profileForm").submit();
            document.getElementById("update_button").value = true;
        }

        // CONFIRM LOGOUT
        function confirmLogout(){
            if (confirm("Are you sure you want to log out?") == true) {
                 return true;
            } else {
                 return false;
             }
        }
        // toggle profile
        function toggleProfile() {
            const profileContainer = document.getElementById("profileContainer");
            const headerContainer = document.getElementById("header");
            const body = document.getElementById("body");

            profileContainer.classList.toggle("open");


            if (profileContainer.classList.contains("open")) {
                headerContainer.style.position = "fixed";
                headerContainer.style.top = "0";
                headerContainer.style.width = "100%";
                body.style.marginTop = "140px";

            } else {
                headerContainer.style.position = "";
                headerContainer.style.top = "";
                headerContainer.style.width = "";
                body.style.marginTop = "";
            }
        }
        //passwords
       function changePassword() {
         const passwordContainer = document.getElementById("password-container");
         passwordContainer.style.display="block";
         document.getElementById("body").style.display
      }
      function closePassword(){
        const passwordContainer = document.getElementById("password-container");
      
        passwordContainer.style.display="none";
        
      }
      function editFirstName(){
            document.getElementById("firstName").removeAttribute("disabled");
      }
      function editLastName(){
            document.getElementById("lastName").removeAttribute("disabled");
      }
      function editAddress(){
            document.getElementById("Address").removeAttribute("disabled");
      }
      function editAddress2(){
            document.getElementById("Address2").removeAttribute("disabled");
      }
      function editPhone(){
            document.getElementById("Phone").removeAttribute("disabled");
      }


      // password eyes 
      function seePassword(){
            let password = document.getElementById("currentPass_text");
            let eyeIcon = document.getElementById("eye-icon1");

            if(password.type == "password"){
                password.type = "text";
                
                eyeIcon.src ="./pictures/eye-open.png";
                eyeIcon.style.transform = "scale(.9)";
            }else{
                password.type = "password";
                
                eyeIcon.src ="./pictures/eye-close.png";
            }
        }
        function seePassword2(){
           
            let confirm = document.getElementById("newPass_text");
            let eyeIcon2 = document.getElementById("eye-icon2");

            if(confirm.type == "password"){
               
                confirm.type = "text";
                eyeIcon2.src ="./pictures/eye-open.png";
                eyeIcon2.style.transform = "scale(.9)";

            }else{
                
                confirm.type = "password";
                eyeIcon2.src ="./pictures/eye-close.png";
            }
        }
        function seePassword3(){
           
            let confirm = document.getElementById("confirmPass_text");
            let eyeIcon3 = document.getElementById("eye-icon3");

            if(confirm.type == "password"){
               
                confirm.type = "text";
                eyeIcon3.src ="./pictures/eye-open.png";
                eyeIcon3.style.transform = "scale(.9)";

            }else{
                
                confirm.type = "password";
                eyeIcon3.src ="./pictures/eye-close.png";
            }
        }

        function enableEditing(inputId) {
            const input = document.getElementById(inputId);
            input.removeAttribute("disabled");
            input.classList.add("editing");
            input.focus(); // Automatically focus on the input field

           // Remove editing style when the input loses focus
            input.addEventListener("blur", function() {
                input.classList.remove("editing");
                input.setAttribute("disabled", "true");
            });
        }

        // Update functions for individual inputs
        function editFirstName() {
            enableEditing("firstName");
        }

        function editLastName() {
            enableEditing("lastName");
        }

        function editAddress() {
            enableEditing("Address");
        }

        // VERIFYYY
        function verify(){
            window.location.href = "./mail.php";
        }

    </script>
    
    <link rel="stylesheet" href="./navigationBar1.css">

    <style>
        header{
            background-color: <?php echo $themeRow['navigatorColor'] ?>;
        }
        .nav_links li a, .logo h1 {
            color: <?php echo $themeRow['navigatorFont'] ?>;
        }
        header > button {
            background-color: <?php echo $themeRow['navigatorButton'] ?>;

        }
     
    </style>
    
</head>
<body id="body">
    <header id="header">
        <div class="logo">
        <img src="data:image/png;base64,<?php echo $imageDataLogo; ?>" onclick="goToHome()">
            <h1 onclick="goToHome()">BIGBREW</h1>
        </div>
        
        <nav>
            <ul class="nav_links">
                <li><a href="./products.php">PRODUCTS</a></li>
                <li><a href="#">ABOUT US</a></li>
                <li><a href="#">CONTACT US</a></li>
            </ul>
        </nav> 

            <a href=<?php if ($_SESSION['customer_id'] == 1){
                echo "./pos_checkout.php";
            }else {
                echo "./usercart.php";
            }
            ?>><div class="cart-icon">

                <p class="cart-count"><?php
                  $count_query = mysqli_query($conn, "SELECT COUNT(quantity) AS total_quantity FROM cart WHERE customer_id = '$_SESSION[customer_id]' ");

                  // Fetch the result
                  $rowCart = mysqli_fetch_assoc($count_query);
                  
                  // Access the 'total_quantity' column and display it
                echo $rowCart['total_quantity'];
                ?></p>
                <img src="./pictures/cart icon.png" alt="">
            </a>

            </div>
        
        <button onclick="toggleProfile()"><img  
        style="<?php 
                 if($_SESSION['Verified'] == 1){
                echo "border: 5px solid #0fb0ff; border-radius: 50%;object-fit:cover;";
                 }else{
                    echo "border: 5px solid red; border-radius: 50%;object-fit:cover;";
                } 
                 ?>"
        src="<?php echo isset($_SESSION['Avatar']) && !empty($_SESSION['Avatar']) ? 'avatars/' . $_SESSION['Avatar'] : './pictures/user.png'; ?>" 
        alt="User Icon" 
        class="<?php 
            if($_SESSION['Avatar'] == ''){
                echo "user-icon";
            }else{
                echo "user-profile";
            }
        ?>">
            <?php echo $_SESSION ['firstName'] ?> </button>
    </header>

</header>


<!-- PROFILE CONTAINER -->
    <div id="profileContainer" class="profile-container">
        <form style="margin-top: 40px;" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data" >
            <!-- Profile Image Design -->
            <div class="profile-image-container" >
                <img src="avatars/<?php echo $row['avatar']?>" alt=""  class="avatar" id = "image">

                <div class="right-round" id="upload">
                    <img src="./pictures/photo.png" alt="" class="camera" >
                    <input type="file" name="fileImg" id="fileImg" accept=".jpg, .jpeg, .png" class="file"> 
                </div>
                <div class="left-round" id="cancel" style="display: none;">
                    <img src="./pictures/multiply.png" alt=""  class="cancel">
                </div>
                <div class="right-round" id="confirm" style="display: none;">
                    <img src="./pictures/checked.png" alt="" class="checked">
                    <input type="submit" class="checked_submit" id="checked_button" value="">
                    
                </div>
            </div>
            <script type="text/javascript">
                document.getElementById("fileImg").onchange = function(){
                    document.getElementById("image").src = URL.createObjectURL(fileImg.files[0]);

                    document.getElementById("cancel").style.display = "block";
                    document.getElementById("confirm").style.display = "block";

                    document.getElementById("upload").style.display = "none";

                    let userImage = document.getElementById("image").src;

                    document.getElementById("cancel").onclick = function(){
                    document.getElementById("image").src = userImage;

                    document.getElementById("cancel").style.display = "none";
                    document.getElementById("confirm").style.display = "none";

                    document.getElementById("upload").style.display = "block";
                    }
                }
            </script>
        </form>

        <!-- SHOW VERIFIED -->
        <?php
            if ($_SESSION['emailAddress'] !== 'dranskyxd@gmail.com') {
                    // Check the account verification status
                    $verified = $_SESSION['Verified'] === '1';

                    echo "<div id='verify-container'>";
                    if ($verified) {
                        // Account is verified, make the button non-clickable (disabled)
                        echo '<img src="./pictures/verified.png" alt="Verified" style="vertical-align: middle; width: 16px; height: 16px; margin-right: 5px; margin-top: -10px;">';
                        echo '<button type="button" disabled style="background: none; border: none; padding: 0; font: inherit; color: inherit; cursor: default; font-weight: 500; color: #0fb0ff; margin-top: -10px;">Verified</button>';
                    } else {
                        // Account is not verified, make the button clickable
                        echo '<img src="./pictures/unverified.png" alt="Unverified" style="vertical-align: middle; width: 16px; height: 16px; margin-right: 5px; margin-top: -10px;">';
                        echo '<button onclick="verify()" style="background: none; border: none; padding: 0; font: inherit; color: inherit; cursor: pointer; font-weight: 500; color: red; margin-top: -20px;">Unverified</button>';
                    }
                
                    echo '</div>';
                }
            
            ?>

        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" id="profileForm" onsubmit="return processForm(event)" > 
            <!-- sa mga textfields and labels -->
            <label for="">First name:</label>
            <input type="text" id="firstName" name="firstName_txt" value="<?php echo $firstName1; ?>" disabled  /> 
            <img src="./pictures/edit.png" alt="" class="edit-icon" id="edit1" onclick="editFirstName()">
            <br/>
            <label for="">Last name:</label>
            <input type="text" id="lastName" name="lastName_txt" value="<?php echo $lastName1; ?>"  disabled/>
            <img src="./pictures/edit.png" alt="" class="edit-icon" id="edit2" onclick="editLastName()">
            <br />
            
            <label for="">Primary Address:</label>
            <input type="text" id="Address" name="Address_txt" value="<?php echo $address1; ?>"  disabled />
            <img src="./pictures/edit.png" alt="Edit Address" class="edit-icon" id="edit3"  onclick="editAddress()">
            <br />
            
            <label for="">Secondary Address:</label>
            <input type="text" id="Address2" name="Address_txt2" value="<?php echo $address2; ?>"  disabled />
            <img src="./pictures/edit.png" alt="Edit Address" class="edit-icon" id="edit4"  onclick="editAddress2()">
            <br />
            
            
            <label for="phone">Phone:</label>
            <input  type="number" id="Phone" name="Phone_txt" value="<?php echo $phone1; ?>" disabled />
            <img src="./pictures/edit.png" alt="" class="edit-icon" id="edit5" onclick="editPhone()">
            <br/>

            <label for="email">Email:</label>
            <input  type="email" id="email" name="email" value="<?php echo $_SESSION['emailAddress']; ?>" disabled />
            <!-- <img src="./pictures/edit.png" alt="" class="edit-icon" id="edit3" onclick="editEmail()"> -->
            <br/>
            <input type="hidden" name="update_button" id="update_button_hidden" class="close-btn">
            <input type="submit" id="update_button_submit" value="Save Information" class="close-btn">        
        </form>

        <!-- password container -->
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="password-container" id="password-container">
                <div class="changepass-text">
                    <h2>Change Password</h2>
                </div>
                <img src="./pictures/cross.png" alt="" class="exit" onclick="closePassword()">
                
                <label for="currentPass_text">Current password:</label>
                <input type="password" id="currentPass_text" name="currentPass" required >
                <img src="./pictures/eye-close.png" id="eye-icon1" onclick="seePassword()" class="eye-flex">
                <br /><br>

                <label for="newPass_text">New password:</label>
                <input type="password" id="newPass_text" name="newPass" required >
                <img src="./pictures/eye-close.png" id="eye-icon2" onclick="seePassword2()" class="eye-flex">
                <br><br>
                
                <label for="confirmPass_text">Confirm password:</label>
                <input type="password" id="confirmPass_text" name="confirmPass" required >
                <img src="./pictures/eye-close.png" id="eye-icon3" onclick="seePassword3()" class="eye-flex">
                <br /><br>

                <input type="submit" class="password-buttons" name="change_password" value="Change Password" id="updateButton" >
            </div>
        </form>

    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" class="button-container">
        <input type="button" value="Password settings" class="close-btn" id="up_pass" onclick="changePassword()">
        <!-- SA PAG LAGAY NG ADMIN SETTINGS -->
        <?php 
            if($_SESSION['emailAddress'] == "dranskyxd@gmail.com"){
                echo "<input type='button' value='Admin Settings' class='close-btn' id='admin_settings' onclick='goToAdmin()' >";
            }
            else{
                echo "<input style = 'padding-left: 115px; padding-right: 115px' type='button' value='Transactions' class='close-btn' id='admin_settings' onclick='goToHistory()' >";
            }


            ?>
        <input  type="submit" class="close-btn" id="log_pass" onclick="return confirmLogout()" name="confirm_logout" value="Logout">
    </form>

    


</div>


</body>
</html>