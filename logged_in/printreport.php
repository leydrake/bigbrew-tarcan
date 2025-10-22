<?php
ob_start();
// printreport.php
$_SESSION ['emailAddress'] = 'dranskyxd@gmail.com';

include ('tcpdf/tcpdf.php');
include 'databaseConnection.php';

include 'isAdmin.php';


if(isset($_POST['submit_date'])){
    $startDate = mysqli_real_escape_string($conn, $_POST['startDate']);
    $endDate = date('Y-m-d', strtotime($_POST['endDate'])) . ' 23:59:59';
    $endDate = mysqli_real_escape_string($conn, $endDate);
    
    $reportType = $_POST['reportType'];

   
    // Initialize TCPDF
    $pdf = new TCPDF();
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();

    // Add title to the re
    $date = date('Y-m-d');
    $pdf->Cell(0, 10, 'Date: ' . $date, 0, false, 'R', 0, '', 0, false, 'T', 'M');
    $pdf->Ln();
    $pdf->SetFont('times', 'B', 16);
    $pdf->Cell(0, 10, 'Big Brew', 0, 1, 'C');

    // Retrieve sales data based on the selected type
    $reportQuery = "";
    if ($reportType == 'total') {
        // Query to get total sales
        $reportQuery = "SELECT 
                    SUM(total_amount) AS total_sales, 
                    SUM(CASE WHEN customer_id = 1 THEN total_amount ELSE 0 END) AS total_pos,
                    SUM(CASE WHEN customer_id != 1 THEN total_amount ELSE 0 END) AS total_online 
                FROM orders 
                WHERE status = 'completed' AND order_date BETWEEN '$startDate' AND '$endDate'";
    } elseif ($reportType == 'by_product') {
        // Query to get sales by product
        $reportQuery = "SELECT p.name, SUM(o.total_amount) AS total_sales
                FROM product p
                JOIN orders o ON p.product_id = o.product_id
                WHERE o.status = 'completed' AND o.order_date BETWEEN '$startDate' AND '$endDate'
                GROUP BY p.product_id, p.name";

    } elseif ($reportType == 'product_inventory') {
        // Query to get product inventory
        $reportQuery = "SELECT p.product_id, p.name, p.qty AS available_stocks
                        FROM product p";
    } elseif ($reportType == 'registered_customers') {
        // Query to get registered customers
        $reportQuery = "SELECT customer_id, firstName, lastName, address, Phone, emailAddress,registrationDate
                        FROM login_credentials";
    } elseif ($reportType == 'all_orders') {
        // Query to get list of all orders
        $reportQuery = "SELECT order_id, customer_id, order_date, order_quantity, total_amount
                        FROM orders WHERE status = 'completed' ";
    }   elseif ($reportType == 'payment_history') {
        // Query to get list of all orders
        $reportQuery = "SELECT payment_id, order_id, customer_id, payment_date, amount_paid, payment_mode 
                FROM payment 
               WHERE status = 'completed' AND payment_date BETWEEN '$startDate' AND '$endDate'";

    }

    $reportResult = mysqli_query($conn, $reportQuery);

    if (!$reportResult) {
        echo "Error retrieving report: " . mysqli_error($conn);
    }

    // Add data to the PDF
    while ($row = mysqli_fetch_assoc($reportResult)) {
        $pdf->SetFont('times', '', 12);

       
        if ($reportType == 'total') {
            // Fetch sales by product data from the database
            $result = mysqli_query($conn, "SELECT 
                            SUM(total_amount) AS total_sales, 
                            SUM(CASE WHEN customer_id = 1 THEN total_amount ELSE 0 END) AS total_pos,
                            SUM(CASE WHEN customer_id != 1 THEN total_amount ELSE 0 END) AS total_online 
                        FROM orders WHERE status = 'completed' AND order_date BETWEEN '$startDate' AND '$endDate' ");
        
            // Add section title to the PDF
            $pdf->SetFont('times', 'B', 16);
            $pdf->Cell(0, 10, 'Total Sales', 0, 1, 'C');
        
            // Set table headers

            $pdf->SetFillColor(211, 142, 64);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.1);
            $pdf->SetFont('times', 'B', 12);
        
            $pdf->Cell(60, 10, 'Total Sales (Online)', 1, 0, 'C', 1);
            $pdf->Cell(60, 10, 'Total Sales (POS)', 1, 0, 'C', 1);
            $pdf->Cell(60, 10, 'Total Sales (ALL)', 1, 1, 'C', 1);
        
            // Display sales by product in the PDF
            $pdf->SetFont('times', '', 12);
        
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $pdf->Cell(60, 10, 'Php ' . $row['total_online'], 1);
                    $pdf->Cell(60, 10, 'Php ' . $row['total_pos'], 1);
                    $pdf->Cell(60, 10, 'Php ' . $row['total_sales'], 1, 1);
                }
            } else {
                // Add a row indicating no data available
                $pdf->Cell(160, 10, 'No sales data available.', 1, 1, 'C');
            }
        
            // Add a line break after the section
            $pdf->Ln();
            // Save or output the PDF
    $pdf->Output('total_sales.pdf', 'D');
        }

        
         
        else if ($reportType == 'by_product') {
            // Fetch sales by product data from the database
            $result = mysqli_query($conn, "SELECT p.name, SUM(o.total_amount) AS total_sales
                        FROM product p
                        JOIN orders o ON p.product_id = o.product_id
                        WHERE o.status = 'completed' AND o.order_date BETWEEN '$startDate' AND '$endDate'
                        GROUP BY p.product_id, p.name");
        
            // Add section title to the PDF
            $pdf->SetFont('times', 'B', 16);
            $pdf->Cell(0, 10, 'Sales by Product', 0, 1, 'C');
        
            // Set table headers

            $pdf->SetFillColor(211, 142, 64);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.1);
            $pdf->SetFont('times', 'B', 12);
        
            $pdf->Cell(80, 10, 'Product Name', 1, 0, 'C', 1);
            $pdf->Cell(80, 10, 'Total Sales (Php )', 1, 1, 'C', 1);
        
            // Display sales by product in the PDF
            $pdf->SetFont('times', '', 12);
        
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $pdf->Cell(80, 10, $row['name'], 1);
                    $pdf->Cell(80, 10, 'Php ' . $row['total_sales'], 1, 1);
                }
            } else {
                // Add a row indicating no data available
                $pdf->Cell(160, 10, 'No sales data available.', 1, 1, 'C');
            }
        
            // Add a line break after the section
            $pdf->Ln();
            // Save or output the PDF
    $pdf->Output('product_sales.pdf', 'D');
        }

        elseif ($reportType == 'product_inventory') {
            // Fetch product inventory data from the database
            $result = mysqli_query($conn, "SELECT product_id, name,category, qty AS available_stocks, price FROM product");
        
            // Display product inventory in the PDF with a table
            $pdf->SetFont('times', 'B', 14);
            $pdf->Cell(0, 10, 'Product Inventory Report', 0, 1, 'C');
        
            $pdf->SetFillColor(211, 142, 64);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.3);
        
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(30, 10, 'Product ID', 1, 0, 'C', 1);
            $pdf->Cell(60, 10, 'Product Name', 1, 0, 'C', 1);
            $pdf->Cell(30, 10, 'Category', 1, 0, 'C', 1);
            $pdf->Cell(40, 10, 'Available Stocks', 1, 0, 'C', 1);
            $pdf->Cell(30, 10, 'Price', 1, 1, 'C', 1);
        
            while ($row = mysqli_fetch_assoc($result)) {
                $pdf->SetFont('times', '', 12);
                $pdf->Cell(30, 10, $row['product_id'], 1);
                $pdf->Cell(60, 10, $row['name'], 1);
                $pdf->Cell(30, 10, $row['category'], 1);
                $pdf->Cell(40, 10, $row['available_stocks'], 1);
                $pdf->Cell(30, 10, $row['price'], 1, 1);
            }



            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $pdf->Cell(30, 10, $row['product_id'], 1);
                    $pdf->Cell(60, 10, $row['name'], 1);
                    $pdf->Cell(60, 10, $row['category'], 1);
                    $pdf->Cell(40, 10, $row['available_stocks'], 1);
                    $pdf->Cell(30, 10, $row['price'], 1, 1);
                }
            } else {
                // Add a row indicating no data available
                $pdf->Cell(160, 10, 'No data available.', 1, 1, 'C');
            }
        
            // Add a line break after the section
            $pdf->Ln();
            // Save or output the PDF
    $pdf->Output('inventory.pdf', 'D');
        }
        
        elseif ($reportType == 'registered_customers') {
            // Fetch registered customers data from the database
            $result = mysqli_query($conn, "SELECT customer_id, firstName, lastName, Address, Phone, emailAddress, Blocked FROM login_credentials WHERE customer_id != 1");
            
            // Set PDF to landscape mode and remove auto page break
        
            // Display registered customers in the PDF with a table
            $pdf->SetFont('times', 'B', 15); // Adjusted font size
            $pdf->Ln();
            $pdf->Cell(0, 5, 'Registered Customers Report', 0, 1, 'C');
            $pdf->Ln();
            $pdf->SetFillColor(211, 142, 64);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.3);
        
            // Adjusted cell widths
            $pdf->SetFont('times', 'B', 9);
            $pdf->Cell(20, 8, 'Customer ID', 1, 0, 'C', 1);
            $pdf->Cell(20, 8, 'First Name', 1, 0, 'C', 1);
            $pdf->Cell(20, 8, 'Last Name', 1, 0, 'C', 1);
            $pdf->Cell(30, 8, 'Address', 1, 0, 'C', 1);
            $pdf->Cell(30, 8, 'Contact', 1, 0, 'C', 1);
            $pdf->Cell(50, 8, 'Email', 1, 0, 'C', 1);
            $pdf->Cell(20, 8, 'Block Status', 1, 1, 'C', 1);
        
            while ($row = mysqli_fetch_assoc($result)) {
                $blockStatus = ($row['Blocked'] == 1) ? 'Blocked' : 'Active';
                $pdf->SetFont('times', '', 10); // Adjusted font size
                $pdf->Cell(20, 8, $row['customer_id'], 1);
                $pdf->Cell(20, 8, $row['firstName'], 1);
                $pdf->Cell(20, 8, $row['lastName'], 1);
                $pdf->Cell(30, 8, $row['Address'], 1);
                $pdf->Cell(30, 8, $row['Phone'], 1);
                $pdf->Cell(50, 8, $row['emailAddress'], 1);
                $pdf->Cell(20, 8, $blockStatus, 1, 1);
            }
        
            // Reset auto page break
            $pdf->SetAutoPageBreak(true);
        
            // Save or output the PDF
            $pdf->Output('customers.pdf', 'D');
        }
        
        elseif ($reportType == 'all_orders') {
            // Fetch all orders data from the database
            $result = mysqli_query($conn, "SELECT order_id, customer_id, order_date, order_quantity, total_amount FROM orders WHERE status = 'completed' AND order_date BETWEEN '$startDate' AND '$endDate'");
            
            // Display all orders in the PDF with a table
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 10, 'All Orders Report', 0, 1, 'C');
        
            $pdf->SetFillColor(211, 142, 64);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.3);
        
            $pdf->Cell(25, 10, 'Order ID', 1, 0, 'C', 1);
            $pdf->Cell(30, 10, 'Customer ID', 1, 0, 'C', 1);
            $pdf->Cell(45, 10, 'Order Date', 1, 0, 'C', 1);
            $pdf->Cell(25, 10, 'Quantity', 1, 0, 'C', 1);
            $pdf->Cell(40, 10, 'Total Amount', 1, 1, 'C', 1);
        
            while ($row = mysqli_fetch_assoc($result)) {
                $pdf->SetFont('times', '', 12);
                $pdf->Cell(25, 10, $row['order_id'], 1);
                $pdf->Cell(30, 10, $row['customer_id'], 1);
                $pdf->Cell(45, 10, $row['order_date'], 1);
                $pdf->Cell(25, 10, $row['order_quantity'], 1);
                $pdf->Cell(40, 10, 'Php ' . $row['total_amount'], 1, 1);
            }
            // Reset auto page break
            $pdf->SetAutoPageBreak(true);
        
            // Save or output the PDF
            $pdf->Output('overall_report.pdf', 'D');
        }
        
    elseif ($reportType == 'payment_history') {
        // Fetch all orders data from the database
        $stmt = $conn->prepare("SELECT payment_id, order_id, customer_id, payment_date, amount_paid, payment_mode 
        FROM payment 
        WHERE status = ? AND payment_date BETWEEN ? AND ?");
$status = 'completed';
$stmt->bind_param("sss", $status, $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();        
        // Display all orders in the PDF with a table
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 10, 'Payment History Report', 0, 1, 'C');
    
        $pdf->SetFillColor(211, 142, 64);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        
        $pdf->Cell(25, 10, 'Payment ID', 1, 0, 'C', 1);
        $pdf->Cell(25, 10, 'Order ID', 1, 0, 'C', 1);
        $pdf->Cell(30, 10, 'Customer ID', 1, 0, 'C', 1);
        $pdf->Cell(45, 10, 'Payment Date', 1, 0, 'C', 1);
        $pdf->Cell(25, 10, 'Amount Paid', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Mode of Payment', 1, 1, 'C', 1);
    
        while ($row = mysqli_fetch_assoc($result)) {
            $pdf->SetFont('times', '', 12);
            $pdf->Cell(25, 10, $row['payment_id'], 1);
            $pdf->Cell(25, 10, $row['order_id'], 1);
            $pdf->Cell(30, 10, $row['customer_id'], 1);
            $pdf->Cell(45, 10, $row['payment_date'], 1);
            $pdf->Cell(25, 10, $row['amount_paid'], 1);
            $pdf->Cell(40, 10, $row['payment_mode'], 1, 1);
        }
        // Reset auto page break
        $pdf->SetAutoPageBreak(true);
    
        // Save or output the PDF
        $pdf->Output('payment_history.pdf', 'D');
    }
    

    $pdf->Ln(); // Add a line break after each set of information
    }


}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected report type
    $reportType = $_POST['reportType'];

    // Initialize TCPDF
    $pdf = new TCPDF();
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();

    // Add title to the re
    $date = date('Y-m-d');
    $pdf->Cell(0, 10, 'Date: ' . $date, 0, false, 'R', 0, '', 0, false, 'T', 'M');
    $pdf->Ln();
    $pdf->SetFont('times', 'B', 16);
    $pdf->Cell(0, 10, 'Big Brew', 0, 1, 'C');

    // Retrieve sales data based on the selected type
    $reportQuery = "";
    if ($reportType == 'total') {
        // Query to get total sales
        $reportQuery = "SELECT 
                            SUM(total_amount) AS total_sales, 
                            SUM(CASE WHEN customer_id = 1 THEN total_amount ELSE 0 END) AS total_pos,
                            SUM(CASE WHEN customer_id != 1 THEN total_amount ELSE 0 END) AS total_online 
                        FROM orders WHERE status = 'completed' ";
    } elseif ($reportType == 'by_product') {
        // Query to get sales by product
        $reportQuery = "SELECT p.name, SUM(o.total_amount) AS total_sales
                        FROM product p
                        JOIN orders o ON p.product_id = o.product_id
                        WHERE o.status = 'completed'
                        GROUP BY p.product_id, p.name";
    } elseif ($reportType == 'product_inventory') {
        // Query to get product inventory
        $reportQuery = "SELECT p.product_id, p.name, p.qty AS available_stocks
                        FROM product p";
    } elseif ($reportType == 'registered_customers') {
        // Query to get registered customers
        $reportQuery = "SELECT customer_id, firstName, lastName, address, Phone, emailAddress,registrationDate
                        FROM login_credentials";
    } elseif ($reportType == 'all_orders') {
        // Query to get list of all orders
        $reportQuery = "SELECT order_id, customer_id, order_date, order_quantity, total_amount
                        FROM orders WHERE status = 'completed' ";
    }   elseif ($reportType == 'payment_history') {
        // Query to get list of all orders
        $reportQuery = "SELECT payment_id, order_id, customer_id, payment_date, amount_paid, payment_mode FROM payment WHERE status = 'completed' ";
    }

    $reportResult = mysqli_query($conn, $reportQuery);

    if (!$reportResult) {
        echo "Error retrieving report: " . mysqli_error($conn);
    }

    // Add data to the PDF
    while ($row = mysqli_fetch_assoc($reportResult)) {
        $pdf->SetFont('times', '', 12);

       
        if ($reportType == 'total') {
            // Fetch sales by product data from the database
            $result = mysqli_query($conn, "SELECT 
                            SUM(total_amount) AS total_sales, 
                            SUM(CASE WHEN customer_id = 1 THEN total_amount ELSE 0 END) AS total_pos,
                            SUM(CASE WHEN customer_id != 1 THEN total_amount ELSE 0 END) AS total_online 
                        FROM orders WHERE status = 'completed'");
        
            // Add section title to the PDF
            $pdf->SetFont('times', 'B', 16);
            $pdf->Cell(0, 10, 'Total Sales', 0, 1, 'C');
        
            // Set table headers

            $pdf->SetFillColor(211, 142, 64);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.1);
            $pdf->SetFont('times', 'B', 12);
        
            $pdf->Cell(60, 10, 'Total Sales (Online)', 1, 0, 'C', 1);
            $pdf->Cell(60, 10, 'Total Sales (POS)', 1, 0, 'C', 1);
            $pdf->Cell(60, 10, 'Total Sales (ALL)', 1, 1, 'C', 1);
        
            // Display sales by product in the PDF
            $pdf->SetFont('times', '', 12);
        
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $pdf->Cell(60, 10, 'Php ' . $row['total_online'], 1);
                    $pdf->Cell(60, 10, 'Php ' . $row['total_pos'], 1);
                    $pdf->Cell(60, 10, 'Php ' . $row['total_sales'], 1, 1);
                }
            } else {
                // Add a row indicating no data available
                $pdf->Cell(160, 10, 'No sales data available.', 1, 1, 'C');
            }
        
            // Add a line break after the section
            $pdf->Ln();
            // Save or output the PDF
    $pdf->Output('total_sales.pdf', 'D');
        }

        
         
        else if ($reportType == 'by_product') {
            // Fetch sales by product data from the database
            $result = mysqli_query($conn, "SELECT p.name, SUM(o.total_amount) AS total_sales
                        FROM product p
                        JOIN orders o ON p.product_id = o.product_id
                        WHERE o.status = 'completed'
                        GROUP BY p.product_id, p.name");
        
            // Add section title to the PDF
            $pdf->SetFont('times', 'B', 16);
            $pdf->Cell(0, 10, 'Sales by Product', 0, 1, 'C');
        
            // Set table headers

            $pdf->SetFillColor(211, 142, 64);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.1);
            $pdf->SetFont('times', 'B', 12);
        
            $pdf->Cell(80, 10, 'Product Name', 1, 0, 'C', 1);
            $pdf->Cell(80, 10, 'Total Sales (Php )', 1, 1, 'C', 1);
        
            // Display sales by product in the PDF
            $pdf->SetFont('times', '', 12);
        
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $pdf->Cell(80, 10, $row['name'], 1);
                    $pdf->Cell(80, 10, 'Php ' . $row['total_sales'], 1, 1);
                }
            } else {
                // Add a row indicating no data available
                $pdf->Cell(160, 10, 'No sales data available.', 1, 1, 'C');
            }
        
            // Add a line break after the section
            $pdf->Ln();
            // Save or output the PDF
    $pdf->Output('product_sales.pdf', 'D');
        }

        elseif ($reportType == 'product_inventory') {
            // Fetch product inventory data from the database
            $result = mysqli_query($conn, "SELECT product_id, name,category, qty AS available_stocks, price FROM product");
        
            // Display product inventory in the PDF with a table
            $pdf->SetFont('times', 'B', 14);
            $pdf->Cell(0, 10, 'Product Inventory Report', 0, 1, 'C');
        
            $pdf->SetFillColor(211, 142, 64);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.3);
        
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(30, 10, 'Product ID', 1, 0, 'C', 1);
            $pdf->Cell(60, 10, 'Product Name', 1, 0, 'C', 1);
            $pdf->Cell(30, 10, 'Category', 1, 0, 'C', 1);
            $pdf->Cell(40, 10, 'Available Stocks', 1, 0, 'C', 1);
            $pdf->Cell(30, 10, 'Price', 1, 1, 'C', 1);
        
            while ($row = mysqli_fetch_assoc($result)) {
                $pdf->SetFont('times', '', 12);
                $pdf->Cell(30, 10, $row['product_id'], 1);
                $pdf->Cell(60, 10, $row['name'], 1);
                $pdf->Cell(30, 10, $row['category'], 1);
                $pdf->Cell(40, 10, $row['available_stocks'], 1);
                $pdf->Cell(30, 10, $row['price'], 1, 1);
            }



            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $pdf->Cell(30, 10, $row['product_id'], 1);
                    $pdf->Cell(60, 10, $row['name'], 1);
                    $pdf->Cell(60, 10, $row['category'], 1);
                    $pdf->Cell(40, 10, $row['available_stocks'], 1);
                    $pdf->Cell(30, 10, $row['price'], 1, 1);
                }
            } else {
                // Add a row indicating no data available
                $pdf->Cell(160, 10, 'No data available.', 1, 1, 'C');
            }
        
            // Add a line break after the section
            $pdf->Ln();
            // Save or output the PDF
    $pdf->Output('inventory.pdf', 'D');
        }
        
        elseif ($reportType == 'registered_customers') {
            // Fetch registered customers data from the database
            $result = mysqli_query($conn, "SELECT customer_id, firstName, lastName, Address, Phone, emailAddress, Blocked FROM login_credentials WHERE customer_id != 1");
            
            // Set PDF to landscape mode and remove auto page break
        
            // Display registered customers in the PDF with a table
            $pdf->SetFont('times', 'B', 15); // Adjusted font size
            $pdf->Ln();
            $pdf->Cell(0, 5, 'Registered Customers Report', 0, 1, 'C');
            $pdf->Ln();
            $pdf->SetFillColor(211, 142, 64);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.3);
        
            // Adjusted cell widths
            $pdf->SetFont('times', 'B', 9);
            $pdf->Cell(20, 8, 'Customer ID', 1, 0, 'C', 1);
            $pdf->Cell(20, 8, 'First Name', 1, 0, 'C', 1);
            $pdf->Cell(20, 8, 'Last Name', 1, 0, 'C', 1);
            $pdf->Cell(30, 8, 'Address', 1, 0, 'C', 1);
            $pdf->Cell(30, 8, 'Contact', 1, 0, 'C', 1);
            $pdf->Cell(50, 8, 'Email', 1, 0, 'C', 1);
            $pdf->Cell(20, 8, 'Block Status', 1, 1, 'C', 1);
        
            while ($row = mysqli_fetch_assoc($result)) {
                $blockStatus = ($row['Blocked'] == 1) ? 'Blocked' : 'Active';
                $pdf->SetFont('times', '', 10); // Adjusted font size
                $pdf->Cell(20, 8, $row['customer_id'], 1);
                $pdf->Cell(20, 8, $row['firstName'], 1);
                $pdf->Cell(20, 8, $row['lastName'], 1);
                $pdf->Cell(30, 8, $row['Address'], 1);
                $pdf->Cell(30, 8, $row['Phone'], 1);
                $pdf->Cell(50, 8, $row['emailAddress'], 1);
                $pdf->Cell(20, 8, $blockStatus, 1, 1);
            }
        
            // Reset auto page break
            $pdf->SetAutoPageBreak(true);
        
            // Save or output the PDF
            $pdf->Output('customers.pdf', 'D');
        }
        
        elseif ($reportType == 'all_orders') {
            // Fetch all orders data from the database
            $result = mysqli_query($conn, "SELECT order_id, customer_id, order_date, order_quantity, total_amount FROM orders WHERE status = 'completed'");
            
            // Display all orders in the PDF with a table
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 10, 'All Orders Report', 0, 1, 'C');
        
            $pdf->SetFillColor(211, 142, 64);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.3);
        
            $pdf->Cell(25, 10, 'Order ID', 1, 0, 'C', 1);
            $pdf->Cell(30, 10, 'Customer ID', 1, 0, 'C', 1);
            $pdf->Cell(45, 10, 'Order Date', 1, 0, 'C', 1);
            $pdf->Cell(25, 10, 'Quantity', 1, 0, 'C', 1);
            $pdf->Cell(40, 10, 'Total Amount', 1, 1, 'C', 1);
        
            while ($row = mysqli_fetch_assoc($result)) {
                $pdf->SetFont('times', '', 12);
                $pdf->Cell(25, 10, $row['order_id'], 1);
                $pdf->Cell(30, 10, $row['customer_id'], 1);
                $pdf->Cell(45, 10, $row['order_date'], 1);
                $pdf->Cell(25, 10, $row['order_quantity'], 1);
                $pdf->Cell(40, 10, 'Php ' . $row['total_amount'], 1, 1);
            }
            // Reset auto page break
            $pdf->SetAutoPageBreak(true);
        
            // Save or output the PDF
            $pdf->Output('overall_report.pdf', 'D');
        }
        
    elseif ($reportType == 'payment_history') {
        // Fetch all orders data from the database
        $result = mysqli_query($conn, "SELECT payment_id, order_id, customer_id, payment_date, amount_paid, payment_mode FROM payment WHERE status = 'completed'");
        
        // Display all orders in the PDF with a table
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 10, 'Payment History Report', 0, 1, 'C');
    
        $pdf->SetFillColor(211, 142, 64);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        
        $pdf->Cell(25, 10, 'Payment ID', 1, 0, 'C', 1);
        $pdf->Cell(25, 10, 'Order ID', 1, 0, 'C', 1);
        $pdf->Cell(30, 10, 'Customer ID', 1, 0, 'C', 1);
        $pdf->Cell(45, 10, 'Payment Date', 1, 0, 'C', 1);
        $pdf->Cell(25, 10, 'Amount Paid', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Mode of Payment', 1, 1, 'C', 1);
    
        while ($row = mysqli_fetch_assoc($result)) {
            $pdf->SetFont('times', '', 12);
            $pdf->Cell(25, 10, $row['payment_id'], 1);
            $pdf->Cell(25, 10, $row['order_id'], 1);
            $pdf->Cell(30, 10, $row['customer_id'], 1);
            $pdf->Cell(45, 10, $row['payment_date'], 1);
            $pdf->Cell(25, 10, $row['amount_paid'], 1);
            $pdf->Cell(40, 10, $row['payment_mode'], 1, 1);
        }
        // Reset auto page break
        $pdf->SetAutoPageBreak(true);
    
        // Save or output the PDF
        $pdf->Output('payment_history.pdf', 'D');
    }
    

    $pdf->Ln(); // Add a line break after each set of information
    }
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="printreport.css">

</head>
<body>
    <a href="admin_settings.php" style="left: 100px; top: 50px"> <!-- Arrow Link to Homepage -->
        <svg width="54" height="74" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 12H4" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 18L4 12L10 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>


    <h1 class="title-header">Printing of Reports</h1>

    <div class="report-container">

         <form method="post" action="" id="form1">
                    <div class="wrapper">
            <div class="card">
                <input class="input" type="radio" name="reportType" id="total" value="total" required>
                <span class="check"></span>
                <label class="label" for="total">
                <div class="title">Total Sales</div>
                </label>
            </div>
            <div class="card">
                <input class="input" type="radio" name="reportType" id="by_product" value="by_product" required>
                <span class="check"></span>
                <label class="label" for="by_product">
                <div class="title">Sales by Product</div>
                </label>
            </div>
            <div class="card">
                <input class="input" type="radio" name="reportType" id="product_inventory" value="product_inventory" required>
                <span class="check"></span>
                <label class="label" for="product_inventory">
                <div class="title">Product Inventory</div>
                </label>
            </div>
            <div class="card">
                <input class="input" type="radio" name="reportType" id="registered_customers" value="registered_customers" required>
                <span class="check"></span>
                <label class="label" for="registered_customers">
                <div class="title">Registered Customers</div>
                </label>
            </div>
            <div class="card">
                <input class="input" type="radio" name="reportType" id="payment_history" value="payment_history" required>
                <span class="check"></span>
                <label class="label" for="payment_history">
                <div class="title">Payment History</div>
                </label>
            </div>
            <div class="card">
                <input class="input" type="radio" name="reportType" id="all_orders" value="all_orders" required>
                <span class="check"></span>
                <label class="label" for="all_orders">
                <div class="title">List of <br> All Orders</div>
                </label>
            </div>
            </div>
            <div id="dateDiv" style="display:none; position:absolute; left: 1000px; top: 400px" >
                <label for="startDate"><strong>Start Date:</strong></label>
                <input type="date" id="startDate" name="startDate" style="padding:7px">
                <br>
                &nbsp;
                <label for="endDate"><strong>End Date:</strong></label>
                <input type="date" id="endDate" name="endDate" style="padding:7px">
                <br> 
                <input type="submit" name="submit_date" value="Print By Date"  style="margin-left: 90px;margin-top:10px;padding: 15px 30px; border-radius: 5px; border:none; background-color: #d38e40; color: white">

            </div>

                        <div class="generate-report-button">
                            <button type="submit">
                                Generate Report <br>
                            </button>
                        </div>

                <!-- Radio buttons -->

                <!-- Date input fields (Initially hidden) -->
            

  <!-- Submit button for 'All Time' -->
</form>

        <div id="preview-container"></div>

        <script>

           
            function previewReport() {
                // Get the selected report type
                var reportType = document.getElementById('reportType').value;

                // Fetch the preview content
                var previewContainer = document.getElementById('preview-container');
                previewContainer.innerHTML = "Loading...";

                // Use AJAX to fetch the preview content from the server
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            // Update the preview container with the fetched HTML
                            previewContainer.innerHTML = xhr.responseText;
                        } else {
                            previewContainer.innerHTML = "Error loading preview.";
                        }
                    }
                };
                xhr.open('GET', 'preview.php?reportType=' + reportType, true);
                xhr.send();
            }

            document.addEventListener('DOMContentLoaded', function () {
                const radioButtons = document.querySelectorAll('input[type="radio"]');
                const dateDiv = document.getElementById('dateDiv');
                const form = document.getElementById('myForm');

                // Event listener for radio buttons
                radioButtons.forEach(radio => {
                    radio.addEventListener('change', function () {
                    if (this.checked) {
                        // Show date input fields if any radio button is selected
                        dateDiv.style.display = 'block';
                    }
                    });
                });

                
                });
        </script>
    </div>
</body>
</html>