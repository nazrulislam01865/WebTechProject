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
$success_message = "";
$bookings = [];
$complaints = [];

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

        $sql = "SELECT booking_id, route, date, status FROM bookings WHERE user_id = ? AND status IN ('Completed', 'Upcoming')";
        $stmt_bookings = $conn->prepare($sql);
        $stmt_bookings->bind_param("i", $_SESSION['user_id']);
        $stmt_bookings->execute();
        $result = $stmt_bookings->get_result();
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        $stmt_bookings->close();


        function fetchComplaints($conn, $user_id) {
            $sql = "SELECT c.id, c.booking_id, b.route, c.complaint_type, c.description, c.status, c.created_at, 
                           r.response_text, r.created_at AS response_date
                    FROM complaints c
                    LEFT JOIN bookings b ON c.booking_id = b.booking_id
                    LEFT JOIN responses r ON c.id = r.item_id AND r.item_type = 'Complaint'
                    WHERE c.user_id = ?
                    ORDER BY c.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $complaints = [];
            while ($row = $result->fetch_assoc()) {
                $complaints[] = $row;
            }
            $stmt->close();
            return $complaints;
        }


        $complaints = fetchComplaints($conn, $_SESSION['user_id']);
        //Controller
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_complaint'])) {
            $booking_id = $_POST['booking_id'] ?? '';
            $complaint_type = $_POST['complaint_type'] ?? '';
            $description = trim($_POST['description'] ?? '');

            if (empty($booking_id)) {
                $errors['booking_id'] = "Please select a booking.";
            } else {
                $sql = "SELECT booking_id FROM bookings WHERE booking_id = ? AND user_id = ?";
                $stmt_validate = $conn->prepare($sql);
                $stmt_validate->bind_param("si", $booking_id, $_SESSION['user_id']);
                $stmt_validate->execute();
                $result = $stmt_validate->get_result();
                if ($result->num_rows == 0) {
                    $errors['booking_id'] = "Invalid booking selected.";
                }
                $stmt_validate->close();
            }

            if (!in_array($complaint_type, ['Service', 'Driver', 'Vehicle', 'Other'])) {
                $errors['complaint_type'] = "Please select a valid complaint type.";
            }

            if (empty($description)) {
                $errors['description'] = "Complaint description is required.";
            } elseif (strlen($description) > 500) {
                $errors['description'] = "Description must be 500 characters or less.";
            }

            if (empty($errors)) {
                $sql = "INSERT INTO complaints (user_id, booking_id, complaint_type, description) VALUES (?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql);
                $stmt_insert->bind_param("isss", $_SESSION['user_id'], $booking_id, $complaint_type, $description);
                if ($stmt_insert->execute()) {
                    $success_message = "Complaint submitted successfully!";
                    $complaints = fetchComplaints($conn, $_SESSION['user_id']);
                } else {
                    $errors['general'] = "Error submitting complaint: " . $conn->error;
                }
                $stmt_insert->close();
            }
        }

        $conn->close();
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
    <title>GoBus|File a Complaint</title>
    <link rel="stylesheet" type="text/css" href="../css/userComplaint.css">
</head>
<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="#" class="logout-btn" onclick="return confirm('Do you want to log out?') ? window.location.href='logout.php' : false;">
                <img src="../picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> <?php echo $username; ?>
            </a>
        </div>
    </header>

    <nav class="sidebar">
        <ul>
            <li><a href="userDashboard.php" class="sidebar-link">Booking Details</a></li>
            <li><a href="userFeedback.php" class="sidebar-link">Share Feedback</a></li>
            <li><a href="userAccountSettings.php" class="sidebar-link">Account Settings</a></li>
            <li><a href="../index.php" class="sidebar-link">Search Bus</a></li>
            <li><a href="./userComplaint.php" class="sidebar-link active">Complain</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <section id="complaints" class="section">
            <h2>File a Complaint</h2>
            <?php if (isset($errors['general'])): ?>
                <div style="color: red; text-align: center;">
                    <p><?php echo htmlspecialchars($errors['general']); ?></p>
                </div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div style="color: green; text-align: center;">
                    <p><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            <?php endif; ?>
            <?php if (empty($bookings)): ?>
                <div style="text-align: center;">
                    <p>No eligible bookings found for filing a complaint.</p>
                </div>
            <?php else: ?>
                <form class="complaint-form" method="POST" action="">
                    <label for="booking-id">Select Booking:</label>
                    <select id="booking-id" name="booking_id">
                        <option value="">-- Select a Booking --</option>
                        <?php foreach ($bookings as $booking): ?>
                            <option value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                                <?php echo htmlspecialchars($booking['booking_id'] . ' - ' . $booking['route'] . ' (' . $booking['date'] . ', ' . $booking['status'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['booking_id'])): ?>
                        <div style="color: red;"><?php echo htmlspecialchars($errors['booking_id']); ?></div>
                    <?php endif; ?>

                    <label for="complaint-type">Complaint Type:</label>
                    <select id="complaint-type" name="complaint_type">
                        <option value="">-- Select Complaint Type --</option>
                        <option value="Service">Service</option>
                        <option value="Driver">Driver</option>
                        <option value="Vehicle">Vehicle</option>
                        <option value="Other">Other</option>
                    </select>
                    <?php if (isset($errors['complaint_type'])): ?>
                        <div style="color: red;"><?php echo htmlspecialchars($errors['complaint_type']); ?></div>
                    <?php endif; ?>

                    <label for="description">Complaint Description:</label>
                    <textarea id="description" name="description" placeholder="Describe your complaint (max 500 characters)" maxlength="500"></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div style="color: red;"><?php echo htmlspecialchars($errors['description']); ?></div>
                    <?php endif; ?>

                    <button type="submit" name="submit_complaint">Submit Complaint</button>
                </form>
            <?php endif; ?>

            <h2>Your Complaints</h2>
            <div class="complaint-list">
                <?php if (empty($complaints)): ?>
                    <div class="complaint-item">
                        <p>No complaints found.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($complaints as $complaint): ?>
                        <div class="complaint-item">
                            <h4>Complaint ID: <?php echo htmlspecialchars($complaint['id']); ?> 
                                (Booking ID: <?php echo htmlspecialchars($complaint['booking_id']); ?>, 
                                Route: <?php echo htmlspecialchars($complaint['route'] ?? 'Unknown'); ?>)</h4>
                            <p><strong>Type:</strong> <?php echo htmlspecialchars($complaint['complaint_type']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($complaint['description']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($complaint['status']); ?></p>
                            <p><strong>Date Submitted:</strong> <?php echo date('Y-m-d H:i:s', strtotime($complaint['created_at'])); ?></p>
                            <?php if ($complaint['response_text']): ?>
                                <p><strong>Response:</strong> <?php echo htmlspecialchars($complaint['response_text']); ?> 
                                   (<?php echo date('Y-m-d H:i:s', strtotime($complaint['response_date'])); ?>)</p>
                            <?php else: ?>
                                <p><strong>Response:</strong> No response yet.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
                <h3>About GoBUS</h3>
                <a href="aboutUs.php">About Us</a>
                <a href="contact.php">Contact Us</a>
            </div>

            <div class="footerSection">
                <h3>Company Info</h3>
                <a href="terms.php">Terms and Condition</a>
                <a href="privacy.php">Privacy Policy</a>
            </div>
        </div>

        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
        </div>
    </footer>
</body>
</html>
