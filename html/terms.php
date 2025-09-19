<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - GoBus</title>
    <link rel="stylesheet" href="../css/terms.css">

<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>

        <div class="header-right">
            <a href="tel:xxxxxxxxxxxxx" class="call-btn">Call +880</a>
            <a href="html/login.html" class="login-btn"><i class="fa-solid fa-user-circle"></i> Login</a>
        </div>
    </header>
    <div class="container">
        <section class="terms-hero">
            <h2>Terms & Conditions</h2>
            <p>Please read these terms carefully before using our services</p>
        </section>

        <section class="terms-content">
            <div class="terms-section">
                <h3>1. Ticket Booking</h3>
                <p>When you book a ticket with GoBus, you agree to:</p>
                <ul>
                    <li>Provide accurate passenger information</li>
                    <li>Pay the correct fare</li>
                    <li>Arrive at the boarding point at least 30 minutes before departure</li>
                </ul>
            </div>

            <div class="terms-section">
                <h3>2. Cancellation & Refunds</h3>
                <p>Our cancellation policy:</p>
                <ul>
                    <li>Cancellation 24+ hours before departure: Full refund</li>
                    <li>Cancellation 6-24 hours before departure: 50% refund</li>
                    <li>Cancellation less than 6 hours before departure: No refund</li>
                </ul>
            </div>

            <div class="terms-section">
                <h3>3. Travel Rules</h3>
                <p>During your journey with GoBus:</p>
                <ul>
                    <li>Keep your ticket with you at all times</li>
                    <li>Follow the instructions of our staff</li>
                    <li>No smoking or alcohol consumption on the bus</li>
                    <li>Wear your seatbelt when the bus is moving</li>
                </ul>
            </div>

            <div class="terms-section">
                <h3>4. Luggage</h3>
                <p>Our luggage policy:</p>
                <ul>
                    <li>Each passenger can bring one suitcase (max 20kg)</li>
                    <li>One small handbag is allowed</li>
                    <li>Dangerous items are not permitted</li>
                    <li>We are not responsible for lost or damaged luggage</li>
                </ul>
            </div>

            <div class="terms-section">
                <h3>5. Changes & Delays</h3>
                <p>Sometimes changes happen:</p>
                <ul>
                    <li>Bus schedules may change due to weather or road conditions</li>
                    <li>We will inform you of any changes as soon as possible</li>
                    <li>No compensation for delays due to traffic or weather</li>
                </ul>
            </div>

            <div class="terms-section">
                <h3>6. Behavior</h3>
                <p>We expect all passengers to:</p>
                <ul>
                    <li>Be respectful to other passengers and staff</li>
                    <li>Keep noise levels reasonable</li>
                    <li>Keep the bus clean and tidy</li>
                    <li>Not cause any disturbance or damage</li>
                </ul>
            </div>

            <div class="terms-note">
                <p><strong>Note:</strong> By using GoBus services, you agree to these terms and conditions. We may
                    update these terms from time to time.</p>
                <p>Last updated: August 2025</p>
            </div>
        </section>

    </div>

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
                <a href="../html/about us.html">About Us</a>
                <a href="../html/contact.html">Contact Us</a>
                <a href="../html/cancelTicket.html">Cancel Ticket</a>
            </div>

            <div class="footerSection">
                <h3>Company Info</h3>
                <a href="../html/terms.html">Terms and Condition</a>
                <a href="../html/privacy.html">Privacy Policy</a>
            </div>
        </div>

        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span></p>
        </div>
    </footer>
</body>

</html> -->


<?php
session_start(); // Start session for consistent user experience

// Database connection settings (included for consistency, but not used in this page)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gobus";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Create database if it doesn't exist
    if (!$conn->query("CREATE DATABASE IF NOT EXISTS gobus")) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    $conn->select_db($dbname);
} catch (Exception $e) {
    // Log error silently (no display, as there's no form or output)
    error_log($e->getMessage());
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - GoBus</title>
    <link rel="stylesheet" href="../css/terms.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="tel:+8809611123456" class="call-btn">Call +880</a>
            <a href="login.php" class="login-btn"><i class="fa-solid fa-user-circle"></i> Login</a>
        </div>
    </header>
    
    <div class="container">
        <section class="terms-hero">
            <h2>Terms & Conditions</h2>
            <p>Please read these terms carefully before using our services</p>
        </section>
        <section class="terms-content">
            <div class="terms-section">
                <h3>1. Ticket Booking</h3>
                <p>When you book a ticket with GoBus, you agree to:</p>
                <ul>
                    <li>Provide accurate passenger information</li>
                    <li>Pay the correct fare</li>
                    <li>Arrive at the boarding point at least 30 minutes before departure</li>
                </ul>
            </div>
            <div class="terms-section">
                <h3>2. Cancellation & Refunds</h3>
                <p>Our cancellation policy:</p>
                <ul>
                    <li>Cancellation 24+ hours before departure: Full refund</li>
                    <li>Cancellation 6-24 hours before departure: 50% refund</li>
                    <li>Cancellation less than 6 hours before departure: No refund</li>
                </ul>
            </div>
            <div class="terms-section">
                <h3>3. Travel Rules</h3>
                <p>During your journey with GoBus:</p>
                <ul>
                    <li>Keep your ticket with you at all times</li>
                    <li>Follow the instructions of our staff</li>
                    <li>No smoking or alcohol consumption on the bus</li>
                    <li>Wear your seatbelt when the bus is moving</li>
                </ul>
            </div>
            <div class="terms-section">
                <h3>4. Luggage</h3>
                <p>Our luggage policy:</p>
                <ul>
                    <li>Each passenger can bring one suitcase (max 20kg)</li>
                    <li>One small handbag is allowed</li>
                    <li>Dangerous items are not permitted</li>
                    <li>We are not responsible for lost or damaged luggage</li>
                </ul>
            </div>
            <div class="terms-section">
                <h3>5. Changes & Delays</h3>
                <p>Sometimes changes happen:</p>
                <ul>
                    <li>Bus schedules may change due to weather or road conditions</li>
                    <li>We will inform you of any changes as soon as possible</li>
                    <li>No compensation for delays due to traffic or weather</li>
                </ul>
            </div>
            <div class="terms-section">
                <h3>6. Behavior</h3>
                <p>We expect all passengers to:</p>
                <ul>
                    <li>Be respectful to other passengers and staff</li>
                    <li>Keep noise levels reasonable</li>
                    <li>Keep the bus clean and tidy</li>
                    <li>Not cause any disturbance or damage</li>
                </ul>
            </div>
            <div class="terms-note">
                <p><strong>Note:</strong> By using GoBus services, you agree to these terms and conditions. We may
                    update these terms from time to time.</p>
                <p>Last updated: August 2025</p>
            </div>
        </section>
    </div>

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
                <a href="aboutus.php">About Us</a>
                <a href="contact.php">Contact Us</a>
                <a href="cancelTicket.php">Cancel Ticket</a>
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