<!-- 


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBUS | Verify OTP</title>
        <link rel="stylesheet" type="text/css" href="../css/forgetPassoword.css">
    </head>
    <body>
        <div class="container">
            <h1>Verify OTP</h1>
            <p>Enter the OTP sent to your email address.</p>
            <form action="verify_otp_process.php" method="POST">
                <label for="otp">OTP<sup>*</sup></label>
                <div class="input-box">
                    <input type="text" name="otp" placeholder="Enter OTP" required>
                </div>
                <div id="otp-error" style="color: red;">
                    <?php
                    session_start();
                    if (isset($_SESSION['error'])) {
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    }
                    ?>
                </div>
                <button class="OTP-btn" type="submit">Verify OTP</button>
                <div class="back">
                    Go back to <a href="forgotPassword.php">Resend OTP</a>
                </div>
            </form>
        </div>   
    </body>
</html>



 -->

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
    $otp = $_POST['otp'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !is_numeric($otp)) {
        $error = "Invalid email or OTP format.";
    } else {
        $stmt = $conn->prepare("SELECT u.id, pr.otp, pr.otp_expiry, pr.is_used
                                FROM users u
                                JOIN password_resets pr ON u.id = pr.user_id
                                WHERE u.email = ? 
                                ORDER BY pr.id DESC LIMIT 1");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id, $db_otp, $otp_expiry, $is_used);
        $stmt->fetch();

        if ($is_used) {
            $error = "OTP already used.";
        } elseif ($db_otp == $otp && strtotime($otp_expiry) > time()) {
            // Mark OTP as used
            $stmt2 = $conn->prepare("UPDATE password_resets SET is_used = 1 WHERE user_id = ? AND otp = ?");
            if (!$stmt2) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt2->bind_param("is", $user_id, $db_otp);
            $stmt2->execute();
            $success = "OTP verified successfully.";
            header("Location: reset_password.php");
            exit();
        } else {
            $error = "Invalid or expired OTP.";
        }
        $stmt->close();
        if (isset($stmt2)) {
            $stmt2->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBUS | Verify OTP</title>
    <link rel="stylesheet" type="text/css" href="../css/forgetPassword.css">
</head>
<body>
    <div class="container">
        <h1>Verify OTP</h1>
        <p>Enter the OTP sent to your email address.</p>
        <form method="POST" action="">
            <label for="email">Email<sup>*</sup></label>
            <div class="input-box">
                <input type="email" name="email" placeholder="Enter email" value="<?php echo isset($_SESSION['reset_email']) ? htmlspecialchars($_SESSION['reset_email']) : ''; ?>" required>
            </div>
            <label for="otp">OTP<sup>*</sup></label>
            <div class="input-box">
                <input type="text" name="otp" placeholder="Enter OTP" required>
            </div>
            <?php if ($error): ?>
                <div id="otp-error" style="color: red;"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div id="success" style="color: green;"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <button class="OTP-btn" type="submit">Verify OTP</button>
            <div class="back">
                Go back to <a href="forgotPassword.php">Resend OTP</a>
            </div>
        </form>
    </div>
</body>
</html>