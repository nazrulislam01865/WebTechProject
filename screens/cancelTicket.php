
<?php
// Start session at the very top
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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
$required_tables = ['bookings', 'users'];
$missing_tables = [];
foreach ($required_tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) == 0) {
        $missing_tables[] = $table;
    }
}
if (!empty($missing_tables)) {
    die("Error: The following database tables are missing: " . implode(", ", $missing_tables) . ". Please verify the 'bookings' and 'users' tables using the provided SQL.");
}

// Handle form submission (Cancel Booking)
$errors = [];
$booking_id = $_GET['booking_id'] ?? ($_POST['booking_id'] ?? '');
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_booking'])) {
    $booking_id = trim($_POST['booking_id'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $logged_in_user_id = $_SESSION['user_id'];

    // Validations
    if (empty($booking_id)) {
        $errors[] = "Booking ID is required.";
    } else {
        // Check if booking exists, is Upcoming, and belongs to the logged-in user
        $stmt = $conn->prepare("SELECT b.user_id, b.status, u.phone
                                FROM bookings b 
                                JOIN users u ON b.user_id = u.id 
                                WHERE b.booking_id = ?");
        if (!$stmt) {
            $errors[] = "Error preparing booking check: " . mysqli_error($conn);
        } else {
            $stmt->bind_param("s", $booking_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) {
                $errors[] = "Invalid booking ID.";
            } else {
                $booking = $result->fetch_assoc();
                if ($booking['status'] !== 'Upcoming') {
                    $errors[] = "This booking is not eligible for cancellation.";
                }
                if ($booking['user_id'] != $logged_in_user_id) {
                    $errors[] = "You are not authorized to cancel this booking.";
                }
                $stored_phone = $booking['phone'];
            }
            $stmt->close();
        }
    }

    if (empty($phone_number)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^\d{11}$/', $phone_number)) {
        $errors[] = "Phone number must be an 11-digit number.";
    } elseif (isset($stored_phone) && $phone_number !== $stored_phone) {
        $errors[] = "Phone number does not match your account.";
    }

    // Cancel booking if no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ? AND status = 'Upcoming'");
        if (!$stmt) {
            $errors[] = "Error preparing status update: " . mysqli_error($conn);
        } else {
            $stmt->bind_param("s", $booking_id);
            if ($stmt->execute()) {
                // Output JavaScript for alert and redirect
                echo "<script>
                        alert('Booking cancelled successfully!');
                        setTimeout(function() {
                            window.location.href = 'userDashboard.php';
                        }, 2000);
                      </script>";
                exit();
            } else {
                $errors[] = "Error cancelling booking: " . mysqli_error($conn);
            }
            $stmt->close();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus|Cancel Booking</title>
    <link rel="stylesheet" type="text/css" href="../css/cancelTicket1.css">
</head>
<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="tel:+8801234567890" class="call-btn">Call +8801234567890</a>
            <a href="logout.php" class="logout-btn"><img src="../picture/logout.png" alt="Logout Icon" style="width: 18px; height: 18px; vertical-align: middle;"></i> Logout</a>
        </div>
    </header>
    <div class="nav-bar">
        <div class="navbar-text">
            Home &gt; Cancel Booking
        </div>
    </div>
    <div class="container">
        <form method="POST" action="">
            <div class="form-container">
                <h1>Cancel Booking</h1>
                <?php if (!empty($errors)) { ?>
                    <div style="color: red;">
                        <ul>
                            <?php foreach ($errors as $error) { ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <label for="booking-id">Booking ID<sup>*</sup></label>
                    <div class="input-box">
                        <input type="text" id="booking-id" name="booking_id" placeholder="Enter Booking ID" value="<?php echo htmlspecialchars($booking_id); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone-number">Phone No.<sup>*</sup></label>
                    <div class="input-box">
                        <input type="text" id="phone-number" name="phone_number" placeholder="Enter Phone Number" value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ''); ?>" required>
                    </div>
                </div>
                <button type="submit" name="cancel_booking" class="otp-btn">Cancel Booking</button>
            </div>
            <div class="info-box">
                <p>1. In case you are unable to cancel your booking online, please call +8801234567890.</p>
                <p>2. Cancellation policies differ from operator to operator and are not set by GoBUS.com.</p>
                <p>3. Please read our <a href="../html/cancellation_policy.html">Cancellation and Refund policies</a> before cancelling your booking.</p>
            </div>
        </form>
    </div>
    <footer>
        <div class="footerContainer">
            <div class="footerSection">
                <h2>GO BUS</h2>
                <p>
                    gobus.com is a premium online booking portal which allows you to purchase tickets
                    for various bus booking services locally across the country.
                </p>
            </div>
            <div class="footerSection">
                <h3>About GoBUS</h3>
                <a href="../index.php">Home</a>
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
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group</span>
        </div>
    </footer>
</body>
</html>

<?php
mysqli_close($conn);
?>

