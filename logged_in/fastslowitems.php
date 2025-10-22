<?php
include 'databaseConnection.php';
include ("navigationBar.php");
include 'isAdmin.php';

// Database connection


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected report type
    $reportType = $_POST['reportType'];

    // Retrieve fast or slow-moving items based on the selected type
    $reportQuery = "";
    if ($reportType == 'fast') {
        // Query to get fast-moving items (most sold)
        $reportQuery = "SELECT p.product_id, p.name, SUM(o.order_quantity) AS total_quantity_sold
                        FROM product p
                        JOIN orders o ON p.product_id = o.product_id
                        GROUP BY p.product_id, p.name
                        ORDER BY total_quantity_sold DESC";
    } elseif ($reportType == 'slow') {
        // Query to get slow-moving items (least sold)
        $reportQuery = "SELECT p.product_id, p.name, SUM(o.order_quantity) AS total_quantity_sold
                        FROM product p
                        JOIN orders o ON p.product_id = o.product_id
                        GROUP BY p.product_id, p.name
                        ORDER BY total_quantity_sold ASC";
    }

    $reportResult = mysqli_query($conn, $reportQuery);

    if (!$reportResult) {
        echo "Error retrieving report: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<script>
    // toggle profile
    function toggleProfile() {
            const profileContainer = document.getElementById("profileContainer");
            const headerContainer = document.getElementById("header");
            const body = document.getElementById("body");
            const nav = document.getElementById("navigator");

            profileContainer.classList.toggle("open");


            if (profileContainer.classList.contains("open")) {
                headerContainer.style.position = "fixed";
                headerContainer.style.top = "0";
                headerContainer.style.width = "100%";
                body.style.marginTop = "200px";


            } else {
                headerContainer.style.position = "";
                headerContainer.style.top = "";
                headerContainer.style.width = "";
                body.style.marginTop = "0px";


                

                

            }
        }
</script>
<link rel="shortcut icon" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Product Report</title>
    <link rel="stylesheet" href="stylez.css">
<style>

        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;700;900&display=swap');
        *{
        font-family: "Montserrat";
        }
        .title-header{
        text-align: center;
        margin: 50px;
        color: #bc6e19;
        font-size: 40px;
        
        }
        .navigationbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
        
            padding: 10px 20px;

        }

        #logo {
            width: 200px;
            height: auto;
        }

        h1 {
            margin: 20px 0;
            color: #333;
        }

        .report-container {
            width: 70%;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
            margin-top: 20px;
            overflow-x: auto;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            margin-right: 10px;
            font-weight: bold;
            color: #333;
        }

        .form-group select {
            padding: 10px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #3498db;
            border-radius: 4px;
            background-color: #fff;
            color: #333;
            transition: border-color 0.3s, background-color 0.3s, color 0.3s;
        }

        .form-group select:hover,
        .form-group select:focus {
            border-color: #2980b9;
            background-color: #f2f2f2;
            color: #000;
            outline: none;
        }

        .generate-report-button {
            margin-top: 20px;
            text-align: right;
            
        }

        button {
            border: none;
            cursor: pointer;
            background-color: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #d38e40;
            color: #fff;
        }

        /* Hover effect for rows */
        tr:hover {
            background-color: #fffaeb;;
            color: #000;
            font-weight: bold;
        }

        /* Style for cells in the hover effect */
        tr:hover td {
            border: none; 
        }
        td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        tr:nth-child(even) {
            background-color: #ebebeb;
        }

        tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.3s ease;
        }

                

        a {
                text-align: left;
                position: absolute;
                top: 20px;
                left: 20px;
                text-decoration: none;
                color: black;
                display: flex;
                align-items: center;
            }

            
        /* SIZES */
        .size-container {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            display: block;
            margin-top: 20px;
        }
        
        .size-container .custom-radio {
            display: inline-flex;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
            overflow: hidden;
        }
        
        .size-container .custom-radio input[type='radio'] {
            display: none;
        }
        
        .size-container .radio-label {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }
        
        .size-container .radio-circle {
            width: 20px;
            height: 20px;
            border: 2px solid #d38e40;
            border-radius: 50%;
            margin-right: 10px;
            transition: border-color 0.3s ease-in-out, background-color 0.3s ease-in-out;
        }
        
        .size-container .radio-text {
            font-size: 1rem;
            color: #333;
            transition: color 0.3s ease-in-out;
        }
        
        .size-container .custom-radio input[type='radio']:checked + .radio-label {
            background-color: #d38e40;
        }
        
        .size-container .custom-radio input[type='radio']:checked + .radio-label .radio-circle {
            border-color: #fff;
            background-color: #d38e40;
        }
        
        .size-container .custom-radio input[type='radio']:checked + .radio-label .radio-text {
            color: white;
        }

</style>
</head>
<body id="body">

<a href="admin_settings.php" style="left:30px; top:150px"> <!-- Arrow Link to Homepage -->
        <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 12H4" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 18L4 12L10 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <!-- Navigation Bar -->
   
    <h1 class="title-header">Admin Product Report</h1>

    <div class="report-container">
        <form method="post" action="">

        <div class="size-container">
  <div class="custom-radio">
    <input type="radio" id="fast" name="reportType" value="fast" required>
    <label class="radio-label" for="fast">
      <div class="radio-circle"></div>
      <span class="radio-text">Fast-moving Items</span>
    </label>
    <input type="radio" id="slow" name="reportType" value="slow" required>
    <label class="radio-label" for="slow">
      <div class="radio-circle"></div>
      <span class="radio-text">Slow-moving Items</span>
    </label>
  </div>
</div>


            <div class="generate-report-button">
                <button type="submit" style="color:white;
                    background-color:#d38e40;">Generate Report</button>
            </div>
        </form>

        <?php
        if (isset($reportResult)) {
            if (mysqli_num_rows($reportResult) > 0) {
                echo "<table>";
                echo "<tr><th>Product ID</th><th>Product Name</th><th>Total Quantity Sold</th></tr>";

                while ($row = mysqli_fetch_assoc($reportResult)) {
                    echo "<tr>";
                    echo "<td>" . $row['product_id'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['total_quantity_sold'] . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "No data available for the selected report type.";
            }
        }
        ?>
    </div>
</body>
</html>
