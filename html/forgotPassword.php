
<!-- 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBUS|ForgotPassword</title>
        <link rel = "stylesheet" type="text/css" href="../css/forgetPassoword.css">
    </head>
    <body>
        <div class="container">
            <h1>Forgot Password?</h1>
            <p>Enter your phone number and we will send you an OTP to reset it.</p>
            <form id="forgotForm" method="POST">
                <label for ="phone-number">Phone Number<sup>*</sup></label>
                <div class ="input-box">
                    <input type="text" placeholder ="Enter phone number" required>
                </div>
                <div id="number-error"></div>
                <button class="OTP-btn">Send OTP</button>
                <div class ="back">
                    Go back to <a href="login.php">Log In</a>
                </div>
            </form>
        </div>  
    </body>
</html> -->


<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "gobus");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if tables exist
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows == 0) {
    die("Error: The 'users' table does not exist. Please create it in the 'gobus' database.");
}
$result = $conn->query("SHOW TABLES LIKE 'password_resets'");
if ($result->num_rows == 0) {
    die("Error: The 'password_resets' table does not exist. Please create it in the 'gobus' database.");
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id);
            $stmt->fetch();

            // Generate OTP
            $otp = rand(100000, 999999);
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            // Store OTP in DB
            $stmt2 = $conn->prepare("INSERT INTO password_resets (user_id, otp, otp_expiry, is_used) VALUES (?, ?, ?, 0)");
            if (!$stmt2) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt2->bind_param("iss", $user_id, $otp, $expiry);
            if (!$stmt2->execute()) {
                $error = "Failed to store OTP: " . $conn->error;
            } else {
                // Send OTP via email using mail()
                $to = $email;
                $subject = "Password Reset OTP";
                $message = "Your OTP is <b>$otp</b>. It will expire in 10 minutes.";
                $headers = "From: GoBus App <mkmasum420@gmail.com>\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                if (mail($to, $subject, $message, $headers)) {
                    $success = "OTP sent to your email.";
                    $_SESSION['reset_email'] = $email; // Store email for next steps
                } else {
                    $error = "Failed to send OTP. Please check your server mail configuration.";
                }
            }
            $stmt2->close();
        } else {
            $error = "Email not registered.";
        }
        $stmt->close();
    }
}

// Clean up expired OTPs
$conn->query("DELETE FROM password_resets WHERE otp_expiry < NOW() OR is_used = 1");
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBUS | Forgot Password</title>
    <link rel="stylesheet" type="text/css" href="../css/forgetPassword.css">
</head>
<body>
    <div class="container">
        <h1>Forgot Password?</h1>
        <p>Enter your email address and we will send you an OTP to reset your password.</p>
        <form method="POST" action="">
            <label for="email">Email<sup>*</sup></label>
            <div class="input-box">
                <input type="email" name="email" placeholder="Enter email" required>
            </div>
            <?php if ($error): ?>
                <div id="error" style="color: red;"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div id="success" style="color: green;"><?php echo htmlspecialchars($success); ?> <a href="verify_otp.php">Verify OTP</a></div>
            <?php endif; ?>
            <button class="OTP-btn" type="submit">Send OTP</button>
            <div class="back">
                Go back to <a href="login.php">Log In</a>
            </div>
        </form>
    </div>
</body>
</html>