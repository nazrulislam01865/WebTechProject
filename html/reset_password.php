<!-- 

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBUS | Reset Password</title>
        <link rel="stylesheet" type="text/css" href="../css/forgetPassoword.css">
    </head>
    <body>
        <div class="container">
            <h1>Reset Password</h1>
            <p>Enter your new password.</p>
            <form action="reset_password_process.php" method="POST">
                <label for="password">New Password<sup>*</sup></label>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Enter new password" required>
                </div>
                <label for="confirm-password">Confirm Password<sup>*</sup></label>
                <div class="input-box">
                    <input type="password" name="confirm-password" placeholder="Confirm password" required>
                </div>
                <div id="password-error" style="color: red;">
                    <?php
                    session_start();
                    if (isset($_SESSION['error'])) {
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    }
                    ?>
                </div>
                <button class="OTP-btn" type="submit">Reset Password</button>
                <div class="back">
                    Go back to <a href="login.html">Log In</a>
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

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows == 0) {
    die("Error: The 'users' table does not exist. Please create it in the 'gobus' database.");
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        $new_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ss", $new_password, $email);

        if ($stmt->execute()) {
            $success = "Password reset successfully.";
            unset($_SESSION['reset_email']); // Clear session
        } else {
            $error = "Failed to reset password.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBUS | Reset Password</title>
    <link rel="stylesheet" type="text/css" href="../css/forgetPassword.css">
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        <p>Enter your new password.</p>
        <form method="POST" action="">
            <label for="email">Email<sup>*</sup></label>
            <div class="input-box">
                <input type="email" name="email" placeholder="Enter email" value="<?php echo isset($_SESSION['reset_email']) ? htmlspecialchars($_SESSION['reset_email']) : ''; ?>" required>
            </div>
            <label for="password">New Password<sup>*</sup></label>
            <div class="input-box">
                <input type="password" name="password" placeholder="Enter new password" required>
            </div>
            <label for="confirm-password">Confirm Password<sup>*</sup></label>
            <div class="input-box">
                <input type="password" name="confirm-password" placeholder="Confirm password" required>
            </div>
            <?php if ($error): ?>
                <div id="password-error" style="color: red;"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div id="success" style="color: green;"><?php echo htmlspecialchars($success); ?> <a href="login.php">Log In</a></div>
            <?php endif; ?>
            <button class="OTP-btn" type="submit">Reset Password</button>
            <div class="back">
                Go back to <a href="login.php">Log In</a>
            </div>
        </form>
    </div>
</body>
</html>