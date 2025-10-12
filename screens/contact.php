<?php
session_start(); 

//Model
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gobus";

$purpose = $name = $phone = $email = $city = $message = "";
$purpose_error = $name_error = $phone_error = $email_error = $city_error = $message_error = "";
$success = "";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    if (!$conn->query("CREATE DATABASE IF NOT EXISTS gobus")) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    $conn->select_db($dbname);

    $createTable = "CREATE TABLE IF NOT EXISTS contact_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        purpose VARCHAR(50) NOT NULL,
        name VARCHAR(50) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(100) NOT NULL,
        city VARCHAR(50) NOT NULL,
        message TEXT NOT NULL
    )";
    if (!$conn->query($createTable)) {
        throw new Exception("Error creating table: " . $conn->error);
    }

    //Controller
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $purpose = trim(filter_input(INPUT_POST, 'purpose', FILTER_SANITIZE_STRING));
        $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $city = trim(filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING));
        $message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING));

        // Validation
        if (empty($purpose) || !in_array($purpose, ['bus', 'ticket', 'schedule', 'other'])) {
            $purpose_error = "Please select a valid purpose.";
        }

        if (empty($name) || !preg_match("/^[a-zA-Z\s]{2,50}$/", $name)) {
            $name_error = "Name must be 2-50 characters (letters and spaces only).";
        }

        if (empty($phone) || !preg_match("/^\+880\s?1[0-9]{3}-?[0-9]{6}$/", $phone)) {
            $phone_error = "Phone number must be in format +880 1XXX-XXXXXX.";
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = "Valid email is required.";
        }

        if (empty($city) || !in_array($city, ['dhaka', 'chittagong', 'sylhet', 'khulna', 'rajshahi', 'barisal', 'rangpur'])) {
            $city_error = "Please select a valid city.";
        }

        if (empty($message) || strlen($message) < 10 || strlen($message) > 500) {
            $message_error = "Message must be 10-500 characters long.";
        }

        if (empty($purpose_error) && empty($name_error) && empty($phone_error) && empty($email_error) && empty($city_error) && empty($message_error)) {
            $stmt = $conn->prepare("INSERT INTO contact_submissions (purpose, name, phone, email, city, message) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("ssssss", $purpose, $name, $phone, $email, $city, $message);
            if ($stmt->execute()) {
                $success = "Your message has been successfully sent and stored in our database!";
                $purpose = $name = $phone = $email = $city = $message = "";
            } else {
                throw new Exception("Error saving your message to the database: " . $stmt->error);
            }
            $stmt->close();
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    $message_error = $error;
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>

<!-- View -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - GoBus</title>
    <link rel="stylesheet" href="../css/contact.css">
    <style>
        .error-text { color: red; font-size: 0.9em; display: block; margin-top: 5px; }
        .success { color: green; font-size: 0.9em; margin-bottom: 10px; font-weight: bold; text-align: center; }
        .form-group { margin-bottom: 15px; }
    </style>
</head>
<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="tel:+8801234567890" class="call-btn">Call +8801234567890</a>
            <a href="./html/login.php" class="login-btn"><img src="../picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> Login</a>
           
        </div>
    </header>
    
    <div class="container">
        <main>
            <section class="contact-header">
                <h2>Contact Us</h2>
                <p>Thank you for reaching us! We are always happy to hear from you</p>
            </section>
            <div class="divider"></div>
            
            <section class="contact-form-section">
                <?php if (!empty($success)): ?>
                    <p class="success"><?php echo htmlspecialchars($success); ?></p>
                <?php endif; ?>
                
                <form class="contact-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="purpose">Purpose</label>
                        <select id="purpose" name="purpose">
                            <option value="" disabled <?php echo empty($purpose) ? 'selected' : ''; ?>>Select purpose</option>
                            <option value="bus" <?php echo $purpose === 'bus' ? 'selected' : ''; ?>>Bus</option>
                            <option value="ticket" <?php echo $purpose === 'ticket' ? 'selected' : ''; ?>>Ticket Issue</option>
                            <option value="schedule" <?php echo $purpose === 'schedule' ? 'selected' : ''; ?>>Schedule Information</option>
                            <option value="other" <?php echo $purpose === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <span class="error-text"><?php echo htmlspecialchars($purpose_error); ?></span>
                    </div>

                    <div class="form-group">
                        <label for="name">Your Name *</label>
                        <input type="text" id="name" name="name" placeholder="Your Name" value="<?php echo htmlspecialchars($name); ?>" >
                        <span class="error-text"><?php echo htmlspecialchars($name_error); ?></span>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" placeholder="+880 1XXX-XXXXXX" value="<?php echo htmlspecialchars($phone); ?>">
                        <span class="error-text"><?php echo htmlspecialchars($phone_error); ?></span>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" placeholder="your.email@example.com" value="<?php echo htmlspecialchars($email); ?>" >
                        <span class="error-text"><?php echo htmlspecialchars($email_error); ?></span>
                    </div>

                    <div class="form-group">
                        <label for="city">Your City *</label>
                        <select id="city" name="city">
                            <option value="" disabled <?php echo empty($city) ? 'selected' : ''; ?>>Select your city</option>
                            <option value="dhaka" <?php echo $city === 'dhaka' ? 'selected' : ''; ?>>Dhaka</option>
                            <option value="chittagong" <?php echo $city === 'chittagong' ? 'selected' : ''; ?>>Chittagong</option>
                            <option value="sylhet" <?php echo $city === 'sylhet' ? 'selected' : ''; ?>>Sylhet</option>
                            <option value="khulna" <?php echo $city === 'khulna' ? 'selected' : ''; ?>>Khulna</option>
                            <option value="rajshahi" <?php echo $city === 'rajshahi' ? 'selected' : ''; ?>>Rajshahi</option>
                            <option value="barisal" <?php echo $city === 'barisal' ? 'selected' : ''; ?>>Barisal</option>
                            <option value="rangpur" <?php echo $city === 'rangpur' ? 'selected' : ''; ?>>Rangpur</option>
                        </select>
                        <span class="error-text"><?php echo htmlspecialchars($city_error); ?></span>
                    </div>

                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="4" placeholder="Type your message here..." ><?php echo htmlspecialchars($message); ?></textarea>
                        <span class="error-text"><?php echo htmlspecialchars($message_error); ?></span>
                    </div>
                    
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </section>

            <section class="contact-info">
                <div class="info-card">
                    <div class="icon"><img src="../picture/call.png" alt="call Icon" style="width: 45px; height: 45px; vertical-align: middle;"></div>
                    <h3>Call Us</h3>
                    <p>+880 9611-123456</p>
                    <p>+880 9611-123457</p>
                </div>

                <div class="info-card">
                    <div class="icon"><img src="../picture/email.png" alt="call Icon" style="width: 45px; height: 45px; vertical-align: middle;"></div>
                    <h3>Email Us</h3>
                    <p>info@gobus.com</p>
                    <p>support@gobus.com</p>
                </div>

                <div class="info-card">
                   <div class="icon"><img src="../picture/location.png" alt="location Icon" style="width: 40px; height: 40px; vertical-align: middle;"></div>
                    <h3>Visit Us</h3>
                    <p>American International University, Bangladesh</p>
                    <p>Kuril, Dhaka, Bangladesh</p>
                </div>
            </section>
        </main>
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
                <a href="../index.php">Home</a>
                <a href="./aboutUs.php">About Us</a>
                
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