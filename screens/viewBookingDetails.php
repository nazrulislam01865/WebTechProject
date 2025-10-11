<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$errors = [];
$booking = null;

//Model
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "gobus";

try {
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        $errors['general'] = "Database connection failed: " . $conn->connect_error;
    } else {
        //Controller
        $booking_id = $_GET['booking_id'] ?? '';
        if (empty($booking_id)) {
            $errors['general'] = "No booking ID provided.";
        } else {
            //Model
            $sql = "SELECT booking_id, route, date, status, operator_name, seat_number 
                    FROM bookings 
                    WHERE booking_id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $booking_id, $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) {
                $errors['general'] = "Booking not found or you are not authorized to view this booking.";
            } else {
                $booking = $result->fetch_assoc();
            }
            $stmt->close();
            $conn->close();
        }
    }
} catch (mysqli_sql_exception $e) {
    $errors['general'] = "Database error: " . $e->getMessage();
}
?>

<!-- VIEW -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus | Booking Details</title>
    <link rel="stylesheet" type="text/css" href="../css/userDashboard.css">
</head>
<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="#" class="logout-btn" onclick="return confirm('Do you want to log out?') ? window.location.href='logout.php' : false;">
                <img src="../picture/user_logo.png" alt="User Icon" style="width:18px; height:18px; vertical-align: middle;"> <?php echo $username; ?>
            </a>
        </div>
    </header>

    <nav class="sidebar">
        <ul>
            <li><a href="userDashboard.php" class="sidebar-link">Booking Details</a></li>
            <li><a href="userFeedback.php" class="sidebar-link">Share Feedback</a></li>
            <li><a href="userAccountSettings.php" class="sidebar-link">Account Settings</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <section id="booking-details" class="section">
            <h2>Booking Details</h2>
            <?php if (isset($errors['general'])): ?>
                <div style="color: red; text-align: center;">
                    <p><?php echo htmlspecialchars($errors['general']); ?></p>
                </div>
            <?php elseif ($booking): ?>
                <div class="booking-details">
                    <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($booking['booking_id']); ?></p>
                    <p><strong>Route:</strong> <?php echo htmlspecialchars($booking['route']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($booking['date']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
                    <p><strong>Bus Operator:</strong> <?php echo htmlspecialchars($booking['operator_name'] ?: 'N/A'); ?></p>
                    <p><strong>Seat Number:</strong> <?php echo htmlspecialchars($booking['seat_number'] ?: 'N/A'); ?></p>
                    <a href="userDashboard.php" class="view-btn">Back to Dashboard</a>
                </div>
            <?php endif; ?>
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
                <h3>About GoBUS</h3>
                <a href="./aboutUs.php">About Us</a>
                <a href="./contact.php">Contact Us</a>
            </div>

            <div class="footerSection">
                <h3>Company Info</h3>
                <a href="./terms.php">Terms and Condition</a>
                <a href="./privacy.php">Privacy Policy</a>
            </div>
        </div>

        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
        </div>
    </footer>
</body>
</html>
