<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBUS|Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>
<body>
    <header>
         <div class="logo">Go<span id = "logo">Bus</span></div>
        <div class="header-right">

            <a href="#" class="my-bookings">My Bookings</a>
            <a href="#"><i class="fa-solid fa-user-circle"></i> Admin</a>
        </div>
    </header>

    <nav class="sidebar">
        <ul>
            <li><a href="#revenue" class="sidebar-link active" data-section="revenue">Revenue Tracking</a></li>
            <li><a href="#discounts" class="sidebar-link" data-section="discounts">Discounts & Promotions</a></li>
            <li><a href="#reports" class="sidebar-link" data-section="reports">Report Generation</a></li>
            <li><a href="#feedback" class="sidebar-link" data-section="feedback">Feedback Handling</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <section id="revenue" class="section">
            <h2>Revenue Tracking per Route</h2>
            <table class="revenue-table">
                <thead>
                    <tr>
                        <th>Route</th>
                        <th>Revenue (USD)</th>
                        <th>Tickets Sold</th>
                        <th>Average Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Dhaka - Rajshahi</td>
                        <td>15000</td>
                        <td>300</td>
                        <td>50</td>
                    </tr>
                    <tr>
                        <td>Dhaka - Barisal</td>
                        <td>22000</td>
                        <td>440</td>
                        <td>50</td>
                    </tr>
                    <tr>
                        <td>Dhaka - Cox's Bazar</td>
                        <td>10000</td>
                        <td>200</td>
                        <td>50</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section id="discounts" class="section" style="display: none;">
            <h2>Discounts & Promotions</h2>
            <form class="promotion-form">
                <label for="promo-code">Promo Code:</label>
                <input type="text" id="promo-code" placeholder="Enter promo code">

                <label for="discount-type">Discount Type:</label>
                <select id="discount-type">
                    <option>Percentage</option>
                    <option>Fixed Amount</option>
                </select>

                <label for="discount-value">Value:</label>
                <input type="number" id="discount-value" placeholder="Enter value">

                <label for="route">Applicable Route:</label>
                <input type="text" id="route" placeholder="Enter route">

                <button type="submit">Add Promotion</button>
            </form>

            <div class="promotion-list">
                <h3>Current Promotions</h3>
                <ul>
                    <li>Code: SUMMER10 - 10% off on Dhaka routes</li>
                    <li>Code: FALL20 - $20 off on Chittagong routes</li>
                </ul>
            </div>
        </section>

        <section id="reports" class="section" style="display: none;">
            <h2>Report Generation</h2>
            <form class="report-form">
                <label for="report-type">Report Type:</label>
                <select id="report-type">
                    <option>Revenue Report</option>
                    <option>Sales Report</option>
                    <option>User Feedback Report</option>
                </select>

                <label for="date-range">Date Range:</label>
                <select id="date-range">
                    <option>Last 7 Days</option>
                    <option>Last 30 Days</option>
                    <option>Custom</option>
                </select>

                <button type="submit">Generate Report</button>
            </form>
        </section>

        <section id="feedback" class="section" style="display: none;">
            <h2>Feedback and Complaint Handling</h2>
            <div class="feedback-list">
                <div class="feedback-item">
                    <h4>User: John Doe</h4>
                    <p>Complaint: Delayed bus on Dhaka-Rajshahi route.</p>
                    <button>Respond</button>
                </div>
                <div class="feedback-item">
                    <h4>User: Jane Smith</h4>
                    <p>Feedback: Excellent service!</p>
                    <button>Respond</button>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footerContainer">
            <div class="footerSection">
                <h2>GO BUS</h2>
                <p>
                    gobus.com is a premium online booking portal which allows you to purchase ticket 
                    for various bus booking services locally across the country.
                </p>
            </div>

            <div class="footerSection">
                <h3>Company Info</h3>
                <a href="#">Terms and Condition</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Cancellation Policy</a>
            </div>

            <div class="footerSection">
                <h3>About GoBUS</h3>
                <a href="#">About Us</a>
                <a href="#">Contact Us</a>
            </div>
        </div>

        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group</span>
        </div>
    </footer>

    <script src="../data/admin_dashboard.js"></script>
</body>
</html> -->

<?php
// Start session at the very top
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if admin is logged in (uncomment once login.php is set up)
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "gobus";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if required tables exist
$required_tables = ['routes', 'promotions', 'feedbackadmin', 'responses'];
$missing_tables = [];
foreach ($required_tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) == 0) {
        $missing_tables[] = $table;
    }
}
if (!empty($missing_tables)) {
    die("Error: The following database tables are missing: " . implode(", ", $missing_tables) . ". Please create them using the provided SQL.");
}

// Determine active section
$active_section = 'revenue'; // Default
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generate_report'])) {
    $active_section = 'reports';
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_promo'])) {
    $active_section = 'discounts';
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_response'])) {
    $active_section = 'feedback';
}

// Handle promotion form submission
$promo_errors = [];
$success_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_promo'])) {
    $promo_code = trim($_POST['promo_code'] ?? '');
    $discount_type = $_POST['discount_type'] ?? '';
    $discount_value = floatval($_POST['discount_value'] ?? 0);
    $route = trim($_POST['route'] ?? '');

    // Validations
    if (empty($promo_code)) {
        $promo_errors[] = "Promo code is required.";
    } elseif (strlen($promo_code) > 20) {
        $promo_errors[] = "Promo code must be 20 characters or less.";
    }

    if ($discount_value <= 0) {
        $promo_errors[] = "Discount value must be greater than 0.";
    }

    if (empty($route)) {
        $promo_errors[] = "Route is required.";
    }

    // Check if promo code already exists
    $check_promo = mysqli_query($conn, "SELECT * FROM promotions WHERE promo_code = '" . mysqli_real_escape_string($conn, $promo_code) . "'");
    if (!$check_promo) {
        $promo_errors[] = "Error checking promo code: " . mysqli_error($conn);
    } elseif (mysqli_num_rows($check_promo) > 0) {
        $promo_errors[] = "Promo code already exists.";
    }

    // If no errors, insert into database
    if (empty($promo_errors)) {
        $stmt = $conn->prepare("INSERT INTO promotions (promo_code, discount_type, discount_value, route) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            $promo_errors[] = "Prepare failed: " . mysqli_error($conn);
        } else {
            $stmt->bind_param("ssds", $promo_code, $discount_type, $discount_value, $route);
            if ($stmt->execute()) {
                $success_message = "Promotion added successfully!";
            } else {
                $promo_errors[] = "Error adding promotion: " . mysqli_error($conn);
            }
            $stmt->close();
        }
    }
}

// Handle response form submission
$response_errors = [];
$response_success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_response'])) {
    $feedback_id = intval($_POST['feedback_id'] ?? 0);
    $response_text = trim($_POST['response_text'] ?? '');

    // Validations
    if ($feedback_id <= 0) {
        $response_errors[] = "Invalid feedback ID.";
    } else {
        // Verify feedback_id exists
        $check_feedback = mysqli_query($conn, "SELECT id FROM feedbackadmin WHERE id = $feedback_id");
        if (!$check_feedback) {
            $response_errors[] = "Error checking feedback ID: " . mysqli_error($conn);
        } elseif (mysqli_num_rows($check_feedback) == 0) {
            $response_errors[] = "Feedback ID does not exist.";
        }
    }

    if (empty($response_text)) {
        $response_errors[] = "Response text is required.";
    } elseif (strlen($response_text) > 500) {
        $response_errors[] = "Response text must be 500 characters or less.";
    }

    // If no errors, insert into database
    if (empty($response_errors)) {
        $stmt = $conn->prepare("INSERT INTO responses (feedback_id, response_text) VALUES (?, ?)");
        if (!$stmt) {
            $response_errors[] = "Prepare failed: " . mysqli_error($conn);
        } else {
            $stmt->bind_param("is", $feedback_id, $response_text);
            if ($stmt->execute()) {
                $response_success = "Response submitted successfully!";
            } else {
                $response_errors[] = "Error submitting response: " . mysqli_error($conn);
            }
            $stmt->close();
        }
    }
}

// Handle report form submission
$report_errors = [];
$report_data = [];
$report_type = "";
$date_range = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generate_report'])) {
    $report_type = $_POST['report_type'] ?? '';
    $date_range = $_POST['date_range'] ?? '';
    $custom_start = $_POST['custom_start'] ?? '';
    $custom_end = $_POST['custom_end'] ?? '';

    // Validations
    if (!in_array($report_type, ['Revenue Report', 'Sales Report', 'User Feedback Report'])) {
        $report_errors[] = "Invalid report type.";
    }

    if (!in_array($date_range, ['Last 7 Days', 'Last 30 Days', 'Custom'])) {
        $report_errors[] = "Invalid date range.";
    }

    if ($date_range === 'Custom') {
        if (empty($custom_start) || empty($custom_end)) {
            $report_errors[] = "Custom start and end dates are required.";
        } elseif (!strtotime($custom_start) || !strtotime($custom_end)) {
            $report_errors[] = "Invalid custom date format.";
        } elseif (strtotime($custom_start) > strtotime($custom_end)) {
            $report_errors[] = "Start date must be before end date.";
        }
    }

    // Build date filter
    $date_filter = "";
    if (empty($report_errors)) {
        if ($date_range === 'Last 7 Days') {
            $date_filter = "WHERE transaction_date >= CURDATE() - INTERVAL 7 DAY";
        } elseif ($date_range === 'Last 30 Days') {
            $date_filter = "WHERE transaction_date >= CURDATE() - INTERVAL 30 DAY";
        } elseif ($date_range === 'Custom') {
            $custom_start = mysqli_real_escape_string($conn, $custom_start);
            $custom_end = mysqli_real_escape_string($conn, $custom_end);
            $date_filter = "WHERE transaction_date BETWEEN '$custom_start' AND '$custom_end'";
        }
    }

    // Generate report
    if (empty($report_errors)) {
        if ($report_type === 'Revenue Report') {
            $query = "SELECT route_name, SUM(revenue) as total_revenue, SUM(tickets_sold) as total_tickets, 
                             (SUM(revenue) / SUM(tickets_sold)) as avg_price 
                      FROM routes $date_filter 
                      GROUP BY route_name";
            $report_data = mysqli_query($conn, $query);
            if (!$report_data) {
                $report_errors[] = "Error generating revenue report: " . mysqli_error($conn);
            }
        } elseif ($report_type === 'Sales Report') {
            $query = "SELECT route_name, tickets_sold, transaction_date 
                      FROM routes $date_filter 
                      ORDER BY transaction_date DESC";
            $report_data = mysqli_query($conn, $query);
            if (!$report_data) {
                $report_errors[] = "Error generating sales report: " . mysqli_error($conn);
            }
        } elseif ($report_type === 'User Feedback Report') {
            $date_filter = str_replace("transaction_date", "created_at", $date_filter);
            $query = "SELECT user_name, feedback_text, feedback_type, created_at 
                      FROM feedbackadmin $date_filter 
                      ORDER BY created_at DESC";
            $report_data = mysqli_query($conn, $query);
            if (!$report_data) {
                $report_errors[] = "Error generating feedback report: " . mysqli_error($conn);
            }
        }
    }
}

// Fetch revenue data
$revenue_query = "SELECT route_name, revenue, tickets_sold, (revenue / tickets_sold) AS avg_price 
                 FROM routes 
                 WHERE tickets_sold > 0";
$revenue_result = mysqli_query($conn, $revenue_query);
if (!$revenue_result) {
    die("Error fetching revenue data: " . mysqli_error($conn));
}

// Fetch promotions
$promos_query = "SELECT promo_code, discount_type, discount_value, route 
                FROM promotions";
$promos_result = mysqli_query($conn, $promos_query);
if (!$promos_result) {
    die("Error fetching promotions: " . mysqli_error($conn));
}

// Fetch feedback with responses
$feedback_query = "SELECT f.id, f.user_name, f.feedback_text, f.feedback_type, 
                          r.response_text, r.created_at AS response_date
                  FROM feedbackadmin f
                  LEFT JOIN responses r ON f.id = r.feedback_id
                  ORDER BY f.created_at DESC";
$feedback_result = mysqli_query($conn, $feedback_query);
if (!$feedback_result) {
    die("Error fetching feedback: " . mysqli_error($conn));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBUS|Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>
<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="#"><i class="fa-solid fa-user-circle"></i> Admin</a>
        </div>
    </header>

    <nav class="sidebar">
        <ul>
            <li><a href="#revenue" class="sidebar-link <?php echo $active_section === 'revenue' ? 'active' : ''; ?>" data-section="revenue">Revenue Tracking</a></li>
            <li><a href="#discounts" class="sidebar-link <?php echo $active_section === 'discounts' ? 'active' : ''; ?>" data-section="discounts">Discounts & Promotions</a></li>
            <li><a href="#reports" class="sidebar-link <?php echo $active_section === 'reports' ? 'active' : ''; ?>" data-section="reports">Report Generation</a></li>
            <li><a href="#feedback" class="sidebar-link <?php echo $active_section === 'feedback' ? 'active' : ''; ?>" data-section="feedback">Feedback Handling</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <section id="revenue" class="section" style="display: <?php echo $active_section === 'revenue' ? 'block' : 'none'; ?>;">
            <h2>Revenue Tracking per Route</h2>
            <table class="revenue-table">
                <thead>
                    <tr>
                        <th>Route</th>
                        <th>Revenue (USD)</th>
                        <th>Tickets Sold</th>
                        <th>Average Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($revenue_result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['route_name']); ?></td>
                            <td><?php echo number_format($row['revenue'], 2); ?></td>
                            <td><?php echo $row['tickets_sold']; ?></td>
                            <td><?php echo number_format($row['avg_price'], 2); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (mysqli_num_rows($revenue_result) == 0) { ?>
                        <tr>
                            <td colspan="4">No revenue data available.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <section id="discounts" class="section" style="display: <?php echo $active_section === 'discounts' ? 'block' : 'none'; ?>;">
            <h2>Discounts & Promotions</h2>
            <?php if (!empty($promo_errors)) { ?>
                <div style="color: red;">
                    <ul>
                        <?php foreach ($promo_errors as $error) { ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <?php if ($success_message) { ?>
                <div style="color: green;"><?php echo htmlspecialchars($success_message); ?></div>
            <?php } ?>
            <form class="promotion-form" method="POST" action="">
                <label for="promo-code">Promo Code:</label>
                <input type="text" id="promo-code" name="promo_code" placeholder="Enter promo code" maxlength="20">

                <label for="discount-type">Discount Type:</label>
                <select id="discount-type" name="discount_type">
                    <option value="Percentage">Percentage</option>
                    <option value="Fixed Amount">Fixed Amount</option>
                </select>

                <label for="discount-value">Value:</label>
                <input type="number" id="discount-value" name="discount_value" placeholder="Enter value" min="0" step="0.01">

                <label for="route">Applicable Route:</label>
                <input type="text" id="route" name="route" placeholder="Enter route">

                <button type="submit" name="add_promo">Add Promotion</button>
            </form>

            <div class="promotion-list">
                <h3>Current Promotions</h3>
                <ul>
                    <?php while ($promo = mysqli_fetch_assoc($promos_result)) { ?>
                        <li>Code: <?php echo htmlspecialchars($promo['promo_code']); ?> - 
                            <?php echo $promo['discount_type'] == 'Percentage' ? 
                                $promo['discount_value'] . '% off' : 
                                '$' . number_format($promo['discount_value'], 2) . ' off'; ?> 
                            on <?php echo htmlspecialchars($promo['route']); ?> routes
                        </li>
                    <?php } ?>
                    <?php if (mysqli_num_rows($promos_result) == 0) { ?>
                        <li>No promotions available.</li>
                    <?php } ?>
                </ul>
            </div>
        </section>

        <section id="reports" class="section" style="display: <?php echo $active_section === 'reports' ? 'block' : 'none'; ?>;">
            <h2>Report Generation</h2>
            <?php if (!empty($report_errors)) { ?>
                <div style="color: red;">
                    <ul>
                        <?php foreach ($report_errors as $error) { ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <form class="report-form" method="POST" action="">
                <label for="report-type">Report Type:</label>
                <select id="report-type" name="report_type">
                    <option value="Revenue Report" <?php echo $report_type === 'Revenue Report' ? 'selected' : ''; ?>>Revenue Report</option>
                    <option value="Sales Report" <?php echo $report_type === 'Sales Report' ? 'selected' : ''; ?>>Sales Report</option>
                    <option value="User Feedback Report" <?php echo $report_type === 'User Feedback Report' ? 'selected' : ''; ?>>User Feedback Report</option>
                </select>

                <label for="date-range">Date Range:</label>
                <select id="date-range" name="date_range" onchange="toggleCustomDates(this)">
                    <option value="Last 7 Days" <?php echo $date_range === 'Last 7 Days' ? 'selected' : ''; ?>>Last 7 Days</option>
                    <option value="Last 30 Days" <?php echo $date_range === 'Last 30 Days' ? 'selected' : ''; ?>>Last 30 Days</option>
                    <option value="Custom" <?php echo $date_range === 'Custom' ? 'selected' : ''; ?>>Custom</option>
                </select>

                <div id="custom-dates" style="display: <?php echo $date_range === 'Custom' ? 'block' : 'none'; ?>;">
                    <label for="custom-start">Start Date:</label>
                    <input type="date" id="custom-start" name="custom_start" value="<?php echo htmlspecialchars($custom_start ?? ''); ?>">
                    <label for="custom-end">End Date:</label>
                    <input type="date" id="custom-end" name="custom_end" value="<?php echo htmlspecialchars($custom_end ?? ''); ?>">
                </div>

                <button type="submit" name="generate_report">Generate Report</button>
            </form>

            <?php if ($report_data && mysqli_num_rows($report_data) > 0) { ?>
                <div class="report-results">
                    <h3><?php echo htmlspecialchars($report_type); ?> (<?php echo htmlspecialchars($date_range); ?>)</h3>
                    <?php if ($report_type === 'Revenue Report') { ?>
                        <table class="revenue-table">
                            <thead>
                                <tr>
                                    <th>Route</th>
                                    <th>Total Revenue (USD)</th>
                                    <th>Total Tickets Sold</th>
                                    <th>Average Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($report_data)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['route_name']); ?></td>
                                        <td><?php echo number_format($row['total_revenue'], 2); ?></td>
                                        <td><?php echo $row['total_tickets']; ?></td>
                                        <td><?php echo number_format($row['avg_price'], 2); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } elseif ($report_type === 'Sales Report') { ?>
                        <table class="revenue-table">
                            <thead>
                                <tr>
                                    <th>Route</th>
                                    <th>Tickets Sold</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($report_data)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['route_name']); ?></td>
                                        <td><?php echo $row['tickets_sold']; ?></td>
                                        <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } elseif ($report_type === 'User Feedback Report') { ?>
                        <div class="feedback-list">
                            <?php while ($row = mysqli_fetch_assoc($report_data)) { ?>
                                <div class="feedback-item">
                                    <h4>User: <?php echo htmlspecialchars($row['user_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($row['feedback_type']); ?>: 
                                       <?php echo htmlspecialchars($row['feedback_text']); ?></p>
                                    <p><strong>Date:</strong> <?php echo date('Y-m-d H:i:s', strtotime($row['created_at'])); ?></p>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } elseif ($report_data && mysqli_num_rows($report_data) == 0) { ?>
                <div class="report-results">
                    <p>No data available for <?php echo htmlspecialchars($report_type); ?> in the selected date range.</p>
                </div>
            <?php } ?>
        </section>

        <section id="feedback" class="section" style="display: <?php echo $active_section === 'feedback' ? 'block' : 'none'; ?>;">
            <h2>Feedback and Complaint Handling</h2>
            <?php if (!empty($response_errors)) { ?>
                <div style="color: red;">
                    <ul>
                        <?php foreach ($response_errors as $error) { ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <?php if ($response_success) { ?>
                <div style="color: green;"><?php echo htmlspecialchars($response_success); ?></div>
            <?php } ?>
            <div class="feedback-list">
                <?php while ($feedback = mysqli_fetch_assoc($feedback_result)) { ?>
                    <div class="feedback-item">
                        <h4>User: <?php echo htmlspecialchars($feedback['user_name']); ?></h4>
                        <p><?php echo htmlspecialchars($feedback['feedback_type']); ?>: 
                           <?php echo htmlspecialchars($feedback['feedback_text']); ?></p>
                        <?php if ($feedback['response_text']) { ?>
                            <p><strong>Response:</strong> <?php echo htmlspecialchars($feedback['response_text']); ?> 
                               (<?php echo date('Y-m-d H:i:s', strtotime($feedback['response_date'])); ?>)</p>
                        <?php } else { ?>
                            <form method="POST" action="">
                                <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                                <label for="response-text-<?php echo $feedback['id']; ?>">Response:</label>
                                <textarea id="response-text-<?php echo $feedback['id']; ?>" name="response_text" placeholder="Enter your response" maxlength="500"></textarea>
                                <button type="submit" name="submit_response">Submit Response</button>
                            </form>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (mysqli_num_rows($feedback_result) == 0) { ?>
                    <div class="feedback-item">
                        <p>No feedback available.</p>
                    </div>
                <?php } ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="footerContainer">
            <div class="footerSection">
                <h2>GO BUS</h2>
                <p>
                    gobus.com is a premium online booking portal which allows you to purchase ticket 
                    for various bus booking services locally across the country.
                </p>
            </div>

            <div class="footerSection">
                <h3>Company Info</h3>
                <a href="#">Terms and Condition</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Cancellation Policy</a>
            </div>

            <div class="footerSection">
                <h3>About GoBUS</h3>
                <a href="#">About Us</a>
                <a href="#">Contact Us</a>
            </div>
        </div>

        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group</span>
        </div>
    </footer>

    <script>
        function toggleCustomDates(select) {
            const customDates = document.getElementById('custom-dates');
            customDates.style.display = select.value === 'Custom' ? 'block' : 'none';
        }
        // Pass active section to JavaScript
        window.activeSection = '<?php echo $active_section; ?>';
    </script>
    <script src="../data/admin_dashboard.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
?>