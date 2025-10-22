<?php

// Include database connection code here
include 'databaseConnection.php';
include ('tcpdf/tcpdf.php');


    $pdf = new TCPDF();
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();

    // Add title to the re
    $date = date('Y-m-d');
    $pdf->Cell(0, 10, 'Date: ' . $date, 0, false, 'R', 0, '', 0, false, 'T', 'M');
    $pdf->Ln();
    $pdf->SetFont('times', 'B', 16);
    $pdf->Cell(0, 10, 'Big Brew', 0, 1, 'C');

$searchResultHTML = '';



// Check if the payment ID is provided for searching
if (isset($_POST['search'])) {
    $startDate = mysqli_real_escape_string($conn, $_POST['start_date']);
    $endDate = date('Y-m-d', strtotime($_POST['end_date'])) . ' 23:59:59';
    $endDate = mysqli_real_escape_string($conn, $endDate);

    $searchSQL = "SELECT payment.payment_receipt, payment.payment_id, payment.order_id, payment.customer_id, payment.payment_date, payment.amount_paid, payment.payment_mode, login_credentials.firstName, login_credentials.lastName
    FROM payment
    INNER JOIN login_credentials ON payment.customer_id = login_credentials.customer_id
    WHERE payment.payment_date BETWEEN ? AND ?";
    
    $stmt = $conn->prepare($searchSQL);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $searchResult = $stmt->get_result();

    if (mysqli_num_rows($searchResult) > 0) {
        while ($row = $searchResult->fetch_assoc()) {
            $searchResultHTML .= "<tr><td>" . $row['payment_id'] . "</td>";
            $searchResultHTML .= "<td>" . $row['order_id'] . "</td>";
            $searchResultHTML .= "<td>" . $row['customer_id'] . "</td>";
            $searchResultHTML .= "<td>" . $row['firstName'] . " " . $row['lastName'] . "</td>";
            $searchResultHTML .= "<td>" . $row['payment_date'] . "</td>";
            $searchResultHTML .= "<td>" . $row['amount_paid'] . "</td>";
            $searchResultHTML .= "<td>" . $row['payment_mode'] . "</td>";
            $searchResultHTML .= "<td><a href='#' class='open-modal' data-image='./receipt/" . htmlspecialchars($row['payment_receipt']) . "'>View Receipt</a></td>";
            $searchResultHTML .= "</tr>";
        }
    }else{
        echo "<script> alert(No records found with this ID) ;";
        exit;
    }
}
if (isset($_POST['print_report'])) {
    // Fetch data
    $fullSQL = "SELECT payment.payment_receipt, payment.payment_id, payment.order_id, payment.customer_id, 
                       payment.payment_date, payment.amount_paid, payment.payment_mode, 
                       login_credentials.firstName, login_credentials.lastName
                FROM payment
                INNER JOIN orders ON payment.order_id = orders.order_id
                INNER JOIN login_credentials ON payment.customer_id = login_credentials.customer_id
                ORDER BY payment.payment_id ASC";
    $fullResult = mysqli_query($conn, $fullSQL);

    // Include TCPDF
    require_once('tcpdf/tcpdf.php');
    $pdf = new TCPDF('P', 'mm', 'A4');

    // Set document properties
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Application');
    $pdf->SetTitle('Payment History Report');
    $pdf->SetAutoPageBreak(true, 10);

    // Add a page
    $pdf->AddPage();

    // Title
    $pdf->SetFont('times', 'B', 16);
    $pdf->Cell(0, 10, 'Payment History Report', 0, 1, 'C');
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('times', 'B', 10);
    $pdf->SetFillColor(211, 142, 64);
    $pdf->SetTextColor(255);

    // Column widths (fit to A4 width)
    $headerWidths = [20, 20, 25, 40, 40, 25, 30];
    $headers = ['Payment ID', 'Order ID', 'Customer ID', 'Customer Name', 'Payment Date', 'Amount Paid', 'Payment Mode'];

    foreach ($headers as $i => $header) {
        $pdf->Cell($headerWidths[$i], 10, $header, 1, 0, 'C', 1);
    }
    $pdf->Ln();

    // Table rows
    $pdf->SetFont('times', '', 9);
    $pdf->SetTextColor(0);

    while ($row = mysqli_fetch_assoc($fullResult)) {
        $pdf->Cell($headerWidths[0], 8, $row['payment_id'], 1);
        $pdf->Cell($headerWidths[1], 8, $row['order_id'], 1);
        $pdf->Cell($headerWidths[2], 8, $row['customer_id'], 1);
        $pdf->Cell($headerWidths[3], 8, $row['firstName'] . ' ' . $row['lastName'], 1);
        $pdf->Cell($headerWidths[4], 8, $row['payment_date'], 1);
        $pdf->Cell($headerWidths[5], 8, $row['amount_paid'], 1);
        $pdf->Cell($headerWidths[6], 8, $row['payment_mode'], 1, 1);
    }

    // Output PDF
    $pdf->Output('Payment_History_Report.pdf', 'D');
}

$pdf->Ln(); // Add a line break after each set of information



if (isset($_POST['searchId_text'])){
    $_SESSION['customer_print'] = $_POST['searchId_text'] ;
}

// Check if the payment ID is provided for searching
if (isset($_POST['searchId'])) {
    $searchText = $_POST['searchId_text'];
    $_SESSION['customer_print'] = $searchText;


    // Display full payment history if there is no search
    $searchSQL = "SELECT payment.payment_receipt, payment.payment_id, payment.order_id, payment.customer_id, payment.payment_date, payment.amount_paid, payment.payment_mode, login_credentials.firstName, login_credentials.lastName
    FROM payment
    INNER JOIN orders ON payment.order_id = orders.order_id
    INNER JOIN login_credentials ON payment.customer_id = login_credentials.customer_id
    WHERE payment.customer_id = '$searchText'";
    
    $fullResult = mysqli_query($conn, $searchSQL);

    // Start the table structure
    echo "<form method='POST' action='paymenthistory.php'>
         <button type='submit' name ='print_customerId'> Print Report </button>
        </form>
    ";

    echo "<table border='1'>
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Payment Date</th>
                    <th>Amount Paid</th>
                    <th>Payment Mode</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>";

    if ($fullResult && mysqli_num_rows($fullResult) > 0) {
        // Fetch each row and populate the table
        while ($row = mysqli_fetch_assoc($fullResult)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['payment_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['customer_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['firstName']) . " " . htmlspecialchars($row['lastName']) . "</td>";
            echo "<td>" . htmlspecialchars($row['payment_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['amount_paid']) . "</td>";
            echo "<td>" . htmlspecialchars($row['payment_mode']) . "</td>";
            echo "<td><a href='#' class='open-modal' data-image='./receipt/" . htmlspecialchars($row['payment_receipt']) . "'>View Receipt</a></td>";
            echo "</tr>";
        }
    } else {
        // Handle case where no results are found
        echo "<tr><td colspan='8'>No payment history found.</td></tr>";
    }

    // Close the table structure
    echo "</tbody></table>";    

}


if (isset($_POST['print_customerId'])){

    $customerId = $_SESSION['customer_print'] ;
    // Display full payment history if there is no search
    $searchSQL = "SELECT payment.payment_receipt, payment.payment_id, payment.order_id, payment.customer_id, payment.payment_date, payment.amount_paid, payment.payment_mode, login_credentials.firstName, login_credentials.lastName
    FROM payment
    INNER JOIN orders ON payment.order_id = orders.order_id
    INNER JOIN login_credentials ON payment.customer_id = login_credentials.customer_id
    WHERE payment.customer_id = '$customerId'";
    
    $fullResult = mysqli_query($conn, $searchSQL);

    // Set document properties
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Application');
    $pdf->SetTitle('Payment History Report');
    $pdf->SetAutoPageBreak(true, 10);

    // Add a page
    $pdf->AddPage();

    // Title
    $pdf->SetFont('times', 'B', 16);
    $pdf->Cell(0, 10, 'Payment History Report', 0, 1, 'C');
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('times', 'B', 10);
    $pdf->SetFillColor(211, 142, 64);
    $pdf->SetTextColor(255);

    // Column widths (fit to A4 width)
    $headerWidths = [20, 20, 25, 40, 40, 25, 30];
    $headers = ['Payment ID', 'Order ID', 'Customer ID', 'Customer Name', 'Payment Date', 'Amount Paid', 'Payment Mode'];

    foreach ($headers as $i => $header) {
        $pdf->Cell($headerWidths[$i], 10, $header, 1, 0, 'C', 1);
    }
    $pdf->Ln();

    // Table rows
    $pdf->SetFont('times', '', 9);
    $pdf->SetTextColor(0);

    while ($row = mysqli_fetch_assoc($fullResult)) {
        $pdf->Cell($headerWidths[0], 8, $row['payment_id'], 1);
        $pdf->Cell($headerWidths[1], 8, $row['order_id'], 1);
        $pdf->Cell($headerWidths[2], 8, $row['customer_id'], 1);
        $pdf->Cell($headerWidths[3], 8, $row['firstName'] . ' ' . $row['lastName'], 1);
        $pdf->Cell($headerWidths[4], 8, $row['payment_date'], 1);
        $pdf->Cell($headerWidths[5], 8, $row['amount_paid'], 1);
        $pdf->Cell($headerWidths[6], 8, $row['payment_mode'], 1, 1);
    }

    // Output PDF
    $pdf->Output('Payment_History_Report.pdf', 'D');
}

$pdf->Ln(); // Add a line break after each set of information

session_start(); // Ensure session is started at the top of the script

$order_id= '';
if (isset($_POST['search_order_text'])){
    $_SESSION['search_order_id'] = $_POST['search_order_text'] ;
}

// Check if the payment ID is provided for searching
if (isset($_POST['search_orderId'])) {

    $order_id = $_POST['search_order_text']; // Get input from the form
    $_SESSION['search_order_text'] = $order_id; // Store it in the session
    $_SESSION['customer_search'] = $order_id;
     
    // Display full payment history if there is no search
    $searchSQL = "SELECT payment.payment_receipt, payment.payment_id, payment.order_id, payment.customer_id, payment.payment_date, payment.amount_paid, payment.payment_mode, login_credentials.firstName, login_credentials.lastName
    FROM payment
    INNER JOIN orders ON payment.order_id = orders.order_id
    INNER JOIN login_credentials ON payment.customer_id = login_credentials.customer_id
    WHERE payment.order_id = '$order_id'";
    
    $fullResult = mysqli_query($conn, $searchSQL);
    echo "<form method='POST' action='paymenthistory.php'>
         <button type='submit' name ='order_id_print'> Print Report </button>
        </form>
    ";
   // Start the table structure
    echo "<table border='1'>
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Payment Date</th>
                    <th>Amount Paid</th>
                    <th>Payment Mode</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>";

    if ($fullResult && mysqli_num_rows($fullResult) > 0) {
        // Fetch each row and populate the table
        while ($row = mysqli_fetch_assoc($fullResult)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['payment_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['customer_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['firstName']) . " " . htmlspecialchars($row['lastName']) . "</td>";
            echo "<td>" . htmlspecialchars($row['payment_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['amount_paid']) . "</td>";
            echo "<td>" . htmlspecialchars($row['payment_mode']) . "</td>";
            echo "<td><a href='#' class='open-modal' data-image='./receipt/" . htmlspecialchars($row['payment_receipt']) . "'>View Receipt</a></td>";
            echo "</tr>";
        }
    } else {
        // Handle case where no results are found
        echo "<tr><td colspan='8'>No payment history found.</td></tr>";
    }

    // Close the table structure
    echo "</tbody></table>";
}



if (isset($_POST['order_id_print'])){

    // Retrieve the stored value from the session
    $customerId = $_SESSION['search_order_text'];

    
    // Your report generation logic using $customerId
    $searchSQL = "SELECT payment.payment_receipt, payment.payment_id, payment.order_id, payment.customer_id, 
                  payment.payment_date, payment.amount_paid, payment.payment_mode, 
                  login_credentials.firstName, login_credentials.lastName
                  FROM payment
                  INNER JOIN orders ON payment.order_id = orders.order_id
                  INNER JOIN login_credentials ON payment.customer_id = login_credentials.customer_id
                  WHERE payment.order_id = '$customerId'";

    $fullResult = mysqli_query($conn, $searchSQL);

    // Set document properties
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Application');
    $pdf->SetTitle('Payment History Report');
    $pdf->SetAutoPageBreak(true, 10);

    // Add a page

    // Title
    $pdf->SetFont('times', 'B', 16);
    $pdf->Cell(0, 10, 'Payment History Report', 0, 1, 'C');
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('times', 'B', 10);
    $pdf->SetFillColor(211, 142, 64);
    $pdf->SetTextColor(255);

    // Column widths (fit to A4 width)
    $headerWidths = [20, 20, 25, 40, 40, 25, 30];
    $headers = ['Payment ID', 'Order ID', 'Customer ID', 'Customer Name', 'Payment Date', 'Amount Paid', 'Payment Mode'];

    foreach ($headers as $i => $header) {
        $pdf->Cell($headerWidths[$i], 10, $header, 1, 0, 'C', 1);
    }
    $pdf->Ln();

    // Table rows
    $pdf->SetFont('times', '', 9);
    $pdf->SetTextColor(0);

    while ($row = mysqli_fetch_assoc($fullResult)) {
        $pdf->Cell($headerWidths[0], 8, $row['payment_id'], 1);
        $pdf->Cell($headerWidths[1], 8, $row['order_id'], 1);
        $pdf->Cell($headerWidths[2], 8, $row['customer_id'], 1);
        $pdf->Cell($headerWidths[3], 8, $row['firstName'] . ' ' . $row['lastName'], 1);
        $pdf->Cell($headerWidths[4], 8, $row['payment_date'], 1);
        $pdf->Cell($headerWidths[5], 8, $row['amount_paid'], 1);
        $pdf->Cell($headerWidths[6], 8, $row['payment_mode'], 1, 1);
    }

    // Output PDF
    $pdf->Output('Payment_History_Report.pdf', 'D');
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    
<link rel="shortcut icon" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <style>
        body {
            font-family: "Poppins", Helvetica, Tahoma, Arial, serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-top: 20px;
            font-size: 40px;
        }

        a#arrow {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
        }

        a#arrow svg {
            width: 44px;
            height: 44px;
            margin-right: 5px;
        }

        table {
            border-collapse: collapse;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        width: 80%; /* Set table width to 80% */
        margin: 20px auto; /* Center the table horizontally */
    }


        th, td {
            border: 1px solid #dddddd; 
            text-align: left;
            padding: 12px;
        }

        th {
            background-color: #d38e40;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: light violet;
        }

        td {
            padding: 15px 15px;
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

        

        /* Add styles for modal overlay */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            z-index: 1;
        }

        .modal {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            max-width: 80%;
            max-height: 80%;
            overflow-y: auto;
        }

        .modal h3 {
            color: #45a049;
        }
        form {
            margin-top: 20px;
            text-align: center;
        }

        label {
            font-size: 16px;
            margin-right: 10px;
        }

        input[type="text"] {
            padding: 8px;
            font-size: 14px;
        }

        input[type="submit"] {
            padding: 9px 25px;
            font-size: 16px;
            background-color: #d38e40;
            color: #fff;
            border-color: #d38e40;
            cursor: pointer;
            border-radius:8px;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            transition: all 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: white;
            color: #4b4b4b;
            transition: all 0.3s ease;

        }
        th{
            color: white;
        }


        #start_date, #end_date {
            padding: 9px 20px;
            border: 2px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

                /* Modal styles */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            z-index: 2;
        }

        .modal {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            max-width: 80%;
            max-height: 80%;
            overflow-y: auto;
            text-align: center;
        }

        #modalImage {
            width: 100%; /* Adjust as needed */
            height: auto;
            max-height: 600px;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 30px;
            color: #fff;
            cursor: pointer;
            background-color: transparent;
            border: none;
        }

        .close-btn:hover {
            color: red;
        }

        .modal img {
            max-width: 100%;
            max-height: 600px;
        }

    </style>
</head>
<body>

<a href="admin_settings.php" style="left:100px; top:100px;position:absolute"> <!-- Arrow Link to Homepage -->
        <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 12H4" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 18L4 12L10 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

<h2 style="color:#bc6e19">Payment History</h2>

<form method="post">
    <h3>Search by Date Range:</h3>
    <label for="start_date">Select Start Date:</label>
    <input type="date" name="start_date" id="start_date">
    <label for="end_date">Select End Date:</label>
    <input type="date" name="end_date" id="end_date">
    <input type="submit" name="search" value="Search">
</form>

<form method="post" style="margin-left:-700px">
    <h3>Search by Customer ID:</h3>
    <input type="text" name="searchId_text">
    <input type="submit" name="searchId" value="Search">
</form>

<form method="post" style="margin-left:100px; margin-top:-99px;">
    <h3>Search by Order ID:</h3>
    <input type="text" name="search_order_text">
    <input type="submit" name="search_orderId" value="Search">
</form>

    <form method='POST' action='paymenthistory.php' style="margin-left:700px; margin-top:-40px;">
         <input type='submit' name ='print_report' value = "Print Report">
    </form>

<!-- Display search results if available -->
<?php if (!empty($searchResultHTML)) : ?>
        <!-- Display search results if available -->
         
        
    <h3 style="color: black; margin-left:15%;">Search Results</h3>
        <table>
            <!-- Table headers -->
            <tr>
                <th>Payment ID</th>
                <th>Order ID</th>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>Payment Date</th>
                <th>Amount Paid</th>
                <th>Payment Mode</th>
                <th>Payment Receipt</th>
            </tr>
            <?php echo $searchResultHTML; ?>
        </table>
        <br>
    <?php endif; ?>

    <h3 style="color: black; margin-left:15%;">Total Transaction History</h3>
<table>
    <tr>
        <th>Payment ID</th>
        <th>Order ID</th>
        <th>Customer ID</th>
        <th>Customer Name</th>
        <th>Payment Date</th>
        <th>Amount Paid</th>
        <th>Payment Mode</th>
        <th>Payment Receipt</th>
    </tr>
   
    <?php
    // Display full payment history if there is no search
    $fullSQL = "SELECT payment.payment_receipt, payment.payment_id, payment.order_id, payment.customer_id, payment.payment_date, payment.amount_paid, payment.payment_mode, login_credentials.firstName, login_credentials.lastName
    FROM payment
    INNER JOIN orders ON payment.order_id = orders.order_id
    INNER JOIN login_credentials ON payment.customer_id = login_credentials.customer_id
    ORDER BY payment.payment_id ASC";
    
    $fullResult = mysqli_query($conn, $fullSQL);

    if ($fullResult && mysqli_num_rows($fullResult) > 0) {
        while ($row = mysqli_fetch_assoc($fullResult)) {
            echo "<td>" . $row['payment_id'] . "</td>";
            echo "<td>" . $row['order_id'] . "</td>";
            echo "<td>" . $row['customer_id'] . "</td>";
            echo "<td>" . $row['firstName'] . " " . $row['lastName'] . "</td>";
            echo "<td>" . $row['payment_date'] . "</td>";
            echo "<td>" . $row['amount_paid'] . "</td>";
            echo "<td>" . $row['payment_mode'] . "</td>";
            echo "<td><a href='#' class='open-modal' data-image='./receipt/" . htmlspecialchars($row['payment_receipt']) . "'>View Receipt</a></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No payment history found.</td></tr>";
    }
    ?>
</table>
<!-- Modal for displaying large image -->
<div id="imageModal" class="overlay">
    <div class="modal">
        <span class="close-btn">&times;</span>
        <img id="modalImage" src="" alt="Payment Receipt" style="width:100%; height:auto;">
    </div>
</div>

<!-- Add the following JavaScript to handle modal image display -->
<script>
    // Get the modal and the image element
    const modal = document.getElementById("imageModal");
    const modalImage = document.getElementById("modalImage");
    const closeBtn = document.querySelector(".close-btn");

    // Event listener for opening the modal when the image link is clicked
    document.querySelectorAll('.open-modal').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();  // Prevent the default link behavior
            const imageUrl = this.getAttribute('data-image');
            modalImage.src = imageUrl;  // Set the modal image source
            modal.style.display = "flex";  // Show the modal
        });
    });

    // Event listener for closing the modal
    closeBtn.addEventListener('click', function() {
        modal.style.display = "none";  // Close the modal
    });

    // Close the modal when clicked outside of the modal content
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = "none";  // Close the modal if outside is clicked
        }
    });

        
</script>

</body>
</html>
