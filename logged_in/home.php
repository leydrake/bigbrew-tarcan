<?php
// Start the session
session_start();

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "cafe_shop");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$email = $_SESSION['email'];

$query = "SELECT * FROM account WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);
  $fullname = $row['fname'] . ' ' . $row['lname'];
  $mobile_num = $row['mobile_num'];
  $address = $row['address'];
  // $username = $row['username'];
  $email = $row['email'];
  $password = $row['passwords'];
  $verify = $row['verify'];
  $imagelocation = $row['imagelocation'];
} else {
  echo "No user found with that email!";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Coffee District</title>
  <link rel="stylesheet" href="coffee.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Anton+SC&family=Barrio&family=Londrina+Sketch&family=Lugrasimo&display=swap');

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #aba069;
    }

    .container {
      width: 100%;
      display: flex;
      flex-direction: column;
      background-color: #d4cca2;
      padding-top: 20px;
    }

    .navbar {
      background-color: #6f4e37;
      padding: 5px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
    }

    .navbar .logo {
      font-size: 24px;
      color: white;
      font-weight: bold;
    }

    .nav-links {
      display: flex;
      align-items: center;
    }

    .nav-links a {
      text-decoration: none;
      color: white;
      margin: 0 15px;
      font-size: 23px;
      transition: color 0.3s ease, background-color 0.3s ease;
      padding: 10px 5px;
      position: relative;
    }

    .nav-links a::before {
      content: "";
      position: absolute;
      width: 0;
      height: 2px;
      bottom: 0;
      left: 0;
      background-color: #f4a261;
      transition: width 0.3s ease-in-out;
    }

    .nav-links a:hover::before {
      width: 100%;
    }

    .nav-links a:hover {
      color: #f4a261;
      /* Caramel hover text */
      border-radius: 5px;
    }

    .search-icon {
      margin-left: 15px;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      transition: background-color 0.3s ease;
      background-color: #fff;
      border-radius: 50%;
      width: 30px;
      height: 30px;
    }

    .search-icon:hover {
      background-color: #f4a261;
    }

    .search-icon svg {
      fill: #595037;
    }

    /* Add to Cart Icon */
    .cart-icon {
      margin-left: 15px;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      transition: background-color 0.3s ease;
      background-color: #fff;
      border-radius: 50%;
      width: 30px;
      height: 30px;
    }

    .cart-icon:hover {
      background-color: #f4a261;
    }

    .cart-icon svg {
      fill: #595037;
    }

    .account-icon {
      width: 30px;
      height: 30px;
      background-color: #fff;
      border-radius: 50%;
      margin-left: 15px;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .account-icon:hover {
      background-color: #f4a261;
    }

    .account-icon svg {
      fill: #595037;
    }

    /* Sliding profile */
    .profile-container {
      position: fixed;
      right: -700px;
      top: 60px;
      padding-top: 0px;
      background-color: #fff;
      width: 300px;
      height: 100vh;
      box-shadow: -4px 0 10px rgba(0, 0, 0, 0.2);
      transition: right 0.3s ease;
      z-index: 999;
      padding: 0px;
    }

    .profile-container.open {
      right: 0;
    }

    .profile-container h2 {
      margin-top: 0;
      font-size: 24px;
      color: #6f4e37;
    }

    .profile-container p {
      font-size: 16px;
      color: #595037;
    }

    .profile-container .close-btn {
      background-color: #8c6448;
      color: white;
      border: none;
      padding: 8px 16px;
      font-size: 16px;
      margin-top: 10px;
      cursor: pointer;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    .profile-container .close-btn:hover {
      background-color: #f4a261;
    }

    .profile-container {
      width: 100%;
      max-width: 300px;
      margin: 0 auto;
      padding: 20px;
      background-color: #fffbf8;
      border-radius: 8px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
      font-family: Arial, sans-serif;
    }

    #profileForm {
      display: flex;
      flex-direction: column;
    }

    #profileForm label {
      font-weight: bold;
      font-size: 15pt;
      color: #6f4e37;
      margin-bottom: 8px;
    }

    #profileForm input {
      padding: 7px;
      border: 2px solid #6f4e37;
      border-radius: 4px;
      font-size: 16px;
      margin-top: -4px;
      margin-bottom: -10px;
    }

    .close-btn {
      padding: 10px 20px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s;
    }

    .close-btn:hover {
      background-color: #218838;
    }

    .profile-container h2 {
      font-size: 24px;
      margin-bottom: 20px;
      color: #333;
      margin-top: -10px;
      text-align: center;
    }

    .close-btn:nth-of-type(2) {
      background-color: #dc3545;
    }

    .close-btn:nth-of-type(2):hover {
      background-color: #c82333;
    }


    @keyframes slideUp {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }

      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes slideIn {
      0% {
        opacity: 0;
        transform: translateX(-20px);
      }

      100% {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes slidefromright {
      0% {
        opacity: 0;
        transform: translateX(20px);
      }

      100% {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes fadeInButton {
      0% {
        opacity: 0;
        transform: translateY(20px);
        /* Button starts below */
      }

      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .header {
      background-image: url("cafe/head.png");
      background-repeat: no-repeat;
      width: 100%;
      top: 30px;
      position: relative;
      background-attachment: none;
      background-size: cover;
      height: 880px;
    }

    .anton img {
      /* font-family: "Anton SC", sans-serif; */
      /* font-weight: 400; */
      /* font-size: 85pt; */
      color: #6f4e37;
      margin-left: -30px;
      width: 600px;
      position: relative;
      margin-top: -30px;
      padding: 0px;
      opacity: 0;
      animation: slideIn 1.5s forwards;
    }

    .desc {
      width: 580px;
      font-size: 20pt;
      position: relative;
      top: -30px;
      left: 70px;
      opacity: 0;
      animation: slideUp 1.5s forwards;
      animation-delay: 1s;
    }

    .beans {
      position: relative;
      top: -500px;
      left: 730px;
      width: 750px;
      opacity: 0;
      animation: slidefromright 1.5s forwards;
      animation-delay: 0.5s;
    }

    .order-button {
      background-color: #6f4e37;
      /* Coffee brown color */
      color: white;
      font-size: 18pt;
      padding: 15px 30px;
      text-align: center;
      border-radius: 5px;
      text-decoration: none;
      position: relative;
      top: -630px;
      /* Adjust as needed to position */
      left: -660px;

      transform: translateX(-50%);
      opacity: 0;
      /* Initially hidden */
      animation: slideUp 1.5s forwards;
      animation-delay: 1.2s;
    }

    .order-button:hover {
      background-color: #4e3629;
      /* Darker shade on hover */
      cursor: pointer;
    }


    .footer {
      background-color: #6f4e37;
      color: #f6efcf;
      padding: 20px 20px 10px 20px;

    }

    .footer-content {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      max-width: 1200px;
      margin: 0 auto;
    }

    .footer-section {
      width: 22%;
      margin-bottom: 30px;
    }

    .footer-section h3 {
      font-size: 28px;
      margin-bottom: 15px;
      color: #f6efcf;
    }

    .footer-section p {
      font-size: 19px;
      line-height: 1;
    }

    .footer-section .follow-quote {
      font-style: italic;
      color: #f4e8c1;
    }

    .social-icons {
      display: flex;
      gap: 15px;
    }

    .social-icon {
      font-size: 32px;
      color: #f4e8c1;
      /* Light cream color */
      transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .social-icon:hover {
      transform: scale(1.1);
      opacity: 0.8;
      color: #f4a261;
    }


    .newsletter input {
      padding: 12px;
      width: 90%;
      margin-bottom: 15px;
      font-size: 14pt;
      border: none;
      border-radius: 30px;
      background-color: #f4e8c1;
      color: #6f4e37;
    }

    .newsletter button {
      padding: 10px 25px;
      background-color: #fab175;
      border: none;
      font-size: 15pt;
      border-radius: 30px;
      color: #fff;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .newsletter button:hover {
      background-color: #e76f51;
      transform: translateY(-3px);
    }

    .footer-bottom {
      text-align: center;
      padding: 20px;
      border-top: 1px solid #ffffff50;
      font-size: 14px;
      color: #f4e8c1;
    }


    .profile-image-container {
      text-align: center;
      margin-bottom: 5px;
    }

    /* color: #6f4e37; */

    #profileImage {
      border-radius: 20px;
      border: 4px solid #6f4e37;
      margin-top: -10px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease-in-out;
    }

    #profileImage:hover {
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);

    }

    /* #verify , #postalcode{
    width: 135px;
  }
  .ver{
    margin-left: -50px;
    margin-bottom: 30px;
  } 
  #verify{
    margin-left: -50px;

  } */
  </style>
</head>

<body>
  <div class="container">
    <div class="navbar">
      <div class="logo">
        <span><a href="home.php"><img src="cafe/logoo.png" alt="" width="100px" style="margin-left: 0px;"></a></span> <!--<span>Coffee District</span> -->
      </div>
      <div class="nav-links">
        <a href="home.php" style="left: -510px;">Coffee District</a>
        <a href="home.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="about.php">About Us</a>
        <a href="contact.html">Contact Us</a>
        <!-- Search Icon -->
        <div class="search-icon" onclick="searchFunction()">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            width="20"
            height="20">
            <path
              d="M15.9 14.3h-.8l-.3-.3c1-1.2 1.7-2.8 1.7-4.5C16.5 5.3 13.2 2 9.2 2S2 5.3 2 9.2 5.3 16.5 9.2 16.5c1.7 0 3.3-.7 4.5-1.7l.3.3v.8l5.1 5.1 1.5-1.5-5.1-5.2zm-6.7 0C6.2 14.3 4 12 4 9.2S6.2 4 9.2 4s5.2 2.3 5.2 5.2-2.3 5.1-5.2 5.1z" />
          </svg>
        </div>
        <div class="cart-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="22" height="">
            <path d="M0 24C0 10.7 10.7 0 24 0L69.5 0c22 0 41.5 12.8 50.6 32l411 0c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3l-288.5 0 5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5L488 336c13.3 0 24 10.7 24 24s-10.7 24-24 24l-288.3 0c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5L24 48C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96zM252 160c0 11 9 20 20 20l44 0 0 44c0 11 9 20 20 20s20-9 20-20l0-44 44 0c11 0 20-9 20-20s-9-20-20-20l-44 0 0-44c0-11-9-20-20-20s-20 9-20 20l0 44-44 0c-11 0-20 9-20 20z" />
          </svg>
        </div>


        <!-- Account Icon -->
        <div class="account-icon" onclick="toggleProfile()">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 512 512"
            width="20"
            height="20">
            <path
              d="M256 256c41.9 0 76-34.1 76-76 0-41.9-34.1-76-76-76-41.9 0-76 34.1-76 76 0 41.9 34.1 76 76 76zm0 32c-58.3 0-174 29.2-174 88v36h348v-36c0-58.8-115.7-88-174-88z" />
          </svg>
        </div>
      </div>
    </div>


    <div class="header">
      <!-- <pre class="anton">
            COFFEE 
        </pre> -->
      <pre class="anton" style="margin-top: 100px;">
            <img src="cafe/title.png" alt="">
        </pre>
      <div class="desc">
        <p>Discover crafted brews, cozy vibes, and a place to unwind. Here, every cup brings warmth and community. Come sip, relax, and enjoy your new favorite coffee spot.
        </p>
      </div>
      <img class="beans" src="cafe/brown1.png" alt="">
      <a href="order.html" class="order-button">Order Now</a>
    </div>

    <div id="profileContainer" class="profile-container">
      <h2>Account Profile</h2>

      <form style="margin-top: 0px;" method="post" action="update_info.php" id="profileForm">
        <!-- Profile Image Design -->
        <div class="profile-image-container">
        <img src="<?php echo $imagelocation; ?>" alt="" id="profileImage" width="160px" height="160px">
        </div>
        <label for="fullname">Name:</label>
        <input type="text" id="fullname" name="fullname" value="<?php echo $fullname; ?>" readonly /><br />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>" readonly /><br />

        <label for="mobile_num">Mobile Number:</label>
        <input type="tel" id="mobile_num" name="mobile_num" value="<?php echo $mobile_num; ?>" readonly /><br />

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo $address; ?>" readonly /><br />

        <label class="ver" for="verify">Status:</label>
        <input style="" type="text" id="verify" name="verify" value="<?php echo ($verify == 1) ? 'Verified' : 'Unverified'; ?>" readonly />


        <button style="margin-top: 30px;" type="submit" class="close-btn" onclick="toggleProfile()">Update Information</button>
      </form>
      <form action="logout.php" method="POST" id="logoutForm">
        <button style="margin-top: 8px; margin-left: 189px; padding-left: 27px; padding-right:27px;" type="button" class="close-btn" id="log_pass" onclick="confirmLogout()">Log Out</button>
      </form>

      <form action="update_pass.php" method="POST" id="update">
        <!-- <input type="submit"><button  style="margin-top: -50px; margin-left: 0px; padding-left: 26px; padding-right:26px;" type="button" class="close-btn" id="up_pass">Password Setting</button>  -->

        <input type="submit" style="top:-44px; margin-left: 0px; padding-left: 26px; padding-right:26px; position:relative;" value="Password Setting" class="close-btn" id="up_pass">
      </form>
      <button class="close-btn" onclick="toggleProfile()">Close</button>
    </div>



  </div>
</body>
<footer class="footer">
  <div class="footer-content">
    <div class="footer-section opening-hours">
      <h3>Opening Hours</h3>
      <p class="follow-quote">
        "Savor every moment at Coffee District, where our doors are always open for coffee lovers!"
      </p>
      <p><br />Mon - Fri: 10 AM - 10 PM<br />Sat - Sun: 11 AM - 11 PM</p>
    </div>
    <div class="footer-section follow-us">
      <h3>Follow Us</h3>
      <p class="follow-quote">
        "Stay connected and keep the coffee passion alive!"
      </p>
      <div class="social-icons">
        <a href="#" aria-label="Facebook" class="social-icon fb">
          <i class="fab fa-facebook-f"></i>
        </a>
        <a href="#" aria-label="Instagram" class="social-icon ig">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="#" aria-label="Twitter" class="social-icon tw">
          <i class="fab fa-twitter"></i>
        </a>
      </div>

    </div>
    <div class="footer-section contact-us">
      <h3>Contact Us</h3>
      <p>Email: CoffeeDistrict@email.com</p>
      <p>Phone: +63 912 345 6789</p>
      <p>Address: 123 Coffee St, Brewtown, Philippines</p>
    </div>
    <div class="footer-section newsletter">
      <h3>Stay Connected</h3>
      <p>Get exclusive offers, recipes, and the latest updates from Coffee District.</p>
      <form onsubmit="subscribe(); return false;">
        <input
          type="email"
          id="newsletter-email"
          placeholder="Enter your email"
          required /><br />
        <button type="submit">Subscribe Now</button>
      </form>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; 2024Coffee District | All rights reserved</p>
  </div>
</footer>

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
</script>

</html>