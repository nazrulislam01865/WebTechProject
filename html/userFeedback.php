
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus|User Feedback</title>
    <link rel="stylesheet" type="text/css" href="../css/userFeedback.css">
</head>
<body>
    <?php
    session_start();

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    $username = htmlspecialchars($_SESSION['username']);
    $errors = [];
    $success = "";
    $form_data = [
        'booking_id' => $_POST['booking_id'] ?? '',
        'rating' => $_POST['rating'] ?? '',
        'comments' => $_POST['comments'] ?? ''
    ];
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
            $sql = "SELECT booking_id, route FROM bookings WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row;
            }
            $stmt->close();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (empty($form_data['booking_id']) || $form_data['booking_id'] === 'Select Booking') {
                    $errors['booking_id'] = "Please select a valid booking.";
                } else {
                    $sql = "SELECT booking_id FROM bookings WHERE user_id = ? AND booking_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $_SESSION['user_id'], $form_data['booking_id']);
                    $stmt->execute();
                    if ($stmt->get_result()->num_rows === 0) {
                        $errors['booking_id'] = "Invalid booking selected.";
                    }
                    $stmt->close();
                }

                if (!in_array($form_data['rating'], ['1', '2', '3', '4', '5'])) {
                    $errors['rating'] = "Please select a valid rating (1-5 stars).";
                }

                if (!empty($form_data['comments']) && strlen($form_data['comments']) > 500) {
                    $errors['comments'] = "Comments must be 500 characters or less.";
                }

                if (empty($errors)) {
                    $sql = "INSERT INTO feedback (user_id, booking_id, rating, comments) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isis", $_SESSION['user_id'], $form_data['booking_id'], $form_data['rating'], $form_data['comments']);
                    
                    if ($stmt->execute()) {
                        $success = "Feedback submitted successfully!";
                    } else {
                        $errors['general'] = "Error submitting feedback: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
            $conn->close();
        }
    } catch (mysqli_sql_exception $e) {
        $errors['general'] = "Database error: " . $e->getMessage();
    }
    ?>

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
            <li><a href="userFeedback.php" class="sidebar-link active">Share Feedback</a></li>
            <li><a href="userAccountSettings.php" class="sidebar-link">Account Settings</a></li>
            <li><a href="../index.php" class="sidebar-link">Search Bus</a></li>
            <li><a href="./userComplaint.php" class="sidebar-link">Complain</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <section id="feedback" class="section">
            <h2>Share Your Feedback</h2>
            <?php if (!empty($success)): ?>
                <div style="color: green; text-align: center;">
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
            <?php endif; ?>
            <?php if (isset($errors['general'])): ?>
                <div style="color: red; text-align: center;">
                    <p><?php echo htmlspecialchars($errors['general']); ?></p>
                </div>
            <?php endif; ?>
            <form class="feedback-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <label for="booking-id">Booking ID:</label>
                <select id="booking-id" name="booking_id">
                    <option value="Select Booking">Select Booking</option>
                    <?php foreach ($bookings as $booking): ?>
                        <option value="<?php echo htmlspecialchars($booking['booking_id']); ?>" <?php echo $form_data['booking_id'] === $booking['booking_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($booking['booking_id'] . " - " . $booking['route']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['booking_id'])): ?>
                    <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['booking_id']); ?></span>
                <?php endif; ?>

                <label for="rating">Rating:</label>
                <select id="rating" name="rating">
                    <option value="">Select Rating</option>
                    <option value="1" <?php echo $form_data['rating'] === '1' ? 'selected' : ''; ?>>1 Star</option>
                    <option value="2" <?php echo $form_data['rating'] === '2' ? 'selected' : ''; ?>>2 Stars</option>
                    <option value="3" <?php echo $form_data['rating'] === '3' ? 'selected' : ''; ?>>3 Stars</option>
                    <option value="4" <?php echo $form_data['rating'] === '4' ? 'selected' : ''; ?>>4 Stars</option>
                    <option value="5" <?php echo $form_data['rating'] === '5' ? 'selected' : ''; ?>>5 Stars</option>
                </select>
                <?php if (isset($errors['rating'])): ?>
                    <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['rating']); ?></span>
                <?php endif; ?>

                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments" placeholder="Share your experience" rows="5"><?php echo htmlspecialchars($form_data['comments']); ?></textarea>
                <?php if (isset($errors['comments'])): ?>
                    <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['comments']); ?></span>
                <?php endif; ?>

                <button type="submit">Submit Feedback</button>
            </form>
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