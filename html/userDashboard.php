
<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

echo "<!-- DEBUG: Session - user_id: " . ($_SESSION['user_id'] ?? 'not set') . ", username: " . ($_SESSION['username'] ?? 'not set') . " -->";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$errors = [];
$bookings = [];

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "gobus";

try {
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        $errors['general'] = "Database connection failed: " . $conn->connect_error;
    } else {
        $sql = "SELECT booking_id, route, date, status FROM bookings WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        $stmt->close();
        $conn->close();
    }
} catch (mysqli_sql_exception $e) {
    $errors['general'] = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus|User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../css/userDashboard.css">
</head>
<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="#" class="logout-btn" onclick="return confirm('Do you want to log out?') ? window.location.href='logout.php' : false;">
                <i class="fa-solid fa-user-circle"></i> <?php echo $username; ?>
            </a>
        </div>
    </header>

    <nav class="sidebar">
        <ul>
            <li><a href="userDashboard.php" class="sidebar-link active">Booking Details</a></li>
            <li><a href="userFeedback.php" class="sidebar-link">Share Feedback</a></li>
            <li><a href="userAccountSettings.php" class="sidebar-link">Account Settings</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <section id="bookings" class="section">
            <h2>Booking Details</h2>
            <?php if (isset($errors['general'])): ?>
                <div style="color: red; text-align: center;">
                    <p><?php echo htmlspecialchars($errors['general']); ?></p>
                </div>
            <?php endif; ?>
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Route</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No bookings found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                <td><?php echo htmlspecialchars($booking['route']); ?></td>
                                <td><?php echo htmlspecialchars($booking['date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                <td>
                                    <a href="./viewBookingDetails.php?booking_id=<?php echo urlencode($booking['booking_id']); ?>" class="view-btn">View Details</a>
                                    <?php if ($booking['status'] === 'Upcoming'): ?>
                                        <a href="cancelTicket.php?booking_id=<?php echo urlencode($booking['booking_id']); ?>" class="cancel-btn">Cancel Booking</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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
                <a href="#">About Us</a>
                <a href="#">Contact Us</a>
                <a href="cancelTicket.php">Cancel Ticket</a>
            </div>

            <div class="footerSection">
                <h3>Company Info</h3>
                <a href="#">Terms and Condition</a>
                <a href="#">Privacy Policy</a>
            </div>
        </div>

        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
        </div>
    </footer>
</body>
</html>